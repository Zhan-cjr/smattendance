<?php

namespace App\Http\Controllers;

use App\Models\SlipgajiHarian;
use App\Models\SlipgajiHarianDetail;
use App\Models\Karyawan;
use App\Models\Presensi;
use App\Models\Gajipokok;
use App\Models\Pengaturanumum;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SlipgajiHarianController extends Controller
{
    public function create()
    {
        return view('payroll.slipgajiharian.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $dari = $request->dari;
            $sampai = $request->sampai;
            // Generate kode: SGH + tanggal dari (YYYYMMDD)
            $kode = 'SGH' . date('Ymd', strtotime($dari));

            // Cek duplikat
            $exists = SlipgajiHarian::where('kode_slip_gaji_harian', $kode)->exists();
            if ($exists) {
                return Redirect::back()->with(messageError('Data dengan periode tersebut sudah ada!'));
            }

            SlipgajiHarian::create([
                'kode_slip_gaji_harian' => $kode,
                'tanggal_slip' => $request->tanggal_slip,
                'dari' => $dari,
                'sampai' => $sampai,
                'status' => $request->status
            ]);

            // Simpan detail karyawan yang dipilih
            if ($request->has('nik') && is_array($request->nik)) {
                foreach ($request->nik as $nik) {
                    SlipgajiHarianDetail::create([
                        'kode_slip_gaji_harian' => $kode,
                        'nik' => $nik
                    ]);
                }
            }

            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function edit($kode_slip)
    {
        $kode_slip = Crypt::decrypt($kode_slip);
        $data['slipgaji'] = SlipgajiHarian::where('kode_slip_gaji_harian', $kode_slip)->first();
        $data['selected_nik'] = SlipgajiHarianDetail::where('kode_slip_gaji_harian', $kode_slip)
            ->join('karyawan', 'slip_gaji_harian_detail.nik', '=', 'karyawan.nik')
            ->select('slip_gaji_harian_detail.nik', 'karyawan.nama_karyawan')
            ->get();
        return view('payroll.slipgajiharian.edit', $data);
    }

    public function update(Request $request, $kode_slip)
    {
        $kode_slip = Crypt::decrypt($kode_slip);
        DB::beginTransaction();
        try {
            SlipgajiHarian::where('kode_slip_gaji_harian', $kode_slip)->update([
                'tanggal_slip' => $request->tanggal_slip,
                'dari' => $request->dari,
                'sampai' => $request->sampai,
                'status' => $request->status
            ]);

            // Update detail karyawan: hapus lama, simpan baru
            SlipgajiHarianDetail::where('kode_slip_gaji_harian', $kode_slip)->delete();
            if ($request->has('nik') && is_array($request->nik)) {
                foreach ($request->nik as $nik) {
                    SlipgajiHarianDetail::create([
                        'kode_slip_gaji_harian' => $kode_slip,
                        'nik' => $nik
                    ]);
                }
            }

            DB::commit();
            return Redirect::back()->with(messageSuccess('Data Berhasil Diupdate'));
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function show($kode_slip)
    {
        $kode_slip = Crypt::decrypt($kode_slip);
        $data['slipgaji'] = SlipgajiHarian::where('kode_slip_gaji_harian', $kode_slip)->first();
        $data['detail'] = SlipgajiHarianDetail::where('kode_slip_gaji_harian', $kode_slip)
            ->join('karyawan', 'slip_gaji_harian_detail.nik', '=', 'karyawan.nik')
            ->join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select('slip_gaji_harian_detail.nik', 'karyawan.nama_karyawan', 'jabatan.nama_jabatan', 'departemen.nama_dept')
            ->get();
        return view('payroll.slipgajiharian.show', $data);
    }

    public function cetak(Request $request)
    {
        $periode_dari = $request->dari;
        $periode_sampai = $request->sampai;
        $nik = $request->nik;

        if (empty($nik)) {
            return Redirect::back()->with(messageError('Pilih karyawan terlebih dahulu!'));
        }

        // 1. Get Employee Master Data
        $karyawan = Karyawan::whereIn('karyawan.nik', $nik)
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select('karyawan.*', 'jabatan.nama_jabatan', 'departemen.nama_dept')
            ->get();

        // 2. Get Presensi Data for these employees
        $presensi = Presensi::whereIn('nik', $nik)
            ->whereBetween('tanggal', [$periode_dari, $periode_sampai])
            ->join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select('presensi.*', 'presensi_jamkerja.jam_masuk', 'presensi_jamkerja.jam_pulang')
            ->get();

        // 3. Get Gaji Pokok (Daily Rate)
        $gaji_pokok = Gajipokok::whereIn('nik', $nik)
            ->whereIn('kode_gaji', function($query) use ($periode_sampai) {
                $query->select(DB::raw('MAX(kode_gaji)'))
                    ->from('karyawan_gaji_pokok')
                    ->where('tanggal_berlaku', '<=', $periode_sampai)
                    ->groupBy('nik');
            })->pluck('jumlah', 'nik');

        // 4. Other configuration
        $data['generalsetting'] = Pengaturanumum::where('id', 1)->first();
        $data['denda_list'] = Denda::all()->toArray();
        $data['periode_dari'] = $periode_dari;
        $data['periode_sampai'] = $periode_sampai;

        // 5. Build $laporan_presensi structure
        $laporan_presensi = $karyawan->mapWithKeys(function($k) use ($presensi, $gaji_pokok) {
            $emp_presensi = $presensi->where('nik', $k->nik);
            
            $item = [
                'nik' => $k->nik,
                'nik_show' => $k->nik_show,
                'nama_karyawan' => $k->nama_karyawan,
                'nama_jabatan' => $k->nama_jabatan,
                'nama_dept' => $k->nama_dept,
                'kode_dept' => $k->kode_dept,
                'gaji_pokok' => $gaji_pokok[$k->nik] ?? 0,
            ];

            foreach($emp_presensi as $p) {
                $item[$p->tanggal] = [
                    'status' => $p->status,
                    'jam_in' => $p->jam_in,
                    'jam_masuk' => $p->jam_masuk,
                    'denda' => $p->denda,
                ];
            }

            return [$k->nik => $item];
        });

        $data['laporan_presensi'] = $laporan_presensi;

        return view('laporan.slip_harian_cetak', $data);
    }

    public function destroy($kode_slip)
    {
        $kode_slip = Crypt::decrypt($kode_slip);
        try {
            // Detail dihapus otomatis via cascade
            SlipgajiHarian::where('kode_slip_gaji_harian', $kode_slip)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
