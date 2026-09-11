<?php

namespace App\Http\Controllers;

use App\Models\Approveizinsakit;
use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Detailsetjamkerjabydept;
use App\Models\Izinabsen;
use App\Models\Izincuti;
use App\Models\Izinsakit;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Setjamkerjabydate;
use App\Models\Setjamkerjabyday;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Services\ApprovalService;
use App\Models\Approval;
use App\Models\ApprovalLayer;

class IzinsakitController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $qizin = Izinsakit::query();
        $qizin->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik');
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

        $qizin->select('presensi_izinsakit.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang', 'karyawan.kode_dept');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $qizin->whereBetween('presensi_izinsakit.tanggal', [$request->dari, $request->sampai]);
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
            $qizin->where('presensi_izinsakit.status', $request->status);
        }

        $qizin->orderBy('presensi_izinsakit.status');
        $qizin->orderBy('presensi_izinsakit.tanggal', 'desc');
        $izinsakit = $qizin->paginate(15);
        $izinsakit->appends($request->all());

        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        $data['izinsakit'] = $izinsakit;
        return view('izinsakit.index', $data);
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
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

        if ($user->hasRole('karyawan')) {
            return view('izinsakit.create-mobile', $data);
        }

        return view('izinsakit.create', $data);
    }

    public function edit($kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $karyawanData = Karyawan::where('nik', $izinsakit->nik)->first();
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($karyawanData->kode_cabang, $userCabangs) || !in_array($karyawanData->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
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
        $data['izinsakit'] = $izinsakit;

        return view('izinsakit.edit', $data);
    }

    public function store(Request $request)
    {
        $user = User::findorfail(auth()->user()->id);
        $role = $user->getRoleNames()->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
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
            $cek_izin_absen = Izinabsen::where('nik', $nik)
                ->whereBetween('dari', [$request->dari, $request->sampai])
                ->orWhereBetween('sampai', [$request->dari, $request->sampai])
                ->where('nik', $nik)
                ->first();

            $cek_izin_sakit = Izinsakit::where('nik', $nik)
                ->whereBetween('dari', [$request->dari, $request->sampai])
                ->orWhereBetween('sampai', [$request->dari, $request->sampai])
                ->where('nik', $nik)
                ->first();

            $cek_izin_cuti = Izincuti::where('nik', $nik)
                ->whereBetween('dari', [$request->dari, $request->sampai])
                ->orWhereBetween('sampai', [$request->dari, $request->sampai])
                ->where('nik', $nik)
                ->first();

            if ($cek_izin_absen) {
                return Redirect::back()->with(messageError('Anda Sudah Mengajukan Izin Absen/Sakit/Cuti Pada Rentang Tanggal Tersebut!'));
            } else if ($cek_izin_sakit) {
                return Redirect::back()->with(messageError('Anda Sudah Mengajukan Izin Absen/Sakit/Cuti Absen Pada Rentang Tanggal Tersebut!'));
            } else if ($cek_izin_cuti) {
                return Redirect::back()->with(messageError('Anda Sudah Mengajukan Izin Absen/Sakit/Cuti Absen Pada Rentang Tanggal Tersebut!'));
            }
            $lastizinsakit = Izinsakit::select('kode_izin_sakit')
                ->whereRaw('YEAR(tanggal)="' . date('Y', strtotime($request->dari)) . '"')
                ->whereRaw('MONTH(tanggal)="' . date('m', strtotime($request->dari)) . '"')
                ->orderBy("kode_izin_sakit", "desc")
                ->first();
            $last_kode_izin_sakit = $lastizinsakit != null ? $lastizinsakit->kode_izin_sakit : '';
            $kode_izin_sakit  = buatkode($last_kode_izin_sakit, "IS"  . date('ym', strtotime($request->dari)), 4);


            $data_sid = [];
            if ($request->hasfile('sid')) {
                $sid_name =  $kode_izin_sakit . "." . $request->file('sid')->getClientOriginalExtension();
                $destination_sid_path = "/public/uploads/sid";
                $sid = $sid_name;
                $data_sid = [
                    'doc_sid' => $sid,
                ];
            }

            $dataizinsakit = [
                'kode_izin_sakit' => $kode_izin_sakit,
                'nik' => $nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan,
                'status' => 0,
                'approval_step' => 1,
                'id_user' => $user->id,
            ];

            $data = array_merge($dataizinsakit, $data_sid);
            $simpandatasakit = Izinsakit::create($data);
            if ($simpandatasakit) {
                if ($request->hasfile('sid')) {
                    $request->file('sid')->storeAs($destination_sid_path, $sid_name);
                }
            }
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function approve($kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinabsen = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }

        $data['izinsakit'] = $izinabsen;
        return view('izinsakit.approve', $data);
    }

    public function storeapprove(Request $request, $kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->select('presensi_izinsakit.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Untuk delegasi, gunakan cabang/dept admin
            $accessUser = $user->getApprovalAdmin() ?? $user;
            $userCabangs = $accessUser->getCabangCodes();
            $userDepartemens = $accessUser->getDepartemenCodes();
            
            if (!in_array($izinsakit->kode_cabang, $userCabangs) || !in_array($izinsakit->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }

        // Dynamic Approval Logic
        $approvalService = app(ApprovalService::class);
        $userRole = $user->getRoleNames()->first();
        $currentStep = $izinsakit->approval_step;
        $approvalUserId = $approvalService->getApprovalUserId($user);
        $approvalAdmin = $approvalUserId != $user->id ? User::find($approvalUserId) : $user;

        // Check Authorization using Service
        $kode_cabang = $izinsakit->kode_cabang;
        if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $izinsakit->kode_dept, $izinsakit->kode_jabatan, $user, $kode_cabang)) {
             if (!$user->isSuperAdmin()) {
                 return Redirect::back()->with(messageError('Anda tidak memiliki wewenang untuk approval tahap ke-' . $currentStep));
             }
        }
        
        $dari = $izinsakit->dari;
        $sampai = $izinsakit->sampai;
        $nik = $izinsakit->nik;
        $kode_dept = $izinsakit->kode_dept;
        $kode_jabatan = $izinsakit->kode_jabatan;
        $error = '';
        DB::beginTransaction();
        try {
            if (isset($request->approve)) {
                
                // 1. Record Approval (atas nama admin jika delegasi)
                Approval::create([
                    'approvable_type' => Izinsakit::class,
                    'approvable_id' => $kode_izin_sakit,
                    'user_id' => $approvalUserId,
                    'level' => $currentStep,
                    'status' => 'approved',
                    'keterangan' => 'Approved by ' . $approvalAdmin->name,
                ]);

                // 2. Check for Next Level rule
                $nextLevel = $currentStep + 1;
                $nextRule = $approvalService->getLayer('IZIN', $nextLevel, $kode_dept, $kode_jabatan, $kode_cabang);
                
                 if ($nextRule && !$user->hasRole('super admin')) {
                    // Move to next step
                     Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                        'approval_step' => $nextLevel
                    ]);
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Berhasil disetujui (Tahap ' . $currentStep . '). Menunggu approval tahap selanjutnya.'));
                }

                // If No Next Rule -> FINAL APPROVAL
                Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                    'status' => 1
                ]);

                // Buat presensi untuk setiap hari dalam rentang izin sakit
                $error = $this->buatPresensiIzinSakit($izinsakit, $kode_izin_sakit, $dari, $sampai, $nik, $kode_dept, 's');
            } else {
                 // REJECTION
                 Approval::create([
                    'approvable_type' => Izinsakit::class,
                    'approvable_id' => $kode_izin_sakit,
                    'user_id' => $approvalUserId,
                    'level' => $currentStep,
                    'status' => 'rejected',
                    'keterangan' => 'Rejected by ' . $approvalAdmin->name,
                ]);

                Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                    'status' => 2
                ]);
            }
            
            // Commit transaction terlebih dahulu
            DB::commit();

            // Send Push Notification to Employee
            try {
                $statusTxt = isset($request->approve) ? 'DISETUJUI' : 'DITOLAK';
                $iconStatus = isset($request->approve) ? '✅' : '❌';
                $dariIndo = DateToIndo($dari);
                $sampaiIndo = DateToIndo($sampai);
                app(\App\Services\WebPushService::class)->sendToNik(
                    $nik,
                    "{$iconStatus} Status Izin Sakit",
                    "Pengajuan Izin Sakit Anda ({$dariIndo} s.d {$sampaiIndo}) telah {$statusTxt}.",
                    url('/izinsakit')
                );
            } catch (\Exception $pushEx) {
                Log::warning('WebPush Izinsakit error: ' . $pushEx->getMessage());
            }
            
            // Return success - dengan warning jika ada jadwal kerja yang belum di-set
            if (!empty($error)) {
                return Redirect::back()->with(messageSuccess('Approval berhasil, namun ada peringatan: <br>' . $error));
            }
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function cancelapprove($kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->select('presensi_izinsakit.*', 'karyawan.kode_cabang', 'karyawan.kode_dept')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinsakit->kode_cabang, $userCabangs) || !in_array($izinsakit->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }
        
        DB::beginTransaction();
        try {
            // Case 1: Status is Pending (0) but moved steps (Intermediate Cancellation)
            if ($izinsakit->status == 0) {
                 // Logic: Find the approval for the *previous* step (current_step - 1)
                 // NOTE: Since approval_step points to the *requirement* (waiting for X), 
                 // the *last done* approval was at level (approval_step - 1).
                 $lastStep = $izinsakit->approval_step - 1;
                 
                $lastApproval = Approval::where('approvable_type', Izinsakit::class)
                    ->where('approvable_id', $kode_izin_sakit)
                    ->where('level', $lastStep)
                    ->where('user_id', $user->id) // Must be the one who approved it
                    ->first();

                if ($lastApproval) {
                    $lastApproval->delete();
                    Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                        'approval_step' => $lastStep
                    ]);
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Approval dibatalkan. Kembali ke tahap sebelumnya.'));
                } else {
                     return Redirect::back()->with(messageError('Anda tidak dapat membatalkan approval ini (Bukan approver terakhir atau sudah diproses lanjut).'));
                }
            } 
            // Case 2: Status is Final Approved (1)
            else if ($izinsakit->status == 1) {
                // This is the "Final Cancellation" - undo the final approval
                // Current code logic deletes presensi and resets status to 0. 
                // We should also delete the Final Approval record.
                
                // Find final approval record (highest level)
                $lastApproval = Approval::where('approvable_type', Izinsakit::class)
                    ->where('approvable_id', $kode_izin_sakit)
                    ->where('user_id', $user->id)
                    ->orderBy('level', 'desc')
                    ->first();

                if($lastApproval){
                     // Revert step to this level (so it becomes pending at this level again)
                     $revertStep = $lastApproval->level;
                     $lastApproval->delete();
                     
                     // Delete Presensi Data
                     $presensi = Approveizinsakit::where('kode_izin_sakit', $kode_izin_sakit)->get();
                     Presensi::whereIn('id', $presensi->pluck('id_presensi'))->delete();
                     Approveizinsakit::where('kode_izin_sakit', $kode_izin_sakit)->delete();

                     Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                        'status' => 0,
                        'approval_step' => $revertStep
                    ]);
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Approval final dibatalkan. Kembali ke tahap sebelumnya.'));

                } else {
                    // Fallback for Super Admin or legacy cleanup 
                    $presensi = Approveizinsakit::where('kode_izin_sakit', $kode_izin_sakit)->get();
                    Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update([
                        'status' => 0
                        // approval_step stays same?? or reset? 
                        // Safer to not touch approval_step if we don't know, but ideally we should knows.
                        // Im assuming legacy manual reset sets status 0.
                    ]);
                    Approveizinsakit::where('kode_izin_sakit', $kode_izin_sakit)->delete();
                    Presensi::whereIn('id', $presensi->pluck('id_presensi'))->delete();
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

    public function update(Request $request, $kode_izin_sakit)
    {
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);

        $request->validate([
            'nik' => 'required',
            'dari' => 'required',
            'sampai' => 'required',
            'keterangan' => 'required',
        ]);
        DB::beginTransaction();
        try {
            $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->first();
            $data_sid = [];
            if ($request->hasfile('sid')) {
                $sid_name =  $kode_izin_sakit . "." . $request->file('sid')->getClientOriginalExtension();
                $destination_sid_path = "/public/uploads/sid";
                $sid = $sid_name;
                $data_sid = [
                    'doc_sid' => $sid,
                ];
            }

            $dataizinsakit = [
                'nik' => $request->nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan,

            ];

            $data = array_merge($dataizinsakit, $data_sid);

            $simpandatasakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->update($data);
            if ($simpandatasakit) {
                if ($request->hasfile('sid')) {
                    Storage::delete($destination_sid_path . "/" . $izinsakit->doc_sid);
                    $request->file('sid')->storeAs($destination_sid_path, $sid_name);
                }
            }
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function destroy($kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinsakit = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Cek apakah user adalah pemilik izin (untuk karyawan)
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $isOwner = $userkaryawan && $userkaryawan->nik == $izinsakit->nik;
            
            // Jika bukan pemilik, cek akses cabang/dept
            if (!$isOwner) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($izinsakit->kode_cabang, $userCabangs) || !in_array($izinsakit->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
                }
            }
        }
        
        try {
            Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function show($kode_izin_sakit)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_sakit = Crypt::decrypt($kode_izin_sakit);
        $izinabsen = Izinsakit::where('kode_izin_sakit', $kode_izin_sakit)
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izinabsen->kode_cabang, $userCabangs) || !in_array($izinabsen->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin sakit ini.');
            }
        }

        $data['izinsakit'] = $izinabsen;
        return view('izinsakit.show', $data);
    }

    /**
     * Helper method untuk membuat presensi izin sakit
     * Dipanggil saat final approval tercapai
     */
    private function buatPresensiIzinSakit($izinsakit, $kode_izin_sakit, $dari, $sampai, $nik, $kode_dept, $status)
    {
        $error = '';
        
        while (strtotime($dari) <= strtotime($sampai)) {
            $namahari = getnamaHari(date('D', strtotime($dari)));

            $jamkerja = Setjamkerjabydate::join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('nik', $izinsakit->nik)
                ->where('tanggal', $dari)
                ->first();
            
            if ($jamkerja == null) {
                $jamkerja = Setjamkerjabyday::join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                    ->where('nik', $izinsakit->nik)
                    ->where('hari', $namahari)
                    ->first();
            }

            if ($jamkerja == null) {
                $jamkerja = Detailsetjamkerjabydept::join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
                    ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                    ->where('kode_dept', $kode_dept)
                    ->where('kode_cabang', $izinsakit->kode_cabang)
                    ->where('hari', $namahari)
                    ->first();
            }

            if ($jamkerja == null) {
                $error .= 'Jam Kerja pada Tanggal ' . $dari . ' Belum Di Set! <br>';
            } else {
                // Cek apakah presensi sudah ada untuk tanggal ini
                $existingPresensi = Presensi::where('nik', $nik)
                    ->where('tanggal', $dari)
                    ->first();
                
                if (!$existingPresensi) {
                    $presensi = Presensi::create([
                        'nik' => $nik,
                        'tanggal' => $dari,
                        'kode_jam_kerja' => $jamkerja->kode_jam_kerja,
                        'status' => $status,
                    ]);

                    Approveizinsakit::create([
                        'id_presensi' => $presensi->id,
                        'kode_izin_sakit' => $kode_izin_sakit,
                    ]);
                }
            }

            $dari = date('Y-m-d', strtotime($dari . ' +1 day'));
        }
        
        return $error;
    }
}
