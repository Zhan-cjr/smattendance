@extends('v2.layouts.app')

@section('titlepage', 'Periode Jadwal')
@section('page_title', 'Periode Jadwal Kerja')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalPeriodes = $periodes->total();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-calendar-time text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Periode Jadwal Kerja</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola draft perancangan, matriks alokasi shift karyawan, serta publish jadwal bulanan
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
                    <i class="ti ti-calendar text-sm"></i>
                    <span>Jadwal Kerja</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Periode Jadwal</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('jadwalperiode.create')
                    <!-- Buat Draft Jadwal Baru -->
                    <a href="{{ route('jadwalperiode.create') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                       style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Buat Draft Jadwal</span>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER BAR STANDAR V2 (2-BARIS KONSISTEN) ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('jadwalperiode.index') }}" method="GET" class="space-y-3">
            
            <!-- BARIS 1: BULAN & TAHUN (GRID 2 KOLOM RAPI) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3 w-full">
                <!-- Bulan -->
                <div class="relative w-full">
                    <select name="bulan" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-semibold shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Bulan</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Tahun -->
                <div class="relative w-full">
                    <select name="tahun" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-semibold shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Tahun</option>
                        @for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                            <option value="{{ $y }}" {{ (request('tahun') == $y || (!request('tahun') && $y == date('Y'))) ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- BARIS 2: STATUS, DEPARTEMEN, GRUP & TOMBOL AKSI -->
            <div class="flex flex-col lg:flex-row items-center gap-2.5 sm:gap-3 w-full">
                
                <!-- Status Filter -->
                <div class="w-full sm:w-auto lg:w-40 shrink-0">
                    <select name="status" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <!-- Departemen Filter -->
                <div class="w-full sm:w-auto lg:flex-1">
                    <select name="kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Grup Filter -->
                <div class="w-full sm:w-auto lg:flex-1">
                    <select name="kode_grup" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Grup</option>
                        @foreach ($grups as $g)
                            <option value="{{ $g->kode_grup }}" {{ request('kode_grup') == $g->kode_grup ? 'selected' : '' }}>
                                {{ $g->nama_grup }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Buttons: Cari & Reset -->
                <div class="flex items-center gap-1.5 shrink-0 w-full lg:w-auto">
                    <button type="submit" 
                            class="w-full lg:w-auto px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['bulan', 'tahun', 'status', 'kode_dept', 'kode_grup']))
                        <a href="{{ route('jadwalperiode.index') }}" 
                           class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1 active:scale-95"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    <!-- ================= 3. LIST PERIODE JADWAL CARD MODERN ================= -->
    <div class="space-y-3">
        @forelse ($periodes as $p)
            @php
                $bulanStr = \Carbon\Carbon::create($p->tahun, $p->bulan, 1)->translatedFormat('F Y');
            @endphp
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 group">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    
                    <!-- Sisi Kiri: Icon Kalender & Info Jadwal -->
                    <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                        <!-- Icon Box Modern -->
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0 text-white shadow-2xs"
                             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-calendar-time"></i>
                        </div>

                        <!-- Details -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center flex-wrap gap-2 mb-1">
                                <h3 class="text-sm font-black text-slate-900 truncate tracking-tight group-hover:text-emerald-700 transition">
                                    {{ $p->nama_periode }}
                                </h3>
                                
                                <!-- Status Badge -->
                                @if ($p->status === 'draft')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs inline-flex items-center gap-1">
                                        <i class="ti ti-pencil text-[11px]"></i> Draft
                                    </span>
                                @elseif ($p->status === 'published')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs inline-flex items-center gap-1">
                                        <i class="ti ti-check text-[11px]"></i> Published
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs inline-flex items-center gap-1">
                                        <i class="ti ti-archive text-[11px]"></i> Archived
                                    </span>
                                @endif

                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-100 text-slate-600">
                                    {{ $p->items_count }} Sel Jadwal
                                </span>
                            </div>

                            <!-- Periode Bulan / Tahun -->
                            <div class="text-xs font-semibold text-slate-600 flex items-center gap-1.5 mb-2">
                                <i class="ti ti-calendar-event text-emerald-600 text-sm"></i>
                                <span>Periode: {{ $bulanStr }}</span>
                            </div>

                            <!-- Meta Chips: Scope Dept, Cabang, Grup, Creator -->
                            <div class="flex items-center flex-wrap gap-1.5 text-[11px]">
                                @if($p->grup)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-100 font-bold">
                                        <i class="ti ti-users text-emerald-600"></i>
                                        <span>Grup: {{ $p->grup->nama_grup }}</span>
                                    </span>
                                @endif
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium">
                                    <i class="ti ti-building text-slate-400"></i>
                                    <span>{{ $p->departemen->nama_dept ?? 'Semua Departemen' }}</span>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium">
                                    <i class="ti ti-map-pin text-slate-400"></i>
                                    <span>{{ $p->cabang->nama_cabang ?? 'Semua Cabang' }}</span>
                                </span>
                                @if ($p->creator)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-50 text-slate-400 font-medium">
                                        <i class="ti ti-user-check text-slate-400"></i>
                                        <span>Dibuat: {{ $p->creator->name ?? '-' }}</span>
                                    </span>
                                @endif
                                @if ($p->catatan)
                                    <span class="text-slate-400 italic text-[10.5px]">
                                        &bull; {{ $p->catatan }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Action Buttons -->
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0 border-t md:border-t-0 pt-3 md:pt-0 border-slate-100 w-full md:w-auto justify-end">
                        <a href="{{ route('jadwalperiode.show', Crypt::encrypt($p->id)) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 text-white font-bold text-xs rounded-xl shadow-xs transition hover:opacity-95 active:scale-95 cursor-pointer"
                           style="background-color: {{ $p->status === 'published' ? '#059669' : $themeColor1 }};">
                            <i class="ti {{ $p->status === 'published' ? 'ti-eye' : 'ti-edit' }} text-sm"></i>
                            <span>{{ $p->status === 'published' ? 'Lihat Matriks' : 'Edit Matriks' }}</span>
                        </a>

                        @if ($p->status === 'draft')
                            @can('jadwalperiode.delete')
                                <form action="{{ route('jadwalperiode.destroy', Crypt::encrypt($p->id)) }}" 
                                      method="POST" 
                                      class="formDelete inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-8 h-8 rounded-lg border border-slate-200 hover:border-rose-200 hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                            title="Hapus Draft">
                                        <i class="ti ti-trash text-sm"></i>
                                    </button>
                                </form>
                            @endcan
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/90 p-12 text-center shadow-2xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                    <i class="ti ti-calendar-off"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800 mb-1">Belum Ada Periode Jadwal</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
                    Belum ada draft atau periode jadwal bulanan yang dibuat. Klik tombol di bawah untuk membuat draft jadwal baru.
                </p>
                @can('jadwalperiode.create')
                    <a href="{{ route('jadwalperiode.create') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition hover:opacity-95 active:scale-95 cursor-pointer"
                       style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Buat Draft Jadwal Baru</span>
                    </a>
                @endcan
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($periodes->hasPages())
        <div class="pt-2">
            {{ $periodes->links('v2.components.pagination') }}
        </div>
    @endif

</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        // Konfirmasi Hapus Draft
        $('.formDelete').submit(function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Draft Jadwal?',
                text: 'Draft periode jadwal ini beserta alokasi per selnya akan dihapus permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl shadow-xl'
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
