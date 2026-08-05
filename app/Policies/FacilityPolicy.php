<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_facility');
    }

    public function view(User $user, Facility $facility): bool
    {
        return $user->can('view_facility');
    }

    public function create(User $user): bool
    {
        return $user->can('create_facility');
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->can('update_facility');
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->can('delete_facility');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_facility');
    }

    public function forceDelete(User $user, Facility $facility): bool
    {
        return $user->can('force_delete_facility');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_facility');
    }

    public function restore(User $user, Facility $facility): bool
    {
        return $user->can('restore_facility');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_facility');
    }

    public function replicate(User $user, Facility $facility): bool
    {
        return $user->can('replicate_facility');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_facility');
    }
}
