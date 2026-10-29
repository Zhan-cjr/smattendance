@inject('pph21Service', 'App\Services\Pph21Service')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Rekapitulasi Gaji Karyawan - {{ date('d/m/Y H:i') }}</title>
    
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
            padding: 6px 6px;
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
            font-size: 9.5px;
            position: sticky;
            z-index: 20;
            line-height: 1.2;
            height: 30px;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        .datatable-matrix thead tr:nth-child(1) th { top: 0; z-index: 25; background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%); }
        .datatable-matrix thead tr:nth-child(2) th { top: 30px; z-index: 24; background: var(--primary-dark); }

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
            .col-nik { left: 34px; width: 80px; min-width: 80px; max-width: 80px; font-family: monospace; font-size: 10px; }
            .col-nama { left: 114px; width: 170px; min-width: 170px; max-width: 170px; font-weight: 700; color: var(--slate-900); border-right: 2px solid var(--slate-400) !important; }
        }

        /* Footer Highlights */
        .datatable-matrix tfoot th {
            position: sticky;
            bottom: 0;
            z-index: 20;
            background: #1e293b;
            color: #ffffff;
            font-weight: 800;
            border-top: 2px solid #0f172a;
            padding: 8px 6px;
        }

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
                padding: 4px 3px;
                font-size: 7.5px;
            }
            .sticky-col {
                position: static !important;
            }
        }
    </style>
</head>

<body>

    @php
        $isPphActive = $pph21Service->isAktif();
        $pphSetting = $isPphActive ? $pph21Service->getSetting() : null;
        $isGrossUp = $isPphActive && $pphSetting->metode_tanggungan === 'GROSS_UP';
        
        $bulanNum = $bulan ?? request('bulan') ?? date('m', strtotime($periode_dari));
        $tahunNum = $tahun ?? request('tahun') ?? date('Y', strtotime($periode_dari));
    @endphp

    <!-- ================= 1. SCREEN TOP ACTION BAR ================= -->
    <div class="top-bar">
        <div class="top-bar-left">
            <span class="top-bar-badge">
                <i class="ti ti-cash"></i>
                Laporan Gaji & Payroll (V2 Engine)
            </span>
            <span style="font-size: 11.5px; color: var(--slate-500);">
                Total: <strong>{{ $laporan_presensi->count() }}</strong> Karyawan Terdata
            </span>
        </div>

        <form method="POST" action="{{ route('laporan.cetakpresensi') }}" target="_blank" style="display: inline-block;">
            @csrf
            @foreach($request_params as $k => $v) <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endforeach
            <input type="hidden" name="format_laporan" value="2">
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
                <h2>LAPORAN REKAPITULASI GAJI (PAYROLL)</h2>
                <h3>{{ $generalsetting->nama_perusahaan }}</h3>
                <p>{{ $generalsetting->alamat }} @if($generalsetting->telepon) | Telp: {{ $generalsetting->telepon }} @endif</p>
            </div>
        </div>

        <div class="header-period-card">
            <div class="period-label">Rentang Periode Penggajian</div>
            <div class="period-value">
                {{ date('d F Y', strtotime($periode_dari)) }} - {{ date('d F Y', strtotime($periode_sampai)) }}
            </div>
        </div>
    </div>

    <!-- ================= 3. PAYROLL MATRIX TABLE ================= -->
    <div class="table-responsive">
        <table class="datatable-matrix">
            <thead>
                <tr>
                    <th rowspan="2" class="sticky-col col-no">No</th>
                    <th rowspan="2" class="sticky-col col-nik">NIK</th>
                    <th rowspan="2" class="sticky-col col-nama">Nama Karyawan</th>
                    <th rowspan="2">Rekening Bank</th>
                    <th rowspan="2">Status</th>
                    <th rowspan="2">Jabatan</th>
                    <th rowspan="2">Dept</th>
                    <th rowspan="2">Cabang</th>
                    <th rowspan="2">Gaji Pokok</th>
                    @if(count($jenis_tunjangan) > 0 || $isGrossUp)
                        <th colspan="{{ count($jenis_tunjangan) + ($isGrossUp ? 1 : 0) }}">Tunjangan</th>
                    @endif
                    <th rowspan="2" style="background: #ea580c;">Gaji Bruto</th>
                    <th rowspan="2">Jam Kerja</th>
                    <th rowspan="2">Upah/Jam</th>
                    <th rowspan="2" style="background: #b91c1c;">Denda</th>
                    <th colspan="2" style="background: #b91c1c;">Pot. Jam Kerja</th>
                    <th colspan="2" style="background: #b91c1c;">BPJS</th>
                    <th rowspan="2" style="background: #b91c1c;">Pinjaman</th>
                    @if ($isPphActive)
                        <th rowspan="2" style="background: #b91c1c;">PPh 21</th>
                    @endif
                    <th rowspan="2" style="background: #991b1b;">Total Pot.</th>
                    <th colspan="2" style="background: #0d9488;">Lembur</th>
                    <th colspan="2" style="background: #0284c7;">Penyesuaian</th>
                    <th rowspan="2" style="background: #065f46;">Gaji Bersih</th>
                </tr>
                <tr>
                    @foreach ($jenis_tunjangan as $j)
                        <th>{{ $j->jenis_tunjangan }}</th>
                    @endforeach
                    @if ($isGrossUp)
                        <th style="background: #0d9488;">Tunj. Pajak</th>
                    @endif
                    <th style="background: #b91c1c;">Jam</th>
                    <th style="background: #b91c1c;">Jumlah (Rp)</th>
                    <th style="background: #b91c1c;">Kesehatan</th>
                    <th style="background: #b91c1c;">Ketenagakerjaan</th>
                    <th style="background: #0d9488;">Jam (A|N)</th>
                    <th style="background: #0d9488;">Nominal (Rp)</th>
                    <th style="background: #0284c7;">(+) Tambah</th>
                    <th style="background: #0284c7;">(-) Kurang</th>
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
                    $total_pph21 = 0;
                    $total_tunjangan_pajak = 0;
                @endphp

                @foreach ($laporan_presensi as $d)
                    @php
                        $tanggal_presensi = $periode_dari;
                        $mapJadwalByDate = $jadwal_bydate[$d['nik']] ?? [];
                        $mapJadwalGrupByDate = $jadwal_grup_bydate[$d['nik']] ?? [];
                        $mapJadwalByDay = $jadwal_byday[$d['nik']] ?? [];

                        $total_tunjangan = 0;
                        foreach ($jenis_tunjangan as $j) {
                            $total_tunjangan += $d[$j->kode_jenis_tunjangan];
                        }

                        $total_denda = 0;
                        $total_potongan_jam = 0;
                        $total_jam_lembur_aktual = 0;
                        $total_jam_netto_lembur = 0;
                        $total_nominal_lembur_snapshot = 0;
                        $has_lembur_snapshot = false;
                        $lemburKhusus = $lembur_khusus_map[$d['nik']] ?? null;

                        while (strtotime($tanggal_presensi) <= strtotime($periode_sampai)) {
                            $denda = 0;
                            $potongan_jam = 0;

                            $is_libur_nasional = isset($libur_nasional_dates[$tanggal_presensi]);
                            if ($is_libur_nasional) {
                                $is_libur = true;
                            } else {
                                $has_schedule = false;
                                $nama_hari = getHari($tanggal_presensi);
                                if (isset($mapJadwalByDate[$tanggal_presensi])) $has_schedule = true;
                                elseif (isset($mapJadwalGrupByDate[$tanggal_presensi])) $has_schedule = true;
                                elseif (isset($mapJadwalByDay[$nama_hari])) $has_schedule = true;
                                else {
                                    $keyDC = $d['kode_dept'] . '|' . $d['kode_cabang'];
                                    $mapD = $jadwal_bydept[$keyDC] ?? [];
                                    if (isset($mapD[$nama_hari])) $has_schedule = true;
                                }
                                $is_libur = !$has_schedule;
                            }
                            $tipe_hari = $is_libur ? 2 : 1;

                            $lembur_key = $d['nik'] . '|' . $tanggal_presensi;
                            $ceklembur_data = $datalembur_indexed[$lembur_key] ?? [];
                            $snapshot_lembur = isset($d[$tanggal_presensi]) && ($d[$tanggal_presensi]['jam_lembur_aktual'] ?? null) !== null;
                            if ($snapshot_lembur) {
                                $has_lembur_snapshot = true;
                                $jml_jam_lembur = $d[$tanggal_presensi]['jam_lembur_aktual'];
                                $jam_netto_harian = $d[$tanggal_presensi]['jam_lembur_netto'];
                                $total_nominal_lembur_snapshot += $d[$tanggal_presensi]['nominal_lembur'] ?? 0;
                            } else {
                                $lembur_aktual = hitungLembur($ceklembur_data);
                                if ($lembur_aktual > 0) {
                                    $jml_jam_lembur = $lembur_aktual;
                                    $jam_netto_harian = hitungJamNetto($lembur_aktual, $tipe_hari);
                                } else {
                                    $jml_jam_lembur = 0;
                                    $jam_netto_harian = 0;
                                }
                            }
                            $nama_hari = getHari($tanggal_presensi);

                            $libur_key = $d['nik'] . '|' . $tanggal_presensi;
                            $ceklibur = $datalibur_indexed[$libur_key] ?? ($datalibur_by_tanggal[$tanggal_presensi] ?? []);

                            if (isset($d[$tanggal_presensi])) {
                                if ($d[$tanggal_presensi]['status'] == 'h') {
                                    $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                                    $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);
                                    
                                    $denda_dari_db = isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                        ? $d[$tanggal_presensi]['denda']
                                        : null;

                                    if ($denda_dari_db !== null) {
                                        $denda = $denda_dari_db;
                                        if ($terlambat != null) {
                                            if ($terlambat['desimal_terlambat'] < 1) {
                                                $potongan_jam_terlambat = 0;
                                            } else {
                                                $potongan_jam_terlambat = $terlambat['desimal_terlambat'] > $d[$tanggal_presensi]['total_jam']
                                                    ? $d[$tanggal_presensi]['total_jam']
                                                    : $terlambat['desimal_terlambat'];
                                            }
                                        } else {
                                            $potongan_jam_terlambat = 0;
                                        }
                                    } else {
                                        if ($terlambat != null) {
                                            if ($terlambat['desimal_terlambat'] < 1) {
                                                $potongan_jam_terlambat = 0;
                                                $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                                            } else {
                                                $potongan_jam_terlambat = $terlambat['desimal_terlambat'] > $d[$tanggal_presensi]['total_jam']
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
                                        $d[$tanggal_presensi]['lintashari']
                                    );
                                    $pulangcepat = $pulangcepat > $d[$tanggal_presensi]['total_jam'] ? $d[$tanggal_presensi]['total_jam'] : $pulangcepat;
                                    
                                    $potongan_tidak_absen_masuk_atau_pulang = empty($d[$tanggal_presensi]['jam_out']) || empty($d[$tanggal_presensi]['jam_in'])
                                        ? $d[$tanggal_presensi]['total_jam']
                                        : 0;
                                    $potongan_istirahat = hitungPotonganIstirahat(
                                        $d[$tanggal_presensi]['istirahat_out'],
                                        $d[$tanggal_presensi]['istirahat_in'],
                                        $d[$tanggal_presensi]['jam_awal_istirahat'],
                                        $d[$tanggal_presensi]['jam_akhir_istirahat']
                                    );
                                    $potongan_istirahat = $potongan_istirahat > $d[$tanggal_presensi]['total_jam'] ? $d[$tanggal_presensi]['total_jam'] : $potongan_istirahat;

                                    $status_potongan_istirahat = isset($d[$tanggal_presensi]['status_potongan_istirahat']) && $d[$tanggal_presensi]['status_potongan_istirahat'] !== null
                                        ? $d[$tanggal_presensi]['status_potongan_istirahat']
                                        : $generalsetting->potongan_istirahat;

                                    $potongan_jam = $potongan_tidak_absen_masuk_atau_pulang == 0
                                        ? ($pulangcepat + $potongan_jam_terlambat + ($status_potongan_istirahat == 1 ? $potongan_istirahat : 0))
                                        : $potongan_tidak_absen_masuk_atau_pulang;

                                    $status_potongan_harian = isset($d[$tanggal_presensi]['status_potongan']) && $d[$tanggal_presensi]['status_potongan'] !== null
                                        ? $d[$tanggal_presensi]['status_potongan']
                                        : $generalsetting->status_potongan_jam;

                                    if ($status_potongan_harian == 0) {
                                        $potongan_jam = 0;
                                    }
                                } elseif ($d[$tanggal_presensi]['status'] == 'i') {
                                    $status_potongan_izin = isset($d[$tanggal_presensi]['status_potongan']) && $d[$tanggal_presensi]['status_potongan'] !== null
                                        ? $d[$tanggal_presensi]['status_potongan']
                                        : $generalsetting->status_potongan_jam;
                                    $potongan_jam = $status_potongan_izin == 1 ? $d[$tanggal_presensi]['total_jam'] : 0;
                                    $denda = $d[$tanggal_presensi]['denda'] ?? 0;
                                } elseif ($d[$tanggal_presensi]['status'] == 'a') {
                                    $status_potongan_alpa = isset($d[$tanggal_presensi]['status_potongan']) && $d[$tanggal_presensi]['status_potongan'] !== null
                                        ? $d[$tanggal_presensi]['status_potongan']
                                        : $generalsetting->status_potongan_jam;
                                    $potongan_jam = $status_potongan_alpa == 1 ? $d[$tanggal_presensi]['total_jam'] : 0;
                                    $denda = $d[$tanggal_presensi]['denda'] ?? 0;
                                }
                            } else {
                                $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                                if (empty($ceklibur)) {
                                    $totalJamJadwal = $mapJadwalByDate[$tanggal_presensi] ?? null;
                                    if ($totalJamJadwal === null) $totalJamJadwal = $mapJadwalGrupByDate[$tanggal_presensi] ?? null;
                                    if ($totalJamJadwal === null) $totalJamJadwal = $mapJadwalByDay[$nama_hari] ?? null;
                                    if ($totalJamJadwal === null) {
                                        $keyDC = $d['kode_dept'] . '|' . $d['kode_cabang'];
                                        $mapD = $jadwal_bydept[$keyDC] ?? [];
                                        $totalJamJadwal = $mapD[$nama_hari] ?? null;
                                    }
                                    if ($totalJamJadwal !== null && !$is_future) {
                                        $potongan_jam = $generalsetting->status_potongan_jam == 1
                                            ? (is_array($totalJamJadwal) ? $totalJamJadwal['total_jam'] : $totalJamJadwal)
                                            : 0;
                                    }
                                }
                            }

                            $total_denda += $denda;
                            $total_potongan_jam += $potongan_jam;
                            $total_jam_lembur_aktual += $jml_jam_lembur;
                            $total_jam_netto_lembur += $jam_netto_harian;
                            $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                        }

                        if ($generalsetting->status_potongan_jam == 0) {
                            $total_potongan_jam = 0;
                        } elseif ($total_potongan_jam > $generalsetting->total_jam_bulan) {
                            $total_potongan_jam = $generalsetting->total_jam_bulan;
                        }

                        $upah_perjam = $generalsetting->total_jam_bulan > 0 ? $d['gaji_pokok'] / $generalsetting->total_jam_bulan : 0;
                        $jumlah_potongan_jam = $total_potongan_jam * $upah_perjam;

                        if ($has_lembur_snapshot) {
                            $upah_lembur = $total_nominal_lembur_snapshot;
                        } else {
                            if ($lemburKhusus) {
                                $upah_lembur = $lemburKhusus->nominal_lembur * $total_jam_lembur_aktual;
                            } else {
                                $upah_lembur = $total_jam_netto_lembur * (1 / 173) * $d['gaji_pokok'];
                            }
                        }

                        $tunjangan_pajak = 0;
                        $potongan_pph21 = 0;

                        if ($isPphActive && $d['hitung_pph21']) {
                            $active_metode_tanggungan = $pphSetting->metode_tanggungan;
                            $bulanGaji = (int) date('m', strtotime($periode_dari));
                            $isDecember = ($bulanGaji === 12);
                            $penghasilanBrutoBulanIni = ($d['gaji_pokok'] + $total_tunjangan + $d['penambah'] + $upah_lembur) - ($jumlah_potongan_jam + $total_denda + $d['pengurang']);

                            if (!$isDecember) {
                                $pphResult = $pph21Service->hitungBulanan(
                                    $penghasilanBrutoBulanIni,
                                    $d['kode_status_kawin'],
                                    $active_metode_tanggungan,
                                    $d['nik'],
                                    $bulanGaji
                                );
                                $pph21_terutang = $pphResult['pph21_terutang'] ?? 0;
                                $pph21_ditanggung_perusahaan = $pphResult['pph21_ditanggung_perusahaan'] ?? 0;
                            } else {
                                $history = $janNovStatsAll[$d['nik']] ?? null;
                                if ($history && $history->bulan_kerja_count > 0) {
                                    $pphResult = $pph21Service->hitungDesember(
                                        $history->total_bruto_jan_nov,
                                        $history->total_pph21_jan_nov,
                                        $penghasilanBrutoBulanIni,
                                        $d['kode_status_kawin'],
                                        $active_metode_tanggungan,
                                        $history->bulan_kerja_count + 1,
                                        $d['nik']
                                    );
                                    $pph21_terutang = $pphResult['pph21_terutang_desember'] ?? 0;
                                    $pph21_ditanggung_perusahaan = $pphResult['pph21_ditanggung_perusahaan_desember'] ?? 0;
                                } else {
                                    $pphResult = $pph21Service->hitungBulanan(
                                        $penghasilanBrutoBulanIni,
                                        $d['kode_status_kawin'],
                                        $active_metode_tanggungan,
                                        $d['nik'],
                                        $bulanGaji
                                    );
                                    $pph21_terutang = $pphResult['pph21_terutang'] ?? 0;
                                    $pph21_ditanggung_perusahaan = $pphResult['pph21_ditanggung_perusahaan'] ?? 0;
                                }
                            }

                            if ($active_metode_tanggungan === 'GROSS_UP') {
                                $tunjangan_pajak = $pph21_terutang + $pph21_ditanggung_perusahaan;
                                $potongan_pph21 = $pph21_terutang + $pph21_ditanggung_perusahaan;
                            } else {
                                $tunjangan_pajak = 0;
                                $potongan_pph21 = $pph21_terutang;
                            }
                        }

                        $bruto = $d['gaji_pokok'] + $total_tunjangan + $tunjangan_pajak;
                        $total_potongan = ROUND($jumlah_potongan_jam) + $total_denda + $d['bpjs_kesehatan'] + $d['bpjs_tenagakerja'] + ($d['cicilan_pinjaman'] ?? 0) + $potongan_pph21;

                        $total_all_potongan += $total_potongan;
                        $total_upah_lembur += $upah_lembur;
                        $total_gaji_pokok += $d['gaji_pokok'];
                        $total_bpjs_kesehatan += $d['bpjs_kesehatan'];
                        $total_bpjs_tenagakerja += $d['bpjs_tenagakerja'];
                        $total_penambah += $d['penambah'];
                        $total_pengurang += $d['pengurang'];
                        $total_bruto += $bruto;
                        $total_all_denda += $total_denda;
                        $total_jumlah_potongan_jam += $jumlah_potongan_jam;
                        $total_pph21 += $potongan_pph21;
                        $total_tunjangan_pajak += $tunjangan_pajak;

                        $gaji_bersih = $d['gaji_pokok'] + $total_tunjangan + $tunjangan_pajak - $total_potongan + $d['penambah'] - $d['pengurang'] + $upah_lembur;
                        $total_gaji_bersih += $gaji_bersih;
                    @endphp

                    <tr>
                        <td class="sticky-col col-no">{{ $loop->iteration }}</td>
                        <td class="sticky-col col-nik">{{ $d['nik_show'] ?? $d['nik'] }}</td>
                        <td class="sticky-col col-nama">{{ $d['nama_karyawan'] }}</td>
                        <td>
                            {{ $d['no_rekening'] ?? '-' }} {{ !empty($d['nama_bank']) ? '(' . strtoupper($d['nama_bank']) . ')' : '' }}
                            @if(!empty($d['nama_rekening']))
                                <br><small style="color: var(--slate-400); font-style: italic;">{{ $d['nama_rekening'] }}</small>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $d['kode_status_kawin'] ?? '-' }}</td>
                        <td>{{ $d['nama_jabatan'] }}</td>
                        <td style="text-align: center;">{{ $d['kode_dept'] }}</td>
                        <td>{{ $d['kode_cabang'] }}</td>
                        <td style="text-align: right; font-weight: 700;">{{ formatAngka($d['gaji_pokok']) }}</td>
                        @foreach ($jenis_tunjangan as $j)
                            @php
                                ${'total_tunjangan_' . $j->kode_jenis_tunjangan} += $d[$j->kode_jenis_tunjangan];
                            @endphp
                            <td style="text-align: right;">{{ formatAngka($d[$j->kode_jenis_tunjangan]) }}</td>
                        @endforeach
                        @if ($isGrossUp)
                            <td style="text-align: right; color: #0d9488;">{{ formatAngka($tunjangan_pajak) }}</td>
                        @endif
                        <td style="text-align: right; font-weight: 800; background: #fff7ed; color: #c2410c;">
                            {{ formatAngka($bruto) }}
                        </td>
                        <td style="text-align: center;">{{ $generalsetting->total_jam_bulan }}</td>
                        <td style="text-align: right;">{{ formatAngka($upah_perjam) }}</td>
                        <td style="text-align: right; color: {{ $total_denda > 0 ? '#b91c1c' : 'inherit' }}; font-weight: 700;">{{ formatAngka($total_denda) }}</td>
                        <td style="text-align: center;">{{ formatAngkaDesimal($total_potongan_jam) }}</td>
                        <td style="text-align: right; color: {{ $jumlah_potongan_jam > 0 ? '#9333ea' : 'inherit' }}; font-weight: 700;">{{ formatAngka($jumlah_potongan_jam) }}</td>
                        <td style="text-align: right;">{{ formatAngka($d['bpjs_kesehatan']) }}</td>
                        <td style="text-align: right;">{{ formatAngka($d['bpjs_tenagakerja']) }}</td>
                        <td style="text-align: right; color: {{ ($d['cicilan_pinjaman'] ?? 0) > 0 ? '#b91c1c' : 'inherit' }};">{{ formatAngka($d['cicilan_pinjaman'] ?? 0) }}</td>
                        @if ($isPphActive)
                            <td style="text-align: right; color: #b91c1c;">{{ formatAngka($potongan_pph21) }}</td>
                        @endif
                        <td style="text-align: right; font-weight: 800; background: #fef2f2; color: #b91c1c;">{{ formatAngka($total_potongan) }}</td>
                        <td style="text-align: center;">
                            <a href="{{ route('laporan.lemburdetail', [$d['nik'], $periode_dari, $periode_sampai]) }}" target="_blank"
                               style="color: #0d9488; font-weight: 700; text-decoration: underline;">
                                @if ($lemburKhusus)
                                    {{ formatAngkaDesimal($total_jam_lembur_aktual) }} <span style="font-size: 9px; color: #ea580c;">★</span>
                                @else
                                    {{ formatAngkaDesimal($total_jam_netto_lembur) }}
                                @endif
                            </a>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #0d9488;">{{ formatAngka($upah_lembur) }}</td>
                        <td style="text-align: right; color: #0284c7;">{{ formatAngka($d['penambah']) }}</td>
                        <td style="text-align: right; color: #b91c1c;">{{ formatAngka($d['pengurang']) }}</td>
                        <td style="text-align: right; font-weight: 800; background: var(--primary-light); color: var(--primary-dark); font-size: 10.5px;">{{ formatAngka($gaji_bersih) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8" style="text-align: center;">TOTAL KESELURUHAN</th>
                    <th style="text-align: right;">{{ formatAngka($total_gaji_pokok) }}</th>
                    @foreach ($jenis_tunjangan as $d_tj)
                        <th style="text-align: right;">
                            {{ formatAngka(${'total_tunjangan_' . $d_tj->kode_jenis_tunjangan}) }}
                        </th>
                    @endforeach
                    @if ($isGrossUp)
                        <th style="text-align: right;">{{ formatAngka($total_tunjangan_pajak) }}</th>
                    @endif
                    <th style="text-align: right; color: #fed7aa;">{{ formatAngka($total_bruto) }}</th>
                    <th colspan="2"></th>
                    <th style="text-align: right;">{{ formatAngka($total_all_denda) }}</th>
                    <th></th>
                    <th style="text-align: right;">{{ formatAngka($total_jumlah_potongan_jam) }}</th>
                    <th style="text-align: right;">{{ formatAngka($total_bpjs_kesehatan) }}</th>
                    <th style="text-align: right;">{{ formatAngka($total_bpjs_tenagakerja) }}</th>
                    <th style="text-align: right;">{{ formatAngka($laporan_presensi->sum('cicilan_pinjaman')) }}</th>
                    @if ($isPphActive)
                        <th style="text-align: right;">{{ formatAngka($total_pph21) }}</th>
                    @endif
                    <th style="text-align: right; color: #fca5a5;">{{ formatAngka($total_all_potongan) }}</th>
                    <th></th>
                    <th style="text-align: right; color: #99f6e4;">{{ formatAngka($total_upah_lembur) }}</th>
                    <th style="text-align: right;">{{ formatAngka($total_penambah) }}</th>
                    <th style="text-align: right;">{{ formatAngka($total_pengurang) }}</th>
                    <th style="text-align: right; color: #6ee7b7; font-size: 11px;">{{ formatAngka($total_gaji_bersih) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

</body>
</html>
