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
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('asset_categories')->insert(collect([
            'Tanah', 'Bangunan Komersial', 'Hunian', 'Hotel/Resort', 'Pabrik/Gudang',
            'Perkebunan/Pertanian', 'Lainnya',
        ])->map(fn (string $name) => [
            'name' => $name, 'slug' => Str::slug($name), 'is_active' => true,
            'created_at' => $now, 'updated_at' => $now,
        ])->all());

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('asset_category_id')->constrained()->restrictOnDelete();
            $table->uuid('public_id')->unique();
            $table->string('name', 150);
            $table->string('province', 100);
            $table->string('city', 100);
            $table->text('full_address');
            $table->decimal('area_sqm', 15, 2);
            $table->string('certificate_type', 50);
            $table->string('certificate_number', 100);
            $table->string('certificate_file');
            $table->string('condition', 30);
            $table->text('condition_notes')->nullable();
            $table->string('ownership_status', 30);
            $table->text('ownership_notes')->nullable();
            $table->string('utilization_status', 30);
            $table->text('utilization_notes')->nullable();
            $table->string('objective', 30);
            $table->string('status', 30)->default('draft')->index();
            $table->text('latest_review_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('asset_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->boolean('data_complete');
            $table->boolean('basic_legality');
            $table->boolean('photos_adequate');
            $table->boolean('publishable');
            $table->string('decision', 30);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['asset_id', 'user_id']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('asset_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('asset_id');
        });
        Schema::dropIfExists('asset_interests');
        Schema::dropIfExists('asset_reviews');
        Schema::dropIfExists('asset_photos');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
    }
};
