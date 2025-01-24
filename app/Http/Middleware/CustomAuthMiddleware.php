<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAuthMiddleware
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
        // Example: Check if the user is authenticated
        if (!Auth::check()) {
            // Redirect or abort if not authenticated
            return redirect()->route('/');  // Or abort(403)
        }

        // Continue with the request if authenticated
        return $next($request);
    }
}
