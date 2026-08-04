<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        // Change enum to include new types
        DB::statement("ALTER TABLE brands MODIFY COLUMN type ENUM('trusted', 'media', 'partner', 'client') DEFAULT 'trusted'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE brands MODIFY COLUMN type ENUM('trusted', 'media') DEFAULT 'trusted'");
    }
};
