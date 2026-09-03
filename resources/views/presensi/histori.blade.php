@extends('layouts.mobile.modern')


@section('title', 'Histori Presensi')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-90 transition-transform">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@section('content')

    {{-- ===== FILTER ===== --}}
    <form method="GET" action="{{ route('presensi.histori') }}" id="formHistori">
        <div class="mt-1 mb-2 rounded-xl overflow-hidden border"
             style="background: #fff; border-color: #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
            {{-- Filter Header --}}
            <div class="flex items-center gap-2 px-3 py-2" style="border-bottom: 1px solid #f1f5f9;">

                <div class="w-6 h-6 rounded flex items-center justify-center" style="background: {{ $t['primary'] }}15;">
                    <ion-icon name="calendar-outline" class="text-[12px]" style="color: {{ $t['primary'] }};"></ion-icon>
                </div>
                <span class="text-[12px] font-semibold" style="color: #475569;">Pilih Rentang Tanggal</span>

            </div>
            {{-- Filter Inputs --}}
            <div class="px-3 py-2.5">
                <div class="flex items-center gap-2">
                    {{-- Dari --}}
                    <div class="flex-1">
                        <input type="text" name="dari" id="dari" 
                            class="w-full rounded-lg py-1.5 px-3 text-[12px] font-medium text-center focus:outline-none transition-all"
                            style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;"
                            placeholder="Dari" value="{{ Request('dari') }}" autocomplete="off" required readonly>

                    </div>
                    <div class="flex-shrink-0 w-4 flex items-center justify-center">
                        <div class="w-3 h-[1px]" style="background: #cbd5e1;"></div>

                    </div>
                    {{-- Sampai --}}
                    <div class="flex-1">
                        <input type="text" name="sampai" id="sampai" 
                            class="w-full rounded-lg py-1.5 px-3 text-[12px] font-medium text-center focus:outline-none transition-all"
                            style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;"
                            placeholder="Sampai" value="{{ Request('sampai') }}" autocomplete="off" required readonly>

                    </div>
                    {{-- Button --}}
                    <button type="submit" id="btnCari"
                        class="flex-shrink-0 w-9 h-8 rounded-lg text-white flex items-center justify-center active:scale-90 transition-transform"
                        style="background: {{ $t['primary'] }};">
                        <ion-icon name="search-outline" class="text-base"></ion-icon>
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ===== HISTORY LIST ===== --}}
    <div id="showhistori">
        {{-- Skeleton synced with dashboard --}}
        <div id="skeleton-container" class="space-y-2">
            @for ($i = 0; $i < 5; $i++)
                <div class="rounded-[10px] p-1 border shadow-sm" style="background: #fff; border-color: #f1f5f9;">

                    <div class="flex items-center gap-2">
                        <div class="skeleton-avatar sk flex-shrink-0"></div>
                        <div class="flex-1 space-y-2 pr-2">
                            <div class="flex justify-between items-center">
                                <div class="skeleton-text w-24 sk"></div>
                                <div class="skeleton-text w-12 sk"></div>
                            </div>
                            <div class="skeleton-text w-32 sk"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Data synced with dashboard --}}
        <div id="data-container" style="display:none;" class="space-y-2">
            @foreach ($datapresensi as $index => $d)
                @php
                    $namahari = [
                        'Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu',
                        'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'
                    ];
                    $day_eng = date('D', strtotime($d->tanggal));
                    $day_indo = $namahari[$day_eng] ?? $day_eng;
                    $day_short = strtoupper(substr($day_indo, 0, 3));
                    $tgl = date('d', strtotime($d->tanggal));
                    $bulan_indo = getNamabulan((int)date('m', strtotime($d->tanggal)));
                    $tahun = date('Y', strtotime($d->tanggal));

                    $statusStyles = [
                        'h' => ['label' => 'Hadir', 'color' => $t['primary'], 'rgb' => '50, 116, 94'],

                        'i' => ['label' => 'Izin',  'color' => '#1e90ff', 'rgb' => '30, 144, 255'],
                        's' => ['label' => 'Sakit', 'color' => '#ff6384', 'rgb' => '255, 99, 132'],
                        'c' => ['label' => 'Cuti',  'color' => '#ff9f40', 'rgb' => '255, 159, 64'],
                        'a' => ['label' => 'Alpha', 'color' => '#e74c3c', 'rgb' => '231, 76, 60'],
                    ];
                    $st = $statusStyles[$d->status] ?? $statusStyles['a'];
                    $bgColor = "rgba({$st['rgb']}, 0.1)";

                    $is_late = false;
                    $denda_display = 0;
                    $pulangcepat = 0;

                    if ($d->status == 'h') {
                        $jam_in_ts = strtotime($d->jam_in);
                        $jam_masuk_ts = strtotime($d->tanggal . ' ' . $d->jam_masuk);
                        $is_late = $jam_in_ts > $jam_masuk_ts;

                        if ($is_late && $d->jam_in) {
                            $terlambat_selisih = $jam_in_ts - $jam_masuk_ts;
                            $menit_telat = floor(($terlambat_selisih % 3600) / 60);
                            $denda_display = !empty($d->denda) ? $d->denda : hitungdenda($denda_list, $menit_telat);
                        }

                        $pulangcepat = hitungpulangcepat($d->tanggal, $d->jam_out, $d->jam_pulang, $d->istirahat, $d->jam_awal_istirahat, $d->jam_akhir_istirahat, $d->lintashari);
                    }
                @endphp

                <div class="fade-up card press mb-1 overflow-hidden"
                     style="border: 1px solid {{ $t['primary'] }}; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); background: #fff; animation-delay: {{ $index * 0.04 }}s;">

                    <div class="card-body p-1 flex items-center gap-2">
                        {{-- Date Badge --}}
                        <div class="flex-shrink-0 w-[45px] h-[45px] flex flex-col items-center justify-center rounded-[12px]"
                             style="background: {{ $bgColor }}; color: {{ $st['color'] }};">
                            <span class="text-[10px] font-bold leading-none">{{ $day_short }}</span>
                            <span class="text-[16px] font-extrabold leading-tight mt-0.5">{{ $tgl }}</span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0 pr-1">
                            <div class="flex items-center justify-between mb-0.5">
                                <h3 class="text-[14px] font-semibold truncate" style="color: #333;">

                                    {{ DateToIndo($d->tanggal) }}
                                </h3>
                                <span class="inline-flex items-center text-[10px] px-1.5 py-0.5 rounded border"
                                      style="background: #f8f9fa; color: #666; border-color: #eee;">

                                    {{ $d->nama_jam_kerja }}
                                </span>
                            </div>

                            @if ($d->status == 'h')
                                <div class="flex items-center justify-between mb-0.5">
                                    <div class="flex items-center gap-1.5 text-[12px] font-medium" style="color: #555;">

                                        <span>{{ $d->jam_in ? date('H:i', strtotime($d->jam_in)) : '__:__' }}</span>
                                        <span style="color: #ccc;">-</span>
                                        <span>{{ $d->jam_out ? date('H:i', strtotime($d->jam_out)) : '__:__' }}</span>
                                    </div>
                                    @if ($is_late)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-red-500 text-white">TELAT</span>
                                    @else
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-500 text-white">TEPAT WAKTU</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    @if ($denda_display > 0)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-red-500 text-white">
                                            Denda Rp. {{ number_format($denda_display) }}
                                        </span>
                                    @endif
                                    @if ($pulangcepat > 0)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-red-500 text-white">PULANG CEPAT</span>
                                    @endif
                                </div>
                            @elseif ($d->status == 'i')
                                <p class="text-[12px] leading-tight" style="color: #1e90ff;">Izin: {{ $d->keterangan_izin }}</p>
                            @elseif ($d->status == 's')
                                <p class="text-[12px] leading-tight" style="color: #ff6384;">Sakit: {{ $d->keterangan_izin_sakit }}</p>
                            @elseif ($d->status == 'c')
                                <p class="text-[12px] leading-tight" style="color: #ff9f40;">Cuti: {{ $d->keterangan_izin_cuti }}</p>
                            @else
                                <p class="text-[12px] leading-tight" style="color: #e74c3c;">Alpha: Tanpa Keterangan</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($datapresensi->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 px-6 text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4" style="background: #f1f5f9;">
                        <ion-icon name="calendar-outline" class="text-3xl" style="color: #cbd5e1;"></ion-icon>
                    </div>
                    <h3 class="text-[14px] font-bold mb-1" style="color: #334155;">Tidak Ada Data</h3>
                    <p class="text-[12px] leading-relaxed max-w-[220px]" style="color: #94a3b8;">Pilih rentang tanggal untuk melihat histori presensi Anda.</p>

                </div>
            @endif
        </div>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        const localeIndo = {
            days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
            months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            today: 'Hari ini', clear: 'Hapus', dateFormat: 'yyyy-MM-dd', timeFormat: 'HH:mm', firstDay: 1
        };
        const dpOpt = { locale: localeIndo, autoClose: true, isMobile: true, buttons: ['today', 'clear'], position: 'bottom center' };
        new AirDatepicker('#dari', dpOpt);
        new AirDatepicker('#sampai', dpOpt);

        function showSkeleton() { $('#data-container').hide(); $('#skeleton-container').show(); }
        function hideSkeleton() { $('#skeleton-container').fadeOut(200, function() { $('#data-container').fadeIn(300); }); }
        $('#formHistori').on('submit', function() { showSkeleton(); });
        $(document).ready(function() { setTimeout(hideSkeleton, 400); });
        $(window).on('load', function() { setTimeout(hideSkeleton, 250); });
    </script>
@endpush
