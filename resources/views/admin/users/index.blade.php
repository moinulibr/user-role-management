@extends('admin.roles.base')
@section('title', 'User Management')

@section('content')
    <h2 style="font-size: 1.5em; margin-bottom: 15px;">All Users</h2>

    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Current Roles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @forelse ($user->roles as $role)
                                <span class="tag" style="background-color: #17a2b8; color: white;">
                                    {{ $role->display_name }}
                                </span>
                            @empty
                                <span style="color: gray;">No Role Assigned</span>
                            @endforelse
                        </td>
                        <td>
                            @can('users.assign')
                                <a href="{{ route('admin.users.edit', $user) }}" style="color: #007bff; text-decoration: none;">
                                    Assign Roles
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection