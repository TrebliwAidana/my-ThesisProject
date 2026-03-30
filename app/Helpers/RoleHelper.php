<?php

namespace App\Helpers;

use App\Models\Role;

class RoleHelper
{
    public static function hasPermission($user, $permission)
    {
        if (!$user || !$user->role) {
            return false;
        }
        
        $role = $user->role;
        
        // Check current role permissions
        if ($role->permissions && in_array($permission, $role->permissions)) {
            return true;
        }
        
        // Check parent role permissions
        if ($role->parent && $role->parent->permissions) {
            if (in_array($permission, $role->parent->permissions)) {
                return true;
            }
        }
        
        // Super Admin has all permissions
        if ($role->name === 'Super Admin') {
            return true;
        }
        
        return false;
    }
    
    public static function getInheritedPermissions($role)
    {
        $permissions = $role->permissions ?? [];
        
        if ($role->parent) {
            $permissions = array_merge(
                $permissions,
                self::getInheritedPermissions($role->parent)
            );
        }
        
        return array_unique($permissions);
    }
}