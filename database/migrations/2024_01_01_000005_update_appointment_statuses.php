<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('appointments')
            ->where('status', 'scheduled')
            ->update(['status' => 'reserved']);
    }

    public function down(): void
    {
        DB::table('appointments')
            ->where('status', 'reserved')
            ->update(['status' => 'scheduled']);
    }
};
