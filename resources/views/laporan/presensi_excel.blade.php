<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Presensi</title>
</head>
<body>
    <table class="datatable3" style="width: 100%; border-collapse: collapse; border: 1px solid #000000">
        <thead>
            <tr>
                <td colspan="{{ 19 + $jmlhari }}" style="font-weight: bold; font-size: 14px">LAPORAN PRESENSI</td>
            </tr>
            <tr>
                <td colspan="{{ 19 + $jmlhari }}" style="font-weight: bold; font-size: 14px">{{ $generalsetting->nama_perusahaan }}</td>
            </tr>
            <tr>
                <td colspan="{{ 19 + $jmlhari }}" style="font-size: 12px">PERIODE {{ date('d-m-Y', strtotime($periode_dari)) }} - {{ date('d-m-Y', strtotime($periode_sampai)) }}</td>
            </tr>
            <tr>
                <td colspan="{{ 19 + $jmlhari }}" style="font-size: 12px; font-style: italic;">{{ $generalsetting->alamat }}</td>
            </tr>
            <tr>
                <td colspan="{{ 19 + $jmlhari }}" style="font-size: 12px; font-style: italic;">{{ $generalsetting->telepon }}</td>
            </tr>
            <tr>
                <td colspan="{{ 18 + $jmlhari }}"></td>
            </tr>
            <tr>
                <th rowspan="3" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">No</th>
                <th rowspan="3" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Nik</th>
                <th rowspan="3" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Nama Karyawan</th>
                <th rowspan="3" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Jabatan</th>
                <th rowspan="3" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Dept</th>
                <th rowspan="3" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Cabang</th>
                <th colspan="{{ $jmlhari }}" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Tanggal</th>
                <th rowspan="3" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Jam Istirahat</th>
                <th rowspan="3" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Denda</th>
                <th rowspan="3" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Pot. Jam</th>
                <th rowspan="3" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Lembur</th>
                <th colspan="9" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Rekap</th>
            </tr>
            <tr>
                @php
                    $tanggal_presensi = $periode_dari;
                @endphp
                @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                    <th style="width: 100px; border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">{{ getHari(date('Y-m-d', strtotime($tanggal_presensi))) }}</th>
                    @php
                        $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                    @endphp
                @endwhile
                <th rowspan="2" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Hadir</th>
                <th rowspan="2" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Izin</th>
                <th rowspan="2" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Sakit</th>
                <th rowspan="2" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Alfa</th>
                <th rowspan="2" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Libur</th>
                <th rowspan="2" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Terlambat</th>
                <th rowspan="2" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Tidak Scan Masuk</th>
                <th rowspan="2" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Tidak Scan Pulang</th>
                <th rowspan="2" style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Pulang Cepat</th>
            </tr>
            <tr>
                @php
                    $tanggal_presensi = $periode_dari;
                @endphp
                @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                    <th style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">{{ date('d', strtotime($tanggal_presensi)) }}</th>
                    @php
                        $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                    @endphp
                @endwhile
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan_presensi as $d)
                @php
                    $tanggal_presensi = $periode_dari;
                    // Mapping jadwal untuk NIK ini dari berbagai sumber
                    $mapJadwalByDate = $jadwal_bydate[$d['nik']] ?? [];
                    $mapJadwalGrupByDate = $jadwal_grup_bydate[$d['nik']] ?? [];
                    $mapJadwalByDay = $jadwal_byday[$d['nik']] ?? [];
                @endphp
                <tr>
                    <td style="border: 1px solid #000000; vertical-align: middle;">{{ $loop->iteration }}</td>
                    <td style="border: 1px solid #000000; vertical-align: middle;">'{{ $d['nik_show'] ?? $d['nik'] }}</td>
                    <td style="border: 1px solid #000000; vertical-align: middle;">{{ $d['nama_karyawan'] }}</td>
                    <td style="border: 1px solid #000000; vertical-align: middle;">{{ $d['nama_jabatan'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $d['kode_dept'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $d['kode_cabang'] }}</td>
                    @php
                        $total_denda = 0;
                        $total_potongan_jam = 0;
                        $total_jam_lembur = 0;
                        $jml_hadir = 0;
                        $jml_sakit = 0;
                        $jml_izin = 0;
                        $jml_cuti = 0;
                        $jml_libur = 0;
                        $jml_alfa = 0;
                        $jml_terlambat = 0;
                        $jml_pulangcepat = 0;
                        $jml_tidakscanmasuk = 0;
                        $jml_tidakscanpulang = 0;
                        $total_potongan_istirahat = 0;
                        $rekap_istirahat = [];
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
                            $tipe_hari = $is_libur ? 2 : 1;

                            $ceklembur = ceklibur($datalembur, $search);
                            $lembur = hitungLembur($ceklembur);
                            if (!empty($ceklembur)) {
                                $jml_jam_lembur = $lembur;
                            } else {
                                $jml_jam_lembur = 0;
                            }
                            $nama_hari = getHari($tanggal_presensi);
                        @endphp
                        @if (isset($d[$tanggal_presensi]))
                            @if ($d[$tanggal_presensi]['status'] == 'h')
                                @php
                                    $bgcolor = '';
                                    $textcolor = '';
                                    $jml_hadir++;

                                    $ket_nama_jam_kerja =
                                        '<span style="font-weight:bold;">' . $d[$tanggal_presensi]['nama_jam_kerja'] . '</span><br>';
                                    $ket_jadwal_kerja =
                                        '<span style="color:blue">' .
                                        date('H:i', strtotime($d[$tanggal_presensi]['jam_masuk'])) .
                                        ' - ' .
                                        date('H:i', strtotime($d[$tanggal_presensi]['jam_pulang'])) .
                                        '</span><br>';
                                    $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                                    $jam_in = !empty($d[$tanggal_presensi]['jam_in'])
                                        ? date('H:i', strtotime($d[$tanggal_presensi]['jam_in']))
                                        : '&#10008;';
                                    $jam_out = !empty($d[$tanggal_presensi]['jam_out'])
                                        ? date('H:i', strtotime($d[$tanggal_presensi]['jam_out']))
                                        : '&#10008;';

                                    $color_jam_in = !empty($d[$tanggal_presensi]['jam_in']) ? 'green' : 'red';
                                    $color_jam_out = !empty($d[$tanggal_presensi]['jam_out']) ? 'green' : 'red';

                                    $ket_presensi =
                                        '<span style="color:' .
                                        $color_jam_in .
                                        '">' .
                                        $jam_in .
                                        '</span> -
                                        <span style="color:' .
                                        $color_jam_out .
                                        '">' .
                                        $jam_out .
                                        '</span><br>';

                                    $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);

                                    $color_terlambat = $terlambat != null ? $terlambat['color'] : '';
                                    $ket_terlambat =
                                        $terlambat != null
                                            ? '<span style="color:' .
                                                $color_terlambat .
                                                '">' .
                                                $terlambat['show_laporan'] .
                                                '</span><br>'
                                            : '';

                                    // Cek apakah denda sudah dikunci (ada di database)
                                    $denda_dari_db =
                                        isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                            ? $d[$tanggal_presensi]['denda']
                                            : null;

                                    if ($denda_dari_db !== null) {
                                        // Gunakan denda dari database (sudah dikunci)
                                        $denda = $denda_dari_db;
                                        // Hitung potongan jam tetap menggunakan rumus
                                        if ($terlambat != null) {
                                            if ($terlambat['desimal_terlambat'] < 1) {
                                                $potongan_jam_terlambat = 0;
                                            } else {
                                                $potongan_jam_terlambat =
                                                    $terlambat['desimal_terlambat'] > $d[$tanggal_presensi]['total_jam']
                                                        ? $d[$tanggal_presensi]['total_jam']
                                                        : $terlambat['desimal_terlambat'];
                                            }
                                            if ($terlambat['menitterlambat'] > 0) {
                                                $jml_terlambat++;
                                            }
                                        } else {
                                            $potongan_jam_terlambat = 0;
                                        }
                                    } else {
                                        // Hitung denda menggunakan rumus (belum dikunci)
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
                                            if ($terlambat['menitterlambat'] > 0) {
                                                $jml_terlambat++;
                                            }
                                        } else {
                                            $potongan_jam_terlambat = 0;
                                            $denda = 0;
                                        }
                                    }

                                    $ket_denda = $denda != 0 ? '<span style="color:red">Denda : ' . formatAngka($denda) . '</span><br>' : '';

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

                                    if ($pulangcepat != null) {
                                        $jml_pulangcepat++;
                                    }
                                    $ket_pulang_cepat =
                                        $pulangcepat != null ? '<span style="color:red">PC : ' . $pulangcepat . ' Jam </span><br>' : '';
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

                                    if (!empty($d[$tanggal_presensi]['istirahat_in']) && !empty($d[$tanggal_presensi]['istirahat_out'])) {
                                        $rekap_istirahat[] = '<span style="color:#3498db">' . date('H:i', strtotime($d[$tanggal_presensi]['istirahat_in'])) . ' - ' . date('H:i', strtotime($d[$tanggal_presensi]['istirahat_out'])) . '</span>';
                                    } else {
                                        $rekap_istirahat[] = '<span style="color:red">Belum Absen</span>';
                                    }
                                    $ket_potongan_jam = !empty($potongan_jam)
                                        ? '<span style="color:red">PJ: ' . formatAngkaDesimal($potongan_jam) . ' Jam</span><br>'
                                        : '';

                                    $ket_jam_lembur =
                                        $jml_jam_lembur > 0
                                            ? '<span style="color:rgb(11, 153, 179)"> Lembur :' . $jml_jam_lembur . ' Jam</span><br>'
                                            : '';
                                    $ket =
                                        $ket_nama_jam_kerja .
                                        $ket_jadwal_kerja .
                                        $ket_presensi .
                                        $ket_terlambat .
                                        $ket_denda .
                                        $ket_pulang_cepat .
                                        $ket_potongan_jam .
                                        $ket_jam_lembur;

                                    if (empty($d[$tanggal_presensi]['jam_in'])) {
                                        $jml_tidakscanmasuk++;
                                    }

                                    if (empty($d[$tanggal_presensi]['jam_out'])) {
                                        $jml_tidakscanpulang++;
                                    }
                                @endphp
                            @elseif($d[$tanggal_presensi]['status'] == 'i')
                                @php
                                    $bgcolor = '#dea51f';
                                    $textcolor = 'white';
                                    $jml_izin++;
                                    $potongan_jam = $d[$tanggal_presensi]['total_jam'];

                                    // Cek apakah denda sudah dikunci (untuk izin biasanya 0, tapi ambil dari DB jika ada)
                                    $denda_dari_db =
                                        isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                            ? $d[$tanggal_presensi]['denda']
                                            : null;
                                    $denda = $denda_dari_db !== null ? $denda_dari_db : 0;

                                    $ket =
                                        '<span style="font-weight: bold;">IZIN</span><br><span>' .
                                        $d[$tanggal_presensi]['keterangan_izin_absen'] .
                                        '</span><br>
                                        <span>PJ : ' .
                                        formatAngkaDesimal($potongan_jam) .
                                        ' Jam</span>';
                                @endphp
                            @elseif($d[$tanggal_presensi]['status'] == 's')
                                @php
                                    $bgcolor = '#c8075b';
                                    $textcolor = 'white';
                                    $jml_sakit++;

                                    // Cek apakah denda sudah dikunci (untuk sakit biasanya 0, tapi ambil dari DB jika ada)
                                    $denda_dari_db =
                                        isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                            ? $d[$tanggal_presensi]['denda']
                                            : null;
                                    $denda = $denda_dari_db !== null ? $denda_dari_db : 0;

                                    $ket =
                                        '<span style="font-weight: bold;">SAKIT</span><br><span>' .
                                        $d[$tanggal_presensi]['keterangan_izin_sakit'] .
                                        '</span>
                                        ';
                                @endphp
                            @elseif($d[$tanggal_presensi]['status'] == 'c')
                                @php
                                    $bgcolor = '#0164b5';
                                    $textcolor = 'white';
                                    $jml_cuti++;

                                    // Cek apakah denda sudah dikunci (untuk cuti biasanya 0, tapi ambil dari DB jika ada)
                                    $denda_dari_db =
                                        isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                            ? $d[$tanggal_presensi]['denda']
                                            : null;
                                    $denda = $denda_dari_db !== null ? $denda_dari_db : 0;

                                    $ket =
                                        '<span style="font-weight: bold;">CUTI</span><br><span>' .
                                        $d[$tanggal_presensi]['keterangan_izin_cuti'] .
                                        '</span>';
                                @endphp
                            @elseif($d[$tanggal_presensi]['status'] == 'a')
                                @php
                                    $bgcolor = 'red';
                                    $textcolor = 'white';
                                    $jml_alfa++;
                                    $potongan_jam = $d[$tanggal_presensi]['total_jam'];

                                    // Cek apakah denda sudah dikunci (ada di database)
                                    $denda_dari_db =
                                        isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                            ? $d[$tanggal_presensi]['denda']
                                            : null;

                                    // Untuk alpa, denda biasanya 0 atau null, tapi tetap ambil dari DB jika sudah dikunci
                                    $denda = $denda_dari_db !== null ? $denda_dari_db : 0;

                                    $ket_denda_alpa =
                                        $denda != 0 ? '<br><span style="color:red">Denda : ' . formatAngka($denda) . '</span>' : '';

                                    $ket =
                                        '<span style="font-weight: bold;">Alpa</span><br>
                                    <span>PJ : ' .
                                        formatAngkaDesimal($potongan_jam) .
                                        ' Jam</span>' .
                                        $ket_denda_alpa;
                                @endphp
                            @endif
                        @else
                            @php
                                $bgcolor = 'red';
                                $textcolor = 'white';
                                $ket = '';
                                $potongan_jam = 0;

                                // Jika hari ini libur khusus karyawan, tandai libur
                                if (!empty($ceklibur)) {
                                    $bgcolor = 'green';
                                    $textcolor = 'white';
                                    $jml_libur++;
                                    $ket = $ceklibur[0]['keterangan'];
                                } else {
                                    // Bukan libur → cek jadwal berurutan:
                                    // 1) Jadwal by-date per karyawan
                                    $totalJamJadwal = $mapJadwalByDate[$tanggal_presensi] ?? null;

                                    // 2) Kalau kosong, cek jadwal grup by-date
                                    if ($totalJamJadwal === null) {
                                        $totalJamJadwal = $mapJadwalGrupByDate[$tanggal_presensi] ?? null;
                                    }

                                    // 3) Kalau masih kosong, cek jadwal by-day per karyawan
                                    if ($totalJamJadwal === null) {
                                        $totalJamJadwal = $mapJadwalByDay[$nama_hari] ?? null;
                                    }

                                    // 4) Kalau masih kosong, cek jadwal by-day per departemen & cabang
                                    if ($totalJamJadwal === null) {
                                        $keyDeptCabang = $d['kode_dept'] . '|' . $d['kode_cabang'];
                                        $mapDept = $jadwal_bydept[$keyDeptCabang] ?? [];
                                        $totalJamJadwal = $mapDept[$nama_hari] ?? null;
                                    }

                                    if ($totalJamJadwal !== null) {
                                        // Ada jadwal tapi tidak ada presensi sama sekali → Alpa & potong full jam kerja
                                        $jml_alfa++;
                                        $potongan_jam = is_array($totalJamJadwal) ? $totalJamJadwal['total_jam'] : $totalJamJadwal;

                                        // Untuk alpa yang belum ada di database, denda = 0
                                        $denda = 0;

                                        $ket =
                                            '<span style="font-weight: bold;">Alpa</span><br>
                                            <span>PJ : ' .
                                            formatAngkaDesimal($potongan_jam) .
                                            ' Jam</span>';
                                    }
                                }

                                // Jika ada lembur terpisah, tetap tampilkan info lembur
                                if (!empty($ceklembur)) {
                                    $bgcolor = 'white';
                                    $textcolor = 'black';
                                    $ket_jam_lembur = '<span style="color:rgb(11, 153, 179)"> Lembur :' . $jml_jam_lembur . ' Jam</span><br>';
                                    $ket = $ket_jam_lembur;
                                }
                            @endphp
                        @endif
                        @php
                            $total_denda += $denda;
                            $total_potongan_jam += $potongan_jam;
                            $total_jam_lembur += $jml_jam_lembur;

                            $bgcolor = $is_libur ? 'orange' : $bgcolor;
                        @endphp
                        <td style="background-color:{{ $bgcolor }}; color:{{ $textcolor }}; border: 1px solid #000000; vertical-align: middle;">
                            {!! $ket !!}
                        </td>
                        @php
                            $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                        @endphp
                    @endwhile
                    <td style="border: 1px solid #000000; vertical-align: middle;">
                        @if ($generalsetting->absen_istirahat == 1)
                            {!! implode('<br>', $rekap_istirahat) !!}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: right; border: 1px solid #000000; vertical-align: middle;">{{ formatAngka($total_denda) }}</td>
                    <td style="text-align: center; border: 1px solid #000000; vertical-align: middle;">{{ formatAngkaDesimal($total_potongan_jam) }}</td>
                    <td style="text-align:center; border: 1px solid #000000; vertical-align: middle;">{{ formatAngkaDesimal($total_jam_lembur) }}</td>
                    <td style="text-align:center; border: 1px solid #000000; vertical-align: middle;">{{ $jml_hadir }}</td>
                    <td style="text-align:center; border: 1px solid #000000; vertical-align: middle;">{{ $jml_izin }}</td>
                    <td style="text-align:center; border: 1px solid #000000; vertical-align: middle;">{{ $jml_sakit }}</td>
                    <td style="text-align:center; border: 1px solid #000000; vertical-align: middle;">{{ $jml_alfa }}</td>
                    <td style="text-align:center; border: 1px solid #000000; vertical-align: middle;">{{ $jml_libur }}</td>
                    <td style="text-align:center; border: 1px solid #000000; vertical-align: middle;">{{ $jml_terlambat }}</td>
                    <td style="text-align:center; border: 1px solid #000000; vertical-align: middle;">{{ $jml_tidakscanmasuk }}</td>
                    <td style="text-align:center; border: 1px solid #000000; vertical-align: middle;">{{ $jml_tidakscanpulang }}</td>
                    <td style="text-align:center; border: 1px solid #000000; vertical-align: middle;">{{ $jml_pulangcepat }}</td>
                </tr>
            @endforeach
            {{-- LEGEND --}}
             <tr>
                <td colspan="{{ 19 + $jmlhari }}"></td>
            </tr>
             <tr>
                <th colspan="2" style="border: 1px solid #000000; text-align:center; background-color: #f2f2f2;">Kode</th>
                <th colspan="4" style="border: 1px solid #000000; text-align:center; background-color: #f2f2f2;">Keterangan</th>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid #000000; text-align:center;">PC</td>
                <td colspan="4" style="border: 1px solid #000000;">Pulang Cepat</td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid #000000; text-align:center;">PJ</td>
                <td colspan="4" style="border: 1px solid #000000;">Potongan Jam</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
