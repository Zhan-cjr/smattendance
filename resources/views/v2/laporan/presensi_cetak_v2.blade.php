<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Presensi (Format 2) - {{ date('d/m/Y H:i') }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    
    <style>
        :root {
            --primary: {{ $generalsetting->theme_color_1 ?? '#0a5c41' }};
            --primary-dark: {{ $generalsetting->theme_color_2 ?? '#064430' }};
            --primary-light: #ecfdf5;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        @page {
            size: A3 landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 10px;
            color: var(--slate-800);
            background-color: #f8fafc;
            margin: 0;
            padding: 16px 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Action Bar (Screen Only) */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            padding: 12px 18px;
            border-radius: 14px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 16px;
        }

        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-bar-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid rgba(10, 92, 65, 0.2);
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            font-size: 11.5px;
            font-weight: 700;
            border-radius: 9px;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }

        .btn-excel {
            background: #16a34a;
            color: #ffffff;
            border-color: #15803d;
        }
        .btn-excel:hover {
            background: #15803d;
            color: #ffffff;
        }

        .btn-print {
            background: var(--primary);
            color: #ffffff;
        }
        .btn-print:hover {
            opacity: 0.92;
        }

        /* Company & Report Header */
        .report-header {
            background: #ffffff;
            padding: 16px 20px;
            border-radius: 14px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header-logo {
            max-width: 80px;
            max-height: 55px;
            object-fit: contain;
        }

        .header-title h2 {
            font-size: 16px;
            font-weight: 800;
            color: var(--slate-900);
            margin: 0 0 2px 0;
            text-transform: uppercase;
        }

        .header-title h3 {
            font-size: 13px;
            font-weight: 700;
            color: var(--primary);
            margin: 0 0 4px 0;
        }

        .header-title p {
            font-size: 11px;
            color: var(--slate-500);
            margin: 0;
        }

        .header-period-card {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            padding: 8px 14px;
            text-align: right;
        }

        .header-period-card .period-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--slate-400);
            text-transform: uppercase;
        }

        .header-period-card .period-value {
            font-size: 12px;
            font-weight: 800;
            color: var(--slate-900);
            margin-top: 2px;
        }

        /* Table Container & Sticky Matrix */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            max-height: 72vh;
            overflow-y: auto;
            position: relative;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 20px;
        }

        .datatable-matrix {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 9.5px;
            min-width: 100%;
        }

        .datatable-matrix th,
        .datatable-matrix td {
            border-right: 1px solid var(--slate-200);
            border-bottom: 1px solid var(--slate-200);
            padding: 5px 4px;
            vertical-align: middle;
            white-space: nowrap;
            box-sizing: border-box;
            text-align: center;
        }

        .datatable-matrix thead th {
            background-color: var(--primary);
            color: #ffffff;
            text-transform: uppercase;
            text-align: center;
            font-weight: 700;
            font-size: 9.5px;
            position: sticky;
            z-index: 20;
            line-height: 1.2;
            height: 30px;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        .datatable-matrix thead tr:nth-child(1) th { top: 0; z-index: 25; background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%); }
        .datatable-matrix thead tr:nth-child(2) th { top: 30px; z-index: 24; background: var(--primary-dark); }
        .datatable-matrix thead tr:nth-child(3) th { top: 60px; z-index: 23; background: var(--primary); }
        .datatable-matrix thead tr:nth-child(4) th { top: 90px; z-index: 22; background: var(--primary-dark); font-size: 8.5px; }

        /* Sticky Columns */
        @media screen {
            .sticky-col {
                position: sticky;
                background-color: #ffffff;
                z-index: 10;
            }

            .datatable-matrix tbody tr:hover td.sticky-col {
                background-color: #f1f5f9;
            }

            .datatable-matrix tbody tr:hover td {
                background-color: #f8fafc;
            }

            .datatable-matrix th.sticky-col {
                z-index: 50 !important;
                background: var(--primary-dark) !important;
            }

            .col-no { left: 0; width: 34px; min-width: 34px; max-width: 34px; }
            .col-nik { left: 34px; width: 80px; min-width: 80px; max-width: 80px; font-family: monospace; }
            .col-nama { left: 114px; width: 170px; min-width: 170px; max-width: 170px; text-align: left; font-weight: 700; }
            .col-jabatan { left: 284px; width: 120px; min-width: 120px; max-width: 120px; text-align: left; }
            .col-dept { left: 404px; width: 55px; min-width: 55px; max-width: 55px; font-weight: 700; border-right: 2px solid var(--slate-400) !important; }
        }

        /* Status colors */
        .status-izin { background-color: #fef3c7 !important; color: #92400e !important; }
        .status-sakit { background-color: #fce7f3 !important; color: #9d174d !important; }
        .status-cuti { background-color: #e0f2fe !important; color: #075985 !important; }
        .status-alpa { background-color: #fee2e2 !important; color: #991b1b !important; }
        .status-libur { background-color: #dcfce7 !important; color: #166534 !important; }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .top-bar {
                display: none !important;
            }
            .report-header {
                box-shadow: none;
                border: 1px solid #000;
                padding: 8px 12px;
                margin-bottom: 8px;
            }
            .table-responsive {
                overflow: visible;
                border: none;
                max-height: none;
                box-shadow: none;
            }
            .datatable-matrix {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
            }
            .datatable-matrix th,
            .datatable-matrix td {
                border: 1px solid #000;
                padding: 3px 2px;
                font-size: 7.5px;
            }
            .sticky-col {
                position: static !important;
            }
        }
    </style>
</head>

<body>

    <!-- ================= 1. SCREEN TOP ACTION BAR ================= -->
    <div class="top-bar">
        <div class="top-bar-left">
            <span class="top-bar-badge">
                <i class="ti ti-layout-grid"></i>
                Format 2: Struktur Komprehensif (Multi-Kolom Harian)
            </span>
            <span style="font-size: 11.5px; color: var(--slate-500);">
                Total: <strong>{{ $laporan_presensi->count() }}</strong> Karyawan
            </span>
        </div>

        <form method="POST" action="{{ route('laporan.cetakpresensi') }}" target="_blank" style="display: inline-block;">
            @csrf
            @foreach($request_params as $k => $v) <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endforeach
            <input type="hidden" name="format_laporan" value="1">
            <input type="hidden" name="format_rekap" value="2">
            <button type="submit" name="exportButton" value="1" class="btn btn-excel">
                <i class="ti ti-file-spreadsheet"></i>
                <span>Export Excel</span>
            </button>
        </form>
    </div>

    <!-- ================= 2. REPORT HEADER ================= -->
    <div class="report-header">
        <div class="header-brand">
            @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo Perusahaan" class="header-logo">
            @else
                <div style="width: 48px; height: 48px; border-radius: 10px; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px;">
                    <i class="ti ti-building"></i>
                </div>
            @endif

            <div class="header-title">
                <h2>LAPORAN PRESENSI (STRUKTUR KOMPREHENSIF)</h2>
                <h3>{{ $generalsetting->nama_perusahaan }}</h3>
                <p>{{ $generalsetting->alamat }} @if($generalsetting->telepon) | Telp: {{ $generalsetting->telepon }} @endif</p>
            </div>
        </div>

        <div class="header-period-card">
            <div class="period-label">Rentang Periode</div>
            <div class="period-value">
                {{ date('d F Y', strtotime($periode_dari)) }} - {{ date('d F Y', strtotime($periode_sampai)) }}
            </div>
        </div>
    </div>

    <!-- ================= 3. COMPREHENSIVE MATRIX TABLE ================= -->
    <div class="table-responsive">
        <table class="datatable-matrix">
            <thead>
                <tr>
                    <th rowspan="4" class="sticky-col col-no">No</th>
                    <th rowspan="4" class="sticky-col col-nik">NIK</th>
                    <th rowspan="4" class="sticky-col col-nama">Nama Karyawan</th>
                    <th rowspan="4" class="sticky-col col-jabatan">Jabatan</th>
                    <th rowspan="4" class="sticky-col col-dept">Dept</th>
                    <th colspan="{{ $jmlhari * 8 }}">Tanggal Presensi Harian</th>
                    <th rowspan="4" style="min-width: 65px">Denda (Rp)</th>
                    <th rowspan="4" style="min-width: 55px">Pot. Jam</th>
                    <th rowspan="4" style="min-width: 55px">Lembur</th>
                    <th colspan="9">Rekap Kehadiran</th>
                </tr>
                <tr>
                    @php $tanggal_presensi = $periode_dari; @endphp
                    @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                        <th colspan="8">{{ getHari(date('Y-m-d', strtotime($tanggal_presensi))) }}</th>
                        @php $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi))); @endphp
                    @endwhile
                    <th rowspan="3">Hadir</th>
                    <th rowspan="3">Izin</th>
                    <th rowspan="3">Sakit</th>
                    <th rowspan="3">Alfa</th>
                    <th rowspan="3">Libur</th>
                    <th rowspan="3">Telat</th>
                    <th rowspan="3">TSM</th>
                    <th rowspan="3">TSP</th>
                    <th rowspan="3">PC</th>
                </tr>
                <tr>
                    @php $tanggal_presensi = $periode_dari; @endphp
                    @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                        <th colspan="8">{{ date('d', strtotime($tanggal_presensi)) }}</th>
                        @php $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi))); @endphp
                    @endwhile
                </tr>
                <tr>
                    @php $tanggal_presensi = $periode_dari; @endphp
                    @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                        <th style="min-width: 60px;">Jadwal</th>
                        <th style="min-width: 36px;">In</th>
                        <th style="min-width: 36px;">Out</th>
                        <th style="min-width: 36px;">Ist-O</th>
                        <th style="min-width: 36px;">Ist-I</th>
                        <th style="min-width: 36px; color: #5eead4;">Lbr</th>
                        <th style="min-width: 34px; color: #fca5a5;">PJ</th>
                        <th style="min-width: 34px; color: #fca5a5;">Dnd</th>
                        @php $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi))); @endphp
                    @endwhile
                </tr>
            </thead>
            <tbody>
                @foreach ($laporan_presensi as $d)
                    @php
                        $tanggal_presensi = $periode_dari;
                        $mapJadwalByDate = $jadwal_bydate[$d['nik']] ?? [];
                        $mapJadwalGrupByDate = $jadwal_grup_bydate[$d['nik']] ?? [];
                        $mapJadwalByDay = $jadwal_byday[$d['nik']] ?? [];
                    @endphp
                    <tr>
                        <td class="sticky-col col-no">{{ $loop->iteration }}</td>
                        <td class="sticky-col col-nik">{{ $d['nik_show'] ?? $d['nik'] }}</td>
                        <td class="sticky-col col-nama">{{ $d['nama_karyawan'] }}</td>
                        <td class="sticky-col col-jabatan">{{ $d['nama_jabatan'] }}</td>
                        <td class="sticky-col col-dept">{{ $d['kode_dept'] }}</td>

                        @php
                            $total_denda = 0; $total_potongan_jam = 0; $total_jam_lembur = 0;
                            $jml_hadir = 0; $jml_sakit = 0; $jml_izin = 0; $jml_cuti = 0;
                            $jml_libur = 0; $jml_alfa = 0; $jml_terlambat = 0;
                            $jml_pulangcepat = 0; $jml_tidakscanmasuk = 0; $jml_tidakscanpulang = 0;
                        @endphp

                        @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                            @php
                                $denda = 0; $potongan_jam = 0;
                                $search = ['nik' => $d['nik'], 'tanggal' => $tanggal_presensi];
                                $ceklibur = ceklibur($datalibur, $search);

                                $snapshot_lembur = isset($d[$tanggal_presensi]) && $d[$tanggal_presensi]['jam_lembur_aktual'] !== null;
                                if ($snapshot_lembur) {
                                    $jml_jam_lembur = $d[$tanggal_presensi]['jam_lembur_aktual'];
                                } else {
                                    $ceklembur = ceklembur($datalembur, $search);
                                    $lembur_jam = hitungLembur($ceklembur);
                                    $jml_jam_lembur = !empty($ceklembur) ? $lembur_jam : 0;
                                }

                                $nama_hari = getHari($tanggal_presensi);
                                $row_d = $d[$tanggal_presensi] ?? null;
                            @endphp

                            @if (isset($d[$tanggal_presensi]))
                                @if ($d[$tanggal_presensi]['status'] == 'h')
                                    @php
                                        $jml_hadir++;
                                        $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                                        $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);
                                        if ($terlambat && $terlambat['menitterlambat'] > 0) $jml_terlambat++;

                                        $denda_db = $d[$tanggal_presensi]['denda'] ?? null;
                                        if ($denda_db !== null) {
                                            $denda = $denda_db;
                                        } elseif ($terlambat) {
                                            $denda = $terlambat['desimal_terlambat'] < 1 ? hitungdenda($denda_list, $terlambat['menitterlambat']) : 0;
                                        }

                                        $pc = hitungpulangcepat($tanggal_presensi, $d[$tanggal_presensi]['jam_out'], $d[$tanggal_presensi]['jam_pulang'], $d[$tanggal_presensi]['istirahat'], $d[$tanggal_presensi]['jam_awal_istirahat'], $d[$tanggal_presensi]['jam_akhir_istirahat'], $d[$tanggal_presensi]['lintashari']);
                                        if ($pc) $jml_pulangcepat++;

                                        $ist_pot = hitungPotonganIstirahat($d[$tanggal_presensi]['istirahat_out'], $d[$tanggal_presensi]['istirahat_in'], $d[$tanggal_presensi]['jam_awal_istirahat'], $d[$tanggal_presensi]['jam_akhir_istirahat']);
                                        $no_abs_pot = (empty($d[$tanggal_presensi]['jam_out']) || empty($d[$tanggal_presensi]['jam_in'])) ? $d[$tanggal_presensi]['total_jam'] : 0;
                                        $pj_ist_stat = $d[$tanggal_presensi]['status_potongan_istirahat'] ?? $generalsetting->potongan_istirahat;
                                        $potongan_jam = $no_abs_pot == 0 ? ($pc + ($terlambat && $terlambat['desimal_terlambat'] >= 1 ? $terlambat['desimal_terlambat'] : 0) + ($pj_ist_stat == 1 ? $ist_pot : 0)) : $no_abs_pot;
                                        
                                        if (($d[$tanggal_presensi]['status_potongan'] ?? $generalsetting->status_potongan_jam) == 0) $potongan_jam = 0;
                                        if (empty($d[$tanggal_presensi]['jam_in'])) $jml_tidakscanmasuk++;
                                        if (empty($d[$tanggal_presensi]['jam_out'])) $jml_tidakscanpulang++;
                                    @endphp
                                    <td>{{ $d[$tanggal_presensi]['nama_jam_kerja'] }}</td>
                                    <td style="color: {{ !empty($d[$tanggal_presensi]['jam_in']) ? '#16a34a' : '#dc2626' }};">{{ !empty($d[$tanggal_presensi]['jam_in']) ? date('H:i', strtotime($d[$tanggal_presensi]['jam_in'])) : '-' }}</td>
                                    <td style="color: {{ !empty($d[$tanggal_presensi]['jam_out']) ? '#16a34a' : '#dc2626' }};">{{ !empty($d[$tanggal_presensi]['jam_out']) ? date('H:i', strtotime($d[$tanggal_presensi]['jam_out'])) : '-' }}</td>
                                    <td>{{ !empty($d[$tanggal_presensi]['istirahat_out']) ? date('H:i', strtotime($d[$tanggal_presensi]['istirahat_out'])) : '-' }}</td>
                                    <td>{{ !empty($d[$tanggal_presensi]['istirahat_in']) ? date('H:i', strtotime($d[$tanggal_presensi]['istirahat_in'])) : '-' }}</td>
                                    <td style="color: #0d9488;">{{ $jml_jam_lembur > 0 ? formatAngkaDesimal($jml_jam_lembur) : '-' }}</td>
                                    <td style="color: #9333ea;">{{ $potongan_jam > 0 ? formatAngkaDesimal($potongan_jam) : '-' }}</td>
                                    <td style="color: #b91c1c;">{{ $denda > 0 ? formatAngka($denda) : '-' }}</td>
                                @else
                                    @php
                                        $st_map = ['i' => ['IZIN', 'status-izin', 'jml_izin'], 's' => ['SAKIT', 'status-sakit', 'jml_sakit'], 'c' => ['CUTI', 'status-cuti', 'jml_cuti'], 'a' => ['ALPA', 'status-alpa', 'jml_alfa']];
                                        $curr = $st_map[$d[$tanggal_presensi]['status']];
                                        ${$curr[2]}++;
                                        if ($d[$tanggal_presensi]['status'] == 'a' || $d[$tanggal_presensi]['status'] == 'i') {
                                            $potongan_jam = ($d[$tanggal_presensi]['status_potongan'] ?? $generalsetting->status_potongan_jam) == 1 ? $d[$tanggal_presensi]['total_jam'] : 0;
                                        }
                                        $denda = $d[$tanggal_presensi]['denda'] ?? 0;
                                    @endphp
                                    <td colspan="8" class="{{ $curr[1] }}"><strong>{{ $curr[0] }}</strong></td>
                                @endif
                            @else
                                @php
                                    $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                                    if (!empty($ceklibur)) {
                                        $jml_libur++;
                                        echo "<td colspan='8' class='status-libur'><strong>LIBUR</strong></td>";
                                    } else {
                                        $totalJamJadwal = $mapJadwalByDate[$tanggal_presensi] ?? null;
                                        if ($totalJamJadwal === null) $totalJamJadwal = $mapJadwalGrupByDate[$tanggal_presensi] ?? null;
                                        if ($totalJamJadwal === null) $totalJamJadwal = $mapJadwalByDay[$nama_hari] ?? null;
                                        if ($totalJamJadwal === null) {
                                            $keyDeptCabang = $d['kode_dept'] . '|' . $d['kode_cabang'];
                                            $mapDept = $jadwal_bydept[$keyDeptCabang] ?? [];
                                            $totalJamJadwal = $mapDept[$nama_hari] ?? null;
                                        }
                                        if ($totalJamJadwal === null) {
                                            $totalJamJadwal = $jadwal_global[$nama_hari] ?? null;
                                        }

                                        if ($totalJamJadwal !== null && !$is_future) {
                                            $jml_alfa++;
                                            $potongan_jam = $generalsetting->status_potongan_jam == 1 ? (is_array($totalJamJadwal) ? $totalJamJadwal['total_jam'] : $totalJamJadwal) : 0;
                                            echo "<td colspan='8' class='status-alpa'><strong>ALPA</strong></td>";
                                        } else {
                                            echo "<td colspan='8'>-</td>";
                                        }
                                    }
                                @endphp
                            @endif

                            @php
                                $total_denda += $denda;
                                $total_potongan_jam += $potongan_jam;
                                $total_jam_lembur += $jml_jam_lembur;
                                $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                            @endphp
                        @endwhile

                        <!-- Totals & Recap -->
                        <td style="text-align: right; font-weight: 700; color: {{ $total_denda > 0 ? '#b91c1c' : 'inherit' }};">{{ $total_denda > 0 ? formatAngka($total_denda) : '-' }}</td>
                        <td style="font-weight: 700; color: {{ $total_potongan_jam > 0 ? '#9333ea' : 'inherit' }};">{{ $total_potongan_jam > 0 ? formatAngkaDesimal($total_potongan_jam) : '-' }}</td>
                        <td style="font-weight: 700; color: {{ $total_jam_lembur > 0 ? '#0d9488' : 'inherit' }};">{{ $total_jam_lembur > 0 ? formatAngkaDesimal($total_jam_lembur) : '-' }}</td>
                        <td style="font-weight: 800; background: var(--primary-light); color: var(--primary-dark);">{{ $jml_hadir }}</td>
                        <td style="color: #92400e; font-weight: 700;">{{ $jml_izin }}</td>
                        <td style="color: #9d174d; font-weight: 700;">{{ $jml_sakit }}</td>
                        <td style="color: {{ $jml_alfa > 0 ? '#dc2626' : 'inherit' }}; font-weight: 800;">{{ $jml_alfa }}</td>
                        <td style="color: #166534; font-weight: 700;">{{ $jml_libur }}</td>
                        <td style="color: {{ $jml_terlambat > 0 ? '#b91c1c' : 'inherit' }}; font-weight: 700;">{{ $jml_terlambat }}</td>
                        <td>{{ $jml_tidakscanmasuk }}</td>
                        <td>{{ $jml_tidakscanpulang }}</td>
                        <td>{{ $jml_pulangcepat }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
