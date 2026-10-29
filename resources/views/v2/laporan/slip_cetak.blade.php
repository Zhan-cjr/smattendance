@inject('pph21Service', 'App\Services\Pph21Service')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Slip Gaji - {{ date('d-m-Y') }}</title>
    
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
            size: A4 portrait;
            margin: 6mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background: #f1f5f9;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--slate-800);
            margin: 0;
            padding: 16px;
            font-size: 10px;
            line-height: 1.35;
        }

        /* Top Action Bar (Screen Only) */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            padding: 12px 18px;
            border-radius: 12px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin: 0 auto 16px auto;
            max-width: 1200px;
        }

        .top-bar-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-bar-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--slate-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge-total {
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid rgba(10, 92, 65, 0.15);
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .layout-selector {
            display: flex;
            align-items: center;
            background: var(--slate-100);
            padding: 3px;
            border-radius: 8px;
            gap: 2px;
        }

        .layout-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            background: transparent;
            color: var(--slate-600);
            cursor: pointer;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .layout-btn.active {
            background: #ffffff;
            color: var(--slate-900);
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
            font-family: inherit;
        }

        .btn-print {
            background: var(--slate-900);
            color: #ffffff;
        }

        .btn-print:hover {
            background: var(--slate-800);
        }

        /* Container Layout */
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: flex-start;
            margin: 0 auto;
            max-width: 1200px;
        }

        /* Slip Card Structure */
        .slip-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
            padding: 14px 16px;
            box-sizing: border-box;
            page-break-inside: avoid;
            break-inside: avoid;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* 2 Slips per page (Default: 2 Kolom) */
        .layout-2col .slip-card {
            width: calc(50% - 6px);
        }

        /* 4 Slips per page (2x2 Grid Compact) */
        .layout-4col .slip-card {
            width: calc(50% - 6px);
            padding: 10px 12px;
            font-size: 9px;
        }
        .layout-4col .slip-card .company-name { font-size: 11px; }
        .layout-4col .slip-card .slip-title { font-size: 11px; }
        .layout-4col .slip-card .meta-table td { padding: 2px 4px; font-size: 8.5px; }
        .layout-4col .slip-card .table-data td { padding: 2.5px 6px; font-size: 8.5px; }
        .layout-4col .slip-card .thp-box { padding: 6px 10px; margin-bottom: 8px; }
        .layout-4col .slip-card .thp-value { font-size: 12px; }

        /* 1 Slip per page */
        .layout-1col .slip-card {
            width: 100%;
            max-width: 600px;
            margin: 0 auto 20px auto;
            padding: 24px;
        }

        /* Header */
        .slip-header {
            border-bottom: 1.5px solid var(--primary);
            padding-bottom: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .company-block {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .company-logo {
            max-height: 32px;
            max-width: 65px;
            object-fit: contain;
        }

        .company-name {
            font-size: 12px;
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: 0.2px;
            text-transform: uppercase;
        }

        .company-address {
            font-size: 8.5px;
            color: var(--slate-500);
            margin-top: 1px;
        }

        .title-block {
            text-align: right;
        }

        .slip-title {
            font-size: 12px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .slip-periode {
            font-size: 8.5px;
            color: var(--slate-600);
            font-weight: 600;
            margin-top: 2px;
            background: var(--slate-100);
            padding: 1.5px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Employee Meta Table */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            background: var(--slate-50);
            border-radius: 6px;
            border: 1px solid var(--slate-200);
        }

        .meta-table td {
            padding: 3px 6px;
            font-size: 9.5px;
            vertical-align: top;
        }

        .meta-table .label {
            color: var(--slate-500);
            width: 68px;
            font-weight: 500;
        }

        .meta-table .colon {
            width: 6px;
            text-align: center;
            color: var(--slate-400);
        }

        .meta-table .value {
            font-weight: 700;
            color: var(--slate-900);
        }

        /* Work Rate Summary Bar */
        .work-summary-bar {
            background: var(--primary-light);
            border: 1px solid rgba(10, 92, 65, 0.15);
            border-radius: 6px;
            padding: 4px 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 8.5px;
            color: var(--primary-dark);
            font-weight: 500;
        }

        .work-summary-bar span {
            font-weight: 700;
            color: var(--primary-dark);
        }

        /* Financial Section */
        .financial-section {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 10px;
        }

        .section-box {
            border: 1px solid var(--slate-200);
            border-radius: 6px;
            overflow: hidden;
        }

        .section-title {
            background: var(--slate-100);
            padding: 3.5px 8px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid var(--slate-200);
            display: flex;
            justify-content: space-between;
        }

        .section-title.earning {
            color: #047857;
            background: #f0fdf4;
            border-left: 3px solid #10b981;
        }

        .section-title.deduction {
            color: #b91c1c;
            background: #fef2f2;
            border-left: 3px solid #ef4444;
        }

        .section-title.adjustment {
            color: #0369a1;
            background: #f0f9ff;
            border-left: 3px solid #0ea5e9;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
        }

        .table-data td {
            padding: 3px 8px;
            font-size: 9.5px;
        }

        .table-data tr:not(:last-child) td {
            border-bottom: 1px dashed var(--slate-100);
        }

        .table-data .item-name {
            color: var(--slate-700);
            text-align: left;
        }

        .table-data .item-amount {
            font-weight: 600;
            color: var(--slate-900);
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .subtotal-row {
            background: var(--slate-50);
            border-top: 1px solid var(--slate-200) !important;
            font-weight: 700;
        }

        .subtotal-row td {
            padding: 4px 8px !important;
            font-size: 9.5px !important;
            color: var(--slate-900) !important;
        }

        /* Take Home Pay Highlight Box */
        .thp-box {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 6px;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            color: #ffffff;
            box-shadow: 0 2px 6px -1px rgba(10, 92, 65, 0.2);
        }

        .thp-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: rgba(255, 255, 255, 0.95);
        }

        .thp-value {
            font-size: 13.5px;
            font-weight: 800;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
        }

        /* Formal Signatures & Footer */
        .slip-footer {
            margin-top: auto;
            border-top: 1px solid var(--slate-200);
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .footer-note {
            font-size: 8px;
            color: var(--slate-400);
            line-height: 1.3;
        }

        .signature-box {
            text-align: center;
            width: 110px;
        }

        .signature-role {
            font-size: 8.5px;
            font-weight: 600;
            color: var(--slate-600);
            margin-bottom: 22px;
        }

        .signature-line {
            border-bottom: 1px solid var(--slate-400);
            margin-bottom: 2px;
        }

        .signature-name {
            font-size: 8px;
            font-weight: 600;
            color: var(--slate-700);
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .top-bar {
                display: none !important;
            }

            .container {
                gap: 10px !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .layout-2col .slip-card {
                width: 48.5% !important;
                border: 1px solid var(--slate-300) !important;
                box-shadow: none !important;
                margin-bottom: 10px !important;
                padding: 12px 14px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .layout-4col .slip-card {
                width: 48.5% !important;
                border: 1px solid var(--slate-300) !important;
                box-shadow: none !important;
                margin-bottom: 8px !important;
                padding: 8px 10px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .layout-1col .slip-card {
                width: 100% !important;
                border: 1px solid var(--slate-300) !important;
                box-shadow: none !important;
                page-break-after: always !important;
                break-after: page !important;
            }

            .thp-box {
                background: #f8fafc !important;
                border: 1.5px solid var(--slate-900) !important;
                color: var(--slate-900) !important;
                box-shadow: none !important;
            }

            .thp-label, .thp-value {
                color: var(--slate-900) !important;
            }
        }

        @media (max-width: 768px) {
            .slip-card {
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    @php
        $isPphActive = $pph21Service->isAktif();
        $pphSetting = $isPphActive ? $pph21Service->getSetting() : null;
        
        $bulanNum = $bulan ?? request('bulan') ?? date('m', strtotime($periode_dari));
        $tahunNum = $tahun ?? request('tahun') ?? date('Y', strtotime($periode_dari));
        $slipGajiRecord = DB::table('slip_gaji')
            ->where('bulan', $bulanNum)
            ->where('tahun', $tahunNum)
            ->first();
        $kodeSlipGaji = $slipGajiRecord ? $slipGajiRecord->kode_slip_gaji : null;
        $isPublished = $slipGajiRecord && $slipGajiRecord->status == 1;
        $janNovStats = $janNovStatsAll ?? collect();
    @endphp

    <!-- Top Action Bar (Screen Only) -->
    <div class="top-bar">
        <div class="top-bar-info">
            <div class="top-bar-title">
                <i class="ti ti-receipt text-primary"></i>
                Slip Gaji Karyawan
            </div>
            <span class="badge-total">{{ count($laporan_presensi) }} Karyawan</span>
        </div>
        <div class="top-bar-actions">
            <!-- Layout Switcher: 2 Slips / 4 Slips / 1 Slip per page -->
            <div class="layout-selector">
                <button type="button" class="layout-btn active" onclick="setLayout('2col', this)" title="2 Slip per lembar (Berdampingan)">
                    <i class="ti ti-layout-grid"></i> 2 Slip / Lembar
                </button>
                <button type="button" class="layout-btn" onclick="setLayout('4col', this)" title="4 Slip per lembar (Kompak)">
                    <i class="ti ti-layout-grid-add"></i> 4 Slip (Kompak)
                </button>
                <button type="button" class="layout-btn" onclick="setLayout('1col', this)" title="1 Slip per lembar">
                    <i class="ti ti-layout-rows"></i> 1 Slip / Lembar
                </button>
            </div>

            <button onclick="window.print()" class="btn-action btn-print">
                <i class="ti ti-printer"></i>
                Cetak Slip
            </button>
        </div>
    </div>

    <!-- Container untuk layout -->
    <div id="slip-container" class="container layout-2col">
        @foreach ($laporan_presensi as $d)
            @php
                $tanggal_presensi = $periode_dari;
                $total_denda = 0;
                $total_potongan_jam = 0;
                $total_tunjangan = 0;
                $total_jam_lembur_aktual = 0;
                $total_jam_netto_lembur = 0;
                $total_nominal_lembur_snapshot = 0;
                $has_lembur_snapshot = false;
                $lemburKhusus = $lembur_khusus_map[$d['nik']] ?? null;

                $mapJadwalByDate = $jadwal_bydate[$d['nik']] ?? [];
                $mapJadwalGrupByDate = $jadwal_grup_bydate[$d['nik']] ?? [];
                $mapJadwalByDay = $jadwal_byday[$d['nik']] ?? [];

                foreach ($jenis_tunjangan as $j) {
                    $total_tunjangan += $d[$j->kode_jenis_tunjangan];
                }

                $upah_perjam = $d['gaji_pokok'] / $generalsetting->total_jam_bulan;
            @endphp

            @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                @php
                    $denda = 0;
                    $potongan_jam = 0;

                    $is_libur_nasional = isset($libur_nasional_dates[$tanggal_presensi]);
                    if ($is_libur_nasional) {
                        $is_libur = true;
                    } else {
                        $has_schedule = false;
                        $nama_hari_check = getHari($tanggal_presensi);
                        if (isset($mapJadwalByDate[$tanggal_presensi])) $has_schedule = true;
                        elseif (isset($mapJadwalGrupByDate[$tanggal_presensi])) $has_schedule = true;
                        elseif (isset($mapJadwalByDay[$nama_hari_check])) $has_schedule = true;
                        else {
                            $keyDC = $d['kode_dept'] . '|' . $d['kode_cabang'];
                            $mapD = $jadwal_bydept[$keyDC] ?? [];
                            if (isset($mapD[$nama_hari_check])) $has_schedule = true;
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

                    $libur_key = $d['nik'] . '|' . $tanggal_presensi;
                    $ceklibur = $datalibur_indexed[$libur_key] ?? ($datalibur_by_tanggal[$tanggal_presensi] ?? []);
                @endphp

                @if (isset($d[$tanggal_presensi]))
                    @if ($d[$tanggal_presensi]['status'] == 'h')
                        @php
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
                                        $potongan_jam_terlambat =
                                            $terlambat['desimal_terlambat'] > $d[$tanggal_presensi]['total_jam']
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
                            $pulangcepat = $pulangcepat > $d[$tanggal_presensi]['total_jam'] ? $d[$tanggal_presensi]['total_jam'] : $pulangcepat;
                            $potongan_tidak_absen_masuk_atau_pulang =
                                empty($d[$tanggal_presensi]['jam_out']) || empty($d[$tanggal_presensi]['jam_in'])
                                    ? $d[$tanggal_presensi]['total_jam']
                                    : 0;
                            $potongan_istirahat = hitungPotonganIstirahat(
                                $d[$tanggal_presensi]['istirahat_out'],
                                $d[$tanggal_presensi]['istirahat_in'],
                                $d[$tanggal_presensi]['jam_awal_istirahat'],
                                $d[$tanggal_presensi]['jam_akhir_istirahat']
                            );
                            $status_potongan_istirahat = $d[$tanggal_presensi]['status_potongan_istirahat'] ?? $generalsetting->potongan_istirahat;
                            $potongan_jam =
                                $potongan_tidak_absen_masuk_atau_pulang == 0
                                    ? $pulangcepat + $potongan_jam_terlambat + ($status_potongan_istirahat == 1 ? $potongan_istirahat : 0)
                                    : $potongan_tidak_absen_masuk_atau_pulang;
                        @endphp
                    @elseif($d[$tanggal_presensi]['status'] == 'i')
                        @php
                            $potongan_jam = $d[$tanggal_presensi]['total_jam'];
                            $denda_dari_db = isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null ? $d[$tanggal_presensi]['denda'] : null;
                            $denda = $denda_dari_db !== null ? $denda_dari_db : 0;
                        @endphp
                    @elseif($d[$tanggal_presensi]['status'] == 'a')
                        @php
                            $potongan_jam = $d[$tanggal_presensi]['total_jam'];
                            $denda_dari_db = isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null ? $d[$tanggal_presensi]['denda'] : null;
                            $denda = $denda_dari_db !== null ? $denda_dari_db : 0;
                        @endphp
                    @endif
                @else
                    @php
                        if (empty($ceklibur)) {
                            $totalJamJadwal = $mapJadwalByDate[$tanggal_presensi] ?? null;
                            if ($totalJamJadwal === null) $totalJamJadwal = $mapJadwalGrupByDate[$tanggal_presensi] ?? null;
                            if ($totalJamJadwal === null) {
                                $nama_hari = getHari($tanggal_presensi);
                                $totalJamJadwal = $mapJadwalByDay[$nama_hari] ?? null;
                            }
                            if ($totalJamJadwal === null) {
                                $nama_hari = isset($nama_hari) ? $nama_hari : getHari($tanggal_presensi);
                                $keyDeptCabang = $d['kode_dept'] . '|' . $d['kode_cabang'];
                                $mapDept = $jadwal_bydept[$keyDeptCabang] ?? [];
                                $totalJamJadwal = $mapDept[$nama_hari] ?? null;
                            }

                            $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                            if ($totalJamJadwal !== null && !$is_future) {
                                $potongan_jam = is_array($totalJamJadwal) ? $totalJamJadwal['total_jam'] : $totalJamJadwal;
                            }
                        }
                    @endphp
                @endif

                @php
                    $status_potongan_harian = isset($d[$tanggal_presensi]['status_potongan']) ? $d[$tanggal_presensi]['status_potongan'] : $generalsetting->status_potongan_jam;
                    if ($status_potongan_harian == 0) $potongan_jam = 0;
                    $total_denda += $denda;
                    $total_potongan_jam += $potongan_jam;
                    $total_jam_lembur_aktual += $jml_jam_lembur;
                    $total_jam_netto_lembur += $jam_netto_harian;
                    $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                @endphp
            @endwhile

            @php
                if ($total_potongan_jam > $generalsetting->total_jam_bulan) {
                    $total_potongan_jam = $generalsetting->total_jam_bulan;
                }
                $jumlah_potongan_jam = ROUND($upah_perjam) * $total_potongan_jam;
                
                if ($has_lembur_snapshot) {
                    $upah_lembur = $total_nominal_lembur_snapshot;
                } elseif ($lemburKhusus) {
                    $upah_lembur = $lemburKhusus->upah_perjam * $total_jam_lembur_aktual;
                } else {
                    $upah_perjam_lembur = ($d['gaji_pokok'] + $total_tunjangan) / ($generalsetting->total_jam_bulan ?: 173);
                    $upah_lembur = ROUND($upah_perjam_lembur) * $total_jam_netto_lembur;
                }

                // PPh 21 Calculation
                $pph21_terutang = 0;
                $pph21_ditanggung_perusahaan = 0;
                $tunjangan_pajak = 0;
                $potongan_pph21 = 0;
                $active_metode_tanggungan = 'GROSS';

                if ($isPphActive) {
                    $snapshot = null;
                    if ($kodeSlipGaji) {
                        $snapshot = $pph21Service->getSnapshot($kodeSlipGaji, $d['nik']);
                    }

                    if ($snapshot) {
                        $pph21_terutang = $snapshot->pph21_terutang;
                        $pph21_ditanggung_perusahaan = $snapshot->pph21_ditanggung_perusahaan;
                        $active_metode_tanggungan = $snapshot->metode_tanggungan;
                    } else {
                        $hitungPph = (($d['hitung_pph21'] ?? 1) == 1);
                        $pphSetting = $pph21Service->getSetting();
                        $active_metode_tanggungan = $pphSetting->metode_tanggungan;

                        $pphResult = [
                            'status_aktif' => true,
                            'metode' => $pphSetting->metode,
                            'metode_tanggungan' => $active_metode_tanggungan,
                            'bruto' => 0,
                            'detail_komponen' => [],
                            'kategori_ter' => 'A',
                            'tarif_ter_persen' => 0,
                            'biaya_jabatan' => 0,
                            'ptkp' => 0,
                            'pkp_setahun' => 0,
                            'pph21_terutang' => 0,
                            'pph21_ditanggung_perusahaan' => 0,
                            'bulan' => (int)$bulanNum
                        ];

                        if ($hitungPph) {
                            $tunjanganMap = [];
                            foreach ($jenis_tunjangan as $j) {
                                $tunjanganMap[$j->kode_jenis_tunjangan] = $d[$j->kode_jenis_tunjangan] ?? 0;
                            }
                            $nilaiKomponen = [
                                'gaji_pokok' => $d['gaji_pokok'],
                                'bpjs_kesehatan' => $d['bpjs_kesehatan'],
                                'bpjs_tenagakerja' => $d['bpjs_tenagakerja'],
                                'lembur' => $upah_lembur,
                                'tunjangan' => $tunjanganMap,
                            ];

                            $totalPphJanNov = 0;
                            $totalBrutoJanNov = 0;
                            if ($bulanNum == 12 && isset($janNovStats[$d['nik']])) {
                                $totalPphJanNov = $janNovStats[$d['nik']]->total_pph ?? 0;
                                $totalBrutoJanNov = $janNovStats[$d['nik']]->total_bruto ?? 0;
                            }

                            $pphResult = $pph21Service->hitung(
                                $nilaiKomponen,
                                $d['kode_status_kawin'] ?? null,
                                (int)$bulanNum,
                                (int)$totalPphJanNov,
                                (float)$totalBrutoJanNov
                            );

                            $pph21_terutang = $pphResult['pph21_terutang'] ?? 0;
                            $pph21_ditanggung_perusahaan = $pphResult['pph21_ditanggung_perusahaan'] ?? 0;
                        }

                        if ($isPublished && $kodeSlipGaji) {
                            $pph21Service->simpanSnapshot($kodeSlipGaji, $d['nik'], $d['kode_status_kawin'] ?? null, $pphResult);
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

                $total_potongan = ROUND($jumlah_potongan_jam) + $total_denda + $d['bpjs_kesehatan'] + $d['bpjs_tenagakerja'] + ($d['cicilan_pinjaman'] ?? 0) + $potongan_pph21;
                $bruto_total = $d['gaji_pokok'] + $total_tunjangan + $tunjangan_pajak + ROUND($upah_lembur);
                $gaji_bersih = $d['gaji_pokok'] + $total_tunjangan + $tunjangan_pajak - $total_potongan + $d['penambah'] - $d['pengurang'] + ROUND($upah_lembur);
            @endphp

            <div class="slip-card">
                <!-- Header -->
                <div class="slip-header">
                    <div class="company-block">
                        @if (!empty($generalsetting->logo) && Storage::disk('public')->exists('/logo/' . $generalsetting->logo))
                            <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo" class="company-logo">
                        @elseif (!empty($generalsetting->logo) && file_exists(public_path('storage/logo/' . $generalsetting->logo)))
                            <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo" class="company-logo">
                        @endif
                        <div>
                            <div class="company-name">{{ $generalsetting->nama_perusahaan }}</div>
                            <div class="company-address">{{ $generalsetting->alamat ?? 'Payroll Department' }}</div>
                        </div>
                    </div>
                    <div class="title-block">
                        <div class="slip-title">SLIP GAJI</div>
                        <div class="slip-periode">{{ date('d/m/Y', strtotime($periode_dari)) }} - {{ date('d/m/Y', strtotime($periode_sampai)) }}</div>
                    </div>
                </div>

                <!-- Meta Table -->
                <table class="meta-table">
                    <tr>
                        <td class="label">NIK</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $d['nik_show'] ?? $d['nik'] }}</td>
                        <td class="label" style="padding-left: 8px;">Jabatan</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $d['nama_jabatan'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $d['nama_karyawan'] }}</td>
                        <td class="label" style="padding-left: 8px;">Departemen</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $d['nama_dept'] ?? ($d['kode_dept'] ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Rekening</td>
                        <td class="colon">:</td>
                        <td class="value" colspan="4">
                            {{ $d['no_rekening'] ?? '-' }} 
                            @if(!empty($d['nama_bank']))
                                ({{ strtoupper($d['nama_bank']) }})
                            @endif
                        </td>
                    </tr>
                </table>

                @if ($generalsetting->show_rate_slip ?? true)
                <div class="work-summary-bar">
                    <div>Jadwal: <span>{{ $generalsetting->total_jam_bulan }} Jam</span></div>
                    <div>Upah/Jam: <span>Rp {{ number_format($upah_perjam, 0, ',', '.') }}</span></div>
                    <div>Pot. Jam: <span>{{ number_format($total_potongan_jam, 2) }} Jam</span></div>
                </div>
                @endif

                <!-- Financial Sections -->
                <div class="financial-section">
                    <!-- 1. Penghasilan -->
                    <div class="section-box">
                        <div class="section-title earning">
                            <span>A. PENGHASILAN</span>
                            <span>JUMLAH (RP)</span>
                        </div>
                        <table class="table-data">
                            <tr>
                                <td class="item-name">Gaji Pokok</td>
                                <td class="item-amount">{{ number_format($d['gaji_pokok'], 0, ',', '.') }}</td>
                            </tr>
                            @foreach ($jenis_tunjangan as $j)
                                @if ($d[$j->kode_jenis_tunjangan] > 0)
                                    <tr>
                                        <td class="item-name">{{ $j->jenis_tunjangan }}</td>
                                        <td class="item-amount">{{ number_format($d[$j->kode_jenis_tunjangan], 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            @if ($isPphActive && $active_metode_tanggungan === 'GROSS_UP' && $tunjangan_pajak > 0)
                                <tr>
                                    <td class="item-name">Tunjangan PPh 21</td>
                                    <td class="item-amount">{{ number_format($tunjangan_pajak, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($total_jam_netto_lembur > 0 || $total_jam_lembur_aktual > 0)
                                <tr>
                                    <td class="item-name">
                                        Upah Lembur 
                                        @if ($lemburKhusus)
                                            ({{ formatAngkaDesimal($total_jam_lembur_aktual) }} jam)
                                        @else
                                            ({{ formatAngkaDesimal($total_jam_netto_lembur) }} jam)
                                        @endif
                                    </td>
                                    <td class="item-amount">{{ number_format($upah_lembur, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr class="subtotal-row">
                                <td class="item-name">Total Penghasilan Bruto</td>
                                <td class="item-amount">Rp {{ number_format($bruto_total, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- 2. Potongan -->
                    <div class="section-box">
                        <div class="section-title deduction">
                            <span>B. POTONGAN</span>
                            <span>JUMLAH (RP)</span>
                        </div>
                        <table class="table-data">
                            @if ($total_denda > 0)
                                <tr>
                                    <td class="item-name">Denda Keterlambatan</td>
                                    <td class="item-amount">{{ number_format($total_denda, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($jumlah_potongan_jam > 0)
                                <tr>
                                    <td class="item-name">Potongan Jam Kerja ({{ number_format($total_potongan_jam, 2) }} jam)</td>
                                    <td class="item-amount">{{ number_format($jumlah_potongan_jam, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($d['bpjs_kesehatan'] > 0)
                                <tr>
                                    <td class="item-name">Iuran BPJS Kesehatan</td>
                                    <td class="item-amount">{{ number_format($d['bpjs_kesehatan'], 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($d['bpjs_tenagakerja'] > 0)
                                <tr>
                                    <td class="item-name">Iuran BPJS Ketenagakerjaan</td>
                                    <td class="item-amount">{{ number_format($d['bpjs_tenagakerja'], 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if (($d['cicilan_pinjaman'] ?? 0) > 0)
                                <tr>
                                    <td class="item-name">Cicilan Pinjaman</td>
                                    <td class="item-amount">{{ number_format($d['cicilan_pinjaman'], 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($isPphActive && $potongan_pph21 > 0)
                                <tr>
                                    <td class="item-name">Potongan PPh 21</td>
                                    <td class="item-amount">{{ number_format($potongan_pph21, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($total_potongan == 0)
                                <tr>
                                    <td class="item-name" style="color: var(--slate-400); font-style: italic;">Tidak ada potongan</td>
                                    <td class="item-amount">0</td>
                                </tr>
                            @endif
                            <tr class="subtotal-row">
                                <td class="item-name">Total Potongan</td>
                                <td class="item-amount">Rp {{ number_format($total_potongan, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- 3. Penyesuaian -->
                    @if ($d['penambah'] > 0 || $d['pengurang'] > 0)
                        <div class="section-box">
                            <div class="section-title adjustment">
                                <span>C. PENYESUAIAN</span>
                                <span>JUMLAH (RP)</span>
                            </div>
                            <table class="table-data">
                                @if ($d['penambah'] > 0)
                                    <tr>
                                        <td class="item-name">(+) Penambah Lain</td>
                                        <td class="item-amount">{{ number_format($d['penambah'], 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                @if ($d['pengurang'] > 0)
                                    <tr>
                                        <td class="item-name">(-) Pengurang Lain</td>
                                        <td class="item-amount">{{ number_format($d['pengurang'], 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                @if(!empty($d['keterangan_penyesuaian']))
                                    <tr>
                                        <td colspan="2" style="font-size: 8px; color: var(--slate-500); padding-top: 2px;">
                                            <em>Ket: {{ $d['keterangan_penyesuaian'] }}</em>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Total Take Home Pay -->
                <div class="thp-box">
                    <span class="thp-label">Gaji Bersih (THP)</span>
                    <span class="thp-value">Rp {{ number_format($gaji_bersih, 0, ',', '.') }}</span>
                </div>

                <!-- Formal Signatures & Footer -->
                <div class="slip-footer">
                    <div class="footer-note">
                        Dicetak: {{ date('d/m/Y H:i') }}<br>
                        <em>Dokumen payroll resmi.</em>
                    </div>
                    <div class="signature-box">
                        <div class="signature-role">Bagian Keuangan / HRD</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ $generalsetting->nama_perusahaan }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function setLayout(layoutType, btnElement) {
            const container = document.getElementById('slip-container');
            container.className = 'container layout-' + layoutType;
            
            document.querySelectorAll('.layout-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            if (btnElement) {
                btnElement.classList.add('active');
            }
        }
    </script>
</body>

</html>
