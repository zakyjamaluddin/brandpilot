<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $authUser): bool
    {
        return $authUser->isSuperAdmin();
    }

    public function view(User $authUser, User $model): bool
    {
        return $authUser->isSuperAdmin();
    }

    public function create(User $authUser): bool
    {
        return $authUser->isSuperAdmin();
    }

    public function update(User $authUser, User $model): bool
    {
        return $authUser->isSuperAdmin();
    }

    public function delete(User $authUser, User $model): bool
    {
        return $authUser->isSuperAdmin()
            && $authUser->getKey() !== $model->getKey()
            && $model->getKey() !== 1;
    }

    public function deleteAny(User $authUser): bool
    {
        return $authUser->isSuperAdmin();
    }
}
