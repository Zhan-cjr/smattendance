<?php

namespace App\Http\Controllers;

use App\Models\Bpjskesehatan;
use App\Models\Cabang;
use App\Models\Departemen;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class BpjskesehatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Bpjskesehatan::query();
        $query->join('karyawan', 'karyawan_bpjskesehatan.nik', '=', 'karyawan.nik');
        $query->select('karyawan_bpjskesehatan.*', 'karyawan.nama_karyawan', 'karyawan.kode_dept', 'karyawan.kode_cabang', 'karyawan.nik_show');
        if (!empty($request->nama_karyawan)) {
            $query->where('karyawan.nama_karyawan', 'like', '%' . $request->nama_karyawan . '%');
        }

        if (!empty($request->kode_cabang)) {
            $query->where('karyawan.kode_cabang', $request->kode_cabang);
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }
        $bpjskesehatan = $query->paginate(20);
        $bpjskesehatan->appends($request->all());
        $data['bpjskesehatan'] = $bpjskesehatan;
        $data['departemen'] = Departemen::orderBy('kode_dept')->get();
        $data['cabang'] = Cabang::orderBy('kode_cabang')->get();
        return view('datamaster.bpjskesehatan.index', $data);
    }

    public function create()
    {
        $data['karyawan'] = Karyawan::orderby('nama_karyawan')->get();
        return view('datamaster.bpjskesehatan.create', $data);
    }

    public function store(Request $request)
    {
        // Validasi dengan pesan error yang jelas
        $request->validate([
            'nik' => [
                'required',
                'exists:karyawan,nik'
            ],
            'jumlah' => [
                'required'
            ],
            'tanggal_berlaku' => [
                'required',
                'date'
            ]
        ], [
            'nik.required' => 'Karyawan wajib dipilih',
            'nik.exists' => 'Karyawan yang dipilih tidak valid',
            'jumlah.required' => 'BPJS Kesehatan wajib diisi',
            'tanggal_berlaku.required' => 'Tanggal Berlaku wajib diisi',
            'tanggal_berlaku.date' => 'Format Tanggal Berlaku tidak valid'
        ]);

        //Kode BPJS Kesehatan = K250001;
        $tahun = date('Y', strtotime($request->tanggal_berlaku));
        $last_bpjs_kesehatan = Bpjskesehatan::orderBy('kode_bpjs_kesehatan', 'desc')
            ->whereRaw('YEAR(tanggal_berlaku) = ' . $tahun)
            ->first();
        $last_kode_bpjs_kesehatan = $last_bpjs_kesehatan != null ? $last_bpjs_kesehatan->kode_bpjs_kesehatan : '';
        $kode_bpjs_kesehatan = buatkode($last_kode_bpjs_kesehatan, "K" . substr($tahun, 2, 2), 4);
        
        try {
            // Validasi jumlah setelah konversi
            $jumlah = toNumber($request->jumlah);
            if (!is_numeric($jumlah) || $jumlah < 0 || $jumlah > 999999999) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('BPJS Kesehatan harus berupa angka antara 0 sampai 999.999.999'));
            }

            Bpjskesehatan::create([
                'kode_bpjs_kesehatan' => $kode_bpjs_kesehatan,
                'nik' => $request->nik,
                'jumlah' => $jumlah,
                'tanggal_berlaku' => $request->tanggal_berlaku
            ]);

            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang'));
            } else {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Terjadi kesalahan: ' . $errorMessage));
            }
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with(messageError('Data Gagal Disimpan: ' . $e->getMessage()));
        }
    }


    public function edit($kode_bpjs_kesehatan)
    {
        $kode_bpjs_kesehatan = Crypt::decrypt($kode_bpjs_kesehatan);
        $data['karyawan'] = Karyawan::orderby('nama_karyawan')->get();
        $data['bpjskesehatan'] = Bpjskesehatan::where('kode_bpjs_kesehatan', $kode_bpjs_kesehatan)->first();
        return view('datamaster.bpjskesehatan.edit', $data);
    }

    public function update(Request $request, $kode_bpjs_kesehatan)
    {
        $kode_bpjs_kesehatan = Crypt::decrypt($kode_bpjs_kesehatan);
        
        // Validasi dengan pesan error yang jelas
        $request->validate([
            'jumlah' => [
                'required'
            ],
            'tanggal_berlaku' => [
                'required',
                'date'
            ]
        ], [
            'jumlah.required' => 'BPJS Kesehatan wajib diisi',
            'tanggal_berlaku.required' => 'Tanggal Berlaku wajib diisi',
            'tanggal_berlaku.date' => 'Format Tanggal Berlaku tidak valid'
        ]);
        
        try {
            // Validasi jumlah setelah konversi
            $jumlah = toNumber($request->jumlah);
            if (!is_numeric($jumlah) || $jumlah < 0 || $jumlah > 999999999) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('BPJS Kesehatan harus berupa angka antara 0 sampai 999.999.999'));
            }

            Bpjskesehatan::where('kode_bpjs_kesehatan', $kode_bpjs_kesehatan)->update([
                'jumlah' => $jumlah,
                'tanggal_berlaku' => $request->tanggal_berlaku
            ]);
            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangani error database khusus
            $errorMessage = $e->getMessage();

            if (str_contains($errorMessage, 'Data too long')) {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Data yang dimasukkan terlalu panjang'));
            } else {
                return Redirect::back()
                    ->withInput()
                    ->with(messageError('Terjadi kesalahan: ' . $errorMessage));
            }
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with(messageError('Data Gagal Diupdate: ' . $e->getMessage()));
        }
    }

    public function destroy($kode_bpjs_kesehatan)
    {
        $kode_bpjs_kesehatan = Crypt::decrypt($kode_bpjs_kesehatan);
        try {
            bpjskesehatan::where('kode_bpjs_kesehatan', $kode_bpjs_kesehatan)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError('Data Gagal Dihapus ' . $e->getMessage()));
        }
    }
}
