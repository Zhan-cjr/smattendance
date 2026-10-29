@extends('v2.layouts.app')

@section('titlepage', 'Konfigurasi Template SPL')
@section('page_title', 'Template Dokumen Lembur')

@push('mystyle')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border-radius: 1rem !important;
        border: 1px solid #cbd5e1 !important;
        overflow: hidden !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .note-toolbar {
        background-color: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 0.5rem 0.75rem !important;
    }
    .note-btn {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.5rem !important;
        padding: 0.35rem 0.65rem !important;
        font-size: 0.75rem !important;
        color: #334155 !important;
        transition: all 0.15s ease !important;
    }
    .note-btn:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }
    .note-editable {
        background-color: #ffffff !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 13.5px !important;
        line-height: 1.6 !important;
        color: #1e293b !important;
        padding: 1.25rem !important;
    }
    .note-statusbar {
        background-color: #f8fafc !important;
        border-top: 1px solid #e2e8f0 !important;
    }
</style>
@endpush

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (STANDAR V2) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-template text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Template Surat Perintah Lembur (SPL)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Atur tata letak, susunan redaksi bahasa, dan variabel cetak dokumen Surat Perintah Kerja Lembur
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('lembur.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-clock-play text-sm"></i>
                    <span>Lembur</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Template SPL</span>
            </nav>

            <!-- Top Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('lembur.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 text-slate-700 hover:bg-slate-50 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                    <i class="ti ti-arrow-left text-sm"></i>
                    <span>Kembali ke Lembur</span>
                </a>
                
                <form action="{{ route('lembur.updateTemplate') }}" method="POST" id="formResetTemplate" class="inline m-0">
                    @csrf
                    <input type="hidden" name="reset" value="true">
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                        <i class="ti ti-rotate text-sm"></i>
                        <span>Reset ke Default</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ================= 2. MAIN CARD EDITOR (TAILWIND V2 CLEAN WHITE) ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-2xs space-y-5">
        
        <!-- Header Section in Card -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold text-white shadow-sm"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-file-text"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight">Editor Format Dokumen PDF</h3>
                    <p class="text-xs text-slate-500 font-medium">Kustomisasi konten cetak SPL dengan teks dinamis dan placeholder</p>
                </div>
            </div>
        </div>

        <form action="{{ route('lembur.updateTemplate') }}" method="POST" id="formTemplateSPL" class="space-y-5">
            @csrf
            
            <!-- Summernote Textarea Input -->
            <div class="space-y-2">
                <label for="konten" class="block text-xs font-bold text-slate-700">
                    Redaksi & Tata Letak Dokumen Surat :
                </label>
                <textarea class="form-control" name="konten" id="konten">{{ $template->konten }}</textarea>
            </div>

            <!-- Kamus Variabel Placeholders (Modern 3-Column Card Grid) -->
            <div class="rounded-2xl border border-slate-200/90 bg-slate-50/70 p-4 sm:p-5 space-y-3.5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 pb-2.5 border-b border-slate-200/80">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                            <i class="ti ti-code"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800">Kamus Kode Variabel (Placeholders)</h4>
                    </div>
                    <span class="text-[11px] text-slate-500 italic">
                        Klik pada kode badge untuk otomatis menyalin ke clipboard
                    </span>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed">
                    Gunakan kode variabel di bawah ini pada editor teks di atas. Sistem akan secara otomatis mengganti kode tersebut dengan data asli karyawan dan jadwal penugasan lembur saat dokumen dicetak ke PDF:
                </p>

                <!-- Grid 3 Kolom Kartu Placeholders -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
                    
                    <!-- Kolom 1: Data Karyawan & Dokumen -->
                    <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-2xs space-y-2.5">
                        <div class="flex items-center gap-1.5 text-xs font-extrabold text-slate-800 pb-1.5 border-b border-slate-100">
                            <i class="ti ti-user text-emerald-600"></i>
                            <span>Data Karyawan & SPL</span>
                        </div>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{no_spl}}')" title="Klik untuk salin">@{{no_spl}}</span>
                                <span class="text-slate-500 text-[11px]">Nomor Dokumen SPL</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{nama_karyawan}}')" title="Klik untuk salin">@{{nama_karyawan}}</span>
                                <span class="text-slate-500 text-[11px]">Nama Lengkap Pegawai</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{nik}}')" title="Klik untuk salin">@{{nik}}</span>
                                <span class="text-slate-500 text-[11px]">NIK Karyawan</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{jabatan}}')" title="Klik untuk salin">@{{jabatan}}</span>
                                <span class="text-slate-500 text-[11px]">Jabatan Pegawai</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{departemen}}')" title="Klik untuk salin">@{{departemen}}</span>
                                <span class="text-slate-500 text-[11px]">Departemen / Divisi</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{cabang}}')" title="Klik untuk salin">@{{cabang}}</span>
                                <span class="text-slate-500 text-[11px]">Kantor Cabang</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 2: Waktu & Pelaksanaan Lembur -->
                    <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-2xs space-y-2.5">
                        <div class="flex items-center gap-1.5 text-xs font-extrabold text-slate-800 pb-1.5 border-b border-slate-100">
                            <i class="ti ti-clock text-sky-600"></i>
                            <span>Waktu & Uraian Lembur</span>
                        </div>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-sky-50 text-sky-800 border border-sky-200 hover:bg-sky-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{hari}}')" title="Klik untuk salin">@{{hari}}</span>
                                <span class="text-slate-500 text-[11px]">Hari Pelaksanaan</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-sky-50 text-sky-800 border border-sky-200 hover:bg-sky-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{tanggal_lembur}}')" title="Klik untuk salin">@{{tanggal_lembur}}</span>
                                <span class="text-slate-500 text-[11px]">Tanggal Pelaksanaan</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-sky-50 text-sky-800 border border-sky-200 hover:bg-sky-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{jam_mulai}}')" title="Klik untuk salin">@{{jam_mulai}}</span>
                                <span class="text-slate-500 text-[11px]">Jam Rencana Mulai</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-sky-50 text-sky-800 border border-sky-200 hover:bg-sky-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{jam_selesai}}')" title="Klik untuk salin">@{{jam_selesai}}</span>
                                <span class="text-slate-500 text-[11px]">Jam Rencana Selesai</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-sky-50 text-sky-800 border border-sky-200 hover:bg-sky-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{estimasi_durasi}}')" title="Klik untuk salin">@{{estimasi_durasi}}</span>
                                <span class="text-slate-500 text-[11px]">Estimasi Total Jam</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-sky-50 text-sky-800 border border-sky-200 hover:bg-sky-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{keterangan_lembur}}')" title="Klik untuk salin">@{{keterangan_lembur}}</span>
                                <span class="text-slate-500 text-[11px]">Uraian Tugas / Alasan</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 3: Perusahaan, Approval & Cetak -->
                    <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-2xs space-y-2.5">
                        <div class="flex items-center gap-1.5 text-xs font-extrabold text-slate-800 pb-1.5 border-b border-slate-100">
                            <i class="ti ti-building text-amber-600"></i>
                            <span>Instansi & Persetujuan</span>
                        </div>
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{status_approval}}')" title="Klik untuk salin">@{{status_approval}}</span>
                                <span class="text-slate-500 text-[11px]">Status Persetujuan</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{nama_perusahaan}}')" title="Klik untuk salin">@{{nama_perusahaan}}</span>
                                <span class="text-slate-500 text-[11px]">Nama Perusahaan</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{alamat_perusahaan}}')" title="Klik untuk salin">@{{alamat_perusahaan}}</span>
                                <span class="text-slate-500 text-[11px]">Alamat Perusahaan</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{nama_hrd}}')" title="Klik untuk salin">@{{nama_hrd}}</span>
                                <span class="text-slate-500 text-[11px]">Pimpinan / HRD</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{tanggal_hari_ini}}')" title="Klik untuk salin">@{{tanggal_hari_ini}}</span>
                                <span class="text-slate-500 text-[11px]">Tanggal Cetak</span>
                            </div>
                            <div class="flex items-center justify-between gap-1">
                                <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 cursor-pointer transition active:scale-95" onclick="copyTag('@{{hari_ini}}')" title="Klik untuk salin">@{{hari_ini}}</span>
                                <span class="text-slate-500 text-[11px]">Hari Cetak</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Bottom Submit Button -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <a href="{{ route('lembur.index') }}" 
                   class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 text-xs font-bold transition active:scale-95 cursor-pointer">
                    Batal
                </a>
                <button type="submit" 
                        id="btnSimpanTemplate"
                        class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-device-floppy text-sm"></i>
                    <span>Simpan Perubahan Template</span>
                </button>
            </div>
        </form>

    </div>

</div>

@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    function initSummernote() {
        if (typeof $.fn.summernote !== 'undefined') {
            $('#konten').summernote({
                placeholder: 'Tulis susunan template Surat Perintah Lembur di sini...',
                tabsize: 2,
                height: 540,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        }
    }

    initSummernote();

    // Reset Template Confirmation
    $('#formResetTemplate').submit(function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Reset Template SPL?',
            text: 'Format template akan dikembalikan ke pengaturan default sistem. Perubahan yang Anda buat akan digantikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Reset ke Default',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-2xl shadow-xl' }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Save Template Confirmation & Loading
    $('#formTemplateSPL').submit(function() {
        const $btn = $('#btnSimpanTemplate');
        $btn.prop('disabled', true).html(`
            <div style="width: 15px; height: 15px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
            <span>Menyimpan Template...</span>
        `);
    });
});

function copyTag(tag) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(tag).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'Kode Disalin!',
                text: tag + ' telah disalin ke clipboard.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true
            });
        });
    }
}
</script>
@endpush
