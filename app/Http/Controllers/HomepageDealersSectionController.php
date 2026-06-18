<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HomepageDealersSectionSetting;
use Illuminate\Http\Request;

class HomepageDealersSectionController extends Controller
{
    public function edit()
    {
        $setting = HomepageDealersSectionSetting::instance();

        return view('admin.homepage_dealers_section.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomepageDealersSectionSetting::instance();

        $validated = $request->validate([
            'eyebrow' => ['required', 'string', 'max:255'],
            'title_line_1' => ['required', 'string', 'max:255'],
            'title_highlight' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'footer_note' => ['required', 'string', 'max:255'],
            'card_badge' => ['required', 'string', 'max:255'],
            'cta_label' => ['required', 'string', 'max:255'],
            'view_all_label' => ['required', 'string', 'max:255'],
        ]);

        $setting->update($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_dealers_section_updated', 'Homepage dealers section copy updated.');
        }

        return redirect()->route('admin.homepage-dealers-section.edit')->with('status', 'Dealers section saved. Agent cards load automatically from dealers marked “Show on homepage”.');
    }
}
