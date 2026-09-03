<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MonitoringMasterController extends Controller
{
    /**
     * Show monitoring master page
     */
    public function index()
    {
        if (!Gate::allows('presensi.index')) {
            abort(403);
        }

        $spyEmployees = Karyawan::with('jabatan', 'departemen', 'cabang')
            ->where('spy', 1)
            ->where('status_aktif_karyawan', 1)
            ->orderBy('nama_karyawan')
            ->get();

        return view('datamaster.monitoring-master.index', compact('spyEmployees'));
    }

    /**
     * Search employees not being monitored
     */
    public function search(Request $request)
    {
        if (!Gate::allows('presensi.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $q = $request->input('q', '');
        $q = trim($q);

        if (strlen($q) < 2) {
            return response()->json([
                'results' => [],
                'message' => 'Minimal 2 karakter'
            ]);
        }

        $employees = Karyawan::with('jabatan', 'departemen')
            ->where('status_aktif_karyawan', 1)
            ->where('spy', 0)
            ->where(function ($query) use ($q) {
                $query->where('nama_karyawan', 'like', "%$q%")
                      ->orWhere('nik', 'like', "%$q%");
            })
            ->orderBy('nama_karyawan')
            ->limit(15)
            ->get(['nik', 'nama_karyawan'])
            ->map(function ($e) {
                return [
                    'nik' => $e->nik,
                    'nama' => $e->nama_karyawan,
                    'jabatan' => optional($e->jabatan)->nama_jabatan ?? '-',
                    'dept' => optional($e->departemen)->nama_dept ?? '-'
                ];
            });

        return response()->json(['results' => $employees]);
    }

    /**
     * Add employee to monitoring
     */
    public function add(Request $request)
    {
        if (!Gate::allows('presensi.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $nik = $request->input('nik');
        if (!$nik) {
            return response()->json(['error' => 'NIK required'], 400);
        }

        $emp = Karyawan::where('nik', $nik)
            ->where('status_aktif_karyawan', 1)
            ->first();

        if (!$emp) {
            return response()->json(['error' => 'Karyawan tidak ditemukan'], 404);
        }

        if ($emp->spy == 1) {
            return response()->json(['error' => 'Sudah dalam monitoring'], 400);
        }

        $emp->update(['spy' => 1]);

        return response()->json(['ok' => true]);
    }

    /**
     * Remove employee from monitoring
     */
    public function remove(Request $request)
    {
        if (!Gate::allows('presensi.index')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $nik = $request->input('nik');
        if (!$nik) {
            return response()->json(['error' => 'NIK required'], 400);
        }

        $emp = Karyawan::where('nik', $nik)->first();

        if (!$emp) {
            return response()->json(['error' => 'Karyawan tidak ditemukan'], 404);
        }

        if ($emp->spy == 0) {
            return response()->json(['error' => 'Tidak dalam monitoring'], 400);
        }

        $emp->update(['spy' => 0]);

        return response()->json(['ok' => true]);
    }
}
