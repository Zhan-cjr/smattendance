@extends('v2.layouts.app')

@section('titlepage', 'Buat Draft Jadwal')
@section('page_title', 'Buat Draft Jadwal Bulanan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $nextMonth = \Carbon\Carbon::now()->addMonth()->month;
    $curYear = \Carbon\Carbon::now()->year;
@endphp

<div class="space-y-4">

    <!-- ================= 1. TOP HEADER & BREADCRUMB ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-1">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('jadwalperiode.index') }}" 
                   class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition active:scale-95 shadow-2xs">
                    <i class="ti ti-arrow-left text-sm"></i>
                </a>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                    Buat Draft Jadwal Bulanan
                </h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Generate draf jadwal kerja untuk 1 bulan kalender berdasarkan Pola Shift, Duplikasi Bulan Lalu, atau Konfigurasi Default.
            </p>
        </div>

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center text-xs text-slate-400 font-medium">
            <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                <i class="ti ti-home text-sm"></i>
                <span>Dashboard</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <a href="{{ route('jadwalperiode.index') }}" class="hover:text-slate-700 transition">
                <span>Periode Jadwal</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="font-bold text-slate-800">Buat Baru</span>
        </nav>
    </div>

    <!-- ================= 2. FORM CREATE DRAFT ================= -->
    <form action="{{ route('jadwalperiode.store') }}" method="POST" id="formCreateJadwalV2" class="space-y-4" novalidate>
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            <!-- PANEL KIRI: INFORMASI PERIODE & TARGET -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col">
                <!-- Card Header dengan Warna Gradien Sesuai Tema -->
                <div class="px-5 py-3.5 text-white flex items-center gap-2.5"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-sm font-bold shadow-inner text-white">
                        <i class="ti ti-calendar"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black tracking-tight text-white uppercase">Informasi Periode Target</h3>
                        <p class="text-[10.5px] text-white/80 leading-none mt-0.5">Tentukan rentang bulan, tahun, dan cakupan karyawan</p>
                    </div>
                </div>

                <div class="p-5 space-y-4 flex-1">
                    <!-- Nama Periode -->
                    <div class="space-y-1.5 field-group">
                        <label for="nama_periode" class="block text-xs font-bold text-slate-700">
                            Nama Periode Jadwal <span class="text-rose-500 font-bold">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                                <i class="ti ti-typography text-base"></i>
                            </span>
                            <input type="text" 
                                   id="nama_periode" 
                                   name="nama_periode" 
                                   value="{{ old('nama_periode', 'Jadwal ' . \Carbon\Carbon::now()->addMonth()->translatedFormat('F Y')) }}" 
                                   placeholder="Contoh: Jadwal Produksi Oktober 2026" 
                                   autocomplete="off"
                                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        </div>
                    </div>

                    <!-- Target Bulan & Tahun -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Bulan -->
                        <div class="space-y-1.5 field-group">
                            <label for="bulan" class="block text-xs font-bold text-slate-700">
                                Target Bulan <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <div class="relative">
                                <select name="bulan" 
                                        id="bulan" 
                                        class="w-full h-10 px-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ old('bulan', $nextMonth) == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Tahun -->
                        <div class="space-y-1.5 field-group">
                            <label for="tahun" class="block text-xs font-bold text-slate-700">
                                Target Tahun <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <div class="relative">
                                <select name="tahun" 
                                        id="tahun" 
                                        class="w-full h-10 px-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                                    @for ($y = $curYear - 1; $y <= $curYear + 2; $y++)
                                        <option value="{{ $y }}" {{ old('tahun', $curYear) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Scope Filter: Departemen, Cabang, Grup -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <!-- Departemen -->
                        <div class="space-y-1.5">
                            <label for="kode_dept" class="block text-xs font-bold text-slate-700">
                                Departemen
                            </label>
                            <select name="kode_dept" id="kode_dept" class="w-full h-10 px-2.5 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                                <option value="">Semua Dept</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}" {{ old('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                        {{ $d->nama_dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cabang -->
                        <div class="space-y-1.5">
                            <label for="kode_cabang" class="block text-xs font-bold text-slate-700">
                                Cabang
                            </label>
                            <select name="kode_cabang" id="kode_cabang" class="w-full h-10 px-2.5 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->kode_cabang }}" {{ old('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                        {{ $c->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Grup -->
                        <div class="space-y-1.5">
                            <label for="kode_grup" class="block text-xs font-bold text-slate-700">
                                Grup Karyawan
                            </label>
                            <select name="kode_grup" id="kode_grup" class="w-full h-10 px-2.5 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                                <option value="">Semua Grup</option>
                                @foreach ($grups as $g)
                                    <option value="{{ $g->kode_grup }}" {{ old('kode_grup') == $g->kode_grup ? 'selected' : '' }}>
                                        {{ $g->nama_grup }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Catatan / Keterangan -->
                    <div class="space-y-1.5">
                        <label for="catatan" class="block text-xs font-bold text-slate-700">
                            Catatan Internal <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea id="catatan" 
                                  name="catatan" 
                                  rows="3" 
                                  placeholder="Catatan internal perihal jadwal bulan ini..." 
                                  class="w-full p-3 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- PANEL KANAN: METODE SUMBER & LOGIKA AUTO-GENERATE -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col">
                <!-- Card Header dengan Warna Gradien Sesuai Tema -->
                <div class="px-5 py-3.5 text-white flex items-center gap-2.5"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-sm font-bold shadow-inner text-white">
                        <i class="ti ti-wand"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black tracking-tight text-white uppercase">Metode Sumber Jadwal</h3>
                        <p class="text-[10.5px] text-white/80 leading-none mt-0.5">Pilih cara sistem mengisi matriks jadwal awal</p>
                    </div>
                </div>

                <div class="p-5 space-y-3 flex-1 flex flex-col justify-between">
                    
                    <div class="space-y-2.5">
                        <!-- Opsi 1: Dari Pola Shift -->
                        <div class="border border-slate-200 rounded-xl p-3.5 hover:border-emerald-500 transition-colors cursor-pointer" onclick="$('#sumber_pattern').prop('checked', true).trigger('change');">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input name="sumber" id="sumber_pattern" type="radio" value="pattern" checked class="w-4 h-4 mt-0.5 text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                                <div class="min-w-0 flex-1">
                                    <span class="block text-xs font-bold text-slate-800">Generate dari Pola Shift (Template)</span>
                                    <span class="block text-[11px] text-slate-500 mt-0.5">Menghitung rotasi otomatis (misal 4-2, 5-2, 2-2-2-1) dimulai dari tanggal acuan.</span>
                                    
                                    <!-- Sub-options for pattern -->
                                    <div id="section_pattern_options" class="mt-3 pt-2.5 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-2.5" onclick="event.stopPropagation();">
                                        <div class="space-y-1.5 field-group">
                                            <label for="kode_pola" class="block text-[11px] font-bold text-slate-700">Template Pola Shift <span class="text-rose-500 font-bold">*</span></label>
                                            <select name="kode_pola" id="kode_pola" class="w-full h-8 px-2.5 text-xs bg-slate-50 border border-slate-300 rounded-lg outline-none transition cursor-pointer">
                                                <option value="">-- Pilih Pola --</option>
                                                @foreach ($patterns as $pat)
                                                    <option value="{{ $pat->kode_pola }}">
                                                        {{ $pat->nama_pola }} ({{ $pat->jumlah_hari_siklus }} Hari)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="space-y-1.5 field-group">
                                            <label for="tanggal_mulai_pola" class="block text-[11px] font-bold text-slate-700">Tgl Mulai Hari ke-1 <span class="text-rose-500 font-bold">*</span></label>
                                            <div class="relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors z-10">
                                                    <i class="ti ti-calendar text-sm"></i>
                                                </span>
                                                <input type="text" 
                                                       id="tanggal_mulai_pola" 
                                                       name="tanggal_mulai_pola" 
                                                       value="{{ date('Y-m-01') }}" 
                                                       placeholder="Pilih Tanggal Mulai"
                                                       class="w-full h-8 pl-8 pr-2.5 text-xs bg-slate-50 border border-slate-300 rounded-lg flatpickr-date outline-none transition cursor-pointer font-medium text-slate-800">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Opsi 2: Copy dari Bulan Sebelumnya -->
                        <div class="border border-slate-200 rounded-xl p-3.5 hover:border-emerald-500 transition-colors cursor-pointer" onclick="$('#sumber_copy').prop('checked', true).trigger('change');">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input name="sumber" id="sumber_copy" type="radio" value="copy" class="w-4 h-4 mt-0.5 text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                                <div>
                                    <span class="block text-xs font-bold text-slate-800">Copy dari Jadwal Bulan Sebelumnya</span>
                                    <span class="block text-[11px] text-slate-500 mt-0.5">Menyalin susunan shift tanggal 1..N persis dari jadwal bulan lalu.</span>
                                </div>
                            </label>
                        </div>

                        <!-- Opsi 3: Dari Jadwal Default Existing -->
                        <div class="border border-slate-200 rounded-xl p-3.5 hover:border-emerald-500 transition-colors cursor-pointer" onclick="$('#sumber_existing').prop('checked', true).trigger('change');">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input name="sumber" id="sumber_existing" type="radio" value="existing" class="w-4 h-4 mt-0.5 text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                                <div>
                                    <span class="block text-xs font-bold text-slate-800">Gunakan Jam Kerja Default (By Day / Dept)</span>
                                    <span class="block text-[11px] text-slate-500 mt-0.5">Mengisi berdasarkan konfigurasi jam kerja rutin mingguan per karyawan atau departemen.</span>
                                </div>
                            </label>
                        </div>

                        <!-- Opsi 4: Draft Kosong (Manual) -->
                        <div class="border border-slate-200 rounded-xl p-3.5 hover:border-emerald-500 transition-colors cursor-pointer" onclick="$('#sumber_empty').prop('checked', true).trigger('change');">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input name="sumber" id="sumber_empty" type="radio" value="empty" class="w-4 h-4 mt-0.5 text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                                <div>
                                    <span class="block text-xs font-bold text-slate-800">Draft Kosong (Input Manual Penuh)</span>
                                    <span class="block text-[11px] text-slate-500 mt-0.5">Membuat matriks kosong untuk diisi manual di halaman Matriks Jadwal.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex items-center gap-2.5">
                        <button type="submit" 
                                id="btnSubmitJadwalV2" 
                                class="flex-1 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-wand text-sm"></i>
                            <span>Generate Draft Jadwal</span>
                        </button>
                        <a href="{{ route('jadwalperiode.index') }}" 
                           class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95">
                            Batal
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </form>

</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        let fpTanggalMulai = null;
        if (typeof flatpickr !== 'undefined') {
            fpTanggalMulai = flatpickr("#tanggal_mulai_pola", {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                altInputClass: 'w-full h-8 pl-8 pr-2.5 text-xs bg-slate-50 border border-slate-300 rounded-lg outline-none transition cursor-pointer font-medium text-slate-800',
                defaultDate: "{{ date('Y-m-01') }}",
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    validateSingleField($('#tanggal_mulai_pola'));
                }
            });
        }

        $('input[name="sumber"]').change(function() {
            if ($(this).val() === 'pattern') {
                $('#section_pattern_options').slideDown(200);
            } else {
                $('#section_pattern_options').slideUp(200);
                clearError($('#kode_pola'));
                clearError($('#tanggal_mulai_pola'));
            }
        });

        // Consistent Helper to show error
        function showError(element, message) {
            const $el = $(element);
            const $container = $el.closest('.field-group, .space-y-1\\.5, .space-y-1');
            
            // If flatpickr altInput exists, style the altInput
            const $targetInput = $el.next('.flatpickr-input[type="text"]').length ? $el.next('.flatpickr-input[type="text"]') : $el;

            $targetInput.css({
                'border-color': '#ef4444',
                'background-color': '#fff5f5',
                'box-shadow': '0 0 0 2px rgba(239, 68, 68, 0.2)'
            });
            
            $el.closest('.relative').find('i').css('color', '#ef4444');
            
            $container.find('.error-msg').remove();
            $container.append(`
                <p class="error-msg" style="font-size: 11px; font-weight: 600; color: #ef4444; margin: 4px 0 0 0; display: flex; align-items: center; gap: 4px;">
                    <i class="ti ti-alert-circle" style="font-size: 13px;"></i>
                    <span>${message}</span>
                </p>
            `);
        }

        // Consistent Helper to clear error
        function clearError(element) {
            const $el = $(element);
            const $container = $el.closest('.field-group, .space-y-1\\.5, .space-y-1');
            
            const $targetInput = $el.next('.flatpickr-input[type="text"]').length ? $el.next('.flatpickr-input[type="text"]') : $el;

            $targetInput.css({
                'border-color': '#cbd5e1',
                'background-color': '#ffffff',
                'box-shadow': 'none'
            });
            
            $el.closest('.relative').find('i').css('color', '#94a3b8');
            $container.find('.error-msg').remove();
        }

        function validateSingleField(el) {
            const $el = $(el);
            const name = $el.attr('name') || $el.attr('id');
            const val = ($el.val() || '').toString().trim();
            const sumber = $('input[name="sumber"]:checked').val();

            if (name === 'nama_periode') {
                if (!val) {
                    showError($el, 'Nama periode jadwal wajib diisi');
                    return false;
                }
            }

            if (sumber === 'pattern') {
                if (name === 'kode_pola') {
                    if (!val) {
                        showError($el, 'Template Pola Shift wajib dipilih');
                        return false;
                    }
                }
                if (name === 'tanggal_mulai_pola') {
                    if (!val) {
                        showError($el, 'Tanggal mulai siklus pola shift wajib diisi');
                        return false;
                    }
                }
            }

            clearError($el);
            return true;
        }

        // Realtime input/change listeners
        $('#formCreateJadwalV2').on('input change blur', 'input, select, textarea', function(e) {
            validateSingleField(this);
        });

        // Form Submit
        $('#formCreateJadwalV2').on('submit', function(e) {
            let isValid = true;
            let firstInvalidEl = null;

            const fieldsToValidate = ['#nama_periode'];
            const sumber = $('input[name="sumber"]:checked').val();

            if (sumber === 'pattern') {
                fieldsToValidate.push('#kode_pola', '#tanggal_mulai_pola');
            }

            fieldsToValidate.forEach(function(selector) {
                const $el = $(selector);
                if ($el.length > 0) {
                    const valid = validateSingleField($el);
                    if (!valid) {
                        isValid = false;
                        if (!firstInvalidEl) {
                            firstInvalidEl = $el;
                        }
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();

                if (firstInvalidEl) {
                    firstInvalidEl.focus();
                    if (firstInvalidEl[0].scrollIntoView) {
                        firstInvalidEl[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Formulir Belum Lengkap',
                        text: 'Silakan periksa kembali field yang bertanda merah.',
                        confirmButtonColor: '{{ $themeColor1 }}',
                        customClass: {
                            popup: 'rounded-2xl shadow-xl'
                        }
                    });
                }

                const $btn = $('#btnSubmitJadwalV2');
                $btn.prop('disabled', false).html('<i class="ti ti-wand text-sm"></i><span>Generate Draft Jadwal</span>');
                return false;
            }

            const $btn = $('#btnSubmitJadwalV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Sedang Meng-generate...</span>');
        });
    });
</script>
@endpush
