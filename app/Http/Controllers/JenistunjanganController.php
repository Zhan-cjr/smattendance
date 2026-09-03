<?php

namespace App\Http\Controllers;

use App\Models\Jenistunjangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class JenistunjanganController extends Controller
{
    public function index()
    {
        $data['jenistunjangan'] = Jenistunjangan::all();
        return view('datamaster.jenistunjangan.index', $data);
    }

    public function create()
    {
        return view('datamaster.jenistunjangan.create');
    }

    public function store(Request $request)
    {
        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_jenis_tunjangan' => [
                'required',
                'string',
                'max:4',
                'unique:jenis_tunjangan,kode_jenis_tunjangan'
            ],
            'jenis_tunjangan' => [
                'required',
                'string',
                'max:50'
            ]
        ], [
            'kode_jenis_tunjangan.required' => 'Kode Jenis Tunjangan wajib diisi',
            'kode_jenis_tunjangan.max' => 'Kode Jenis Tunjangan maksimal 4 karakter',
            'kode_jenis_tunjangan.unique' => 'Kode Jenis Tunjangan sudah digunakan, silakan gunakan kode lain',
            'jenis_tunjangan.required' => 'Jenis Tunjangan wajib diisi',
            'jenis_tunjangan.max' => 'Jenis Tunjangan maksimal 50 karakter'
        ]);

        try {
            // Cek duplicate sebelum insert
            $existing = Jenistunjangan::where('kode_jenis_tunjangan', $request->kode_jenis_tunjangan)->first();
            if ($existing) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jenis Tunjangan sudah digunakan, silakan gunakan kode lain'));
            }

            // Trim whitespace
            $kode_jenis_tunjangan = strtoupper(trim($request->kode_jenis_tunjangan));
            $jenis_tunjangan = trim($request->jenis_tunjangan);

            // Validasi panjang setelah trim
            if (strlen($kode_jenis_tunjangan) > 4) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jenis Tunjangan maksimal 4 karakter'));
            }

            if (strlen($jenis_tunjangan) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Jenis Tunjangan maksimal 50 karakter'));
            }

            // Simpan Data Jenis Tunjangan
            Jenistunjangan::create([
                'kode_jenis_tunjangan' => $kode_jenis_tunjangan,
                'jenis_tunjangan' => $jenis_tunjangan
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jenis Tunjangan sudah digunakan, silakan gunakan kode lain'));
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

    public function edit($kode_jenis_tunjangan)
    {
        $kode_jenis_tunjangan = Crypt::decrypt($kode_jenis_tunjangan);
        $data['jenistunjangan'] = Jenistunjangan::where('kode_jenis_tunjangan', $kode_jenis_tunjangan)->first();
        return view('datamaster.jenistunjangan.edit', $data);
    }


    public function update(Request $request, $kode_jenis_tunjangan)
    {
        $kode_jenis_tunjangan_old = Crypt::decrypt($kode_jenis_tunjangan);

        // Validasi dengan pesan error yang jelas
        $request->validate([
            'jenis_tunjangan' => [
                'required',
                'string',
                'max:50'
            ]
        ], [
            'jenis_tunjangan.required' => 'Jenis Tunjangan wajib diisi',
            'jenis_tunjangan.max' => 'Jenis Tunjangan maksimal 50 karakter'
        ]);

        try {
            // Trim whitespace
            $jenis_tunjangan = trim($request->jenis_tunjangan);

            // Validasi panjang setelah trim
            if (strlen($jenis_tunjangan) > 50) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Jenis Tunjangan maksimal 50 karakter'));
            }

            // Update Data Jenis Tunjangan
            Jenistunjangan::where('kode_jenis_tunjangan', $kode_jenis_tunjangan_old)->update([
                'jenis_tunjangan' => $jenis_tunjangan
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

    public function destroy($kode_jenis_tunjangan)
    {
        $kode_jenis_tunjangan = Crypt::decrypt($kode_jenis_tunjangan);
        try {
            Jenistunjangan::where('kode_jenis_tunjangan', $kode_jenis_tunjangan)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
