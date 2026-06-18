<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('dha_settings')) {
            return;
        }

        DB::table('dha_settings')
            ->where(function ($q) {
                $q->whereNull('phases_heading_white')
                    ->orWhere('phases_heading_white', '')
                    ->orWhere('phases_heading_white', 'PHASES');
            })
            ->update(['phases_heading_white' => 'PROJECTS']);

        DB::table('dha_settings')
            ->where(function ($q) {
                $q->whereNull('hero_btn_primary_label')
                    ->orWhere('hero_btn_primary_label', '')
                    ->orWhere('hero_btn_primary_label', 'EXPLORE PHASES');
            })
            ->update(['hero_btn_primary_label' => 'EXPLORE PROJECTS']);

        DB::table('dha_settings')
            ->where(function ($q) {
                $q->whereNull('cta_btn_primary_label')
                    ->orWhere('cta_btn_primary_label', '')
                    ->orWhere('cta_btn_primary_label', 'EXPLORE PHASES');
            })
            ->update(['cta_btn_primary_label' => 'EXPLORE PROJECTS']);
    }

    public function down(): void
    {
        // Labels are content; no automatic rollback.
    }
};
