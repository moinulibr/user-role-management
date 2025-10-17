<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users (List).
     */
    public function index()
    {
        // রোল এবং পারমিশন সহ ইউজারদের তালিকা
        $users = User::with('roles')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user (Create).
     */
    public function create()
    {
        $roles = Role::all();
        // Create এর জন্য একটি নতুন User ইনস্ট্যান্স পাস করা হলো
        $user = new User();
        $userRoleIds = [];
        return view('admin.users.create', compact('user', 'roles', 'userRoleIds'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        // নতুন ইউজার তৈরি
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
            // মডেল-এ cast: 'password' => 'hashed', থাকায় এখানে Hash::make() প্রয়োজন নেই, তবে রাখলে ক্ষতি নেই।
            'password' => Hash::make($validated['password']),
        ]);

        // রোল অ্যাসাইন করা
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index')->with('success', 'User created successfully and roles assigned.');
    }

    /**
     * Display the specified user (View).
     */
    public function show(User $user)
    {
        // Roles সহ ইউজার ডেটা ভিউ পেজে পাঠানো হলো
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user (Edit).
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        // User এর বর্তমান রোল ID গুলি একটি অ্যারেতে নেওয়া
        $userRoleIds = $user->roles->pluck('id')->toArray();

        // create.blade.php কে edit পেজ হিসেবে ব্যবহার করার জন্য create ভিউ-এ পাস করা
        return view('admin.users.create', compact('user', 'roles', 'userRoleIds'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // বর্তমান ইউজারকে ignore করে unique চেক করা
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'mobile' => ['nullable', 'string', 'max:20'],
            // যদি পাসওয়ার্ড ফিল্ডে ইনপুট দেওয়া হয়, তবেই ভ্যালিডেট করা হবে
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
        ];

        // যদি পাসওয়ার্ড ফিল্ডে কিছু লেখা থাকে, তবেই পাসওয়ার্ড আপডেট করা হবে
        if ($request->filled('password')) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        // রোল অ্যাসাইন করা (assignRoles মেথডের লজিক এখানে সরাসরি ব্যবহার করা হলো)
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage (Delete).
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }


    /**
     * Show the form for assigning roles to a specific user. (NEW METHOD)
     */
    public function assignRoleForm(User $user)
    {
        $roles = Role::all();
        // User এর বর্তমান রোল ID গুলি একটি অ্যারেতে নেওয়া
        $userRoleIds = $user->roles->pluck('id')->toArray();

        // admin.users.assign-roles.blade.php ভিউ-এ পাঠানো হলো
        return view('admin.users.assign-roles', compact('user', 'roles', 'userRoleIds'));
    }

    /**
     * Handle the assignment of roles to a specific user (Existing method).
     */
    public function assignRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        // sync() মেথডটি বিদ্যমান রোলগুলিকে নতুনগুলির সাথে প্রতিস্থাপন করে
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index')->with('success', "Roles successfully assigned/updated for user: {$user->name}.");
    }
   
}
