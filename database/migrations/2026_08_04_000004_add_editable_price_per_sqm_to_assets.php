<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->decimal('price_per_sqm', 18, 2)->nullable()->after('price');
        });

        DB::table('assets')
            ->whereNotNull('price')
            ->where('area_sqm', '>', 0)
            ->update(['price_per_sqm' => DB::raw('price / area_sqm')]);
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('price_per_sqm');
        });
    }
};
