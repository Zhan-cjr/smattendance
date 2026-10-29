@extends('v2.layouts.app')

@section('titlepage', 'Laporan Cuti Karyawan')
@section('page_title', 'Laporan Cuti Karyawan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $currentYear = date('Y');
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMBS) ================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ti ti-calendar-off text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Laporan Rekapitulasi Cuti Karyawan</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Cetak dan ekspor rekapitulasi kuota jatah cuti tahunan, cuti khusus, dan sisa saldo cuti pegawai
            </p>
        </div>

        <!-- Right Side: Breadcrumbs -->
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-file-text text-sm"></i>
                    <span>Laporan</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Cuti</span>
            </nav>
        </div>
    </div>

    <!-- ================= 2. FORM REPORT FILTER & GENERATOR ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Left Side: Main Parameter Form -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <!-- Form Card Header -->
                <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white shadow-2xs"
                             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-calendar-user text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 tracking-tight">Parameter Rekapitulasi Cuti</h2>
                        </div>
                    </div>
                    <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <i class="ti ti-check text-xs"></i>
                        Leave Ledger
                    </span>
                </div>

                <!-- Form Content -->
                <form action="{{ route('laporan.cetakcuti') }}" method="POST" target="_blank" id="formLaporanCuti" class="p-4 sm:p-5 space-y-3.5" novalidate>
                    @csrf

                    <!-- Filter Organisasi: Cabang & Departemen -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Cabang -->
                        <div class="space-y-1">
                            <label for="kode_cabang_cuti" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-building text-slate-400 text-xs"></i>
                                <span>Kantor / Cabang</span>
                            </label>
                            <select name="kode_cabang" id="kode_cabang_cuti" class="select2Cuti w-full">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabang as $d)
                                    <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Departemen -->
                        <div class="space-y-1">
                            <label for="kode_dept_cuti" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-category text-slate-400 text-xs"></i>
                                <span>Departemen / Divisi</span>
                            </label>
                            <select name="kode_dept" id="kode_dept_cuti" class="select2Cuti w-full">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Jenis Cuti & Tahun Rekap -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Jenis Cuti -->
                        <div class="space-y-1">
                            <label for="kode_cuti" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-bookmark text-slate-400 text-xs"></i>
                                <span>Jenis Cuti <span class="text-rose-500">*</span></span>
                            </label>
                            <select name="kode_cuti" id="kode_cuti" class="select2Cuti w-full">
                                <option value="">Pilih Jenis Cuti</option>
                                @foreach ($cuti as $d)
                                    <option value="{{ $d->kode_cuti }}">{{ textUpperCase($d->jenis_cuti) }} ({{ $d->jumlah_hari }} Hari)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tahun Rekap -->
                        <div class="space-y-1">
                            <label for="tahun_cuti" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-calendar-event text-slate-400 text-xs"></i>
                                <span>Tahun Periode Cuti</span>
                            </label>
                            <select name="tahun" id="tahun_cuti" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                <option value="">Pilih Tahun</option>
                                @for ($t = $start_year; $t <= date('Y'); $t++)
                                    <option {{ $currentYear == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Action Submit Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                        <!-- Cetak Button -->
                        <button type="submit" name="submitButton" value="1" id="btnCetak"
                                class="w-full sm:flex-1 h-11 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-[0.98] cursor-pointer hover:opacity-95 flex items-center justify-center gap-2"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-printer text-base"></i>
                            <span>Cetak Laporan Cuti</span>
                        </button>

                        <!-- Export Excel Button -->
                        <button type="submit" name="exportButton" value="1" id="btnExport"
                                class="w-full sm:flex-1 h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                            <i class="ti ti-file-spreadsheet text-base"></i>
                            <span>Export Data ke Excel</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Right Side: Info & Guidance Widget Cards -->
        <div class="lg:col-span-4 space-y-3">
            
            <!-- Quick Summary Guide Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs space-y-3">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-[11px] uppercase tracking-wider">
                    <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <i class="ti ti-info-circle text-xs"></i>
                    </div>
                    <span>Ketentuan Rekap Cuti</span>
                </div>

                <div class="space-y-2 text-[11.5px] text-slate-600">
                    <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ti ti-circle-check text-emerald-600 text-xs mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="text-slate-800 block text-[11px] mb-0.5">Cuti Disetujui (Approved)</strong>
                            <span class="text-[10.5px] leading-relaxed">Hanya pengajuan cuti yang telah disetujui pimpinan / HRD yang dihitung ke saldo pengambilan.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ti ti-calculator text-blue-600 text-xs mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="text-slate-800 block text-[11px] mb-0.5">Kalkulasi Sisa Cuti</strong>
                            <span class="text-[10.5px] leading-relaxed">Sisa cuti dihitung otomatis dari total kuota master cuti dikurangi total hari yang telah digunakan.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Checklist Card -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-4 shadow-2xs space-y-2.5 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 text-white/5 pointer-events-none">
                    <i class="ti ti-calendar-off text-[90px]"></i>
                </div>

                <div class="relative z-10 flex items-center gap-1.5 text-emerald-400 font-bold text-[11px] uppercase tracking-wider">
                    <i class="ti ti-sparkles text-xs"></i>
                    <span>Fitur Cuti Terpadu</span>
                </div>

                <ul class="relative z-10 space-y-1.5 text-[11px] text-slate-300">
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Distribusi penggunaan per bulan (Jan - Des)</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Audit kuota & riwayat cuti tahunan</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Ekspor format Excel siap audit ketenagakerjaan</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>

</div>

@endsection

@push('myscript')
<script>
    $(function() {
        // Initialize Select2 with V2 Theme styling
        const initSelect2 = (selector) => {
            $(selector).each(function() {
                var $this = $(this);
                var placeholder = $this.find("option:first").text() || "Pilih opsi...";
                $this.select2({
                    theme: "v2",
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $this.parent()
                });
            });
        };

        initSelect2(".select2Cuti");

        // Form Validation before submit
        $("#formLaporanCuti").on("submit", function(e) {
            const kode_cuti = $("#kode_cuti").val();
            const tahun = $("#tahun_cuti").val();

            if (!kode_cuti) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Parameter Belum Lengkap',
                    text: 'Silakan pilih Jenis Cuti terlebih dahulu!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (!tahun) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Parameter Belum Lengkap',
                    text: 'Silakan pilih Tahun Periode Cuti terlebih dahulu!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            return true;
        });
    });
</script>
@endpush
