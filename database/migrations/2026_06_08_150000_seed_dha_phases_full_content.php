<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('dha_phases')) {
            return;
        }

        $seedFile = database_path('data/dha_phases_seed.php');
        if (! is_file($seedFile)) {
            return;
        }

        /** @var list<array<string, mixed>> $phases */
        $phases = require $seedFile;
        $now = now();

        foreach ($phases as $row) {
            $slug = (string) ($row['slug'] ?? '');
            if ($slug === '') {
                continue;
            }

            $payload = [
                'title' => $row['title'],
                'sort_order' => (int) ($row['sort_order'] ?? 0),
                'description' => $row['description'] ?? null,
                'hero_lead' => $row['hero_lead'] ?? null,
                'stat_location' => $row['stat_location'] ?? null,
                'stat_total_area' => $row['stat_total_area'] ?? null,
                'stat_total_plots' => $row['stat_total_plots'] ?? null,
                'stat_year_developed' => $row['stat_year_developed'] ?? null,
                'features_content' => $row['features_content'] ?? null,
                'market_insights' => $row['market_insights'] ?? null,
                'contact_intro' => $row['contact_intro'] ?? null,
                'attractions_heading' => $row['attractions_heading'] ?? null,
                'help_bar_eyebrow' => $row['help_bar_eyebrow'] ?? null,
                'help_bar_title' => $row['help_bar_title'] ?? null,
                'help_bar_text' => $row['help_bar_text'] ?? null,
                'latitude' => $row['latitude'] ?? null,
                'longitude' => $row['longitude'] ?? null,
                'map_zoom' => (int) ($row['map_zoom'] ?? 14),
                'meta_title' => $row['meta_title'] ?? null,
                'meta_description' => $row['meta_description'] ?? null,
                'meta_keywords' => $row['meta_keywords'] ?? null,
                'status' => $row['status'] ?? 'active',
                'updated_at' => $now,
            ];

            foreach (['value_propositions', 'attractions', 'investment_reasons', 'project_highlights'] as $jsonCol) {
                if (array_key_exists($jsonCol, $row) && is_array($row[$jsonCol])) {
                    $payload[$jsonCol] = json_encode($row[$jsonCol], JSON_UNESCAPED_UNICODE);
                }
            }

            $existing = DB::table('dha_phases')->where('slug', $slug)->first();

            if ($existing) {
                DB::table('dha_phases')->where('slug', $slug)->update($payload);
            } else {
                $payload['slug'] = $slug;
                $payload['created_at'] = $now;
                DB::table('dha_phases')->insert($payload);
            }
        }
    }

    public function down(): void
    {
        // Content seed; no automatic rollback.
    }
};
