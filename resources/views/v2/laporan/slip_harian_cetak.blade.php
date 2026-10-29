<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Slip Gaji Harian - {{ date('d-m-Y') }}</title>
    
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

        /* Top Action Bar */
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

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: flex-start;
            margin: 0 auto;
            max-width: 1200px;
        }

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

        /* 3 Slips / 2 Slips / 4 Slips layout */
        .layout-3col .slip-card {
            width: calc(33.333% - 8px);
        }

        .layout-2col .slip-card {
            width: calc(50% - 6px);
        }

        .layout-4col .slip-card {
            width: calc(50% - 6px);
            padding: 10px 12px;
        }

        /* Header */
        .slip-header {
            border-bottom: 1.5px solid var(--primary);
            padding-bottom: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company-info {
            text-align: left;
        }

        .company-name {
            font-size: 11.5px;
            font-weight: 800;
            color: var(--slate-900);
            text-transform: uppercase;
        }

        .company-sub {
            font-size: 8.5px;
            color: var(--slate-500);
            font-weight: 500;
        }

        .slip-title-block {
            text-align: right;
        }

        .slip-title {
            font-size: 11.5px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .slip-periode {
            font-size: 8px;
            color: var(--slate-600);
            font-weight: 600;
            margin-top: 2px;
            background: var(--slate-100);
            padding: 1.5px 5px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Employee Info */
        .employee-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 5px;
            margin-bottom: 10px;
            padding: 6px 8px;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 6px;
        }

        .employee-item {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .employee-label {
            font-size: 7.5px;
            font-weight: 600;
            color: var(--slate-500);
            text-transform: uppercase;
        }

        .employee-value {
            font-size: 9.5px;
            font-weight: 700;
            color: var(--slate-900);
        }

        .work-summary-badge {
            grid-column: span 2;
            border-top: 1px dashed var(--slate-200);
            padding-top: 4px;
            margin-top: 2px;
            font-size: 8.5px;
            color: var(--primary-dark);
            font-weight: 600;
        }

        /* Financial rows */
        .table-section {
            margin-bottom: 8px;
            border: 1px solid var(--slate-200);
            border-radius: 6px;
            overflow: hidden;
        }

        .section-header {
            font-size: 9px;
            font-weight: 700;
            padding: 3px 6px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid var(--slate-200);
        }

        .section-header.earning {
            color: #047857;
            background: #f0fdf4;
            border-left: 3px solid #10b981;
        }

        .section-header.deduction {
            color: #b91c1c;
            background: #fef2f2;
            border-left: 3px solid #ef4444;
        }

        .slip-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 6px;
            font-size: 9px;
        }

        .slip-row:not(:last-child) {
            border-bottom: 1px dashed var(--slate-100);
        }

        .slip-row.subtotal {
            font-weight: 700;
            background: var(--slate-50);
            border-top: 1px solid var(--slate-200);
            color: var(--slate-900);
        }

        .slip-row-label {
            color: var(--slate-700);
        }

        .slip-row-value {
            font-weight: 600;
            color: var(--slate-900);
            font-variant-numeric: tabular-nums;
        }

        /* Net Salary */
        .net-salary-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 6px;
            padding: 7px 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 8px 0;
            color: #ffffff;
            box-shadow: 0 2px 6px -1px rgba(10, 92, 65, 0.2);
        }

        .net-salary-label {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.95);
        }

        .net-salary-value {
            font-size: 12.5px;
            font-weight: 800;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
        }

        /* Footer */
        .slip-footer {
            border-top: 1px solid var(--slate-200);
            padding-top: 6px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
        }

        .system-info {
            font-size: 7.5px;
            color: var(--slate-400);
            line-height: 1.3;
        }

        .signature-block {
            text-align: center;
            min-width: 90px;
        }

        .signature-title {
            font-size: 7.5px;
            font-weight: 600;
            color: var(--slate-600);
            margin-bottom: 20px;
        }

        .signature-line {
            border-bottom: 1px solid var(--slate-400);
            width: 100%;
            margin-bottom: 2px;
        }

        .signature-subtitle {
            font-size: 7px;
            color: var(--slate-600);
            font-weight: 600;
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
                gap: 8px !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
            }

            .layout-3col .slip-card {
                width: 32% !important;
                border: 1px solid var(--slate-300) !important;
                box-shadow: none !important;
                margin-bottom: 8px !important;
                padding: 10px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .layout-2col .slip-card {
                width: 48.5% !important;
                border: 1px solid var(--slate-300) !important;
                box-shadow: none !important;
                margin-bottom: 10px !important;
                padding: 12px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .layout-4col .slip-card {
                width: 48.5% !important;
                border: 1px solid var(--slate-300) !important;
                box-shadow: none !important;
                margin-bottom: 6px !important;
                padding: 8px 10px !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .net-salary-card {
                background: #f8fafc !important;
                border: 1.5px solid var(--slate-900) !important;
                color: var(--slate-900) !important;
                box-shadow: none !important;
            }

            .net-salary-label, .net-salary-value {
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
    <!-- Top Action Bar -->
    <div class="top-bar">
        <div class="top-bar-info">
            <div class="top-bar-title">
                <i class="ti ti-receipt text-primary"></i>
                Slip Gaji Harian
            </div>
            <span class="badge-total">{{ count($laporan_presensi) }} Karyawan</span>
        </div>
        <div class="top-bar-actions">
            <!-- Layout Switcher: 3 Slips / 2 Slips / 4 Slips -->
            <div class="layout-selector">
                <button type="button" class="layout-btn active" onclick="setLayout('3col', this)" title="3 Slip per baris">
                    <i class="ti ti-layout-columns"></i> 3 Slip / Lembar
                </button>
                <button type="button" class="layout-btn" onclick="setLayout('2col', this)" title="2 Slip berdampingan">
                    <i class="ti ti-layout-grid"></i> 2 Slip / Lembar
                </button>
                <button type="button" class="layout-btn" onclick="setLayout('4col', this)" title="4 Slip kompak (2x2)">
                    <i class="ti ti-layout-grid-add"></i> 4 Slip (2x2)
                </button>
            </div>

            <button onclick="window.print()" class="btn-action btn-print">
                <i class="ti ti-printer"></i>
                Cetak Slip
            </button>
        </div>
    </div>

    <div id="slip-container" class="container layout-3col">
        @foreach ($laporan_presensi as $d)
            @php
                $tanggal_presensi = $periode_dari;
                $hari_hadir = 0;
                $total_denda = 0;

                while (strtotime($tanggal_presensi) <= strtotime($periode_sampai)) {
                    if (isset($d[$tanggal_presensi])) {
                        if ($d[$tanggal_presensi]['status'] == 'h') {
                            $hari_hadir++;
                            
                            $denda_dari_db = isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                ? $d[$tanggal_presensi]['denda']
                                : null;
                            
                            if ($denda_dari_db !== null) {
                                $total_denda += $denda_dari_db;
                            } else {
                                $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                                $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);
                                if ($terlambat != null && $terlambat['desimal_terlambat'] < 1) {
                                    $total_denda += hitungdenda($denda_list, $terlambat['menitterlambat']);
                                }
                            }
                        }
                    }
                    $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                }

                $total_upah = $d['gaji_pokok'] * $hari_hadir;
                $gaji_bersih = $total_upah - $total_denda;
            @endphp

            <div class="slip-card">
                <!-- Header -->
                <div class="slip-header">
                    <div class="company-info">
                        <div class="company-name">{{ $generalsetting->nama_perusahaan }}</div>
                        <div class="company-sub">Sistem Payroll Harian</div>
                    </div>
                    <div class="slip-title-block">
                        <div class="slip-title">Slip Gaji</div>
                        <div class="slip-periode">{{ date('d/m/Y', strtotime($periode_dari)) }} - {{ date('d/m/Y', strtotime($periode_sampai)) }}</div>
                    </div>
                </div>

                <!-- Employee Info -->
                <div class="employee-grid">
                    <div class="employee-item">
                        <span class="employee-label">NIK</span>
                        <span class="employee-value">{{ $d['nik_show'] ?? $d['nik'] }}</span>
                    </div>
                    <div class="employee-item">
                        <span class="employee-label">No Rekening</span>
                        <span class="employee-value">{{ $d['no_rekening'] ?? '-' }} {{ !empty($d['nama_bank']) ? '(' . strtoupper($d['nama_bank']) . ')' : '' }}</span>
                    </div>
                    <div class="employee-item">
                        <span class="employee-label">Nama Karyawan</span>
                        <span class="employee-value">{{ $d['nama_karyawan'] }}</span>
                    </div>
                    <div class="employee-item">
                        <span class="employee-label">Jabatan</span>
                        <span class="employee-value">{{ $d['nama_jabatan'] ?? '-' }}</span>
                    </div>
                    <div class="work-summary-badge">
                        <div style="display: flex; justify-content: space-between; width: 100%;">
                            <span>Rate: Rp {{ number_format($d['gaji_pokok'], 0, ',', '.') }}/hari</span>
                            <span>Hadir: {{ $hari_hadir }} Hari</span>
                        </div>
                    </div>
                </div>

                <!-- Penghasilan -->
                <div class="table-section">
                    <div class="section-header earning">Penghasilan</div>
                    <div class="slip-row">
                        <span class="slip-row-label">Upah Pokok (Harian)</span>
                        <span class="slip-row-value">Rp {{ number_format($total_upah, 0, ',', '.') }}</span>
                    </div>
                    <div class="slip-row subtotal">
                        <span class="slip-row-label">Total Penghasilan (Bruto)</span>
                        <span class="slip-row-value">Rp {{ number_format($total_upah, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Potongan -->
                @if ($total_denda > 0)
                    <div class="table-section">
                        <div class="section-header deduction">Potongan</div>
                        <div class="slip-row">
                            <span class="slip-row-label">Denda Keterlambatan</span>
                            <span class="slip-row-value">Rp {{ number_format($total_denda, 0, ',', '.') }}</span>
                        </div>
                        <div class="slip-row subtotal">
                            <span class="slip-row-label">Total Potongan</span>
                            <span class="slip-row-value">Rp {{ number_format($total_denda, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Total -->
                <div class="net-salary-card">
                    <span class="net-salary-label">Gaji Bersih (THP)</span>
                    <span class="net-salary-value">Rp {{ number_format($gaji_bersih, 0, ',', '.') }}</span>
                </div>

                <!-- Footer -->
                <div class="slip-footer">
                    <div class="system-info">
                        Dicetak: {{ date('d/m/Y H:i') }}<br>
                        <em>Dokumen resmi payroll.</em>
                    </div>
                    <div class="signature-block">
                        <div class="signature-title">Manager Payroll</div>
                        <div class="signature-line"></div>
                        <div class="signature-subtitle">{{ $generalsetting->nama_perusahaan }}</div>
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
