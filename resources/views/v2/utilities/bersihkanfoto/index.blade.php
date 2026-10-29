@extends('v2.layouts.app')

@section('titlepage', 'Bersihkan File Foto & Retensi')
@section('page_title', 'Bersihkan File Foto & Retensi')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMBS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-photo-x text-2xl text-rose-600"></i>
                <span>Bersihkan File Foto & Retensi</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Utilitas pembersihan storage hosting dari file foto lampiran lama tanpa menghapus record data presensi & checklist
            </p>
        </div>

        <!-- Breadcrumbs -->
        <nav class="flex items-center text-xs text-slate-400 font-medium self-start md:self-auto">
            <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                <i class="ti ti-home text-sm"></i>
                <span>Dashboard</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="text-slate-500 flex items-center gap-1">
                <i class="ti ti-tool text-sm"></i>
                <span>Utilities</span>
            </span>
            <span class="mx-2 text-slate-300">/</span>
            <span class="font-bold text-slate-800">Bersihkan Foto</span>
        </nav>
    </div>

    <!-- ================= 1B. AUTO-CLEAN SCHEDULER CARD ================= -->
    <div class="bg-gradient-to-r from-teal-50/70 via-white to-slate-50 border border-teal-200/80 border-l-4 border-l-teal-600 rounded-2xl shadow-xs p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center shrink-0">
                    <i class="ti ti-clock-bolt text-2xl"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-sm font-extrabold text-slate-900">
                            Auto Bersihkan Foto Berkala (Scheduler)
                        </h3>
                        <span id="autoBadge" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ ($setting && $setting->auto_hapus_foto) ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-200 text-slate-700' }}">
                            {{ ($setting && $setting->auto_hapus_foto) ? 'Aktif (Setiap Hari 02:30)' : 'Nonaktif' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                        Secara otomatis menghapus file fisik foto yang lebih lama dari batasan hari (<strong>default 60 hari</strong>).
                        <em class="text-slate-500">Data rekapitulasi, riwayat transaksi, dan audit trail di database tetap 100% aman</em>.
                    </p>
                </div>
            </div>

            <!-- Toggle & Days input -->
            <div class="flex flex-wrap items-center gap-4 bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs shrink-0">
                <div class="flex items-center gap-2">
                    <label for="auto_days" class="text-xs font-bold text-slate-600">Batas Retensi:</label>
                    <div class="flex items-center border border-slate-300 rounded-lg overflow-hidden bg-slate-50">
                        <input type="number" id="auto_days"
                            class="w-16 h-8 text-center text-xs font-extrabold text-slate-800 bg-white focus:outline-none"
                            value="{{ $setting->auto_hapus_foto_hari ?? 60 }}" min="7" max="365">
                        <span class="px-2 text-[11px] font-semibold text-slate-500 bg-slate-100 border-l border-slate-200">hari</span>
                    </div>
                </div>

                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="toggleAutoClean" class="sr-only peer"
                        {{ ($setting && $setting->auto_hapus_foto) ? 'checked' : '' }}>
                    <div class="relative w-10 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-teal-600"></div>
                    <span id="toggleLabel" class="text-xs font-extrabold text-slate-700">
                        {{ ($setting && $setting->auto_hapus_foto) ? 'Auto Bersihkan Aktif' : 'Aktifkan Auto' }}
                    </span>
                </label>
            </div>
        </div>

        <div class="mt-4 pt-3.5 border-t border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <span class="text-xs text-slate-500 flex items-center gap-1.5">
                <i class="ti ti-info-circle text-teal-600 text-sm"></i>
                <span>Meliputi: <strong class="text-slate-700">Foto Absensi Masuk/Pulang/Istirahat, Foto Kunjungan, Foto Aktivitas, dan Foto Checklist & Grid Kolase</strong>.</span>
            </span>

            <button type="button" id="btnQuickClean60"
                class="px-3.5 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold flex items-center justify-center gap-1.5 transition cursor-pointer shrink-0">
                <i class="ti ti-trash-x text-sm"></i>
                <span>Bersihkan Foto > {{ $setting->auto_hapus_foto_hari ?? 60 }} Hari Sekarang (1-Klik)</span>
            </button>
        </div>
    </div>

    <!-- ================= 2. MAIN CLEANUP CARD ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Solid Header -->
        <div class="px-5 py-3.5 flex items-center justify-between text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-trash"></i>
                </div>
                <h3 class="text-sm font-extrabold text-white tracking-tight">
                    Pembersihan Foto Manual Berdasarkan Periode
                </h3>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                Mode Aman (Storage Only)
            </span>
        </div>

        <!-- Form Container -->
        <form id="formBersihkanFotoV2" class="p-5 sm:p-6 space-y-6" novalidate>
            @csrf

            <!-- A. Pilih Jenis Foto / Dokumen (Interactive Visual Checkbox Cards) -->
            <div class="space-y-2.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                    <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                        1. Pilih Jenis File yang Akan Dibersihkan <span class="text-rose-500">*</span>
                    </label>
                    <button type="button" id="btnToggleAllJenis" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 self-start sm:self-auto cursor-pointer">
                        Batalkan Semua
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Foto Presensi -->
                    <label class="jenis-card flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition select-none">
                        <input type="checkbox" name="jenis[]" value="presensi" checked
                               class="jenis-checkbox mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 font-bold text-xs text-slate-800">
                                <i class="ti ti-camera text-emerald-700"></i>
                                <span>Foto Presensi</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                Foto check-in, check-out, & istirahat karyawan
                            </p>
                        </div>
                    </label>

                    <!-- Foto Kunjungan -->
                    <label class="jenis-card flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition select-none">
                        <input type="checkbox" name="jenis[]" value="kunjungan" checked
                               class="jenis-checkbox mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 font-bold text-xs text-slate-800">
                                <i class="ti ti-map-pin text-cyan-700"></i>
                                <span>Foto Kunjungan</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                Dokumentasi foto saat kunjungan dinas/sales
                            </p>
                        </div>
                    </label>

                    <!-- Foto Aktivitas Karyawan -->
                    <label class="jenis-card flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition select-none">
                        <input type="checkbox" name="jenis[]" value="aktivitas" checked
                               class="jenis-checkbox mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 font-bold text-xs text-slate-800">
                                <i class="ti ti-activity text-amber-700"></i>
                                <span>Foto Aktivitas</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                Foto log harian & kegiatan karyawan
                            </p>
                        </div>
                    </label>

                    <!-- Foto Checklist & Grid Kolase 4-in-1 -->
                    <label class="jenis-card flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition select-none">
                        <input type="checkbox" name="jenis[]" value="checklist" checked
                               class="jenis-checkbox mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 font-bold text-xs text-slate-800">
                                <i class="ti ti-list-check text-teal-700"></i>
                                <span>Foto Checklist & Grid Kolase 4-in-1</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">
                                Foto opening/closing toko & kolase checklist
                            </p>
                        </div>
                    </label>
                </div>
                <p class="error-feedback text-[11px] font-semibold text-rose-500 hidden items-center gap-1.5 mt-1" id="err_jenis">
                    <i class="ti ti-alert-circle text-sm"></i>
                    <span class="err-text">Pilih minimal satu jenis file foto yang akan dibersihkan.</span>
                </p>
            </div>

            <!-- B. Rentang Periode Tanggal -->
            <div class="space-y-2.5">
                <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                    2. Tentukan Periode Tanggal Pembersihan <span class="text-rose-500">*</span>
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tanggal Awal -->
                    <div class="space-y-1.5">
                        <label for="tanggal_awal" class="block text-xs font-bold text-slate-700">
                            Tanggal Awal <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="input-icon absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                                <i class="ti ti-calendar-event text-base"></i>
                            </span>
                            <input type="text" name="tanggal_awal" id="tanggal_awal" placeholder="YYYY-MM-DD" autocomplete="off"
                                   class="flatpickr-date w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                        </div>
                        <p class="error-feedback text-[11px] font-semibold text-rose-500 hidden items-center gap-1.5 mt-1" id="err_tanggal_awal">
                            <i class="ti ti-alert-circle text-sm"></i>
                            <span class="err-text">Tanggal awal wajib dipilih.</span>
                        </p>
                    </div>

                    <!-- Tanggal Akhir -->
                    <div class="space-y-1.5">
                        <label for="tanggal_akhir" class="block text-xs font-bold text-slate-700">
                            Tanggal Akhir <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="input-icon absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                                <i class="ti ti-calendar-event text-base"></i>
                            </span>
                            <input type="text" name="tanggal_akhir" id="tanggal_akhir" placeholder="YYYY-MM-DD" autocomplete="off"
                                   class="flatpickr-date w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                        </div>
                        <p class="error-feedback text-[11px] font-semibold text-rose-500 hidden items-center gap-1.5 mt-1" id="err_tanggal_akhir">
                            <i class="ti ti-alert-circle text-sm"></i>
                            <span class="err-text">Tanggal akhir wajib dipilih.</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- C. Security Info Banner -->
            <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200/80 flex items-start gap-3 text-amber-900">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-base shrink-0 mt-0.5">
                    <i class="ti ti-shield-alert"></i>
                </div>
                <div class="space-y-1 text-xs">
                    <h5 class="font-extrabold text-amber-900">Catatan Penting Kebijakan Retensi:</h5>
                    <ul class="list-disc list-inside space-y-0.5 text-amber-800 text-[11.5px] leading-relaxed">
                        <li><strong>Data Record Aman:</strong> Data absensi, data kunjungan, data aktivitas, dan data checklist <strong>TIDAK AKAN DIHAPUS</strong> dari database dan tetap utuh sebagai riwayat laporan resmi.</li>
                        <li><strong>Hanya File Fisik:</strong> Tindakan ini hanya menghapus berkas fisik file foto di server storage untuk menghemat ruang penyimpanan disk.</li>
                        <li><strong>Tidak Dapat Dibatalkan:</strong> Berkas foto yang telah terhapus tidak dapat dipulihkan kembali.</li>
                    </ul>
                </div>
            </div>

            <!-- D. Submit Action Button -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-[11px] text-slate-400">
                    Pastikan Anda telah melakukan backup berkala sebelum menjalankan utilitas ini.
                </div>

                <button type="button" id="btnExecuteCleanup"
                        class="w-full sm:w-auto px-6 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-2 transition active:scale-95 cursor-pointer hover:bg-rose-700 bg-rose-600">
                    <i class="ti ti-trash text-sm"></i>
                    <span>Hapus File Foto Periode Terpilih</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    // Inisialisasi Flatpickr
    if (typeof flatpickr !== 'undefined') {
        $('.flatpickr-date').flatpickr({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            allowInput: false
        });
    }

    // 1. Toggle Auto Clean Handler
    $('#toggleAutoClean').on('change', function() {
        var isChecked = $(this).is(':checked');
        var days = $('#auto_days').val() || 60;
        saveAutoCleanSettings(isChecked, days);
    });

    $('#auto_days').on('change', function() {
        var isChecked = $('#toggleAutoClean').is(':checked');
        var days = parseInt($(this).val(), 10) || 60;
        if (days < 7) {
            $(this).val(7);
            days = 7;
        }
        saveAutoCleanSettings(isChecked, days);
    });

    function saveAutoCleanSettings(isActive, days) {
        $.ajax({
            url: '{{ route("bersihkanfoto.toggle-auto") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                auto_hapus_foto: isActive ? 1 : 0,
                auto_hapus_foto_hari: days
            },
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    if (res.status) {
                        $('#autoBadge')
                            .removeClass('bg-slate-200 text-slate-700')
                            .addClass('bg-emerald-100 text-emerald-800 border border-emerald-200')
                            .text('Aktif (Setiap Hari 02:30)');
                        $('#toggleLabel').text('Auto Bersihkan Aktif');
                    } else {
                        $('#autoBadge')
                            .removeClass('bg-emerald-100 text-emerald-800 border border-emerald-200')
                            .addClass('bg-slate-200 text-slate-700')
                            .text('Nonaktif');
                        $('#toggleLabel').text('Aktifkan Auto');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Pengaturan Disimpan!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: (res && res.error) ? res.error : 'Terjadi kesalahan saat menyimpan pengaturan'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan jaringan atau server.'
                });
            }
        });
    }

    // 2. Quick Clean 60 Days Button
    $('#btnQuickClean60').on('click', function() {
        var cutoffDate = '{{ $cutoffDate ?? now()->subDays(60)->format("Y-m-d") }}';
        var defaultDays = $('#auto_days').val() || 60;

        Swal.fire({
            title: 'Bersihkan Foto > ' + defaultDays + ' Hari?',
            html: 'Tindakan ini akan menghapus semua file fisik foto (Presensi, Kunjungan, Aktivitas, Checklist) yang diambil <strong>sebelum atau pada tanggal ' + cutoffDate + '</strong>.<br><br><span class="text-emerald-600 font-bold">✓ Semua data rekapitulasi database tetap aman 100%.</span>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Bersihkan Sekarang',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition active:scale-95 cursor-pointer',
                cancelButton: 'px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition active:scale-95 mr-2 cursor-pointer'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $('.jenis-checkbox').prop('checked', true);
                executeDeletion(['presensi', 'kunjungan', 'aktivitas', 'checklist'], '2020-01-01', cutoffDate);
            }
        });
    });

    // Toggle Semua Jenis
    $('#btnToggleAllJenis').on('click', function() {
        let allChecked = $('.jenis-checkbox:checked').length === $('.jenis-checkbox').length;
        $('.jenis-checkbox').prop('checked', !allChecked);
        $(this).text(allChecked ? 'Pilih Semua Jenis' : 'Batalkan Semua');
        validateJenis();
    });

    function setFieldError($input, $err, msg) {
        let $visibleInput = $input;
        if ($input[0] && $input[0]._flatpickr && $input[0]._flatpickr.altInput) {
            $visibleInput = $($input[0]._flatpickr.altInput);
        }

        $visibleInput.addClass('border-rose-500 bg-rose-50/30 focus:border-rose-500 focus:ring-rose-500/20').removeClass('border-slate-300 focus:border-emerald-600 focus:ring-emerald-500/20');
        $input.closest('.space-y-1.5').find('.input-icon i').addClass('text-rose-500').removeClass('text-slate-400 text-emerald-600');
        
        $err.find('.err-text').text(msg);
        $err.removeClass('hidden').addClass('flex');
    }

    function clearFieldError($input, $err) {
        let $visibleInput = $input;
        if ($input[0] && $input[0]._flatpickr && $input[0]._flatpickr.altInput) {
            $visibleInput = $($input[0]._flatpickr.altInput);
        }

        $visibleInput.removeClass('border-rose-500 bg-rose-50/30 focus:border-rose-500 focus:ring-rose-500/20').addClass('border-slate-300 focus:border-emerald-600 focus:ring-emerald-500/20');
        $input.closest('.space-y-1.5').find('.input-icon i').removeClass('text-rose-500').addClass('text-slate-400');

        $err.addClass('hidden').removeClass('flex');
        $err.find('.err-text').text('');
    }

    function validateJenis() {
        let checkedCount = $('.jenis-checkbox:checked').length;
        if (checkedCount === 0) {
            $('#err_jenis').removeClass('hidden').addClass('flex');
            return false;
        }
        $('#err_jenis').addClass('hidden').removeClass('flex');
        return true;
    }

    function validateTanggalAwal() {
        let val = $('#tanggal_awal').val().trim();
        if (!val) {
            setFieldError($('#tanggal_awal'), $('#err_tanggal_awal'), 'Tanggal awal wajib dipilih.');
            return false;
        }
        clearFieldError($('#tanggal_awal'), $('#err_tanggal_awal'));
        return true;
    }

    function validateTanggalAkhir() {
        let valAwal = $('#tanggal_awal').val().trim();
        let valAkhir = $('#tanggal_akhir').val().trim();

        if (!valAkhir) {
            setFieldError($('#tanggal_akhir'), $('#err_tanggal_akhir'), 'Tanggal akhir wajib dipilih.');
            return false;
        }

        if (valAwal && valAkhir && valAkhir < valAwal) {
            setFieldError($('#tanggal_akhir'), $('#err_tanggal_akhir'), 'Tanggal akhir tidak boleh lebih kecil dari tanggal awal.');
            return false;
        }

        clearFieldError($('#tanggal_akhir'), $('#err_tanggal_akhir'));
        return true;
    }

    // Realtime triggers
    $('.jenis-checkbox').on('change', validateJenis);
    $('#tanggal_awal').on('change', function() {
        validateTanggalAwal();
        if ($('#tanggal_akhir').val().trim()) validateTanggalAkhir();
    });
    $('#tanggal_akhir').on('change', validateTanggalAkhir);

    // Execute Cleanup Action
    $('#btnExecuteCleanup').on('click', function(e) {
        e.preventDefault();

        let isJenisValid = validateJenis();
        let isAwalValid = validateTanggalAwal();
        let isAkhirValid = validateTanggalAkhir();

        if (!isJenisValid || !isAwalValid || !isAkhirValid) {
            if (!isJenisValid) $('html, body').animate({ scrollTop: $('#err_jenis').offset().top - 120 }, 300);
            else if (!isAwalValid) $('#tanggal_awal').focus();
            else if (!isAkhirValid) $('#tanggal_akhir').focus();
            return false;
        }

        let jenis = [];
        let jenisLabels = [];
        $('.jenis-checkbox:checked').each(function() {
            jenis.push($(this).val());
            jenisLabels.push($(this).closest('label').find('span').text().trim());
        });

        let tglAwal = $('#tanggal_awal').val();
        let tglAkhir = $('#tanggal_akhir').val();

        Swal.fire({
            title: 'Konfirmasi Hapus File Foto?',
            html: `
                <div class="text-xs text-slate-600 text-left space-y-2 mt-2">
                    <p>Anda akan menghapus berkas foto fisik pada storage server untuk kriteria:</p>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1 font-semibold text-slate-800">
                        <div>&bull; <strong>Tipe File:</strong> ${jenisLabels.join(', ')}</div>
                        <div>&bull; <strong>Periode:</strong> ${tglAwal} s/d ${tglAkhir}</div>
                    </div>
                    <p class="text-rose-600 font-bold"><i class="ti ti-alert-triangle"></i> Catatan: Data record tetap aman di database, hanya berkas foto fisiknya yang akan dihapus secara permanen.</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Jalankan Pembersihan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition active:scale-95 cursor-pointer',
                cancelButton: 'px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition active:scale-95 mr-2 cursor-pointer'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                executeDeletion(jenis, tglAwal, tglAkhir);
            }
        });
    });

    function executeDeletion(jenis, tglAwal, tglAkhir) {
        let btn = $('#btnExecuteCleanup');
        let originalHtml = btn.html();

        btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed').html(`
            <i class="ti ti-loader animate-spin text-sm"></i>
            <span>Sedang Membersihkan Storage...</span>
        `);

        $.ajax({
            url: '{{ route("bersihkanfoto.destroy") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tanggal_awal: tglAwal,
                tanggal_akhir: tglAkhir,
                jenis: jenis
            },
            dataType: 'json',
            success: function(response) {
                btn.prop('disabled', false).removeClass('opacity-75 cursor-not-allowed').html(originalHtml);

                if (response && response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembersihan Selesai!',
                        text: response.success,
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md'
                        },
                        buttonsStyling: false
                    }).then(() => {
                        $('#tanggal_awal').val('');
                        $('#tanggal_akhir').val('');
                        if ($('#tanggal_awal')[0] && $('#tanggal_awal')[0]._flatpickr) {
                            $('#tanggal_awal')[0]._flatpickr.clear();
                            $('#tanggal_akhir')[0]._flatpickr.clear();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.error || 'Terjadi kesalahan saat pembersihan.',
                        confirmButtonText: 'Tutup'
                    });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).removeClass('opacity-75 cursor-not-allowed').html(originalHtml);
                let msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : ('Terjadi kesalahan pada server (Status: ' + xhr.status + ')');

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memproses!',
                    text: msg,
                    confirmButtonText: 'Tutup'
                });
            }
        });
    }
});
</script>
@endpush
