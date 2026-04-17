<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('documents.view') 
        || $user->hasPermission('documents.trash')
        || $user->hasPermission('documents.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Document $document): bool
    {
        // Public documents are visible to everyone
        if ($document->is_public) {
            return true;
        }

        // Must be logged in for private documents
        if (!$user) {
            return false;
        }

        // Owner can always view
        if ($document->owner_id === $user->id) {
            return true;
        }

        // Additional share checks can go here...

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // any authenticated user can upload documents
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        return $user->id === $document->owner_id;
    }

    /**
     * Determine whether the user can download the model.
     */
    public function download(?User $user, Document $document): bool
    {
        return $this->view($user, $document);
        
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->id === $document->owner_id;
    }
    public function trash(User $user): bool
    {
        return $user->hasPermission('documents.trash') 
            || $user->hasPermission('documents.manage');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.restore') 
            || $user->hasPermission('documents.manage');
    }

    public function forceDelete(User $user, Document $document): bool
    {
        return $user->hasPermission('documents.force-delete') 
            || $user->hasPermission('documents.manage');
    }
}