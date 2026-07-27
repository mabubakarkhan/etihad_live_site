<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('dha_phases', 'investment_scorecard')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->json('investment_scorecard')->nullable()->after('featured_agent_ids');
            });
        }

        $dummy = [
            ['label' => 'Rental Yield', 'score' => 8, 'icon' => 'trending-up'],
            ['label' => 'Development', 'score' => 10, 'icon' => 'building-2'],
            ['label' => 'Commercial Activity', 'score' => 9, 'icon' => 'store'],
            ['label' => 'Appreciation Potential', 'score' => 8, 'icon' => 'line-chart'],
            ['label' => 'Family Living', 'score' => 9, 'icon' => 'home'],
        ];

        $phase = DB::table('dha_phases')
            ->where('slug', 'like', '%phase-1%')
            ->orWhere('title', 'like', '%Phase 1%')
            ->orderBy('id')
            ->first();

        if ($phase) {
            DB::table('dha_phases')->where('id', $phase->id)->update([
                'investment_scorecard' => json_encode($dummy),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dha_phases', 'investment_scorecard')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->dropColumn('investment_scorecard');
            });
        }
    }
};
