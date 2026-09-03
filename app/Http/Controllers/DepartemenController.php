<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class DepartemenController extends Controller
{

    public function index(Request $request)
    {
        $query = Departemen::query();
        if (!empty($request->nama_dept)) {
            $query->where('nama_dept', 'like', '%' . $request->nama_dept . '%');
        }
        $data['departemen'] = $query->orderBy('kode_dept')->get();
        return view('datamaster.departemen.index', $data);
    }

    public function create()
    {
        return view('datamaster.departemen.create');
    }

    public function store(Request $request)
    {
        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_dept' => [
                'required',
                'string',
                'max:3',
                'unique:departemen,kode_dept'
            ],
            'nama_dept' => [
                'required',
                'string',
                'max:30'
            ]
        ], [
            'kode_dept.required' => 'Kode Departemen wajib diisi',
            'kode_dept.max' => 'Kode Departemen maksimal 3 karakter',
            'kode_dept.unique' => 'Kode Departemen sudah digunakan, silakan gunakan kode lain',
            'nama_dept.required' => 'Nama Departemen wajib diisi',
            'nama_dept.max' => 'Nama Departemen maksimal 30 karakter'
        ]);

        try {
            // Cek duplicate sebelum insert
            $existing = Departemen::where('kode_dept', $request->kode_dept)->first();
            if ($existing) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Departemen sudah digunakan, silakan gunakan kode lain'));
            }

            // Trim whitespace
            $kode_dept = strtoupper(trim($request->kode_dept));
            $nama_dept = trim($request->nama_dept);

            // Validasi panjang setelah trim
            if (strlen($kode_dept) > 3) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Departemen maksimal 3 karakter'));
            }

            if (strlen($nama_dept) > 30) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Departemen maksimal 30 karakter'));
            }

            // Simpan Data Departemen
            Departemen::create([
                'kode_dept' => $kode_dept,
                'nama_dept' => $nama_dept
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Departemen sudah digunakan, silakan gunakan kode lain'));
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


    public function edit($kode_dept)
    {
        $kode_dept = Crypt::decrypt($kode_dept);
        $data['departemen'] = Departemen::where('kode_dept', $kode_dept)->first();
        return view('datamaster.departemen.edit', $data);
    }

    public function update($kode_dept, Request $request)
    {
        $kode_dept_old = Crypt::decrypt($kode_dept);

        // Validasi dengan pesan error yang jelas
        $request->validate([
            'kode_dept' => [
                'required',
                'string',
                'max:3',
                'unique:departemen,kode_dept,' . $kode_dept_old . ',kode_dept'
            ],
            'nama_dept' => [
                'required',
                'string',
                'max:30'
            ]
        ], [
            'kode_dept.required' => 'Kode Departemen wajib diisi',
            'kode_dept.max' => 'Kode Departemen maksimal 3 karakter',
            'kode_dept.unique' => 'Kode Departemen sudah digunakan, silakan gunakan kode lain',
            'nama_dept.required' => 'Nama Departemen wajib diisi',
            'nama_dept.max' => 'Nama Departemen maksimal 30 karakter'
        ]);

        try {
            // Trim whitespace
            $kode_dept_new = strtoupper(trim($request->kode_dept));
            $nama_dept = trim($request->nama_dept);

            // Validasi panjang setelah trim
            if (strlen($kode_dept_new) > 3) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Departemen maksimal 3 karakter'));
            }

            if (strlen($nama_dept) > 30) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Nama Departemen maksimal 30 karakter'));
            }

            // Cek duplicate jika kode berubah
            if ($kode_dept_new !== $kode_dept_old) {
                $existing = Departemen::where('kode_dept', $kode_dept_new)->first();
                if ($existing) {
                    return Redirect::back()
                        ->withInput()
                        ->with(messageError('Kode Departemen sudah digunakan, silakan gunakan kode lain'));
                }
            }

            // Update Data Departemen
            Departemen::where('kode_dept', $kode_dept_old)->update([
                'kode_dept' => $kode_dept_new,
                'nama_dept' => $nama_dept
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Duplicate entry')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Kode Departemen sudah digunakan, silakan gunakan kode lain'));
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

    public function destroy($kode_dept)
    {
        $kode_dept = Crypt::decrypt($kode_dept);
        try {
            Departemen::where('kode_dept', $kode_dept)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
