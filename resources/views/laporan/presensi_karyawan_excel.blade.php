<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Presensi Karyawan</title>
</head>

<body>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td colspan="11">
                <h4 style="line-height: 20px; margin-bottom: 5px">
                    LAPORAN PRESENSI<br>
                    {{ $generalsetting->nama_perusahaan }}<br>
                    PERIODE {{ date('d-m-Y', strtotime($periode_dari)) }} -
                    {{ date('d-m-Y', strtotime($periode_sampai)) }}
                </h4>
                <span style="font-style: italic;">{{ $generalsetting->alamat }}</span><br>
                <span style="font-style: italic;">{{ $generalsetting->telepon }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="11"></td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">NIK</td>
            <td colspan="9">: {{ $karyawan->nik_show ?? $karyawan->nik }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Nama</td>
            <td colspan="9">: {{ $karyawan->nama_karyawan }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Jabatan</td>
            <td colspan="9">: {{ $karyawan->nama_jabatan }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Departemen</td>
            <td colspan="9">: {{ $karyawan->nama_dept }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Cabang</td>
            <td colspan="9">: {{ $karyawan->nama_cabang }}</td>
        </tr>
        <tr>
            <td colspan="11"></td>
        </tr>
        <thead>
            <tr>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">No</th>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">Tanggal</th>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">Hari</th>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">Jadwal Kerja</th>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">Jam Masuk</th>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">Jam Pulang</th>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">Status</th>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">Terlambat</th>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">Denda</th>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">Pot. Jam</th>
                <th style="border: 1px solid #333; padding: 10px; background-color: #024a75; color: white;">Lembur</th>
            </tr>
        </thead>
        <tbody>
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

                // Mapping data presensi agar mudah diakses
                $presensiByDate = [];
                foreach ($presensi as $row) {
                    $presensiByDate[$row->tanggal] = $row;
                }

                $mapJadwalByDate = $jadwal_bydate[$karyawan->nik] ?? [];
                $mapJadwalGrupByDate = $jadwal_grup_bydate[$karyawan->nik] ?? [];
                $mapJadwalByDay = $jadwal_byday[$karyawan->nik] ?? [];

                $tanggal_presensi = $periode_dari;
                $no = 1;
            @endphp

            @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                @php
                    $denda = 0;
                    $potongan_jam = 0;
                    $search = [
                        'nik' => $karyawan->nik,
                        'tanggal' => $tanggal_presensi,
                    ];

                    $ceklibur = ceklibur($datalibur, $search);
                    $ceklembur = ceklembur($datalembur, $search);
                    $lembur_jam = hitungLembur($ceklembur);
                    $jml_jam_lembur = !empty($ceklembur) ? $lembur_jam : 0;
                    $nama_hari = getHari($tanggal_presensi);

                    $d = $presensiByDate[$tanggal_presensi] ?? null;
                    $bgcolor = '';
                    $textcolor = '';
                    $bgcolor_hex = '';
                    $textcolor_hex = '';
                    $ket_status = '';
                    $ket_jam_kerja = '-';
                    $ket_jam_in = '-';
                    $ket_jam_out = '-';
                    $ket_terlambat = '';
                @endphp

                @if ($d)
                    @php
                        $ket_jam_kerja = $d->nama_jam_kerja . ' (' . date('H:i', strtotime($d->jam_masuk)) . ' - ' . date('H:i', strtotime($d->jam_pulang)) . ')';
                        if ($d->status == 'h') {
                            $jml_hadir++;
                            $ket_jam_in = !empty($d->jam_in) ? date('H:i', strtotime($d->jam_in)) : 'X';
                            $ket_jam_out = !empty($d->jam_out) ? date('H:i', strtotime($d->jam_out)) : 'X';
                            $jam_masuk_reg = $tanggal_presensi . ' ' . $d->jam_masuk;
                            $terlambat_info = hitungjamterlambat($d->jam_in, $jam_masuk_reg);

                            if ($terlambat_info != null) {
                                $ket_terlambat = str_replace('<br>', ' ', strip_tags($terlambat_info['show_laporan'], '<span><br>'));
                                // For Excel, we might just want the plain text
                                $ket_terlambat = strip_tags($terlambat_info['show_laporan']);
                                if ($terlambat_info['menitterlambat'] > 0) {
                                    $jml_terlambat++;
                                }
                            }

                            // Denda Logic
                            $denda_dari_db = isset($d->denda) && $d->denda !== null ? $d->denda : null;
                            if ($denda_dari_db !== null) {
                                $denda = $denda_dari_db;
                                if ($terlambat_info != null) {
                                    $potongan_jam_terlambat = $terlambat_info['desimal_terlambat'] < 1 ? 0 : ($terlambat_info['desimal_terlambat'] > $d->total_jam ? $d->total_jam : $terlambat_info['desimal_terlambat']);
                                } else {
                                    $potongan_jam_terlambat = 0;
                                }
                            } else {
                                if ($terlambat_info != null) {
                                    if ($terlambat_info['desimal_terlambat'] < 1) {
                                        $potongan_jam_terlambat = 0;
                                        $denda = hitungdenda($denda_list, $terlambat_info['menitterlambat']);
                                    } else {
                                        $potongan_jam_terlambat = $terlambat_info['desimal_terlambat'] > $d->total_jam ? $d->total_jam : $terlambat_info['desimal_terlambat'];
                                        $denda = 0;
                                    }
                                } else {
                                    $potongan_jam_terlambat = 0;
                                    $denda = 0;
                                }
                            }

                            $pulangcepat = hitungpulangcepat($tanggal_presensi, $d->jam_out, $d->jam_pulang, $d->istirahat, $d->jam_awal_istirahat, $d->jam_akhir_istirahat, $d->lintashari);
                            $pulangcepat = $pulangcepat > $d->total_jam ? $d->total_jam : $pulangcepat;
                            if ($pulangcepat != null) $jml_pulangcepat++;

                            $potongan_tidak_absen = (empty($d->jam_out) || empty($d->jam_in)) ? $d->total_jam : 0;
                            $potongan_jam = $potongan_tidak_absen == 0 ? ($pulangcepat + $potongan_jam_terlambat) : $potongan_tidak_absen;

                            $status_potongan_harian = $d->status_potongan ?? $generalsetting->status_potongan_jam;
                            if ($status_potongan_harian == 0) $potongan_jam = 0;

                            if (empty($d->jam_in)) $jml_tidakscanmasuk++;
                            if (empty($d->jam_out)) $jml_tidakscanpulang++;

                            $ket_status = 'HADIR';
                        } elseif ($d->status == 'i') {
                            $jml_izin++;
                            $bgcolor_hex = '#dea51f';
                            $textcolor_hex = '#ffffff';
                            $ket_status = 'IZIN';
                            $potongan_jam = $generalsetting->status_potongan_jam == 1 ? $d->total_jam : 0;
                            $denda = $d->denda ?? 0;
                        } elseif ($d->status == 's') {
                            $jml_sakit++;
                            $bgcolor_hex = '#c8075b';
                            $textcolor_hex = '#ffffff';
                            $ket_status = 'SAKIT';
                            $denda = $d->denda ?? 0;
                        } elseif ($d->status == 'c') {
                            $jml_cuti++;
                            $bgcolor_hex = '#0164b5';
                            $textcolor_hex = '#ffffff';
                            $ket_status = 'CUTI';
                            $denda = $d->denda ?? 0;
                        } elseif ($d->status == 'a') {
                            $jml_alfa++;
                            $bgcolor_hex = '#ff0000';
                            $textcolor_hex = '#ffffff';
                            $ket_status = 'ALPA';
                            $status_pot_alpa = $d->status_potongan ?? $generalsetting->status_potongan_jam;
                            $potongan_jam = $status_pot_alpa == 1 ? $d->total_jam : 0;
                            $denda = $d->denda ?? 0;
                        }
                    @endphp
                @else
                    @php
                        if (!empty($ceklibur)) {
                            $bgcolor_hex = '#008000';
                            $textcolor_hex = '#ffffff';
                            $jml_libur++;
                            $ket_status = 'LIBUR (' . $ceklibur[0]['keterangan'] . ')';
                        } else {
                            $totalJamJadwal = $mapJadwalByDate[$tanggal_presensi] ?? null;
                            if ($totalJamJadwal === null) $totalJamJadwal = $mapJadwalGrupByDate[$tanggal_presensi] ?? null;
                            if ($totalJamJadwal === null) $totalJamJadwal = $mapJadwalByDay[$nama_hari] ?? null;
                            if ($totalJamJadwal === null) {
                                $keyDeptCabang = $karyawan->kode_dept . '|' . $karyawan->kode_cabang;
                                $mapDept = $jadwal_bydept[$keyDeptCabang] ?? [];
                                $totalJamJadwal = $mapDept[$nama_hari] ?? null;
                            }

                            if ($totalJamJadwal !== null) {
                                $jml_alfa++;
                                $bgcolor_hex = '#ff0000';
                                $textcolor_hex = '#ffffff';
                                $ket_status = 'ALPA';
                                $potongan_jam = $generalsetting->status_potongan_jam == 1 ? (is_array($totalJamJadwal) ? $totalJamJadwal['total_jam'] : $totalJamJadwal) : 0;
                            } else {
                                $ket_status = '-';
                            }
                        }
                    @endphp
                @endif

                @php
                    $total_denda += $denda;
                    $total_potongan_jam += $potongan_jam;
                    $total_jam_lembur += $jml_jam_lembur;
                    if ($nama_hari == 'Minggu' && empty($bgcolor_hex)) {
                        $bgcolor_hex = '#ffa500';
                    }
                @endphp

                <tr>
                    <td style="border: 1px solid #333; text-align: center">{{ $no++ }}</td>
                    <td style="border: 1px solid #333; text-align: center">{{ date('d-m-Y', strtotime($tanggal_presensi)) }}</td>
                    <td style="border: 1px solid #333; text-align: center">{{ $nama_hari }}</td>
                    <td style="border: 1px solid #333">{{ $ket_jam_kerja }}</td>
                    <td style="border: 1px solid #333; text-align: center">{{ $ket_jam_in }}</td>
                    <td style="border: 1px solid #333; text-align: center">{{ $ket_jam_out }}</td>
                    <td style="border: 1px solid #333; text-align: center; background-color: {{ $bgcolor_hex }}; color: {{ $textcolor_hex }};">
                        {{ $ket_status }}
                    </td>
                    <td style="border: 1px solid #333; text-align: center">{{ $ket_terlambat }}</td>
                    <td style="border: 1px solid #333; text-align: right">{{ $denda != 0 ? $denda : '' }}</td>
                    <td style="border: 1px solid #333; text-align: center">{{ $potongan_jam != 0 ? $potongan_jam : '' }}</td>
                    <td style="border: 1px solid #333; text-align: center">{{ $jml_jam_lembur != 0 ? $jml_jam_lembur : '' }}</td>
                </tr>

                @php
                    $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                @endphp
            @endwhile
        </tbody>
    </table>

    @php
        if ($generalsetting->status_potongan_jam == 0) {
            $total_potongan_jam = 0;
        } elseif ($total_potongan_jam > $generalsetting->total_jam_bulan) {
            $total_potongan_jam = $generalsetting->total_jam_bulan;
        }
    @endphp

    <table style="margin-top: 40px; border-collapse: collapse;">
        <tr>
            <th colspan="2" style="border: 1px solid #333; background-color: #f2f2f2; padding: 5px;">Rekapitulasi Presensi</th>
        </tr>
        <tr>
            <td style="border: 1px solid #333; padding: 5px;">Hadir</td>
            <td style="border: 1px solid #333; text-align: center; padding: 5px;">{{ $jml_hadir }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #333; padding: 5px;">Izin</td>
            <td style="border: 1px solid #333; text-align: center; padding: 5px;">{{ $jml_izin }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #333; padding: 5px;">Sakit</td>
            <td style="border: 1px solid #333; text-align: center; padding: 5px;">{{ $jml_sakit }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #333; padding: 5px;">Cuti</td>
            <td style="border: 1px solid #333; text-align: center; padding: 5px;">{{ $jml_cuti }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #333; padding: 5px;">Alfa</td>
            <td style="border: 1px solid #333; text-align: center; padding: 5px;">{{ $jml_alfa }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #333; padding: 5px;">Libur</td>
            <td style="border: 1px solid #333; text-align: center; padding: 5px;">{{ $jml_libur }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #333; padding: 5px;">Terlambat</td>
            <td style="border: 1px solid #333; text-align: center; padding: 5px;">{{ $jml_terlambat }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #333; padding: 5px;">Denda</td>
            <td style="border: 1px solid #333; text-align: right; padding: 5px;">{{ $total_denda }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #333; padding: 5px;">Pot. Jam</td>
            <td style="border: 1px solid #333; text-align: right; padding: 5px;">{{ $total_potongan_jam }} Jam</td>
        </tr>
        <tr>
            <td style="border: 1px solid #333; padding: 5px;">Lembur</td>
            <td style="border: 1px solid #333; text-align: right; padding: 5px;">{{ $total_jam_lembur }} Jam</td>
        </tr>
    </table>
</body>

</html>
