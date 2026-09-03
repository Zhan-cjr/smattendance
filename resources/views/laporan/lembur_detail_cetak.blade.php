<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Lembur - {{ $karyawan->nama_karyawan }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/report.css') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            margin: 20px;
        }
        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #024a75;
            padding-bottom: 10px;
        }
        .header table { width: 100%; }
        .header h3 { margin: 0; color: #024a75; }
        .info-table { margin-bottom: 20px; font-size: 14px; }
        .info-table td { padding: 4px 8px; }
        .datatable3 {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .datatable3 th {
            background-color: #024a75;
            color: #fff;
            padding: 12px 8px;
            text-transform: uppercase;
        }
        .datatable3 td {
            border: 1px solid #ddd;
            padding: 10px 8px;
        }
        .datatable3 tr:nth-child(even) { background-color: #f9f9f9; }
        .total-row {
            font-weight: bold;
            background-color: #eee !important;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .badge-kerja { background: #e3f2fd; color: #1976d2; }
        .badge-libur { background: #fff3e0; color: #f57c00; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%">
            <tr>
                <td style="width: 100px; vertical-align: top;">
                    @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                        <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo" style="max-width: 100px;">
                    @else
                        <img src="https://placehold.co/100x100?text=Logo" alt="Logo" style="max-width: 100px;">
                    @endif
                </td>
                <td style="vertical-align: top; padding-left: 15px;">
                    <h3 style="margin: 0; color: #024a75;">LAPORAN DETAIL LEMBUR</h3>
                    <div style="font-weight: bold; font-size: 16px;">{{ $generalsetting->nama_perusahaan }}</div>
                    <div style="font-size: 12px; font-style: italic; color: #666;">{{ $generalsetting->alamat }}</div>
                    <div style="font-size: 12px; font-style: italic; color: #666;">{{ $generalsetting->telepon }}</div>
                </td>
            </tr>
        </table>
        <div style="text-align: right; margin-top: 10px;" class="no-print">
            <button onclick="window.print()" style="padding: 8px 16px; background: #024a75; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                <i class="ti ti-printer"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <div style="display: flex; align-items: flex-start; margin-bottom: 20px;">
        <div style="width: 110px; margin-right: 20px;">
            @if ($karyawan->foto && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                <img src="{{ getfotoKaryawan($karyawan->foto) }}" alt="Foto Karyawan" 
                     style="width: 100px; height: 120px; object-fit: cover; border: 2px solid #024a75; border-radius: 4px;">
            @else
                <img src="https://placehold.co/100x120?text=No+Photo" alt="No Photo" 
                     style="width: 100px; height: 120px; object-fit: cover; border: 2px solid #ddd; border-radius: 4px;">
            @endif
        </div>
        <div style="flex-grow: 1;">
            <table class="info-table" style="margin-bottom: 0;">
                <tr>
                    <td><strong>NIK</strong></td><td>: {{ $karyawan->nik_show ?? $karyawan->nik }}</td>
                    <td style="width: 30px"></td>
                    <td><strong>Jabatan</strong></td><td>: {{ $karyawan->nama_jabatan }}</td>
                </tr>
                <tr>
                    <td><strong>Nama</strong></td><td>: {{ $karyawan->nama_karyawan }}</td>
                    <td></td>
                    <td><strong>Departemen</strong></td><td>: {{ $karyawan->nama_dept }}</td>
                </tr>
                <tr>
                    <td><strong>Periode</strong></td><td>: {{ date('d M Y', strtotime($dari)) }} - {{ date('d M Y', strtotime($sampai)) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <table class="datatable3">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Hari</th>
                <th>Status</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
                <th>Aktual (Jam)</th>
                <th>Detail Indeks</th>
                <th>Netto (Jam)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_aktual = 0;
                $total_netto = 0;
                $no = 1;
                $curr_date = $dari;
            @endphp
            @while (strtotime($curr_date) <= strtotime($sampai))
                @php
                    $search = ['nik' => $karyawan->nik, 'tanggal' => $curr_date];
                    $ceklembur = ceklibur($datalembur, $search);
                    $jam_aktual = hitungLembur($ceklembur);
                    
                    $is_libur = isLiburKaryawan($karyawan->nik, $curr_date);
                    $tipe_hari = $is_libur ? 2 : 1; 
                    
                    $detail_netto = [];
                    $jam_netto = 0;
                    
                    if ($jam_aktual > 0) {
                        // Logic calculation mirrors hitungJamNetto helper but with breakdown
                        $rules = DB::table('lembur_aturan')
                            ->where('tipe_hari', $tipe_hari)
                            ->orderBy('jam_dari', 'asc')
                            ->get();

                        $remaining_jam = $jam_aktual;
                        foreach ($rules as $rule) {
                            $start = $rule->jam_dari; // Use absolute start from DB
                            $end = $rule->jam_sampai ?: 999;
                            
                            $jam_di_tier_ini = max(0, min($jam_aktual, $end) - $start);
                            
                            if ($jam_di_tier_ini > 0) {
                                $weighted = $jam_di_tier_ini * $rule->faktor;
                                $jam_netto += $weighted;
                                $detail_netto[] = formatAngkaDesimal($jam_di_tier_ini) . "j x " . number_format($rule->faktor, 1);
                            }
                        }
                        
                        $total_aktual += $jam_aktual;
                        $total_netto += $jam_netto;
                    }
                @endphp

                @if ($jam_aktual > 0)
                    <tr>
                        <td style="text-align: center">{{ $no++ }}</td>
                        <td style="text-align: center">{{ date('d/m/Y', strtotime($curr_date)) }}</td>
                        <td style="text-align: center">{{ getHari($curr_date) }}</td>
                        <td style="text-align: center">
                            <span class="badge {{ $is_libur ? 'badge-libur' : 'badge-kerja' }}">
                                {{ $is_libur ? 'LIBUR' : 'KERJA' }}
                            </span>
                        </td>
                        <td style="text-align: center">{{ $ceklembur[0]['lembur_mulai'] }}</td>
                        <td style="text-align: center">{{ $ceklembur[0]['lembur_selesai'] }}</td>
                        <td style="text-align: center">{{ formatAngkaDesimal($jam_aktual) }}</td>
                        <td style="font-size: 11px">
                            {{ implode(' + ', $detail_netto) }}
                        </td>
                        <td style="text-align: center; font-weight: bold">{{ formatAngkaDesimal($jam_netto) }}</td>
                    </tr>
                @endif

                @php
                    $curr_date = date('Y-m-d', strtotime('+1 day', strtotime($curr_date)));
                @endphp
            @endwhile
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" style="text-align: right">TOTAL</td>
                <td style="text-align: center">{{ formatAngkaDesimal($total_aktual) }}</td>
                <td></td>
                <td style="text-align: center">{{ formatAngkaDesimal($total_netto) }}</td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; font-size: 11px; color: #666;">
        * Jam Aktual adalah durasi lembur yang telah disetujui (Capped by approval).<br>
        * Jam Netto adalah hasil perkalian Jam Aktual dengan Indeks Faktor sesuai aturan yang berlaku.
    </div>
</body>
</html>
