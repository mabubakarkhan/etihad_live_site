<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class HomepageMediaController extends Controller
{
    /** @var array<string, array{dir: string, kind: string, max_kb: int}> */
    private const UPLOAD_TYPES = [
        'hero_image' => ['dir' => 'homepage-hero', 'kind' => 'image', 'max_kb' => 8192],
        'ceo_image' => ['dir' => 'homepage-vision', 'kind' => 'image', 'max_kb' => 8192],
        'image_left' => ['dir' => 'homepage-why', 'kind' => 'image', 'max_kb' => 8192],
        'image_center' => ['dir' => 'homepage-why', 'kind' => 'image', 'max_kb' => 8192],
        'image_right' => ['dir' => 'homepage-why', 'kind' => 'image', 'max_kb' => 8192],
        'image_center_back' => ['dir' => 'homepage-why', 'kind' => 'image', 'max_kb' => 8192],
        'video' => ['dir' => 'homepage-about', 'kind' => 'video', 'max_kb' => 102400],
        'center_image' => ['dir' => 'homepage-about', 'kind' => 'image', 'max_kb' => 8192],
        'secondary_image' => ['dir' => 'homepage-about', 'kind' => 'image', 'max_kb' => 8192],
        'map_background_image' => ['dir' => 'homepage-location', 'kind' => 'image', 'max_kb' => 8192],
        'card_image' => ['dir' => 'homepage-location', 'kind' => 'image', 'max_kb' => 8192],
        'pin_image' => ['dir' => 'homepage-location', 'kind' => 'image', 'max_kb' => 4096],
        'background_image' => ['dir' => 'homepage-choice', 'kind' => 'image', 'max_kb' => 10240],
        'background_image_portrait' => ['dir' => 'homepage-choice', 'kind' => 'image', 'max_kb' => 10240],
        'card_image' => ['dir' => 'homepage-choice', 'kind' => 'image', 'max_kb' => 8192],
        'icon_image' => ['dir' => 'homepage-what-sets-apart', 'kind' => 'image', 'max_kb' => 2048],
    ];

    /** @var list<string> */
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg'];

    /** @var list<string> */
    private const VIDEO_EXTENSIONS = ['mp4', 'webm'];

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(self::UPLOAD_TYPES))],
            'file' => ['required', 'file'],
        ]);

        $config = self::UPLOAD_TYPES[$request->input('type')];
        $file = $request->file('file');

        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return response()->json(['success' => false, 'message' => 'Invalid upload.'], 422);
        }

        $maxBytes = $config['max_kb'] * 1024;
        if ($file->getSize() > $maxBytes) {
            return response()->json([
                'success' => false,
                'message' => 'File is too large. Maximum size is ' . $config['max_kb'] . ' KB.',
            ], 422);
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $allowed = $config['kind'] === 'video' ? self::VIDEO_EXTENSIONS : self::IMAGE_EXTENSIONS;

        if (! in_array($extension, $allowed, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file type. Allowed: ' . implode(', ', $allowed) . '.',
            ], 422);
        }

        $path = public_storage_store_upload($file, $config['dir']);

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => public_storage_url($path),
            'message' => $config['kind'] === 'video' ? 'Video uploaded successfully.' : 'Image uploaded successfully.',
        ]);
    }
}
