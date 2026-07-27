<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('dha_phases', 'quick_stats')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->json('quick_stats')->nullable()->after('project_highlights');
            });
        }

        $now = now();

        $phases = DB::table('dha_phases')->select(['id', 'title', 'stat_location'])->get();
        foreach ($phases as $phase) {
            $location = trim((string) ($phase->stat_location ?? '')) ?: 'Lahore, Pakistan';
            $stats = [
                [
                    'icon' => 'map-pin',
                    'title' => 'Location',
                    'text' => $location,
                ],
                [
                    'icon' => 'building-2',
                    'title' => 'Total Blocks',
                    'text' => '12 Blocks',
                ],
                [
                    'icon' => 'trending-up',
                    'title' => 'Current Market Trend',
                    'text' => 'Rising Demand',
                ],
                [
                    'icon' => 'banknote',
                    'title' => 'Starting Plot Price',
                    'text' => 'From PKR 2.5 Cr',
                ],
                [
                    'icon' => 'home',
                    'title' => 'Available Houses',
                    'text' => '120+',
                ],
                [
                    'icon' => 'user-round',
                    'title' => 'Active Agents',
                    'text' => '45+',
                ],
            ];

            DB::table('dha_phases')->where('id', $phase->id)->update([
                'quick_stats' => json_encode($stats),
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dha_phases', 'quick_stats')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->dropColumn('quick_stats');
            });
        }
    }
};
