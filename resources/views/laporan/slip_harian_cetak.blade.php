<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Slip Gaji Harian {{ date('Y-m-d H:i:s') }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 15px;
            font-size: 11px;
            line-height: 1.3;
            background-color: #f5f5f5;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: flex-start;
        }

        .slip-struk {
            width: 280px;
            background: white;
            border: 1px solid #333;
            border-radius: 3px;
            padding: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            page-break-inside: avoid;
            margin-bottom: 15px;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #333;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .company-name {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .slip-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 2px;
        }

        .periode {
            font-size: 9px;
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
            font-size: 10px;
        }

        .label {
            font-weight: bold;
        }

        .value {
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            font-size: 10px;
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
            font-size: 11px;
            padding: 4px;
            background: #f8f9fa;
            border: 1px solid #333;
        }

        .work-info {
            font-size: 9px;
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
            font-size: 8px;
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
</head>

<body>
    <div class="container">
        @foreach ($laporan_presensi as $d)
            @php
                $tanggal_presensi = $periode_dari;
                $hari_hadir = 0;
                $total_denda = 0;

                while (strtotime($tanggal_presensi) <= strtotime($periode_sampai)) {
                    if (isset($d[$tanggal_presensi])) {
                        if ($d[$tanggal_presensi]['status'] == 'h') {
                            $hari_hadir++;
                            
                            $denda_dari_db = isset($d[$tanggal_presensi]['denda']) && $d[$tanggal_presensi]['denda'] !== null
                                ? $d[$tanggal_presensi]['denda']
                                : null;
                            
                            if ($denda_dari_db !== null) {
                                $total_denda += $denda_dari_db;
                            } else {
                                $jam_masuk = $tanggal_presensi . ' ' . $d[$tanggal_presensi]['jam_masuk'];
                                $terlambat = hitungjamterlambat($d[$tanggal_presensi]['jam_in'], $jam_masuk);
                                if ($terlambat != null && $terlambat['desimal_terlambat'] < 1) {
                                    $total_denda += hitungdenda($denda_list, $terlambat['menitterlambat']);
                                }
                            }
                        }
                    }
                    $tanggal_presensi = date('Y-m-d', strtotime('+1 day', strtotime($tanggal_presensi)));
                }

                $total_upah = $d['gaji_pokok'] * $hari_hadir;
                $gaji_bersih = $total_upah - $total_denda;
            @endphp

            <div class="slip-struk">
                <div class="header">
                    <div class="company-name">{{ $generalsetting->nama_perusahaan }}</div>
                    <div class="slip-title">SLIP GAJI (HARIAN)</div>
                    <div class="periode">{{ date('d/m/Y', strtotime($periode_dari)) }} - {{ date('d/m/Y', strtotime($periode_sampai)) }}</div>
                </div>

                <div class="employee-section">
                    <div class="row"><span class="label">NIK:</span><span class="value">{{ $d['nik_show'] ?? $d['nik'] }}</span></div>
                    <div class="row"><span class="label">Nama:</span><span class="value">{{ $d['nama_karyawan'] }}</span></div>
                    <div class="row"><span class="label">Jabatan:</span><span class="value">{{ $d['nama_jabatan'] }}</span></div>
                    <div class="row"><span class="label">Dept:</span><span class="value">{{ $d['kode_dept'] }}</span></div>
                </div>

                <div class="work-info">
                   Rate: Rp {{ number_format($d['gaji_pokok'], 0, ',', '.') }}/hari | {{ $hari_hadir }} hari hadir
                </div>

                <div class="section-title earning">PENGHASILAN</div>
                <div class="row"><span>Upah Pokok</span><span class="currency">{{ number_format($total_upah, 0, ',', '.') }}</span></div>
                <div class="row" style="font-weight: bold; border-top: 1px dotted #333; padding-top: 2px;"><span>Sub Total</span><span class="currency">{{ number_format($total_upah, 0, ',', '.') }}</span></div>

                @if ($total_denda > 0)
                    <div class="section-title deduction">POTONGAN</div>
                    <div class="row"><span>Denda</span><span class="currency">{{ number_format($total_denda, 0, ',', '.') }}</span></div>
                    <div class="row" style="font-weight: bold; border-top: 1px dotted #333; padding-top: 2px;"><span>Sub Total</span><span class="currency">{{ number_format($total_denda, 0, ',', '.') }}</span></div>
                @endif

                <div class="total-section">
                    <div class="net-salary"><span>GAJI BERSIH</span><span class="currency">{{ number_format($gaji_bersih, 0, ',', '.') }}</span></div>
                </div>

                <div class="footer">Dicetak: {{ date('d/m/Y H:i') }}<br>Sistem Payroll v1.0</div>
            </div>
        @endforeach
    </div>
</body>

</html>
