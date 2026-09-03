<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Izindinas;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use App\Models\Approval;
use App\Models\ApprovalLayer;
use App\Services\ApprovalService;

class IzindinasController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $qizin = Izindinas::query();
        $qizin->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik');
        $qizin->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $qizin->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $qizin->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');

        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qizin->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $qizin->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qizin->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $qizin->whereRaw('1 = 0');
            }
        }

        $qizin->select('presensi_izindinas.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang', 'karyawan.kode_dept');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $qizin->whereBetween('presensi_izindinas.tanggal', [$request->dari, $request->sampai]);
        }
        if (!empty($request->nama_karyawan)) {
            $qizin->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        if (!empty($request->kode_cabang)) {
            $qizin->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $qizin->where('karyawan.kode_dept', $request->kode_dept);
        }

        if (!empty($request->status) || $request->status === '0') {
            $qizin->where('presensi_izindinas.status', $request->status);
        }
        $qizin->orderBy('presensi_izindinas.status');
        $qizin->orderBy('presensi_izindinas.tanggal', 'desc');
        $izindinas = $qizin->paginate(15);
        $izindinas->appends($request->all());

        $data['izindinas'] = $izindinas;
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        return view('izindinas.index', $data);
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        if ($user->hasRole('karyawan')) {
            return view('izindinas.create-mobile');
        }
        
        $qkaryawan = Karyawan::query();
        $qkaryawan->select('karyawan.nik', 'karyawan.nama_karyawan');
        
        // Filter karyawan berdasarkan akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qkaryawan->whereIn('kode_cabang', $userCabangs);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qkaryawan->whereIn('kode_dept', $userDepartemens);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
        }
        
        $karyawan = $qkaryawan->get();

        $data['karyawan'] = $karyawan;

        return view('izindinas.create', $data);
    }

    public function edit($kode_izin_dinas)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $kode_izin_dinas = Crypt::decrypt($kode_izin_dinas);

        $izindinas = Izindinas::where('kode_izin_dinas', $kode_izin_dinas)
            ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $karyawanData = Karyawan::where('nik', $izindinas->nik)->first();
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($karyawanData->kode_cabang, $userCabangs) || !in_array($karyawanData->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin dinas ini.');
            }
        }
        
        $qkaryawan = Karyawan::query();
        $qkaryawan->select('karyawan.nik', 'karyawan.nama_karyawan');
        
        // Filter karyawan berdasarkan akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qkaryawan->whereIn('kode_cabang', $userCabangs);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qkaryawan->whereIn('kode_dept', $userDepartemens);
            } else {
                $qkaryawan->whereRaw('1 = 0');
            }
        }
        
        $karyawan = $qkaryawan->get();

        $data['karyawan'] = $karyawan;
        $data['izindinas'] = $izindinas;

        return view('izindinas.edit', $data);
    }

    public function store(Request $request)
    {
        $user = User::findorfail(auth()->user()->id);
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $role = $user->getRoleNames()->first();

        $nik = $user->hasRole('karyawan') ? $userkaryawan->nik : $request->nik;

        if ($role == 'karyawan') {
            $request->validate([
                'dari' => 'required',
                'sampai' => 'required',
                'keterangan' => 'required',
            ]);
        } else {
            $request->validate([
                'nik' => 'required',
                'dari' => 'required',
                'sampai' => 'required',
                'keterangan' => 'required',
            ]);
        }

        DB::beginTransaction();
        try {
            $jmlhari = hitungHari($request->dari, $request->sampai);
            if ($jmlhari > 3) {
                return Redirect::back()->with(messageError('Tidak Boleh Lebih dari 3 Hari!'));
            }

            $cek_izin_dinas = Izindinas::where('nik', $nik)
                ->whereBetween('dari', [$request->dari, $request->sampai])
                ->orWhereBetween('sampai', [$request->dari, $request->sampai])->first();

            if ($cek_izin_dinas) {
                return Redirect::back()->with(messageError('Anda Sudah Mengajukan Izin Dinas Pada Rentang Tanggal Tersebut!'));
            }

            $lastizin = Izindinas::select('kode_izin_dinas')
                ->whereRaw('YEAR(dari)="' . date('Y', strtotime($request->dari)) . '"')
                ->whereRaw('MONTH(dari)="' . date('m', strtotime($request->dari)) . '"')
                ->orderBy("kode_izin_dinas", "desc")
                ->first();
            $last_kode_izin = $lastizin != null ? $lastizin->kode_izin_dinas : '';
            $kode_izin_dinas  = buatkode($last_kode_izin, "ID"  . date('ym', strtotime($request->dari)), 4);

            Izindinas::create([
                'kode_izin_dinas' => $kode_izin_dinas,
                'nik' => $nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan,
                'status' => 0,
                'approval_step' => 1,
            ]);
            DB::commit();

            if ($role == 'karyawan') {
                return Redirect::route('pengajuanizin.index')->with(messageSuccess('Data Berhasil Disimpan'));
            } else {
                return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function approve($kode_izin_dinas)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_dinas = Crypt::decrypt($kode_izin_dinas);
        $izindinas = Izindinas::where('kode_izin_dinas', $kode_izin_dinas)
            ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izindinas->kode_cabang, $userCabangs) || !in_array($izindinas->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin dinas ini.');
            }
        }

        $data['izindinas'] = $izindinas;
        return view('izindinas.approve', $data);
    }

    public function storeapprove(Request $request, $kode_izin_dinas)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $approvalService = app(ApprovalService::class);
        
        $kode_izin_dinas = Crypt::decrypt($kode_izin_dinas);
        $izindinas = Izindinas::where('kode_izin_dinas', $kode_izin_dinas)
            ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
            ->select('presensi_izindinas.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Untuk delegasi, gunakan cabang/dept admin
            $accessUser = $user->getApprovalAdmin() ?? $user;
            $userCabangs = $accessUser->getCabangCodes();
            $userDepartemens = $accessUser->getDepartemenCodes();
            
            if (!in_array($izindinas->kode_cabang, $userCabangs) || !in_array($izindinas->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin dinas ini.');
            }
        }
        
        $kode_dept = $izindinas->kode_dept;
        $kode_jabatan = $izindinas->kode_jabatan;
        $kode_cabang = $izindinas->kode_cabang;
        $currentStep = $izindinas->approval_step;
        $userRole = $user->getRoleNames()->first();
        $approvalUserId = $approvalService->getApprovalUserId($user);
        $approvalAdmin = $approvalUserId != $user->id ? User::find($approvalUserId) : $user;

         // Check Authorization using Service
        if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $kode_dept, $kode_jabatan, $user, $kode_cabang)) {
             return Redirect::back()->with(messageError('Anda tidak memiliki wewenang untuk menyetujui tahap ini.'));
        }
        
        DB::beginTransaction();
        try {
            if (isset($request->approve)) {
                 // 1. Record Approval (atas nama admin jika delegasi)
                Approval::create([
                    'approvable_type' => Izindinas::class,
                    'approvable_id' => $kode_izin_dinas,
                    'user_id' => $approvalUserId,
                    'level' => $currentStep,
                    'status' => 'approved',
                    'keterangan' => 'Approved by ' . $approvalAdmin->name,
                ]);

                // 2. Check for Next Level rule
                $nextLevel = $currentStep + 1;
                $nextRule = $approvalService->getLayer('IZIN', $nextLevel, $kode_dept, $kode_jabatan, $kode_cabang);
                
                 if ($nextRule && !$user->hasRole('super admin')) {
                    // Update to next step
                    Izindinas::where('kode_izin_dinas', $kode_izin_dinas)->update(['approval_step' => $nextLevel]);
                } else {
                     // Final Approval
                    Izindinas::where('kode_izin_dinas', $kode_izin_dinas)->update([
                        'status' => 1
                    ]);
                }

            } else {
                 // REJECTION Logic
                Approval::create([
                    'approvable_type' => Izindinas::class,
                    'approvable_id' => $kode_izin_dinas,
                    'user_id' => $approvalUserId,
                    'level' => $currentStep,
                    'status' => 'rejected',
                    'keterangan' => 'Rejected by ' . $approvalAdmin->name,
                ]);

                Izindinas::where('kode_izin_dinas', $kode_izin_dinas)->update([
                    'status' => 2
                ]);
            }
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function cancelapprove($kode_izin_dinas)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_dinas = Crypt::decrypt($kode_izin_dinas);
        $izindinas = Izindinas::where('kode_izin_dinas', $kode_izin_dinas)
            ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
            ->select('presensi_izindinas.*', 'karyawan.kode_dept', 'karyawan.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izindinas->kode_cabang, $userCabangs) || !in_array($izindinas->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin dinas ini.');
            }
        }
        
        DB::beginTransaction();
        try {
             // Case 1: Status is Pending (0) but moved steps (Intermediate Cancellation)
             if ($izindinas->status == 0) {
                 // Logic: Find the approval for the *previous* step (current_step - 1)
                 $lastStep = $izindinas->approval_step - 1;
                 
                 $lastApproval = Approval::where('approvable_type', Izindinas::class)
                    ->where('approvable_id', $kode_izin_dinas)
                    ->where('level', $lastStep)
                    ->where('user_id', $user->id) // Must be the one who approved it
                    ->first();

                if ($lastApproval) {
                    $lastApproval->delete();
                    Izindinas::where('kode_izin_dinas', $kode_izin_dinas)->update([
                        'approval_step' => $lastStep
                    ]);
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Approval dibatalkan. Kembali ke tahap sebelumnya.'));
                } else {
                     return Redirect::back()->with(messageError('Anda tidak dapat membatalkan approval ini (Bukan approver terakhir atau sudah diproses lanjut).'));
                }
             }
            // Case 2: Status is Final Approved (1)
            else if ($izindinas->status == 1) {
                  // Find final approval record (highest level)
                 $lastApproval = Approval::where('approvable_type', Izindinas::class)
                    ->where('approvable_id', $kode_izin_dinas)
                    ->where('user_id', $user->id)
                    ->orderBy('level', 'desc')
                    ->first();
                    
                if($lastApproval){
                     // Revert step to this level (so it becomes pending at this level again)
                     $revertStep = $lastApproval->level;
                     $lastApproval->delete();
                     
                     Izindinas::where('kode_izin_dinas', $kode_izin_dinas)->update([
                        'status' => 0,
                        'approval_step' => $revertStep
                    ]);
                    DB::commit();
                     return Redirect::back()->with(messageSuccess('Approval final dibatalkan. Kembali ke tahap sebelumnya.'));
                } else {
                    // Fallback
                    Izindinas::where('kode_izin_dinas', $kode_izin_dinas)->update([
                        'status' => 0
                    ]);
                     DB::commit();
                    return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
                }
            }
             return Redirect::back()->with(messageError('Status tidak valid untuk pembatalan.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroy($kode_izin_dinas)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_dinas = Crypt::decrypt($kode_izin_dinas);
        $izindinas = Izindinas::where('kode_izin_dinas', $kode_izin_dinas)
            ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Cek apakah user adalah pemilik izin (untuk karyawan)
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $isOwner = $userkaryawan && $userkaryawan->nik == $izindinas->nik;
            
            // Jika bukan pemilik, cek akses cabang/dept
            if (!$isOwner) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($izindinas->kode_cabang, $userCabangs) || !in_array($izindinas->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke izin dinas ini.');
                }
            }
        }
        
        try {
            Izindinas::where('kode_izin_dinas', $kode_izin_dinas)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function update(Request $request, $kode_izin_dinas)
    {
        $kode_izin_dinas = Crypt::decrypt($kode_izin_dinas);
        $request->validate([
            'nik' => 'required',
            'dari' => 'required',
            'sampai' => 'required',
            'keterangan' => 'required',
        ]);
        DB::beginTransaction();
        try {
            Izindinas::where('kode_izin_dinas', $kode_izin_dinas)->update([
                'nik' => $request->nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan
            ]);
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function show($kode_izin_dinas)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_dinas = Crypt::decrypt($kode_izin_dinas);
        $izindinas = Izindinas::where('kode_izin_dinas', $kode_izin_dinas)
            ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izindinas->kode_cabang, $userCabangs) || !in_array($izindinas->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin dinas ini.');
            }
        }

        $data['izindinas'] = $izindinas;
        return view('izindinas.show', $data);
    }
}
