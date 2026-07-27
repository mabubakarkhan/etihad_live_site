<?php

namespace App\Models\Prototype;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrototypeMapOverlay extends Model
{
    protected $table = 'prototype_map_overlays';

    protected $fillable = [
        'title',
        'overlay_image',
        'north',
        'south',
        'east',
        'west',
        'default_zoom',
        'min_zoom',
        'max_zoom',
        'overlay_opacity',
        'overlay_rotation',
        'show_overlay_from_zoom',
        'status',
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
        'show_overlay_from_zoom' => 'integer',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(PrototypeMapSection::class, 'prototype_map_overlay_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function activeSections(): HasMany
    {
        return $this->sections()->where('status', 'active');
    }

    public function overlayUrl(): ?string
    {
        if (! is_string($this->overlay_image) || trim($this->overlay_image) === '') {
            return null;
        }

        return public_storage_url($this->overlay_image);
    }

    public function hasOverlayImage(): bool
    {
        return public_storage_exists($this->overlay_image);
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

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isReadyForMap(): bool
    {
        return $this->isActive()
            && $this->hasOverlayImage()
            && $this->boundsArray() !== null;
    }

    /** @return array<string, mixed> */
    public function toMapConfig(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'overlay_url' => $this->overlayUrl(),
            'bounds' => $this->boundsArray(),
            'default_zoom' => $this->default_zoom,
            'min_zoom' => $this->min_zoom,
            'max_zoom' => $this->max_zoom,
            'overlay_opacity' => $this->overlay_opacity,
            'overlay_rotation' => $this->overlay_rotation,
            'show_overlay_from_zoom' => $this->show_overlay_from_zoom,
            'status' => $this->status,
            'sections' => $this->relationLoaded('sections')
                ? $this->sections->map->toMapConfig()->values()->all()
                : [],
        ];
    }

    /** @return array<string, mixed> */
    public function toEditorPayload(): array
    {
        return array_merge($this->toMapConfig(), [
            'has_overlay' => $this->hasOverlayImage(),
            'overlay_image' => $this->overlay_image,
            'updated_at' => $this->updated_at?->toIso8601String(),
            'sections' => $this->relationLoaded('sections')
                ? $this->sections->map->toEditorPayload()->values()->all()
                : [],
        ]);
    }
}
