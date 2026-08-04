<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetObjective;
use App\Enums\AssetOwnershipStatus;
use App\Enums\AssetStatus;
use App\Enums\AssetUtilizationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Asset extends Model
{
    protected $fillable = [
        'owner_id', 'asset_category_id', 'name', 'province', 'city', 'full_address', 'area_sqm',
        'certificate_type', 'certificate_number', 'certificate_file', 'condition', 'condition_notes',
        'ownership_status', 'ownership_notes', 'utilization_status', 'utilization_notes', 'objective',
        'status', 'latest_review_notes', 'reviewed_by', 'reviewed_at', 'submitted_at', 'published_at',
    ];

    protected static function booted(): void
    {
        static::creating(fn (Asset $asset) => $asset->public_id ??= (string) Str::uuid());
    }

    protected function casts(): array
    {
        return [
            'area_sqm' => 'decimal:2', 'condition' => AssetCondition::class,
            'ownership_status' => AssetOwnershipStatus::class, 'utilization_status' => AssetUtilizationStatus::class,
            'objective' => AssetObjective::class, 'status' => AssetStatus::class,
            'reviewed_at' => 'datetime', 'submitted_at' => 'datetime', 'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AssetPhoto::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AssetReview::class)->latest();
    }

    public function interests(): HasMany
    {
        return $this->hasMany(AssetInterest::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', AssetStatus::Published->value);
    }
}
