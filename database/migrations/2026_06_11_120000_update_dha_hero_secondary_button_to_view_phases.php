<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('dha_settings')) {
            return;
        }

        DB::table('dha_settings')->update([
            'hero_btn_secondary_label' => 'VIEW PHASES',
            'hero_btn_secondary_url' => '#dha-phases',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('dha_settings')) {
            return;
        }

        DB::table('dha_settings')->update([
            'hero_btn_secondary_label' => 'VIEW MASTER PLAN',
            'hero_btn_secondary_url' => '#',
            'updated_at' => now(),
        ]);
    }
};
