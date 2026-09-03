@extends('layouts.mobile.modern')
@section('title', 'Detail Pelanggaran')

@section('header_left')
    <a href="{{ route('pelanggaran.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: #f8fafc !important; /* light slate background */
        }
        
        .letter-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            padding: 24px;
            font-family: 'Inter', Arial, sans-serif; /* Modern font fallback */
            font-size: 13px;
            line-height: 1.6;
            color: #334155;
            position: relative;
            overflow: hidden;
        }

        /* Decorative top accent for the letter */
        .letter-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ef4444, #f97316, #eab308); /* Warning colors */
        }

        .letter-header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f1f5f9;
        }

        .letter-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .letter-nomor {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        .letter-recipient {
            margin-bottom: 24px;
            font-size: 13px;
        }

        .recipient-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 14px;
        }

        .letter-content {
            text-align: justify;
        }

        .letter-content p {
            margin-bottom: 12px;
        }

        .letter-list-item {
            background: #f8fafc;
            border-left: 3px solid #ef4444; /* Red accent for the violation */
            padding: 10px 12px;
            margin: 16px 0;
            border-radius: 4px;
            font-weight: 500;
            color: #1e293b;
            font-size: 12.5px;
        }

        .letter-signature-section {
            margin-top: 32px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            text-align: center;
        }

        .signature-date {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .signature-title {
            font-weight: 600;
            color: #0f172a;
            font-size: 13px;
        }

        .signature-space {
            height: 60px;
            width: 120px;
            border-bottom: 1px dashed #cbd5e1;
            margin: 10px 0;
        }

        /* Animations */
        .fade-up {
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-2 pb-24">
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

        <div class="letter-card fade-up" style="padding: 20px 15px;">
            <div class="letter-header">
                <div class="letter-title">SURAT PERINGATAN {{ $sp_name }}</div>
                <div class="letter-nomor">Nomor: {{ $pelanggaran->no_sp }}</div>
            </div>

            <div class="letter-recipient">
                <span class="text-slate-500">Kepada Yth,</span><br>
                <span class="recipient-name">{{ $pelanggaran->nama_karyawan }}</span><br>
                <span class="text-slate-600">{{ $pelanggaran->alamat ?? 'Di Tempat' }}</span>
            </div>

            <div class="letter-content">
                <p>Dengan hormat,</p>

                <p>
                    Sehubungan dengan hasil evaluasi kinerja dan kedisiplinan yang dilakukan Departemen <b>{{ $pelanggaran->nama_dept }}</b>, kami memberikan surat peringatan ini sebagai tindak lanjut dari ketidakpatuhan Saudara/i <span class="font-semibold text-slate-800">{{ $pelanggaran->nama_karyawan }}</span> terhadap peraturan dan kebijakan perusahaan. Kami mencatat pelanggaran sebagai berikut:
                </p>

                <div class="letter-list-item">
                    {{ $pelanggaran->keterangan }}
                </div>

                <p>
                    Berdasarkan hal tersebut, dengan ini kami memberikan <span class="font-bold text-slate-800">Surat Peringatan {{ ucfirst(strtolower($sp_name)) }}</span> kepada Saudara/i <span class="font-semibold text-slate-800">{{ $pelanggaran->nama_karyawan }}</span>. Harap surat ini dijadikan bahan introspeksi dan motivasi untuk memperbaiki sikap dan kinerja yang bersangkutan kedepannya. Kami berharap yang bersangkutan dapat menunjukkan perubahan positif dalam waktu <span class="font-semibold">{{ \Carbon\Carbon::parse($pelanggaran->dari)->translatedFormat('d F Y') }}</span> sampai dengan <span class="font-semibold">{{ \Carbon\Carbon::parse($pelanggaran->sampai)->translatedFormat('d F Y') }}</span>.
                </p>

                <p>
                    Jika dalam waktu yang telah ditentukan tidak ada perubahan yang signifikan, maka kami akan mengambil langkah-langkah lebih lanjut sesuai dengan kebijakan perusahaan, yang dapat berupa <span class="font-semibold text-red-600">{{ $next_sp }}</span>.
                </p>

                <p>
                    Demikian surat peringatan ini dikeluarkan untuk diperhatikan dan dilaksanakan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
                </p>
            </div>

            <div class="letter-signature-section">
                <div class="signature-date">
                    {{ $pelanggaran->nama_cabang ?? 'Jakarta' }}, {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->translatedFormat('d F Y') }}
                </div>
                <div class="text-slate-500 text-xs mb-1">Hormat kami,</div>
                <div class="signature-space"></div>
                <div class="signature-title">
                    HRD {{ $pengaturan->nama_perusahaan ?? 'Perusahaan' }}
                </div>
            </div>
        </div>
    </div>
@endsection
