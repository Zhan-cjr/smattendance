<?php

namespace App\Providers;

use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izindinas;
use App\Models\Izinsakit;
use App\Models\Lembur;
use App\Models\Pengaturanumum;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class Globalprovider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Guard $auth): void
    {
        // Share general_setting globally with try-catch to prevent errors during dump-autoload
        try {
            $settings = Pengaturanumum::first();
            View::share('general_setting', $settings);

            // Modern theme variables (shared globally for modern layout)
            $scheme = $settings->mobile_theme_scheme ?? 'green';
            $themeColors = [
                'green' => ['primary' => '#32745e', 'primary_light' => '#58907D', 'bg_body' => '#f0fdf9'],
                'blue' => ['primary' => '#0d47a1', 'primary_light' => '#1976d2', 'bg_body' => '#eff6ff'],
                'red' => ['primary' => '#b71c1c', 'primary_light' => '#d32f2f', 'bg_body' => '#fef2f2'],
                'purple' => ['primary' => '#4a148c', 'primary_light' => '#7b1fa2', 'bg_body' => '#faf5ff'],
                'orange' => ['primary' => '#e65100', 'primary_light' => '#f57c00', 'bg_body' => '#fff8f1'],
            ];
            $t = $themeColors[$scheme] ?? $themeColors['green'];
            $isDark = false;
            View::share('t', $t);
            View::share('isDark', $isDark);
        } catch (\Exception $e) {
            View::share('general_setting', null);
            View::share('t', ['primary' => '#32745e', 'primary_light' => '#58907D', 'bg_body' => '#f0fdf9']);
            View::share('isDark', false);
        }


        view()->composer('*', function ($view) use ($auth) {
            static $composed = false;
            if ($composed)
                return;
            $composed = true;
            if ($auth->check()) {
                /** @var \App\Models\User $user */
                $user = $auth->user();
                $isSuperAdmin = $user->isSuperAdmin();
                $userCabangs = $isSuperAdmin ? [] : $user->getCabangCodes();
                $userDepartemens = $isSuperAdmin ? [] : $user->getDepartemenCodes();

                $applyFilter = function ($query) use ($isSuperAdmin, $userCabangs, $userDepartemens) {
                    if (!$isSuperAdmin) {
                        $query->join('karyawan', $query->getModel()->getTable() . '.nik', '=', 'karyawan.nik');
                        if (!empty($userCabangs)) {
                            $query->whereIn('karyawan.kode_cabang', $userCabangs);
                        }
                        if (!empty($userDepartemens)) {
                            $query->whereIn('karyawan.kode_dept', $userDepartemens);
                        }
                    }
                };

                // Queries for Counts
                $q_izinabsen = Izinabsen::where('status', 0);
                $applyFilter($q_izinabsen);
                $notifikasi_izinabsen = $q_izinabsen->count();

                $q_izinsakit = Izinsakit::where('status', 0);
                $applyFilter($q_izinsakit);
                $notifikasi_izinsakit = $q_izinsakit->count();

                $q_izincuti = Izincuti::where('status', 0);
                $applyFilter($q_izincuti);
                $notifikasi_izincuti = $q_izincuti->count();

                $q_lembur = Lembur::where('status', 0);
                $applyFilter($q_lembur);
                $notifikasi_lembur = $q_lembur->count();

                $q_izindinas = Izindinas::where('status', 0);
                $applyFilter($q_izindinas);
                $notifikasi_izin_dinas = $q_izindinas->count();

                $q_ajuanjadwal = \App\Models\AjuanJadwal::where('status', 'p');
                $applyFilter($q_ajuanjadwal);
                $notifikasi_ajuan_jadwal = $q_ajuanjadwal->count();

                // Queries for Data List (already joining karyawan in original code, but we need to handle it carefully)
                // Actually, original code joined karyawan below. My applyFilter also joins. 
                // To avoid double join, I should construct these queries fresh or be careful.
                // The original code for data_izin used joins for selecting names.

                $data_izinabsen = Izinabsen::select('presensi_izinabsen.nik', 'nama_karyawan', DB::raw('"i" as status'), 'presensi_izinabsen.created_at')
                    ->where('status', 0)
                    ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik');

                if (!$isSuperAdmin) {
                    if (!empty($userCabangs))
                        $data_izinabsen->whereIn('karyawan.kode_cabang', $userCabangs);
                    if (!empty($userDepartemens))
                        $data_izinabsen->whereIn('karyawan.kode_dept', $userDepartemens);
                }

                $data_izinsakit = Izinsakit::select('presensi_izinsakit.nik', 'nama_karyawan', DB::raw('"s" as status'), 'presensi_izinsakit.created_at')
                    ->where('status', 0)
                    ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik');
                if (!$isSuperAdmin) {
                    if (!empty($userCabangs))
                        $data_izinsakit->whereIn('karyawan.kode_cabang', $userCabangs);
                    if (!empty($userDepartemens))
                        $data_izinsakit->whereIn('karyawan.kode_dept', $userDepartemens);
                }

                $data_izincuti = Izincuti::select('presensi_izincuti.nik', 'nama_karyawan', DB::raw('"c" as status'), 'presensi_izincuti.created_at')
                    ->where('status', 0)
                    ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik');
                if (!$isSuperAdmin) {
                    if (!empty($userCabangs))
                        $data_izincuti->whereIn('karyawan.kode_cabang', $userCabangs);
                    if (!empty($userDepartemens))
                        $data_izincuti->whereIn('karyawan.kode_dept', $userDepartemens);
                }

                $data_izin_dinas = Izindinas::select('presensi_izindinas.nik', 'nama_karyawan', DB::raw('"d" as status'), 'presensi_izindinas.created_at')
                    ->where('status', 0)
                    ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik');
                if (!$isSuperAdmin) {
                    if (!empty($userCabangs))
                        $data_izin_dinas->whereIn('karyawan.kode_cabang', $userCabangs);
                    if (!empty($userDepartemens))
                        $data_izin_dinas->whereIn('karyawan.kode_dept', $userDepartemens);
                }

                $data_izin = $data_izinabsen->unionAll($data_izinsakit)->unionAll($data_izincuti)->unionAll($data_izin_dinas)->get();

                $notifikasi_ajuan_absen = $notifikasi_izinabsen + $notifikasi_izincuti + $notifikasi_izinsakit + $notifikasi_izin_dinas + $notifikasi_ajuan_jadwal;
                $shareddata = [
                    'notifikasi_izinabsen' => $notifikasi_izinabsen,
                    'notifikasi_izinsakit' => $notifikasi_izinsakit,
                    'notifikasi_izincuti' => $notifikasi_izincuti,
                    'notifikasi_lembur' => $notifikasi_lembur,
                    'notifikasi_izin_dinas' => $notifikasi_izin_dinas,
                    'notifikasi_ajuan_absen' => $notifikasi_ajuan_absen,
                    'notifikasi_ajuan_jadwal' => $notifikasi_ajuan_jadwal,
                    'data_izin' => $data_izin,
                ];
                View::share($shareddata);
            }
        });
    }
}
