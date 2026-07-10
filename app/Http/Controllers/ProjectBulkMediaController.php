<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Services\ProjectBulkMedia\ProjectBulkMediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectBulkMediaController extends Controller
{
    public function preview(Request $request, Project $project, ProjectBulkMediaService $service): JsonResponse
    {
        $maxKb = (int) config('project_bulk_media.max_zip_kb', 204800);

        $request->validate([
            'zip' => ['required', 'file', 'mimes:zip', 'max:' . $maxKb],
        ]);

        try {
            $result = $service->previewFromUpload($request->file('zip')->getRealPath(), $project);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'token' => $result['token'],
            'items' => $result['items'],
            'warnings' => $result['warnings'],
            'errors' => $result['errors'],
            'can_import' => $result['can_import'],
        ]);
    }

    public function import(Request $request, Project $project, ProjectBulkMediaService $service): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string', 'max:64'],
        ]);

        try {
            $result = $service->import((string) $validated['token'], $project);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'project_bulk_media_imported',
                'Bulk media imported for project: ' . $project->title . ' (ID: ' . $project->id . ').'
            );
        }

        return response()->json([
            'success' => true,
            'message' => count($result['imported']) > 0
                ? 'Bulk media imported successfully.'
                : 'No files were imported.',
            'imported' => $result['imported'],
            'warnings' => $result['warnings'],
            'skipped' => $result['skipped'],
        ]);
    }
}
