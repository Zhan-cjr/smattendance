@extends('v2.layouts.app')

@section('titlepage', 'Mesin Fingerprint')
@section('page_title', 'Data Mesin Fingerprint')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalMesin = $mesinfinger->total();
    $totalAktif = \App\Models\MesinFingerprint::where('status', 'Aktif')->count();
    $totalNonaktif = \App\Models\MesinFingerprint::where('status', 'Nonaktif')->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-device-desktop text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Mesin Fingerprint</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Manajemen perangkat sidik jari (biometrik), koneksi ADMS, dan lokasi mesin presensi
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
                    <i class="ti ti-settings text-sm"></i>
                    <span>Konfigurasi</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Mesin Fingerprint</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('mesinfingerprint.create')
                    <!-- Tambah Mesin -->
                    <button type="button" id="btnCreate" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Mesin</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER TOOLBAR (FULL WIDTH RESPONSIVE) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('mesin-fingerprint.index') }}" method="GET" class="w-full">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 sm:gap-3 w-full items-center">
                
                <!-- Search Input: Nama / Merek / SN -->
                <div class="sm:col-span-9 lg:col-span-10 relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" name="nama_mesin" id="nama_mesin_search"
                           value="{{ request('nama_mesin') }}"
                           placeholder="Cari berdasarkan merek (Fingerspot / Solution), type, atau nama mesin..."
                           class="w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>

                <!-- Action Buttons: Cari & Reset -->
                <div class="sm:col-span-3 lg:col-span-2 flex items-center gap-2 w-full justify-end">
                    <button type="submit" 
                            class="flex-1 h-10 px-4 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-search text-sm"></i>
                        <span>Cari</span>
                    </button>
                    @if(request()->filled('nama_mesin'))
                        <a href="{{ route('mesin-fingerprint.index') }}" 
                           class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center justify-center border border-slate-200"
                           title="Reset Pencarian">
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
                    <i class="ti ti-device-desktop"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Mesin Fingerprint</h3>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                    {{ $totalAktif }} Aktif
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-white/90 border border-white/20 shadow-2xs backdrop-blur-xs">
                    {{ $totalMesin }} Total Mesin
                </span>
            </div>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2 px-3 text-center w-12 text-white/90 border-0 border-t-0 whitespace-nowrap">No</th>
                        <th class="py-2 px-3.5 min-w-[160px] text-white/90 border-0 border-t-0">Serial Number (SN)</th>
                        <th class="py-2 px-3.5 w-36 text-white/90 border-0 border-t-0 whitespace-nowrap">Merek Mesin</th>
                        <th class="py-2 px-3.5 w-36 text-white/90 border-0 border-t-0 whitespace-nowrap">Type Model</th>
                        <th class="py-2 px-3.5 min-w-[140px] text-white/90 border-0 border-t-0">Lokasi</th>
                        <th class="py-2 px-3.5 min-w-[160px] text-white/90 border-0 border-t-0">Titik Koordinat</th>
                        <th class="py-2 px-3 text-center w-28 text-white/90 border-0 border-t-0 whitespace-nowrap">Status</th>
                        <th class="py-2 px-3 text-center w-20 text-white/90 border-0 border-t-0 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($mesinfinger as $d)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- No -->
                            <td class="py-1.5 px-3 text-center text-slate-400 font-semibold text-xs whitespace-nowrap">
                                {{ $loop->iteration + $mesinfinger->firstItem() - 1 }}
                            </td>

                            <!-- Serial Number (SN) -->
                            <td class="py-1.5 px-3.5">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200">
                                        <i class="ti ti-barcode text-xs"></i>
                                    </div>
                                    <span class="font-bold text-slate-900 font-mono text-xs">
                                        {{ $d->sn }}
                                    </span>
                                </div>
                            </td>

                            <!-- Merek -->
                            <td class="py-1.5 px-3.5 whitespace-nowrap">
                                @if($d->nama_mesin == 'Fingerspot')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <i class="ti ti-fingerprint text-xs"></i>
                                        <span>Fingerspot</span>
                                    </span>
                                @elseif($d->nama_mesin == 'Solution')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                        <i class="ti ti-cpu text-xs"></i>
                                        <span>Solution</span>
                                    </span>
                                @else
                                    <span class="font-semibold text-slate-700 text-xs">
                                        {{ $d->nama_mesin }}
                                    </span>
                                @endif
                            </td>

                            <!-- Type -->
                            <td class="py-1.5 px-3.5 whitespace-nowrap">
                                <span class="text-xs font-semibold text-slate-700">
                                    {{ $d->merk ?: '-' }}
                                </span>
                            </td>

                            <!-- Lokasi -->
                            <td class="py-1.5 px-3.5">
                                <div class="flex items-center gap-1 text-xs text-slate-700 font-medium">
                                    <i class="ti ti-map-pin text-slate-400 text-xs shrink-0"></i>
                                    <span class="truncate max-w-[180px]" title="{{ $d->lokasi }}">
                                        {{ $d->lokasi ?: '-' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Titik Koordinat -->
                            <td class="py-1.5 px-3.5 font-mono text-xs text-slate-500">
                                @if(!empty($d->titik_koordinat))
                                    <span class="truncate block max-w-[160px]" title="{{ $d->titik_koordinat }}">
                                        {{ $d->titik_koordinat }}
                                    </span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                @if ($d->status == 'Aktif')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    @can('mesinfingerprint.edit')
                                        <button type="button" 
                                                class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 border border-blue-200 flex items-center justify-center transition active:scale-95 btnEdit cursor-pointer shadow-2xs"
                                                data-id="{{ Crypt::encrypt($d->id) }}" 
                                                title="Edit Mesin">
                                            <i class="ti ti-edit text-xs"></i>
                                        </button>
                                    @endcan

                                    @can('mesinfingerprint.delete')
                                        <form action="{{ route('mesin-fingerprint.delete', Crypt::encrypt($d->id)) }}" 
                                              method="POST" 
                                              class="deleteform inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs"
                                                    title="Hapus Mesin">
                                                <i class="ti ti-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center bg-white">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">
                                        <i class="ti ti-device-desktop-off text-2xl"></i>
                                    </div>
                                    <span class="font-bold text-slate-700 text-sm">Belum ada data mesin fingerprint</span>
                                    <p class="text-xs text-slate-400 max-w-sm">
                                        Silakan klik tombol "Tambah Mesin" untuk mendaftarkan perangkat sidik jari baru ke sistem.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        @if ($mesinfinger->hasPages() || $mesinfinger->total() > 0)
            <div class="p-4 bg-white border-t border-slate-200/80">
                {{ $mesinfinger->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

</div>

<!-- Universal V2 Modal for Mesin Fingerprint (Consistent with Departemen) -->
<div class="modal fade" id="modalV2Mesin" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border border-slate-200/90 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-device-desktop"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalV2Title">Tambah Mesin Fingerprint</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalV2Subtitle">Kelola konfigurasi perangkat mesin presensi ADMS</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="modal-body p-6 bg-white" id="loadmodalV2Mesin">
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
        const modalEl = document.getElementById('modalV2Mesin');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoadingSpinner() {
            $('#loadmodalV2Mesin').html(`
                <div class="flex flex-col items-center justify-center py-10 text-slate-400 gap-3">
                    <i class="ti ti-loader animate-spin text-3xl" style="color: ${themeColor1};"></i>
                    <span class="text-xs font-semibold tracking-wide">Memuat formulir...</span>
                </div>
            `);
        }

        // Tambah Mesin
        $("#btnCreate").on("click", function(e) {
            e.preventDefault();
            $("#modalV2Title").text("Tambah Mesin Fingerprint");
            $("#modalV2Subtitle").text("Daftarkan perangkat mesin sidik jari baru");
            modalInstance.show();
            showLoadingSpinner();
            $("#loadmodalV2Mesin").load("{{ route('mesin-fingerprint.create') }}");
        });

        // Edit Mesin
        $(document).on("click", ".btnEdit", function(e) {
            e.preventDefault();
            var id = $(this).data("id");
            $("#modalV2Title").text("Edit Mesin Fingerprint");
            $("#modalV2Subtitle").text("Perbarui konfigurasi dan lokasi perangkat mesin");
            modalInstance.show();
            showLoadingSpinner();
            $.ajax({
                type: 'POST',
                url: "{{ route('mesin-fingerprint.edit') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(response) {
                    $('#loadmodalV2Mesin').html(response);
                },
                error: function(err) {
                    $('#loadmodalV2Mesin').html(`
                        <div class="text-center py-8 text-rose-500 text-xs font-bold">
                            <i class="ti ti-alert-circle text-2xl mb-1"></i>
                            <p>Gagal memuat data formulir mesin.</p>
                        </div>
                    `);
                }
            });
        });

        // SweetAlert2 Confirmation for Delete
        $(document).on("submit", ".deleteform", function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Hapus Mesin Fingerprint?',
                text: "Apakah Anda yakin ingin menghapus data mesin ini dari sistem?",
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
