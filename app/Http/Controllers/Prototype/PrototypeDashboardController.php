<?php

namespace App\Http\Controllers\Prototype;

use App\Http\Controllers\Controller;
use App\Models\Prototype\PrototypeMapOverlay;
use App\Services\Prototype\PrototypeMapOverlayService;
use Illuminate\Http\Request;

class PrototypeDashboardController extends Controller
{
    public function __construct(
        private readonly PrototypeMapOverlayService $service
    ) {}

    public function dashboard()
    {
        $overlays = $this->service->listOverlays();

        return view('prototype.dashboard', [
            'overlays' => $overlays,
        ]);
    }

    public function interactiveMap(Request $request, ?PrototypeMapOverlay $overlay = null)
    {
        $overlays = $this->service->listOverlays();

        if ($overlay === null && $overlays->isNotEmpty()) {
            $overlay = $overlays->firstWhere('status', 'active') ?? $overlays->first();
        }

        if ($overlay) {
            $overlay->load(['sections' => function ($query) {
                $query->where('status', 'active');
            }]);
        }

        return view('prototype.interactive-map', [
            'overlays' => $overlays,
            'selected' => $overlay,
            'googleMapsApiKey' => config('app.google_maps_api_key', ''),
            'googleMapsMapId' => config('app.google_maps_map_id', 'DEMO_MAP_ID'),
        ]);
    }
}
