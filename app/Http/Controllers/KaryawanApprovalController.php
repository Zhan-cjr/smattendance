<?php

namespace App\Http\Controllers;

use App\Models\Izinabsen;
use App\Models\Izinsakit;
use App\Models\Izincuti;
use App\Models\Izindinas;
use App\Models\Userkaryawan;
use App\Models\User;
use App\Models\ApprovalLayer;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class KaryawanApprovalController extends Controller
{
    /**
     * Tampilkan daftar izin pending yang bisa di-approve oleh karyawan (via delegasi admin).
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $approvalService = app(ApprovalService::class);

        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan || !$userkaryawan->approval_admin_id) {
            abort(403, 'Anda tidak memiliki akses approval.');
        }

        $admin = User::find($userkaryawan->approval_admin_id);
        if (!$admin) {
            abort(403, 'Admin approval tidak ditemukan.');
        }

        $adminRole = $admin->getRoleNames()->first();

        // Get admin's cabang & departemen access
        $adminDeptCodes = $admin->getDepartemenCodes();
        $adminCabangCodes = $admin->getCabangCodes();

        // Cari semua ApprovalLayer yang cocok dengan role admin
        $layers = ApprovalLayer::where('role_name', $adminRole)->get();
        $featureLevels = $layers->map(function ($l) {
            return ['feature' => $l->feature, 'level' => $l->level, 'kode_dept' => $l->kode_dept, 'kode_jabatan' => $l->kode_jabatan];
        });

        // Query pending izin yang sesuai
        $pendingIzinAbsen = collect();
        $pendingIzinSakit = collect();
        $pendingIzinCuti = collect();
        $pendingIzinDinas = collect();

        foreach ($featureLevels as $fl) {
            if ($fl['feature'] === 'IZIN') {
                // Izin Absen
                $q = Izinabsen::where('presensi_izinabsen.status', 0)
                    ->where('presensi_izinabsen.approval_step', $fl['level'])
                    ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
                    ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->select('presensi_izinabsen.*', 'karyawan.nama_karyawan', 'karyawan.kode_dept', 'karyawan.kode_jabatan', 'departemen.nama_dept');

                if ($fl['kode_dept']) {
                    $q->where('karyawan.kode_dept', $fl['kode_dept']);
                }
                if ($fl['kode_jabatan']) {
                    $q->where('karyawan.kode_jabatan', $fl['kode_jabatan']);
                }
                // Filter by admin's access rights
                if (!empty($adminDeptCodes)) {
                    $q->whereIn('karyawan.kode_dept', $adminDeptCodes);
                }
                if (!empty($adminCabangCodes)) {
                    $q->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                }
                $pendingIzinAbsen = $pendingIzinAbsen->merge($q->get());

                // Izin Sakit
                $q2 = Izinsakit::where('presensi_izinsakit.status', 0)
                    ->where('presensi_izinsakit.approval_step', $fl['level'])
                    ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
                    ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->select('presensi_izinsakit.*', 'karyawan.nama_karyawan', 'karyawan.kode_dept', 'karyawan.kode_jabatan', 'departemen.nama_dept');

                if ($fl['kode_dept']) {
                    $q2->where('karyawan.kode_dept', $fl['kode_dept']);
                }
                if ($fl['kode_jabatan']) {
                    $q2->where('karyawan.kode_jabatan', $fl['kode_jabatan']);
                }
                if (!empty($adminDeptCodes)) {
                    $q2->whereIn('karyawan.kode_dept', $adminDeptCodes);
                }
                if (!empty($adminCabangCodes)) {
                    $q2->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                }
                $pendingIzinSakit = $pendingIzinSakit->merge($q2->get());

                // Izin Cuti
                $q3 = Izincuti::where('presensi_izincuti.status', 0)
                    ->where('presensi_izincuti.approval_step', $fl['level'])
                    ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
                    ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->select('presensi_izincuti.*', 'karyawan.nama_karyawan', 'karyawan.kode_dept', 'karyawan.kode_jabatan', 'departemen.nama_dept');

                if ($fl['kode_dept']) {
                    $q3->where('karyawan.kode_dept', $fl['kode_dept']);
                }
                if ($fl['kode_jabatan']) {
                    $q3->where('karyawan.kode_jabatan', $fl['kode_jabatan']);
                }
                if (!empty($adminDeptCodes)) {
                    $q3->whereIn('karyawan.kode_dept', $adminDeptCodes);
                }
                if (!empty($adminCabangCodes)) {
                    $q3->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                }
                $pendingIzinCuti = $pendingIzinCuti->merge($q3->get());

                // Izin Dinas
                $q4 = Izindinas::where('presensi_izindinas.status', 0)
                    ->where('presensi_izindinas.approval_step', $fl['level'])
                    ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
                    ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                    ->select('presensi_izindinas.*', 'karyawan.nama_karyawan', 'karyawan.kode_dept', 'karyawan.kode_jabatan', 'departemen.nama_dept');

                if ($fl['kode_dept']) {
                    $q4->where('karyawan.kode_dept', $fl['kode_dept']);
                }
                if ($fl['kode_jabatan']) {
                    $q4->where('karyawan.kode_jabatan', $fl['kode_jabatan']);
                }
                if (!empty($adminDeptCodes)) {
                    $q4->whereIn('karyawan.kode_dept', $adminDeptCodes);
                }
                if (!empty($adminCabangCodes)) {
                    $q4->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                }
                $pendingIzinDinas = $pendingIzinDinas->merge($q4->get());
            }
        }

        // Deduplicate
        $pendingIzinAbsen = $pendingIzinAbsen->unique('kode_izin');
        $pendingIzinSakit = $pendingIzinSakit->unique('kode_izin_sakit');
        $pendingIzinCuti = $pendingIzinCuti->unique('kode_izin_cuti');
        $pendingIzinDinas = $pendingIzinDinas->unique('kode_izin_dinas');

        $data['pendingIzinAbsen'] = $pendingIzinAbsen;
        $data['pendingIzinSakit'] = $pendingIzinSakit;
        $data['pendingIzinCuti'] = $pendingIzinCuti;
        $data['pendingIzinDinas'] = $pendingIzinDinas;
        $data['admin'] = $admin;
        $data['totalPending'] = $pendingIzinAbsen->count() + $pendingIzinSakit->count() + $pendingIzinCuti->count() + $pendingIzinDinas->count();

        return view('karyawanapproval.index', $data);
    }

    /**
     * Hitung total pending approval untuk badge di shortcut.
     */
    public static function getPendingCount($userId)
    {
        $userkaryawan = Userkaryawan::where('id_user', $userId)->first();
        if (!$userkaryawan || !$userkaryawan->approval_admin_id) {
            return 0;
        }

        $admin = User::find($userkaryawan->approval_admin_id);
        if (!$admin) return 0;

        $adminRole = $admin->getRoleNames()->first();
        $layers = ApprovalLayer::where('role_name', $adminRole)->get();

        // Get admin's cabang & departemen access
        $adminDeptCodes = $admin->getDepartemenCodes();
        $adminCabangCodes = $admin->getCabangCodes();

        $count = 0;
        foreach ($layers as $l) {
            if ($l->feature === 'IZIN') {
                $q1 = Izinabsen::where('presensi_izinabsen.status', 0)->where('presensi_izinabsen.approval_step', $l->level)
                    ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik');
                $q2 = Izinsakit::where('presensi_izinsakit.status', 0)->where('presensi_izinsakit.approval_step', $l->level)
                    ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik');
                $q3 = Izincuti::where('presensi_izincuti.status', 0)->where('presensi_izincuti.approval_step', $l->level)
                    ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik');
                $q4 = Izindinas::where('presensi_izindinas.status', 0)->where('presensi_izindinas.approval_step', $l->level)
                    ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik');

                if ($l->kode_dept) {
                    $q1->where('karyawan.kode_dept', $l->kode_dept);
                    $q2->where('karyawan.kode_dept', $l->kode_dept);
                    $q3->where('karyawan.kode_dept', $l->kode_dept);
                    $q4->where('karyawan.kode_dept', $l->kode_dept);
                }

                // Filter by admin's access rights
                if (!empty($adminDeptCodes)) {
                    $q1->whereIn('karyawan.kode_dept', $adminDeptCodes);
                    $q2->whereIn('karyawan.kode_dept', $adminDeptCodes);
                    $q3->whereIn('karyawan.kode_dept', $adminDeptCodes);
                    $q4->whereIn('karyawan.kode_dept', $adminDeptCodes);
                }
                if (!empty($adminCabangCodes)) {
                    $q1->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                    $q2->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                    $q3->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                    $q4->whereIn('karyawan.kode_cabang', $adminCabangCodes);
                }

                $count += $q1->count() + $q2->count() + $q3->count() + $q4->count();
            }
        }

        return $count;
    }

    /**
     * Validasi akses delegasi karyawan. Return admin user atau abort 403.
     */
    private function validateDelegationAccess()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        if (!$userkaryawan || !$userkaryawan->approval_admin_id) {
            abort(403, 'Anda tidak memiliki akses approval delegasi.');
        }
        $admin = User::find($userkaryawan->approval_admin_id);
        if (!$admin) {
            abort(403, 'Admin approval tidak ditemukan.');
        }
        return $admin;
    }

    // ==================== IZIN ABSEN ====================
    public function approveIzinAbsen($kode_izin)
    {
        $admin = $this->validateDelegationAccess();
        $kode_izin = Crypt::decrypt($kode_izin);
        $izinabsen = Izinabsen::where('kode_izin', $kode_izin)
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        // Check admin's access
        $adminCabangs = $admin->getCabangCodes();
        $adminDepts = $admin->getDepartemenCodes();
        if (!in_array($izinabsen->kode_cabang, $adminCabangs) || !in_array($izinabsen->kode_dept, $adminDepts)) {
            abort(403, 'Admin tidak memiliki akses ke izin absen ini.');
        }

        $data['izinabsen'] = $izinabsen;
        return view('karyawanapproval.approve_izinabsen', $data);
    }

    public function storeApproveIzinAbsen(Request $request, $kode_izin)
    {
        $this->validateDelegationAccess();
        app(IzinabsenController::class)->storeapprove($request, $kode_izin, app(ApprovalService::class));
        return redirect()->route('karyawan-approval.index');
    }

    public function cancelApproveIzinAbsen($kode_izin)
    {
        $this->validateDelegationAccess();
        app(IzinabsenController::class)->cancelapprove($kode_izin);
        return redirect()->route('karyawan-approval.index');
    }

    // ==================== IZIN SAKIT ====================
    public function approveIzinSakit($kode_izin_sakit)
    {
        $admin = $this->validateDelegationAccess();
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        $adminCabangs = $admin->getCabangCodes();
        $adminDepts = $admin->getDepartemenCodes();
        if (!in_array($izinsakit->kode_cabang, $adminCabangs) || !in_array($izinsakit->kode_dept, $adminDepts)) {
            abort(403, 'Admin tidak memiliki akses ke izin sakit ini.');
        }

        $data['izinsakit'] = $izinsakit;
        return view('karyawanapproval.approve_izinsakit', $data);
    }

    public function storeApproveIzinSakit(Request $request, $kode_izin_sakit)
    {
        $this->validateDelegationAccess();
        app(IzinsakitController::class)->storeapprove($request, $kode_izin_sakit);
        return redirect()->route('karyawan-approval.index');
    }

    public function cancelApproveIzinSakit($kode_izin_sakit)
    {
        $this->validateDelegationAccess();
        app(IzinsakitController::class)->cancelapprove($kode_izin_sakit);
        return redirect()->route('karyawan-approval.index');
    }

    // ==================== IZIN CUTI ====================
    public function approveIzinCuti($kode_izin_cuti)
    {
        $admin = $this->validateDelegationAccess();
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        $adminCabangs = $admin->getCabangCodes();
        $adminDepts = $admin->getDepartemenCodes();
        if (!in_array($izincuti->kode_cabang, $adminCabangs) || !in_array($izincuti->kode_dept, $adminDepts)) {
            abort(403, 'Admin tidak memiliki akses ke izin cuti ini.');
        }

        $data['izincuti'] = $izincuti;
        return view('karyawanapproval.approve_izincuti', $data);
    }

    public function storeApproveIzinCuti(Request $request, $kode_izin_cuti)
    {
        $this->validateDelegationAccess();
        app(IzincutiController::class)->storeapprove($request, $kode_izin_cuti);
        return redirect()->route('karyawan-approval.index');
    }

    public function cancelApproveIzinCuti($kode_izin_cuti)
    {
        $this->validateDelegationAccess();
        app(IzincutiController::class)->cancelapprove($kode_izin_cuti);
        return redirect()->route('karyawan-approval.index');
    }

    // ==================== IZIN DINAS ====================
    public function approveIzinDinas($kode_izin_dinas)
    {
        $admin = $this->validateDelegationAccess();
        $kode_izin_dinas = Crypt::decrypt($kode_izin_dinas);
        $izindinas = Izindinas::where('kode_izin_dinas', $kode_izin_dinas)
            ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();

        $adminCabangs = $admin->getCabangCodes();
        $adminDepts = $admin->getDepartemenCodes();
        if (!in_array($izindinas->kode_cabang, $adminCabangs) || !in_array($izindinas->kode_dept, $adminDepts)) {
            abort(403, 'Admin tidak memiliki akses ke izin dinas ini.');
        }

        $data['izindinas'] = $izindinas;
        return view('karyawanapproval.approve_izindinas', $data);
    }

    public function storeApproveIzinDinas(Request $request, $kode_izin_dinas)
    {
        $this->validateDelegationAccess();
        app(IzindinasController::class)->storeapprove($request, $kode_izin_dinas);
        return redirect()->route('karyawan-approval.index');
    }

    public function cancelApproveIzinDinas($kode_izin_dinas)
    {
        $this->validateDelegationAccess();
        app(IzindinasController::class)->cancelapprove($kode_izin_dinas);
        return redirect()->route('karyawan-approval.index');
    }
}
