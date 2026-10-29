@extends('v2.layouts.app')

@section('titlepage', 'Backup & Restore Database')
@section('page_title', 'Backup & Restore Database')

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
                <i class="ti ti-database-export text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Backup & Restore Database</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Unduh salinan berkas database (.sql) sistem secara berkala atau pulihkan data saat dibutuhkan
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
            <span class="font-bold text-slate-800">Backup Data</span>
        </nav>
    </div>

    <!-- ================= NOTIFICATIONS ================= -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 text-emerald-900 shadow-2xs">
            <i class="ti ti-circle-check text-emerald-600 text-lg mt-0.5 shrink-0"></i>
            <div class="flex-1 text-xs font-semibold">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3 text-rose-900 shadow-2xs">
            <i class="ti ti-alert-triangle text-rose-600 text-lg mt-0.5 shrink-0"></i>
            <div class="flex-1 text-xs font-semibold">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- ================= INFORMATION BANNER ================= -->
    <div class="p-4 rounded-2xl bg-sky-50/80 border border-sky-200/90 flex items-start gap-3.5 text-sky-950 shadow-2xs">
        <div class="w-9 h-9 rounded-xl bg-sky-100/90 text-sky-700 flex items-center justify-center text-lg shrink-0 mt-0.5 border border-sky-200">
            <i class="ti ti-info-circle"></i>
        </div>
        <div class="space-y-1.5 text-xs flex-1">
            <h5 class="font-extrabold text-sky-900 text-xs sm:text-sm">Panduan Backup & Pemulihan Database:</h5>
            <div class="space-y-1 text-sky-900/90 text-[11.5px] leading-relaxed">
                <p>
                    <strong class="text-sky-950">1. Backup Data:</strong> Menghasilkan file dump <code class="px-1.5 py-0.5 rounded bg-sky-100/80 font-mono text-[11px] font-bold text-sky-800">.sql</code> yang berisi seluruh struktur tabel dan record data aplikasi saat ini.
                </p>
                <p>
                    <strong class="text-rose-900">2. Restore Data:</strong> Mengembalikan seluruh database sistem ke keadaan persis seperti pada file <code class="px-1.5 py-0.5 rounded bg-rose-100/80 font-mono text-[11px] font-bold text-rose-800">.sql</code> yang diunggah.
                    <span class="text-rose-600 font-bold ml-1"><i class="ti ti-alert-circle"></i> Perhatian: Proses ini akan menimpa data yang ada saat ini!</span>
                </p>
            </div>
        </div>
    </div>

    <!-- ================= 2 CARDS: BACKUP & RESTORE ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        <!-- CARD 1: BACKUP DATABASE -->
        <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between">
            <!-- Solid Header -->
            <div class="px-5 py-3.5 flex items-center justify-between text-white border-0"
                 style="background-color: {{ $themeColor1 }};">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                        <i class="ti ti-cloud-download"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Download Salinan Database
                    </h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                    File Format: .sql
                </span>
            </div>

            <!-- Body -->
            <div class="p-6 sm:p-8 flex flex-col items-center text-center flex-1 justify-between space-y-6">
                <div class="space-y-4 flex flex-col items-center">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-xs border border-emerald-100"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}15, {{ $themeColor2 }}25);">
                        <i class="ti ti-database-export text-4xl" style="color: {{ $themeColor1 }};"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-extrabold text-slate-800 tracking-tight">
                            Ekspor & Unduh Database
                        </h4>
                        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed max-w-sm">
                            Amankan seluruh data presensi, master karyawan, setting, dan transaksi sistem ke perangkat lokal Anda secara berkala.
                        </p>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="w-full pt-4 border-t border-slate-100">
                    <a href="{{ route('backup.download') }}" id="btnDownloadBackupV2"
                       class="w-full h-11 px-5 text-white font-bold text-xs rounded-xl shadow-md transition inline-flex items-center justify-center gap-2 cursor-pointer hover:opacity-95 active:scale-95"
                       style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-download text-base"></i>
                        <span>Download Backup Sekarang</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- CARD 2: RESTORE DATABASE -->
        <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden flex flex-col justify-between">
            <!-- Solid Header (Rose / Danger Theme for Warning) -->
            <div class="px-5 py-3.5 flex items-center justify-between text-white bg-rose-600 border-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                        <i class="ti ti-database-import"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Pemulihan / Restore Database
                    </h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                    Zona Sensitif
                </span>
            </div>

            <!-- Body -->
            <div class="p-6 sm:p-8 flex flex-col items-center text-center flex-1 justify-between space-y-6">
                <div class="space-y-4 flex flex-col items-center w-full">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center shadow-xs bg-rose-50 border border-rose-100">
                        <i class="ti ti-refresh-alert text-4xl text-rose-600"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-extrabold text-slate-800 tracking-tight">
                            Unggah File Backup (.sql)
                        </h4>
                        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed max-w-sm">
                            Pilih file backup sebelumnya untuk mengembalikan kondisi data sistem. Data yang ada saat ini akan ditimpa.
                        </p>
                    </div>

                    <!-- Form Restore -->
                    <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data" id="formRestoreV2" class="w-full text-left space-y-2 pt-2" novalidate>
                        @csrf
                        <div class="space-y-1.5">
                            <label for="backup_file" class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                                File Database (.sql) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="backup_file" id="backup_file" accept=".sql,.txt"
                                       class="w-full text-xs font-semibold text-slate-700 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 file:cursor-pointer cursor-pointer border border-slate-300 rounded-xl bg-slate-50/50 p-1.5 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition">
                            </div>
                            <p class="error-feedback text-[11px] font-semibold text-rose-500 hidden items-center gap-1.5 mt-1" id="err_backup_file">
                                <i class="ti ti-alert-circle text-sm"></i>
                                <span class="err-text">Pilih berkas file .sql terlebih dahulu.</span>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Action Button -->
                <div class="w-full pt-4 border-t border-slate-100">
                    <button type="button" id="btnRestoreConfirmV2"
                            class="w-full h-11 px-5 text-white font-bold text-xs rounded-xl shadow-md transition inline-flex items-center justify-center gap-2 cursor-pointer bg-rose-600 hover:bg-rose-700 active:scale-95">
                        <i class="ti ti-database-import text-base"></i>
                        <span>Mulai Restore Data</span>
                    </button>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {

    // Download Backup Feedback
    $('#btnDownloadBackupV2').on('click', function() {
        let $btn = $(this);
        let originalHtml = $btn.html();

        $btn.html(`
            <i class="ti ti-loader animate-spin text-base"></i>
            <span>Mempersiapkan File...</span>
        `).addClass('opacity-75 pointer-events-none');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Menyiapkan File Backup...',
                text: 'Mohon tunggu sementara server menghasilkan berkas database Anda.',
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });
        }

        setTimeout(function() {
            $btn.html(originalHtml).removeClass('opacity-75 pointer-events-none');
        }, 4000);
    });

    // Validasi file input realtime
    $('#backup_file').on('change', function() {
        let file = this.files[0];
        if (file) {
            let fileName = file.name;
            let ext = fileName.split('.').pop().toLowerCase();
            if (ext !== 'sql' && ext !== 'txt') {
                showFileError('Format file tidak didukung. Harap pilih file berekstensi .sql atau .txt');
            } else {
                clearFileError();
            }
        }
    });

    function showFileError(msg) {
        $('#backup_file').addClass('border-rose-500 bg-rose-50/30');
        $('#err_backup_file').find('.err-text').text(msg);
        $('#err_backup_file').removeClass('hidden').addClass('flex');
    }

    function clearFileError() {
        $('#backup_file').removeClass('border-rose-500 bg-rose-50/30');
        $('#err_backup_file').addClass('hidden').removeClass('flex');
        $('#err_backup_file').find('.err-text').text('');
    }

    // Restore Database Action with SweetAlert2
    $('#btnRestoreConfirmV2').on('click', function(e) {
        e.preventDefault();

        let fileInput = document.getElementById('backup_file');
        if (!fileInput || fileInput.files.length === 0) {
            showFileError('Pilih berkas file .sql terlebih dahulu sebelum memulai restore!');
            $('#backup_file').focus();
            return false;
        }

        let file = fileInput.files[0];
        let ext = file.name.split('.').pop().toLowerCase();
        if (ext !== 'sql' && ext !== 'txt') {
            showFileError('Format file tidak didukung. Harap pilih file berekstensi .sql!');
            return false;
        }

        clearFileError();

        Swal.fire({
            title: 'Apakah Anda Yakin Ingin Merestore?',
            html: `
                <div class="text-xs text-slate-600 text-left space-y-2 mt-2">
                    <p>Anda akan memulihkan data sistem menggunakan file:</p>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs font-bold text-slate-800 break-all">
                        ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)
                    </div>
                    <p class="text-rose-600 font-bold leading-relaxed">
                        <i class="ti ti-alert-triangle"></i> Peringatan Kritis: Seluruh data tabel dan transaksi saat ini akan DITIMPA dengan data dari file backup tersebut. Tindakan ini TIDAK DAPAT dibatalkan!
                    </p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Jalankan Restore Sekarang',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'px-5 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition active:scale-95 cursor-pointer',
                cancelButton: 'px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition active:scale-95 mr-2 cursor-pointer'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang Merestore Database...',
                    text: 'Mohon tunggu dan jangan menutup atau merefresh halaman ini hingga proses selesai.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $('#formRestoreV2').submit();
            }
        });
    });

});
</script>
@endpush
