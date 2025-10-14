<style>
    .permission-module {
        border: 1px solid #ccc;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        background-color: #f9f9f9;
    }
    .action-checkbox {
        margin-right: 15px;
        display: inline-block;
    }
    .module-title {
        font-weight: bold;
        color: #333;
        text-transform: capitalize;
        border-bottom: 1px dashed #ddd;
        padding-bottom: 5px;
        margin-bottom: 10px;
    }
</style>

<h2>নতুন Role তৈরি করুন এবং পারমিশন দিন</h2>

<form action="{{ route('admin.roles.store') }}" method="POST">
    @csrf
    
    <div>
        <label for="display_name">Role নাম:</label>
        <input type="text" id="display_name" name="display_name" required>
    </div>
    <br>
    
    <h3>পারমিশন অ্যাসাইন করুন</h3>

    @php
        // বর্তমানে assign করা পারমিশন (যদি edit মোড হয়)
        $rolePermissions = []; // Role তৈরি করার সময় এটি খালি থাকবে
    @endphp

    @foreach ($permissions as $module => $actions)
        <div class="permission-module">
            <div class="module-title">{{ str_replace('_', ' ', $module) }}</div>

            @foreach ($actions as $action)
                @php
                    $permissionName = "{$module}.{$action}";
                    $isChecked = in_array($permissionName, $rolePermissions);
                @endphp
                
                <div class="action-checkbox">
                    <input 
                        type="checkbox" 
                        name="permissions[]" 
                        value="{{ $permissionName }}"
                        id="{{ $permissionName }}"
                        {{ $isChecked ? 'checked' : '' }}
                    >
                    <label for="{{ $permissionName }}">
                        {{ str_replace('_', ' ', $action) }}
                    </label>
                </div>
            @endforeach
        </div>
    @endforeach

    <button type="submit" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 5px;">
        Role তৈরি করুন ও সেভ করুন
    </button>
</form>