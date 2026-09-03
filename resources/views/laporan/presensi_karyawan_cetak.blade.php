<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Presensi Karyawan {{ date('Y-m-d H:i:s') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/report.css') }}">
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10mm;
        }

        .sheet {
            overflow: auto !important;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header h4 {
            line-height: 1.2;
            margin: 0 0 5px 0;
        }

        .tablereport {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
        }

        .tablereport td {
            border: 1px solid #333;
            padding: 8px;
            font-size: 11px;
            vertical-align: middle;
        }

        .tablereport th {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
            background-color: #024a75;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 11px;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            cursor: pointer;
            border: 1px solid transparent;
            border-radius: 4px;
            text-decoration: none;
        }

        .btn-warning {
            color: #fff;
            background-color: #f0ad4e;
            border-color: #eea236;
        }

        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }

        @media print {
            .btn {
                display: none;
            }
        }
    </style>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>
</head>

<body>
        <div class="header" style="margin-bottom: 10px">
            <table style="width: 100%">
                <tr>
                    <td style="width: 70px; padding-right: 10px;">
                        @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                            <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo Perusahaan" style="max-width: 100px;">
                        @else
                            <img src="https://placehold.co/100x100?text=Logo" alt="Logo Default" style="max-width: 100px;">
                        @endif
                    </td>
                    <td>
                        <h4 style="line-height: 20px; margin-bottom: 5px">
                            LAPORAN PRESENSI
                            <br>
                            {{ $generalsetting->nama_perusahaan }}
                            <br>
                            PERIODE {{ date('d-m-Y', strtotime($periode_dari)) }} -
                            {{ date('d-m-Y', strtotime($periode_sampai)) }}
                        </h4>
                        <span style="font-style: italic;">{{ $generalsetting->alamat }}</span><br>
                        <span style="font-style: italic;">{{ $generalsetting->telepon }}</span>
                    </td>
                    <td style="text-align: right; vertical-align: top;">
                    </td>
                </tr>
            </table>
        </div>
        <div class="datakaryawan" style="display: flex; gap: 20px; margin-top: 40px">
            <div id="fotokaryawan">
                @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                    <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="user image" class="rounded" height="150" width="140"
                        style="object-fit: cover">
                @else
                    <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="user image" class="rounded" width="150">
                @endif
            </div>
            <div id="detailkaryawan">
                <table class="tablereport">
                    <tr>
                        <td style="border: none">NIK</td>
                        <td style="border: none">:</td>
                        <td style="border: none">{{ $karyawan->nik_show ?? $karyawan->nik }}</td>
                    </tr>
                    <tr>
                        <td style="border: none">Nama</td>
                        <td style="border: none">:</td>
                        <td style="border: none">{{ $karyawan->nama_karyawan }}</td>
                    </tr>
                    <tr>
                        <td style="border: none">Jabatan</td>
                        <td style="border: none">:</td>
                        <td style="border: none">{{ $karyawan->nama_jabatan }}</td>
                    </tr>
                    <tr>
                        <td style="border: none">Departemen</td>
                        <td style="border: none">:</td>
                        <td style="border: none">{{ $karyawan->nama_dept }}</td>
                    </tr>
                    <tr>
                        <td style="border: none">Cabang</td>
                        <td style="border: none">:</td>
                        <td style="border: none">{{ $karyawan->nama_cabang }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="presensi" style="margin-top: 40px">
            <table class="tablereport">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Jadwal Kerja</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Jam Istirahat</th>
                        <th>Status</th>
                        <th>Terlambat</th>
                        <th>Denda</th>
                        <th>Pot. Jam</th>
                        <th>Lembur</th>
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
                            $ket_status = '';
                            $ket_jam_kerja = '-';
                            $ket_jam_in = '-';
                            $ket_jam_out = '-';
                            $ket_istirahat = '-';
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
                                        $ket_terlambat = $terlambat_info['show_laporan'];
                                        if ($terlambat_info['menitterlambat'] > 0) {
                                            $jml_terlambat++;
                                        }
                                    }

                                    // Denda Logic (matching presensi_cetak)
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

                                    $potongan_istirahat = hitungPotonganIstirahat($d->istirahat_in, $d->istirahat_out, $d->jam_awal_istirahat, $d->jam_akhir_istirahat);
                                    $potongan_tidak_absen = (empty($d->jam_out) || empty($d->jam_in)) ? $d->total_jam : 0;
                                    $status_potongan_istirahat = $d->status_potongan_istirahat ?? $generalsetting->potongan_istirahat;
                                    $potongan_jam = $potongan_tidak_absen == 0 ? ($pulangcepat + $potongan_jam_terlambat + ($status_potongan_istirahat == 1 ? $potongan_istirahat : 0)) : $potongan_tidak_absen;

                                    $status_potongan_harian = $d->status_potongan ?? $generalsetting->status_potongan_jam;
                                    if ($status_potongan_harian == 0) $potongan_jam = 0;

                                    if (empty($d->jam_in)) $jml_tidakscanmasuk++;
                                    if (empty($d->jam_out)) $jml_tidakscanpulang++;

                                    if ($generalsetting->absen_istirahat == 1) {
                                        $istirahat_in_val = (!empty($d->istirahat_in)) ? date('H:i', strtotime($d->istirahat_in)) : null;
                                        $istirahat_out_val = (!empty($d->istirahat_out)) ? date('H:i', strtotime($d->istirahat_out)) : null;

                                        if ($istirahat_in_val && $istirahat_out_val) {
                                            $ket_istirahat = '<span style="color:#3498db">' . $istirahat_in_val . ' - ' . $istirahat_out_val . '</span>';
                                        } else {
                                            $ket_istirahat = '<span style="color:red">Belum Absen</span>';
                                        }
                                    } else {
                                        $ket_istirahat = '-';
                                    }

                                    $ket_status = 'HADIR';
                                } elseif ($d->status == 'i') {
                                    $jml_izin++;
                                    $bgcolor = '#dea51f';
                                    $textcolor = 'white';
                                    $ket_status = 'IZIN';
                                    $potongan_jam = $generalsetting->status_potongan_jam == 1 ? $d->total_jam : 0;
                                    $denda = $d->denda ?? 0;
                                } elseif ($d->status == 's') {
                                    $jml_sakit++;
                                    $bgcolor = '#c8075b';
                                    $textcolor = 'white';
                                    $ket_status = 'SAKIT';
                                    $denda = $d->denda ?? 0;
                                } elseif ($d->status == 'c') {
                                    $jml_cuti++;
                                    $bgcolor = '#0164b5';
                                    $textcolor = 'white';
                                    $ket_status = 'CUTI';
                                    $denda = $d->denda ?? 0;
                                } elseif ($d->status == 'a') {
                                    $jml_alfa++;
                                    $bgcolor = 'red';
                                    $textcolor = 'white';
                                    $ket_status = 'ALPA';
                                    $status_pot_alpa = $d->status_potongan ?? $generalsetting->status_potongan_jam;
                                    $potongan_jam = $status_pot_alpa == 1 ? $d->total_jam : 0;
                                    $denda = $d->denda ?? 0;
                                }
                            @endphp
                        @else
                            @php
                                $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                                if (!empty($ceklibur)) {
                                    $bgcolor = 'green';
                                    $textcolor = 'white';
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

                                    if ($totalJamJadwal !== null && !$is_future) {
                                        $jml_alfa++;
                                        $bgcolor = 'red';
                                        $textcolor = 'white';
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
                            if ($nama_hari == 'Minggu') $bgcolor = 'orange';
                        @endphp

                        <tr>
                            <td style="text-align: center">{{ $no++ }}</td>
                            <td style="text-align: center">{{ date('d-m-Y', strtotime($tanggal_presensi)) }}</td>
                            <td style="text-align: center">{{ $nama_hari }}</td>
                            <td>{{ $ket_jam_kerja }}</td>
                            <td style="text-align: center">{{ $ket_jam_in }}</td>
                            <td style="text-align: center">{{ $ket_jam_out }}</td>
                             <td style="text-align: center">{!! $ket_istirahat !!}</td>
                            <td style="text-align: center; background-color: {{ $bgcolor }}; color: {{ $textcolor }}; position: relative;">
                                @if(isset($d) && (isset($d->status_potongan) || isset($d->denda)))
                                    <span style="position:absolute; top:2px; right:2px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </span>
                                @endif
                                {{ $ket_status }}
                            </td>
                            <td style="text-align: center">{!! $ket_terlambat !!}</td>
                            <td style="text-align: right">{{ $denda != 0 ? formatAngka($denda) : '' }}</td>
                            <td style="text-align: center">{{ $potongan_jam != 0 ? formatAngkaDesimal($potongan_jam) : '' }}</td>
                            <td style="text-align: center">{{ $jml_jam_lembur != 0 ? formatAngkaDesimal($jml_jam_lembur) : '' }}</td>
                        </tr>

                        @php
                            $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                        @endphp
                    @endwhile
                </tbody>
            </table>
        </div>

        @php
            if ($generalsetting->status_potongan_jam == 0) {
                $total_potongan_jam = 0;
            } elseif ($total_potongan_jam > $generalsetting->total_jam_bulan) {
                $total_potongan_jam = $generalsetting->total_jam_bulan;
            }
        @endphp

        <div class="rekap" style="margin-top: 40px; display: flex; gap: 40px">
            <table class="tablereport" style="width: 300px">
                <tr>
                    <th colspan="2">Rekapitulasi Presensi</th>
                </tr>
                <tr>
                    <td>Hadir</td>
                    <td style="text-align: center">{{ $jml_hadir }}</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td style="text-align: center">{{ $jml_izin }}</td>
                </tr>
                <tr>
                    <td>Sakit</td>
                    <td style="text-align: center">{{ $jml_sakit }}</td>
                </tr>
                <tr>
                    <td>Cuti</td>
                    <td style="text-align: center">{{ $jml_cuti }}</td>
                </tr>
                <tr>
                    <td>Alfa</td>
                    <td style="text-align: center">{{ $jml_alfa }}</td>
                </tr>
                <tr>
                    <td>Libur</td>
                    <td style="text-align: center">{{ $jml_libur }}</td>
                </tr>
                <tr>
                    <td>Terlambat</td>
                    <td style="text-align: center">{{ $jml_terlambat }}</td>
                </tr>
                <tr>
                    <td>Denda</td>
                    <td style="text-align: right">{{ formatAngka($total_denda) }}</td>
                </tr>
                <tr>
                    <td>Pot. Jam</td>
                    <td style="text-align: right">{{ formatAngkaDesimal($total_potongan_jam) }} Jam</td>
                </tr>
                <tr>
                    <td>Lembur</td>
                    <td style="text-align: right">{{ formatAngkaDesimal($total_jam_lembur) }} Jam</td>
                </tr>
            </table>

            <table class="tablereport" style="width: 300px; height: fit-content">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 5px">Kode</th>
                        <th style="padding: 5px">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align:center;">PJ</td>
                        <td>Potongan Jam</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </td>
                        <td>Data Terkunci</td>
                    </tr>
                </tbody>
            </table>
        </div>

    <script>
        $(document).ready(function() {
            // Locking scripts removed as requested
        });
    </script>
</body>

</html>
