<?php

namespace App\Enums;

enum AssetOwnershipStatus: string
{
    case Personal = 'personal';
    case Company = 'company';
    case Joint = 'joint';
    case Representative = 'representative';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Personal => 'Pribadi', self::Company => 'Badan Usaha',
            self::Joint => 'Bersama', self::Representative => 'Dalam Kuasa/Perwakilan',
            self::Other => 'Lainnya',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
