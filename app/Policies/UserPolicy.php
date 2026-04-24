<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

class UserPolicy
{
    /**
     * Determine whether the user can view any members.
     */
    public function viewAny(User $user): bool
    {
        // System Admin (level 1) can view all
        if ($user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('members.view');
    }

    /**
     * Determine whether the user can view a specific member.
     */
    public function view(User $user, User $member): bool
    {
        // System Admin (level 1) can view all
        if ($user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('members.view');
    }

    /**
     * Determine whether the user can create members.
     */
    public function create(User $user): bool
    {
        // System Admin (level 1) can create all
        if ($user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('members.create');
    }

    /**
     * Determine whether user can assign specific role to a member.
     * This prevents privilege escalation.
     */
    public function assignRole(User $user, ?Role $role = null): bool
    {
        if (!$this->create($user)) {
            return false;
        }

        if ($user->role->level === 1) {
            return true;
        }

        if (!$role) {
            return false;
        }

        if ($role->level <= $user->role->level) {
            return $user->hasPermission('members.assign_roles');
        }

        return false;
    }

    /**
     * Determine whether the user can update a member.
     */
    public function update(User $user, User $member): bool
    {
        // System Admin (level 1) can update all
        if ($user->role->level === 1) {
            return true;
        }

        // Users can update themselves
        if ($user->id === $member->id) {
            return true;
        }

        return $user->hasPermission('members.edit');
    }

    /**
     * Determine whether the user can delete a member.
     */
    public function delete(User $user, User $member): bool
    {
        // Only System Admin (level 1) can delete
        return $user->role->level === 1;
    }

    /**
     * Determine whether the user can force delete a member.
     */
    public function forceDelete(User $user, User $member): bool
    {
        // Only System Admin (level 1) can force delete
        return $user->role->level === 1;
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    // /**
    //  * Determine whether the user can permanently delete the model.
    //  */
    // public function forceDelete(User $user, User $model): bool
    // {
    //     return false;
    // }
}
