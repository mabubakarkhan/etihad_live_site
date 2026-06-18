<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingSortController extends Controller
{
    public function index(Request $request): View
    {
        $activeTab = in_array($request->query('tab'), ['projects', 'listings'], true)
            ? $request->query('tab')
            : 'projects';

        $projects = Project::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get(['id', 'title', 'slug', 'status', 'city', 'logo', 'featured_image', 'homepage_listing_image', 'sort_order']);

        $properties = Property::query()
            ->with('dealer:id,name')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get(['id', 'title', 'slug', 'status', 'city', 'dealer_id', 'featured_image', 'sort_order']);

        return view('admin.sort_order.index', compact('projects', 'properties', 'activeTab'));
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_ids' => ['nullable', 'array'],
            'project_ids.*' => ['integer', 'exists:projects,id'],
            'property_ids' => ['nullable', 'array'],
            'property_ids.*' => ['integer', 'exists:properties,id'],
        ]);

        foreach (array_values($validated['project_ids'] ?? []) as $index => $id) {
            Project::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        foreach (array_values($validated['property_ids'] ?? []) as $index => $id) {
            Property::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'sort_order_updated', 'Project and listing display order updated.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Display order saved successfully.',
        ]);
    }
}
