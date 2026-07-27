<?php

namespace App\Http\Controllers\Prototype;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prototype\StorePrototypeMapSectionRequest;
use App\Http\Requests\Prototype\UpdatePrototypeMapSectionRequest;
use App\Models\ActivityLog;
use App\Models\Prototype\PrototypeMapOverlay;
use App\Models\Prototype\PrototypeMapSection;
use App\Services\Prototype\PrototypeMapSectionService;
use Illuminate\Http\JsonResponse;

class PrototypeMapSectionController extends Controller
{
    public function __construct(
        private readonly PrototypeMapSectionService $service
    ) {}

    public function index(PrototypeMapOverlay $overlay): JsonResponse
    {
        $sections = $this->service->listForOverlay($overlay);

        return response()->json([
            'sections' => $sections->map->toEditorPayload()->values(),
        ]);
    }

    public function store(StorePrototypeMapSectionRequest $request, PrototypeMapOverlay $overlay): JsonResponse
    {
        $section = $this->service->create($overlay, $request->validated());

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'prototype_map_section_created',
                "Prototype section created: {$section->title} on overlay #{$overlay->id}."
            );
        }

        return response()->json([
            'message' => 'Section saved.',
            'section' => $section->toEditorPayload(),
        ], 201);
    }

    public function update(UpdatePrototypeMapSectionRequest $request, PrototypeMapOverlay $overlay, PrototypeMapSection $section): JsonResponse
    {
        abort_unless($section->prototype_map_overlay_id === $overlay->id, 404);

        $section = $this->service->update($section, $request->validated());

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'prototype_map_section_updated',
                "Prototype section updated: {$section->title} (ID: {$section->id})."
            );
        }

        return response()->json([
            'message' => 'Section updated.',
            'section' => $section->toEditorPayload(),
        ]);
    }

    public function destroy(PrototypeMapOverlay $overlay, PrototypeMapSection $section): JsonResponse
    {
        abort_unless($section->prototype_map_overlay_id === $overlay->id, 404);

        $title = $section->title;
        $id = $section->id;

        $this->service->delete($section);

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'prototype_map_section_deleted',
                "Prototype section deleted: {$title} (ID: {$id})."
            );
        }

        return response()->json([
            'message' => 'Section deleted.',
        ]);
    }
}
