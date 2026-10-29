@extends('v2.layouts.app')

@section('titlepage', 'Set Jam Kerja - ' . $grup->nama_grup)
@section('page_title', 'Atur Jadwal Kerja Grup')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-6" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <!-- Breadcrumb & Back Navigation -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('grup.index') }}" 
               class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-sm">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('grup.index') }}" class="hover:text-emerald-600 transition-colors">Data Grup</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Atur Jadwal Shift</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight mt-0.5">Jadwal Shift: {{ $grup->nama_grup }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button" 
                    id="btnOpenGeneratePola"
                    class="px-4 py-2 text-xs font-bold text-white rounded-xl shadow-sm flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-wand text-sm"></i>
                <span>Generate dari Pola Shift</span>
            </button>
            <a href="{{ route('grup.detail', Crypt::encrypt($grup->kode_grup)) }}" 
               class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 shadow-sm transition-all flex items-center gap-1.5">
                <i class="ti ti-users text-base text-sky-600"></i>
                <span>Kelola Anggota</span>
            </a>
        </div>
    </div>

    <!-- Layout Grid: Left Sidebar (Anggota & Shift Draggable) + Right Content (Interactive Calendar) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Sidebar (Col 4) -->
        <div class="lg:col-span-4 space-y-5">
            
            <!-- Group Info Card -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-2xs space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-lg font-black shrink-0 shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-users-group"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-extrabold text-slate-900 truncate">{{ $grup->nama_grup }}</h4>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-0.5">
                            <span class="font-mono font-bold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">
                                {{ $grup->kode_grup }}
                            </span>
                            <span>•</span>
                            <span id="leftMemberCountBadge">Memuat...</span>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-emerald-50/60 border border-emerald-100 rounded-xl text-xs text-emerald-800 leading-relaxed font-medium">
                    <i class="ti ti-info-circle text-emerald-600 me-1"></i>
                    Drag & drop shift kerja ke tanggal kalender atau klik shift untuk menerapkan langsung.
                </div>
            </div>

            <!-- Draggable Shift Work Palette -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500">Pilihan Shift Kerja</h5>
                    <span class="text-[10.5px] text-slate-400">Drag ke Tanggal</span>
                </div>

                <div class="flex flex-col gap-2" id="jamKerjaTemplates">
                    @foreach ($jamKerja as $index => $d)
                        @php
                            $hash = 0;
                            for ($i = 0; $i < strlen($d->kode_jam_kerja); $i++) {
                                $hash = ord($d->kode_jam_kerja[$i]) + (($hash << 5) - $hash);
                            }
                            $colors = [
                                '#059669', '#2563eb', '#d97706', '#dc2626', 
                                '#7c3aed', '#0891b2', '#ea580c', '#475569'
                            ];
                            $color = $colors[abs($hash) % count($colors)];
                        @endphp
                        <div class="jam-kerja-template p-3 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 flex items-center justify-between gap-3 cursor-grab transition shadow-2xs active:cursor-grabbing group select-none"
                             data-kode="{{ $d->kode_jam_kerja }}" 
                             data-nama="{{ $d->nama_jam_kerja }}"
                             data-jam="{{ $d->jam_masuk }} - {{ $d->jam_pulang }}" 
                             data-color="{{ $color }}">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <span class="w-3.5 h-3.5 rounded-md shrink-0 shadow-2xs" style="background-color: {{ $color }};"></span>
                                <div class="min-w-0 flex-1">
                                    <h6 class="text-xs font-bold text-slate-800 truncate group-hover:text-emerald-700 transition">{{ $d->nama_jam_kerja }}</h6>
                                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $d->jam_masuk }} - {{ $d->jam_pulang }}</p>
                                </div>
                            </div>
                            <i class="ti ti-grip-vertical text-slate-300 group-hover:text-slate-500 text-base"></i>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Registered Members in Group (Mini Roster List) -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-5 shadow-2xs space-y-3">
                <div class="flex items-center justify-between">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-500">Anggota Regu</h5>
                    <a href="{{ route('grup.detail', Crypt::encrypt($grup->kode_grup)) }}" class="text-[11px] font-bold text-emerald-700 hover:underline">Kelola</a>
                </div>

                <div id="miniAnggotaContainer" class="max-h-[280px] overflow-y-auto space-y-2 pr-1 divide-y divide-slate-100">
                    <div class="text-center py-6 text-slate-400 text-xs">
                        <i class="ti ti-loader animate-spin text-lg block mb-1"></i>
                        Memuat daftar anggota...
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Main Content: Interactive Calendar (Col 8) -->
        <div class="lg:col-span-8 space-y-5">
            
            <!-- Calendar Card -->
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                
                <!-- Card Header with Month/Year Navigation -->
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                            <i class="ti ti-calendar-event text-emerald-600"></i>
                            <span>Kalender Jadwal Shift</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Penjadwalan shift kerja grup per hari dalam kalender</p>
                    </div>

                    <!-- Month & Year Selector Controls -->
                    <div class="flex items-center gap-2">
                        <select name="bulan" id="bulan" class="px-3 py-2 text-xs font-bold bg-slate-50 border border-slate-300 rounded-xl text-slate-700 outline-none focus:border-emerald-500 transition">
                            @php
                                $list_bulan = [
                                    ['kode_bulan' => '01', 'nama_bulan' => 'Januari'],
                                    ['kode_bulan' => '02', 'nama_bulan' => 'Februari'],
                                    ['kode_bulan' => '03', 'nama_bulan' => 'Maret'],
                                    ['kode_bulan' => '04', 'nama_bulan' => 'April'],
                                    ['kode_bulan' => '05', 'nama_bulan' => 'Mei'],
                                    ['kode_bulan' => '06', 'nama_bulan' => 'Juni'],
                                    ['kode_bulan' => '07', 'nama_bulan' => 'Juli'],
                                    ['kode_bulan' => '08', 'nama_bulan' => 'Agustus'],
                                    ['kode_bulan' => '09', 'nama_bulan' => 'September'],
                                    ['kode_bulan' => '10', 'nama_bulan' => 'Oktober'],
                                    ['kode_bulan' => '11', 'nama_bulan' => 'November'],
                                    ['kode_bulan' => '12', 'nama_bulan' => 'Desember'],
                                ];
                            @endphp
                            @foreach ($list_bulan as $d)
                                <option {{ $d['kode_bulan'] == date('m') ? 'selected' : '' }} value="{{ $d['kode_bulan'] }}">
                                    {{ $d['nama_bulan'] }}
                                </option>
                            @endforeach
                        </select>

                        <select name="tahun" id="tahun" class="px-3 py-2 text-xs font-bold bg-slate-50 border border-slate-300 rounded-xl text-slate-700 outline-none focus:border-emerald-500 transition">
                            @php
                                $start_year = date('Y') - 2;
                            @endphp
                            @for ($t = $start_year; $t <= date('Y') + 1; $t++)
                                <option {{ $t == date('Y') ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Multi-Date Selection Action Banner -->
                <div class="px-5 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2 text-xs">
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                id="btn-mode-seleksi" 
                                class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                            <i class="ti ti-select text-sm text-emerald-600"></i>
                            <span id="selectionModeText">Mode Seleksi Tanggal</span>
                        </button>
                        <span id="count-selected" class="hidden px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            0 tanggal terpilih
                        </span>
                    </div>

                    <div class="flex items-center gap-2" id="selectionActionButtons" style="display: none;">
                        <button type="button" 
                                id="btn-apply-jam-kerja" 
                                class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                            <i class="ti ti-check text-sm"></i>
                            <span>Terapkan Shift</span>
                        </button>
                        <button type="button" 
                                id="btn-clear-selection" 
                                class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-600 font-bold transition flex items-center gap-1 cursor-pointer">
                            <i class="ti ti-x text-sm"></i>
                            <span>Batal</span>
                        </button>
                    </div>
                </div>

                <!-- Calendar Interactive Grid Container -->
                <div class="p-4 sm:p-5">
                    <div id="calendar-container" class="w-full">
                        <!-- Calendar Rendered via JS -->
                        <div class="text-center py-12 text-slate-400">
                            <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mx-auto mb-2"></div>
                            <p class="text-xs font-medium">Memuat kalender jadwal...</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<!-- Modal Generate dari Pola Shift -->
<div class="modal fade" id="modalGeneratePolaV2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <form action="{{ (\Illuminate\Support\Facades\Route::has('grup.generateDariPola') ? route('grup.generateDariPola', Crypt::encrypt($grup->kode_grup)) : '#') }}" method="POST" id="formGeneratePolaV2" novalidate>
                @csrf
                <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-wand"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0">Generate dari Pola Shift</h5>
                            <p class="text-xs text-slate-500 mb-0 font-medium">Otomatiskan jadwal shift bulanan sesuai siklus rotasi</p>
                        </div>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal">
                        <i class="ti ti-x text-lg"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4" style="background-color: #ffffff;">
                    <!-- Alert Notice Info -->
                    <div class="p-3 rounded-xl border border-emerald-100 bg-emerald-50/60 flex items-start gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs shrink-0 mt-0.5">
                            <i class="ti ti-info-circle"></i>
                        </div>
                        <p class="text-xs text-emerald-900 leading-relaxed m-0">
                            Pilih template <strong>Pola Shift</strong> dan tentukan <strong>Tanggal Mulai Siklus (Hari 1)</strong>. Sistem akan menjadwalkan rotasi shift otomatis sepanjang bulan target.
                        </p>
                    </div>

                    <!-- Pola Shift Selection -->
                    <div class="space-y-1">
                        <label for="pola_shift_select" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                            Pilih Pola Shift <span style="color: #ef4444; font-weight: bold;">*</span>
                        </label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <span style="position: absolute; left: 12px; pointer-events: none; color: #94a3b8; display: flex; align-items: center; z-index: 2;">
                                <i class="ti ti-layers-subtract" style="font-size: 16px;"></i>
                            </span>
                            <select name="kode_pola" 
                                    id="pola_shift_select" 
                                    required
                                    style="width: 100%; height: 40px; padding-left: 38px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                                <option value="">-- Pilih Pola Shift --</option>
                                @foreach ($shiftPatterns as $p)
                                    <option value="{{ $p->kode_pola }}" {{ $grup->kode_pola == $p->kode_pola ? 'selected' : '' }}>
                                        {{ $p->nama_pola }} ({{ $p->jumlah_hari_siklus }} hari siklus)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tanggal Mulai Siklus (Flatpickr) -->
                    <div class="space-y-1">
                        <label for="tanggal_mulai_pola" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                            Tanggal Mulai Siklus (Hari Ke-1) <span style="color: #ef4444; font-weight: bold;">*</span>
                        </label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <span style="position: absolute; left: 12px; pointer-events: none; color: #94a3b8; display: flex; align-items: center; z-index: 2;">
                                <i class="ti ti-calendar" style="font-size: 16px;"></i>
                            </span>
                            <input type="text" 
                                   id="tanggal_mulai_pola" 
                                   name="tanggal_mulai_pola" 
                                   class="flatpickr-date"
                                   placeholder="Pilih Tanggal Mulai"
                                   value="{{ $grup->tanggal_mulai_pola ?? date('Y-m-01') }}" 
                                   required
                                   style="width: 100%; height: 40px; padding-left: 38px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease; cursor: pointer;">
                        </div>
                    </div>

                    <!-- Bulan & Tahun Target -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="gen_bulan" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                                Bulan Target <span style="color: #ef4444; font-weight: bold;">*</span>
                            </label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <span style="position: absolute; left: 12px; pointer-events: none; color: #94a3b8; display: flex; align-items: center; z-index: 2;">
                                    <i class="ti ti-calendar-month" style="font-size: 16px;"></i>
                                </span>
                                <select name="bulan" 
                                        id="gen_bulan" 
                                        required
                                        style="width: 100%; height: 40px; padding-left: 38px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                                    @foreach ($list_bulan as $d)
                                        <option {{ $d['kode_bulan'] == date('m') ? 'selected' : '' }} value="{{ (int)$d['kode_bulan'] }}">
                                            {{ $d['nama_bulan'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="gen_tahun" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                                Tahun Target <span style="color: #ef4444; font-weight: bold;">*</span>
                            </label>
                            <div style="position: relative; display: flex; align-items: center;">
                                <span style="position: absolute; left: 12px; pointer-events: none; color: #94a3b8; display: flex; align-items: center; z-index: 2;">
                                    <i class="ti ti-calendar-time" style="font-size: 16px;"></i>
                                </span>
                                <select name="tahun" 
                                        id="gen_tahun" 
                                        required
                                        style="width: 100%; height: 40px; padding-left: 38px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
                                    @for ($t = $start_year; $t <= date('Y') + 1; $t++)
                                        <option {{ $t == date('Y') ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Overwrite Checkbox -->
                    <label class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100/80 transition select-none">
                        <input type="checkbox" name="overwrite" value="1" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        <span class="text-xs text-slate-700 font-medium">Timpa (overwrite) jadwal yang sudah ada pada bulan target</span>
                    </label>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition cursor-pointer" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" 
                            id="btnSubmitGenerateV2"
                            class="px-5 py-2 text-xs font-bold text-white rounded-xl shadow-md flex items-center gap-1.5 transition active:scale-95 cursor-pointer"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-wand text-sm"></i>
                        <span>Mulai Generate</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Modern V2 Calendar Grid Styling */
    .v2-cal-header {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 6px;
        margin-bottom: 6px;
    }
    .v2-cal-header-day {
        text-align: center;
        padding: 8px 4px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        background: #f8fafc;
        border-radius: 8px;
    }
    .v2-cal-body {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 6px;
    }
    .v2-cal-cell {
        min-height: 88px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 7px 8px;
        background: #ffffff;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .v2-cal-cell.other-month {
        background: #fafafa !important;
        opacity: 0.4;
        border-color: #f1f5f9 !important;
    }
    .v2-cal-cell.today {
        box-shadow: inset 0 0 0 1.5px #10b981;
    }
    .v2-cal-cell.selection-active {
        border-color: #059669 !important;
        background: #ecfdf5 !important;
        box-shadow: 0 0 0 2.5px rgba(5,150,105,0.3) !important;
    }
    .v2-cal-cell.has-shift-active {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
    }
    .v2-badge-shift {
        font-size: 10.5px;
        font-weight: 700;
        padding: 4px 7px;
        border-radius: 7px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 4px;
        line-height: 1.2;
        box-shadow: 0 2px 4px -1px rgba(0,0,0,0.15);
    }
</style>

@endsection

@push('myscript')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        const kodeGrup = "{{ $grup->kode_grup }}";
        let jamKerjaData = [];
        let selectionMode = false;
        let selectedDates = new Set();
        let activeShiftPalette = null;

        const modalGenEl = document.getElementById('modalGeneratePolaV2');
        const modalGenInstance = new bootstrap.Modal(modalGenEl);

        $('#btnOpenGeneratePola').click(function() {
            modalGenInstance.show();
        });

        // Load Initial Data
        loadAnggotaList();
        loadJamKerjaData();

        $('#bulan, #tahun').on('change', function() {
            loadJamKerjaData();
        });

        function loadAnggotaList() {
            $.ajax({
                url: "{{ route('grup.getAnggotaGrup', Crypt::encrypt($grup->kode_grup)) }}",
                method: 'GET',
                success: function(response) {
                    if (response.count !== undefined) {
                        $('#leftMemberCountBadge').text(`${response.count} Anggota`);
                    }
                    if (response.data && response.data.length > 0) {
                        let html = '';
                        response.data.forEach(m => {
                            html += `
                                <div class="flex items-center gap-2.5 py-1.5">
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-slate-700 text-xs shrink-0">
                                        ${m.nama_karyawan ? m.nama_karyawan.charAt(0).toUpperCase() : '?'}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-bold text-slate-800 truncate">${m.nama_karyawan}</div>
                                        <div class="text-[10px] text-slate-400 truncate">${m.nik} • ${m.nama_dept || '-'}</div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#miniAnggotaContainer').html(html);
                    } else {
                        $('#miniAnggotaContainer').html('<div class="text-center py-4 text-xs text-slate-400">Belum ada anggota di grup ini.</div>');
                    }
                }
            });
        }

        function loadJamKerjaData() {
            const bulan = $('#bulan').val();
            const tahun = $('#tahun').val();

            $.ajax({
                url: "{{ route('grup.getJamKerjaBydate', Crypt::encrypt($grup->kode_grup)) }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    bulan: bulan,
                    tahun: tahun
                },
                success: function(response) {
                    jamKerjaData = response || [];
                    renderCalendar();
                },
                error: function(xhr) {
                    console.error('Error loading jam kerja data:', xhr);
                    jamKerjaData = [];
                    renderCalendar();
                }
            });
        }

        function hexToRgba(hex, alpha) {
            let c = hex.replace('#', '');
            if (c.length === 3) {
                c = c.split('').map(x => x + x).join('');
            }
            const r = parseInt(c.substring(0, 2), 16);
            const g = parseInt(c.substring(2, 4), 16);
            const b = parseInt(c.substring(4, 6), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        function renderCalendar() {
            const bulanVal = $('#bulan').val() || '{{ date("m") }}';
            const tahunVal = $('#tahun').val() || '{{ date("Y") }}';
            const bulan = parseInt(bulanVal) - 1;
            const tahun = parseInt(tahunVal);

            const dayNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];
            const firstDay = new Date(tahun, bulan, 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            let html = `
                <div class="v2-cal-header">
                    ${dayNames.map(d => `<div class="v2-cal-header-day">${d}</div>`).join('')}
                </div>
                <div class="v2-cal-body">
            `;

            for (let i = 0; i < 42; i++) {
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const isCurrentMonth = date.getMonth() === bulan;
                const isToday = date.toDateString() === new Date().toDateString();
                const dayNumber = date.getDate();
                const dateString = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

                const jkForDay = jamKerjaData.filter(jk => {
                    const jkDate = jk.tanggal ? jk.tanggal.split(' ')[0] : '';
                    return jkDate === dateString;
                });

                let badgeHtml = '';
                let primaryColor = null;

                jkForDay.forEach(jk => {
                    const color = getShiftColor(jk.kode_jam_kerja);
                    if (!primaryColor) primaryColor = color;
                    const jamMasuk = jk.jam_masuk || '';
                    const jamPulang = jk.jam_pulang || '';
                    badgeHtml += `
                        <div class="v2-badge-shift" style="background-color: ${color};" title="${jk.nama_jam_kerja} (${jamMasuk} - ${jamPulang})">
                            <span class="truncate pr-1">${jk.nama_jam_kerja}</span>
                            <button type="button" class="btn-del-shift hover:text-rose-200 ml-1 text-sm font-bold leading-none cursor-pointer" data-encid="${jk.encrypted_id}">&times;</button>
                        </div>
                    `;
                });

                const isSelected = selectedDates.has(dateString);
                const hasShift = jkForDay.length > 0;
                const cellClasses = [
                    'v2-cal-cell',
                    isCurrentMonth ? '' : 'other-month',
                    isToday ? 'today' : '',
                    isSelected ? 'selection-active' : '',
                    hasShift ? 'has-shift-active' : ''
                ].filter(Boolean).join(' ');

                let customStyle = '';
                if (hasShift && primaryColor && isCurrentMonth && !isSelected) {
                    const bgTint = hexToRgba(primaryColor, 0.08);
                    const borderTint = hexToRgba(primaryColor, 0.4);
                    customStyle = `style="background-color: ${bgTint}; border-color: ${borderTint};" data-original-bg="${bgTint}" data-original-border="${borderTint}"`;
                }

                html += `
                    <div class="${cellClasses}" data-date="${dateString}" ${customStyle}>
                        <div class="flex items-center justify-between pointer-events-none">
                            <span class="text-xs font-black ${hasShift ? 'text-slate-900 font-extrabold' : (isCurrentMonth ? 'text-slate-800' : 'text-slate-400')}">${dayNumber}</span>
                            ${isToday ? '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 ring-2 ring-emerald-200"></span>' : ''}
                        </div>
                        <div class="shift-badges-container flex-1 mt-1">
                            ${badgeHtml}
                        </div>
                    </div>
                `;
            }

            html += `</div>`;
            $('#calendar-container').html(html);

            setupDragAndDrop();
        }

        function getShiftColor(kode) {
            let hash = 0;
            for (let i = 0; i < (kode || '').length; i++) {
                hash = kode.charCodeAt(i) + ((hash << 5) - hash);
            }
            const colors = ['#059669', '#2563eb', '#d97706', '#dc2626', '#7c3aed', '#0891b2', '#ea580c', '#475569'];
            return colors[Math.abs(hash) % colors.length];
        }

        function setupDragAndDrop() {
            $('.jam-kerja-template').draggable({
                helper: 'clone',
                revert: 'invalid',
                cursor: 'grabbing',
                zIndex: 9999,
                start: function(e, ui) {
                    $(ui.helper).addClass('opacity-90 scale-95 shadow-xl');
                }
            });

            $('.v2-cal-cell').droppable({
                accept: '.jam-kerja-template',
                over: function(event, ui) {
                    const color = ui.draggable.data('color') || '#059669';
                    const bgTint = hexToRgba(color, 0.22);
                    const borderTint = hexToRgba(color, 0.9);
                    $(this).css({
                        'background-color': bgTint,
                        'border-color': borderTint,
                        'transform': 'scale(1.03)',
                        'box-shadow': `0 4px 12px ${hexToRgba(color, 0.25)}`
                    });
                },
                out: function(event, ui) {
                    const origBg = $(this).data('original-bg') || '';
                    const origBorder = $(this).data('original-border') || '';
                    $(this).css({
                        'background-color': origBg,
                        'border-color': origBorder,
                        'transform': 'scale(1)',
                        'box-shadow': 'none'
                    });
                },
                drop: function(event, ui) {
                    const date = $(this).data('date');
                    const kodeJamKerja = ui.draggable.data('kode');
                    const color = ui.draggable.data('color') || '#059669';
                    
                    // Instant visual feedback
                    const bgTint = hexToRgba(color, 0.12);
                    const borderTint = hexToRgba(color, 0.5);
                    $(this).css({
                        'background-color': bgTint,
                        'border-color': borderTint,
                        'transform': 'scale(1)',
                        'box-shadow': 'none'
                    });

                    applySingleShift(date, kodeJamKerja);
                }
            });
        }

        function applySingleShift(date, kodeJamKerja) {
            $.ajax({
                url: "{{ route('grup.updateJamKerja', Crypt::encrypt($grup->kode_grup)) }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggal: date,
                    kode_jam_kerja: kodeJamKerja
                },
                success: function() {
                    if (window.toast) window.toast.success(`Shift berhasil disimpan untuk ${date}`);
                    loadJamKerjaData();
                },
                error: function() {
                    if (window.toast) window.toast.error('Gagal menyimpan shift kerja.');
                }
            });
        }

        // Multi-Date Selection Mode Toggle
        $('#btn-mode-seleksi').click(function() {
            selectionMode = !selectionMode;
            if (selectionMode) {
                $(this).addClass('bg-emerald-50 border-emerald-500 text-emerald-800');
                $('#selectionModeText').text('Mode Seleksi Aktif');
                $('#selectionActionButtons, #count-selected').show();
            } else {
                $(this).removeClass('bg-emerald-50 border-emerald-500 text-emerald-800');
                $('#selectionModeText').text('Mode Seleksi Tanggal');
                $('#selectionActionButtons, #count-selected').hide();
                selectedDates.clear();
                renderCalendar();
            }
        });

        $(document).on('click', '.v2-cal-cell', function(e) {
            if (!selectionMode) return;
            if ($(e.target).closest('.btn-del-shift').length) return;

            const date = $(this).data('date');
            if (selectedDates.has(date)) {
                selectedDates.delete(date);
                $(this).removeClass('selection-active');
            } else {
                selectedDates.add(date);
                $(this).addClass('selection-active');
            }
            $('#count-selected').text(`${selectedDates.size} tanggal terpilih`);
        });

        // Apply Shift to Multi-Selected Dates
        $('#btn-apply-jam-kerja').click(function() {
            if (selectedDates.size === 0) {
                if (window.toast) window.toast.warning('Pilih minimal 1 tanggal pada kalender.');
                return;
            }

            Swal.fire({
                title: 'Terapkan Shift Kerja?',
                text: `Shift akan diterapkan ke ${selectedDates.size} tanggal terpilih.`,
                icon: 'question',
                input: 'select',
                inputOptions: {
                    @foreach($jamKerja as $jk)
                        "{{ $jk->kode_jam_kerja }}": "{{ $jk->nama_jam_kerja }} ({{ $jk->jam_masuk }}-{{ $jk->jam_pulang }})",
                    @endforeach
                },
                showCancelButton: true,
                confirmButtonText: 'Terapkan',
                confirmButtonColor: '#059669',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.isConfirmed && res.value) {
                    const chosenShift = res.value;
                    let promises = [];
                    selectedDates.forEach(d => {
                        promises.push($.ajax({
                            url: "{{ route('grup.updateJamKerja', Crypt::encrypt($grup->kode_grup)) }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                tanggal: d,
                                kode_jam_kerja: chosenShift
                            }
                        }));
                    });

                    Promise.all(promises).then(() => {
                        if (window.toast) window.toast.success(`Shift berhasil diterapkan ke ${selectedDates.size} tanggal.`);
                        selectedDates.clear();
                        $('#count-selected').text('0 tanggal terpilih');
                        loadJamKerjaData();
                    });
                }
            });
        });

        $('#btn-clear-selection').click(function() {
            selectedDates.clear();
            $('#count-selected').text('0 tanggal terpilih');
            $('.v2-cal-cell').removeClass('selection-active');
        });

        // Delete Single Shift from Date
        $(document).on('click', '.btn-del-shift', function(e) {
            e.stopPropagation();
            const encId = $(this).data('encid');

            $.ajax({
                url: "{{ route('grup.deleteJamKerjaBydate') }}",
                method: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: encId
                },
                success: function() {
                    if (window.toast) window.toast.success('Shift berhasil dihapus dari tanggal.');
                    loadJamKerjaData();
                },
                error: function() {
                    if (window.toast) window.toast.error('Gagal menghapus shift.');
                }
            });
        });

        // Flatpickr initialization for Modal Generate Pola
        if (typeof flatpickr !== 'undefined') {
            flatpickr('#tanggal_mulai_pola', {
                dateFormat: 'Y-m-d',
                allowInput: true,
                defaultDate: $('#tanggal_mulai_pola').val() || new Date(),
                static: false,
                position: 'auto',
                onOpen: function(selectedDates, dateStr, instance) {
                    $(instance.calendarContainer).css('z-index', 99999);
                }
            });
        }

        // Handle Submit State for Generate Modal
        $('#formGeneratePolaV2').on('submit', function() {
            const pola = $('#pola_shift_select').val();
            const tgl = $('#tanggal_mulai_pola').val();

            if (!pola || !tgl) {
                if (window.toast) window.toast.warning('Silakan lengkapi Pola Shift dan Tanggal Mulai Siklus.');
                return false;
            }

            const $btn = $('#btnSubmitGenerateV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Memproses...</span>');
        });
    });
</script>
@endpush
