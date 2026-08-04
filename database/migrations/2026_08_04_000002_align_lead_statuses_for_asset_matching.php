<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('new','contacted','meeting','proposal','negotiation','deal','rejected') DEFAULT 'new'");
        }
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::table('leads')->whereIn('status', ['contacted', 'meeting', 'proposal'])->update(['status' => 'new']);
            DB::statement("ALTER TABLE leads MODIFY COLUMN status ENUM('new','in_progress','negotiation','deal','rejected') DEFAULT 'new'");
        }
    }
};
