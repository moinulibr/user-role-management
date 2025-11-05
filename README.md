---

##  **Summary: Custom Role-Permission System Overview**

### 🔹 1. **System Type**

এটি একটি **single-tenant, single-user-role** ভিত্তিক system।
প্রত্যেক `User` এর একটি নির্দিষ্ট `role_id` থাকে, যা তার access control নির্ধারণ করে।

---

### 🔹 2. **Key Components**

| Component                                                | Description                                                                                |
| -------------------------------------------------------- | ------------------------------------------------------------------------------------------ |
| **RoleController**                                       | Role create, edit, update, delete & permission assign করে।                                 |
| **AuthorizePermission Middleware**                       | নির্দিষ্ট permission ছাড়া কোনো route access করতে দেয় না।                                   |
| **AuthServiceProvider (Gates)**                          | প্রতিটি permission এর জন্য dynamic Gate তৈরি করে।                                          |
| **HasCustomPermissions Trait**                           | User model-এ permission checking logic define করা আছে (যেমন `hasPermission($permission)`)। |
| **Config Files (`app_permissions.php` & `sidebar.php`)** | সব permission এবং sidebar menu config আকারে সংরক্ষিত।                                      |
| **SidebarComposer**                                      | Sidebar menu load করার সময় শুধুমাত্র permitted item দেখায়।                                 |

---

### 🔹 3. **Permission Configuration**

**config/app_permissions.php**

```php
'modules' => [
    'users' => ['manage', 'assign'],
    'roles' => ['manage', 'assign'],
    'settings' => ['manage', 'view', 'update'],
]
```

>  এতে প্রতিটি module এবং তার অনুমোদিত action define করা হয়।
> যেমন `users.manage` বা `settings.update`।

---

### 🔹 4. **Dynamic Gate Registration**

`AuthServiceProvider` automatically সব permission এর জন্য Gate create করে:

```php
Gate::define('users.manage', fn(User $user) => $user->hasPermission('users.manage'));
```

তাই `@can('users.manage')` বা `Gate::allows('users.manage')` — দুটোই কাজ করবে।

---

### 🔹 5. **Permission Middleware**

`AuthorizePermission` middleware route level এ permission enforce করে:

```php
Route::middleware(['auth', 'permission:users.manage'])->group(function () {
    Route::resource('users', UserController::class);
});
```

> Unauthorized হলে AJAX request এ JSON 403 দেয়,
> আর সাধারণ request হলে redirect করে dashboard এ error সহ।

---

### 🔹 6. **Sidebar Composer**

`SidebarComposer` automatically sidebar menu filter করে শুধুমাত্র যেসব item user দেখতে পারে সেগুলো দেখায়।

```php
View::composer('layouts.sidebar', SidebarComposer::class);
```

Menu config (`config/sidebar.php`) থেকে permission check করে item filter হয়।

---

### 🔹 7. **Roles & Users**

* প্রতিটি `Role` এর একটি `permissions` ফিল্ড আছে (JSON আকারে সংরক্ষিত)।
* User এর সাথে `role_id` সংযুক্ত থাকে।
* Trait (`HasCustomPermissions`) এর মাধ্যমে `user->hasPermission('users.manage')` চেক করা হয়।

---

### 🔹 8. **Routes Example**

```php
Route::middleware(['auth', 'permission:users.manage'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        Route::get('users/{user}/roles/assign', [UserController::class, 'assignRoleForm'])
            ->name('users.assignRoleForm')
            ->middleware('permission:users.assign');
    });
```

---

#  Laravel Custom Role & Permission System

A simple **custom role & permission management** built on top of Laravel Gates & Middleware.
This system provides **single-tenant user-role management** with dynamic sidebar filtering and permission-based route control.

---

## Features

✅ Role CRUD (create, update, delete, view)
✅ Dynamic Permission system from config file
✅ Middleware-based access control
✅ Role-wise permission assignment
✅ Sidebar auto-filter based on user permissions
✅ Simple and extendable architecture

---

##  Folder Structure Overview

```
app/
 ├── Http/
 │   ├── Controllers/
 │   │   └── RoleController.php
 │   ├── Middleware/
 │   │   └── AuthorizePermission.php
 │   ├── Providers/
 │   │   ├── AppServiceProvider.php
 │   │   └── AuthServiceProvider.php
 │   └── View/
 │       └── Composers/
 │           └── SidebarComposer.php
 ├── Models/
 │   └── User.php
 └── Traits/
     └── HasCustomPermissions.php
```

---

## ⚙️ Configuration

### 1️⃣ Define Permissions

**config/app_permissions.php**

```php
return [
    'modules' => [
        'users' => ['manage', 'assign'],
        'roles' => ['manage', 'assign'],
        'settings' => ['manage', 'view', 'update'],
    ],
];
```

### 2️⃣ Define Sidebar Menu

**config/sidebar.php**

```php
return [
    ['title' => 'Users', 'route' => 'admin.users.index', 'icon' => 'mdi mdi-account-group', 'permission' => 'users.manage'],
    ['title' => 'Roles', 'route' => 'admin.roles.index', 'icon' => 'mdi mdi-shield-account', 'permission' => 'roles.manage'],
    ['title' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'mdi mdi-settings', 'permission' => 'settings.manage'],
];
```

---

##  Middleware Setup

**app/Http/Middleware/AuthorizePermission.php**

```php
if (Gate::denies($permission)) {
    return $request->expectsJson()
        ? response()->json(['message' => 'Access Denied', 'permission' => $permission], 403)
        : redirect()->route('dashboard')->with('error', "Access Denied: {$permission}");
}
```

And register alias in `AppServiceProvider`:

```php
$router->aliasMiddleware('permission', AuthorizePermission::class);
```

---

##  Dynamic Gates

In `AuthServiceProvider`:

```php
foreach (config('app_permissions.modules', []) as $module => $actions) {
    foreach ($actions as $action) {
        Gate::define("$module.$action", fn(User $user) => $user->hasPermission("$module.$action"));
    }
}
```

---

##  Sidebar Filter

`App\View\Composers\SidebarComposer` dynamically hides unauthorized menu items.

```php
View::composer('layouts.sidebar', SidebarComposer::class);
```

---

##  Example Route Usage

```php
Route::middleware(['auth', 'permission:users.manage'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
    });
```

---

## ✅ Role Table Structure (Example)

| Column       | Type   | Description         |
| ------------ | ------ | ------------------- |
| id           | int    | Primary key         |
| name         | string | Role identifier     |
| display_name | string | Human readable name |
| permissions  | json   | List of permissions |

---

##  How It Works

1. Permissions are defined in config (`app_permissions.php`).
2. `AuthServiceProvider` registers Gates for each permission.
3. Middleware (`permission`) checks if user has permission for route.
4. SidebarComposer filters visible menu items.
5. Roles store their assigned permissions in JSON.
6. User model uses `HasCustomPermissions` trait to validate access.

---

##  Extend / Customize

* Add new module permission → `config/app_permissions.php`
* Add new menu item → `config/sidebar.php`
* Attach permissions to roles via RoleController UI
* Assign role to user (UserController → `assignRoles`)

---

##  Requirements

* Laravel 10+
* Auth scaffolding enabled
* `roles` table with `permissions` (JSON) field
* `users` table with `role_id` foreign key

---

##  Author

**Developed by:** Moinul Islam
**GitHub:** [github.com/moinulibr](#)
**License:** MIT

---
--------------------------------------------------------------


## 🧠 1. Overview — Role Module কীভাবে কাজ করে

এই module মূলত:

* User-দের জন্য Role assign/manage করার কাজ করে।
* প্রতিটি Role-এ একটি `name`, একটি `display_name`, এবং এক বা একাধিক `permissions` থাকে।
* এক User-এর একাধিক Role থাকতে পারে (Many-to-Many relation)।
* Permissions JSON আকারে `roles` টেবিলে সংরক্ষিত থাকে।

**Flow:**

1. Admin নতুন Role তৈরি করে (name, display_name, permissions সহ)
2. Admin User-এর সাথে Role assign করে (via `role_user` pivot table)
3. Application logic-এ (যেমন middleware বা blade view) তুমি `hasPermissionTo()` ব্যবহার করে permission চেক করতে পারো।

---

## 🧩 2. Database Tables

### `roles` table

| Column       | Type   | Description                                 |
| ------------ | ------ | ------------------------------------------- |
| id           | bigint | Primary key                                 |
| name         | string | Unique system name (e.g., `super_admin`)    |
| display_name | string | Friendly name (e.g., `Super Administrator`) |
| permissions  | json   | Permission list as JSON array               |
| timestamps   | —      | Created & updated timestamps                |

### `role_user` table (pivot)

| Column  | Type      | Description           |
| ------- | --------- | --------------------- |
| user_id | foreignId | Reference to users.id |
| role_id | foreignId | Reference to roles.id |

---

## ⚙️ 3. Model Explanation

### `App\Models\Role`

```php
class Role extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['permissions' => 'array'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        return in_array($permissionName, $this->permissions ?? []);
    }
}
```

* **`$casts`** → `permissions` JSON ফিল্ডকে স্বয়ংক্রিয়ভাবে array হিসেবে ব্যবহার করতে দেয়।
* **`hasPermissionTo()`** → কোনো Role-এর নির্দিষ্ট permission আছে কিনা চেক করে।

### `App\Models\User` (তুমি এটা include করবে)

```php
public function roles()
{
    return $this->belongsToMany(Role::class, 'role_user');
}

public function hasRole($roleName)
{
    return $this->roles()->where('name', $roleName)->exists();
}

public function hasPermission($permission)
{
    return $this->roles->contains(fn($role) => $role->hasPermissionTo($permission));
}
```

---

## 🧭 4. Controller Structure

`app/Http/Controllers/Admin/RoleController.php`

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'display_name' => 'required',
        ]);

        Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'display_name' => 'required',
        ]);

        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
```

---

## 🧩 5. Routes

`routes/web.php`

```php
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
});
```

---

## 🧱 6. Views Folder Structure

📁 `resources/views/admin/roles/`

```
roles/
 ├── index.blade.php     // Role list
 ├── create.blade.php    // Role create form
 ├── edit.blade.php      // Role edit form
 └── _form.blade.php     // Shared form (optional)
```

**Copy করার জন্য তোমার views path:**

```
resources/views/admin/roles/
```

যদি তোমার `x-admin-layout` নামে reusable layout থাকে, সব ফাইলের শুরুতে সেটা ব্যবহার করবে।

### Example: `index.blade.php`

```blade
<x-admin-layout>
    <x-slot name="page_title">Roles Management</x-slot>

    <div class="rms-container">
        <div class="rms-header">
            <h2 class="rms-title">Roles</h2>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Add New Role</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Display Name</th>
                    <th>Permissions</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->display_name }}</td>
                    <td>{{ implode(', ', $role->permissions ?? []) }}</td>
                    <td>
                        <a href="{{ route('roles.edit', $role) }}">Edit</a> |
                        <form method="POST" action="{{ route('roles.destroy', $role) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this role?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
```

---

```md
# 🧩 Role Management Module

### 📁 Folder Structure
```

app/
└── Models/Role.php
app/Http/Controllers/Admin/RoleController.php
resources/views/admin/roles/
routes/web.php
database/migrations/xxxx_xx_xx_create_roles_table.php

````

### ⚙️ Features
- Create, Update, Delete Roles
- Assign Permissions (JSON array)
- User-Role many-to-many relationship
- Permission checking helpers (`hasRole`, `hasPermission`)
- Reusable views under `resources/views/admin/roles/`

### 🚀 Usage
1. Run migration  
   ```bash
   php artisan migrate
````

2. Access roles page

   ```
   /admin/roles
   ```

3. Create roles with permissions (comma-separated array).

4. Check permissions:

   ```php
   if (auth()->user()->hasPermission('user.create')) {
       // allowed
   }
   ```


---
