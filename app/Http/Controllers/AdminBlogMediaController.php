<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class AdminBlogMediaController extends Controller
{
    /** @var list<string> */
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file'],
            'context' => ['nullable', 'string', 'in:featured,content'],
        ]);

        $file = $request->file('file');
        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return response()->json(['success' => false, 'message' => 'Invalid upload.'], 422);
        }

        $maxBytes = 8 * 1024 * 1024;
        if ($file->getSize() > $maxBytes) {
            return response()->json(['success' => false, 'message' => 'File is too large. Maximum size is 8 MB.'], 422);
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        if (! in_array($extension, self::IMAGE_EXTENSIONS, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file type. Allowed: ' . implode(', ', self::IMAGE_EXTENSIONS) . '.',
            ], 422);
        }

        $dir = $request->input('context') === 'featured' ? 'blog/featured' : 'blog/content';
        $path = public_storage_store_upload($file, $dir);

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => public_storage_url($path),
            'message' => 'Image uploaded successfully.',
        ]);
    }
}
