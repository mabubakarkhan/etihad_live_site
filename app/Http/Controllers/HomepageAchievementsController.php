<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HomepageAchievementStat;
use App\Models\HomepageAchievementsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomepageAchievementsController extends Controller
{
    public function edit()
    {
        $setting = HomepageAchievementsSetting::instance();
        $stats = HomepageAchievementsSetting::orderedStats();

        return view('admin.homepage_achievements.edit', compact('setting', 'stats'));
    }

    public function update(Request $request)
    {
        $setting = HomepageAchievementsSetting::instance();

        $validated = $request->validate([
            'title_line_1' => ['required', 'string', 'max:255'],
            'title_highlight' => ['required', 'string', 'max:255'],
            'stats' => ['required', 'array', 'min:1', 'max:12'],
            'stats.*.id' => ['nullable', 'integer', 'exists:homepage_achievement_stats,id'],
            'stats.*.value' => ['required', 'string', 'max:32'],
            'stats.*.suffix' => ['nullable', 'string', 'max:8'],
            'stats.*.label' => ['required', 'string', 'max:255'],
        ]);

        $setting->update([
            'title_line_1' => $validated['title_line_1'],
            'title_highlight' => $validated['title_highlight'],
        ]);

        DB::transaction(function () use ($validated) {
            $keptIds = [];

            foreach ($validated['stats'] as $index => $statData) {
                $payload = [
                    'sort_order' => $index + 1,
                    'value' => $statData['value'],
                    'suffix' => $statData['suffix'] ?? null,
                    'label' => $statData['label'],
                ];

                if (! empty($statData['id'])) {
                    $stat = HomepageAchievementStat::query()->findOrFail($statData['id']);
                    $stat->update($payload);
                    $keptIds[] = $stat->id;
                } else {
                    $stat = HomepageAchievementStat::query()->create($payload);
                    $keptIds[] = $stat->id;
                }
            }

            HomepageAchievementStat::query()
                ->whereNotIn('id', $keptIds)
                ->delete();
        });

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_achievements_updated', 'Homepage achievements section updated.');
        }

        return redirect()->route('admin.homepage-achievements.edit')->with('status', 'Achievements section saved.');
    }
}
