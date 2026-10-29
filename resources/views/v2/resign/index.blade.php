@extends('v2.layouts.app')

@section('titlepage', 'Data Resign Karyawan')
@section('page_title', 'Resign Karyawan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

@push('mystyle')
<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

<div class="space-y-4">
    <!-- PAGE HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background: linear-gradient(135deg, {{ $themeColor1 }}15, {{ $themeColor2 }}10);">
                    <i class="ti ti-user-x text-2xl" style="color: {{ $themeColor1 }};"></i>
                </div>
                <span>Resign Karyawan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola riwayat karyawan non-aktif dan pengunduran diri
            </p>
        </div>

        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Resign Karyawan</span>
            </nav>
            <button type="button" id="btnTambah" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-md transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-plus text-sm"></i>
                <span>Tambah Data Resign</span>
            </button>
        </div>
    </div>

    <!-- ALERTS -->
    @if (Session::has('success'))
        <div data-flash-resign class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="ti ti-circle-check text-emerald-600 text-base"></i>
                <span class="font-semibold">{{ Session::get('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900"><i class="ti ti-x"></i></button>
        </div>
    @endif
    @if (Session::has('warning'))
        <div data-flash-resign class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <i class="ti ti-alert-triangle text-amber-600 text-base"></i>
                <span class="font-semibold">{{ Session::get('warning') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-amber-700 hover:text-amber-900"><i class="ti ti-x"></i></button>
        </div>
    @endif

    <!-- FILTER TOOLBAR (STANDAR V2) -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('resign.index') }}" method="GET" class="w-full">
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
                        <a href="{{ route('resign.index') }}" 
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

    <!-- DATA LIST (CLEAN V2 CARDS) -->
    <div class="space-y-2.5">
        @forelse ($resign as $d)
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-2xs hover:shadow-xs hover:border-slate-300 transition-all duration-150 px-3.5 py-3 sm:px-4 sm:py-3.5 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 lg:gap-4">
                    
                    <!-- 1. Karyawan Identity & Status -->
                    <div class="flex items-center gap-3 min-w-[240px] lg:w-80 shrink-0">
                        <div class="relative shrink-0">
                            @if (!empty($d->karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $d->karyawan->foto))
                                <img src="{{ getfotoKaryawan($d->karyawan->foto) }}" alt="{{ $d->karyawan->nama_karyawan }}" class="w-10 h-10 rounded-xl object-cover ring-1 ring-slate-200 shadow-2xs grayscale">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 border border-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs shadow-2xs">
                                    {{ strtoupper(substr($d->karyawan->nama_karyawan ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white flex items-center justify-center" title="Non Aktif">
                                <i class="ti ti-x text-[9px] text-white font-bold"></i>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h3 class="text-xs sm:text-[13px] font-bold text-slate-900 truncate" title="{{ $d->karyawan->nama_karyawan ?? '-' }}">
                                    {{ $d->karyawan->nama_karyawan ?? '-' }}
                                </h3>
                                <span class="px-1.5 py-0.2 rounded-md bg-rose-50 text-rose-700 border border-rose-200/80 text-[10px] font-extrabold uppercase">
                                    Non Aktif
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-slate-500">
                                <span class="font-mono font-semibold bg-slate-100 px-1.5 py-0.2 rounded border border-slate-200/60">{{ $d->nik }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="truncate">{{ $d->karyawan->cabang->nama_cabang ?? '-' }}</span>
                                <span class="text-slate-300">/</span>
                                <span class="truncate">{{ $d->karyawan->departemen->nama_dept ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Detail Resign Info & Alasan -->
                    <div class="flex-1 bg-slate-50/80 rounded-xl px-3 py-2 border border-slate-200/70 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 sm:gap-3">
                        
                        <!-- Tanggal & Kategori -->
                        <div class="min-w-[180px] sm:w-56 shrink-0">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-rose-600 flex items-center gap-1">
                                <i class="ti ti-calendar-off text-[11px]"></i>
                                <span>Tanggal Resign</span>
                            </div>
                            <div class="text-xs font-bold text-slate-800 mt-0.5">
                                {{ date('d M Y', strtotime($d->tanggal_resign)) }}
                            </div>
                            <div class="mt-0.5">
                                @if(!empty($d->kategoriResign))
                                    <span class="inline-flex items-center gap-1 text-[10.5px] font-semibold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200/60">
                                        <i class="ti ti-tag text-[10px]"></i>
                                        {{ $d->kategoriResign->nama_kategori }}
                                    </span>
                                @else
                                    <span class="text-[10.5px] text-slate-400">-</span>
                                @endif
                            </div>
                        </div>

                        <!-- Alasan -->
                        <div class="flex-1 min-w-0 sm:border-l sm:border-slate-200/80 sm:pl-3">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                Alasan / Keterangan
                            </div>
                            <p class="text-xs text-slate-700 font-medium line-clamp-2 mt-0.5 italic" title="{{ $d->alasan }}">
                                "{{ $d->alasan ? $d->alasan : 'Tidak ada keterangan' }}"
                            </p>
                        </div>
                    </div>

                    <!-- 3. Dokumen & Action (Aktifkan Kembali) -->
                    <div class="flex items-center justify-end gap-1.5 shrink-0 pt-2 lg:pt-0 border-t border-slate-100 lg:border-t-0">
                        @if ($d->dokumen)
                            <a href="{{ asset('storage/uploads/resign/' . $d->dokumen) }}" 
                               target="_blank" 
                               class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 border border-sky-200/80 flex items-center justify-center transition shadow-2xs" 
                               title="Lihat Surat/Dokumen Pendukung">
                                <i class="ti ti-file-text text-sm"></i>
                            </a>
                        @endif

                        <form method="POST" action="{{ route('resign.delete', $d->id) }}" class="inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="h-8 px-2.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200/80 flex items-center gap-1.5 text-xs font-bold transition shadow-2xs delete-confirm cursor-pointer" 
                                    title="Batalkan Resign (Aktifkan Karyawan Kembali)">
                                <i class="ti ti-arrow-back-up text-sm"></i>
                                <span>Aktifkan Lagi</span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="ti ti-user-check text-2xl text-slate-400"></i>
                </div>
                <h3 class="text-xs font-bold text-slate-700">Belum Ada Data Resign</h3>
                <p class="text-[11px] text-slate-400 mt-0.5 max-w-sm mx-auto">Semua karyawan terdata masih dalam status aktif atau filter pencarian tidak menemukan hasil.</p>
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    <div class="pt-2">
        {{ $resign->links('v2.components.pagination') }}
    </div>
</div>

<!-- MODAL TAMBAH DATA RESIGN -->
<div id="modalInput" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all my-8 animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between" style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}05);">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-2xs" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); color: white;">
                    <i class="ti ti-user-x text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Tambah Data Resign</h3>
                    <p class="text-[11px] text-slate-500">Proses pengunduran diri & non-aktifkan karyawan</p>
                </div>
            </div>
            <button type="button" onclick="closeModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Modal Body (Loaded via AJAX) -->
        <div id="load-form" class="p-5">
            <div class="py-12 flex flex-col items-center justify-center text-slate-400">
                <div class="w-7 h-7 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs font-semibold mt-2.5 text-slate-500">Memuat formulir...</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    function closeModal() {
        const modal = document.getElementById('modalInput');
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    $(function() {
        // Open Modal Create
        $("#btnTambah").on('click', function(e) {
            e.preventDefault();
            const modal = document.getElementById('modalInput');
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            $("#load-form").html(`
                <div class="py-12 flex flex-col items-center justify-center text-slate-400">
                    <div class="w-7 h-7 border-2 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-xs font-semibold mt-2.5 text-slate-500">Memuat formulir...</span>
                </div>
            `);

            $("#load-form").load("{{ route('resign.create') }}", function(response, status, xhr) {
                if (status == "error") {
                    $("#load-form").html(`
                        <div class="text-center py-6 text-rose-500">
                            <i class="ti ti-alert-triangle text-2xl"></i>
                            <p class="text-xs font-semibold mt-1">Gagal memuat form: ` + xhr.status + " " + xhr.statusText + `</p>
                        </div>
                    `);
                }
            });
        });

        // Close on backdrop click
        $('#modalInput').on('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Confirm Delete / Re-Activate
        $(document).on('click', '.delete-confirm', function(e) {
            e.preventDefault();
            const form = $(this).closest("form");
            Swal.fire({
                title: 'Batalkan Resign?',
                text: "Karyawan ini akan dikembalikan menjadi status Aktif kembali.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '{{ $themeColor1 }}',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Aktifkan Kembali',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl text-xs font-bold px-4 py-2.5',
                    cancelButton: 'rounded-xl text-xs font-bold px-4 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
        // Auto-dismiss flash alerts after 4s
        setTimeout(function() {
            $('[data-flash-resign]').fadeOut(400, function() { $(this).remove(); });
        }, 4000);
    });
</script>
@endpush
