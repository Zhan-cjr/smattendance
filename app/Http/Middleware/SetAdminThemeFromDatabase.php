<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Pengaturanumum;
use Symfony\Component\HttpFoundation\Response;

class SetAdminThemeFromDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $setting = Pengaturanumum::where('id', 1)->first();
            $theme = $setting->admin_theme ?? 'v2';
            session(['admin_theme' => $theme]);
        } catch (\Throwable $e) {
            if (!session()->has('admin_theme')) {
                session(['admin_theme' => 'v2']);
            }
        }

        return $next($request);
    }
}
