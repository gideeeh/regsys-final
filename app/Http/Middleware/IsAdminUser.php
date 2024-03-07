<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (Auth::check()) {
            // Check if user is an admin
            if (Auth::user()->role === 'admin') {
                // Allow request to proceed to intended route for admin
                return $next($request);
            } else {
                // If user is not an admin, redirect to the user dashboard
                return redirect()->route('user.dashboard');
            }
        }

        // If user is not authenticated, redirect to login
        return redirect()->route('login');
    }
}
