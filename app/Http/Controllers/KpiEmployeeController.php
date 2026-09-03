<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\KpiDetail;
use App\Models\KpiEmployee;
use App\Models\KpiIndicator;
use App\Models\KpiIndicatorDetail;
use App\Models\KpiJabatanIndicator;
use App\Models\KpiPeriod;
use App\Models\Presensi;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KpiEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $role = auth()->user()->getRoleNames()->first();
        // if ($role == 'karyawan') {
        //     $nik = auth()->user()->nik;
        //      return Redirect::route('kpi.transactions.myscore');
        // }

        $active_period = KpiPeriod::where('is_active', 1)->first();
        $period_id = $active_period ? $active_period->id : 0;

        $query = Karyawan::query();
        $query->select('karyawan.*', 'departemen.nama_dept', 'jabatan.nama_jabatan', 'kpi_employees.status as kpi_status', 'kpi_employees.id as kpi_id', 'kpi_employees.total_nilai', 'kpi_employees.grade');
        $query->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $query->leftJoin('kpi_employees', function($join) use ($period_id) {
            $join->on('karyawan.nik', '=', 'kpi_employees.nik')
                 ->where('kpi_employees.kpi_period_id', '=', $period_id);
        });
        $query->orderBy('nama_karyawan');

        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        $user = auth()->user();
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $query->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                // Jika user tidak memiliki akses cabang, mereka tidak boleh melihat data apa pun
                 $query->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $query->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                 // Jika user tidak memiliki akses departemen, mereka tidak boleh melihat data apa pun
                 $query->whereRaw('1 = 0');
            }
        }

        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }

        $karyawan = $query->paginate(10);
        $departemen = Departemen::orderBy('nama_dept')->get();

        return view('kpi.transactions.index', compact('karyawan', 'departemen', 'active_period'));
    }

    /**
     * Show the form for creating a new resource.
     * Often used to Set Target for an employee
     */
    public function create()
    {
        // Tidak digunakan secara langsung, menggunakan settarget/{nik}
    }

    public function settarget($nik)
    {
        $karyawan = Karyawan::where('nik', $nik)->firstOrFail();
        
        // Ambil Periode Aktif
        $period = KpiPeriod::where('is_active', 1)->first();
        if (!$period) {
            return Redirect::back()->with(['warning' => 'Belum ada Periode KPI yang Aktif']);
        }

        // Cek jika target sudah ada untuk periode ini
        $existing_kpi = KpiEmployee::where('nik', $nik)->where('kpi_period_id', $period->id)->first();
        if ($existing_kpi) {
            // Jika ada, alihkan ke Edit/Input Realisasi
             return Redirect::route('kpi.transactions.show', $existing_kpi->id);
        }

        // Ambil Indikator khusus untuk Jabatan DAN Departemen (Header -> Detail)
        $kpi_indicator_header = KpiIndicator::where('kode_jabatan', $karyawan->kode_jabatan)
                                            ->where('kode_dept', $karyawan->kode_dept)
                                            ->first();
        
        $indicators = collect([]);
        if ($kpi_indicator_header) {
            $indicators = KpiIndicatorDetail::where('kpi_indicator_id', $kpi_indicator_header->id)->get();
        }

        // Ambil Indikator Global (Tanpa Jabatan DAN Tanpa Dept)
        $global_indicator_header = KpiIndicator::whereNull('kode_jabatan')
                                                ->whereNull('kode_dept')
                                                ->first();
        
        if ($global_indicator_header) {
            $global_details = KpiIndicatorDetail::where('kpi_indicator_id', $global_indicator_header->id)->get();
            $indicators = $indicators->merge($global_details);
        }

        return view('kpi.transactions.settarget', compact('karyawan', 'period', 'indicators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $nik = $request->nik;
        $kpi_period_id = $request->kpi_period_id;
        $tanggal_penilaian = date('Y-m-d'); 

        DB::beginTransaction();
        try {
            // Buat Header
            $kpi_employee = KpiEmployee::create([
                'nik' => $nik,
                'kpi_period_id' => $kpi_period_id,
                'tanggal_penilaian' => $tanggal_penilaian,
                'status' => 'draft', 
                'total_nilai' => 0
            ]);

            // Validasi jika indikator ada
            if (!$request->has('indicator_id') || empty($request->indicator_id)) {
                 throw new \Exception('Tidak ada indikator KPI yang dipilih.');
            }

            // Buat Detail
            if ($request->has('indicator_id')) {
                foreach ($request->indicator_id as $key => $id_indikator_detail) {
                    $target = $request->target[$key];
                    $bobot = $request->bobot[$key]; 

                    KpiDetail::create([
                        'kpi_employee_id' => $kpi_employee->id,
                        'kpi_indicator_detail_id' => $id_indikator_detail,
                        'target' => $target,
                        'bobot' => $bobot,
                    ]);
                }
            }

            DB::commit();
            return Redirect::route('kpi.transactions.index')->with(['success' => 'Target KPI Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         $kpi_employee = KpiEmployee::with(['details.indicator', 'karyawan', 'period'])->findOrFail($id);

         // Hitung otomatis metrik otomatis
         foreach ($kpi_employee->details as $detail) {
             if ($detail->indicator->mode == 'auto' && !empty($detail->indicator->metric_source)) {
                 $realisasi = $this->calculateAutomatedRealization($kpi_employee, $detail->indicator->metric_source);
                 
                 // Perbarui jika berbeda (untuk menjaga sinkronisasi DB)
                 if ($detail->realisasi != $realisasi) {
                     $detail->update(['realisasi' => $realisasi]);
                     $detail->realisasi = $realisasi; // Perbarui objek untuk tampilan
                 }
                 
                 // Hitung ulang Skor juga untuk jaga-jaga
                 $this->calculateScore($detail, $realisasi);
             }
         }
         
         // Refresh untuk mendapatkan skor terbaru jika ada
         // $kpi_employee->refresh();
         
         return view('kpi.transactions.show', compact('kpi_employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * Digunakan untuk Input Realisasi
     */
    public function update(Request $request, string $id)
    {
         DB::beginTransaction();
         try {
             $kpi_employee = KpiEmployee::findOrFail($id);
             $total_score = 0;

             if ($request->has('detail_id')) {
                 foreach ($request->detail_id as $key => $detail_id) {
                     $detail = KpiDetail::with('indicator')->find($detail_id);
                     $indicator = $detail->indicator;

                     // Tentukan Realisasi
                     if ($indicator->mode == 'auto' && !empty($indicator->metric_source)) {
                         $realisasi = $this->calculateAutomatedRealization($kpi_employee, $indicator->metric_source);
                     } else {
                         $realisasi = $request->realisasi[$key];
                     }
                     
                     // Hitung Skor & Perbarui
                     $score = $this->calculateScore($detail, $realisasi);
                     
                     $total_score += $score;
                 }
             }

             // Tentukan Grade
             $grade = '';
             if ($total_score >= 90) $grade = 'A';
             elseif ($total_score >= 80) $grade = 'B';
             elseif ($total_score >= 70) $grade = 'C';
             elseif ($total_score >= 60) $grade = 'D';
             else $grade = 'E';

             $kpi_employee->update([
                 'total_nilai' => $total_score,
                 'grade' => $grade,
                 'status' => 'submitted' // Atau 'approved' jika manajer melakukannya secara langsung
             ]);

             DB::commit();
             return Redirect::back()->with(['success' => 'Realisasi KPI Berhasil Disimpan']);

         } catch (\Exception $e) {
             DB::rollBack();
             return Redirect::back()->with(['warning' => $e->getMessage()]);
         }
    }
    
    private function calculateScore($detail, $realisasi) {
        $indicator = $detail->indicator;
        $score = 0;
        
        if ($indicator->jenis_target == 'max') {
             if ($detail->target > 0) {
                $score = ($realisasi / $detail->target) * $detail->bobot;
             }
        } else { // min
             // Untuk MIN, penanganan khusus jika realisasi adalah 0 (sempurna) vs target 0
             // Biasanya: (Target / Realisasi) * Bobot. 
             // Jika Realisasi > Target -> Skor berkurang.
             // Jika Realisasi < Target -> Skor meningkat
             // Rumus standar: (Target / Realisasi) * Bobot
             
             if ($realisasi > 0) {
                $score = ($detail->target / $realisasi) * $detail->bobot;
             } else {
                 // Jika realisasi 0 (misalnya terlambat 0), dan target misalnya 5.
                 // Skor harusnya MAX. 
                 // Jika target 0 dan realisasi 0 -> Skor Sempurna (Bobot).
                 $score = $detail->bobot; 
                 // Wait, if target is 5 (allowed 5 late), and actual is 0. 
                 // (5/0) is undefined. 
                 // Logika untuk MIN biasanya: 
                 // Jika Realisasi <= Target, Skor = Bobot (Skor Maks).
                 // Jika Realisasi > Target, Skor = (Target / Realisasi) * Bobot.
                 // Mari kita adopsi logika "Target adalah Ambang Batas" yang umum ini.
                 if ($detail->target > 0) {
                      // Jika rumus sederhana (Target/Realisasi)*Bobot, realisasi 0 akan merusaknya.
                      // Menyesuaikan logika:
                      $score = $detail->bobot; // Sempurna
                 }
             }
             
             // Cek rumus MIN standar lagi. 
             // Seringkali: (2 - (Realisasi/Target)) * Bobot ... tidak.
             // Mari Tetap pada: 
             // Jika Realisasi == 0, Skor = Bobot (atau bahkan lebih tinggi jika logika mengizinkan, tapi mari kita batasi di Bobot).
             // Jika Realisasi > 0:
             // $score = ($detail->target / $realisasi) * $detail->bobot;
             
             // Detail implementasi untuk MIN:
             if ($realisasi == 0) {
                 $score = $detail->bobot;
             } else {
                 $score = ($detail->target / $realisasi) * $detail->bobot;
             }
        }
        
        $detail->update([
             'realisasi' => $realisasi,
             'skor' => $score
         ]);
         
         return $score;
    }

    private function calculateAutomatedRealization($kpi_employee, $metric_source) {
        $nik = $kpi_employee->nik;
        $start = $kpi_employee->period->start_date;
        $end = $kpi_employee->period->end_date;
        
        // Gunakan model Presensi yang kita tahu ada
        // Perlu memastikan kita mengimpor Presensi di bagian atas file (sudah diimpor)
        
        switch ($metric_source) {
            case 'attendance_sakit':
                return Presensi::where('nik', $nik)
                    ->whereBetween('tanggal', [$start, $end])
                    ->where('status', 's')
                    ->count();
            case 'attendance_izin':
                return Presensi::where('nik', $nik)
                    ->whereBetween('tanggal', [$start, $end])
                    ->where('status', 'i')
                    ->count();
            case 'attendance_alpa':
                return Presensi::where('nik', $nik)
                    ->whereBetween('tanggal', [$start, $end])
                    ->where('status', 'a')
                    ->count();
            case 'attendance_cuti':
                return Presensi::where('nik', $nik)
                    ->whereBetween('tanggal', [$start, $end])
                    ->where('status', 'c')
                    ->count();
            case 'attendance_hadir':
                return Presensi::where('nik', $nik)
                    ->whereBetween('tanggal', [$start, $end])
                    ->where('status', 'h')
                    ->count();
            case 'attendance_terlambat':
                $presensi = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                    ->where('nik', $nik)
                    ->whereBetween('tanggal', [$start, $end])
                    ->where('status', 'h')
                    ->select('presensi.*', 'presensi_jamkerja.jam_masuk')
                    ->get();
                
                $total_late_minutes = 0;
                foreach ($presensi as $p) {
                    $jam_masuk = $p->tanggal . ' ' . $p->jam_masuk;
                    // hitungjamterlambat adalah fungsi pembantu
                    $terlambat = hitungjamterlambat($p->jam_in, $jam_masuk);
                    
                    if ($terlambat && isset($terlambat['jamterlambat'])) {
                        $total_late_minutes += ($terlambat['jamterlambat'] * 60) + $terlambat['menitterlambat'];
                    }
                }
                return $total_late_minutes;
            default:
                return 0;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         try {
            KpiEmployee::findOrFail($id)->delete();
            return Redirect::back()->with(['success' => 'Data KPI Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function approve($id)
    {
        try {
            $kpi_employee = KpiEmployee::findOrFail($id);
            $kpi_employee->update(['status' => 'approved']);
            return Redirect::back()->with(['success' => 'KPI Berhasil Disetujui']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function print($id)
    {
        $kpi_employee = KpiEmployee::with(['details', 'karyawan.jabatan', 'karyawan.departemen', 'period'])->findOrFail($id);
        return view('kpi.transactions.print', compact('kpi_employee'));
    }
    public function myScore()
    {
        $id_user = auth()->user()->id;
        $user_karyawan = Userkaryawan::where('id_user', $id_user)->first();
        
        if (!$user_karyawan) {
            return Redirect::back()->with(['warning' => 'Akun Anda tidak terhubung dengan data Karyawan.']);
        }
        
        $nik = $user_karyawan->nik;
        $karyawan = Karyawan::where('nik', $nik)->first();
        
        if (!$karyawan) {
             return Redirect::back()->with(['warning' => 'Data Karyawan tidak ditemukan untuk NIK: ' . $nik]);
        }
        
        // Get Active Period
        $period = KpiPeriod::where('is_active', 1)->first();
        if (!$period) {
            return Redirect::back()->with(['warning' => 'Belum ada Periode KPI yang Aktif']);
        }

        // Check if target already exists for this period
        $kpi_employee = KpiEmployee::with(['details.indicator', 'karyawan', 'period'])
                                    ->where('nik', $nik)
                                    ->where('kpi_period_id', $period->id)
                                    ->first();

        if ($kpi_employee) {
             // Hitung otomatis metrik otomatis (Gunakan kembali logika dari show)
             foreach ($kpi_employee->details as $detail) {
                 if ($detail->indicator->mode == 'auto' && !empty($detail->indicator->metric_source)) {
                     $realisasi = $this->calculateAutomatedRealization($kpi_employee, $detail->indicator->metric_source);
                     
                     // Update if different (to keep DB in sync)
                     if ($detail->realisasi != $realisasi) {
                         $detail->update(['realisasi' => $realisasi]);
                         $detail->realisasi = $realisasi; // Update object for view
                     }
                     
                     // Hitung ulang Skor
                     $this->calculateScore($detail, $realisasi);
                 }
             }
             $kpi_employee->refresh();
             
             return view('kpi.transactions.myscore', compact('kpi_employee', 'karyawan', 'period'));
        } else {
            // Logika untuk Membuat Baru (Ambil Indikator)
            // Get Indicators specific to Jabatan AND Departemen (Header -> Details)
            $kpi_indicator_header = KpiIndicator::where('kode_jabatan', $karyawan->kode_jabatan)
                                                ->where('kode_dept', $karyawan->kode_dept)
                                                ->first();
            
            $indicators = collect([]);
            if ($kpi_indicator_header) {
                $indicators = KpiIndicatorDetail::where('kpi_indicator_id', $kpi_indicator_header->id)->get();
            }

            // Get Global Indicators (No Jabatan AND No Dept)
            $global_indicator_header = KpiIndicator::whereNull('kode_jabatan')
                                                    ->whereNull('kode_dept')
                                                    ->first();
            
            if ($global_indicator_header) {
                $global_details = KpiIndicatorDetail::where('kpi_indicator_id', $global_indicator_header->id)->get();
                $indicators = $indicators->merge($global_details);
            }

            return view('kpi.transactions.myscore', compact('karyawan', 'period', 'indicators', 'kpi_employee'));
        }
    }
}
