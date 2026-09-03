<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Jadwal {{ date('Y-m-d H:i:s') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/report.css') }}">
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header h4 {
            line-height: 1.2;
            margin: 0 0 5px 0;
        }

        /* Responsive Table Container */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            max-height: 75vh;
            overflow-y: auto;
            position: relative;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .datatable3 {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            min-width: 100%;
        }

        .datatable3 th,
        .datatable3 td {
            border: 1px solid #333;
            padding: 5px;
            vertical-align: middle;
            text-align: center;
        }

        .datatable3 th {
            background-color: #024a75;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 20;
            height: 40px;
            box-sizing: border-box;
            box-shadow: inset 0 0 0 1px #333;
            border: none;
        }
        
        .datatable3 thead tr:nth-child(2) th {
            top: 40px;
        }

        /* Sticky Column Styles (Screen Only) */
        @media screen {
            .sticky-col {
                position: sticky;
                background-color: #fff;
                z-index: 10;
                box-shadow: inset 0 0 0 1px #333;
                border: none !important;
            }
            
            .datatable3 th.sticky-col {
                z-index: 30;
                background-color: #024a75;
            }

            .first-col { left: 0; width: 30px; }
            .second-col { left: 30px; width: 70px; }
            .third-col { left: 100px; width: 150px; }
        }

        @media print {
            .table-responsive {
                overflow: visible;
                border: none;
            }
            .datatable3 {
                width: 100%;
                table-layout: auto;
            }
            .sticky-col {
                position: static !important;
            }
        }
    </style>
</head>

<body>

    <div class="header" style="margin-bottom: 10px">
        <table>
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
                        LAPORAN JADWAL KARYAWAN
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

    <div class="table-responsive">
        <table class="datatable3">
            <thead>
                <tr>
                    <th rowspan="2" class="sticky-col first-col">No</th>
                    <th rowspan="2" class="sticky-col second-col">NIK</th>
                    <th rowspan="2" class="sticky-col third-col">Nama Karyawan</th>
                    <th colspan="{{ $jmlhari }}">Tanggal</th>
                </tr>
                <tr>
                    @php
                        $tanggal_loop = $periode_dari;
                    @endphp
                    @while (strtotime($tanggal_loop) <= strtotime($periode_sampai))
                        <th style="width: 80px">
                            {{ date('d', strtotime($tanggal_loop)) }}<br>
                            {{ getHari(date('Y-m-d', strtotime($tanggal_loop))) }}
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
                        <td class="sticky-col first-col">{{ $loop->iteration }}</td>
                        <td class="sticky-col second-col">{{ $d->nik }}</td>
                        <td class="sticky-col third-col" style="text-align: left">{{ $d->nama_karyawan }}</td>
                        
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
                                }
                                
                                $bgcolor = '';
                                $content = '';
                                
                                if (!empty($ceklibur)) {
                                    $bgcolor = 'green';
                                    $content = '<span style="color:white">LIBUR</span>';
                                } elseif ($nama_hari == 'Minggu') {
                                    $bgcolor = 'orange';
                                }
                                
                                if ($jadwal_info) {
                                    $content = $jadwal_info['nama_jam_kerja'] . '<br><small>' . 
                                               date('H:i', strtotime($jadwal_info['jam_masuk'])) . '-' . 
                                               date('H:i', strtotime($jadwal_info['jam_pulang'])) . '</small>';
                                    if (empty($bgcolor) && !empty($jadwal_info['color'])) {
                                        $bgcolor = $jadwal_info['color'];
                                    }
                                }
                            @endphp
                            <td style="background-color: {{ $bgcolor }}; color: {{ !empty($bgcolor) && $bgcolor != 'white' ? 'white' : 'black' }}">
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

</body>

</html>
