<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SyndicationTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Syndication-Token');

        if (! $token || $token !== config('services.syndication.token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
