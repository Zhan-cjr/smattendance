<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Presensi Karyawan - {{ date('d/m/Y H:i') }}</title>
    
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
            size: A4 landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-size: 11px;
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

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Buttons */
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

        .btn-lock {
            background: #fef3c7;
            color: #92400e;
            border-color: #fde68a;
        }
        .btn-lock:hover {
            background: #fde68a;
        }

        .btn-unlock {
            background: var(--slate-100);
            color: var(--slate-700);
            border-color: var(--slate-300);
        }
        .btn-unlock:hover {
            background: var(--slate-200);
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
            letter-spacing: -0.01em;
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
            letter-spacing: 0.05em;
        }

        .header-period-card .period-value {
            font-size: 12px;
            font-weight: 800;
            color: var(--slate-900);
            margin-top: 2px;
        }

        /* Legend Badge Strip */
        .legend-strip {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            padding: 8px 14px;
            border-radius: 10px;
            border: 1px solid var(--slate-200);
            margin-bottom: 14px;
            font-size: 10.5px;
        }

        .legend-strip-title {
            font-weight: 800;
            color: var(--slate-500);
            text-transform: uppercase;
            font-size: 10px;
            margin-right: 4px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
            color: var(--slate-700);
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
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
            font-size: 10px;
            min-width: 100%;
        }

        .datatable-matrix th,
        .datatable-matrix td {
            border-right: 1px solid var(--slate-200);
            border-bottom: 1px solid var(--slate-200);
            padding: 6px 5px;
            vertical-align: middle;
            white-space: nowrap;
            box-sizing: border-box;
        }

        .datatable-matrix thead th {
            background-color: var(--primary);
            color: #ffffff;
            text-transform: uppercase;
            text-align: center;
            font-weight: 700;
            font-size: 10px;
            position: sticky;
            z-index: 20;
            line-height: 1.2;
            height: 32px;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        /* Gradient header top row */
        .datatable-matrix thead tr:nth-child(1) th {
            top: 0;
            z-index: 25;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
        }
        .datatable-matrix thead tr:nth-child(2) th {
            top: 32px;
            z-index: 24;
            background: var(--primary-dark);
            font-size: 9.5px;
        }
        .datatable-matrix thead tr:nth-child(3) th {
            top: 64px;
            z-index: 23;
            background: var(--primary);
            font-size: 9px;
            font-weight: 800;
        }

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

            .col-no { left: 0; width: 34px; min-width: 34px; max-width: 34px; text-align: center; font-weight: bold; }
            .col-nik { left: 34px; width: 80px; min-width: 80px; max-width: 80px; font-family: monospace; font-size: 10.5px; }
            .col-nama { left: 114px; width: 170px; min-width: 170px; max-width: 170px; font-weight: 700; color: var(--slate-900); }
            .col-jabatan { left: 284px; width: 120px; min-width: 120px; max-width: 120px; color: var(--slate-600); }
            .col-dept { left: 404px; width: 55px; min-width: 55px; max-width: 55px; text-align: center; font-weight: 700; color: var(--slate-700); }
            
            .col-dept {
                border-right: 2px solid var(--slate-400) !important;
            }
        }

        /* Cell Styling */
        .cell-daily {
            text-align: center;
            font-size: 9.5px;
            line-height: 1.25;
            min-width: 68px;
            padding: 4px 3px !important;
        }

        .badge-shift {
            font-weight: 800;
            color: #0f172a;
            font-size: 9px;
            display: block;
        }

        .time-sched {
            color: #2563eb;
            font-size: 8.5px;
            font-weight: 600;
        }

        .time-in {
            font-weight: 700;
            font-size: 9px;
        }

        .text-late {
            color: #dc2626;
            font-weight: 700;
            font-size: 8.5px;
        }

        .text-denda {
            color: #b91c1c;
            font-weight: 700;
            font-size: 8.5px;
        }

        .text-potongan {
            color: #9333ea;
            font-weight: 700;
            font-size: 8.5px;
        }

        .text-lembur {
            color: #0d9488;
            font-weight: 700;
            font-size: 8.5px;
        }

        /* Attendance Status Colors */
        .status-izin { background-color: #fef3c7 !important; color: #92400e !important; }
        .status-sakit { background-color: #fce7f3 !important; color: #9d174d !important; }
        .status-cuti { background-color: #e0f2fe !important; color: #075985 !important; }
        .status-alpa { background-color: #fee2e2 !important; color: #991b1b !important; }
        .status-libur { background-color: #dcfce7 !important; color: #166534 !important; }
        .status-libur-reguler { background-color: #ffedd5 !important; color: #9a3412 !important; }

        /* Summary Columns */
        .col-summary {
            text-align: center;
            font-weight: 700;
            background-color: var(--slate-50);
        }

        .col-summary-highlight {
            background-color: var(--primary-light);
            color: var(--primary-dark);
            font-weight: 800;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .top-bar, .legend-strip {
                display: none !important;
            }
            .report-header {
                box-shadow: none;
                border: 1px solid #000;
                padding: 10px 14px;
                margin-bottom: 10px;
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
                padding: 4px 2px;
                font-size: 8.5px;
            }
            .sticky-col {
                position: static !important;
                background-color: transparent !important;
            }
            .datatable-matrix thead th {
                background-color: #eaeaea !important;
                color: #000000 !important;
            }
        }
    </style>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <!-- ================= 1. SCREEN TOP ACTION BAR ================= -->
    <div class="top-bar">
        <div class="top-bar-left">
            <span class="top-bar-badge">
                <i class="ti ti-file-analytics"></i>
                Rekapitulasi Presensi (V2 Engine)
            </span>
            <span style="font-size: 11.5px; color: var(--slate-500);">
                Total: <strong>{{ $laporan_presensi->count() }}</strong> Karyawan Terdata
            </span>
        </div>

        <div class="top-bar-actions">
            <!-- Tombol Kunci Laporan -->
            <form id="formKunciLaporan" method="POST" action="{{ route('laporan.kuncilaporan') }}" style="display: inline-block;">
                @csrf
                @foreach($request_params as $k => $v) <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endforeach
                <button type="submit" class="btn btn-lock">
                    <i class="ti ti-lock"></i>
                    <span>Kunci Laporan</span>
                </button>
            </form>

            <!-- Tombol Batal Kunci -->
            <form id="formBatalkanKunciLaporan" method="POST" action="{{ route('laporan.batalkankuncilaporan') }}" style="display: inline-block;">
                @csrf
                @foreach($request_params as $k => $v) <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endforeach
                <button type="submit" class="btn btn-unlock">
                    <i class="ti ti-lock-open"></i>
                    <span>Batal Kunci</span>
                </button>
            </form>

            <!-- Tombol Export Excel -->
            <form method="POST" action="{{ route('laporan.cetakpresensi') }}" target="_blank" style="display: inline-block;">
                @csrf
                @foreach($request_params as $k => $v) <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endforeach
                <input type="hidden" name="format_laporan" value="1">
                <input type="hidden" name="format_rekap" value="1">
                <button type="submit" name="exportButton" value="1" class="btn btn-excel">
                    <i class="ti ti-file-spreadsheet"></i>
                    <span>Export Excel</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ================= 2. REPORT HEADER ================= -->
    <div class="report-header">
        <div class="header-brand">
            @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo Perusahaan" class="header-logo">
            @else
                <div style="width: 50px; height: 50px; border-radius: 10px; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px;">
                    <i class="ti ti-building"></i>
                </div>
            @endif

            <div class="header-title">
                <h2>LAPORAN REKAPITULASI PRESENSI</h2>
                <h3>{{ $generalsetting->nama_perusahaan }}</h3>
                <p>{{ $generalsetting->alamat }} @if($generalsetting->telepon) | Telp: {{ $generalsetting->telepon }} @endif</p>
            </div>
        </div>

        <div class="header-period-card">
            <div class="period-label">Rentang Periode Presensi</div>
            <div class="period-value">
                {{ date('d F Y', strtotime($periode_dari)) }} - {{ date('d F Y', strtotime($periode_sampai)) }}
            </div>
            <div style="font-size: 10px; color: var(--slate-500); margin-top: 3px;">
                Total: <strong>{{ $jmlhari }}</strong> Hari Kalender
            </div>
        </div>
    </div>

    <!-- ================= 3. COLOR LEGEND STRIP ================= -->
    <div class="legend-strip">
        <span class="legend-strip-title">Keterangan:</span>
        <span class="legend-item"><span class="legend-dot" style="background: #ecfdf5; border: 1px solid #10b981;"></span> Hadir</span>
        <span class="legend-item"><span class="legend-dot" style="background: #fef3c7; border: 1px solid #f59e0b;"></span> Izin</span>
        <span class="legend-item"><span class="legend-dot" style="background: #fce7f3; border: 1px solid #ec4899;"></span> Sakit</span>
        <span class="legend-item"><span class="legend-dot" style="background: #e0f2fe; border: 1px solid #0ea5e9;"></span> Cuti</span>
        <span class="legend-item"><span class="legend-dot" style="background: #fee2e2; border: 1px solid #ef4444;"></span> Alpa</span>
        <span class="legend-item"><span class="legend-dot" style="background: #dcfce7; border: 1px solid #22c55e;"></span> Libur Nasional</span>
        <span class="legend-item"><span class="legend-dot" style="background: #ffedd5; border: 1px solid #f97316;"></span> Libur Reguler</span>
        <span class="legend-item" style="margin-left: auto; color: #f59e0b;"><i class="ti ti-lock"></i> Data Terkunci (Snapshot)</span>
    </div>

    <!-- ================= 4. MAIN ATTENDANCE MATRIX TABLE ================= -->
    <div class="table-responsive">
        <table class="datatable-matrix">
            <thead>
                <!-- Top Row -->
                <tr>
                    <th rowspan="3" class="sticky-col col-no">No</th>
                    <th rowspan="3" class="sticky-col col-nik">NIK</th>
                    <th rowspan="3" class="sticky-col col-nama">Nama Karyawan</th>
                    <th rowspan="3" class="sticky-col col-jabatan">Jabatan</th>
                    <th rowspan="3" class="sticky-col col-dept">Dept</th>
                    <th colspan="{{ $jmlhari }}">
                        Tanggal Absensi ({{ date('d M', strtotime($periode_dari)) }} - {{ date('d M Y', strtotime($periode_sampai)) }})
                    </th>
                    <th rowspan="3" style="min-width: 75px;">Denda (Rp)</th>
                    <th rowspan="3" style="min-width: 60px;">Pot. Jam</th>
                    <th rowspan="3" style="min-width: 60px;">Lembur</th>
                    <th colspan="9">Rekapitulasi Kehadiran</th>
                </tr>

                <!-- Middle Row: Nama Hari -->
                <tr>
                    @php $tanggal_presensi = $periode_dari; @endphp
                    @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                        <th style="min-width: 68px;">{{ getHari(date('Y-m-d', strtotime($tanggal_presensi))) }}</th>
                        @php $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi))); @endphp
                    @endwhile
                    <th rowspan="2" style="min-width: 40px;">Hadir</th>
                    <th rowspan="2" style="min-width: 40px;">Izin</th>
                    <th rowspan="2" style="min-width: 40px;">Sakit</th>
                    <th rowspan="2" style="min-width: 40px;">Alfa</th>
                    <th rowspan="2" style="min-width: 40px;">Libur</th>
                    <th rowspan="2" style="min-width: 40px;">Telat</th>
                    <th rowspan="2" style="min-width: 40px;" title="Tidak Scan Masuk">TSM</th>
                    <th rowspan="2" style="min-width: 40px;" title="Tidak Scan Pulang">TSP</th>
                    <th rowspan="2" style="min-width: 40px;" title="Pulang Cepat">PC</th>
                </tr>

                <!-- Bottom Row: Nomor Tanggal -->
                <tr>
                    @php $tanggal_presensi = $periode_dari; @endphp
                    @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                        <th>{{ date('d', strtotime($tanggal_presensi)) }}</th>
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
                        $lemburKhusus = $lembur_khusus_map[$d['nik']] ?? null;
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
                                $cell_class = ''; $denda = 0; $potongan_jam = 0;
                                $libur_key = $d['nik'] . '|' . $tanggal_presensi;
                                $ceklibur = $datalibur_indexed[$libur_key] ?? ($datalibur_by_tanggal[$tanggal_presensi] ?? []);

                                if (!empty($ceklibur)) {
                                    $is_libur = true;
                                } else {
                                    $has_schedule = false; $nama_hari_check = getHari($tanggal_presensi);
                                    if (isset($mapJadwalByDate[$tanggal_presensi])) $has_schedule = true;
                                    elseif (isset($mapJadwalGrupByDate[$tanggal_presensi])) $has_schedule = true;
                                    elseif (isset($mapJadwalByDay[$nama_hari_check])) $has_schedule = true;
                                    else {
                                        $keyDC = $d['kode_dept'] . '|' . $d['kode_cabang'];
                                        $mapD = $jadwal_bydept[$keyDC] ?? [];
                                        if (isset($mapD[$nama_hari_check])) $has_schedule = true;
                                        elseif (isset($jadwal_global[$nama_hari_check])) $has_schedule = true;
                                    }
                                    $is_libur = !$has_schedule;
                                }
                                $tipe_hari = $is_libur ? 2 : 1;

                                $lembur_key = $d['nik'] . '|' . $tanggal_presensi;
                                $ceklembur_data = $datalembur_indexed[$lembur_key] ?? [];
                                $snapshot_lembur = isset($d[$tanggal_presensi]) && $d[$tanggal_presensi]['jam_lembur_aktual'] !== null;

                                if ($snapshot_lembur) {
                                    $jml_jam_lembur = $d[$tanggal_presensi]['is_lembur_khusus'] ? $d[$tanggal_presensi]['jam_lembur_aktual'] : $d[$tanggal_presensi]['jam_lembur_netto'];
                                } else {
                                    $lembur_aktual = hitungLembur($ceklembur_data);
                                    $jam_netto_harian = $lembur_aktual > 0 ? hitungJamNetto($lembur_aktual, $tipe_hari) : 0;
                                    $jml_jam_lembur = $lemburKhusus ? $lembur_aktual : $jam_netto_harian;
                                }
                                $ket = "";
                            @endphp

                            @if (isset($d[$tanggal_presensi]))
                                @if ($d[$tanggal_presensi]['status'] == 'h')
                                    @php
                                        $jml_hadir++;
                                        $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                                        $jam_in = !empty($d[$tanggal_presensi]['jam_in']) ? date('H:i', strtotime($d[$tanggal_presensi]['jam_in'])) : 'X';
                                        $jam_out = !empty($d[$tanggal_presensi]['jam_out']) ? date('H:i', strtotime($d[$tanggal_presensi]['jam_out'])) : 'X';
                                        $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);
                                        if ($terlambat && $terlambat['menitterlambat'] > 0) $jml_terlambat++;
                                        $denda_db = $d[$tanggal_presensi]['denda'] ?? null;
                                        if ($denda_db !== null) { $denda = $denda_db; } 
                                        else if ($terlambat) { $denda = $terlambat['desimal_terlambat'] < 1 ? hitungdenda($denda_list, $terlambat['menitterlambat']) : 0; }
                                        $pc = hitungpulangcepat($tanggal_presensi, $d[$tanggal_presensi]['jam_out'], $d[$tanggal_presensi]['jam_pulang'], $d[$tanggal_presensi]['istirahat'], $d[$tanggal_presensi]['jam_awal_istirahat'], $d[$tanggal_presensi]['jam_akhir_istirahat'], $d[$tanggal_presensi]['lintashari']);
                                        if ($pc) $jml_pulangcepat++;
                                        $ist_pot = hitungPotonganIstirahat($d[$tanggal_presensi]['istirahat_out'], $d[$tanggal_presensi]['istirahat_in'], $d[$tanggal_presensi]['jam_awal_istirahat'], $d[$tanggal_presensi]['jam_akhir_istirahat']);
                                        $no_abs_pot = (empty($d[$tanggal_presensi]['jam_out']) || empty($d[$tanggal_presensi]['jam_in'])) ? $d[$tanggal_presensi]['total_jam'] : 0;
                                        $pj_ist_stat = $d[$tanggal_presensi]['status_potongan_istirahat'] ?? $generalsetting->potongan_istirahat;
                                        $potongan_jam = $no_abs_pot == 0 ? ($pc + ($terlambat && $terlambat['desimal_terlambat'] >= 1 ? $terlambat['desimal_terlambat'] : 0) + ($pj_ist_stat == 1 ? $ist_pot : 0)) : $no_abs_pot;
                                        if (($d[$tanggal_presensi]['status_potongan'] ?? $generalsetting->status_potongan_jam) == 0) $potongan_jam = 0;
                                        if (empty($d[$tanggal_presensi]['jam_in'])) $jml_tidakscanmasuk++;
                                        if (empty($d[$tanggal_presensi]['jam_out'])) $jml_tidakscanpulang++;

                                        $ket = "<span class='badge-shift'>" . e($d[$tanggal_presensi]['nama_jam_kerja']) . "</span>" .
                                               "<span class='time-sched'>" . date('H:i', strtotime($d[$tanggal_presensi]['jam_masuk'])) . "-" . date('H:i', strtotime($d[$tanggal_presensi]['jam_pulang'])) . "</span><br/>" .
                                               "<span class='time-in' style='color:" . (!empty($d[$tanggal_presensi]['jam_in']) ? "#16a34a" : "#dc2626") . "'>" . $jam_in . "</span> - " .
                                               "<span class='time-in' style='color:" . (!empty($d[$tanggal_presensi]['jam_out']) ? "#16a34a" : "#dc2626") . "'>" . $jam_out . "</span>";
                                        
                                        if ($terlambat && $terlambat['menitterlambat'] > 0) $ket .= "<br/><span class='text-late'>" . $terlambat['show_laporan'] . "</span>";
                                        if ($denda > 0) $ket .= "<br/><span class='text-denda'>Rp " . formatAngka($denda) . "</span>";
                                        if ($potongan_jam > 0) $ket .= "<br/><span class='text-potongan'>PJ: " . formatAngkaDesimal($potongan_jam) . "j</span>";
                                        if ($jml_jam_lembur > 0) $ket .= "<br/><span class='text-lembur'>OT: " . formatAngkaDesimal($jml_jam_lembur) . "j</span>";
                                    @endphp
                                @else
                                    @php
                                        $status_map = ['i' => ['IZIN', 'status-izin', 'jml_izin'], 's' => ['SAKIT', 'status-sakit', 'jml_sakit'], 'c' => ['CUTI', 'status-cuti', 'jml_cuti'], 'a' => ['ALPA', 'status-alpa', 'jml_alfa']];
                                        $st = $status_map[$d[$tanggal_presensi]['status']];
                                        $cell_class = $st[1]; ${$st[2]}++;
                                        $ket = "<strong>" . $st[0] . "</strong><br/>" . e($d[$tanggal_presensi]['keterangan_izin_absen'] ?? ($d[$tanggal_presensi]['keterangan_izin_sakit'] ?? ($d[$tanggal_presensi]['keterangan_izin_cuti'] ?? "")));
                                        if ($d[$tanggal_presensi]['status'] == 'a' || $d[$tanggal_presensi]['status'] == 'i') {
                                            $potongan_jam = ($d[$tanggal_presensi]['status_potongan'] ?? $generalsetting->status_potongan_jam) == 1 ? $d[$tanggal_presensi]['total_jam'] : 0;
                                            if ($potongan_jam > 0) $ket .= "<br/><span class='text-potongan'>PJ: " . formatAngkaDesimal($potongan_jam) . "j</span>";
                                        }
                                        $denda = $d[$tanggal_presensi]['denda'] ?? 0;
                                        if ($denda > 0) $ket .= "<br/><span class='text-denda'>Denda: " . formatAngka($denda) . "</span>";
                                    @endphp
                                @endif
                            @else
                                @php
                                    $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                                    if (!empty($ceklibur)) {
                                        $cell_class = 'status-libur'; $jml_libur++;
                                        $ket = "<strong>LIBUR</strong><br/>" . e($ceklibur[0]['keterangan']);
                                    } else {
                                        $fallback = $mapJadwalByDate[$tanggal_presensi] ?? ($mapJadwalGrupByDate[$tanggal_presensi] ?? ($mapJadwalByDay[getHari($tanggal_presensi)] ?? ($jadwal_bydept[$d['kode_dept'].'|'.$d['kode_cabang']][getHari($tanggal_presensi)] ?? ($jadwal_global[getHari($tanggal_presensi)] ?? null))));
                                        if (is_array($fallback) && !$is_future) {
                                            $jml_alfa++; $cell_class = 'status-alpa';
                                            $potongan_jam = $generalsetting->status_potongan_jam == 1 ? $fallback['total_jam'] : 0;
                                            $ket = "<strong>ALPA</strong>";
                                            if ($potongan_jam > 0) $ket .= "<br/><span class='text-potongan'>PJ: " . formatAngkaDesimal($potongan_jam) . "j</span>";
                                        }
                                    }
                                    if ($is_libur && empty($ceklibur)) { $cell_class = 'status-libur-reguler'; $ket = "<strong>OFF</strong>"; }
                                @endphp
                            @endif

                            @php
                                $total_denda += $denda; $total_potongan_jam += $potongan_jam; $total_jam_lembur += $jml_jam_lembur;
                                if (!empty($ceklibur)) $cell_class = 'status-libur';
                                elseif ($is_libur) $cell_class = 'status-libur-reguler';
                                $is_locked = isset($d[$tanggal_presensi]['status_potongan']) && $d[$tanggal_presensi]['status_potongan'] !== null;
                            @endphp

                            <td class="cell-daily {{ $cell_class }}" style="position: relative;">
                                @if ($is_locked)
                                    <span style="position: absolute; top: 2px; right: 2px; color: #f59e0b;" title="Data Terkunci"><i class="ti ti-lock" style="font-size: 8px;"></i></span>
                                @endif
                                {!! $ket !!}
                            </td>

                            @php $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi))); @endphp
                        @endwhile

                        <!-- Totals & Recap -->
                        <td style="text-align: right; font-weight: 700; color: {{ $total_denda > 0 ? '#b91c1c' : 'inherit' }};">
                            {{ $total_denda > 0 ? formatAngka($total_denda) : '-' }}
                        </td>
                        <td style="text-align: center; font-weight: 700; color: {{ $total_potongan_jam > 0 ? '#9333ea' : 'inherit' }};">
                            {{ $total_potongan_jam > 0 ? formatAngkaDesimal($total_potongan_jam) : '-' }}
                        </td>
                        <td style="text-align: center; font-weight: 700; color: {{ $total_jam_lembur > 0 ? '#0d9488' : 'inherit' }};">
                            {{ $total_jam_lembur > 0 ? formatAngkaDesimal($total_jam_lembur) : '-' }}
                            @if ($lemburKhusus) <span style="font-size: 9px; color: #ea580c;">★</span> @endif
                        </td>
                        <td class="col-summary col-summary-highlight">{{ $jml_hadir }}</td>
                        <td class="col-summary" style="color: #92400e;">{{ $jml_izin }}</td>
                        <td class="col-summary" style="color: #9d174d;">{{ $jml_sakit }}</td>
                        <td class="col-summary" style="color: {{ $jml_alfa > 0 ? '#dc2626' : 'inherit' }}; font-weight: {{ $jml_alfa > 0 ? '800' : '600' }};">{{ $jml_alfa }}</td>
                        <td class="col-summary" style="color: #166534;">{{ $jml_libur }}</td>
                        <td class="col-summary" style="color: {{ $jml_terlambat > 0 ? '#b91c1c' : 'inherit' }};">{{ $jml_terlambat }}</td>
                        <td class="col-summary">{{ $jml_tidakscanmasuk }}</td>
                        <td class="col-summary">{{ $jml_tidakscanpulang }}</td>
                        <td class="col-summary">{{ $jml_pulangcepat }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ================= 5. SWEETALERT LOCK REPORT SCRIPTS ================= -->
    <script>
        $(function() {
            $('#formKunciLaporan, #formBatalkanKunciLaporan').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let data = form.serialize();
                let isLock = form.attr('id') === 'formKunciLaporan';
                let actionText = isLock ? 'mengunci' : 'membatalkan kunci';
                
                Swal.fire({
                    title: 'Konfirmasi Tindakan',
                    text: `Apakah Anda yakin ingin ${actionText} seluruh data laporan presensi pada periode ini?`,
                    icon: isLock ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isLock ? '#0a5c41' : '#d33',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: isLock ? 'Ya, Kunci Data!' : 'Ya, Batalkan Kunci!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mohon Tunggu...',
                            text: 'Sedang memproses dan menyinkronkan data presensi...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: data,
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#0a5c41'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: response.message || 'Terjadi kesalahan pada sistem.',
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errMsg = 'Terjadi kesalahan saat memproses data.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errMsg = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    text: errMsg,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
