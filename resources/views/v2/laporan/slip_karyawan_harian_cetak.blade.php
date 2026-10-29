@extends('layouts.mobile.app')
@section('content')
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

        body {
            background: #f8fafc !important;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--slate-800);
            margin: 0;
            padding: 0;
        }

        #content-section {
            margin-top: 20px;
            padding: 10px 12px 80px 12px;
            position: relative;
            z-index: 1;
            min-height: 100vh;
            background: #f8fafc;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            max-width: 440px;
            margin: 0 auto;
            padding: 0;
        }

        .print-action-bar {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 5px;
        }

        .btn-print-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
            color: #ffffff !important;
            border: none !important;
            padding: 12px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(10, 92, 65, 0.2);
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            gap: 8px;
            text-transform: uppercase;
        }

        .btn-print-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(10, 92, 65, 0.3);
        }

        .slip-card {
            width: 100%;
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 18px;
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        .slip-header {
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company-info {
            text-align: left;
        }

        .company-name {
            font-size: 12.5px;
            font-weight: 800;
            color: var(--slate-900);
            text-transform: uppercase;
        }

        .company-sub {
            font-size: 9px;
            color: var(--slate-500);
            font-weight: 500;
        }

        .slip-title-block {
            text-align: right;
        }

        .slip-title {
            font-size: 12px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
        }

        .slip-periode {
            font-size: 8.5px;
            color: var(--slate-600);
            font-weight: 600;
            margin-top: 2px;
            background: var(--slate-100);
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        .employee-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            margin-bottom: 12px;
            padding: 8px 10px;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 8px;
        }

        .employee-item {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .employee-label {
            font-size: 8px;
            font-weight: 600;
            color: var(--slate-500);
            text-transform: uppercase;
        }

        .employee-value {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--slate-900);
        }

        .work-summary-badge {
            grid-column: span 2;
            border-top: 1px dashed var(--slate-200);
            padding-top: 5px;
            margin-top: 2px;
            font-size: 9px;
            color: var(--primary-dark);
            font-weight: 600;
        }

        .table-section {
            margin-bottom: 10px;
            border: 1px solid var(--slate-200);
            border-radius: 8px;
            overflow: hidden;
        }

        .section-header {
            font-size: 9.5px;
            font-weight: 700;
            padding: 4px 8px;
            text-transform: uppercase;
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
            padding: 4px 8px;
            font-size: 10px;
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

        .net-salary-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 8px;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0;
            color: #ffffff;
            box-shadow: 0 4px 8px -2px rgba(10, 92, 65, 0.25);
        }

        .net-salary-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.9);
        }

        .net-salary-value {
            font-size: 13.5px;
            font-weight: 800;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
        }

        .slip-footer {
            border-top: 1px solid var(--slate-200);
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
        }

        .system-info {
            font-size: 8px;
            color: var(--slate-400);
            line-height: 1.3;
        }

        .signature-block {
            text-align: center;
            min-width: 90px;
        }

        .signature-title {
            font-size: 8px;
            font-weight: 600;
            color: var(--slate-600);
            margin-bottom: 22px;
        }

        .signature-line {
            border-bottom: 1px solid var(--slate-400);
            width: 100%;
            margin-bottom: 2px;
        }

        .signature-subtitle {
            font-size: 7.5px;
            color: var(--slate-600);
            font-weight: 600;
        }

        @media print {
            body {
                background: #ffffff !important;
            }
            #header-section, .header-large, .appHeader, #appBottomMenu, .print-action-bar {
                display: none !important;
            }
            #content-section {
                padding: 0 !important;
                margin: 0 !important;
            }
            .container {
                max-width: 100% !important;
            }
            .slip-card {
                border: 1px solid var(--slate-300) !important;
                box-shadow: none !important;
                padding: 10px !important;
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
    </style>

    <div id="content-section">
        <div class="container">
            <div class="print-action-bar">
                <button type="button" class="btn-print-custom" onclick="window.print()">
                    <i class="ti ti-printer"></i>
                    Cetak / Simpan Slip
                </button>
            </div>

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
                            <div class="company-sub">Payroll Harian</div>
                        </div>
                        <div class="slip-title-block">
                            <div class="slip-title">Slip Gaji</div>
                            <div class="slip-periode">{{ date('d/m/Y', strtotime($periode_dari)) }} - {{ date('d/m/Y', strtotime($periode_sampai)) }}</div>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="employee-grid">
                        <div class="employee-item">
                            <span class="employee-label">NIK</span>
                            <span class="employee-value">{{ $d['nik_show'] ?? $d['nik'] }}</span>
                        </div>
                        <div class="employee-item">
                            <span class="employee-label">Rekening</span>
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

                    <!-- Net Salary -->
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
    </div>
@endsection
