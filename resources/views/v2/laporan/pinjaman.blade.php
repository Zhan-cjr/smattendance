@extends('v2.layouts.app')

@section('titlepage', 'Laporan Pinjaman Karyawan')
@section('page_title', 'Laporan Pinjaman Karyawan')

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
                <i class="ti ti-credit-card text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Laporan Pinjaman & Kasbon Pegawai</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Cetak dan ekspor laporan detail angsuran pinjaman aktif/lunas serta rekapitulasi mutasi bulanan kasbon
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
                <span class="font-bold text-slate-800">Pinjaman</span>
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
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 tracking-tight">Parameter Laporan Pinjaman</h2>
                        </div>
                    </div>
                    <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <i class="ti ti-check text-xs"></i>
                        Loan Ledger
                    </span>
                </div>

                <!-- Form Content -->
                <form action="{{ route('laporan.cetakpinjaman') }}" method="POST" target="_blank" id="formLaporanPinjaman" class="p-4 sm:p-5 space-y-3.5" novalidate>
                    @csrf

                    <!-- Jenis Format Laporan (Detail Pinjaman vs Rekap Bulanan) -->
                    <div class="space-y-1">
                        <label for="jenis_laporan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                            <i class="ti ti-layout-grid text-slate-400 text-xs"></i>
                            <span>Jenis & Format Laporan <span class="text-rose-500">*</span></span>
                        </label>
                        <select name="jenis_laporan" id="jenis_laporan" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                            <option value="detail" selected>Detail Pinjaman (Rincian Tenor, Terbayar, Sisa & Status)</option>
                            <option value="rekap">Rekap Mutasi Bulanan (Saldo Awal, Penambah, Potong Gaji, Cash, Saldo Akhir)</option>
                        </select>
                    </div>

                    <!-- Filter Organisasi: Cabang & Departemen -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Cabang -->
                        <div class="space-y-1">
                            <label for="kode_cabang_pinjaman" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-building text-slate-400 text-xs"></i>
                                <span>Kantor / Cabang</span>
                            </label>
                            <select name="kode_cabang" id="kode_cabang_pinjaman" class="select2Pinjaman w-full">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabang as $d)
                                    <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Departemen -->
                        <div class="space-y-1">
                            <label for="kode_dept_pinjaman" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-category text-slate-400 text-xs"></i>
                                <span>Departemen / Divisi</span>
                            </label>
                            <select name="kode_dept" id="kode_dept_pinjaman" class="select2Pinjaman w-full">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Karyawan & Status Pinjaman -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Karyawan (AJAX Filtered) -->
                        <div class="space-y-1">
                            <label for="nik_pinjaman" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-user text-slate-400 text-xs"></i>
                                <span>Karyawan / Peminjam</span>
                            </label>
                            <select name="nik" id="nik_pinjaman" class="select2Pinjaman w-full">
                                <option value="">Semua Karyawan</option>
                            </select>
                        </div>

                        <!-- Status Pinjaman (Hanya untuk Mode Detail) -->
                        <div class="space-y-1" id="group_status">
                            <label for="status_pinjaman" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-progress-check text-slate-400 text-xs"></i>
                                <span>Status Pinjaman</span>
                            </label>
                            <select name="status" id="status_pinjaman" class="select2Pinjaman w-full">
                                <option value="">Semua Status</option>
                                <option value="A">Aktif (Sedang Berjalan)</option>
                                <option value="L">Lunas (Selesai Dibayar)</option>
                                <option value="B">Batal</option>
                            </select>
                        </div>
                    </div>

                    <!-- Mode 1: Rentang Tanggal Pinjaman (Untuk Detail) -->
                    <div class="space-y-1" id="baris_tanggal">
                        <label class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                            <i class="ti ti-calendar text-slate-400 text-xs"></i>
                            <span>Periode Tanggal Pinjaman <span class="text-rose-500">*</span></span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <i class="ti ti-calendar-event text-sm"></i>
                                </span>
                                <input type="text" name="dari" id="dari"
                                    class="flatpickr-date w-full h-9 pl-9 pr-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs"
                                    placeholder="Tanggal Mulai (Dari)" autocomplete="off">
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                                    <i class="ti ti-calendar-event text-sm"></i>
                                </span>
                                <input type="text" name="sampai" id="sampai"
                                    class="flatpickr-date w-full h-9 pl-9 pr-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs"
                                    placeholder="Tanggal Akhir (Sampai)" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <!-- Mode 2: Bulan & Tahun (Untuk Rekap Bulanan) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 hidden" id="baris_bulan_tahun">
                        <!-- Bulan Rekap -->
                        <div class="space-y-1">
                            <label for="bulan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-calendar text-slate-400 text-xs"></i>
                                <span>Bulan Periode <span class="text-rose-500">*</span></span>
                            </label>
                            <select name="bulan" id="bulan" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                <option value="">Pilih Bulan</option>
                                @foreach ($list_bulan as $d)
                                    <option {{ date('m') == $d['kode_bulan'] ? 'selected' : '' }} value="{{ $d['kode_bulan'] }}">{{ $d['nama_bulan'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tahun Rekap -->
                        <div class="space-y-1">
                            <label for="tahun" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-calendar-time text-slate-400 text-xs"></i>
                                <span>Tahun Periode <span class="text-rose-500">*</span></span>
                            </label>
                            <select name="tahun" id="tahun" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
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
                            <span>Export Data ke Excel</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Right Side: Info & Guidelines Widget Cards -->
        <div class="lg:col-span-4 space-y-3">
            
            <!-- Quick Summary Guide Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs space-y-3">
                <div class="flex items-center gap-2 text-slate-800 font-bold text-[11px] uppercase tracking-wider">
                    <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <i class="ti ti-info-circle text-xs"></i>
                    </div>
                    <span>Struktur & Alur Pinjaman</span>
                </div>

                <div class="space-y-2 text-[11.5px] text-slate-600">
                    <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ti ti-file-analytics text-emerald-600 text-xs mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="text-slate-800 block text-[11px] mb-0.5">Mode Detail Pinjaman</strong>
                            <span class="text-[10.5px] leading-relaxed">Menampilkan data setiap transaksi pinjaman, jumlah pokok, tenor angsuran, total terbayar, dan sisa kewajiban.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ti ti-calculator text-blue-600 text-xs mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="text-slate-800 block text-[11px] mb-0.5">Mode Rekap Mutasi Bulanan</strong>
                            <span class="text-[10.5px] leading-relaxed">Laporan neraca mutasi pinjaman pegawai mencakup saldo awal, pinjaman baru, potongan payroll gaji, bayar cash, dan saldo akhir.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Checklist Card -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-4 shadow-2xs space-y-2.5 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 text-white/5 pointer-events-none">
                    <i class="ti ti-credit-card text-[90px]"></i>
                </div>

                <div class="relative z-10 flex items-center gap-1.5 text-emerald-400 font-bold text-[11px] uppercase tracking-wider">
                    <i class="ti ti-sparkles text-xs"></i>
                    <span>Fitur Pinjaman Terpadu</span>
                </div>

                <ul class="relative z-10 space-y-1.5 text-[11px] text-slate-300">
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Otomatisasi pemotongan angsuran slip gaji</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Tracking sisa saldo real-time per peminjam</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Ekspor format Excel siap audit keuangan</span>
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

        initSelect2(".select2Pinjaman");

        // AJAX Load Karyawan based on Cabang & Departemen
        function loadKaryawan() {
            const kode_cabang = $("#kode_cabang_pinjaman").val();
            const kode_dept = $("#kode_dept_pinjaman").val();
            
            $.ajax({
                type: "GET",
                url: "{{ route('karyawan.getkaryawan') }}",
                data: {
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept
                },
                cache: false,
                success: function(respond) {
                    $("#nik_pinjaman").empty();
                    $("#nik_pinjaman").append("<option value=''>Semua Karyawan</option>");
                    if (Array.isArray(respond)) {
                        respond.forEach(function(item) {
                            $("#nik_pinjaman").append("<option value='" + item.nik + "'>" + item.nik + " - " + item.nama_karyawan + "</option>");
                        });
                    }
                }
            });
        }

        $("#kode_cabang_pinjaman, #kode_dept_pinjaman").change(function() {
            loadKaryawan();
        });

        loadKaryawan();

        // Toggle Form Inputs based on Jenis Laporan
        function toggleJenisLaporan() {
            const jenis = $("#jenis_laporan").val();
            if (jenis === 'rekap') {
                $("#baris_tanggal").addClass('hidden');
                $("#group_status").addClass('hidden');
                $("#baris_bulan_tahun").removeClass('hidden');
            } else {
                $("#baris_tanggal").removeClass('hidden');
                $("#group_status").removeClass('hidden');
                $("#baris_bulan_tahun").addClass('hidden');
            }
        }

        $("#jenis_laporan").on("change", toggleJenisLaporan);
        toggleJenisLaporan();

        // Form Validation before submit
        $("#formLaporanPinjaman").on("submit", function(e) {
            const jenis = $("#jenis_laporan").val();
            if (jenis === 'detail') {
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
            } else {
                const bulan = $("#bulan").val();
                const tahun = $("#tahun").val();
                
                if (!bulan) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Parameter Belum Lengkap',
                        text: 'Bulan periode harus dipilih!',
                        confirmButtonText: 'OK',
                        didClose: () => {
                            $("#bulan").focus();
                        }
                    });
                    return false;
                }
                if (!tahun) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Parameter Belum Lengkap',
                        text: 'Tahun periode harus dipilih!',
                        confirmButtonText: 'OK',
                        didClose: () => {
                            $("#tahun").focus();
                        }
                    });
                    return false;
                }
            }
            return true;
        });
    });
</script>
@endpush
