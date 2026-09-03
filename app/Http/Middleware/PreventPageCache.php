<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventPageCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya tambahkan header untuk HTML responses (view)
        // Jangan terapkan ke API (JSON), file downloads, atau static assets
        $contentType = $response->headers->get('Content-Type', '');
        
        // Jika response adalah HTML, tambahkan cache control headers
        if (str_contains($contentType, 'text/html')) {
            $response->headers->set('Cache-Control', 'private, no-cache, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }
}
