<?php

namespace App\Http\Requests\Prototype;

use Illuminate\Foundation\Http\FormRequest;

class UploadPrototypeMapOverlayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $maxKb = (int) config('prototype_map.max_upload_kb', 204800);

        return [
            'overlay_image' => [
                'required',
                'file',
                'mimes:png',
                'mimetypes:image/png',
                'max:' . $maxKb,
            ],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'overlay_image.mimes' => 'Overlay must be a PNG image with transparency support.',
            'overlay_image.mimetypes' => 'Overlay must be a PNG image with transparency support.',
            'overlay_image.max' => 'Overlay image exceeds the maximum allowed upload size.',
        ];
    }
}
