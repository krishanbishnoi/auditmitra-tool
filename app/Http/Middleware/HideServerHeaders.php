<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HideServerHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Remove version disclosure headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // Optional hardening headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}
