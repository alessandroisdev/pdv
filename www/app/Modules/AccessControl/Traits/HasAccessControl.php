<?php

namespace App\Modules\AccessControl\Traits;

use App\Modules\AccessControl\Models\Role;
use App\Modules\AccessControl\Models\Permission;

trait HasAccessControl
{
    public function hasRole($roleName)
    {
        // 1. Opcional: Super Admin sempre retorna true se quisermos. 
        // Mas para verificação exata de papel, testamos estrito:
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_has_roles');
    }

    public function customPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_has_permissions')
                    ->withPivot('is_granted')
                    ->withTimestamps();
    }

    public function hasPermissionTo($permissionName)
    {
        // 1. Super Admin bypass
        if ($this->roles()->where('name', 'Super Admin')->exists()) {
            return true;
        }

        // 2. Check specific override first (granular precedence)
        $override = $this->customPermissions()->where('name', $permissionName)->first();
        if ($override) {
            return (bool) $override->pivot->is_granted;
        }

        // 3. Fallback to assigned role permissions
        foreach ($this->roles as $role) {
            if ($role->permissions()->where('name', $permissionName)->exists()) {
                return true;
            }
        }

        return false;
    }
}
