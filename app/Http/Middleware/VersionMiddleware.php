<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VersionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('version')) {
            return response()->json(['success' => 0, 'message' => 'Version is missing in header']);
        }

        return $next($request);
    }
}
