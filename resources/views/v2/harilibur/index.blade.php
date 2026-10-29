@extends('v2.layouts.app')

@section('titlepage', 'Hari Libur')
@section('page_title', 'Data Hari Libur')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalLibur = $harilibur->total();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-calendar-off text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Hari Libur</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola kalender hari libur nasional, cuti bersama, dan hari libur khusus cabang/perusahaan
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-database text-sm"></i>
                    <span>Master Data</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Hari Libur</span>
            </nav>

            @can('harilibur.create')
                <button type="button" id="btnCreate"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Hari Libur</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- ================= 2. FILTER TOOLBAR (FULL WIDTH RESPONSIVE) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('harilibur.index') }}" method="GET" class="w-full">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 sm:gap-3 w-full items-center">
                
                <!-- Dari Tanggal -->
                <div class="lg:col-span-3 relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           name="dari" 
                           value="{{ request('dari') }}" 
                           placeholder="Dari Tanggal..." 
                           class="w-full h-10 pl-10 pr-3.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition flatpickr-date">
                </div>

                <!-- Sampai Tanggal -->
                <div class="lg:col-span-3 relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           name="sampai" 
                           value="{{ request('sampai') }}" 
                           placeholder="Sampai Tanggal..." 
                           class="w-full h-10 pl-10 pr-3.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition flatpickr-date">
                </div>

                <!-- Cabang Filter -->
                @if ($user->hasRole(['super admin', 'gm administrasi', 'admin pusat']) || !$cabang->isEmpty())
                    <div class="lg:col-span-4 w-full">
                        <select name="kode_cabang" class="w-full h-10 px-3.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                            <option value="">Semua Cabang / Unit Bisnis</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                    {{ textUpperCase($c->nama_cabang) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="hidden lg:block lg:col-span-4"></div>
                @endif

                <!-- Buttons: Cari & Reset -->
                <div class="lg:col-span-2 flex items-center gap-2 w-full justify-end">
                    <button type="submit" 
                            class="flex-1 lg:flex-none h-10 px-4 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['dari', 'sampai', 'kode_cabang']))
                        <a href="{{ route('harilibur.index') }}" 
                           class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center justify-center border border-slate-200"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- ================= 3. SEAMLESS SOLID TABLE VIEW ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-calendar-off"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Jadwal Hari Libur</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $totalLibur }} Data
            </span>
        </div>

        <!-- Responsive Table Container with Matching Solid Header -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 w-36 text-white/90 border-0 border-t-0">Kode Libur</th>
                        <th class="py-2.5 px-4 w-44 text-white/90 border-0 border-t-0">Tanggal</th>
                        <th class="py-2.5 px-4 w-40 text-white/90 border-0 border-t-0">Cabang</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Keterangan</th>
                        <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($harilibur as $d)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- No Index -->
                            <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
                                <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration + $harilibur->firstItem() - 1 }}
                                </span>
                            </td>

                            <!-- Kode Libur -->
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/80 font-mono text-[11.5px]">
                                    <i class="ti ti-barcode text-slate-400"></i>
                                    {{ $d->kode_libur }}
                                </span>
                            </td>

                            <!-- Tanggal -->
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                    <i class="ti ti-calendar text-emerald-600"></i>
                                    {{ DateToIndo($d->tanggal) }}
                                </span>
                            </td>

                            <!-- Cabang -->
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-black uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200 shadow-2xs">
                                    {{ $d->nama_cabang }}
                                </span>
                            </td>

                            <!-- Keterangan -->
                            <td class="py-2.5 px-4 text-slate-600 text-xs">
                                <span class="font-semibold text-slate-800 block">{{ $d->keterangan }}</span>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                    @can('harilibur.edit')
                                        <button type="button" 
                                                class="w-7 h-7 rounded-md hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition cursor-pointer btnEdit" 
                                                kode_libur="{{ Crypt::encrypt($d->kode_libur) }}" 
                                                title="Edit Hari Libur">
                                            <i class="ti ti-pencil text-sm"></i>
                                        </button>
                                    @endcan

                                    @can('harilibur.setharilibur')
                                        <a href="{{ route('harilibur.aturharilibur', Crypt::encrypt($d->kode_libur)) }}" 
                                           class="w-7 h-7 rounded-md hover:bg-emerald-50 text-emerald-600 hover:text-emerald-700 flex items-center justify-center transition cursor-pointer"
                                           title="Atur Karyawan Libur">
                                            <i class="ti ti-users text-sm"></i>
                                        </a>
                                    @endcan

                                    @can('harilibur.delete')
                                        <form method="POST" class="delete-form inline m-0" action="{{ route('harilibur.delete', Crypt::encrypt($d->kode_libur)) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    class="w-7 h-7 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition cursor-pointer" 
                                                    title="Hapus Hari Libur">
                                                <i class="ti ti-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 px-4 text-slate-400 bg-white">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg mb-2">
                                    <i class="ti ti-calendar-off"></i>
                                </div>
                                <p class="font-bold text-slate-700 text-xs">Tidak ada data hari libur ditemukan</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Coba sesuaikan filter pencarian atau tambahkan hari libur baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-2">
        {{ $harilibur->links('v2.components.pagination') }}
    </div>

</div>

<!-- Universal V2 Modal for Hari Libur -->
<div class="modal fade" id="modalV2HariLibur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between text-white border-0"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs shrink-0">
                        <i class="ti ti-calendar-off"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm font-extrabold text-white tracking-tight truncate mb-0" id="modalV2Title">Form Hari Libur</h5>
                    </div>
                </div>
                <button type="button" class="text-white/80 hover:text-white transition p-1 rounded-lg hover:bg-white/10 cursor-pointer" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6 bg-white" id="loadmodalV2HariLibur">
                <!-- Dynamic form loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        const modalEl = document.getElementById('modalV2HariLibur');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoadingSpinner() {
            $('#loadmodalV2HariLibur').html(`
                <div class="py-10 flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mb-3"></div>
                    <p class="text-xs text-slate-500 font-medium">Memuat form data...</p>
                </div>
            `);
        }

        // Initialize flatpickr on page
        $(".flatpickr-date").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // 1. Tambah Hari Libur
        $('#btnCreate').click(function(e) {
            e.preventDefault();
            $('#modalV2Title').text('Tambah Hari Libur');
            $('#modalV2Subtitle').text('Tentukan tanggal, cabang, dan keterangan hari libur');
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2HariLibur').load("{{ route('harilibur.create') }}");
        });

        // 2. Edit Hari Libur
        $('.btnEdit').click(function(e) {
            e.preventDefault();
            const kode_libur = $(this).attr('kode_libur');
            $('#modalV2Title').text('Edit Hari Libur');
            $('#modalV2Subtitle').text('Perbarui tanggal, cabang, atau keterangan hari libur');
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2HariLibur').load('/harilibur/' + kode_libur + '/edit');
        });

        // 3. Delete Confirmation (SweetAlert2)
        $('.delete-form').submit(function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Hari Libur?',
                text: 'Jadwal hari libur dan relasi karyawan libur ini akan dihapus dari sistem.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
