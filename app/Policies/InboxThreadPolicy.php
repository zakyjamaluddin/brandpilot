<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\InboxThread;
use Illuminate\Auth\Access\HandlesAuthorization;

class InboxThreadPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:InboxThread');
    }

    public function view(AuthUser $authUser, InboxThread $inboxThread): bool
    {
        return $authUser->can('View:InboxThread');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InboxThread');
    }

    public function update(AuthUser $authUser, InboxThread $inboxThread): bool
    {
        return $authUser->can('Update:InboxThread');
    }

    public function delete(AuthUser $authUser, InboxThread $inboxThread): bool
    {
        return $authUser->can('Delete:InboxThread');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:InboxThread');
    }

    public function restore(AuthUser $authUser, InboxThread $inboxThread): bool
    {
        return $authUser->can('Restore:InboxThread');
    }

    public function forceDelete(AuthUser $authUser, InboxThread $inboxThread): bool
    {
        return $authUser->can('ForceDelete:InboxThread');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InboxThread');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InboxThread');
    }

    public function replicate(AuthUser $authUser, InboxThread $inboxThread): bool
    {
        return $authUser->can('Replicate:InboxThread');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InboxThread');
    }

}