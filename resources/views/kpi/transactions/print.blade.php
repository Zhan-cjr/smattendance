<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan KPI - {{ $kpi_employee->karyawan->nama_karyawan }}</title>
    <style>
        @page { size: A4; margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 5px 0; text-transform: uppercase; }
        .header h4 { margin: 5px 0; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 5px; vertical-align: top; }
        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .kpi-table th, .kpi-table td { border: 1px solid #000; padding: 8px; text-align: center; }
        .kpi-table th { background-color: #f0f0f0; }
        .kpi-table td.text-left { text-align: left; }
        .kpi-table td.text-right { text-align: right; }
        .signature-table { width: 100%; margin-top: 50px; page-break-inside: avoid; }
        .signature-table td { text-align: center; vertical-align: top; width: 33%; }
        .signature-space { height: 80px; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px; display: none;"> 
        <h2>{{ 'PT. SMATT ID' }}</h2>
        <h4>LAPORAN KEY PERFORMANCE INDICATOR (KPI)</h4>
        
         @php
            $startDate = \Carbon\Carbon::parse($kpi_employee->period->start_date);
            $endDate = \Carbon\Carbon::parse($kpi_employee->period->end_date);
         @endphp
        <span>Periode: {{ $startDate->translatedFormat('d F Y') }} - {{ $endDate->translatedFormat('d F Y') }}</span>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>NIK</strong></td>
            <td width="2%">:</td>
            <td width="33%">{{ $kpi_employee->karyawan->nik }}</td>
            <td width="15%"><strong>Departemen</strong></td>
            <td width="2%">:</td>
            <td>{{ $kpi_employee->karyawan->departemen->nama_dept ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Nama</strong></td>
            <td>:</td>
            <td>{{ $kpi_employee->karyawan->nama_karyawan }}</td>
            <td><strong>Jabatan</strong></td>
            <td>:</td>
            <td>{{ $kpi_employee->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
        </tr>
    </table>

    <table class="kpi-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Indikator Kinerja</th>
                <th width="10%">Satuan</th>
                <th width="10%">Target</th>
                <th width="10%">Bobot</th>
                <th width="10%">Realisasi</th>
                <th width="10%">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kpi_employee->details as $detail)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td class="text-left">
                    <strong>{{ $detail->indicator->nama_indikator }}</strong><br>
                    <small style="color: #666;">{{ $detail->indicator->deskripsi }}</small>
                </td>
                <td>{{ $detail->indicator->satuan }}</td>
                <td>
                    {{ $detail->target }}
                    <br><small>({{ strtoupper($detail->indicator->jenis_target) }})</small>
                </td>
                <td>{{ $detail->bobot }}</td>
                <td>{{ $detail->realisasi }}</td>
                <td class="text-right">{{ number_format($detail->skor, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
             <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="6" class="text-right">TOTAL NILAI</td>
                <td class="text-right">{{ number_format($kpi_employee->total_nilai, 2) }}</td>
            </tr>
             <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="6" class="text-right">GRADE</td>
                <td class="text-right">{{ $kpi_employee->grade ?? '-' }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                Dibuat Oleh,<br>
                Karyawan<br>
                <div class="signature-space"></div>
                <u>{{ $kpi_employee->karyawan->nama_karyawan }}</u>
            </td>
            <td>
                Diperiksa Oleh,<br>
                Atasan Langsung<br>
                <div class="signature-space"></div>
                ____________________
            </td>
            <td>
                Disetujui Oleh,<br>
                HRD / Direktur<br>
                <div class="signature-space"></div>
                ____________________
            </td>
        </tr>
    </table>
</body>
</html>
