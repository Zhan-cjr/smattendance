@extends('v2.layouts.app')

@section('titlepage', 'Jam Kerja Departemen')
@section('page_title', 'Jam Kerja Departemen')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalData = $jamkerjabydept->total();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-clock-cog text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Jam Kerja Departemen</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Manajemen pengaturan pola jam kerja per departemen dan unit cabang operasional
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
                <span class="font-bold text-slate-800">Jam Kerja Dept</span>
            </nav>

            @can('jamkerjabydept.create')
                <button type="button" id="btnCreate"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Jam Kerja Dept</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- ================= 2. FILTER TOOLBAR (FULL WIDTH RESPONSIVE) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('jamkerjabydept.index') }}" method="GET" class="w-full">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 sm:gap-3 w-full items-center">
                
                <!-- Filter Cabang (Spans full width responsively) -->
                <div class="sm:col-span-9 lg:col-span-10 relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-building text-base"></i>
                    </span>
                    <select name="kode_cabang" id="kode_cabang" 
                            class="w-full h-10 pl-10 pr-9 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                        <option value="">Semua Cabang / Unit Kerja</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ textUpperCase($c->nama_cabang) }} ({{ $c->kode_cabang }})
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-chevron-down text-xs"></i>
                    </span>
                </div>

                <!-- Action Buttons: Cari & Reset -->
                <div class="sm:col-span-3 lg:col-span-2 flex items-center gap-2 w-full justify-end">
                    <button type="submit" 
                            class="flex-1 h-10 px-4 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->filled('kode_cabang'))
                        <a href="{{ route('jamkerjabydept.index') }}" 
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
                    <i class="ti ti-layout-grid"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Data Jam Kerja Departemen</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $totalData }} Jadwal Dept
            </span>
        </div>

        <!-- Responsive Table Container with Matching Solid Header -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-14 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 w-44 text-white/90 border-0 border-t-0">Kode JK Dept</th>
                        <th class="py-2.5 px-4 w-48 text-white/90 border-0 border-t-0">Cabang</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Departemen</th>
                        <th class="py-2.5 px-3.5 text-center w-28 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($jamkerjabydept as $d)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- No Index -->
                            <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
                                <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration + $jamkerjabydept->firstItem() - 1 }}
                                </span>
                            </td>

                            <!-- Kode JK Dept -->
                            <td class="py-2.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-black bg-slate-100 text-slate-800 border border-slate-200 group-hover:bg-emerald-50 group-hover:text-emerald-800 group-hover:border-emerald-200 transition">
                                    {{ $d->kode_jk_dept }}
                                </span>
                            </td>

                            <!-- Cabang -->
                            <td class="py-2.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-md bg-sky-50 text-sky-700 border border-sky-100 flex items-center justify-center text-xs shrink-0">
                                        <i class="ti ti-building"></i>
                                    </span>
                                    <span class="font-bold text-slate-800 uppercase tracking-tight">
                                        {{ $d->nama_cabang }}
                                    </span>
                                </div>
                            </td>

                            <!-- Departemen -->
                            <td class="py-2.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center justify-center text-xs shrink-0">
                                        <i class="ti ti-hierarchy-2"></i>
                                    </span>
                                    <span class="font-semibold text-slate-800 uppercase tracking-tight">
                                        {{ $d->nama_dept }}
                                    </span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="inline-flex items-center justify-center gap-1">
                                    @can('jamkerjabydept.edit')
                                        <button type="button" 
                                                class="btnEdit w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/80 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer"
                                                kode_jk_dept="{{ Crypt::encrypt($d->kode_jk_dept) }}"
                                                title="Edit Konfigurasi Jam Kerja">
                                            <i class="ti ti-edit text-sm"></i>
                                        </button>
                                    @endcan

                                    @can('jamkerjabydept.delete')
                                        <form method="POST" action="{{ route('jamkerjabydept.delete', Crypt::encrypt($d->kode_jk_dept)) }}" class="deleteform inline-block m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btnDelete w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/80 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer"
                                                    title="Hapus Data">
                                                <i class="ti ti-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                                        <i class="ti ti-clock-off text-2xl"></i>
                                    </div>
                                    <span class="font-bold text-slate-700 text-sm">Belum ada data jam kerja departemen</span>
                                    <p class="text-xs text-slate-400 max-w-sm">
                                        Silakan klik tombol "Tambah Jam Kerja Dept" untuk mengatur jadwal jam kerja harian tiap departemen.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Card Footer / Pagination -->
        @if($jamkerjabydept->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $jamkerjabydept->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

</div>

<!-- Universal V2 Modal for Jam Kerja Departemen -->
<div class="modal fade" id="modalV2JamKerjaDept" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between text-white border-0"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs shrink-0">
                        <i class="ti ti-clock-cog"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm font-extrabold text-white tracking-tight truncate mb-0" id="modalV2Title">Form Jam Kerja Departemen</h5>
                    </div>
                </div>
                <button type="button" class="text-white/80 hover:text-white transition p-1 rounded-lg hover:bg-white/10 cursor-pointer" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6 bg-white" id="loadmodalV2JamKerjaDept">
                <!-- Dynamic Content Loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        const themeColor1 = "{{ $themeColor1 }}";
        const modalEl = document.getElementById('modalV2JamKerjaDept');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoadingSpinner() {
            $('#loadmodalV2JamKerjaDept').html(`
                <div class="flex flex-col items-center justify-center py-12 text-slate-400 gap-3">
                    <i class="ti ti-loader animate-spin text-3xl" style="color: ${themeColor1};"></i>
                    <span class="text-xs font-semibold tracking-wide">Memuat formulir...</span>
                </div>
            `);
        }

        // Tambah Jam Kerja Dept
        $("#btnCreate").on("click", function(e) {
            e.preventDefault();
            $("#modalV2Title").text("Tambah Jam Kerja Departemen");
            modalInstance.show();
            showLoadingSpinner();
            $("#loadmodalV2JamKerjaDept").load("{{ route('jamkerjabydept.create') }}");
        });

        // Edit Jam Kerja Dept
        $(document).on("click", ".btnEdit", function(e) {
            e.preventDefault();
            var kode_jk_dept = $(this).attr("kode_jk_dept");
            $("#modalV2Title").text("Edit Jam Kerja Departemen");
            modalInstance.show();
            showLoadingSpinner();
            $("#loadmodalV2JamKerjaDept").load('/jamkerjabydept/' + kode_jk_dept + '/edit');
        });

        // SweetAlert2 Confirmation for Delete
        $(document).on("submit", ".deleteform", function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Hapus Jam Kerja?',
                text: "Apakah Anda yakin ingin menghapus konfigurasi jam kerja departemen ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-slate-100',
                    confirmButton: 'px-4 py-2 text-xs font-bold rounded-xl shadow-xs',
                    cancelButton: 'px-4 py-2 text-xs font-bold rounded-xl shadow-xs'
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
