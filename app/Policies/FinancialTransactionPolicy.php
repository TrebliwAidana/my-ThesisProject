<?php

namespace App\Policies;

use App\Models\FinancialTransaction;
use App\Models\User;

class FinancialTransactionPolicy
{
    /**
     * Determine whether the user can view any financial transactions.
     */
    public function viewAny(User $user): bool
    {
        // System Admin (level 1) can always view
        if ($user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('financial.view');
    }

    /**
     * Determine whether the user can view a specific transaction.
     */
    public function view(User $user, FinancialTransaction $transaction): bool
    {
        // System Admin (level 1) can always view
        if ($user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('financial.view');
    }

    /**
     * Determine whether the user can create transactions.
     */
    public function create(User $user): bool
    {
        // System Admin (level 1) can always create
        if ($user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('financial.create');
    }

    /**
     * Determine whether the user can update a transaction.
     */
    public function update(User $user, FinancialTransaction $transaction): bool
    {
        // Only updatable if pending status
        if ($transaction->status !== 'pending') {
            return false;
        }

        // System Admin (level 1) can always update
        if ($user->role->level === 1) {
            return true;
        }

        // Creator can update their own pending transaction
        if ($transaction->user_id === $user->id) {
            return $user->hasPermission('financial.create');
        }

        return false;
    }

    /**
     * Determine whether the user can approve a transaction.
     */
    public function approve(User $user, FinancialTransaction $transaction): bool
    {
        // Only approvable if pending
        if ($transaction->status !== 'pending') {
            return false;
        }

        // System Admin (level 1) can approve
        if ($user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('financial.approve');
    }

    /**
     * Determine whether the user can audit a transaction.
     */
    public function audit(User $user, FinancialTransaction $transaction): bool
    {
        // Only auditable if approved
        if ($transaction->status !== 'approved') {
            return false;
        }

        // System Admin (level 1) can audit
        if ($user->role->level === 1) {
            return true;
        }

        return $user->hasPermission('financial.audit');
    }

    /**
     * Determine whether the user can delete a transaction.
     */
    public function delete(User $user, FinancialTransaction $transaction): bool
    {
        // Only System Admin (level 1) can delete
        return $user->role->level === 1;
    }

    /**
     * Determine whether the user can permanently delete a transaction.
     */
    public function forceDelete(User $user, FinancialTransaction $transaction): bool
    {
        // Only System Admin (level 1) can force delete
        return $user->role->level === 1;
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FinancialTransaction $financialTransaction): bool
    {
        return false;
    }

    // /**
    //  * Determine whether the user can permanently delete the model.
    //  */
    // public function forceDelete(User $user, FinancialTransaction $financialTransaction): bool
    // {
    //     return false;
    // }
}
