<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Property;
use App\Models\Dealer;
use App\Models\ProjectType;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    private const LISTING_OWN = 'own';
    private const LISTING_DEALER = 'dealer';

    private function index(bool $dealerListings): \Illuminate\Contracts\View\View
    {
        $query = Property::with(['projectTypes', 'dealer']);
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
        $properties = $query->orderBy('id', 'desc')->limit(2000)->get();
        $pageTitle = $dealerListings ? 'Dealer listings' : 'Own listings';
        $routePrefix = $dealerListings ? 'admin.dealer-listings' : 'admin.own-listings';
        $projectTypes = ProjectType::orderBy('name')->get();
        $filterStatus = request('status');
        $filterProjectType = request('project_type');
        $filterPropertyType = request('property_type');
        $filterPurpose = request('purpose');
        return view('admin.properties.index', compact('properties', 'pageTitle', 'routePrefix', 'filterDealer', 'projectTypes', 'filterStatus', 'filterProjectType', 'filterPropertyType', 'filterPurpose'));
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
        return view('admin.properties.create', compact('listingType', 'projectTypes', 'dealers', 'states', 'property', 'routePrefix', 'pageTitle', 'preselectedDealer'));
    }

    public function createOwn()
    {
        return $this->create(false);
    }

    public function createDealer()
    {
        return $this->create(true);
    }

    private function store(Request $request, bool $dealerListings): \Illuminate\Http\RedirectResponse
    {
        $dealerId = $dealerListings ? (int) $request->input('dealer_id', 0) : 0;
        if ($dealerListings) {
            $request->validate(['dealer_id' => ['required', 'integer', 'min:1', 'exists:dealers,id']]);
        }
        $validated = $this->validateProperty($request, null);
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
        return view('admin.properties.edit', compact('property', 'listingType', 'projectTypes', 'dealers', 'states', 'routePrefix', 'pageTitle'));
    }

    public function editOwn(Property $property)
    {
        return $this->edit($property, false);
    }

    public function editDealer(Property $property)
    {
        return $this->edit($property, true);
    }

    private function update(Request $request, Property $property, bool $dealerListings): \Illuminate\Http\RedirectResponse
    {
        $dealerId = $dealerListings ? (int) $request->input('dealer_id', 0) : 0;
        $validated = $this->validateProperty($request, $property);
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
        return redirect()->route($editRoute, $property)->with('status', 'Listing updated.');
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
        ]);
    }

    protected function buildPropertyData(Request $request, array $validated, ?Property $property, int $dealerId): array
    {
        $data = array_merge($validated, [
            'dealer_id' => $dealerId,
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
        $base = 'properties/' . $property->id;

        if ($request->boolean('remove_featured_image') && $property->featured_image) {
            Storage::disk($disk)->delete($property->featured_image);
            $property->update(['featured_image' => null]);
        }
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store($base, $disk);
            $property->update(['featured_image' => $path]);
        }

        $gallery = $property->gallery ?? [];
        $paths = (array) $request->input('gallery_paths', []);
        $order = (array) $request->input('gallery_order', []);
        $remove = (array) $request->input('gallery_remove', []);
        $out = [];
        foreach ($paths as $i => $path) {
            $path = trim($path);
            if ($path === '' || in_array($path, $remove, true)) continue;
            $out[] = ['path' => $path, 'order' => isset($order[$i]) ? (int) $order[$i] : $i];
        }
        foreach ($remove as $path) {
            if ($path) Storage::disk($disk)->delete($path);
        }
        usort($out, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        if ($request->hasFile('gallery_images')) {
            $files = $request->file('gallery_images');
            $files = is_array($files) ? $files : [$files];
            $startOrder = empty($out) ? 0 : (max(array_column($out, 'order')) + 1);
            foreach ($files as $i => $file) {
                if (!$file || !$file->isValid()) continue;
                $path = $file->store($base . '/gallery', $disk);
                $out[] = ['path' => $path, 'order' => $startOrder + $i];
            }
        }
        $property->update(['gallery' => $out]);
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
