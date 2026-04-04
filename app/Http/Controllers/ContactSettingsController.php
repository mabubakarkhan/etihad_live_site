<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ContactSetting;
use Illuminate\Http\Request;

class ContactSettingsController extends Controller
{
    public function edit()
    {
        $contactSetting = ContactSetting::instance();
        return view('admin.contact_settings.edit', compact('contactSetting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'timings' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:100'],
            'facebook' => ['nullable', 'string', 'max:500'],
            'instagram' => ['nullable', 'string', 'max:500'],
            'linkedin' => ['nullable', 'string', 'max:500'],
            'youtube' => ['nullable', 'string', 'max:500'],
        ]);

        $contactSetting = ContactSetting::instance();
        $contactSetting->update($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'contact_settings_updated', 'Contact settings updated.');
        }

        return redirect()->route('admin.contact-settings.edit')->with('status', 'Contact settings updated.');
    }
}
