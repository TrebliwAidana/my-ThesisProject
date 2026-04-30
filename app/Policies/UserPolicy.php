<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

/**
 * UserPolicy
 *
 * FIXES applied vs original:
 *
 * 1. assignRole(): referenced 'members.assign_roles' — this slug is NOT defined
 *                  in PermissionMatrixSeeder. Removed the permission check and
 *                  simplified the guard: non-admins can only assign roles with a
 *                  strictly higher level number (lower privilege), and only if they
 *                  already have 'members.create'. This matches the AdminController
 *                  behaviour without requiring a non-existent slug.
 *
 * 2. restore():    was always returning false — inconsistent with AdminController
 *                  which calls $user->restore() for admins. Fixed to allow System
 *                  Admin to restore soft-deleted users.
 *
 * 3. All level comparisons cast to int to guard against DB returning string "1".
 */
class UserPolicy
{
    // ── View Any ──────────────────────────────────────────────────────────────

    public function viewAny(User $user): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('users.view');
    }

    // ── View ──────────────────────────────────────────────────────────────────

    public function view(User $user, User $target): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('users.view');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(User $user): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('users.create');
    }

    // ── Assign Role ───────────────────────────────────────────────────────────

    /**
     * FIX: original referenced 'members.assign_roles' which is NOT seeded in
     * PermissionMatrixSeeder. Privilege escalation prevention is kept intact:
     * non-admins can only assign roles with a strictly higher level number
     * (meaning less privileged). System Admin can assign any role.
     *
     * Requires 'users.create' as the base capability before role assignment
     * is considered — consistent with AdminController::storeUser().
     */
    public function assignRole(User $user, ?Role $role = null): bool
    {
        // Must be able to create users first
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

        // Prevent privilege escalation: can only assign roles with a higher
        // level number (lower privilege) than the current user's own role.
        // e.g. level-3 user can assign level-4, 5, 6… but NOT 1, 2, or 3.
        return (int) $role->level > (int) $user->role->level;
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(User $user, User $target): bool
    {
        if ((int) $user->role->level === 1) {
            return true;
        }

        // Users can always update their own profile
        if ($user->id === $target->id) {
            return true;
        }

        return $user->hasPermission('users.edit');
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function delete(User $user, User $target): bool
    {
        // Cannot delete yourself
        if ($user->id === $target->id) {
            return false;
        }

        return (int) $user->role->level === 1
            || $user->hasPermission('users.delete');
    }

    // ── Restore ───────────────────────────────────────────────────────────────

    /**
     * FIX: was always returning false — inconsistent with AdminController
     * which has a working restoreUser() action for admins. System Admin and
     * users with 'users.delete' can restore soft-deleted accounts.
     */
    public function restore(User $user, User $target): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('users.delete');
    }

    // ── Force Delete ──────────────────────────────────────────────────────────

    public function forceDelete(User $user, User $target): bool
    {
        // Cannot force-delete yourself
        if ($user->id === $target->id) {
            return false;
        }

        return (int) $user->role->level === 1;
    }
}