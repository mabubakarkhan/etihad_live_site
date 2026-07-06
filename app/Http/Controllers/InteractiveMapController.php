<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInteractiveMapRequest;
use App\Models\ActivityLog;
use App\Models\DhaPhase;
use App\Models\Project;
use App\Services\InteractiveMap\InteractiveMapOwnerResolver;
use App\Services\InteractiveMap\InteractiveMapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class InteractiveMapController extends Controller
{
    public function __construct(
        private readonly InteractiveMapService $maps,
        private readonly InteractiveMapOwnerResolver $owners
    ) {}

    public function editProject(Project $project): View
    {
        return $this->edit('projects', (int) $project->id);
    }

    public function editDhaPhase(DhaPhase $dhaPhase): View
    {
        return $this->edit('dha-phases', (int) $dhaPhase->id);
    }

    public function edit(string $ownerType, int $ownerId): View
    {
        $context = $this->ownerContext($ownerType, $ownerId);
        $map = $this->maps->findOrCreateForOwner($ownerType, $ownerId);

        return view('admin.interactive-map.edit', [
            'ownerType' => $ownerType,
            'ownerId' => $ownerId,
            'ownerLabel' => $context['label'],
            'backUrl' => $context['back_url'],
            'map' => $map,
        ]);
    }

    public function show(string $ownerType, int $ownerId): JsonResponse
    {
        $map = $this->maps->findOrCreateForOwner($ownerType, $ownerId);

        return response()->json([
            'data' => $this->maps->toEditorPayload($map, $ownerType, $ownerId),
        ]);
    }

    public function update(UpdateInteractiveMapRequest $request, string $ownerType, int $ownerId): JsonResponse
    {
        $map = $this->maps->findOrCreateForOwner($ownerType, $ownerId);
        $map = $this->maps->updateSettings($map, $request->validated());

        $this->logActivity($ownerType, $ownerId, 'interactive_map_updated', 'Interactive map settings updated.');

        return response()->json([
            'message' => 'Interactive map settings saved.',
            'data' => $this->maps->toEditorPayload($map, $ownerType, $ownerId),
        ]);
    }

    public function uploadOverlay(Request $request, string $ownerType, int $ownerId): JsonResponse
    {
        $validated = $request->validate([
            'overlay' => ['required', 'file', 'mimes:png,svg', 'max:51200'],
        ]);

        $map = $this->maps->findOrCreateForOwner($ownerType, $ownerId);
        $map = $this->maps->storeOverlay($map, $validated['overlay']);

        $this->logActivity($ownerType, $ownerId, 'interactive_map_overlay_uploaded', 'Interactive map overlay uploaded.');

        return response()->json([
            'message' => 'Overlay uploaded.',
            'data' => $this->maps->toEditorPayload($map, $ownerType, $ownerId),
        ]);
    }

    public function deleteOverlay(string $ownerType, int $ownerId): JsonResponse
    {
        $map = $this->maps->findOrCreateForOwner($ownerType, $ownerId);
        $map = $this->maps->deleteOverlay($map);

        $this->logActivity($ownerType, $ownerId, 'interactive_map_overlay_deleted', 'Interactive map overlay removed.');

        return response()->json([
            'message' => 'Overlay removed.',
            'data' => $this->maps->toEditorPayload($map, $ownerType, $ownerId),
        ]);
    }

    public function placesAutocomplete(Request $request, string $ownerType, int $ownerId): JsonResponse
    {
        $this->maps->findOrCreateForOwner($ownerType, $ownerId);

        $validated = $request->validate([
            'input' => ['required', 'string', 'min:2', 'max:200'],
        ]);

        $apiKey = (string) config('interactive_map.places_api_key', '');
        if ($apiKey === '') {
            return response()->json(['message' => 'Places API key is not configured.'], 503);
        }

        $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/place/autocomplete/json', [
            'input' => $validated['input'],
            'key' => $apiKey,
            'components' => 'country:pk',
            'location' => '31.5204,74.3587',
            'radius' => 50000,
        ]);

        $data = $response->json();
        if (! is_array($data)) {
            return response()->json(['message' => 'Places search failed.'], 502);
        }

        $status = (string) ($data['status'] ?? '');
        if (! in_array($status, ['OK', 'ZERO_RESULTS'], true)) {
            return response()->json([
                'message' => (string) ($data['error_message'] ?? 'Places search failed.'),
            ], $status === 'REQUEST_DENIED' ? 403 : 502);
        }

        return response()->json($this->legacyAutocompletePayload($data));
    }

    public function placesDetails(string $ownerType, int $ownerId, string $placeId): JsonResponse
    {
        $this->maps->findOrCreateForOwner($ownerType, $ownerId);

        $normalizedId = preg_replace('/^places\//', '', trim($placeId));
        if ($normalizedId === '') {
            return response()->json(['message' => 'Place ID is required.'], 422);
        }

        $apiKey = (string) config('interactive_map.places_api_key', '');
        if ($apiKey === '') {
            return response()->json(['message' => 'Places API key is not configured.'], 503);
        }

        $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/place/details/json', [
            'place_id' => $normalizedId,
            'key' => $apiKey,
            'fields' => 'place_id,name,formatted_address,geometry',
        ]);

        $data = $response->json();
        if (! is_array($data)) {
            return response()->json(['message' => 'Place details failed.'], 502);
        }

        $status = (string) ($data['status'] ?? '');
        if ($status !== 'OK') {
            return response()->json([
                'message' => (string) ($data['error_message'] ?? 'Place details failed.'),
            ], $status === 'REQUEST_DENIED' ? 403 : 502);
        }

        return response()->json($this->legacyPlaceDetailsPayload($data));
    }

    /** @param array<string, mixed> $data */
    private function legacyAutocompletePayload(array $data): array
    {
        $suggestions = [];

        foreach ($data['predictions'] ?? [] as $prediction) {
            if (! is_array($prediction) || empty($prediction['place_id'])) {
                continue;
            }

            $suggestions[] = [
                'placePrediction' => [
                    'placeId' => (string) $prediction['place_id'],
                    'text' => [
                        'text' => (string) ($prediction['description'] ?? $prediction['place_id']),
                    ],
                ],
            ];
        }

        return ['suggestions' => $suggestions];
    }

    /** @param array<string, mixed> $data */
    private function legacyPlaceDetailsPayload(array $data): array
    {
        $result = is_array($data['result'] ?? null) ? $data['result'] : [];
        $geometry = is_array($result['geometry'] ?? null) ? $result['geometry'] : [];
        $location = is_array($geometry['location'] ?? null) ? $geometry['location'] : [];
        $viewport = is_array($geometry['viewport'] ?? null) ? $geometry['viewport'] : null;

        $payload = [
            'displayName' => [
                'text' => (string) ($result['name'] ?? ''),
            ],
            'formattedAddress' => (string) ($result['formatted_address'] ?? ''),
            'location' => [
                'latitude' => (float) ($location['lat'] ?? 0),
                'longitude' => (float) ($location['lng'] ?? 0),
            ],
        ];

        if (is_array($viewport)
            && is_array($viewport['southwest'] ?? null)
            && is_array($viewport['northeast'] ?? null)) {
            $payload['viewport'] = [
                'low' => [
                    'latitude' => (float) $viewport['southwest']['lat'],
                    'longitude' => (float) $viewport['southwest']['lng'],
                ],
                'high' => [
                    'latitude' => (float) $viewport['northeast']['lat'],
                    'longitude' => (float) $viewport['northeast']['lng'],
                ],
            ];
        }

        return $payload;
    }

    /** @return array{label: string, back_url: string} */
    private function ownerContext(string $ownerType, int $ownerId): array
    {
        $model = $this->owners->findModel($ownerType, $ownerId);

        return match ($ownerType) {
            'projects' => [
                'label' => (string) $model->title,
                'back_url' => route('admin.projects.edit', $model),
            ],
            'dha-phases' => [
                'label' => (string) $model->title,
                'back_url' => route('admin.dha-phases.edit', $model),
            ],
            default => [
                'label' => 'Record #' . $ownerId,
                'back_url' => route('admin.dashboard'),
            ],
        };
    }

    private function logActivity(string $ownerType, int $ownerId, string $action, string $message): void
    {
        if (! $admin = admin_user()) {
            return;
        }

        ActivityLog::record($admin, $action, $message . ' (' . $ownerType . ' #' . $ownerId . ')');
    }
}
