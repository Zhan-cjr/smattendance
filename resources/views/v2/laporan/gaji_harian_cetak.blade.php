<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Gaji Harian - {{ date('d/m/Y H:i') }}</title>
    
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
            font-size: 10.5px;
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

        /* Table Container */
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
            padding: 7px 8px;
            vertical-align: middle;
            white-space: nowrap;
            box-sizing: border-box;
        }

        .datatable-matrix thead th {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            text-transform: uppercase;
            text-align: center;
            font-weight: 700;
            font-size: 10px;
            position: sticky;
            top: 0;
            z-index: 20;
            line-height: 1.2;
            height: 32px;
            border-color: rgba(255, 255, 255, 0.15) !important;
        }

        .datatable-matrix tfoot th {
            position: sticky;
            bottom: 0;
            z-index: 20;
            background: #1e293b;
            color: #ffffff;
            font-weight: 800;
            border-top: 2px solid #0f172a;
            padding: 8px;
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
                padding: 4px;
                font-size: 8px;
            }
        }
    </style>
</head>

<body>

    <!-- ================= 1. SCREEN TOP ACTION BAR ================= -->
    <div class="top-bar">
        <div class="top-bar-left">
            <span class="top-bar-badge">
                <i class="ti ti-clock-hour-4"></i>
                Laporan Upah Harian (V2 Engine)
            </span>
            <span style="font-size: 11.5px; color: var(--slate-500);">
                Total: <strong>{{ $laporan_presensi->count() }}</strong> Karyawan
            </span>
        </div>

        <form method="POST" action="{{ route('laporan.cetakpresensi') }}" target="_blank" style="display: inline-block;">
            @csrf
            @foreach($request_params as $k => $v) <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endforeach
            <input type="hidden" name="format_laporan" value="2">
            <input type="hidden" name="jenis_upah" value="Harian">
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
                <h2>LAPORAN GAJI / UPAH HARIAN</h2>
                <h3>{{ $generalsetting->nama_perusahaan }}</h3>
                <p>{{ $generalsetting->alamat }} @if($generalsetting->telepon) | Telp: {{ $generalsetting->telepon }} @endif</p>
            </div>
        </div>

        <div class="header-period-card">
            <div class="period-label">Rentang Periode Upah Harian</div>
            <div class="period-value">
                {{ date('d F Y', strtotime($periode_dari)) }} - {{ date('d F Y', strtotime($periode_sampai)) }}
            </div>
        </div>
    </div>

    <!-- ================= 3. TABLE HARIAN ================= -->
    <div class="table-responsive">
        <table class="datatable-matrix">
            <thead>
                <tr>
                    <th style="width: 34px;">No</th>
                    <th style="width: 80px;">NIK</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th style="width: 60px;">Dept</th>
                    <th>Rate Upah Harian</th>
                    <th style="width: 70px;">Hari Hadir</th>
                    <th>Total Upah</th>
                    <th style="color: #fca5a5;">Denda</th>
                    <th style="color: #6ee7b7;">Gaji Bersih</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_upah_all = 0;
                    $total_all_denda = 0;
                    $total_gaji_bersih_all = 0;
                @endphp

                @foreach ($laporan_presensi as $d)
                    @php
                        $tanggal_presensi = $periode_dari;
                        $hari_hadir = 0;
                        $total_denda = 0;

                        while (strtotime($tanggal_presensi) <= strtotime($periode_sampai)) {
                            $denda = 0;
                            if (isset($d[$tanggal_presensi])) {
                                if ($d[$tanggal_presensi]['status'] == 'h') {
                                    $hari_hadir++;
                                    $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                                    $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);
                                    if ($terlambat != null) {
                                        if ($terlambat['desimal_terlambat'] < 1) {
                                            $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                                        }
                                    }
                                }
                            }
                            $total_denda += $denda;
                            $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                        }

                        $total_upah = $hari_hadir * $d['gaji_pokok'];
                        $gaji_bersih = $total_upah - $total_denda;

                        $total_upah_all += $total_upah;
                        $total_all_denda += $total_denda;
                        $total_gaji_bersih_all += $gaji_bersih;
                    @endphp

                    <tr>
                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                        <td style="font-family: monospace;">{{ $d['nik_show'] ?? $d['nik'] }}</td>
                        <td style="font-weight: 700;">{{ $d['nama_karyawan'] }}</td>
                        <td>{{ $d['nama_jabatan'] }}</td>
                        <td style="text-align: center; font-weight: 700;">{{ $d['kode_dept'] }}</td>
                        <td style="text-align: right;">{{ formatAngka($d['gaji_pokok']) }}</td>
                        <td style="text-align: center; font-weight: 800; background: var(--primary-light); color: var(--primary-dark);">{{ $hari_hadir }} Hari</td>
                        <td style="text-align: right; font-weight: 700;">{{ formatAngka($total_upah) }}</td>
                        <td style="text-align: right; color: {{ $total_denda > 0 ? '#b91c1c' : 'inherit' }}; font-weight: 700;">{{ $total_denda > 0 ? formatAngka($total_denda) : '-' }}</td>
                        <td style="text-align: right; font-weight: 800; color: var(--primary-dark); font-size: 11px;">{{ formatAngka($gaji_bersih) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" style="text-align: center;">TOTAL KESELURUHAN</th>
                    <th style="text-align: right;">{{ formatAngka($total_upah_all) }}</th>
                    <th style="text-align: right; color: #fca5a5;">{{ formatAngka($total_all_denda) }}</th>
                    <th style="text-align: right; color: #6ee7b7; font-size: 11.5px;">{{ formatAngka($total_gaji_bersih_all) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

</body>
</html>
