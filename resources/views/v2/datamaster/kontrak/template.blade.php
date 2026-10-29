@extends('v2.layouts.app')

@section('titlepage', 'Template Kontrak')
@section('page_title', 'Template Kontrak')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-settings text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Konfigurasi Template Kontrak</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Atur redaksi template surat perjanjian kerja untuk PKWT dan PKWTT
            </p>
        </div>

        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('kontrak.index') }}" class="hover:text-slate-700 transition">Kontrak</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Template</span>
            </nav>
        </div>
    </div>

    <!-- Alerts -->
    @if (Session::has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-circle-check text-emerald-600 text-base"></i>
                <span class="font-semibold">{{ Session::get('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900"><i class="ti ti-x"></i></button>
        </div>
    @endif
    @if (Session::has('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-900 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-alert-circle text-rose-600 text-base"></i>
                <span class="font-semibold">{{ Session::get('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900"><i class="ti ti-x"></i></button>
        </div>
    @endif

    <!-- MAIN CONTENT -->
    <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm overflow-hidden">
        
        <!-- TABS -->
        <div class="flex border-b border-slate-200 bg-slate-50/50">
            <a href="{{ route('kontrak.template', ['type' => 'PKWT']) }}" 
               class="flex-1 text-center py-4 text-sm font-bold transition-all {{ $type == 'PKWT' ? 'text-slate-800 bg-white border-b-2' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100' }}"
               style="{{ $type == 'PKWT' ? 'border-bottom-color: '.$themeColor1.';' : '' }}">
                <div class="flex items-center justify-center gap-2">
                    <i class="ti ti-file-text text-lg"></i>
                    <span>Template PKWT</span>
                </div>
            </a>
            <a href="{{ route('kontrak.template', ['type' => 'PKWTT']) }}" 
               class="flex-1 text-center py-4 text-sm font-bold transition-all {{ $type == 'PKWTT' ? 'text-slate-800 bg-white border-b-2' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100' }}"
               style="{{ $type == 'PKWTT' ? 'border-bottom-color: '.$themeColor1.';' : '' }}">
                <div class="flex items-center justify-center gap-2">
                    <i class="ti ti-file-certificate text-lg"></i>
                    <span>Template PKWTT</span>
                </div>
            </a>
        </div>

        <div class="p-6 space-y-6">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-black text-slate-800">Edit Template {{ $type }}</h3>
                    <p class="text-xs text-slate-500">Gunakan tag dictionary untuk mengisi data dinamis secara otomatis.</p>
                </div>
                <form action="{{ route('kontrak.updateTemplate') }}" method="POST" id="formResetTemplate">
                    @csrf
                    <input type="hidden" name="reset" value="true">
                    <input type="hidden" name="kode_dokumen" value="{{ $type }}">
                    <button type="button" onclick="confirmReset()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-xl text-xs font-bold transition-all active:scale-95">
                        <i class="ti ti-refresh text-sm"></i>
                        <span>Reset ke Default</span>
                    </button>
                </form>
            </div>

            <!-- Kamus Kode -->
            <div class="p-4 rounded-2xl bg-sky-50 border border-sky-100">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-lg shrink-0">
                        <i class="ti ti-code"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 mb-2">Kamus Kode (Placeholder)</h4>
                        <p class="text-[11px] text-slate-600 mb-3">Gunakan kode berikut agar data otomatis terisi saat dicetak:</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-4">
                            @php
                                $placeholders = [
                                    'no_kontrak' => 'Nomor Kontrak',
                                    'no_dokumen' => 'Nomor Dokumen',
                                    'nama_karyawan' => 'Nama Karyawan',
                                    'tempat_lahir' => 'Tempat Lahir',
                                    'tanggal_lahir' => 'Tanggal Lahir',
                                    'jenis_kelamin' => 'Jenis Kelamin',
                                    'alamat_karyawan' => 'Alamat Karyawan',
                                    'no_ktp' => 'No KTP',
                                    'no_hp' => 'No HP / Telepon',
                                    'pendidikan_terakhir' => 'Pendidikan Terakhir',
                                    'jabatan' => 'Jabatan Karyawan',
                                    'cabang' => 'Cabang',
                                    'nama_perusahaan' => 'Nama Perusahaan',
                                    'alamat_perusahaan' => 'Alamat Perusahaan',
                                    'nama_hrd' => 'Nama HRD / Owner',
                                    'jabatan_hrd' => 'Jabatan HRD / Owner',
                                    'gaji_pokok' => 'Gaji Pokok (Format Rupiah)',
                                    'total_gaji' => 'Total Gaji (Gaji Pokok + Tunjangan)',
                                    'tabel_tunjangan' => 'Tabel List Tunjangan',
                                    'tanggal_mulai' => 'Tanggal Mulai Kontrak',
                                    'tanggal_selesai' => 'Tanggal Selesai Kontrak',
                                    'hari_ini' => 'Hari Cetak',
                                    'tanggal_hari_ini' => 'Tanggal Cetak'
                                ];
                            @endphp
                            
                            @foreach($placeholders as $code => $desc)
                            <div class="flex items-center gap-2 group cursor-pointer" onclick="insertCode('{{ '{' . '{' . $code . '}' . '}' }}')">
                                <code class="px-1.5 py-0.5 rounded bg-white border border-slate-200 text-[10px] font-mono text-sky-600 font-bold group-hover:bg-sky-500 group-hover:text-white group-hover:border-sky-500 transition-colors">
                                    {{ '{' . '{' . $code . '}' . '}' }}
                                </code>
                                <span class="text-[10px] text-slate-500 truncate" title="{{ $desc }}">{{ $desc }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3 text-[10px] text-slate-400 italic">
                            * Klik pada kode untuk menyisipkan otomatis ke editor.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Template -->
            <form action="{{ route('kontrak.updateTemplate') }}" method="POST" id="formTemplate">
                @csrf
                <input type="hidden" name="kode_dokumen" value="{{ $type }}">
                
                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white">
                    <textarea name="konten" id="konten_template" class="w-full hidden">{{ $template->konten }}</textarea>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" id="btnSimpanTemplate" class="inline-flex items-center gap-1.5 px-6 py-2.5 text-white font-bold rounded-xl text-xs shadow-md transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-device-floppy text-sm"></i>
                        <span>Simpan Template {{ $type }}</span>
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

{{-- Summernote CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    /* Tailwind adjustments for Summernote */
    .note-editor.note-frame {
        border: none !important;
    }
    .note-toolbar {
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 10px 15px !important;
        border-radius: 0.75rem 0.75rem 0 0 !important;
    }
    .note-btn {
        background: white !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 6px !important;
        padding: 4px 10px !important;
        color: #475569 !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        margin-right: 4px !important;
    }
    .note-btn:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }
    .note-editable {
        padding: 2rem !important;
        background: #ffffff !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #1e293b;
        font-size: 14px;
        line-height: 1.6;
    }
    .note-statusbar {
        background: #f8fafc !important;
        border-top: 1px solid #e2e8f0 !important;
        border-radius: 0 0 0.75rem 0.75rem !important;
    }
    
    /* Restore styles stripped by Tailwind CSS */
    .note-editable h1 { font-size: 2em; font-weight: bold; margin: 0.67em 0; }
    .note-editable h2 { font-size: 1.5em; font-weight: bold; margin: 0.83em 0; }
    .note-editable h3 { font-size: 1.17em; font-weight: bold; margin: 1em 0; }
    .note-editable h4 { font-size: 1em; font-weight: bold; margin: 1.33em 0; }
    .note-editable h5 { font-size: 0.83em; font-weight: bold; margin: 1.67em 0; }
    .note-editable h6 { font-size: 0.67em; font-weight: bold; margin: 2.33em 0; }
    .note-editable p { margin-bottom: 1em; }
    .note-editable ul { list-style-type: disc; padding-left: 2em; margin-bottom: 1em; }
    .note-editable ol { list-style-type: decimal; padding-left: 2em; margin-bottom: 1em; }
    .note-editable b, .note-editable strong { font-weight: bolder; }
    .note-editable i, .note-editable em { font-style: italic; }
    .note-editable u { text-decoration: underline; }
</style>
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        // Init Summernote
        $('#konten_template').summernote({
            placeholder: 'Tulis isi template kontrak disini...',
            tabsize: 2,
            height: 600,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onInit: function() {
                    $('.note-editor').addClass('rounded-xl overflow-hidden');
                }
            }
        });
        
        // Handle Submit Animation
        $('#formTemplate').on('submit', function() {
            const btn = $('#btnSimpanTemplate');
            btn.prop('disabled', true).html(`
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Menyimpan...
            `);
        });
    });

    // Helper to insert code to summernote
    function insertCode(code) {
        $('#konten_template').summernote('editor.insertText', code);
        
        // Show small success toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            background: '#10b981',
            color: '#ffffff'
        });
        Toast.fire({
            icon: 'success',
            title: `Kode disalin!`
        });
    }

    // Reset Confirmation
    function confirmReset() {
        Swal.fire({
            title: 'Reset Template?',
            text: 'Apakah Anda yakin ingin mereset template ini ke default? Semua perubahan Anda akan hilang!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl shadow-xl border border-slate-100',
                confirmButton: 'px-5 py-2.5 rounded-xl text-xs font-bold',
                cancelButton: 'px-5 py-2.5 rounded-xl text-xs font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formResetTemplate').submit();
            }
        });
    }
</script>
@endpush
