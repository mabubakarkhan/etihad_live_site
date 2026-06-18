<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HomepageInvestmentJourneySetting;
use App\Models\HomepageInvestmentJourneyStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomepageInvestmentJourneyController extends Controller
{
    public function edit()
    {
        $setting = HomepageInvestmentJourneySetting::instance();
        $steps = HomepageInvestmentJourneySetting::orderedSteps();

        return view('admin.homepage_investment_journey.edit', compact('setting', 'steps'));
    }

    public function update(Request $request)
    {
        $setting = HomepageInvestmentJourneySetting::instance();

        $validated = $request->validate([
            'title_line_1' => ['required', 'string', 'max:255'],
            'title_highlight' => ['required', 'string', 'max:255'],
            'steps' => ['required', 'array', 'min:1', 'max:8'],
            'steps.*.id' => ['nullable', 'integer', 'exists:homepage_investment_journey_steps,id'],
            'steps.*.title' => ['required', 'string', 'max:255'],
            'steps.*.description' => ['required', 'string', 'max:2000'],
        ]);

        $setting->update([
            'title_line_1' => $validated['title_line_1'],
            'title_highlight' => $validated['title_highlight'],
        ]);

        DB::transaction(function () use ($validated) {
            $keptIds = [];

            foreach ($validated['steps'] as $index => $stepData) {
                $payload = [
                    'sort_order' => $index + 1,
                    'title' => $stepData['title'],
                    'description' => $stepData['description'],
                ];

                if (! empty($stepData['id'])) {
                    $step = HomepageInvestmentJourneyStep::query()->findOrFail($stepData['id']);
                    $step->update($payload);
                    $keptIds[] = $step->id;
                } else {
                    $step = HomepageInvestmentJourneyStep::query()->create($payload);
                    $keptIds[] = $step->id;
                }
            }

            HomepageInvestmentJourneyStep::query()
                ->whereNotIn('id', $keptIds)
                ->delete();
        });

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_investment_journey_updated', 'Homepage investment journey section updated.');
        }

        return redirect()->route('admin.homepage-investment-journey.edit')->with('status', 'Investment journey section saved.');
    }
}
