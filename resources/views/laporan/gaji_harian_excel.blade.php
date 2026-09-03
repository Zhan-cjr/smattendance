<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Gaji Harian</title>
</head>

<body>
    <table class="datatable3" style="width: 100%; border-collapse: collapse; border: 1px solid #000000">
        <thead>
            <tr>
                <td colspan="10">
                    <h4 style="line-height: 20px; margin-bottom: 5px; font-weight: bold; font-size: 14px">
                        LAPORAN GAJI (HARIAN)<br>
                        {{ $generalsetting->nama_perusahaan }}<br>
                        PERIODE {{ date('d-m-Y', strtotime($periode_dari)) }} - {{ date('d-m-Y', strtotime($periode_sampai)) }}
                    </h4>
                    <span style="font-style: italic; font-size: 12px">{{ $generalsetting->alamat }}</span><br>
                    <span style="font-style: italic; font-size: 12px">{{ $generalsetting->telepon }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="10"></td>
            </tr>
            <tr>
                <th style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">No</th>
                <th style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Nik</th>
                <th style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Nama Karyawan</th>
                <th style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Jabatan</th>
                <th style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Dept</th>
                <th style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Cabang</th>
                <th style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Rate Harian</th>
                <th style="border: 1px solid #000000; background-color: #024a75; color: white; vertical-align: middle;">Hari Hadir</th>
                <th style="border: 1px solid #000000; background-color: #007148; color: white; vertical-align: middle;">Total Upah</th>
                <th style="border: 1px solid #000000; background-color: red; color: white; vertical-align: middle;">Denda</th>
                <th style="border: 1px solid #000000; background-color: #007148; color: white; vertical-align: middle;">Gaji Bersih</th>
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

                    $total_upah_all += $total_upah;
                    $total_all_denda += $total_denda;
                    $total_gaji_bersih_all += $gaji_bersih;
                @endphp

                <tr>
                    <td style="border: 1px solid #000000; vertical-align: middle;">{{ $loop->iteration }}</td>
                    <td style="border: 1px solid #000000; vertical-align: middle;">'{{ $d['nik_show'] ?? $d['nik'] }}</td>
                    <td style="border: 1px solid #000000; vertical-align: middle;">{{ $d['nama_karyawan'] }}</td>
                    <td style="border: 1px solid #000000; vertical-align: middle;">{{ $d['nama_jabatan'] }}</td>
                    <td style="border: 1px solid #000000; vertical-align: middle;">{{ $d['kode_dept'] }}</td>
                    <td style="border: 1px solid #000000; vertical-align: middle;">{{ $d['kode_cabang'] }}</td>
                    <td style="border: 1px solid #000000; text-align: right; vertical-align: middle;">{{ $d['gaji_pokok'] }}</td>
                    <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $hari_hadir }}</td>
                    <td style="border: 1px solid #000000; text-align: right; vertical-align: middle; font-weight: bold;">{{ $total_upah }}</td>
                    <td style="border: 1px solid #000000; text-align: right; vertical-align: middle;">{{ $total_denda }}</td>
                    <td style="border: 1px solid #000000; text-align: right; vertical-align: middle; font-weight: bold;">{{ $gaji_bersih }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="8" style="border: 1px solid #000000; vertical-align: middle; text-align: center; font-weight: bold;">TOTAL</th>
                <th style="border: 1px solid #000000; text-align: right; vertical-align: middle; font-weight: bold;">{{ $total_upah_all }}</th>
                <th style="border: 1px solid #000000; text-align: right; vertical-align: middle; font-weight: bold;">{{ $total_all_denda }}</th>
                <th style="border: 1px solid #000000; text-align: right; vertical-align: middle; font-weight: bold;">{{ $total_gaji_bersih_all }}</th>
            </tr>
        </tbody>
    </table>
</body>

</html>
