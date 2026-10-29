@extends('v2.layouts.app')

@section('titlepage', 'Pengaturan PPh 21')
@section('page_title', 'Konfigurasi PPh 21')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & SUB-NAVIGASI) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-receipt-tax text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Pengaturan PPh 21</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Konfigurasi parameter perhitungan Pajak Penghasilan Pasal 21 (PMK 168/2023 & PP 58/2023)
            </p>
        </div>

        <!-- Breadcrumb Navigation -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-database text-sm"></i>
                    <span>Data Master</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">PPh 21</span>
            </nav>
        </div>
    </div>

    <!-- Sub-navigasi Tabs PPh 21 -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-200">
        <a href="{{ route('pph21.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95 text-white"
           style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-settings text-sm"></i>
            <span>Pengaturan Global</span>
        </a>
        <a href="{{ route('pph21.formula') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-function text-sm"></i>
            <span>Formula Komponen</span>
        </a>
        <a href="{{ route('pph21.ter') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-table text-sm"></i>
            <span>Tabel TER</span>
        </a>
        <a href="{{ route('pph21.progresif') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-chart-bar text-sm"></i>
            <span>Tarif Progresif</span>
        </a>
        <a href="{{ route('pph21.simulasi') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-emerald-50 text-emerald-700 border border-emerald-200 bg-white">
            <i class="ti ti-calculator text-sm"></i>
            <span>Simulasi Kalkulator</span>
        </a>
    </div>

    <!-- ================= 2. MAIN CONTENT ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- Left Column: Form Konfigurasi (8 Cols) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-5">
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                <!-- Header -->
                <div class="px-5 py-3.5 flex items-center justify-between text-white"
                     style="background-color: {{ $themeColor1 }};">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                            <i class="ti ti-receipt-tax"></i>
                        </div>
                        <h3 class="text-sm font-extrabold text-white tracking-tight">Parameter Perhitungan PPh 21</h3>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold shadow-2xs {{ $setting->status_aktif ? 'bg-emerald-500/90 text-white border border-emerald-400' : 'bg-rose-500/90 text-white border border-rose-400' }}">
                        {{ $setting->status_aktif ? '✓ AKTIF' : '✕ NONAKTIF' }}
                    </span>
                </div>

                <!-- Form Body -->
                <form action="{{ route('pph21.setting.update') }}" method="POST" class="p-5 sm:p-6 space-y-5" id="formPphSetting">
                    @csrf
                    @method('PUT')

                    <!-- Status Aktif Banner & Switch -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 flex items-center justify-between gap-4">
                        <div>
                            <label for="status_aktif" class="font-extrabold text-slate-800 text-xs sm:text-sm block cursor-pointer">
                                Aktifkan Fitur PPh 21 Otomatis
                            </label>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Jika diaktifkan, PPh 21 akan dihitung otomatis saat generate slip gaji dan rekap payroll.
                            </p>
                        </div>
                        <label class="v2-switch">
                            <input type="checkbox" name="status_aktif" id="status_aktif" value="1" {{ $setting->status_aktif ? 'checked' : '' }}>
                            <span class="v2-slider"></span>
                        </label>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Metode Perhitungan -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Metode Perhitungan <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="metode" id="metode" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                                    <option value="TER" {{ $setting->metode === 'TER' ? 'selected' : '' }}>
                                        TER (Tarif Efektif Rata-rata) — PP 58/2023
                                    </option>
                                    <option value="PROGRESIF" {{ $setting->metode === 'PROGRESIF' ? 'selected' : '' }}>
                                        Progresif Manual — Pasal 17 UU HPP
                                    </option>
                                </select>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">
                                <strong>TER:</strong> Jan–Nov menggunakan tarif flat TER, Desember menggunakan penyesuaian tarif progresif Pasal 17.
                            </p>
                        </div>

                        <!-- Siapa yang Menanggung -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Metode Tanggungan PPh <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="metode_tanggungan" id="metode_tanggungan" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                                    <option value="GROSS" {{ $setting->metode_tanggungan === 'GROSS' ? 'selected' : '' }}>
                                        GROSS — PPh ditanggung Karyawan (potong gaji)
                                    </option>
                                    <option value="GROSS_UP" {{ $setting->metode_tanggungan === 'GROSS_UP' ? 'selected' : '' }}>
                                        GROSS UP — PPh ditanggung Perusahaan (tunjangan pajak)
                                    </option>
                                </select>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">
                                <strong>GROSS:</strong> Potong gaji karyawan langsung. <strong>GROSS UP:</strong> Ditambahkan sebagai tunjangan pajak.
                            </p>
                        </div>

                        <!-- Biaya Jabatan (%) -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Biaya Jabatan (%) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" step="0.01" min="0" max="100" name="biaya_jabatan_persen" id="biaya_jabatan_persen"
                                       value="{{ $setting->biaya_jabatan_persen }}"
                                       class="w-full h-10 pl-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                            </div>
                            <p class="text-[11px] text-slate-500">Standar regulasi UU PPh: 5% dari bruto.</p>
                        </div>

                        <!-- Maks. Biaya Jabatan per Bulan -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">
                                Maks. Biaya Jabatan / Bulan <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" name="biaya_jabatan_max_bulan" id="biaya_jabatan_max_bulan"
                                       value="{{ number_format($setting->biaya_jabatan_max_bulan, 0, ',', '.') }}"
                                       class="w-full h-10 pl-9 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition formatAngka" required>
                            </div>
                            <p class="text-[11px] text-slate-500">Standar regulasi: Rp 500.000 / bulan (Rp 6.000.000 / thn).</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="submit" id="btnSubmitSetting"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-md transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-device-floppy text-sm"></i>
                            <span>Simpan Pengaturan</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ringkasan Alur Regulasi PMK 168/2023 -->
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs p-4 sm:p-5">
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="ti ti-book text-base text-emerald-600"></i>
                    <span>Ringkasan Skema Regulasi PPh 21 (PMK 168/2023)</span>
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/50">
                        <div class="flex items-center gap-1.5 text-blue-700 font-bold text-xs mb-1">
                            <i class="ti ti-calendar text-sm"></i>
                            <span>Masa Jan – Nov</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mb-2">Menggunakan metode <strong>TER Bulanan</strong></p>
                        <div class="px-2 py-1.5 rounded-lg bg-white border border-blue-200 text-blue-800 font-bold text-[11px] text-center shadow-2xs">
                            Bruto × Tarif TER Kategori
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-rose-200 bg-rose-50/50">
                        <div class="flex items-center gap-1.5 text-rose-700 font-bold text-xs mb-1">
                            <i class="ti ti-calendar-event text-sm"></i>
                            <span>Masa Desember</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mb-2">Penyesuaian Tarif <strong>Pasal 17</strong></p>
                        <div class="px-2 py-1.5 rounded-lg bg-white border border-rose-200 text-rose-800 font-bold text-[11px] text-center shadow-2xs">
                            PPh Setahun − Terbayar Jan-Nov
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-emerald-200 bg-emerald-50/50">
                        <div class="flex items-center gap-1.5 text-emerald-700 font-bold text-xs mb-1">
                            <i class="ti ti-receipt text-sm"></i>
                            <span>Lapisan Tarif Ps 17</span>
                        </div>
                        <p class="text-[11px] text-slate-600 mb-1 leading-tight">≤ 60 jt: <strong>5%</strong> | 60-250 jt: <strong>15%</strong></p>
                        <p class="text-[11px] text-slate-600 leading-tight">250-500 jt: <strong>25%</strong> | 500 jt-5 M: <strong>30%</strong></p>
                        <p class="text-[11px] text-slate-600 leading-tight">> 5 M: <strong>35%</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: PTKP & Kategori TER (4 Cols) -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-5">
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                <!-- Header -->
                <div class="px-4 py-3 flex items-center justify-between text-white"
                     style="background-color: {{ $themeColor1 }};">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold">
                            <i class="ti ti-users"></i>
                        </div>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-tight">Status PTKP & Kategori TER</h3>
                    </div>
                    <span class="text-[11px] font-bold text-white/90 bg-white/20 px-2 py-0.5 rounded-md">
                        {{ $statuskawin->count() }} Status
                    </span>
                </div>

                <!-- Table PTKP -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="py-2.5 px-3.5">Kode</th>
                                <th class="py-2.5 px-3 text-end">Nilai PTKP (Thn)</th>
                                <th class="py-2.5 px-3.5 text-center">TER</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                            @foreach($statuskawin as $sk)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-2 px-3.5 font-bold text-slate-900">
                                    {{ $sk->kode_status_kawin }}
                                    <span class="block text-[10px] text-slate-400 font-normal truncate max-w-[110px]">{{ $sk->status_kawin ?? '' }}</span>
                                </td>
                                <td class="py-2 px-3 text-end font-semibold text-slate-800">
                                    Rp {{ number_format($sk->nilai_ptkp ?? 54000000, 0, ',', '.') }}
                                </td>
                                <td class="py-2 px-3.5 text-center">
                                    @if($sk->kategori_ter === 'A')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-blue-100 text-blue-700 border border-blue-200">A</span>
                                    @elseif($sk->kategori_ter === 'B')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-100 text-amber-700 border border-amber-200">B</span>
                                    @elseif($sk->kategori_ter === 'C')
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200">C</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 bg-slate-50/70 border-t border-slate-100 text-[11px] text-slate-500 flex items-center gap-1.5">
                    <i class="ti ti-info-circle text-sm text-slate-400"></i>
                    <span>Kategori TER otomatis terpaut pada master status kawin karyawan.</span>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('myscript')
<script>
$(function () {
    // Format angka otomatis pada input biaya jabatan max
    $('.formatAngka').on('input', function () {
        let val = $(this).val().replace(/\D/g, '');
        $(this).val(val.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    });

    $('.formatAngka').on('focus', function () {
        let val = $(this).val().replace(/\./g, '');
        $(this).val(val);
    });

    $('.formatAngka').on('blur', function () {
        let val = parseInt($(this).val().replace(/\D/g, '')) || 0;
        $(this).val(val.toLocaleString('id-ID'));
    });

    // Sebelum submit form, unformat angka
    $('#formPphSetting').on('submit', function () {
        $('.formatAngka').each(function () {
            $(this).val($(this).val().replace(/\./g, ''));
        });
    });
});
</script>
@endpush
