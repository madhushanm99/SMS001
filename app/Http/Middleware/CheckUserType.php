<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$types)
    {
        $user = $request->user();

        if (!$user || !in_array($user->usertype, $types)) {
            return redirect()->back()->with('error', 'You do not have access to this action.');
        }

        return $next($request);
    }
}
