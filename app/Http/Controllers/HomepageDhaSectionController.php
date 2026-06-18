<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HomepageDhaSectionSetting;
use Illuminate\Http\Request;

class HomepageDhaSectionController extends Controller
{
    public function edit()
    {
        $setting = HomepageDhaSectionSetting::instance();

        return view('admin.homepage_dha_section.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomepageDhaSectionSetting::instance();

        $validated = $request->validate([
            'eyebrow' => ['required', 'string', 'max:255'],
            'title_line_1' => ['required', 'string', 'max:255'],
            'title_highlight' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'footer_note' => ['required', 'string', 'max:255'],
        ]);

        $setting->update($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_dha_section_updated', 'Homepage DHA section copy updated.');
        }

        return redirect()->route('admin.homepage-dha-section.edit')->with('status', 'DHA section saved. Phase cards load automatically from DHA Phases admin.');
    }
}
