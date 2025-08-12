<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCustomerChangedDefaultPassword
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('customer')->user();

        if ($user && $user->must_change_password) {
            // Allow only password change routes and logout
            if (! $request->routeIs('customer.password.force.*') && ! $request->routeIs('customer.logout')) {
                return redirect()->route('customer.password.force.form');
            }
        }

        return $next($request);
    }
}


