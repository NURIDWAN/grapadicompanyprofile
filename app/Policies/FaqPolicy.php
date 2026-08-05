<?php

namespace App\Policies;

use App\Models\Faq;
use App\Models\User;

class FaqPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_faq');
    }

    public function view(User $user, Faq $faq): bool
    {
        return $user->can('view_faq');
    }

    public function create(User $user): bool
    {
        return $user->can('create_faq');
    }

    public function update(User $user, Faq $faq): bool
    {
        return $user->can('update_faq');
    }

    public function delete(User $user, Faq $faq): bool
    {
        return $user->can('delete_faq');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_faq');
    }

    public function forceDelete(User $user, Faq $faq): bool
    {
        return $user->can('force_delete_faq');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_faq');
    }

    public function restore(User $user, Faq $faq): bool
    {
        return $user->can('restore_faq');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_faq');
    }

    public function replicate(User $user, Faq $faq): bool
    {
        return $user->can('replicate_faq');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_faq');
    }
}
