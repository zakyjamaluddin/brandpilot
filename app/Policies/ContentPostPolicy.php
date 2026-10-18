<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ContentPost;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContentPostPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContentPost');
    }

    public function view(AuthUser $authUser, ContentPost $contentPost): bool
    {
        return $authUser->can('View:ContentPost');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContentPost');
    }

    public function update(AuthUser $authUser, ContentPost $contentPost): bool
    {
        return $authUser->can('Update:ContentPost');
    }

    public function delete(AuthUser $authUser, ContentPost $contentPost): bool
    {
        return $authUser->can('Delete:ContentPost');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ContentPost');
    }

    public function restore(AuthUser $authUser, ContentPost $contentPost): bool
    {
        return $authUser->can('Restore:ContentPost');
    }

    public function forceDelete(AuthUser $authUser, ContentPost $contentPost): bool
    {
        return $authUser->can('ForceDelete:ContentPost');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContentPost');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContentPost');
    }

    public function replicate(AuthUser $authUser, ContentPost $contentPost): bool
    {
        return $authUser->can('Replicate:ContentPost');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContentPost');
    }

}