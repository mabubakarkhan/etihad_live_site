<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteractiveMapSection extends Model
{
    protected $table = 'interactive_map_sections';

    protected $fillable = [
        'interactive_map_id',
        'title',
        'section_type',
        'geometry',
        'fill_color',
        'stroke_color',
        'fill_opacity',
        'stroke_opacity',
        'stroke_weight',
        'label',
        'show_label_from_zoom',
        'notes',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'geometry' => 'array',
        'fill_opacity' => 'float',
        'stroke_opacity' => 'float',
        'stroke_weight' => 'integer',
        'show_label_from_zoom' => 'integer',
        'sort_order' => 'integer',
    ];

    public function map(): BelongsTo
    {
        return $this->belongsTo(InteractiveMap::class, 'interactive_map_id');
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
            'show_label_from_zoom' => $this->show_label_from_zoom,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
        ];
    }

    /** @return array<string, mixed> */
    public function toEditorPayload(): array
    {
        return array_merge($this->toMapConfig(), [
            'notes' => $this->notes,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ]);
    }
}
