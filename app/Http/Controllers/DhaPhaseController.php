<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DhaPhase;
use App\Models\ProjectType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DhaPhaseController extends Controller
{
    public function index()
    {
        $phases = DhaPhase::query()
            ->withCount(['properties as active_listings_count' => fn ($q) => $q->where('status', 'active')])
            ->frontOrdered()
            ->limit(100)
            ->get();

        return view('admin.dha_phases.index', compact('phases'));
    }

    public function create()
    {
        $phase = new DhaPhase(['status' => DhaPhase::STATUS_ACTIVE, 'map_zoom' => 14]);
        $projectTypes = ProjectType::orderBy('name')->get(['id', 'name']);
        $uploadToken = Str::uuid()->toString();
        session(['dha_phase_upload_token' => $uploadToken]);

        return view('admin.dha_phases.create', compact('phase', 'projectTypes', 'uploadToken'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePhase($request);
        $phase = DhaPhase::create(array_merge(
            $this->phaseAttributesFromValidated($validated),
            $this->phaseContentFromRequest($request)
        ));
        $phase->projectTypes()->sync($request->input('project_type_ids', []));
        $this->persistPhaseMedia($request, $phase);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'dha_phase_created', "DHA phase created: {$phase->title} (ID: {$phase->id}).");
        }

        return redirect()->route('admin.dha-phases.edit', $phase)->with('status', 'Phase created.');
    }

    public function edit(DhaPhase $dhaPhase)
    {
        $phase = $dhaPhase;
        $projectTypes = ProjectType::orderBy('name')->get(['id', 'name']);
        $selectedTypeIds = $phase->projectTypes()->pluck('project_types.id')->all();

        return view('admin.dha_phases.edit', compact('phase', 'projectTypes', 'selectedTypeIds'));
    }

    public function update(Request $request, DhaPhase $dhaPhase)
    {
        $validated = $this->validatePhase($request, $dhaPhase);
        $dhaPhase->update(array_merge(
            $this->phaseAttributesFromValidated($validated),
            $this->phaseContentFromRequest($request)
        ));
        $dhaPhase->projectTypes()->sync($request->input('project_type_ids', []));
        $this->persistPhaseMedia($request, $dhaPhase);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'dha_phase_updated', "DHA phase updated: {$dhaPhase->title} (ID: {$dhaPhase->id}).");
        }

        return redirect()->route('admin.dha-phases.edit', $dhaPhase)->with('status', 'Phase saved.');
    }

    public function destroy(DhaPhase $dhaPhase)
    {
        $title = $dhaPhase->title;
        $id = $dhaPhase->id;
        $dhaPhase->delete();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'dha_phase_deleted', "DHA phase deleted: {$title} (ID: {$id}).");
        }

        return redirect()->route('admin.dha-phases.index')->with('status', 'Phase deleted.');
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:featured,card,image_gallery,plot_maps,phase_pdf'],
            'file' => ['required', 'file', $request->type === 'phase_pdf' ? 'mimes:pdf' : 'image', 'max:20480'],
            'phase_id' => ['nullable', 'integer', 'exists:dha_phases,id'],
            'upload_token' => ['nullable', 'string', 'max:64'],
        ]);

        $subdir = match ($request->type) {
            'featured' => 'featured',
            'card' => 'card',
            'plot_maps' => 'plot-maps',
            'phase_pdf' => 'pdf',
            default => 'gallery',
        };

        if ($request->filled('phase_id')) {
            $base = 'dha/phases/' . (int) $request->phase_id . '/' . $subdir;
        } else {
            $token = $request->input('upload_token') ?: session('dha_phase_upload_token');
            if (!$token) {
                return response()->json(['success' => false, 'message' => 'Upload session expired. Please refresh the page.'], 422);
            }
            $base = 'dha/phases/staging/' . $token . '/' . $subdir;
        }

        $path = $request->file('file')->store($base, 'public');
        $this->mirrorToPublicStorage($path);

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . ltrim($path, '/')),
            'message' => $request->type === 'phase_pdf' ? 'PDF uploaded successfully.' : 'Image uploaded successfully.',
        ]);
    }

    /** @return array<string, mixed> */
    private function validatePhase(Request $request, ?DhaPhase $phase = null): array
    {
        $slugRule = ['nullable', 'string', 'max:255'];
        $slugRule[] = $phase
            ? 'unique:dha_phases,slug,' . $phase->id
            : 'unique:dha_phases,slug';

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => $slugRule,
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'hero_lead' => ['nullable', 'string', 'max:1000'],
            'stat_location' => ['nullable', 'string', 'max:255'],
            'stat_total_area' => ['nullable', 'string', 'max:255'],
            'stat_total_plots' => ['nullable', 'string', 'max:255'],
            'stat_year_developed' => ['nullable', 'string', 'max:64'],
            'features_content' => ['nullable', 'string'],
            'market_insights' => ['nullable', 'string'],
            'contact_intro' => ['nullable', 'string', 'max:1000'],
            'attractions_heading' => ['nullable', 'string', 'max:255'],
            'help_bar_eyebrow' => ['nullable', 'string', 'max:255'],
            'help_bar_title' => ['nullable', 'string', 'max:255'],
            'help_bar_text' => ['nullable', 'string', 'max:1000'],
            'highlight_tag_primary' => ['nullable', 'string', 'max:64'],
            'highlight_tag_secondary' => ['nullable', 'string', 'max:64'],
            'highlight_location' => ['nullable', 'string', 'max:255'],
            'highlight_total_views' => ['nullable', 'string', 'max:64'],
            'highlight_developed_year' => ['nullable', 'string', 'max:64'],
            'highlight_register_title' => ['nullable', 'string', 'max:128'],
            'highlight_register_text' => ['nullable', 'string', 'max:500'],
            'highlight_register_url' => ['nullable', 'string', 'max:500'],
            'value_props' => ['nullable', 'array'],
            'value_props.*.title' => ['nullable', 'string', 'max:255'],
            'value_props.*.text' => ['nullable', 'string', 'max:500'],
            'value_props.*.icon' => ['nullable', 'string', 'max:64'],
            'attractions' => ['nullable', 'array'],
            'attractions.*.title' => ['nullable', 'string', 'max:255'],
            'attractions.*.text' => ['nullable', 'string', 'max:500'],
            'attractions.*.icon' => ['nullable', 'string', 'max:64'],
            'attractions.*.image' => ['nullable', 'string', 'max:500'],
            'invest_reasons' => ['nullable', 'array'],
            'invest_reasons.*.title' => ['nullable', 'string', 'max:255'],
            'invest_reasons.*.text' => ['nullable', 'string', 'max:500'],
            'invest_reasons.*.icon' => ['nullable', 'string', 'max:64'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'map_zoom' => ['nullable', 'integer', 'min:1', 'max:21'],
            'google_map' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'project_type_ids' => ['nullable', 'array'],
            'project_type_ids.*' => ['exists:project_types,id'],
            'video_gallery' => ['nullable', 'array'],
            'video_gallery.*' => ['nullable', 'string'],
            'image_gallery_paths' => ['nullable', 'array'],
            'image_gallery_paths.*' => ['nullable', 'string', 'max:500'],
            'plot_map_paths' => ['nullable', 'array'],
            'plot_map_paths.*' => ['nullable', 'string', 'max:500'],
            'plot_map_titles' => ['nullable', 'array'],
            'plot_map_titles.*' => ['nullable', 'string', 'max:255'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'featured_image_path' => ['nullable', 'string', 'max:500'],
            'remove_card_image' => ['nullable', 'boolean'],
            'card_image_path' => ['nullable', 'string', 'max:500'],
            'phase_pdf_path' => ['nullable', 'string', 'max:500'],
            'remove_phase_pdf' => ['nullable', 'boolean'],
            'vr_tour_url' => ['nullable', 'string', 'max:2000'],
            'show_map_button' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['slug'] ?? $validated['title']);
        $validated['status'] = $validated['status'] ?? DhaPhase::STATUS_ACTIVE;
        $validated['map_zoom'] = $validated['map_zoom'] ?? 14;
        $validated['sort_order'] = $validated['sort_order'] ?? ($phase?->sort_order ?? ((int) DhaPhase::max('sort_order') + 1));
        $validated['show_map_button'] = $request->boolean('show_map_button');

        return $validated;
    }

    /** @param array<string, mixed> $validated */
    private function phaseAttributesFromValidated(array $validated): array
    {
        return array_intersect_key($validated, array_flip((new DhaPhase())->getFillable()));
    }

    private function persistPhaseMedia(Request $request, DhaPhase $phase): void
    {
        $updates = [];

        if ($request->boolean('remove_featured_image')) {
            if ($phase->featured_image) {
                $this->deleteStoredFile($phase->featured_image);
            }
            $updates['featured_image'] = null;
        } elseif ($request->filled('featured_image_path')) {
            $updates['featured_image'] = $request->input('featured_image_path');
        }

        if ($request->boolean('remove_card_image')) {
            if ($phase->card_image) {
                $this->deleteStoredFile($phase->card_image);
            }
            $updates['card_image'] = null;
        } elseif ($request->filled('card_image_path')) {
            $updates['card_image'] = $request->input('card_image_path');
        }

        if ($request->boolean('remove_phase_pdf')) {
            if ($phase->phase_pdf) {
                $this->deleteStoredFile($phase->phase_pdf);
            }
            $updates['phase_pdf'] = null;
        } elseif ($request->filled('phase_pdf_path')) {
            $updates['phase_pdf'] = $request->input('phase_pdf_path');
        }

        $galleryPaths = array_values(array_filter((array) $request->input('image_gallery_paths', [])));
        $updates['image_gallery'] = array_map(fn ($path) => ['path' => $path], $galleryPaths);

        $plotPaths = (array) $request->input('plot_map_paths', []);
        $plotTitles = (array) $request->input('plot_map_titles', []);
        $plotMaps = [];
        foreach ($plotPaths as $i => $path) {
            $path = trim((string) $path);
            if ($path === '') {
                continue;
            }
            $plotMaps[] = [
                'path' => $path,
                'title' => trim((string) ($plotTitles[$i] ?? '')),
            ];
        }
        $updates['plot_maps'] = $plotMaps;

        $updates['video_gallery'] = array_values(array_filter(array_map('trim', (array) $request->input('video_gallery', []))));

        $phase->update($updates);
    }

    private function deleteStoredFile(?string $path): void
    {
        if (!$path) {
            return;
        }
        Storage::disk('public')->delete($path);
        File::delete(public_path('storage/' . ltrim($path, '/')));
    }

    /** @return array<string, mixed> */
    private function phaseContentFromRequest(Request $request): array
    {
        $highlights = array_filter([
            'tag_primary' => trim((string) $request->input('highlight_tag_primary', '')),
            'tag_secondary' => trim((string) $request->input('highlight_tag_secondary', '')),
            'location' => trim((string) $request->input('highlight_location', '')),
            'total_views' => trim((string) $request->input('highlight_total_views', '')),
            'developed_year' => trim((string) $request->input('highlight_developed_year', '')),
            'register_title' => trim((string) $request->input('highlight_register_title', '')),
            'register_text' => trim((string) $request->input('highlight_register_text', '')),
            'register_url' => trim((string) $request->input('highlight_register_url', '')),
        ], fn ($v) => $v !== '');

        return [
            'value_propositions' => $this->buildContentItems($request->input('value_props', [])),
            'attractions_heading' => $request->input('attractions_heading'),
            'attractions' => $this->buildContentItems($request->input('attractions', []), true),
            'investment_reasons' => $this->buildContentItems($request->input('invest_reasons', [])),
            'project_highlights' => $highlights !== [] ? $highlights : null,
            'help_bar_eyebrow' => $request->input('help_bar_eyebrow'),
            'help_bar_title' => $request->input('help_bar_title'),
            'help_bar_text' => $request->input('help_bar_text'),
        ];
    }

    /** @param array<int, array<string, mixed>> $rows */
    private function buildContentItems(array $rows, bool $withImage = false): ?array
    {
        $items = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            $text = trim((string) ($row['text'] ?? ''));
            $icon = trim((string) ($row['icon'] ?? ''));
            if ($title === '' && $text === '' && $icon === '') {
                continue;
            }
            $item = [
                'title' => $title,
                'text' => $text,
                'icon' => $icon,
            ];
            if ($withImage) {
                $item['image'] = trim((string) ($row['image'] ?? ''));
            }
            $items[] = $item;
        }

        return $items !== [] ? $items : null;
    }

    private function mirrorToPublicStorage(string $storedPath): void
    {
        $source = storage_path('app/public/' . ltrim($storedPath, '/'));
        $destination = public_path('storage/' . ltrim($storedPath, '/'));
        if (!File::exists($source)) {
            return;
        }
        File::ensureDirectoryExists(dirname($destination));
        File::copy($source, $destination);
    }
}
