<?php

namespace App\Http\Controllers;

use App\Models\Statuskaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class StatuskaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Statuskaryawan::query();
        if (!empty($request->nama_status_karyawan)) {
            $query->where('nama_status_karyawan', 'like', '%' . $request->nama_status_karyawan . '%');
        }
        $data['statuskaryawan'] = $query->orderBy('kode_status_karyawan')->get();
        return view('datamaster.status_karyawan.index', $data);
    }

    public function create()
    {
        return view('datamaster.status_karyawan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_status_karyawan' => 'required|string|max:5|unique:status_karyawan,kode_status_karyawan',
            'nama_status_karyawan' => 'required|string|max:30'
        ], [
            'kode_status_karyawan.required' => 'Kode Status Karyawan wajib diisi',
            'kode_status_karyawan.max' => 'Kode Status Karyawan maksimal 5 karakter',
            'kode_status_karyawan.unique' => 'Kode Status Karyawan sudah digunakan',
            'nama_status_karyawan.required' => 'Nama Status Karyawan wajib diisi',
            'nama_status_karyawan.max' => 'Nama Status Karyawan maksimal 30 karakter'
        ]);

        try {
            Statuskaryawan::create([
                'kode_status_karyawan' => strtoupper($request->kode_status_karyawan),
                'nama_status_karyawan' => $request->nama_status_karyawan
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with(['error' => $e->getMessage()]);
        }
    }

    public function edit($kode_status_karyawan)
    {
        $kode_status_karyawan = Crypt::decrypt($kode_status_karyawan);
        $data['statuskaryawan'] = Statuskaryawan::where('kode_status_karyawan', $kode_status_karyawan)->first();
        return view('datamaster.status_karyawan.edit', $data);
    }

    public function update($kode_status_karyawan, Request $request)
    {
        $kode_status_karyawan_old = Crypt::decrypt($kode_status_karyawan);
        
        $request->validate([
            'kode_status_karyawan' => 'required|string|max:5|unique:status_karyawan,kode_status_karyawan,' . $kode_status_karyawan_old . ',kode_status_karyawan',
            'nama_status_karyawan' => 'required|string|max:30'
        ], [
            'kode_status_karyawan.required' => 'Kode Status Karyawan wajib diisi',
            'kode_status_karyawan.max' => 'Kode Status Karyawan maksimal 5 karakter',
            'kode_status_karyawan.unique' => 'Kode Status Karyawan sudah digunakan',
            'nama_status_karyawan.required' => 'Nama Status Karyawan wajib diisi',
            'nama_status_karyawan.max' => 'Nama Status Karyawan maksimal 30 karakter'
        ]);

        try {
            Statuskaryawan::where('kode_status_karyawan', $kode_status_karyawan_old)->update([
                'kode_status_karyawan' => strtoupper($request->kode_status_karyawan),
                'nama_status_karyawan' => $request->nama_status_karyawan
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with(['error' => $e->getMessage()]);
        }
    }

    public function destroy($kode_status_karyawan)
    {
        $kode_status_karyawan = Crypt::decrypt($kode_status_karyawan);
        try {
            Statuskaryawan::where('kode_status_karyawan', $kode_status_karyawan)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }
}
