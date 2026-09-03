<?php

namespace App\Http\Controllers;

use App\Models\Facerecognition;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class FacerecognitionController extends Controller
{
    public function create($nik)
    {
        $data['nik'] = Crypt::decrypt($nik);
        return view('facerecognition.create', $data);
    }

    // Halaman daftarkan wajah untuk karyawan (mobile layout)
    public function createKaryawan()
    {
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        if (!$userkaryawan) {
            return redirect()->route('dashboard.index')->with('error', 'Data karyawan tidak ditemukan');
        }

        $data['nik'] = $userkaryawan->nik;
        $data['karyawan'] = Karyawan::where('nik', $userkaryawan->nik)->first();

        // Cek apakah sudah ada data wajah sebelumnya
        $existingWajah = Facerecognition::where('nik', $userkaryawan->nik)->get();
        if ($existingWajah->count() > 0) {
            // Jika sudah ada, redirect ke halaman preview
            return redirect()->route('facerecognition.karyawan.preview');
        }

        return view('facerecognition.create-karyawan', $data);
    }

    // Halaman preview data wajah yang sudah ada untuk karyawan
    public function previewKaryawan()
    {
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        if (!$userkaryawan) {
            return redirect()->route('dashboard.index')->with('error', 'Data karyawan tidak ditemukan');
        }

        $data['nik'] = $userkaryawan->nik;
        $data['karyawan'] = Karyawan::where('nik', $userkaryawan->nik)->first();
        
        // Ambil semua data wajah yang sudah ada
        $wajahList = Facerecognition::where('nik', $userkaryawan->nik)
            ->orderBy('created_at', 'desc')
            ->get();

        // Siapkan URL untuk setiap gambar dengan pengecekan file
        $nama_folder = $data['karyawan']->nik . "-" . getNamaDepan(strtolower($data['karyawan']->nama_karyawan));
        $folderPath = 'public/uploads/facerecognition/' . $nama_folder . '/';
        
        $data['wajahList'] = $wajahList->map(function($wajah) use ($folderPath, $nama_folder) {
            $filePath = $folderPath . $wajah->wajah;
            $wajah->file_exists = Storage::exists($filePath);
            
            // Buat URL dengan encoding yang benar dan cache busting
            if ($wajah->file_exists) {
                // Encode setiap bagian path secara terpisah
                $encodedFolder = rawurlencode($nama_folder);
                $encodedFileName = rawurlencode($wajah->wajah);
                // Ganti %2F kembali menjadi / untuk folder (karena folder separator tidak perlu di-encode)
                $encodedFolder = str_replace('%2F', '/', $encodedFolder);
                
                // Gunakan timestamp dari file modified time untuk cache busting
                // Jika file di-update, timestamp akan berubah dan browser akan fetch versi baru
                try {
                    $fileTimestamp = Storage::lastModified($filePath);
                } catch (\Exception $e) {
                    // Fallback ke created_at timestamp dari database jika file timestamp tidak tersedia
                    $fileTimestamp = \Carbon\Carbon::parse($wajah->created_at)->timestamp;
                }
                
                // Tambahkan cache busting dengan file timestamp untuk memastikan gambar selalu fresh
                $wajah->image_url = url('/storage/uploads/facerecognition/' . $encodedFolder . '/' . $encodedFileName . '?v=' . $fileTimestamp);
            } else {
                $wajah->image_url = null;
            }
            
            return $wajah;
        });

        return view('facerecognition.preview-karyawan', $data);
    }

    // Hapus semua wajah karyawan yang sedang login
    public function destroyAllKaryawan()
    {
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

        if (!$userkaryawan) {
            return redirect()->route('dashboard.index')->with('error', 'Data karyawan tidak ditemukan');
        }

        $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();
        if (!$karyawan) {
            return redirect()->route('dashboard.index')->with('error', 'Data karyawan tidak ditemukan');
        }

        $folder = $karyawan->nik . '-' . getNamaDepan(strtolower($karyawan->nama_karyawan));
        $folderPath = 'public/uploads/facerecognition/' . $folder;
        
        try {
            // Hapus semua file di folder
            if (Storage::exists($folderPath)) {
                Storage::deleteDirectory($folderPath);
            }
            // Hapus semua record di database
            Facerecognition::where('nik', $userkaryawan->nik)->delete();
            
            // Redirect ke halaman create untuk perekaman baru
            return redirect()->route('facerecognition.karyawan.create')->with('success', 'Data wajah lama berhasil dihapus. Silakan lakukan perekaman ulang.');
        } catch (\Exception $e) {
            return redirect()->route('facerecognition.karyawan.preview')->with('error', 'Gagal menghapus data wajah: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $karyawan = Karyawan::where('nik', $request->nik)->first();
        $nama_folder = $karyawan->nik . "-" . getNamaDepan($karyawan->nama_karyawan);
        $folderPath = "public/uploads/facerecognition/" . $request->nik . "-" . getNamaDepan(strtolower($karyawan->nama_karyawan)) . "/";

        // dd(storage_path($folderPath));
        // Membuat folder jika belum ada dan set permission
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath, 0775, true);
            Storage::setVisibility($folderPath, 'public');
            // chmod(storage_path($folderPath), 0775);
        }

        try {
            $saved = [];
            // Jika multi-capture dengan file upload (metode baru)
            if ($request->hasFile('files')) {
                $metadata = json_decode($request->metadata, true);
                $files = $request->file('files');
                $cekWajah = Facerecognition::where('nik', $request->nik)->count();
                $urutan = $cekWajah + 1;

                foreach ($files as $index => $file) {
                    $direction = isset($metadata[$index]['direction']) ? $metadata[$index]['direction'] : 'front';
                    
                    $fileName = $urutan . "_" . $direction . ".png";
                    $file->storeAs($folderPath, $fileName); // Simpan file langsung
                    // Tidak perlu file_get_contents + Storage::put karena storeAs lebih efisien

                    // Simpan ke database
                    Facerecognition::create([
                        'nik' => $request->nik,
                        'wajah' => $fileName
                    ]);

                    $saved[] = $fileName;
                    $urutan++;
                }
                 return response()->json(['success' => true, 'message' => count($saved) . ' gambar berhasil disimpan', 'files' => $saved]);

            } else if ($request->has('images')) {
                // Legacy: JSON Base64 string
                $images = json_decode($request->images, true);
                $cekWajah = Facerecognition::where('nik', $request->nik)->count();
                $urutan = $cekWajah + 1;
                foreach ($images as $img) {
                    $direction = isset($img['direction']) ? $img['direction'] : 'front';
                    $image = $img['image'];
                    $image_parts = explode(';base64', $image);
                    $image_base64 = base64_decode($image_parts[1]);
                    $fileName = $urutan . "_" . $direction . ".png";
                    $file = $folderPath . $fileName;
                    Facerecognition::create([
                        'nik' => $request->nik,
                        'wajah' => $fileName
                    ]);
                    Storage::put($file, $image_base64);
                    $saved[] = $fileName;
                    $urutan++;
                }
                return response()->json(['success' => true, 'message' => count($saved) . ' gambar berhasil disimpan', 'files' => $saved]);
            } else if ($request->has('image')) {
                // Backward compatibility: satu gambar saja
                $cekWajah = Facerecognition::where('nik', $request->nik)->count();
                $formatName = $cekWajah + 1;
                $image = $request->image;
                $image_parts = explode(';base64', $image);
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = $formatName . ".png";
                $file = $folderPath . $fileName;
                Facerecognition::create([
                    'nik' => $request->nik,
                    'wajah' => $fileName
                ]);
                Storage::put($file, $image_base64);
                return response()->json(['success' => true, 'message' => 'Data Berhasil Disimpan', 'file' => $fileName]);
            } else {
                return response()->json(['success' => false, 'message' => 'Tidak ada gambar yang dikirim']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        $facerecognition = Facerecognition::where('id', $id)->firstorfail();
        $karyawan = Karyawan::where('nik', $facerecognition->nik)->first();
        try {
            $nama_file = $facerecognition->wajah;
            $nama_folder = $karyawan->nik . "-" . getNamaDepan(strtolower($karyawan->nama_karyawan));
            $path = 'public/uploads/facerecognition/' . $nama_folder . "/" . $nama_file;
            Storage::delete($path);
            $facerecognition->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function getWajah()
    {
        $user = User::where('id', auth()->user()->id)->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $wajah = Facerecognition::where('nik', $userkaryawan->nik)->get();
        return response()->json($wajah);
    }

    // Hapus semua wajah berdasarkan NIK
    public function destroyAll($nik)
    {
        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('nik', $nik)->first();
        if (!$karyawan) {
            return Redirect::back()->with(messageError('Karyawan tidak ditemukan'));
        }
        $folder = $karyawan->nik . '-' . getNamaDepan(strtolower($karyawan->nama_karyawan));
        $folderPath = 'public/uploads/facerecognition/' . $folder;
        try {
            // Hapus semua file di folder
            if (Storage::exists($folderPath)) {
                Storage::deleteDirectory($folderPath);
            }
            // Hapus semua record di database
            Facerecognition::where('nik', $nik)->delete();
            return Redirect::back()->with(messageSuccess('Semua data wajah berhasil dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Gagal menghapus semua wajah: ' . $e->getMessage()));
        }
    }
}
