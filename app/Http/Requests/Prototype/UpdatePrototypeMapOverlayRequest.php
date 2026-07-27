<?php

namespace App\Http\Requests\Prototype;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePrototypeMapOverlayRequest extends FormRequest
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
            'north' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'south' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'east' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'west' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'default_zoom' => ['sometimes', 'integer', 'min:0', 'max:22'],
            'min_zoom' => ['sometimes', 'integer', 'min:0', 'max:22'],
            'max_zoom' => ['sometimes', 'integer', 'min:0', 'max:22'],
            'overlay_opacity' => ['sometimes', 'numeric', 'min:0', 'max:1'],
            'overlay_rotation' => ['sometimes', 'numeric', 'min:-360', 'max:360'],
            'show_overlay_from_zoom' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:22'],
            'status' => ['sometimes', 'string', Rule::in(['draft', 'active', 'inactive'])],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'overlay_opacity.max' => 'Opacity must be between 0 and 1.',
            'overlay_opacity.min' => 'Opacity must be between 0 and 1.',
        ];
    }

    protected function passedValidation(): void
    {
        $minZoom = $this->input('min_zoom');
        $maxZoom = $this->input('max_zoom');
        $defaultZoom = $this->input('default_zoom');

        if ($minZoom !== null && $maxZoom !== null && (int) $minZoom > (int) $maxZoom) {
            $this->validator->errors()->add('min_zoom', 'Minimum zoom cannot exceed maximum zoom.');
            $this->failedValidation($this->validator);
        }

        if ($defaultZoom !== null && $minZoom !== null && (int) $defaultZoom < (int) $minZoom) {
            $this->validator->errors()->add('default_zoom', 'Default zoom cannot be less than minimum zoom.');
            $this->failedValidation($this->validator);
        }

        if ($defaultZoom !== null && $maxZoom !== null && (int) $defaultZoom > (int) $maxZoom) {
            $this->validator->errors()->add('default_zoom', 'Default zoom cannot exceed maximum zoom.');
            $this->failedValidation($this->validator);
        }
    }
}
