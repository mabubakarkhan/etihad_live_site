<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInteractiveMapSectionRequest;
use App\Http\Requests\UpdateInteractiveMapSectionRequest;
use App\Models\ActivityLog;
use App\Models\InteractiveMapSection;
use App\Services\InteractiveMap\InteractiveMapSectionService;
use App\Services\InteractiveMap\InteractiveMapService;
use Illuminate\Http\JsonResponse;

class InteractiveMapSectionController extends Controller
{
    public function __construct(
        private readonly InteractiveMapService $maps,
        private readonly InteractiveMapSectionService $sections
    ) {}

    public function index(string $ownerType, int $ownerId): JsonResponse
    {
        $map = $this->maps->findOrCreateForOwner($ownerType, $ownerId);
        $list = $this->sections->listForMap($map);

        return response()->json([
            'sections' => $list->map->toEditorPayload()->values(),
        ]);
    }

    public function store(StoreInteractiveMapSectionRequest $request, string $ownerType, int $ownerId): JsonResponse
    {
        $map = $this->maps->findOrCreateForOwner($ownerType, $ownerId);
        $section = $this->sections->create($map, $request->validated());

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'interactive_map_section_created',
                "Map plot created: {$section->title} ({$ownerType} #{$ownerId})."
            );
        }

        return response()->json([
            'message' => 'Plot saved.',
            'section' => $section->toEditorPayload(),
        ], 201);
    }

    public function update(
        UpdateInteractiveMapSectionRequest $request,
        string $ownerType,
        int $ownerId,
        InteractiveMapSection $section
    ): JsonResponse {
        $map = $this->maps->findOrCreateForOwner($ownerType, $ownerId);
        abort_unless((int) $section->interactive_map_id === (int) $map->id, 404);

        $section = $this->sections->update($section, $request->validated());

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'interactive_map_section_updated',
                "Map plot updated: {$section->title} (ID: {$section->id})."
            );
        }

        return response()->json([
            'message' => 'Plot updated.',
            'section' => $section->toEditorPayload(),
        ]);
    }

    public function destroy(string $ownerType, int $ownerId, InteractiveMapSection $section): JsonResponse
    {
        $map = $this->maps->findOrCreateForOwner($ownerType, $ownerId);
        abort_unless((int) $section->interactive_map_id === (int) $map->id, 404);

        $title = $section->title;
        $id = $section->id;
        $this->sections->delete($section);

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'interactive_map_section_deleted',
                "Map plot deleted: {$title} (ID: {$id})."
            );
        }

        return response()->json([
            'message' => 'Plot deleted.',
        ]);
    }
}
