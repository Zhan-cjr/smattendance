<?php

namespace App\Http\Controllers;

use App\Models\AktivitasKaryawan;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class AktivitasKaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = User::where('id', auth()->user()->id)->first();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();
        $query = AktivitasKaryawan::join('karyawan', 'aktivitas_karyawan.nik', '=', 'karyawan.nik')
            ->select('aktivitas_karyawan.*', 'karyawan.nama_karyawan');

        // If user is karyawan role, only show their own activities
        if ($user->hasRole('karyawan')) {
            $query->where('aktivitas_karyawan.nik', $user_karyawan->nik);
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
                $query->where('aktivitas_karyawan.nik', $request->nik);
            }
        }

        // Filter by date range if provided
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('aktivitas_karyawan.created_at', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('aktivitas_karyawan.created_at', '<=', $request->tanggal_akhir);
        }

        $aktivitas = $query->orderBy('aktivitas_karyawan.created_at', 'desc')->paginate(10);

        // Get karyawans based on access
        if ($user->hasRole('karyawan')) {
            $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        } else {
            $karyawans = $this->getKaryawansByAccess($user);
        }

        // If user is karyawan role, use mobile view
        if ($user->hasRole('karyawan')) {
            return view('aktivitaskaryawan.index-mobile', compact('aktivitas', 'karyawans'));
        }

        return view('aktivitaskaryawan.index', compact('aktivitas', 'karyawans'));
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
            return view('aktivitaskaryawan.create-mobile', compact('karyawan'));
        } else {
            $karyawans = $this->getKaryawansByAccess($user);
            return view('aktivitaskaryawan.create', compact('karyawans'));
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
            'jenis_aktivitas_final' => 'required|string|max:255',
            'foto' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            // Jika AJAX request, return JSON error
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => implode(', ', $validator->messages()->all())
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['nik', 'jenis_aktivitas_final', 'lokasi']);
        $data['jenis_aktivitas'] = $data['jenis_aktivitas_final'];
        $data['aktivitas'] = $data['jenis_aktivitas_final'];
        unset($data['jenis_aktivitas_final']);

        // Handle foto upload (base64 or file)
        if ($request->filled('foto')) {
            // Handle base64 foto from camera
            $fotoData = $request->input('foto');
            if (strpos($fotoData, 'data:image') === 0) {
                // Extract base64 data
                $image_parts = explode(";base64,", $fotoData);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);

                // Generate filename
                $fotoName = time() . '_aktivitas.' . $image_type;

                // Save file
                $destinationPath = 'public/uploads/aktivitas/';
                if (!Storage::exists($destinationPath)) {
                    Storage::makeDirectory($destinationPath, 0775, true);
                    $path = Storage::path($destinationPath);
                    chmod($path, 0775);
                }
                Storage::put($destinationPath . $fotoName, $image_base64);
                $data['foto'] = $fotoName;
            }
        } elseif ($request->hasFile('foto')) {
            // Handle file upload (for admin)
            $foto = $request->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            
            $destinationPath = 'public/uploads/aktivitas';
            if (!Storage::exists($destinationPath)) {
                Storage::makeDirectory($destinationPath, 0775, true);
                $path = Storage::path($destinationPath);
                chmod($path, 0775);
            }
            
            $foto->storeAs($destinationPath, $fotoName);
            $data['foto'] = $fotoName;
        }

        AktivitasKaryawan::create($data);

        // Jika AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => 'Aktivitas karyawan berhasil ditambahkan.'
            ]);
        }

        return redirect()->route('aktivitaskaryawan.index')
            ->with('success', 'Aktivitas karyawan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AktivitasKaryawan $aktivitaskaryawan)
    {
        // Get aktivitas with karyawan data using join
        $aktivitaskaryawan = AktivitasKaryawan::join('karyawan', 'aktivitas_karyawan.nik', '=', 'karyawan.nik')
            ->select('aktivitas_karyawan.*', 'karyawan.nama_karyawan')
            ->where('aktivitas_karyawan.id', $aktivitaskaryawan->id)
            ->first();

        return view('aktivitaskaryawan.show', compact('aktivitaskaryawan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AktivitasKaryawan $aktivitaskaryawan)
    {
        /** @var \App\Models\User $user */
        $user = User::where('id', auth()->user()->id)->first();

        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();

        // If user is karyawan role, only allow editing their own activities
        if ($user->hasRole('karyawan') && $aktivitaskaryawan->nik !== $user_karyawan->nik) {
            abort(403, 'Unauthorized action.');
        }

        // Validasi akses untuk admin jika bukan super admin
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $karyawan = Karyawan::where('nik', $aktivitaskaryawan->nik)->first();
            if ($karyawan) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();

                if (!in_array($karyawan->kode_cabang, $userCabangs) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke aktivitas karyawan ini.');
                }
            }
        }

        // If user is karyawan role, only show their own data
        if ($user->hasRole('karyawan')) {
            $karyawan = Karyawan::where('nik', $user_karyawan->nik)->first();
            return view('aktivitaskaryawan.edit-mobile', compact('aktivitaskaryawan', 'karyawan'));
        } else {
            $karyawans = $this->getKaryawansByAccess($user);
            return view('aktivitaskaryawan.edit', compact('aktivitaskaryawan', 'karyawans'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AktivitasKaryawan $aktivitaskaryawan)
    {
        /** @var \App\Models\User $user */
        $user = User::where('id', auth()->user()->id)->first();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();

        // If user is karyawan role, only allow updating their own activities
        if ($user->hasRole('karyawan') && $aktivitaskaryawan->nik !== $user_karyawan->nik) {
            abort(403, 'Unauthorized action.');
        }

        // Validasi akses untuk admin jika bukan super admin
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $karyawan = Karyawan::where('nik', $aktivitaskaryawan->nik)->first();
            if ($karyawan) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();

                if (!in_array($karyawan->kode_cabang, $userCabangs) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke aktivitas karyawan ini.');
                }
            }
        }

        // If user is karyawan role, force their own NIK
        if ($user->hasRole('karyawan')) {
            $request->merge(['nik' => $user_karyawan->nik]);
        } else {
            // Validasi akses untuk admin jika bukan super admin dan NIK berubah
            if (!$user->isSuperAdmin() && $request->filled('nik') && $request->nik !== $aktivitaskaryawan->nik) {
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
            'jenis_aktivitas_final' => 'nullable|string|max:255',
            'foto' => 'nullable|string',
            'lokasi' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only(['nik', 'jenis_aktivitas_final', 'lokasi']);
        if (!empty($data['jenis_aktivitas_final'])) {
            $data['jenis_aktivitas'] = $data['jenis_aktivitas_final'];
        }
        unset($data['jenis_aktivitas_final']);

        // Handle foto upload (base64 or file)
        if ($request->filled('foto')) {
            // Handle base64 foto from camera
            $fotoData = $request->input('foto');
            if (strpos($fotoData, 'data:image') === 0) {
                // Delete old foto if exists
                if ($aktivitaskaryawan->foto) {
                    Storage::delete('public/uploads/aktivitas/' . $aktivitaskaryawan->foto);
                }

                // Extract base64 data
                $image_parts = explode(";base64,", $fotoData);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);

                // Generate filename
                $fotoName = time() . '_aktivitas.' . $image_type;

                // Save file
                $destinationPath = 'public/uploads/aktivitas/';
                if (!Storage::exists($destinationPath)) {
                    Storage::makeDirectory($destinationPath, 0775, true);
                    $path = Storage::path($destinationPath);
                    chmod($path, 0775);
                }
                Storage::put($destinationPath . $fotoName, $image_base64);
                $data['foto'] = $fotoName;
            }
        } elseif ($request->hasFile('foto')) {
            // Handle file upload (for admin)
            // Delete old foto if exists
            if ($aktivitaskaryawan->foto) {
                Storage::delete('public/uploads/aktivitas/' . $aktivitaskaryawan->foto);
            }

            $foto = $request->file('foto');
            $fotoName = time() . '_' . $foto->getClientOriginalName();
            
            $destinationPath = 'public/uploads/aktivitas';
            if (!Storage::exists($destinationPath)) {
                Storage::makeDirectory($destinationPath, 0775, true);
                $path = Storage::path($destinationPath);
                chmod($path, 0775);
            }
            
            $foto->storeAs($destinationPath, $fotoName);
            $data['foto'] = $fotoName;
        }

        $aktivitaskaryawan->update($data);

        return redirect()->route('aktivitaskaryawan.index')
            ->with('success', 'Aktivitas karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AktivitasKaryawan $aktivitaskaryawan)
    {
        /** @var \App\Models\User $user */
        $user = User::where('id', auth()->user()->id)->first();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();

        // If user is karyawan role, only allow deleting their own activities
        if ($user->hasRole('karyawan') && $aktivitaskaryawan->nik !== $user_karyawan->nik) {
            abort(403, 'Unauthorized action.');
        }

        // Validasi akses untuk admin jika bukan super admin
        if (!$user->hasRole('karyawan') && !$user->isSuperAdmin()) {
            $karyawan = Karyawan::where('nik', $aktivitaskaryawan->nik)->first();
            if ($karyawan) {
                $userCabangs = $user->getCabangCodes();
                $userDepartemens = $user->getDepartemenCodes();

                if (!in_array($karyawan->kode_cabang, $userCabangs) || !in_array($karyawan->kode_dept, $userDepartemens)) {
                    abort(403, 'Anda tidak memiliki akses ke aktivitas karyawan ini.');
                }
            }
        }

        // Delete foto if exists
        if ($aktivitaskaryawan->foto) {
            Storage::delete('public/uploads/aktivitas/' . $aktivitaskaryawan->foto);
        }

        $aktivitaskaryawan->delete();

        return redirect()->route('aktivitaskaryawan.index')
            ->with('success', 'Aktivitas karyawan berhasil dihapus.');
    }

    /**
     * Export aktivitas karyawan to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();
        $user_karyawan = Userkaryawan::where('id_user', $user->id)->first();

        // Validate that NIK is required for export
        if (!$request->filled('nik') && !$user->hasRole('karyawan')) {
            return redirect()->route('aktivitaskaryawan.index')
                ->with('error', 'Silakan pilih karyawan terlebih dahulu untuk export PDF.');
        }

        // Validate user_karyawan for karyawan role
        if ($user->hasRole('karyawan') && !$user_karyawan) {
            return redirect()->route('aktivitaskaryawan.index')
                ->with('error', 'Data karyawan tidak ditemukan.');
        }

        $query = AktivitasKaryawan::join('karyawan', 'aktivitas_karyawan.nik', '=', 'karyawan.nik')
            ->select('aktivitas_karyawan.*', 'karyawan.nama_karyawan', 'cabang.nama_cabang', 'departemen.nama_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');

        // If user is karyawan role, only show their own activities
        if ($user->hasRole('karyawan')) {
            $query->where('aktivitas_karyawan.nik', $user_karyawan->nik);
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
            $query->where('aktivitas_karyawan.nik', $request->nik);
        }

        // Filter by date range if provided
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('aktivitas_karyawan.created_at', '>=', $request->tanggal_awal);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('aktivitas_karyawan.created_at', '<=', $request->tanggal_akhir);
        }

        $aktivitas = $query->orderBy('aktivitas_karyawan.created_at', 'desc')->get();

        // Check if no data found
        if ($aktivitas->count() == 0) {
            return redirect()->route('aktivitaskaryawan.index')
                ->with('error', 'Tidak ada data aktivitas untuk diekspor.');
        }

        if ($user->hasRole('karyawan')) {
            $karyawan = Karyawan::join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->select('karyawan.nik', 'karyawan.nama_karyawan', 'cabang.nama_cabang', 'departemen.nama_dept')
                ->where('karyawan.nik', $user_karyawan->nik)->first();
        } else {
            $karyawan = Karyawan::join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->select('karyawan.nik', 'karyawan.nama_karyawan', 'cabang.nama_cabang', 'departemen.nama_dept')
                ->where('karyawan.nik', $request->nik)->first();
        }


        $data = [
            'aktivitas' => $aktivitas,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
            'nik_filter' => $request->nik ?? $user_karyawan->nik,
            'karyawan' => $karyawan,
            'export_date' => now()->format('d F Y, H:i:s'),
            'user' => $user
        ];


        $pdf = Pdf::loadView('aktivitaskaryawan.export-pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        $filename = 'aktivitas_karyawan_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->stream($filename);
    }
}
