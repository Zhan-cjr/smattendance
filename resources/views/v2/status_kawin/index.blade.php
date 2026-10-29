@extends('v2.layouts.app')

@section('titlepage', 'Status Kawin')
@section('page_title', 'Data Status Kawin')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalStatus = $statuskawin->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-heart text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Status Kawin</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola status perkawinan, tanggungan keluarga & klasifikasi PTKP pajak penghasilan karyawan
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
                    <span>Master Data</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Status Kawin</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('statuskawin.create')
                    <!-- Tambah Status Kawin -->
                    <button type="button" id="btnCreate" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Status</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER TOOLBAR ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs">
        <form method="GET" action="{{ route('statuskawin.index') }}" id="filterStatusKawinForm">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                
                <!-- Search Box -->
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" 
                           name="status_kawin" 
                           id="searchStatusKawin"
                           value="{{ request('status_kawin') }}" 
                           placeholder="Cari kode atau status perkawinan..." 
                           class="w-full h-10 pl-10 pr-10 text-xs sm:text-sm font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                    
                    @if(request('status_kawin'))
                        <a href="{{ route('statuskawin.index') }}" 
                           class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition flex items-center"
                           title="Reset pencarian">
                            <i class="ti ti-x text-sm"></i>
                        </a>
                    @endif
                </div>

                <!-- Right Side: Action Buttons -->
                <div class="flex items-center gap-2 justify-end">
                    <button type="submit" 
                            class="h-10 px-4 text-xs font-bold text-white rounded-xl shadow-2xs flex items-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>

                    @if(request('status_kawin'))
                        <a href="{{ route('statuskawin.index') }}" 
                           class="h-10 px-3 border border-slate-200 text-slate-600 hover:bg-slate-100 rounded-xl text-xs font-bold flex items-center gap-1 transition"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-sm"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    <!-- ================= 3. MAIN DATA DISPLAY (TABLE ONLY) ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        
        <!-- Table Header Bar -->
        <div class="px-5 py-3.5 flex items-center justify-between text-white"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <div class="flex items-center gap-2.5">
                <i class="ti ti-heart text-lg"></i>
                <span class="text-xs sm:text-sm font-extrabold tracking-wide">Daftar Status Perkawinan</span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-white/20 text-white border border-white/30 backdrop-blur-xs">
                {{ $statuskawin->count() }} Data
            </span>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[650px]">
                <thead>
                    <tr class="text-white border-b border-white/20 text-[11px] font-black uppercase tracking-wider"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <th class="py-3 px-3 text-center w-14 whitespace-nowrap">NO</th>
                        <th class="py-3 px-3 w-32 whitespace-nowrap">KODE</th>
                        <th class="py-3 px-4 min-w-[320px]">STATUS PERKAWINAN</th>
                        <th class="py-3 px-3 text-center w-28 whitespace-nowrap">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700 bg-white">
                    @forelse ($statuskawin as $s)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- NO -->
                            <td class="py-3.5 px-3 text-center font-bold text-slate-400 whitespace-nowrap">
                                {{ $loop->iteration }}
                            </td>

                            <!-- KODE STATUS KAWIN -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-black tracking-wider bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $s->kode_status_kawin }}
                                </span>
                            </td>

                            <!-- STATUS KAWIN -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white shrink-0 shadow-2xs transition-transform group-hover:scale-105"
                                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                        <i class="ti ti-heart"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h6 class="font-bold text-slate-900 text-xs sm:text-sm group-hover:text-emerald-700 transition-colors mb-0">
                                            {{ $s->status_kawin }}
                                        </h6>
                                        <span class="text-[11px] text-slate-400">Klasifikasi Status & Tanggungan</span>
                                    </div>
                                </div>
                            </td>

                            <!-- AKSI (GROUPED BUTTON WITH BLUE EDIT & RED DELETE) -->
                            <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                    @can('statuskawin.edit')
                                        <button type="button" 
                                                class="w-7 h-7 rounded-md hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition-all cursor-pointer editStatusKawin"
                                                kode_status_kawin="{{ Crypt::encrypt($s->kode_status_kawin) }}"
                                                title="Edit Status Kawin">
                                            <i class="ti ti-edit text-sm"></i>
                                        </button>
                                    @endcan

                                    @can('statuskawin.delete')
                                        <form action="{{ route('statuskawin.destroy', Crypt::encrypt($s->kode_status_kawin)) }}" 
                                              method="POST" 
                                              class="m-0 deleteform">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-7 h-7 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition-all cursor-pointer delete-confirm"
                                                    title="Hapus Status Kawin">
                                                <i class="ti ti-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl">
                                        <i class="ti ti-heart-broken"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-600 mb-0">Tidak ada data status perkawinan ditemukan</p>
                                    <p class="text-[11px] text-slate-400 mb-0">Coba sesuaikan kata kunci pencarian Anda atau tambahkan status baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer Summary -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between flex-wrap gap-3">
            <div class="text-xs text-slate-500 font-medium">
                Total terdaftar: <span class="font-bold text-slate-700">{{ $statuskawin->count() }}</span> status perkawinan
            </div>
            <div class="text-xs font-semibold text-slate-400">
                Data Master Presensi GPS V2
            </div>
        </div>

    </div>

</div>

<!-- ================= MODAL CONTAINER ================= -->
<div class="modal fade" id="modalStatusKawinV2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-heart"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalStatusKawinTitle">Tambah Status Kawin</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalStatusKawinSubtitle">Formulir konfigurasi master status perkawinan karyawan</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6" id="loadModalContent">
                <!-- AJAX Content Loaded Here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        const modalEl = document.getElementById('modalStatusKawinV2');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoading() {
            $("#loadModalContent").html(`
                <div class="py-10 text-center text-slate-400">
                    <div style="width: 32px; height: 32px; border: 3px solid #059669; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 12px;"></div>
                    <p class="text-xs font-bold text-slate-600 mb-0">Memuat Formulir...</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Mohon tunggu sebentar</p>
                </div>
            `);
        }

        // Create Modal Trigger
        $('#btnCreate').on('click', function() {
            $('#modalStatusKawinTitle').text('Tambah Status Kawin');
            $('#modalStatusKawinSubtitle').text('Formulir penambahan data master status perkawinan');
            showLoading();
            modalInstance.show();
            $('#loadModalContent').load("{{ route('statuskawin.create') }}");
        });

        // Edit Modal Trigger
        $(document).on('click', '.editStatusKawin', function() {
            const kode_status_kawin = $(this).attr('kode_status_kawin');
            $('#modalStatusKawinTitle').text('Edit Status Kawin');
            $('#modalStatusKawinSubtitle').text('Perbarui konfigurasi nama status perkawinan');
            showLoading();
            modalInstance.show();
            $('#loadModalContent').load(`/statuskawin/${kode_status_kawin}/edit`);
        });

        // Delete Confirmation Alert
        $(document).on('click', '.delete-confirm', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Data status kawin yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-slate-100',
                        confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold shadow-xs',
                        cancelButton: 'px-4 py-2 rounded-xl text-xs font-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Apakah Anda yakin ingin menghapus data status kawin ini?')) {
                    form.submit();
                }
            }
        });
    });
</script>
@endpush
