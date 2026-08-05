<?php

namespace App\Services;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\User;
use App\Notifications\AssetReviewResultNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssetReviewService
{
    public function review(Asset $asset, User $reviewer, array $checklist, ?string $notes = null): Asset
    {
        if ($asset->status !== AssetStatus::PendingReview) {
            throw ValidationException::withMessages(['status' => 'Hanya aset yang menunggu review yang dapat diproses.']);
        }
        $fields = ['data_complete', 'basic_legality', 'photos_adequate', 'publishable'];
        $passed = collect($fields)->every(fn ($field) => (bool) ($checklist[$field] ?? false));
        if (! $passed && blank($notes)) {
            throw ValidationException::withMessages(['notes' => 'Catatan wajib diisi jika aset memerlukan revisi.']);
        }
        $status = $passed ? AssetStatus::Published : AssetStatus::RevisionRequired;

        DB::transaction(function () use ($asset, $reviewer, $checklist, $notes, $status, $fields) {
            $asset->reviews()->create([
                ...collect($checklist)->only($fields)->all(), 'reviewer_id' => $reviewer->id,
                'decision' => $status->value, 'notes' => $notes,
            ]);
            $asset->update([
                'status' => $status, 'latest_review_notes' => $notes, 'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(), 'published_at' => $status === AssetStatus::Published ? now() : null,
                'slug_locked_at' => $status === AssetStatus::Published ? ($asset->slug_locked_at ?: now()) : $asset->slug_locked_at,
            ]);
        });

        try {
            $asset->owner->notify(new AssetReviewResultNotification($asset->fresh()));
        } catch (\Throwable $e) {
            report($e);
        }

        return $asset->fresh();
    }
}
