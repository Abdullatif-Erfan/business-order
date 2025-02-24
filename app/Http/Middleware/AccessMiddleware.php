<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AccessMiddleware
{
    public function handle(Request $request, Closure $next, $module = null, $permission = null)
    {
        // Get the authenticated user
        $user = auth()->user();
        $accessInfo = Session::get('accessInfo', []);

        // Log::info("User: " . ($user ? $user->id : 'Guest') . ", Module: " . $module . ", Permission: " . $permission);

        // If the user is an admin, allow access
        if ($user && $user->isAdmin == 1) {
            return $next($request);
        }

        // If no module or permission is passed, allow the request
        // some request doesn't need permission, should be allowed
        if (!$module || !$permission) {
            return $next($request);
        }

        // Check if module exists in accessInfo
        if (isset($accessInfo[$module])) {
            $access = $accessInfo[$module];

            // Log access data safely
            // Log::info('Access Info for module ' . $module . ': ' . json_encode($access));

            // Check permission
            if (isset($access[$permission]) && ($access[$permission] == 1 || ($access['total_access'] ?? 0) == 1)) {
                // Log::info('User has required permission: ' . $permission);
                return $next($request);
            }
        } else {
            // Log::warning("Module '$module' not found in accessInfo session.");
        }

        // 🔹 Check if request expects JSON (e.g., API calls)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 🔹 Otherwise, return an access denied view
        return response()->view('component.access', [], 403);
    }
}
