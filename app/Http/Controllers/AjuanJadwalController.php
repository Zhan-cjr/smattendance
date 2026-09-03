<?php

namespace App\Http\Controllers;

use App\Models\AjuanJadwal;
use App\Models\Jamkerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Models\Userkaryawan;
use App\Models\Karyawan;
use App\Models\Setjamkerjabydate;
use App\Models\GrupDetail;
use App\Models\GrupJamkerjaBydate;
use App\Models\Setjamkerjabyday;
use App\Models\Detailsetjamkerjabydept;

class AjuanJadwalController extends Controller
{
    public function index(Request $request)
    {
        $auth_user = auth()->user();
        $role = $auth_user->getRoleNames()->first();
        
        $query = AjuanJadwal::query();
        $query->with(['karyawan.jabatan', 'karyawan.departemen', 'karyawan.cabang', 'jamKerjaAwal', 'jamKerjaTujuan']);
        
        // If employee, only show their own requests
        if ($role == 'karyawan') {
            $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
            $nik = $userkaryawan ? $userkaryawan->nik : null;
            $query->where('nik', $nik);
        }

        // Filter by date
        if (!empty($request->dari) && !empty($request->sampai)) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        // Role-based Access Control (RBAC) & Filters
        if ($role != 'karyawan') {
            // Apply Branch/Dept restrictions for non-super admins
            if (!$auth_user->isSuperAdmin()) {
                $userCabangs = $auth_user->getCabangCodes();
                $userDepartemens = $auth_user->getDepartemenCodes();

                if (!empty($userCabangs)) {
                    $query->whereHas('karyawan', function ($q) use ($userCabangs) {
                        $q->whereIn('kode_cabang', $userCabangs);
                    });
                }
                
                if (!empty($userDepartemens)) {
                    $query->whereHas('karyawan', function ($q) use ($userDepartemens) {
                        $q->whereIn('kode_dept', $userDepartemens);
                    });
                }
            }

            // Filter by Name
            if (!empty($request->nama_karyawan)) {
                $query->whereHas('karyawan', function ($q) use ($request) {
                    $q->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
                });
            }

            // Filter by Cabang
            if (!empty($request->kode_cabang)) {
                $query->whereHas('karyawan', function ($q) use ($request) {
                    $q->where('kode_cabang', $request->kode_cabang);
                });
            }

            // Filter by Dept
            if (!empty($request->kode_dept)) {
                $query->whereHas('karyawan', function ($q) use ($request) {
                    $q->where('kode_dept', $request->kode_dept);
                });
            }
            
            // Filter by Status
            if (!empty($request->status)) {
                $query->where('status', $request->status);
            }
        } else {
             // Karyawan filters (usually just date and maybe status)
             if (!empty($request->status)) {
                $query->where('status', $request->status);
            }
        }
        
        // Order by latest
        $query->orderBy('created_at', 'desc');

        $ajuanjadwal = $query->paginate(10);
        $ajuanjadwal->appends($request->all());

        $cabang = auth()->user()->getCabang();
        $departemen = auth()->user()->getDepartemen();

        if ($role == 'karyawan') {
            $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
            $nik = $userkaryawan ? $userkaryawan->nik : null;

            if ($nik) {
                $karyawan = Karyawan::where('nik', $nik)->with(['jabatan', 'departemen', 'cabang'])->first();
                return view('ajuanjadwal.index_mobile', compact('ajuanjadwal', 'karyawan'));
            }
             // Fallback if no NIK found (shouldn't happen for valid employee user)
             return redirect()->back()->with('warning', 'Data Karyawan tidak ditemukan.');
        }
        return view('ajuanjadwal.index', compact('ajuanjadwal', 'cabang', 'departemen'));
    }

    public function create()
    {
        $jamkerja = Jamkerja::orderBy('nama_jam_kerja')->get();
        $user = auth()->user();
        $karyawan = [];
        
        if (!$user->hasRole('karyawan')) {
             $karyawan = Karyawan::orderBy('nama_karyawan')->get();
        }
        
        return view('ajuanjadwal.create', compact('jamkerja', 'karyawan'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first();
        
        if ($role == 'karyawan') {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $nik = $userkaryawan ? $userkaryawan->nik : null;
            
            $request->validate([
                'tanggal' => 'required|date',
                'kode_jam_kerja_tujuan' => 'required',
                'keterangan' => 'required'
            ]);
        } else {
            $nik = $request->nik;
            $request->validate([
                'nik' => 'required',
                'tanggal' => 'required|date',
                'kode_jam_kerja_tujuan' => 'required',
                'keterangan' => 'required'
            ]);
        }

        if (empty($nik)) {
             return Redirect::back()->with(['warning' => 'NIK tidak ditemukan. Hubungi IT.']);
        }

        // Get Employee Data
    $karyawan = Karyawan::where('nik', $nik)->first();
    $kode_cabang = $karyawan->kode_cabang;
    $kode_dept = $karyawan->kode_dept;
    $tanggal = $request->tanggal;
    $namahari = getnamaHari(date('D', strtotime($tanggal)));

    // Calculate Original Schedule (kode_jam_kerja_awal)
    $kode_jam_kerja_awal = null;

    // 1. Cek Jam Kerja By Date
    $jamkerja_by_date = Setjamkerjabydate::where('nik', $nik)->where('tanggal', $tanggal)->first();
    if ($jamkerja_by_date) {
        $kode_jam_kerja_awal = $jamkerja_by_date->kode_jam_kerja;
    }

    // 2. Jika tidak ada, Cek Jam Kerja Group
    if ($kode_jam_kerja_awal == null) {
        $cek_group = GrupDetail::where('nik', $nik)->first();
        if ($cek_group) {
            $jamkerja_group = GrupJamkerjaBydate::where('kode_grup', $cek_group->kode_grup)
                ->where('tanggal', $tanggal)
                ->first();
            if ($jamkerja_group) {
                $kode_jam_kerja_awal = $jamkerja_group->kode_jam_kerja;
            }
        }
    }

    // 3. Jika tidak ada, Cek Jam Kerja Harian (Per Orang)
    if ($kode_jam_kerja_awal == null) {
        $jamkerja_harian = Setjamkerjabyday::where('nik', $nik)->where('hari', $namahari)->first();
        if ($jamkerja_harian) {
             $kode_jam_kerja_awal = $jamkerja_harian->kode_jam_kerja;
        }
    }

    // 4. Jika tidak ada, Cek Jam Kerja Departemen (Default)
    if ($kode_jam_kerja_awal == null) {
        $jamkerja_dept = DB::table('presensi_jamkerja_bydept_detail as detail')
            ->join('presensi_jamkerja_bydept as parent', 'detail.kode_jk_dept', '=', 'parent.kode_jk_dept')
            ->where('parent.kode_dept', $kode_dept)
            ->where('parent.kode_cabang', $kode_cabang)
            ->where('detail.hari', $namahari)
            ->select('detail.kode_jam_kerja')
            ->first();
        if ($jamkerja_dept) {
            $kode_jam_kerja_awal = $jamkerja_dept->kode_jam_kerja;
        }
    }
    
    try {
        AjuanJadwal::create([
            'nik' => $nik,
            'tanggal' => $request->tanggal,
            'kode_jam_kerja_awal' => $kode_jam_kerja_awal,
            'kode_jam_kerja_tujuan' => $request->kode_jam_kerja_tujuan,
            'keterangan' => $request->keterangan,
            'status' => 'p'
        ]);

        return Redirect::route('ajuanjadwal.index')->with(['success' => 'Pengajuan Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function approve($id)
    {
        try {
            $ajuan = AjuanJadwal::findOrFail($id);
            $ajuan->update(['status' => 'a']); // Approved
            return Redirect::back()->with(['success' => 'Pengajuan Disetujui']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function cancelapprove($id)
    {
        try {
            $ajuan = AjuanJadwal::findOrFail($id);
            $ajuan->update(['status' => 'p']); // Revert to Pending
            return Redirect::back()->with(['success' => 'Approval Dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function reject($id)
    {
        try {
            $ajuan = AjuanJadwal::findOrFail($id);
            $ajuan->update(['status' => 'r']); // Rejected
            return Redirect::back()->with(['success' => 'Pengajuan Ditolak']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $id = \Illuminate\Support\Facades\Crypt::decrypt($id);
            $ajuan = AjuanJadwal::findOrFail($id);

            if ($ajuan->status != 'p') {
                return Redirect::back()->with(['warning' => 'Hanya pengajuan dengan status Pending yang dapat dihapus.']);
            }

            $ajuan->delete();
            return Redirect::back()->with(['success' => 'Pengajuan berhasil dibatalkan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
}
