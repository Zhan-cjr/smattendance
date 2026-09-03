<?php

namespace App\Http\Controllers;

use App\Models\LemburAturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class LemburaturanController extends Controller
{
    public function index()
    {
        $data['aturan_kerja'] = LemburAturan::where('tipe_hari', '1')->orderBy('jam_dari')->get();
        $data['aturan_libur'] = LemburAturan::where('tipe_hari', '2')->orderBy('jam_dari')->get();
        return view('konfigurasi.lembur_aturan.index', $data);
    }

    public function create(Request $request)
    {
        $data['tipe_hari'] = $request->tipe_hari;
        return view('konfigurasi.lembur_aturan.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_hari' => 'required',
            'jam_dari' => 'required|numeric',
            'jam_sampai' => 'nullable|numeric',
            'faktor' => 'required|numeric',
        ]);

        try {
            LemburAturan::create([
                'tipe_hari' => $request->tipe_hari,
                'jam_dari' => $request->jam_dari,
                'jam_sampai' => $request->jam_sampai,
                'faktor' => $request->faktor,
            ]);
            return Redirect::back()->with(messageSuccess('Aturan Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $data['aturan'] = LemburAturan::find($id);
        return view('konfigurasi.lembur_aturan.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipe_hari' => 'required',
            'jam_dari' => 'required|numeric',
            'jam_sampai' => 'nullable|numeric',
            'faktor' => 'required|numeric',
        ]);

        try {
            LemburAturan::where('id', $id)->update([
                'tipe_hari' => $request->tipe_hari,
                'jam_dari' => $request->jam_dari,
                'jam_sampai' => $request->jam_sampai,
                'faktor' => $request->faktor,
            ]);
            return Redirect::back()->with(messageSuccess('Aturan Berhasil Diupdate'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroy($id)
    {
        try {
            LemburAturan::where('id', $id)->delete();
            return Redirect::back()->with(messageSuccess('Aturan Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
