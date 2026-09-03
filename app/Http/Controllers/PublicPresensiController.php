<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\Jamkerja;
use App\Models\Setjamkerjabydate;
use App\Models\Setjamkerjabyday;
use App\Models\Detailsetjamkerjabydept;
use App\Jobs\SendWaMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PublicPresensiController extends Controller
{
    public function index()
    {
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $scheme = $generalsetting->mobile_theme_scheme ?? 'green';
        
        $colors = [
            'green' => ['primary' => '#32745e', 'rgb' => '50, 116, 94', 'secondary' => '#3ab58c', 'bg_gradient' => 'linear-gradient(135deg, #064e3b 0%, #065f46 100%)'],
            'blue' => ['primary' => '#0d47a1', 'rgb' => '13, 71, 161', 'secondary' => '#1976d2', 'bg_gradient' => 'linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%)'],
            'red' => ['primary' => '#b71c1c', 'rgb' => '183, 28, 28', 'secondary' => '#d32f2f', 'bg_gradient' => 'linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%)'],
            'purple' => ['primary' => '#4a148c', 'rgb' => '74, 20, 140', 'secondary' => '#7b1fa2', 'bg_gradient' => 'linear-gradient(135deg, #4c1d95 0%, #5b21b6 100%)'],
            'orange' => ['primary' => '#e65100', 'rgb' => '230, 81, 0', 'secondary' => '#f57c00', 'bg_gradient' => 'linear-gradient(135deg, #7c2d12 0%, #9a3412 100%)'],
            'dark' => ['primary' => '#bb86fc', 'rgb' => '187, 134, 252', 'secondary' => '#cf6679', 'bg_gradient' => 'linear-gradient(135deg, #121212 0%, #1e1e1e 100%)'],
        ];

        $active_colors = $colors[$scheme] ?? $colors['green'];

        return view('presensi.public_kiosk', [
            'generalsetting' => $generalsetting,
            'active_colors' => $active_colors
        ]);
    }

    public function checkRfid(Request $request)
    {
        $rfid_uid = $request->rfid_uid;
        $karyawan = Karyawan::leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select('karyawan.*', 'jabatan.nama_jabatan', 'departemen.nama_dept')
            ->where('karyawan.rfid_uid', $rfid_uid)
            ->first();

        if (!$karyawan) {
            return response()->json(['status' => 'error', 'message' => 'Kartu RFID tidak terdaftar'], 200);
        }

        if ($karyawan->status_aktif_karyawan != '1') {
            return response()->json(['status' => 'error', 'message' => 'Karyawan tidak aktif'], 200);
        }

        // Determine if this is check-in or check-out
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $cabang = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();
        $timezone_cabang = $cabang->timezone ?? $generalsetting->timezone ?? config('app.timezone');
        $carbon_now = Carbon::now($timezone_cabang);
        $tanggal_sekarang = $carbon_now->format('Y-m-d');

        $presensi_hariini = Presensi::where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_sekarang)
            ->first();

        $type = ($presensi_hariini && $presensi_hariini->jam_in != null) ? 'out' : 'in';

        // Get jam kerja
        $jam_kerja = $this->getJamKerjaKaryawan($karyawan);

        // Foto URL
        $foto = null;
        if (!empty($karyawan->foto) && Storage::disk('public')->exists('karyawan/' . $karyawan->foto)) {
            $foto = url('/storage/karyawan/' . $karyawan->foto);
        }

        return response()->json([
            'status' => 'success',
            'nama' => $karyawan->nama_karyawan,
            'nik' => $karyawan->nik,
            'jabatan' => $karyawan->nama_jabatan ?? '-',
            'departemen' => $karyawan->nama_dept ?? '-',
            'foto' => $foto,
            'jam_kerja' => $jam_kerja ? $jam_kerja->nama_jam_kerja . ' (' . $jam_kerja->jam_masuk . ' - ' . $jam_kerja->jam_pulang . ')' : 'Tidak ada jadwal',
            'type' => $type
        ]);
    }

    public function store(Request $request)
    {
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $rfid_uid = $request->rfid_uid;
        $karyawan = Karyawan::where('rfid_uid', $rfid_uid)->first();

        if (!$karyawan) {
            return response()->json(['status' => 'error', 'message' => 'Karyawan tidak ditemukan'], 200);
        }

        $cabang = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();
        $timezone_cabang = $cabang->timezone ?? $generalsetting->timezone ?? config('app.timezone');
        $carbon_now = Carbon::now($timezone_cabang);

        $tanggal_sekarang = $carbon_now->format('Y-m-d');
        $jam_sekarang = $carbon_now->format('H:i');
        $tanggal_kemarin = $carbon_now->copy()->subDay()->format('Y-m-d');
        $tanggal_besok = $carbon_now->copy()->addDay()->format('Y-m-d');

        // Cek Presensi Kemarin (untuk lintas hari)
        $presensi_kemarin = Presensi::where('nik', $karyawan->nik)
            ->join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_kemarin)->first();

        $lintas_hari = $presensi_kemarin ? $presensi_kemarin->lintashari : 0;
        $batas_presensi_lintashari = $generalsetting->batas_presensi_lintashari;

        $tanggal_presensi = $lintas_hari == 1 ? $tanggal_kemarin : $tanggal_sekarang;
        $tanggal_pulang = $lintas_hari == 1 ? $tanggal_besok : $tanggal_sekarang;
        if ($jam_sekarang > $batas_presensi_lintashari && $lintas_hari == 1) {
            $tanggal_presensi = $tanggal_sekarang;
            $tanggal_pulang = $tanggal_besok;
        }

        $jam_kerja = $this->getJamKerjaKaryawan($karyawan);
        if (!$jam_kerja) {
            return response()->json(['status' => 'error', 'message' => 'Jadwal kerja tidak ditemukan'], 200);
        }

        // Determine status (1: Masuk, 2: Pulang)
        $presensi_hariini_check = Presensi::where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_presensi)->first();
        $status = ($presensi_hariini_check && $presensi_hariini_check->jam_in != null) ? 2 : 1;
        $in_out = $status == 1 ? "in" : "out";

        // Image handling (base64 from webcam)
        $image = $request->image;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $folderPath = "public/uploads/absensi/";
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath, 0775, true);
        }

        $jam_presensi = $tanggal_sekarang . " " . $jam_sekarang;
        $batas_jam_absen = $generalsetting->batas_jam_absen * 60;
        $batas_jam_absen_pulang = $generalsetting->batas_jam_absen_pulang * 60;

        // Full lintas hari logic (same as PresensiController)
        if ($presensi_kemarin != null) {
            if ($presensi_kemarin->lintashari == 1) {
                if ($jam_sekarang > $generalsetting->batas_presensi_lintashari) {
                    $tanggal_pulang = $tanggal_besok;
                    $jam_kerja_pulang = $jam_kerja->jam_pulang;
                    $tanggal_presensi = $tanggal_sekarang;
                } else {
                    $tanggal_pulang = $tanggal_sekarang;
                    $jam_kerja_pulang = $presensi_kemarin->jam_pulang;
                    $tanggal_presensi = $tanggal_kemarin;
                }
            } else {
                if ($jam_kerja->lintashari == 1) {
                    $tanggal_pulang = $tanggal_besok;
                    $jam_kerja_pulang = $jam_kerja->jam_pulang;
                    $tanggal_presensi = $tanggal_sekarang;
                } else {
                    $tanggal_pulang = $tanggal_sekarang;
                    $jam_kerja_pulang = $jam_kerja->jam_pulang;
                    $tanggal_presensi = $tanggal_sekarang;
                }
            }
        } else {
            if ($jam_kerja->lintashari == 1) {
                $tanggal_pulang = $tanggal_besok;
                $jam_kerja_pulang = $jam_kerja->jam_pulang;
                $tanggal_presensi = $tanggal_sekarang;
            } else {
                $tanggal_pulang = $tanggal_sekarang;
                $jam_kerja_pulang = $jam_kerja->jam_pulang;
                $tanggal_presensi = $tanggal_sekarang;
            }
        }

        $fileName = $karyawan->nik . "-" . $tanggal_presensi . "-" . $in_out . ".png";
        $file = $folderPath . $fileName;

        // Jam masuk & batas waktu absen
        $jam_masuk_string = $tanggal_presensi . " " . $jam_kerja->jam_masuk;
        $jam_masuk_carbon = Carbon::parse($jam_masuk_string, $timezone_cabang);
        $jam_mulai_masuk_carbon = $jam_masuk_carbon->copy()->subMinutes($batas_jam_absen);
        $jam_akhir_masuk_carbon = $jam_masuk_carbon->copy()->addMinutes($batas_jam_absen);

        // Jam pulang & batas waktu absen pulang
        $jam_pulang_string = $tanggal_pulang . " " . $jam_kerja_pulang;
        $jam_pulang_carbon = Carbon::parse($jam_pulang_string, $timezone_cabang);
        $jam_mulai_pulang_carbon = $jam_pulang_carbon->copy()->subMinutes($batas_jam_absen_pulang);

        $presensi_hariini = Presensi::where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_presensi)->first();

        $jam_presensi_carbon = Carbon::parse($jam_presensi, $timezone_cabang);

        if ($status == 1) {
            // --- ABSEN MASUK ---
            if ($presensi_hariini && $presensi_hariini->jam_in != null) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah absen masuk hari ini'], 200);
            } else if ($jam_presensi_carbon->lt($jam_mulai_masuk_carbon) && $generalsetting->batasi_absen == 1) {
                return response()->json(['status' => 'error', 'message' => 'Belum waktunya absen masuk, mulai pukul ' . $jam_mulai_masuk_carbon->format('H:i')], 200);
            } else if ($jam_presensi_carbon->gt($jam_akhir_masuk_carbon) && $generalsetting->batasi_absen == 1) {
                return response()->json(['status' => 'error', 'message' => 'Waktu absen masuk sudah habis'], 200);
            } else {
                try {
                    if ($presensi_hariini != null) {
                        Presensi::where('id', $presensi_hariini->id)->update([
                            'jam_in' => $jam_presensi,
                            'foto_in' => $fileName
                        ]);
                    } else {
                        Presensi::create([
                            'nik' => $karyawan->nik,
                            'tanggal' => $tanggal_presensi,
                            'jam_in' => $jam_presensi,
                            'jam_out' => null,
                            'lokasi_in' => null,
                            'lokasi_out' => null,
                            'foto_in' => $fileName,
                            'foto_out' => null,
                            'kode_jam_kerja' => $jam_kerja->kode_jam_kerja,
                            'status' => 'h'
                        ]);
                    }

                    Storage::put($file, $image_base64);

                    // Notifikasi WA (try-catch agar error WA tidak menggagalkan absen)
                    if ($generalsetting->notifikasi_wa == 1) {
                        try {
                            $message = "Terimakasih, Hari ini " . $karyawan->nama_karyawan . " absen Masuk pada " . $jam_presensi;
                            if ($generalsetting->tujuan_notifikasi_wa == 0) {
                                if ($karyawan->no_hp != "") {
                                    dispatch(new SendWaMessage($karyawan->no_hp, $message));
                                }
                            } else {
                                dispatch(new SendWaMessage($generalsetting->id_group_wa, $message));
                            }
                        } catch (\Exception $waEx) {
                            Log::error('Gagal kirim WA (kiosk masuk)', ['nik' => $karyawan->nik, 'error' => $waEx->getMessage()]);
                        }
                    }

                    return response()->json(['status' => 'success', 'message' => 'Berhasil Absen Masuk', 'type' => 'masuk']);
                } catch (\Exception $e) {
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
                }
            }
        } else {
            // --- ABSEN PULANG ---
            if ($presensi_hariini && $presensi_hariini->jam_out != null) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah absen pulang hari ini'], 200);
            } else if ($jam_presensi_carbon->lt($jam_mulai_pulang_carbon) && $generalsetting->batasi_absen == 1) {
                return response()->json(['status' => 'error', 'message' => 'Belum waktunya absen pulang, mulai pukul ' . $jam_mulai_pulang_carbon->format('H:i')], 200);
            } else {
                try {
                    if ($presensi_hariini != null) {
                        Presensi::where('id', $presensi_hariini->id)->update([
                            'jam_out' => $jam_presensi,
                            'foto_out' => $fileName
                        ]);
                    } else {
                        Presensi::create([
                            'nik' => $karyawan->nik,
                            'tanggal' => $tanggal_presensi,
                            'jam_in' => null,
                            'jam_out' => $jam_presensi,
                            'lokasi_in' => null,
                            'lokasi_out' => null,
                            'foto_in' => null,
                            'foto_out' => $fileName,
                            'kode_jam_kerja' => $jam_kerja->kode_jam_kerja,
                            'status' => 'h'
                        ]);
                    }

                    Storage::put($file, $image_base64);

                    // Notifikasi WA
                    if ($generalsetting->notifikasi_wa == 1) {
                        try {
                            $message = "Terimakasih, Hari ini " . $karyawan->nama_karyawan . " absen Pulang pada " . $jam_presensi . " Hati Hati di Jalan";
                            if ($generalsetting->tujuan_notifikasi_wa == 0) {
                                if ($karyawan->no_hp != "") {
                                    dispatch(new SendWaMessage($karyawan->no_hp, $message));
                                }
                            } else {
                                dispatch(new SendWaMessage($generalsetting->id_group_wa, $message));
                            }
                        } catch (\Exception $waEx) {
                            Log::error('Gagal kirim WA (kiosk pulang)', ['nik' => $karyawan->nik, 'error' => $waEx->getMessage()]);
                        }
                    }

                    return response()->json(['status' => 'success', 'message' => 'Berhasil Absen Pulang', 'type' => 'pulang']);
                } catch (\Exception $e) {
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
                }
            }
        }
    }

    private function getJamKerjaKaryawan($karyawan)
    {
        $hariini = date("Y-m-d");
        $namahari = $this->getnamaHari(date('D', strtotime($hariini)));
        $kode_dept = $karyawan->kode_dept;

        $jamkerja = Setjamkerjabydate::join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('nik', $karyawan->nik)
            ->where('tanggal', $hariini)
            ->first();

        if ($jamkerja == null) {
            $jamkerja = Setjamkerjabyday::join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('nik', $karyawan->nik)
                ->where('hari', $namahari)
                ->first();

            if ($jamkerja == null) {
                $jamkerja = Detailsetjamkerjabydept::join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
                    ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                    ->where('kode_dept', $kode_dept)
                    ->where('kode_cabang', $karyawan->kode_cabang)
                    ->where('hari', $namahari)
                    ->first();
            }
        }
        return $jamkerja;
    }

    private function getnamaHari($hari)
    {
        $namaHari = [
            'Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'
        ];
        return $namaHari[$hari] ?? $hari;
    }
}
