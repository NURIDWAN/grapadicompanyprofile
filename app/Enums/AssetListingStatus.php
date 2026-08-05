<?php

namespace App\Enums;

enum AssetListingStatus: string
{
    case Available = 'available';
    case Negotiation = 'negotiation';
    case Closed = 'closed';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Tersedia',
            self::Negotiation => 'Dalam Negosiasi',
            self::Closed => 'Terjual/Tersewa/Selesai',
            self::Inactive => 'Tidak Aktif',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $status) => [$status->value => $status->label()])->all();
    }
}
