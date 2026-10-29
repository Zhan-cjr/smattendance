@extends('v2.layouts.app')

@section('titlepage', 'Pola Shift')
@section('page_title', 'Template Pola Shift')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalPatterns = $patterns->total();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-repeat text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Template Pola Shift</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola template siklus perulangan shift kerja karyawan / grup (misal: 4-2, 5-2, 2-2-2-1)
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
                <span class="font-bold text-slate-800">Pola Shift</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('shiftpattern.create')
                    <a href="{{ route('shiftpattern.create') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                       style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Pola Shift</span>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER BAR STANDAR V2 ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('shiftpattern.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2.5 sm:gap-3 w-full">
            <!-- Search Input -->
            <div class="relative flex-1 w-full">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-search text-base"></i>
                </span>
                <input type="text" 
                       name="nama_pola" 
                       value="{{ request('nama_pola') }}" 
                       placeholder="Cari kode atau nama pola shift..." 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-4 text-xs sm:text-sm font-medium text-slate-800 bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-1.5 shrink-0 w-full sm:w-auto">
                <button type="submit" 
                        class="w-full sm:w-auto px-5 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                        style="background-color: {{ $themeColor1 }};">
                    <i class="ti ti-search text-sm"></i>
                    <span>Cari Pola</span>
                </button>
                @if(request()->has('nama_pola'))
                    <a href="{{ route('shiftpattern.index') }}" 
                       class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1 active:scale-95"
                       title="Reset Pencarian">
                        <i class="ti ti-rotate text-sm"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- ================= 3. LIST CARD POLA SHIFT MODERN ================= -->
    <div class="space-y-3">
        @forelse ($patterns as $p)
            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Left Section: Icon, Title & Shift Timeline -->
                    <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                        <!-- Icon Box -->
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0 text-white shadow-2xs"
                             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-repeat"></i>
                        </div>

                        <!-- Details -->
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="flex items-center flex-wrap gap-2">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-mono font-bold bg-slate-800 text-white shadow-2xs">
                                    {{ $p->kode_pola }}
                                </span>
                                <h3 class="text-sm font-black text-slate-900 tracking-tight group-hover:text-emerald-700 transition">
                                    {{ $p->nama_pola }}
                                </h3>
                                
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200 shadow-2xs">
                                    {{ $p->jumlah_hari_siklus }} Hari Siklus
                                </span>

                                @if ($p->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                    </span>
                                @endif
                            </div>

                            @if($p->keterangan)
                                <p class="text-xs text-slate-500 leading-relaxed line-clamp-1">
                                    {{ $p->keterangan }}
                                </p>
                            @endif

                            <!-- Sequence of Shift Badges (Timeline Chips) -->
                            <div class="flex items-center flex-wrap gap-1 pt-0.5">
                                <span class="text-[11px] font-semibold text-slate-400 mr-1 flex items-center gap-1">
                                    <i class="ti ti-timeline text-xs"></i> Rotasi:
                                </span>
                                @foreach ($p->details as $d)
                                    @php
                                        $jk = $d->jamKerja;
                                        $jkColor = $jk->color ?? ($jk ? '#0d6efd' : '#64748b');
                                    @endphp
                                    @if ($jk)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold text-white shadow-2xs"
                                              style="background-color: {{ $jkColor }};"
                                              title="Hari {{ $d->urutan_hari }}: {{ $jk->nama_jam_kerja }} ({{ substr($jk->jam_masuk, 0, 5) }}-{{ substr($jk->jam_pulang, 0, 5) }})">
                                            <span>H{{ $d->urutan_hari }}:</span>
                                            <span class="truncate max-w-[100px]">{{ $jk->nama_jam_kerja }}</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-slate-100 text-slate-600 border border-slate-200"
                                              title="Hari {{ $d->urutan_hari }}: Libur / OFF">
                                            <span>H{{ $d->urutan_hari }}:</span>
                                            <span>OFF</span>
                                        </span>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Assigned Groups Meta -->
                            <div class="flex items-center flex-wrap gap-1.5 text-[11px] text-slate-400 pt-0.5">
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-users-group text-slate-400"></i>
                                    <span>Grup Pengguna:</span>
                                </span>
                                @if ($p->grup->isNotEmpty())
                                    @foreach ($p->grup as $g)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10.5px]">
                                            {{ $g->nama_grup }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="italic text-slate-400">Belum terpasang pada grup</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Action Buttons -->
                    <div class="flex items-center gap-2 self-end lg:self-center shrink-0 border-t lg:border-t-0 pt-3 lg:pt-0 border-slate-100 w-full lg:w-auto justify-end">
                        @can('shiftpattern.edit')
                            <a href="{{ route('shiftpattern.edit', Crypt::encrypt($p->id)) }}" 
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                                <i class="ti ti-edit text-sm text-emerald-600"></i>
                                <span>Edit Pola</span>
                            </a>
                        @endcan

                        @can('shiftpattern.delete')
                            <form action="{{ route('shiftpattern.destroy', Crypt::encrypt($p->id)) }}" 
                                  method="POST" 
                                  class="formDelete inline m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-8 h-8 rounded-lg border border-slate-200 hover:border-rose-200 hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                        title="Hapus Pola">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </form>
                        @endcan
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/90 p-12 text-center shadow-2xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                    <i class="ti ti-repeat-off"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800 mb-1">Belum Ada Pola Shift</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
                    Belum ada template pola perulangan shift yang dibuat. Klik tombol di bawah untuk membuat pola baru.
                </p>
                @can('shiftpattern.create')
                    <a href="{{ route('shiftpattern.create') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition hover:opacity-95 active:scale-95 cursor-pointer"
                       style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Pola Shift Baru</span>
                    </a>
                @endcan
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($patterns->hasPages())
        <div class="pt-2">
            {{ $patterns->links('v2.components.pagination') }}
        </div>
    @endif

</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        // Konfirmasi Hapus Pola
        $('.formDelete').submit(function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Pola Shift?',
                text: 'Template pola shift beserta konfigurasi siklus harian akan dihapus permanen.',
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
