<?php

namespace App\Http\Controllers;

use App\Models\Pengaturanumum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class GeneralsettingController extends Controller
{
    public function index()
    {
        $data['setting'] = Pengaturanumum::where('id', 1)->first();
        return view('generalsettings.index', $data);
    }

    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'nama_perusahaan' => 'required',
            'alamat' => 'required',
            'telepon' => 'required',
            'total_jam_bulan' => 'required',
            'status_potongan_jam' => 'nullable',
            'periode_laporan_dari' => 'required',
            'periode_laporan_sampai' => 'required',
            'domain_email' => 'required|regex:/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/',
            'provider_wa' => 'required|in:ig,fe',
            'tujuan_notifikasi_wa' => 'required|in:0,1',
            'id_group_wa' => 'nullable|string|max:255',
            'timezone' => 'required|string|max:50',
            'nama_hrd' => 'nullable|string',
            'theme_color_1' => 'nullable|string|max:20',
            'mobile_theme_scheme' => 'nullable|string|max:20',
            'session_time' => 'nullable|integer|min:1',
            'absen_istirahat' => 'nullable',
            'potongan_istirahat' => 'nullable',
            'sistem_hari_kerja' => 'required|in:5,6',
            'monitoring_live_location_mode' => 'required|in:0,1',
            'enable_live_location_monitoring' => 'nullable',
        ]);

        try {
            //dd($request->denda);
            DB::beginTransaction();
            $setting = Pengaturanumum::findOrFail($id);

            $data = [
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'nama_hrd' => $request->nama_hrd,
                'total_jam_bulan' => $request->total_jam_bulan,
                'status_potongan_jam' => $request->has('status_potongan_jam') ? 1 : 0,
                'denda' => $request->has('denda') ? true : false,
                'face_recognition' => $request->has('face_recognition') ? true : false,
                'periode_laporan_dari' => $request->periode_laporan_dari,
                'periode_laporan_sampai' => $request->periode_laporan_sampai,
                'periode_laporan_next_bulan' => $request->has('periode_laporan_next_bulan') ? true : false,
                'batasi_absen' => $request->has('batasi_absen') ? true : false,
                'multi_lokasi' => $request->has('multi_lokasi') ? true : false,
                'batas_jam_absen' => $request->batas_jam_absen,
                'batas_jam_absen_pulang' => $request->batas_jam_absen_pulang,
                'cloud_id' => $request->cloud_id,
                'api_key' => $request->api_key,
                'domain_email' => $request->domain_email,
                'domain_wa_gateway' => $request->domain_wa_gateway,
                'wa_api_key' => $request->wa_api_key,
                'provider_wa' => $request->provider_wa,
                'tujuan_notifikasi_wa' => $request->tujuan_notifikasi_wa,
                'id_group_wa' => $request->id_group_wa,
                'notifikasi_wa' => $request->has('notifikasi_wa') ? true : false,
                'batasi_hari_izin' => $request->has('batasi_hari_izin') ? true : false,
                'jml_hari_izin_max' => $request->jml_hari_izin_max,
                'batas_presensi_lintashari' => $request->batas_presensi_lintashari,
                'timezone' => $request->timezone,
                'theme_color_1' => $request->theme_color_1,
                'theme_color_2' => $request->theme_color_2,
                'mobile_theme_scheme' => $request->mobile_theme_scheme,
                'session_time' => $request->session_time,
                'absen_istirahat' => $request->has('absen_istirahat') ? 1 : 0,
                'potongan_istirahat' => $request->has('potongan_istirahat') ? 1 : 0,
                'sistem_hari_kerja' => $request->sistem_hari_kerja,
                'monitoring_live_location_mode' => $request->monitoring_live_location_mode,
                'enable_live_location_monitoring' => $request->has('enable_live_location_monitoring') ? true : false,
            ];

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                
                $destinationPath = 'public/logo';
                if (!Storage::exists($destinationPath)) {
                    Storage::makeDirectory($destinationPath, 0775, true);
                    $path = Storage::path($destinationPath);
                    chmod($path, 0775);
                }
                
                $logo->storeAs($destinationPath, $logoName);

                // Hapus logo lama jika ada
                if ($setting->logo && Storage::exists('public/logo/' . $setting->logo)) {
                    Storage::delete('public/logo/' . $setting->logo);
                }

                $data['logo'] = $logoName;
            }

            $oldTimezone = $setting->timezone ?? 'Asia/Jakarta';
            $oldSessionTime = $setting->session_time;
            $setting->update($data);
            
            // Update .env file dengan timezone baru jika timezone berubah
            if ($oldTimezone != $request->timezone) {
                $this->updateEnvFile('APP_TIMEZONE', $request->timezone);
                
                // Clear config cache agar perubahan .env langsung diterapkan
                try {
                    Artisan::call('config:clear');
                    Artisan::call('cache:clear');
                } catch (\Exception $e) {
                    // Jika clear cache gagal, tetap lanjutkan (bisa di-clear manual)
                }
            }

            // Update SESSION_LIFETIME jika session_time diubah
            if ($request->has('session_time') && $request->session_time != $oldSessionTime) {
                // Konversi hari ke menit (1 hari = 1440 menit)
                $sessionLifetime = $request->session_time * 1440;
                $this->updateEnvFile('SESSION_LIFETIME', $sessionLifetime);
                try {
                    Artisan::call('config:clear');
                } catch (\Exception $e) {
                }
            }
            
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan. Perubahan timezone telah diterapkan.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    /**
     * Update .env file dengan key dan value baru
     */
    private function updateEnvFile($key, $value)
    {
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            return false;
        }

        $envContent = File::get($envFile);
        
        // Cek apakah key sudah ada
        if (preg_match("/^{$key}=.*/m", $envContent)) {
            // Update existing key
            $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $envContent);
        } else {
            // Tambahkan key baru di akhir file
            $envContent .= "\n{$key}={$value}\n";
        }

        File::put($envFile, $envContent);
        
        return true;
    }
}
