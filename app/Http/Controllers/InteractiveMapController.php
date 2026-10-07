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

    public function dhaPhasesHub(): View
    {
        $phases = DhaPhase::query()
            ->with(['interactiveMap.sections'])
            ->frontOrdered()
            ->get();

        return view('admin.interactive-map.dha-phases', [
            'phases' => $phases,
        ]);
    }

    public function edit(string $ownerType, int $ownerId): View
    {
        $context = $this->ownerContext($ownerType, $ownerId);
        $map = $this->maps->findOrCreateForOwner($ownerType, $ownerId);
        $map->load('sections');

        $phaseList = [];
        if ($ownerType === 'dha-phases') {
            $phaseList = DhaPhase::query()
                ->frontOrdered()
                ->get(['id', 'title', 'slug', 'status', 'sort_order'])
                ->map(fn (DhaPhase $phase) => [
                    'id' => $phase->id,
                    'title' => $phase->title,
                    'slug' => $phase->slug,
                    'status' => $phase->status,
                    'url' => route('admin.dha-phases.interactive-map', $phase),
                    'is_current' => (int) $phase->id === (int) $ownerId,
                ])
                ->values()
                ->all();
        }

        return view('admin.interactive-map.edit', [
            'ownerType' => $ownerType,
            'ownerId' => $ownerId,
            'ownerLabel' => $context['label'],
            'backUrl' => $context['back_url'],
            'hubUrl' => $context['hub_url'] ?? null,
            'map' => $map,
            'phaseList' => $phaseList,
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
            $apiKey = (string) config('app.google_maps_api_key', '');
        }
        if ($apiKey === '') {
            return response()->json(['message' => 'Places API key is not configured. Set INTERACTIVE_MAP_PLACES_API_KEY or GOOGLE_MAPS_API_KEY.'], 503);
        }

        // Places API (New) — legacy Place Autocomplete is unavailable on many new Google Cloud projects.
        $response = Http::timeout(10)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $apiKey,
            ])
            ->post('https://places.googleapis.com/v1/places:autocomplete', [
                'input' => $validated['input'],
                'includedRegionCodes' => ['pk'],
                'locationBias' => [
                    'circle' => [
                        'center' => [
                            'latitude' => 31.5204,
                            'longitude' => 74.3587,
                        ],
                        'radius' => 50000.0,
                    ],
                ],
            ]);

        $data = $response->json();
        if (! $response->successful() || ! is_array($data)) {
            $message = is_array($data)
                ? (string) (data_get($data, 'error.message') ?: data_get($data, 'error.status') ?: 'Places search failed.')
                : 'Places search failed.';

            return response()->json(['message' => $message], $response->status() === 403 ? 403 : 502);
        }

        return response()->json($this->newAutocompletePayload($data));
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
            $apiKey = (string) config('app.google_maps_api_key', '');
        }
        if ($apiKey === '') {
            return response()->json(['message' => 'Places API key is not configured. Set INTERACTIVE_MAP_PLACES_API_KEY or GOOGLE_MAPS_API_KEY.'], 503);
        }

        $response = Http::timeout(10)
            ->withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'id,displayName,formattedAddress,location,viewport',
            ])
            ->get('https://places.googleapis.com/v1/places/' . rawurlencode($normalizedId));

        $data = $response->json();
        if (! $response->successful() || ! is_array($data)) {
            $message = is_array($data)
                ? (string) (data_get($data, 'error.message') ?: data_get($data, 'error.status') ?: 'Place details failed.')
                : 'Place details failed.';

            return response()->json(['message' => $message], $response->status() === 403 ? 403 : 502);
        }

        return response()->json($this->newPlaceDetailsPayload($data));
    }

    /** @param array<string, mixed> $data */
    private function newAutocompletePayload(array $data): array
    {
        $suggestions = [];

        foreach ($data['suggestions'] ?? [] as $suggestion) {
            if (! is_array($suggestion)) {
                continue;
            }

            $prediction = is_array($suggestion['placePrediction'] ?? null)
                ? $suggestion['placePrediction']
                : null;

            if (! $prediction) {
                continue;
            }

            $placeId = (string) ($prediction['placeId'] ?? '');
            if ($placeId === '' && ! empty($prediction['place'])) {
                $placeId = (string) preg_replace('/^places\//', '', (string) $prediction['place']);
            }

            if ($placeId === '') {
                continue;
            }

            $text = is_array($prediction['text'] ?? null)
                ? (string) ($prediction['text']['text'] ?? $placeId)
                : $placeId;

            $suggestions[] = [
                'placePrediction' => [
                    'placeId' => $placeId,
                    'text' => [
                        'text' => $text,
                    ],
                ],
            ];
        }

        return ['suggestions' => $suggestions];
    }

    /** @param array<string, mixed> $data */
    private function newPlaceDetailsPayload(array $data): array
    {
        $displayName = is_array($data['displayName'] ?? null) ? $data['displayName'] : [];
        $location = is_array($data['location'] ?? null) ? $data['location'] : [];
        $viewport = is_array($data['viewport'] ?? null) ? $data['viewport'] : null;

        $payload = [
            'displayName' => [
                'text' => (string) ($displayName['text'] ?? ''),
            ],
            'formattedAddress' => (string) ($data['formattedAddress'] ?? ''),
            'location' => [
                'latitude' => (float) ($location['latitude'] ?? 0),
                'longitude' => (float) ($location['longitude'] ?? 0),
            ],
        ];

        if (is_array($viewport)
            && is_array($viewport['low'] ?? null)
            && is_array($viewport['high'] ?? null)) {
            $payload['viewport'] = [
                'low' => [
                    'latitude' => (float) ($viewport['low']['latitude'] ?? 0),
                    'longitude' => (float) ($viewport['low']['longitude'] ?? 0),
                ],
                'high' => [
                    'latitude' => (float) ($viewport['high']['latitude'] ?? 0),
                    'longitude' => (float) ($viewport['high']['longitude'] ?? 0),
                ],
            ];
        }

        return $payload;
    }

    /** @return array{label: string, back_url: string, hub_url?: string} */
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
                'hub_url' => route('admin.dha-phase-maps.index'),
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
