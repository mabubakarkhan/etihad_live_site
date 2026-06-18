<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPropertySectionUpdates;
use App\Models\ActivityLog;
use App\Models\Property;
use App\Models\Dealer;
use App\Models\ProjectType;
use App\Models\State;
use App\Models\DhaPhase;
use App\Support\PropertyEditSections;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PropertyController extends Controller
{
    use HandlesPropertySectionUpdates;

    private const LISTING_OWN = 'own';
    private const LISTING_DEALER = 'dealer';

    private function index(bool $dealerListings): \Illuminate\Contracts\View\View
    {
        $query = Property::with(['projectTypes', 'dealer', 'dhaPhase:id,title,slug']);
        $filterDealer = null;
        if ($dealerListings) {
            $query->where('dealer_id', '!=', 0);
            $dealerId = request('dealer');
            if ($dealerId) {
                $query->where('dealer_id', (int) $dealerId);
                $filterDealer = Dealer::find((int) $dealerId);
            }
        }
        if (!$dealerListings) {
            $query->where('dealer_id', 0);
        }
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        if (request()->filled('project_type')) {
            $query->whereHas('projectTypes', function ($q) {
                $q->where('project_types.id', request('project_type'));
            });
        }
        if (request()->filled('property_type')) {
            $query->where('property_type', request('property_type'));
        }
        if (request()->filled('purpose')) {
            $query->where('purpose', request('purpose'));
        }
        if (request()->filled('dha_phase')) {
            $query->where('dha_phase_id', (int) request('dha_phase'));
        }
        $properties = $query->frontOrdered()->limit(2000)->get();
        $pageTitle = $dealerListings ? 'Dealer listings' : 'Own listings';
        $routePrefix = $dealerListings ? 'admin.dealer-listings' : 'admin.own-listings';
        $projectTypes = ProjectType::orderBy('name')->get();
        $filterStatus = request('status');
        $filterProjectType = request('project_type');
        $filterPropertyType = request('property_type');
        $filterPurpose = request('purpose');
        $dhaPhases = DhaPhase::frontOrdered()->get(['id', 'title', 'slug']);
        $filterDhaPhase = request('dha_phase');
        return view('admin.properties.index', compact('properties', 'pageTitle', 'routePrefix', 'filterDealer', 'projectTypes', 'filterStatus', 'filterProjectType', 'filterPropertyType', 'filterPurpose', 'dhaPhases', 'filterDhaPhase'));
    }

    public function indexOwn()
    {
        return $this->index(false);
    }

    public function indexDealer()
    {
        return $this->index(true);
    }

    private function create(bool $dealerListings): \Illuminate\Contracts\View\View
    {
        $listingType = $dealerListings ? self::LISTING_DEALER : self::LISTING_OWN;
        $projectTypes = ProjectType::orderBy('name')->get();
        $dealers = $dealerListings ? Dealer::active()->orderBy('name')->get() : collect();
        $states = State::with('cities')->orderBy('sort_order')->orderBy('name')->get();
        $property = new Property();
        $routePrefix = $dealerListings ? 'admin.dealer-listings' : 'admin.own-listings';
        $pageTitle = $dealerListings ? 'Add dealer listing' : 'Add own listing';
        $preselectedDealer = $dealerListings && request('dealer') ? Dealer::find((int) request('dealer')) : null;
        $uploadToken = session('property_upload_token', Str::uuid()->toString());
        session(['property_upload_token' => $uploadToken]);
        $dhaPhases = DhaPhase::frontOrdered()->get(['id', 'title', 'slug']);
        return view('admin.properties.create', compact('listingType', 'projectTypes', 'dealers', 'states', 'property', 'routePrefix', 'pageTitle', 'preselectedDealer', 'uploadToken', 'dhaPhases'));
    }

    public function createOwn()
    {
        return $this->create(false);
    }

    public function createDealer()
    {
        return $this->create(true);
    }

    private function store(Request $request, bool $dealerListings): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        try {
            $dealerId = $dealerListings ? (int) $request->input('dealer_id', 0) : 0;
            if ($dealerListings) {
                $request->validate(['dealer_id' => ['required', 'integer', 'min:1', 'exists:dealers,id']]);
            }
            $validated = $this->validateProperty($request, null);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        }
        $data = $this->buildPropertyData($request, $validated, null, $dealerId);
        $data['status'] = $data['status'] ?? 'active';
        $data['purpose'] = $data['purpose'] ?? 'sale';
        $projectTypeIds = $request->input('project_type_ids', []);
        unset($data['project_type_ids']);
        $property = Property::create($data);
        $property->projectTypes()->sync($projectTypeIds);
        $this->handlePropertyFiles($request, $property);
        $listingLabel = $dealerListings ? 'Dealer listing' : 'Own listing';
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'listing_created', "{$listingLabel} created: {$property->title} (ID: {$property->id}, slug: {$property->slug}).");
        }
        $editRoute = $dealerListings ? 'admin.dealer-listings.edit' : 'admin.own-listings.edit';
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route($editRoute, $property),
                'message' => 'Listing created.',
            ]);
        }
        return redirect()->route($editRoute, $property)->with('status', 'Listing created.');
    }

    public function storeOwn(Request $request)
    {
        return $this->store($request, false);
    }

    public function storeDealer(Request $request)
    {
        return $this->store($request, true);
    }

    private function edit(Property $property, bool $dealerListings): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        $isDealer = $property->dealer_id != 0;
        if ($dealerListings && !$isDealer) {
            return redirect()->route('admin.own-listings.edit', $property);
        }
        if (!$dealerListings && $isDealer) {
            return redirect()->route('admin.dealer-listings.edit', $property);
        }
        $listingType = $dealerListings ? self::LISTING_DEALER : self::LISTING_OWN;
        $projectTypes = ProjectType::orderBy('name')->get();
        $dealers = $dealerListings ? Dealer::active()->orderBy('name')->get() : collect();
        $states = State::with('cities')->orderBy('sort_order')->orderBy('name')->get();
        $routePrefix = $dealerListings ? 'admin.dealer-listings' : 'admin.own-listings';
        $pageTitle = $dealerListings ? 'Edit dealer listing' : 'Edit own listing';
        $dhaPhases = DhaPhase::frontOrdered()->get(['id', 'title', 'slug']);
        $property->load('dhaPhase:id,title,slug');
        return view('admin.properties.edit', compact('property', 'listingType', 'projectTypes', 'dealers', 'states', 'routePrefix', 'pageTitle', 'dhaPhases'));
    }

    public function editOwn(Property $property)
    {
        return $this->edit($property, false);
    }

    public function editDealer(Property $property)
    {
        return $this->edit($property, true);
    }

    private function editSection(Property $property, string $section, bool $dealerListings): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        if (!PropertyEditSections::exists($section)) {
            abort(404);
        }
        $isDealer = $property->dealer_id != 0;
        if ($dealerListings && !$isDealer) {
            return redirect()->route('admin.own-listings.edit-section', [$property, $section]);
        }
        if (!$dealerListings && $isDealer) {
            return redirect()->route('admin.dealer-listings.edit-section', [$property, $section]);
        }
        $listingType = $dealerListings ? self::LISTING_DEALER : self::LISTING_OWN;
        $projectTypes = ProjectType::orderBy('name')->get();
        $dealers = $dealerListings ? Dealer::active()->orderBy('name')->get() : collect();
        $states = State::with('cities')->orderBy('sort_order')->orderBy('name')->get();
        $routePrefix = $dealerListings ? 'admin.dealer-listings' : 'admin.own-listings';
        $pageTitle = $dealerListings ? 'Edit dealer listing' : 'Edit own listing';
        $sections = PropertyEditSections::all();
        $dhaPhases = DhaPhase::frontOrdered()->get(['id', 'title', 'slug']);
        return view('admin.properties.edit-section', compact(
            'property', 'listingType', 'projectTypes', 'dealers', 'states',
            'routePrefix', 'pageTitle', 'section', 'sections', 'dhaPhases'
        ));
    }

    public function editSectionOwn(Property $property, string $section)
    {
        return $this->editSection($property, $section, false);
    }

    public function editSectionDealer(Property $property, string $section)
    {
        return $this->editSection($property, $section, true);
    }

    private function loadSection(Property $property, string $section, bool $dealerListings): JsonResponse
    {
        if (!PropertyEditSections::exists($section)) {
            abort(404);
        }
        $this->resolvePropertyForUpload($property->id, $dealerListings);
        $listingType = $dealerListings ? self::LISTING_DEALER : self::LISTING_OWN;
        $projectTypes = ProjectType::orderBy('name')->get();
        $dealers = $dealerListings ? Dealer::active()->orderBy('name')->get() : collect();
        $states = State::with('cities')->orderBy('sort_order')->orderBy('name')->get();
        $dhaPhases = DhaPhase::frontOrdered()->get(['id', 'title', 'slug']);
        $html = view('admin.properties._section_fragment', [
            'property' => $property,
            'listingType' => $listingType,
            'projectTypes' => $projectTypes,
            'dealers' => $dealers,
            'states' => $states,
            'dhaPhases' => $dhaPhases,
            'section' => $section,
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'section' => $section,
            'label' => PropertyEditSections::all()[$section]['label'] ?? $section,
        ]);
    }

    public function loadSectionOwn(Property $property, string $section): JsonResponse
    {
        return $this->loadSection($property, $section, false);
    }

    public function loadSectionDealer(Property $property, string $section): JsonResponse
    {
        return $this->loadSection($property, $section, true);
    }

    private function updateSection(Request $request, Property $property, string $section, bool $dealerListings): JsonResponse
    {
        if (!PropertyEditSections::exists($section)) {
            abort(404);
        }
        $dealerId = $dealerListings ? (int) $request->input('dealer_id', $property->dealer_id) : 0;
        try {
            $validated = $this->validatePropertySection($request, $property, $section);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }
        $this->applyPropertySection($request, $property, $section, $validated, $dealerId);
        $property->refresh();
        $listingLabel = $dealerListings ? 'Dealer listing' : 'Own listing';
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'listing_section_updated', "{$listingLabel} section \"{$section}\" updated: {$property->title} (ID: {$property->id}).");
        }
        return response()->json([
            'success' => true,
            'message' => (PropertyEditSections::all()[$section]['label'] ?? $section) . ' saved.',
            'section' => $section,
        ]);
    }

    public function updateSectionOwn(Request $request, Property $property, string $section): JsonResponse
    {
        return $this->updateSection($request, $property, $section, false);
    }

    public function updateSectionDealer(Request $request, Property $property, string $section): JsonResponse
    {
        return $this->updateSection($request, $property, $section, true);
    }

    private function update(Request $request, Property $property, bool $dealerListings): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        try {
            $dealerId = $dealerListings ? (int) $request->input('dealer_id', 0) : 0;
            $validated = $this->validateProperty($request, $property);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        }
        $data = $this->buildPropertyData($request, $validated, $property, $dealerId);
        $this->handlePropertyFiles($request, $property);
        unset($data['gallery'], $data['project_type_ids']);
        $property->update($data);
        $property->projectTypes()->sync($request->input('project_type_ids', []));
        $listingLabel = $dealerListings ? 'Dealer listing' : 'Own listing';
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'listing_updated', "{$listingLabel} updated: {$property->title} (ID: {$property->id}, slug: {$property->slug}).");
        }
        $editRoute = $dealerListings ? 'admin.dealer-listings.edit' : 'admin.own-listings.edit';
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Listing updated.',
            ]);
        }
        return redirect()->route($editRoute, $property)->with('status', 'Listing updated.');
    }

    public function uploadMediaOwn(Request $request): JsonResponse
    {
        return $this->uploadMedia($request, false);
    }

    public function uploadMediaDealer(Request $request): JsonResponse
    {
        return $this->uploadMedia($request, true);
    }

    private function uploadMedia(Request $request, bool $dealerListings): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:featured,gallery'],
            'file' => ['required', 'image', 'max:10240'],
            'property_id' => ['nullable', 'integer', 'exists:properties,id'],
            'upload_token' => ['nullable', 'string', 'max:64'],
        ]);

        $property = null;
        if ($request->filled('property_id')) {
            $property = $this->resolvePropertyForUpload((int) $request->input('property_id'), $dealerListings);
        }

        $type = $request->input('type');
        $subdir = $type === 'gallery' ? 'gallery' : 'featured';
        if ($property) {
            $base = 'properties/' . $property->id . '/' . $subdir;
        } else {
            $token = $request->input('upload_token') ?: session('property_upload_token');
            if (!$token) {
                return response()->json(['success' => false, 'message' => 'Upload session expired. Please refresh the page.'], 422);
            }
            $base = 'properties/staging/' . $token . '/' . $subdir;
        }

        $path = $request->file('file')->store($base, 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
            'message' => 'Image uploaded successfully.',
        ]);
    }

    private function resolvePropertyForUpload(int $propertyId, bool $dealerListings): Property
    {
        $property = Property::findOrFail($propertyId);
        $isDealer = $property->dealer_id != 0;
        if ($dealerListings && !$isDealer) {
            abort(403);
        }
        if (!$dealerListings && $isDealer) {
            abort(403);
        }
        return $property;
    }

    public function updateOwn(Request $request, Property $property)
    {
        return $this->update($request, $property, false);
    }

    public function updateDealer(Request $request, Property $property)
    {
        return $this->update($request, $property, true);
    }

    private function destroy(Property $property, bool $dealerListings): \Illuminate\Http\RedirectResponse
    {
        $title = $property->title;
        $id = $property->id;
        $slug = $property->slug;
        $listingLabel = $dealerListings ? 'Dealer listing' : 'Own listing';
        $this->deletePropertyFiles($property);
        $property->delete();
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'listing_deleted', "{$listingLabel} deleted: {$title} (ID: {$id}, slug: {$slug}).");
        }
        $indexRoute = $dealerListings ? 'admin.dealer-listings.index' : 'admin.own-listings.index';
        return redirect()->route($indexRoute)->with('status', 'Listing deleted.');
    }

    public function destroyOwn(Property $property)
    {
        return $this->destroy($property, false);
    }

    public function destroyDealer(Property $property)
    {
        return $this->destroy($property, true);
    }

    public function preview(Property $property): \Illuminate\Contracts\View\View
    {
        $property->load(['projectTypes', 'dealer']);
        $routePrefix = $property->dealer_id != 0 ? 'admin.dealer-listings' : 'admin.own-listings';
        return view('admin.properties.preview', compact('property', 'routePrefix'));
    }

    private function duplicate(Property $property, bool $dealerListings): \Illuminate\Http\RedirectResponse
    {
        $base = preg_replace('/-\d+$/', '', $property->slug);
        $maxSuffix = Property::where('slug', 'like', $base . '-%')
            ->where('id', '!=', $property->id)
            ->pluck('slug')
            ->map(function ($slug) use ($base) {
                return preg_match('/^' . preg_quote($base, '/') . '-(\d+)$/', $slug, $m) ? (int) $m[1] : null;
            })
            ->filter()
            ->max();
        $newSlug = $base . '-' . ($maxSuffix ? $maxSuffix + 1 : 2);

        $newProperty = $property->replicate();
        $newProperty->slug = $newSlug;
        $newProperty->title = $property->title . ' (Copy)';
        $newProperty->featured_image = null;
        $newProperty->gallery = null;
        $newProperty->video_gallery = $property->video_gallery;
        $newProperty->videos = $property->videos;
        $newProperty->sort_order = (int) Property::max('sort_order') + 1;
        $newProperty->save();
        $newProperty->projectTypes()->sync($property->projectTypes->pluck('id')->toArray());

        if ($admin = admin_user()) {
            $label = $dealerListings ? 'Dealer listing' : 'Own listing';
            ActivityLog::record($admin, 'listing_duplicated', "{$label} duplicated: {$property->title} (ID: {$property->id}) → new ID: {$newProperty->id}, slug: {$newProperty->slug}.");
        }

        $editRoute = $dealerListings ? 'admin.dealer-listings.edit' : 'admin.own-listings.edit';
        return redirect()->route($editRoute, $newProperty)->with('status', 'Listing duplicated.');
    }

    public function duplicateOwn(Property $property)
    {
        if ($property->dealer_id != 0) {
            return redirect()->route('admin.dealer-listings.edit', $property);
        }
        return $this->duplicate($property, false);
    }

    public function duplicateDealer(Property $property)
    {
        if ($property->dealer_id == 0) {
            return redirect()->route('admin.own-listings.edit', $property);
        }
        return $this->duplicate($property, true);
    }

    protected function validateProperty(Request $request, ?Property $property): array
    {
        $slugRule = ['nullable', 'string', 'max:255'];
        if ($property) {
            $slugRule[] = 'unique:properties,slug,' . $property->id;
        } else {
            $slugRule[] = 'unique:properties,slug';
        }
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,hold,inactive,close'],
            'is_hot' => ['nullable', 'boolean'],
            'slug' => $slugRule,
            'project_type_ids' => ['nullable', 'array'],
            'project_type_ids.*' => ['exists:project_types,id'],
            'purpose' => ['nullable', 'string', 'in:sale,rent'],
            'dealer_id' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'short_address' => ['nullable', 'string', 'max:500'],
            'town' => ['nullable', 'string', 'max:255'],
            'is_dha_property' => ['nullable', 'boolean'],
            'dha_phase_id' => ['nullable', 'integer', 'exists:dha_phases,id'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'google_map' => ['nullable', 'string'],
            'price_string' => ['nullable', 'string', 'max:255'],
            'price_digits' => ['nullable', 'numeric', 'min:0'],
            'property_type' => ['nullable', 'string', 'in:plot,home,plaza,flat,apartment,file'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'garage' => ['nullable', 'integer', 'min:0', 'max:20'],
            'kitchen' => ['nullable', 'integer', 'min:0', 'max:20'],
            'area_marla' => ['nullable', 'numeric', 'min:0'],
            'area_kanal' => ['nullable', 'numeric', 'min:0'],
            'amenities_description' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'featured_image_path' => ['nullable', 'string', 'max:500'],
            'gallery_paths' => ['nullable', 'array'],
            'gallery_paths.*' => ['nullable', 'string', 'max:500'],
            'upload_token' => ['nullable', 'string', 'max:64'],
        ]);
    }

    protected function buildPropertyData(Request $request, array $validated, ?Property $property, int $dealerId): array
    {
        $data = array_merge($validated, [
            'dealer_id' => $dealerId,
            'dha_phase_id' => $request->boolean('is_dha_property') && $request->filled('dha_phase_id')
                ? (int) $request->input('dha_phase_id')
                : null,
            'is_hot' => $request->boolean('is_hot'),
            'purpose' => $request->input('purpose', 'sale'),
            'features' => array_values(array_filter(array_map('trim', (array) $request->input('features', [])))),
            'location_accessibility' => array_values(array_filter(array_map('trim', (array) $request->input('location_accessibility', [])))),
            'nearest_hospitals' => array_values(array_filter(array_map('trim', (array) $request->input('nearest_hospitals', [])))),
            'nearest_markets' => array_values(array_filter(array_map('trim', (array) $request->input('nearest_markets', [])))),
            'nearest_restaurants' => array_values(array_filter(array_map('trim', (array) $request->input('nearest_restaurants', [])))),
            'videos' => array_values(array_filter(array_map('trim', (array) $request->input('videos', [])))),
            'video_gallery' => array_values(array_filter(array_map('trim', (array) $request->input('video_gallery', [])))),
            'amenities' => $this->normalizeAmenities($request),
        ]);

        $areaMarla = $request->input('area_marla');
        $areaKanal = $request->input('area_kanal');
        if ($areaMarla !== null && $areaMarla !== '') {
            $marla = (float) $areaMarla;
            $data['area_marla'] = $marla;
            $data['area_kanal'] = round($marla / 20, 2);
        } elseif ($areaKanal !== null && $areaKanal !== '') {
            $kanal = (float) $areaKanal;
            $data['area_kanal'] = $kanal;
            $data['area_marla'] = round($kanal * 20, 2);
        }

        $rawSlug = trim((string) ($data['slug'] ?? ''));
        $data['slug'] = $rawSlug !== '' ? Str::slug($rawSlug) : Str::slug($data['title']);
        if ($data['slug'] === '') {
            $data['slug'] = Str::slug($data['title']);
        }
        $base = $data['slug'];
        $i = 1;
        while (Property::where('slug', $data['slug'])->where('id', '!=', $property?->id ?? 0)->exists()) {
            $data['slug'] = $base . '-' . $i++;
        }

        return $data;
    }

    protected function normalizeAmenities(Request $request): array
    {
        $titles = (array) $request->input('amenity_titles', []);
        $icons = (array) $request->input('amenity_icons', []);
        $out = [];
        foreach ($titles as $i => $title) {
            $title = trim($title ?? '');
            if ($title === '') continue;
            $out[] = ['title' => $title, 'icon' => $icons[$i] ?? ''];
        }
        return $out;
    }

    protected function handlePropertyFiles(Request $request, Property $property): void
    {
        $disk = 'public';
        $uploadToken = $request->input('upload_token');
        $updates = [];

        if ($request->boolean('remove_featured_image')) {
            if ($property->featured_image) {
                Storage::disk($disk)->delete($property->featured_image);
            }
            $updates['featured_image'] = null;
        } elseif ($request->filled('featured_image_path')) {
            $path = trim((string) $request->input('featured_image_path'));
            if ($this->isAllowedMediaPath($path, $property->id, $uploadToken)) {
                $finalPath = $this->finalizeMediaPath($path, $property->id, $uploadToken);
                if ($property->featured_image && $property->featured_image !== $finalPath) {
                    Storage::disk($disk)->delete($property->featured_image);
                }
                $updates['featured_image'] = $finalPath;
            }
        }

        $paths = (array) $request->input('gallery_paths', []);
        $order = (array) $request->input('gallery_order', []);
        $remove = (array) $request->input('gallery_remove', []);
        $out = [];
        foreach ($paths as $i => $path) {
            $path = trim((string) $path);
            if ($path === '' || in_array($path, $remove, true)) {
                continue;
            }
            if (!$this->isAllowedMediaPath($path, $property->id, $uploadToken)) {
                continue;
            }
            $finalPath = $this->finalizeMediaPath($path, $property->id, $uploadToken);
            $out[] = ['path' => $finalPath, 'order' => isset($order[$i]) ? (int) $order[$i] : $i];
        }
        foreach ($remove as $path) {
            if ($path && Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
        usort($out, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        $updates['gallery'] = $out;

        if (!empty($updates)) {
            $property->update($updates);
        }

        if ($uploadToken) {
            Storage::disk($disk)->deleteDirectory('properties/staging/' . $uploadToken);
        }
    }

    protected function isAllowedMediaPath(string $path, int $propertyId, ?string $uploadToken): bool
    {
        if (str_starts_with($path, 'properties/' . $propertyId . '/')) {
            return true;
        }
        if ($uploadToken && str_starts_with($path, 'properties/staging/' . $uploadToken . '/')) {
            return true;
        }
        return false;
    }

    protected function finalizeMediaPath(string $path, int $propertyId, ?string $uploadToken): string
    {
        if (!$uploadToken || !str_starts_with($path, 'properties/staging/' . $uploadToken . '/')) {
            return $path;
        }

        $relative = substr($path, strlen('properties/staging/' . $uploadToken . '/'));
        $destDir = str_starts_with($relative, 'gallery/')
            ? 'properties/' . $propertyId . '/gallery'
            : 'properties/' . $propertyId . '/featured';
        $filename = basename($path);
        $newPath = $destDir . '/' . $filename;
        Storage::disk('public')->makeDirectory($destDir);
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->move($path, $newPath);
        }
        return $newPath;
    }

    protected function deletePropertyFiles(Property $property): void
    {
        if ($property->featured_image) {
            Storage::disk('public')->delete($property->featured_image);
        }
        foreach ($property->gallery ?? [] as $g) {
            if (!empty($g['path'])) Storage::disk('public')->delete($g['path']);
        }
        Storage::disk('public')->deleteDirectory('properties/' . $property->id);
    }
}
