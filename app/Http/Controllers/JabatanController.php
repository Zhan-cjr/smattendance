<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Jabatan::query();
        $data['jabatan'] = $query->get();
        return view('datamaster.jabatan.index', $data);
    }

    public function create()
    {
        return view('datamaster.jabatan.create');
    }

    public function store(Request $request)
    {
        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_jabatan' => [
                'required',
                'string',
                'max:3',
                'unique:jabatan,kode_jabatan'
            ],
            'nama_jabatan' => [
                'required',
                'string',
                'max:30'
            ]
        ], [
            'kode_jabatan.required' => 'Kode Jabatan wajib diisi',
            'kode_jabatan.max' => 'Kode Jabatan maksimal 3 karakter',
            'kode_jabatan.unique' => 'Kode Jabatan sudah digunakan, silakan gunakan kode lain',
            'nama_jabatan.required' => 'Nama Jabatan wajib diisi',
            'nama_jabatan.max' => 'Nama Jabatan maksimal 30 karakter'
        ]);

        try {
            // Cek duplicate sebelum insert
            $existing = Jabatan::where('kode_jabatan', $request->kode_jabatan)->first();
            if ($existing) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jabatan sudah digunakan, silakan gunakan kode lain'));
            }

            // Trim whitespace
            $kode_jabatan = strtoupper(trim($request->kode_jabatan));
            $nama_jabatan = trim($request->nama_jabatan);

            // Validasi panjang setelah trim
            if (strlen($kode_jabatan) > 3) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jabatan maksimal 3 karakter'));
            }

            if (strlen($nama_jabatan) > 30) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Jabatan maksimal 30 karakter'));
            }

            // Simpan Data Jabatan
            Jabatan::create([
                'kode_jabatan' => $kode_jabatan,
                'nama_jabatan' => $nama_jabatan
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jabatan sudah digunakan, silakan gunakan kode lain'));
            } elseif (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang. Kode maksimal 3 karakter, Nama maksimal 30 karakter'));
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


    public function edit($kode_jabatan)
    {
        $kode_jabatan = Crypt::decrypt($kode_jabatan);
        $data['jabatan'] = Jabatan::where('kode_jabatan', $kode_jabatan)->first();
        return view('datamaster.jabatan.edit', $data);
    }

    public function update($kode_jabatan, Request $request)
    {
        $kode_jabatan_old = Crypt::decrypt($kode_jabatan);

        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_jabatan' => [
                'required',
                'string',
                'max:3',
                'unique:jabatan,kode_jabatan,' . $kode_jabatan_old . ',kode_jabatan'
            ],
            'nama_jabatan' => [
                'required',
                'string',
                'max:30'
            ]
        ], [
            'kode_jabatan.required' => 'Kode Jabatan wajib diisi',
            'kode_jabatan.max' => 'Kode Jabatan maksimal 3 karakter',
            'kode_jabatan.unique' => 'Kode Jabatan sudah digunakan, silakan gunakan kode lain',
            'nama_jabatan.required' => 'Nama Jabatan wajib diisi',
            'nama_jabatan.max' => 'Nama Jabatan maksimal 30 karakter'
        ]);

        try {
            // Trim whitespace
            $kode_jabatan_new = strtoupper(trim($request->kode_jabatan));
            $nama_jabatan = trim($request->nama_jabatan);

            // Validasi panjang setelah trim
            if (strlen($kode_jabatan_new) > 3) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jabatan maksimal 3 karakter'));
            }

            if (strlen($nama_jabatan) > 30) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Jabatan maksimal 30 karakter'));
            }

            // Cek duplicate jika kode berubah
            if ($kode_jabatan_new !== $kode_jabatan_old) {
                $existing = Jabatan::where('kode_jabatan', $kode_jabatan_new)->first();
                if ($existing) {
                    return Redirect::back()
                        ->withInput()
                        ->with(messageError('Kode Jabatan sudah digunakan, silakan gunakan kode lain'));
                }
            }

            // Update Data Jabatan
            Jabatan::where('kode_jabatan', $kode_jabatan_old)->update([
                'kode_jabatan' => $kode_jabatan_new,
                'nama_jabatan' => $nama_jabatan
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Jabatan sudah digunakan, silakan gunakan kode lain'));
            } elseif (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang. Kode maksimal 3 karakter, Nama maksimal 30 karakter'));
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

    public function destroy($kode_jabatan)
    {
        $kode_jabatan = Crypt::decrypt($kode_jabatan);
        try {
            Jabatan::where('kode_jabatan', $kode_jabatan)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
