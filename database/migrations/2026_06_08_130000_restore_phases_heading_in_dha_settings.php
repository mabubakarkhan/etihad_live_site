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

        DB::table('dha_settings')
            ->where(function ($q) {
                $q->whereNull('phases_heading_white')
                    ->orWhere('phases_heading_white', '')
                    ->orWhere('phases_heading_white', 'PROJECTS');
            })
            ->update(['phases_heading_white' => 'PHASES']);
    }

    public function down(): void
    {
        // Content label; no automatic rollback.
    }
};
