<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\FirstVisitPopupSetting;
use App\Models\VisitorDailyCount;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class FirstVisitPopupController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:60'],
            'city' => ['required', 'string', 'max:120'],
        ], [
            'name.required' => 'Please enter your name.',
            'phone.required' => 'Please enter your phone number.',
            'city.required' => 'Please enter your city.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(' ', $validator->errors()->all()),
            ], 422);
        }

        $validated = $validator->validated();

        $message = ContactMessage::create([
            'name' => $validated['name'],
            'email' => null,
            'phone' => $validated['phone'],
            'city' => $validated['city'],
            'message' => 'First-time visitor popup enquiry from ' . $validated['city'] . '.',
            'status' => ContactMessage::STATUS_NEW,
            'source' => ContactMessage::SOURCE_POPUP_FIRST_VISITOR,
        ]);

        return response()->json([
            'success' => (bool) $message,
            'message' => 'Thanks! Our team will contact you shortly.',
        ]);
    }

    public function track(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_visit' => ['nullable', 'boolean'],
            'path' => ['nullable', 'string', 'max:500'],
        ]);

        $today = Carbon::today()->toDateString();
        $row = VisitorDailyCount::firstOrCreate(
            ['date' => $today],
            [
                'count' => 0,
                'count_own_listing' => 0,
                'count_dealer_listing' => 0,
                'count_projects' => 0,
                'page_views' => 0,
                'first_visitors' => 0,
            ]
        );

        $row->increment('page_views');

        if (! empty($validated['first_visit'])) {
            $row->increment('first_visitors');
        }

        return response()->json(['success' => true]);
    }

    public function analytics(): View
    {
        $days = 14;
        $labels = [];
        $pageViews = [];
        $firstVisitors = [];

        $collection = VisitorDailyCount::query()
            ->where('date', '>=', Carbon::now()->subDays($days - 1)->startOfDay())
            ->get()
            ->keyBy(fn ($r) => $r->date->format('Y-m-d'));

        for ($i = $days - 1; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->startOfDay();
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('M j');
            $row = $collection->get($key);
            $pageViews[] = $row ? (int) ($row->page_views ?? 0) : 0;
            $firstVisitors[] = $row ? (int) ($row->first_visitors ?? 0) : 0;
        }

        $totals = [
            'page_views_today' => (int) (VisitorDailyCount::query()->whereDate('date', Carbon::today())->value('page_views') ?? 0),
            'first_visitors_today' => (int) (VisitorDailyCount::query()->whereDate('date', Carbon::today())->value('first_visitors') ?? 0),
            'page_views_14' => (int) array_sum($pageViews),
            'first_visitors_14' => (int) array_sum($firstVisitors),
            'popup_leads' => ContactMessage::query()->where('source', ContactMessage::SOURCE_POPUP_FIRST_VISITOR)->count(),
            'popup_leads_new' => ContactMessage::query()
                ->where('source', ContactMessage::SOURCE_POPUP_FIRST_VISITOR)
                ->where('status', ContactMessage::STATUS_NEW)
                ->count(),
        ];

        $popup = FirstVisitPopupSetting::instance();

        return view('admin.first_visit_popup.analytics', compact('labels', 'pageViews', 'firstVisitors', 'totals', 'popup'));
    }
}
