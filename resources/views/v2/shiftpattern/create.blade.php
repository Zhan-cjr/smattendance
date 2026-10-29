@extends('v2.layouts.app')

@section('titlepage', 'Tambah Pola Shift')
@section('page_title', 'Tambah Pola Shift Baru')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">

    <!-- ================= 1. TOP HEADER & BREADCRUMB ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-1">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('shiftpattern.index') }}" 
                   class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition active:scale-95 shadow-2xs">
                    <i class="ti ti-arrow-left text-sm"></i>
                </a>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                    Tambah Pola Shift Baru
                </h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Tentukan jumlah hari siklus dan alokasikan jadwal shift jam kerja untuk setiap urutan hari (Hari 1 s/d N).
            </p>
        </div>

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center text-xs text-slate-400 font-medium">
            <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                <i class="ti ti-home text-sm"></i>
                <span>Dashboard</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <a href="{{ route('shiftpattern.index') }}" class="hover:text-slate-700 transition">
                <span>Pola Shift</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="font-bold text-slate-800">Tambah Baru</span>
        </nav>
    </div>

    <!-- ================= 2. FORM CREATE SHIFT PATTERN ================= -->
    <form action="{{ route('shiftpattern.store') }}" method="POST" id="formShiftPatternV2" class="space-y-4" novalidate>
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            
            <!-- PANEL KIRI (4 KOLOM): INFORMASI POLA & PRESET -->
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <!-- Header dengan Gradien Tema -->
                    <div class="px-5 py-3.5 text-white flex items-center gap-2.5"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-sm font-bold shadow-inner text-white">
                            <i class="ti ti-settings"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black tracking-tight text-white uppercase">Informasi Pola</h3>
                            <p class="text-[10.5px] text-white/80 leading-none mt-0.5">Kode unik, nama, & durasi hari siklus</p>
                        </div>
                    </div>

                    <div class="p-5 space-y-4">
                        <!-- Kode Pola -->
                        <div class="space-y-1.5 field-group">
                            <label for="kode_pola" class="block text-xs font-bold text-slate-700">
                                Kode Pola <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                                    <i class="ti ti-barcode text-base"></i>
                                </span>
                                <input type="text" 
                                       id="kode_pola" 
                                       name="kode_pola" 
                                       maxlength="5" 
                                       placeholder="Contoh: P42" 
                                       value="{{ old('kode_pola') }}" 
                                       autocomplete="off"
                                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                            </div>
                            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                                <span>Maksimal 5 karakter unik</span>
                                <span id="kodePolaCount">0/5</span>
                            </div>
                        </div>

                        <!-- Nama Pola -->
                        <div class="space-y-1.5 field-group">
                            <label for="nama_pola" class="block text-xs font-bold text-slate-700">
                                Nama Pola Shift <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                                    <i class="ti ti-typography text-base"></i>
                                </span>
                                <input type="text" 
                                       id="nama_pola" 
                                       name="nama_pola" 
                                       maxlength="50" 
                                       placeholder="Contoh: Pola 4 Kerja 2 Off" 
                                       value="{{ old('nama_pola') }}" 
                                       autocomplete="off"
                                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                            </div>
                        </div>

                        <!-- Jumlah Hari Siklus -->
                        <div class="space-y-1.5 field-group">
                            <label for="jumlah_hari_siklus" class="block text-xs font-bold text-slate-700">
                                Durasi Hari dalam 1 Siklus <span class="text-rose-500 font-bold">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                                    <i class="ti ti-calendar-stats text-base"></i>
                                </span>
                                <input type="number" 
                                       id="jumlah_hari_siklus" 
                                       name="jumlah_hari_siklus" 
                                       min="2" 
                                       max="31" 
                                       value="{{ old('jumlah_hari_siklus', 6) }}" 
                                       class="w-full h-10 pl-10 pr-14 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                                <span class="absolute right-3 text-xs font-bold text-slate-400 pointer-events-none">
                                    Hari
                                </span>
                            </div>
                            <p class="text-[10.5px] text-slate-400">Rentang antara 2 s/d 31 hari perulangan siklus.</p>
                        </div>

                        <!-- Keterangan / Catatan -->
                        <div class="space-y-1.5">
                            <label for="keterangan" class="block text-xs font-bold text-slate-700">
                                Keterangan / Catatan <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea id="keterangan" 
                                      name="keterangan" 
                                      rows="3" 
                                      placeholder="Penjelasan alur rotasi shift atau departemen pengguna..." 
                                      class="w-full p-3 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">{{ old('keterangan') }}</textarea>
                        </div>

                        <!-- Toggle Status Aktif -->
                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="block text-xs font-bold text-slate-800">Status Aktif</span>
                                <span class="block text-[10.5px] text-slate-400">Pola aktif dapat dipilih pada jadwal periode</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" id="is_active" value="1" checked class="sr-only peer">
                                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <!-- Template Cepat / Preset Preset -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-2">
                            <span class="block text-xs font-bold text-slate-700 flex items-center gap-1.5">
                                <i class="ti ti-wand text-emerald-600"></i> Preset Cepat (Populer):
                            </span>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button type="button" class="btn-preset px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:border-emerald-500 hover:text-emerald-700 text-[11px] font-bold transition text-left cursor-pointer active:scale-95 shadow-2xs" 
                                        data-days="6" data-name="Pola 4-2 (4 Kerja, 2 Off)" data-code="P42">
                                    4-2 (6 Hari)
                                </button>
                                <button type="button" class="btn-preset px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:border-emerald-500 hover:text-emerald-700 text-[11px] font-bold transition text-left cursor-pointer active:scale-95 shadow-2xs" 
                                        data-days="7" data-name="Pola 5-2 (5 Kerja, 2 Off)" data-code="P52">
                                    5-2 (7 Hari)
                                </button>
                                <button type="button" class="btn-preset px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:border-emerald-500 hover:text-emerald-700 text-[11px] font-bold transition text-left cursor-pointer active:scale-95 shadow-2xs" 
                                        data-days="7" data-name="Pola 2-2-2-1 (2 Pagi, 2 Siang, 2 Malam, 1 Off)" data-code="P2221">
                                    2-2-2-1 (7 Hari)
                                </button>
                                <button type="button" class="btn-preset px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:border-emerald-500 hover:text-emerald-700 text-[11px] font-bold transition text-left cursor-pointer active:scale-95 shadow-2xs" 
                                        data-days="7" data-name="Pola 6-1 (6 Kerja, 1 Off)" data-code="P61">
                                    6-1 (7 Hari)
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                            <button type="submit" 
                                    id="btnSubmitShiftPatternV2" 
                                    class="flex-1 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-device-floppy text-sm"></i>
                                <span>Simpan Pola</span>
                            </button>
                            <a href="{{ route('shiftpattern.index') }}" 
                               class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL KANAN (8 KOLOM): TIMELINE HARI SIKLUS & SIMULASI PREVIEW -->
            <div class="lg:col-span-8 space-y-4">
                
                <!-- TIMELINE ALOKASI SHIFT HARIAN -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-5 py-3.5 text-white flex items-center justify-between gap-3"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-sm font-bold shadow-inner text-white">
                                <i class="ti ti-calendar-event"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-black tracking-tight text-white uppercase">Timeline Alokasi Shift Harian</h3>
                                <p class="text-[10.5px] text-white/80 leading-none mt-0.5">Tentukan jam kerja untuk Hari ke-1 sampai ke-N</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-xl bg-white/20 text-white font-bold text-xs backdrop-blur-xs shadow-inner" id="cycleBadge">
                            6 Hari Siklus
                        </span>
                    </div>

                    <div class="p-5 space-y-3">
                        <div class="p-3 bg-sky-50 border border-sky-200 rounded-xl flex items-center gap-2.5 text-sky-900 text-xs">
                            <i class="ti ti-info-circle text-base text-sky-600 shrink-0"></i>
                            <span>Hari & tanggal dihitung dari <strong>Tanggal Mulai Hari 1</strong> pada bar simulasi di bawah. Hari Minggu otomatis ditandai aksen merah.</span>
                        </div>

                        <!-- Dynamic Grid Container Hari -->
                        <div id="patternTimelineContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            <!-- Injected by JavaScript -->
                        </div>
                    </div>
                </div>

                <!-- SIMULASI PREVIEW 1 BULAN KALENDER -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50 border-b border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <i class="ti ti-eye text-emerald-600 text-base"></i>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-tight">Simulasi Preview 1 Bulan Kalender</h4>
                        </div>

                        <!-- Controls -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- Mulai Tgl 1 Flatpickr -->
                            <div class="relative flex items-center">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                                    <i class="ti ti-calendar text-xs"></i>
                                </span>
                                <input type="text" 
                                       id="previewStartDate" 
                                       value="{{ date('Y-m-01') }}" 
                                       placeholder="Mulai Tgl 1"
                                       class="h-8 pl-7 pr-2.5 text-xs bg-white border border-slate-300 rounded-lg flatpickr-date outline-none transition cursor-pointer font-medium text-slate-800"
                                       style="width: 140px;">
                            </div>

                            <!-- Bulan Select -->
                            <select id="previewBulan" class="h-8 px-2 text-xs bg-white border border-slate-300 rounded-lg outline-none transition cursor-pointer font-medium text-slate-800">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>

                            <!-- Tahun Select -->
                            <select id="previewTahun" class="h-8 px-2 text-xs bg-white border border-slate-300 rounded-lg outline-none transition cursor-pointer font-medium text-slate-800">
                                @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>

                            <!-- Generate Preview Button -->
                            <button type="button" id="btnRunPreview" 
                                    class="h-8 px-3 rounded-lg text-white font-bold text-xs shadow-xs transition hover:opacity-95 active:scale-95 cursor-pointer flex items-center gap-1"
                                    style="background-color: {{ $themeColor1 }};">
                                <i class="ti ti-refresh text-xs"></i>
                                <span>Preview</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-4">
                        <div id="previewCalendarGrid" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-1.5 text-center text-xs">
                            <div class="col-span-full text-slate-400 py-4 text-xs italic">
                                Klik tombol <strong>Preview</strong> di atas untuk melihat simulasi kalender perulangan pola shift.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

<!-- Master Jam Kerja Data for JS -->
<script>
    const MASTER_JAM_KERJA = @json($jamkerja);
</script>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    // Inisialisasi Flatpickr
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#previewStartDate", {
            locale: 'id',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd M Y',
            altInputClass: 'h-8 pl-7 pr-2.5 text-xs bg-white border border-slate-300 rounded-lg outline-none transition cursor-pointer font-medium text-slate-800 w-32',
            defaultDate: "{{ date('Y-m-01') }}",
            allowInput: true,
            onChange: function() {
                $('#previewStartDate').trigger('change');
            }
        });
    }

    const kodeInput = $('#kode_pola');
    const kodeCount = $('#kodePolaCount');

    kodeInput.on('input', function(e) {
        let val = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (val.length > 5) val = val.substring(0, 5);
        $(this).val(val);
        kodeCount.text(`${val.length}/5`);
    });

    const indoDayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const indoMonthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    function renderDays(totalDays) {
        totalDays = parseInt(totalDays) || 1;
        $('#cycleBadge').text(totalDays + ' Hari Siklus');
        let container = $('#patternTimelineContainer');
        container.empty();

        let anchorVal = $('#previewStartDate').val();
        let baseDate = anchorVal ? new Date(anchorVal + 'T00:00:00') : new Date();

        for (let i = 1; i <= totalDays; i++) {
            let curDate = new Date(baseDate.getTime());
            curDate.setDate(curDate.getDate() + (i - 1));

            let dayName = indoDayNames[curDate.getDay()];
            let dateFormatted = `${curDate.getDate()} ${indoMonthNames[curDate.getMonth()]}`;
            let isSunday = (curDate.getDay() === 0);

            let optionsHtml = '<option value="" data-color="#64748b" data-jam="Libur / Istirahat">-- LIBUR / OFF --</option>';
            MASTER_JAM_KERJA.forEach(function(jk) {
                let timeStr = `${jk.jam_masuk.substring(0,5)} - ${jk.jam_pulang.substring(0,5)}`;
                optionsHtml += `<option value="${jk.kode_jam_kerja}" data-color="${jk.color || '#0d6efd'}" data-jam="${timeStr}">${jk.nama_jam_kerja} (${timeStr})</option>`;
            });

            let cardBorder = isSunday ? 'border-rose-300' : 'border-slate-200';
            let sundayBadge = isSunday ? `<span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 font-bold text-[9px] ml-1">Minggu</span>` : '';

            let cardHtml = `
                <div class="day-card" data-day="${i}">
                    <div class="p-3 bg-white rounded-xl border ${cardBorder} shadow-2xs hover:shadow-xs transition flex flex-col justify-between h-full" id="card_day_${i}">
                        
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs shrink-0 transition-colors" id="avatar_${i}" 
                                     style="background: ${isSunday ? '#fee2e2' : '#f1f5f9'}; color: ${isSunday ? '#dc2626' : '#475569'};">
                                    ${i}
                                </div>
                                <div>
                                    <div class="font-black text-slate-800 text-xs flex items-center">
                                        Hari Ke-${i}
                                        ${sundayBadge}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-medium flex items-center gap-0.5">
                                        <i class="ti ti-calendar text-[11px]"></i>
                                        <span>${dayName}, ${dateFormatted}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold transition-colors" id="pill_${i}" 
                                  style="background-color: #f1f5f9; color: #64748b;">
                                OFF
                            </span>
                        </div>

                        <div class="space-y-1 mt-1">
                            <input type="hidden" name="details[${i-1}][urutan_hari]" value="${i}">
                            <div class="relative">
                                <select name="details[${i-1}][kode_jam_kerja]" 
                                        class="w-full h-8 px-2.5 text-xs bg-slate-50 border border-slate-300 rounded-lg select-jam-kerja outline-none transition cursor-pointer font-medium text-slate-800" 
                                        data-day="${i}">
                                    ${optionsHtml}
                                </select>
                            </div>
                            <div class="text-[10px] text-slate-400 truncate mt-0.5" id="time_info_${i}">Libur / Istirahat</div>
                        </div>

                    </div>
                </div>
            `;
            container.append(cardHtml);
        }
    }

    // Event saat previewStartDate diubah
    $('#previewStartDate').on('change', function() {
        let currentSelections = {};
        $('.select-jam-kerja').each(function() {
            let day = $(this).data('day');
            currentSelections[day] = $(this).val();
        });

        renderDays($('#jumlah_hari_siklus').val());

        Object.keys(currentSelections).forEach(function(day) {
            if (currentSelections[day]) {
                let sel = $(`.select-jam-kerja[data-day="${day}"]`);
                sel.val(currentSelections[day]).trigger('change');
            }
        });
    });

    // Init with default
    renderDays($('#jumlah_hari_siklus').val());

    // When cycle days change
    $('#jumlah_hari_siklus').on('input change', function() {
        let val = $(this).val();
        if (val >= 2 && val <= 31) {
            renderDays(val);
        }
    });

    // Handle shift select change (color & UI update)
    $(document).on('change', '.select-jam-kerja', function() {
        let day = $(this).data('day');
        let selected = $(this).find('option:selected');
        let val = selected.val();
        let color = selected.data('color') || '#0d6efd';
        let jam = selected.data('jam') || 'Libur / Istirahat';
        let text = selected.text().split('(')[0].trim();

        let pill = $(`#pill_${day}`);
        let avatar = $(`#avatar_${day}`);
        let timeInfo = $(`#time_info_${day}`);

        timeInfo.text(jam);

        if (val === "") {
            avatar.css({'background': '#f1f5f9', 'color': '#475569'});
            pill.text('OFF').css({'background-color': '#f1f5f9', 'color': '#64748b'});
        } else {
            avatar.css({'background': color, 'color': '#ffffff'});
            pill.text(text).css({'background-color': color, 'color': '#ffffff'});
        }
    });

    // Presets Click
    $('.btn-preset').click(function() {
        let days = $(this).data('days');
        let name = $(this).data('name');
        let code = $(this).data('code');

        $('#jumlah_hari_siklus').val(days);
        $('#nama_pola').val(name);
        $('#kode_pola').val(code);
        kodeCount.text(`${code.length}/5`);

        clearError($('#kode_pola'));
        clearError($('#nama_pola'));
        clearError($('#jumlah_hari_siklus'));

        renderDays(days);
    });

    // Preview Simulation
    $('#btnRunPreview').click(function() {
        let totalDays = parseInt($('#jumlah_hari_siklus').val()) || 1;
        let startDate = new Date($('#previewStartDate').val() + 'T00:00:00');
        let bulan = parseInt($('#previewBulan').val());
        let tahun = parseInt($('#previewTahun').val());

        let daysInMonth = new Date(tahun, bulan, 0).getDate();
        let grid = $('#previewCalendarGrid');
        grid.empty();

        let patternMap = {};
        $('.select-jam-kerja').each(function() {
            let day = $(this).data('day');
            let opt = $(this).find('option:selected');
            patternMap[day] = {
                val: opt.val(),
                nama: opt.val() ? opt.text().split('(')[0].trim() : 'OFF',
                color: opt.val() ? (opt.data('color') || '#007bff') : '#64748b'
            };
        });

        let dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for (let d = 1; d <= daysInMonth; d++) {
            let currDate = new Date(tahun, bulan - 1, d);
            let diffTime = currDate.getTime() - startDate.getTime();
            let diffDays = Math.floor(diffTime / (1000 * 3600 * 24));
            let dayIndex = (((diffDays % totalDays) + totalDays) % totalDays) + 1;

            let shift = patternMap[dayIndex] || { val: '', nama: 'OFF', color: '#64748b' };
            let dayName = dayNames[currDate.getDay()];
            let isSunday = currDate.getDay() === 0;

            let colHtml = `
                <div class="p-1.5 rounded-xl border ${isSunday ? 'bg-rose-50 border-rose-200' : 'bg-white border-slate-200'} shadow-2xs">
                    <div class="font-bold text-[11px] ${isSunday ? 'text-rose-700' : 'text-slate-800'}">${d} ${dayName}</div>
                    <div class="mt-1 py-0.5 px-1 rounded text-[9.5px] font-bold text-white truncate shadow-2xs" style="background-color: ${shift.color};">
                        H${dayIndex}: ${shift.nama}
                    </div>
                </div>
            `;
            grid.append(colHtml);
        }
    });

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

        if (name === 'kode_pola') {
            if (!val) {
                showError($el, 'Kode Pola wajib diisi');
                return false;
            }
        }

        if (name === 'nama_pola') {
            if (!val) {
                showError($el, 'Nama Pola wajib diisi');
                return false;
            }
        }

        if (name === 'jumlah_hari_siklus') {
            const num = parseInt(val);
            if (!val || isNaN(num) || num < 2 || num > 31) {
                showError($el, 'Jumlah hari siklus harus antara 2 s/d 31 hari');
                return false;
            }
        }

        clearError($el);
        return true;
    }

    $('#formShiftPatternV2').on('input change blur', 'input, select, textarea', function(e) {
        validateSingleField(this);
    });

    $('#formShiftPatternV2').on('submit', function(e) {
        let isValid = true;
        let firstInvalidEl = null;

        ['#kode_pola', '#nama_pola', '#jumlah_hari_siklus'].forEach(function(selector) {
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

            const $btn = $('#btnSubmitShiftPatternV2');
            $btn.prop('disabled', false).html('<i class="ti ti-device-floppy text-sm"></i><span>Simpan Pola</span>');
            return false;
        }

        const $btn = $('#btnSubmitShiftPatternV2');
        $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
    });
});
</script>
@endpush
