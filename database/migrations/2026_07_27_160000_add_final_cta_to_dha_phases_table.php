<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('dha_phases', 'final_cta')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->json('final_cta')->nullable()->after('nearby_landmarks');
            });
        }

        $dummy = [
            'heading' => 'Need Expert Advice About DHA Phase 1?',
            'benefits' => [
                'Latest Prices',
                'Updated Inventory',
                'Investment Consultation',
                'Verified Property Options',
            ],
            'cta_label' => 'Get Property Consultation',
        ];

        $phase = DB::table('dha_phases')
            ->where('slug', 'like', '%phase-1%')
            ->orWhere('title', 'like', '%Phase 1%')
            ->orderBy('id')
            ->first();

        if ($phase) {
            DB::table('dha_phases')->where('id', $phase->id)->update([
                'final_cta' => json_encode($dummy),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dha_phases', 'final_cta')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->dropColumn('final_cta');
            });
        }
    }
};
