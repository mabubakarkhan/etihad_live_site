<?php

namespace App\Http\Requests\Prototype;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePrototypeMapSectionRequest extends FormRequest
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
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'status' => ['sometimes', 'nullable', 'string', Rule::in(['active', 'inactive', 'draft'])],
        ];
    }
}
