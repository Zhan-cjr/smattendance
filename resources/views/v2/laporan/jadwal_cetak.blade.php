<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Matriks Jadwal Karyawan - {{ date('d/m/Y H:i') }}</title>
    
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
            margin-bottom: 16px;
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
            font-size: 10px;
        }

        table.matrix-table thead th {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.3px;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid rgba(255, 255, 255, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        table.matrix-table thead tr:nth-child(2) th {
            top: 28px;
            background: var(--primary-dark);
            font-size: 9px;
            padding: 4px 2px;
        }

        table.matrix-table tbody td {
            padding: 5px 4px;
            border-bottom: 1px solid var(--slate-200);
            border-right: 1px solid var(--slate-200);
            text-align: center;
            vertical-align: middle;
            font-size: 9.5px;
        }

        table.matrix-table tbody tr:nth-child(even) td:not([style*="background-color"]) {
            background-color: var(--slate-50);
        }

        table.matrix-table tbody tr:hover td:not([style*="background-color"]) {
            background-color: var(--primary-light);
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
            width: 32px;
            min-width: 32px;
            background: #ffffff;
        }

        .col-nik {
            left: 32px;
            width: 75px;
            min-width: 75px;
            background: #ffffff;
            font-weight: 600;
        }

        .col-nama {
            left: 107px;
            width: 140px;
            min-width: 140px;
            background: #ffffff;
            text-align: left !important;
            font-weight: 700;
            color: var(--slate-900);
            padding-left: 8px !important;
            border-right: 2px solid var(--slate-300) !important;
        }

        /* Shift & Libur Cell Badges */
        .shift-badge {
            display: inline-block;
            font-weight: 700;
            line-height: 1.2;
            font-size: 9px;
        }

        .shift-time {
            font-size: 8px;
            opacity: 0.9;
            display: block;
        }

        .libur-cell {
            background-color: #059669 !important;
            color: #ffffff !important;
            font-weight: 700;
        }

        .minggu-cell {
            background-color: #f59e0b !important;
            color: #ffffff !important;
            font-weight: 700;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .top-bar {
                display: none !important;
            }

            .report-header {
                border: 1px solid var(--slate-300) !important;
                border-radius: 0 !important;
                margin-bottom: 10px !important;
                padding: 10px 14px !important;
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
                <i class="ti ti-calendar-event text-primary"></i>
                Laporan Jadwal Kerja Karyawan
            </div>
            <span class="badge-total">{{ count($karyawan) }} Karyawan</span>
        </div>
        <div class="top-bar-actions">
            <form action="{{ route('laporan.cetakjadwal') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="kode_cabang" value="{{ request('kode_cabang') }}">
                <input type="hidden" name="kode_dept" value="{{ request('kode_dept') }}">
                <input type="hidden" name="nik" value="{{ request('nik') }}">
                <input type="hidden" name="dari" value="{{ $periode_dari }}">
                <input type="hidden" name="sampai" value="{{ $periode_sampai }}">
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
                <p>{{ $generalsetting->alamat ?? 'Payroll & Attendance System' }} | Telp: {{ $generalsetting->telepon ?? '-' }}</p>
            </div>
        </div>
        <div class="report-title-badge">
            <h3>Laporan Matriks Jadwal Kerja</h3>
            <span class="badge-periode">
                <i class="ti ti-calendar"></i>
                Periode: {{ date('d/m/Y', strtotime($periode_dari)) }} - {{ date('d/m/Y', strtotime($periode_sampai)) }}
            </span>
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
                        <th colspan="{{ $jmlhari }}">Tanggal & Hari Kerja</th>
                    </tr>
                    <tr>
                        @php
                            $tanggal_loop = $periode_dari;
                        @endphp
                        @while (strtotime($tanggal_loop) <= strtotime($periode_sampai))
                            <th style="min-width: 65px; width: 65px;">
                                {{ date('d', strtotime($tanggal_loop)) }}<br>
                                <span style="font-weight: 500; font-size: 8px; opacity: 0.85;">{{ getHari(date('Y-m-d', strtotime($tanggal_loop))) }}</span>
                            </th>
                            @php
                                $tanggal_loop = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_loop)));
                            @endphp
                        @endwhile
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawan as $d)
                        @php
                            $tanggal_loop = $periode_dari;
                            $mapJadwalByDate = $jadwal_bydate[$d->nik] ?? [];
                            $mapJadwalGrupByDate = $jadwal_grup_bydate[$d->nik] ?? [];
                            $mapJadwalByDay = $jadwal_byday[$d->nik] ?? [];
                        @endphp
                        <tr>
                            <td class="sticky-col col-no">{{ $loop->iteration }}</td>
                            <td class="sticky-col col-nik">{{ $d->nik_show ?? $d->nik }}</td>
                            <td class="sticky-col col-nama">{{ $d->nama_karyawan }}</td>
                            
                            @while (strtotime($tanggal_loop) <= strtotime($periode_sampai))
                                @php
                                    $search = [
                                        'nik' => $d->nik,
                                        'tanggal' => $tanggal_loop,
                                    ];
                                    $ceklibur = ceklibur($datalibur, $search);
                                    $nama_hari = getHari($tanggal_loop);
                                    
                                    $jadwal_info = null;
                                    
                                    // 1) By-Date Employee
                                    if (isset($mapJadwalByDate[$tanggal_loop])) {
                                        $jadwal_info = $mapJadwalByDate[$tanggal_loop];
                                    }
                                    // 2) By-Date Group
                                    elseif (isset($mapJadwalGrupByDate[$tanggal_loop])) {
                                        $jadwal_info = $mapJadwalGrupByDate[$tanggal_loop];
                                    }
                                    // 3) By-Day Employee
                                    elseif (isset($mapJadwalByDay[$nama_hari])) {
                                        $jadwal_info = $mapJadwalByDay[$nama_hari];
                                    }
                                    // 4) By-Day Dept/Branch
                                    else {
                                        $keyDeptCabang = $d->kode_dept . '|' . $d->kode_cabang;
                                        $mapDept = $jadwal_bydept[$keyDeptCabang] ?? [];
                                        if (isset($mapDept[$nama_hari])) {
                                            $jadwal_info = $mapDept[$nama_hari];
                                        }
                                        // 5) Global Schedule
                                        elseif (isset($jadwal_global[$nama_hari])) {
                                            $jadwal_info = $jadwal_global[$nama_hari];
                                        }
                                    }
                                    
                                    $bgcolor = '';
                                    $content = '';
                                    
                                    if (!empty($ceklibur)) {
                                        $bgcolor = '#059669';
                                        $ket_libur = !empty($ceklibur[0]['keterangan']) ? '<span class="shift-time">' . $ceklibur[0]['keterangan'] . '</span>' : '';
                                        $content = '<span class="shift-badge" style="color: white;">LIBUR</span>' . $ket_libur;
                                    } else {
                                        if ($nama_hari == 'Minggu') {
                                            $bgcolor = '#f59e0b';
                                        }
                                        
                                        if ($jadwal_info) {
                                            $content = '<span class="shift-badge">' . $jadwal_info['nama_jam_kerja'] . '</span>' . 
                                                       '<span class="shift-time">' . 
                                                       date('H:i', strtotime($jadwal_info['jam_masuk'])) . '-' . 
                                                       date('H:i', strtotime($jadwal_info['jam_pulang'])) . '</span>';
                                            if (empty($bgcolor) && !empty($jadwal_info['color'])) {
                                                $bgcolor = $jadwal_info['color'];
                                            }
                                        }
                                    }
                                @endphp
                                <td style="{{ !empty($bgcolor) ? 'background-color: ' . $bgcolor . ';' : '' }} color: {{ (!empty($bgcolor) && $bgcolor != 'white' && $bgcolor != '#ffffff') ? '#ffffff' : 'inherit' }}">
                                    {!! $content !!}
                                </td>
                                @php
                                    $tanggal_loop = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_loop)));
                                @endphp
                            @endwhile
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
