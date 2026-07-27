<?php

namespace App\Models\Prototype;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrototypeMapSection extends Model
{
    protected $table = 'prototype_map_sections';

    protected $fillable = [
        'prototype_map_overlay_id',
        'title',
        'section_type',
        'geometry',
        'fill_color',
        'stroke_color',
        'fill_opacity',
        'stroke_opacity',
        'stroke_weight',
        'label',
        'notes',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'geometry' => 'array',
        'fill_opacity' => 'float',
        'stroke_opacity' => 'float',
        'stroke_weight' => 'integer',
        'sort_order' => 'integer',
    ];

    public function overlay(): BelongsTo
    {
        return $this->belongsTo(PrototypeMapOverlay::class, 'prototype_map_overlay_id');
    }

    /** @return array<string, mixed> */
    public function toMapConfig(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'section_type' => $this->section_type,
            'geometry' => $this->geometry,
            'fill_color' => $this->fill_color,
            'stroke_color' => $this->stroke_color,
            'fill_opacity' => $this->fill_opacity,
            'stroke_opacity' => $this->stroke_opacity,
            'stroke_weight' => $this->stroke_weight,
            'label' => $this->label,
            'status' => $this->status,
        ];
    }

    /** @return array<string, mixed> */
    public function toEditorPayload(): array
    {
        return array_merge($this->toMapConfig(), [
            'notes' => $this->notes,
            'sort_order' => $this->sort_order,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ]);
    }
}
