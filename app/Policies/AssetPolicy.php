<?php

namespace App\Policies;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $this->isAdmin($user) || $asset->owner_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Asset $asset): bool
    {
        return $asset->owner_id === $user->id || $this->isAdmin($user);
    }

    public function update(User $user, Asset $asset): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return $asset->owner_id === $user->id
            && $asset->status !== AssetStatus::Archived;
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
