<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ProjectType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectTypeController extends Controller
{
    public function index()
    {
        $types = ProjectType::withCount([
            'projects',
            'properties as own_listings_count' => fn ($q) => $q->where('properties.dealer_id', 0),
            'properties as dealer_listings_count' => fn ($q) => $q->where('properties.dealer_id', '!=', 0),
        ])->orderBy('name')->limit(500)->get();
        return view('admin.project_types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.project_types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:project_types,slug'],
            'show_in_projects' => ['boolean'],
            'show_in_properties' => ['boolean'],
            'show_in_dealers' => ['boolean'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['show_in_projects'] = $request->boolean('show_in_projects');
        $validated['show_in_properties'] = $request->boolean('show_in_properties');
        $validated['show_in_dealers'] = $request->boolean('show_in_dealers');

        $type = ProjectType::create($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'project_type_created', "Project type created: {$type->name} (ID: {$type->id}, slug: {$type->slug}).");
        }
        return redirect()->route('admin.project_types.index')->with('status', 'Project type created.');
    }

    public function edit(ProjectType $projectType)
    {
        return view('admin.project_types.edit', compact('projectType'));
    }

    public function update(Request $request, ProjectType $projectType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:project_types,slug,' . $projectType->id],
            'show_in_projects' => ['boolean'],
            'show_in_properties' => ['boolean'],
            'show_in_dealers' => ['boolean'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['show_in_projects'] = $request->boolean('show_in_projects');
        $validated['show_in_properties'] = $request->boolean('show_in_properties');
        $validated['show_in_dealers'] = $request->boolean('show_in_dealers');

        $projectType->update($validated);
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'project_type_updated', "Project type updated: {$projectType->name} (ID: {$projectType->id}, slug: {$projectType->slug}).");
        }
        return redirect()->route('admin.project_types.index')->with('status', 'Project type updated.');
    }

    public function destroy(ProjectType $projectType)
    {
        $name = $projectType->name;
        $id = $projectType->id;
        $projectType->delete();
        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'project_type_deleted', "Project type deleted: {$name} (ID: {$id}).");
        }
        return redirect()->route('admin.project_types.index')->with('status', 'Project type deleted.');
    }
}
