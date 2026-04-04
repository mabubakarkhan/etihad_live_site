<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    protected function currentAdmin(Request $request): User
    {
        $adminId = $request->session()->get('admin_id');

        $admin = $adminId ? User::find($adminId) : null;

        if (! $admin) {
            abort(403);
        }

        return $admin;
    }

    public function show(Request $request)
    {
        $admin = $this->currentAdmin($request);

        return view('admin.profile', [
            'admin' => $admin,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $admin = $this->currentAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $admin->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $admin->id],
            'timezone' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:10'],
            'dark_mode' => ['nullable', 'boolean'],
            'email_notifications' => ['nullable', 'boolean'],
        ]);

        $admin->name = $validated['name'];
        $admin->username = $validated['username'];
        $admin->email = $validated['email'];

        $settings = $admin->settings ?? [];
        $settings['timezone'] = $validated['timezone'] ?? ($settings['timezone'] ?? null);
        $settings['language'] = $validated['language'] ?? ($settings['language'] ?? 'en');
        $settings['dark_mode'] = (bool) ($validated['dark_mode'] ?? $request->boolean('dark_mode'));
        $settings['email_notifications'] = (bool) ($validated['email_notifications'] ?? $request->boolean('email_notifications'));

        $admin->settings = $settings;

        $admin->save();

        ActivityLog::record(
            $admin,
            'admin_profile_updated',
            'Admin updated their profile and settings.'
        );

        return redirect()
            ->route('admin.profile.show')
            ->with('status', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $admin = $this->currentAdmin($request);

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $admin->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput($request->except(['current_password', 'password', 'password_confirmation']));
        }

        $admin->password = $validated['password'];
        $admin->save();

        ActivityLog::record(
            $admin,
            'admin_password_changed',
            'Admin changed their account password.'
        );

        return redirect()
            ->route('admin.profile.show')
            ->with('password_status', 'Password updated successfully.');
    }
}

