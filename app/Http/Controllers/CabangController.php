<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Pengaturanumum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class CabangController extends Controller
{
    public function index(Request $request)
    {

        $query = Cabang::query();
        if (!empty($request->nama_cabang)) {
            $query->where('nama_cabang', 'like', '%' . $request->nama_cabang . '%');
        }
        $query->orderBy('kode_cabang');
        $cabang = $query->paginate(10);
        $cabang->appends(request()->all());
        return view('datamaster.cabang.index', compact('cabang'));
    }

    public function create()
    {
        // Ambil default timezone dari pengaturan umum
        $pengaturan = Pengaturanumum::where('id', 1)->first();
        $defaultTimezone = $pengaturan->timezone ?? 'Asia/Jakarta';
        
        return view('datamaster.cabang.create', compact('defaultTimezone'));
    }

    public function store(Request $request)
    {
        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_cabang' => [
                'required',
                'string',
                'max:3',
                'unique:cabang,kode_cabang'
            ],
            'nama_cabang' => [
                'required',
                'string',
                'max:50'
            ],
            'alamat_cabang' => [
                'required',
                'string',
                'max:100'
            ],
            'telepon_cabang' => [
                'required',
                'string',
                'max:13'
            ],
            'lokasi_cabang' => [
                'required',
                'string'
            ],
            'radius_cabang' => [
                'required',
                'integer',
                'min:1',
                'max:9999'
            ],
            'timezone' => [
                'required',
                'string',
                'max:50'
            ]
        ], [
            'kode_cabang.required' => 'Kode Cabang wajib diisi',
            'kode_cabang.max' => 'Kode Cabang maksimal 3 karakter',
            'kode_cabang.unique' => 'Kode Cabang sudah digunakan, silakan gunakan kode lain',
            'nama_cabang.required' => 'Nama Cabang wajib diisi',
            'nama_cabang.max' => 'Nama Cabang maksimal 50 karakter',
            'alamat_cabang.required' => 'Alamat Cabang wajib diisi',
            'alamat_cabang.max' => 'Alamat Cabang maksimal 100 karakter',
            'telepon_cabang.required' => 'Telepon Cabang wajib diisi',
            'telepon_cabang.max' => 'Telepon Cabang maksimal 13 karakter',
            'lokasi_cabang.required' => 'Lokasi Cabang wajib diisi',
            'radius_cabang.required' => 'Radius Cabang wajib diisi',
            'radius_cabang.integer' => 'Radius Cabang harus berupa angka',
            'radius_cabang.min' => 'Radius Cabang minimal 1 meter',
            'radius_cabang.max' => 'Radius Cabang maksimal 9999 meter'
        ]);

        try {
            // Cek duplicate sebelum insert
            $existing = Cabang::where('kode_cabang', $request->kode_cabang)->first();
            if ($existing) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Cabang sudah digunakan, silakan gunakan kode lain'));
            }

            // Trim whitespace
            $kode_cabang = strtoupper(trim($request->kode_cabang));
            $nama_cabang = trim($request->nama_cabang);
            $alamat_cabang = trim($request->alamat_cabang);
            $telepon_cabang = trim($request->telepon_cabang);
            $lokasi_cabang = trim($request->lokasi_cabang);

            // Validasi panjang setelah trim
            if (strlen($kode_cabang) > 3) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Cabang maksimal 3 karakter'));
            }

            if (strlen($nama_cabang) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Cabang maksimal 50 karakter'));
            }

            if (strlen($alamat_cabang) > 100) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Alamat Cabang maksimal 100 karakter'));
            }

            if (strlen($telepon_cabang) > 13) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Telepon Cabang maksimal 13 karakter'));
            }

            // Ambil default timezone jika tidak diisi
            $pengaturan = Pengaturanumum::where('id', 1)->first();
            $timezone = $request->timezone ?? ($pengaturan->timezone ?? 'Asia/Jakarta');

            // Simpan Data Cabang
            Cabang::create([
                'kode_cabang' => $kode_cabang,
                'nama_cabang' => $nama_cabang,
                'alamat_cabang' => $alamat_cabang,
                'telepon_cabang' => $telepon_cabang,
                'lokasi_cabang' => $lokasi_cabang,
                'radius_cabang' => $request->radius_cabang,
                'timezone' => $timezone,
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Cabang sudah digunakan, silakan gunakan kode lain'));
            } elseif (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang. Pastikan panjang data sesuai batas maksimal'));
            } else {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Terjadi kesalahan: ' . $errorMessage));
            }
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with(messageError('Terjadi kesalahan: ' . $e->getMessage()));
        }
    }


    public function edit($kode_cabang)
    {
        $kode_cabang = Crypt::decrypt($kode_cabang);
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        
        // Ambil default timezone dari pengaturan umum
        $pengaturan = Pengaturanumum::where('id', 1)->first();
        $defaultTimezone = $pengaturan->timezone ?? 'Asia/Jakarta';
        
        return view('datamaster.cabang.edit', compact('cabang', 'defaultTimezone'));
    }


    public function update(Request $request, $kode_cabang)
    {
        $kode_cabang = Crypt::decrypt($kode_cabang);

        // Validasi dengan pesan error yang jelas
        $request->validate([
            'nama_cabang' => [
                'required',
                'string',
                'max:50'
            ],
            'alamat_cabang' => [
                'required',
                'string',
                'max:100'
            ],
            'telepon_cabang' => [
                'required',
                'string',
                'max:13'
            ],
            'lokasi_cabang' => [
                'required',
                'string'
            ],
            'radius_cabang' => [
                'required',
                'integer',
                'min:1',
                'max:9999'
            ],
            'timezone' => [
                'required',
                'string',
                'max:50'
            ]
        ], [
            'nama_cabang.required' => 'Nama Cabang wajib diisi',
            'nama_cabang.max' => 'Nama Cabang maksimal 50 karakter',
            'alamat_cabang.required' => 'Alamat Cabang wajib diisi',
            'alamat_cabang.max' => 'Alamat Cabang maksimal 100 karakter',
            'telepon_cabang.required' => 'Telepon Cabang wajib diisi',
            'telepon_cabang.max' => 'Telepon Cabang maksimal 13 karakter',
            'lokasi_cabang.required' => 'Lokasi Cabang wajib diisi',
            'radius_cabang.required' => 'Radius Cabang wajib diisi',
            'radius_cabang.integer' => 'Radius Cabang harus berupa angka',
            'radius_cabang.min' => 'Radius Cabang minimal 1 meter',
            'radius_cabang.max' => 'Radius Cabang maksimal 9999 meter',
            'timezone.required' => 'Zona Waktu Cabang wajib dipilih',
            'timezone.max' => 'Zona Waktu Cabang maksimal 50 karakter'
        ]);

        try {
            // Trim whitespace
            $nama_cabang = trim($request->nama_cabang);
            $alamat_cabang = trim($request->alamat_cabang);
            $telepon_cabang = trim($request->telepon_cabang);
            $lokasi_cabang = trim($request->lokasi_cabang);

            // Validasi panjang setelah trim
            if (strlen($nama_cabang) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Cabang maksimal 50 karakter'));
            }

            if (strlen($alamat_cabang) > 100) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Alamat Cabang maksimal 100 karakter'));
            }

            if (strlen($telepon_cabang) > 13) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Telepon Cabang maksimal 13 karakter'));
            }

            // Ambil default timezone jika tidak diisi
            $pengaturan = Pengaturanumum::where('id', 1)->first();
            $timezone = $request->timezone ?? ($pengaturan->timezone ?? 'Asia/Jakarta');

            // Update Data Cabang
            Cabang::where('kode_cabang', $kode_cabang)->update([
                'nama_cabang' => $nama_cabang,
                'alamat_cabang' => $alamat_cabang,
                'telepon_cabang' => $telepon_cabang,
                'lokasi_cabang' => $lokasi_cabang,
                'radius_cabang' => $request->radius_cabang,
                'timezone' => $timezone,
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang. Pastikan panjang data sesuai batas maksimal'));
            } else {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Terjadi kesalahan: ' . $errorMessage));
            }
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with(messageError('Terjadi kesalahan: ' . $e->getMessage()));
        }
    }

    public function destroy($kode_cabang)
    {
        $kode_cabang = Crypt::decrypt($kode_cabang);
        try {
            Cabang::where('kode_cabang', $kode_cabang)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
