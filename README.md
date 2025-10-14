Custom User Role & Permission Management Setup Guide (Laravel)
এই ডকুমেন্টেশনটি ব্যাখ্যা করে যে কীভাবে কোনো থার্ড-পার্টি প্যাকেজ (যেমন Spatie) ব্যবহার না করে তৈরি করা একটি কাস্টম রোল এবং পারমিশন ম্যানেজমেন্ট মডিউল Laravel প্রজেক্টে ইন্টিগ্রেট করবেন।

১. ফাইল কপি করুন (Copy Files)
আপনার প্রজেক্টে নিম্নলিখিত ফাইল এবং ফোল্ডারগুলি তাদের নির্দিষ্ট পাথে কপি করুন:

A. কনফিগারেশন এবং ডাটাবেস
উৎস ফাইল

গন্তব্য পাথ

বর্ণনা

[আপনার সোর্স]/config/app_permissions.php

config/app_permissions.php

অ্যাপ্লিকেশনের সমস্ত পারমিশনের তালিকা।

[আপনার সোর্স]/database/migrations/*_role_user_role_tables.php

database/migrations/

roles এবং role_user টেবিল তৈরির জন্য মাইগ্রেশন ফাইল।

B. মডেলস এবং ট্রেইট
উৎস ফাইল

গন্তব্য পাথ

বর্ণনা

[আপনার সোর্স]/app/Models/Role.php

app/Models/Role.php

রোল ডাটাবেস মডেল।

[আপনার সোর্স]/app/Traits/HasRolesAndPermissions.php

app/Traits/HasRolesAndPermissions.php

রোল ও পারমিশন লজিক ধারণকারী ট্রেইট।

C. কন্ট্রোলার্স
উৎস ফাইল

গন্তব্য পাথ

বর্ণনা

[আপনার সোর্স]/app/Http/Controllers/RoleController.php

app/Http/Controllers/RoleController.php

রোল এবং পারমিশন ম্যানেজমেন্ট।

[আপনার সোর্স]/app/Http/Controllers/UserController.php

app/Http/Controllers/UserController.php

ইউজার ম্যানেজমেন্ট।

D. ভিউজ
উৎস ফোল্ডার

গন্তব্য পাথ

বর্ণনা

[আপনার সোর্স]/resources/views/admin/roles/

resources/views/admin/roles/

রোল সম্পর্কিত সমস্ত ভিউ (Index, Create, Edit, AJAX Modal)।

[আপনার সোর্স]/resources/views/admin/users/

resources/views/admin/users/

ইউজার সম্পর্কিত সমস্ত ভিউ।

২. কোড ইন্টিগ্রেট করুন (Code Integration Steps)
ধাপ ২.১: User মডেলে Trait যোগ করা
app/Models/User.php ফাইলটি খুলুন এবং HasRolesAndPermissions ট্রেইটটি যোগ করুন:

// app/Models/User.php

namespace App\Models;

use App\Traits\HasRolesAndPermissions; // <--- এই লাইনটি যোগ করুন
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, HasRolesAndPermissions; // <--- এখানে ট্রেইটটি ব্যবহার করুন
    // ...
}

ধাপ ২.২: রুট সেটআপ করা
আপনার routes/web.php ফাইলটি খুলুন এবং অ্যাডমিন গ্রুপ বা যেখানে প্রয়োজন সেখানে নিম্নলিখিত রুটগুলি যোগ করুন:

// routes/web.php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    
    // Role Management
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::post('roles/{role}/sync-permissions', [RoleController::class, 'syncPermissions'])->name('roles.sync-permissions');

    // User Management
    Route::resource('users', UserController::class);
    // ...
});

ধাপ ২.৩: AuthServiceProvider এ Blade Directives যোগ করা
app/Providers/AuthServiceProvider.php ফাইলটি খুলুন এবং boot মেথডের মধ্যে নিম্নলিখিত কাস্টম ব্লেড ডিরেক্টিভগুলি যোগ করুন:

// app/Providers/AuthServiceProvider.php

use Illuminate\Support\Facades\Blade;
// ...

public function boot()
{
    $this->registerPolicies();

    // Custom Blade Directives
    Blade::if('role', function ($role) {
        return auth()->check() && auth()->user()->hasRole($role);
    });

    Blade::if('can', function ($permission) {
        return auth()->check() && auth()->user()->can($permission);
    });
}

৩. AJAX Modal ইন্টিগ্রেট করুন (Important)
এই মডিউলটি সঠিকভাবে কাজ করার জন্য এর AJAX Modal এবং Content Loading লজিকটি আপনার গ্লোবাল লেআউট ফাইল (resources/views/layouts/app.blade.php) এ যোগ করা অপরিহার্য।

resources/views/layouts/app.blade.php (ইন্টিগ্রেশন পয়েন্ট)
১. <head> ট্যাগের মধ্যে Modal-এর স্টাইল যোগ করুন:

<!-- HEAD ট্যাগের মধ্যে -->
<head>
    <!-- ... অন্যান্য CSS/স্ক্রিপ্টস ... -->
    @include('admin.roles.modal_styles')
    <!-- ... -->
</head>

২. <body> ট্যাগের শেষের দিকে Modal HTML, JavaScript লজিক এবং ইনিশিয়ালাইজেশন স্ক্রিপ্ট যোগ করুন:

<!-- BODY ট্যাগের শেষে, </body> এর ঠিক আগে -->
@include('admin.roles.modal_scripts')

<script>
    // AJAX Content Area কে টার্গেট করা
    const mainContentArea = document.querySelector('main > div > div'); // আপনার কন্টেন্ট কন্টেইনারের সঠিক সিলেক্টর ব্যবহার করুন
    
    // নেভিগেশন লিঙ্কগুলিতে AJAX ক্লাস যোগ করা
    document.querySelectorAll('nav a').forEach(link => {
        if (link.href.includes('/admin/')) { // অ্যাডমিন রুটগুলি চেক করুন
            link.classList.add('content-load-link');
        }
    });
    
    // AJAX লিসেনার ইনিশিয়ালাইজ করা
    if (typeof attachAllListeners === 'function') {
        attachAllListeners(mainContentArea);
    }
</script>
</body>

৪. কমান্ড চালান (Run Commands)
সব ফাইল কপি এবং কোড ইন্টিগ্রেট করার পর, নিম্নলিখিত কমান্ডগুলি চালান:

১. মাইগ্রেশন চালান:

php artisan migrate

২. কনফিগারেশন ক্যাশে পরিষ্কার করুন:

php artisan config:clear

এখন আপনার কাস্টম User Role Management মডিউলটি সম্পূর্ণরূপে সেটআপ হয়ে গেছে এবং ব্যবহারের জন্য প্রস্তুত!