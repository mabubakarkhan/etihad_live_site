<?php

namespace App\Services\Prototype;

use App\Models\Prototype\PrototypeMapOverlay;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PrototypeMapOverlayService
{
    public function listOverlays(): Collection
    {
        return PrototypeMapOverlay::query()
            ->orderByDesc('updated_at')
            ->get();
    }

    public function findOrFail(int $id): PrototypeMapOverlay
    {
        return PrototypeMapOverlay::query()->findOrFail($id);
    }

    public function create(array $data): PrototypeMapOverlay
    {
        $defaults = config('prototype_map.defaults', []);

        return PrototypeMapOverlay::query()->create([
            'title' => $data['title'] ?? 'Untitled Overlay',
            'north' => $data['north'] ?? $defaults['north'],
            'south' => $data['south'] ?? $defaults['south'],
            'east' => $data['east'] ?? $defaults['east'],
            'west' => $data['west'] ?? $defaults['west'],
            'default_zoom' => $data['default_zoom'] ?? $defaults['default_zoom'],
            'min_zoom' => $data['min_zoom'] ?? $defaults['min_zoom'],
            'max_zoom' => $data['max_zoom'] ?? $defaults['max_zoom'],
            'overlay_opacity' => $data['overlay_opacity'] ?? $defaults['overlay_opacity'],
            'overlay_rotation' => $data['overlay_rotation'] ?? $defaults['overlay_rotation'],
            'show_overlay_from_zoom' => $data['show_overlay_from_zoom'] ?? $defaults['show_overlay_from_zoom'],
            'status' => $data['status'] ?? $defaults['status'],
        ]);
    }

    /** @param array<string, mixed> $data */
    public function updateSettings(PrototypeMapOverlay $overlay, array $data): PrototypeMapOverlay
    {
        $this->validateBounds($data, $overlay);

        $overlay->fill([
            'title' => $data['title'] ?? $overlay->title,
            'north' => array_key_exists('north', $data) ? $data['north'] : $overlay->north,
            'south' => array_key_exists('south', $data) ? $data['south'] : $overlay->south,
            'east' => array_key_exists('east', $data) ? $data['east'] : $overlay->east,
            'west' => array_key_exists('west', $data) ? $data['west'] : $overlay->west,
            'default_zoom' => $data['default_zoom'] ?? $overlay->default_zoom,
            'min_zoom' => $data['min_zoom'] ?? $overlay->min_zoom,
            'max_zoom' => $data['max_zoom'] ?? $overlay->max_zoom,
            'overlay_opacity' => $data['overlay_opacity'] ?? $overlay->overlay_opacity,
            'overlay_rotation' => $data['overlay_rotation'] ?? $overlay->overlay_rotation,
            'show_overlay_from_zoom' => array_key_exists('show_overlay_from_zoom', $data)
                ? $data['show_overlay_from_zoom']
                : $overlay->show_overlay_from_zoom,
            'status' => $data['status'] ?? $overlay->status,
        ]);

        $overlay->save();

        return $overlay->fresh();
    }

    public function storeOverlayImage(PrototypeMapOverlay $overlay, UploadedFile $file): PrototypeMapOverlay
    {
        $this->validateImageFile($file);

        if (! empty($overlay->overlay_image)) {
            public_storage_delete($overlay->overlay_image);
        }

        $directory = trim((string) config('prototype_map.storage_directory', 'prototype/maps'), '/');

        try {
            $path = public_storage_store_upload($file, $directory);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages([
                'overlay_image' => 'Failed to store overlay image. Please check server storage permissions.',
            ]);
        }

        $overlay->overlay_image = $path;
        $overlay->save();

        return $overlay->fresh();
    }

    public function deleteOverlayImage(PrototypeMapOverlay $overlay): PrototypeMapOverlay
    {
        if (! empty($overlay->overlay_image)) {
            public_storage_delete($overlay->overlay_image);
        }

        $overlay->overlay_image = null;
        $overlay->save();

        return $overlay->fresh();
    }

    public function delete(PrototypeMapOverlay $overlay): void
    {
        if (! empty($overlay->overlay_image)) {
            public_storage_delete($overlay->overlay_image);
        }

        $overlay->delete();
    }

    /** @param array<string, mixed> $data */
    private function validateBounds(array $data, PrototypeMapOverlay $overlay): void
    {
        $north = array_key_exists('north', $data) ? $data['north'] : $overlay->north;
        $south = array_key_exists('south', $data) ? $data['south'] : $overlay->south;
        $east = array_key_exists('east', $data) ? $data['east'] : $overlay->east;
        $west = array_key_exists('west', $data) ? $data['west'] : $overlay->west;

        if ($north === null || $south === null || $east === null || $west === null) {
            return;
        }

        if ((float) $north <= (float) $south) {
            throw ValidationException::withMessages([
                'north' => 'North bound must be greater than south bound.',
            ]);
        }

        if ((float) $east <= (float) $west) {
            throw ValidationException::withMessages([
                'east' => 'East bound must be greater than west bound.',
            ]);
        }
    }

    private function validateImageFile(UploadedFile $file): void
    {
        $allowedMimes = config('prototype_map.allowed_mimes', ['image/png']);
        $maxKb = (int) config('prototype_map.max_upload_kb', 204800);

        if (! in_array($file->getMimeType(), $allowedMimes, true)
            && ! in_array('image/' . strtolower($file->getClientOriginalExtension()), $allowedMimes, true)) {
            throw ValidationException::withMessages([
                'overlay_image' => 'Overlay must be a transparent PNG image.',
            ]);
        }

        if ($file->getSize() > $maxKb * 1024) {
            throw ValidationException::withMessages([
                'overlay_image' => 'Overlay image exceeds the maximum allowed size of ' . round($maxKb / 1024) . ' MB.',
            ]);
        }
    }
}
