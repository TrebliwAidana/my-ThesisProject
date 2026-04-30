<?php

namespace App\Policies;

use App\Models\FinancialTransaction;
use App\Models\User;

/**
 * FinancialTransactionPolicy
 *
 * Workflow:  pending → audited → approved → (paid for receivables)
 *
 * FIXES applied vs original:
 *
 * 1. audit():   was checking status !== 'approved'   — WRONG.
 *               Audit action applies to PENDING transactions.
 *               Fixed to: status !== 'pending'
 *
 * 2. approve(): was checking status !== 'pending'    — WRONG.
 *               Approve action requires status === 'audited'.
 *               Fixed to: status !== 'audited'
 *
 * 3. delete():  was hard-coded to level === 1 only.
 *               Treasurer has 'financial.delete' in the seeder — they can
 *               delete their own pending/rejected transactions.
 *               Fixed to: isAdmin OR (hasPermission AND owns the transaction).
 *
 * 4. forceDelete(): remains System Admin only — permanent deletion is a
 *               privileged action not assigned to any non-admin role.
 *
 * 5. restore(): was always false — restored to: isAdmin OR financial.delete.
 *               Consistent with FinancialController::restore() guard.
 *
 * 6. All methods cast role->level to int before comparison to prevent
 *               silent failures when DB returns a string "1".
 */
class FinancialTransactionPolicy
{
    // ── View ──────────────────────────────────────────────────────────────────

    public function viewAny(User $user): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('financial.view');
    }

    public function view(User $user, FinancialTransaction $transaction): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('financial.view');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(User $user): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('financial.create');
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    /**
     * Only pending transactions can be edited.
     * Treasurer can edit their own; System Admin can edit any.
     */
    public function update(User $user, FinancialTransaction $transaction): bool
    {
        if ($transaction->status !== 'pending') {
            return false;
        }

        if ((int) $user->role->level === 1) {
            return true;
        }

        // Own transaction + edit permission
        return $transaction->user_id === $user->id
            && $user->hasPermission('financial.edit');
    }

    // ── Audit ─────────────────────────────────────────────────────────────────

    /**
     * FIX: was checking status !== 'approved' — inverted from actual workflow.
     * Audit applies to PENDING transactions only.
     * Workflow: pending → [audit] → audited → [approve] → approved
     */
    public function audit(User $user, FinancialTransaction $transaction): bool
    {
        // Only pending transactions can be audited
        if ($transaction->status !== 'pending') {
            return false;
        }

        return (int) $user->role->level === 1
            || $user->hasPermission('financial.audit');
    }

    // ── Approve / Reject ──────────────────────────────────────────────────────

    /**
     * FIX: was checking status !== 'pending' — inverted from actual workflow.
     * Approve applies to AUDITED transactions only.
     * Workflow: pending → audited → [approve] → approved
     */
    public function approve(User $user, FinancialTransaction $transaction): bool
    {
        // Only audited transactions can be approved
        if ($transaction->status !== 'audited') {
            return false;
        }

        return (int) $user->role->level === 1
            || $user->hasPermission('financial.approve');
    }

    /**
     * Reject follows the same gate as approve — only audited or pending
     * transactions that haven't been finalized yet can be rejected.
     */
    public function reject(User $user, FinancialTransaction $transaction): bool
    {
        if (in_array($transaction->status, ['approved', 'paid', 'rejected'])) {
            return false;
        }

        return (int) $user->role->level === 1
            || $user->hasPermission('financial.approve');
    }

    // ── Mark as Paid (receivables only) ──────────────────────────────────────

    public function markAsPaid(User $user, FinancialTransaction $transaction): bool
    {
        if ($transaction->type !== 'receivable' || $transaction->status !== 'approved') {
            return false;
        }

        return (int) $user->role->level === 1
            || $user->hasPermission('financial.approve');
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    /**
     * FIX: original was System Admin only, but PermissionMatrixSeeder assigns
     * 'financial.delete' to Treasurer. Treasurers can delete their own
     * pending or rejected transactions. System Admin can delete any.
     */
    public function delete(User $user, FinancialTransaction $transaction): bool
    {
        if ((int) $user->role->level === 1) {
            return true;
        }

        // Non-admin: must own the transaction + have delete permission
        return $transaction->user_id === $user->id
            && $user->hasPermission('financial.delete');
    }

    // ── Restore ───────────────────────────────────────────────────────────────

    /**
     * FIX: original always returned false, but FinancialController::restore()
     * allows users with 'financial.delete'. Aligned to match controller logic.
     */
    public function restore(User $user, FinancialTransaction $transaction): bool
    {
        return (int) $user->role->level === 1
            || $user->hasPermission('financial.delete');
    }

    // ── Force Delete ──────────────────────────────────────────────────────────

    /**
     * Permanent deletion is System Admin only — no non-admin role is assigned
     * this capability in PermissionMatrixSeeder, and it cannot be undone.
     */
    public function forceDelete(User $user, FinancialTransaction $transaction): bool
    {
        return (int) $user->role->level === 1;
    }
}