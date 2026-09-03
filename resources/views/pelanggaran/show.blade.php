@extends('layouts.app')
@section('titlepage', 'Detail Pelanggaran')

@section('content')
@section('navigasi')
    <span>Detail Pelanggaran</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('pelanggaran.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left me-2"></i>Kembali
                </a>
                @can('pelanggaran.edit')
                    <a href="{{ route('pelanggaran.edit', Crypt::encrypt($pelanggaran->no_sp)) }}" class="btn btn-primary">
                        <i class="ti ti-edit me-2"></i>Edit
                    </a>
                @endcan
            </div>
            <div class="card-body" style="padding: 40px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.5; color: #000;">
                <style>
                    .letter-header {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .letter-title {
                        font-size: 16px;
                        font-weight: bold;
                        text-transform: uppercase;
                        text-decoration: underline;
                        margin-bottom: 5px;
                    }
                    .letter-nomor {
                        font-size: 14px;
                        font-weight: bold;
                        margin-bottom: 30px;
                    }
                    .letter-recipient {
                        margin-bottom: 20px;
                    }
                    .letter-content {
                        text-align: justify;
                    }
                    .letter-list-item {
                        margin-left: 20px;
                        margin-bottom: 10px;
                    }
                    .letter-signature-section {
                        margin-top: 50px;
                        float: right;
                        text-align: center;
                        width: 200px;
                    }
                    .letter-signature-space {
                        height: 60px;
                    }
                    /* Clearfix */
                    .clearfix::after {
                        content: "";
                        clear: both;
                        display: table;
                    }
                </style>

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

                <div class="letter-header">
                    <div class="letter-title">SURAT PERINGATAN {{ $sp_name }}</div>
                    <div class="letter-nomor">Nomor: {{ $pelanggaran->no_sp }}</div>
                </div>

                <div class="letter-recipient">
                    Kepada Yth,<br>
                    <b>{{ $pelanggaran->nama_karyawan }}</b><br>
                    {{ $pelanggaran->alamat ?? 'Di Tempat' }}
                </div>

                <div class="letter-content">
                    <p>Dengan hormat,</p>

                    <p>
                        Sehubungan dengan hasil evaluasi kinerja dan kedisiplinan yang dilakukan Departemen {{ $pelanggaran->nama_dept }}, kami memberikan surat peringatan ini sebagai tindak lanjut dari ketidakpatuhan Saudara/i <b>{{ $pelanggaran->nama_karyawan }}</b> terhadap peraturan dan kebijakan perusahaan. Kami mencatat beberapa pelanggaran sebagai berikut:
                    </p>

                    <div class="letter-list-item">
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

                <div class="clearfix">
                    <div class="letter-signature-section">
                        <p>
                            {{ $pelanggaran->nama_cabang ?? 'Jakarta' }}, {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->translatedFormat('d F Y') }}<br>
                            Hormat kami,
                        </p>
                        <div class="letter-signature-space"></div>
                        <p>
                            <b>HRD {{ $pengaturan->nama_perusahaan ?? 'Perusahaan' }}</b>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
