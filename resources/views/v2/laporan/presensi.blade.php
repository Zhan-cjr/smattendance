@extends('v2.layouts.app')

@section('titlepage', 'Laporan Presensi')
@section('page_title', 'Laporan Presensi')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $currentMonth = date('m');
    $currentYear = date('Y');
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMBS) ================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ti ti-file-analytics text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Laporan & Rekapitulasi Presensi</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Generate rekapitulasi kehadiran, akumulasi jam kerja, denda, lembur, dan ekspor ke cetak / Excel
            </p>
        </div>

        <!-- Right Side: Breadcrumbs -->
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-[11px] font-bold text-slate-700 shadow-2xs">
                <i class="ti ti-calendar-stats text-emerald-600"></i>
                <span>Periode: <strong class="text-slate-900">{{ DateToIndo(date('Y-m-d')) }}</strong></span>
            </div>
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
                            <i class="ti ti-adjustments-horizontal text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 tracking-tight">Parameter Laporan Presensi</h2>
                        </div>
                    </div>
                    <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <i class="ti ti-check text-xs"></i>
                        Live Filter
                    </span>
                </div>

                <!-- Form Content -->
                <form action="{{ route('laporan.cetakpresensi') }}" method="POST" target="_blank" id="formPresensi" class="p-4 sm:p-5 space-y-3.5" novalidate>
                    @csrf
                    <input type="hidden" name="format_laporan" value="1">

                    <!-- Filter Organisasi: Cabang & Departemen -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Cabang -->
                        <div class="space-y-1">
                            <label for="kode_cabang" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-building text-slate-400 text-xs"></i>
                                <span>Kantor / Cabang</span>
                            </label>
                            <select name="kode_cabang" id="kode_cabang" class="select2 w-full">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabang as $d)
                                    <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Departemen -->
                        <div class="space-y-1">
                            <label for="kode_dept" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-category text-slate-400 text-xs"></i>
                                <span>Departemen / Divisi</span>
                            </label>
                            <select name="kode_dept" id="kode_dept" class="select2 w-full">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Karyawan Target (Auto-Loaded via AJAX) -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label for="nik" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-user text-slate-400 text-xs"></i>
                                <span>Pilih Karyawan</span>
                            </label>
                            <span id="loadingKaryawan" class="hidden text-[10px] font-bold text-emerald-600 items-center gap-1">
                                <i class="ti ti-loader-2 animate-spin text-xs"></i>
                                <span>Memuat karyawan...</span>
                            </span>
                        </div>
                        <select name="nik" id="nik" class="select2 w-full">
                            <option value="">Semua Karyawan</option>
                        </select>
                    </div>

                    <!-- Tipe Periode Laporan -->
                    <div class="space-y-1.5 pt-1">
                        <label class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                            <i class="ti ti-calendar-time text-slate-400 text-xs"></i>
                            <span>Model Periode Presensi</span>
                        </label>

                        <!-- Radio-style Segment Cards for Periode Laporan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <!-- Periode Gaji (Cut-off) -->
                            <label class="relative flex items-center p-2.5 rounded-lg border border-slate-200/90 cursor-pointer transition hover:bg-slate-50/80 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                                <input type="radio" name="periode_laporan" value="1" class="sr-only" checked>
                                <div class="flex items-center gap-2">
                                    <div class="w-3.5 h-3.5 rounded-full border border-slate-300 flex items-center justify-center peer-checked:border-emerald-600 ring-offset-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-600 hidden indicator"></div>
                                    </div>
                                    <div>
                                        <span class="block text-[11px] font-bold text-slate-800">Periode Gaji</span>
                                        <span class="block text-[9.5px] text-slate-500 leading-tight">Cut-Off Payroll</span>
                                    </div>
                                </div>
                            </label>

                            <!-- Bulan Berjalan (1 - 31) -->
                            <label class="relative flex items-center p-2.5 rounded-lg border border-slate-200/90 cursor-pointer transition hover:bg-slate-50/80 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                                <input type="radio" name="periode_laporan" value="2" class="sr-only">
                                <div class="flex items-center gap-2">
                                    <div class="w-3.5 h-3.5 rounded-full border border-slate-300 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-600 hidden indicator"></div>
                                    </div>
                                    <div>
                                        <span class="block text-[11px] font-bold text-slate-800">Bulan Berjalan</span>
                                        <span class="block text-[9.5px] text-slate-500 leading-tight">Tgl 01 s/d 31</span>
                                    </div>
                                </div>
                            </label>

                            <!-- Range Tanggal Custom -->
                            <label class="relative flex items-center p-2.5 rounded-lg border border-slate-200/90 cursor-pointer transition hover:bg-slate-50/80 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                                <input type="radio" name="periode_laporan" value="3" class="sr-only">
                                <div class="flex items-center gap-2">
                                    <div class="w-3.5 h-3.5 rounded-full border border-slate-300 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-600 hidden indicator"></div>
                                    </div>
                                    <div>
                                        <span class="block text-[11px] font-bold text-slate-800">Range Tanggal</span>
                                        <span class="block text-[9.5px] text-slate-500 leading-tight">Kustom Tanggal</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Dynamic Row: Bulan & Tahun (for Mode 1 & 2) -->
                    <div id="baris_bulan_tahun" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Bulan -->
                        <div class="space-y-1">
                            <label for="bulan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-calendar text-slate-400 text-xs"></i>
                                <span>Pilih Bulan</span>
                            </label>
                            <select name="bulan" id="bulan" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                <option value="">Pilih Bulan</option>
                                @foreach ($list_bulan as $d)
                                    <option {{ $currentMonth == $d['kode_bulan'] ? 'selected' : '' }} value="{{ $d['kode_bulan'] }}">
                                        {{ $d['nama_bulan'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tahun -->
                        <div class="space-y-1">
                            <label for="tahun" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-calendar-event text-slate-400 text-xs"></i>
                                <span>Pilih Tahun</span>
                            </label>
                            <select name="tahun" id="tahun" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                <option value="">Pilih Tahun</option>
                                @for ($t = $start_year; $t <= date('Y'); $t++)
                                    <option {{ $currentYear == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Row: Range Tanggal (for Mode 3) -->
                    <div id="baris_tanggal" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Tanggal Dari -->
                        <div class="space-y-1">
                            <label for="dari" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-calendar-arrow-right text-slate-400 text-xs"></i>
                                <span>Dari Tanggal</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="dari" id="dari" placeholder="YYYY-MM-DD"
                                       class="w-full h-9 pl-9 pr-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs flatpickr-date">
                                <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Tanggal Sampai -->
                        <div class="space-y-1">
                            <label for="sampai" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-calendar-arrow-left text-slate-400 text-xs"></i>
                                <span>Sampai Tanggal</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="sampai" id="sampai" placeholder="YYYY-MM-DD"
                                       class="w-full h-9 pl-9 pr-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs flatpickr-date">
                                <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Format Rekapitulasi -->
                    <div class="space-y-1">
                        <label for="format_rekap" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                            <i class="ti ti-layout-grid text-slate-400 text-xs"></i>
                            <span>Format Struktur Rekap</span>
                        </label>
                        <select name="format_rekap" id="format_rekap" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                            <option value="1" selected>Format 1 (Tampilan Klasik / Standar)</option>
                            <option value="2">Format 2 (Struktur Baru & Komprehensif)</option>
                        </select>
                    </div>

                    <!-- Action Submit Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                        <!-- Cetak Button -->
                        <button type="submit" name="submitButton" value="1" id="btnCetak"
                                class="w-full sm:flex-1 h-11 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-[0.98] cursor-pointer hover:opacity-95 flex items-center justify-center gap-2"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-printer text-base"></i>
                            <span>Cetak Laporan / PDF</span>
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
                    <span>Panduan Parameter</span>
                </div>

                <div class="space-y-2 text-[11.5px] text-slate-600">
                    <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ti ti-calendar-star text-emerald-600 text-xs mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="text-slate-800 block text-[11px] mb-0.5">Periode Gaji (Cut-off)</strong>
                            <span class="text-[10.5px] leading-relaxed">Mengikuti tanggal cut-off payroll yang telah dikonfigurasi di menu Pengaturan Umum.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ti ti-calendar-month text-blue-600 text-xs mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="text-slate-800 block text-[11px] mb-0.5">Bulan Berjalan</strong>
                            <span class="text-[10.5px] leading-relaxed">Menghitung kehadiran dari tanggal 1 sampai akhir bulan kalender masehi.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ti ti-calendar-event text-amber-600 text-xs mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="text-slate-800 block text-[11px] mb-0.5">Range Tanggal Bebas</strong>
                            <span class="text-[10.5px] leading-relaxed">Pilih rentang tanggal khusus untuk audit mingguan atau evaluasi periode tertentu.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Checklist Card -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-4 shadow-2xs space-y-2.5 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 text-white/5 pointer-events-none">
                    <i class="ti ti-report-analytics text-[90px]"></i>
                </div>

                <div class="relative z-10 flex items-center gap-1.5 text-emerald-400 font-bold text-[11px] uppercase tracking-wider">
                    <i class="ti ti-sparkles text-xs"></i>
                    <span>Fitur Laporan Presensi</span>
                </div>

                <ul class="relative z-10 space-y-1.5 text-[11px] text-slate-300">
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Rekap Hadir, Sakit, Izin, Cuti & Alpa</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Kalkulasi otomatis Denda Terlambat</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Rincian jam lembur & lembur khusus</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Kompatibel untuk impor ke Slip Gaji</span>
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

        initSelect2(".select2");

        // Initialize Flatpickr for custom date fields
        if (typeof flatpickr !== 'undefined') {
            flatpickr(".flatpickr-date", {
                dateFormat: "Y-m-d",
                locale: "id",
                allowInput: true
            });
        }

        // AJAX Load Karyawan Function
        function loadKaryawan() {
            const kode_cabang = $("#kode_cabang").val();
            const kode_dept = $("#kode_dept").val();
            const dari = $("#dari").val();
            const targetSelect = $("#nik");
            const loadingIndicator = $("#loadingKaryawan");

            loadingIndicator.removeClass('hidden').addClass('inline-flex');

            $.ajax({
                type: "GET",
                url: "{{ route('karyawan.getkaryawan') }}",
                data: {
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept,
                    tanggal: dari
                },
                cache: false,
                success: function(respond) {
                    targetSelect.empty();
                    targetSelect.append("<option value=''>Semua Karyawan</option>");
                    if (Array.isArray(respond)) {
                        respond.forEach(function(item) {
                            targetSelect.append(`<option value="${item.nik}">${item.nik} - ${item.nama_karyawan}</option>`);
                        });
                    }
                    targetSelect.trigger('change.select2');
                },
                error: function(err) {
                    console.error("Gagal memuat karyawan:", err);
                },
                complete: function() {
                    loadingIndicator.removeClass('inline-flex').addClass('hidden');
                }
            });
        }

        // Trigger load karyawan on filter change
        $("#kode_cabang, #kode_dept, #dari").on("change", function() {
            loadKaryawan();
        });

        // Initial load
        loadKaryawan();

        // Toggle Periode Options Logic
        function updatePeriodeOptions() {
            const selectedPeriode = $('input[name="periode_laporan"]:checked').val();
            
            // Visual indicator for radio cards
            $('input[name="periode_laporan"]').each(function() {
                const isChecked = $(this).is(':checked');
                $(this).closest('label').find('.indicator').toggleClass('hidden', !isChecked);
                if (isChecked) {
                    $(this).closest('label').addClass('border-emerald-600 bg-emerald-50/40').removeClass('border-slate-200/90');
                } else {
                    $(this).closest('label').removeClass('border-emerald-600 bg-emerald-50/40').addClass('border-slate-200/90');
                }
            });

            if (selectedPeriode === "3") {
                // Range Tanggal
                $("#baris_tanggal").removeClass('hidden');
                $("#baris_bulan_tahun").addClass('hidden');
            } else {
                // Periode Gaji (1) or Bulan Berjalan (2)
                $("#baris_tanggal").addClass('hidden');
                $("#baris_bulan_tahun").removeClass('hidden');
            }
        }

        $('input[name="periode_laporan"]').on('change', updatePeriodeOptions);
        updatePeriodeOptions();

        // Form Validation before submit
        $("#formPresensi").on("submit", function(e) {
            const periode = $('input[name="periode_laporan"]:checked').val();
            const format_rekap = $("#format_rekap").val();

            if (!format_rekap) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Format Struktur Rekap harus dipilih!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (!periode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Model Periode Presensi harus dipilih!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (periode === "3") {
                const dari = $("#dari").val();
                const sampai = $("#sampai").val();

                if (!dari || !sampai) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Parameter Belum Lengkap',
                        text: 'Silakan isi tanggal Dari dan Sampai dengan lengkap!',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            } else {
                const bulan = $("#bulan").val();
                const tahun = $("#tahun").val();

                if (!bulan || !tahun) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Parameter Belum Lengkap',
                        text: 'Silakan pilih Bulan dan Tahun dengan benar!',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            }
            return true;
        });
    });
</script>
@endpush
