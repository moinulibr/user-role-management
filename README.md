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

##  **README.md**

তুমি নিচেরটা সরাসরি GitHub-এ `README.md` হিসেবে রাখতে পারো 👇

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
**GitHub:** [github.com/yourusername](#)
**License:** MIT

---