@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';

    $sp_names = [
        'ST' => 'Surat Teguran',
        'SP1' => 'Surat Peringatan Pertama (SP 1)',
        'SP2' => 'Surat Peringatan Kedua (SP 2)',
        'SP3' => 'Surat Peringatan Ketiga (SP 3)'
    ];
    $sp_name = $sp_names[$pelanggaran->jenis_sp] ?? $pelanggaran->jenis_sp;

    $next_sp = '';
    if ($pelanggaran->jenis_sp == 'ST') $next_sp = 'Surat Peringatan Pertama (SP 1)';
    elseif ($pelanggaran->jenis_sp == 'SP1') $next_sp = 'Surat Peringatan Kedua (SP 2)';
    elseif ($pelanggaran->jenis_sp == 'SP2') $next_sp = 'Surat Peringatan Ketiga (SP 3)';
    else $next_sp = 'Pemutusan Hubungan Kerja (PHK)';

    $keteranganList = is_string($pelanggaran->keterangan) 
        ? json_decode($pelanggaran->keterangan, true) 
        : ($pelanggaran->keterangan ?? []);
    if (empty($keteranganList)) {
        $keteranganList = !empty($pelanggaran->keterangan) ? [$pelanggaran->keterangan] : [];
    }
    if (!is_array($keteranganList)) {
        $keteranganList = [$keteranganList];
    }

    $pasalList = is_string($pelanggaran->pasal_pelanggaran) 
        ? json_decode($pelanggaran->pasal_pelanggaran, true) 
        : ($pelanggaran->pasal_pelanggaran ?? []);
        
    $uuList = is_string($pelanggaran->dasar_uu) 
        ? json_decode($pelanggaran->dasar_uu, true) 
        : ($pelanggaran->dasar_uu ?? []);

    // Badge Styling
    $jenisSp = strtoupper(trim($pelanggaran->jenis_sp ?? ''));
    $badgeStyle = 'bg-slate-100 text-slate-700 border-slate-200';
    if ($jenisSp === 'ST') {
        $badgeStyle = 'bg-sky-50 text-sky-700 border-sky-200';
    } elseif ($jenisSp === 'SP1') {
        $badgeStyle = 'bg-amber-50 text-amber-700 border-amber-200';
    } elseif ($jenisSp === 'SP2') {
        $badgeStyle = 'bg-orange-50 text-orange-700 border-orange-200';
    } elseif ($jenisSp === 'SP3') {
        $badgeStyle = 'bg-rose-50 text-rose-700 border-rose-200';
    }
@endphp

<div class="space-y-4">
    
    <!-- Top Action & Print Bar -->
    <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 border border-slate-200/80 rounded-2xl">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-black rounded-lg border {{ $badgeStyle }}">
                <i class="ti ti-shield-alert text-sm"></i>
                <span>{{ $sp_name }}</span>
            </span>
            <span class="text-xs font-bold text-slate-600">
                No: {{ $pelanggaran->no_dokumen ?? $pelanggaran->no_sp }}
            </span>
        </div>

        <a href="{{ route('pelanggaran.print', Crypt::encrypt($pelanggaran->no_sp)) }}" target="_blank"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl text-xs border border-slate-300/80 shadow-2xs transition active:scale-95 cursor-pointer">
            <i class="ti ti-printer text-slate-500 text-sm"></i>
            <span>Cetak PDF</span>
        </a>
    </div>

    <!-- Letter Layout Preview Container -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-7 space-y-5 text-slate-800 text-xs sm:text-sm leading-relaxed shadow-2xs">
        
        <!-- Kop / Judul Surat -->
        <div class="text-center pb-4 border-b border-slate-200 space-y-1">
            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight underline">
                SURAT PERINGATAN {{ strtoupper($pelanggaran->jenis_sp) }}
            </h2>
            <p class="font-bold text-slate-700 text-xs sm:text-sm">
                Nomor: {{ $pelanggaran->no_dokumen ?? $pelanggaran->no_sp }}
            </p>
            @if (!empty($pelanggaran->no_kontrak))
                <p class="text-slate-500 text-xs">
                    Nomor Perjanjian / Kontrak: <strong class="text-slate-700">{{ $pelanggaran->no_kontrak }}</strong>
                </p>
            @endif
        </div>

        <!-- Penerima Surat -->
        <div class="p-3.5 bg-slate-50/70 border border-slate-200/60 rounded-xl space-y-1">
            <p class="text-slate-500 text-xs font-semibold">Kepada yang bersangkutan:</p>
            <p class="font-bold text-slate-900 text-sm">{{ $pelanggaran->nama_karyawan }}</p>
            <p class="text-slate-600 text-xs">
                NIK: <span class="font-semibold text-slate-800">{{ $pelanggaran->nik_show ?? $pelanggaran->nik }}</span> 
                • Jabatan: <span class="font-semibold text-slate-800">{{ $pelanggaran->nama_jabatan ?? '-' }}</span> 
                • Departemen: <span class="font-semibold text-slate-800">{{ $pelanggaran->nama_dept ?? '-' }}</span>
            </p>
            <p class="text-slate-500 text-xs">
                Alamat: {{ $pelanggaran->alamat ?? 'Di Tempat' }}
            </p>
        </div>

        <!-- Pembuka -->
        <div class="space-y-2">
            <p class="font-semibold text-slate-900">Dengan hormat,</p>
            <p class="text-justify text-slate-600 leading-relaxed">
                Sehubungan dengan hasil evaluasi kinerja dan kedisiplinan yang dilakukan Departemen {{ $pelanggaran->nama_dept ?? 'Terkait' }}, kami memberikan surat peringatan ini sebagai tindak lanjut dari ketidakpatuhan Saudara/i <strong class="text-slate-900">{{ $pelanggaran->nama_karyawan }}</strong> terhadap peraturan dan kebijakan perusahaan. Kami mencatat beberapa butir pelanggaran sebagai berikut:
            </p>
        </div>

        <!-- Poin-poin Tindakan Pelanggaran -->
        @if (!empty($keteranganList) && count($keteranganList) > 0)
            <div class="p-3.5 bg-rose-50/50 border border-rose-200/60 rounded-xl space-y-2">
                <span class="text-xs font-black text-rose-900 flex items-center gap-1.5">
                    <i class="ti ti-alert-triangle text-rose-600"></i>
                    <span>Tindakan / Pelanggaran yang Dilakukan:</span>
                </span>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-700 pl-1">
                    @foreach ($keteranganList as $k)
                        @if (!empty($k))
                            <li class="text-justify leading-relaxed">{{ $k }}</li>
                        @endif
                    @endforeach
                </ol>
            </div>
        @endif

        <!-- Poin / Pasal Pelanggaran -->
        @if (!empty($pasalList) && count($pasalList) > 0)
            @php
                $firstVal = is_array($pasalList[0]) ? ($pasalList[0]['pasal'] ?? '') : $pasalList[0];
            @endphp
            @if (!empty($firstVal))
                <div class="p-3.5 bg-amber-50/50 border border-amber-200/60 rounded-xl space-y-2">
                    <span class="text-xs font-black text-amber-900 flex items-center gap-1.5">
                        <i class="ti ti-gavel text-amber-600"></i>
                        <span>A. Ketentuan Peraturan yang Dilanggar:</span>
                    </span>
                    <ol class="list-decimal list-inside space-y-2 text-slate-700 pl-1">
                        @foreach ($pasalList as $p)
                            @php
                                $pasalText = is_array($p) ? ($p['pasal'] ?? '') : $p;
                                $bunyiText = is_array($p) ? ($p['bunyi'] ?? '') : '';
                                if (empty($bunyiText) && strpos($pasalText, ':') !== false) {
                                    $parts = explode(':', $pasalText, 2);
                                    $pasalText = trim($parts[0]);
                                    $bunyiText = trim($parts[1]);
                                }
                            @endphp
                            @if (!empty($pasalText))
                                <li class="text-justify leading-relaxed">
                                    <strong class="text-slate-900">{{ $pasalText }}</strong>
                                    @if (!empty($bunyiText))
                                        <p class="mt-1 pl-4 text-xs italic text-slate-600 border-l-2 border-amber-400">
                                            "{{ $bunyiText }}"
                                        </p>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </div>
            @endif
        @endif

        <!-- Dasar UU -->
        @if (!empty($uuList) && count($uuList) > 0 && !empty($uuList[0]))
            <div class="p-3.5 bg-indigo-50/50 border border-indigo-200/60 rounded-xl space-y-2">
                <span class="text-xs font-black text-indigo-900 flex items-center gap-1.5">
                    <i class="ti ti-scale text-indigo-600"></i>
                    <span>B. Dasar Peraturan Perundang-undangan:</span>
                </span>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-700 pl-1">
                    @foreach ($uuList as $uu)
                        @if (!empty($uu))
                            <li class="text-justify leading-relaxed">{{ $uu }}</li>
                        @endif
                    @endforeach
                </ol>
            </div>
        @endif

        <!-- Masa Berlaku & Konsekuensi -->
        <div class="space-y-2 text-justify text-slate-600 leading-relaxed">
            <p>
                Berdasarkan hal tersebut, dengan ini kami memberikan <strong class="text-slate-900">{{ $sp_name }}</strong> kepada Saudara/i <strong class="text-slate-900">{{ $pelanggaran->nama_karyawan }}</strong>. Harap surat ini dijadikan bahan introspeksi dan motivasi untuk memperbaiki sikap serta kinerja kedepannya. Kami berharap yang bersangkutan dapat menunjukkan perubahan positif dalam waktu <strong class="text-slate-900">{{ \Carbon\Carbon::parse($pelanggaran->dari)->translatedFormat('d F Y') }}</strong> sampai dengan <strong class="text-slate-900">{{ \Carbon\Carbon::parse($pelanggaran->sampai)->translatedFormat('d F Y') }}</strong>.
            </p>
            <p>
                Jika dalam waktu yang telah ditentukan tidak ada perubahan yang signifikan, maka manajemen akan mengambil tindakan lebih lanjut sesuai kebijakan perusahaan, yang dapat berupa <strong class="text-rose-700">{{ $next_sp }}</strong>.
            </p>
            <p>
                Demikian surat peringatan ini dikeluarkan untuk diperhatikan dan dilaksanakan dengan penuh tanggung jawab.
            </p>
        </div>

        <!-- Tanda Tangan & Lokasi -->
        <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6">
            <div class="text-xs text-slate-500 space-y-1">
                <p>Status Dokumen: <span class="font-bold text-slate-700">Tercatat di Sistem Presensi V2</span></p>
                <p>Tanggal Diterbitkan: <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($pelanggaran->tanggal)->translatedFormat('d F Y') }}</span></p>
            </div>

            <div class="text-center sm:text-right space-y-1 sm:w-60">
                <p class="text-slate-600 text-xs">
                    {{ $pelanggaran->nama_cabang ?? 'Pusat' }}, {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->translatedFormat('d F Y') }}
                </p>
                <p class="text-slate-600 text-xs font-semibold">Hormat kami,</p>
                <div class="h-14"></div>
                <p class="font-black text-slate-900 text-xs sm:text-sm">
                    HRD {{ $pengaturan->nama_perusahaan ?? 'Perusahaan' }}
                </p>
            </div>
        </div>

    </div>

</div>
