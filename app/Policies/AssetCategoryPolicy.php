<?php

namespace App\Policies;

use App\Models\AssetCategory;
use App\Models\User;

class AssetCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asset::category');
    }

    public function view(User $user, AssetCategory $category): bool
    {
        return $user->can('view_asset::category');
    }

    public function create(User $user): bool
    {
        return $user->can('create_asset::category');
    }

    public function update(User $user, AssetCategory $category): bool
    {
        return $user->can('update_asset::category');
    }

    public function delete(User $user, AssetCategory $category): bool
    {
        return $user->can('delete_asset::category');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_asset::category');
    }

    public function forceDelete(User $user, AssetCategory $category): bool
    {
        return $user->can('force_delete_asset::category');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_asset::category');
    }

    public function restore(User $user, AssetCategory $category): bool
    {
        return $user->can('restore_asset::category');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_asset::category');
    }

    public function replicate(User $user, AssetCategory $category): bool
    {
        return $user->can('replicate_asset::category');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_asset::category');
    }
}
