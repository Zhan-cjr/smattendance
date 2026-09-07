<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Facerecognition;
use App\Models\Jabatan;
use App\Models\Jamkerja;
use App\Models\Karyawan;
use App\Models\MutasiKaryawan;
use App\Models\Pengaturanumum;
use App\Models\Setjamkerjabydate;
use App\Models\Setjamkerjabyday;
use App\Models\Statuskawin;
use App\Models\Statuskaryawan;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Imports\KaryawanImport;
use App\Exports\TemplateKaryawanExport;
use App\Exports\KaryawanExport;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $query = Karyawan::query();
        $latest_gaji = DB::table('karyawan_gaji_pokok')
            ->select('nik', 'jenis_upah')
            ->whereIn('kode_gaji', function ($query) {
                $query->select(DB::raw('MAX(kode_gaji)'))
                    ->from('karyawan_gaji_pokok')
                    ->groupBy('nik');
            });

        $query->select('karyawan.*', 'departemen.nama_dept', 'jabatan.nama_jabatan', 'cabang.nama_cabang', 'id_user', 'gaji.jenis_upah');
        $query->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $query->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang');
        $query->leftJoin('users_karyawan', 'karyawan.nik', '=', 'users_karyawan.nik');
        $query->leftJoinSub($latest_gaji, 'gaji', function ($join) {
            $join->on('karyawan.nik', '=', 'gaji.nik');
        });

        // Filter berdasarkan akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            // Ambil kode cabang dan departemen yang diakses user
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            // Filter berdasarkan cabang yang diakses
            if (!empty($userCabangs)) {
                $query->whereIn('karyawan.kode_cabang', $userCabangs);
            } else {
                // Jika tidak ada akses cabang, tidak tampilkan data
                $query->whereRaw('1 = 0');
            }

            // Filter berdasarkan departemen yang diakses
            if (!empty($userDepartemens)) {
                $query->whereIn('karyawan.kode_dept', $userDepartemens);
            } else {
                // Jika tidak ada akses departemen, tidak tampilkan data
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }
        if (!empty($request->kode_group)) {
            $query->where('karyawan.kode_group', $request->kode_group);
        }

        if (!empty($request->nama_karyawan)) {
            $query->where('nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }
        $query->orderBy('nama_karyawan', 'asc');
        $karyawan = $query->paginate(15);
        $karyawan->appends($request->all());

        $data['karyawan'] = $karyawan;
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();

        return view('datamaster.karyawan.index', $data);
    }


    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Generate NIK berikutnya dengan format XX.XX.XXX
        $last = Karyawan::orderBy('nik', 'desc')->first();

        $nextNumber = 1; // default untuk karyawan pertama
        if ($last) {
            // Ambil nik terakhir, hapus titik, convert ke int
            $lastNikNumeric = (int)str_replace('.', '', $last->nik);
            $nextNumber = $lastNikNumeric + 1;
        }

        // Format kembali ke XX.XX.XXX
        $nikString = str_pad((string)$nextNumber, 7, '0', STR_PAD_LEFT);
        $nextNik = substr($nikString, 0, 2) . '.' . substr($nikString, 2, 2) . '.' . substr($nikString, 4, 3);

        $data['next_nik'] = $nextNik;
        $data['status_kawin'] = Statuskawin::orderBy('kode_status_kawin')->get();
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        $data['jabatan'] = Jabatan::orderBy('kode_jabatan')->get();
        $data['status_karyawan'] = Statuskaryawan::orderBy('kode_status_karyawan')->get();
        return view('datamaster.karyawan.create', $data);
    }


    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $request->validate([
            // nik akan digenerate otomatis; user mengisi nik_show
            'nik_show' => 'required',
            'no_ktp' => 'required',
            'nama_karyawan' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
            'no_hp' => 'required',
            'kode_status_kawin' => 'required',
            'pendidikan_terakhir' => 'required',
            'kode_cabang' => 'required',
            'kode_dept' => 'required',
            'kode_jabatan' => 'required',
            'tanggal_masuk' => 'required',
            'status_karyawan' => 'required'
        ]);

        // Validasi akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($request->kode_cabang, $userCabangs)) {
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke cabang yang dipilih'));
            }

            if (!in_array($request->kode_dept, $userDepartemens)) {
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke departemen yang dipilih'));
            }
        }

        try {
            // Generate NIK format XX.XX.XXX dengan increment otomatis jika ada bentrok
            $last = Karyawan::orderBy('nik', 'desc')->first();

            $nextNumber = 1; // default untuk karyawan pertama
            if ($last) {
                // Ambil nik terakhir, hapus titik, convert ke int
                $lastNikNumeric = (int)str_replace('.', '', $last->nik);
                $nextNumber = $lastNikNumeric + 1;
            }
            
            // Generate NIK dan handle duplikat dengan increment otomatis
            $nikAuto = null;
            $maxAttempts = 9999999; // maksimal attempts untuk mencegah infinite loop
            while ($maxAttempts > 0) {
                // Format nomor ke XX.XX.XXX
                $nikString = str_pad((string)$nextNumber, 7, '0', STR_PAD_LEFT);
                $potentialNik = substr($nikString, 0, 2) . '.' . substr($nikString, 2, 2) . '.' . substr($nikString, 4, 3);
                
                // Cek apakah NIK sudah ada di database
                if (!Karyawan::where('nik', $potentialNik)->exists()) {
                    $nikAuto = $potentialNik;
                    break;
                }
                $nextNumber++;
                $maxAttempts--;
            }
            
            // Jika tidak bisa generate NIK, throw exception
            if (!$nikAuto) {
                throw new \Exception('Gagal generate NIK - maksimal nomor urut tercapai');
            }
            $data_foto = [];
            if ($request->hasfile('foto')) {
                $foto_name =  $nikAuto . "." . $request->file('foto')->getClientOriginalExtension();
                $destination_foto_path = "/public/karyawan";
                $foto = $foto_name;
                $data_foto = [
                    'foto' => $foto
                ];
            }
            $data_karyawan = [
                'nik' => $nikAuto,
                'nik_show' => $request->nik_show,
                'no_ktp' => $request->no_ktp,
                'nama_karyawan' => $request->nama_karyawan,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_hp' => $request->no_hp,
                'kode_status_kawin' => $request->kode_status_kawin,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'kode_cabang' => $request->kode_cabang,
                'kode_dept' => $request->kode_dept,
                'kode_jabatan' => $request->kode_jabatan,
                'tanggal_masuk' => $request->tanggal_masuk,
                'status_karyawan' => $request->status_karyawan,
                'lock_location' => 1,
                'status_aktif_karyawan' => 1,
                'rfid_uid' => $request->rfid_uid,
                'password' => Hash::make('12345')
            ];
            $data = array_merge($data_karyawan, $data_foto);
            $simpan = Karyawan::create($data);
            if ($simpan) {
                if ($request->hasfile('foto')) {
                    if (!Storage::exists($destination_foto_path)) {
                        Storage::makeDirectory($destination_foto_path, 0775, true);
                        // Explicit chmod to ensure permissions are correct on some hosting environments
                        $path = Storage::path($destination_foto_path);
                        chmod($path, 0775);
                    }
                    $request->file('foto')->storeAs($destination_foto_path, $foto_name);
                }
            }
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function edit($nik)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $nik = Crypt::decrypt($nik);
        $data['karyawan'] = Karyawan::where('nik', $nik)->first();
        $data['status_kawin'] = Statuskawin::orderBy('kode_status_kawin')->get();
        $data['cabang'] = $user->getCabang();
        $data['departemen'] = $user->getDepartemen();
        $data['jabatan'] = Jabatan::orderBy('kode_jabatan')->get();
        $data['status_karyawan'] = Statuskaryawan::orderBy('kode_status_karyawan')->get();
        return view('datamaster.karyawan.edit', $data);
    }


    public function update($nik, Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $nik = Crypt::decrypt($nik);
        $request->validate([
            // nik tetap primary key dan tidak diedit; nik_show yang bisa diubah
            'nik_show' => 'required',
            'no_ktp' => 'required',
            'nama_karyawan' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
            'no_hp' => 'required',
            'kode_status_kawin' => 'required',
            'pendidikan_terakhir' => 'required',
            'kode_cabang' => 'required',
            'kode_dept' => 'required',
            'kode_jabatan' => 'required',
            'tanggal_masuk' => 'required',
            'status_karyawan' => 'required'
        ]);

        // Validasi akses cabang dan departemen jika bukan super admin
        if (!$user->isSuperAdmin()) {
            $userCabangs = $user->getCabangCodes();
            $userDepartemens = $user->getDepartemenCodes();

            if (!in_array($request->kode_cabang, $userCabangs)) {
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke cabang yang dipilih'));
            }

            if (!in_array($request->kode_dept, $userDepartemens)) {
                return Redirect::back()->with(messageError('Anda tidak memiliki akses ke departemen yang dipilih'));
            }
        }

        try {
            $karyawan = Karyawan::where('nik', $nik)->first();
            $data_foto = [];
            if ($request->hasfile('foto')) {
                $foto_name =  $nik . "." . $request->file('foto')->getClientOriginalExtension();
                $destination_foto_path = "/public/karyawan";
                $foto = $foto_name;
                $data_foto = [
                    'foto' => $foto
                ];
            }

            $data_karyawan = [
                // 'nik' tidak diubah di update
                'nik_show' => $request->nik_show,
                'no_ktp' => $request->no_ktp,
                'nama_karyawan' => $request->nama_karyawan,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'alamat' => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'no_hp' => $request->no_hp,
                'kode_status_kawin' => $request->kode_status_kawin,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'kode_cabang' => $request->kode_cabang,
                'kode_dept' => $request->kode_dept,
                'kode_jabatan' => $request->kode_jabatan,
                'tanggal_masuk' => $request->tanggal_masuk,
                'status_karyawan' => $request->status_karyawan,
                'status_aktif_karyawan' => $request->status_aktif_karyawan,
                'rfid_uid' => $request->rfid_uid,
                'pin' => $request->pin
            ];

            $data = array_merge($data_karyawan, $data_foto);
            $simpan = Karyawan::where('nik', $nik)->update($data);
            if ($simpan) {
                $user_karyawan = Userkaryawan::where('nik', $nik)->first();
                if ($user_karyawan) {
                    User::where('id', $user_karyawan->id_user)->update([
                        'name' => $request->nama_karyawan
                    ]);
                }

                if ($request->hasfile('foto')) {
                    if (!Storage::exists($destination_foto_path)) {
                        Storage::makeDirectory($destination_foto_path, 0775, true);
                        // Explicit chmod to ensure permissions are correct on some hosting environments
                        $path = Storage::path($destination_foto_path);
                        chmod($path, 0775);
                    }
                    Storage::delete($destination_foto_path . "/" . $karyawan->foto);
                    $request->file('foto')->storeAs($destination_foto_path, $foto_name);
                }
            }
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function lockunlocklocation($nik)
    {
        $nik = Crypt::decrypt($nik);
        try {
            $karyawan = Karyawan::where('nik', $nik)->first();
            if ($karyawan->lock_location == '1') {
                $lock_location = 0;
            } else {
                $lock_location = 1;
            }

            Karyawan::where('nik', $nik)->update([
                'lock_location' => $lock_location
            ]);
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function lockunlockjamkerja($nik)
    {
        $nik = Crypt::decrypt($nik);
        try {
            $karyawan = Karyawan::where('nik', $nik)->first();
            if ($karyawan->lock_jam_kerja == '1') {
                $lock_jam_kerja = 0;
            } else {
                $lock_jam_kerja = 1;
            }

            Karyawan::where('nik', $nik)->update([
                'lock_jam_kerja' => $lock_jam_kerja
            ]);
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function lockunlockfacerecognition($nik)
    {
        $nik = Crypt::decrypt($nik);
        try {
            $karyawan = Karyawan::where('nik', $nik)->first();
            if ($karyawan->is_niqab == '0') {
                $is_niqab = 1;
            } else {
                $is_niqab = 0;
            }

            Karyawan::where('nik', $nik)->update([
                'is_niqab' => $is_niqab
            ]);
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function show($nik)
    {
        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('nik', $nik)
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('status_kawin', 'karyawan.kode_status_kawin', '=', 'status_kawin.kode_status_kawin')

            ->first();
        $user_karyawan = Userkaryawan::where('nik', $nik)->first();
        $user = $user_karyawan ? User::where('id', $user_karyawan->id_user)->first() : null;
        $karyawan_wajah = Facerecognition::where('nik', $nik)->get();
        $mutasi = MutasiKaryawan::with(['cabangLama', 'cabangBaru', 'deptLama', 'deptBaru', 'jabatanLama', 'jabatanBaru'])
            ->where('nik', $nik)
            ->orderBy('tanggal_mutasi', 'desc')
            ->get();
            
        $data['karyawan'] = $karyawan;
        $data['user'] = $user;
        $data['karyawan_wajah'] = $karyawan_wajah;
        $data['mutasi'] = $mutasi;
        return view('datamaster.karyawan.show', $data);
    }


    public function destroy($nik)
    {
        $nik = Crypt::decrypt($nik);
        try {
            $karyawan = Karyawan::where('nik', $nik)->first();
            $user_karyawan = Userkaryawan::where('nik', $nik)->first();
            if (!empty($user_karyawan)) {
                User::where('id', $user_karyawan->id_user)->delete();
                Userkaryawan::where('nik', $nik)->delete();
            }
            //$facerecognition = Facerecognition::where('nik', $nik)->get();
            // foreach ($facerecognition as $fr) {
            //     $nama_file = $facerecognition->wajah;
            //     $nama_folder = $karyawan->nik . "-" . getNamaDepan(strtolower($karyawan->nama_karyawan));
            //     $path = 'public/uploads/facerecognition/' . $nama_folder . "/" . $nama_file;
            //     Storage::delete($path);
            // }

            $nama_folder = $karyawan->nik . "-" . getNamaDepan(strtolower($karyawan->nama_karyawan));
            $path_folder = 'public/uploads/facerecognition/' . $nama_folder;
            Storage::deleteDirectory($path_folder);


            $nama_file_foto = $karyawan->foto;
            $path_foto = '/public/karyawan/' . $nama_file_foto;
            Storage::delete($path_foto);
            Karyawan::where('nik', $nik)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function setjamkerja($nik)
    {
        $nik = Crypt::decrypt($nik);
        $data['karyawan'] = Karyawan::where('nik', $nik)
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        $data['list_bulan'] = config('global.list_bulan');
        $data['start_year'] = config('global.start_year');
        $data['jamkerja'] = Jamkerja::orderBy('kode_jam_kerja')->get();
        $data['jamkerjabyday'] = Setjamkerjabyday::where('nik', $nik)->pluck('kode_jam_kerja', 'hari')->toArray();
        // dd($data['jamkerjabyday']);
        return view('datamaster.karyawan.setjamkerja', $data);
    }

    public function setcabang($nik)
    {
        $nik = Crypt::decrypt($nik);
        $data['karyawan'] = Karyawan::where('nik', $nik)
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->first();
        // Exclude cabang utama dari pilihan
        $data['cabang'] = Cabang::where('kode_cabang', '!=', $data['karyawan']->kode_cabang)->orderBy('kode_cabang')->get();
        $data['kode_cabang_array'] = $data['karyawan']->kode_cabang_array ?? [];
        return view('datamaster.karyawan.setcabang', $data);
    }

    public function storecabang(Request $request, $nik)
    {
        $nik = Crypt::decrypt($nik);
        try {
            // Ambil cabang utama karyawan
            $karyawan = Karyawan::where('nik', $nik)->first();
            $kode_cabang_utama = $karyawan->kode_cabang;

            // Gabungkan cabang utama dengan cabang yang dipilih
            $kode_cabang_array = $request->kode_cabang_array ?? [];
            $kode_cabang_array[] = $kode_cabang_utama; // Tambahkan cabang utama
            $kode_cabang_array = array_unique($kode_cabang_array); // Hapus duplikasi

            Karyawan::where('nik', $nik)->update([
                'kode_cabang_array' => $kode_cabang_array
            ]);
            return Redirect::back()->with(messageSuccess('Data Cabang Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function storejamkerjabyday(Request $request, $nik)
    {
        $nik = Crypt::decrypt($nik);
        $hari = $request->hari;
        $kode_jam_kerja = $request->kode_jam_kerja;
        DB::beginTransaction();
        try {
            Setjamkerjabyday::where('nik', $nik)->delete();
            for ($i = 0; $i < count($hari); $i++) {
                if (!empty($kode_jam_kerja[$i])) {
                    Setjamkerjabyday::create([
                        'nik' => $nik,
                        'hari' => $hari[$i],
                        'kode_jam_kerja' => $kode_jam_kerja[$i]
                    ]);
                }
            }
            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function storejamkerjabydate(Request $request)
    {
        // Convert tanggal to proper format (YYYY-MM-DD) to avoid timezone issues
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');

        try {
            $cek = Setjamkerjabydate::where('nik', $request->nik)->where('tanggal', $tanggal)->first();
            if (!empty($cek)) {
                // Update jika sudah ada
                Setjamkerjabydate::where('nik', $request->nik)->where('tanggal', $tanggal)->update([
                    'kode_jam_kerja' => $request->kode_jam_kerja
                ]);
                return response()->json(['success' => true, 'message' => 'Data Berhasil Diupdate']);
            }

            // Simpan baru
            Setjamkerjabydate::create([
                'nik' => $request->nik,
                'tanggal' => $tanggal,
                'kode_jam_kerja' => $request->kode_jam_kerja
            ]);

            return response()->json(['success' => true, 'message' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getjamkerjabydate(Request $request)
    {
        $nik = $request->nik;
        $tanggal = $request->tanggal;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $jamkerjabydate = Setjamkerjabydate::where('nik', $nik)
            ->join('presensi_jamkerja', 'presensi_jamkerja.kode_jam_kerja', '=', 'presensi_jamkerja_bydate.kode_jam_kerja')
            ->whereRaw('MONTH(tanggal) = ' . $bulan . ' AND YEAR(tanggal) = ' . $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();


        return response()->json($jamkerjabydate);
    }

    public function deletejamkerjabydate(Request $request)
    {
        // Convert tanggal to proper format (YYYY-MM-DD) to avoid timezone issues
        $tanggal = Carbon::parse($request->tanggal)->format('Y-m-d');

        try {
            Setjamkerjabydate::where('nik', $request->nik)->where('tanggal', $tanggal)->delete();
            return response()->json(['success' => true, 'message' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function createuser($nik)
    {
        $generalsetting = Pengaturanumum::first();
        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('nik', $nik)->first();
        DB::beginTransaction();
        try {
            //code...
            $user = User::create([
                'name' => $karyawan->nama_karyawan,
                'username' => $karyawan->nik,
                'password' => Hash::make($karyawan->nik),
                'email' => strtolower(removeTitik($karyawan->nik)) . '@' . $generalsetting->domain_email,
            ]);

            Userkaryawan::create([
                'nik' => $nik,
                'id_user' => $user->id
            ]);

            $user->assignRole('karyawan');
            DB::commit();
            return Redirect::route('karyawan.index')->with(messageSuccess('User Berhasil Dibuat'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function generateAllUser()
    {
        $generalsetting = Pengaturanumum::first();
        // Get all active employees who don't have a user yet
        // Since 'user' relation might not exist in Karyawan model, we use a left join check or explicit query
        $karyawan = Karyawan::where('status_aktif_karyawan', 1)
            ->leftJoin('users_karyawan', 'karyawan.nik', '=', 'users_karyawan.nik')
            ->whereNull('users_karyawan.id_user')
            ->select('karyawan.*')
            ->get();

        if ($karyawan->isEmpty()) {
            return Redirect::back()->with(messageError('Tidak ada karyawan aktif yang belum memiliki user'));
        }

        DB::beginTransaction();
        try {
            $count = 0;
            foreach ($karyawan as $k) {
                // Check if user already exists (double check)
                $existingUser = Userkaryawan::where('nik', $k->nik)->first();
                if (!$existingUser) {
                    $user = User::create([
                        'name' => $k->nama_karyawan,
                        'username' => $k->nik,
                        'password' => Hash::make($k->nik),
                        'email' => strtolower(removeTitik($k->nik)) . '@' . $generalsetting->domain_email,
                    ]);

                    Userkaryawan::create([
                        'nik' => $k->nik,
                        'id_user' => $user->id
                    ]);

                    $user->assignRole('karyawan');
                    $count++;
                }
            }

            DB::commit();
            return Redirect::back()->with(messageSuccess($count . ' User Berhasil Dibuat'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function deleteAllUser()
    {
        DB::beginTransaction();
        try {
            $users = User::role('karyawan')->get();
            $count = $users->count();

            foreach ($users as $user) {
                Userkaryawan::where('id_user', $user->id)->delete();
                $user->delete();
            }

            DB::commit();
            return Redirect::back()->with(messageSuccess($count . ' User Berhasil Dihapus'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function deleteuser($nik)
    {
        $nik = Crypt::decrypt($nik);
        try {
            $user_karyawan = Userkaryawan::where('nik', $nik)->first();
            User::where('id', $user_karyawan->id_user)->delete();
            Userkaryawan::where('nik', $nik)->delete();
            return Redirect::back()->with(messageSuccess('User Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Data User gagal dihapus ' . $e->getMessage()));
        }
    }

    public function import()
    {
        $status_kawin = Statuskawin::orderBy('kode_status_kawin')->get();
        return view('datamaster.karyawan.import_modal', compact('status_kawin'));
    }

    public function download_template()
    {
        return Excel::download(new TemplateKaryawanExport, 'template_import_karyawan.xlsx');
    }

    public function import_proses(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file');
            Excel::import(new KaryawanImport, $file);
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diimport'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        return Excel::download(new KaryawanExport($request->all()), 'karyawan_export.xlsx');
    }


    public function getkaryawan(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $kode_dept = $request->kode_dept;
        $jenis_upah = $request->jenis_upah;
        $tanggal = $request->tanggal;
        $q = $request->q;

        $query = Karyawan::query();

        if (!empty($kode_cabang)) {
            $query->where('karyawan.kode_cabang', $kode_cabang);
        }

        if (!empty($kode_dept)) {
            $query->where('karyawan.kode_dept', $kode_dept);
        }

        if (!empty($jenis_upah)) {
            $tanggal_filter = $tanggal ?? date('Y-m-d');
            $latest_gaji = DB::table('karyawan_gaji_pokok as gp1')
                ->select('nik', 'jenis_upah')
                ->where('tanggal_berlaku', function ($sub) use ($tanggal_filter) {
                    $sub->selectRaw('MAX(tanggal_berlaku)')
                        ->from('karyawan_gaji_pokok as gp2')
                        ->whereColumn('gp2.nik', 'gp1.nik')
                        ->where('gp2.tanggal_berlaku', '<=', $tanggal_filter);
                });

            $query->joinSub($latest_gaji, 'gaji', function ($join) {
                $join->on('karyawan.nik', '=', 'gaji.nik');
            })->where('gaji.jenis_upah', $jenis_upah);
        }

        if (!empty($q)) {
            $query->where(function ($query) use ($q) {
                $query->where('nama_karyawan', 'like', '%' . $q . '%')
                    ->orWhere('karyawan.nik', 'like', '%' . $q . '%');
            });
        }

        $karyawan = $query->orderBy('nama_karyawan')->get();
        return response()->json($karyawan);
    }

    public function idcard($nik)
    {
        $nik = Crypt::decrypt($nik);
        $karyawan = Karyawan::where('karyawan.nik', $nik)
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select('karyawan.*', 'departemen.nama_dept', 'jabatan.nama_jabatan', 'cabang.nama_cabang')
            ->first();
        $data['karyawan'] = $karyawan;
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $data['generalsetting'] = $generalsetting;
        return view('datamaster.karyawan.idcard', $data);
    }

    public function getkaryawantable(Request $request)
    {
        $q = $request->q;
        $query = Karyawan::query()->with(['jabatan', 'cabang', 'departemen']);
        if (!empty($q)) {
            $query->where(function($query) use ($q) {
                $query->where('nama_karyawan', 'like', '%' . $q . '%')
                      ->orWhere('nik', 'like', '%' . $q . '%');
            });
        }
        $karyawan = $query->orderBy('nama_karyawan')->limit(20)->get();
        return view('datamaster.karyawan.getkaryawantable', compact('karyawan'));
    }
}
