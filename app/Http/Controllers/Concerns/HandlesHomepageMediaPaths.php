<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait HandlesHomepageMediaPaths
{
    protected function applyHomepageMediaPath(Request $request, object $model, string $field, ?string $removeFlag = null): void
    {
        $removeFlag = $removeFlag ?? 'remove_' . $field;
        $pathKey = $field . '_path';

        if ($request->boolean($removeFlag)) {
            if (! empty($model->{$field})) {
                public_storage_delete($model->{$field});
            }
            $model->{$field} = null;

            return;
        }

        if ($request->filled($pathKey)) {
            $newPath = $request->input($pathKey);

            if (! $this->isValidHomepageStoragePath($newPath)) {
                return;
            }

            if (! empty($model->{$field}) && $model->{$field} !== $newPath) {
                public_storage_delete($model->{$field});
            }

            $model->{$field} = $newPath;
        }
    }

    protected function isValidHomepageStoragePath(?string $path): bool
    {
        if (! is_string($path) || trim($path) === '') {
            return false;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (str_contains($path, '..')) {
            return false;
        }

        return (bool) preg_match('#^homepage-[a-z0-9-]+/.+#', $path)
            && public_storage_exists($path);
    }
}
