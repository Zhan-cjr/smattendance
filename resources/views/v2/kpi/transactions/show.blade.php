@extends('v2.layouts.app')

@section('titlepage', 'Detail & Realisasi KPI')
@section('page_title', 'Detail & Realisasi KPI')

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
                    <span class="text-slate-700">Detail & Realisasi</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 mt-0.5">
                    <i class="ti ti-file-analytics text-2xl" style="color: {{ $themeColor1 }};"></i>
                    <span>Detail & Realisasi KPI</span>
                </h1>
            </div>
        </div>

        <!-- Breadcrumbs & Quick Action -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('kpi.transactions.index') }}" class="hover:text-slate-700 transition">KPI</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Detail Transaksi</span>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('kpi.transactions.print', $kpi_employee->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-2xs cursor-pointer">
                    <i class="ti ti-printer text-sm"></i>
                    <span>Cetak Laporan</span>
                </a>

                @can('kpi.transaction.approve')
                    @if($kpi_employee->status == 'submitted')
                        <button type="button" id="btnApproveKPI"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-circle-check text-sm"></i>
                            <span>Setujui KPI</span>
                        </button>
                        <form id="formApproveKPI" action="{{ route('kpi.transactions.approve', $kpi_employee->id) }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @elseif($kpi_employee->status == 'approved')
                        <button type="button" id="btnUnapproveKPI"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer">
                            <i class="ti ti-rotate-2 text-sm"></i>
                            <span>Batalkan Approve</span>
                        </button>
                        <form id="formUnapproveKPI" action="{{ \Illuminate\Support\Facades\Route::has('kpi.transactions.unapprove') ? route('kpi.transactions.unapprove', $kpi_employee->id) : '#' }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. PROFILE & SCORE SUMMARY CARD ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        
        <!-- Left: Profile Info Card (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                    @if(!empty($kpi_employee->karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $kpi_employee->karyawan->foto))
                        <img src="{{ getfotoKaryawan($kpi_employee->karyawan->foto) }}" 
                             alt="{{ $kpi_employee->karyawan->nama_karyawan }}" 
                             class="w-14 h-14 rounded-2xl object-cover border border-slate-200 shrink-0 shadow-2xs">
                    @else
                        <div class="w-14 h-14 rounded-2xl text-white font-bold flex items-center justify-center text-xl shrink-0 shadow-2xs"
                             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            {{ substr($kpi_employee->karyawan->nama_karyawan, 0, 1) }}
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-900 truncate">
                                {{ $kpi_employee->karyawan->nama_karyawan }}
                            </h2>
                            <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200/80">
                                {{ $kpi_employee->karyawan->nik_show ?? $kpi_employee->karyawan->nik }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-1.5 text-xs text-slate-500 font-medium">
                            <span class="flex items-center gap-1.5">
                                <i class="ti ti-briefcase text-slate-400"></i>
                                {{ $kpi_employee->karyawan->jabatan->nama_jabatan ?? '-' }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="flex items-center gap-1.5">
                                <i class="ti ti-building text-slate-400"></i>
                                {{ $kpi_employee->karyawan->departemen->nama_dept ?? '-' }}
                            </span>
                            <span class="text-slate-300">•</span>
                            <span class="flex items-center gap-1.5">
                                <i class="ti ti-calendar-event text-slate-400"></i>
                                Join: {{ date('d M Y', strtotime($kpi_employee->karyawan->tanggal_masuk)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Periode Info -->
                <div class="pt-3 sm:pt-0 border-t sm:border-t-0 sm:border-l sm:pl-5 border-slate-100 shrink-0">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Periode KPI</span>
                    <span class="text-xs font-bold text-slate-800 mt-0.5 block">{{ $kpi_employee->period->nama_periode }}</span>
                    <span class="text-[11px] text-slate-500 font-medium block">
                        {{ date('d M Y', strtotime($kpi_employee->period->start_date)) }} - {{ date('d M Y', strtotime($kpi_employee->period->end_date)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Right: Score & Grade Card (4 cols) -->
        <div class="lg:col-span-4 rounded-2xl p-4 sm:p-5 text-white shadow-2xs flex items-center justify-between relative overflow-hidden"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            
            <div class="relative z-10">
                <span class="text-[11px] font-bold uppercase tracking-wider text-white/80 block">Total Nilai Capaian</span>
                <div class="text-3xl sm:text-4xl font-black text-white mt-1" id="totalNilaiDisplay">
                    {{ number_format($kpi_employee->total_nilai, 2) }}
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-xs text-white/90 font-medium">Status:</span>
                    @if ($kpi_employee->status == 'draft')
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-amber-400 text-amber-950">Draft</span>
                    @elseif ($kpi_employee->status == 'submitted')
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-sky-300 text-sky-950">Submitted</span>
                    @elseif ($kpi_employee->status == 'approved')
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-white text-emerald-900">Approved</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-white/20 text-white">{{ $kpi_employee->status }}</span>
                    @endif
                </div>
            </div>

            <div class="text-center relative z-10 bg-white/15 backdrop-blur-xs rounded-2xl px-5 py-3.5 border border-white/20">
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/80 block">Grade</span>
                <span class="text-3xl sm:text-4xl font-black text-white block mt-0.5" id="gradeDisplay">
                    {{ $kpi_employee->grade ?? '-' }}
                </span>
            </div>

            <!-- Background subtle pattern -->
            <div class="absolute -bottom-6 -right-6 w-32 h-32 rounded-full bg-white/10 pointer-events-none"></div>
        </div>

    </div>

    <!-- ================= 3. REALISASI FORM & CARD LIST ================= -->
    <form action="{{ route('kpi.transactions.update', $kpi_employee->id) }}?{{ http_build_query(request()->query()) }}" method="POST" id="formRealisasiV2">
        @csrf

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="ti ti-list-check text-slate-400"></i>
                        <span>Matriks Indikator & Input Realisasi</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Masukkan realisasi aktual kinerja untuk menghitung skor capaian secara otomatis
                    </p>
                </div>
                @if($kpi_employee->status == 'approved')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
                        <i class="ti ti-lock text-sm"></i> Terkunci (Approved)
                    </span>
                @endif
            </div>
        </div>

        <!-- List Card Indikator Realisasi -->
        <div class="space-y-3">
            @foreach ($kpi_employee->details as $detail)
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 transition-all duration-200 hover:border-slate-300 kpi-row"
                     data-target="{{ $detail->target }}" 
                     data-jenis-target="{{ $detail->indicator->jenis_target }}" 
                     data-bobot="{{ $detail->bobot }}">
                    
                    <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">

                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        
                        <!-- Left: Detail Indikator -->
                        <div class="flex items-start gap-3.5 flex-1 min-w-0">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 text-slate-700 bg-slate-100 border border-slate-200">
                                {{ $loop->iteration }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm sm:text-base font-bold text-slate-800">
                                        {{ $detail->indicator->nama_indikator }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                                        {{ $detail->indicator->satuan }}
                                    </span>
                                    @if($detail->indicator->jenis_target == 'min')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="ti ti-arrow-down text-[10px]"></i> Min (Makin kecil makin baik)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="ti ti-arrow-up text-[10px]"></i> Max (Makin besar makin baik)
                                        </span>
                                    @endif
                                    @if($detail->indicator->mode == 'auto')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                            <i class="ti ti-cpu text-[10px]"></i> Auto ({{ $detail->indicator->metric_source }})
                                        </span>
                                    @endif
                                </div>
                                @if($detail->indicator->deskripsi)
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $detail->indicator->deskripsi }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Right: Target, Bobot, Realisasi & Skor -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 items-center gap-3 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                            <!-- Target -->
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Target</span>
                                <span class="text-xs sm:text-sm font-bold text-slate-800">
                                    {{ $detail->target }} <span class="text-[11px] font-normal text-slate-500">{{ $detail->indicator->satuan }}</span>
                                </span>
                            </div>

                            <!-- Bobot -->
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bobot</span>
                                <span class="text-xs sm:text-sm font-bold text-slate-800">
                                    {{ $detail->bobot }}%
                                </span>
                            </div>

                            <!-- Input Realisasi -->
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Realisasi</label>
                                @if(strtolower($detail->indicator->satuan) == 'skala')
                                    @if($kpi_employee->status == 'approved' || $detail->indicator->mode == 'auto')
                                        <input type="number" step="0.01" class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-700 realisasi-input" 
                                               name="realisasi[]" value="{{ $detail->realisasi }}" readonly>
                                    @else
                                        <select class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all realisasi-input cursor-pointer" 
                                                name="realisasi[]" required style="--tw-ring-color: {{ $themeColor1 }};">
                                            <option value="">Pilih Skala</option>
                                            <option value="1" {{ (int)$detail->realisasi == 1 ? 'selected' : '' }}>1 (Sangat Kurang)</option>
                                            <option value="2" {{ (int)$detail->realisasi == 2 ? 'selected' : '' }}>2 (Kurang)</option>
                                            <option value="3" {{ (int)$detail->realisasi == 3 ? 'selected' : '' }}>3 (Cukup)</option>
                                            <option value="4" {{ (int)$detail->realisasi == 4 ? 'selected' : '' }}>4 (Baik)</option>
                                            <option value="5" {{ (int)$detail->realisasi == 5 ? 'selected' : '' }}>5 (Sangat Baik)</option>
                                        </select>
                                    @endif
                                @else
                                    <div class="relative">
                                        <input type="number" step="0.01" 
                                               class="w-full px-3 py-2 {{ $kpi_employee->status == 'approved' || $detail->indicator->mode == 'auto' ? 'bg-slate-100 text-slate-700 cursor-not-allowed' : 'bg-slate-50 text-slate-800' }} border border-slate-200 rounded-xl text-xs sm:text-sm font-bold focus:outline-none focus:ring-2 focus:border-transparent transition-all realisasi-input" 
                                               name="realisasi[]" value="{{ $detail->realisasi }}" required 
                                               {{ $kpi_employee->status == 'approved' || $detail->indicator->mode == 'auto' ? 'readonly' : '' }}
                                               style="--tw-ring-color: {{ $themeColor1 }};">
                                    </div>
                                @endif
                            </div>

                            <!-- Nilai / Skor -->
                            <div class="p-2.5 rounded-xl bg-emerald-50/60 border border-emerald-100 text-center">
                                <span class="block text-[10px] font-bold text-emerald-800 uppercase tracking-wider">Skor</span>
                                <span class="text-xs sm:text-sm font-black text-emerald-700 skor-display">
                                    {{ number_format($detail->skor, 2) }}
                                </span>
                            </div>

                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <!-- Submit Realisasi Button Footer -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4">
            <a href="{{ route('kpi.transactions.index', request()->query()) }}" 
               class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-2xs text-center cursor-pointer">
                Kembali ke Daftar
            </a>

            @if($kpi_employee->status != 'approved')
                <button type="submit" id="btnSubmitRealisasi"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-device-floppy text-base"></i>
                    <span>Simpan & Hitung Realisasi</span>
                </button>
            @endif
        </div>

    </form>

</div>

@push('myscript')
<script>
$(document).ready(function() {
    function calculateKPI() {
        let totalScore = 0;
        
        $('.kpi-row').each(function() {
            const row = $(this);
            const target = parseFloat(row.data('target')) || 0;
            const jenisTarget = row.data('jenis-target');
            const bobot = parseFloat(row.data('bobot')) || 0;
            const realisasiVal = row.find('.realisasi-input').val();
            const realisasi = parseFloat(realisasiVal);
            
            let score = 0;
            
            if (!isNaN(realisasi)) {
                if (jenisTarget === 'max') {
                    if (target > 0) {
                        score = (realisasi / target) * bobot;
                    }
                } else { // min
                    if (realisasi === 0) {
                        score = bobot;
                    } else {
                        score = (target / realisasi) * bobot;
                    }
                }
                
                // Skor tidak boleh melebihi bobot (poin maksimal)
                if (score > bobot) {
                    score = bobot;
                }
            }
            
            // Update individual display
            row.find('.skor-display').text(score.toFixed(2));
            totalScore += score;
        });
        
        // Update total score display
        $('#totalNilaiDisplay').text(totalScore.toFixed(2));
        
        // Update Grade
        let grade = 'E';
        if (totalScore >= 90) grade = 'A';
        else if (totalScore >= 80) grade = 'B';
        else if (totalScore >= 70) grade = 'C';
        else if (totalScore >= 60) grade = 'D';
        else grade = 'E';
        
        $('#gradeDisplay').text(grade);
    }
    
    // Bind change/input events
    $(document).on('input change', '.realisasi-input', calculateKPI);

    // SweetAlert2 Approve Confirmation
    $('#btnApproveKPI').on('click', function() {
        Swal.fire({
            title: 'Setujui Transaksi KPI?',
            text: 'Setelah disetujui, realisasi KPI akan berstatus Approved dan dikunci.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '{{ $themeColor1 }}',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formApproveKPI').submit();
            }
        });
    });

    // SweetAlert2 Unapprove Confirmation
    $('#btnUnapproveKPI').on('click', function() {
        Swal.fire({
            title: 'Batalkan Persetujuan?',
            text: 'Status KPI akan dikembalikan ke Submitted sehingga realisasi dapat diedit kembali.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Batalkan Approve',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formUnapproveKPI').submit();
            }
        });
    });

    // Form submit loading spinner
    $('#formRealisasiV2').on('submit', function() {
        const btn = $('#btnSubmitRealisasi');
        btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed').html(`
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Menyimpan Realisasi...</span>
        `);
    });
});
</script>
@endpush

@endsection
