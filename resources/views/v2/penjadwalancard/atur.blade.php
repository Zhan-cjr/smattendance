@extends('v2.layouts.app')

@section('titlepage', 'Atur Shift Card')
@section('page_title', 'Atur Pembagian Shift Card')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $tglDari = \Carbon\Carbon::parse($jadwalcard->dari)->translatedFormat('d F Y');
    $tglSampai = \Carbon\Carbon::parse($jadwalcard->sampai)->translatedFormat('d F Y');
@endphp

<div class="space-y-4">

    <!-- ================= 1. TOP HEADER & BREADCRUMB ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-1">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('penjadwalancard.index') }}" 
                   class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition active:scale-95 shadow-2xs">
                    <i class="ti ti-arrow-left text-sm"></i>
                </a>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                    {{ $jadwalcard->nama_jadwal }}
                </h1>
            </div>
            <div class="text-xs text-slate-500 font-medium mt-1 flex items-center flex-wrap gap-2">
                <span class="flex items-center gap-1 text-emerald-700 font-semibold">
                    <i class="ti ti-calendar-time"></i> {{ $tglDari }} s/d {{ $tglSampai }}
                </span>
                <span class="text-slate-300">•</span>
                <span>{{ $jadwalcard->departemen->nama_dept ?? 'Semua Dept' }}</span>
                <span class="text-slate-300">•</span>
                <span>{{ $jadwalcard->cabang->nama_cabang ?? 'Semua Cabang' }}</span>
            </div>
        </div>

        <!-- Right Side: Breadcrumbs & Refresh Button -->
        <div class="flex items-center gap-2">
            <button type="button" 
                    id="btnReloadData" 
                    class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs active:scale-95 cursor-pointer">
                <i class="ti ti-refresh text-emerald-600"></i>
                <span>Refresh Board</span>
            </button>
            <a href="{{ route('penjadwalancard.index') }}" 
               class="px-3.5 py-2 rounded-xl text-white text-xs font-bold transition flex items-center gap-1.5 shadow-xs hover:opacity-95 active:scale-95 cursor-pointer"
               style="background-color: {{ $themeColor1 }};">
                <i class="ti ti-check"></i>
                <span>Selesai</span>
            </a>
        </div>
    </div>

    <!-- ================= 2. SEARCH BAR & STATS SUMMARY ================= -->
    <div class="bg-white rounded-2xl p-3.5 border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Global Search Input -->
        <div class="relative w-full sm:w-80">
            <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
            <input type="text" 
                   id="globalSearchInput" 
                   placeholder="Cari karyawan di semua kolom..." 
                   autocomplete="off"
                   class="w-full pl-10 pr-9 py-2 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300 rounded-xl text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
            <button class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden text-xs" type="button" id="btnClearGlobalSearch">
                <i class="ti ti-x"></i>
            </button>
        </div>

        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium self-start sm:self-auto">
            <i class="ti ti-info-circle text-emerald-600"></i>
            <span>Tarik (drag) kartu karyawan antar kolom shift atau gunakan fitur centang di kolom kiri.</span>
        </div>
    </div>

    <!-- ================= 3. KANBAN CARD BOARD CONTAINER ================= -->
    <div class="kanban-layout-v2 flex flex-col lg:flex-row items-start gap-4" id="kanbanBoard">
        
        <!-- PANEL KIRI: BELUM DIJADWALKAN (UNASSIGNED) -->
        <div class="w-full lg:w-80 lg:shrink-0">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col">
                <!-- Column Header -->
                <div class="px-4 py-3.5 bg-slate-800 text-white flex items-center justify-between border-b border-slate-700">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-white/20 flex items-center justify-center text-xs">
                            <i class="ti ti-user-x"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black tracking-tight text-white">Belum Terjadwal</h3>
                            <p class="text-[10px] text-slate-300 leading-none mt-0.5">Karyawan tanpa shift</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-black bg-white text-slate-800 shadow-2xs" id="count-unassigned">0</span>
                </div>

                <!-- Column Filter & Bulk Action -->
                <div class="p-2.5 bg-slate-50 border-b border-slate-200 space-y-2">
                    <div class="relative">
                        <i class="ti ti-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="text" 
                               class="w-full pl-8 pr-3 py-1.5 text-[11px] bg-white border border-slate-300 rounded-lg text-slate-800 font-medium focus:outline-none focus:border-emerald-600 transition" 
                               placeholder="Filter kolom ini..." 
                               oninput="filterColumn(this, 'unassigned')">
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-0.5">
                        <label class="flex items-center gap-1.5 text-xs text-slate-600 font-bold cursor-pointer select-none">
                            <input class="w-3.5 h-3.5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer" type="checkbox" id="checkAllUnassigned">
                            <span class="text-[11px]">Pilih Semua</span>
                        </label>

                        <!-- Dropdown Bulk Move -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    type="button" 
                                    class="px-2.5 py-1 text-[11px] font-bold rounded-lg text-white shadow-2xs flex items-center gap-1 transition cursor-pointer hover:opacity-95"
                                    style="background-color: {{ $themeColor1 }};">
                                <span>Pindah Ke</span>
                                <i class="ti ti-chevron-down text-[10px]"></i>
                            </button>

                            <div x-show="open" 
                                 @click.away="open = false" 
                                 x-transition 
                                 class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-xl border border-slate-200 py-1 z-30">
                                <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    Pilih Shift Tujuan:
                                </div>
                                @foreach ($jamkerja as $jk)
                                    @php
                                        $jkColColor = $jk->color ?: '#059669';
                                    @endphp
                                    <a href="javascript:void(0)" 
                                       @click="open = false"
                                       class="bulk-move-btn flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-slate-50 font-medium transition" 
                                       data-kode-jk="{{ $jk->kode_jam_kerja }}">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $jkColColor }};"></span>
                                        <span class="truncate">{{ $jk->nama_jam_kerja }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dropzone Area -->
                <div class="p-2.5 overflow-y-auto max-h-[70vh] min-h-[160px] dropzone-area space-y-2 bg-slate-50/50" 
                     id="zone-unassigned" 
                     data-kode-jk="unassigned">
                    <!-- Loaded via AJAX -->
                </div>
            </div>
        </div>

        <!-- PANEL KANAN: GRID SHIFT JAM KERJA -->
        <div class="flex-1 min-w-0 w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($jamkerja as $jk)
                    @php
                        $colColor = !empty($jk->color) ? $jk->color : '#059669';
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col">
                        <!-- Shift Column Header -->
                        <div class="px-4 py-3 text-white flex items-center justify-between border-0 shadow-2xs"
                             style="background-color: {{ $colColor }};">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 rounded-md bg-white/20 flex items-center justify-center text-xs shrink-0 font-bold">
                                    <i class="ti ti-clock-hour-4"></i>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-xs font-black tracking-tight text-white truncate">{{ $jk->nama_jam_kerja }}</h3>
                                    <p class="text-[10px] text-white/80 font-mono leading-none mt-0.5 truncate">
                                        {{ date('H:i', strtotime($jk->jam_masuk)) }} - {{ date('H:i', strtotime($jk->jam_pulang)) }} • {{ $jk->kode_jam_kerja }}
                                    </p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-black bg-white text-slate-900 shadow-2xs shrink-0" 
                                  id="count-{{ $jk->kode_jam_kerja }}">0</span>
                        </div>

                        <!-- Search in Column -->
                        <div class="p-2 bg-slate-50 border-b border-slate-200">
                            <div class="relative">
                                <i class="ti ti-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                                <input type="text" 
                                       class="w-full pl-8 pr-3 py-1.5 text-[11px] bg-white border border-slate-300 rounded-lg text-slate-800 font-medium focus:outline-none focus:border-emerald-600 transition" 
                                       placeholder="Cari di shift ini..." 
                                       oninput="filterColumn(this, '{{ $jk->kode_jam_kerja }}')">
                            </div>
                        </div>

                        <!-- Dropzone Area -->
                        <div class="p-2.5 overflow-y-auto max-h-[62vh] min-h-[160px] dropzone-area space-y-2 flex-1 bg-slate-50/50" 
                             id="zone-{{ $jk->kode_jam_kerja }}" 
                             data-kode-jk="{{ $jk->kode_jam_kerja }}">
                            <!-- Loaded via AJAX -->
                        </div>

                        <!-- Bottom Action: Kosongkan Shift -->
                        <div class="p-2 bg-slate-50 border-t border-slate-200 text-center">
                            <button type="button" 
                                    class="clear-zone-btn w-full py-1.5 px-3 rounded-lg border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold text-[11px] transition flex items-center justify-center gap-1.5 cursor-pointer active:scale-95" 
                                    data-kode-jk="{{ $jk->kode_jam_kerja }}">
                                <i class="ti ti-arrow-back-up text-xs"></i>
                                <span>Kosongkan {{ $jk->nama_jam_kerja }}</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

<style>
.karyawan-card-v2 {
    background: #ffffff;
    border-radius: 12px;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    cursor: grab;
    transition: all 0.15s ease-in-out;
}
.karyawan-card-v2:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    transform: translateY(-1px);
}
.karyawan-card-v2.sortable-ghost {
    opacity: 0.35;
    background: #f0fdf4;
    border: 2px dashed #059669;
}
.karyawan-card-v2.sortable-chosen {
    cursor: grabbing;
}
</style>

@endsection

@push('myscript')
<script src="{{ asset('assets/vendor/libs/sortablejs/sortable.js') }}"></script>
<script>
    const jadwalCardId = "{{ $jadwalcard->id }}";
    let sortableList = [];

    function renderCardItem(item, isUnassigned = false) {
        const avatar = item.foto 
            ? `<img src="${item.foto}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shrink-0" alt="${item.nama_karyawan}">`
            : `<div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-black shrink-0">${(item.nama_karyawan || 'U').charAt(0).toUpperCase()}</div>`;

        return `
            <div class="karyawan-card-v2" data-nik="${item.nik}" data-name="${(item.nama_karyawan || '').toLowerCase()}" data-dept="${(item.nama_dept || '').toLowerCase()}">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0 flex-1">
                        ${isUnassigned ? `<input type="checkbox" class="card-check-nik w-3.5 h-3.5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer shrink-0" value="${item.nik}">` : ''}
                        ${avatar}
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-slate-800 text-xs truncate card-emp-name leading-tight" title="${item.nama_karyawan}">
                                ${item.nama_karyawan}
                            </div>
                            <div class="text-[10px] text-slate-500 font-mono truncate mt-0.5">
                                <span class="card-emp-nik">${item.nik}</span> <span class="text-slate-300">•</span> <span class="px-1 py-0.2 rounded bg-slate-100 font-sans text-slate-600 card-emp-dept">${item.nama_dept}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-slate-400 hover:text-slate-600 cursor-grab shrink-0">
                        <i class="ti ti-grip-vertical text-sm"></i>
                    </div>
                </div>
            </div>
        `;
    }

    function updateBadgeCounters() {
        const unassignedCount = $('#zone-unassigned .karyawan-card-v2:visible').length;
        $('#count-unassigned').text(unassignedCount);

        $('.dropzone-area').each(function() {
            const kodeJk = $(this).data('kode-jk');
            if (kodeJk !== 'unassigned') {
                const count = $(this).find('.karyawan-card-v2:visible').length;
                $(`#count-${kodeJk}`).text(count);
            }
        });
    }

    function filterColumn(input, zoneCode) {
        const query = ($(input).val() || '').trim().toLowerCase();
        $(`#zone-${zoneCode} .karyawan-card-v2`).each(function() {
            const name = ($(this).attr('data-name') || $(this).find('.card-emp-name').text() || '').toLowerCase();
            const nik = ($(this).attr('data-nik') || $(this).find('.card-emp-nik').text() || '').toLowerCase();
            const dept = ($(this).attr('data-dept') || $(this).find('.card-emp-dept').text() || '').toLowerCase();

            if (query === '' || name.indexOf(query) !== -1 || nik.indexOf(query) !== -1 || dept.indexOf(query) !== -1) {
                $(this).removeAttr('style');
            } else {
                $(this).attr('style', 'display: none !important');
            }
        });
        updateBadgeCounters();
    }

    function applyGlobalSearch(query) {
        query = (query || '').trim().toLowerCase();
        $('.karyawan-card-v2').each(function() {
            const name = ($(this).attr('data-name') || $(this).find('.card-emp-name').text() || '').toLowerCase();
            const nik = ($(this).attr('data-nik') || $(this).find('.card-emp-nik').text() || '').toLowerCase();
            const dept = ($(this).attr('data-dept') || $(this).find('.card-emp-dept').text() || '').toLowerCase();

            if (query === '' || name.indexOf(query) !== -1 || nik.indexOf(query) !== -1 || dept.indexOf(query) !== -1) {
                $(this).removeAttr('style');
            } else {
                $(this).attr('style', 'display: none !important');
            }
        });
        updateBadgeCounters();
    }

    function initKanbanSortables() {
        sortableList.forEach(inst => inst.destroy());
        sortableList = [];

        const columns = document.querySelectorAll('.dropzone-area');
        columns.forEach(col => {
            const sortable = new Sortable(col, {
                group: 'kanban-shift-group',
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                handle: '.ti-grip-vertical, .karyawan-card-v2',
                onEnd: function(evt) {
                    const nik = $(evt.item).data('nik');
                    const targetJk = $(evt.to).data('kode-jk');
                    const sourceJk = $(evt.from).data('kode-jk');

                    if (targetJk === sourceJk) return;

                    // Update UI checkbox
                    if (targetJk !== 'unassigned') {
                        $(evt.item).find('.card-check-nik').remove();
                    } else {
                        if ($(evt.item).find('.card-check-nik').length === 0) {
                            $(evt.item).find('.flex.items-center:first').prepend(
                                `<input type="checkbox" class="card-check-nik w-3.5 h-3.5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer shrink-0" value="${nik}">`
                            );
                        }
                    }

                    updateBadgeCounters();

                    // AJAX Move
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('penjadwalancard.move') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: jadwalCardId,
                            nik: nik,
                            kode_jam_kerja: targetJk
                        },
                        success: function(res) {
                            if (!res.success) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message || 'Gagal memindahkan shift'
                                });
                                loadCardData();
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan sistem saat memindahkan shift'
                            });
                            loadCardData();
                        }
                    });
                }
            });
            sortableList.push(sortable);
        });
    }

    function loadCardData() {
        $('.dropzone-area').html(`
            <div class="py-8 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-5 w-5 border-2 border-emerald-600 border-t-transparent"></div>
                <div class="text-[11px] text-slate-400 mt-2">Memuat data...</div>
            </div>
        `);

        $.ajax({
            type: 'POST',
            url: "{{ route('penjadwalancard.getData') }}",
            data: {
                _token: "{{ csrf_token() }}",
                id: jadwalCardId
            },
            success: function(res) {
                if (res.success) {
                    const data = res.data;

                    // 1. Render Unassigned
                    let unassignedHtml = '';
                    if (data.unassigned && data.unassigned.length > 0) {
                        data.unassigned.forEach(item => {
                            unassignedHtml += renderCardItem(item, true);
                        });
                    } else {
                        unassignedHtml = `<div class="py-6 text-center text-slate-400 text-xs italic">Semua karyawan sudah terjadwal</div>`;
                    }
                    $('#zone-unassigned').html(unassignedHtml);

                    // 2. Render Shifts
                    if (data.shifts) {
                        Object.keys(data.shifts).forEach(kodeJk => {
                            let shiftHtml = '';
                            const shiftItems = data.shifts[kodeJk];
                            if (shiftItems && shiftItems.length > 0) {
                                shiftItems.forEach(item => {
                                    shiftHtml += renderCardItem(item, false);
                                });
                            } else {
                                shiftHtml = `<div class="py-6 text-center text-slate-400 text-xs italic">Belum ada karyawan di shift ini</div>`;
                            }
                            $(`#zone-${kodeJk}`).html(shiftHtml);
                        });
                    }

                    updateBadgeCounters();
                    initKanbanSortables();
                    $('#checkAllUnassigned').prop('checked', false);
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON ? xhr.responseJSON.message : 'Gagal mengambil data jadwal'
                });
            }
        });
    }

    $(document).ready(function() {
        loadCardData();

        $('#btnReloadData').click(function() {
            loadCardData();
        });

        // Check All checkbox
        $('#checkAllUnassigned').change(function() {
            const isChecked = $(this).is(':checked');
            $('#zone-unassigned .card-check-nik:visible').prop('checked', isChecked);
        });

        // Bulk Move action
        $(document).on('click', '.bulk-move-btn', function() {
            const targetJk = $(this).data('kode-jk');
            let selectedNiks = [];

            $('#zone-unassigned .card-check-nik:checked').each(function() {
                selectedNiks.push($(this).val());
            });

            if (selectedNiks.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Karyawan',
                    text: 'Silakan centang minimal satu karyawan yang ingin dipindahkan.',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl'
                    }
                });
                return;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('penjadwalancard.move') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: jadwalCardId,
                    nik: selectedNiks,
                    kode_jam_kerja: targetJk
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: `${selectedNiks.length} karyawan berhasil dipindahkan.`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadCardData();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memindahkan jadwal karyawan' });
                }
            });
        });

        // Clear Zone action
        $('.clear-zone-btn').click(function() {
            const targetJk = $(this).data('kode-jk');
            let selectedNiks = [];

            $(`#zone-${targetJk} .karyawan-card-v2`).each(function() {
                selectedNiks.push($(this).data('nik'));
            });

            if (selectedNiks.length === 0) {
                Swal.fire({ icon: 'info', title: 'Info', text: 'Shift ini sudah kosong.' });
                return;
            }

            Swal.fire({
                title: 'Kosongkan Shift?',
                text: 'Semua karyawan di shift ini akan dikembalikan ke status Belum Terjadwal untuk periode ini.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl shadow-xl'
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('penjadwalancard.move') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: jadwalCardId,
                            nik: selectedNiks,
                            kode_jam_kerja: 'unassigned'
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Shift berhasil dikosongkan.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                loadCardData();
                            }
                        }
                    });
                }
            });
        });

        // Global Search listener
        $('#globalSearchInput').on('input keyup', function() {
            const val = $(this).val();
            if (val.length > 0) {
                $('#btnClearGlobalSearch').show();
            } else {
                $('#btnClearGlobalSearch').hide();
            }
            applyGlobalSearch(val);
        });

        $('#btnClearGlobalSearch').click(function() {
            $('#globalSearchInput').val('').trigger('input');
        });
    });
</script>
@endpush
