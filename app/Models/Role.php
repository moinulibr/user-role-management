<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];

    // JSON field থেকে PHP অ্যারেতে রূপান্তর করার জন্য
    protected $casts = [
        'permissions' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * নির্দিষ্ট পারমিশনটি রোলে আছে কিনা তা পরীক্ষা করে।
     * @param string $permissionName
     */
    public function hasPermissionTo(string $permissionName): bool
    {
        // permissions অ্যারেতে নির্দিষ্ট পারমিশন আছে কিনা চেক করা হয়
        return in_array($permissionName, $this->permissions ?? []);
    }
}
