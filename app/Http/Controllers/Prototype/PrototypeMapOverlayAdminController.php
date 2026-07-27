<?php

namespace App\Http\Controllers\Prototype;

use App\Http\Controllers\Controller;
use App\Http\Requests\Prototype\UpdatePrototypeMapOverlayRequest;
use App\Http\Requests\Prototype\UploadPrototypeMapOverlayRequest;
use App\Models\ActivityLog;
use App\Models\Prototype\PrototypeMapOverlay;
use App\Services\Prototype\PrototypeMapOverlayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrototypeMapOverlayAdminController extends Controller
{
    public function __construct(
        private readonly PrototypeMapOverlayService $service
    ) {}

    public function index(Request $request)
    {
        $overlays = $this->service->listOverlays();
        $selected = null;

        if ($overlays->isNotEmpty()) {
            $selectedId = (int) $request->query('overlay', $overlays->first()->id);
            $selected = $overlays->firstWhere('id', $selectedId) ?? $overlays->first();
            $selected?->load('sections');
        }

        return view('admin.prototype.map_overlay.index', [
            'overlays' => $overlays,
            'selected' => $selected,
            'googleMapsApiKey' => config('app.google_maps_api_key', ''),
            'googleMapsMapId' => config('app.google_maps_map_id', 'DEMO_MAP_ID'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $overlay = $this->service->create($validated);

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'prototype_map_overlay_created',
                "Prototype map overlay created: {$overlay->title} (ID: {$overlay->id})."
            );
        }

        return response()->json([
            'message' => 'Overlay created.',
            'overlay' => $overlay->toEditorPayload(),
        ], 201);
    }

    public function update(UpdatePrototypeMapOverlayRequest $request, PrototypeMapOverlay $overlay): JsonResponse
    {
        $overlay = $this->service->updateSettings($overlay, $request->validated());

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'prototype_map_overlay_updated',
                "Prototype map overlay updated: {$overlay->title} (ID: {$overlay->id})."
            );
        }

        return response()->json([
            'message' => 'Settings saved.',
            'overlay' => $overlay->toEditorPayload(),
        ]);
    }

    public function upload(UploadPrototypeMapOverlayRequest $request, PrototypeMapOverlay $overlay): JsonResponse
    {
        $overlay = $this->service->storeOverlayImage($overlay, $request->file('overlay_image'));

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'prototype_map_overlay_image_uploaded',
                "Prototype map overlay image uploaded: {$overlay->title} (ID: {$overlay->id})."
            );
        }

        return response()->json([
            'message' => 'Overlay image uploaded.',
            'overlay' => $overlay->toEditorPayload(),
        ]);
    }

    public function deleteImage(PrototypeMapOverlay $overlay): JsonResponse
    {
        $overlay = $this->service->deleteOverlayImage($overlay);

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'prototype_map_overlay_image_deleted',
                "Prototype map overlay image deleted: {$overlay->title} (ID: {$overlay->id})."
            );
        }

        return response()->json([
            'message' => 'Overlay image removed.',
            'overlay' => $overlay->toEditorPayload(),
        ]);
    }

    public function destroy(PrototypeMapOverlay $overlay): JsonResponse
    {
        $title = $overlay->title;
        $id = $overlay->id;

        $this->service->delete($overlay);

        if ($admin = admin_user()) {
            ActivityLog::record(
                $admin,
                'prototype_map_overlay_deleted',
                "Prototype map overlay deleted: {$title} (ID: {$id})."
            );
        }

        $remaining = $this->service->listOverlays();
        $next = $remaining->first();

        return response()->json([
            'message' => 'Overlay deleted.',
            'next_overlay_id' => $next?->id,
            'overlays' => $remaining->map(fn (PrototypeMapOverlay $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'status' => $item->status,
            ])->values(),
        ]);
    }

    public function config(PrototypeMapOverlay $overlay): JsonResponse
    {
        $overlay->load('sections');

        return response()->json([
            'overlay' => $overlay->toEditorPayload(),
            'routes' => $this->routesForOverlay($overlay),
        ]);
    }

    /** @return array<string, mixed> */
    private function routesForOverlay(PrototypeMapOverlay $overlay): array
    {
        return [
            'update' => route('admin.prototype.interactive-map.update', $overlay),
            'upload' => route('admin.prototype.interactive-map.upload', $overlay),
            'deleteImage' => route('admin.prototype.interactive-map.delete-image', $overlay),
            'destroy' => route('admin.prototype.interactive-map.destroy', $overlay),
            'config' => route('admin.prototype.interactive-map.config', $overlay),
            'store' => route('admin.prototype.interactive-map.store'),
            'index' => route('admin.prototype.interactive-map.index'),
            'sections' => [
                'index' => route('admin.prototype.sections.index', $overlay),
                'store' => route('admin.prototype.sections.store', $overlay),
                'update' => route('admin.prototype.sections.update', ['overlay' => $overlay, 'section' => '__SECTION__']),
                'destroy' => route('admin.prototype.sections.destroy', ['overlay' => $overlay, 'section' => '__SECTION__']),
            ],
        ];
    }
}
