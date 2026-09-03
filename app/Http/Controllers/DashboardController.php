<?php

namespace App\Http\Controllers;

use App\Charts\JeniskelaminkaryawanChart;
use App\Charts\PendidikankaryawanChart;
use App\Charts\StatusKaryawanChart;
use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Denda;
use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\Presensi;
use App\Models\Pengumuman;
use App\Models\User;
use App\Models\Userkaryawan;
use App\Models\Pengaturanumum;
use App\Http\Controllers\KaryawanApprovalController;
use App\Jobs\SendWaMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class DashboardController extends Controller
{
    public function index(StatusKaryawanChart $chart, JeniskelaminkaryawanChart $jkchart, PendidikankaryawanChart $pddchart, Request $request)
    {
        $agent = new Agent();
        $user = User::where('id', auth()->user()->id)->first();

        // Gunakan Carbon dengan timezone aplikasi (dari config/app.php)
        // BUKAN date() yang menggunakan timezone PHP default
        $hari_ini = Carbon::now(config('app.timezone'))->format('Y-m-d');
        if ($user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
            $data['karyawan'] = Karyawan::where('nik', $userkaryawan->nik)
                ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->first();

            $data['presensi'] = Presensi::where('presensi.nik', $userkaryawan->nik)->where('presensi.tanggal', $hari_ini)->first();
            
            // Query 1: Dari tabel presensi (normal hadir, alpha, lembur, dll)
            $query_presensi = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('presensi.nik', $userkaryawan->nik)
                ->whereMonth('presensi.tanggal', Carbon::now()->month)
                ->whereYear('presensi.tanggal', Carbon::now()->year)
                ->leftJoin('presensi_izinabsen_approve', 'presensi.id', '=', 'presensi_izinabsen_approve.id_presensi')
                ->leftJoin('presensi_izinabsen', 'presensi_izinabsen_approve.kode_izin', '=', 'presensi_izinabsen.kode_izin')
                ->leftJoin('presensi_izinsakit_approve', 'presensi.id', '=', 'presensi_izinsakit_approve.id_presensi')
                ->leftJoin('presensi_izinsakit', 'presensi_izinsakit_approve.kode_izin_sakit', '=', 'presensi_izinsakit.kode_izin_sakit')
                ->leftJoin('presensi_izincuti_approve', 'presensi.id', '=', 'presensi_izincuti_approve.id_presensi')
                ->leftJoin('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
                ->select(
                    'presensi.id',
                    'presensi.nik',
                    'presensi.tanggal',
                    'presensi.kode_jam_kerja',
                    'presensi.jam_in',
                    'presensi.jam_out',
                    'presensi.status',
                    'presensi.denda',
                    'presensi.status_potongan',
                    'presensi_jamkerja.nama_jam_kerja',
                    'presensi_jamkerja.jam_masuk',
                    'presensi_jamkerja.jam_pulang',
                    'presensi_jamkerja.total_jam',
                    'presensi_jamkerja.istirahat',
                    'presensi_jamkerja.jam_awal_istirahat',
                    'presensi_jamkerja.jam_akhir_istirahat',
                    'presensi_jamkerja.lintashari',
                    'presensi_izinabsen.keterangan as keterangan_izin',
                    'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                    'presensi_izincuti.keterangan as keterangan_izin_cuti',
                    DB::raw("'presensi' as source"),
                    DB::raw("NULL as sampai")
                );

            // Query 2: Izin Absen yang approved tapi tidak ada di presensi (backward compat)
            $query_izin_absen = DB::table('presensi_izinabsen')
                ->where('presensi_izinabsen.nik', $userkaryawan->nik)
                ->where('presensi_izinabsen.status', 1)  // approved
                ->whereMonth('presensi_izinabsen.dari', Carbon::now()->month)
                ->whereYear('presensi_izinabsen.dari', Carbon::now()->year)
                ->whereNotIn('presensi_izinabsen.kode_izin', function($q) use ($userkaryawan) {
                    $q->select('presensi_izinabsen_approve.kode_izin')
                        ->from('presensi_izinabsen_approve')
                        ->join('presensi', 'presensi_izinabsen_approve.id_presensi', '=', 'presensi.id')
                        ->where('presensi.nik', $userkaryawan->nik);
                })
                ->select(
                    DB::raw('NULL as id'),
                    'presensi_izinabsen.nik',
                    'presensi_izinabsen.dari as tanggal',
                    DB::raw('NULL as kode_jam_kerja'),
                    DB::raw('NULL as jam_in'),
                    DB::raw('NULL as jam_out'),
                    DB::raw("'i' as status"),
                    DB::raw('NULL as denda'),
                    DB::raw('NULL as status_potongan'),
                    DB::raw("'Izin Absen' as nama_jam_kerja"),
                    DB::raw('NULL as jam_masuk'),
                    DB::raw('NULL as jam_pulang'),
                    DB::raw('NULL as total_jam'),
                    DB::raw('NULL as istirahat'),
                    DB::raw('NULL as jam_awal_istirahat'),
                    DB::raw('NULL as jam_akhir_istirahat'),
                    DB::raw('0 as lintashari'),
                    'presensi_izinabsen.keterangan as keterangan_izin',
                    DB::raw('NULL as keterangan_izin_sakit'),
                    DB::raw('NULL as keterangan_izin_cuti'),
                    DB::raw("'izinabsen' as source"),
                    'presensi_izinabsen.sampai as sampai'
                );

            // Query 3: Izin Sakit yang approved tapi tidak ada di presensi (backward compat)
            $query_izin_sakit = DB::table('presensi_izinsakit')
                ->where('presensi_izinsakit.nik', $userkaryawan->nik)
                ->where('presensi_izinsakit.status', 1)  // approved
                ->whereMonth('presensi_izinsakit.dari', Carbon::now()->month)
                ->whereYear('presensi_izinsakit.dari', Carbon::now()->year)
                ->whereNotIn('presensi_izinsakit.kode_izin_sakit', function($q) use ($userkaryawan) {
                    $q->select('presensi_izinsakit_approve.kode_izin_sakit')
                        ->from('presensi_izinsakit_approve')
                        ->join('presensi', 'presensi_izinsakit_approve.id_presensi', '=', 'presensi.id')
                        ->where('presensi.nik', $userkaryawan->nik);
                })
                ->select(
                    DB::raw('NULL as id'),
                    'presensi_izinsakit.nik',
                    'presensi_izinsakit.dari as tanggal',
                    DB::raw('NULL as kode_jam_kerja'),
                    DB::raw('NULL as jam_in'),
                    DB::raw('NULL as jam_out'),
                    DB::raw("'s' as status"),
                    DB::raw('NULL as denda'),
                    DB::raw('NULL as status_potongan'),
                    DB::raw("'Izin Sakit' as nama_jam_kerja"),
                    DB::raw('NULL as jam_masuk'),
                    DB::raw('NULL as jam_pulang'),
                    DB::raw('NULL as total_jam'),
                    DB::raw('NULL as istirahat'),
                    DB::raw('NULL as jam_awal_istirahat'),
                    DB::raw('NULL as jam_akhir_istirahat'),
                    DB::raw('0 as lintashari'),
                    DB::raw('NULL as keterangan_izin'),
                    'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                    DB::raw('NULL as keterangan_izin_cuti'),
                    DB::raw("'izinsakit' as source"),
                    'presensi_izinsakit.sampai as sampai'
                );

            // Query 4: Izin Cuti yang approved tapi tidak ada di presensi (backward compat)
            $query_izin_cuti = DB::table('presensi_izincuti')
                ->where('presensi_izincuti.nik', $userkaryawan->nik)
                ->where('presensi_izincuti.status', 1)  // approved
                ->whereMonth('presensi_izincuti.dari', Carbon::now()->month)
                ->whereYear('presensi_izincuti.dari', Carbon::now()->year)
                ->whereNotIn('presensi_izincuti.kode_izin_cuti', function($q) use ($userkaryawan) {
                    $q->select('presensi_izincuti_approve.kode_izin_cuti')
                        ->from('presensi_izincuti_approve')
                        ->join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
                        ->where('presensi.nik', $userkaryawan->nik);
                })
                ->select(
                    DB::raw('NULL as id'),
                    'presensi_izincuti.nik',
                    'presensi_izincuti.dari as tanggal',
                    DB::raw('NULL as kode_jam_kerja'),
                    DB::raw('NULL as jam_in'),
                    DB::raw('NULL as jam_out'),
                    DB::raw("'c' as status"),
                    DB::raw('NULL as denda'),
                    DB::raw('NULL as status_potongan'),
                    DB::raw("'Izin Cuti' as nama_jam_kerja"),
                    DB::raw('NULL as jam_masuk'),
                    DB::raw('NULL as jam_pulang'),
                    DB::raw('NULL as total_jam'),
                    DB::raw('NULL as istirahat'),
                    DB::raw('NULL as jam_awal_istirahat'),
                    DB::raw('NULL as jam_akhir_istirahat'),
                    DB::raw('0 as lintashari'),
                    DB::raw('NULL as keterangan_izin'),
                    DB::raw('NULL as keterangan_izin_sakit'),
                    'presensi_izincuti.keterangan as keterangan_izin_cuti',
                    DB::raw("'izincuti' as source"),
                    'presensi_izincuti.sampai as sampai'
                );

            // Combine dengan UNION
            $datapresensi_result = $query_presensi
                ->unionAll($query_izin_absen)
                ->unionAll($query_izin_sakit)
                ->unionAll($query_izin_cuti)
                ->get();
            
            // Expand izin/sakit/cuti yang lebih dari 1 hari menjadi multiple rows per hari
            $expanded_data = collect();
            foreach ($datapresensi_result as $item) {
                // Cek apakah ini izin/sakit/cuti dari tabel terpisah (bukan presensi normal) dengan sampai date
                if (in_array($item->source ?? null, ['izinabsen', 'izinsakit', 'izincuti']) && !empty($item->sampai)) {
                    // Loop untuk setiap hari dalam range
                    $dari_date = $item->tanggal;
                    $sampai_date = $item->sampai;
                    $current_date = $dari_date;
                    
                    while (strtotime($current_date) <= strtotime($sampai_date)) {
                        $item_copy = clone $item;
                        $item_copy->tanggal = $current_date;
                        $expanded_data->push($item_copy);
                        $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                    }
                } else {
                    // Data presensi normal atau izin dengan sampai kosong, tidak perlu di-expand
                    $expanded_data->push($item);
                }
            }
            
            // Sort by date descending
            $data['datapresensi'] = $expanded_data->sortByDesc(function($item) {
                return strtotime($item->tanggal ?? now());
            })->values();

            // Tab lembur: presensi bulan ini dengan durasi >= 12 jam
            $data['lembur_presensi'] = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('presensi.nik', $userkaryawan->nik)
                ->whereMonth('presensi.tanggal', Carbon::now()->month)
                ->whereYear('presensi.tanggal', Carbon::now()->year)
                ->whereNotNull('presensi.jam_in')
                ->whereNotNull('presensi.jam_out')
                ->whereRaw('TIMESTAMPDIFF(HOUR, presensi.jam_in, presensi.jam_out) >= 12')
                ->select(
                    'presensi.*',
                    'presensi_jamkerja.nama_jam_kerja',
                    'presensi_jamkerja.jam_masuk',
                    'presensi_jamkerja.jam_pulang',
                    'presensi_jamkerja.total_jam',
                    'presensi_jamkerja.lintashari'
                )
                ->orderBy('presensi.tanggal', 'desc')
                ->get();
            // Hitung rekap presensi dengan proper accounting untuk izin/sakit/cuti dari tabel terpisah
            $dari_bulan = Carbon::now()->startOfMonth()->format('Y-m-d');
            $sampai_bulan = Carbon::now()->endOfMonth()->format('Y-m-d');
            
            $hadir = Presensi::where('presensi.nik', $userkaryawan->nik)
                ->where('status', 'h')
                ->whereMonth('presensi.tanggal', Carbon::now()->month)
                ->whereYear('presensi.tanggal', Carbon::now()->year)
                ->count();

            $izin = Presensi::where('presensi.nik', $userkaryawan->nik)
                ->where('status', 'i')
                ->whereMonth('presensi.tanggal', Carbon::now()->month)
                ->whereYear('presensi.tanggal', Carbon::now()->year)
                ->count();
            
            // Tambah izin dari presensi_izinabsen yang approved - hitung hari dalam bulan ini
            $izin_dari_absen = DB::table('presensi_izinabsen')
                ->where('nik', $userkaryawan->nik)
                ->where('status', 1)  // status 1 = approved
                ->whereRaw('DATE(dari) <= ? AND DATE(sampai) >= ?', [$sampai_bulan, $dari_bulan])
                ->get()
                ->sum(function($item) use ($dari_bulan, $sampai_bulan) {
                    $dari = max(
                        Carbon::parse($item->dari)->format('Y-m-d'),
                        $dari_bulan
                    );
                    $sampai = min(
                        Carbon::parse($item->sampai)->format('Y-m-d'),
                        $sampai_bulan
                    );
                    return Carbon::parse($dari)->diffInDays(Carbon::parse($sampai)) + 1;
                });
            
            $sakit = Presensi::where('presensi.nik', $userkaryawan->nik)
                ->where('status', 's')
                ->whereMonth('presensi.tanggal', Carbon::now()->month)
                ->whereYear('presensi.tanggal', Carbon::now()->year)
                ->count();
            
            // Tambah sakit dari presensi_izinsakit yang approved - hitung hari dalam bulan ini
            $sakit_dari_izin = DB::table('presensi_izinsakit')
                ->where('nik', $userkaryawan->nik)
                ->where('status', 1)  // status 1 = approved
                ->whereRaw('DATE(dari) <= ? AND DATE(sampai) >= ?', [$sampai_bulan, $dari_bulan])
                ->get()
                ->sum(function($item) use ($dari_bulan, $sampai_bulan) {
                    $dari = max(
                        Carbon::parse($item->dari)->format('Y-m-d'),
                        $dari_bulan
                    );
                    $sampai = min(
                        Carbon::parse($item->sampai)->format('Y-m-d'),
                        $sampai_bulan
                    );
                    return Carbon::parse($dari)->diffInDays(Carbon::parse($sampai)) + 1;
                });

            $cuti = Presensi::where('presensi.nik', $userkaryawan->nik)
                ->where('status', 'c')
                ->whereMonth('presensi.tanggal', Carbon::now()->month)
                ->whereYear('presensi.tanggal', Carbon::now()->year)
                ->count();
            
            // Tambah cuti dari presensi_izincuti yang approved - hitung hari dalam bulan ini
            $cuti_dari_izin = DB::table('presensi_izincuti')
                ->where('nik', $userkaryawan->nik)
                ->where('status', 1)  // status 1 = approved
                ->whereRaw('DATE(dari) <= ? AND DATE(sampai) >= ?', [$sampai_bulan, $dari_bulan])
                ->get()
                ->sum(function($item) use ($dari_bulan, $sampai_bulan) {
                    $dari = max(
                        Carbon::parse($item->dari)->format('Y-m-d'),
                        $dari_bulan
                    );
                    $sampai = min(
                        Carbon::parse($item->sampai)->format('Y-m-d'),
                        $sampai_bulan
                    );
                    return Carbon::parse($dari)->diffInDays(Carbon::parse($sampai)) + 1;
                });

            $alpa = Presensi::where('presensi.nik', $userkaryawan->nik)
                ->where('status', 'a')
                ->whereMonth('presensi.tanggal', Carbon::now()->month)
                ->whereYear('presensi.tanggal', Carbon::now()->year)
                ->count();

            $lembur = Presensi::where('presensi.nik', $userkaryawan->nik)
                ->where('status', 'h')
                ->whereMonth('presensi.tanggal', Carbon::now()->month)
                ->whereYear('presensi.tanggal', Carbon::now()->year)
                ->whereNotNull('jam_in')
                ->whereNotNull('jam_out')
                ->whereRaw('TIMESTAMPDIFF(HOUR, jam_in, jam_out) >= 12')
                ->count();

            // Gabungkan hasilnya
            $data['rekappresensi'] = (object)[
                'hadir' => $hadir,
                'izin' => $izin + $izin_dari_absen,
                'sakit' => $sakit + $sakit_dari_izin,
                'cuti' => $cuti + $cuti_dari_izin,
                'alpa' => $alpa,
                'lembur' => $lembur
            ];

            $data['lembur'] = Lembur::where('nik', $userkaryawan->nik)
                ->whereIn('status', [0, 1])
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            $data['notiflembur'] = Lembur::where('nik', $userkaryawan->nik)
                ->whereIn('status', [0, 1])
                ->where(function($query) {
                    $query->whereNull('lembur_in')
                        ->orWhereNull('lembur_out');
                })
                ->count();

            // Cek apakah hari ini adalah ulang tahun karyawan
            $isBirthday = false;
            $umur = null;
            if ($data['karyawan'] && $data['karyawan']->tanggal_lahir) {
                $tanggalLahir = Carbon::parse($data['karyawan']->tanggal_lahir);
                $today = Carbon::now();
                if ($tanggalLahir->month == $today->month && $tanggalLahir->day == $today->day) {
                    $isBirthday = true;
                    $umur = $tanggalLahir->age;
                }
            }
            $data['is_birthday'] = $isBirthday;
            $data['umur'] = $umur;

            // Cek Notifikasi Kontrak Berakhir (H-30)
            $kontrak = DB::table('kontrak')
                ->where('nik', $userkaryawan->nik)
                ->where('status_kontrak', '1')
                ->orderBy('sampai', 'desc')
                ->first();

            $notif_kontrak = null;
            if ($kontrak) {
                $tgl_akhir = Carbon::parse($kontrak->sampai);
                $today = Carbon::now(config('app.timezone'));
                $sisa_hari = $today->diffInDays($tgl_akhir, false); // false agar negatif jika lewat

                // Jika sisa hari <= 30 hari dan belum lewat (atau lewat hari ini)
                // Kita anggap sisa_hari < 0 berarti sudah expired
                if ($sisa_hari >= 0 && $sisa_hari <= 30) {
                     $notif_kontrak = [
                        'sisa_hari' => $sisa_hari,
                        'tanggal_akhir' => $tgl_akhir->translatedFormat('d F Y')
                    ];
                }
            }
            $data['notif_kontrak'] = $notif_kontrak;

            // Cek Notifikasi SP Aktif
            $notif_sp = DB::table('pelanggaran')
                ->where('nik', $userkaryawan->nik)
                ->where('dari', '<=', $today->toDateString())
                ->where('sampai', '>=', $today->toDateString())
                ->first();
            
            $data['notif_sp'] = $notif_sp;

            // Cek Pengumuman Aktif (Ambil yang terakhir dibuat)
            $data['pengumuman'] = Pengumuman::orderBy('created_at', 'desc')->first();
            $data['namasettings'] = Pengaturanumum::first();
            $data['denda_list'] = Denda::orderBy('dari')->get()->toArray();
            $data['pendingApprovalCount'] = KaryawanApprovalController::getPendingCount(auth()->user()->id);
            $data['bulan_skrg'] = Carbon::parse($hari_ini)->translatedFormat('F');
            $data['tahun_skrg'] = Carbon::parse($hari_ini)->year;

            return view('dashboard.karyawan', $data);
        } else {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            //Dashboard Admin
            $sk = new Karyawan();

            // Modifikasi request untuk getRekapstatuskaryawan dengan filter akses
            $filterRequest = new Request($request->all());
            if (!$user->isSuperAdmin()) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();

                // Jika user tidak punya akses, set filter untuk tidak menampilkan data
                if (empty($userCabangs) || empty($userDepartemens)) {
                    $filterRequest->merge(['kode_cabang' => 'INVALID']);
                } else {
                    // Tambahkan filter akses ke request jika belum ada filter dari user
                    if (empty($filterRequest->kode_cabang) && !empty($userCabangs)) {
                        // Jika hanya 1 cabang, set sebagai default
                        if (count($userCabangs) == 1) {
                            $filterRequest->merge(['kode_cabang' => $userCabangs[0]]);
                        }
                    }
                    if (empty($filterRequest->kode_dept) && !empty($userDepartemens)) {
                        // Jika hanya 1 departemen, set sebagai default
                        if (count($userDepartemens) == 1) {
                            $filterRequest->merge(['kode_dept' => $userDepartemens[0]]);
                        }
                    }
                }
            }
            $data['status_karyawan'] = $sk->getRekapstatuskaryawan($filterRequest);

            // Modifikasi request untuk chart dengan filter akses
            $chartRequest = new Request($request->all());
            if (!$user->isSuperAdmin()) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();

                // Tambahkan filter akses ke request
                if (!empty($userCabangs)) {
                    $chartRequest->merge(['user_cabangs' => $userCabangs]);
                }
                if (!empty($userDepartemens)) {
                    $chartRequest->merge(['user_departemens' => $userDepartemens]);
                }
            }

            $data['chart'] = $chart->build($chartRequest);
            $data['jkchart'] = $jkchart->build($chartRequest);
            $data['pddchart'] = $pddchart->build($chartRequest);

            // Target Tanggal
            $targetTanggal = !empty($request->tanggal) ? $request->tanggal : Carbon::now(config('app.timezone'))->format('Y-m-d');
            $data['target_tanggal'] = $targetTanggal;

            $queryPresensi = Presensi::query();
            $queryPresensi->join('karyawan', 'presensi.nik', '=', 'karyawan.nik');
            $queryPresensi->leftJoin('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja');
            $queryPresensi->select(
                DB::raw("SUM(IF(presensi.status='h',1,0)) as hadir"),
                DB::raw("SUM(IF(presensi.status='i',1,0)) as izin"),
                DB::raw("SUM(IF(presensi.status='s',1,0)) as sakit"),
                DB::raw("SUM(IF(presensi.status='a',1,0)) as alpa"),
                DB::raw("SUM(IF(presensi.status='c',1,0)) as cuti"),
                DB::raw("SUM(IF(presensi.status='h' AND presensi_jamkerja.jam_masuk IS NOT NULL AND TIME(presensi.jam_in) > TIME(presensi_jamkerja.jam_masuk), 1, 0)) as terlambat"),
                DB::raw("SUM(IF(presensi.status='h' AND (presensi_jamkerja.jam_masuk IS NULL OR TIME(presensi.jam_in) <= TIME(presensi_jamkerja.jam_masuk)), 1, 0)) as tepat_waktu")
            );

            // Filter berdasarkan akses cabang dan departemen jika bukan super admin
            if (!$user->isSuperAdmin()) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();

                if (!empty($userCabangs)) {
                    $queryPresensi->whereIn('karyawan.kode_cabang', $userCabangs);
                } else {
                    $queryPresensi->whereRaw('1 = 0');
                }

                if (!empty($userDepartemens)) {
                    $queryPresensi->whereIn('karyawan.kode_dept', $userDepartemens);
                } else {
                    $queryPresensi->whereRaw('1 = 0');
                }
            }

            $queryPresensi->where('presensi.tanggal', $targetTanggal);

            if (!empty($request->kode_cabang)) {
                $queryPresensi->where('karyawan.kode_cabang', $request->kode_cabang);
            }

            if (!empty($request->kode_dept)) {
                $queryPresensi->where('karyawan.kode_dept', $request->kode_dept);
            }
            $data['rekappresensi'] = $queryPresensi->first();

            // Presensi Log Terkini Hari Ini
            $queryPresensiTerbaru = Presensi::join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
                ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->leftJoin('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select(
                    'presensi.*',
                    'karyawan.nama_karyawan',
                    'karyawan.foto',
                    'departemen.nama_dept',
                    'cabang.nama_cabang',
                    'jabatan.nama_jabatan',
                    'presensi_jamkerja.jam_masuk',
                    'presensi_jamkerja.jam_pulang',
                    'presensi_jamkerja.nama_jam_kerja'
                )
                ->where('presensi.tanggal', $targetTanggal);

            if (!$user->isSuperAdmin()) {
                if (!empty($userCabangs)) {
                    $queryPresensiTerbaru->whereIn('karyawan.kode_cabang', $userCabangs);
                } else {
                    $queryPresensiTerbaru->whereRaw('1 = 0');
                }
                if (!empty($userDepartemens)) {
                    $queryPresensiTerbaru->whereIn('karyawan.kode_dept', $userDepartemens);
                } else {
                    $queryPresensiTerbaru->whereRaw('1 = 0');
                }
            }

            if (!empty($request->kode_cabang)) {
                $queryPresensiTerbaru->where('karyawan.kode_cabang', $request->kode_cabang);
            }
            if (!empty($request->kode_dept)) {
                $queryPresensiTerbaru->where('karyawan.kode_dept', $request->kode_dept);
            }

            $data['presensi_terbaru'] = $queryPresensiTerbaru->orderBy('presensi.jam_in', 'desc')
                ->limit(6)
                ->get();

            // Rekap Departemen untuk Widget Progress
            $queryDeptRekap = DB::table('departemen')
                ->leftJoin('karyawan', function($join) {
                    $join->on('departemen.kode_dept', '=', 'karyawan.kode_dept')
                        ->where('karyawan.status_aktif_karyawan', 1);
                })
                ->leftJoin('presensi', function($join) use ($targetTanggal) {
                    $join->on('karyawan.nik', '=', 'presensi.nik')
                        ->where('presensi.tanggal', $targetTanggal)
                        ->where('presensi.status', 'h');
                })
                ->select(
                    'departemen.kode_dept',
                    'departemen.nama_dept',
                    DB::raw('COUNT(DISTINCT karyawan.nik) as total_karyawan'),
                    DB::raw('COUNT(DISTINCT presensi.nik) as total_hadir')
                )
                ->groupBy('departemen.kode_dept', 'departemen.nama_dept')
                ->having('total_karyawan', '>', 0)
                ->orderBy('total_karyawan', 'desc');

            if (!$user->isSuperAdmin()) {
                if (!empty($userDepartemens)) {
                    $queryDeptRekap->whereIn('departemen.kode_dept', $userDepartemens);
                }
                if (!empty($userCabangs)) {
                    $queryDeptRekap->whereIn('karyawan.kode_cabang', $userCabangs);
                }
            }
            if (!empty($request->kode_cabang)) {
                $queryDeptRekap->where('karyawan.kode_cabang', $request->kode_cabang);
            }
            if (!empty($request->kode_dept)) {
                $queryDeptRekap->where('departemen.kode_dept', $request->kode_dept);
            }

            $data['rekap_dept'] = $queryDeptRekap->limit(6)->get();

            $data['departemen'] = $user->getDepartemen();
            $data['cabang'] = $user->getCabang();
            $today = Carbon::now(config('app.timezone'));
            $data['birthday'] = Karyawan::whereMonth('tanggal_lahir', $today->month)->whereDay('tanggal_lahir', $today->day)
                ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->select(
                    'karyawan.*',
                    'jabatan.nama_jabatan',
                    'departemen.nama_dept',
                    'cabang.nama_cabang',
                    'karyawan.status_karyawan'
                )
                ->when(!$user->isSuperAdmin(), function ($query) use ($user) {
                    $userCabangs = $user->getCabangCodes();
                    $userDepartemens = $user->getDepartemenCodes();

                    if (!empty($userCabangs)) {
                        $query->whereIn('karyawan.kode_cabang', $userCabangs);
                    } else {
                        $query->whereRaw('1 = 0');
                    }

                    if (!empty($userDepartemens)) {
                        $query->whereIn('karyawan.kode_dept', $userDepartemens);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                })
                ->when($request->kode_cabang, function ($query) use ($request) {
                    $query->where('karyawan.kode_cabang', $request->kode_cabang);
                })
                ->when($request->kode_dept, function ($query) use ($request) {
                    $query->where('karyawan.kode_dept', $request->kode_dept);
                })
                ->orderBy('tanggal_lahir', 'asc')->get();

            // Filter akses untuk kontrak
            $userCabangs = null;
            $userDepartemens = null;
            if (!$user->isSuperAdmin()) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
            }

            $data['kontrak_lewat'] = $sk->getRekapkontrak(0, $userCabangs, $userDepartemens);
            $data['kontrak_bulanini'] = $sk->getRekapkontrak(1, $userCabangs, $userDepartemens);
            $data['kontrak_bulandepan'] = $sk->getRekapkontrak(2, $userCabangs, $userDepartemens);
            $data['kontrak_duabulan'] = $sk->getRekapkontrak(3, $userCabangs, $userDepartemens);

            return view('dashboard.dashboard', $data);
        }
    }

    public function kirimUcapanBirthday(Request $request)
    {
        try {
            // Ambil karyawan yang ulang tahun hari ini (menggunakan timezone aplikasi)
            $today = Carbon::now(config('app.timezone'));
            $birthday = Karyawan::whereMonth('tanggal_lahir', $today->month)
                ->whereDay('tanggal_lahir', $today->day)
                ->when($request->kode_cabang, function ($query) use ($request) {
                    $query->where('kode_cabang', $request->kode_cabang);
                })
                ->when($request->kode_dept, function ($query) use ($request) {
                    $query->where('kode_dept', $request->kode_dept);
                })
                ->whereNotNull('no_hp')
                ->where('no_hp', '!=', '')
                ->get();

            if ($birthday->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada karyawan yang ulang tahun hari ini atau tidak ada nomor HP yang tersedia.'
                ], 400);
            }

            $count = 0;
            foreach ($birthday as $karyawan) {
                // Hitung umur
                $umur = Carbon::parse($karyawan->tanggal_lahir)->age;

                // Format pesan ucapan ulang tahun
                $message = "🎉 *Selamat Ulang Tahun!* 🎂\n\n";
                $message .= "Halo *{$karyawan->nama_karyawan}*,\n\n";
                $message .= "Di hari yang istimewa ini, kami ingin mengucapkan:\n\n";
                $message .= "🎂 *Selamat Ulang Tahun yang ke-{$umur}!* 🎂\n\n";
                $message .= "Semoga di hari ulang tahunmu ini:\n";
                $message .= "✨ Panjang umur\n";
                $message .= "✨ Sehat selalu\n";
                $message .= "✨ Bahagia selalu\n";
                $message .= "✨ Sukses dalam karir\n";
                $message .= "✨ Diberkahi rezeki yang berlimpah\n\n";
                $message .= "Terima kasih atas dedikasi dan kontribusinya selama ini. Semoga hubungan kerja kita terus berjalan dengan baik!\n\n";
                $message .= "*Salam Hangat,*\nTim HR";

                // Format nomor HP (hapus 0 di depan jika ada, pastikan format 62xxx)
                $phoneNumber = $karyawan->no_hp;
                $phoneNumber = preg_replace('/^0+/', '', $phoneNumber);
                if (!str_starts_with($phoneNumber, '62')) {
                    $phoneNumber = '62' . $phoneNumber;
                }

                // Dispatch job untuk mengirim WhatsApp
                SendWaMessage::dispatch($phoneNumber, $message, true);
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "Ucapan ulang tahun sedang dikirim ke {$count} karyawan."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
