<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteractiveMap extends Model
{
    protected $fillable = [
        'project_id',
        'dha_phase_id',
        'overlay_image_path',
        'north',
        'south',
        'east',
        'west',
        'default_zoom',
        'min_zoom',
        'max_zoom',
        'overlay_opacity',
        'overlay_rotation',
        'overlay_visibility_zoom',
        'is_active',
    ];

    protected $casts = [
        'north' => 'float',
        'south' => 'float',
        'east' => 'float',
        'west' => 'float',
        'default_zoom' => 'integer',
        'min_zoom' => 'integer',
        'max_zoom' => 'integer',
        'overlay_opacity' => 'float',
        'overlay_rotation' => 'float',
        'overlay_visibility_zoom' => 'integer',
        'is_active' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function dhaPhase(): BelongsTo
    {
        return $this->belongsTo(DhaPhase::class);
    }

    public function overlayUrl(): ?string
    {
        if (! is_string($this->overlay_image_path) || trim($this->overlay_image_path) === '') {
            return null;
        }

        return public_storage_url($this->overlay_image_path);
    }

    public function hasOverlay(): bool
    {
        return public_storage_exists($this->overlay_image_path);
    }

    /** @return array{north: float, south: float, east: float, west: float}|null */
    public function boundsArray(): ?array
    {
        if ($this->north === null || $this->south === null || $this->east === null || $this->west === null) {
            return null;
        }

        return [
            'north' => (float) $this->north,
            'south' => (float) $this->south,
            'east' => (float) $this->east,
            'west' => (float) $this->west,
        ];
    }

    public function isReadyForFront(): bool
    {
        return (bool) $this->is_active
            && $this->hasOverlay()
            && $this->boundsArray() !== null;
    }

    /** @return array<string, mixed> */
    public function toFrontConfig(): array
    {
        return [
            'overlay_url' => $this->overlayUrl(),
            'bounds' => $this->boundsArray(),
            'default_zoom' => $this->default_zoom,
            'min_zoom' => $this->min_zoom,
            'max_zoom' => $this->max_zoom,
            'overlay_opacity' => $this->overlay_opacity,
            'overlay_rotation' => $this->overlay_rotation,
            'overlay_visibility_zoom' => $this->overlay_visibility_zoom,
        ];
    }

    /** @return array<string, mixed> */
    public function toEditorArray(string $ownerType, int $ownerId): array
    {
        return [
            'id' => $this->id,
            'owner_type' => $ownerType,
            'owner_id' => $ownerId,
            'overlay_url' => $this->overlayUrl(),
            'overlay_image_path' => $this->overlay_image_path,
            'north' => $this->north,
            'south' => $this->south,
            'east' => $this->east,
            'west' => $this->west,
            'default_zoom' => $this->default_zoom,
            'min_zoom' => $this->min_zoom,
            'max_zoom' => $this->max_zoom,
            'overlay_opacity' => $this->overlay_opacity,
            'overlay_rotation' => $this->overlay_rotation,
            'overlay_visibility_zoom' => $this->overlay_visibility_zoom,
            'is_active' => $this->is_active,
        ];
    }
}
