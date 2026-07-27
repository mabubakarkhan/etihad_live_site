<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('dha_phases', 'nearby_landmarks')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->json('nearby_landmarks')->nullable()->after('investment_scorecard');
            });
        }

        $dummy = [
            ['icon' => 'plane', 'title' => 'Airport Distance', 'text' => '25 min to Allama Iqbal International Airport'],
            ['icon' => 'route', 'title' => 'Ring Road Access', 'text' => 'Direct connectivity via Lahore Ring Road'],
            ['icon' => 'shopping-bag', 'title' => 'Packages Mall', 'text' => '12 min drive to Packages Mall'],
            ['icon' => 'building-2', 'title' => 'Raya', 'text' => '10 min to Raya commercial corridor'],
            ['icon' => 'landmark', 'title' => 'Main Boulevard', 'text' => 'Easy access to Main Boulevard DHA'],
        ];

        $phase = DB::table('dha_phases')
            ->where('slug', 'like', '%phase-1%')
            ->orWhere('title', 'like', '%Phase 1%')
            ->orderBy('id')
            ->first();

        if ($phase) {
            DB::table('dha_phases')->where('id', $phase->id)->update([
                'nearby_landmarks' => json_encode($dummy),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dha_phases', 'nearby_landmarks')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->dropColumn('nearby_landmarks');
            });
        }
    }
};
