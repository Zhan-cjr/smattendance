<?php

namespace App\Http\Controllers;

use App\Models\AjuanJadwal;
use App\Models\Cabang;
use App\Models\Denda;
use App\Models\Departemen;
use App\Models\Detailharilibur;
use App\Models\Detailsetjamkerjabydept;
use App\Models\Device;
use App\Models\Facerecognition;
use App\Models\GrupDetail;
use App\Models\GrupJamkerjaBydate;
use App\Models\Harilibur;
use App\Models\Izindinas;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\Setjamkerjabydate;
use App\Models\Setjamkerjabyday;
use App\Models\Setjamkerjabydept;
use App\Models\User;
use App\Models\Userkaryawan;
use App\Jobs\SendWaMessage;
use Carbon\Carbon;
use CURLFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PresensiController extends Controller
{

    public function index(Request $request)
    {
       
        $user = auth()->user();

        $tanggal = !empty($request->tanggal) ? $request->tanggal : date('Y-m-d');
        $presensi = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi.id',
                'presensi.nik',
                'presensi.tanggal',
                'presensi.kode_jam_kerja',
                'nama_jam_kerja',
                'jam_masuk',
                'jam_pulang',
                'istirahat',
                'jam_awal_istirahat',
                'jam_akhir_istirahat',
                'jam_in',
                'foto_in',
                'jam_out',
                'foto_out',
                'status',
                'lintashari',
                'total_jam',
                'presensi.denda',
                'presensi.status_potongan'
            )
            ->where('presensi.tanggal', $tanggal);

        $query = Karyawan::query();
        $query->select(
            'presensi.id',
            'karyawan.nik',
            'karyawan.nik_show',
            'nama_karyawan',
            'kode_dept',
            'kode_cabang',
            'presensi.tanggal as tanggal_presensi',
            'presensi.jam_in',
            'presensi.kode_jam_kerja',
            'nama_jam_kerja',
            'jam_masuk',
            'jam_pulang',
            'istirahat',
            'jam_awal_istirahat',
            'jam_akhir_istirahat',
            'jam_in',
            'jam_out',
            'status',
            'foto_in',
            'foto_out',
            'lintashari',
            'karyawan.pin',
            'total_jam',
            'presensi.denda',
            'presensi.status_potongan'
        );
        $query->leftjoinSub($presensi, 'presensi', function ($join) {
            $join->on('karyawan.nik', '=', 'presensi.nik');
        });
        
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
        
        $query->orderBy('nama_karyawan');
        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }

        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        $karyawan = $query->paginate(10);
        $karyawan->appends(request()->all());
        $data['karyawan'] = $karyawan;
        $data['cabang'] = $user->getCabang();
        
        // Get departemen berdasarkan cabang yang dipilih
        $departemen = collect();
        if (!empty($request->kode_cabang)) {
            $departemen = Departemen::whereIn('kode_dept', 
                Karyawan::where('kode_cabang', $request->kode_cabang)->distinct()->pluck('kode_dept')
            )->get();
        } else {
            // Jika tidak ada filter cabang, ambil semua departemen yang accessible
            if ($user->isSuperAdmin()) {
                $departemen = Departemen::get();
            } else {
                $userDepartemens = $user->getDepartemenCodes();
                if (!empty($userDepartemens)) {
                    $departemen = Departemen::whereIn('kode_dept', $userDepartemens)->get();
                }
            }
        }
        
        $data['departemen'] = $departemen;
        $data['denda_list'] = Denda::all()->toArray();
        return view('presensi.index', $data);
    }
    public function create(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja ?? null;

        //Get Data Karyawan By User
        //Get Data Karyawan By User
        $user = User::where('id', auth()->user()->id)->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();

        if ($karyawan->lock_jam_kerja == 0 && $kode_jam_kerja == null) {
            $presensi = Presensi::where('nik', $karyawan->nik)->where('tanggal', date('Y-m-d'))->first();
            // Jika sudah ada jam_in (absen masuk sudah dilakukan), redirect untuk absen pulang
            // Tidak perlu lagi tampilkan pilih jadwal karena sudah diisi saat absen masuk
            if ($presensi != null && $presensi->jam_in != null) {
                return redirect('/presensi/create?kode_jam_kerja=' . $presensi->kode_jam_kerja);
            }
            // Jika belum ada jam_in, lanjut cari jadwal otomatis
            // Jika tidak ada jadwal otomatis, baru tampilkan pilih jadwal di bagian bawah
        }

        $general_setting = Pengaturanumum::where('id', 1)->first();
        //Cek Lokasi Kantor
        $lokasi_kantor = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();

        // Ambil timezone dari cabang (jika ada), jika tidak gunakan default sistem
        $timezone_cabang = $lokasi_kantor->timezone ?? $general_setting->timezone ?? config('app.timezone');

        // Gunakan Carbon dengan timezone cabang untuk mendapatkan waktu lokal cabang
        $carbon_now = Carbon::now($timezone_cabang);
        $hariini = $carbon_now->format('Y-m-d');
        $jamsekarang = $carbon_now->format('H:i');
        $tgl_sebelumnya = $carbon_now->copy()->subDay()->format('Y-m-d');
        $cekpresensi_sebelumnya = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('tanggal', $tgl_sebelumnya)
            ->where('nik', $karyawan->nik)
            ->first();

        // dd($cekpresensi_sebelumnya);
        $ceklintashari_presensi = $cekpresensi_sebelumnya != null  ? $cekpresensi_sebelumnya->lintashari : 0;

        if ($ceklintashari_presensi == 1) {
            if ($jamsekarang < $general_setting->batas_presensi_lintashari) {
                $hariini = $tgl_sebelumnya;
            }
        }

        $namahari = getnamaHari(date('D', strtotime($hariini)));

        $kode_dept = $karyawan->kode_dept;

        //Cek Presensi
        $presensi = Presensi::where('nik', $karyawan->nik)->where('tanggal', $hariini)->first();


        if ($kode_jam_kerja == null) {
            // PRIORITAS UTAMA: Cek Ajuan Jadwal yang sudah disetujui
            $ajuan_jadwal = AjuanJadwal::where('nik', $karyawan->nik)
                ->where('tanggal', $hariini)
                ->where('status', 'a') // Approved
                ->first();

            if ($ajuan_jadwal) {
                $jamkerja = Jamkerja::where('kode_jam_kerja', $ajuan_jadwal->kode_jam_kerja_tujuan)->first();
            } else {
                // Jika tidak ada ajuan, cek prioritas berikutnya
                
                //Cek Jam Kerja By Date
                $jamkerja = Setjamkerjabydate::join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                    ->where('nik', $karyawan->nik)
                    ->where('tanggal', $hariini)
                    ->first();

                //Jika Tidak Memiliki Jam Kerja By Date
                if ($jamkerja == null) {
                    //Cek Jam Kerja Grup
                    $cek_group = GrupDetail::where('nik', $karyawan->nik)->first();
                    if ($cek_group) {
                        $jamkerja = GrupJamkerjaBydate::where('kode_grup', $cek_group->kode_grup)
                            ->where('tanggal', $hariini)
                            ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                            ->first();
                    } else {
                        $jamkerja = null;
                    }

                    if ($jamkerja == null) {
                        //Cek Jam Kerja harian / Jam Kerja Khusus / Jam Kerja Per Orangannya
                        $jamkerja = Setjamkerjabyday::join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                            ->where('nik', $karyawan->nik)->where('hari', $namahari)->first();
                    }


                    // Jika Jam Kerja Harian Kosong
                    if ($jamkerja == null) {
                        $jamkerja = Detailsetjamkerjabydept::join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
                            ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                            ->where('kode_dept', $kode_dept)
                            ->where('kode_cabang', $karyawan->kode_cabang)
                            ->where('hari', $namahari)->first();
                    }
                }
            }
        } else {
            $jamkerja = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->first();
        }

        // dd($jamkerja);
        $ceklibur = Detailharilibur::join('hari_libur', 'hari_libur_detail.kode_libur', '=', 'hari_libur.kode_libur')
            ->where('nik', $karyawan->nik)
            ->where('tanggal', $hariini)
            ->first();
        $data['harilibur'] = $ceklibur;

        if ($presensi != null && $presensi->status != 'h') {
            return view('presensi.notif_izin');
        } else if ($ceklibur != null) {
            return view('presensi.notif_libur', $data);
        } else if ($jamkerja == null) {
            // Jika tidak ada jadwal otomatis, cek apakah sudah ada jam_in (absen masuk)
            if ($presensi != null && $presensi->jam_in != null) {
                // Sudah ada absen masuk hari ini, gunakan jadwal dari presensi yang sudah ada
                // Tidak perlu tampilkan pilih jadwal, lanjut ke form untuk absen pulang
                $jamkerja = Jamkerja::where('kode_jam_kerja', $presensi->kode_jam_kerja)->first();
                if ($jamkerja == null) {
                    // Fallback: gunakan jadwal pertama jika tidak ditemukan
                    $jamkerja = Jamkerja::orderBy('jam_masuk')->first();
                }
            } else {
                // Belum ada jam_in (belum absen masuk) dan tidak ada jadwal otomatis
                // Tampilkan pilih jadwal hanya dalam kasus ini
                $data['jamkerja'] = Jamkerja::orderBy('jam_masuk')->get();
                return view('presensi.pilih_jam_kerja', $data);
            }
        }

        $multi_lokasi = $general_setting->multi_lokasi;

        // Jika lock_location = 0, karyawan bisa absen dimana saja, tidak perlu pilih cabang
        if ($karyawan->lock_location == 0) {
            // Hanya kirim cabang utama saja (tidak ditampilkan di dropdown)
            $data['cabang'] = Cabang::where('kode_cabang', $karyawan->kode_cabang)->get();
        } else {
            // lock_location = 1: Karyawan harus memilih cabang
            if ($multi_lokasi == 0) {
                // Multi lokasi NON-AKTIF: Hanya tampilkan cabang utama
                $data['cabang'] = Cabang::where('kode_cabang', $karyawan->kode_cabang)->get();
            } else {
                // Multi lokasi AKTIF: Tampilkan cabang yang sudah di-set
                $kode_cabang_array = $karyawan->kode_cabang_array ?? [];
                $data['cabang'] = Cabang::WhereIn('kode_cabang', $kode_cabang_array)
                    ->orWhere('kode_cabang', $karyawan->kode_cabang)
                    ->get();
            }
        }

        $data['hariini'] = $hariini;
        $data['jam_kerja'] = $jamkerja;
        $data['lokasi_kantor'] = $lokasi_kantor;
        $data['presensi'] = $presensi;
        $data['karyawan'] = $karyawan;
        $data['general_setting'] = $general_setting;
        $data['wajah'] = Facerecognition::where('nik', $karyawan->nik)->count();



        return view('presensi.create', $data);
    }

    public function store(Request $request)
    {
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $user = User::where('id', auth()->user()->id)->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();


        $status_lock_location = $karyawan->lock_location;

        $status = $request->status;
        $lokasi = $request->lokasi;
        $kode_jam_kerja = $request->kode_jam_kerja;

        //Get Lokasi Kantor untuk mendapatkan timezone cabang
        $cabang = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();
        $lokasi_kantor = $request->lokasi_cabang;

        // Ambil timezone dari cabang (jika ada), jika tidak gunakan default sistem
        $timezone_cabang = $cabang->timezone ?? $generalsetting->timezone ?? config('app.timezone');

        // Konversi waktu presensi ke timezone cabang
        // Waktu dari client biasanya dalam UTC atau timezone sistem, konversi ke timezone cabang
        $carbon_now = Carbon::now($timezone_cabang);
        $tanggal_sekarang = $carbon_now->format('Y-m-d');
        $jam_sekarang = $carbon_now->format('H:i');
        $tanggal_kemarin = $carbon_now->copy()->subDay()->format('Y-m-d');
        $tanggal_besok = $carbon_now->copy()->addDay()->format('Y-m-d');

        //Cek Presensi Kemarin
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

        //dd($jam_sekarang);
        //Get Lokasi User
        $koordinat_user = explode(",", $lokasi);
        $latitude_user = $koordinat_user[0];
        $longitude_user = $koordinat_user[1];

        $koordinat_kantor = explode(",", $lokasi_kantor);
        $latitude_kantor = $koordinat_kantor[0];
        $longitude_kantor = $koordinat_kantor[1];

        $jarak = hitungjarak($latitude_kantor, $longitude_kantor, $latitude_user, $longitude_user);


        $radius = round($jarak["meters"]);



        $in_out = $status == 1 ? "in" : "out";
        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath, 0775, true);
            $path = Storage::path($folderPath);
            chmod($path, 0775);
        }


        $jam_kerja = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->first();

        $jam_presensi = $tanggal_sekarang . " " . $jam_sekarang;


        $batas_jam_absen = $generalsetting->batas_jam_absen * 60;
        $batas_jam_absen_pulang = $generalsetting->batas_jam_absen_pulang * 60;


        //Jamulai Absen Pulang adalah Berapa Jam Sebelum Jam Pulang

        //Jiak Kemarin Melakukan Presensi
        if ($presensi_kemarin != null) {
            //Jika Presensi Kemarin Lintas Hari
            if ($presensi_kemarin->lintashari == 1) {
                //Jika Jam Sekarang Lebih Besar dari batas_presensi_lintashari
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
        $formatName = $karyawan->nik . "-" . $tanggal_presensi . "-" . $in_out;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = $formatName . ".png";
            $file = $folderPath . $fileName;
            Storage::put($file, file_get_contents($image));
        } else {
            $image_parts = explode(";base64", $image);
            $image_base64 = base64_decode($image_parts[1]);
            $fileName = $formatName . ".png";
            $file = $folderPath . $fileName;
            Storage::put($file, $image_base64);
        }

        // Gunakan Carbon dengan timezone cabang untuk perhitungan jam
        // Parse jam_masuk (bisa H:i atau H:i:s) dan gabungkan dengan tanggal
        $jam_masuk_string = $tanggal_presensi . " " . $jam_kerja->jam_masuk;
        $jam_masuk_carbon = Carbon::parse($jam_masuk_string, $timezone_cabang);
        $jam_masuk = $jam_masuk_carbon->format('Y-m-d H:i');

        //Jam Mulai Absen adalah X Menit Sebelum Jam Masuk (dalam timezone cabang)
        $jam_mulai_masuk_carbon = $jam_masuk_carbon->copy()->subMinutes($batas_jam_absen);
        $jam_mulai_masuk = $jam_mulai_masuk_carbon->format('Y-m-d H:i');

        //Jam Akhir Absen adalah X Menit Setelah Jam Masuk (dalam timezone cabang)
        $jam_akhir_masuk_carbon = $jam_masuk_carbon->copy()->addMinutes($batas_jam_absen);
        $jam_akhir_masuk = $jam_akhir_masuk_carbon->format('Y-m-d H:i');

        // Jika jam akhir masuk melewati tengah malam, sesuaikan tanggalnya
        if ($jam_akhir_masuk_carbon->format('H:i') >= '00:00' && $jam_akhir_masuk_carbon->day != $jam_masuk_carbon->day) {
            $jam_akhir_masuk = $jam_akhir_masuk_carbon->format('Y-m-d H:i');
        }

        // Parse jam_pulang (bisa H:i atau H:i:s) dan gabungkan dengan tanggal
        $jam_pulang_string = $tanggal_pulang . " " . $jam_kerja_pulang;
        $jam_pulang_carbon = Carbon::parse($jam_pulang_string, $timezone_cabang);
        $jam_pulang = $jam_pulang_carbon->format('Y-m-d H:i');
        // dd($presensi_kemarin);

        //Jam Mulai Absen Pulang adalah X Menit Sebelum Jam Pulang (dalam timezone cabang)
        $jam_mulai_pulang_carbon = $jam_pulang_carbon->copy()->subMinutes($batas_jam_absen_pulang);
        $jam_mulai_pulang = $jam_mulai_pulang_carbon->format('Y-m-d H:i');
        //return $jam_mulai_pulang;

        // Cek Izin Dinas
        $izin_dinas = Izindinas::where('nik', $karyawan->nik)
            ->where('status', 1)
            ->where('dari', '<=', $tanggal_presensi)
            ->where('sampai', '>=', $tanggal_presensi)
            ->first();

        // dd($izin_dinas);

        if ($izin_dinas) {
            $status_lock_location = 0;
        }
        //dd($jam_presensi . " " . $jam_mulai_pulang);
        //Cek Radius
        //dd($jam_presensi . " " . $jam_mulai_masuk);
        $presensi_hariini = Presensi::where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_presensi)
            ->first();

        // Konversi jam_presensi ke Carbon untuk perbandingan
        // Gunakan parse() yang lebih fleksibel untuk menghindari error format
        $jam_presensi_carbon = Carbon::parse($jam_presensi, $timezone_cabang);

        // $jam_mulai_masuk, $jam_akhir_masuk, dan $jam_mulai_pulang sudah dalam format Y-m-d H:i dari Carbon
        $jam_mulai_masuk_carbon = Carbon::parse($jam_mulai_masuk, $timezone_cabang);
        $jam_akhir_masuk_carbon = Carbon::parse($jam_akhir_masuk, $timezone_cabang);
        $jam_mulai_pulang_carbon = Carbon::parse($jam_mulai_pulang, $timezone_cabang);

        //dd($presensi_hariini);

        //dd($jam_presensi . " " . $jam_akhir_masuk);
        if ($status_lock_location == 1 && $radius > $cabang->radius_cabang) {
            return response()->json(['status' => false, 'message' => 'Anda Berada Di Luar Radius Kantor, Jarak Anda ' . formatAngka($radius) . ' Meters Dari Kantor', 'notifikasi' => 'notifikasi_radius'], 400);
        } else {
            // Cek apakah ada approval izin untuk tanggal ini
            $approval_check = $this->checkApprovalIzin($karyawan->nik, $tanggal_presensi);
            if ($approval_check['has_approval']) {
                return response()->json(['status' => false, 'message' => $approval_check['message'], 'notifikasi' => 'notifikasi_izin_approval'], 400);
            }

            if ($status == 1) {
                if ($presensi_hariini && $presensi_hariini->jam_in != null) {
                    return response()->json(['status' => false, 'message' => 'Anda Sudah Absen Masuk Hari Ini', 'notifikasi' => 'notifikasi_sudahabsen'], 400);
                } else if ($jam_presensi_carbon->lt($jam_mulai_masuk_carbon) && $generalsetting->batasi_absen == 1) {
                    return response()->json(['status' => false, 'message' => 'Maaf Belum Waktunya Absen Masuk, Waktu Absen Dimulai Pukul ' . formatIndo3($jam_mulai_masuk), 'notifikasi' => 'notifikasi_mulaiabsen'], 400);
                } else if ($jam_presensi_carbon->gt($jam_akhir_masuk_carbon) && $generalsetting->batasi_absen == 1) {
                    return response()->json(['status' => false, 'message' => 'Maaf Waktu Absen Masuk Sudah Habis ', 'notifikasi' => 'notifikasi_akhirabsen'], 400);
                } else {
                    try {
                        if ($presensi_hariini != null) {
                            Presensi::where('id', $presensi_hariini->id)->update([
                                'jam_in' => $jam_presensi,
                                'lokasi_in' => $lokasi,
                                'foto_in' => $fileName
                            ]);
                        } else {
                            Presensi::create([
                                'nik' => $karyawan->nik,
                                'tanggal' => $tanggal_presensi,
                                'jam_in' => $jam_presensi,
                                'jam_out' => null,
                                'lokasi_in' => $lokasi,
                                'lokasi_out' => null,
                                'foto_in' => $fileName,
                                'foto_out' => null,
                                'kode_jam_kerja' => $kode_jam_kerja,
                                'status' => 'h'
                            ]);
                        }


                        //Kirim Notifikasi Ke WA (dibungkus try-catch agar error WA tidak mempengaruhi response sukses)
                        if ($generalsetting->notifikasi_wa == 1) {
                            try {
                                if ($generalsetting->tujuan_notifikasi_wa == 0) {
                                    if ($karyawan->no_hp != "") {
                                        $message = "Bismillah, " . $karyawan->nama_karyawan . " Absen masuk pada hari ini, tanggal " . DateToIndo($tanggal_presensi) . " pkl " . $jam_sekarang . ". Semangat Bekerja !";
                                        $this->sendwa($karyawan->no_hp, $message);
                                    }
                                } else {
                                    $message = "Bismillah, " . $karyawan->nama_karyawan . " Absen masuk pada hari ini, tanggal " . DateToIndo($tanggal_presensi) . " pkl " . $jam_sekarang . ". Semangat Bekerja !";
                                    $this->sendwa($generalsetting->id_group_wa, $message);
                                }
                            } catch (\Exception $waException) {
                                // Log error pengiriman WA tapi tidak mempengaruhi response sukses
                                Log::error('Gagal mengirim notifikasi WA untuk absen masuk', [
                                    'nik' => $karyawan->nik,
                                    'nama' => $karyawan->nama_karyawan,
                                    'error' => $waException->getMessage(),
                                    'trace' => $waException->getTraceAsString()
                                ]);
                            }
                        }
                        return response()->json(['status' => true, 'message' => 'Berhasil Absen Masuk', 'notifikasi' => 'notifikasi_absenmasuk'], 200);
                    } catch (\Exception $e) {
                        return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
                    }
                }
            } else {
                if ($presensi_hariini && $presensi_hariini->jam_out != null) {
                    return response()->json(['status' => false, 'message' => 'Anda Sudah Absen Pulang Hari Ini', 'notifikasi' => 'notifikasi_sudahabsen'], 400);
                } else if ($jam_presensi_carbon->lt($jam_mulai_pulang_carbon) && $generalsetting->batasi_absen == 1) {
                    return response()->json(['status' => false, 'message' => 'Maaf Belum Waktunya Absen Pulang, Waktu Absen Dimulai Pukul ' . formatIndo3($jam_mulai_pulang), 'notifikasi' => 'notifikasi_mulaiabsen'], 400);
                } else {
                    try {
                        if ($presensi_hariini != null) {
                            Presensi::where('id', $presensi_hariini->id)->update([
                                'jam_out' => $jam_presensi,
                                'lokasi_out' => $lokasi,
                                'foto_out' => $fileName
                            ]);
                        } else {
                            Presensi::create([
                                'nik' => $karyawan->nik,
                                'tanggal' => $tanggal_presensi,
                                'jam_in' => null,
                                'jam_out' => $jam_presensi,
                                'lokasi_in' => null,
                                'lokasi_out' => $lokasi,
                                'foto_in' => null,
                                'foto_out' => $fileName,
                                'kode_jam_kerja' => $kode_jam_kerja,
                                'status' => 'h'
                            ]);
                        }

                        //Kirim Notifikasi Ke WA (dibungkus try-catch agar error WA tidak mempengaruhi response sukses)
                        if ($generalsetting->notifikasi_wa == 1) {
                            try {
                                if ($generalsetting->tujuan_notifikasi_wa == 0) {
                                    if ($karyawan->no_hp != "") {
                                        $message = "Alhamdulillah,  " . $karyawan->nama_karyawan . " Absen pulang pada hari ini, tanggal " . DateToIndo($tanggal_presensi) . " pkl " . $jam_sekarang . ", Hati-hati dijalan, semoga selamat sampai tujuan.";
                                        $this->sendwa($karyawan->no_hp, $message);
                                    }
                                } else {
                                    $message = "Alhamdulillah,  " . $karyawan->nama_karyawan . " Absen pulang pada hari ini, tanggal " . DateToIndo($tanggal_presensi) . " pkl " . $jam_sekarang . ", Hati-hati dijalan, semoga selamat sampai tujuan.";
                                    $this->sendwa($generalsetting->id_group_wa, $message);
                                }
                            } catch (\Exception $waException) {
                                // Log error pengiriman WA tapi tidak mempengaruhi response sukses
                                Log::error('Gagal mengirim notifikasi WA untuk absen pulang', [
                                    'nik' => $karyawan->nik,
                                    'nama' => $karyawan->nama_karyawan,
                                    'error' => $waException->getMessage(),
                                    'trace' => $waException->getTraceAsString()
                                ]);
                            }
                        }
                        return response()->json(['status' => true, 'message' => 'Berhasil Absen Pulang', 'notifikasi' => 'notifikasi_absenpulang'], 200);
                    } catch (\Exception $e) {
                        return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
                    }
                }
            }
        }
    }


    /**
     * Check jika ada approval izin untuk tanggal tertentu
     * Jika ada, return tipe izin dan pesan
     */
    private function checkApprovalIzin($nik, $tanggal)
    {
        // Check Izin Absen
        $izin_absen = DB::table('presensi_izinabsen')
            ->where('nik', $nik)
            ->where('status', 1)
            ->where('dari', '<=', $tanggal)
            ->where('sampai', '>=', $tanggal)
            ->first();
        
        if ($izin_absen) {
            return [
                'has_approval' => true,
                'type' => 'izin_absen',
                'message' => 'Anda memiliki persetujuan Izin Absen untuk tanggal ini. Tidak boleh absen di hari izin.'
            ];
        }

        // Check Izin Sakit
        $izin_sakit = DB::table('presensi_izinsakit')
            ->where('nik', $nik)
            ->where('status', 1)
            ->where('dari', '<=', $tanggal)
            ->where('sampai', '>=', $tanggal)
            ->first();
        
        if ($izin_sakit) {
            return [
                'has_approval' => true,
                'type' => 'izin_sakit',
                'message' => 'Anda memiliki persetujuan Izin Sakit untuk tanggal ini. Tidak boleh absen di hari sakit.'
            ];
        }

        // Check Izin Cuti
        $izin_cuti = DB::table('presensi_izincuti')
            ->where('nik', $nik)
            ->where('status', 1)
            ->where('dari', '<=', $tanggal)
            ->where('sampai', '>=', $tanggal)
            ->first();
        
        if ($izin_cuti) {
            return [
                'has_approval' => true,
                'type' => 'izin_cuti',
                'message' => 'Anda memiliki persetujuan Izin Cuti untuk tanggal ini. Tidak boleh absen di hari cuti.'
            ];
        }

        // Check Izin Dinas
        $izin_dinas = DB::table('presensi_izindinas')
            ->where('nik', $nik)
            ->where('status', 1)
            ->where('dari', '<=', $tanggal)
            ->where('sampai', '>=', $tanggal)
            ->first();
        
        if ($izin_dinas) {
            return [
                'has_approval' => true,
                'type' => 'izin_dinas',
                'message' => 'Anda memiliki persetujuan Izin Dinas untuk tanggal ini. Tidak boleh absen di hari dinas.'
            ];
        }

        return [
            'has_approval' => false,
            'type' => null,
            'message' => null
        ];
    }

    /**
     * API endpoint untuk check approval izin dari frontend
     */
    public function checkApprovalIzinAPI(Request $request)
    {
        $user = auth()->user();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();
        
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        $result = $this->checkApprovalIzin($karyawan->nik, $tanggal);
        
        return response()->json($result);
    }

    function sendwa($no_hp, $message)
    {
        dispatch(new SendWaMessage($no_hp, $message));
    }
    public function edit(Request $request)
    {
        $nik = Crypt::decrypt($request->nik);
        $tanggal = $request->tanggal;

        $karyawan = Karyawan::where('nik', $nik)->first();
        $jam_kerja = Jamkerja::all();
        $presensi = Presensi::where('nik', $nik)->where('tanggal', $tanggal)->first();
        if ($presensi && $presensi->status_potongan !== null) {
            return '<div class="alert alert-warning">Data Presensi Sudah Dikunci, Hubungi Admin Untuk Membuka Kunci Laporan</div>';
        }
        $data['presensi'] = $presensi;
        $data['karyawan'] = $karyawan;
        $data['jam_kerja'] = $jam_kerja;
        $data['tanggal'] = $tanggal;

        return view('presensi.edit', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'tanggal' => 'required',
            'kode_jam_kerja' => 'required',
            'status' => 'required',
        ]);
        $presensi = Presensi::where('nik', $request->nik)->where('tanggal', $request->tanggal)->first();
        if ($presensi && $presensi->status_potongan !== null) {
            return redirect()->back()->with(['warning' => 'Data Presensi Sudah Dikunci, Hubungi Admin Untuk Membuka Kunci Laporan']);
        }

        $nik = Crypt::decrypt($request->nik);
        $tanggal = $request->tanggal;
        $kode_jam_kerja = $request->kode_jam_kerja;
        $jam_in = $request->jam_in;
        $jam_out = $request->jam_out;
        $status = $request->status;

        try {
            $cekpresensi = Presensi::where('nik', $nik)->where('tanggal', $tanggal)->first();
            if (!empty($cekpresensi)) {
                Presensi::where('nik', $nik)->where('tanggal', $tanggal)->update([
                    'jam_in' => $jam_in,
                    'jam_out' => $jam_out,
                    'status' => $status,
                    'kode_jam_kerja' => $kode_jam_kerja,
                ]);
            } else {
                Presensi::create([
                    'nik' => $nik,
                    'tanggal' => $tanggal,
                    'jam_in' => $jam_in,
                    'jam_out' => $jam_out,
                    'kode_jam_kerja' => $kode_jam_kerja,
                    'status' => $status
                ]);
            }

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function show($id, $status)
    {
        $presensi = Presensi::where('presensi.id', $id)
            ->with('mesinfingerprint')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select('presensi.*', 'karyawan.nama_karyawan', 'karyawan.kode_cabang', 'departemen.nama_dept', 'jabatan.nama_jabatan', 'cabang.nama_cabang', 'cabang.lokasi_cabang')
            ->first();
        $cabang = Cabang::where('kode_cabang', $presensi->kode_cabang)->first();
        $lokasi = explode(',', $cabang->lokasi_cabang);
        $data['latitude'] = $lokasi[0];
        $data['longitude'] = $lokasi[1];
        // if (!empty($presensi->lokasi_cabang)) {
        //     $lokasi = explode(',', $presensi->lokasi_cabang);
        //     $data['latitude'] = $lokasi[0];
        //     $data['longitude'] = $lokasi[1];
        // } else {
        //     $data['latitude'] = $cabang->latitude_cabang;
        //     $data['longitude'] = $cabang->longitude_cabang;
        // }
        $data['presensi'] = $presensi;
        $data['status'] = $status;
        $data['cabang'] = $cabang;

        return view('presensi.show', $data);
    }


    public function getdatamesin(Request $request)
    {

        $tanggal = $request->tanggal;
        $pin = $request->pin;
        $general_setting = Pengaturanumum::where('id', 1)->first();
        // dd($pin);
        // $kode_jadwal = $request->kode_jadwal;
        // if ($kode_jadwal == "JD004") {
        //     $nextday = date('Y-m-d', strtotime('+1 day', strtotime($tanggal)));
        // } else {
        //     $nextday =  $tanggal;
        // }
        $specific_value = $pin;
        $karyawan = Karyawan::where('pin', $pin)->first();
        $is_locked = false;
        if ($karyawan) {
            $presensi_lock = Presensi::where('nik', $karyawan->nik)->where('tanggal', $tanggal)->first();
            if ($presensi_lock && $presensi_lock->status_potongan !== null) {
                $is_locked = true;
            }
        }


        //Mesin 1
        $url = 'https://developer.fingerspot.io/api/get_attlog';
        $data = '{"trans_id":"1", "cloud_id":"' . $general_setting->cloud_id . '", "start_date":"' . $tanggal . '", "end_date":"' . $tanggal . '"}';
        $authorization = "Authorization: Bearer " . $general_setting->api_key;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($result);
        $datamesin1 = [];
        if ($res && isset($res->data)) {
            $datamesin1 = $res->data;
        }

        $filtered_array = array_filter($datamesin1, function ($obj) use ($specific_value) {
            return isset($obj->pin) && $obj->pin == $specific_value;
        });


        //Mesin 2
        // $url = 'https://developer.fingerspot.io/api/get_attlog';
        // $data = '{"trans_id":"1", "cloud_id":"C268909557211236", "start_date":"' . $tanggal . '", "end_date":"' . $tanggal . '"}';
        // $authorization = "Authorization: Bearer QNBCLO9OA0AWILQD";

        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // $result2 = curl_exec($ch);
        // curl_close($ch);
        // $res2 = json_decode($result2);
        // $datamesin2 = $res2->data;

        // $filtered_array_2 = array_filter($datamesin2, function ($obj) use ($specific_value) {
        //     return $obj->pin == $specific_value;
        // });

        $log_lokal = \App\Models\LogMesinPresensi::select('log_mesin_presensis.*', 'mesin_fingerprints.nama_mesin', 'mesin_fingerprints.sn', 'mesin_fingerprints.lokasi')
            ->leftJoin('mesin_fingerprints', 'log_mesin_presensis.id_mesin', '=', 'mesin_fingerprints.id')
            ->where('pin', $pin)
            ->whereDate('jam_absen', $tanggal)
            ->orderBy('jam_absen', 'desc')
            ->get();

        return view('presensi.getdatamesin', compact('filtered_array', 'is_locked', 'log_lokal'));
    }


    public function histori(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->first();
        $userkaryawan = Userkaryawan::where('id_user', auth()->user()->id)->first();
        $data['datapresensi'] = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('presensi.nik', $userkaryawan->nik)
            ->leftJoin('presensi_izinabsen_approve', 'presensi.id', '=', 'presensi_izinabsen_approve.id_presensi')
            ->leftJoin('presensi_izinabsen', 'presensi_izinabsen_approve.kode_izin', '=', 'presensi_izinabsen.kode_izin')

            ->leftJoin('presensi_izinsakit_approve', 'presensi.id', '=', 'presensi_izinsakit_approve.id_presensi')
            ->leftJoin('presensi_izinsakit', 'presensi_izinsakit_approve.kode_izin_sakit', '=', 'presensi_izinsakit.kode_izin_sakit')

            ->leftJoin('presensi_izincuti_approve', 'presensi.id', '=', 'presensi_izincuti_approve.id_presensi')
            ->leftJoin('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
            ->select(
                'presensi.*',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.total_jam',
                'presensi_jamkerja.lintashari',
                'presensi_izinabsen.keterangan as keterangan_izin',
                'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                'presensi_izincuti.keterangan as keterangan_izin_cuti'
            )
            ->when(!empty($request->dari) && !empty($request->sampai), function ($q) use ($request) {
                $q->whereBetween('presensi.tanggal', [$request->dari, $request->sampai]);
            })
            ->orderBy('presensi.tanggal', 'desc')
            ->limit(30)
            ->get();
            
        $data['namasettings'] = Pengaturanumum::first();
        $data['denda_list'] = Denda::orderBy('dari')->get()->toArray();
        
        return view('presensi.histori', $data);
    }


    public function updatefrommachine(Request $request, $pin, $status_scan)
    {
        $pin = Crypt::decrypt($pin);
        $scan = $request->scan_date;

        $karyawan       = Karyawan::where('pin', $pin)->first();

        if ($karyawan == null) {
            return Redirect::back()->with(messageError('Karyawan Tidak Ditemukan'));
            $nik = "";
        } else {
            $nik = $karyawan->nik;
        }

        // Ambil timezone dari cabang
        $cabang = Cabang::where('kode_cabang', $karyawan->kode_cabang)->first();
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $timezone_cabang = $cabang->timezone ?? $generalsetting->timezone ?? config('app.timezone');

        // Konversi waktu scan ke timezone cabang
        $carbon_scan = Carbon::parse($scan)->setTimezone($timezone_cabang);
        $tanggal_sekarang = $carbon_scan->format('Y-m-d');
        $jam_sekarang = $carbon_scan->format('H:i');
        $tanggal_kemarin = $carbon_scan->copy()->subDay()->format('Y-m-d');
        $tanggal_besok = $carbon_scan->copy()->addDay()->format('Y-m-d');

        //Cek Presensi Kemarin
        $presensi_kemarin = Presensi::where('nik', $karyawan->nik)
            ->join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_kemarin)->first();

        $lintas_hari = $presensi_kemarin ? $presensi_kemarin->lintashari : 0;

        //Jika Presensi Kemarin Status Lintas Hari nya 1 Makan Tanggal Presensi Sekarang adalah Tanggal Kemarin
        $tanggal_presensi = $lintas_hari == 1 ? $tanggal_kemarin : $tanggal_sekarang;
        $tanggal_pulang = $lintas_hari == 1 ? $tanggal_besok : $tanggal_sekarang;


        $namahari = getnamaHari(date('D', strtotime($tanggal_presensi)));
        //Cek Jam Kerja By Date
        $jamkerja = Setjamkerjabydate::join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_presensi)
            ->first();

        //Jika Tidak Memiliki Jam Kerja By Date
        if ($jamkerja == null) {
            //Cek Jam Kerja harian / Jam Kerja Khusus / Jam Kerja Per Orangannya
            $jamkerja = Setjamkerjabyday::join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->where('nik', $karyawan->nik)->where('hari', $namahari)->first();

            // Jika Jam Kerja Harian Kosong
            if ($jamkerja == null) {
                $jamkerja = Jamkerja::where('kode_jam_kerja', 'JK01')->first();
            }
        }

        //Cek Presensi
        $presensi = Presensi::where('nik', $karyawan->nik)->where('tanggal', $tanggal_presensi)->first();

        //Cek Jika Laporan Sudah Dikunci
        if ($presensi != null && $presensi->status_potongan !== null) {
             return Redirect::back()->with(messageError('Data Presensi Sudah Dikunci'));
        }

        if ($presensi != null && $presensi->status != 'h') {
            return Redirect::back()->with(messageError('Sudah Melakukan Presesni'));
        } else if ($jamkerja == null) {
            return Redirect::back()->with(messageError('Tidak Memiliki Jadwal'));
        }

        $kode_jam_kerja = $jamkerja->kode_jam_kerja;
        $jam_kerja = Jamkerja::where('kode_jam_kerja', $kode_jam_kerja)->first();

        $jam_presensi = $tanggal_sekarang . " " . $jam_sekarang;

        $jam_masuk = $tanggal_presensi . " " . date('H:i', strtotime($jam_kerja->jam_masuk));

        $presensi_hariini = Presensi::where('nik', $karyawan->nik)
            ->where('tanggal', $tanggal_presensi)
            ->first();

        if (in_array($status_scan, [0, 2, 4, 6, 8])) {
            if ($presensi_hariini && $presensi_hariini->jam_in != null) {
                return Redirect::back()->with(messageError('Sudah Melakukan Presensi Masuk'));
            } else {
                try {
                    if ($presensi_hariini != null) {
                        Presensi::where('id', $presensi_hariini->id)->update([
                            'jam_in' => $jam_presensi,
                        ]);
                    } else {
                        Presensi::create([
                            'nik' => $karyawan->nik,
                            'tanggal' => $tanggal_presensi,
                            'jam_in' => $jam_presensi,
                            'jam_out' => null,
                            'lokasi_out' => null,
                            'foto_out' => null,
                            'kode_jam_kerja' => $kode_jam_kerja,
                            'status' => 'h'
                        ]);
                    }


                    return Redirect::back()->with(messageSuccess('Berhasil Melakukan Presensi Masuk'));
                } catch (\Exception $e) {
                    return Redirect::back()->with(messageError($e->getMessage()));
                }
            }
        } else {
            try {
                if ($presensi_hariini != null) {
                    Presensi::where('id', $presensi_hariini->id)->update([
                        'jam_out' => $jam_presensi,
                    ]);
                } else {
                    Presensi::create([
                        'nik' => $karyawan->nik,
                        'tanggal' => $tanggal_presensi,
                        'jam_in' => null,
                        'jam_out' => $jam_presensi,
                        'lokasi_in' => null,
                        'foto_in' => null,
                        'kode_jam_kerja' => $kode_jam_kerja,
                        'status' => 'h'
                    ]);
                }
                return Redirect::back()->with(messageSuccess('Berhasil Melakukan Presensi Pulang'));
            } catch (\Exception $e) {
                return Redirect::back()->with(messageError($e->getMessage()));
            }
        }
    }

    public function destroy($id)
    {
        $presensi = Presensi::find($id);
        if ($presensi) {
            if ($presensi->status_potongan != null) {
                return Redirect::back()->with(['warning' => 'Data Presensi Sudah Dikunci, Hubungi Admin Untuk Membuka Kunci Laporan']);
            }
            try {
                $folderPath = "public/uploads/absensi/";
                Storage::delete($folderPath . $presensi->foto_in);
                Storage::delete($folderPath . $presensi->foto_out);
                $presensi->delete();
                return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
            } catch (\Exception $e) {
                return Redirect::back()->with(messageError($e->getMessage()));
            }
        } else {
            return Redirect::back()->with(messageError('Data Tidak Ditemukan'));
        }
    }

    /**
     * API endpoint untuk check status presensi karyawan hari ini
     * Digunakan oleh live-location-tracker untuk menentukan apakah tracking boleh berjalan
     * Accept parameter 'nik' untuk check employee tertentu, jika tidak ada check current user
     */
    public function checkStatus(Request $request)
    {
        try {
            // Get NIK from request parameter
            $nik = $request->input('nik');

            // If NIK provided, check that employee, otherwise check current user
            if ($nik) {
                $karyawan = Karyawan::where('nik', $nik)->first();
                if (!$karyawan) {
                    return response()->json(['hasCheckedInToday' => false], 200);
                }
                $checkNik = $nik;
            } else {
                $user = auth()->user();
                $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();

                if (!$userkaryawan) {
                    return response()->json(['hasCheckedInToday' => false], 200);
                }

                $karyawan = Karyawan::where('nik', $userkaryawan->nik)->first();

                if (!$karyawan) {
                    return response()->json(['hasCheckedInToday' => false], 200);
                }

                $checkNik = $userkaryawan->nik;
            }

            // Cek presensi hari ini dengan jam_in ada dan jam_out belum ada
            $presensi = Presensi::where('nik', $checkNik)
                ->where('tanggal', date('Y-m-d'))
                ->whereNotNull('jam_in')
                ->whereNull('jam_out')
                ->first();

            return response()->json([
                'hasCheckedInToday' => $presensi ? true : false
            ], 200);

        } catch (\Exception $e) {
            // Jika ada error, return false untuk safety
            return response()->json(['hasCheckedInToday' => false], 200);
        }
    }
}
