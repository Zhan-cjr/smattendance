@extends('v2.layouts.app')

@section('titlepage', 'Set Target KPI')
@section('page_title', 'Set Target KPI')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $user = auth()->user();
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle with Back Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('kpi.transactions.index', request()->query()) }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('kpi.transactions.index') }}" class="hover:text-emerald-600 transition-colors">Transaksi KPI</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Set Target</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 mt-0.5">
                    <i class="ti ti-target text-2xl" style="color: {{ $themeColor1 }};"></i>
                    <span>Penetapan Target KPI</span>
                </h1>
            </div>
        </div>

        <!-- Breadcrumbs Navigation -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('kpi.transactions.index') }}" class="hover:text-slate-700 transition">KPI</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Set Target</span>
            </nav>
        </div>
    </div>

    <!-- ================= 2. PROFILE & PERIODE CARD ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- Employee Profile Info -->
            <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                @if(!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                    <img src="{{ getfotoKaryawan($karyawan->foto) }}" 
                         alt="{{ $karyawan->nama_karyawan }}" 
                         class="w-14 h-14 rounded-2xl object-cover border border-slate-200 shrink-0 shadow-2xs">
                @else
                    <div class="w-14 h-14 rounded-2xl text-white font-bold flex items-center justify-center text-xl shrink-0 shadow-2xs"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        {{ substr($karyawan->nama_karyawan, 0, 1) }}
                    </div>
                @endif

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-base sm:text-lg font-extrabold text-slate-900 truncate">
                            {{ $karyawan->nama_karyawan }}
                        </h2>
                        <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200/80">
                            {{ $karyawan->nik_show ?? $karyawan->nik }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-1.5 text-xs text-slate-500 font-medium">
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-briefcase text-slate-400"></i>
                            {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
                        </span>
                        <span class="text-slate-300">•</span>
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-building text-slate-400"></i>
                            {{ $karyawan->departemen->nama_dept ?? '-' }}
                        </span>
                        <span class="text-slate-300">•</span>
                        <span class="flex items-center gap-1.5">
                            <i class="ti ti-calendar-event text-slate-400"></i>
                            Join: {{ date('d M Y', strtotime($karyawan->tanggal_masuk)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Periode Info Pill -->
            <div class="flex sm:flex-col items-start sm:items-end justify-between sm:justify-center pt-3 md:pt-0 border-t md:border-t-0 md:border-l md:pl-6 border-slate-100 shrink-0">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Periode Penilaian</span>
                <span class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">{{ $period->nama_periode }}</span>
                <span class="text-[11px] text-slate-500 font-medium">
                    {{ date('d M Y', strtotime($period->start_date)) }} - {{ date('d M Y', strtotime($period->end_date)) }}
                </span>
            </div>

        </div>
    </div>

    <!-- ================= 3. SET TARGET FORM & CARD LIST ================= -->
    <form action="{{ route('kpi.transactions.store') }}?{{ http_build_query(request()->query()) }}" method="POST" id="formSetTargetV2">
        @csrf
        <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
        <input type="hidden" name="kpi_period_id" value="{{ $period->id }}">

        @if ($indicators->isEmpty())
            <div class="bg-white rounded-2xl border border-amber-200/90 p-8 sm:p-10 text-center shadow-2xs">
                <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 mx-auto flex items-center justify-center mb-3">
                    <i class="ti ti-alert-triangle text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum ada Indikator KPI Ditemukan!</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                    Indikator KPI untuk Jabatan <strong>{{ $karyawan->jabatan->nama_jabatan ?? '-' }}</strong> dan Departemen <strong>{{ $karyawan->departemen->nama_dept ?? '-' }}</strong> belum dikonfigurasi.
                </p>
                <div class="mt-4">
                    <a href="{{ route('kpi.indicators.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-white text-xs font-bold shadow-xs cursor-pointer"
                       style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-settings text-sm"></i>
                        <span>Buka Konfigurasi Indikator</span>
                    </a>
                </div>
            </div>
        @else
            <!-- Bobot Summary Live Bar -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <i class="ti ti-scale text-slate-400"></i> Akumulasi Bobot Indikator
                        </span>
                        <p class="text-[11px] text-slate-400 mt-0.5">Total bobot seluruh indikator harus bernilai tepat 100%</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span id="totalBobotBadge" class="px-3 py-1 rounded-xl text-xs font-extrabold border">
                            <span id="totalBobotText">0</span>%
                        </span>
                        <span id="bobotStatusText" class="text-xs font-bold"></span>
                    </div>
                </div>
                <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden mt-3">
                    <div id="bobotProgressBar" class="h-full rounded-full transition-all duration-300" style="width: 0%;"></div>
                </div>
            </div>

            <!-- List Card Indikator -->
            <div class="space-y-3 mt-4">
                @php $total_bobot = 0; @endphp
                @foreach ($indicators as $index => $indicator)
                    @php $total_bobot += $indicator->bobot; @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 transition-all duration-200 hover:border-slate-300">
                        <input type="hidden" name="indicator_id[]" value="{{ $indicator->id }}">

                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <!-- Info Indikator -->
                            <div class="flex items-start gap-3.5 flex-1 min-w-0">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 text-slate-700 bg-slate-100 border border-slate-200">
                                    {{ $loop->iteration }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm sm:text-base font-bold text-slate-800">
                                            {{ $indicator->nama_indikator }}
                                        </h3>
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                                            {{ $indicator->satuan }}
                                        </span>
                                        @if($indicator->jenis_target == 'min')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                <i class="ti ti-arrow-down text-[10px]"></i> Min (Makin kecil makin baik)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="ti ti-arrow-up text-[10px]"></i> Max (Makin besar makin baik)
                                            </span>
                                        @endif
                                        @if($indicator->mode == 'auto')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                                <i class="ti ti-cpu text-[10px]"></i> Otomatis ({{ $indicator->metric_source }})
                                            </span>
                                        @endif
                                    </div>
                                    @if($indicator->deskripsi)
                                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $indicator->deskripsi }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Input Target & Bobot -->
                            <div class="grid grid-cols-2 sm:flex sm:items-center gap-3 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                                <!-- Target -->
                                <div class="w-full sm:w-36">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Target</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="target[]" value="{{ $indicator->target }}" required
                                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all"
                                               style="--tw-ring-color: {{ $themeColor1 }};">
                                        <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[10px] font-semibold text-slate-400">
                                            {{ $indicator->satuan }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Bobot -->
                                <div class="w-full sm:w-28">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Bobot (%)</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="bobot[]" value="{{ $indicator->bobot }}" required
                                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all bobot-input"
                                               style="--tw-ring-color: {{ $themeColor1 }};">
                                        <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-[10px] font-semibold text-slate-400">
                                            %
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Action Buttons Footer -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-2.5 pt-4">
                <a href="{{ route('kpi.transactions.index', request()->query()) }}" 
                   class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-2xs text-center cursor-pointer">
                    Batal & Kembali
                </a>
                <button type="submit" id="btnSubmitSetTarget"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-device-floppy text-base"></i>
                    <span>Simpan Target KPI</span>
                </button>
            </div>
        @endif
    </form>

</div>

@push('myscript')
<script>
$(document).ready(function() {
    function updateLiveBobot() {
        let total = 0;
        $('.bobot-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });

        const totalFixed = parseFloat(total.toFixed(2));
        $('#totalBobotText').text(totalFixed);
        
        const badge = $('#totalBobotBadge');
        const statusText = $('#bobotStatusText');
        const progressBar = $('#bobotProgressBar');

        progressBar.css('width', Math.min(totalFixed, 100) + '%');

        if (totalFixed === 100) {
            badge.removeClass('bg-rose-50 border-rose-200 text-rose-700 bg-amber-50 border-amber-200 text-amber-700')
                 .addClass('bg-emerald-50 border-emerald-200 text-emerald-700');
            statusText.removeClass('text-rose-600 text-amber-600').addClass('text-emerald-600').text('Tepat 100%');
            progressBar.removeClass('bg-rose-500 bg-amber-500').addClass('bg-emerald-500');
        } else if (totalFixed < 100) {
            badge.removeClass('bg-rose-50 border-rose-200 text-rose-700 bg-emerald-50 border-emerald-200 text-emerald-700')
                 .addClass('bg-amber-50 border-amber-200 text-amber-700');
            statusText.removeClass('text-rose-600 text-emerald-600').addClass('text-amber-600').text(`Kurang ${(100 - totalFixed).toFixed(2)}%`);
            progressBar.removeClass('bg-rose-500 bg-emerald-500').addClass('bg-amber-500');
        } else {
            badge.removeClass('bg-amber-50 border-amber-200 text-amber-700 bg-emerald-50 border-emerald-200 text-emerald-700')
                 .addClass('bg-rose-50 border-rose-200 text-rose-700');
            statusText.removeClass('text-amber-600 text-emerald-600').addClass('text-rose-600').text(`Kelebihan ${(totalFixed - 100).toFixed(2)}%`);
            progressBar.removeClass('bg-amber-500 bg-emerald-500').addClass('bg-rose-500');
        }
    }

    $(document).on('input', '.bobot-input', updateLiveBobot);
    updateLiveBobot();

    $('#formSetTargetV2').on('submit', function(e) {
        let total = 0;
        $('.bobot-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });

        if (Math.round(total * 100) / 100 !== 100) {
            e.preventDefault();
            Swal.fire({
                title: 'Bobot Belum 100%',
                text: `Total akumulasi bobot saat ini adalah ${total.toFixed(2)}%. Pastikan total tepat 100%.`,
                icon: 'warning',
                confirmButtonColor: '{{ $themeColor1 }}'
            });
            return false;
        }

        const btn = $('#btnSubmitSetTarget');
        btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed').html(`
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Menyimpan Target...</span>
        `);
    });
});
</script>
@endpush

@endsection
