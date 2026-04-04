<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('id', 'desc')->limit(2000)->get();

        return view('admin.users.index', compact('users'));
    }

    public function create(Request $request)
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $adminId = $request->session()->get('admin_id');
        $admin = $adminId ? User::find($adminId) : null;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        if ($admin) {
            ActivityLog::record(
                $admin,
                'user_created',
                "Admin created user {$user->username} ({$user->email})."
            );
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    public function edit(Request $request, User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $adminId = $request->session()->get('admin_id');
        $admin = $adminId ? User::find($adminId) : null;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        if ($admin) {
            ActivityLog::record(
                $admin,
                'user_updated',
                "Admin updated user {$user->username} ({$user->email})."
            );
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        $adminId = $request->session()->get('admin_id');
        $admin = $adminId ? User::find($adminId) : null;

        $username = $user->username;
        $email = $user->email;

        $user->delete();

        if ($admin) {
            ActivityLog::record(
                $admin,
                'user_deleted',
                "Admin deleted user {$username} ({$email})."
            );
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User deleted.');
    }
}
