<?php

namespace App\Policies;

use App\Models\AssetCategory;
use App\Models\User;

class AssetCategoryPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('super_admin') || $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, AssetCategory $category): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AssetCategory $category): bool
    {
        return false;
    }

    public function delete(User $user, AssetCategory $category): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
