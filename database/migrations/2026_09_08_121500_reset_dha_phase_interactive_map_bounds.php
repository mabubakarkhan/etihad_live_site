<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Wipe interactive map plot cuttings and seed/reset main gold-box bounds
 * for each Lahore DHA phase from the phase center coordinates.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interactive_maps')) {
            return;
        }

        // Remove all plot / cutting records.
        if (Schema::hasTable('interactive_map_sections')) {
            DB::table('interactive_map_sections')->delete();
        }

        if (! Schema::hasTable('dha_phases')) {
            return;
        }

        $defaults = config('interactive_map.defaults', []);
        $now = now();

        $phases = DB::table('dha_phases')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'latitude', 'longitude', 'map_zoom']);

        foreach ($phases as $phase) {
            $lat = is_numeric($phase->latitude) ? (float) $phase->latitude : null;
            $lng = is_numeric($phase->longitude) ? (float) $phase->longitude : null;

            if ($lat === null || $lng === null) {
                continue;
            }

            // ~2km×2km starter box around each DHA phase center (Lahore).
            $halfLat = 0.0090;
            $halfLng = 0.0110;

            $north = round($lat + $halfLat, 7);
            $south = round($lat - $halfLat, 7);
            $east = round($lng + $halfLng, 7);
            $west = round($lng - $halfLng, 7);

            $path = [
                ['lat' => $north, 'lng' => $west],
                ['lat' => $north, 'lng' => $east],
                ['lat' => $south, 'lng' => $east],
                ['lat' => $south, 'lng' => $west],
            ];

            $zoom = is_numeric($phase->map_zoom) ? (int) $phase->map_zoom : (int) ($defaults['default_zoom'] ?? 15);
            $zoom = max(12, min(18, $zoom));

            $payload = [
                'north' => $north,
                'south' => $south,
                'east' => $east,
                'west' => $west,
                'default_zoom' => $zoom,
                'min_zoom' => (int) ($defaults['min_zoom'] ?? 10),
                'max_zoom' => (int) ($defaults['max_zoom'] ?? 20),
                'overlay_opacity' => (float) ($defaults['overlay_opacity'] ?? 0.85),
                'overlay_rotation' => (float) ($defaults['overlay_rotation'] ?? 0),
                'overlay_visibility_zoom' => $defaults['overlay_visibility_zoom'] ?? 14,
                'show_label_from_zoom' => $defaults['show_label_from_zoom'] ?? 16,
                'is_active' => (bool) ($defaults['is_active'] ?? true),
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('interactive_maps', 'overlay_path')) {
                $payload['overlay_path'] = json_encode($path);
            }

            $existing = DB::table('interactive_maps')
                ->where('dha_phase_id', $phase->id)
                ->first();

            if ($existing) {
                DB::table('interactive_maps')
                    ->where('id', $existing->id)
                    ->update($payload);
            } else {
                $payload['dha_phase_id'] = $phase->id;
                $payload['project_id'] = null;
                $payload['overlay_image_path'] = null;
                $payload['created_at'] = $now;
                DB::table('interactive_maps')->insert($payload);
            }
        }
    }

    public function down(): void
    {
        // Non-destructive: plot wipe and bound reset are not reversed.
    }
};
