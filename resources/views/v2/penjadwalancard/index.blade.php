@extends('v2.layouts.app')

@section('titlepage', 'Penjadwalan Card')
@section('page_title', 'Penjadwalan Card (Shift Roster)')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalCards = $jadwalcards->total();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-cards text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Penjadwalan Card</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola setting periode jadwal kerja dan atur distribusi shift karyawan dengan Kanban Card Board
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
                <span class="font-bold text-slate-800">Penjadwalan Card</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('penjadwalancard.create')
                    <!-- Buat Setting Periode Baru -->
                    <button type="button" id="btnCreate" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Buat Setting Periode</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER BAR STANDAR V2 (2-BARIS KONSISTEN) ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('penjadwalancard.index') }}" method="GET" class="space-y-3">
            
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

            <!-- BARIS 2: CABANG, DEPARTEMEN & TOMBOL AKSI -->
            <div class="flex flex-col lg:flex-row items-center gap-2.5 sm:gap-3 w-full">
                
                <!-- Cabang Filter -->
                <div class="w-full lg:flex-1">
                    <select name="kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ $c->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Departemen Filter -->
                <div class="w-full lg:flex-1">
                    <select name="kode_dept" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Departemen</option>
                        @foreach ($departemen as $d)
                            <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
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
                    @if(request()->hasAny(['bulan', 'tahun', 'kode_cabang', 'kode_dept']))
                        <a href="{{ route('penjadwalancard.index') }}" 
                           class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1 active:scale-95"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>
    </div>

    <!-- ================= 4. LIST ITEM PERIODE JADWAL CARD ================= -->
    <div class="space-y-3">
        @forelse ($jadwalcards as $jc)
            @php
                $startDate = \Carbon\Carbon::parse($jc->dari);
                $endDate = \Carbon\Carbon::parse($jc->sampai);
                $duration = $startDate->diffInDays($endDate) + 1;
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
                                    {{ $jc->nama_jadwal }}
                                </h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                    {{ $duration }} Hari
                                </span>
                            </div>

                            <!-- Periode Tanggal -->
                            <div class="text-xs font-semibold text-slate-600 flex items-center gap-1.5 mb-2">
                                <i class="ti ti-calendar-event text-emerald-600 text-sm"></i>
                                <span>{{ DateToIndo($jc->dari) }} &mdash; {{ DateToIndo($jc->sampai) }}</span>
                            </div>

                            <!-- Meta Chips: Dept, Cabang, Creator -->
                            <div class="flex items-center flex-wrap gap-1.5 text-[11px]">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium">
                                    <i class="ti ti-building text-slate-400"></i>
                                    <span>{{ $jc->departemen->nama_dept ?? 'Semua Departemen' }}</span>
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-medium">
                                    <i class="ti ti-map-pin text-slate-400"></i>
                                    <span>{{ $jc->cabang->nama_cabang ?? 'Semua Cabang' }}</span>
                                </span>
                                @if ($jc->creator)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-50 text-slate-400 font-medium">
                                        <i class="ti ti-user-check text-slate-400"></i>
                                        <span>Oleh: {{ $jc->creator->name ?? '-' }}</span>
                                    </span>
                                @endif
                                @if ($jc->keterangan)
                                    <span class="text-slate-400 italic text-[10.5px]">
                                        &bull; {{ $jc->keterangan }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan: Action Buttons -->
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0 border-t md:border-t-0 pt-3 md:pt-0 border-slate-100 w-full md:w-auto justify-end">
                        @can('penjadwalancard.atur')
                            <a href="{{ route('penjadwalancard.atur', Crypt::encrypt($jc->id)) }}" 
                               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-white font-bold text-xs rounded-xl shadow-xs transition hover:opacity-95 active:scale-95 cursor-pointer"
                               style="background-color: {{ $themeColor1 }};">
                                <i class="ti ti-cards text-sm"></i>
                                <span>Atur Shift</span>
                            </a>
                        @endcan

                        @can('penjadwalancard.edit')
                            <button type="button" 
                                    class="btnEdit w-8 h-8 rounded-lg border border-slate-200 hover:border-slate-300 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                    data-id="{{ Crypt::encrypt($jc->id) }}"
                                    title="Edit Periode">
                                <i class="ti ti-pencil text-sm"></i>
                            </button>
                        @endcan

                        @can('penjadwalancard.delete')
                            <form action="{{ route('penjadwalancard.destroy', Crypt::encrypt($jc->id)) }}" 
                                  method="POST" 
                                  class="formDelete inline m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-8 h-8 rounded-lg border border-slate-200 hover:border-rose-200 hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                        title="Hapus Setting">
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
                    <i class="ti ti-calendar-off"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800 mb-1">Belum Ada Setting Periode</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">
                    Belum ada setting periode jadwal card yang dibuat. Klik tombol di bawah untuk membuat jadwal shift periode baru.
                </p>
                @can('penjadwalancard.create')
                    <button type="button" class="btnTambahModal inline-flex items-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition hover:opacity-95 active:scale-95 cursor-pointer"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Buat Setting Periode Baru</span>
                    </button>
                @endcan
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($jadwalcards->hasPages())
        <div class="pt-2">
            {{ $jadwalcards->links('v2.components.pagination') }}
        </div>
    @endif

</div>

<!-- Universal V2 Modal for Create & Edit -->
<div class="modal fade" id="modalFormCardV2" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-cards"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalTitleCard">Setting Periode Jadwal</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalSubtitleCard">Kelola periode jadwal card dan shift karyawan</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>

            <!-- Modal Body Container -->
            <div class="p-6 bg-white" id="loadModalContentCard">
                <!-- Dynamic form loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        function loadingModal() {
            $('#loadModalContentCard').html(`
                <div class="py-10 text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-3 border-emerald-600 border-t-transparent"></div>
                    <p class="text-xs text-slate-500 font-medium mt-2.5">Memuat form...</p>
                </div>
            `);
        }

        // Buka Modal Create
        $('#btnCreate, .btnTambahModal').click(function(e) {
            e.preventDefault();
            $('#modalTitleCard').text('Buat Setting Periode Jadwal Card');
            $('#modalFormCardV2').modal('show');
            loadingModal();
            $('#loadModalContentCard').load("{{ route('penjadwalancard.create') }}");
        });

        // Buka Modal Edit
        $('.btnEdit').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#modalTitleCard').text('Edit Setting Periode Jadwal Card');
            $('#modalFormCardV2').modal('show');
            loadingModal();
            $('#loadModalContentCard').load(`/penjadwalancard/${id}/edit`);
        });

        // Konfirmasi Hapus
        $('.formDelete').submit(function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus setting periode jadwal ini beserta alokasi shift karyawannya?',
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
