<?php

namespace App\Http\Controllers;

use App\Models\Approveizincuti;
use App\Models\Cabang;
use App\Models\Cuti;
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
use App\Models\Approval;
use App\Models\ApprovalLayer;
use App\Services\ApprovalService;

class IzincutiController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $qcuti = Izincuti::query();
        $qcuti->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik');
        $qcuti->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $qcuti->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $qcuti->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $qcuti->join('cuti', 'presensi_izincuti.kode_cuti', '=', 'cuti.kode_cuti');
        
        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qcuti->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $qcuti->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qcuti->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $qcuti->whereRaw('1 = 0');
            }
        }
        
        $qcuti->select('presensi_izincuti.*', 'karyawan.nama_karyawan', 'karyawan.nik_show', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang', 'presensi_izincuti.keterangan as nama_cuti');
        if (!empty($request->dari) && !empty($request->sampai)) {
            $qcuti->whereBetween('izincuti.dari', [$request->dari, $request->sampai]);
        }
        if (!empty($request->nama_karyawan)) {
            $qcuti->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }
        if (!empty($request->kode_cabang)) {
            $qcuti->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $qcuti->where('karyawan.kode_dept', $request->kode_dept);
        }
        
        $qcuti->addSelect('karyawan.kode_dept');

        if (!empty($request->status) || $request->status === '0') {
            $qcuti->where('presensi_izincuti.status', $request->status);
        }

        $qcuti->orderBy('presensi_izincuti.status');
        $qcuti->orderBy('presensi_izincuti.dari', 'desc');
        $cuti = $qcuti->paginate(15);
        $cuti->appends($request->all());
        $data['izincuti'] = $cuti;
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        return view('izincuti.index', $data);
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
        $data['jenis_cuti'] = Cuti::orderBy('kode_cuti')->get();
        $data['karyawan'] = $karyawan;

        if ($user->hasRole('karyawan')) {
            return view('izincuti.create-mobile', $data);
        }
        return view('izincuti.create', $data);
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
                'kode_cuti' => 'required',
            ]);
        } else {
            $request->validate([
                'nik' => 'required',
                'dari' => 'required',
                'sampai' => 'required',
                'keterangan' => 'required',
                'kode_cuti' => 'required',
            ]);
        }


        $format = "IC" . date('ym', strtotime($request->dari));
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
            $lastizincuti = Izincuti::select('kode_izin_cuti')
                ->whereRaw('LEFT(kode_izin_cuti,6)="' . $format . '"')
                ->orderBy("kode_izin_cuti", "desc")
                ->first();
            $last_kode_izin_cuti = $lastizincuti != null ? $lastizincuti->kode_izin_cuti : '';
            $kode_izin_cuti  = buatkode($last_kode_izin_cuti, "IC"  . date('ym', strtotime($request->dari)), 4);


            $jmlhari = hitungHari($request->dari, $request->sampai);
            $cuti = Cuti::where('kode_cuti', $request->kode_cuti)->first();
            $jml_hari_max = $cuti->jumlah_hari;

            if ($request->kode_cuti == "C01") {
                $tahun_cuti = date('Y', strtotime($request->dari));
                $cek_cuti_dipakai = Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
                    ->where('presensi.nik', $nik)
                    ->whereRaw("YEAR(presensi.tanggal) = $tahun_cuti")
                    ->count();
                $sisa_cuti = $jml_hari_max - $cek_cuti_dipakai;

                if ($jmlhari > $sisa_cuti) {
                    return Redirect::back()->with(messageError('Sisa Cuti Tahunan Anda Adalah ' . $sisa_cuti . ' Hari. Pengajuan ' . $jmlhari . ' Hari Melebihi Batas!'));
                }
            } else {
                if ($jmlhari > $jml_hari_max) {
                    return Redirect::back()->with(messageError('Maksimal Pengambilan Cuti ' . $cuti->jenis_cuti . ' Adalah ' . $jml_hari_max . ' Hari Per Pengajuan!'));
                }
            }

            $dataizincuti = [
                'kode_izin_cuti' => $kode_izin_cuti,
                'nik' => $nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'kode_cuti' => $request->kode_cuti,
                'keterangan' => $request->keterangan,
                'status' => 0,
                'approval_step' => 1,
                'id_user' => $user->id,
            ];

            Izincuti::create($dataizincuti);
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


    public function edit($kode_izin_cuti)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $karyawanData = Karyawan::where('nik', $izincuti->nik)->first();
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($karyawanData->kode_cabang, $userCabangs) || !in_array($karyawanData->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
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
        $data['izincuti'] = $izincuti;
        $data['jenis_cuti'] = Cuti::orderBy('kode_cuti')->get();
        return view('izincuti.edit', $data);
    }


    public function update(Request $request, $kode_izin_cuti)
    {
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $request->validate([
            'nik' => 'required',
            'dari' => 'required',
            'sampai' => 'required',
            'keterangan' => 'required',
            'kode_cuti' => 'required',
        ]);
        DB::beginTransaction();
        try {
            Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                'nik' => $request->nik,
                'tanggal' => $request->dari,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'keterangan' => $request->keterangan,
                'kode_cuti' => $request->kode_cuti,
            ]);
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function approve($kode_izin_cuti)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
            }
        }

        $data['izincuti'] = $izincuti;
        return view('izincuti.approve', $data);
    }


    public function storeapprove(Request $request, $kode_izin_cuti)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $approvalService = app(ApprovalService::class);
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->select('presensi_izincuti.*', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.kode_jabatan')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Untuk delegasi, gunakan cabang/dept admin
            $accessUser = $user->getApprovalAdmin() ?? $user;
            $userCabangs = $accessUser->getCabangCodes();
            $userDepartemens = $accessUser->getDepartemenCodes();
            
            if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
            }
        }
        $dari = $izincuti->dari;
        $sampai = $izincuti->sampai;
        $nik = $izincuti->nik;
        $kode_dept = $izincuti->kode_dept;
        $kode_jabatan = $izincuti->kode_jabatan;
        $kode_cabang = $izincuti->kode_cabang;
        $currentStep = $izincuti->approval_step;
        $userRole = $user->getRoleNames()->first();
        $approvalUserId = $approvalService->getApprovalUserId($user);
        $approvalAdmin = $approvalUserId != $user->id ? User::find($approvalUserId) : $user;
        $error = '';

        // Check Authorization using Service
        if (!$approvalService->canApprove('IZIN', $currentStep, $userRole, $kode_dept, $kode_jabatan, $user, $kode_cabang)) {
             if (!$user->isSuperAdmin()) {
                 return Redirect::back()->with(messageError('Anda tidak memiliki wewenang untuk approval tahap ke-' . $currentStep));
             }
        }

        DB::beginTransaction();
        try {
            if (isset($request->approve)) {
                 // 1. Record Approval (atas nama admin jika delegasi)
                Approval::create([
                    'approvable_type' => Izincuti::class,
                    'approvable_id' => $kode_izin_cuti,
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
                    Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update(['approval_step' => $nextLevel]);
                     DB::commit();
                    return Redirect::back()->with(messageSuccess('Berhasil disetujui (Tahap ' . $currentStep . '). Menunggu approval tahap selanjutnya.'));
                } else {
                    // Final Approval
                    Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                        'status' => 1
                    ]);

                    // Buat presensi untuk setiap hari dalam rentang cuti
                    $error = $this->buatPresensiIzinCuti($izincuti, $kode_izin_cuti, $dari, $sampai, $nik, $kode_dept, 'c');
                }

            } else {
                 // REJECTION Logic
                Approval::create([
                    'approvable_type' => Izincuti::class,
                    'approvable_id' => $kode_izin_cuti,
                    'user_id' => $approvalUserId,
                    'level' => $currentStep,
                    'status' => 'rejected',
                    'keterangan' => 'Rejected by ' . $approvalAdmin->name,
                ]);

                Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                    'status' => 2
                ]);
            }
            
            // Commit transaction terlebih dahulu
            DB::commit();
            
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

    public function cancelapprove($kode_izin_cuti)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->select('presensi_izincuti.*', 'karyawan.kode_cabang', 'karyawan.kode_dept')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
            }
        }
        
        DB::beginTransaction();
        try {
            // Case 1: Status is Pending (0) but moved steps (Intermediate Cancellation)
             if ($izincuti->status == 0) {
                 // Logic: Find the approval for the *previous* step (current_step - 1)
                 $lastStep = $izincuti->approval_step - 1;
                 
                $lastApproval = Approval::where('approvable_type', Izincuti::class)
                    ->where('approvable_id', $kode_izin_cuti)
                    ->where('level', $lastStep)
                    ->where('user_id', $user->id) // Must be the one who approved it
                    ->first();

                if ($lastApproval) {
                    $lastApproval->delete();
                    Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                        'approval_step' => $lastStep
                    ]);
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Approval dibatalkan. Kembali ke tahap sebelumnya.'));
                } else {
                     return Redirect::back()->with(messageError('Anda tidak dapat membatalkan approval ini (Bukan approver terakhir atau sudah diproses lanjut).'));
                }
            }
            // Case 2: Status is Final Approved (1)
            else if ($izincuti->status == 1) {
                  // Find final approval record (highest level)
                 $lastApproval = Approval::where('approvable_type', Izincuti::class)
                    ->where('approvable_id', $kode_izin_cuti)
                    ->where('user_id', $user->id)
                    ->orderBy('level', 'desc')
                    ->first();

                if($lastApproval){
                     // Revert step to this level (so it becomes pending at this level again)
                     $revertStep = $lastApproval->level;
                     $lastApproval->delete();
                     
                     // Delete Presensi Data & Approveizincuti
                     $presensi = Approveizincuti::where('kode_izin_cuti', $kode_izin_cuti)->get();
                     Presensi::whereIn('id', $presensi->pluck('id_presensi'))->delete();
                     Approveizincuti::where('kode_izin_cuti', $kode_izin_cuti)->delete();

                     Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                        'status' => 0,
                        'approval_step' => $revertStep
                    ]);
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Approval final dibatalkan. Kembali ke tahap sebelumnya.'));

                } else {
                    // Fallback/Legacy
                    $presensi = Approveizincuti::where('kode_izin_cuti', $kode_izin_cuti)->get();
                    Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->update([
                        'status' => 0
                    ]);
                    Approveizincuti::where('kode_izin_cuti', $kode_izin_cuti)->delete();
                    Presensi::whereIn('id', $presensi->pluck('id_presensi'))->delete();
                    DB::commit();
                    return Redirect::back()->with(messageSuccess('Data Berhasil Dibatalkan'));
                }
            }
            return Redirect::back()->with(messageError('Status tidak valid untuk pembatalan.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroy($kode_izin_cuti)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Cek apakah user adalah pemilik izin (untuk karyawan)
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            $isOwner = $userkaryawan && $userkaryawan->nik == $izincuti->nik;
            
            // Jika bukan pemilik, cek akses cabang/dept
            if (!$isOwner) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
                }
            }
        }
        
        try {
            Izincuti::where('kode_izin_cuti', $kode_izin_cuti)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function show($kode_izin_cuti)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $kode_izin_cuti = Crypt::decrypt($kode_izin_cuti);
        $izincuti = Izincuti::where('kode_izin_cuti', $kode_izin_cuti)
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        
        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($izincuti->kode_cabang, $userCabangs) || !in_array($izincuti->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke izin cuti ini.');
            }
        }

        $data['izincuti'] = $izincuti;
        return view('izincuti.show', $data);
    }

    public function getsisaharicuti(Request $request)
    {
        $user = User::findorfail(auth()->user()->id);
        $role = $user->getRoleNames()->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $nik = $user->hasRole('karyawan') ? $userkaryawan->nik : $request->nik;
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $tahun_cuti = date('Y', strtotime($tanggal));
        $kode_cuti = $request->kode_cuti;
        $cuti = Cuti::where('kode_cuti', $kode_cuti)->first();
        $jml_hari_max = $cuti->jumlah_hari;
        if ($cuti->kode_cuti == "C01") {
            $cek_cuti_dipakai = Approveizincuti::join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
                ->where('presensi.nik', $nik)
                ->whereRaw("YEAR(presensi.tanggal) = $tahun_cuti")
                ->count();
            $sisa_cuti = $jml_hari_max - $cek_cuti_dipakai;
            $message = 'Sisa Cuti ' . $cuti->jenis_cuti . ' Anda Adalah ' . $sisa_cuti . ' Hari Lagi';
        } else {
            $sisa_cuti = $jml_hari_max;
            $message = "Batas Maksimal Cuti " . $cuti->jenis_cuti . " Anda Adalah " . $jml_hari_max . " Hari";
        }
        return response()->json(['status' => true, 'sisa_cuti' => $sisa_cuti, 'message' => $message]);
    }

    /**
     * Helper method untuk membuat presensi izin cuti
     * Dipanggil saat final approval tercapai
     */
    private function buatPresensiIzinCuti($izincuti, $kode_izin_cuti, $dari, $sampai, $nik, $kode_dept, $status)
    {
        $error = '';
        
        while (strtotime($dari) <= strtotime($sampai)) {
            $namahari = getnamaHari(date('D', strtotime($dari)));

            $jamkerja = Setjamkerjabydate::join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('nik', $izincuti->nik)
                ->where('tanggal', $dari)
                ->first();
            
            if ($jamkerja == null) {
                $jamkerja = Setjamkerjabyday::join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                    ->where('nik', $izincuti->nik)
                    ->where('hari', $namahari)
                    ->first();
            }

            if ($jamkerja == null) {
                $jamkerja = Detailsetjamkerjabydept::join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
                    ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                    ->where('kode_dept', $kode_dept)
                    ->where('kode_cabang', $izincuti->kode_cabang)
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

                    Approveizincuti::create([
                        'id_presensi' => $presensi->id,
                        'kode_izin_cuti' => $kode_izin_cuti,
                    ]);
                }
            }

            $dari = date('Y-m-d', strtotime($dari . ' +1 day'));
        }
        
        return $error;
    }
}
