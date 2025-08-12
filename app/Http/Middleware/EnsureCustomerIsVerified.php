<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCustomerIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('customer')->user();

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return redirect()->route('customer.verification.otp.form');
        }

        return $next($request);
    }
}


