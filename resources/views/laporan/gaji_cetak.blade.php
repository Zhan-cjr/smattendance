<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Gaji {{ date('Y-m-d H:i:s') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/report.css') }}">
    <style>
        p {
            line-height: 1rem !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    </style>
</head>

<body>
    <div class="header" style="margin-bottom: 10px">
        <table>
            <tr>
                <td>
                    @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                        <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo Perusahaan" style="max-width: 100px;">
                    @else
                        <img src="https://placehold.co/100x100?text=Logo" alt="Logo Default" style="max-width: 100px;">
                    @endif
                </td>
                <td>
                    <h4 style="line-height: 20px; margin-bottom: 5px">
                        LAPORAN GAJI
                        <br>
                        {{ $generalsetting->nama_perusahaan }}
                        <br>
                        PERIODE {{ date('d-m-Y', strtotime($periode_dari)) }} -
                        {{ date('d-m-Y', strtotime($periode_sampai)) }}
                    </h4>
                    <span style="font-style: italic;">{{ $generalsetting->alamat }}</span><br>
                    <span style="font-style: italic;">{{ $generalsetting->telepon }}</span>
                </td>
            </tr>
        </table>
    </div>
    <div class="content">
        <table class="datatable3">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Nik</th>
                    <th rowspan="2">Nama Karyawan</th>
                    <th rowspan="2">Jabatan</th>
                    <th rowspan="2">Dept</th>
                    <th rowspan="2">Cabang</th>
                    <th rowspan="2">Gaji Pokok</th>
                    <th colspan="{{ count($jenis_tunjangan) }}">Tunjangan</th>
                    <th rowspan="2" style="background: orange; color:white">&#x3A3; Bruto</th>
                    <th rowspan="2">&#x3A3; Jam Kerja</th>
                    <th rowspan="2">Upah/Jam</th>
                    <th rowspan="2" style="background:red; color:white">Denda</th>
                    <th colspan="2" style="background:red; color:white">Pot. Jam</th>
                    <th colspan="2" style="background:red; color:white">BPJS</th>
                    <th rowspan="2" style="background:red; color:white">Pinjaman</th>
                    <th rowspan="2" style="background:red; color:white">Potongan</th>
                    <th colspan="2" style="background:rgb(0, 113, 72); color:white">Lembur</th>
                    <th colspan="2" style="background:rgb(1, 118, 197); color:white">Penyesuaian</th>
                    <th rowspan="2" style="background:rgb(0, 113, 72); color:white">Gaji Bersih</th>
                </tr>
                <tr>
                    @foreach ($jenis_tunjangan as $j)
                        <th>{{ $j->jenis_tunjangan }}</th>
                    @endforeach
                    <th style="background:red; color:white">Jam</th>
                    <th style="background:red; color:white">Jumlah</th>

                    <th style="background:red; color:white">Kesehatan</th>
                    <th style="background:red; color:white">Tenaga Kerja</th>

                    <th style="background:rgb(0, 113, 72); color:white">Jam (A|N)</th>
                    <th style="background:rgb(0, 113, 72); color:white">Jumlah</th>

                    <th style="background:rgb(1, 118, 197); color:white">Penambah</th>
                    <th style="background:rgb(1, 118, 197); color:white">Pengurang</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_gaji_pokok = 0;
                    foreach ($jenis_tunjangan as $j) {
                        ${'total_tunjangan_' . $j->kode_jenis_tunjangan} = 0;
                    }
                    $total_bruto = 0;
                    $total_all_denda = 0;
                    $total_jumlah_potongan_jam = 0;
                    $total_gaji_bersih = 0;
                    $total_bpjs_kesehatan = 0;
                    $total_bpjs_tenagakerja = 0;
                    $total_all_potongan = 0;
                    $total_upah_lembur = 0;
                    $total_penambah = 0;
                    $total_pengurang = 0;
                @endphp
                @foreach ($laporan_presensi as $d)
                    @php
                        $tanggal_presensi = $periode_dari;
                        // Mapping jadwal untuk NIK ini dari berbagai sumber (sama seperti presensi_cetak)
                        $mapJadwalByDate = $jadwal_bydate[$d['nik']] ?? [];
                        $mapJadwalGrupByDate = $jadwal_grup_bydate[$d['nik']] ?? [];
                        $mapJadwalByDay = $jadwal_byday[$d['nik']] ?? [];
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>'{{ $d['nik_show'] ?? $d['nik'] }}</td>
                        <td>{{ $d['nama_karyawan'] }}</td>
                        <td>{{ $d['nama_jabatan'] }}</td>
                        <td>{{ $d['kode_dept'] }}</td>
                        <td>{{ $d['kode_cabang'] }}</td>
                        <td style="text-align: right">{{ formatAngka($d['gaji_pokok']) }}</td>
                        @php
                            $total_tunjangan = 0;
                        @endphp
                        @foreach ($jenis_tunjangan as $j)
                            @php
                                $total_tunjangan += $d[$j->kode_jenis_tunjangan];
                                ${'total_tunjangan_' . $j->kode_jenis_tunjangan} += $d[$j->kode_jenis_tunjangan];
                            @endphp
                            <td style="text-align: right">{{ formatAngka($d[$j->kode_jenis_tunjangan]) }}</td>
                        @endforeach
                        <td style="text-align: right">
                            @php
                                $bruto = $d['gaji_pokok'] + $total_tunjangan;
                            @endphp
                            {{ formatAngka($bruto) }}
                        </td>
                        <td style="text-align: center">{{ $generalsetting->total_jam_bulan }}</td>
                        <td style="text-align: right">
                            @php
                                $upah_perjam = $d['gaji_pokok'] / $generalsetting->total_jam_bulan;
                            @endphp
                            {{ formatAngka($upah_perjam) }}
                        </td>
                        @php
                            $total_denda = 0;
                            $total_potongan_jam = 0;
                            $total_jam_lembur_aktual = 0;
                            $total_jam_netto_lembur = 0;
                        @endphp
                        @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                            @php
                                $denda = 0;
                                $potongan_jam = 0;
                                $search = [
                                    'nik' => $d['nik'],
                                    'tanggal' => $tanggal_presensi,
                                ];

                                $is_libur = isLiburKaryawan($d['nik'], $tanggal_presensi);
                                $tipe_hari = $is_libur ? 2 : 1; // 1: Kerja, 2: Libur/Off

                                $ceklembur = ceklembur($datalembur, $search);
                                $lembur_aktual = hitungLembur($ceklembur);
                                if ($lembur_aktual > 0) {
                                    $jml_jam_lembur = $lembur_aktual;
                                    $jam_netto_harian = hitungJamNetto($lembur_aktual, $tipe_hari);
                                } else {
                                    $jml_jam_lembur = 0;
                                    $jam_netto_harian = 0;
                                }
                            @endphp
                            @if (isset($d[$tanggal_presensi]))
                                @if ($d[$tanggal_presensi]['status'] == 'h')
                                    @php
                                        $bgcolor = '';
                                        $textcolor = '';

                                        $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                                        $jam_in = !empty($d[$tanggal_presensi]['jam_in'])
                                            ? date('H:i', strtotime($d[$tanggal_presensi]['jam_in']))
                                            : '&#10008;';
                                        $jam_out = !empty($d[$tanggal_presensi]['jam_out'])
                                            ? date('H:i', strtotime($d[$tanggal_presensi]['jam_out']))
                                            : '&#10008;';

                                        $color_jam_in = !empty($d[$tanggal_presensi]['jam_in']) ? 'green' : 'red';
                                        $color_jam_out = !empty($d[$tanggal_presensi]['jam_out']) ? 'green' : 'red';

                                        $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);
                                        $color_terlambat = $terlambat != null ? $terlambat['color'] : '';

                                        // Jika denda sudah dikunci di database, gunakan nilai tersebut
                                        $denda_dari_db =
                                            isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                                ? $d[$tanggal_presensi]['denda']
                                                : null;

                                        if ($denda_dari_db !== null) {
                                            // Denda sudah dikunci, gunakan dari DB
                                            $denda = $denda_dari_db;

                                            // Potongan jam tetap dihitung dengan rumus
                                            if ($terlambat != null) {
                                                if ($terlambat['desimal_terlambat'] < 1) {
                                                    $potongan_jam_terlambat = 0;
                                                } else {
                                                    $potongan_jam_terlambat =
                                                        $terlambat['desimal_terlambat'] > $d[$tanggal_presensi]['total_jam']
                                                            ? $d[$tanggal_presensi]['total_jam']
                                                            : $terlambat['desimal_terlambat'];
                                                }
                                            } else {
                                                $potongan_jam_terlambat = 0;
                                            }
                                        } else {
                                            // Belum dikunci → gunakan rumus hitungdenda seperti biasa
                                            if ($terlambat != null) {
                                                if ($terlambat['desimal_terlambat'] < 1) {
                                                    $potongan_jam_terlambat = 0;
                                                    $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                                                } else {
                                                    $potongan_jam_terlambat =
                                                        $terlambat['desimal_terlambat'] > $d[$tanggal_presensi]['total_jam']
                                                            ? $d[$tanggal_presensi]['total_jam']
                                                            : $terlambat['desimal_terlambat'];
                                                    $denda = 0;
                                                }
                                            } else {
                                                $potongan_jam_terlambat = 0;
                                                $denda = 0;
                                            }
                                        }

                                        $pulangcepat = hitungpulangcepat(
                                            $tanggal_presensi,
                                            $d[$tanggal_presensi]['jam_out'],
                                            $d[$tanggal_presensi]['jam_pulang'],
                                            $d[$tanggal_presensi]['istirahat'],
                                            $d[$tanggal_presensi]['jam_awal_istirahat'],
                                            $d[$tanggal_presensi]['jam_akhir_istirahat'],
                                            $d[$tanggal_presensi]['lintashari'],
                                        );
                                        $pulangcepat =
                                            $pulangcepat > $d[$tanggal_presensi]['total_jam'] ? $d[$tanggal_presensi]['total_jam'] : $pulangcepat;
                                        $color_pulang_cepat = $pulangcepat != null ? 'red' : '';

                                        $potongan_tidak_absen_masuk_atau_pulang =
                                            empty($d[$tanggal_presensi]['jam_out']) || empty($d[$tanggal_presensi]['jam_in'])
                                                ? $d[$tanggal_presensi]['total_jam']
                                                : 0;
                                        $potongan_istirahat = hitungPotonganIstirahat(
                                            $d[$tanggal_presensi]['istirahat_in'],
                                            $d[$tanggal_presensi]['istirahat_out'],
                                            $d[$tanggal_presensi]['jam_awal_istirahat'],
                                            $d[$tanggal_presensi]['jam_akhir_istirahat']
                                        );
                                        $status_potongan_istirahat = $d[$tanggal_presensi]['status_potongan_istirahat'] ?? $generalsetting->potongan_istirahat;
                                        $potongan_jam =
                                            $potongan_tidak_absen_masuk_atau_pulang == 0
                                                ? $pulangcepat + $potongan_jam_terlambat + ($status_potongan_istirahat == 1 ? $potongan_istirahat : 0)
                                                : $potongan_tidak_absen_masuk_atau_pulang;

                                        // $ket =
                                        //     $ket_nama_jam_kerja .
                                        //     $ket_jadwal_kerja .
                                        //     $ket_presensi .
                                        //     $ket_terlambat .
                                        //     $ket_denda .
                                        //     $ket_pulang_cepat .
                                        //     $ket_potongan_jam;

                                    @endphp
                                @elseif($d[$tanggal_presensi]['status'] == 'i')
                                    @php
                                        $bgcolor = '#dea51f';
                                        $textcolor = 'white';
                                        $potongan_jam = $d[$tanggal_presensi]['total_jam'];

                                        // Izin: jika denda sudah dikunci, ambil dari DB, jika tidak 0
                                        $denda_dari_db =
                                            isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                                ? $d[$tanggal_presensi]['denda']
                                                : null;
                                        $denda = $denda_dari_db !== null ? $denda_dari_db : 0;

                                    @endphp
                                @elseif($d[$tanggal_presensi]['status'] == 's')
                                    @php
                                        $bgcolor = '#c8075b';
                                        $textcolor = 'white';

                                        // Sakit: jika denda sudah dikunci, ambil dari DB, jika tidak 0
                                        $denda_dari_db =
                                            isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                                ? $d[$tanggal_presensi]['denda']
                                                : null;
                                        $denda = $denda_dari_db !== null ? $denda_dari_db : 0;
                                    @endphp
                                @elseif($d[$tanggal_presensi]['status'] == 'c')
                                    @php
                                        $bgcolor = '#0164b5';
                                        $textcolor = 'white';

                                        // Cuti: jika denda sudah dikunci, ambil dari DB, jika tidak 0
                                        $denda_dari_db =
                                            isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                                ? $d[$tanggal_presensi]['denda']
                                                : null;
                                        $denda = $denda_dari_db !== null ? $denda_dari_db : 0;
                                    @endphp
                                @elseif($d[$tanggal_presensi]['status'] == 'a')
                                    @php
                                        $bgcolor = 'red';
                                        $textcolor = 'white';
                                        $potongan_jam = $d[$tanggal_presensi]['total_jam'];

                                        // Alpa: jika denda sudah dikunci, ambil dari DB, jika tidak 0
                                        $denda_dari_db =
                                            isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                                ? $d[$tanggal_presensi]['denda']
                                                : null;
                                        $denda = $denda_dari_db !== null ? $denda_dari_db : 0;
                                    @endphp
                                @endif
                            @else
                                @php
                                    $bgcolor = 'red';
                                    $textcolor = 'white';
                                    $ket = '';
                                    $potongan_jam = 0;

                                    // Jika hari ini libur khusus karyawan, tidak ada potongan jam
                                    if (!empty($ceklibur)) {
                                        $bgcolor = 'green';
                                        $textcolor = 'white';
                                        $ket = $ceklibur[0]['keterangan'];
                                    } else {
                                        // Bukan libur → cek jadwal berurutan (sama seperti presensi_cetak):
                                        // 1) Jadwal by-date per karyawan
                                        $totalJamJadwal = $mapJadwalByDate[$tanggal_presensi] ?? null;

                                        // 2) Kalau kosong, cek jadwal grup by-date
                                        if ($totalJamJadwal === null) {
                                            $totalJamJadwal = $mapJadwalGrupByDate[$tanggal_presensi] ?? null;
                                        }

                                        // 3) Kalau masih kosong, cek jadwal by-day per karyawan
                                        if ($totalJamJadwal === null) {
                                            $nama_hari = getHari($tanggal_presensi);
                                            $totalJamJadwal = $mapJadwalByDay[$nama_hari] ?? null;
                                        }

                                        // 4) Kalau masih kosong, cek jadwal by-day per departemen & cabang
                                        if ($totalJamJadwal === null) {
                                            $nama_hari = isset($nama_hari) ? $nama_hari : getHari($tanggal_presensi);
                                            $keyDeptCabang = $d['kode_dept'] . '|' . $d['kode_cabang'];
                                            $mapDept = $jadwal_bydept[$keyDeptCabang] ?? [];
                                            $totalJamJadwal = $mapDept[$nama_hari] ?? null;
                                        }

                                        // Jika ada jadwal tapi tidak ada presensi sama sekali → potongan jam = total_jam jadwal
                                        $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                                        if ($totalJamJadwal !== null && !$is_future) {
                                            $potongan_jam = is_array($totalJamJadwal) ? $totalJamJadwal['total_jam'] : $totalJamJadwal;
                                        }
                                    }

                                @endphp
                            @endif
                            @php
                                $status_potongan_harian = isset($d[$tanggal_presensi]['status_potongan']) ? $d[$tanggal_presensi]['status_potongan'] : $generalsetting->status_potongan_jam;
                                if ($status_potongan_harian == 0) {
                                    $potongan_jam = 0;
                                }
                                $total_denda += $denda;
                                $total_potongan_jam += $potongan_jam;
                                $total_jam_lembur_aktual += $jml_jam_lembur;
                                $total_jam_netto_lembur += $jam_netto_harian;
                            @endphp
                            {{-- <td style="background-color:{{ $bgcolor }}; color:{{ $textcolor }}">
                                {!! $ket !!}
                            </td> --}}
                            @php
                                $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                            @endphp
                        @endwhile

                        @php
                            if ($total_potongan_jam > $generalsetting->total_jam_bulan) {
                                $total_potongan_jam = $generalsetting->total_jam_bulan;
                            }
                            $jumlah_potongan_jam = ROUND($upah_perjam) * $total_potongan_jam;
                            $total_potongan = ROUND($jumlah_potongan_jam) + $total_denda + $d['bpjs_kesehatan'] + $d['bpjs_tenagakerja'] + ($d['cicilan_pinjaman'] ?? 0);

                            $total_all_potongan += $total_potongan;
                            
                            // Hitung Upah Lembur menggunakan Jam Netto (Berjenjang)
                            // Basis: Gaji Pokok + Tunjangan Tetap
                            $upah_perjam_lembur = ($d['gaji_pokok'] + $total_tunjangan) / $generalsetting->total_jam_bulan;
                            $upah_lembur = ROUND($upah_perjam_lembur) * $total_jam_netto_lembur;
                            
                            $total_upah_lembur += $upah_lembur;
                            $total_gaji_pokok += $d['gaji_pokok'];
                            $total_bpjs_kesehatan += $d['bpjs_kesehatan'];
                            $total_bpjs_tenagakerja += $d['bpjs_tenagakerja'];
                            $total_penambah += $d['penambah'];
                            $total_pengurang += $d['pengurang'];
                            $total_bruto += $bruto;
                            $total_all_denda += $total_denda;
                            $total_jumlah_potongan_jam += $jumlah_potongan_jam;
                            $gaji_bersih = $d['gaji_pokok'] + $total_tunjangan - $total_potongan + $d['penambah'] - $d['pengurang'] + $upah_lembur;
                            $total_gaji_bersih += $gaji_bersih;
                        @endphp
                        <td style="text-align: right">{{ formatAngka($total_denda) }}</td>
                        <td style="text-align: center">{{ formatAngkaDesimal($total_potongan_jam) }}</td>
                        <td style="text-align: right">
                            {{ formatAngka($jumlah_potongan_jam) }}
                        </td>
                        <td style="text-align: right">{{ formatAngka($d['bpjs_kesehatan']) }}</td>
                        <td style="text-align: right">{{ formatAngka($d['bpjs_tenagakerja']) }}</td>
                        <td style="text-align: right">{{ formatAngka($d['cicilan_pinjaman'] ?? 0) }}</td>
                        <td style="text-align: right">{{ formatAngka($total_potongan) }}</td>
                        <td style="text-align: center">
                            <a href="{{ route('laporan.lemburdetail', [$d['nik'], $periode_dari, $periode_sampai]) }}" target="_blank" style="color: #024a75; text-decoration: underline;">
                                {{ formatAngkaDesimal($total_jam_netto_lembur) }}
                            </a>
                        </td>
                        <td style="text-align: right">{{ formatAngka($upah_lembur) }}</td>
                        <td style="text-align: right">{{ formatAngka($d['penambah']) }}</td>
                        <td style="text-align: right">{{ formatAngka($d['pengurang']) }}</td>
                        <td style="text-align: right">{{ formatAngka($gaji_bersih) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6">TOTAL</th>
                    <th style="text-align: right">{{ formatAngka($total_gaji_pokok) }}</th>
                    @foreach ($jenis_tunjangan as $d)
                        <th style="text-align: right">
                            {{ formatAngka(${'total_tunjangan_' . $d->kode_jenis_tunjangan}) }}</th>
                    @endforeach
                    <th style="text-align: right">{{ formatAngka($total_bruto) }}</th>
                    <th colspan="2"></th>
                    <th style="text-align: right">{{ formatAngka($total_all_denda) }}</th>
                    <th></th>
                    <th style="text-align: right">{{ formatAngka($total_jumlah_potongan_jam) }}</th>
                    <th style="text-align: right">{{ formatAngka($total_bpjs_kesehatan) }}</th>
                    <th style="text-align: right">{{ formatAngka($total_bpjs_tenagakerja) }}</th>
                    <th style="text-align: right">{{ formatAngka($laporan_presensi->sum('cicilan_pinjaman')) }}</th>
                    <th style="text-align: right">{{ formatAngka($total_all_potongan) }}</th>
                    <th></th>
                    <th style="text-align: right">{{ formatAngka($total_upah_lembur) }}</th>
                    <th style="text-align: right">{{ formatAngka($total_penambah) }}</th>
                    <th style="text-align: right">{{ formatAngka($total_pengurang) }}</th>
                    <th style="text-align: right">{{ formatAngka($total_gaji_bersih) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
