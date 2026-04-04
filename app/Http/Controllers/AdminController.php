<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Dealer;
use App\Models\Project;
use App\Models\Property;
use App\Models\VisitorDailyCount;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the main admin dashboard.
     */
    public function index()
    {
        $dealersThisWeek = Dealer::where('created_at', '>=', Carbon::now()->startOfWeek())->count();
        $dealersLastWeek = Dealer::whereBetween('created_at', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek(),
        ])->count();
        $dealersPercentChange = $dealersLastWeek > 0
            ? round((($dealersThisWeek - $dealersLastWeek) / $dealersLastWeek) * 100)
            : ($dealersThisWeek > 0 ? 100 : 0);

        $activeProjects = Project::active()->count();
        $activeListings = Property::active()->count();

        $totalDealers = Dealer::count();
        $totalProjects = Project::count();
        $totalListings = Property::count();
        $projectsByStatus = [
            'active' => Project::where('status', 'active')->count(),
            'hold' => Project::where('status', 'hold')->count(),
            'inactive' => Project::where('status', 'inactive')->count(),
            'close' => Project::where('status', 'close')->count(),
        ];
        $listingsByStatus = [
            'active' => Property::where('status', 'active')->count(),
            'hold' => Property::where('status', 'hold')->count(),
            'inactive' => Property::where('status', 'inactive')->count(),
            'close' => Property::where('status', 'close')->count(),
        ];
        $listingsByPurpose = [
            'own_sale' => Property::where('dealer_id', 0)->where('purpose', 'sale')->count(),
            'own_rent' => Property::where('dealer_id', 0)->where('purpose', 'rent')->count(),
            'dealer_sale' => Property::where('dealer_id', '!=', 0)->where('purpose', 'sale')->count(),
            'dealer_rent' => Property::where('dealer_id', '!=', 0)->where('purpose', 'rent')->count(),
        ];

        $recentActivity = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $projectsLast30 = Project::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $listingsLast30 = Property::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $dealersLast100 = Dealer::where('created_at', '>=', Carbon::now()->subDays(100))->count();

        $chartLabels = [];
        $chartDailyProjects = [];
        $chartDailyListings = [];
        $chartDailyDealers = [];
        $chartDailyOwnListings = [];
        $chartDailyDealerListings = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->startOfDay();
            $chartLabels[] = $day->format('M j');
            $chartDailyProjects[] = Project::whereDate('created_at', $day)->count();
            $chartDailyListings[] = Property::whereDate('created_at', $day)->count();
            $chartDailyDealers[] = Dealer::whereDate('created_at', $day)->count();
            $chartDailyOwnListings[] = Property::whereDate('created_at', $day)->where('dealer_id', 0)->count();
            $chartDailyDealerListings[] = Property::whereDate('created_at', $day)->where('dealer_id', '!=', 0)->count();
        }

        $chartLabels7 = [];
        $chartVisitor7 = [];
        $chartVisitorOwnListing7 = [];
        $chartVisitorDealerListing7 = [];
        $chartVisitorProjects7 = [];
        $visitor7Collection = VisitorDailyCount::where('date', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->get()
            ->keyBy(fn ($r) => $r->date->format('Y-m-d'));
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->startOfDay();
            $dayStr = $day->format('Y-m-d');
            $chartLabels7[] = $day->format('M j');
            $r = $visitor7Collection->get($dayStr);
            $chartVisitor7[] = $r ? (int) $r->count : 0;
            $chartVisitorOwnListing7[] = $r ? (int) $r->count_own_listing : 0;
            $chartVisitorDealerListing7[] = $r ? (int) $r->count_dealer_listing : 0;
            $chartVisitorProjects7[] = $r ? (int) $r->count_projects : 0;
        }

        return view('admin.dashboard', compact(
            'dealersThisWeek',
            'dealersPercentChange',
            'activeProjects',
            'activeListings',
            'totalDealers',
            'totalProjects',
            'totalListings',
            'projectsByStatus',
            'listingsByStatus',
            'listingsByPurpose',
            'recentActivity',
            'projectsLast30',
            'listingsLast30',
            'dealersLast100',
            'chartLabels',
            'chartDailyProjects',
            'chartDailyListings',
            'chartDailyDealers',
            'chartDailyOwnListings',
            'chartDailyDealerListings',
            'chartLabels7',
            'chartVisitor7',
            'chartVisitorOwnListing7',
            'chartVisitorDealerListing7',
            'chartVisitorProjects7'
        ));
    }
}
