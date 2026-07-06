<?php

namespace App\Services\InteractiveMap;

use App\Models\InteractiveMap;
use Illuminate\Http\UploadedFile;

class InteractiveMapService
{
    public function __construct(
        private readonly InteractiveMapOwnerResolver $owners
    ) {}

    public function findOrCreateForOwner(string $ownerType, int $ownerId): InteractiveMap
    {
        $this->owners->findModel($ownerType, $ownerId);
        $foreignKey = $this->owners->foreignKey($ownerType);

        $map = InteractiveMap::query()->where($foreignKey, $ownerId)->first();

        if ($map) {
            return $map;
        }

        $defaults = config('interactive_map.defaults', []);

        return InteractiveMap::query()->create([
            $foreignKey => $ownerId,
            'north' => $defaults['north'] ?? null,
            'south' => $defaults['south'] ?? null,
            'east' => $defaults['east'] ?? null,
            'west' => $defaults['west'] ?? null,
            'default_zoom' => $defaults['default_zoom'] ?? 15,
            'min_zoom' => $defaults['min_zoom'] ?? 10,
            'max_zoom' => $defaults['max_zoom'] ?? 20,
            'overlay_opacity' => $defaults['overlay_opacity'] ?? 0.85,
            'overlay_rotation' => $defaults['overlay_rotation'] ?? 0,
            'overlay_visibility_zoom' => $defaults['overlay_visibility_zoom'] ?? null,
            'is_active' => $defaults['is_active'] ?? true,
        ]);
    }

    /** @param array<string, mixed> $data */
    public function updateSettings(InteractiveMap $map, array $data): InteractiveMap
    {
        $map->fill([
            'north' => $data['north'] ?? $map->north,
            'south' => $data['south'] ?? $map->south,
            'east' => $data['east'] ?? $map->east,
            'west' => $data['west'] ?? $map->west,
            'default_zoom' => $data['default_zoom'] ?? $map->default_zoom,
            'min_zoom' => $data['min_zoom'] ?? $map->min_zoom,
            'max_zoom' => $data['max_zoom'] ?? $map->max_zoom,
            'overlay_opacity' => $data['overlay_opacity'] ?? $map->overlay_opacity,
            'overlay_rotation' => $data['overlay_rotation'] ?? $map->overlay_rotation,
            'overlay_visibility_zoom' => array_key_exists('overlay_visibility_zoom', $data)
                ? $data['overlay_visibility_zoom']
                : $map->overlay_visibility_zoom,
            'is_active' => array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : $map->is_active,
        ]);

        $map->save();

        return $map->fresh();
    }

    public function storeOverlay(InteractiveMap $map, UploadedFile $file): InteractiveMap
    {
        if (! empty($map->overlay_image_path)) {
            public_storage_delete($map->overlay_image_path);
        }

        $directory = trim((string) config('interactive_map.storage_directory', 'maps'), '/');
        $path = public_storage_store_upload($file, $directory);

        $map->overlay_image_path = $path;
        $map->save();

        return $map->fresh();
    }

    public function deleteOverlay(InteractiveMap $map): InteractiveMap
    {
        if (! empty($map->overlay_image_path)) {
            public_storage_delete($map->overlay_image_path);
        }

        $map->overlay_image_path = null;
        $map->save();

        return $map->fresh();
    }

    /** @return array<string, mixed> */
    public function toEditorPayload(InteractiveMap $map, string $ownerType, int $ownerId): array
    {
        return $map->toEditorArray($ownerType, $ownerId);
    }
}
