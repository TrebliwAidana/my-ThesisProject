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
        if ((int) $user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('members.view');
    }

    /**
     * Determine whether the user can view a specific member.
     */
    public function view(User $user, User $member): bool
    {
        if ((int) $user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('members.view');
    }

    /**
     * Determine whether the user can create members.
     */
    public function create(User $user): bool
    {
        if ((int) $user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('members.create');
    }

    /**
     * Determine whether user can assign specific role to a member.
     * Prevents privilege escalation — users cannot assign roles equal
     * to or more privileged than their own (lower level number = more privileged).
     */
    public function assignRole(User $user, ?Role $role = null): bool
    {
        // Must have create permission first
        if (!$this->create($user)) {
            return false;
        }

        // System Admin can assign any role
        if ((int) $user->role->level === 1) {
            return true;
        }

        if (!$role) {
            return false;
        }

        // Allow assigning only roles with a higher level number (lower privilege)
        // e.g. level 3 user can assign level 4, 5, 6... but NOT level 1, 2, or 3
        if ((int) $role->level > (int) $user->role->level) {
            return $user->hasPermission('members.assign_roles');
        }

        // Block assigning roles of equal or greater privilege
        return false;
    }

    /**
     * Determine whether the user can update a member.
     */
    public function update(User $user, User $member): bool
    {
        if ((int) $user->role->level === 1) {
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
        return (int) $user->role->level === 1;
    }

    /**
     * Determine whether the user can force delete a member.
     */
    public function forceDelete(User $user, User $member): bool
    {
        return (int) $user->role->level === 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }
}