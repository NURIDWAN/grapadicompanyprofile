<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('view_any_facility');
    }

    public function view(User $user, Facility $facility): bool
    {
        return $this->isAdmin($user) || $user->can('view_facility');
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('create_facility');
    }

    public function update(User $user, Facility $facility): bool
    {
        return $this->isAdmin($user) || $user->can('update_facility');
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $this->isAdmin($user) || $user->can('delete_facility');
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('delete_any_facility');
    }

    public function forceDelete(User $user, Facility $facility): bool
    {
        return $this->isAdmin($user) || $user->can('force_delete_facility');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('force_delete_any_facility');
    }

    public function restore(User $user, Facility $facility): bool
    {
        return $this->isAdmin($user) || $user->can('restore_facility');
    }

    public function restoreAny(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('restore_any_facility');
    }

    public function replicate(User $user, Facility $facility): bool
    {
        return $this->isAdmin($user) || $user->can('replicate_facility');
    }

    public function reorder(User $user): bool
    {
        return $this->isAdmin($user) || $user->can('reorder_facility');
    }

    private function isAdmin(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('admin');
    }
}
