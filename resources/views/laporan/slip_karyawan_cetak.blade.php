@extends('layouts.mobile.app')
@section('content')
    <style>
        body {

            margin: 0;
            padding: 15px;
            font-size: 11px;
            line-height: 1.3;
        }

        #content-section {
            margin-top: 90px;
            padding: 16px 8px 8px 8px;
            position: relative;
            z-index: 1;
            min-height: 100vh;
            background: var(--md-background);
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .slip-struk {
            width: 100%;
            background: white;
            border: 1px solid #333;
            border-radius: 3px;
            padding: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            page-break-inside: avoid;
            margin-top: 50px;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #333;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .company-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .slip-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .periode {
            font-size: 12px;
            color: #666;
        }

        .employee-section {
            margin-bottom: 8px;
            border-bottom: 1px dotted #666;
            padding-bottom: 6px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 14px;
        }

        .label {
            font-weight: bold;
        }

        .value {
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            text-align: center;
            margin: 8px 0 4px 0;
            padding: 2px;
            background: #f0f0f0;
            border: 1px solid #ddd;
        }

        .earning {
            background: #e8f5e8;
            border-color: #28a745;
        }

        .deduction {
            background: #fde8e8;
            border-color: #dc3545;
        }

        .adjustment {
            background: #e8f4f8;
            border-color: #17a2b8;
        }

        .total-section {
            margin-top: 8px;
            border-top: 2px solid #333;
            padding-top: 6px;
        }

        .net-salary {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 16px;
            padding: 4px;
            background: #f8f9fa;
            border: 1px solid #333;
        }

        .work-info {
            font-size: 12px;
            color: #666;
            text-align: center;
            margin: 6px 0;
            border-top: 1px dotted #666;
            padding-top: 4px;
        }

        .currency {
            font-family: 'Courier New', monospace;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #888;
            margin-top: 8px;
            border-top: 1px dashed #333;
            padding-top: 6px;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
                background: white;
            }

            .slip-struk {
                box-shadow: none;
                border: 1px solid #000;
            }
        }
    </style>
    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Slip Gaji</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section" style="margin-top: 30px;">
        @foreach ($laporan_presensi as $d)
            @php
                $tanggal_presensi = $periode_dari;
                $total_denda = 0;
                $total_potongan_jam = 0;
                $total_tunjangan = 0;

                // Mapping jadwal untuk NIK ini dari berbagai sumber (sama seperti presensi_cetak & gaji_cetak)
                $mapJadwalByDate = $jadwal_bydate[$d['nik']] ?? [];
                $mapJadwalGrupByDate = $jadwal_grup_bydate[$d['nik']] ?? [];
                $mapJadwalByDay = $jadwal_byday[$d['nik']] ?? [];

                // Kalkulasi upah per jam
                $upah_perjam = $d['gaji_pokok'] / $generalsetting->total_jam_bulan;
            @endphp

            {{-- Proses kalkulasi denda dan potongan jam --}}
            @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                @php
                    $denda = 0;
                    $potongan_jam = 0;
                    $search = [
                        'nik' => $d['nik'],
                        'tanggal' => $tanggal_presensi,
                    ];
                    $is_libur = isLiburKaryawan($d['nik'], $tanggal_presensi);
                    $tipe_hari = $is_libur ? 2 : 1;
                    $ceklembur = ceklembur($datalembur, $search);
                    $lembur_aktual = hitungLembur($ceklembur);
                    if ($lembur_aktual > 0) {
                        $jml_jam_lembur = $lembur_aktual;
                        $jam_netto_harian = hitungJamNetto($lembur_aktual, $tipe_hari);
                    } else {
                        $jml_jam_lembur = 0;
                        $jam_netto_harian = 0;
                    }

                @endphp

                @if (isset($d[$tanggal_presensi]))
                    @if ($d[$tanggal_presensi]['status'] == 'h')
                        @php
                            $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                            $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);

                        // Jika denda sudah dikunci di database, gunakan nilai tersebut
                        $denda_dari_db = isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                            ? $d[$tanggal_presensi]['denda']
                            : null;

                        if ($denda_dari_db !== null) {
                            // Denda sudah dikunci, gunakan dari DB
                            $denda = $denda_dari_db;

                            // Potongan jam tetap dihitung dengan rumus
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
                            // Belum dikunci → gunakan rumus hitungdenda seperti biasa
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
                                $d[$tanggal_presensi]['istirahat_in'],
                                $d[$tanggal_presensi]['istirahat_out'],
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

                        // Izin: jika denda sudah dikunci, ambil dari DB, jika tidak 0
                        $denda_dari_db = isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                            ? $d[$tanggal_presensi]['denda']
                            : null;
                        $denda = $denda_dari_db !== null ? $denda_dari_db : 0;
                        @endphp
                    @elseif($d[$tanggal_presensi]['status'] == 'a')
                        @php
                            $potongan_jam = $d[$tanggal_presensi]['total_jam'];

                        // Alpa: jika denda sudah dikunci, ambil dari DB, jika tidak 0
                        $denda_dari_db = isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                            ? $d[$tanggal_presensi]['denda']
                            : null;
                        $denda = $denda_dari_db !== null ? $denda_dari_db : 0;
                        @endphp
                    @endif
                @else
                    @php
                        // Tidak ada data presensi di tanggal ini
                        // Jika hari libur, tidak ada potongan jam
                        if (empty($ceklibur)) {
                            // Bukan libur → cek jadwal berurutan (sama seperti presensi_cetak & gaji_cetak)
                            // 1) Jadwal by-date per karyawan
                            $totalJamJadwal = $mapJadwalByDate[$tanggal_presensi] ?? null;

                            // 2) Kalau kosong, cek jadwal grup by-date
                            if ($totalJamJadwal === null) {
                                $totalJamJadwal = $mapJadwalGrupByDate[$tanggal_presensi] ?? null;
                            }

                            // 3) Kalau masih kosong, cek jadwal by-day per karyawan
                            if ($totalJamJadwal === null) {
                                $nama_hari = getHari($tanggal_presensi);
                                $totalJamJadwal = $mapJadwalByDay[$nama_hari] ?? null;
                            }

                            // 4) Kalau masih kosong, cek jadwal by-day per departemen & cabang
                            if ($totalJamJadwal === null) {
                                $nama_hari = isset($nama_hari) ? $nama_hari : getHari($tanggal_presensi);
                                $keyDeptCabang = $d['kode_dept'] . '|' . $d['kode_cabang'];
                                $mapDept = $jadwal_bydept[$keyDeptCabang] ?? [];
                                $totalJamJadwal = $mapDept[$nama_hari] ?? null;
                            }

                            // Jika ada jadwal tapi tidak ada presensi sama sekali → potongan jam = total_jam jadwal
                            $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                            if ($totalJamJadwal !== null && !$is_future) {
                                $potongan_jam = is_array($totalJamJadwal) ? $totalJamJadwal['total_jam'] : $totalJamJadwal;
                            }
                        }
                    @endphp
                @endif

                @php
                    $total_denda += $denda;
                    $total_potongan_jam += $potongan_jam;
                    $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                @endphp
            @endwhile

            @php
                // Final calculations
                $jumlah_potongan_jam = ROUND($upah_perjam) * $total_potongan_jam;
                $total_potongan = ROUND($jumlah_potongan_jam) + $total_denda + $d['bpjs_kesehatan'] + $d['bpjs_tenagakerja'] + ($d['cicilan_pinjaman'] ?? 0);

            @endphp

            <!-- Slip akan ditampilkan dalam container flex untuk layout horizontal -->
        @endforeach

        <!-- Container untuk layout horizontal -->
        <div class="container">
            @foreach ($laporan_presensi as $d)
                @php
                    $tanggal_presensi = $periode_dari;
                    $total_denda = 0;
                    $total_potongan_jam = 0;
                    $total_tunjangan = 0;
                    $total_jam_lembur_aktual = 0;
                    $total_jam_netto_lembur = 0;

                    // Mapping jadwal untuk NIK ini dari berbagai sumber (sama seperti presensi_cetak & gaji_cetak)
                    $mapJadwalByDate = $jadwal_bydate[$d['nik']] ?? [];
                    $mapJadwalGrupByDate = $jadwal_grup_bydate[$d['nik']] ?? [];
                    $mapJadwalByDay = $jadwal_byday[$d['nik']] ?? [];

                    // Kalkulasi tunjangan
                    foreach ($jenis_tunjangan as $j) {
                        $total_tunjangan += $d[$j->kode_jenis_tunjangan];
                    }

                    // Kalkulasi bruto
                    $bruto = $d['gaji_pokok'] + $total_tunjangan;

                    // Kalkulasi upah per jam
                    $upah_perjam = $d['gaji_pokok'] / $generalsetting->total_jam_bulan;
                @endphp

                {{-- Proses kalkulasi denda dan potongan jam --}}
                @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                    @php
                        $denda = 0;
                        $potongan_jam = 0;
                        $search = [
                            'nik' => $d['nik'],
                            'tanggal' => $tanggal_presensi,
                        ];
                        $is_libur = isLiburKaryawan($d['nik'], $tanggal_presensi);
                        $tipe_hari = $is_libur ? 2 : 1;
                        $ceklembur = ceklibur($datalembur, $search);
                        $lembur_aktual = hitungLembur($ceklembur);
                        if ($lembur_aktual > 0) {
                            $jml_jam_lembur = $lembur_aktual;
                            $jam_netto_harian = hitungJamNetto($lembur_aktual, $tipe_hari);
                        } else {
                            $jml_jam_lembur = 0;
                            $jam_netto_harian = 0;
                        }
                    @endphp

                    @if (isset($d[$tanggal_presensi]))
                        @if ($d[$tanggal_presensi]['status'] == 'h')
                            @php
                                $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                                $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);

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
                                    $d[$tanggal_presensi]['istirahat_in'],
                                    $d[$tanggal_presensi]['istirahat_out'],
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
                            @endphp
                        @elseif($d[$tanggal_presensi]['status'] == 'a')
                            @php
                                $potongan_jam = $d[$tanggal_presensi]['total_jam'];
                            @endphp
                        @endif
                    @else
                        @php
                            // Tidak ada data presensi di tanggal ini
                            // Jika hari libur, tidak ada potongan jam
                            if (empty($ceklibur)) {
                                // Bukan libur → cek jadwal berurutan (sama seperti presensi_cetak & gaji_cetak)
                                // 1) Jadwal by-date per karyawan
                                $totalJamJadwal = $mapJadwalByDate[$tanggal_presensi] ?? null;

                                // 2) Kalau kosong, cek jadwal grup by-date
                                if ($totalJamJadwal === null) {
                                    $totalJamJadwal = $mapJadwalGrupByDate[$tanggal_presensi] ?? null;
                                }

                                // 3) Kalau masih kosong, cek jadwal by-day per karyawan
                                if ($totalJamJadwal === null) {
                                    $nama_hari = getHari($tanggal_presensi);
                                    $totalJamJadwal = $mapJadwalByDay[$nama_hari] ?? null;
                                }

                                // 4) Kalau masih kosong, cek jadwal by-day per departemen & cabang
                                if ($totalJamJadwal === null) {
                                    $nama_hari = isset($nama_hari) ? $nama_hari : getHari($tanggal_presensi);
                                    $keyDeptCabang = $d['kode_dept'] . '|' . $d['kode_cabang'];
                                    $mapDept = $jadwal_bydept[$keyDeptCabang] ?? [];
                                    $totalJamJadwal = $mapDept[$nama_hari] ?? null;
                                }

                                // Jika ada jadwal tapi tidak ada presensi sama sekali → potongan jam = total_jam jadwal
                                $is_future = strtotime($tanggal_presensi) > strtotime(date('Y-m-d'));
                                if ($totalJamJadwal !== null && !$is_future) {
                                    $potongan_jam = is_array($totalJamJadwal) ? $totalJamJadwal['total_jam'] : $totalJamJadwal;
                                }
                            }
                        @endphp
                    @endif

                    @php
                        $status_potongan_harian = isset($d[$tanggal_presensi]['status_potongan']) ? $d[$tanggal_presensi]['status_potongan'] : $generalsetting->status_potongan_jam;
                        if ($status_potongan_harian == 0) {
                            $potongan_jam = 0;
                        }
                        $total_denda += $denda;
                        $total_potongan_jam += $potongan_jam;
                        $total_jam_lembur_aktual += $jml_jam_lembur;
                        $total_jam_netto_lembur += $jam_netto_harian;
                        $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                    @endphp
                @endwhile

                @php
                    // Final calculations
                    if ($total_potongan_jam > $generalsetting->total_jam_bulan) {
                        $total_potongan_jam = $generalsetting->total_jam_bulan;
                    }
                    $jumlah_potongan_jam = ROUND($upah_perjam) * $total_potongan_jam;
                    $total_potongan = ROUND($jumlah_potongan_jam) + $total_denda + $d['bpjs_kesehatan'] + $d['bpjs_tenagakerja'] + ($d['cicilan_pinjaman'] ?? 0);
                    $gaji_bersih = $d['gaji_pokok'] + $total_tunjangan - $total_potongan + $d['penambah'] - $d['pengurang'];
                @endphp

                <div class="slip-struk">
                    <!-- Header -->
                    <div class="header">
                        <div class="company-name">{{ $generalsetting->nama_perusahaan }}</div>
                        <div class="slip-title">SLIP GAJI</div>
                        <div class="periode">{{ date('d/m/Y', strtotime($periode_dari)) }} -
                            {{ date('d/m/Y', strtotime($periode_sampai)) }}</div>
                    </div>

                    <!-- Employee Info -->
                    <div class="employee-section">
                        <div class="row">
                            <span class="label">NIK:</span>
                            <span class="value">{{ $d['nik'] }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Nama:</span>
                            <span class="value">{{ $d['nama_karyawan'] }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Jabatan:</span>
                            <span class="value">{{ $d['nama_jabatan'] }}</span>
                        </div>
                        <div class="row">
                            <span class="label">Dept:</span>
                            <span class="value">{{ $d['kode_dept'] }}</span>
                        </div>
                    </div>

                    <!-- Work Summary -->
                    <div class="work-info">
                        {{ $generalsetting->total_jam_bulan }} jam | Rp {{ number_format($upah_perjam, 0, ',', '.') }}/jam |
                        {{ number_format($total_potongan_jam, 1) }} jam potong
                    </div>

                    <!-- Penghasilan -->
                    <div class="section-title earning">PENGHASILAN</div>
                    <div class="row">
                        <span>Gaji Pokok</span>
                        <span class="currency">{{ number_format($d['gaji_pokok'], 0, ',', '.') }}</span>
                    </div>
                    @foreach ($jenis_tunjangan as $j)
                        @if ($d[$j->kode_jenis_tunjangan] > 0)
                            <div class="row">
                                <span>{{ $j->jenis_tunjangan }}</span>
                                <span class="currency">{{ number_format($d[$j->kode_jenis_tunjangan], 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endforeach
                        @if ($total_jam_netto_lembur > 0)
                            <div class="row">
                                <span>
                                    <a href="{{ route('laporan.lemburdetail', [$d['nik'], $periode_dari, $periode_sampai]) }}" target="_blank" style="color: #024a75; text-decoration: underline;">
                                        Lembur ({{ formatAngkaDesimal($total_jam_netto_lembur) }}) jam 
                                    </a>
                                </span>
                            <span class="currency">
                                @php
                                    $upah_perjam_lembur = ($d['gaji_pokok'] + $total_tunjangan) / $generalsetting->total_jam_bulan;
                                    $upah_lembur = ROUND($upah_perjam_lembur) * $total_jam_netto_lembur;
                                @endphp
                                {{ formatAngka($upah_lembur) }}
                            </span>
                        </div>
                    @else
                        @php
                            $upah_lembur = 0;
                        @endphp
                    @endif
                    <div class="row" style="font-weight: bold; border-top: 1px dotted #333; padding-top: 2px;">
                        <span>Sub Total</span>
                        @php
                            $bruto_total = $bruto + ROUND($upah_lembur);
                        @endphp
                        <span class="currency">{{ number_format($bruto_total, 0, ',', '.') }}</span>
                    </div>

                    <!-- Potongan -->
                    <div class="section-title deduction">POTONGAN</div>
                    @if ($total_denda > 0)
                        <div class="row">
                            <span>Denda</span>
                            <span class="currency">{{ number_format($total_denda, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($jumlah_potongan_jam > 0)
                        <div class="row">
                            <span>Pot. Jam ({{ number_format($total_potongan_jam, 2) }})</span>
                            <span class="currency">{{ number_format($jumlah_potongan_jam, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($d['bpjs_kesehatan'] > 0)
                        <div class="row">
                            <span>BPJS Kes</span>
                            <span class="currency">{{ number_format($d['bpjs_kesehatan'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($d['bpjs_tenagakerja'] > 0)
                        <div class="row">
                            <span>BPJS TK</span>
                            <span class="currency">{{ number_format($d['bpjs_tenagakerja'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if (($d['cicilan_pinjaman'] ?? 0) > 0)
                        <div class="row">
                            <span>Pinjaman</span>
                            <span class="currency">{{ number_format($d['cicilan_pinjaman'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="row" style="font-weight: bold; border-top: 1px dotted #333; padding-top: 2px;">
                        <span>Sub Total</span>
                        <span class="currency">{{ number_format($total_potongan, 0, ',', '.') }}</span>
                    </div>

                    <!-- Penyesuaian -->
                    @if ($d['penambah'] > 0 || $d['pengurang'] > 0)
                        <div class="section-title adjustment">PENYESUAIAN</div>
                        @if ($d['penambah'] > 0)
                            <div class="row">
                                <span>Penambah</span>
                                <span class="currency">{{ number_format($d['penambah'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if ($d['pengurang'] > 0)
                            <div class="row">
                                <span>Pengurang</span>
                                <span class="currency">{{ number_format($d['pengurang'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endif

                    <!-- Total -->
                    <div class="total-section">
                        <div class="net-salary">
                            <span>GAJI BERSIH</span>
                            @php
                                $gaji_bersih =
                                    $d['gaji_pokok'] + $total_tunjangan - $total_potongan + $d['penambah'] - $d['pengurang'] + ROUND($upah_lembur);
                            @endphp
                            <span class="currency">{{ number_format($gaji_bersih, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        Dicetak: {{ date('d/m/Y H:i') }}<br>
                        Sistem Payroll v1.0
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
