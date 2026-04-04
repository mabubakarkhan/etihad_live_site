<?php

use App\Models\User;

if (! function_exists('admin_user')) {
    /**
     * Get the current admin user from session (for admin panel).
     */
    function admin_user(): ?User
    {
        $id = session('admin_id');

        return $id ? User::find($id) : null;
    }
}
