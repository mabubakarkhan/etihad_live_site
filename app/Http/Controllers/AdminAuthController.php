<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            ActivityLog::record(
                null,
                'admin_login_failed',
                'Failed admin login attempt for username: ' . $credentials['username'] . '.'
            );
            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'username' => 'Invalid credentials.',
                ]);
        }

        $request->session()->put('admin_id', $user->id);

        ActivityLog::record(
            $user,
            'admin_login',
            'Admin user logged in via admin panel.'
        );

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $user = null;

        if ($request->session()->has('admin_id')) {
            $user = User::find($request->session()->get('admin_id'));
        }

        if ($user) {
            ActivityLog::record(
                $user,
                'admin_logout',
                'Admin user logged out from admin panel.'
            );
        }

        $request->session()->forget('admin_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
