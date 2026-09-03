<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Gaji Harian {{ date('Y-m-d H:i:s') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/report.css') }}">
    <style>
        p {
            line-height: 1rem !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    </style>
</head>

<body>
    <div class="header" style="margin-bottom: 10px">
        <table>
            <tr>
                <td>
                    @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                        <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo Perusahaan" style="max-width: 100px;">
                    @else
                        <img src="https://placehold.co/100x100?text=Logo" alt="Logo Default" style="max-width: 100px;">
                    @endif
                </td>
                <td>
                    <h4 style="line-height: 20px; margin-bottom: 5px">
                        LAPORAN GAJI (HARIAN)
                        <br>
                        {{ $generalsetting->nama_perusahaan }}
                        <br>
                        PERIODE {{ date('d-m-Y', strtotime($periode_dari)) }} -
                        {{ date('d-m-Y', strtotime($periode_sampai)) }}
                    </h4>
                    <span style="font-style: italic;">{{ $generalsetting->alamat }}</span><br>
                    <span style="font-style: italic;">{{ $generalsetting->telepon }}</span>
                </td>
            </tr>
        </table>
    </div>
    <div class="content">
        <table class="datatable3">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nik</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Dept</th>
                    <th>Rate Harian</th>
                    <th>Hari Hadir</th>
                    <th style="background: rgb(0, 113, 72); color:white">Total Upah</th>
                    <th style="background:red; color:white">Denda</th>
                    <th style="background:rgb(0, 113, 72); color:white">Gaji Bersih</th>
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
                    @endphp

                    @while (strtotime($tanggal_presensi) <= strtotime($periode_sampai))
                        @php
                            $denda = 0;
                        @endphp

                        @if (isset($d[$tanggal_presensi]))
                            @php
                                if ($d[$tanggal_presensi]['status'] == 'h') {
                                    $hari_hadir++;
                                }

                                $denda_dari_db = isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                    ? $d[$tanggal_presensi]['denda']
                                    : null;

                                if ($denda_dari_db !== null) {
                                    $denda = $denda_dari_db;
                                } else if ($d[$tanggal_presensi]['status'] == 'h') {
                                    $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                                    $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);
                                    if ($terlambat != null && $terlambat['desimal_terlambat'] < 1) {
                                        $denda = hitungdenda($denda_list, $terlambat['menitterlambat']);
                                    }
                                }
                            @endphp
                        @endif

                        @php
                            $total_denda += $denda;
                            $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                        @endphp
                    @endwhile

                    @php
                        $total_upah = $d['gaji_pokok'] * $hari_hadir;
                        $gaji_bersih = $total_upah - $total_denda;

                        // Totals
                        $total_upah_all += $total_upah;
                        $total_all_denda += $total_denda;
                        $total_gaji_bersih_all += $gaji_bersih;
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>'{{ $d['nik_show'] ?? $d['nik'] }}</td>
                        <td>{{ $d['nama_karyawan'] }}</td>
                        <td>{{ $d['nama_jabatan'] }}</td>
                        <td>{{ $d['kode_dept'] }}</td>
                        <td style="text-align: right">{{ formatAngka($d['gaji_pokok']) }}</td>
                        <td style="text-align: center">{{ $hari_hadir }}</td>
                        <td style="text-align: right; font-weight: bold">{{ formatAngka($total_upah) }}</td>
                        <td style="text-align: right">{{ formatAngka($total_denda) }}</td>
                        <td style="text-align: right; font-weight: bold">{{ formatAngka($gaji_bersih) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7">TOTAL</th>
                    <th style="text-align: right">{{ formatAngka($total_upah_all) }}</th>
                    <th style="text-align: right">{{ formatAngka($total_all_denda) }}</th>
                    <th style="text-align: right">{{ formatAngka($total_gaji_bersih_all) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
