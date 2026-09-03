<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class KunjunganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = User::where('id', auth()->user()->id)->first();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();
        $query = Kunjungan::join('karyawan', 'kunjungan.nik', '=', 'karyawan.nik')
            ->select('kunjungan.*', 'karyawan.nama_karyawan');

        // If user is karyawan role, only show their own visits
        if ($user->hasRole('karyawan')) {
            $query->where('kunjungan.nik', $user_karyawan->nik);
        } else {
            // Filter berdasarkan akses cabang dan departemen jika bukan super admin
            if (!$user->isSuperAdmin()) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!empty($userCabangs)) {
                    $query->whereIn('karyawan.kode_cabang', $userCabangs);
                } else {
                    $query->whereRaw('1 = 0');
                }
                
                if (!empty($userDepartemens)) {
                    $query->whereIn('karyawan.kode_dept', $userDepartemens);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            
            // Filter by NIK if provided (for admin)
            if ($request->filled('nik')) {
                $query->where('kunjungan.nik', $request->nik);
            }
        }

        // Filter by date range if provided
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('kunjungan.tanggal_kunjungan', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('kunjungan.tanggal_kunjungan', '<=', $request->tanggal_akhir);
        }

        $kunjungan = $query->orderBy('kunjungan.tanggal_kunjungan', 'desc')->paginate(10);
        
        // Get karyawans based on access
        if ($user->hasRole('karyawan')) {
            $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        } else {
            $karyawans = $this->getKaryawansByAccess($user);
        }

        // If user is karyawan role, use mobile view
        if ($user->hasRole('karyawan')) {
            return view('kunjungan.index-mobile', compact('kunjungan', 'karyawans'));
        }

        return view('kunjungan.index', compact('kunjungan', 'karyawans'));
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
        $user = User::where('id', auth()->user()->id)->first();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();

        // If user is karyawan role, only show their own data
        if ($user->hasRole('karyawan')) {
            $karyawan = Karyawan::where('nik', $user_karyawan->nik)->first();
            return view('kunjungan.create-mobile', compact('karyawan'));
        } else {
            $karyawans = $this->getKaryawansByAccess($user);
            return view('kunjungan.create', compact('karyawans'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = User::where('id', auth()->user()->id)->first();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();

        // If user is karyawan role, force their own NIK
        if ($user->hasRole('karyawan')) {
            $request->merge(['nik' => $user_karyawan->nik]);
        } else {
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
        }

        $validator = Validator::make($request->all(), [
            'nik' => 'required|exists:karyawan,nik',
            'deskripsi' => 'nullable|string|max:1000',
            'foto' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255',
            'tanggal_kunjungan' => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['nik',  'deskripsi', 'lokasi', 'tanggal_kunjungan']);

        // Handle foto upload (base64 or file)
        if ($request->filled('foto')) {
            $foto = $request->foto;
            if (strpos($foto, 'data:image') === 0) {
                // Handle base64 image
                $image_parts = explode(";base64,", $foto);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = 'kunjungan_' . time() . '_' . uniqid() . '.' . $image_type;
                $filepath = 'uploads/kunjungan/' . $filename;
                
                $destinationPath = 'uploads/kunjungan/';
                if (!Storage::disk('public')->exists($destinationPath)) {
                    Storage::disk('public')->makeDirectory($destinationPath, 0775, true);
                    $path = Storage::disk('public')->path($destinationPath);
                    chmod($path, 0775);
                }

                Storage::disk('public')->put($filepath, $image_base64);
                $data['foto'] = $filename;
            } else {
                $data['foto'] = $foto;
            }
        }

        Kunjungan::create($data);

        return redirect()->route('kunjungan.index')
            ->with('success', 'Data kunjungan berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kunjungan $kunjungan)
    {
        $kunjungan->load('karyawan');
        return view('kunjungan.show', compact('kunjungan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kunjungan $kunjungan)
    {
        /** @var \App\Models\User $user */
        $user = User::where('id', auth()->user()->id)->first();
        
        // Validasi akses untuk admin jika bukan super admin
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $karyawan = Karyawan::where('nik', $kunjungan->nik)->first();
            if ($karyawan) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($karyawan->kode_cabang, $userCabangs) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke kunjungan karyawan ini.');
                }
            }
        }
        
        $karyawans = $this->getKaryawansByAccess($user);
        return view('kunjungan.edit', compact('kunjungan', 'karyawans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kunjungan $kunjungan)
    {
        /** @var \App\Models\User $user */
        $user = User::where('id', auth()->user()->id)->first();
        
        // Validasi akses untuk admin jika bukan super admin
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $karyawan = Karyawan::where('nik', $kunjungan->nik)->first();
            if ($karyawan) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($karyawan->kode_cabang, $userCabangs) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke kunjungan karyawan ini.');
                }
            }
            
            // Validasi jika NIK berubah
            if ($request->filled('nik') && $request->nik !== $kunjungan->nik) {
                $newKaryawan = Karyawan::where('nik', $request->nik)->first();
                if ($newKaryawan) {
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
        }
        
        $validator = Validator::make($request->all(), [
            'nik' => 'required|exists:karyawan,nik',
            'deskripsi' => 'nullable|string|max:1000',
            'foto' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'lokasi' => 'nullable|string|max:255',
            'tanggal_kunjungan' => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['nik',  'deskripsi', 'lokasi', 'tanggal_kunjungan']);

        // Handle foto upload (file or base64)
        if ($request->hasFile('foto')) {
            // Handle file upload
            $file = $request->file('foto');
            $filename = 'kunjungan_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filepath = 'uploads/kunjungan/' . $filename;

            $destinationPath = 'uploads/kunjungan/';
            if (!Storage::disk('public')->exists($destinationPath)) {
                Storage::disk('public')->makeDirectory($destinationPath, 0775, true);
                $path = Storage::disk('public')->path($destinationPath);
                chmod($path, 0775);
            }

            Storage::disk('public')->put($filepath, file_get_contents($file));
            $data['foto'] = $filename;
        } elseif ($request->filled('foto')) {
            // Handle base64 image (from mobile/camera)
            $foto = $request->foto;
            if (strpos($foto, 'data:image') === 0) {
                $image_parts = explode(";base64,", $foto);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $filename = 'kunjungan_' . time() . '_' . uniqid() . '.' . $image_type;
                $filepath = 'uploads/kunjungan/' . $filename;

                $destinationPath = 'uploads/kunjungan/';
                if (!Storage::disk('public')->exists($destinationPath)) {
                    Storage::disk('public')->makeDirectory($destinationPath, 0775, true);
                    $path = Storage::disk('public')->path($destinationPath);
                    chmod($path, 0775);
                }

                Storage::disk('public')->put($filepath, $image_base64);
                $data['foto'] = $filename;
            } else {
                $data['foto'] = $foto;
            }
        }

        $kunjungan->update($data);

        return redirect()->route('kunjungan.index')
            ->with('success', 'Data kunjungan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kunjungan $kunjungan)
    {
        /** @var \App\Models\User $user */
        $user = User::where('id', auth()->user()->id)->first();
        
        // Validasi akses untuk admin jika bukan super admin
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $karyawan = Karyawan::where('nik', $kunjungan->nik)->first();
            if ($karyawan) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!in_array($karyawan->kode_cabang, $userCabangs) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke kunjungan karyawan ini.');
                }
            }
        }
        
        // Delete foto if exists
        if ($kunjungan->foto && Storage::disk('public')->exists('uploads/kunjungan/' . $kunjungan->foto)) {
            Storage::disk('public')->delete('uploads/kunjungan/' . $kunjungan->foto);
        }

        $kunjungan->delete();

        return redirect()->route('kunjungan.index')
            ->with('success', 'Data kunjungan berhasil dihapus.');
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();

        // Validate that NIK is required for export
        if (!$request->filled('nik') && !$user->hasRole('karyawan')) {
            return redirect()->route('kunjungan.index')
                ->with('error', 'Silakan pilih karyawan terlebih dahulu untuk export PDF.');
        }

        // Validate user_karyawan for karyawan role
        if ($user->hasRole('karyawan') && !$user_karyawan) {
            return redirect()->route('kunjungan.index')
                ->with('error', 'Data karyawan tidak ditemukan.');
        }

        $query = Kunjungan::join('karyawan', 'kunjungan.nik', '=', 'karyawan.nik')
            ->select('kunjungan.*', 'karyawan.nama_karyawan', 'cabang.nama_cabang', 'departemen.nama_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');

        // If user is karyawan role, only show their own visits
        if ($user->hasRole('karyawan')) {
            $query->where('kunjungan.nik', $user_karyawan->nik);
        } else {
            // Filter berdasarkan akses cabang dan departemen jika bukan super admin
            if (!$user->isSuperAdmin()) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();
                
                if (!empty($userCabangs)) {
                    $query->whereIn('karyawan.kode_cabang', $userCabangs);
                } else {
                    $query->whereRaw('1 = 0');
                }
                
                if (!empty($userDepartemens)) {
                    $query->whereIn('karyawan.kode_dept', $userDepartemens);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            
            // Filter by NIK (required for admin)
            $query->where('kunjungan.nik', $request->nik);
        }

        // Filter by date range if provided
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('kunjungan.tanggal_kunjungan', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('kunjungan.tanggal_kunjungan', '<=', $request->tanggal_akhir);
        }

        $kunjungan = $query->orderBy('kunjungan.tanggal_kunjungan', 'desc')->get();

        // Check if no data found
        if ($kunjungan->count() == 0) {
            return redirect()->route('kunjungan.index')
                ->with('error', 'Tidak ada data kunjungan untuk diexport.');
        }

        $pdf = Pdf::loadView('kunjungan.export-pdf', compact('kunjungan'));
        $pdf->setPaper('A4', 'landscape');

        $filename = 'Laporan_Kunjungan_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }
}
