<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'type',
        'url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const TYPE_TRUSTED = 'trusted';
    public const TYPE_MEDIA = 'media';
    public const TYPE_PARTNER = 'partner';
    public const TYPE_CLIENT = 'client';

    public const TYPE_OPTIONS = [
        self::TYPE_TRUSTED => 'Trusted By',
        self::TYPE_MEDIA => 'Media Covered',
        self::TYPE_PARTNER => 'Strategic Partner',
        self::TYPE_CLIENT => 'Our Clients',
    ];

    /**
     * Scope for filtering by type
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for trusted brands (Industry Leaders)
     */
    public function scopeTrusted(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_TRUSTED);
    }

    /**
     * Scope for media brands (Media Covered)
     */
    public function scopeMedia(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_MEDIA);
    }

    /**
     * Scope for partner brands (Strategic Partner)
     */
    public function scopePartner(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_PARTNER);
    }

    /**
     * Scope for client brands (Our Clients)
     */
    public function scopeClient(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_CLIENT);
    }

    /**
     * Scope for active brands only
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered by sort_order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
