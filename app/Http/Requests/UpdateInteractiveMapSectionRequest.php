<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInteractiveMapSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'section_type' => ['sometimes', 'required', 'string', Rule::in(['polygon', 'rectangle', 'marker'])],
            'geometry' => ['sometimes', 'required', 'array'],
            'fill_color' => ['sometimes', 'nullable', 'string', 'max:20'],
            'stroke_color' => ['sometimes', 'nullable', 'string', 'max:20'],
            'fill_opacity' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:1'],
            'stroke_opacity' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:1'],
            'stroke_weight' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:10'],
            'label' => ['sometimes', 'nullable', 'string', 'max:100'],
            'show_label_from_zoom' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:22'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'status' => ['sometimes', 'nullable', 'string', Rule::in(['active', 'inactive', 'draft'])],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }
}
