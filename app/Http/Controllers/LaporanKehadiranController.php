<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\Cabang;
use App\Models\Karyawan;
use Carbon\Carbon;
use App\Exports\KehadiranExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanKehadiranController extends Controller
{
    public function index(Request $request)
    {
        $tanggal_mulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggal_akhir = $request->tanggal_akhir ?? date('Y-m-t');

        $query = \DB::table('presensi as p')
            ->join('karyawan as k', 'p.nik', '=', 'k.nik')
            ->join('cabang as c', 'k.kode_cabang', '=', 'c.kode_cabang')
            ->select(
                'p.nik',
                \DB::raw("DATE_FORMAT(p.jam_in, '%H:%i:%s') as jam_in"),
                \DB::raw("DATE_FORMAT(p.jam_out, '%H:%i:%s') as jam_out"),
                'p.tanggal',
                'p.status',
                'k.nama_karyawan',
                'k.kode_cabang',
                'c.nama_cabang as nama_cabang'
            );

        $query->whereBetween('p.tanggal', [$tanggal_mulai, $tanggal_akhir]);
        
        if ($request->nama_karyawan) {
            $query->where('k.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }
        if ($request->kode_cabang) {
            $query->where('k.kode_cabang', 'like', '%' . $request->kode_cabang . '%');
        }

        $presensis = $query->orderBy('k.kode_cabang')
            ->orderBy('k.nama_karyawan')
            ->orderBy('p.tanggal')
            ->paginate(20);

        return view('laporan.kehadiran', compact('presensis', 'tanggal_mulai', 'tanggal_akhir'));
    }

    public function cetak(Request $request)
    {
        $tanggal_mulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggal_akhir = $request->tanggal_akhir ?? date('Y-m-t');

        $query = \DB::table('presensi as p')
            ->join('karyawan as k', 'p.nik', '=', 'k.nik')
            ->join('cabang as c', 'k.kode_cabang', '=', 'c.kode_cabang')
            ->select(
                'p.nik',
                \DB::raw("DATE_FORMAT(p.jam_in, '%H:%i:%s') as jam_in"),
                \DB::raw("DATE_FORMAT(p.jam_out, '%H:%i:%s') as jam_out"),
                'p.tanggal',
                'p.status',
                'k.nama_karyawan',
                'k.kode_cabang',
                'c.nama_cabang as nama_cabang'
            );

        $query->whereBetween('p.tanggal', [$tanggal_mulai, $tanggal_akhir]);
        
        if ($request->nama_karyawan) {
            $query->where('k.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }
        if ($request->kode_cabang) {
            $query->where('k.kode_cabang', 'like', '%' . $request->kode_cabang . '%');
        }

        $presensis = $query->orderBy('k.kode_cabang')
            ->orderBy('k.nama_karyawan')
            ->orderBy('p.tanggal')
            ->get();

        return view('laporan.kehadiran_cetak', compact('presensis', 'tanggal_mulai', 'tanggal_akhir'));
    }

    public function export(Request $request)
    {
        return Excel::download(new KehadiranExport($request), 'laporan_kehadiran.xlsx');
    }
}
