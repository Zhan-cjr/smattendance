<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Lembur;
use App\Models\Presensi;
use App\Models\Userkaryawan;
use App\Models\Pinjaman;
use App\Models\RencanaCicilan;
use App\Models\PembayaranPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShortcutController extends Controller
{
    public function index()
    {
        $hari_ini = date("Y-m-d");
        $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
        
        $data['karyawan'] = Karyawan::where('nik', $userkaryawan->nik)
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        $data['presensi'] = Presensi::where('presensi.nik', $userkaryawan->nik)->where('presensi.tanggal', $hari_ini)->first();
        $data['datapresensi'] = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('presensi.nik', $userkaryawan->nik)
            ->leftJoin('presensi_izinabsen_approve', 'presensi.id', '=', 'presensi_izinabsen_approve.id_presensi')
            ->leftJoin('presensi_izinabsen', 'presensi_izinabsen_approve.kode_izin', '=', 'presensi_izinabsen.kode_izin')

            ->leftJoin('presensi_izinsakit_approve', 'presensi.id', '=', 'presensi_izinsakit_approve.id_presensi')
            ->leftJoin('presensi_izinsakit', 'presensi_izinsakit_approve.kode_izin_sakit', '=', 'presensi_izinsakit.kode_izin_sakit')

            ->leftJoin('presensi_izincuti_approve', 'presensi.id', '=', 'presensi_izincuti_approve.id_presensi')
            ->leftJoin('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
            ->select(
                'presensi.*',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.total_jam',
                'presensi_jamkerja.lintashari',
                'presensi_izinabsen.keterangan as keterangan_izin',
                'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                'presensi_izincuti.keterangan as keterangan_izin_cuti',
            )
            ->orderBy('tanggal', 'desc')
            ->limit(30)
            ->get();
        $data['rekappresensi'] = Presensi::select(
            DB::raw("SUM(IF(status='h',1,0)) as hadir"),
            DB::raw("SUM(IF(status='i',1,0)) as izin"),
            DB::raw("SUM(IF(status='s',1,0)) as sakit"),
            DB::raw("SUM(IF(status='a',1,0)) as alpa"),
            DB::raw("SUM(IF(status='c',1,0)) as cuti")
        )
            ->groupBy('presensi.nik')
            ->whereRaw('MONTH(presensi.tanggal) = MONTH(?)', [$hari_ini])
            ->whereRaw('YEAR(presensi.tanggal) = YEAR(?)', [$hari_ini])
            ->where('presensi.nik', $userkaryawan->nik)
            ->first();

        $data['lembur'] = Lembur::where('nik', $userkaryawan->nik)->where('status', 1)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();
        $data['notiflembur'] = Lembur::where('nik', $userkaryawan->nik)
            ->where('status', 1)
            ->where('lembur_in', null)
            ->orWhere('lembur_out', null)
            ->where('status', 1)
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
        
        // Count total pelanggaran
        $data['total_pelanggaran'] = DB::table('pelanggaran')
            ->where('nik', $userkaryawan->nik)
            ->count();

        // Cek akses approval delegasi
        $data['hasApprovalAccess'] = false;
        $data['pendingApprovalCount'] = 0;
        if ($userkaryawan->approval_admin_id) {
            $data['hasApprovalAccess'] = true;
            $data['pendingApprovalCount'] = \App\Http\Controllers\KaryawanApprovalController::getPendingCount(auth()->user()->id);
        }
        return view('shortcut.index', $data);
    }

    public function mypinjaman()
    {
        $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
        if (!$userkaryawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $data['karyawan'] = Karyawan::where('nik', $userkaryawan->nik)->first();
        
        // Fetch all active/completed loans
        $data['pinjaman'] = Pinjaman::with(['rencana_cicilan' => function($q) {
                $q->orderBy('tahun', 'asc')->orderBy('bulan', 'asc');
            }, 'pembayaran_pinjaman' => function($q) {
                $q->orderBy('tanggal_bayar', 'desc');
            }])
            ->where('nik', $userkaryawan->nik)
            ->where('status', '!=', 'B') // Exclude Cancelled
            ->orderBy('id', 'desc')
            ->get();

        // Summary calculations
        $data['total_pinjaman'] = $data['pinjaman']->sum('jumlah_pinjaman');
        $data['total_dibayar'] = $data['pinjaman']->sum('total_dibayar');
        $data['sisa_pinjaman'] = $data['pinjaman']->sum('sisa_pinjaman');

        return view('shortcut.mypinjaman', $data);
    }

    public function myschedule(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $nik = auth()->user()->id_user; // Assuming id_user matches NIK based on context

        // Fallback to Userkaryawan logic if id_user is not NIK
        $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
        if ($userkaryawan) {
            $nik = $userkaryawan->nik;
        }

        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $periode_dari = $tahun . '-' . $bulan . '-01';
        $periode_sampai = date('Y-m-t', strtotime($periode_dari));

        // 1) Jadwal by-date per karyawan
        $jadwal_bydate = DB::table('presensi_jamkerja_bydate')
            ->join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi_jamkerja_bydate.tanggal',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.color'
            )
            ->where('presensi_jamkerja_bydate.nik', $nik)
            ->whereBetween('presensi_jamkerja_bydate.tanggal', [$periode_dari, $periode_sampai])
            ->get()
            ->keyBy('tanggal');

        // 2) Jadwal grup by-date
        $jadwal_grup_bydate = DB::table('grup_detail')
            ->join('grup_jamkerja_bydate', 'grup_detail.kode_grup', '=', 'grup_jamkerja_bydate.kode_grup')
            ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'grup_jamkerja_bydate.tanggal',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.color'
            )
            ->where('grup_detail.nik', $nik)
            ->whereBetween('grup_jamkerja_bydate.tanggal', [$periode_dari, $periode_sampai])
            ->get()
            ->keyBy('tanggal');

        // 3) Jadwal by-day per karyawan
        $jadwal_byday = DB::table('presensi_jamkerja_byday')
            ->join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi_jamkerja_byday.hari',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.color'
            )
            ->where('presensi_jamkerja_byday.nik', $nik)
            ->get()
            ->keyBy('hari');

        // 4) Jadwal by-day per departemen & cabang
        $jadwal_bydept = DB::table('presensi_jamkerja_bydept_detail')
            ->join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
            ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi_jamkerja_bydept_detail.hari',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.color'
            )
            ->where('presensi_jamkerja_bydept.kode_dept', $karyawan->kode_dept)
            ->where('presensi_jamkerja_bydept.kode_cabang', $karyawan->kode_cabang)
            ->get()
            ->keyBy('hari');

        $data = [
            'karyawan' => $karyawan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'periode_dari' => $periode_dari,
            'periode_sampai' => $periode_sampai,
            'datalibur' => getdatalibur($periode_dari, $periode_sampai),
        ];

        // Process daily schedule
        $schedule = [];
        $start = Carbon::parse($periode_dari);
        $end = Carbon::parse($periode_sampai);
        
        while ($start <= $end) {
            $date = $start->format('Y-m-d');
            $dayName = getHari($date);
            $info = null;

            // Priority logic
            if (isset($jadwal_bydate[$date])) {
                $info = $jadwal_bydate[$date];
            } elseif (isset($jadwal_grup_bydate[$date])) {
                $info = $jadwal_grup_bydate[$date];
            } elseif (isset($jadwal_byday[$dayName])) {
                $info = $jadwal_byday[$dayName];
            } elseif (isset($jadwal_bydept[$dayName])) {
                $info = $jadwal_bydept[$dayName];
            }

            // Check if holiday
            foreach ($data['datalibur'] as $libur) {
                // Check if this holiday applies to this date AND (applies to all OR matches NIK OR matches Branch)
                if ($date == $libur['tanggal']) {
                    if (empty($libur['nik']) && empty($libur['kode_cabang'])) {
                        // Global holiday
                        $info = (object)[
                            'nama_jam_kerja' => $libur['keterangan'],
                            'jam_masuk' => null,
                            'jam_pulang' => null,
                            'color' => '#ef4444'
                        ];
                        break;
                    } elseif (!empty($libur['nik']) && $libur['nik'] == $nik) {
                        // Personal holiday
                        $info = (object)[
                            'nama_jam_kerja' => $libur['keterangan'],
                            'jam_masuk' => null,
                            'jam_pulang' => null,
                            'color' => '#ef4444'
                        ];
                        break;
                    } elseif (!empty($libur['kode_cabang']) && $libur['kode_cabang'] == $karyawan->kode_cabang) {
                        // Branch holiday
                        $info = (object)[
                            'nama_jam_kerja' => $libur['keterangan'],
                            'jam_masuk' => null,
                            'jam_pulang' => null,
                            'color' => '#ef4444'
                        ];
                        break;
                    }
                }
            }

            $schedule[$date] = $info;
            $start->addDay();
        }

        $data['schedule'] = $schedule;

        return view('shortcut.myschedule', $data);
    }
}
