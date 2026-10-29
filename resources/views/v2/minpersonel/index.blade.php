@extends('v2.layouts.app')

@section('titlepage', 'Minimum Personel Shift')
@section('page_title', 'Aturan Minimum Personel Shift')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalRules = $rules->total();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMBS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-shield-check text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Minimum Personel Shift</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Tentukan batas kuota minimal staf per departemen/cabang/grup untuk menjaga stabilitas operasional pada setiap shift kerja
            </p>
        </div>

        <!-- Right Side: Breadcrumbs Navigation -->
        <nav class="flex items-center text-xs text-slate-400 font-medium">
            <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                <i class="ti ti-home text-sm"></i>
                <span>Dashboard</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="text-slate-500 flex items-center gap-1">
                <i class="ti ti-calendar text-sm"></i>
                <span>Jadwal Kerja</span>
            </span>
            <span class="mx-2 text-slate-300">/</span>
            <span class="font-bold text-slate-800">Min. Personel</span>
        </nav>
    </div>

    <!-- ================= 2. GRID 2 KOLOM (FORM INPUT & DAFTAR ATURAN) ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- PANEL KIRI (4 KOLOM): FORM TAMBAH / UPDATE ATURAN -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <!-- Header dengan Gradien Tema -->
            <div class="px-5 py-3.5 text-white flex items-center gap-2.5"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-sm font-bold shadow-inner text-white">
                    <i class="ti ti-plus"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black tracking-tight text-white uppercase">Konfigurasi Kuota</h3>
                    <p class="text-[10.5px] text-white/80 leading-none mt-0.5">Tambah batas kuota minimum personel</p>
                </div>
            </div>

            <form action="{{ route('minpersonel.store') }}" method="POST" id="formMinPersonelV2" class="p-5 space-y-4" novalidate>
                @csrf

                <!-- 1. Cabang -->
                <div class="space-y-1.5 field-group">
                    <label for="kode_cabang" class="block text-xs font-bold text-slate-700">
                        Cabang <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-map-pin text-base"></i>
                        </span>
                        <select name="kode_cabang" id="kode_cabang" class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                            <option value="">-- Berlaku Semua Cabang --</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[10.5px] text-slate-400">Kosongkan jika aturan berlaku di seluruh cabang.</p>
                </div>

                <!-- 2. Departemen -->
                <div class="space-y-1.5 field-group">
                    <label for="kode_dept" class="block text-xs font-bold text-slate-700">
                        Departemen <span class="text-rose-500 font-bold">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                            <i class="ti ti-building text-base"></i>
                        </span>
                        <select name="kode_dept" id="kode_dept" class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 3. Grup Karyawan -->
                <div class="space-y-1.5 field-group">
                    <label for="kode_grup" class="block text-xs font-bold text-slate-700">
                        Grup Karyawan <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-users-group text-base"></i>
                        </span>
                        <select name="kode_grup" id="kode_grup" class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                            <option value="">-- Berlaku Semua Grup --</option>
                            @foreach ($grups as $g)
                                <option value="{{ $g->kode_grup }}">{{ $g->nama_grup }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-[10.5px] text-slate-400">Pilih jika kuota spesifik untuk regu/shift tertentu.</p>
                </div>

                <!-- 4. Shift / Jam Kerja -->
                <div class="space-y-1.5 field-group">
                    <label for="kode_jam_kerja" class="block text-xs font-bold text-slate-700">
                        Shift / Jam Kerja Target <span class="text-rose-500 font-bold">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                            <i class="ti ti-clock text-base"></i>
                        </span>
                        <select name="kode_jam_kerja" id="kode_jam_kerja" class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer" required>
                            <option value="">-- Pilih Shift / Jam Kerja --</option>
                            @foreach ($jamkerja as $j)
                                <option value="{{ $j->kode_jam_kerja }}">
                                    {{ $j->nama_jam_kerja }} ({{ substr($j->jam_masuk, 0, 5) }} - {{ substr($j->jam_pulang, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 5. Minimum Personel -->
                <div class="space-y-1.5 field-group">
                    <label for="min_personel" class="block text-xs font-bold text-slate-700">
                        Jumlah Minimum Personel <span class="text-rose-500 font-bold">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                            <i class="ti ti-users text-base"></i>
                        </span>
                        <input type="number" 
                               id="min_personel" 
                               name="min_personel" 
                               min="1" 
                               max="999" 
                               value="1" 
                               required
                               class="w-full h-10 pl-10 pr-16 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        <span class="absolute right-3.5 text-xs font-bold text-slate-400 pointer-events-none">
                            Orang
                        </span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2 border-t border-slate-100">
                    <button type="submit" 
                            id="btnSubmitMinPersonelV2" 
                            class="w-full py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-device-floppy text-sm"></i>
                        <span>Simpan Aturan Kuota</span>
                    </button>
                </div>

            </form>
        </div>

        <!-- PANEL KANAN (8 KOLOM): FILTER & TABEL DAFTAR ATURAN -->
        <div class="lg:col-span-8 space-y-4">
            
            <!-- FILTER BAR STANDAR V2 (CABANG, DEPT, GRUP + SEJAJAR DENGAN TOMBOL FILTER) -->
            <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
                <form action="{{ route('minpersonel.index') }}" method="GET" class="w-full">
                    <div class="flex flex-col sm:flex-row items-center gap-2.5 sm:gap-3 w-full">
                        <!-- Cabang Filter -->
                        <div class="w-full sm:w-1/3">
                            <select name="kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabang as $c)
                                    <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                        {{ $c->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Departemen Filter -->
                        <div class="w-full sm:w-1/3">
                            <select name="kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                        {{ $d->nama_dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Grup Filter -->
                        <div class="w-full sm:w-1/3">
                            <select name="kode_grup" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                                <option value="">Semua Grup</option>
                                @foreach ($grups as $g)
                                    <option value="{{ $g->kode_grup }}" {{ request('kode_grup') == $g->kode_grup ? 'selected' : '' }}>
                                        {{ $g->nama_grup }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tombol Filter & Reset Sejajar -->
                        <div class="flex items-center gap-1.5 shrink-0 w-full sm:w-auto">
                            <button type="submit" 
                                    class="w-full sm:w-auto px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                                    style="background-color: {{ $themeColor1 }};">
                                <i class="ti ti-search text-sm"></i>
                                <span>Filter</span>
                            </button>
                            @if(request()->hasAny(['kode_cabang', 'kode_dept', 'kode_grup']))
                                <a href="{{ route('minpersonel.index') }}" 
                                   class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1 active:scale-95"
                                   title="Reset Filter">
                                    <i class="ti ti-rotate text-sm"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR ATURAN KUOTA MINIMUM (SEAMLESS SOLID HEADER STANDAR V2) -->
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                
                <!-- Seamless Solid Card Header -->
                <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
                     style="background-color: {{ $themeColor1 }};">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                            <i class="ti ti-shield-check"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Aturan Kuota Minimum</h3>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                        {{ $totalRules }} aturan
                    </span>
                </div>

                <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
                    <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                        <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                               style="background-color: {{ $themeColor1 }};">
                            <tr class="border-0 border-t-0 text-white/90">
                                <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Departemen & Cakupan</th>
                                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Shift / Jam Kerja</th>
                                <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Min. Personel</th>
                                <th class="py-2.5 px-3.5 text-center w-20 text-white/90 border-0 border-t-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                            @forelse ($rules as $r)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <!-- NO -->
                                    <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
                                        <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                            {{ $rules->firstItem() + $loop->index }}
                                        </span>
                                    </td>

                                    <!-- DEPARTEMEN & CAKUPAN -->
                                    <td class="py-2.5 px-4">
                                        <div class="font-bold text-slate-900 text-xs sm:text-sm">
                                            {{ $r->departemen->nama_dept ?? $r->kode_dept }}
                                        </div>
                                        <div class="flex items-center flex-wrap gap-1 mt-1 text-[11px]">
                                            @if($r->cabang)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium text-[10.5px]">
                                                    <i class="ti ti-map-pin text-slate-400"></i>
                                                    <span>{{ $r->cabang->nama_cabang }}</span>
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[10.5px]">Semua Cabang</span>
                                            @endif

                                            <span class="text-slate-300">&bull;</span>

                                            @if($r->grup)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 font-bold border border-emerald-100 text-[10.5px]">
                                                    <i class="ti ti-users text-emerald-600"></i>
                                                    <span>{{ $r->grup->nama_grup }}</span>
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[10.5px]">Semua Grup</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- SHIFT / JAM KERJA -->
                                    <td class="py-2.5 px-4">
                                        @php
                                            $shiftColor = $r->jamKerja->color ?? '#0d6efd';
                                        @endphp
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-white font-bold text-xs shadow-2xs"
                                             style="background-color: {{ $shiftColor }};">
                                            <i class="ti ti-clock text-xs"></i>
                                            <span>{{ $r->jamKerja->nama_jam_kerja ?? $r->kode_jam_kerja }}</span>
                                        </div>
                                        <div class="text-[10.5px] text-slate-400 mt-0.5">
                                            {{ substr($r->jamKerja->jam_masuk ?? '--', 0, 5) }} - {{ substr($r->jamKerja->jam_pulang ?? '--', 0, 5) }}
                                        </div>
                                    </td>

                                    <!-- MIN PERSONEL -->
                                    <td class="py-2.5 px-3.5 text-center">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-black text-xs border border-emerald-200 shadow-2xs">
                                            <i class="ti ti-users text-emerald-600 text-sm"></i>
                                            <span>{{ $r->min_personel }} Orang</span>
                                        </span>
                                    </td>

                                    <!-- AKSI -->
                                    <td class="py-2.5 px-3.5 text-center">
                                        <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                            <form action="{{ route('minpersonel.destroy', Crypt::encrypt($r->id)) }}" 
                                                  method="POST" 
                                                  class="formDelete inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-7 h-7 rounded-md hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                                        title="Hapus Aturan">
                                                    <i class="ti ti-trash text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 bg-white">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl mx-auto mb-2 text-slate-400">
                                            <i class="ti ti-shield-off"></i>
                                        </div>
                                        <div class="font-bold text-xs text-slate-600">Belum ada aturan kuota minimum</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">Gunakan form di sebelah kiri untuk membuat aturan baru</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($rules->hasPages())
                    <div class="p-3 border-t border-slate-200 bg-white">
                        {{ $rules->links('v2.components.pagination') }}
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    // Consistent Error Helpers
    function showError(element, message) {
        const $el = $(element);
        const $container = $el.closest('.field-group, .space-y-1\\.5, .space-y-1');
        
        $el.css({
            'border-color': '#ef4444',
            'background-color': '#fff5f5',
            'box-shadow': '0 0 0 2px rgba(239, 68, 68, 0.2)'
        });
        
        $el.siblings('span').find('i').css('color', '#ef4444');
        
        $container.find('.error-msg').remove();
        $container.append(`
            <p class="error-msg" style="font-size: 11px; font-weight: 600; color: #ef4444; margin: 4px 0 0 0; display: flex; align-items: center; gap: 4px;">
                <i class="ti ti-alert-circle" style="font-size: 13px;"></i>
                <span>${message}</span>
            </p>
        `);
    }

    function clearError(element) {
        const $el = $(element);
        const $container = $el.closest('.field-group, .space-y-1\\.5, .space-y-1');
        
        $el.css({
            'border-color': '#cbd5e1',
            'background-color': '#ffffff',
            'box-shadow': 'none'
        });
        
        $el.siblings('span').find('i').css('color', '#94a3b8');
        $container.find('.error-msg').remove();
    }

    function validateSingleField(el) {
        const $el = $(el);
        const name = $el.attr('name') || $el.attr('id');
        const val = ($el.val() || '').toString().trim();

        if (name === 'kode_dept') {
            if (!val) {
                showError($el, 'Departemen wajib dipilih');
                return false;
            }
        }

        if (name === 'kode_jam_kerja') {
            if (!val) {
                showError($el, 'Shift / Jam Kerja wajib dipilih');
                return false;
            }
        }

        if (name === 'min_personel') {
            const num = parseInt(val);
            if (!val || isNaN(num) || num < 1 || num > 999) {
                showError($el, 'Jumlah personel minimal 1 orang');
                return false;
            }
        }

        clearError($el);
        return true;
    }

    $('#formMinPersonelV2').on('input change blur', 'input, select', function(e) {
        validateSingleField(this);
    });

    $('#formMinPersonelV2').on('submit', function(e) {
        let isValid = true;
        let firstInvalidEl = null;

        ['#kode_dept', '#kode_jam_kerja', '#min_personel'].forEach(function(selector) {
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
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
            }

            const $btn = $('#btnSubmitMinPersonelV2');
            $btn.prop('disabled', false).html('<i class="ti ti-device-floppy text-sm"></i><span>Simpan Aturan Kuota</span>');
            return false;
        }

        const $btn = $('#btnSubmitMinPersonelV2');
        $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
    });

    // Konfirmasi Hapus Aturan
    $('.formDelete').submit(function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Aturan Kuota?',
            text: 'Aturan minimum kuota staf pada shift ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl shadow-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
