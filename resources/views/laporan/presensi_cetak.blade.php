<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Presensi {{ date('Y-m-d H:i:s') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/report.css') }}">
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
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
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-image: none;
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
            color: #333;
            background-color: #fff;
            border-color: #ccc;
        }

        /* Responsive Table Container */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            max-height: 75vh;
            overflow-y: auto;
            position: relative;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .datatable3 {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            min-width: 100%;
        }

        .datatable3 th,
        .datatable3 td {
            border: 1px solid #333;
            padding: 10px; /* Increased padding */
            vertical-align: middle; /* Align middle as requested */
            white-space: nowrap;
        }

        .datatable3 td p {
            margin-top: 5px !important;
            margin-bottom: 5px !important;
            line-height: 1 !important;
        }

        .datatable3 td h4 {
            margin-top: 0 !important;
            margin-bottom: 5px !important;
        }

        .datatable3 th {
            background-color: #024a75;
            color: white;
            text-transform: uppercase;
            text-align: center;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 20; /* Higher than body columns */
            height: 40px; /* Fixed height for sticky calculation */
            box-sizing: border-box;
            box-shadow: inset 0 0 0 1px #333; /* Simulate border to fix transparency gaps */
            border: none; /* Remove default border to rely on box-shadow */
        }

        .datatable3 thead tr:nth-child(2) th {
            top: 40px;
        }

        .datatable3 thead tr:nth-child(3) th {
            top: 80px;
        }
        
        
        /* Sticky Column Styles (Screen Only) */
        @media screen {
            .sticky-col {
                position: sticky;
                background-color: #fff;
                z-index: 10; /* Body sticky columns */
                /* Fix for missing borders in sticky columns */
                box-shadow: inset 0 0 0 1px #333; /* Simulate border */
                border: none !important; /* Remove actual border to prevent double lines */
            }
            
            .datatable3 th.sticky-col {
                z-index: 30; /* Intersection (Header + Sticky Col) must be highest */
                background-color: #024a75;
                box-shadow: inset 0 0 0 1px #333; /* Simulate border */
            }

            .first-col { left: 0; width: 30px; }
            .second-col { left: 30px; width: 70px; }
            .third-col { left: 100px; width: 150px; }
        }

        /* Print Specific Styles */
        @media print {
            .table-responsive {
                overflow: visible;
                border: none;
            }
            .datatable3 {
                width: 100%;
                table-layout: auto;
            }
            .btn {
                display: none;
            }
            .sticky-col {
                position: static !important;
            }
        }
    </style>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/external/js/sweetalert2@11.js') }}"></script>
</head>

<body>

    <div class="header" style="margin-bottom: 10px">
        <table>
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
                    <form id="formKunciLaporan" method="POST" action="{{ route('laporan.kuncilaporan') }}"
                        style="display: inline-block; margin-right: 5px;">
                        @csrf
                        <input type="hidden" name="periode_laporan" value="{{ $request_params['periode_laporan'] }}">
                        <input type="hidden" name="bulan" value="{{ $request_params['bulan'] }}">
                        <input type="hidden" name="tahun" value="{{ $request_params['tahun'] }}">
                        @if (!empty($request_params['kode_cabang']))
                            <input type="hidden" name="kode_cabang" value="{{ $request_params['kode_cabang'] }}">
                        @endif
                        @if (!empty($request_params['kode_dept']))
                            <input type="hidden" name="kode_dept" value="{{ $request_params['kode_dept'] }}">
                        @endif
                        @if (!empty($request_params['nik']))
                            <input type="hidden" name="nik" value="{{ $request_params['nik'] }}">
                        @endif
                        @if (!empty($request_params['jenis_upah']))
                            <input type="hidden" name="jenis_upah" value="{{ $request_params['jenis_upah'] }}">
                        @endif
                        <button type="submit" class="btn btn-warning" id="btnKunciLaporan" style="padding: 8px 16px; font-size: 14px;">
                            <i class="ti ti-lock"></i> Kunci Laporan
                        </button>
                    </form>
                    <form id="formBatalkanKunciLaporan" method="POST" action="{{ route('laporan.batalkankuncilaporan') }}"
                        style="display: inline-block;">
                        @csrf
                        <input type="hidden" name="periode_laporan" value="{{ $request_params['periode_laporan'] }}">
                        <input type="hidden" name="bulan" value="{{ $request_params['bulan'] }}">
                        <input type="hidden" name="tahun" value="{{ $request_params['tahun'] }}">
                        @if (!empty($request_params['kode_cabang']))
                            <input type="hidden" name="kode_cabang" value="{{ $request_params['kode_cabang'] }}">
                        @endif
                        @if (!empty($request_params['kode_dept']))
                            <input type="hidden" name="kode_dept" value="{{ $request_params['kode_dept'] }}">
                        @endif
                        @if (!empty($request_params['nik']))
                            <input type="hidden" name="nik" value="{{ $request_params['nik'] }}">
                        @endif
                        @if (!empty($request_params['jenis_upah']))
                            <input type="hidden" name="jenis_upah" value="{{ $request_params['jenis_upah'] }}">
                        @endif
                        <button type="submit" class="btn btn-secondary" id="btnBatalkanKunciLaporan"
                            style="padding: 8px 16px; font-size: 14px; background-color: #6c757d; border-color: #6c757d; color: #fff;">
                            <i class="ti ti-lock-off"></i> Batalkan Kunci
                        </button>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <div class="table-responsive">
        <table class="datatable3">
            <thead>
                <tr>
                    <th rowspan="3" class="sticky-col first-col">No</th>
                    <th rowspan="3" class="sticky-col second-col">Nik</th>
                    <th rowspan="3" class="sticky-col third-col">Nama Karyawan</th>
                    <th rowspan="3">Jabatan</th>
                    <th rowspan="3">Dept</th>
                    <th rowspan="3">Cabang</th>
                    <th colspan="{{ $jmlhari }}">Tanggal</th>
                    <th rowspan="3" style="min-width: 60px">Denda</th>
                    <th rowspan="3" style="min-width: 60px">Pot. Jam</th>
                    <th rowspan="3" style="min-width: 60px">Lembur</th>
                    <th colspan="9">Rekap</th>
                </tr>
                <tr>
                    @php
                        $tanggal_presensi = $periode_dari;
                    @endphp
                    @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                        <th style="width: 100px">{{ getHari(date('Y-m-d', strtotime($tanggal_presensi))) }}</th>
                        @php
                            $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                        @endphp
                    @endwhile
                    <th rowspan="2">Hadir</th>
                    <th rowspan="2">Izin</th>
                    <th rowspan="2">Sakit</th>
                    <th rowspan="2">Alfa</th>
                    <th rowspan="2">Libur</th>
                    <th rowspan="2">Terlambat</th>
                    <th rowspan="2">Tidak Scan Masuk</th>
                    <th rowspan="2">Tidak Scan Pulang</th>
                    <th rowspan="2">Pulang Cepat</th>
                </tr>
                <tr>
                    @php
                        $tanggal_presensi = $periode_dari;
                    @endphp
                    @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                        <th>{{ date('d', strtotime($tanggal_presensi)) }}</th>
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
                        <td class="sticky-col first-col">{{ $loop->iteration }}</td>
                        <td class="sticky-col second-col">'{{ $d['nik_show'] ?? $d['nik'] }}</td>
                        <td class="sticky-col third-col">{{ $d['nama_karyawan'] }}</td>
                        <td>{{ $d['nama_jabatan'] }}</td>
                        <td style="text-align: center">{{ $d['kode_dept'] }}</td>
                        <td style="text-align: center">{{ $d['kode_cabang'] }}</td>
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
                                $lembur_aktual = hitungLembur($ceklembur);
                                $tipe_hari = $is_libur ? 2 : 1;
                                $jam_netto_harian = $lembur_aktual > 0 ? hitungJamNetto($lembur_aktual, $tipe_hari) : 0;
                                $jml_jam_lembur = $jam_netto_harian;
                                $nama_hari = getHari($tanggal_presensi);
                            @endphp
                            @if (isset($d[$tanggal_presensi]))
                                @if ($d[$tanggal_presensi]['status'] == 'h')
                                    @php
                                        $bgcolor = '';
                                        $textcolor = '';
                                        $jml_hadir++;

                                        $ket_nama_jam_kerja =
                                            '<h4 style="font-weight:bold; margin-bottom:10px">' . $d[$tanggal_presensi]['nama_jam_kerja'] . '</h4>';
                                        $ket_jadwal_kerja =
                                            '<p><span style="color:blue">' .
                                            date('H:i', strtotime($d[$tanggal_presensi]['jam_masuk'])) .
                                            ' - ' .
                                            date('H:i', strtotime($d[$tanggal_presensi]['jam_pulang'])) .
                                            '</span></p>';
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
                                            '<p> <span
                                                style="color:' .
                                            $color_jam_in .
                                            '">' .
                                            $jam_in .
                                            '</span> -
                                            <span
                                                style="color:' .
                                            $color_jam_out .
                                            '">' .
                                            $jam_out .
                                            '</span></p>';

                                        $istirahat_in = !empty($d[$tanggal_presensi]['istirahat_in'])
                                            ? date('H:i', strtotime($d[$tanggal_presensi]['istirahat_in']))
                                            : '&#10008;';
                                        $istirahat_out = !empty($d[$tanggal_presensi]['istirahat_out'])
                                            ? date('H:i', strtotime($d[$tanggal_presensi]['istirahat_out']))
                                            : '&#10008;';

                                        $color_istirahat_in = !empty($d[$tanggal_presensi]['istirahat_in']) ? '#3498db' : 'red';
                                        $color_istirahat_out = !empty($d[$tanggal_presensi]['istirahat_out']) ? '#3498db' : 'red';

                                        $ket_istirahat = "";
                                        if ($generalsetting->absen_istirahat == 1) {
                                            $ket_istirahat =
                                                '<p> <span
                                                style="color:' .
                                                $color_istirahat_in .
                                                '">' .
                                                $istirahat_in .
                                                '</span> -
                                            <span
                                                style="color:' .
                                                $color_istirahat_out .
                                                '">' .
                                                $istirahat_out .
                                                '</span></p>';
                                        }


                                        $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);

                                        $color_terlambat = $terlambat != null ? $terlambat['color'] : '';
                                        $ket_terlambat =
                                            $terlambat != null
                                                ? '<p><span
                                                style="color:' .
                                                    $color_terlambat .
                                                    '">' .
                                                    $terlambat['show_laporan'] .
                                                    '</span></p>'
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

                                        $ket_denda = $denda != 0 ? '<p><span style="color:red">Denda : ' . formatAngka($denda) . '</span></p>' : '';

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
                                            $pulangcepat != null ? '<p><span style="color:red">PC : ' . $pulangcepat . ' Jam </span></p>' : '';
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
                                        
                                        $status_potongan_harian = isset($d[$tanggal_presensi]['status_potongan']) ? $d[$tanggal_presensi]['status_potongan'] : $generalsetting->status_potongan_jam;
                                        if ($status_potongan_harian == 0) {
                                            $potongan_jam = 0;
                                        }
                                        $ket_potongan_jam = !empty($potongan_jam)
                                            ? '<p><span style="color:red">PJ: ' . formatAngkaDesimal($potongan_jam) . ' Jam</span></p>'
                                            : '';

                                        $ket_jam_lembur =
                                            $jml_jam_lembur > 0
                                                ? '<p><a href="' . route('laporan.lemburdetail', [$d['nik'], $periode_dari, $periode_sampai]) . '" target="_blank" style="color:rgb(11, 153, 179); text-decoration: underline;"> Lembur :' . $jml_jam_lembur . ' Jam</a></p>'
                                                : '';
                                        $ket =
                                            $ket_nama_jam_kerja .
                                            $ket_jadwal_kerja .
                                            $ket_presensi .
                                            $ket_istirahat .
                                            $ket_terlambat .
                                            $ket_denda .
                                            $ket_pulang_cepat .
                                            $ket_potongan_jam .
                                            $ket_jam_lembur;
                                        // $ket =
                                        //     $ket_nama_jam_kerja .
                                        //     $ket_jadwal_kerja .
                                        //     '<br>' .
                                        //     $ket_presensi .
                                        //     '<br>' .
                                        //     $ket_terlambat .
                                        //     '<br>' .
                                        //     $ket_denda .
                                        //     $ket_pulang_cepat .
                                        //     '<br>' .
                                        //     $ket_potongan_jam;

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

                                        $pjl_izin = $generalsetting->status_potongan_jam == 1 ? '<p>PJ : ' . formatAngkaDesimal($potongan_jam) . ' Jam</p>' : '';
                                        $ket =
                                            '<h4 style="font-weight: bold; margin-bottom:10px">IZIN</h4><p>' .
                                            $d[$tanggal_presensi]['keterangan_izin_absen'] .
                                            '</p>' .
                                            $pjl_izin;
                                        
                                        if ($generalsetting->status_potongan_jam == 0) {
                                            $potongan_jam = 0;
                                        }
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
                                            '<h4 style="font-weight: bold; margin-bottom:10px">SAKIT</h4><span>' .
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
                                            '<h4 style="font-weight: bold; margin-bottom:10px">CUTI</h4><span>' .
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
                                            $denda != 0 ? '<p><span style="color:red">Denda : ' . formatAngka($denda) . '</span></p>' : '';
                                        
                                        $status_potongan_harian_alpa = isset($d[$tanggal_presensi]['status_potongan']) ? $d[$tanggal_presensi]['status_potongan'] : $generalsetting->status_potongan_jam;
                                        
                                        $pjl_alpa = $status_potongan_harian_alpa == 1 ? '<span>PJ : ' . formatAngkaDesimal($potongan_jam) . ' Jam</span>' : '';

                                        $ket =
                                            '<h4 style="font-weight: bold; margin-bottom:10px">Alpa</h4>' .
                                            $pjl_alpa .
                                            $ket_denda_alpa;
                                        
                                        if ($status_potongan_harian_alpa == 0) {
                                            $potongan_jam = 0;
                                        }
                                    @endphp
                                @endif
                            @else
                                @php
                                    $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                                    $bgcolor = $is_future ? '' : 'red';
                                    $textcolor = $is_future ? '' : 'white';
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
                                            // Handle if totalJamJadwal is an array (new format)
                                            $tJam = is_array($totalJamJadwal) ? $totalJamJadwal['total_jam'] : $totalJamJadwal;
                                            
                                            // Ada jadwal tapi tidak ada presensi sama sekali → Alpa & potong full jam kerja
                                            if (!$is_future) {
                                                $jml_alfa++;
                                                $potongan_jam = $tJam;
    
                                                // Untuk alpa yang belum ada di database, denda = 0
                                                $denda = 0;
    
                                                $pjl_alpa_else = $generalsetting->status_potongan_jam == 1 ? '<span>PJ : ' . formatAngkaDesimal($potongan_jam) . ' Jam</span>' : '';
                                                $ket =
                                                    '<h4 style="font-weight: bold; margin-bottom:10px">Alpa</h4>' .
                                                    $pjl_alpa_else;
                                                
                                                if ($generalsetting->status_potongan_jam == 0) {
                                                    $potongan_jam = 0;
                                                }
                                            }
                                        }
                                    }

                                    // Jika ada lembur terpisah, tetap tampilkan info lembur
                                    if (!empty($ceklembur)) {
                                        $bgcolor = 'white';
                                        $textcolor = 'black';
                                        $ket_jam_lembur = '<p><span style="color:rgb(11, 153, 179)"> Lembur :' . $jml_jam_lembur . ' Jam</span></p>';
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
                            <td style="background-color:{{ $bgcolor }}; color:{{ $textcolor }}; position: relative;">
                                @if(isset($d[$tanggal_presensi]['status_potongan']))
                                    <span style="position:absolute; top:2px; right:2px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                    </span>
                                @endif
                                {!! $ket !!}
                            </td>
                            @php
                                $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                            @endphp
                        @endwhile
                        {{-- This block ensures that if status_potongan_jam is OFF, daily PJ is not displayed --}}
                        @php
                            $status_potongan_final = isset($d[$tanggal_presensi]['status_potongan']) ? $d[$tanggal_presensi]['status_potongan'] : $generalsetting->status_potongan_jam;
                        @endphp

                        @if ($status_potongan_final == 1)
                            @if (!empty($ket_potongan_jam) || !empty($ket_denda_alpa))
                                @php
                                    $ket .= !empty($ket_potongan_jam) ? $ket_potongan_jam : '';
                                    $ket .= !empty($ket_denda_alpa) ? $ket_denda_alpa : '';
                                @endphp
                            @endif
                        @endif
                        @php
                            if ($generalsetting->status_potongan_jam == 0) {
                                $total_potongan_jam = 0;
                            } elseif ($total_potongan_jam > $generalsetting->total_jam_bulan) {
                                $total_potongan_jam = $generalsetting->total_jam_bulan;
                            }
                        @endphp
                        <td style="text-align: right">{{ formatAngka($total_denda) }}</td>
                        <td style="text-align: center">{{ formatAngkaDesimal($total_potongan_jam) }}</td>
                        <td style="text-align:center">
                            <a href="{{ route('laporan.lemburdetail', [$d['nik'], $periode_dari, $periode_sampai]) }}" target="_blank" style="color: #024a75; text-decoration: underline; font-weight: bold;">
                                {{ formatAngkaDesimal($total_jam_lembur) }}
                            </a>
                        </td>
                        <td style="text-align:center">{{ $jml_hadir }}</td>
                        <td style="text-align:center">{{ $jml_izin }}</td>
                        <td style="text-align:center">{{ $jml_sakit }}</td>
                        <td style="text-align:center">{{ $jml_alfa }}</td>
                        <td style="text-align:center">{{ $jml_libur }}</td>
                        <td style="text-align:center">{{ $jml_terlambat }}</td>
                        <td style="text-align:center">{{ $jml_tidakscanmasuk }}</td>
                        <td style="text-align:center">{{ $jml_tidakscanpulang }}</td>
                        <td style="text-align:center">{{ $jml_pulangcepat }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="margin-top: 30px; width: 350px;">
        <table border="1" cellpadding="4" cellspacing="0" style="border-collapse: collapse; width:100%;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="text-align:center;">Kode</th>
                    <th style="text-align:center;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">PC</td>
                    <td>Pulang Cepat</td>
                </tr>
                <tr>
                    <td style="text-align:center;">PJ</td>
                    <td>Potongan Jam</td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            $('#formKunciLaporan').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin mengunci laporan ini? Denda akan disimpan ke database dan tidak akan berubah meskipun ada perubahan aturan denda.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Kunci Laporan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Sedang mengunci laporan',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        var form = $('#formKunciLaporan');
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let message = 'Terjadi kesalahan saat mengunci laporan';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });

            // Handler untuk batalkan kunci laporan
            $('#formBatalkanKunciLaporan').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin membatalkan kunci laporan ini? Denda yang sudah dikunci akan dihapus dan akan dihitung ulang berdasarkan aturan denda saat ini.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Batalkan Kunci',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Sedang membatalkan kunci laporan',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        var form = $('#formBatalkanKunciLaporan');
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Reload halaman untuk refresh data
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let message = 'Terjadi kesalahan saat membatalkan kunci laporan';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
