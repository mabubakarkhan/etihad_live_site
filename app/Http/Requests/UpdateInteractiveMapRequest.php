<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInteractiveMapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'north' => ['required', 'numeric', 'between:-90,90'],
            'south' => ['required', 'numeric', 'between:-90,90'],
            'east' => ['required', 'numeric', 'between:-180,180'],
            'west' => ['required', 'numeric', 'between:-180,180'],
            'default_zoom' => ['required', 'integer', 'min:0', 'max:22'],
            'min_zoom' => ['required', 'integer', 'min:0', 'max:22'],
            'max_zoom' => ['required', 'integer', 'min:0', 'max:22'],
            'overlay_opacity' => ['required', 'numeric', 'min:0', 'max:1'],
            'overlay_rotation' => ['nullable', 'numeric', 'min:-360', 'max:360'],
            'overlay_visibility_zoom' => ['nullable', 'integer', 'min:0', 'max:22'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
    }
}
