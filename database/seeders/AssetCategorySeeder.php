<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Tanah', 'Bangunan Komersial', 'Hunian', 'Hotel/Resort', 'Pabrik/Gudang', 'Perkebunan/Pertanian', 'Lainnya'] as $name) {
            AssetCategory::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'is_active' => true]);
        }
    }
}
