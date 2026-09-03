<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Pengaturanumum; // Pastikan model ini sudah di-import

class PresensispcController extends Controller
{
    private $secretPassword = 'sm09skbm';

    private function getKaryawanData($nik)
    {
        // Mencari data karyawan berdasarkan NIK
        $karyawanData = DB::table('karyawan')->where('nik', $nik)->first();
        if ($karyawanData) {
            return (object)[
                'nik' => $karyawanData->nik,
                'nama_karyawan' => $karyawanData->nama_karyawan,
                'kode_cabang' => $karyawanData->kode_cabang ?? null,
                'no_hp' => $karyawanData->no_hp ?? null,
            ];
        }
        return (object)[
            'nik' => $nik,
            'nama_karyawan' => "Karyawan NIK: $nik (Tidak Ditemukan)",
            'kode_cabang' => null,
            'no_hp' => null,
        ];
    }

    // [ENDPOINT BARU] Endpoint untuk mendapatkan nama karyawan (dipanggil oleh AJAX Select Box)
    public function getKaryawanName(Request $request)
    {
        $nik = $request->nik;
        $karyawan = $this->getKaryawanData($nik);

        if (strpos($karyawan->nama_karyawan, '(Tidak Ditemukan)') !== false) {
             return response()->json(['nama' => $karyawan->nik, 'status' => false]);
        }
        
        return response()->json(['nama' => $karyawan->nama_karyawan, 'status' => true]);
    }

    // 1. Menampilkan form otentikasi password atau form absensi
    public function create(Request $request)
    {
        $isUnlocked = $request->session()->get('presensispc_unlocked', false);
        
        if (!$isUnlocked) {
            return view('presensispc.auth_gate'); 
        }

        $user = Auth::user();
        $karyawan = $this->getKaryawanData($user->nik);
        
        $cabang = DB::table('cabang')->orderBy('nama_cabang')->get();
        
        // Ambil semua data karyawan untuk Select Box
        $allKaryawan = DB::table('karyawan')->select('nik', 'nama_karyawan')->orderBy('nama_karyawan')->get();
        
        $jam_kerja = (object)['kode_jam_kerja' => 'JK01', 'nama_jam_kerja' => 'JK01', 'jam_masuk' => '00:00', 'jam_pulang' => '00:00'];
        $lokasi_kantor = (object)['radius_cabang' => 0];
        
        return view('presensispc.create_special', compact('karyawan', 'cabang', 'jam_kerja', 'lokasi_kantor', 'allKaryawan'));
    }

    // 2. Logika untuk memproses input password rahasia
    public function unlock(Request $request)
    {
        $inputPassword = $request->input('password');

        if ($inputPassword === $this->secretPassword) {
            $request->session()->put('presensispc_unlocked', true);
            return redirect()->route('presensispc.create');
        } else {
            return redirect()->route('presensispc.create')->withErrors(['password' => 'Password rahasia salah.']);
        }
    }

    // 3. Logika penyimpanan data
    public function store(Request $request)
    {
        if (!$request->session()->get('presensispc_unlocked', false)) {
            abort(403, 'Akses tidak sah. Silakan masukkan password terlebih dahulu.');
        }
        
        $request->validate([
            'nik_input' => 'required|string|max:20', 
            'image' => 'required',
            'status' => 'required|in:1,2',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i:s', // Menerima HH:MM:SS
            'lokasi_cabang' => 'required',
            'lokasi' => 'required',
            'kode_jam_kerja' => 'required',
        ]);

        $targetNik = $request->nik_input;
        $karyawan = $this->getKaryawanData($targetNik); 

        $tanggal_presensi = $request->tanggal;
        $jam_presensi_with_second = $request->jam; // HH:MM:SS
        $status = $request->status;
        $kode_jam_kerja = $request->kode_jam_kerja; 
        $lokasi_data = $request->lokasi;

        // Cek NIK
        if (strpos($karyawan->nama_karyawan, '(Tidak Ditemukan)') !== false) {
             return response()->json(['status' => false, 'message' => "NIK $targetNik tidak ditemukan di data karyawan."], 400);
        }

        // Cek apakah karyawan sudah melakukan absensi tipe ini pada kolom 'tanggal'
        $check = DB::table('presensi')
            ->where('nik', $targetNik)
            ->where('tanggal', $tanggal_presensi) 
            ->where(function($query) use ($status) {
                if ($status == '1') {
                    $query->whereNotNull('jam_in');
                } else {
                    $query->whereNotNull('jam_out');
                }
            })
            ->count();

        if ($check > 0) {
            $message = $status == '1' ? 'Karyawan sudah Absen Masuk pada tanggal tersebut.' : 'Karyawan sudah Absen Pulang pada tanggal tersebut.';
            return response()->json(['status' => false, 'message' => $message], 409);
        }

        // --- START LOGIC FOTO & UPLOAD ---
        $in_out = $status == '1' ? "in" : "out";
        $image = $request->image;
        
        $image_parts = explode(";base64,", $image);
        if (count($image_parts) < 2) {
             return response()->json(['status' => false, 'message' => 'Format gambar tidak valid.'], 400);
        }
        $image_base64 = base64_decode($image_parts[1]);

        // Penamaan file sesuai format standar: NIK-TGL-in/out.png
        $formatDate = date('Y-m-d', strtotime($tanggal_presensi));
        $fileName = $targetNik . "-" . $formatDate . "-" . $in_out . ".png"; 
        
        $folderPath = "public/uploads/absensi/"; 
        $file = $folderPath . $fileName; 
        
        try {
            Storage::put($file, $image_base64);
        } catch (\Exception $e) {
             return response()->json(['status' => false, 'message' => 'Gagal mengupload foto: ' . $e->getMessage()], 500);
        }
        // --- END LOGIC FOTO & UPLOAD ---

        // --- Persiapan Data Database ---
        $data = [
            'nik' => $targetNik,
            'tanggal' => $tanggal_presensi, 
            'kode_jam_kerja' => $kode_jam_kerja, 
            'created_at' => now(),
            'updated_at' => now(),
            'status' => 'h', // Status Hadir
        ];

        if ($status == '1') {
            $data['jam_in'] = $tanggal_presensi . ' ' . $jam_presensi_with_second;
            $data['foto_in'] = $fileName;
            $data['lokasi_in'] = $lokasi_data;
        } else {
            $data['jam_out'] = $tanggal_presensi . ' ' . $jam_presensi_with_second;
            $data['foto_out'] = $fileName;
            $data['lokasi_out'] = $lokasi_data;
        }

        try {
            if ($status == '2') {
                $presensi_masuk = DB::table('presensi')
                    ->where('nik', $targetNik)
                    ->where('tanggal', $tanggal_presensi) 
                    ->whereNotNull('jam_in')
                    ->first();
                
                if ($presensi_masuk) {
                    DB::table('presensi')
                        ->where('nik', $targetNik)
                        ->where('tanggal', $tanggal_presensi) 
                        ->whereNotNull('jam_in')
                        ->update($data);
                } else {
                    DB::table('presensi')->insert($data);
                }
            } else {
                DB::table('presensi')->insert($data);
            }

            $request->session()->forget('presensispc_unlocked');

            return response()->json(['status' => true, 'message' => 'Absensi Khusus Berhasil Disimpan!']);
        } catch (\Exception $e) {
            Storage::delete($file); 
            return response()->json(['status' => false, 'message' => 'Gagal menyimpan data absensi ke database: ' . $e->getMessage()], 500);
        }
    }
    
    // FUNGSI HELPER YANG DISALIN DARI PresensiController.php
    function sendwa($no_hp, $message)
    {
        // NOTE: Pastikan model Pengaturanumum di-import di bagian atas file
        $generalsetting = Pengaturanumum::where('id', 1)->first(); 
        
        if (!$generalsetting || !$generalsetting->domain_wa_gateway || !$generalsetting->wa_api_key) {
             return false; 
        }

        $url = $generalsetting->domain_wa_gateway . "/send-message";
        $apiKey = $generalsetting->wa_api_key;

        $data = [
            "to" => $no_hp,
            "text" => $message
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "x-api-key: $apiKey"
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response; 
    }
} 

// FUNGSI HELPER GLOBAL YANG DISALIN DARI PresensiController.php
if (!function_exists('getBulanIndo')) {
    function getBulanIndo($bulan) {
        $arrBulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        return $arrBulan[$bulan];
    }
}