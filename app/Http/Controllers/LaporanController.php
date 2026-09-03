<?php

namespace App\Http\Controllers;

use App\Models\Bpjskesehatan;
use App\Models\Bpjstenagakerja;
use App\Models\Cabang;
use App\Models\Denda;
use App\Models\Departemen;
use App\Models\Detailpenyesuaiangaji;
use App\Models\Detailtunjangan;
use App\Models\Gajipokok;
use App\Models\Jenistunjangan;
use App\Models\Karyawan;
use App\Models\Pengaturanumum;
use App\Models\Presensi;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\PresensiExport;
use App\Exports\KehadiranExport;
use App\Exports\GajiExport;
use App\Exports\PresensiKaryawanExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function cuti()
    {
        $data['list_bulan'] = config('global.list_bulan');
        $data['start_year'] = config('global.start_year');
        $data['cabang'] = Cabang::orderBy('kode_cabang')->get();
        $data['departemen'] = Departemen::orderBy('kode_dept')->get();
        $data['cuti'] = \App\Models\Cuti::orderBy('kode_cuti')->get();
        return view('laporan.cuti', $data);
    }

    public function cetakcuti(Request $request)
    {
        $tahun = $request->tahun;
        $kode_cabang = $request->kode_cabang;
        $kode_dept = $request->kode_dept;
        $kode_cuti = $request->kode_cuti;
        $generalsetting = \App\Models\Pengaturanumum::where('id', 1)->first();

        // Get Master Cuti info if specific cuti selected
        $master_cuti = null;
        if (!empty($kode_cuti)) {
            $master_cuti = \App\Models\Cuti::where('kode_cuti', $kode_cuti)->first();
        }

        // Get Employees Query
        $query = Karyawan::query();
        $query->orderBy('nama_karyawan');
        if (!empty($kode_cabang)) {
            $query->where('kode_cabang', $kode_cabang);
        }
        if (!empty($kode_dept)) {
            $query->where('kode_dept', $kode_dept);
        }
        $karyawan = $query->get();

        // Get Approved Leave Data (Days) - UNION dari Presensi dan Izin Tables
        // Query 1: Dari presensi dengan izin approval
        $cuti_data_presensi = DB::table('presensi_izincuti_approve')
            ->join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
            ->join('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
            ->select('presensi.nik', 'presensi.tanggal', 'presensi_izincuti.kode_cuti')
            ->whereRaw('YEAR(presensi.tanggal) = ?', [$tahun]);

        // Query 2: Dari izincuti yang approved tapi belum di presensi
        // Expand range dari-sampai ke setiap hari
        $cuti_data_izin = DB::table('presensi_izincuti')
            ->where('status', 1)
            ->whereRaw('YEAR(dari) = ?', [$tahun])
            ->select('nik', 'dari as tanggal', 'kode_cuti')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('presensi_izincuti_approve')
                    ->join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
                    ->where('presensi.nik', DB::raw('presensi_izincuti.nik'))
                    ->where('presensi_izincuti_approve.kode_izin_cuti', DB::raw('presensi_izincuti.kode_izin_cuti'));
            });

        // Combine kedua query
        $cuti_data_all = $cuti_data_presensi->unionAll($cuti_data_izin)->get();

        // Expand range dari-sampai untuk izincuti yang belum di presensi
        $cuti_data = collect();
        foreach ($cuti_data_all as $d) {
            // Tambahkan dari presensi
            $cuti_data->push((object)[
                'nik' => $d->nik,
                'tanggal' => $d->tanggal,
                'kode_cuti' => $d->kode_cuti
            ]);
        }

        // Juga tambahkan expanded days dari izincuti yang belum ada di presensi
        $izincuti_not_in_presensi = DB::table('presensi_izincuti')
            ->where('status', 1)
            ->whereRaw('YEAR(dari) = ?', [$tahun])
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('presensi_izincuti_approve')
                    ->join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id')
                    ->where('presensi.nik', DB::raw('presensi_izincuti.nik'))
                    ->where('presensi_izincuti_approve.kode_izin_cuti', DB::raw('presensi_izincuti.kode_izin_cuti'));
            })
            ->get();

        // Expand each izincuti range
        foreach ($izincuti_not_in_presensi as $iz) {
            $current_date = $iz->dari;
            while (strtotime($current_date) <= strtotime($iz->sampai)) {
                if (date('Y', strtotime($current_date)) == $tahun) {
                    $cuti_data->push((object)[
                        'nik' => $iz->nik,
                        'tanggal' => $current_date,
                        'kode_cuti' => $iz->kode_cuti
                    ]);
                }
                $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
            }
        }

        // Process data structure
        $rekap_cuti = [];
        foreach ($karyawan as $k) {
            $rekap_cuti[$k->nik] = [
                'nama' => $k->nama_karyawan,
                'bulan' => array_fill(1, 12, 0),
                'total_ambil' => 0,
                'sisa' => 0 
            ];
        }

        foreach ($cuti_data as $d) {
            // Check if employee exists in the filtered list
            if (isset($rekap_cuti[$d->nik])) {
                // Filter by specific cuti type if requested
                if (!empty($kode_cuti) && $d->kode_cuti != $kode_cuti) {
                    continue;
                }

                $bulan = (int)date('m', strtotime($d->tanggal));
                $rekap_cuti[$d->nik]['bulan'][$bulan]++;
                $rekap_cuti[$d->nik]['total_ambil']++;
            }
        }
        
        $data['tahun'] = $tahun;
        $data['rekap_cuti'] = $rekap_cuti;
        $data['master_cuti'] = $master_cuti;
        $data['namacabang'] = !empty($kode_cabang) ? Cabang::where('kode_cabang', $kode_cabang)->first()->nama_cabang : 'Semua Cabang';
        $data['namadept'] = !empty($kode_dept) ? Departemen::where('kode_dept', $kode_dept)->first()->nama_dept : 'Semua Departemen';
        $data['jenis_cuti'] = !empty($master_cuti) ? $master_cuti->jenis_cuti : 'Semua Jenis Cuti';
        $data['generalsetting'] = $generalsetting;

        if(isset($_POST['exportexcel'])){
             // Future export
        }
        
        return view('laporan.cetak_cuti', $data);

    }

    public function presensi()
    {
        $data['list_bulan'] = config('global.list_bulan');
        $data['start_year'] = config('global.start_year');
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $departemen = Departemen::orderBy('kode_dept')->get();
        $data['cabang'] = $cabang;
        $data['departemen'] = $departemen;
        return view('laporan.presensi', $data);
    }


    public function cetakpresensi(Request $request)
    {

        $user = User::where('id', Auth::user()->id)->first();
        $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $periode_laporan_dari = $generalsetting->periode_laporan_dari;
        $periode_laporan_sampai = $generalsetting->periode_laporan_sampai;
        $periode_laporan_lintas_bulan = $generalsetting->periode_laporan_next_bulan;
        if ($request->periode_laporan == 1) {
            if ($periode_laporan_lintas_bulan == 1) {
                if ($request->bulan == 1) {
                    $bulan = 12;
                    $tahun = $request->tahun - 1;
                } else {
                    $bulan = $request->bulan - 1;
                    $tahun = $request->tahun;
                }
            } else {
                $bulan = $request->bulan;
                $tahun = $request->tahun;
            }

            // Menambahkan nol di depan bulan jika bulan kurang dari 10

            $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
            $periode_dari = $tahun . '-' . $bulan . '-' . $periode_laporan_dari;
            $periode_sampai = $request->tahun . '-' . $request->bulan . '-' . $periode_laporan_sampai;
        } elseif ($request->periode_laporan == 2) {
            // Menambahkan nol di depan bulan jika bulan kurang dari 10

            $bulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
            $periode_dari = $request->tahun . '-' . $bulan . '-01';
            $periode_sampai = date('Y-m-t', strtotime($periode_dari));
        } else {
            $periode_dari = $request->dari;
            $periode_sampai = $request->sampai;
        }




        // Query 1: Dari tabel presensi (normal hadir, alpha, lembur, dll)
        $presensi_detail  = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->leftJoin('presensi_izinabsen_approve', 'presensi.id', '=', 'presensi_izinabsen_approve.id_presensi')
            ->leftJoin('presensi_izinabsen', 'presensi_izinabsen_approve.kode_izin', '=', 'presensi_izinabsen.kode_izin')
            ->leftJoin('presensi_izinsakit_approve', 'presensi.id', '=', 'presensi_izinsakit_approve.id_presensi')
            ->leftJoin('presensi_izinsakit', 'presensi_izinsakit_approve.kode_izin_sakit', '=', 'presensi_izinsakit.kode_izin_sakit')
            ->leftJoin('presensi_izincuti_approve', 'presensi.id', '=', 'presensi_izincuti_approve.id_presensi')
            ->leftJoin('presensi_izincuti', 'presensi_izincuti_approve.kode_izin_cuti', '=', 'presensi_izincuti.kode_izin_cuti')
            ->select(
                'presensi.id',
                'presensi.nik',
                'presensi.tanggal',
                'presensi.kode_jam_kerja',
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.status',
                'presensi.denda',
                'presensi.status_potongan',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.total_jam',
                'presensi_jamkerja.istirahat',
                'presensi_jamkerja.jam_awal_istirahat',
                'presensi_jamkerja.jam_akhir_istirahat',
                'presensi_jamkerja.lintashari',
                'presensi_izinabsen.keterangan as keterangan_izin',
                'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                'presensi_izincuti.keterangan as keterangan_izin_cuti',
                DB::raw("'presensi' as source"),
                DB::raw("NULL as sampai")
            )
            ->whereBetween('presensi.tanggal', [$periode_dari, $periode_sampai]);

        // Query 2: Izin Absen yang approved tapi tidak ada di presensi
        $presensi_detail_izinabsen = DB::table('presensi_izinabsen')
            ->where('presensi_izinabsen.status', 1)
            ->where('presensi_izinabsen.dari', '<=', $periode_sampai)
            ->where('presensi_izinabsen.sampai', '>=', $periode_dari)
            ->whereNotIn('presensi_izinabsen.kode_izin', function($q) {
                $q->select('presensi_izinabsen_approve.kode_izin')
                    ->from('presensi_izinabsen_approve')
                    ->join('presensi', 'presensi_izinabsen_approve.id_presensi', '=', 'presensi.id');
            })
            ->select(
                DB::raw('NULL as id'),
                'presensi_izinabsen.nik',
                'presensi_izinabsen.dari as tanggal',
                DB::raw('NULL as kode_jam_kerja'),
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'i' as status"),
                DB::raw('NULL as denda'),
                DB::raw('NULL as status_potongan'),
                DB::raw("'Izin Absen' as nama_jam_kerja"),
                DB::raw('NULL as jam_masuk'),
                DB::raw('NULL as jam_pulang'),
                DB::raw('NULL as total_jam'),
                DB::raw('NULL as istirahat'),
                DB::raw('NULL as jam_awal_istirahat'),
                DB::raw('NULL as jam_akhir_istirahat'),
                DB::raw('0 as lintashari'),
                'presensi_izinabsen.keterangan as keterangan_izin',
                DB::raw('NULL as keterangan_izin_sakit'),
                DB::raw('NULL as keterangan_izin_cuti'),
                DB::raw("'izinabsen' as source"),
                'presensi_izinabsen.sampai as sampai'
            );

        // Query 3: Izin Sakit yang approved tapi tidak ada di presensi
        $presensi_detail_izinsakit = DB::table('presensi_izinsakit')
            ->where('presensi_izinsakit.status', 1)
            ->where('presensi_izinsakit.dari', '<=', $periode_sampai)
            ->where('presensi_izinsakit.sampai', '>=', $periode_dari)
            ->whereNotIn('presensi_izinsakit.kode_izin_sakit', function($q) {
                $q->select('presensi_izinsakit_approve.kode_izin_sakit')
                    ->from('presensi_izinsakit_approve')
                    ->join('presensi', 'presensi_izinsakit_approve.id_presensi', '=', 'presensi.id');
            })
            ->select(
                DB::raw('NULL as id'),
                'presensi_izinsakit.nik',
                'presensi_izinsakit.dari as tanggal',
                DB::raw('NULL as kode_jam_kerja'),
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'s' as status"),
                DB::raw('NULL as denda'),
                DB::raw('NULL as status_potongan'),
                DB::raw("'Izin Sakit' as nama_jam_kerja"),
                DB::raw('NULL as jam_masuk'),
                DB::raw('NULL as jam_pulang'),
                DB::raw('NULL as total_jam'),
                DB::raw('NULL as istirahat'),
                DB::raw('NULL as jam_awal_istirahat'),
                DB::raw('NULL as jam_akhir_istirahat'),
                DB::raw('0 as lintashari'),
                DB::raw('NULL as keterangan_izin'),
                'presensi_izinsakit.keterangan as keterangan_izin_sakit',
                DB::raw('NULL as keterangan_izin_cuti'),
                DB::raw("'izinsakit' as source"),
                'presensi_izinsakit.sampai as sampai'
            );

        // Query 4: Izin Cuti yang approved tapi tidak ada di presensi
        $presensi_detail_izincuti = DB::table('presensi_izincuti')
            ->where('presensi_izincuti.status', 1)
            ->where('presensi_izincuti.dari', '<=', $periode_sampai)
            ->where('presensi_izincuti.sampai', '>=', $periode_dari)
            ->whereNotIn('presensi_izincuti.kode_izin_cuti', function($q) {
                $q->select('presensi_izincuti_approve.kode_izin_cuti')
                    ->from('presensi_izincuti_approve')
                    ->join('presensi', 'presensi_izincuti_approve.id_presensi', '=', 'presensi.id');
            })
            ->select(
                DB::raw('NULL as id'),
                'presensi_izincuti.nik',
                'presensi_izincuti.dari as tanggal',
                DB::raw('NULL as kode_jam_kerja'),
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'c' as status"),
                DB::raw('NULL as denda'),
                DB::raw('NULL as status_potongan'),
                DB::raw("'Izin Cuti' as nama_jam_kerja"),
                DB::raw('NULL as jam_masuk'),
                DB::raw('NULL as jam_pulang'),
                DB::raw('NULL as total_jam'),
                DB::raw('NULL as istirahat'),
                DB::raw('NULL as jam_awal_istirahat'),
                DB::raw('NULL as jam_akhir_istirahat'),
                DB::raw('0 as lintashari'),
                DB::raw('NULL as keterangan_izin'),
                DB::raw('NULL as keterangan_izin_sakit'),
                'presensi_izincuti.keterangan as keterangan_izin_cuti',
                DB::raw("'izincuti' as source"),
                'presensi_izincuti.sampai as sampai'
            );

        // UNION semua queries
        $presensi_detail = $presensi_detail
            ->unionAll($presensi_detail_izinabsen)
            ->unionAll($presensi_detail_izinsakit)
            ->unionAll($presensi_detail_izincuti);

        /**
         * Mapping jadwal kerja per karyawan dengan prioritas:
         * 1. presensi_jamkerja_bydate (per karyawan per tanggal)
         * 2. grup_jamkerja_bydate (berdasarkan grup karyawan)
         * 3. presensi_jamkerja_byday (per karyawan per hari)
         * 4. presensi_jamkerja_bydept_detail (per departemen & cabang per hari)
         *
         * Agar laporan tidak berat, semua jadwal diambil sekali di sini
         * lalu dikonversi menjadi array PHP sederhana yang dipakai di view.
         */

        // 1) Jadwal by-date per karyawan
        $jadwal_bydate = DB::table('presensi_jamkerja_bydate')
            ->join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi_jamkerja_bydate.nik',
                'presensi_jamkerja_bydate.tanggal',
                'presensi_jamkerja.total_jam',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang'
            )
            ->whereBetween('presensi_jamkerja_bydate.tanggal', [$periode_dari, $periode_sampai])
            ->get()
            ->groupBy('nik')
            ->map(function ($rows) {
                $result = [];
                foreach ($rows as $row) {
                    $result[$row->tanggal] = [
                        'total_jam' => $row->total_jam,
                        'nama_jam_kerja' => $row->nama_jam_kerja,
                        'jam_masuk' => $row->jam_masuk,
                        'jam_pulang' => $row->jam_pulang,
                    ];
                }
                return $result;
            });

        // 2) Jadwal grup by-date (grup_jamkerja_bydate)
        $jadwal_grup_bydate = DB::table('grup_detail')
            ->join('grup_jamkerja_bydate', 'grup_detail.kode_grup', '=', 'grup_jamkerja_bydate.kode_grup')
            ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'grup_detail.nik',
                'grup_jamkerja_bydate.tanggal',
                'presensi_jamkerja.total_jam',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang'
            )
            ->whereBetween('grup_jamkerja_bydate.tanggal', [$periode_dari, $periode_sampai])
            ->get()
            ->groupBy('nik')
            ->map(function ($rows) {
                $result = [];
                foreach ($rows as $row) {
                    $result[$row->tanggal] = [
                        'total_jam' => $row->total_jam,
                        'nama_jam_kerja' => $row->nama_jam_kerja,
                        'jam_masuk' => $row->jam_masuk,
                        'jam_pulang' => $row->jam_pulang,
                    ];
                }
                return $result;
            });

        // 3) Jadwal by-day per karyawan (presensi_jamkerja_byday)
        $jadwal_byday = DB::table('presensi_jamkerja_byday')
            ->join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi_jamkerja_byday.nik',
                'presensi_jamkerja_byday.hari',
                'presensi_jamkerja.total_jam',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang'
            )
            ->get()
            ->groupBy('nik')
            ->map(function ($rows) {
                $result = [];
                foreach ($rows as $row) {
                    $result[$row->hari] = [
                        'total_jam' => $row->total_jam,
                        'nama_jam_kerja' => $row->nama_jam_kerja,
                        'jam_masuk' => $row->jam_masuk,
                        'jam_pulang' => $row->jam_pulang,
                    ];
                }
                return $result;
            });

        // 4) Jadwal by-day per departemen & cabang (presensi_jamkerja_bydept_detail)
        $jadwal_bydept = DB::table('presensi_jamkerja_bydept_detail')
            ->join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
            ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi_jamkerja_bydept.kode_dept',
                'presensi_jamkerja_bydept.kode_cabang',
                'presensi_jamkerja_bydept_detail.hari',
                'presensi_jamkerja.total_jam',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang'
            )
            ->get()
            ->groupBy(function ($row) {
                return $row->kode_dept . '|' . $row->kode_cabang;
            })
            ->map(function ($rows) {
                $result = [];
                foreach ($rows as $row) {
                    $result[$row->hari] = [
                        'total_jam' => $row->total_jam,
                        'nama_jam_kerja' => $row->nama_jam_kerja,
                        'jam_masuk' => $row->jam_masuk,
                        'jam_pulang' => $row->jam_pulang,
                    ];
                }
                return $result;
            });


        $gaji_pokok = Gajipokok::select(
            'nik',
            'jumlah',
            'jenis_upah'
        )
            ->whereIn('kode_gaji', function ($query) use ($periode_sampai) {
                $query->select(DB::raw('MAX(kode_gaji)'))
                    ->from('karyawan_gaji_pokok')
                    ->where('tanggal_berlaku', '<=', $periode_sampai)
                    ->groupBy('nik');
            });



        $bpjs_kesehatan = Bpjskesehatan::select(
            'nik',
            'jumlah'
        )
            ->whereIn('kode_bpjs_kesehatan', function ($query) use ($periode_sampai) {
                $query->select(DB::raw('MAX(kode_bpjs_kesehatan)'))
                    ->from('karyawan_bpjskesehatan')
                    ->where('tanggal_berlaku', '<=', $periode_sampai)
                    ->groupBy('nik');
            });


        $bpjs_tenagakerja = Bpjstenagakerja::select(
            'nik',
            'jumlah'
        )
            ->whereIn('kode_bpjs_tk', function ($query) use ($periode_sampai) {
                $query->select(DB::raw('MAX(kode_bpjs_tk)'))
                    ->from('karyawan_bpjstenagakerja')
                    ->where('tanggal_berlaku', '<=', $periode_sampai)
                    ->groupBy('nik');
            });


        //Tunjangan
        $jenis_tunjangan = Jenistunjangan::orderBy('kode_jenis_tunjangan')->get();
        $select_tunjangan = [];
        $select_field_tunjangan = [];
        foreach ($jenis_tunjangan as $d) {
            $select_tunjangan[] = DB::raw('SUM(IF(karyawan_tunjangan_detail.kode_jenis_tunjangan = "' . $d->kode_jenis_tunjangan . '", karyawan_tunjangan_detail.jumlah, 0)) as jumlah_' . $d->kode_jenis_tunjangan);
            $select_field_tunjangan[] = 'jumlah_' . $d->kode_jenis_tunjangan;
        }
        $tunjangan = Detailtunjangan::query();
        $tunjangan->join('karyawan_tunjangan', 'karyawan_tunjangan_detail.kode_tunjangan', '=', 'karyawan_tunjangan.kode_tunjangan');
        $tunjangan->select(
            'karyawan_tunjangan.nik',
            ...$select_tunjangan
        );
        $tunjangan->whereIn('karyawan_tunjangan_detail.kode_tunjangan', function ($query) use ($periode_sampai) {
            $query->select(DB::raw('MAX(kode_tunjangan)'))
                ->from('karyawan_tunjangan')
                ->where('tanggal_berlaku', '<=', $periode_sampai)
                ->groupBy('nik');
        });

        $tunjangan->groupBy('karyawan_tunjangan.nik');


        $penyesuaian_gaji = Detailpenyesuaiangaji::select('nik', 'penambah', 'pengurang')
            ->join('karyawan_penyesuaian_gaji', 'karyawan_penyesuaian_gaji_detail.kode_penyesuaian_gaji', '=', 'karyawan_penyesuaian_gaji.kode_penyesuaian_gaji')
            ->where('bulan', $request->bulan)
            ->where('tahun', $request->tahun);

        $pinjaman = DB::table('pembayaran_pinjaman')
            ->select('pinjaman.nik', DB::raw('SUM(pembayaran_pinjaman.jumlah_bayar) as total_cicilan'))
            ->join('pinjaman', 'pembayaran_pinjaman.pinjaman_id', '=', 'pinjaman.id')
            ->where('pembayaran_pinjaman.bulan_gaji', $request->bulan)
            ->where('pembayaran_pinjaman.tahun_gaji', $request->tahun)
            ->groupBy('pinjaman.nik');

        $q_presensi = Karyawan::query();
        $q_presensi->select(
            'karyawan.nik',
            'karyawan.nik_show',
            'nama_karyawan',
            'nama_jabatan',
            'karyawan.kode_dept',
            'nama_dept',
            'karyawan.kode_cabang',
            'presensi.tanggal',
            'presensi.status',
            'presensi.kode_jam_kerja',
            'presensi.nama_jam_kerja',
            'presensi.jam_masuk',
            'presensi.jam_pulang',
            'presensi.jam_in',
            'presensi.jam_out',
            'presensi.istirahat',
            'presensi.jam_awal_istirahat',
            'presensi.jam_akhir_istirahat',
            'presensi.lintashari',
            'presensi.keterangan_izin',
            'presensi.keterangan_izin_sakit',
            'presensi.keterangan_izin_cuti',
            'presensi.total_jam',
            'presensi.denda',
            'presensi.status_potongan',
            'gaji_pokok.jumlah as gaji_pokok',
            'gaji_pokok.jenis_upah',
            'bpjs_kesehatan.jumlah as bpjs_kesehatan',
            'bpjs_tenagakerja.jumlah as bpjs_tenagakerja',
            'penambah',
            'pengurang',
            'pinjaman_cicilan.total_cicilan',
            ...$select_field_tunjangan
        );
        $q_presensi->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $q_presensi->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $q_presensi->leftJoinSub($presensi_detail, 'presensi', function ($join) {
            $join->on('karyawan.nik', '=', 'presensi.nik');
        });
        $q_presensi->leftJoinSub($gaji_pokok, 'gaji_pokok', function ($join) {
            $join->on('karyawan.nik', '=', 'gaji_pokok.nik');
        });


        $q_presensi->leftJoinSub($bpjs_kesehatan, 'bpjs_kesehatan', function ($join) {
            $join->on('karyawan.nik', '=', 'bpjs_kesehatan.nik');
        });

        $q_presensi->leftJoinSub($bpjs_tenagakerja, 'bpjs_tenagakerja', function ($join) {
            $join->on('karyawan.nik', '=', 'bpjs_tenagakerja.nik');
        });


        $q_presensi->leftJoinSub($tunjangan, 'tunjangan', function ($join) {
            $join->on('karyawan.nik', '=', 'tunjangan.nik');
        });

        $q_presensi->leftJoinSub($penyesuaian_gaji, 'penyesuaian_gaji', function ($join) {
            $join->on('karyawan.nik', '=', 'penyesuaian_gaji.nik');
        });
        $q_presensi->leftJoinSub($pinjaman, 'pinjaman_cicilan', function ($join) {
            $join->on('karyawan.nik', '=', 'pinjaman_cicilan.nik');
        });

        if (!empty($request->kode_cabang)) {
            $q_presensi->where('karyawan.kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->kode_dept)) {
            $q_presensi->where('karyawan.kode_dept', $request->kode_dept);
        }

        if (!empty($request->nik)) {
            if (is_array($request->nik)) {
                $q_presensi->whereIn('karyawan.nik', $request->nik);
            } else {
                $q_presensi->where('karyawan.nik', $request->nik);
            }
        }

        if (!empty($request->jenis_upah)) {
            $q_presensi->where('gaji_pokok.jenis_upah', $request->jenis_upah);
        }

        if ($user->hasRole('karyawan')) {
            $q_presensi->where('karyawan.nik', $userkaryawan->nik);
        }
        $q_presensi->orderBy('karyawan.nama_karyawan');
        $q_presensi->orderBy('presensi.tanggal', 'asc');
        $presensi_raw = $q_presensi->get();

        // Expand multi-day izin records dari approval tables
        $presensi = collect();
        foreach ($presensi_raw as $item) {
            // Check jika ini multi-day izin dari approval table (bukan presensi normal)
            if (isset($item->source) && in_array($item->source, ['izinabsen', 'izinsakit', 'izincuti']) && !empty($item->sampai)) {
                // Expand ke setiap hari dalam range, tapi hanya dalam periode laporan
                $dari_date = Carbon::parse($item->tanggal)->format('Y-m-d');
                $sampai_date = Carbon::parse($item->sampai)->format('Y-m-d');
                
                // Batasi ke periode laporan
                $expand_dari = max($dari_date, $periode_dari);
                $expand_sampai = min($sampai_date, $periode_sampai);
                
                $current_date = $expand_dari;
                while (strtotime($current_date) <= strtotime($expand_sampai)) {
                    $item_copy = clone $item;
                    $item_copy->tanggal = $current_date;
                    $presensi->push($item_copy);
                    $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                }
            } else {
                $presensi->push($item);
            }
        }

        $data['periode_dari'] = $periode_dari;
        $data['periode_sampai'] = $periode_sampai;
        $data['jmlhari'] = hitungJumlahHari($periode_dari, $periode_sampai) + 1;
        $data['denda_list'] = Denda::all()->toArray();
        $data['datalibur'] = getdatalibur($periode_dari, $periode_sampai);
        $data['datalembur'] = getlembur($periode_dari, $periode_sampai);
        $data['generalsetting'] = $generalsetting;
        // Kirim mapping jadwal ke view untuk dipakai saat karyawan tidak presensi
        $data['jadwal_bydate'] = $jadwal_bydate;
        $data['jadwal_grup_bydate'] = $jadwal_grup_bydate;
        $data['jadwal_byday'] = $jadwal_byday;
        $data['jadwal_bydept'] = $jadwal_bydept;
        // Simpan parameter request untuk button kunci laporan
        $data['request_params'] = [
            'periode_laporan' => $request->periode_laporan,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
            'kode_cabang' => $request->kode_cabang ?? '',
            'kode_dept' => $request->kode_dept ?? '',
            'nik' => $request->nik ?? '',
            'jenis_upah' => $request->jenis_upah ?? ''
        ];


        if (!empty($request->nik) && $request->format_laporan == 1) {
            $karyawan = Karyawan::join('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
                ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
                ->join('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
                ->select('karyawan.*', 'jabatan.nama_jabatan', 'departemen.nama_dept', 'cabang.nama_cabang')
                ->where('karyawan.nik', $request->nik)
                ->first();
            $data['karyawan'] = $karyawan;
            $data['presensi'] = $presensi;
            if ($request->has('exportButton')) {
                return Excel::download(new PresensiKaryawanExport($data), 'Laporan Presensi Karyawan ' . $periode_dari . ' - ' . $periode_sampai . '.xlsx');
            }
            return view('laporan.presensi_karyawan_cetak', $data);
        } else {
            $laporan_presensi = $presensi->groupBy('nik')->map(function ($rows) use ($jenis_tunjangan) {
                $data = [
                    'nik' => $rows->first()->nik,
                    'nik_show' => $rows->first()->nik_show,
                    'nama_karyawan' => $rows->first()->nama_karyawan,
                    'nama_jabatan' => $rows->first()->nama_jabatan,
                    'kode_dept' => $rows->first()->kode_dept,
                    'nama_dept' => $rows->first()->nama_dept,
                    'kode_cabang' => $rows->first()->kode_cabang,
                    'gaji_pokok' => $rows->first()->gaji_pokok,
                    'jenis_upah' => $rows->first()->jenis_upah,
                    'bpjs_kesehatan' => $rows->first()->bpjs_kesehatan,
                    'bpjs_tenagakerja' => $rows->first()->bpjs_tenagakerja,
                    'penambah' => $rows->first()->penambah,
                    'pengurang' => $rows->first()->pengurang,
                    'cicilan_pinjaman' => $rows->first()->total_cicilan,

                ];

                foreach ($jenis_tunjangan as $j) {
                    $data = [
                        ...$data,
                        $j->kode_jenis_tunjangan => $rows->first()->{"jumlah_" . $j->kode_jenis_tunjangan}
                    ];
                }

                foreach ($rows as $row) {
                    $data[$row->tanggal] = [
                        'status' => $row->status,
                        'kode_jam_kerja' => $row->kode_jam_kerja,
                        'nama_jam_kerja' => $row->nama_jam_kerja,
                        'jam_masuk' => $row->jam_masuk,
                        'jam_pulang' => $row->jam_pulang,
                        'jam_in' => $row->jam_in,
                        'jam_out' => $row->jam_out,
                        'istirahat' => $row->istirahat,
                        'jam_awal_istirahat' => $row->jam_awal_istirahat,
                        'jam_akhir_istirahat' => $row->jam_akhir_istirahat,
                        'istirahat_in' => $row->istirahat_in,
                        'istirahat_out' => $row->istirahat_out,
                        'lintashari' => $row->lintashari,
                        'keterangan_izin_absen' => $row->keterangan_izin_absen,
                        'keterangan_izin_sakit' => $row->keterangan_izin_sakit,
                        'keterangan_izin_cuti' => $row->keterangan_izin_cuti,
                        'total_jam' => $row->total_jam,
                        'denda' => $row->denda ?? null,
                        'status_potongan' => $row->status_potongan ?? null,
                        'status_potongan_istirahat' => $row->status_potongan_istirahat ?? null
                    ];
                }
                return $data;
            });
            $data['laporan_presensi'] = $laporan_presensi;
            $data['jenis_tunjangan'] = $jenis_tunjangan;


            if ($user->hasRole('karyawan')) {
                $first_row = $laporan_presensi->first();
                $jenis_upah = ($first_row && isset($first_row['jenis_upah'])) ? $first_row['jenis_upah'] : 'Bulanan';
                $view = ($jenis_upah == 'Harian') ? 'laporan.slip_karyawan_harian_cetak' : 'laporan.slip_karyawan_cetak';
                return view($view, $data);
            } else {
                if ($request->format_laporan == 1) {
                    if ($request->has('exportButton')) {
                        return Excel::download(new PresensiExport($data), 'Rekap Presensi ' . $periode_dari . ' - ' . $periode_sampai . '.xlsx');
                    }
                    $view = $request->format_rekap == 2 ? 'laporan.presensi_cetak_v2' : 'laporan.presensi_cetak';
                    return view($view, $data);
                } else if ($request->format_laporan == 2) {
                    if ($request->has('exportButton')) {
                        $view = $request->jenis_upah == 'Harian' ? 'laporan.gaji_harian_excel' : 'laporan.gaji_excel';
                        return Excel::download(new GajiExport($data, $view), 'Rekap Gaji ' . $periode_dari . ' - ' . $periode_sampai . '.xlsx');
                    }

                    if ($request->jenis_upah == 'Harian') {
                        return view('laporan.gaji_harian_cetak', $data);
                    }

                    return view('laporan.gaji_cetak', $data);
                } else if ($request->format_laporan == 3) {
                    $first_row = $laporan_presensi->first();
                    $jenis_upah = $request->jenis_upah ?: (($first_row && isset($first_row['jenis_upah'])) ? $first_row['jenis_upah'] : 'Bulanan');
                    
                    if ($user->hasRole('karyawan')) {
                        $view = $jenis_upah == 'Harian' ? 'laporan.slip_karyawan_harian_cetak' : 'laporan.slip_karyawan_cetak';
                        return view($view, $data);
                    }
                    $view = $jenis_upah == 'Harian' ? 'laporan.slip_harian_cetak' : 'laporan.slip_cetak';
                    return view($view, $data);
                }
            }
        }
    }

    public function kunciLaporan(Request $request)
    {
        try {
            $generalsetting = Pengaturanumum::where('id', 1)->first();
            $periode_laporan_dari = $generalsetting->periode_laporan_dari;
            $periode_laporan_sampai = $generalsetting->periode_laporan_sampai;
            $periode_laporan_lintas_bulan = $generalsetting->periode_laporan_next_bulan;
            
            if ($request->periode_laporan == 1) {
                if ($periode_laporan_lintas_bulan == 1) {
                    if ($request->bulan == 1) {
                        $bulan = 12;
                        $tahun = $request->tahun - 1;
                    } else {
                        $bulan = $request->bulan - 1;
                        $tahun = $request->tahun;
                    }
                } else {
                    $bulan = $request->bulan;
                    $tahun = $request->tahun;
                }

                $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
                $periode_dari = $tahun . '-' . $bulan . '-' . $periode_laporan_dari;
                $periode_sampai = $request->tahun . '-' . $request->bulan . '-' . $periode_laporan_sampai;
            } else {
                $bulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
                $periode_dari = $request->tahun . '-' . $bulan . '-01';
                $periode_sampai = date('Y-m-t', strtotime($periode_dari));
            }

            // Ambil mapping jadwal kerja (sama seperti di cetakpresensi)
            // 1) Jadwal by-date per karyawan
            $jadwal_bydate = DB::table('presensi_jamkerja_bydate')
                ->join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select(
                    'presensi_jamkerja_bydate.nik',
                    'presensi_jamkerja_bydate.tanggal',
                    'presensi_jamkerja.kode_jam_kerja',
                    'presensi_jamkerja.total_jam'
                )
                ->whereBetween('presensi_jamkerja_bydate.tanggal', [$periode_dari, $periode_sampai])
                ->get()
                ->groupBy('nik')
                ->map(function ($rows) {
                    $result = [];
                    foreach ($rows as $row) {
                        $result[$row->tanggal] = [
                            'kode_jam_kerja' => $row->kode_jam_kerja,
                            'total_jam' => $row->total_jam
                        ];
                    }
                    return $result;
                });

            // 2) Jadwal grup by-date
            $jadwal_grup_bydate = DB::table('grup_detail')
                ->join('grup_jamkerja_bydate', 'grup_detail.kode_grup', '=', 'grup_jamkerja_bydate.kode_grup')
                ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select(
                    'grup_detail.nik',
                    'grup_jamkerja_bydate.tanggal',
                    'presensi_jamkerja.kode_jam_kerja',
                    'presensi_jamkerja.total_jam'
                )
                ->whereBetween('grup_jamkerja_bydate.tanggal', [$periode_dari, $periode_sampai])
                ->get()
                ->groupBy('nik')
                ->map(function ($rows) {
                    $result = [];
                    foreach ($rows as $row) {
                        $result[$row->tanggal] = [
                            'kode_jam_kerja' => $row->kode_jam_kerja,
                            'total_jam' => $row->total_jam
                        ];
                    }
                    return $result;
                });

            // 3) Jadwal by-day per karyawan
            $jadwal_byday = DB::table('presensi_jamkerja_byday')
                ->join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select(
                    'presensi_jamkerja_byday.nik',
                    'presensi_jamkerja_byday.hari',
                    'presensi_jamkerja.kode_jam_kerja',
                    'presensi_jamkerja.total_jam'
                )
                ->get()
                ->groupBy('nik')
                ->map(function ($rows) {
                    $result = [];
                    foreach ($rows as $row) {
                        $result[$row->hari] = [
                            'kode_jam_kerja' => $row->kode_jam_kerja,
                            'total_jam' => $row->total_jam
                        ];
                    }
                    return $result;
                });

            // 4) Jadwal by-day per departemen & cabang
            $jadwal_bydept = DB::table('presensi_jamkerja_bydept_detail')
                ->join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
                ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->select(
                    'presensi_jamkerja_bydept.kode_dept',
                    'presensi_jamkerja_bydept.kode_cabang',
                    'presensi_jamkerja_bydept_detail.hari',
                    'presensi_jamkerja.kode_jam_kerja',
                    'presensi_jamkerja.total_jam'
                )
                ->get()
                ->groupBy(function ($row) {
                    return $row->kode_dept . '|' . $row->kode_cabang;
                })
                ->map(function ($rows) {
                    $result = [];
                    foreach ($rows as $row) {
                        $result[$row->hari] = [
                            'kode_jam_kerja' => $row->kode_jam_kerja,
                            'total_jam' => $row->total_jam
                        ];
                    }
                    return $result;
                });

            // Ambil data libur
            $datalibur = getdatalibur($periode_dari, $periode_sampai);

            // Ambil data presensi dalam periode dengan join ke karyawan untuk filter
            $presensi_query = Presensi::join('presensi_jamkerja', 'presensi.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
                ->leftJoin('karyawan', 'presensi.nik', '=', 'karyawan.nik')
                ->select(
                    'presensi.*',
                    'presensi_jamkerja.jam_masuk',
                    'presensi_jamkerja.jam_pulang'
                )
                ->whereBetween('presensi.tanggal', [$periode_dari, $periode_sampai])
                ->where('presensi.status', 'h'); // Hanya presensi dengan status hadir

            // Filter berdasarkan request
            if (!empty($request->kode_cabang)) {
                $presensi_query->where('karyawan.kode_cabang', $request->kode_cabang);
            }

            if (!empty($request->kode_dept)) {
                $presensi_query->where('karyawan.kode_dept', $request->kode_dept);
            }

            if (!empty($request->nik)) {
                $presensi_query->where('presensi.nik', $request->nik);
            }

            $presensi_list_raw = $presensi_query->get();
            $presensi_list = $presensi_list_raw->groupBy('nik');
            $denda_list = Denda::all()->toArray();

            $gaji_pokok = Gajipokok::select(
                'nik',
                'jumlah',
                'jenis_upah'
            )
                ->whereIn('kode_gaji', function ($query) use ($periode_sampai) {
                    $query->select(DB::raw('MAX(kode_gaji)'))
                        ->from('karyawan_gaji_pokok')
                        ->where('tanggal_berlaku', '<=', $periode_sampai)
                        ->groupBy('nik');
                });

            // Ambil semua karyawan yang sesuai filter
            $karyawan_query = Karyawan::query()
                ->select('karyawan.nik', 'karyawan.kode_dept', 'karyawan.kode_cabang');

            if (!empty($request->kode_cabang)) {
                $karyawan_query->where('karyawan.kode_cabang', $request->kode_cabang);
            }

            if (!empty($request->kode_dept)) {
                $karyawan_query->where('karyawan.kode_dept', $request->kode_dept);
            }

            if (!empty($request->nik)) {
                $karyawan_query->where('karyawan.nik', $request->nik);
            }

            if (!empty($request->jenis_upah)) {
                $karyawan_query->leftJoinSub($gaji_pokok, 'gaji_pokok', function ($join) {
                    $join->on('karyawan.nik', '=', 'gaji_pokok.nik');
                });
                $karyawan_query->where('gaji_pokok.jenis_upah', $request->jenis_upah);
            }

            $karyawan_list = $karyawan_query->get();

            $updated_count = 0;
            $inserted_alpa_count = 0;

            // Loop setiap karyawan
            foreach ($karyawan_list as $karyawan) {
                // Ambil presensi yang sudah ada untuk karyawan ini
                $presensi_karyawan = $presensi_list[$karyawan->nik] ?? collect();
                $presensi_by_tanggal = $presensi_karyawan->keyBy('tanggal');

                // Loop setiap tanggal dalam periode
                $tanggal_loop = $periode_dari;
                while (strtotime($tanggal_loop) <= strtotime($periode_sampai)) {
                    // Cek apakah sudah ada presensi untuk tanggal ini
                    if ($presensi_by_tanggal->has($tanggal_loop)) {
                        // Ada presensi, update denda
                        $presensi = $presensi_by_tanggal[$tanggal_loop];
                        $jam_masuk = $presensi->tanggal . ' ' . $presensi->jam_masuk;
                        $terlambat = hitungjamterlambat($presensi->jam_in, $jam_masuk);

                        $denda = 0;
                        if ($terlambat != null) {
                            if ($terlambat['desimal_terlambat'] < 1) {
                                $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                            }
                        }

                        // Update denda and status_potongan
                        Presensi::where('id', $presensi->id)->update([
                            'denda' => $denda,
                            'status_potongan' => $generalsetting->status_potongan_jam,
                            'status_potongan_istirahat' => $generalsetting->potongan_istirahat
                        ]);
                        $updated_count++;
                    } else {
                        // Tidak ada presensi, cek apakah alpa
                        $search = [
                            'nik' => $karyawan->nik,
                            'tanggal' => $tanggal_loop,
                        ];
                        
                        $ceklibur = ceklibur($datalibur, $search);
                        $nama_hari = getHari($tanggal_loop);

                        // Jika bukan libur, cek jadwal kerja
                        if (empty($ceklibur)) {
                            // Cek jadwal dengan prioritas yang sama seperti di view
                            $mapJadwalByDate = $jadwal_bydate[$karyawan->nik] ?? [];
                            $mapJadwalGrupByDate = $jadwal_grup_bydate[$karyawan->nik] ?? [];
                            $mapJadwalByDay = $jadwal_byday[$karyawan->nik] ?? [];
                            
                            $jadwal_info = null;
                            $kode_jam_kerja = null;
                            
                            // 1) Cek jadwal by-date per karyawan
                            if (isset($mapJadwalByDate[$tanggal_loop])) {
                                $jadwal_info = $mapJadwalByDate[$tanggal_loop];
                                $kode_jam_kerja = $jadwal_info['kode_jam_kerja'];
                            }
                            // 2) Cek jadwal grup by-date
                            elseif (isset($mapJadwalGrupByDate[$tanggal_loop])) {
                                $jadwal_info = $mapJadwalGrupByDate[$tanggal_loop];
                                $kode_jam_kerja = $jadwal_info['kode_jam_kerja'];
                            }
                            // 3) Cek jadwal by-day per karyawan
                            elseif (isset($mapJadwalByDay[$nama_hari])) {
                                $jadwal_info = $mapJadwalByDay[$nama_hari];
                                $kode_jam_kerja = $jadwal_info['kode_jam_kerja'];
                            }
                            // 4) Cek jadwal by-day per departemen & cabang
                            else {
                                $keyDeptCabang = $karyawan->kode_dept . '|' . $karyawan->kode_cabang;
                                $mapDept = $jadwal_bydept[$keyDeptCabang] ?? [];
                                if (isset($mapDept[$nama_hari])) {
                                    $jadwal_info = $mapDept[$nama_hari];
                                    $kode_jam_kerja = $jadwal_info['kode_jam_kerja'];
                                }
                            }

                            // Jika ada jadwal tapi tidak ada presensi → Alpa
                            if ($kode_jam_kerja !== null) {
                                // Cek apakah sudah ada presensi dengan status lain (izin, sakit, cuti, alpa)
                                $cek_presensi_existing = Presensi::where('nik', $karyawan->nik)
                                    ->where('tanggal', $tanggal_loop)
                                    ->first();

                                if (!$cek_presensi_existing) {
                                    // Insert presensi dengan status alpa
                                    Presensi::create([
                                        'nik' => $karyawan->nik,
                                        'tanggal' => $tanggal_loop,
                                        'kode_jam_kerja' => $kode_jam_kerja,
                                        'status' => 'a', // Alpa
                                        'jam_in' => null,
                                        'jam_out' => null,
                                        'denda' => null, // Alpa tidak ada denda, hanya potongan jam
                                        'status_potongan' => $generalsetting->status_potongan_jam
                                    ]);
                                    $inserted_alpa_count++;
                                }
                            }
                        }
                    }

                    // Increment tanggal
                    $tanggal_loop = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_loop)));
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Laporan berhasil dikunci. Total {$updated_count} presensi telah diupdate dengan denda, {$inserted_alpa_count} presensi alpa telah dibuat.",
                'updated_count' => $updated_count,
                'inserted_alpa_count' => $inserted_alpa_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunci laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function batalkanKunciLaporan(Request $request)
    {
        try {
            $generalsetting = Pengaturanumum::where('id', 1)->first();
            $periode_laporan_dari = $generalsetting->periode_laporan_dari;
            $periode_laporan_sampai = $generalsetting->periode_laporan_sampai;
            $periode_laporan_lintas_bulan = $generalsetting->periode_laporan_next_bulan;
            
            if ($request->periode_laporan == 1) {
                if ($periode_laporan_lintas_bulan == 1) {
                    if ($request->bulan == 1) {
                        $bulan = 12;
                        $tahun = $request->tahun - 1;
                    } else {
                        $bulan = $request->bulan - 1;
                        $tahun = $request->tahun;
                    }
                } else {
                    $bulan = $request->bulan;
                    $tahun = $request->tahun;
                }

                $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
                $periode_dari = $tahun . '-' . $bulan . '-' . $periode_laporan_dari;
                $periode_sampai = $request->tahun . '-' . $request->bulan . '-' . $periode_laporan_sampai;
            } else {
                $bulan = str_pad($request->bulan, 2, '0', STR_PAD_LEFT);
                $periode_dari = $request->tahun . '-' . $bulan . '-01';
                $periode_sampai = date('Y-m-t', strtotime($periode_dari));
            }

            $gaji_pokok = Gajipokok::select(
                'nik',
                'jumlah',
                'jenis_upah'
            )
                ->whereIn('kode_gaji', function ($query) use ($periode_sampai) {
                    $query->select(DB::raw('MAX(kode_gaji)'))
                        ->from('karyawan_gaji_pokok')
                        ->where('tanggal_berlaku', '<=', $periode_sampai)
                        ->groupBy('nik');
                });

            // Query untuk mendapatkan ID presensi yang akan diupdate
            $presensi_query = Presensi::query()
                ->select('presensi.id')
                ->whereBetween('presensi.tanggal', [$periode_dari, $periode_sampai]);

            // Filter berdasarkan request - perlu join dengan karyawan jika ada filter
            if (!empty($request->kode_cabang) || !empty($request->kode_dept)) {
                $presensi_query->leftJoin('karyawan', 'presensi.nik', '=', 'karyawan.nik');
            }

            if (!empty($request->kode_cabang)) {
                $presensi_query->where('karyawan.kode_cabang', $request->kode_cabang);
            }

            if (!empty($request->kode_dept)) {
                $presensi_query->where('karyawan.kode_dept', $request->kode_dept);
            }

            if (!empty($request->nik)) {
                $presensi_query->where('presensi.nik', $request->nik);
            }

            if (!empty($request->jenis_upah)) {
                $presensi_query->leftJoinSub($gaji_pokok, 'gaji_pokok', function ($join) {
                    $join->on('presensi.nik', '=', 'gaji_pokok.nik');
                });
                $presensi_query->where('gaji_pokok.jenis_upah', $request->jenis_upah);
            }

            // Ambil ID presensi yang akan diupdate
            $presensi_ids = $presensi_query->pluck('presensi.id')->toArray();

            // Update denda menjadi null untuk membatalkan kunci
            $updated_count = 0;
            if (!empty($presensi_ids)) {
                $updated_count = Presensi::whereIn('id', $presensi_ids)->update([
                    'denda' => null,
                    'status_potongan' => null
                ]);
            }

            /**
             * Hapus presensi ALPA yang dibuat otomatis saat kunci laporan
             * Kriteria:
             * - status = 'a'
             * - tanggal dalam periode
             * - sesuai filter cabang/dept/nik
             */
            $alpa_query = Presensi::query()
                ->whereBetween('presensi.tanggal', [$periode_dari, $periode_sampai])
                ->where('presensi.status', 'a');

            if (!empty($request->kode_cabang) || !empty($request->kode_dept)) {
                $alpa_query->leftJoin('karyawan', 'presensi.nik', '=', 'karyawan.nik');
            }

            if (!empty($request->kode_cabang)) {
                $alpa_query->where('karyawan.kode_cabang', $request->kode_cabang);
            }

            if (!empty($request->kode_dept)) {
                $alpa_query->where('karyawan.kode_dept', $request->kode_dept);
            }

            if (!empty($request->nik)) {
                $alpa_query->where('presensi.nik', $request->nik);
            }

            if (!empty($request->jenis_upah)) {
                $alpa_query->leftJoinSub($gaji_pokok, 'gaji_pokok', function ($join) {
                    $join->on('presensi.nik', '=', 'gaji_pokok.nik');
                });
                $alpa_query->where('gaji_pokok.jenis_upah', $request->jenis_upah);
            }

            $alpa_ids = $alpa_query->pluck('presensi.id')->toArray();
            $deleted_alpa_count = 0;
            if (!empty($alpa_ids)) {
                $deleted_alpa_count = Presensi::whereIn('id', $alpa_ids)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => "Kunci laporan berhasil dibatalkan. Total {$updated_count} presensi telah diupdate, {$deleted_alpa_count} presensi alpa telah dihapus.",
                'updated_count' => $updated_count,
                'deleted_alpa_count' => $deleted_alpa_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan kunci laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function jadwal()
    {
        $data['list_bulan'] = config('global.list_bulan');
        $data['start_year'] = config('global.start_year');
        $cabang = Cabang::orderBy('kode_cabang')->get();
        $departemen = Departemen::orderBy('kode_dept')->get();
        $data['cabang'] = $cabang;
        $data['departemen'] = $departemen;
        return view('laporan.jadwal', $data);
    }

    public function cetakjadwal(Request $request)
    {
        $generalsetting = Pengaturanumum::where('id', 1)->first();
        $periode_dari = $request->dari;
        $periode_sampai = $request->sampai;

        // 1) Jadwal by-date per karyawan
        $jadwal_bydate = DB::table('presensi_jamkerja_bydate')
            ->join('presensi_jamkerja', 'presensi_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi_jamkerja_bydate.nik',
                'presensi_jamkerja_bydate.tanggal',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.color'
            )
            ->whereBetween('presensi_jamkerja_bydate.tanggal', [$periode_dari, $periode_sampai])
            ->get()
            ->groupBy('nik')
            ->map(function ($rows) {
                $result = [];
                foreach ($rows as $row) {
                    $result[$row->tanggal] = [
                        'nama_jam_kerja' => $row->nama_jam_kerja,
                        'jam_masuk' => $row->jam_masuk,
                        'jam_pulang' => $row->jam_pulang,
                        'color' => $row->color,
                    ];
                }
                return $result;
            });

        // 2) Jadwal grup by-date
        $jadwal_grup_bydate = DB::table('grup_detail')
            ->join('grup_jamkerja_bydate', 'grup_detail.kode_grup', '=', 'grup_jamkerja_bydate.kode_grup')
            ->join('presensi_jamkerja', 'grup_jamkerja_bydate.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'grup_detail.nik',
                'grup_jamkerja_bydate.tanggal',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.color'
            )
            ->whereBetween('grup_jamkerja_bydate.tanggal', [$periode_dari, $periode_sampai])
            ->get()
            ->groupBy('nik')
            ->map(function ($rows) {
                $result = [];
                foreach ($rows as $row) {
                    $result[$row->tanggal] = [
                        'nama_jam_kerja' => $row->nama_jam_kerja,
                        'jam_masuk' => $row->jam_masuk,
                        'jam_pulang' => $row->jam_pulang,
                        'color' => $row->color,
                    ];
                }
                return $result;
            });

        // 3) Jadwal by-day per karyawan
        $jadwal_byday = DB::table('presensi_jamkerja_byday')
            ->join('presensi_jamkerja', 'presensi_jamkerja_byday.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi_jamkerja_byday.nik',
                'presensi_jamkerja_byday.hari',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.color'
            )
            ->get()
            ->groupBy('nik')
            ->map(function ($rows) {
                $result = [];
                foreach ($rows as $row) {
                    $result[$row->hari] = [
                        'nama_jam_kerja' => $row->nama_jam_kerja,
                        'jam_masuk' => $row->jam_masuk,
                        'jam_pulang' => $row->jam_pulang,
                        'color' => $row->color,
                    ];
                }
                return $result;
            });

        // 4) Jadwal by-day per departemen & cabang
        $jadwal_bydept = DB::table('presensi_jamkerja_bydept_detail')
            ->join('presensi_jamkerja_bydept', 'presensi_jamkerja_bydept_detail.kode_jk_dept', '=', 'presensi_jamkerja_bydept.kode_jk_dept')
            ->join('presensi_jamkerja', 'presensi_jamkerja_bydept_detail.kode_jam_kerja', '=', 'presensi_jamkerja.kode_jam_kerja')
            ->select(
                'presensi_jamkerja_bydept.kode_dept',
                'presensi_jamkerja_bydept.kode_cabang',
                'presensi_jamkerja_bydept_detail.hari',
                'presensi_jamkerja.nama_jam_kerja',
                'presensi_jamkerja.jam_masuk',
                'presensi_jamkerja.jam_pulang',
                'presensi_jamkerja.color'
            )
            ->get()
            ->groupBy(function ($row) {
                return $row->kode_dept . '|' . $row->kode_cabang;
            })
            ->map(function ($rows) {
                $result = [];
                foreach ($rows as $row) {
                    $result[$row->hari] = [
                        'nama_jam_kerja' => $row->nama_jam_kerja,
                        'jam_masuk' => $row->jam_masuk,
                        'jam_pulang' => $row->jam_pulang,
                        'color' => $row->color,
                    ];
                }
                return $result;
            });

        $q_karyawan = Karyawan::query();
        $q_karyawan->select('karyawan.nik', 'karyawan.nik_show', 'nama_karyawan', 'nama_jabatan', 'karyawan.kode_dept', 'nama_dept', 'karyawan.kode_cabang');
        $q_karyawan->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan');
        $q_karyawan->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');

        if (!empty($request->kode_cabang)) {
            $q_karyawan->where('karyawan.kode_cabang', $request->kode_cabang);
        }
        if (!empty($request->kode_dept)) {
            $q_karyawan->where('karyawan.kode_dept', $request->kode_dept);
        }
        if (!empty($request->nik)) {
            $q_karyawan->where('karyawan.nik', $request->nik);
        }

        $q_karyawan->orderBy('karyawan.nama_karyawan');
        $karyawan = $q_karyawan->get();

        $data['periode_dari'] = $periode_dari;
        $data['periode_sampai'] = $periode_sampai;
        $data['jmlhari'] = hitungJumlahHari($periode_dari, $periode_sampai) + 1;
        $data['datalibur'] = getdatalibur($periode_dari, $periode_sampai);
        $data['generalsetting'] = $generalsetting;
        $data['jadwal_bydate'] = $jadwal_bydate;
        $data['jadwal_grup_bydate'] = $jadwal_grup_bydate;
        $data['jadwal_byday'] = $jadwal_byday;
        $data['jadwal_bydept'] = $jadwal_bydept;
        $data['karyawan'] = $karyawan;

        return view('laporan.jadwal_cetak', $data);
    }

    public function lemburdetail($nik, $dari, $sampai)
    {
        $karyawan = Karyawan::where('nik', $nik)
            ->leftJoin('jabatan', 'karyawan.kode_jabatan', '=', 'jabatan.kode_jabatan')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select('karyawan.*', 'jabatan.nama_jabatan', 'departemen.nama_dept')
            ->first();

        $datalibur = getdatalibur($dari, $sampai);
        $datalembur = getlembur($dari, $sampai);
        $generalsetting = Pengaturanumum::where('id', 1)->first();

        $data['karyawan'] = $karyawan;
        $data['dari'] = $dari;
        $data['sampai'] = $sampai;
        $data['datalibur'] = $datalibur;
        $data['datalembur'] = $datalembur;
        $data['generalsetting'] = $generalsetting;

        return view('laporan.lembur_detail_cetak', $data);
    }

    /**
     * Laporan Kehadiran - fungsi untuk menampilkan halaman laporan kehadiran
     */
    public function kehadiran(Request $request)
    {
        $tanggal_mulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggal_akhir = $request->tanggal_akhir ?? date('Y-m-t');

        // Query 1: Dari tabel presensi
        $query_presensi = Presensi::query()
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                'presensi.id',
                'presensi.nik',
                'presensi.tanggal',
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.status',
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'presensi' as source"),
                DB::raw("NULL as sampai")
            )
            ->whereBetween('presensi.tanggal', [$tanggal_mulai, $tanggal_akhir]);

        // Query 2: Dari izin absen yang approved
        $query_izin_absen = DB::table('presensi_izinabsen')
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izinabsen.nik',
                'presensi_izinabsen.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'i' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izinabsen' as source"),
                'presensi_izinabsen.sampai'
            )
            ->where('presensi_izinabsen.status', 1)
            ->where('presensi_izinabsen.dari', '<=', $tanggal_akhir)
            ->where('presensi_izinabsen.sampai', '>=', $tanggal_mulai);

        // Query 3: Dari izin sakit yang approved
        $query_izin_sakit = DB::table('presensi_izinsakit')
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izinsakit.nik',
                'presensi_izinsakit.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'s' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izinsakit' as source"),
                'presensi_izinsakit.sampai'
            )
            ->where('presensi_izinsakit.status', 1)
            ->where('presensi_izinsakit.dari', '<=', $tanggal_akhir)
            ->where('presensi_izinsakit.sampai', '>=', $tanggal_mulai);

        // Query 4: Dari izin cuti yang approved
        $query_izin_cuti = DB::table('presensi_izincuti')
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izincuti.nik',
                'presensi_izincuti.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'c' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izincuti' as source"),
                'presensi_izincuti.sampai'
            )
            ->where('presensi_izincuti.status', 1)
            ->where('presensi_izincuti.dari', '<=', $tanggal_akhir)
            ->where('presensi_izincuti.sampai', '>=', $tanggal_mulai);

        // Query 5: Dari izin dinas yang approved
        $query_izin_dinas = DB::table('presensi_izindinas')
            ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izindinas.nik',
                'presensi_izindinas.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'d' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izindinas' as source"),
                'presensi_izindinas.sampai'
            )
            ->where('presensi_izindinas.status', 1)
            ->where('presensi_izindinas.dari', '<=', $tanggal_akhir)
            ->where('presensi_izindinas.sampai', '>=', $tanggal_mulai);

        // Combine semua query - TANPA FILTER DULU
        $presensis_all = $query_presensi
            ->unionAll($query_izin_absen)
            ->unionAll($query_izin_sakit)
            ->unionAll($query_izin_cuti)
            ->unionAll($query_izin_dinas)
            ->orderBy('nik', 'ASC')
            ->orderBy('tanggal', 'ASC')
            ->get();

        // Expand range untuk multi-day izin dengan batasan periode
        $expanded_data = collect();
        foreach ($presensis_all as $item) {
            if (in_array($item->source ?? null, ['izinabsen', 'izinsakit', 'izincuti', 'izindinas']) && !empty($item->sampai)) {
                // Expand multi-day izin hanya dalam periode laporan
                $dari_date = max($item->tanggal, $tanggal_mulai);
                $sampai_date = min($item->sampai, $tanggal_akhir);
                $current_date = $dari_date;
                
                while (strtotime($current_date) <= strtotime($sampai_date)) {
                    $item_copy = clone $item;
                    $item_copy->tanggal = $current_date;
                    $expanded_data->push($item_copy);
                    $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                }
            } else {
                $expanded_data->push($item);
            }
        }

        // Apply filters setelah expansion (collection filter)
        $presensis_filtered = $expanded_data->filter(function ($item) use ($request) {
            // Filter by nama_karyawan
            if ($request->filled('nama_karyawan')) {
                if (stripos($item->nama_karyawan ?? '', $request->nama_karyawan) === false) {
                    return false;
                }
            }

            // Filter by kode_cabang
            if ($request->filled('kode_cabang')) {
                if ($item->kode_cabang != $request->kode_cabang) {
                    return false;
                }
            }

            // Filter by kode_dept
            if ($request->filled('kode_dept')) {
                if ($item->kode_dept != $request->kode_dept) {
                    return false;
                }
            }

            return true;
        })->values();

        $mode = $request->get('mode', 'detail');

        // Manual pagination with LengthAwarePaginator
        $page = request('page', 1);
        $per_page = 30;
        $total = $presensis_filtered->count();
        
        $presensis = new \Illuminate\Pagination\LengthAwarePaginator(
            $presensis_filtered->slice(($page - 1) * $per_page, $per_page)->values(),
            $total,
            $per_page,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        $data['presensis'] = $presensis;
        $data['tanggal_mulai'] = $tanggal_mulai;
        $data['tanggal_akhir'] = $tanggal_akhir;
        $data['cabangs'] = Cabang::orderBy('nama_cabang')->get();
        $data['departemen'] = Departemen::orderBy('nama_dept')->get();
        
        // Load ALL karyawan untuk dropdown
        $data['karyawans'] = Karyawan::orderBy('nama_karyawan')->get();
        $data['mode'] = $mode;
        $data['rekapPresensis'] = $mode === 'rekap' ? $this->generateRekapPresensi($presensis_filtered) : collect();
        
        return view('laporan.kehadiran', $data);
    }

    /**
     * Laporan Kehadiran - fungsi untuk cetak laporan
     */
    public function kehadiranCetak(Request $request)
    {
        $tanggal_mulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggal_akhir = $request->tanggal_akhir ?? date('Y-m-t');

        // Query 1: Dari tabel presensi
        $query_presensi = Presensi::query()
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                'presensi.id',
                'presensi.nik',
                'presensi.tanggal',
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.status',
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'presensi' as source"),
                DB::raw("NULL as sampai")
            )
            ->whereBetween('presensi.tanggal', [$tanggal_mulai, $tanggal_akhir]);

        // Query 2: Dari izin absen yang approved
        $query_izin_absen = DB::table('presensi_izinabsen')
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izinabsen.nik',
                'presensi_izinabsen.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'i' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izinabsen' as source"),
                'presensi_izinabsen.sampai'
            )
            ->where('presensi_izinabsen.status', 1)
            ->where('presensi_izinabsen.dari', '<=', $tanggal_akhir)
            ->where('presensi_izinabsen.sampai', '>=', $tanggal_mulai);

        // Query 3: Dari izin sakit yang approved
        $query_izin_sakit = DB::table('presensi_izinsakit')
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izinsakit.nik',
                'presensi_izinsakit.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'s' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izinsakit' as source"),
                'presensi_izinsakit.sampai'
            )
            ->where('presensi_izinsakit.status', 1)
            ->where('presensi_izinsakit.dari', '<=', $tanggal_akhir)
            ->where('presensi_izinsakit.sampai', '>=', $tanggal_mulai);

        // Query 4: Dari izin cuti yang approved
        $query_izin_cuti = DB::table('presensi_izincuti')
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izincuti.nik',
                'presensi_izincuti.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'c' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izincuti' as source"),
                'presensi_izincuti.sampai'
            )
            ->where('presensi_izincuti.status', 1)
            ->where('presensi_izincuti.dari', '<=', $tanggal_akhir)
            ->where('presensi_izincuti.sampai', '>=', $tanggal_mulai);

        // Query 5: Dari izin dinas yang approved
        $query_izin_dinas = DB::table('presensi_izindinas')
            ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izindinas.nik',
                'presensi_izindinas.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'d' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izindinas' as source"),
                'presensi_izindinas.sampai'
            )
            ->where('presensi_izindinas.status', 1)
            ->where('presensi_izindinas.dari', '<=', $tanggal_akhir)
            ->where('presensi_izindinas.sampai', '>=', $tanggal_mulai);

        // Combine semua query - TANPA FILTER DULU
        $presensis_result = $query_presensi
            ->unionAll($query_izin_absen)
            ->unionAll($query_izin_sakit)
            ->unionAll($query_izin_cuti)
            ->unionAll($query_izin_dinas)
            ->orderBy('nik', 'ASC')
            ->orderBy('tanggal', 'ASC')
            ->get();

        // Expand range untuk multi-day izin dengan batasan periode
        $expanded_data = collect();
        foreach ($presensis_result as $item) {
            if (in_array($item->source ?? null, ['izinabsen', 'izinsakit', 'izincuti', 'izindinas']) && !empty($item->sampai)) {
                // Expand multi-day izin hanya dalam periode laporan
                $dari_date = max($item->tanggal, $tanggal_mulai);
                $sampai_date = min($item->sampai, $tanggal_akhir);
                $current_date = $dari_date;
                
                while (strtotime($current_date) <= strtotime($sampai_date)) {
                    $item_copy = clone $item;
                    $item_copy->tanggal = $current_date;
                    $expanded_data->push($item_copy);
                    $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                }
            } else {
                $expanded_data->push($item);
            }
        }

        // Apply filters setelah expansion (collection filter)
        $presensis = $expanded_data->filter(function ($item) use ($request) {
            // Filter by nama_karyawan
            if ($request->filled('nama_karyawan')) {
                if (stripos($item->nama_karyawan ?? '', $request->nama_karyawan) === false) {
                    return false;
                }
            }

            // Filter by kode_cabang
            if ($request->filled('kode_cabang')) {
                if ($item->kode_cabang != $request->kode_cabang) {
                    return false;
                }
            }

            // Filter by kode_dept
            if ($request->filled('kode_dept')) {
                if ($item->kode_dept != $request->kode_dept) {
                    return false;
                }
            }

            return true;
        })
        ->sortBy([['nik', 'asc'], ['tanggal', 'asc']])
        ->values();

        $mode = $request->get('mode', 'detail');
        $data['presensis'] = $presensis;
        $data['tanggal_mulai'] = $tanggal_mulai;
        $data['tanggal_akhir'] = $tanggal_akhir;
        $data['mode'] = $mode;
        $data['rekapPresensis'] = $mode === 'rekap' ? $this->generateRekapPresensi($presensis) : collect();
        return view('laporan.kehadiran_cetak', $data);
    }

    /**
     * Laporan Kehadiran - fungsi untuk export ke Excel
     */
    public function kehadiranExport(Request $request)
    {
        $tanggal_mulai = $request->tanggal_mulai ?? date('Y-m-01');
        $tanggal_akhir = $request->tanggal_akhir ?? date('Y-m-t');

        // Query 1: Dari tabel presensi
        $query_presensi = Presensi::query()
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                'presensi.id',
                'presensi.nik',
                'presensi.tanggal',
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.status',
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'presensi' as source"),
                DB::raw("NULL as sampai")
            )
            ->whereBetween('presensi.tanggal', [$tanggal_mulai, $tanggal_akhir]);

        // Query 2: Dari izin absen yang approved
        $query_izin_absen = DB::table('presensi_izinabsen')
            ->join('karyawan', 'presensi_izinabsen.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izinabsen.nik',
                'presensi_izinabsen.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'i' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izinabsen' as source"),
                'presensi_izinabsen.sampai'
            )
            ->where('presensi_izinabsen.status', 1)
            ->where('presensi_izinabsen.dari', '<=', $tanggal_akhir)
            ->where('presensi_izinabsen.sampai', '>=', $tanggal_mulai);

        // Query 3: Dari izin sakit yang approved
        $query_izin_sakit = DB::table('presensi_izinsakit')
            ->join('karyawan', 'presensi_izinsakit.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izinsakit.nik',
                'presensi_izinsakit.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'s' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izinsakit' as source"),
                'presensi_izinsakit.sampai'
            )
            ->where('presensi_izinsakit.status', 1)
            ->where('presensi_izinsakit.dari', '<=', $tanggal_akhir)
            ->where('presensi_izinsakit.sampai', '>=', $tanggal_mulai);

        // Query 4: Dari izin cuti yang approved
        $query_izin_cuti = DB::table('presensi_izincuti')
            ->join('karyawan', 'presensi_izincuti.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izincuti.nik',
                'presensi_izincuti.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'c' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izincuti' as source"),
                'presensi_izincuti.sampai'
            )
            ->where('presensi_izincuti.status', 1)
            ->where('presensi_izincuti.dari', '<=', $tanggal_akhir)
            ->where('presensi_izincuti.sampai', '>=', $tanggal_mulai);

        // Query 5: Dari izin dinas yang approved
        $query_izin_dinas = DB::table('presensi_izindinas')
            ->join('karyawan', 'presensi_izindinas.nik', '=', 'karyawan.nik')
            ->leftJoin('cabang', 'karyawan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->select(
                DB::raw('NULL as id'),
                'presensi_izindinas.nik',
                'presensi_izindinas.dari as tanggal',
                DB::raw('NULL as jam_in'),
                DB::raw('NULL as jam_out'),
                DB::raw("'d' as status"),
                'karyawan.nama_karyawan',
                'karyawan.nik_show',
                'karyawan.kode_cabang',
                'karyawan.kode_dept',
                'cabang.nama_cabang',
                'departemen.nama_dept',
                DB::raw("'izindinas' as source"),
                'presensi_izindinas.sampai'
            )
            ->where('presensi_izindinas.status', 1)
            ->where('presensi_izindinas.dari', '<=', $tanggal_akhir)
            ->where('presensi_izindinas.sampai', '>=', $tanggal_mulai);

        // Combine semua query - TANPA FILTER DULU
        $presensis_result = $query_presensi
            ->unionAll($query_izin_absen)
            ->unionAll($query_izin_sakit)
            ->unionAll($query_izin_cuti)
            ->unionAll($query_izin_dinas)
            ->orderBy('nik', 'ASC')
            ->orderBy('tanggal', 'ASC')
            ->get();

        // Expand range untuk multi-day izin
        $expanded_data = collect();
        foreach ($presensis_result as $item) {
            if (in_array($item->source ?? null, ['izinabsen', 'izinsakit', 'izincuti', 'izindinas']) && !empty($item->sampai)) {
                // Expand multi-day izin hanya dalam periode laporan
                $dari_date = max($item->tanggal, $tanggal_mulai);
                $sampai_date = min($item->sampai, $tanggal_akhir);
                $current_date = $dari_date;
                
                while (strtotime($current_date) <= strtotime($sampai_date)) {
                    $item_copy = clone $item;
                    $item_copy->tanggal = $current_date;
                    $expanded_data->push($item_copy);
                    $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                }
            } else {
                $expanded_data->push($item);
            }
        }

        // Apply filters setelah expansion (collection filter)
        $presensis = $expanded_data->filter(function ($item) use ($request) {
            // Filter by nama_karyawan
            if ($request->filled('nama_karyawan')) {
                if (stripos($item->nama_karyawan ?? '', $request->nama_karyawan) === false) {
                    return false;
                }
            }

            // Filter by kode_cabang
            if ($request->filled('kode_cabang')) {
                if ($item->kode_cabang != $request->kode_cabang) {
                    return false;
                }
            }

            // Filter by kode_dept
            if ($request->filled('kode_dept')) {
                if ($item->kode_dept != $request->kode_dept) {
                    return false;
                }
            }

            return true;
        })
        ->sortBy([['nik', 'asc'], ['tanggal', 'asc']])
        ->values();

        $mode = $request->get('mode', 'detail');
        $exportPresensis = $mode === 'rekap' ? $this->generateRekapPresensi($presensis) : $presensis;

        // Return Excel export
        return Excel::download(new KehadiranExport(['presensis' => $exportPresensis, 'mode' => $mode]), 'laporan_kehadiran_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Generate rekap summary data for laporan kehadiran
     */
    private function generateRekapPresensi($items)
    {
        $rekap = [];

        foreach ($items as $item) {
            $nik = $item->nik;
            if (!isset($rekap[$nik])) {
                $rekap[$nik] = [
                    'nik' => $item->nik,
                    'nama_karyawan' => $item->nama_karyawan,
                    'nik_show' => $item->nik_show,
                    'kode_cabang' => $item->kode_cabang,
                    'nama_cabang' => $item->nama_cabang,
                    'kode_dept' => $item->kode_dept,
                    'nama_dept' => $item->nama_dept,
                    'jumlah_hari' => 0,
                    'lembur' => 0,
                    'kelebihan_menit' => 0,
                ];
            }

            $status = $item->status ?? null;
            $is_izin = in_array($status, ['i', 's', 'c', 'd']);
            $jam_in = $item->jam_in ?? $item->jam_masuk ?? null;
            $jam_out = $item->jam_out ?? $item->jam_pulang ?? null;

            if (!$is_izin && $jam_in && $jam_out) {
                $time_in = strtotime($jam_in);
                $time_out = strtotime($jam_out);
                if ($time_out < $time_in) {
                    $time_out = strtotime('+1 day', $time_out);
                }

                $durasi = ($time_out - $time_in) / 60;
                $jumlah_hari = ($durasi >= 420) ? 1 : 0;
                $lembur = ($durasi >= 720) ? 1 : 0;

                if ($jumlah_hari == 1 && $lembur == 1) {
                    // Jika hari 1 dan lembur 1: durasi - 780
                    $kelebihan_menit = $durasi - 780;
                } else if ($jumlah_hari == 1 && $lembur == 0) {
                    // Jika hari 1 dan lembur 0: durasi - 480
                    $kelebihan_menit = $durasi - 480;
                } else if ($jumlah_hari == 0 && $lembur == 0) {
                    // Jika hari 0 dan lembur 0: durasi dalam menit
                    $kelebihan_menit = $durasi;
                } else {
                    // Untuk kasus lainnya
                    $kelebihan_menit = 0;
                }

                $rekap[$nik]['jumlah_hari'] += $jumlah_hari;
                $rekap[$nik]['lembur'] += $lembur;
                $rekap[$nik]['kelebihan_menit'] += $kelebihan_menit;
            }
        }

        return collect(array_values($rekap));
    }

    /**
     * Get karyawan by cabang and/or departemen (API endpoint)
     */
    public function getKaryawanByCabang(Request $request)
    {
        $query = Karyawan::query();

        if ($request->filled('kode_cabang')) {
            $query->where('kode_cabang', $request->kode_cabang);
        }

        if ($request->filled('kode_dept')) {
            $query->where('kode_dept', $request->kode_dept);
        }

        $karyawans = $query->orderBy('nama_karyawan')->get();

        return response()->json($karyawans);
    }
}
