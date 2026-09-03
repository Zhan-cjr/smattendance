@extends('layouts.mobile.modern')
@section('title', 'Detail Kontrak')

@section('header_left')
    <a href="{{ route('kontrak.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
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
            padding: 24px 15px; /* Matched the padding from Pelanggaran */
            font-family: 'Inter', Arial, sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #334155;
            position: relative;
            overflow: hidden;
        }

        /* Decorative top accent for the document */
        .letter-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #32745e, #4b9b82); /* Green theme for contract */
        }

        .contract-header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f1f5f9;
            text-transform: uppercase;
        }

        .contract-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .contract-nomor {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
        }

        .section-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .section-table td {
            vertical-align: top;
            padding: 3px 0;
        }

        .section-table .label {
            width: 110px;
            color: #64748b;
        }

        .section-table .colon {
            width: 10px;
            color: #64748b;
        }

        .section-table strong {
            color: #0f172a;
        }

        .paragraph {
            text-align: justify;
            margin-bottom: 12px;
        }

        .pasal-title {
            text-align: center;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 24px;
            margin-bottom: 12px;
            font-size: 14px;
            border-top: 1px dashed #e2e8f0;
            padding-top: 16px;
        }

        .letter-list {
            padding-left: 20px;
            margin-top: 5px;
            margin-bottom: 12px;
        }
        
        .letter-list li {
            margin-bottom: 6px;
            text-align: justify;
        }

        .comp-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 16px;
            background: #f8fafc;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .comp-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .comp-table tr:last-child td {
            border-bottom: none;
        }

        .comp-table td.label {
            width: 60%;
            color: #475569;
            font-weight: 500;
        }

        .comp-table td.value {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            text-align: center;
        }

        .signature-box {
            width: 48%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .signature-title-text {
            color: #64748b;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .signature-space {
            height: 70px;
            width: 100%;
            border-bottom: 1px dashed #cbd5e1;
            margin: 10px 0;
        }

        .signature-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 13px;
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
@php
    use Carbon\Carbon;
    $startDate = $kontrak->dari ? Carbon::parse($kontrak->dari) : null;
    $endDate = $kontrak->sampai ? Carbon::parse($kontrak->sampai) : null;
    $birthDate = $kontrak->tanggal_lahir ? Carbon::parse($kontrak->tanggal_lahir) : null;
@endphp
    <div class="px-1 pt-2 pb-24">
        <div class="letter-card fade-up">
            <div class="contract-header">
                <div class="contract-title">Perjanjian Kerja Waktu Tertentu</div>
                <div class="contract-nomor">Nomor : {{ $kontrak->no_kontrak }}</div>
            </div>

            <p class="paragraph">
                Pada hari <span class="font-semibold">{{ \Carbon\Carbon::parse($kontrak->tanggal)->isoFormat('dddd') }}</span> tanggal <span class="font-semibold">{{ \Carbon\Carbon::parse($kontrak->tanggal)->isoFormat('D MMMM Y') }}</span>, telah dilakukan kesepakatan untuk
                melakukan Perjanjian Kerja Waktu Tertentu antara :
            </p>

            <table class="section-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="font-semibold text-slate-800">Dian Eka Suphintia</td>
                </tr>
                <tr>
                    <td class="label">Jabatan</td>
                    <td class="colon">:</td>
                    <td class="font-semibold text-slate-800">HRD {{ $pengaturan->nama_perusahaan ?? 'Perusahaan' }}</td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="mt-2 text-justify">
                            Dalam hal ini bertindak untuk dan atas nama <strong>{{ $pengaturan->nama_perusahaan ?? 'Perusahaan' }}</strong> yang berkedudukan di Jakarta Utara,
                            yang selanjutnya dalam perjanjian ini disebut <strong class="text-[#32745e]">PIHAK PERTAMA</strong>.
                        </div>
                    </td>
                </tr>
            </table>

            <table class="section-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="font-semibold text-slate-800">{{ $kontrak->nama_karyawan }}</td>
                </tr>
                <tr>
                    <td class="label">Tempat/Tgl Lahir</td>
                    <td class="colon">:</td>
                    <td class="font-semibold text-slate-800">{{ $kontrak->tempat_lahir }} / {{ $birthDate ? $birthDate->format('d-m-Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Kelamin</td>
                    <td class="colon">:</td>
                    <td class="font-semibold text-slate-800">{{ $kontrak->jenis_kelamin }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="colon">:</td>
                    <td class="font-semibold text-slate-800">{{ $kontrak->alamat }}</td>
                </tr>
                <tr>
                    <td class="label">No KTP</td>
                    <td class="colon">:</td>
                    <td class="font-semibold text-slate-800">{{ $kontrak->no_ktp }}</td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="mt-2 text-justify">
                            Dalam hal ini bertindak untuk dan atas nama pribadi, yang selanjutnya dalam perjanjian ini disebut
                            <strong class="text-[#32745e]">PIHAK KEDUA</strong>.
                        </div>
                    </td>
                </tr>
            </table>

            <p class="paragraph">
                Dengan ini menyatakan bahwa Pihak Pertama dan Pihak Kedua telah sepakat dalam suatu perjanjian kerjasama dengan
                ketentuan-ketentuan dan syarat-syarat sebagaimana tercantum dalam pasal-pasal di bawah ini :
            </p>

            <div class="pasal-title">Pasal 1<br>Penempatan Dan Lokasi Kerja</div>
            <p class="paragraph">
                Pihak Pertama bersedia dan siap untuk menerima Pihak Kedua sebagai karyawan dengan status karyawan kontrak untuk
                waktu tertentu pada Pihak Pertama dan ditempatkan sebagai : <strong>{{ $kontrak->nama_jabatan }}</strong> dengan lokasi kerja
                di <strong>{{ $kontrak->nama_cabang }}</strong>. Pihak kedua menyatakan bersedia dipindahkan atau
                dimutasikan pada cabang lain, bilamana terdapat kebutuhan untuk itu, sesuai dengan keputusan dan kebutuhan Perusahaan
                Pihak Kedua ditempatkan.
            </p>

            <div class="pasal-title">Pasal 2<br>Pelaksanaan Pekerjaan</div>
            <p class="paragraph">
                Pihak Kedua mempunyai tugas dan kewajiban melaksanakan pekerjaan pada bagian yang telah ditetapkan dan mengikuti
                prosedur kerja yang ditetapkan dan berlaku dimana Pihak Kedua ditempatkan.
            </p>

            <div class="pasal-title">Pasal 3<br>Jangka Waktu Perjanjian</div>
            <p class="paragraph">
                Perjanjian kerja untuk waktu tertentu ini berlaku sejak tanggal
                <span class="font-semibold">{{ $startDate ? $startDate->isoFormat('D MMMM Y') : '________' }}</span>
                dan akan berakhir dengan sendirinya pada tanggal
                <span class="font-semibold">{{ $endDate ? $endDate->isoFormat('D MMMM Y') : '________' }}</span>.
            </p>
            <p class="paragraph">
                Bilamana perjanjian kerja waktu tertentu ini telah berakhir sesuai dengan jangka waktu yang telah ditentukan,
                maka hubungan hukum kerja ini putus dengan sendirinya dan Pihak Pertama tidak wajib mengangkat Pihak Kedua menjadi
                karyawan tetap. Perpanjangan perjanjian kerja waktu tertentu dapat dilakukan, sesuai dengan kebutuhan dan
                persetujuan Pihak Pertama dan Pihak Kedua.
            </p>

            <div class="pasal-title">Pasal 4<br>Perpanjangan Kontrak</div>
            <p class="paragraph">
                Dalam hal kesepakatan kerja ini diperpanjang oleh Pihak Pertama, maka hal tersebut akan diberitahukan secara tertulis
                kepada pihak kedua selambat-lambatnya 7 (tujuh) hari sebelum kesepakatan kerja ini berakhir.
            </p>

            <div class="pasal-title">Pasal 5<br>Upah Dan Tunjangan Atau Fasilitas</div>
            <p class="paragraph">
                Pihak Kedua akan menerima upah perbulan dan beberapa tunjangan atau fasilitas, sebagai berikut :
            </p>
            
            <table class="comp-table">
                <tr>
                    <td class="label">Gaji Pokok</td>
                    <td class="value">Rp {{ number_format($kontrak->jumlah_gaji ?? 0, 0, ',', '.') }}</td>
                </tr>
                @if (isset($tunjanganItems) && $tunjanganItems->isNotEmpty())
                    @foreach ($tunjanganItems as $item)
                        <tr>
                            <td class="label">{{ $item->jenis }}</td>
                            <td class="value">Rp {{ number_format($item->jumlah ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="label">Transport</td>
                        <td class="value">Rp 0</td>
                    </tr>
                    <tr>
                        <td class="label">Tunjangan Shift Malam</td>
                        <td class="value">Rp 0</td>
                    </tr>
                    <tr>
                        <td class="label">Uang Makan</td>
                        <td class="value">Rp 0</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Perhitungan Upah Lembur</td>
                    <td class="value">Normatif</td>
                </tr>
                <tr>
                    <td class="label">BPJS Ketenagakerjaan &amp; Kesehatan</td>
                    <td class="value">Ditanggung Perusahaan</td>
                </tr>
            </table>

            <p class="paragraph">
                Pembayaran upah akan dibayarkan oleh Pihak Pertama melalui transfer ke rekening Pihak Kedua paling lambat diberikan
                setiap tanggal 25 (dua puluh lima) setiap bulannya.
            </p>

            <div class="pasal-title">Pasal 6<br>Jam Kerja</div>
            <p class="paragraph">
                Pihak Kedua bersedia bekerja selama 8 (delapan) jam sehari untuk 5 (lima) hari kerja dalam seminggu dan 7 (tujuh) jam
                sehari untuk 6 (enam) hari kerja dalam seminggu dengan 40 (empat puluh) jam seminggu, dengan pengaturan hari dan jam
                kerja disesuaikan dengan situasi dan kebutuhan Perusahaan. Kelebihan jam kerja sebagaimana disebut diatas, akan
                diperhitungkan sebagai jam kerja lembur yang Pihak Kedua berhak mendapatkan upah lembur dengan berdasarkan pada
                Keputusan Menteri Tenaga Kerja No. 102/MEN/VI/2004. Pihak Kedua menyatakan bersedia untuk bekerja dalam hari kerja
                Shift bilamana situasi dan kebutuhan Pemerintah meminta, atau bilamana Pihak Kedua ditempatkan.
            </p>

            <div class="pasal-title">Pasal 7<br>Tata Tertib Dan Disiplin Kerja</div>
            <p class="paragraph">
                Pihak Kedua wajib mengikuti dan mentaati keseluruhan peraturan dan tata tertib serta disiplin kerja yang berlaku di
                PT. Sentral Multi Indontama. Pelanggaran terhadap tata tertib dan disiplin kerja akan mendapatkan sanksi sebagaimana
                yang telah diatur dalam Perjanjian Kerja Bersama dan Peraturan Ketenagakerjaan yang berlaku.
            </p>

            <div class="pasal-title">Pasal 8<br>Pengakhiran Hubungan Kerja</div>
            <p class="paragraph">
                Sewaktu-waktu tanpa harus menunggu berakhirnya masa kontrak kerja, Pihak Kedua dapat dikenakan sanksi Pemutusan Hubungan
                Kerja bilamana melakukan pelanggaran sebagai berikut :
            </p>
            <ul class="letter-list list-disc list-outside text-slate-600">
                <li>Penipuan, penggelapan atau pemalsuan dokumen dan memberikan keterangan palsu di dalam lingkungan Perusahaan.</li>
                <li>Menggunakan, membawa senjata tajam, meminum minuman keras atau obat-obatan terlarang di lingkungan Perusahaan.</li>
                <li>Berusaha atau melakukan tindakan tidak menyenangkan terhadap atasan, bawahan, rekan kerja atau orang lain yang ada
                    hubungan dengan Perusahaan.</li>
                <li>Membuka penghasilan, Kegiatan Perusahaan, Kegiatan, atasan, bawahan atau rekan kerja untuk kepentingan pihak luar
                    yang bertentangan dengan Peraturan Perusahaan.</li>
                <li>Dengan sengaja menjaga/membiarkan dalam keadaan bahaya yang dapat menimbulkan kerugian besar bagi Perusahaan.</li>
                <li>Bertindak dengan sengaja Pekerja di lingkungan Pekerjaan.</li>
                <li>Dan pelanggaran-pelanggaran berat lainnya yang diatur dalam Perjanjian Kerja Bersama.</li>
            </ul>

            <div class="pasal-title">Pasal 9<br>Ketentuan PHK (Pemutusan Hubungan Kerja)</div>
            <p class="paragraph">
                Dalam hal ini Pihak Kedua melakukan pelanggaran sebagaimana disebut dalam pasal 8 perjanjian ini, Pihak Pertama akan
                melakukan tindakan Pemutusan Hubungan Kerja atas diri Pihak Kedua dengan berpedoman pada ketentuan Undang-Undang
                Ketenagakerjaan yang berlaku. Pihak Pertama dibebaskan untuk memberikan kompensasi atau kebijaksanaan dalam bentuk
                apapun sebagai akibat Pemutusan Hubungan Kerja dengan alasan-alasan sebagaimana tersebut dalam pasal 8 perjanjian ini.
            </p>

            <div class="pasal-title">Pasal 10<br>Sisa Kontrak</div>
            <p class="paragraph">
                Pihak Pertama dapat mengakhiri perjanjian kerja ini sebelum waktunya dengan memberikan ganti rugi sisa masa kontrak
                kepada Pihak Kedua.
            </p>

            <div class="pasal-title">Pasal 11<br>Perubahan</div>
            <p class="paragraph">
                Bilamana terdapat kekeliruan didalam ketentuan-ketentuan perjanjian kerja ini, akan dilakukan perubahan dan
                perbaikan seperlunya.
            </p>

            <div class="pasal-title">Pasal 12<br>Penyelesaian</div>
            <p class="paragraph">
                Bilamana dikemudian hari timbul perselisihan sebagai akibat dari perjanjian ini, maka Pihak Pertama dan Pihak Kedua
                sepakat untuk menyelesaikannya secara musyawarah kekeluargaan, tanpa mengesampingkan kemungkinan penyelesaian melalui
                prosedur dan ketentuan hukum yang berlaku.
            </p>

            <p class="paragraph" style="margin-top: 24px;">
                Demikian perjanjian kerja waktu tertentu ini dibuat oleh kedua belah pihak dalam keadaan sehat jasmani dan rohani, tanpa
                tekanan atau paksaan dari pihak manapun dan akan dilaksanakan dengan penuh tanggung jawab.
            </p>

            <p class="paragraph text-slate-500" style="margin-top: 20px;">Jakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>

            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-title-text">Pihak Pertama,</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">Dian Eka S.</div>
                    <div class="text-xs text-slate-500">HRD</div>
                </div>
                <div class="signature-box">
                    <div class="signature-title-text">Pihak Kedua,</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">{{ $kontrak->nama_karyawan }}</div>
                    <div class="text-xs text-slate-500">Karyawan</div>
                </div>
            </div>

            <div class="mt-8 text-center px-2">
                <a href="{{ route('kontrak.print', Crypt::encrypt($kontrak->id)) }}" class="flex items-center justify-center gap-2 w-full py-3 bg-[#32745e] text-white rounded-xl font-bold text-sm shadow-lg shadow-[#32745e]/20 active:scale-95 transition-all">
                    <ion-icon name="print-outline" class="text-lg"></ion-icon> Download / Cetak PDF
                </a>
            </div>

        </div>
    </div>
@endsection
