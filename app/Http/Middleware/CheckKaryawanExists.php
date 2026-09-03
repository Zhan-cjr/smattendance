<?php

namespace App\Http\Middleware;

use App\Models\Karyawan;
use App\Models\Userkaryawan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckKaryawanExists
{
    /**
     * Jika user dengan role karyawan login tetapi data karyawan-nya
     * sudah tidak ada di tabel karyawan, langsung logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', Auth::id())->first();

            // Cek 1: user_karyawan tidak ada
            if (!$userkaryawan) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('loginuser')
                    ->with('error', 'Akun karyawan Anda tidak valid. Silahkan hubungi administrator.');
            }

            // Cek 2: karyawan dengan NIK tersebut tidak ada
            $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();
            if (!$karyawan) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('loginuser')
                    ->with('error', 'Data karyawan Anda tidak ditemukan. Silahkan hubungi administrator.');
            }
        }

        return $next($request);
    }
}
