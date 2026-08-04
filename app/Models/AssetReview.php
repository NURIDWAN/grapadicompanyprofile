<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetReview extends Model
{
    protected $fillable = ['asset_id', 'reviewer_id', 'data_complete', 'basic_legality', 'photos_adequate', 'publishable', 'decision', 'notes'];

    protected $casts = ['data_complete' => 'boolean', 'basic_legality' => 'boolean', 'photos_adequate' => 'boolean', 'publishable' => 'boolean'];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
