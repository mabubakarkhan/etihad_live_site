<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesProjectSectionUpdates;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\State;
use App\Support\ProjectEditSections;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    use HandlesProjectSectionUpdates;
    public function index()
    {
        $query = Project::with('projectTypes')->frontOrdered();
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }
        if (request()->filled('project_type')) {
            $query->whereHas('projectTypes', function ($q) {
                $q->where('project_types.id', request('project_type'));
            });
        }
        $projects = $query->limit(2000)->get();
        $projectTypes = ProjectType::orderBy('name')->get();
        $filterStatus = request('status');
        $filterProjectType = request('project_type');
        return view('admin.projects.index', compact('projects', 'projectTypes', 'filterStatus', 'filterProjectType'));
    }

    public function create()
    {
        $projectTypes = ProjectType::orderBy('name')->get();
        $states = State::with('cities')->orderBy('sort_order')->orderBy('name')->get();
        $uploadToken = session('project_upload_token', Str::uuid()->toString());
        session(['project_upload_token' => $uploadToken]);
        return view('admin.projects.create', compact('projectTypes', 'states', 'uploadToken'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        try {
            $validated = $this->validateProject($request);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        }
        $data = $this->buildProjectData($request, $validated, null);
        $data['slug'] = $this->uniqueProjectSlug(
            trim((string) ($data['slug'] ?? '')) !== '' ? (string) $data['slug'] : (string) $data['title']
        );
        $data['status'] = $data['status'] ?? 'active';
        $data['launch_year'] = $data['launch_year'] ?? 2023;
        unset($data['plans'], $data['gallery'], $data['pricing_place_cards']);
        $project = Project::create($data);
        $project->update($this->processFileUploads($request, $project));
        $project->projectTypes()->sync($request->input('project_type_ids', []));
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'project_created', "Project created: {$project->title} (ID: {$project->id}, slug: {$project->slug}).");
        }
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('admin.projects.edit', $project),
                'message' => 'Project created.',
            ]);
        }
        return redirect()->route('admin.projects.edit', $project)->with('status', 'Project created.');
    }

    public function preview(Project $project)
    {
        $project->load('projectTypes');
        return view('admin.projects.preview', compact('project'));
    }

    public function edit(Project $project)
    {
        $projectTypes = ProjectType::orderBy('name')->get();
        $states = State::with('cities')->orderBy('sort_order')->orderBy('name')->get();
        return view('admin.projects.edit', compact('project', 'projectTypes', 'states'));
    }

    public function editSection(Project $project, string $section)
    {
        if (!ProjectEditSections::exists($section)) {
            abort(404);
        }
        $projectTypes = ProjectType::orderBy('name')->get();
        $states = State::with('cities')->orderBy('sort_order')->orderBy('name')->get();
        $sections = ProjectEditSections::all();
        return view('admin.projects.edit-section', compact('project', 'projectTypes', 'states', 'section', 'sections'));
    }

    public function loadSection(Request $request, Project $project, string $section): JsonResponse
    {
        if (!ProjectEditSections::exists($section)) {
            abort(404);
        }
        $projectTypes = ProjectType::orderBy('name')->get();
        $states = State::with('cities')->orderBy('sort_order')->orderBy('name')->get();
        $html = view('admin.projects._section_fragment', [
            'project' => $project,
            'projectTypes' => $projectTypes,
            'states' => $states,
            'section' => $section,
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'section' => $section,
            'label' => ProjectEditSections::all()[$section]['label'] ?? $section,
        ]);
    }

    public function updateSection(Request $request, Project $project, string $section): JsonResponse
    {
        if (!ProjectEditSections::exists($section)) {
            abort(404);
        }
        try {
            $validated = $this->validateProjectSection($request, $project, $section);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }
        $this->applyProjectSection($request, $project, $section, $validated);
        $project->refresh();
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'project_section_updated', "Project section \"{$section}\" updated: {$project->title} (ID: {$project->id}).");
        }
        return response()->json([
            'success' => true,
            'message' => (ProjectEditSections::all()[$section]['label'] ?? $section) . ' saved.',
            'section' => $section,
        ]);
    }

    public function update(Request $request, Project $project): \Illuminate\Http\RedirectResponse|JsonResponse
    {
        try {
            $validated = $this->validateProject($request, $project);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        }
        $data = $this->buildProjectData($request, $validated, $project);
        if ($request->boolean('remove_featured_video')) {
            $data['featured_youtube_url'] = null;
            $data['featured_video_title'] = null;
            $data['featured_video_description'] = null;
        }
        $data['slug'] = $this->uniqueProjectSlug(
            trim((string) ($data['slug'] ?? '')) !== '' ? (string) $data['slug'] : (string) $data['title'],
            $project->id
        );
        unset($data['plans'], $data['gallery'], $data['pricing_place_cards']);
        $project->update(array_merge($data, $this->processFileUploads($request, $project)));
        $project->projectTypes()->sync($request->input('project_type_ids', []));
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'project_updated', "Project updated: {$project->title} (ID: {$project->id}, slug: {$project->slug}).");
        }
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Project updated.',
            ]);
        }
        return redirect()->route('admin.projects.edit', $project)->with('status', 'Project updated.');
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $type = $request->input('type');
        $fileRules = ['required', 'file', 'max:20480'];
        if ($type === 'project_file_pdf') {
            $fileRules[] = 'mimes:pdf';
        } else {
            $fileRules[] = 'image';
        }

        $request->validate([
            'type' => ['required', 'string', 'in:logo,featured_image,homepage_listing_image,address_image,developer_logo,noc_planning_image,invest_image,project_file_pdf,gallery,plan,pricing_place'],
            'file' => $fileRules,
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'upload_token' => ['nullable', 'string', 'max:64'],
        ]);

        $dirMap = [
            'logo' => 'logo',
            'featured_image' => 'featured',
            'homepage_listing_image' => 'homepage',
            'address_image' => 'address',
            'developer_logo' => 'developer_logo',
            'noc_planning_image' => 'noc',
            'invest_image' => 'invest',
            'project_file_pdf' => 'pdf',
            'gallery' => 'gallery',
            'plan' => 'plans',
            'pricing_place' => 'pricing-place',
        ];
        $subdir = $dirMap[$type] ?? 'misc';

        if ($request->filled('project_id')) {
            $base = 'projects/' . (int) $request->input('project_id') . '/' . $subdir;
        } else {
            $token = $request->input('upload_token') ?: session('project_upload_token');
            if (!$token) {
                return response()->json(['success' => false, 'message' => 'Upload session expired. Please refresh the page.'], 422);
            }
            $base = 'projects/staging/' . $token . '/' . $subdir;
        }

        $path = $request->file('file')->store($base, 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
            'message' => $type === 'project_file_pdf' ? 'PDF uploaded successfully.' : 'Image uploaded successfully.',
        ]);
    }

    public function destroy(Project $project)
    {
        $title = $project->title;
        $id = $project->id;
        $slug = $project->slug;
        $this->deleteProjectFiles($project);
        $project->delete();
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'project_deleted', "Project deleted: {$title} (ID: {$id}, slug: {$slug}).");
        }
        return redirect()->route('admin.projects.index')->with('status', 'Project deleted.');
    }

    public function duplicate(Project $project)
    {
        $newProject = $project->replicate();
        $newProject->title = $project->title . ' (Copy)';
        $base = preg_replace('/-\d+$/', '', $project->slug);
        $maxSuffix = Project::where('id', '!=', $project->id)
            ->where(function ($q) use ($base) {
                $q->where('slug', 'like', $base . '-%');
            })
            ->pluck('slug')
            ->map(function ($slug) use ($base) {
                return preg_match('/^' . preg_quote($base, '/') . '-(\d+)$/', $slug, $m) ? (int) $m[1] : null;
            })
            ->filter()
            ->max();
        $newProject->slug = $base . '-' . ($maxSuffix ? $maxSuffix + 1 : 2);
        $newProject->sort_order = (int) Project::max('sort_order') + 1;
        $newProject->save();
        $newProject->projectTypes()->sync($project->projectTypes->pluck('id'));
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'project_duplicated', "Project duplicated: {$project->title} (ID: {$project->id}) → new project ID: {$newProject->id}, slug: {$newProject->slug}.");
        }
        return redirect()->route('admin.projects.edit', $newProject)->with('status', 'Project duplicated. Update details and save.');
    }

    protected function validateProject(Request $request, ?Project $project = null): array
    {
        $slugRule = ['nullable', 'string', 'max:255'];
        if ($project) {
            $slugRule[] = 'unique:projects,slug,' . $project->id;
        } else {
            $slugRule[] = 'unique:projects,slug';
        }

        return $request->validate([
            'project_type_ids' => ['array'],
            'project_type_ids.*' => ['exists:project_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,hold,inactive,close'],
            'slug' => $slugRule,
            'price' => ['nullable', 'string', 'max:255'],
            'launch_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'description' => ['nullable', 'string'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'short_address' => ['nullable', 'string'],
            'full_address' => ['nullable', 'string'],
            'google_map' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'featured_youtube_url' => ['nullable', 'string', 'max:2000'],
            'featured_video_title' => ['nullable', 'string', 'max:255'],
            'featured_video_description' => ['nullable', 'string'],
            'vr_tour_url' => ['nullable', 'string', 'max:2000'],
            'vr_tour_meta_title' => ['nullable', 'string', 'max:255'],
            'vr_tour_meta_description' => ['nullable', 'string', 'max:500'],
            'vr_tour_meta_keywords' => ['nullable', 'string', 'max:500'],
            'vr_tour_canonical_url' => ['nullable', 'string', 'max:500'],
            'about_developers' => ['nullable', 'string'],
            'noc_planning_content' => ['nullable', 'string'],
            'future_note_title' => ['nullable', 'string', 'max:255'],
            'future_note_content' => ['nullable', 'string'],
            'extra_section_title' => ['nullable', 'string', 'max:255'],
            'extra_section_content' => ['nullable', 'string'],
            'price_plan_section_title' => ['nullable', 'string', 'max:255'],
            'invest_title' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'upload_token' => ['nullable', 'string', 'max:64'],
            'logo_path' => ['nullable', 'string', 'max:500'],
            'featured_image_path' => ['nullable', 'string', 'max:500'],
            'homepage_listing_image_path' => ['nullable', 'string', 'max:500'],
            'address_image_path' => ['nullable', 'string', 'max:500'],
            'developer_logo_path' => ['nullable', 'string', 'max:500'],
            'noc_planning_image_path' => ['nullable', 'string', 'max:500'],
            'invest_image_path' => ['nullable', 'string', 'max:500'],
            'project_file_pdf_path' => ['nullable', 'string', 'max:500'],
            'gallery_paths' => ['nullable', 'array'],
            'gallery_paths.*' => ['nullable', 'string', 'max:500'],
        ]);
    }

    protected function buildProjectData(Request $request, array $validated, ?Project $project): array
    {
        return array_merge($validated, [
            'unique_features' => $this->normalizeUniqueFeatures($request),
            'price_plan_items' => array_values(array_filter((array) $request->input('price_plan_items', []))),
            'faqs' => $this->normalizeFaqs($request),
            'plans' => $this->normalizePlans($request, $project),
            'pricing_place_cards' => $this->normalizePricingPlaceCards($request, $project),
            'testimonial_items' => $this->normalizeTestimonialItems($request),
            'invest_points' => $this->normalizeInvestPoints($request),
            'title_descriptions' => $this->normalizeTitleDescriptions($request),
            'videos' => $this->normalizeVideos($request),
            'gallery' => $this->normalizeGallery($request, $project),
            'hero_feature_cards' => $this->normalizeHeroFeatureCards($request),
            'hero_stat_cards' => $this->normalizeHeroStatCards($request),
        ]);
    }

    protected function normalizePricingPlaceCards(Request $request, ?Project $project): array
    {
        $titles = (array) $request->input('pricing_place_titles', []);
        $prices = (array) $request->input('pricing_place_prices', []);
        $f1 = (array) $request->input('pricing_place_feature_1', []);
        $f2 = (array) $request->input('pricing_place_feature_2', []);
        $f3 = (array) $request->input('pricing_place_feature_3', []);
        $f4 = (array) $request->input('pricing_place_feature_4', []);
        $buttons = (array) $request->input('pricing_place_button_text', []);
        $popular = (array) $request->input('pricing_place_is_popular', []);
        $existingImages = (array) $request->input('existing_pricing_place_images', []);

        $out = [];
        foreach ($titles as $i => $title) {
            $title = trim((string) $title);
            $price = trim((string) ($prices[$i] ?? ''));
            $image = trim((string) ($existingImages[$i] ?? ''));
            $features = array_values(array_filter([
                trim((string) ($f1[$i] ?? '')),
                trim((string) ($f2[$i] ?? '')),
                trim((string) ($f3[$i] ?? '')),
                trim((string) ($f4[$i] ?? '')),
            ], fn($v) => $v !== ''));
            $buttonText = trim((string) ($buttons[$i] ?? 'View Plan'));
            $isPopular = isset($popular[$i]) && (string) $popular[$i] === '1';

            if ($title === '' && $price === '' && $image === '' && empty($features)) {
                continue;
            }

            $out[] = [
                'title' => $title,
                'price' => $price,
                'features' => $features,
                'image' => $image,
                'button_text' => $buttonText !== '' ? $buttonText : 'View Plan',
                'is_popular' => $isPopular,
            ];
        }

        return $out;
    }

    protected function normalizeTestimonialItems(Request $request): array
    {
        $quotes = (array) $request->input('testimonial_quotes', []);
        $names = (array) $request->input('testimonial_names', []);
        $roles = (array) $request->input('testimonial_roles', []);
        $out = [];
        foreach ($quotes as $i => $quote) {
            $quote = trim((string) $quote);
            $name = trim((string) ($names[$i] ?? ''));
            $role = trim((string) ($roles[$i] ?? ''));
            if ($quote === '' && $name === '' && $role === '') {
                continue;
            }
            $out[] = ['quote' => $quote, 'name' => $name, 'role' => $role];
        }
        return $out;
    }

    protected function normalizeInvestPoints(Request $request): array
    {
        $points = (array) $request->input('invest_points', []);
        return array_values(array_filter(array_map(fn($v) => trim((string) $v), $points), fn($v) => $v !== ''));
    }

    protected function normalizePlans(Request $request, ?Project $project): array
    {
        $titles = (array) $request->input('plan_titles', []);
        $existing = (array) $request->input('existing_plan_images', []);
        $out = [];
        foreach ($titles as $i => $title) {
            $title = trim($title ?? '');
            $img = $existing[$i] ?? null;
            $out[] = ['title' => $title, 'image' => $img ?: ''];
        }
        return $out;
    }

    protected function normalizeGallery(Request $request, ?Project $project): array
    {
        $current = $project->gallery ?? [];
        $order = (array) $request->input('gallery_order', []);
        if (!empty($order)) {
            foreach ($current as $i => $item) {
                if (isset($order[$i])) {
                    $current[$i]['order'] = (int) $order[$i];
                }
            }
            usort($current, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        }
        return $current;
    }

    protected function normalizeUniqueFeatures(Request $request): array
    {
        $out = [];
        $titles = (array) $request->input('feature_titles', []);
        $icons = (array) $request->input('feature_icons', []);
        foreach ($titles as $i => $title) {
            $title = trim($title ?? '');
            if ($title === '') continue;
            $out[] = ['title' => $title, 'icon' => $icons[$i] ?? ''];
        }
        return $out;
    }

    protected function normalizeFaqs(Request $request): array
    {
        $out = [];
        $questions = (array) $request->input('faq_questions', []);
        $answers = (array) $request->input('faq_answers', []);
        foreach ($questions as $i => $q) {
            $q = trim($q ?? '');
            if ($q === '') continue;
            $out[] = ['question' => $q, 'answer' => $answers[$i] ?? ''];
        }
        return $out;
    }

    protected function normalizeTitleDescriptions(Request $request): ?array
    {
        $sectionTitle = $request->input('td_section_title');
        $sectionDesc = $request->input('td_section_description');
        $titles = (array) $request->input('td_titles', []);
        $descriptions = (array) $request->input('td_descriptions', []);
        $items = [];
        foreach ($titles as $i => $t) {
            $t = trim($t ?? '');
            if ($t === '') continue;
            $items[] = ['title' => $t, 'description' => $descriptions[$i] ?? ''];
        }
        if ($sectionTitle === null && $sectionDesc === null && empty($items)) {
            return null;
        }
        return [
            'section_title' => $sectionTitle ?? '',
            'section_description' => $sectionDesc ?? '',
            'items' => $items,
        ];
    }

    protected function normalizeVideos(Request $request): array
    {
        $urls = (array) $request->input('video_urls', []);
        return array_values(array_filter(array_map('trim', $urls)));
    }

    protected function uniqueProjectSlug(string $raw, ?int $ignoreId = null): string
    {
        $slug = Str::slug($raw);
        if ($slug === '') {
            return '';
        }

        $query = Project::query()->where(function ($q) use ($slug) {
            $q->where('slug', $slug)->orWhere('slug', 'like', $slug . '-%');
        });
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $pattern = '/^' . preg_quote($slug, '/') . '(?:-(\d+))?$/';
        $existing = $query->pluck('slug')->filter(function ($existingSlug) use ($pattern) {
            return preg_match($pattern, (string) $existingSlug) === 1;
        })->values();

        if ($existing->isEmpty()) {
            return $slug;
        }

        $maxSuffix = 0;
        foreach ($existing as $existingSlug) {
            if (preg_match($pattern, (string) $existingSlug, $matches)) {
                $maxSuffix = max($maxSuffix, isset($matches[1]) ? (int) $matches[1] : 0);
            }
        }

        return $slug . '-' . ($maxSuffix + 1);
    }

    /**
     * Process uploads and return attributes to persist (no DB writes here).
     *
     * @return array<string, mixed>
     */
    protected function processFileUploads(Request $request, Project $project): array
    {
        $disk = 'public';
        $uploadToken = $request->input('upload_token');
        $updates = [];

        $removeFields = [
            'remove_logo' => 'logo',
            'remove_featured_image' => 'featured_image',
            'remove_homepage_listing_image' => 'homepage_listing_image',
            'remove_address_image' => 'address_image',
            'remove_project_file_pdf' => 'project_file_pdf',
            'remove_developer_logo' => 'developer_logo',
            'remove_noc_planning_image' => 'noc_planning_image',
        ];
        foreach ($removeFields as $removeKey => $fieldKey) {
            if ($request->boolean($removeKey)) {
                $path = $project->{$fieldKey};
                if ($path) {
                    Storage::disk($disk)->delete($path);
                }
                $updates[$fieldKey] = null;
            }
        }

        $singlePathMap = [
            'logo_path' => 'logo',
            'featured_image_path' => 'featured_image',
            'homepage_listing_image_path' => 'homepage_listing_image',
            'address_image_path' => 'address_image',
            'project_file_pdf_path' => 'project_file_pdf',
            'developer_logo_path' => 'developer_logo',
            'noc_planning_image_path' => 'noc_planning_image',
            'invest_image_path' => 'invest_image',
        ];
        foreach ($singlePathMap as $pathKey => $fieldKey) {
            if (array_key_exists($fieldKey, $updates) && $updates[$fieldKey] === null) {
                continue;
            }
            if (!$request->filled($pathKey)) {
                continue;
            }
            $path = trim((string) $request->input($pathKey));
            if ($path === '' || !$this->isAllowedProjectMediaPath($path, $project->id, $uploadToken)) {
                continue;
            }
            $finalPath = $this->finalizeProjectMediaPath($path, $project->id, $uploadToken);
            if ($project->{$fieldKey} && $project->{$fieldKey} !== $finalPath) {
                Storage::disk($disk)->delete($project->{$fieldKey});
            }
            $updates[$fieldKey] = $finalPath;
        }

        $planTitles = (array) $request->input('plan_titles', []);
        $existingPlanImages = (array) $request->input('existing_plan_images', []);
        $currentPlans = $project->plans ?? [];
        $plans = [];
        foreach ($planTitles as $i => $title) {
            $title = trim($title ?? '');
            $imagePath = trim((string) ($existingPlanImages[$i] ?? $currentPlans[$i]['image'] ?? ''));
            if ($imagePath !== '' && $this->isAllowedProjectMediaPath($imagePath, $project->id, $uploadToken)) {
                $imagePath = $this->finalizeProjectMediaPath($imagePath, $project->id, $uploadToken);
            } elseif ($imagePath !== '' && !str_starts_with($imagePath, 'projects/' . $project->id . '/')) {
                $imagePath = trim((string) ($currentPlans[$i]['image'] ?? ''));
            }
            $plans[] = ['title' => $title, 'image' => $imagePath];
        }
        $newPlans = array_values(array_filter($plans, fn ($p) => $p['title'] !== '' || $p['image'] !== ''));
        $this->deleteOrphanedStorageFiles(
            array_filter(array_column($currentPlans, 'image')),
            array_filter(array_column($newPlans, 'image')),
            $disk
        );
        $updates['plans'] = $newPlans;

        $pricingTitles = (array) $request->input('pricing_place_titles', []);
        $existingPricingImages = (array) $request->input('existing_pricing_place_images', []);
        $pricingPrices = (array) $request->input('pricing_place_prices', []);
        $pricingF1 = (array) $request->input('pricing_place_feature_1', []);
        $pricingF2 = (array) $request->input('pricing_place_feature_2', []);
        $pricingF3 = (array) $request->input('pricing_place_feature_3', []);
        $pricingF4 = (array) $request->input('pricing_place_feature_4', []);
        $pricingButtons = (array) $request->input('pricing_place_button_text', []);
        $pricingPopular = (array) $request->input('pricing_place_is_popular', []);
        $currentPricing = is_array($project->pricing_place_cards) ? $project->pricing_place_cards : [];
        $pricingCards = [];
        foreach ($pricingTitles as $i => $title) {
            $title = trim((string) $title);
            $price = trim((string) ($pricingPrices[$i] ?? ''));
            $imagePath = trim((string) ($existingPricingImages[$i] ?? ($currentPricing[$i]['image'] ?? '')));
            if ($imagePath !== '' && $this->isAllowedProjectMediaPath($imagePath, $project->id, $uploadToken)) {
                $imagePath = $this->finalizeProjectMediaPath($imagePath, $project->id, $uploadToken);
            } elseif ($imagePath !== '' && !str_starts_with($imagePath, 'projects/' . $project->id . '/')) {
                $imagePath = trim((string) ($currentPricing[$i]['image'] ?? ''));
            }
            $features = array_values(array_filter([
                trim((string) ($pricingF1[$i] ?? '')),
                trim((string) ($pricingF2[$i] ?? '')),
                trim((string) ($pricingF3[$i] ?? '')),
                trim((string) ($pricingF4[$i] ?? '')),
            ], fn ($v) => $v !== ''));
            $buttonText = trim((string) ($pricingButtons[$i] ?? 'View Plan'));
            $isPopular = isset($pricingPopular[$i]) && (string) $pricingPopular[$i] === '1';

            if ($title === '' && $price === '' && $imagePath === '' && empty($features)) {
                continue;
            }
            $pricingCards[] = [
                'title' => $title,
                'price' => $price,
                'features' => $features,
                'image' => $imagePath,
                'button_text' => $buttonText !== '' ? $buttonText : 'View Plan',
                'is_popular' => $isPopular,
            ];
        }
        $this->deleteOrphanedStorageFiles(
            array_filter(array_column($currentPricing, 'image')),
            array_filter(array_column($pricingCards, 'image')),
            $disk
        );
        $updates['pricing_place_cards'] = $pricingCards;

        $galleryRemove = (array) $request->input('gallery_remove', []);
        $galleryPaths = (array) $request->input('gallery_paths', []);
        $galleryOrder = (array) $request->input('gallery_order', []);
        $gallery = [];
        foreach ($galleryPaths as $i => $path) {
            $path = trim((string) $path);
            if ($path === '' || in_array($path, $galleryRemove, true)) {
                continue;
            }
            if (!$this->isAllowedProjectMediaPath($path, $project->id, $uploadToken)) {
                continue;
            }
            $finalPath = $this->finalizeProjectMediaPath($path, $project->id, $uploadToken);
            $order = isset($galleryOrder[$i]) ? (int) $galleryOrder[$i] : $i;
            $gallery[] = ['path' => $finalPath, 'order' => $order];
        }
        foreach ($galleryRemove as $path) {
            if ($path && Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
        usort($gallery, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        $updates['gallery'] = $gallery;

        if ($uploadToken) {
            Storage::disk($disk)->deleteDirectory('projects/staging/' . $uploadToken);
        }

        return $updates;
    }

    protected function isAllowedProjectMediaPath(string $path, int $projectId, ?string $uploadToken): bool
    {
        if (str_starts_with($path, 'projects/' . $projectId . '/')) {
            return true;
        }
        if ($uploadToken && str_starts_with($path, 'projects/staging/' . $uploadToken . '/')) {
            return true;
        }
        return false;
    }

    protected function finalizeProjectMediaPath(string $path, int $projectId, ?string $uploadToken): string
    {
        if (!$uploadToken || !str_starts_with($path, 'projects/staging/' . $uploadToken . '/')) {
            return $path;
        }

        $relative = substr($path, strlen('projects/staging/' . $uploadToken . '/'));
        $slashPos = strpos($relative, '/');
        $subdir = $slashPos !== false ? substr($relative, 0, $slashPos) : $relative;
        $destDir = 'projects/' . $projectId . '/' . $subdir;
        $filename = basename($path);
        $newPath = $destDir . '/' . $filename;
        Storage::disk('public')->makeDirectory($destDir);
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->move($path, $newPath);
        }
        return $newPath;
    }

    /**
     * @param  array<int, string>  $oldPaths
     * @param  array<int, string>  $newPaths
     */
    protected function deleteOrphanedStorageFiles(array $oldPaths, array $newPaths, string $disk = 'public'): void
    {
        foreach ($oldPaths as $oldPath) {
            if ($oldPath !== '' && ! in_array($oldPath, $newPaths, true)) {
                Storage::disk($disk)->delete($oldPath);
            }
        }
    }

    protected function deleteProjectFiles(Project $project): void
    {
        $paths = array_filter([
            $project->logo,
            $project->featured_image,
            $project->homepage_listing_image,
            $project->address_image,
            $project->project_file_pdf,
            $project->developer_logo,
            $project->noc_planning_image,
        ]);
        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }
        foreach ($project->plans ?? [] as $p) {
            if (!empty($p['image'])) {
                Storage::disk('public')->delete($p['image']);
            }
        }
        foreach ($project->pricing_place_cards ?? [] as $card) {
            if (!empty($card['image'])) {
                Storage::disk('public')->delete($card['image']);
            }
        }
        foreach ($project->testimonial_items ?? [] as $item) {
            if (!empty($item['image'])) {
                Storage::disk('public')->delete($item['image']);
            }
        }
        if (!empty($project->invest_image)) {
            Storage::disk('public')->delete($project->invest_image);
        }
        foreach ($project->gallery ?? [] as $g) {
            if (!empty($g['path'])) {
                Storage::disk('public')->delete($g['path']);
            }
        }
        Storage::disk('public')->deleteDirectory('projects/' . $project->id);
    }
}
