<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClickjackingProtection
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Prevent framing completely
        $response->headers->set('X-Frame-Options', 'DENY');

        // Modern browsers (recommended)
        $response->headers->set(
            'Content-Security-Policy',
            "frame-ancestors 'none';"
        );

        return $response;
    }
}

