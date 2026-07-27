<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('dha_phases', 'nearby_facilities')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->json('nearby_facilities')->nullable()->after('quick_stats');
            });
        }

        $now = now();
        $phases = DB::table('dha_phases')->select(['id', 'latitude', 'longitude', 'title'])->get();

        foreach ($phases as $phase) {
            $lat = $phase->latitude !== null ? (float) $phase->latitude : 31.476723;
            $lng = $phase->longitude !== null ? (float) $phase->longitude : 74.384087;

            $facilities = [
                ['category' => 'Schools', 'name' => 'DHA Junior School', 'lat' => $lat + 0.0042, 'lng' => $lng + 0.0031],
                ['category' => 'Schools', 'name' => 'Beaconhouse DHA Campus', 'lat' => $lat - 0.0038, 'lng' => $lng + 0.0055],
                ['category' => 'Mosques', 'name' => 'Central Mosque DHA', 'lat' => $lat + 0.0018, 'lng' => $lng - 0.0024],
                ['category' => 'Mosques', 'name' => 'Masjid-e-Noor', 'lat' => $lat - 0.0026, 'lng' => $lng - 0.0041],
                ['category' => 'Markets', 'name' => 'Y Block Market', 'lat' => $lat + 0.0051, 'lng' => $lng - 0.0012],
                ['category' => 'Markets', 'name' => 'MM Alam Commercial Hub', 'lat' => $lat - 0.0015, 'lng' => $lng + 0.0062],
                ['category' => 'Hospitals', 'name' => 'DHA Medical Centre', 'lat' => $lat + 0.0029, 'lng' => $lng + 0.0048],
                ['category' => 'Hospitals', 'name' => 'National Hospital DHA', 'lat' => $lat - 0.0047, 'lng' => $lng + 0.0019],
                ['category' => 'Parks', 'name' => 'DHA Central Park', 'lat' => $lat + 0.0009, 'lng' => $lng + 0.0022],
                ['category' => 'Parks', 'name' => 'Canal View Garden', 'lat' => $lat - 0.0031, 'lng' => $lng - 0.0036],
            ];

            DB::table('dha_phases')->where('id', $phase->id)->update([
                'nearby_facilities' => json_encode($facilities),
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('dha_phases', 'nearby_facilities')) {
            Schema::table('dha_phases', function (Blueprint $table) {
                $table->dropColumn('nearby_facilities');
            });
        }
    }
};
