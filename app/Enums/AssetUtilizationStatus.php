<?php

namespace App\Enums;

enum AssetUtilizationStatus: string
{
    case Vacant = 'vacant';
    case SelfUsed = 'self_used';
    case Rented = 'rented';
    case Operated = 'operated';
    case InDevelopment = 'in_development';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Vacant => 'Belum Dimanfaatkan', self::SelfUsed => 'Digunakan Sendiri',
            self::Rented => 'Disewakan', self::Operated => 'Dioperasikan',
            self::InDevelopment => 'Dalam Pengembangan', self::Other => 'Lainnya',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
