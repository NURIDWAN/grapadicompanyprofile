<?php

namespace App\Enums;

enum AssetStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case RevisionRequired = 'revision_required';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::PendingReview => 'Menunggu Review',
            self::RevisionRequired => 'Perlu Revisi',
            self::Published => 'Published',
            self::Archived => 'Diarsipkan',
        };
    }
}
