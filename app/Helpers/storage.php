<?php

use Illuminate\Http\UploadedFile;

if (! function_exists('public_storage_path')) {
    function public_storage_path(string $relative = ''): string
    {
        $relative = ltrim(str_replace('\\', '/', $relative), '/');

        return $relative === ''
            ? storage_path('app/public')
            : storage_path('app/public/' . $relative);
    }
}

if (! function_exists('public_storage_exists')) {
    function public_storage_exists(?string $relative): bool
    {
        if (! is_string($relative) || trim($relative) === '') {
            return false;
        }

        return is_file(public_storage_path($relative));
    }
}

if (! function_exists('public_storage_public_path')) {
    function public_storage_public_path(string $relative = ''): string
    {
        $relative = ltrim(str_replace('\\', '/', $relative), '/');

        return $relative === ''
            ? public_path('storage')
            : public_path('storage/' . $relative);
    }
}

if (! function_exists('public_storage_mirror_file')) {
    function public_storage_mirror_file(?string $relative): void
    {
        if (! is_string($relative) || trim($relative) === '') {
            return;
        }

        $relative = ltrim(str_replace('\\', '/', $relative), '/');
        $source = public_storage_path($relative);

        if (! is_file($source)) {
            return;
        }

        $destination = public_storage_public_path($relative);
        $directory = dirname($destination);

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            return;
        }

        @copy($source, $destination);
    }
}

if (! function_exists('public_storage_delete_mirror')) {
    function public_storage_delete_mirror(?string $relative): void
    {
        if (! is_string($relative) || trim($relative) === '') {
            return;
        }

        $path = public_storage_public_path($relative);

        if (is_file($path)) {
            @unlink($path);
        }
    }
}

if (! function_exists('public_storage_url')) {
    function public_storage_url(?string $relative): ?string
    {
        if (! is_string($relative) || trim($relative) === '') {
            return null;
        }

        $relative = ltrim(str_replace('\\', '/', $relative), '/');

        if (is_file(public_storage_public_path($relative))) {
            return url('storage/' . $relative);
        }

        if (public_storage_exists($relative)) {
            public_storage_mirror_file($relative);

            if (is_file(public_storage_public_path($relative))) {
                return url('storage/' . $relative);
            }
        }

        return null;
    }
}

if (! function_exists('public_storage_delete')) {
    function public_storage_delete(?string $relative): void
    {
        if (! is_string($relative) || trim($relative) === '') {
            return;
        }

        $path = public_storage_path($relative);

        if (is_file($path)) {
            @unlink($path);
        }

        public_storage_delete_mirror($relative);
    }
}

if (! function_exists('public_storage_store_upload')) {
    /**
     * Store an uploaded file on the public disk without Flysystem/finfo.
     */
    function public_storage_store_upload(UploadedFile $file, string $directory): string
    {
        $directory = trim(str_replace('\\', '/', $directory), '/');
        $targetDir = public_storage_path($directory);

        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            throw new RuntimeException('Unable to create upload directory: ' . $targetDir);
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $extension = preg_replace('/[^a-z0-9]+/i', '', $extension) ?: 'bin';
        $filename = uniqid('', true) . '.' . $extension;

        $file->move($targetDir, $filename);

        $storedPath = $directory . '/' . $filename;
        public_storage_mirror_file($storedPath);

        return $storedPath;
    }
}
