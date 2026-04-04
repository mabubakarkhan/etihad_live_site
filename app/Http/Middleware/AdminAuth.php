<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('admin_id')) {
            return redirect()->route('admin.login');
        }

        $user = User::find($request->session()->get('admin_id'));

        if (! $user) {
            $request->session()->forget('admin_id');
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
