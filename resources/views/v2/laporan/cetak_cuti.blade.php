<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Rekapitulasi Cuti - {{ date('d/m/Y H:i') }}</title>
    
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
            gap: 8px;
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

        .btn-excel {
            background: #059669;
            color: #ffffff;
        }

        .btn-excel:hover {
            background: #047857;
        }

        .btn-print {
            background: var(--slate-900);
            color: #ffffff;
        }

        .btn-print:hover {
            background: var(--slate-800);
        }

        /* Company Header */
        .report-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--slate-200);
            margin-bottom: 14px;
        }

        .company-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .company-logo {
            max-height: 46px;
            max-width: 90px;
            object-fit: contain;
        }

        .company-details h2 {
            font-size: 15px;
            font-weight: 800;
            color: var(--slate-900);
            margin: 0 0 2px 0;
            letter-spacing: -0.2px;
        }

        .company-details p {
            font-size: 11px;
            color: var(--slate-500);
            margin: 0;
        }

        .report-title-badge {
            text-align: right;
        }

        .report-title-badge h3 {
            font-size: 14px;
            font-weight: 800;
            color: var(--primary);
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-periode {
            display: inline-block;
            background: var(--slate-100);
            color: var(--slate-700);
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 6px;
            border: 1px solid var(--slate-200);
        }

        /* Meta Parameters Bar */
        .meta-param-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding: 10px 16px;
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid var(--slate-200);
            margin-bottom: 14px;
            font-size: 11px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .meta-label {
            color: var(--slate-500);
            font-weight: 500;
        }

        .meta-value {
            font-weight: 700;
            color: var(--slate-900);
        }

        /* Table Card Container */
        .table-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--slate-200);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            max-height: 75vh;
        }

        /* Matrix Table */
        table.matrix-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 10.5px;
        }

        table.matrix-table thead th {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.3px;
            padding: 7px 5px;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        table.matrix-table thead tr:nth-child(2) th {
            top: 30px;
            background: var(--primary-dark);
            font-size: 9.5px;
            padding: 5px 3px;
        }

        table.matrix-table tbody td {
            padding: 6px 6px;
            border-bottom: 1px solid var(--slate-200);
            border-right: 1px solid var(--slate-200);
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }

        table.matrix-table tbody tr:nth-child(even) td:not([style*="background-color"]) {
            background-color: var(--slate-50);
        }

        table.matrix-table tbody tr:hover td:not([style*="background-color"]) {
            background-color: var(--primary-light);
        }

        /* Highlight Used Leave */
        .leave-used {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
            font-weight: 700 !important;
        }

        .highlight-total {
            font-weight: 800;
            background-color: #f1f5f9;
            color: var(--slate-900);
        }

        .highlight-sisa {
            font-weight: 800;
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        /* Sticky Columns */
        .sticky-col {
            position: sticky;
            z-index: 10;
        }

        table.matrix-table thead th.sticky-col {
            z-index: 30;
        }

        .col-no {
            left: 0;
            width: 35px;
            min-width: 35px;
            background: #ffffff;
        }

        .col-nik {
            left: 35px;
            width: 80px;
            min-width: 80px;
            background: #ffffff;
            font-weight: 600;
        }

        .col-nama {
            left: 115px;
            width: 170px;
            min-width: 170px;
            background: #ffffff;
            text-align: left !important;
            font-weight: 700;
            color: var(--slate-900);
            padding-left: 10px !important;
            border-right: 2px solid var(--slate-300) !important;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .top-bar {
                display: none !important;
            }

            .report-header, .meta-param-bar {
                border: 1px solid var(--slate-300) !important;
                border-radius: 0 !important;
                margin-bottom: 8px !important;
                padding: 8px 12px !important;
            }

            .table-card {
                border: 1px solid var(--slate-300) !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .table-responsive {
                overflow: visible !important;
                max-height: none !important;
            }

            .sticky-col {
                position: static !important;
            }

            table.matrix-table thead th {
                position: static !important;
            }
        }
    </style>
</head>

<body>
    <!-- Top Action Bar (Screen Only) -->
    <div class="top-bar">
        <div class="top-bar-info">
            <div class="top-bar-title">
                <i class="ti ti-calendar-off text-primary"></i>
                Laporan Rekapitulasi Cuti Pegawai
            </div>
            <span class="badge-total">{{ count($rekap_cuti) }} Karyawan</span>
        </div>
        <div class="top-bar-actions">
            <form action="{{ route('laporan.cetakcuti') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="kode_cabang" value="{{ request('kode_cabang') }}">
                <input type="hidden" name="kode_dept" value="{{ request('kode_dept') }}">
                <input type="hidden" name="kode_cuti" value="{{ request('kode_cuti') }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="exportButton" value="1">
                <button type="submit" class="btn-action btn-excel">
                    <i class="ti ti-file-spreadsheet"></i>
                    Export Excel
                </button>
            </form>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="ti ti-printer"></i>
                Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Company & Report Header -->
    <div class="report-header">
        <div class="company-info">
            @if (!empty($generalsetting->logo) && Storage::disk('public')->exists('/logo/' . $generalsetting->logo))
                <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo" class="company-logo">
            @elseif (!empty($generalsetting->logo) && file_exists(public_path('storage/logo/' . $generalsetting->logo)))
                <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo" class="company-logo">
            @endif
            <div class="company-details">
                <h2>{{ $generalsetting->nama_perusahaan }}</h2>
                <p>{{ $generalsetting->alamat ?? 'Human Resource & Payroll Management' }} | Telp: {{ $generalsetting->telepon ?? '-' }}</p>
            </div>
        </div>
        <div class="report-title-badge">
            <h3>Laporan Cuti Karyawan</h3>
            <span class="badge-periode">
                <i class="ti ti-calendar"></i>
                Periode Tahun: {{ $tahun }}
            </span>
        </div>
    </div>

    <!-- Parameter Filter Summary Bar -->
    <div class="meta-param-bar">
        <div class="meta-item">
            <span class="meta-label">Cabang:</span>
            <span class="meta-value">{{ textUpperCase($namacabang) }}</span>
        </div>
        <span class="text-slate-300">|</span>
        <div class="meta-item">
            <span class="meta-label">Departemen:</span>
            <span class="meta-value">{{ textUpperCase($namadept) }}</span>
        </div>
        <span class="text-slate-300">|</span>
        <div class="meta-item">
            <span class="meta-label">Jenis Cuti:</span>
            <span class="meta-value text-emerald-700">{{ textUpperCase($jenis_cuti) }}</span>
        </div>
    </div>

    <!-- Table Card Container -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="sticky-col col-no">No</th>
                        <th rowspan="2" class="sticky-col col-nik">NIK</th>
                        <th rowspan="2" class="sticky-col col-nama">Nama Karyawan</th>
                        <th colspan="12">Penggunaan Cuti Per Bulan (Hari)</th>
                        <th rowspan="2" style="width: 60px;">Total<br>Ambil</th>
                        <th rowspan="2" style="width: 60px;">Jatah<br>Cuti</th>
                        <th rowspan="2" style="width: 60px;">Sisa<br>Cuti</th>
                    </tr>
                    <tr>
                        <th style="width: 36px;">Jan</th>
                        <th style="width: 36px;">Feb</th>
                        <th style="width: 36px;">Mar</th>
                        <th style="width: 36px;">Apr</th>
                        <th style="width: 36px;">Mei</th>
                        <th style="width: 36px;">Jun</th>
                        <th style="width: 36px;">Jul</th>
                        <th style="width: 36px;">Agu</th>
                        <th style="width: 36px;">Sep</th>
                        <th style="width: 36px;">Okt</th>
                        <th style="width: 36px;">Nov</th>
                        <th style="width: 36px;">Des</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rekap_cuti as $nik => $d)
                        @php
                            $jatah = '-';
                            $sisa = '-';
                            if (!empty($master_cuti)) {
                                $jatah = $master_cuti->jumlah_hari;
                                if ($master_cuti->kode_cuti == 'C01') {
                                    $sisa = $master_cuti->jumlah_hari - $d['total_ambil'];
                                } else {
                                    $sisa = '-';
                                }
                            }
                        @endphp
                        <tr>
                            <td class="sticky-col col-no">{{ $loop->iteration }}</td>
                            <td class="sticky-col col-nik">{{ $d['nik_show'] ?? $nik }}</td>
                            <td class="sticky-col col-nama">{{ $d['nama'] }}</td>
                            @for ($i = 1; $i <= 12; $i++)
                                <td class="{{ $d['bulan'][$i] > 0 ? 'leave-used' : '' }}">
                                    {{ $d['bulan'][$i] > 0 ? $d['bulan'][$i] : '' }}
                                </td>
                            @endfor
                            <td class="highlight-total">{{ $d['total_ambil'] }}</td>
                            <td>{{ $jatah }}</td>
                            <td class="highlight-sisa">{{ $sisa }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
