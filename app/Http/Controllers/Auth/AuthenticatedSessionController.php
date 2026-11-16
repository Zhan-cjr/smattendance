<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $agent = new \Jenssegers\Agent\Agent();
        $isV2 = function_exists('get_admin_theme') && get_admin_theme() === 'v2';

        if ($agent->isMobile()) {
            return $isV2 ? view('v2.auth.loginusermobile') : view('auth.loginusermobile');
        }

        if ($isV2) {
            return view('v2.auth.login');
        }
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        $validSchemes = ['green', 'blue', 'red', 'purple', 'orange', 'rose', 'dark'];
        $selectedScheme = $request->input('mobile_theme_scheme');

        if ($user && !empty($selectedScheme) && in_array($selectedScheme, $validSchemes, true)) {
            if (Schema::hasColumn('users', 'mobile_theme_scheme')) {
                $user->mobile_theme_scheme = $selectedScheme;
                $user->save();
            }
            Cookie::queue('smatt_user_mobile_scheme', $selectedScheme, 525600, '/', null, false, false);
        } elseif ($user && !empty($user->mobile_theme_scheme) && in_array($user->mobile_theme_scheme, $validSchemes, true)) {
            Cookie::queue('smatt_user_mobile_scheme', $user->mobile_theme_scheme, 525600, '/', null, false, false);
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
