<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('view_any_user');
    }

    public function view(User $user, ?User $model = null): bool
    {
        return $this->isAdmin($user) || ($model && $user->id === $model->id) || $user->can('view_user');
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('create_user');
    }

    public function update(User $user, ?User $model = null): bool
    {
        return $this->isAdmin($user) || ($model && $user->id === $model->id) || $user->can('update_user');
    }

    public function delete(User $user, ?User $model = null): bool
    {
        return $this->isAdmin($user) || $user->can('delete_user');
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('delete_any_user');
    }

    public function forceDelete(User $user, ?User $model = null): bool
    {
        return $this->isAdmin($user) || $user->can('force_delete_user');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('force_delete_any_user');
    }

    public function restore(User $user, ?User $model = null): bool
    {
        return $this->isAdmin($user) || $user->can('restore_user');
    }

    public function restoreAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('restore_any_user');
    }

    public function replicate(User $user, ?User $model = null): bool
    {
        return $this->isAdmin($user) || $user->can('replicate_user');
    }

    public function reorder(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('reorder_user');
    }

    private function isAdmin(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('admin');
    }
}
