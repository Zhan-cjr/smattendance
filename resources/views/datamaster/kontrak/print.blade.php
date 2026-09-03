@php
    use Carbon\Carbon;
    $startDate = $kontrak->dari ? Carbon::parse($kontrak->dari) : null;
    $endDate = $kontrak->sampai ? Carbon::parse($kontrak->sampai) : null;
    $birthDate = $kontrak->tanggal_lahir ? Carbon::parse($kontrak->tanggal_lahir) : null;
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kontrak {{ $kontrak->no_kontrak }}</title>
    <style>
        @page {
            margin: 25mm 20mm 20mm 25mm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
        }

        .title {
            text-align: center;
            text-transform: uppercase;
        }

        .title h2,
        .title h4 {
            margin: 0;
        }

        .content {
            margin-top: 25px;
        }

        .section-table {
            width: 100%;
            border-collapse: collapse;
        }

        .section-table td {
            vertical-align: top;
            padding: 2px 0;
        }

        .section-table .label {
            width: 160px;
        }

        .section-table .colon {
            width: 10px;
        }

        .paragraph {
            text-align: justify;
        }

        .pasal-title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        ul {
            padding-left: 18px;
        }

        .page-break {
            page-break-before: always;
        }

        table.comp-table {
            width: 70%;
            border-collapse: separate;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        table.comp-table td {
            padding: 6px 10px;
            border: none;
        }

        table.comp-table td.label {
            width: 55%;
        }

        table.comp-table td.value {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="title">
        <h2>Perjanjian Kerja Waktu Tertentu</h2>
        <h4>Nomor : {{ $kontrak->no_kontrak ?? '____/SMI-PKWT/__/____' }}</h4>
    </div>

    <div class="content">
        <p class="paragraph">
            Pada hari {{ now()->isoFormat('dddd') }} tanggal {{ now()->isoFormat('D MMMM Y') }}, telah dilakukan kesepakatan untuk
            melakukan Perjanjian Kerja Waktu Tertentu antara :
        </p>

        <table class="section-table">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td>{{ $setting->nama_hrd ?? 'Pihak Pertama' }}</td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td class="colon">:</td>
                <td>HRD {{ $setting->nama_perusahaan ?? 'Perusahaan' }}</td>
            </tr>
            <tr>
                <td colspan="3">
                    Dalam hal ini bertindak untuk dan atas nama {{ $setting->nama_perusahaan ?? 'Perusahaan' }} yang berkedudukan di {{ $setting->alamat ?? 'Lokasi Perusahaan' }},
                    yang selanjutnya dalam perjanjian ini disebut <strong>PIHAK PERTAMA</strong>.
                </td>
            </tr>
        </table>

        <br>

        <table class="section-table">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td>{{ $kontrak->nama_karyawan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tempat/Tgl Lahir</td>
                <td class="colon">:</td>
                <td>{{ $kontrak->tempat_lahir ?? '-' }} / {{ $birthDate ? $birthDate->format('d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td>{{ $kontrak->jenis_kelamin ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="colon">:</td>
                <td>{{ $kontrak->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">No KTP</td>
                <td class="colon">:</td>
                <td>{{ $kontrak->no_ktp ?? '-' }}</td>
            </tr>
            <tr>
                <td colspan="3">
                    Dalam hal ini bertindak untuk dan atas nama pribadi, yang selanjutnya dalam perjanjian ini disebut
                    <strong>PIHAK KEDUA</strong>.
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
            waktu tertentu pada Pihak Pertama dan ditempatkan sebagai : {{ $kontrak->nama_jabatan ?? '-' }} dengan lokasi kerja
            di {{ $kontrak->nama_cabang ?? 'unit kerja yang ditentukan' }}. Pihak kedua menyatakan bersedia dipindahkan atau
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
            {{ $startDate ? $startDate->isoFormat('D MMMM Y') : '________' }}
            dan akan berakhir dengan sendirinya pada tanggal
            {{ $endDate ? $endDate->isoFormat('D MMMM Y') : '________' }}.
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
    </div>

    <div class="content">
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
                        <td class="label">{{ $item->jenis ?? '-' }}</td>
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
            {{ $setting->nama_perusahaan ?? 'Perusahaan' }}. Pelanggaran terhadap tata tertib dan disiplin kerja akan mendapatkan sanksi sebagaimana
            yang telah diatur dalam Perjanjian Kerja Bersama dan Peraturan Ketenagakerjaan yang berlaku.
        </p>

        <div class="pasal-title">Pasal 8<br>Pengakhiran Hubungan Kerja</div>
        <p class="paragraph">
            Sewaktu-waktu tanpa harus menunggu berakhirnya masa kontrak kerja, Pihak Kedua dapat dikenakan sanksi Pemutusan Hubungan
            Kerja bilamana melakukan pelanggaran sebagai berikut :
        </p>
        <ul>
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
    </div>

    <div class="content">
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

        <p class="paragraph">
            Demikian perjanjian kerja waktu tertentu ini dibuat oleh kedua belah pihak dalam keadaan sehat jasmani dan rohani, tanpa
            tekanan atau paksaan dari pihak manapun dan akan dilaksanakan dengan penuh tanggung jawab.
        </p>

        <p class="paragraph">Jakarta, _______________________</p>

        <table width="100%" style="margin-top: 40px;">
            <tr>
                <td width="50%" align="center">
                    Pihak Pertama,
                    <br><br><br><br>
                    <strong><u>{{ $setting->nama_hrd ?? '________________' }}</u></strong><br>
                    HRD
                </td>
                <td width="50%" align="center">
                    Pihak Kedua,
                    <br><br><br><br>
                    <strong><u>{{ $kontrak->nama_karyawan ?? '________________' }}</u></strong><br>
                    Karyawan
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
