<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetSlugHistory extends Model
{
    protected $fillable = ['asset_id', 'slug'];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
