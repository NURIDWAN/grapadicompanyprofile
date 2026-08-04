<?php

namespace App\Policies;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, Asset $asset): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Asset $asset): bool
    {
        return $asset->owner_id === $user->id || $this->isAdmin($user);
    }

    public function update(User $user, Asset $asset): bool
    {
        return $asset->owner_id === $user->id
            && ! in_array($asset->status, [AssetStatus::PendingReview, AssetStatus::Archived], true);
    }

    public function review(User $user): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('admin');
    }
}
