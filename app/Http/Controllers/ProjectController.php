<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        $query = Project::with('projectTypes')->orderBy('id', 'desc');
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
        return view('admin.projects.create', compact('projectTypes', 'states'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProject($request);
        $data = $this->buildProjectData($request, $validated, null);
        $rawSlug = trim((string) ($data['slug'] ?? ''));
        $data['slug'] = $rawSlug !== '' ? Str::slug($rawSlug) : Str::slug($data['title']);
        if ($data['slug'] === '') {
            $data['slug'] = Str::slug($data['title']);
        }
        $base = $data['slug'];
        $i = 1;
        while (Project::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $base . '-' . $i++;
        }
        $data['status'] = $data['status'] ?? 'active';
        $project = Project::create($data);
        $project->projectTypes()->sync($request->input('project_type_ids', []));
        $this->handleFileUploads($request, $project);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'project_created', "Project created: {$project->title} (ID: {$project->id}, slug: {$project->slug}).");
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

    public function update(Request $request, Project $project)
    {
        $validated = $this->validateProject($request, $project);
        $data = $this->buildProjectData($request, $validated, $project);
        if ($request->boolean('remove_featured_video')) {
            $data['featured_youtube_url'] = null;
            $data['featured_video_title'] = null;
            $data['featured_video_description'] = null;
        }
        $rawSlug = trim((string) ($data['slug'] ?? ''));
        $data['slug'] = $rawSlug !== '' ? Str::slug($rawSlug) : Str::slug($data['title']);
        if ($data['slug'] === '') {
            $data['slug'] = Str::slug($data['title']);
        }
        $base = $data['slug'];
        $i = 1;
        while (Project::where('slug', $data['slug'])->where('id', '!=', $project->id)->exists()) {
            $data['slug'] = $base . '-' . $i++;
        }
        $this->handleFileUploads($request, $project);
        unset($data['plans'], $data['gallery']);
        $project->update($data);
        $project->projectTypes()->sync($request->input('project_type_ids', []));
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'project_updated', "Project updated: {$project->title} (ID: {$project->id}, slug: {$project->slug}).");
        }
        return redirect()->route('admin.projects.edit', $project)->with('status', 'Project updated.');
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
            'about_developers' => ['nullable', 'string'],
            'noc_planning_content' => ['nullable', 'string'],
            'future_note_title' => ['nullable', 'string', 'max:255'],
            'future_note_content' => ['nullable', 'string'],
            'extra_section_title' => ['nullable', 'string', 'max:255'],
            'extra_section_content' => ['nullable', 'string'],
            'price_plan_section_title' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
        ]);
    }

    protected function buildProjectData(Request $request, array $validated, ?Project $project): array
    {
        return array_merge($validated, [
            'unique_features' => $this->normalizeUniqueFeatures($request),
            'price_plan_items' => array_values(array_filter((array) $request->input('price_plan_items', []))),
            'faqs' => $this->normalizeFaqs($request),
            'plans' => $this->normalizePlans($request, $project),
            'title_descriptions' => $this->normalizeTitleDescriptions($request),
            'videos' => $this->normalizeVideos($request),
            'gallery' => $this->normalizeGallery($request, $project),
        ]);
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

    protected function handleFileUploads(Request $request, Project $project): void
    {
        $disk = 'public';
        $base = 'projects/' . $project->id;

        // Handle "Remove" for single image/PDF fields
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
                $project->update([$fieldKey => null]);
            }
        }

        $singleFiles = [
            'logo' => $base . '/logo',
            'featured_image' => $base . '/featured',
            'homepage_listing_image' => $base . '/homepage',
            'address_image' => $base . '/address',
            'project_file_pdf' => $base . '/pdf',
            'developer_logo' => $base . '/developer_logo',
            'noc_planning_image' => $base . '/noc',
        ];
        foreach ($singleFiles as $key => $dir) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store($dir, $disk);
                $project->update([$key => $path]);
            }
        }

        $planTitles = (array) $request->input('plan_titles', []);
        $existingPlanImages = (array) $request->input('existing_plan_images', []);
        $currentPlans = $project->plans ?? [];
        $plans = [];
        foreach ($planTitles as $i => $title) {
            $title = trim($title ?? '');
            $imagePath = $existingPlanImages[$i] ?? $currentPlans[$i]['image'] ?? null;
            if ($request->hasFile('plan_images.' . $i)) {
                $file = $request->file('plan_images')[$i];
                if ($file->isValid()) {
                    $imagePath = $file->store($base . '/plans', $disk);
                }
            }
            $plans[] = ['title' => $title, 'image' => $imagePath ?? ''];
        }
        $newPlans = array_values(array_filter($plans, fn($p) => $p['title'] !== '' || $p['image'] !== ''));
        $oldPlanImages = array_filter(array_column($currentPlans, 'image'));
        $newPlanImages = array_filter(array_column($newPlans, 'image'));
        foreach ($oldPlanImages as $oldPath) {
            if (!in_array($oldPath, $newPlanImages, true)) {
                Storage::disk($disk)->delete($oldPath);
            }
        }
        $project->update(['plans' => $newPlans]);

        // Gallery: remove marked images, build from gallery_paths[] + gallery_order[], then append new uploads
        $galleryRemove = (array) $request->input('gallery_remove', []);
        $galleryPaths = (array) $request->input('gallery_paths', []);
        $galleryOrder = (array) $request->input('gallery_order', []);
        $gallery = [];
        foreach ($galleryPaths as $i => $path) {
            $path = trim($path);
            if ($path === '' || in_array($path, $galleryRemove, true)) {
                continue;
            }
            $order = isset($galleryOrder[$i]) ? (int) $galleryOrder[$i] : $i;
            $gallery[] = ['path' => $path, 'order' => $order];
        }
        foreach ($galleryRemove as $path) {
            if ($path) {
                Storage::disk($disk)->delete($path);
            }
        }
        usort($gallery, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        if ($request->hasFile('gallery_images')) {
            $files = $request->file('gallery_images');
            $files = is_array($files) ? $files : [$files];
            $startOrder = empty($gallery) ? 0 : (max(array_column($gallery, 'order')) + 1);
            foreach ($files as $i => $file) {
                if (!$file || !$file->isValid()) continue;
                $path = $file->store($base . '/gallery', $disk);
                $gallery[] = ['path' => $path, 'order' => $startOrder + $i];
            }
        }
        $project->update(['gallery' => $gallery]);
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
        foreach ($project->gallery ?? [] as $g) {
            if (!empty($g['path'])) {
                Storage::disk('public')->delete($g['path']);
            }
        }
        Storage::disk('public')->deleteDirectory('projects/' . $project->id);
    }
}
