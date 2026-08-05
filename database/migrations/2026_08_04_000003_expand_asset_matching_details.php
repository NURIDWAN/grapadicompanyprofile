<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('slug', 180)->nullable()->after('public_id');
            $table->string('listing_status', 30)->default('available')->index()->after('name');
            $table->decimal('price', 18, 2)->nullable()->after('area_sqm');
            $table->string('district', 100)->nullable()->after('city');
            $table->string('village', 100)->nullable()->after('district');
            $table->text('google_maps_url')->nullable()->after('full_address');
            $table->text('description')->nullable()->after('google_maps_url');
            $table->string('seo_title', 180)->nullable()->after('objective');
            $table->string('meta_description', 255)->nullable()->after('seo_title');
            $table->timestamp('slug_locked_at')->nullable()->after('meta_description');
            $table->string('ownership_status', 30)->nullable()->change();
        });

        DB::table('assets')->orderBy('id')->get(['id', 'name', 'city', 'province', 'area_sqm'])->each(function ($asset) {
            $base = Str::slug($asset->name) ?: 'aset-'.$asset->id;
            $slug = $base;
            $counter = 2;
            while (DB::table('assets')->where('slug', $slug)->exists()) {
                $slug = $base.'-'.$counter++;
            }
            $title = Str::limit($asset->name.' - Aset di '.$asset->city.' | Grapadi', 180, '');
            $description = Str::limit('Temukan '.$asset->name.' seluas '.number_format((float) $asset->area_sqm, 0, ',', '.').' m² di '.$asset->city.', '.$asset->province.'. Hubungi Grapadi untuk informasi lebih lanjut.', 160, '');
            DB::table('assets')->where('id', $asset->id)->update(['slug' => $slug, 'seo_title' => $title, 'meta_description' => $description]);
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->unique('slug');
        });

        Schema::table('asset_photos', function (Blueprint $table) {
            $table->string('alt_text', 180)->nullable()->after('path');
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('icon', 60)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('asset_facility', function (Blueprint $table) {
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->primary(['asset_id', 'facility_id']);
        });

        Schema::create('asset_slug_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 180)->unique();
            $table->timestamps();
        });

        $now = now();
        DB::table('facilities')->insert(collect([
            ['Listrik', 'bolt'], ['Air Bersih', 'water_drop'], ['Akses Jalan', 'add_road'],
            ['Parkir', 'local_parking'], ['Keamanan', 'shield'], ['Internet', 'wifi'],
            ['Drainase', 'water'], ['Loading Dock', 'move_to_inbox'],
        ])->map(fn ($item, $index) => [
            'name' => $item[0], 'slug' => Str::slug($item[0]), 'icon' => $item[1],
            'is_active' => true, 'sort_order' => $index + 1, 'created_at' => $now, 'updated_at' => $now,
        ])->all());
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_slug_histories');
        Schema::dropIfExists('asset_facility');
        Schema::dropIfExists('facilities');
        Schema::table('asset_photos', fn (Blueprint $table) => $table->dropColumn('alt_text'));
        Schema::table('assets', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropIndex(['listing_status']);
            $table->dropColumn(['slug', 'listing_status', 'price', 'district', 'village', 'google_maps_url', 'description', 'seo_title', 'meta_description', 'slug_locked_at']);
        });
    }
};
