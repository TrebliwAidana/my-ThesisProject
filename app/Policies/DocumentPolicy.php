<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

/**
 * DocumentPolicy
 *
 * FIXES applied vs original:
 *
 * 1. view():   referenced 'documents.view_all' — this slug is NOT seeded in
 *              PermissionMatrixSeeder. The seeder only defines 'documents.view'.
 *              Fixed to: owner check OR 'documents.view' permission.
 *
 * 2. create(): was returning true for ALL authenticated users regardless of
 *              permissions — inconsistent with the permission matrix which
 *              grants 'documents.create' only to specific roles.
 *              Fixed to: isAdmin OR 'documents.create' permission.
 *
 * 3. All methods cast role->level to int before strict comparison to prevent
 *              silent failures when DB returns the level as a string "1".
 *
 * 4. Slugs verified against PermissionMatrixSeeder:
 *              documents.view, documents.create, documents.edit,
 *              documents.delete, documents.trash, documents.restore,
 *              documents.force-delete  ← note the hyphen, not underscore
 */
class DocumentPolicy
{
    // ── View Any ──────────────────────────────────────────────────────────────

    public function viewAny(User $user): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('documents.view')
            || $user->hasPermission('documents.trash');
    }

    // ── View ──────────────────────────────────────────────────────────────────

    /**
     * FIX: removed 'documents.view_all' — not a seeded permission slug.
     * View access is granted to: System Admin, document owner, or anyone
     * with 'documents.view'.
     */
    public function view(?User $user, Document $document): bool
    {
        if (!$user) {
            return false;
        }

        if ((int) $user->role->level === 1) {
            return true;
        }

        // Owner can always view their own document
        if ($document->owner_id === $user->id) {
            return true;
        }

        return $user->hasPermission('documents.view');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    /**
     * FIX: was returning true for all authenticated users — inconsistent with
     * PermissionMatrixSeeder which only grants 'documents.create' to specific
     * roles. Any role without this permission should not be able to upload.
     */
    public function create(User $user): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('documents.create');
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(User $user, Document $document): bool
    {
        if ((int) $user->role->level === 1) {
            return true;
        }

        // Owner with edit permission can update their own document
        if ($document->owner_id === $user->id) {
            return $user->hasPermission('documents.edit');
        }

        // Non-owners with edit permission can also update (e.g. admin staff)
        return $user->hasPermission('documents.edit');
    }

    // ── Download ──────────────────────────────────────────────────────────────

    public function download(?User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function delete(User $user, Document $document): bool
    {
        if ((int) $user->role->level === 1) {
            return true;
        }

        return $document->owner_id === $user->id
            && $user->hasPermission('documents.delete');
    }

    // ── Trash ─────────────────────────────────────────────────────────────────

    public function trash(User $user): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('documents.trash');
    }

    // ── Restore ───────────────────────────────────────────────────────────────

    public function restore(User $user, Document $document): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('documents.restore');
    }

    // ── Force Delete ──────────────────────────────────────────────────────────

    /**
     * Note: seeder uses 'documents.force-delete' (hyphen, not underscore).
     * This matches the PermissionMatrixSeeder matrix definition exactly.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('documents.force-delete');
    }
}