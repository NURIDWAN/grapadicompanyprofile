<?php

namespace App\Enums;

enum AssetCondition: string
{
    case Excellent = 'excellent';
    case Good = 'good';
    case NeedsRenovation = 'needs_renovation';
    case Damaged = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::Excellent => 'Sangat Baik', self::Good => 'Baik',
            self::NeedsRenovation => 'Perlu Renovasi', self::Damaged => 'Rusak',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
