<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetPhoto extends Model
{
    protected $fillable = ['asset_id', 'path', 'sort_order'];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
