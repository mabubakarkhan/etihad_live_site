<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FirstVisitPopupSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FirstVisitPopupSettingsController extends Controller
{
    public function edit(): View
    {
        $popup = FirstVisitPopupSetting::instance();

        return view('admin.first_visit_popup.edit', compact('popup'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_enabled' => ['nullable', 'boolean'],
            'force_show_every_time' => ['nullable', 'boolean'],
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:500'],
            'body_text' => ['nullable', 'string', 'max:2000'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'form_heading' => ['nullable', 'string', 'max:255'],
            'form_submit_label' => ['nullable', 'string', 'max:120'],
            'show_logo' => ['nullable', 'boolean'],
            'delay_ms' => ['nullable', 'integer', 'min:0', 'max:5000'],
            'background_image' => ['nullable', 'image', 'max:5120'],
        ]);

        $popup = FirstVisitPopupSetting::instance();

        $data = [
            'is_enabled' => $request->boolean('is_enabled'),
            'force_show_every_time' => $request->boolean('force_show_every_time'),
            'eyebrow' => $validated['eyebrow'] ?? null,
            'heading' => $validated['heading'] ?? null,
            'subheading' => $validated['subheading'] ?? null,
            'body_text' => $validated['body_text'] ?? null,
            'cta_label' => $validated['cta_label'] ?? null,
            'form_heading' => $validated['form_heading'] ?? null,
            'form_submit_label' => $validated['form_submit_label'] ?? null,
            'show_logo' => $request->boolean('show_logo'),
            'delay_ms' => (int) ($validated['delay_ms'] ?? 0),
        ];

        if ($request->boolean('remove_background_image') && $popup->background_image) {
            Storage::disk('public')->delete($popup->background_image);
            $data['background_image'] = null;
        }

        if ($request->hasFile('background_image')) {
            if ($popup->background_image) {
                Storage::disk('public')->delete($popup->background_image);
            }
            $data['background_image'] = $request->file('background_image')->store('first-visit-popup', 'public');
        }

        $popup->update($data);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'first_visit_popup_updated', 'First visit popup settings updated.');
        }

        return redirect()->route('admin.first-visit-popup.edit')->with('status', 'First visit popup settings saved.');
    }
}
