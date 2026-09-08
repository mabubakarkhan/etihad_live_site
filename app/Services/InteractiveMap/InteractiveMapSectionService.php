<?php

namespace App\Services\InteractiveMap;

use App\Models\InteractiveMap;
use App\Models\InteractiveMapSection;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class InteractiveMapSectionService
{
    /** @return Collection<int, InteractiveMapSection> */
    public function listForMap(InteractiveMap $map): Collection
    {
        return $map->sections()->get();
    }

    /** @param array<string, mixed> $data */
    public function create(InteractiveMap $map, array $data): InteractiveMapSection
    {
        $this->validateGeometry($data['section_type'] ?? 'polygon', $data['geometry'] ?? []);

        $maxSort = (int) $map->sections()->max('sort_order');

        return InteractiveMapSection::query()->create([
            'interactive_map_id' => $map->id,
            'title' => $data['title'] ?? 'Untitled Plot',
            'section_type' => $data['section_type'] ?? 'polygon',
            'geometry' => $data['geometry'],
            'fill_color' => $data['fill_color'] ?? '#a9823d',
            'stroke_color' => $data['stroke_color'] ?? '#6c4815',
            'fill_opacity' => $data['fill_opacity'] ?? 0.45,
            'stroke_opacity' => $data['stroke_opacity'] ?? 0.9,
            'stroke_weight' => $data['stroke_weight'] ?? 2,
            'label' => $data['label'] ?? null,
            'show_label_from_zoom' => $data['show_label_from_zoom'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? 'active',
            'sort_order' => $data['sort_order'] ?? ($maxSort + 1),
        ]);
    }

    /** @param array<string, mixed> $data */
    public function update(InteractiveMapSection $section, array $data): InteractiveMapSection
    {
        if (isset($data['section_type'], $data['geometry'])) {
            $this->validateGeometry($data['section_type'], $data['geometry']);
        } elseif (isset($data['geometry'])) {
            $this->validateGeometry($section->section_type, $data['geometry']);
        }

        $section->fill([
            'title' => $data['title'] ?? $section->title,
            'section_type' => $data['section_type'] ?? $section->section_type,
            'geometry' => $data['geometry'] ?? $section->geometry,
            'fill_color' => $data['fill_color'] ?? $section->fill_color,
            'stroke_color' => $data['stroke_color'] ?? $section->stroke_color,
            'fill_opacity' => $data['fill_opacity'] ?? $section->fill_opacity,
            'stroke_opacity' => $data['stroke_opacity'] ?? $section->stroke_opacity,
            'stroke_weight' => $data['stroke_weight'] ?? $section->stroke_weight,
            'label' => array_key_exists('label', $data) ? $data['label'] : $section->label,
            'show_label_from_zoom' => array_key_exists('show_label_from_zoom', $data)
                ? $data['show_label_from_zoom']
                : $section->show_label_from_zoom,
            'notes' => array_key_exists('notes', $data) ? $data['notes'] : $section->notes,
            'status' => $data['status'] ?? $section->status,
            'sort_order' => array_key_exists('sort_order', $data) ? $data['sort_order'] : $section->sort_order,
        ]);

        $section->save();

        return $section->fresh();
    }

    public function delete(InteractiveMapSection $section): void
    {
        $section->delete();
    }

    /** @param array<string, mixed> $geometry */
    private function validateGeometry(string $type, array $geometry): void
    {
        switch ($type) {
            case 'polygon':
                if (empty($geometry['paths']) || ! is_array($geometry['paths']) || count($geometry['paths']) < 3) {
                    throw ValidationException::withMessages([
                        'geometry' => 'Polygon requires at least 3 coordinate points.',
                    ]);
                }
                break;

            case 'rectangle':
                $required = ['north', 'south', 'east', 'west'];
                foreach ($required as $key) {
                    if (! isset($geometry['bounds'][$key])) {
                        throw ValidationException::withMessages([
                            'geometry' => 'Rectangle requires north, south, east, and west bounds.',
                        ]);
                    }
                }
                break;

            case 'marker':
                if (! isset($geometry['position']['lat'], $geometry['position']['lng'])) {
                    throw ValidationException::withMessages([
                        'geometry' => 'Marker requires a lat/lng position.',
                    ]);
                }
                break;

            default:
                throw ValidationException::withMessages([
                    'section_type' => 'Unsupported section type.',
                ]);
        }
    }
}
