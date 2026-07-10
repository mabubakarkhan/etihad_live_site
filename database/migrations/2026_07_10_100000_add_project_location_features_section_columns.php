<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @return array<int, array{title: string, icon: string}> */
    private function defaultUniqueFeatures(): array
    {
        return [
            ['title' => 'Swimming Pool', 'icon' => 'fa-water-ladder'],
            ['title' => 'Rooftop Lounge', 'icon' => 'fa-building'],
            ['title' => 'Gym & Fitness', 'icon' => 'fa-dumbbell'],
            ['title' => 'CCTV Surveillance', 'icon' => 'fa-camera-cctv'],
            ['title' => "Children's Play Area", 'icon' => 'fa-child-reaching'],
            ['title' => 'Ample Parking', 'icon' => 'fa-square-parking'],
            ['title' => 'Community Mosque', 'icon' => 'fa-mosque'],
            ['title' => 'Power Backup', 'icon' => 'fa-bolt'],
        ];
    }

    /** @return array<int, string> */
    private function defaultLocationHighlights(?string $city): array
    {
        $cityLabel = trim((string) $city) !== '' ? trim((string) $city) : 'City Centre';

        return [
            '5 mins to ' . $cityLabel,
            '10 mins to Main Boulevard',
            'Close to top schools',
            'Near hospitals & malls',
        ];
    }

    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('amenities_section_heading')->nullable()->after('unique_features');
            $table->string('location_section_heading')->nullable()->after('amenities_section_heading');
            $table->text('location_section_description')->nullable()->after('location_section_heading');
            $table->json('location_highlights')->nullable()->after('location_section_description');
            $table->string('key_features_section_heading')->nullable()->after('location_highlights');
        });

        $defaultFeatures = $this->defaultUniqueFeatures();

        Project::query()->orderBy('id')->chunkById(100, function ($projects) use ($defaultFeatures): void {
            foreach ($projects as $project) {
                $updates = [];

                if (! is_string($project->amenities_section_heading) || trim($project->amenities_section_heading) === '') {
                    $updates['amenities_section_heading'] = 'World-Class Amenities';
                }

                if (! is_string($project->location_section_heading) || trim($project->location_section_heading) === '') {
                    $updates['location_section_heading'] = 'Prime Location';
                }

                if (! is_string($project->key_features_section_heading) || trim($project->key_features_section_heading) === '') {
                    $updates['key_features_section_heading'] = 'Key Features';
                }

                $highlights = $project->location_highlights;
                if (! is_array($highlights) || $highlights === []) {
                    $updates['location_highlights'] = $this->defaultLocationHighlights($project->city);
                }

                $features = $project->unique_features;
                if (! is_array($features) || $features === []) {
                    $updates['unique_features'] = $defaultFeatures;
                }

                if ($updates !== []) {
                    $project->forceFill($updates)->save();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'amenities_section_heading',
                'location_section_heading',
                'location_section_description',
                'location_highlights',
                'key_features_section_heading',
            ]);
        });
    }
};
