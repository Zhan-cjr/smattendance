@extends('v2.layouts.app')

@section('titlepage', 'Laporan Gaji & Payroll')
@section('page_title', 'Laporan Gaji & Payroll')

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
                <i class="ti ti-cash-banknote text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Laporan Gaji & Rekap Payroll</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Cetak dan ekspor rekapitulasi penggajian bulanan/harian serta slip gaji karyawan
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Active Period Badge -->
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-[11px] font-bold text-slate-700 shadow-2xs">
                <i class="ti ti-wallet text-emerald-600"></i>
                <span>Payroll Engine: <strong class="text-slate-900">Bulanan & Harian</strong></span>
            </div>
        </div>
    </div>

    <!-- ================= 2. TABBED / DUAL FORM GENERATOR ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Left Side: Form Generator (Bulanan / Harian Tabbed Card) -->
        <div class="lg:col-span-8" x-data="{ payrollType: 'bulanan' }">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                
                <!-- Card Top Header & Tab Switcher -->
                <div class="px-4 sm:px-5 py-3 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 bg-slate-50/60">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white shadow-2xs"
                             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-adjustments-alt text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 tracking-tight">Generator Laporan Gaji</h2>
                        </div>
                    </div>

                    <!-- Type Switcher Tabs -->
                    <div class="inline-flex p-0.5 bg-slate-200/70 rounded-lg border border-slate-300/60 shadow-inner gap-0.5 self-start sm:self-auto">
                        <button type="button" 
                                @click="payrollType = 'bulanan'"
                                :class="payrollType === 'bulanan' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="px-3 py-1 rounded-md text-[11px] transition flex items-center gap-1 cursor-pointer">
                            <i class="ti ti-calendar-month text-xs"></i>
                            <span>Gaji Bulanan</span>
                        </button>
                        <button type="button" 
                                @click="payrollType = 'harian'"
                                :class="payrollType === 'harian' ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="px-3 py-1 rounded-md text-[11px] transition flex items-center gap-1 cursor-pointer">
                            <i class="ti ti-clock-hour-4 text-xs"></i>
                            <span>Upah Harian</span>
                        </button>
                    </div>
                </div>

                <!-- ================= TAB 1: FORM GAJI BULANAN ================= -->
                <div x-show="payrollType === 'bulanan'" class="p-4 sm:p-5">
                    <form action="{{ route('laporan.cetakpresensi') }}" method="POST" target="_blank" id="formBulanan" class="space-y-3.5" novalidate>
                        @csrf
                        <input type="hidden" name="jenis_upah" value="Bulanan">

                        <!-- Filter Organisasi: Cabang & Departemen -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Cabang -->
                            <div class="space-y-1">
                                <label for="kode_cabang_bulanan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-building text-slate-400 text-xs"></i>
                                    <span>Kantor / Cabang</span>
                                </label>
                                <select name="kode_cabang" id="kode_cabang_bulanan" class="select2Bulanan w-full">
                                    <option value="">Semua Cabang</option>
                                    @foreach ($cabang as $d)
                                        <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Departemen -->
                            <div class="space-y-1">
                                <label for="kode_dept_bulanan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-category text-slate-400 text-xs"></i>
                                    <span>Departemen / Divisi</span>
                                </label>
                                <select name="kode_dept" id="kode_dept_bulanan" class="select2Bulanan w-full">
                                    <option value="">Semua Departemen</option>
                                    @foreach ($departemen as $d)
                                        <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Pilih Karyawan Bulanan -->
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label for="nik_bulanan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-user text-slate-400 text-xs"></i>
                                    <span>Pilih Karyawan (Bulanan)</span>
                                </label>
                                <span id="loadingBulanan" class="hidden text-[10px] font-bold text-emerald-600 items-center gap-1">
                                    <i class="ti ti-loader-2 animate-spin text-xs"></i>
                                    <span>Memuat karyawan...</span>
                                </span>
                            </div>
                            <select name="nik" id="nik_bulanan" class="select2Bulanan w-full">
                                <option value="">Semua Karyawan (Bulanan)</option>
                            </select>
                        </div>

                        <!-- Model Periode Laporan Bulanan -->
                        <div class="space-y-1.5 pt-1">
                            <label class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-calendar-time text-slate-400 text-xs"></i>
                                <span>Model Periode Payroll</span>
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <!-- Cut-off Periode Gaji -->
                                <label class="relative flex items-center p-2.5 rounded-lg border border-slate-200/90 cursor-pointer transition hover:bg-slate-50/80 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                                    <input type="radio" name="periode_laporan" value="1" class="sr-only" checked>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3.5 h-3.5 rounded-full border border-slate-300 flex items-center justify-center">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-600 hidden indicator"></div>
                                        </div>
                                        <div>
                                            <span class="block text-[11px] font-bold text-slate-800">Periode Gaji</span>
                                            <span class="block text-[9.5px] text-slate-500 leading-tight">Cut-Off Payroll</span>
                                        </div>
                                    </div>
                                </label>

                                <!-- Bulan Berjalan -->
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

                                <!-- Range Tanggal Bebas -->
                                <label class="relative flex items-center p-2.5 rounded-lg border border-slate-200/90 cursor-pointer transition hover:bg-slate-50/80 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                                    <input type="radio" name="periode_laporan" value="3" class="sr-only">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3.5 h-3.5 rounded-full border border-slate-300 flex items-center justify-center">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-600 hidden indicator"></div>
                                        </div>
                                        <div>
                                            <span class="block text-[11px] font-bold text-slate-800">Range Bebas</span>
                                            <span class="block text-[9.5px] text-slate-500 leading-tight">Kustom Tanggal</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Dropdown Khusus Periode Gaji Spesifik (Muncul saat Mode 1) -->
                        <div id="baris_kode_periode_bulanan" class="space-y-1">
                            <label for="kode_periode_gaji_bulanan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-calendar-stats text-slate-400 text-xs"></i>
                                <span>Master Periode Gaji (Opsional)</span>
                            </label>
                            <select name="kode_periode_gaji" id="kode_periode_gaji_bulanan" class="select2Bulanan w-full">
                                <option value="">- Semua / Default Periode Global -</option>
                                @foreach ($periode_gaji as $p)
                                    <option value="{{ $p->kode_periode_gaji }}">
                                        {{ $p->kode_periode_gaji }} - {{ $p->nama_periode }} (Tgl {{ $p->tanggal_dari }} s/d {{ $p->tanggal_sampai }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dynamic Row: Bulan & Tahun (for Mode 1 & 2) -->
                        <div id="baris_bulan_tahun_bulanan" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label for="bulan_bulanan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-calendar text-slate-400 text-xs"></i>
                                    <span>Pilih Bulan</span>
                                </label>
                                <select name="bulan" id="bulan_bulanan" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                    <option value="">Pilih Bulan</option>
                                    @foreach ($list_bulan as $d)
                                        <option {{ $currentMonth == $d['kode_bulan'] ? 'selected' : '' }} value="{{ $d['kode_bulan'] }}">
                                            {{ $d['nama_bulan'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label for="tahun_bulanan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-calendar-event text-slate-400 text-xs"></i>
                                    <span>Pilih Tahun</span>
                                </label>
                                <select name="tahun" id="tahun_bulanan" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                    <option value="">Pilih Tahun</option>
                                    @for ($t = $start_year; $t <= date('Y'); $t++)
                                        <option {{ $currentYear == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- Dynamic Row: Range Tanggal (for Mode 3) -->
                        <div id="baris_tanggal_bulanan" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label for="dari_bulanan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-calendar-arrow-right text-slate-400 text-xs"></i>
                                    <span>Dari Tanggal</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="dari" id="dari_bulanan" placeholder="YYYY-MM-DD"
                                           class="w-full h-9 pl-9 pr-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs flatpickr-date">
                                    <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label for="sampai_bulanan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-calendar-arrow-left text-slate-400 text-xs"></i>
                                    <span>Sampai Tanggal</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="sampai" id="sampai_bulanan" placeholder="YYYY-MM-DD"
                                           class="w-full h-9 pl-9 pr-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs flatpickr-date">
                                    <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Format Laporan Output -->
                        <div class="space-y-1">
                            <label for="format_laporan_bulanan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-file-type-doc text-slate-400 text-xs"></i>
                                <span>Format Output Laporan</span>
                            </label>
                            <select name="format_laporan" id="format_laporan_bulanan" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                <option value="2" selected>Laporan Rekapitulasi Gaji (Tabel Rekap)</option>
                                <option value="3">Slip Gaji Karyawan (Cetak Slip / Cetak Massal)</option>
                            </select>
                        </div>

                        <!-- Action Submit Buttons -->
                        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                            <button type="submit" name="submitButton" value="1"
                                    class="w-full sm:flex-1 h-11 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-[0.98] cursor-pointer hover:opacity-95 flex items-center justify-center gap-2"
                                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-printer text-base"></i>
                                <span>Cetak Laporan / PDF</span>
                            </button>

                            <button type="submit" name="exportButton" value="1"
                                    class="w-full sm:flex-1 h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                                <i class="ti ti-file-spreadsheet text-base"></i>
                                <span>Export Data ke Excel</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB 2: FORM UPAH HARIAN ================= -->
                <div x-show="payrollType === 'harian'" class="p-4 sm:p-5" style="display: none;">
                    <form action="{{ route('laporan.cetakpresensi') }}" method="POST" target="_blank" id="formHarian" class="space-y-3.5" novalidate>
                        @csrf
                        <input type="hidden" name="jenis_upah" value="Harian">
                        <input type="hidden" name="periode_laporan" value="3"> <!-- Upah Harian uses Range Tanggal -->

                        <!-- Filter Organisasi: Cabang & Departemen -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Cabang -->
                            <div class="space-y-1">
                                <label for="kode_cabang_harian" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-building text-slate-400 text-xs"></i>
                                    <span>Kantor / Cabang</span>
                                </label>
                                <select name="kode_cabang" id="kode_cabang_harian" class="select2Harian w-full">
                                    <option value="">Semua Cabang</option>
                                    @foreach ($cabang as $d)
                                        <option value="{{ $d->kode_cabang }}">{{ textUpperCase($d->nama_cabang) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Departemen -->
                            <div class="space-y-1">
                                <label for="kode_dept_harian" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-category text-slate-400 text-xs"></i>
                                    <span>Departemen / Divisi</span>
                                </label>
                                <select name="kode_dept" id="kode_dept_harian" class="select2Harian w-full">
                                    <option value="">Semua Departemen</option>
                                    @foreach ($departemen as $d)
                                        <option value="{{ $d->kode_dept }}">{{ textUpperCase($d->nama_dept) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Range Tanggal Upah Harian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label for="dari_harian" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-calendar-arrow-right text-slate-400 text-xs"></i>
                                    <span>Dari Tanggal</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="dari" id="dari_harian" placeholder="YYYY-MM-DD"
                                           class="w-full h-9 pl-9 pr-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs flatpickr-date">
                                    <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label for="sampai_harian" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-calendar-arrow-left text-slate-400 text-xs"></i>
                                    <span>Sampai Tanggal</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="sampai" id="sampai_harian" placeholder="YYYY-MM-DD"
                                           class="w-full h-9 pl-9 pr-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs flatpickr-date">
                                    <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Pilih Karyawan Harian -->
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label for="nik_harian" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-user text-slate-400 text-xs"></i>
                                    <span>Pilih Karyawan (Harian)</span>
                                </label>
                                <span id="loadingHarian" class="hidden text-[10px] font-bold text-emerald-600 items-center gap-1">
                                    <i class="ti ti-loader-2 animate-spin text-xs"></i>
                                    <span>Memuat karyawan...</span>
                                </span>
                            </div>
                            <select name="nik" id="nik_harian" class="select2Harian w-full">
                                <option value="">Semua Karyawan (Harian)</option>
                            </select>
                        </div>

                        <!-- Format Laporan Output Harian -->
                        <div class="space-y-1">
                            <label for="format_laporan_harian" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-file-type-doc text-slate-400 text-xs"></i>
                                <span>Format Output Laporan</span>
                            </label>
                            <select name="format_laporan" id="format_laporan_harian" class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                <option value="2" selected>Laporan Rekapitulasi Gaji Harian</option>
                                <option value="3">Slip Upah Harian Karyawan</option>
                            </select>
                        </div>

                        <!-- Action Submit Buttons Harian -->
                        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                            <button type="submit" name="submitButton" value="1"
                                    class="w-full sm:flex-1 h-11 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-[0.98] cursor-pointer hover:opacity-95 flex items-center justify-center gap-2"
                                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-printer text-base"></i>
                                <span>Cetak Laporan Harian</span>
                            </button>

                            <button type="submit" name="exportButton" value="1"
                                    class="w-full sm:flex-1 h-11 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-[0.98] cursor-pointer flex items-center justify-center gap-2">
                                <i class="ti ti-file-spreadsheet text-base"></i>
                                <span>Export Data ke Excel</span>
                            </button>
                        </div>
                    </form>
                </div>

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
                    <span>Struktur Komponen Gaji</span>
                </div>

                <div class="space-y-2 text-[11.5px] text-slate-600">
                    <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ti ti-plus text-emerald-600 text-xs mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="text-slate-800 block text-[11px] mb-0.5">Komponen Pendapatan</strong>
                            <span class="text-[10.5px] leading-relaxed">Gaji pokok, tunjangan jabatan/fungsional, insentif lembur, dan penyesuaian penambah payroll.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ti ti-minus text-rose-600 text-xs mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="text-slate-800 block text-[11px] mb-0.5">Komponen Potongan</strong>
                            <span class="text-[10.5px] leading-relaxed">BPJS Kesehatan & TK, angsuran kasbon, denda terlambat, dan potongan jam kerja.</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                        <i class="ti ti-receipt text-amber-600 text-xs mt-0.5 shrink-0"></i>
                        <div>
                            <strong class="text-slate-800 block text-[11px] mb-0.5">Slip Gaji Digital</strong>
                            <span class="text-[10.5px] leading-relaxed">Dapat dicetak perorangan maupun massal untuk dibagikan ke pegawai.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Checklist Card -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl p-4 shadow-2xs space-y-2.5 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 text-white/5 pointer-events-none">
                    <i class="ti ti-currency-dollar text-[90px]"></i>
                </div>

                <div class="relative z-10 flex items-center gap-1.5 text-emerald-400 font-bold text-[11px] uppercase tracking-wider">
                    <i class="ti ti-sparkles text-xs"></i>
                    <span>Fitur Payroll Terpadu</span>
                </div>

                <ul class="relative z-10 space-y-1.5 text-[11px] text-slate-300">
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Otomatisasi kalkulasi PPh 21 (TER / Tahunan)</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Integrasi pemotongan angsuran pinjaman</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Sinkronisasi Denda & Jam Kehadiran GPS</span>
                    </li>
                    <li class="flex items-center gap-1.5">
                        <i class="ti ti-circle-check text-emerald-400 text-xs"></i>
                        <span>Ekspor format Excel siap upload Bank Payroll</span>
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

        initSelect2(".select2Bulanan");
        initSelect2(".select2Harian");

        // Initialize Flatpickr for date fields
        if (typeof flatpickr !== 'undefined') {
            flatpickr(".flatpickr-date", {
                dateFormat: "Y-m-d",
                locale: "id",
                allowInput: true
            });
        }

        // AJAX Load Karyawan Function
        function loadKaryawan(type) {
            const kode_cabang = $(`#kode_cabang_${type}`).val();
            const kode_dept = $(`#kode_dept_${type}`).val();
            const jenis_upah = type === 'bulanan' ? 'Bulanan' : 'Harian';
            const tanggal = type === 'harian' ? $(`#dari_harian`).val() : null;
            const targetSelect = $(`#nik_${type}`);
            const loadingIndicator = $(`#loading${type.charAt(0).toUpperCase() + type.slice(1)}`);

            loadingIndicator.removeClass('hidden').addClass('inline-flex');

            $.ajax({
                type: "GET",
                url: "{{ route('karyawan.getkaryawan') }}",
                data: {
                    kode_cabang: kode_cabang,
                    kode_dept: kode_dept,
                    jenis_upah: jenis_upah,
                    tanggal: tanggal
                },
                cache: false,
                success: function(respond) {
                    targetSelect.empty();
                    targetSelect.append(`<option value=''>Semua Karyawan (${jenis_upah})</option>`);
                    if (Array.isArray(respond)) {
                        respond.forEach(function(item) {
                            targetSelect.append(`<option value="${item.nik}">${item.nik} - ${item.nama_karyawan}</option>`);
                        });
                    }
                    targetSelect.trigger('change.select2');
                },
                error: function(err) {
                    console.error(`Gagal memuat karyawan ${type}:`, err);
                },
                complete: function() {
                    loadingIndicator.removeClass('inline-flex').addClass('hidden');
                }
            });
        }

        // Listeners Bulanan
        $("#kode_cabang_bulanan, #kode_dept_bulanan").on("change", function() {
            loadKaryawan('bulanan');
        });

        // Listeners Harian
        $("#kode_cabang_harian, #kode_dept_harian, #dari_harian").on("change", function() {
            loadKaryawan('harian');
        });

        // Initial Load
        loadKaryawan('bulanan');
        loadKaryawan('harian');

        // Toggle Periode Options Logic for Bulanan
        function updatePeriodeBulanan() {
            const selectedPeriode = $('input[name="periode_laporan"]:checked', '#formBulanan').val();
            
            // Visual indicators for radio cards
            $('input[name="periode_laporan"]', '#formBulanan').each(function() {
                const isChecked = $(this).is(':checked');
                $(this).closest('label').find('.indicator').toggleClass('hidden', !isChecked);
                if (isChecked) {
                    $(this).closest('label').addClass('border-emerald-600 bg-emerald-50/40').removeClass('border-slate-200/90');
                } else {
                    $(this).closest('label').removeClass('border-emerald-600 bg-emerald-50/40').addClass('border-slate-200/90');
                }
            });

            if (selectedPeriode === "1") {
                // Periode Gaji (Cut-off)
                $("#baris_kode_periode_bulanan").removeClass('hidden');
                $("#baris_tanggal_bulanan").addClass('hidden');
                $("#baris_bulan_tahun_bulanan").removeClass('hidden');
            } else if (selectedPeriode === "2") {
                // Bulan Berjalan (1-31)
                $("#baris_kode_periode_bulanan").addClass('hidden');
                $("#baris_tanggal_bulanan").addClass('hidden');
                $("#baris_bulan_tahun_bulanan").removeClass('hidden');
            } else if (selectedPeriode === "3") {
                // Range Tanggal Bebas
                $("#baris_kode_periode_bulanan").addClass('hidden');
                $("#baris_tanggal_bulanan").removeClass('hidden');
                $("#baris_bulan_tahun_bulanan").addClass('hidden');
            }
        }

        $('input[name="periode_laporan"]', '#formBulanan').on('change', updatePeriodeBulanan);
        updatePeriodeBulanan();

        // Form Validation Logic
        const validateForm = (formId) => {
            const form = $(formId);
            const format = form.find("[name='format_laporan']").val();
            const type = formId === '#formBulanan' ? 'bulanan' : 'harian';
            
            if (!format) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Format Output Laporan harus dipilih!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (type === 'bulanan') {
                const periode = $('input[name="periode_laporan"]:checked', '#formBulanan').val();
                if (!periode) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Model Periode Payroll harus dipilih!',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
                if (periode === "3") {
                    if (!$("#dari_bulanan").val() || !$("#sampai_bulanan").val()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Parameter Belum Lengkap',
                            text: 'Silakan isi tanggal Dari dan Sampai dengan lengkap!',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                } else {
                    if (!$("#bulan_bulanan").val() || !$("#tahun_bulanan").val()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Parameter Belum Lengkap',
                            text: 'Silakan pilih Bulan dan Tahun dengan benar!',
                            confirmButtonText: 'OK'
                        });
                        return false;
                    }
                }
            } else {
                // Harian
                if (!$("#dari_harian").val() || !$("#sampai_harian").val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Parameter Belum Lengkap',
                        text: 'Silakan isi rentang Tanggal Upah Harian dengan lengkap!',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }
            }
            return true;
        };

        $("#formBulanan, #formHarian").on("submit", function(e) {
            if (!validateForm('#' + $(this).attr('id'))) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush
