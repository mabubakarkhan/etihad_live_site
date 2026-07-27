<?php

namespace App\Http\Requests\Prototype;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePrototypeMapSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'section_type' => ['required', 'string', Rule::in(['polygon', 'rectangle', 'marker'])],
            'geometry' => ['required', 'array'],
            'fill_color' => ['nullable', 'string', 'max:20'],
            'stroke_color' => ['nullable', 'string', 'max:20'],
            'fill_opacity' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'stroke_opacity' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'stroke_weight' => ['nullable', 'integer', 'min:1', 'max:10'],
            'label' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'draft'])],
        ];
    }
}
