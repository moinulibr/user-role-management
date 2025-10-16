<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index');
    }

    public function table()
    {
        $roles = Role::all();
        return view('admin.roles.table', compact('roles'));
    }

    public function create()
    {
        $modules = Config::get('app_permissions.modules');
        $role = new Role();
        return view('admin.roles.form', compact('modules', 'role'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
        ]);

        $name = Str::slug($validated['display_name'], '_');

        Role::create([
            'name' => $name,
            'display_name' => $validated['display_name'],
            'permissions' => $request->input('permissions', []),
        ]);

        return response()->json(['success' => true]);
    }

    public function edit(Role $role)
    {
        $modules = Config::get('app_permissions.modules');
        return view('admin.roles.form', compact('modules', 'role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
        ]);

        $role->update([
            'display_name' => $validated['display_name'],
            'permissions' => $request->input('permissions', []),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['success' => true]);
    }
}
