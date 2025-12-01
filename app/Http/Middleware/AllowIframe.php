<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowIframe
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Remove the blocking header (if Laravel set it previously)
        $response->headers->remove('X-Frame-Options');

        // 2. Add the modern CSP header to allow embedding
        // 'self' allows the site to embed itself.
        // The URL is your WordPress site that will host the iframe.
        $response->headers->set(
            'Content-Security-Policy',
            "frame-ancestors 'self' https://bodhidhammayan.org/"
        );

        return $response;
    }
}
