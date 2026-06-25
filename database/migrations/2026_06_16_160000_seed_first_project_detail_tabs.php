<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $project = Project::query()
            ->where('title', 'First')
            ->orWhere('slug', 'first')
            ->first();

        if (! $project) {
            return;
        }

        if (! empty($project->project_detail_tabs)) {
            return;
        }

        $images = [];
        if (! empty($project->featured_image)) {
            $images[] = $project->featured_image;
        }
        $gallery = is_array($project->image_gallery) ? $project->image_gallery : [];
        foreach ($gallery as $item) {
            $path = is_array($item) ? trim((string) ($item['path'] ?? '')) : trim((string) $item);
            if ($path !== '' && ! in_array($path, $images, true)) {
                $images[] = $path;
            }
            if (count($images) >= 4) {
                break;
            }
        }

        $tabImages = static function (int $start, int $count) use ($images): array {
            if ($images === []) {
                return [];
            }

            return array_values(array_slice($images, $start, $count));
        };

        $project->project_detail_tabs = [
            [
                'label' => 'Owner',
                'icon' => 'fa-user',
                'heading' => 'Registered Owner Information',
                'detail' => '<p>Review verified ownership and registration details for this development. Our team confirms documentation before listings go live.</p>',
                'bullets' => 'Faisal Town 1, Faisal Town 2, Faisal Town 3, Faisal Town 4',
                'images' => $tabImages(0, 1),
            ],
            [
                'label' => 'Developer',
                'icon' => 'fa-building',
                'heading' => 'Developer Profile',
                'detail' => '<p>Learn about the developer behind this project, delivery track record, and planned infrastructure upgrades.</p>',
                'bullets' => 'Master developer, Approved NOC, On-site sales office',
                'images' => $tabImages(1, 2),
            ],
            [
                'label' => 'Master Plan',
                'icon' => 'fa-up-right-and-down-left-from-center',
                'heading' => 'Master Plan Overview',
                'detail' => '<p>Explore sector layout, road networks, commercial corridors, and amenity zones across the master plan.</p>',
                'bullets' => 'Residential blocks, Commercial zones, Parks and green belts, Mosque and community areas',
                'images' => $tabImages(0, 3),
            ],
            [
                'label' => 'Payment Plan',
                'icon' => 'fa-credit-card',
                'heading' => 'Flexible Payment Options',
                'detail' => '<p>Flexible installment schedules are available for select inventory. Contact our experts for the latest offers and booking terms.</p>',
                'bullets' => 'Easy installments, Limited-time offers, Verified booking process',
                'images' => [],
            ],
        ];
        $project->save();
    }

    public function down(): void
    {
        $project = Project::query()
            ->where('title', 'First')
            ->orWhere('slug', 'first')
            ->first();

        if (! $project) {
            return;
        }

        $project->project_detail_tabs = null;
        $project->save();
    }
};
