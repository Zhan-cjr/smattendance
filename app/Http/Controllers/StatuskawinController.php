<?php

namespace App\Http\Controllers;

use App\Models\Statuskawin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class StatuskawinController extends Controller
{
    public function index(Request $request)
    {
        $query = Statuskawin::query();
        if (!empty($request->status_kawin)) {
            $query->where('status_kawin', 'like', '%' . $request->status_kawin . '%');
        }
        $data['statuskawin'] = $query->orderBy('kode_status_kawin')->get();
        return view('datamaster.status_kawin.index', $data);
    }

    public function create()
    {
        return view('datamaster.status_kawin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_status_kawin' => 'required|string|max:5|unique:status_kawin,kode_status_kawin',
            'status_kawin' => 'required|string|max:30'
        ], [
            'kode_status_kawin.required' => 'Kode Status Kawin wajib diisi',
            'kode_status_kawin.max' => 'Kode Status Kawin maksimal 5 karakter',
            'kode_status_kawin.unique' => 'Kode Status Kawin sudah digunakan',
            'status_kawin.required' => 'Status Kawin wajib diisi',
            'status_kawin.max' => 'Status Kawin maksimal 30 karakter'
        ]);

        try {
            Statuskawin::create([
                'kode_status_kawin' => strtoupper($request->kode_status_kawin),
                'status_kawin' => $request->status_kawin
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with(['error' => $e->getMessage()]);
        }
    }

    public function edit($kode_status_kawin)
    {
        $kode_status_kawin = Crypt::decrypt($kode_status_kawin);
        $data['statuskawin'] = Statuskawin::where('kode_status_kawin', $kode_status_kawin)->first();
        return view('datamaster.status_kawin.edit', $data);
    }

    public function update($kode_status_kawin, Request $request)
    {
        $kode_status_kawin_old = Crypt::decrypt($kode_status_kawin);
        
        $request->validate([
            'kode_status_kawin' => 'required|string|max:5|unique:status_kawin,kode_status_kawin,' . $kode_status_kawin_old . ',kode_status_kawin',
            'status_kawin' => 'required|string|max:30'
        ], [
            'kode_status_kawin.required' => 'Kode Status Kawin wajib diisi',
            'kode_status_kawin.max' => 'Kode Status Kawin maksimal 5 karakter',
            'kode_status_kawin.unique' => 'Kode Status Kawin sudah digunakan',
            'status_kawin.required' => 'Status Kawin wajib diisi',
            'status_kawin.max' => 'Status Kawin maksimal 30 karakter'
        ]);

        try {
            Statuskawin::where('kode_status_kawin', $kode_status_kawin_old)->update([
                'kode_status_kawin' => strtoupper($request->kode_status_kawin),
                'status_kawin' => $request->status_kawin
            ]);

            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with(['error' => $e->getMessage()]);
        }
    }

    public function destroy($kode_status_kawin)
    {
        $kode_status_kawin = Crypt::decrypt($kode_status_kawin);
        try {
            Statuskawin::where('kode_status_kawin', $kode_status_kawin)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }
}
