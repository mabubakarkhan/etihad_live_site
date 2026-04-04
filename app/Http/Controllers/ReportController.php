<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Dealer;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();
        $to = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::now()->endOfDay();

        $fromStr = $from->format('Y-m-d');
        $toStr = $to->format('Y-m-d');

        $projectsTotal = Project::count();
        $projectsInRange = Project::whereBetween('created_at', [$from, $to])->count();
        $projectsByStatus = Project::selectRaw('status, count(*) as cnt')->groupBy('status')->pluck('cnt', 'status')->toArray();
        $projectsByType = Project::join('project_project_type', 'projects.id', '=', 'project_project_type.project_id')
            ->join('project_types', 'project_project_type.project_type_id', '=', 'project_types.id')
            ->select('project_types.name', DB::raw('count(*) as cnt'))
            ->groupBy('project_types.id', 'project_types.name')
            ->orderByDesc('cnt')
            ->get();

        $listingsRangeQuery = Property::whereBetween('created_at', [$from, $to]);
        if ($request->filled('purpose')) {
            $listingsRangeQuery->where('purpose', $request->purpose);
        }
        $listingsTotal = Property::count();
        $ownListings = Property::where('dealer_id', 0)->count();
        $dealerListings = Property::where('dealer_id', '!=', 0)->count();
        $listingsInRange = $listingsRangeQuery->count();
        $listingsByStatus = Property::whereBetween('created_at', [$from, $to])
            ->when($request->filled('purpose'), fn ($q) => $q->where('purpose', $request->purpose))
            ->selectRaw('status, count(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();
        $listingsByPurpose = Property::selectRaw("coalesce(purpose, 'sale') as p, count(*) as cnt")
            ->groupBy(DB::raw("coalesce(purpose, 'sale')"))
            ->pluck('cnt', 'p')
            ->toArray();
        $filterPurpose = $request->purpose;
        $listingsByPropertyType = Property::selectRaw('property_type, count(*) as cnt')
            ->whereNotNull('property_type')
            ->where('property_type', '!=', '')
            ->groupBy('property_type')
            ->orderByDesc('cnt')
            ->get();
        $listingsByProjectType = Property::join('property_project_type', 'properties.id', '=', 'property_project_type.property_id')
            ->join('project_types', 'property_project_type.project_type_id', '=', 'project_types.id')
            ->select('project_types.name', DB::raw('count(*) as cnt'))
            ->groupBy('project_types.id', 'project_types.name')
            ->orderByDesc('cnt')
            ->get();

        $dealersTotal = Dealer::count();
        $dealersActive = Dealer::where('status', 'active')->count();
        $dealersInRange = Dealer::whereBetween('created_at', [$from, $to])->count();
        $dealersByStatus = Dealer::selectRaw('status, count(*) as cnt')->groupBy('status')->pluck('cnt', 'status')->toArray();

        $usersTotal = User::count();
        $activityInRange = ActivityLog::whereBetween('created_at', [$from, $to])->count();
        $activityByAction = ActivityLog::whereBetween('created_at', [$from, $to])
            ->selectRaw('action, count(*) as cnt')
            ->groupBy('action')
            ->orderByDesc('cnt')
            ->get();
        $topUsersByActivity = ActivityLog::whereBetween('created_at', [$from, $to])
            ->select('user_id', DB::raw('count(*) as cnt'))
            ->groupBy('user_id')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get()
            ->load('user');

        $dailyProjects = Project::whereBetween('created_at', [$from, $to])
            ->selectRaw('date(created_at) as d, count(*) as cnt')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('cnt', 'd')
            ->toArray();
        $dailyListings = Property::whereBetween('created_at', [$from, $to])
            ->selectRaw('date(created_at) as d, count(*) as cnt')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('cnt', 'd')
            ->toArray();
        $dailyDealers = Dealer::whereBetween('created_at', [$from, $to])
            ->selectRaw('date(created_at) as d, count(*) as cnt')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('cnt', 'd')
            ->toArray();

        return view('admin.reports.index', compact(
            'fromStr',
            'toStr',
            'filterPurpose',
            'projectsTotal',
            'projectsInRange',
            'projectsByStatus',
            'projectsByType',
            'listingsTotal',
            'ownListings',
            'dealerListings',
            'listingsInRange',
            'listingsByStatus',
            'listingsByPurpose',
            'listingsByPropertyType',
            'listingsByProjectType',
            'dealersTotal',
            'dealersActive',
            'dealersInRange',
            'dealersByStatus',
            'usersTotal',
            'activityInRange',
            'activityByAction',
            'topUsersByActivity',
            'dailyProjects',
            'dailyListings',
            'dailyDealers'
        ));
    }
}
