@extends('v2.layouts.app')

@section('titlepage', 'Laporan Reimbursement')
@section('page_title', 'Laporan Reimbursement')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMBS) ================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ti ti-receipt-tax text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Laporan Klaim Reimbursement</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Cetak dan ekspor laporan pengajuan reimbursement, status approval, rincian biaya, serta rekapitulasi per kategori
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
                <span class="font-bold text-slate-800">Reimbursement</span>
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
                            <i class="ti ti-filter text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 tracking-tight">Filter Parameter Reimbursement</h2>
                        </div>
                    </div>
                    <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <i class="ti ti-check text-xs"></i>
                        Claim Report
                    </span>
                </div>

                <!-- Form Content -->
                <form action="{{ route('laporan.cetakreimbursement') }}" method="POST" target="_blank" id="formLaporanReimbursement" class="p-4 sm:p-5 space-y-3.5" novalidate>
                    @csrf

                    <!-- Filter Organisasi: Cabang & Departemen -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Cabang -->
                        <div class="space-y-1">
                            <label for="kode_cabang_reimburse" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-building text-slate-400 text-xs"></i>
                                <span>Kantor / Cabang</span>
                            </label>
                            <select name="kode_cabang" id="kode_cabang_reimburse" class="select2Reimburse w-full">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabang as $d)
                                    <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Departemen -->
                        <div class="space-y-1">
                            <label for="kode_dept_reimburse" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-category text-slate-400 text-xs"></i>
                                <span>Departemen / Divisi</span>
                            </label>
                            <select name="kode_dept" id="kode_dept_reimburse" class="select2Reimburse w-full">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Karyawan & Status Pengajuan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Karyawan (AJAX Filtered) -->
                        <div class="space-y-1">
                            <label for="nik_reimburse" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-user text-slate-400 text-xs"></i>
                                <span>Karyawan / Pegawai</span>
                            </label>
                            <select name="nik" id="nik_reimburse" class="select2Reimburse w-full">
                                <option value="">Semua Karyawan</option>
                            </select>
                        </div>

                        <!-- Status Pengajuan -->
                        <div class="space-y-1">
                            <label for="status_reimburse" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-progress-check text-slate-400 text-xs"></i>
                                <span>Status Persetujuan</span>
                            </label>
                            <select name="status" id="status_reimburse" class="select2Reimburse w-full">
                                <option value="">Semua Status</option>
                                <option value="P">Pending (Menunggu Persetujuan)</option>
                                <option value="A">Approved (Disetujui)</option>
                                <option value="R">Rejected (Ditolak)</option>
                                <option value="D">Dibayar (Selesai Cair)</option>
                                <option value="B">Batal (Dibatalkan)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Rentang Tanggal Pengajuan -->
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                            <i class="ti ti-calendar text-slate-400 text-xs"></i>
                            <span>Periode Tanggal Pengajuan <span class="text-rose-500">*</span></span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <i class="ti ti-calendar-event text-sm"></i>
                                </span>
                                <input type="text" name="dari" id="dari"
                                    class="flatpickr-date w-full h-9 pl-9 pr-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs"
                                    placeholder="Tanggal Mulai (Dari)" autocomplete="off" required>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <i class="ti ti-calendar-event text-sm"></i>
                                </span>
                                <input type="text" name="sampai" id="sampai"
                                    class="flatpickr-date w-full h-9 pl-9 pr-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs"
                                    placeholder="Tanggal Akhir (Sampai)" autocomplete="off" required>
                            </div>
                        </div>
                    </div>

                    <!-- Action Submit Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                        <!-- Cetak Button -->
                        <button type="submit" name="submitButton" value="1" id="submitButton"
                                class="w-full sm:flex-1 h-11 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-[0.98] cursor-pointer hover:opacity-95 flex items-center justify-center gap-2"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-printer text-base"></i>
                            <span>Cetak Laporan</span>
                        </button>

                        <!-- Export Excel Button -->
                        <button type="submit" name="exportButton" value="1" id="exportButton"
                                class="w-full sm:flex-1 h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                            <i class="ti ti-file-spreadsheet text-base"></i>
                            <span>Export Excel</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Right Side: Info & Status Guidance Widget Cards -->
        <div class="lg:col-span-4 space-y-3">
            
            <!-- Status Badges Legend Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs space-y-3">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-[11px] uppercase tracking-wider">
                    <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <i class="ti ti-info-circle text-xs"></i>
                    </div>
                    <span>Indikator Status Klaim</span>
                </div>

                <div class="space-y-2 text-[11.5px] text-slate-600">
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="inline-flex items-center gap-1.5 font-bold text-[11px] text-amber-700">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Pending
                        </span>
                        <span class="text-[10.5px] text-slate-500">Menunggu approval</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="inline-flex items-center gap-1.5 font-bold text-[11px] text-emerald-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Approved
                        </span>
                        <span class="text-[10.5px] text-slate-500">Telah disetujui</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="inline-flex items-center gap-1.5 font-bold text-[11px] text-blue-700">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            Dibayar
                        </span>
                        <span class="text-[10.5px] text-slate-500">Dana telah dicairkan</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="inline-flex items-center gap-1.5 font-bold text-[11px] text-rose-700">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Rejected / Batal
                        </span>
                        <span class="text-[10.5px] text-slate-500">Klaim ditolak / void</span>
                    </div>
                </div>
            </div>

            <!-- Summary Breakdown Feature Card -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-4 shadow-2xs space-y-2.5 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 text-white/5 pointer-events-none">
                    <i class="ti ti-receipt-tax text-[90px]"></i>
                </div>

                <div class="relative z-10 flex items-center gap-1.5 text-emerald-400 font-bold text-[11px] uppercase tracking-wider">
                    <i class="ti ti-sparkles text-xs"></i>
                    <span>Fitur Laporan Reimbursement</span>
                </div>

                <ul class="relative z-10 space-y-1.5 text-[11px] text-slate-300">
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Rincian item, nota & nominal per klaim</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Perbandingan nominal ajuan vs disetujui</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Tabel rekapitulasi per kategori pengeluaran</span>
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
        // Initialize Flatpickr Datepicker
        if (typeof flatpickr !== 'undefined') {
            $('.flatpickr-date').flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true,
                locale: "id"
            });
        }

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

        initSelect2(".select2Reimburse");

        // AJAX Load Karyawan based on Cabang & Departemen
        function loadKaryawan() {
            const kode_cabang = $("#kode_cabang_reimburse").val();
            const kode_dept = $("#kode_dept_reimburse").val();
            
            $.ajax({
                type: "GET",
                url: "{{ route('karyawan.getkaryawan') }}",
                data: {
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept
                },
                cache: false,
                success: function(respond) {
                    $("#nik_reimburse").empty();
                    $("#nik_reimburse").append("<option value=''>Semua Karyawan</option>");
                    respond.forEach(function(item) {
                        $("#nik_reimburse").append("<option value='" + item.nik + "'>" + item.nik + " - " + item.nama_karyawan + "</option>");
                    });
                }
            });
        }

        $("#kode_cabang_reimburse, #kode_dept_reimburse").change(function() {
            loadKaryawan();
        });

        loadKaryawan();

        // Form Validation before submit
        $("#formLaporanReimbursement").on("submit", function(e) {
            const dari = $("#dari").val();
            const sampai = $("#sampai").val();
            
            if (!dari) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Parameter Belum Lengkap',
                    text: 'Tanggal Mulai (Dari) harus diisi!',
                    confirmButtonText: 'OK',
                    didClose: () => {
                        $("#dari").focus();
                    }
                });
                return false;
            }

            if (!sampai) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Parameter Belum Lengkap',
                    text: 'Tanggal Akhir (Sampai) harus diisi!',
                    confirmButtonText: 'OK',
                    didClose: () => {
                        $("#sampai").focus();
                    }
                });
                return false;
            }

            return true;
        });
    });
</script>
@endpush
