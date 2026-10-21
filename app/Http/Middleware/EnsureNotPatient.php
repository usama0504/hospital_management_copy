<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Patient-portal users ko staff area (dashboard, patients list, doctors,
 * reports, API...) se bahar rakhta hai. Web par unhe portal par bhej deta hai,
 * API par 403 deta hai.
 */
class EnsureNotPatient
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('patient')) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Patient accounts cannot use this area.',
                ], 403);
            }

            return redirect()->route('portal.dashboard');
        }

        return $next($request);
    }
}
