<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Facility extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Facility $facility) {
            $facility->slug = Str::slug($facility->slug ?: $facility->name);
        });
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class);
    }
}
