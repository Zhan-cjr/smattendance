<?php

namespace App\Http\Controllers;

use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izindinas;
use App\Models\Izinsakit;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanizinController extends Controller
{
    public function index()
    {
        $user = User::where('id', auth()->user()->id)->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        $izinabsen = Izinabsen::where('nik', $userkaryawan->nik)
            ->select('kode_izin as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'i\' as ket'), 'status as status_izin');

        $izinsakit = Izinsakit::where('nik', $userkaryawan->nik)
            ->select('kode_izin_sakit as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'s\' as ket'), 'status as status_izin');

        $izincuti = Izincuti::where('nik', $userkaryawan->nik)
            ->select('kode_izin_cuti as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'c\' as ket'), 'status as status_izin');

        $izin_dinas = Izindinas::where('nik', $userkaryawan->nik)
            ->select('kode_izin_dinas as kode', 'tanggal', 'keterangan', 'dari', 'sampai', DB::raw('\'d\' as ket'), 'status as status_izin');

        $pengajuan_izin = $izinabsen->union($izinsakit)->union($izincuti)->union($izin_dinas)->orderBy('tanggal', 'desc')->get();
        $data['pengajuan_izin'] = $pengajuan_izin;
        return view('pengajuanizin.index', $data);
    }

    public function getHitungHariKerja(Request $request)
    {
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $nik = $user->hasRole('karyawan') ? ($userkaryawan ? $userkaryawan->nik : null) : ($request->nik ?? ($userkaryawan ? $userkaryawan->nik : null));
        $dari = $request->dari;
        $sampai = $request->sampai ?? $dari;

        if (empty($nik) || empty($dari) || empty($sampai)) {
            return response()->json([
                'status' => false,
                'total_hari' => 0,
                'message' => 'Parameter tidak lengkap'
            ]);
        }

        $hitung = hitungHariKerja($nik, $dari, $sampai);
        return response()->json([
            'status' => true,
            'total_hari' => $hitung['total_hari'],
            'hari_libur_count' => count($hitung['hari_libur']),
            'hari_off_count' => count($hitung['hari_off']),
            'detail_libur' => $hitung['hari_libur'],
            'detail_off' => $hitung['hari_off'],
        ]);
    }
}
