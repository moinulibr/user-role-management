<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleOld extends Model
{
    protected $guarded = ['id'];

    // from JSON field to convert to array in php
    protected $casts = [
    'permissions' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
    * It will be checked whether the specified permission is in the role.
    * @param string $permissionName
    */
    public function hasPermissionTo(string $permissionName): bool
    {
        // it will be checked whether the specified permission is in the persmissions array
        return in_array($permissionName, $this->permissions ?? []);
    }
}