<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HomepageWhatSetsApartCard;
use App\Models\HomepageWhatSetsApartSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomepageWhatSetsApartController extends Controller
{
    public function edit()
    {
        $setting = HomepageWhatSetsApartSetting::instance();
        $cards = HomepageWhatSetsApartSetting::orderedCards();

        return view('admin.homepage_what_sets_apart.edit', compact('setting', 'cards'));
    }

    public function update(Request $request)
    {
        $setting = HomepageWhatSetsApartSetting::instance();

        $validated = $request->validate([
            'title_line_1' => ['required', 'string', 'max:255'],
            'title_highlight' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:2000'],
            'cards' => ['required', 'array', 'min:1', 'max:12'],
            'cards.*.id' => ['nullable', 'integer', 'exists:homepage_what_sets_apart_cards,id'],
            'cards.*.title' => ['required', 'string', 'max:255'],
            'cards.*.description' => ['required', 'string', 'max:2000'],
            'cards.*.icon_svg' => ['nullable', 'string', 'max:10000'],
            'cards.*.icon_image' => ['nullable', 'image', 'max:2048'],
            'cards.*.remove_icon_image' => ['nullable', 'boolean'],
        ]);

        $setting->update([
            'title_line_1' => $validated['title_line_1'],
            'title_highlight' => $validated['title_highlight'],
            'subtitle' => $validated['subtitle'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $keptIds = [];

            foreach ($validated['cards'] as $index => $cardData) {
                $card = ! empty($cardData['id'])
                    ? HomepageWhatSetsApartCard::query()->findOrFail($cardData['id'])
                    : new HomepageWhatSetsApartCard();

                $card->fill([
                    'sort_order' => $index + 1,
                    'title' => $cardData['title'],
                    'description' => $cardData['description'],
                    'icon_svg' => $cardData['icon_svg'] ?? null,
                ]);

                if ($request->boolean("cards.{$index}.remove_icon_image") && $card->icon_image) {
                    public_storage_delete($card->icon_image);
                    $card->icon_image = null;
                }

                if ($request->hasFile("cards.{$index}.icon_image")) {
                    if ($card->icon_image) {
                        public_storage_delete($card->icon_image);
                    }
                    $card->icon_image = public_storage_store_upload($request->file("cards.{$index}.icon_image"), 'homepage-what-sets-apart');
                }

                $card->save();
                $keptIds[] = $card->id;
            }

            $cardsToDelete = HomepageWhatSetsApartCard::query()
                ->whereNotIn('id', $keptIds)
                ->get();

            foreach ($cardsToDelete as $card) {
                if ($card->icon_image) {
                    public_storage_delete($card->icon_image);
                }
                $card->delete();
            }
        });

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_what_sets_apart_updated', 'Homepage What Set Us Apart section updated.');
        }

        return redirect()->route('admin.homepage-what-sets-apart.edit')->with('status', 'What Set Us Apart section saved.');
    }
}
