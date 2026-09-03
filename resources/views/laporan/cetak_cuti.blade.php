<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Cuti</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
        }

        .tabeldatakaryawan {
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .tabeldatakaryawan tr td {
            padding: 5px;
        }

        .tabelpresensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabelpresensi th {
            border: 1px solid #131212;
            padding: 8px;
            background-color: #024a75;
            color: white;
            font-size: 10px;
            font-weight: bold;
        }

        .tabelpresensi td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 12px;
        }
        
        /* Ensure table header repeats */
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
    </style>
</head>

<body>

    <table style="width: 100%">
        <tr>
            <td style="width: 70px; padding-right: 10px;">
                @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                    <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo Perusahaan" style="max-width: 100px;">
                @else
                    <img src="https://placehold.co/100x100?text=Logo" alt="Logo Default" style="max-width: 100px;">
                @endif
            </td>
            <td>
                <h4 style="line-height: 20px; margin-bottom: 5px">
                    LAPORAN CUTI KARYAWAN
                    <br>
                    {{ textUpperCase($generalsetting->nama_perusahaan) }}
                    <br>
                    PERIODE TAHUN {{ $tahun }}
                </h4>
                <span style="font-style: italic;">{{ $generalsetting->alamat }}</span><br>
                <span style="font-style: italic;">{{ $generalsetting->telepon }}</span>
            </td>
        </tr>
    </table>

    <table class="tabeldatakaryawan">
        <tr>
            <td>Cabang</td>
            <td>:</td>
            <td>{{ textUpperCase($namacabang) }}</td>
        </tr>
        <tr>
            <td>Departemen</td>
            <td>:</td>
            <td>{{ textUpperCase($namadept) }}</td>
        </tr>
        <tr>
            <td>Jenis Cuti</td>
            <td>:</td>
            <td>{{ textUpperCase($jenis_cuti) }}</td>
        </tr>
    </table>

    <table class="tabelpresensi">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">NIK</th>
                <th rowspan="2">Nama Karyawan</th>
                <th colspan="12">Bulan</th>
                <th rowspan="2">Total<br>Ambil</th>
                <th rowspan="2">Jatah<br>Cuti</th>
                <th rowspan="2">Sisa<br>Cuti</th>
            </tr>
            <tr>
                <th>Jan</th>
                <th>Feb</th>
                <th>Mar</th>
                <th>Apr</th>
                <th>Mei</th>
                <th>Jun</th>
                <th>Jul</th>
                <th>Agu</th>
                <th>Sep</th>
                <th>Okt</th>
                <th>Nov</th>
                <th>Des</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekap_cuti as $nik => $d)
                <tr>
                    <td style="text-align: center">{{ $loop->iteration }}</td>
                    <td style="text-align: center">'{{ $nik }}</td>
                    <td>{{ $d['nama'] }}</td>
                    @for ($i = 1; $i <= 12; $i++)
                        <td style="text-align: center; {{ $d['bulan'][$i] > 0 ? 'background-color: #f7d7da; font-weight: bold;' : '' }}">
                            {{ $d['bulan'][$i] > 0 ? $d['bulan'][$i] : '' }}
                        </td>
                    @endfor
                    <td style="text-align: center; font-weight: bold">{{ $d['total_ambil'] }}</td>
                    
                    <!-- Logic for Sisa Cuti -->
                    @php
                        $jatah = '-';
                        $sisa = '-';
                        if (!empty($master_cuti)) {
                            $jatah = $master_cuti->jumlah_hari;
                            if ($master_cuti->kode_cuti == 'C01') {
                                    // Annual Leave logic often calculated per year.
                                $sisa = $master_cuti->jumlah_hari - $d['total_ambil'];
                            } else {
                                    // Other leaves might be per event, so "Sisa" per year might just be max - used
                                $sisa = $master_cuti->jumlah_hari - $d['total_ambil'];
                            }
                            
                            // Handling negative sisa if overrides?
                            if($sisa < 0) $sisa = 0; 
                        }
                    @endphp

                    <td style="text-align: center">{{ $jatah }}</td>
                    <td style="text-align: center">{{ $sisa }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table width="100%" style="margin-top: 100px; page-break-inside: avoid;">
        <tr>
            <td style="text-align: center; vertical-align: bottom" height="100px">
                <u>Manager HRD</u><br>
                <i><b>HRD Manager</b></i>
            </td>
            <td style="text-align: center; vertical-align: bottom">
                <u>Direktur</u><br>
                <i><b>Direktur</b></i>
            </td>
        </tr>
    </table>

</body>
</html>
