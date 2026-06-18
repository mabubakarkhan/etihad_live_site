<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesHomepageMediaPaths;
use App\Models\ActivityLog;
use App\Models\HomepageChoiceSetting;
use App\Models\HomepageChoiceSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomepageChoiceController extends Controller
{
    use HandlesHomepageMediaPaths;

    public function edit()
    {
        $setting = HomepageChoiceSetting::instance();
        $slides = HomepageChoiceSetting::orderedSlides();

        return view('admin.homepage_choice.edit', compact('setting', 'slides'));
    }

    public function update(Request $request)
    {
        $setting = HomepageChoiceSetting::instance();

        $validated = $request->validate([
            'section_heading' => ['required', 'string', 'max:255'],
            'scroll_label_desktop' => ['required', 'string', 'max:64'],
            'scroll_label_mobile' => ['required', 'string', 'max:64'],
            'background_image_path' => ['nullable', 'string', 'max:500'],
            'background_image_portrait_path' => ['nullable', 'string', 'max:500'],
            'remove_background_image' => ['nullable', 'boolean'],
            'remove_background_image_portrait' => ['nullable', 'boolean'],
            'slides' => ['required', 'array', 'min:1', 'max:6'],
            'slides.*.id' => ['nullable', 'integer', 'exists:homepage_choice_slides,id'],
            'slides.*.heading_text' => ['required', 'string', 'max:255'],
            'slides.*.counter_to' => ['required', 'integer', 'min:0', 'max:999999999'],
            'slides.*.counter_text' => ['required', 'string', 'max:64'],
            'slides.*.description' => ['required', 'string', 'max:255'],
        ]);

        $setting->fill(collect($validated)->only([
            'section_heading',
            'scroll_label_desktop',
            'scroll_label_mobile',
        ])->all());

        $this->applyHomepageMediaPath($request, $setting, 'background_image', 'remove_background_image');
        $this->applyHomepageMediaPath($request, $setting, 'background_image_portrait', 'remove_background_image_portrait');

        $setting->save();

        DB::transaction(function () use ($validated) {
            $keptIds = [];

            foreach ($validated['slides'] as $index => $slideData) {
                $payload = [
                    'sort_order' => $index + 1,
                    'heading_text' => $slideData['heading_text'],
                    'counter_to' => $slideData['counter_to'],
                    'counter_text' => $slideData['counter_text'],
                    'description' => $slideData['description'],
                ];

                if (! empty($slideData['id'])) {
                    $slide = HomepageChoiceSlide::query()->findOrFail($slideData['id']);
                    $slide->update($payload);
                    $keptIds[] = $slide->id;
                } else {
                    $slide = HomepageChoiceSlide::query()->create($payload);
                    $keptIds[] = $slide->id;
                }
            }

            HomepageChoiceSlide::query()
                ->whereNotIn('id', $keptIds)
                ->delete();
        });

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_choice_updated', 'Homepage Make Your Choice section updated.');
        }

        return redirect()->route('admin.homepage-choice.edit')->with('status', 'Make Your Choice section saved.');
    }
}
