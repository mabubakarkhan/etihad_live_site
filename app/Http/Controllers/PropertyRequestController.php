<?php

namespace App\Http\Controllers;

use App\Models\PropertyRequest;
use Illuminate\Http\Request;

class PropertyRequestController extends Controller
{
    /**
     * Project requests (from project request-info form).
     */
    public function indexProjects(Request $request)
    {
        return $this->index($request, 'project');
    }

    /**
     * Property requests (from property request-showing form; own + dealer on same page).
     */
    public function indexProperties(Request $request)
    {
        return $this->index($request, 'property');
    }

    protected function index(Request $request, string $source)
    {
        $query = $source === 'project'
            ? PropertyRequest::projectRequests()->with('project')
            : PropertyRequest::propertyRequests()->with(['property', 'dealer']);

        // Status filter: new (default) | seen | all
        if ($request->filled('status') && $request->status === 'seen') {
            $query->seen();
        } elseif ($request->filled('status') && $request->status === 'all') {
            // show all
        } else {
            $query->new();
        }

        // For property requests: filter by type (own / dealer)
        if ($source === 'property' && $request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search (name, email, message)
        if ($request->filled('search')) {
            $term = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('message', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('property_type', 'like', $term)
                    ->orWhere('budget', 'like', $term);
            });
        }

        $requests = $query->orderByDesc('created_at')->limit(2000)->get();

        $pageTitle = $source === 'project' ? 'Project requests' : 'Property requests';

        return view('admin.property_requests.index', [
            'requests' => $requests,
            'source' => $source,
            'pageTitle' => $pageTitle,
            'filterStatus' => $request->status,
            'filterType' => $request->type,
            'filterFromDate' => $request->from_date,
            'filterToDate' => $request->to_date,
            'filterSearch' => $request->search,
        ]);
    }

    /**
     * Show single request; mark as seen when opened.
     */
    public function show(PropertyRequest $propertyRequest)
    {
        if ($propertyRequest->status === PropertyRequest::STATUS_NEW) {
            $propertyRequest->update(['status' => PropertyRequest::STATUS_SEEN]);
        }

        $propertyRequest->load(['property', 'project', 'dealer']);
        $source = $propertyRequest->project_id > 0 ? 'project' : 'property';

        return view('admin.property_requests.show', [
            'request' => $propertyRequest,
            'source' => $source,
        ]);
    }
}
