@extends('v2.layouts.app')

@section('titlepage', 'Gaji Pokok')
@section('page_title', 'Master Gaji Pokok')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalGaji = $gajipokok->total();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTIONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-cash text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Gaji Pokok</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola data besaran upah dasar (bulanan / harian) dan tanggal berlaku gaji pokok karyawan
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
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-database text-sm"></i>
                    <span>Data Master</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Gaji Pokok</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('gajipokok.create')
                    <!-- Import Excel -->
                    @if(\Illuminate\Support\Facades\Route::has('gajipokok.import'))
                    <button type="button" id="btnImportGajiPokok" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 text-emerald-700 hover:bg-emerald-50 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                        <i class="ti ti-file-import text-sm"></i>
                        <span>Import Excel</span>
                    </button>
                    @endif

                    <!-- Tambah Gaji Pokok -->
                    <button type="button" id="btnCreateGajiPokok" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Gaji Pokok</span>
                    </button>
                @endcan

                <!-- Tombol Hapus Multiple Terpilih -->
                <button type="button" id="btnDeleteMultiple" 
                        class="hidden inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                    <i class="ti ti-trash text-sm"></i>
                    <span>Hapus Terpilih (<span id="countSelected">0</span>)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER BAR STANDAR V2 ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('gajipokok.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-12 gap-2.5 sm:gap-3 items-center">
                
                <!-- Cari Nama Karyawan -->
                <div class="md:col-span-3 relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-search text-sm"></i>
                    </span>
                    <input type="text" name="nama_karyawan" id="filter_nama_karyawan" 
                           value="{{ request('nama_karyawan') }}" 
                           placeholder="Cari Nama Karyawan..." 
                           class="w-full pl-9 pr-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 placeholder-slate-400 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                </div>

                <!-- Filter Cabang -->
                <div class="md:col-span-3">
                    <select name="kode_cabang" id="filter_kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Departemen -->
                <div class="md:col-span-2">
                    <select name="kode_dept" id="filter_kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Tanggal Berlaku -->
                <div class="md:col-span-2 relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-sm"></i>
                    </span>
                    <input type="text" name="tanggal" id="filter_tanggal" 
                           value="{{ request('tanggal') }}" 
                           placeholder="Tanggal Berlaku" 
                           class="w-full pl-9 pr-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 placeholder-slate-400 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition flatpickr-filter"
                           autocomplete="off">
                </div>

                <!-- Actions Button (Filter & Reset) -->
                <div class="md:col-span-2 flex items-center gap-1.5">
                    <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if (request()->hasAny(['nama_karyawan', 'kode_cabang', 'kode_dept', 'tanggal']))
                        <a href="{{ route('gajipokok.index') }}" 
                           class="inline-flex items-center justify-center p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <!-- Divider with Counter -->
        <div class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="text-slate-500 text-[11.5px] flex items-center gap-2">
                <span>Total <strong>{{ $totalGaji }}</strong> Data Gaji Pokok</span>
                @if ($gajipokok->hasPages())
                    <span class="text-slate-300">|</span>
                    <span>Halaman <strong>{{ $gajipokok->currentPage() }}</strong> dari <strong>{{ $gajipokok->lastPage() }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <!-- ================= 3. TABEL DATA GAJI POKOK V2 ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        
        <!-- Table Top Banner Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-cash"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Gaji Pokok Karyawan</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $totalGaji }} data ditemukan
            </span>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse">
                <!-- Table Header -->
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15 whitespace-nowrap"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3 text-center w-10 text-white/90 border-0 border-t-0">
                            <input type="checkbox" id="checkAllRows" class="w-4 h-4 text-emerald-600 rounded border-white/40 focus:ring-emerald-500 cursor-pointer">
                        </th>
                        <th class="py-2.5 px-2.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-3 w-28 text-white/90 border-0 border-t-0">Kode Gaji</th>
                        <th class="py-2.5 px-3 w-28 text-white/90 border-0 border-t-0">NIK</th>
                        <th class="py-2.5 px-3.5 text-white/90 border-0 border-t-0">Nama Karyawan</th>
                        <th class="py-2.5 px-3 w-24 text-white/90 border-0 border-t-0">Cabang</th>
                        <th class="py-2.5 px-3 w-24 text-white/90 border-0 border-t-0">Departemen</th>
                        <th class="py-2.5 px-3 text-center w-28 text-white/90 border-0 border-t-0">Jenis Upah</th>
                        <th class="py-2.5 px-3.5 text-right w-36 text-white/90 border-0 border-t-0">Gaji Pokok</th>
                        <th class="py-2.5 px-3 text-center w-32 text-white/90 border-0 border-t-0">Tanggal Berlaku</th>
                        <th class="py-2.5 px-3 text-center w-24 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white whitespace-nowrap">
                    @forelse ($gajipokok as $item)
                        @php
                            $isHarian = ($item->jenis_upah == 'Harian');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- Checkbox Item -->
                            <td class="py-2 px-3 text-center">
                                <input type="checkbox" name="selected_gaji[]" value="{{ $item->kode_gaji }}" 
                                       class="checkItemRow w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                            </td>

                            <!-- Nomor Baris -->
                            <td class="py-2 px-2.5 text-center text-slate-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration + ($gajipokok->currentPage() - 1) * $gajipokok->perPage() }}
                                </span>
                            </td>

                            <!-- Kode Gaji -->
                            <td class="py-2 px-3">
                                <span class="inline-flex items-center font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/80 text-[11.5px]">
                                    {{ $item->kode_gaji }}
                                </span>
                            </td>

                            <!-- NIK -->
                            <td class="py-2 px-3">
                                <span class="text-xs font-semibold text-slate-600 font-mono">
                                    {{ $item->nik_show ?? $item->nik }}
                                </span>
                            </td>

                            <!-- Nama Karyawan -->
                            <td class="py-2 px-3.5">
                                <span class="font-bold text-slate-900 group-hover:text-emerald-700 transition text-xs sm:text-sm">
                                    {{ $item->nama_karyawan }}
                                </span>
                            </td>

                            <!-- Cabang -->
                            <td class="py-2 px-3">
                                @if (!empty($item->kode_cabang))
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/60">
                                        <i class="ti ti-map-pin text-[11px] text-slate-400"></i>
                                        <span>{{ $item->kode_cabang }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>

                            <!-- Departemen -->
                            <td class="py-2 px-3">
                                @if (!empty($item->kode_dept))
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                        <i class="ti ti-building text-[11px]"></i>
                                        <span>{{ $item->kode_dept }}</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>

                            <!-- Jenis Upah -->
                            <td class="py-2 px-3 text-center">
                                @if ($isHarian)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                        <span>Harian</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Bulanan</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Gaji Pokok -->
                            <td class="py-2 px-3.5 text-right">
                                <span class="font-bold text-emerald-700 font-mono text-xs sm:text-sm">
                                    Rp {{ formatAngka($item->jumlah) }}
                                </span>
                            </td>

                            <!-- Tanggal Berlaku -->
                            <td class="py-2 px-3 text-center text-xs text-slate-600 font-medium">
                                <div class="inline-flex items-center gap-1 bg-slate-50 px-2 py-0.5 rounded-md border border-slate-200/70">
                                    <i class="ti ti-calendar text-slate-400 text-xs"></i>
                                    <span>{{ date('d-m-Y', strtotime($item->tanggal_berlaku)) }}</span>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-2 px-3 text-center">
                                <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                    @can('gajipokok.edit')
                                        <button type="button" 
                                                class="w-6.5 h-6.5 rounded-md hover:bg-white text-emerald-600 hover:text-emerald-700 flex items-center justify-center transition-all cursor-pointer btnEditGajiPokok" 
                                                data-kodegaji="{{ Crypt::encrypt($item->kode_gaji) }}"
                                                title="Edit Gaji Pokok">
                                            <i class="ti ti-edit text-xs"></i>
                                        </button>
                                    @endcan

                                    @can('gajipokok.delete')
                                        <form action="{{ route('gajipokok.delete', Crypt::encrypt($item->kode_gaji)) }}" method="POST" class="inline m-0 formDeleteGajiPokok">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-6.5 h-6.5 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition-all cursor-pointer" 
                                                    title="Hapus Data">
                                                <i class="ti ti-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-10 px-4 text-slate-400">
                                <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg mb-2">
                                    <i class="ti ti-cash-off"></i>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800">Tidak ada data gaji pokok ditemukan</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5 max-w-sm mx-auto">
                                    Belum ada data gaji pokok karyawan yang tersimpan atau sesuai dengan filter pencarian saat ini.
                                </p>
                                @can('gajipokok.create')
                                    <button type="button" id="btnEmptyCreateGajiPokok" 
                                            class="mt-3 inline-flex items-center gap-1.5 px-3.5 py-1.5 text-white font-bold rounded-xl text-xs shadow-xs transition active:scale-95 cursor-pointer"
                                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                        <i class="ti ti-plus text-xs"></i>
                                        <span>Tambah Gaji Pokok</span>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination -->
        @if ($gajipokok->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $gajipokok->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

</div>

<!-- Form Batch Delete Hidden -->
<form id="formBulkDeleteGajiPokok" action="{{ \Illuminate\Support\Facades\Route::has('gajipokok.delete_multiple') ? route('gajipokok.delete_multiple') : '#' }}" method="POST" class="hidden">
    @csrf
    <div id="bulkDeleteInputs"></div>
</form>

<!-- ================= MODAL UNIVERSAL GAJI POKOK V2 ================= -->
<div id="modalGajiPokok" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-cash text-lg" id="modalIcon"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="modalTitle">
                        Gaji Pokok
                    </h3>
                    <p class="text-[11px] text-slate-500" id="modalSubtitle">
                        Formulir data besaran upah pokok karyawan
                    </p>
                </div>
            </div>
            <button type="button" id="btnCloseModal" 
                    class="w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body Content -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalContent">
            <div class="py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                <p class="text-xs font-semibold">Memuat formulir...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        // 1. Inisialisasi Flatpickr Filter
        if (typeof flatpickr !== 'undefined') {
            $('.flatpickr-filter').flatpickr({
                dateFormat: 'Y-m-d',
                allowInput: true
            });
        }

        // 2. Modal Helper
        const $modal = $('#modalGajiPokok');
        const $modalTitle = $('#modalTitle');
        const $modalSubtitle = $('#modalSubtitle');
        const $modalIcon = $('#modalIcon');
        const $modalContent = $('#modalContent');

        function openModal(title, subtitle, icon) {
            $modalTitle.text(title);
            $modalSubtitle.text(subtitle);
            $modalIcon.attr('class', 'ti ' + (icon || 'ti-cash') + ' text-lg');
            $modalContent.html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
                </div>
            `);
            $modal.removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeModal() {
            $modal.addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        $('#btnCloseModal').on('click', closeModal);
        $modal.on('click', function(e) {
            if (e.target === this) closeModal();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$modal.hasClass('hidden')) {
                closeModal();
            }
        });

        // 3. Tambah Gaji Pokok
        $('#btnCreateGajiPokok, #btnEmptyCreateGajiPokok').on('click', function(e) {
            e.preventDefault();
            openModal('Tambah Gaji Pokok', 'Daftarkan upah pokok karyawan', 'ti-plus');

            $.ajax({
                url: "{{ route('gajipokok.create') }}",
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
                    initModalPlugins();
                },
                error: function(xhr) {
                    $modalContent.html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat form tambah.</p>
                            <p class="mt-1">${xhr.responseText || 'Terjadi kesalahan.'}</p>
                        </div>
                    `);
                }
            });
        });

        // 4. Import Excel Modal
        $('#btnImportGajiPokok').on('click', function(e) {
            e.preventDefault();
            openModal('Import Gaji Pokok', 'Unggah berkas Excel data gaji pokok', 'ti-file-import');

            $.ajax({
                url: "{{ \Illuminate\Support\Facades\Route::has('gajipokok.import') ? route('gajipokok.import') : '#' }}",
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
                },
                error: function(xhr) {
                    $modalContent.html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat form import.</p>
                            <p class="mt-1">${xhr.responseText || 'Terjadi kesalahan.'}</p>
                        </div>
                    `);
                }
            });
        });

        // 5. Edit Gaji Pokok
        $(document).on('click', '.btnEditGajiPokok', function(e) {
            e.preventDefault();
            const kodeGaji = $(this).data('kodegaji');
            openModal('Edit Gaji Pokok', 'Perbarui besaran upah dan tanggal berlaku', 'ti-edit');

            $.ajax({
                url: `{{ url('/gajipokok') }}/${kodeGaji}/edit`,
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
                    initModalPlugins();
                },
                error: function(xhr) {
                    $modalContent.html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat form edit.</p>
                            <p class="mt-1">${xhr.responseText || 'Data tidak ditemukan.'}</p>
                        </div>
                    `);
                }
            });
        });

        // 6. Delete Single (SweetAlert2)
        $(document).on('submit', '.formDeleteGajiPokok', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Gaji Pokok?',
                text: 'Catatan gaji pokok ini akan dihapus permanen dari sistem!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Data',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold text-xs px-4 py-2.5',
                    cancelButton: 'rounded-xl font-bold text-xs px-4 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // 7. Checkbox Multiple Selection & Bulk Delete
        function updateBulkSelection() {
            const checkedCount = $('.checkItemRow:checked').length;
            $('#countSelected').text(checkedCount);
            if (checkedCount > 0) {
                $('#btnDeleteMultiple').removeClass('hidden');
            } else {
                $('#btnDeleteMultiple').addClass('hidden');
            }
        }

        $('#checkAllRows').on('change', function() {
            $('.checkItemRow').prop('checked', $(this).is(':checked'));
            updateBulkSelection();
        });

        $(document).on('change', '.checkItemRow', function() {
            const totalItems = $('.checkItemRow').length;
            const checkedCount = $('.checkItemRow:checked').length;
            $('#checkAllRows').prop('checked', totalItems > 0 && totalItems === checkedCount);
            updateBulkSelection();
        });

        $('#btnDeleteMultiple').on('click', function(e) {
            e.preventDefault();
            const selected = [];
            $('.checkItemRow:checked').each(function() {
                selected.push($(this).val());
            });

            if (selected.length === 0) {
                Swal.fire({
                    title: 'Pilih Data',
                    text: 'Silakan centang minimal 1 data yang ingin dihapus.',
                    icon: 'info',
                    confirmButtonColor: '{{ $themeColor1 }}',
                    customClass: { popup: 'rounded-2xl' }
                });
                return;
            }

            Swal.fire({
                title: `Hapus ${selected.length} Data Terpilih?`,
                text: 'Data gaji pokok yang dipilih akan dihapus permanen secara massal!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold text-xs px-4 py-2.5',
                    cancelButton: 'rounded-xl font-bold text-xs px-4 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const $form = $('#formBulkDeleteGajiPokok');
                    const $container = $('#bulkDeleteInputs');
                    $container.empty();
                    selected.forEach(kode => {
                        $container.append(`<input type="hidden" name="kode_gaji[]" value="${kode}">`);
                    });
                    $form.submit();
                }
            });
        });

        // 8. Inisialisasi Plugin Form Modal
        function initModalPlugins() {
            if (typeof flatpickr !== 'undefined') {
                $('.modal-flatpickr-date').flatpickr({
                    dateFormat: 'Y-m-d',
                    allowInput: false
                });
            }

            if ($.fn.select2) {
                $('.select2GajiModal').select2({
                    placeholder: '-- Pilih Karyawan --',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modalGajiPokok')
                });

                $('.select2GajiModalEdit').select2({
                    width: '100%',
                    dropdownParent: $('#modalGajiPokok')
                });
            }

            if ($.fn.maskMoney) {
                $('.modal-money-input').maskMoney({
                    thousands: '.',
                    decimal: ',',
                    precision: 0,
                    allowZero: false
                });
            }
        }
    });
</script>
@endpush
