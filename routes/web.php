<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', fn () => view('welcome'));

// Dashboard (only authenticated & verified users)
Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile routes
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
});

//Tickets
/* Route::middleware('auth')->prefix('tickets')->name('tickets.')->group(function () {
    Route::get('/create', [TicketController::class, 'create'])->name('create');
    Route::post('/', [TicketController::class, 'store'])->name('store');
    Route::post('/{ticket}/sync', [TicketController::class, 'syncSingleTicket'])->name('sync-single');
    Route::post('/sync-all', [TicketController::class, 'syncAllTickets'])->name('sync-all');
}); */
// Group middleware-এ শুধু 'auth' থাকলো।
// 'tickets.view_private' পারমিশনটিকে আপাতত বাদ দেওয়া হলো, কারণ এর ফাংশনালিটি 'create' বা 'sync' এর জন্য অপ্রয়োজনীয়।
// (এটি সাধারণত 'index' বা 'show' রুটের জন্য লাগে, যা আপনার নেই)।
Route::middleware('auth')->prefix('tickets')->name('tickets.')->group(function () {

    // টিকিট তৈরির ফর্ম দেখার পারমিশন: tickets.create
    // এখন এই রুটে শুধুমাত্র tickets.create চেক হবে
    Route::get('/create', [TicketController::class, 'create'])
        ->name('create')
        ->middleware('permission:tickets.create');

    // টিকিট স্টোর করার পারমিশন: tickets.create
    Route::post('/', [TicketController::class, 'store'])
        ->name('store')
        ->middleware('permission:tickets.create');

    // একটি টিকিট সিঙ্ক করার পারমিশন: tickets.sync_single
    Route::post('/{ticket}/sync', [TicketController::class, 'syncSingleTicket'])
        ->name('sync-single')
        ->middleware('permission:tickets.sync_single');

    // সব টিকিট সিঙ্ক করার পারমিশন: tickets.sync_all
    Route::post('/sync-all', [TicketController::class, 'syncAllTickets'])
        ->name('sync-all')
        ->middleware('permission:tickets.sync_all');

    // যদি আপনি পরে একটি Index রুট যোগ করেন, তবে এইভাবে যোগ করুন:
    // Route::get('/', [TicketController::class, 'index'])
    //     ->name('index')
    //     ->middleware('permission:tickets.view_private');
});


// Admin Panel Group (উদাহরণস্বরূপ)
Route::middleware(['auth', 'permission:users.manage'])->prefix('admin')->name('admin.')->group(function () {

    // ১. Role Management
    Route::resource('roles', RoleController::class);

    // অতিরিক্ত পারমিশন চেক: roles.assign
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])
        ->name('roles.assignPermissions')
        ->middleware('permission:roles.assign');
    Route::get('roles-table', [RoleController::class, 'table'])->name('roles.table');

    // ২. User Management
    Route::resource('users', UserController::class);

    // অতিরিক্ত পারমিশন চেক: users.assign
    Route::post('users/{user}/roles', [UserController::class, 'assignRoles'])
        ->name('users.assignRoles')
        ->middleware('permission:users.assign');
});

require __DIR__.'/auth.php';


Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// ব্ল্যাংক পেজ
Route::get('/blank-page', function () {
    return view('blank-page');
})->name('blank');


Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // সেটিংস পেজ দেখানো (GET) এবং আপডেট করা (POST/PUT)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
