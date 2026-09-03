<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        $data['cuti'] = Cuti::orderBy('kode_cuti')->get();
        return view('datamaster.cuti.index', $data);
    }

    public function create()
    {
        return view('datamaster.cuti.create');
    }

    public function store(Request $request)
    {
        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_cuti' => [
                'required',
                'string',
                'max:3',
                'unique:cuti,kode_cuti'
            ],
            'jenis_cuti' => [
                'required',
                'string',
                'max:50'
            ],
            'jumlah_hari' => [
                'required',
                'integer',
                'min:1',
                'max:365'
            ]
        ], [
            'kode_cuti.required' => 'Kode Cuti wajib diisi',
            'kode_cuti.max' => 'Kode Cuti maksimal 3 karakter',
            'kode_cuti.unique' => 'Kode Cuti sudah digunakan, silakan gunakan kode lain',
            'jenis_cuti.required' => 'Jenis Cuti wajib diisi',
            'jenis_cuti.max' => 'Jenis Cuti maksimal 50 karakter',
            'jumlah_hari.required' => 'Jumlah Hari wajib diisi',
            'jumlah_hari.integer' => 'Jumlah Hari harus berupa angka',
            'jumlah_hari.min' => 'Jumlah Hari minimal 1 hari',
            'jumlah_hari.max' => 'Jumlah Hari maksimal 365 hari'
        ]);

        try {
            // Cek duplicate sebelum insert
            $existing = Cuti::where('kode_cuti', $request->kode_cuti)->first();
            if ($existing) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Cuti sudah digunakan, silakan gunakan kode lain'));
            }

            // Trim whitespace
            $kode_cuti = strtoupper(trim($request->kode_cuti));
            $jenis_cuti = trim($request->jenis_cuti);

            // Validasi panjang setelah trim
            if (strlen($kode_cuti) > 3) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Cuti maksimal 3 karakter'));
            }

            if (strlen($jenis_cuti) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Jenis Cuti maksimal 50 karakter'));
            }

            // Validasi jumlah hari
            $jumlah_hari = (int)$request->jumlah_hari;
            if ($jumlah_hari < 1 || $jumlah_hari > 365) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Jumlah Hari harus antara 1 sampai 365 hari'));
            }

            // Simpan Data Cuti
            Cuti::create([
                'kode_cuti' => $kode_cuti,
                'jenis_cuti' => $jenis_cuti,
                'jumlah_hari' => $jumlah_hari,
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Cuti sudah digunakan, silakan gunakan kode lain'));
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


    public function edit($kode_cuti)
    {
        $kode_cuti = Crypt::decrypt($kode_cuti);
        $data['cuti'] = Cuti::where('kode_cuti', $kode_cuti)->first();
        return view('datamaster.cuti.edit', $data);
    }

    public function update(Request $request, $kode_cuti)
    {
        $kode_cuti = Crypt::decrypt($kode_cuti);

        // Validasi dengan pesan error yang jelas
        $request->validate([
            'jenis_cuti' => [
                'required',
                'string',
                'max:50'
            ],
            'jumlah_hari' => [
                'required',
                'integer',
                'min:1',
                'max:365'
            ]
        ], [
            'jenis_cuti.required' => 'Jenis Cuti wajib diisi',
            'jenis_cuti.max' => 'Jenis Cuti maksimal 50 karakter',
            'jumlah_hari.required' => 'Jumlah Hari wajib diisi',
            'jumlah_hari.integer' => 'Jumlah Hari harus berupa angka',
            'jumlah_hari.min' => 'Jumlah Hari minimal 1 hari',
            'jumlah_hari.max' => 'Jumlah Hari maksimal 365 hari'
        ]);

        try {
            // Trim whitespace
            $jenis_cuti = trim($request->jenis_cuti);

            // Validasi panjang setelah trim
            if (strlen($jenis_cuti) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Jenis Cuti maksimal 50 karakter'));
            }

            // Validasi jumlah hari
            $jumlah_hari = (int)$request->jumlah_hari;
            if ($jumlah_hari < 1 || $jumlah_hari > 365) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Jumlah Hari harus antara 1 sampai 365 hari'));
            }

            // Update Data Cuti
            Cuti::where('kode_cuti', $kode_cuti)->update([
                'jenis_cuti' => $jenis_cuti,
                'jumlah_hari' => $jumlah_hari,
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

    function destroy($kode_cuti)
    {
        $kode_cuti = Crypt::decrypt($kode_cuti);
        try {
            Cuti::where('kode_cuti', $kode_cuti)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
