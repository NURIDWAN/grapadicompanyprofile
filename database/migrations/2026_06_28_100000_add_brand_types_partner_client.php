<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change enum to include new types
        DB::statement("ALTER TABLE brands MODIFY COLUMN type ENUM('trusted', 'media', 'partner', 'client') DEFAULT 'trusted'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE brands MODIFY COLUMN type ENUM('trusted', 'media') DEFAULT 'trusted'");
    }
};
