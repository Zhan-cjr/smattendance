<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Surat Peringatan {{ $pelanggaran->no_sp }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline; /* Optional, usually SP letters have underlined title or bold */
            margin-bottom: 5px;
        }

        .nomor {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .recipient {
            margin-bottom: 20px;
        }

        .content {
            text-align: justify;
        }

        .list-item {
            margin-left: 20px;
            margin-bottom: 10px;
        }

        .signature-section {
            margin-top: 50px;
            float: right;
            text-align: center;
            width: 200px;
        }

        .signature-space {
            height: 80px;
        }

        /* Clearfix for float */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    @php
        $sp_names = [
            'SP1' => 'PERTAMA',
            'SP2' => 'KEDUA',
            'SP3' => 'KETIGA'
        ];
        $sp_name = $sp_names[$pelanggaran->jenis_sp] ?? $pelanggaran->jenis_sp;

        $next_sp = '';
        if ($pelanggaran->jenis_sp == 'SP1') $next_sp = 'Surat Peringatan Kedua';
        elseif ($pelanggaran->jenis_sp == 'SP2') $next_sp = 'Surat Peringatan Ketiga';
        else $next_sp = 'Pemutusan Hubungan Kerja';
    @endphp

    <div class="header">
        <div class="title">SURAT PERINGATAN {{ $sp_name }}</div>
        <div class="nomor">Nomor: {{ $pelanggaran->no_sp }}</div>
    </div>

    <div class="recipient">
        Kepada Yth,<br>
        <b>{{ $pelanggaran->nama_karyawan }}</b><br>
        {{ $pelanggaran->alamat ?? 'Di Tempat' }}
    </div>

    <div class="content">
        <p>Dengan hormat,</p>

        <p>
            Sehubungan dengan hasil evaluasi kinerja dan kedisiplinan yang dilakukan Departemen {{ $pelanggaran->nama_dept }}, kami memberikan surat peringatan ini sebagai tindak lanjut dari ketidakpatuhan Saudara/i <b>{{ $pelanggaran->nama_karyawan }}</b> terhadap peraturan dan kebijakan perusahaan. Kami mencatat beberapa pelanggaran sebagai berikut:
        </p>

        <div class="list-item">
            1. {{ $pelanggaran->keterangan }}
        </div>

        <p>
            Berdasarkan hal tersebut, dengan ini kami memberikan <b>Surat Peringatan {{ ucfirst(strtolower($sp_name)) }}</b> kepada Saudara/i <b>{{ $pelanggaran->nama_karyawan }}</b>. Harap surat ini dijadikan bahan introspeksi dan motivasi untuk memperbaiki sikap dan kinerja yang bersangkutan kedepannya. Kami berharap yang bersangkutan dapat menunjukkan perubahan positif dalam waktu {{ \Carbon\Carbon::parse($pelanggaran->dari)->translatedFormat('d F Y') }} sampai dengan {{ \Carbon\Carbon::parse($pelanggaran->sampai)->translatedFormat('d F Y') }}.
        </p>

        <p>
            Jika dalam waktu yang telah ditentukan tidak ada perubahan yang signifikan, maka kami akan mengambil langkah-langkah lebih lanjut sesuai dengan kebijakan perusahaan, yang dapat berupa {{ $next_sp }}.
        </p>

        <p>
            Demikian surat peringatan ini dikeluarkan untuk diperhatikan dan dilaksanakan.
        </p>

        <p>
            Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
        </p>
    </div>

    <div class="signature-section">
        <p>
            {{ $pelanggaran->nama_cabang ?? 'Jakarta' }}, {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->translatedFormat('d F Y') }}<br>
            Hormat kami,
        </p>
        <div class="signature-space"></div>
        <p>
            <b>HRD {{ $pengaturan->nama_perusahaan ?? 'Perusahaan' }}</b>
        </p>
    </div>

</body>

</html>
