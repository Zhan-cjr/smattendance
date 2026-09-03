<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pengaturanumum;

class PelanggaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        $qpelanggaran = Pelanggaran::query();
        $qpelanggaran->join('karyawan', 'pelanggaran.nik', '=', 'karyawan.nik');
        $qpelanggaran->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $qpelanggaran->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $qpelanggaran->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');

        // Filter berdasarkan akses
        if ($user->hasRole('karyawan')) {
            // Jika role karyawan, hanya tampilkan data dia sendiri
            if ($userkaryawan) {
                $qpelanggaran->where('pelanggaran.nik', $userkaryawan->nik);
            } else {
                 // Fallback if no linked karyawan found but has role
                 $qpelanggaran->whereRaw('1 = 0');
            }
        } elseif (!$user->isSuperAdmin()) {
            // Logic existing untuk admin cabang/dept
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $qpelanggaran->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                $qpelanggaran->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $qpelanggaran->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                $qpelanggaran->whereRaw('1 = 0');
            }
        }

        // Filter by date range
        if (!empty($request->dari) && !empty($request->sampai)) {
            $qpelanggaran->whereBetween('pelanggaran.tanggal', [$request->dari, $request->sampai]);
        }

        // Filter by NIK (Admin only usually, but keep for consistency)
        if (!empty($request->nik)) {
            $qpelanggaran->where('pelanggaran.nik', $request->nik);
        }

        // Filter by nama karyawan
        if (!empty($request->nama_karyawan)) {
            $qpelanggaran->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        $qpelanggaran->select(
            'pelanggaran.*',
            'karyawan.nama_karyawan',
            'karyawan.nik_show',
            'jabatan.nama_jabatan',
            'departemen.nama_dept',
            'cabang.nama_cabang'
        );

        $pelanggaran = $qpelanggaran->orderBy('pelanggaran.tanggal', 'desc')->paginate(10);

        // Get karyawans for filter (Only needed for admin view)
        $karyawans = [];
        if (!$user->hasRole('karyawan')) {
            $karyawans = $this->getKaryawansByAccess($user);
        }

        // Return view mobile jika role karyawan
        if ($user->hasRole('karyawan')) {
            return view('pelanggaran.index_mobile', compact('pelanggaran'));
        }

        return view('pelanggaran.index', compact('pelanggaran', 'karyawans'));
    }

    /**
     * Get karyawans based on user access
     */
    private function getKaryawansByAccess($user)
    {
        $query = Karyawan::query();
        
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!empty($userCabangs)) {
                $query->whereIn('kode_cabang', $userCabangs);
            } else {
                $query->whereRaw('1 = 0');
            }
            
            if (!empty($userDepartemens)) {
                $query->whereIn('kode_dept', $userDepartemens);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        
        return $query->orderBy('nama_karyawan')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $karyawans = $this->getKaryawansByAccess($user);
        
        return view('pelanggaran.create', compact('karyawans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'tanggal' => 'required|date',
            'dari' => 'required|date',
            'sampai' => 'required|date',
            'jenis_sp' => 'required|string|max:3',
            'keterangan' => 'required|string|max:255',
            'no_dokumen' => 'nullable|string|max:255'
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Validasi akses untuk admin jika bukan super admin
        if (!$user->isSuperAdmin() && $request->filled('nik')) {
            $karyawan = Karyawan::where('nik', $request->nik)->first();
            if ($karyawan) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($karyawan->kode_cabang, $userCabangs)) {
                    return redirect()->back()
                        ->withErrors(['nik' => 'Anda tidak memiliki akses ke karyawan dari cabang ini.'])
                        ->withInput();
                }
                
                if (!in_array($karyawan->kode_dept, $userDepartemens)) {
                    return redirect()->back()
                        ->withErrors(['nik' => 'Anda tidak memiliki akses ke karyawan dari departemen ini.'])
                        ->withInput();
                }
            }
        }

        DB::beginTransaction();
        try {
            // Generate no_sp dengan format SP + Tahun 2 digit + Nomor Urut 3 digit (contoh: SP25001)
            $tahun = date('y'); // 2 digit tahun (contoh: 25 untuk 2025)
            $prefix = 'SP' . $tahun; // SP25
            
            // Cari nomor urut terakhir untuk tahun ini
            $lastPelanggaran = Pelanggaran::where('no_sp', 'like', $prefix . '%')
                ->orderBy('no_sp', 'desc')
                ->first();
            
            if ($lastPelanggaran) {
                // Ambil 3 digit terakhir dari no_sp terakhir
                $lastNumber = intval(substr($lastPelanggaran->no_sp, -3));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            // Format nomor dengan 3 digit (001, 002, dst)
            $no_sp = $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

            Pelanggaran::create([
                'no_sp' => $no_sp,
                'nik' => $request->nik,
                'tanggal' => $request->tanggal,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'jenis_sp' => $request->jenis_sp,
                'keterangan' => $request->keterangan,
                'no_dokumen' => $request->no_dokumen
            ]);

            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($no_sp)
    {
        $no_sp = Crypt::decrypt($no_sp);
        $pelanggaran = Pelanggaran::where('no_sp', $no_sp)
            ->join('karyawan', 'pelanggaran.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select(
                'pelanggaran.*',
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.alamat',
                'jabatan.nama_jabatan',
                'departemen.nama_dept',
                'cabang.nama_cabang'
            )
            ->first();

        if (!$pelanggaran) {
            return Redirect::route('pelanggaran.index')->with(messageError('Data tidak ditemukan'));
        }

        $pengaturan = Pengaturanumum::first();

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user->hasRole('karyawan')) {
            return view('pelanggaran.show_mobile', compact('pelanggaran', 'pengaturan'));
        }

        return view('pelanggaran.show', compact('pelanggaran', 'pengaturan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($no_sp)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $no_sp = Crypt::decrypt($no_sp);
        $pelanggaran = Pelanggaran::where('no_sp', $no_sp)
            ->join('karyawan', 'pelanggaran.nik', '=', 'karyawan.nik')
            ->first();

        if (!$pelanggaran) {
            return Redirect::route('pelanggaran.index')->with(messageError('Data tidak ditemukan'));
        }

        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($pelanggaran->kode_cabang, $userCabangs) || !in_array($pelanggaran->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke pelanggaran ini.');
            }
        }

        $karyawans = $this->getKaryawansByAccess($user);
        
        return view('pelanggaran.edit', compact('pelanggaran', 'karyawans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $no_sp)
    {
        $request->validate([
            'nik' => 'required|exists:karyawan,nik',
            'tanggal' => 'required|date',
            'dari' => 'required|date',
            'sampai' => 'required|date',
            'jenis_sp' => 'required|string|max:3',
            'keterangan' => 'required|string|max:255',
            'no_dokumen' => 'nullable|string|max:255'
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $no_sp = Crypt::decrypt($no_sp);
        $pelanggaran = Pelanggaran::where('no_sp', $no_sp)
            ->join('karyawan', 'pelanggaran.nik', '=', 'karyawan.nik')
            ->first();

        if (!$pelanggaran) {
            return Redirect::route('pelanggaran.index')->with(messageError('Data tidak ditemukan'));
        }

        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($pelanggaran->kode_cabang, $userCabangs) || !in_array($pelanggaran->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke pelanggaran ini.');
            }
        }

        // Validasi jika NIK berubah
        if ($request->nik !== $pelanggaran->nik) {
            $newKaryawan = Karyawan::where('nik', $request->nik)->first();
            if ($newKaryawan && !$user->isSuperAdmin()) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($newKaryawan->kode_cabang, $userCabangs)) {
                    return redirect()->back()
                        ->withErrors(['nik' => 'Anda tidak memiliki akses ke karyawan dari cabang ini.'])
                        ->withInput();
                }
                
                if (!in_array($newKaryawan->kode_dept, $userDepartemens)) {
                    return redirect()->back()
                        ->withErrors(['nik' => 'Anda tidak memiliki akses ke karyawan dari departemen ini.'])
                        ->withInput();
                }
            }
        }

        DB::beginTransaction();
        try {
            Pelanggaran::where('no_sp', $no_sp)->update([
                'nik' => $request->nik,
                'tanggal' => $request->tanggal,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'jenis_sp' => $request->jenis_sp,
                'keterangan' => $request->keterangan,
                'no_dokumen' => $request->no_dokumen
            ]);

            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($no_sp)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $no_sp = Crypt::decrypt($no_sp);
        $pelanggaran = Pelanggaran::where('no_sp', $no_sp)
            ->join('karyawan', 'pelanggaran.nik', '=', 'karyawan.nik')
            ->first();

        if (!$pelanggaran) {
            return Redirect::route('pelanggaran.index')->with(messageError('Data tidak ditemukan'));
        }

        // Cek akses jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();
            
            if (!in_array($pelanggaran->kode_cabang, $userCabangs) || !in_array($pelanggaran->kode_dept, $userDepartemens)) {
                abort(403, 'Anda tidak memiliki akses ke pelanggaran ini.');
            }
        }

        try {
            Pelanggaran::where('no_sp', $no_sp)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function print($no_sp)
    {
        $no_sp = Crypt::decrypt($no_sp);
        $pelanggaran = Pelanggaran::where('no_sp', $no_sp)
            ->join('karyawan', 'pelanggaran.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select(
                'pelanggaran.*',
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'jabatan.nama_jabatan',
                'departemen.nama_dept',
                'cabang.nama_cabang'
            )
            ->first();

        if (!$pelanggaran) {
            return Redirect::route('pelanggaran.index')->with(messageError('Data tidak ditemukan'));
        }

        $pengaturan = Pengaturanumum::first();

        $pdf = Pdf::loadView('pelanggaran.print', compact('pelanggaran', 'pengaturan'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('SP_' . $pelanggaran->no_sp . '_' . $pelanggaran->nik . '.pdf');
    }
}
