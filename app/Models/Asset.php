<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetListingStatus;
use App\Enums\AssetObjective;
use App\Enums\AssetOwnershipStatus;
use App\Enums\AssetStatus;
use App\Enums\AssetUtilizationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Asset extends Model
{
    protected $fillable = [
        'owner_id', 'asset_category_id', 'name', 'slug', 'listing_status', 'province', 'city', 'district',
        'village', 'full_address', 'google_maps_url', 'description', 'area_sqm', 'price', 'price_per_sqm',
        'certificate_type', 'condition', 'condition_notes',
        'ownership_status', 'ownership_notes', 'utilization_status', 'utilization_notes', 'objective',
        'seo_title', 'meta_description', 'slug_locked_at', 'status', 'latest_review_notes', 'reviewed_by',
        'reviewed_at', 'submitted_at', 'published_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {
            $asset->public_id ??= (string) Str::uuid();
            $asset->slug = static::uniqueSlug($asset->slug ?: $asset->name);
            if ($asset->price_per_sqm === null) {
                $asset->setAutomaticPricePerSquareMeter();
            }
            $asset->fillAutomaticSeo();
        });
        static::updating(function (Asset $asset) {
            if ($asset->isDirty('slug')) {
                $asset->slug = static::uniqueSlug($asset->slug, $asset->id);
                $original = $asset->getOriginal('slug');
                if ($original) {
                    AssetSlugHistory::firstOrCreate(['slug' => $original], ['asset_id' => $asset->id]);
                }
            }
            if (($asset->isDirty('price') || $asset->isDirty('area_sqm')) && ! $asset->isDirty('price_per_sqm')) {
                $asset->setAutomaticPricePerSquareMeter();
            }
            $asset->fillAutomaticSeo();
        });
    }

    protected function casts(): array
    {
        return [
            'area_sqm' => 'decimal:2', 'price' => 'decimal:2', 'price_per_sqm' => 'decimal:2', 'condition' => AssetCondition::class,
            'listing_status' => AssetListingStatus::class,
            'ownership_status' => AssetOwnershipStatus::class, 'utilization_status' => AssetUtilizationStatus::class,
            'objective' => AssetObjective::class, 'status' => AssetStatus::class,
            'reviewed_at' => 'datetime', 'submitted_at' => 'datetime', 'published_at' => 'datetime', 'slug_locked_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?: 'slug', $value)->orWhere('public_id', $value)->first();
    }

    public static function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'aset';
        $slug = $base;
        $counter = 2;
        while (static::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    public function fillAutomaticSeo(): void
    {
        $location = $this->city ?: $this->province;
        $type = $this->relationLoaded('category') ? $this->category?->name : AssetCategory::find($this->asset_category_id)?->name;
        $this->seo_title = Str::limit(collect([$this->name, $type ? $type.' di '.$location : 'Aset di '.$location, 'Grapadi'])->filter()->implode(' | '), 180, '');
        $area = $this->area_sqm ? number_format((float) $this->area_sqm, 0, ',', '.').' m²' : null;
        $this->meta_description = Str::limit('Temukan '.collect([$this->name, $type, $area, $this->village, $location, $this->province])->filter()->implode(', ').'. Hubungi Grapadi untuk informasi lebih lanjut.', 160, '');
    }

    public function setAutomaticPricePerSquareMeter(): void
    {
        $this->price_per_sqm = $this->price !== null && (float) $this->area_sqm > 0
            ? (float) $this->price / (float) $this->area_sqm
            : null;
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

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class)->orderBy('sort_order');
    }

    public function slugHistories(): HasMany
    {
        return $this->hasMany(AssetSlugHistory::class);
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

    public function scopePubliclyListed(Builder $query): Builder
    {
        return $query->published()->where('listing_status', '!=', AssetListingStatus::Inactive->value);
    }
}
