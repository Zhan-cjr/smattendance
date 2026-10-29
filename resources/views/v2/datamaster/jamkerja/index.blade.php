@extends('v2.layouts.app')

@section('titlepage', 'Jam Kerja')
@section('page_title', 'Data Jam Kerja')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalJamKerja = $jamkerja->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-clock text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Jam Kerja</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Manajemen master jadwal jam kerja harian, shift, istirahat, dan lintas hari
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
                <span class="font-bold text-slate-800">Jam Kerja</span>
            </nav>

            @can('jamkerja.create')
                <button type="button" id="btnCreate"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Jam Kerja</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- ================= 2. FILTER TOOLBAR (FULL WIDTH RESPONSIVE) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('jamkerja.index') }}" method="GET" class="w-full">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 sm:gap-3 w-full items-center">
                
                <!-- Search Input: Nama Jam Kerja (Spans full width responsively) -->
                <div class="sm:col-span-9 lg:col-span-10 relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" name="nama_jam_kerja_search" id="nama_jam_kerja_search"
                           value="{{ request('nama_jam_kerja_search') }}"
                           placeholder="Cari berdasarkan nama jam kerja atau shift..."
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
                    @if(request()->filled('nama_jam_kerja_search'))
                        <a href="{{ route('jamkerja.index') }}" 
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
                    <i class="ti ti-clock"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Master Data Jam Kerja</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $totalJamKerja }} Jadwal Terdaftar
            </span>
        </div>

        <!-- Responsive Table Container with Matching Solid Header -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2 px-3 text-center w-12 text-white/90 border-0 border-t-0 whitespace-nowrap">No</th>
                        <th class="py-2 px-3 w-28 text-white/90 border-0 border-t-0 whitespace-nowrap">Kode</th>
                        <th class="py-2 px-3.5 min-w-[180px] text-white/90 border-0 border-t-0">Nama Jam Kerja</th>
                        <th class="py-2 px-3 text-center w-28 text-white/90 border-0 border-t-0 whitespace-nowrap">Masuk</th>
                        <th class="py-2 px-3 text-center w-28 text-white/90 border-0 border-t-0 whitespace-nowrap">Pulang</th>
                        <th class="py-2 px-3 text-center w-36 text-white/90 border-0 border-t-0 whitespace-nowrap">Istirahat</th>
                        <th class="py-2 px-3 text-center w-36 text-white/90 border-0 border-t-0 whitespace-nowrap">Ist. Lembur</th>
                        <th class="py-2 px-3 text-center w-28 text-white/90 border-0 border-t-0 whitespace-nowrap">Lintas Hari</th>
                        <th class="py-2 px-3 text-center w-24 text-white/90 border-0 border-t-0 whitespace-nowrap">Total</th>
                        <th class="py-2 px-2 text-center w-20 text-white/90 border-0 border-t-0 whitespace-nowrap">Warna</th>
                        <th class="py-2 px-3 text-center w-24 text-white/90 border-0 border-t-0 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($jamkerja as $d)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- No Index -->
                            <td class="py-1.5 px-3 text-center text-slate-400 font-bold text-xs whitespace-nowrap">
                                <span class="w-5 h-5 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[10.5px]">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <!-- Kode Jam Kerja -->
                            <td class="py-1.5 px-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200 group-hover:bg-emerald-50 group-hover:text-emerald-800 group-hover:border-emerald-200 transition">
                                    {{ $d->kode_jam_kerja }}
                                </span>
                            </td>

                            <!-- Nama Jam Kerja & Keterangan -->
                            <td class="py-1.5 px-3.5">
                                <div class="font-bold text-slate-800 text-xs leading-snug">{{ $d->nama_jam_kerja }}</div>
                                @if($d->keterangan)
                                    <div class="text-[10.5px] text-slate-500 font-normal leading-tight line-clamp-1" title="{{ $d->keterangan }}">{{ $d->keterangan }}</div>
                                @endif
                            </td>

                            <!-- Jam Masuk (Font Normal) -->
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-700 text-xs">
                                    {{ $d->jam_masuk }}
                                </span>
                            </td>

                            <!-- Jam Pulang (Font Normal) -->
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-700 text-xs">
                                    {{ $d->jam_pulang }}
                                </span>
                            </td>

                            <!-- Istirahat Info (Font Normal) -->
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                @if ($d->istirahat == 1)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold">
                                        <i class="ti ti-check text-xs"></i>
                                        @if($d->jam_awal_istirahat && $d->jam_akhir_istirahat)
                                            <span>{{ date('H:i', strtotime($d->jam_awal_istirahat)) }} - {{ date('H:i', strtotime($d->jam_akhir_istirahat)) }}</span>
                                        @else
                                            <span>Ya</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 border border-slate-200 text-[11px] font-medium">
                                        <i class="ti ti-minus text-xs"></i>
                                        <span>Tidak</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Istirahat Lembur -->
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                @if (($d->istirahatlembur ?? 0) == 1)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-semibold">
                                        <i class="ti ti-check text-xs"></i>
                                        @if($d->jam_awal_istirahatlembur && $d->jam_akhir_istirahatlembur)
                                            <span>{{ date('H:i', strtotime($d->jam_awal_istirahatlembur)) }} - {{ date('H:i', strtotime($d->jam_akhir_istirahatlembur)) }}</span>
                                        @else
                                            <span>Ya</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 border border-slate-200 text-[11px] font-medium">
                                        <i class="ti ti-minus text-xs"></i>
                                        <span>Tidak</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Lintas Hari -->
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                @if ($d->lintashari == 1)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 text-[11px] font-semibold" title="Lintas Hari: Batas {{ $d->batas_presensi_pulang ? date('H:i', strtotime($d->batas_presensi_pulang)) : 'Default' }}">
                                        <i class="ti ti-moon text-xs"></i>
                                        <span>Ya</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 border border-slate-200 text-[11px] font-medium">
                                        <i class="ti ti-sun text-xs"></i>
                                        <span>Tidak</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Total Jam -->
                            <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-800 text-xs">
                                    {{ $d->total_jam }} Jam
                                </span>
                            </td>

                            <!-- Warna Indikator -->
                            <td class="py-1.5 px-2 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center">
                                    <span class="w-4 h-4 rounded-full border border-white shadow-xs inline-block ring-1 ring-slate-200" 
                                          style="background-color: {{ $d->color ?: '#0a5c41' }};"
                                          title="{{ $d->color }}"></span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-1.5 px-3 text-center">
                                <div class="inline-flex items-center justify-center gap-1">
                                    @can('jamkerja.edit')
                                        <button type="button" 
                                                class="btnEdit w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200/80 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer"
                                                kode_jam_kerja="{{ Crypt::encrypt($d->kode_jam_kerja) }}"
                                                title="Edit Jam Kerja">
                                            <i class="ti ti-edit text-xs"></i>
                                        </button>
                                    @endcan

                                    @can('jamkerja.delete')
                                        <form method="POST" action="{{ route('jamkerja.delete', Crypt::encrypt($d->kode_jam_kerja)) }}" class="deleteform inline-block m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btnDelete w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/80 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer"
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
                            <td colspan="10" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                                        <i class="ti ti-clock-off text-2xl"></i>
                                    </div>
                                    <span class="font-bold text-slate-700 text-sm">Belum ada data jam kerja</span>
                                    <p class="text-xs text-slate-400 max-w-sm">
                                        Silakan klik tombol "Tambah Jam Kerja" untuk menambahkan pola jadwal jam kerja baru.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Universal V2 Modal for Jam Kerja -->
<div class="modal fade" id="modalV2JamKerja" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border border-slate-200/90 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-clock"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalV2Title">Tambah Jam Kerja</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalV2Subtitle">Kelola jadwal jam kerja dan aturan shift</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="modal-body p-6 bg-white" id="loadmodalV2JamKerja">
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
        const modalEl = document.getElementById('modalV2JamKerja');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoadingSpinner() {
            $('#loadmodalV2JamKerja').html(`
                <div class="flex flex-col items-center justify-center py-12 text-slate-400 gap-3">
                    <i class="ti ti-loader animate-spin text-3xl" style="color: ${themeColor1};"></i>
                    <span class="text-xs font-semibold tracking-wide">Memuat formulir...</span>
                </div>
            `);
        }

        // Tambah Jam Kerja
        $("#btnCreate").on("click", function(e) {
            e.preventDefault();
            $("#modalV2Title").text("Tambah Jam Kerja");
            $("#modalV2Subtitle").text("Tambah data pola dan rentang jam kerja baru");
            modalInstance.show();
            showLoadingSpinner();
            $("#loadmodalV2JamKerja").load("{{ route('jamkerja.create') }}");
        });

        // Edit Jam Kerja
        $(document).on("click", ".btnEdit", function(e) {
            e.preventDefault();
            var kode_jam_kerja = $(this).attr("kode_jam_kerja");
            $("#modalV2Title").text("Edit Jam Kerja");
            $("#modalV2Subtitle").text("Perbarui aturan jam kerja, istirahat, dan lintas hari");
            modalInstance.show();
            showLoadingSpinner();
            $("#loadmodalV2JamKerja").load('/jamkerja/' + kode_jam_kerja + '/edit');
        });

        // SweetAlert2 Confirmation for Delete
        $(document).on("submit", ".deleteform", function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: 'Hapus Jam Kerja?',
                text: "Apakah Anda yakin ingin menghapus data jam kerja ini?",
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
