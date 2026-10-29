@extends('v2.layouts.app')

@section('titlepage', 'Generate Pembayaran Pinjaman')
@section('page_title', 'Generate Pembayaran Pinjaman')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle with Back Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('pinjaman.index') }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('pinjaman.index') }}" class="hover:text-emerald-600 transition-colors">Pinjaman</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Generate Massal</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 mt-0.5">
                    <i class="ti ti-settings-automation text-2xl" style="color: {{ $themeColor1 }};"></i>
                    <span>Generate Pembayaran Pinjaman Massal</span>
                </h1>
            </div>
        </div>

        <!-- Breadcrumb Navigation -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('pinjaman.index') }}" class="hover:text-slate-700 transition">Pinjaman</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Generate</span>
            </nav>
        </div>
    </div>

    <!-- ================= 2. FORM GENERATE CONTAINER ================= -->
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-xl overflow-hidden">
            <!-- Header Card -->
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-2xs text-white"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-settings-automation text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                            Pemrosesan Otomatis Potongan Gaji
                        </h3>
                        <p class="text-[11px] text-slate-500">
                            Eksekusi pemotongan angsuran pinjaman periode bulanan
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form action="{{ route('pinjaman.prosesgenerate') }}" method="POST" id="formGenerateV2" class="p-6 space-y-4">
                @csrf

                <!-- Info Alert -->
                <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-800 text-xs flex items-start gap-3">
                    <i class="ti ti-info-circle text-lg text-sky-600 shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold block mb-0.5">Mekanisme Generate Massal:</span>
                        Sistem akan mencari seluruh <strong>Rencana Cicilan Pinjaman</strong> yang jatuh tempo pada bulan & tahun terpilih, lalu membuat record pembayaran secara otomatis untuk portal payroll & slip gaji.
                    </div>
                </div>

                <!-- Input Bulan & Tahun Gaji -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Bulan Periode Gaji <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                                <i class="ti ti-calendar-month text-sm"></i>
                            </span>
                            <select name="bulan" id="bulan" required
                                    class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-bold shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                                @foreach(config('global.list_bulan') as $d)
                                    <option value="{{ $d['kode_bulan'] }}" {{ date('n') == (int)$d['kode_bulan'] ? 'selected' : '' }}>
                                        {{ $d['nama_bulan'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Tahun Periode Gaji <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                                <i class="ti ti-calendar text-sm"></i>
                            </span>
                            <select name="tahun" id="tahun" required
                                    class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-bold shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                                @for($t = $start_year; $t <= date('Y'); $t++)
                                    <option value="{{ $t }}" {{ date('Y') == $t ? 'selected' : '' }}>
                                        Tahun {{ $t }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit Button & Cancel -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <a href="{{ route('pinjaman.index') }}" 
                       class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition active:scale-95 cursor-pointer">
                        Batal
                    </a>
                    <button type="submit" id="btnProsesV2" 
                            class="px-5 py-2.5 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95 flex items-center gap-1.5"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-player-play text-sm"></i>
                        <span>Mulai Generate Pembayaran</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $("#formGenerateV2").submit(function(e) {
            let bulan = $("#bulan option:selected").text();
            let tahun = $("#tahun").val();
            
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Generate Massal?',
                text: "Sistem akan membuat record pemotongan angsuran pinjaman untuk periode " + bulan + " " + tahun + ". Lanjutkan?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '{{ $themeColor1 }}',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Generate Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-slate-100',
                    confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold shadow-xs',
                    cancelButton: 'px-4 py-2 rounded-xl text-xs font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#btnProsesV2").prop('disabled', true).html('<i class="ti ti-loader-2 animate-spin text-sm"></i> <span>Memproses...</span>');
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
