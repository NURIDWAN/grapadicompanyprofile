<?php

namespace App\Enums;

enum AssetObjective: string
{
    case Sell = 'sell';
    case Partnership = 'partnership';
    case FindInvestor = 'find_investor';
    case FindOperator = 'find_operator';
    case Undecided = 'undecided';

    public function label(): string
    {
        return match ($this) {
            self::Sell => 'Dijual',
            self::Partnership => 'Kerja Sama',
            self::FindInvestor => 'Mencari Investor',
            self::FindOperator => 'Mencari Operator',
            self::Undecided => 'Belum Menentukan',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case) => [$case->value => $case->label()])->all();
    }
}
