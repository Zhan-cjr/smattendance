<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Presensi Karyawan - {{ $karyawan->nama_karyawan }}</title>
    
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
            size: A4;
            margin: 10mm;
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

        /* Employee Identity Card */
        .employee-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            display: flex;
            gap: 16px;
            align-items: center;
            margin-bottom: 16px;
        }

        .employee-avatar {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid var(--slate-200);
        }

        .employee-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 8px 16px;
            flex: 1;
        }

        .employee-meta-item {
            display: flex;
            flex-direction: column;
        }

        .employee-meta-item span.label {
            font-size: 10px;
            font-weight: 700;
            color: var(--slate-400);
            text-transform: uppercase;
        }

        .employee-meta-item span.val {
            font-size: 12px;
            font-weight: 700;
            color: var(--slate-800);
        }

        /* Table Design */
        .table-wrap {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--slate-200);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
            margin-bottom: 16px;
        }

        .datatable-custom {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        .datatable-custom th {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            padding: 8px 6px;
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .datatable-custom td {
            padding: 6px 8px;
            border: 1px solid var(--slate-200);
            vertical-align: middle;
        }

        .datatable-custom tbody tr:nth-child(even) {
            background-color: var(--slate-50);
        }

        /* Recap Cards */
        .recap-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .recap-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .recap-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--slate-800);
            text-transform: uppercase;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .recap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .recap-table td {
            padding: 6px 8px;
            border-bottom: 1px solid var(--slate-100);
        }

        .recap-table tr:last-child td {
            border-bottom: none;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .top-bar {
                display: none !important;
            }
            .report-header, .employee-card, .table-wrap, .recap-card {
                box-shadow: none;
                border: 1px solid #000;
                border-radius: 0;
            }
            .datatable-custom th {
                background: #eee !important;
                color: #000 !important;
                border: 1px solid #000;
            }
            .datatable-custom td {
                border: 1px solid #000;
            }
        }
    </style>
</head>

<body>

    <!-- ================= 1. SCREEN TOP ACTION BAR ================= -->
    <div class="top-bar">
        <div class="top-bar-badge">
            <i class="ti ti-user-check"></i>
            <span>Rincian Presensi Karyawan</span>
        </div>

        <form method="POST" action="{{ route('laporan.cetakpresensi') }}" target="_blank" style="display: inline-block;">
            @csrf
            @foreach($request_params as $k => $v) <input type="hidden" name="{{ $k }}" value="{{ $v }}"> @endforeach
            <input type="hidden" name="format_laporan" value="1">
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
                <h2>LAPORAN PRESENSI KARYAWAN</h2>
                <h3>{{ $generalsetting->nama_perusahaan }}</h3>
                <p>{{ $generalsetting->alamat }} @if($generalsetting->telepon) | Telp: {{ $generalsetting->telepon }} @endif</p>
            </div>
        </div>

        <div class="header-period-card">
            <div class="period-label">Periode Presensi</div>
            <div class="period-value">
                {{ date('d F Y', strtotime($periode_dari)) }} - {{ date('d F Y', strtotime($periode_sampai)) }}
            </div>
        </div>
    </div>

    <!-- ================= 3. EMPLOYEE IDENTITY ================= -->
    <div class="employee-card">
        @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
            <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="Foto {{ $karyawan->nama_karyawan }}" class="employee-avatar">
        @else
            <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="No Image" class="employee-avatar">
        @endif

        <div class="employee-meta-grid">
            <div class="employee-meta-item">
                <span class="label">Nama Pegawai</span>
                <span class="val">{{ $karyawan->nama_karyawan }}</span>
            </div>
            <div class="employee-meta-item">
                <span class="label">NIK</span>
                <span class="val">{{ $karyawan->nik_show ?? $karyawan->nik }}</span>
            </div>
            <div class="employee-meta-item">
                <span class="label">Jabatan</span>
                <span class="val">{{ $karyawan->nama_jabatan }}</span>
            </div>
            <div class="employee-meta-item">
                <span class="label">Departemen / Cabang</span>
                <span class="val">{{ $karyawan->nama_dept }} ({{ $karyawan->nama_cabang }})</span>
            </div>
        </div>
    </div>

    <!-- ================= 4. ATTENDANCE LOG TABLE ================= -->
    <div class="table-wrap">
        <table class="datatable-custom">
            <thead>
                <tr>
                    <th style="width: 32px;">No</th>
                    <th>Tanggal</th>
                    <th>Hari</th>
                    <th>Jadwal Kerja</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Istirahat</th>
                    <th>Status</th>
                    <th>Keterlambatan</th>
                    <th>Denda (Rp)</th>
                    <th>Pot. Jam</th>
                    <th>Lembur</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_denda = 0; $total_potongan_jam = 0; $total_jam_lembur = 0;
                    $jml_hadir = 0; $jml_sakit = 0; $jml_izin = 0; $jml_cuti = 0;
                    $jml_libur = 0; $jml_alfa = 0; $jml_terlambat = 0;
                    $jml_pulangcepat = 0; $jml_tidakscanmasuk = 0; $jml_tidakscanpulang = 0;

                    $presensiByDate = [];
                    foreach ($presensi as $row) {
                        $presensiByDate[$row->tanggal] = $row;
                    }

                    $mapJadwalByDate = $jadwal_bydate[$karyawan->nik] ?? [];
                    $mapJadwalGrupByDate = $jadwal_grup_bydate[$karyawan->nik] ?? [];
                    $mapJadwalByDay = $jadwal_byday[$karyawan->nik] ?? [];

                    $tanggal_presensi = $periode_dari;
                    $no = 1;
                @endphp

                @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                    @php
                        $denda = 0;
                        $potongan_jam = 0;
                        $search = ['nik' => $karyawan->nik, 'tanggal' => $tanggal_presensi];
                        $ceklibur = ceklibur($datalibur, $search);

                        $d_row = $presensiByDate[$tanggal_presensi] ?? null;
                        if ($d_row && $d_row->jam_lembur_aktual !== null) {
                            $jml_jam_lembur = $d_row->jam_lembur_aktual;
                        } else {
                            $ceklembur = ceklembur($datalembur, $search);
                            $lembur_jam = hitungLembur($ceklembur);
                            $jml_jam_lembur = !empty($ceklembur) ? $lembur_jam : 0;
                        }
                        $nama_hari = getHari($tanggal_presensi);

                        $d = $presensiByDate[$tanggal_presensi] ?? null;
                        $bgcolor = ''; $textcolor = '';
                        $ket_status = ''; $ket_jam_kerja = '-';
                        $ket_jam_in = '-'; $ket_jam_out = '-';
                        $ket_istirahat = '-'; $ket_terlambat = '-';
                    @endphp

                    @if ($d !== null)
                        @php
                            $ket_jam_kerja = $d->nama_jam_kerja;
                            $ket_jam_in = !empty($d->jam_in) ? date('H:i', strtotime($d->jam_in)) : '-';
                            $ket_jam_out = !empty($d->jam_out) ? date('H:i', strtotime($d->jam_out)) : '-';

                            if ($d->status == 'h') {
                                $jml_hadir++;
                                $jam_masuk = $tanggal_presensi . ' ' . $d->jam_masuk;
                                $terlambat = hitungjamterlambat($d->jam_in, $jam_masuk);
                                if ($terlambat && $terlambat['menitterlambat'] > 0) {
                                    $jml_terlambat++;
                                    $ket_terlambat = "<span style='color: #dc2626; font-weight: 700;'>" . $terlambat['show_laporan'] . "</span>";
                                }

                                $denda_db = $d->denda ?? null;
                                if ($denda_db !== null) {
                                    $denda = $denda_db;
                                } elseif ($terlambat) {
                                    $denda = $terlambat['desimal_terlambat'] < 1 ? hitungdenda($denda_list, $terlambat['menitterlambat']) : 0;
                                }

                                $pulangcepat = hitungpulangcepat($tanggal_presensi, $d->jam_out, $d->jam_pulang, $d->istirahat, $d->jam_awal_istirahat, $d->jam_akhir_istirahat, $d->lintashari);
                                if ($pulangcepat) $jml_pulangcepat++;

                                $potongan_istirahat = hitungPotonganIstirahat($d->istirahat_out, $d->istirahat_in, $d->jam_awal_istirahat, $d->jam_akhir_istirahat);
                                $potongan_tidak_absen = (empty($d->jam_out) || empty($d->jam_in)) ? $d->total_jam : 0;
                                $status_potongan_istirahat = $d->status_potongan_istirahat ?? $generalsetting->potongan_istirahat;
                                $potongan_jam_terlambat = ($terlambat && $terlambat['desimal_terlambat'] >= 1) ? $terlambat['desimal_terlambat'] : 0;

                                $potongan_jam = $potongan_tidak_absen == 0 ? ($pulangcepat + $potongan_jam_terlambat + ($status_potongan_istirahat == 1 ? $potongan_istirahat : 0)) : $potongan_tidak_absen;
                                $status_potongan_harian = $d->status_potongan ?? $generalsetting->status_potongan_jam;
                                if ($status_potongan_harian == 0) $potongan_jam = 0;

                                if (empty($d->jam_in)) $jml_tidakscanmasuk++;
                                if (empty($d->jam_out)) $jml_tidakscanpulang++;

                                if ($generalsetting->absen_istirahat == 1) {
                                    $istirahat_in_val = (!empty($d->istirahat_in)) ? date('H:i', strtotime($d->istirahat_in)) : null;
                                    $istirahat_out_val = (!empty($d->istirahat_out)) ? date('H:i', strtotime($d->istirahat_out)) : null;
                                    if ($istirahat_out_val && $istirahat_in_val) {
                                        $ket_istirahat = $istirahat_out_val . ' - ' . $istirahat_in_val;
                                    } else {
                                        $ket_istirahat = '<span style="color: #dc2626;">Belum Absen</span>';
                                    }
                                }
                                $ket_status = '<span style="font-weight: 700; color: #16a34a;">HADIR</span>';
                            } elseif ($d->status == 'i') {
                                $jml_izin++;
                                $ket_status = '<span style="font-weight: 700; color: #d97706;">IZIN</span>';
                                $potongan_jam = $generalsetting->status_potongan_jam == 1 ? $d->total_jam : 0;
                                $denda = $d->denda ?? 0;
                            } elseif ($d->status == 's') {
                                $jml_sakit++;
                                $ket_status = '<span style="font-weight: 700; color: #db2777;">SAKIT</span>';
                                $denda = $d->denda ?? 0;
                            } elseif ($d->status == 'c') {
                                $jml_cuti++;
                                $ket_status = '<span style="font-weight: 700; color: #0284c7;">CUTI</span>';
                                $denda = $d->denda ?? 0;
                            } elseif ($d->status == 'a') {
                                $jml_alfa++;
                                $ket_status = '<span style="font-weight: 700; color: #dc2626;">ALPA</span>';
                                $status_pot_alpa = $d->status_potongan ?? $generalsetting->status_potongan_jam;
                                $potongan_jam = $status_pot_alpa == 1 ? $d->total_jam : 0;
                                $denda = $d->denda ?? 0;
                            }
                        @endphp
                    @else
                        @php
                            $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                            if (!empty($ceklibur)) {
                                $jml_libur++;
                                $ket_status = '<span style="font-weight: 700; color: #15803d;">LIBUR (' . e($ceklibur[0]['keterangan']) . ')</span>';
                            } else {
                                $totalJamJadwal = $mapJadwalByDate[$tanggal_presensi] ?? null;
                                if ($totalJamJadwal === null) $totalJamJadwal = $mapJadwalGrupByDate[$tanggal_presensi] ?? null;
                                if ($totalJamJadwal === null) $totalJamJadwal = $mapJadwalByDay[$nama_hari] ?? null;
                                if ($totalJamJadwal === null) {
                                    $keyDeptCabang = $karyawan->kode_dept . '|' . $karyawan->kode_cabang;
                                    $mapDept = $jadwal_bydept[$keyDeptCabang] ?? [];
                                    $totalJamJadwal = $mapDept[$nama_hari] ?? null;
                                }
                                if ($totalJamJadwal === null) {
                                    $totalJamJadwal = $jadwal_global[$nama_hari] ?? null;
                                }

                                if ($totalJamJadwal !== null && !$is_future) {
                                    $jml_alfa++;
                                    $ket_status = '<span style="font-weight: 700; color: #dc2626;">ALPA</span>';
                                    $potongan_jam = $generalsetting->status_potongan_jam == 1 ? (is_array($totalJamJadwal) ? $totalJamJadwal['total_jam'] : $totalJamJadwal) : 0;
                                } else {
                                    $ket_status = '-';
                                }
                            }
                        @endphp
                    @endif

                    @php
                        $total_denda += $denda;
                        $total_potongan_jam += $potongan_jam;
                        $total_jam_lembur += $jml_jam_lembur;
                    @endphp

                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ date('d-m-Y', strtotime($tanggal_presensi)) }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ $nama_hari }}</td>
                        <td>{{ $ket_jam_kerja }}</td>
                        <td style="text-align: center;">{{ $ket_jam_in }}</td>
                        <td style="text-align: center;">{{ $ket_jam_out }}</td>
                        <td style="text-align: center;">{!! $ket_istirahat !!}</td>
                        <td style="text-align: center;">{!! $ket_status !!}</td>
                        <td style="text-align: center;">{!! $ket_terlambat !!}</td>
                        <td style="text-align: right; color: {{ $denda > 0 ? '#b91c1c' : 'inherit' }}; font-weight: 700;">
                            {{ $denda > 0 ? formatAngka($denda) : '-' }}
                        </td>
                        <td style="text-align: center; color: {{ $potongan_jam > 0 ? '#9333ea' : 'inherit' }}; font-weight: 700;">
                            {{ $potongan_jam > 0 ? formatAngkaDesimal($potongan_jam) : '-' }}
                        </td>
                        <td style="text-align: center; color: {{ $jml_jam_lembur > 0 ? '#0d9488' : 'inherit' }}; font-weight: 700;">
                            {{ $jml_jam_lembur > 0 ? formatAngkaDesimal($jml_jam_lembur) : '-' }}
                        </td>
                    </tr>

                    @php
                        $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                    @endphp
                @endwhile
            </tbody>
        </table>
    </div>

    <!-- ================= 5. SUMMARY ACCUMULATION ================= -->
    <div class="recap-section">
        <!-- Rekapitulasi Status Kehadiran -->
        <div class="recap-card">
            <div class="recap-title">
                <i class="ti ti-chart-pie" style="color: var(--primary);"></i>
                <span>Rekapitulasi Kehadiran</span>
            </div>
            <table class="recap-table">
                <tr><td>Hadir</td><td style="text-align: right; font-weight: 800; color: #16a34a;">{{ $jml_hadir }} Hari</td></tr>
                <tr><td>Izin</td><td style="text-align: right; font-weight: 800; color: #d97706;">{{ $jml_izin }} Hari</td></tr>
                <tr><td>Sakit</td><td style="text-align: right; font-weight: 800; color: #db2777;">{{ $jml_sakit }} Hari</td></tr>
                <tr><td>Cuti</td><td style="text-align: right; font-weight: 800; color: #0284c7;">{{ $jml_cuti }} Hari</td></tr>
                <tr><td>Alpa</td><td style="text-align: right; font-weight: 800; color: #dc2626;">{{ $jml_alfa }} Hari</td></tr>
                <tr><td>Libur Resmi / Libur Kerja</td><td style="text-align: right; font-weight: 800; color: #15803d;">{{ $jml_libur }} Hari</td></tr>
            </table>
        </div>

        <!-- Rekapitulasi Disiplin & Akumulasi -->
        <div class="recap-card">
            <div class="recap-title">
                <i class="ti ti-calculator" style="color: var(--primary);"></i>
                <span>Disiplin & Akumulasi Lembur/Denda</span>
            </div>
            <table class="recap-table">
                <tr><td>Total Hari Terlambat</td><td style="text-align: right; font-weight: 800; color: #dc2626;">{{ $jml_terlambat }} Kali</td></tr>
                <tr><td>Tidak Scan Masuk</td><td style="text-align: right; font-weight: 800;">{{ $jml_tidakscanmasuk }} Kali</td></tr>
                <tr><td>Tidak Scan Pulang</td><td style="text-align: right; font-weight: 800;">{{ $jml_tidakscanpulang }} Kali</td></tr>
                <tr><td>Pulang Cepat</td><td style="text-align: right; font-weight: 800;">{{ $jml_pulangcepat }} Kali</td></tr>
                <tr><td>Total Potongan Jam</td><td style="text-align: right; font-weight: 800; color: #9333ea;">{{ formatAngkaDesimal($total_potongan_jam) }} Jam</td></tr>
                <tr><td>Total Denda Keterlambatan</td><td style="text-align: right; font-weight: 800; color: #b91c1c;">Rp {{ formatAngka($total_denda) }}</td></tr>
                <tr><td>Total Jam Lembur</td><td style="text-align: right; font-weight: 800; color: #0d9488;">{{ formatAngkaDesimal($total_jam_lembur) }} Jam</td></tr>
            </table>
        </div>
    </div>

</body>
</html>
