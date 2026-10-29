@extends('v2.layouts.app')

@section('titlepage', 'Data Mutasi, Promosi & Demosi')
@section('page_title', 'Mutasi, Promosi & Demosi')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: linear-gradient(135deg, {{ $themeColor1 }}15, {{ $themeColor2 }}10);">
                    <i class="ti ti-transform text-2xl" style="color: {{ $themeColor1 }};"></i>
                </div>
                <span>Mutasi, Promosi & Demosi</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola riwayat perubahan penempatan dan jabatan karyawan
            </p>
        </div>

        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Mutasi & Promosi</span>
            </nav>
            <button type="button" id="btnTambah" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-md transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-plus text-sm"></i>
                <span>Tambah Data</span>
            </button>
        </div>
    </div>

    <!-- ALERTS -->
    @if (Session::has('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-circle-check text-emerald-600 text-base"></i>
                <span class="font-semibold">{{ Session::get('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900"><i class="ti ti-x"></i></button>
        </div>
    @endif
    @if (Session::has('warning'))
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-alert-triangle text-amber-600 text-base"></i>
                <span class="font-semibold">{{ Session::get('warning') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900"><i class="ti ti-x"></i></button>
        </div>
    @endif

    <!-- FILTER TOOLBAR (STANDAR V2) -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('mutasi.index') }}" method="GET" class="w-full">
            <div class="flex flex-col lg:flex-row items-center gap-2.5 sm:gap-3 w-full">
                
                <!-- Search Input -->
                <div class="flex-1 min-w-0 w-full relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" 
                           name="nama_karyawan" 
                           value="{{ request('nama_karyawan') }}" 
                           placeholder="Cari Nama Karyawan / NIK..." 
                           class="w-full h-10 pl-10 pr-4 py-2 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <!-- Cabang Filter -->
                <div class="w-full sm:w-auto lg:w-48 shrink-0">
                    <select name="kode_cabang" class="w-full h-10 px-3 py-2 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Departemen Filter -->
                <div class="w-full sm:w-auto lg:w-52 shrink-0">
                    <select name="kode_dept" class="w-full h-10 px-3 py-2 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $dep)
                            <option value="{{ $dep->kode_dept }}" {{ request('kode_dept') == $dep->kode_dept ? 'selected' : '' }}>
                                {{ $dep->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons: Cari & Reset -->
                <div class="flex items-center gap-2 shrink-0 w-full lg:w-auto">
                    <button type="submit" 
                            class="w-full lg:w-auto h-10 px-4 py-2 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['nama_karyawan', 'kode_cabang', 'kode_dept']))
                        <a href="{{ route('mutasi.index') }}" 
                           class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center justify-center gap-1 border border-slate-200"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- DATA LIST (COMPACT & CLEAN FLOW CARDS) -->
    <div class="space-y-2.5">
        @forelse ($mutasi as $d)
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs hover:shadow-xs hover:border-slate-300 transition-all duration-150 px-3.5 py-3 sm:px-4 sm:py-3.5 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 lg:gap-4">
                    
                    <!-- 1. Karyawan Identity & Type -->
                    <div class="flex items-center gap-3 min-w-[220px] lg:w-72 shrink-0">
                        <div class="relative shrink-0">
                            @if (!empty($d->karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $d->karyawan->foto))
                                <img src="{{ getfotoKaryawan($d->karyawan->foto) }}" alt="{{ $d->karyawan->nama_karyawan }}" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200 shadow-2xs">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 border border-slate-200 flex items-center justify-center text-slate-700 font-bold text-xs shadow-2xs">
                                    {{ strtoupper(substr($d->karyawan->nama_karyawan ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5">
                                <h3 class="text-xs sm:text-[13px] font-bold text-slate-900 truncate group-hover:text-emerald-700 transition" title="{{ $d->karyawan->nama_karyawan ?? '-' }}">
                                    {{ $d->karyawan->nama_karyawan ?? '-' }}
                                </h3>
                            </div>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[10.5px] font-mono font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.2 rounded border border-slate-200/60">{{ $d->nik }}</span>
                                <span class="text-slate-300">•</span>
                                <div class="text-[10.5px] text-slate-500 flex items-center gap-0.5">
                                    <i class="ti ti-calendar text-slate-400 text-xs"></i>
                                    <span>{{ date('d M Y', strtotime($d->tanggal_mutasi)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Transition Flow (Compact & Structured) -->
                    <div class="flex-1 bg-slate-50/80 rounded-xl px-3 py-2 border border-slate-200/70 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 sm:gap-3">
                        
                        <!-- Penempatan Lama -->
                        <div class="flex-1 min-w-0 text-left sm:text-right">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1 sm:justify-end">
                                <i class="ti ti-history text-[11px] text-slate-600"></i>
                                <span>Sebelumnya</span>
                            </div>
                            <div class="text-xs font-bold text-slate-800 truncate mt-0.5" title="{{ $d->jabatanLama->nama_jabatan ?? '-' }}">
                                {{ $d->jabatanLama->nama_jabatan ?? '-' }}
                            </div>
                            <div class="text-[10.5px] text-slate-600 font-medium truncate flex items-center gap-1 sm:justify-end mt-0.5">
                                <span>{{ $d->cabangLama->nama_cabang ?? '-' }}</span>
                                <span class="text-slate-300">/</span>
                                <span>{{ $d->deptLama->nama_dept ?? '-' }}</span>
                            </div>
                        </div>
                        
                        <!-- Direction Arrow & Badge Type -->
                        <div class="shrink-0 flex sm:flex-col items-center justify-center gap-1 py-1 sm:py-0 px-1">
                            @if ($d->jenis_mutasi == 'PROMOSI')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9.5px] font-black tracking-wide bg-emerald-100 text-emerald-800 border border-emerald-300/70">
                                    <i class="ti ti-trending-up text-xs"></i> PROMOSI
                                </span>
                            @elseif ($d->jenis_mutasi == 'DEMOSI')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9.5px] font-black tracking-wide bg-rose-100 text-rose-800 border border-rose-300/70">
                                    <i class="ti ti-trending-down text-xs"></i> DEMOSI
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9.5px] font-black tracking-wide bg-sky-100 text-sky-800 border border-sky-300/70">
                                    <i class="ti ti-arrows-left-right text-xs"></i> MUTASI
                                </span>
                            @endif
                            <i class="ti ti-arrow-right hidden sm:block text-slate-400 text-xs mt-0.5"></i>
                        </div>
                        
                        <!-- Penempatan Baru -->
                        <div class="flex-1 min-w-0 text-left">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 flex items-center gap-1">
                                <i class="ti ti-sparkles text-[11px] text-emerald-700"></i>
                                <span>Penempatan Baru</span>
                            </div>
                            <div class="text-xs font-bold text-emerald-800 truncate mt-0.5" title="{{ $d->jabatanBaru->nama_jabatan ?? '-' }}">
                                {{ $d->jabatanBaru->nama_jabatan ?? '-' }}
                            </div>
                            <div class="text-[10.5px] text-emerald-700/90 font-medium truncate flex items-center gap-1 mt-0.5">
                                <span>{{ $d->cabangBaru->nama_cabang ?? '-' }}</span>
                                <span class="text-emerald-400">/</span>
                                <span>{{ $d->deptBaru->nama_dept ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Keterangan, SK Document & Action -->
                    <div class="flex items-center justify-between lg:justify-end gap-3 lg:w-56 shrink-0 pt-1 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        <div class="flex-1 min-w-0 lg:text-right">
                            <p class="text-[11px] text-slate-500 italic truncate" title="{{ $d->keterangan }}">
                                {{ $d->keterangan ?: 'Tanpa keterangan' }}
                            </p>
                            @if ($d->doc_sk)
                                <a href="{{ asset('storage/' . $d->doc_sk) }}" target="_blank" class="inline-flex items-center gap-1 text-[10.5px] font-bold text-blue-600 hover:text-blue-800 hover:underline transition mt-0.5">
                                    <i class="ti ti-file-text text-xs"></i>
                                    <span>Unduh SK</span>
                                </a>
                            @endif
                        </div>
                        <div class="shrink-0 flex items-center">
                            <form method="POST" action="{{ route('mutasi.destroy', $d->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all active:scale-90 cursor-pointer shadow-2xs" title="Hapus Data">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs p-10 text-center">
                <div class="w-16 h-16 mx-auto bg-slate-50 rounded-2xl flex items-center justify-center mb-3 border border-slate-100">
                    <i class="ti ti-folder-off text-2xl text-slate-400"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-800 mb-1">Belum ada data Mutasi & Promosi</h3>
                <p class="text-[11px] text-slate-500 max-w-sm mx-auto">Silakan tambahkan data mutasi, promosi, atau demosi karyawan baru menggunakan tombol di atas.</p>
            </div>
        @endforelse

        <!-- PAGINATION -->
        <div class="mt-4">
            {{ $mutasi->links('v2.components.pagination') }}
        </div>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="modalInput" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-header border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex items-center justify-between">
                <h5 class="modal-title text-base font-black text-slate-800 flex items-center gap-2">
                    <i class="ti ti-transform text-emerald-600 text-xl"></i>
                    Tambah Data Mutasi
                </h5>
                <button type="button" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-200 text-slate-500 transition-colors" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body p-6 bg-white" id="load-form">
                <!-- Form loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        // Load Create Form
        $("#btnTambah").click(function(e) {
            e.preventDefault();
            $("#load-form").html('<div class="flex justify-center items-center py-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500"></div></div>');
            $("#modalInput").modal("show");
            $("#load-form").load("{{ route('mutasi.create') }}");
        });
    });

    // Handle Delete Confirmation
    function confirmDelete(button) {
        Swal.fire({
            title: 'Hapus Data Mutasi?',
            text: "Data yang dihapus akan dikembalikan ke posisi lama!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl shadow-xl border border-slate-100',
                confirmButton: 'px-5 py-2.5 rounded-xl text-xs font-bold',
                cancelButton: 'px-5 py-2.5 rounded-xl text-xs font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $(button).closest('form').submit();
            }
        });
    }
</script>
@endpush
