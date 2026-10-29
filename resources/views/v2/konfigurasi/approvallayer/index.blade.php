@extends('v2.layouts.app')

@section('titlepage', 'Approval Layer')
@section('page_title', 'Konfigurasi Approval Layer')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';

    $groupedLayers = $approvalLayers->groupBy(function($item) {
        return $item->feature . '|' . ($item->kode_cabang ?? 'ALL') . '|' . ($item->kode_dept ?? 'ALL') . '|' . ($item->kode_jabatan ?? 'ALL');
    });

    $totalAlur = $groupedLayers->count();
    $totalIzin = $approvalLayers->where('feature', 'IZIN')->groupBy(function($i) { return ($i->kode_cabang ?? 'ALL') . '|' . ($i->kode_dept ?? 'ALL') . '|' . ($i->kode_jabatan ?? 'ALL'); })->count();
    $totalReimburse = $approvalLayers->where('feature', 'REIMBURSEMENT')->groupBy(function($i) { return ($i->kode_cabang ?? 'ALL') . '|' . ($i->kode_dept ?? 'ALL') . '|' . ($i->kode_jabatan ?? 'ALL'); })->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-git-merge text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Approval Layer</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Atur struktur hirarki persetujuan bertingkat untuk modul Izin & Absensi serta Reimbursement secara modular
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
                <span class="font-bold text-slate-800">Approval Layer</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('approvallayer.create')
                    <a href="{{ route('approvallayer.create', ['feature' => $feature]) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                       style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Konfigurasi</span>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE METRICS CARD ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-git-merge text-[190px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            <!-- Segment 1: Total Alur Konfigurasi -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Alur Skema
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Skema
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalAlur) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-layers-intersect text-xs opacity-80"></i>
                        <span>Total Gabungan</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        Aktif
                    </span>
                </div>
            </div>

            <!-- Divider -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Modul Izin & Cuti -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Izin / Absen / Cuti
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            IZIN
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalIzin) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-folder-check text-xs opacity-80"></i>
                        <span>Pengajuan Absensi</span>
                    </span>
                    <span class="font-bold text-emerald-100 text-[11px]">
                        Target Hirarki
                    </span>
                </div>
            </div>

            <!-- Divider -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Reimbursement -->
            <div class="flex-1 min-w-[140px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Reimbursement
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            KLAIM
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalReimburse) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-receipt-refund text-xs opacity-80"></i>
                        <span>Klaim Keuangan</span>
                    </span>
                    <span class="font-bold text-emerald-100 text-[11px]">
                        Target Hirarki
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. FILTER TABS & TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-3 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Feature Filter Pills -->
        <div class="flex items-center gap-1.5 p-1 bg-slate-100/80 rounded-xl w-full sm:w-auto">
            <a href="{{ route('approvallayer.index') }}" 
               class="flex-1 sm:flex-none px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ empty($feature) ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                Semua Fitur
            </a>
            <a href="{{ route('approvallayer.index', ['feature' => 'IZIN']) }}" 
               class="flex-1 sm:flex-none px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $feature === 'IZIN' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                <i class="ti ti-folder-check text-sm mr-1"></i> Izin & Absen
            </a>
            <a href="{{ route('approvallayer.index', ['feature' => 'REIMBURSEMENT']) }}" 
               class="flex-1 sm:flex-none px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $feature === 'REIMBURSEMENT' ? 'bg-white text-sky-700 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                <i class="ti ti-receipt-refund text-sm mr-1"></i> Reimbursement
            </a>
        </div>

        <div class="text-xs text-slate-500 font-medium px-2 flex items-center gap-1.5">
            <i class="ti ti-info-circle text-slate-400"></i>
            <span>Menampilkan <b>{{ $groupedLayers->count() }}</b> konfigurasi skema approval</span>
        </div>
    </div>

    <!-- ================= 4. APPROVAL LAYER CARDS GRID ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
        @forelse ($groupedLayers as $key => $layers)
            @php
                $first = $layers->first();
                
                $c = $cabangs->firstWhere('kode_cabang', $first->kode_cabang);
                $nama_cabang = $c ? $c->nama_cabang : 'Semua Cabang';
                
                $d = $departemens->firstWhere('kode_dept', $first->kode_dept);
                $nama_dept = $d ? $d->nama_dept : 'Semua Departemen';
                
                $j = $jabatans->firstWhere('kode_jabatan', $first->kode_jabatan);
                $nama_jabatan = $j ? $j->nama_jabatan : 'Semua Jabatan';
                
                $layersSorted = $layers->sortBy('level');
                $isReimburse = $first->feature === 'REIMBURSEMENT';
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                <!-- Card Header -->
                <div class="p-4 border-b border-slate-100 bg-gradient-to-r {{ $isReimburse ? 'from-sky-50/60 to-white' : 'from-emerald-50/60 to-white' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shadow-xs shrink-0 {{ $isReimburse ? 'bg-sky-500 text-white' : 'text-white' }}"
                                 style="{{ !$isReimburse ? 'background: linear-gradient(135deg, ' . $themeColor1 . ', ' . $themeColor2 . ');' : '' }}">
                                <i class="ti ti-{{ $isReimburse ? 'receipt-refund' : 'git-merge' }}"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-slate-800 tracking-tight uppercase line-clamp-1">
                                    {{ $nama_cabang }}
                                </h3>
                                <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-white text-slate-700 border border-slate-200 shadow-2xs">
                                        {{ $nama_dept }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-white text-slate-700 border border-slate-200 shadow-2xs">
                                        {{ $nama_jabatan }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Feature Badge -->
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shrink-0 {{ $isReimburse ? 'bg-sky-100 text-sky-800 border border-sky-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                            {{ $first->feature }}
                        </span>
                    </div>
                </div>

                <!-- Card Body: Step Hierarchy Timeline -->
                <div class="p-4 flex-1">
                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">
                        <span>Urutan Approval</span>
                        <span class="text-slate-600 font-extrabold">{{ $layersSorted->count() }} Level</span>
                    </div>

                    <div class="space-y-2.5 relative">
                        @foreach ($layersSorted as $l)
                            <div class="relative flex items-center gap-3">
                                <!-- Connector line -->
                                @if (!$loop->last)
                                    <div class="absolute left-3.5 top-6 bottom-[-14px] w-[2px] bg-slate-200 z-0"></div>
                                @endif

                                <!-- Step Circle -->
                                <div class="w-7 h-7 rounded-lg text-white font-black text-[11px] flex items-center justify-center shrink-0 z-10 shadow-2xs"
                                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                    {{ $l->level }}
                                </div>

                                <!-- Role Box -->
                                <div class="flex-1 min-w-0 p-2.5 rounded-xl bg-slate-50 border border-slate-200/70 hover:border-slate-300 transition flex items-center justify-between">
                                    <div class="min-w-0 pr-2">
                                        <div class="text-xs font-bold text-slate-800 truncate">
                                            {{ $l->role_name }}
                                        </div>
                                        <div class="text-[10.5px] text-slate-400 font-medium">
                                            Level Persetujuan {{ $l->level }}
                                        </div>
                                    </div>
                                    <div class="w-6 h-6 rounded-md bg-white border border-slate-200 flex items-center justify-center text-slate-400 text-xs shrink-0">
                                        <i class="ti ti-user-check"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Card Footer Actions -->
                <div class="p-3 bg-slate-50/70 border-t border-slate-100 flex items-center justify-between gap-2">
                    <span class="text-[11px] text-slate-400 font-medium pl-1">
                        Aksi Konfigurasi
                    </span>

                    <div class="flex items-center gap-1.5">
                        @can('approvallayer.edit')
                            <a href="{{ route('approvallayer.editGroup', ['cabang' => $first->kode_cabang ?? 'ALL', 'dept' => $first->kode_dept ?? 'ALL', 'jabatan' => $first->kode_jabatan ?? 'ALL', 'feature' => $first->feature]) }}" 
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 hover:text-slate-900 text-xs font-bold transition shadow-2xs cursor-pointer active:scale-95">
                                <i class="ti ti-edit text-sm text-slate-500"></i>
                                <span>Edit Alur</span>
                            </a>
                        @endcan

                        @can('approvallayer.delete')
                            <form method="POST" action="{{ route('approvallayer.destroyGroup', ['cabang' => $first->kode_cabang ?? 'ALL', 'dept' => $first->kode_dept ?? 'ALL', 'jabatan' => $first->kode_jabatan ?? 'ALL', 'feature' => $first->feature]) }}" class="m-0 p-0 delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        class="btnDeleteGroup w-8 h-8 rounded-xl bg-white border border-slate-200 text-rose-600 hover:bg-rose-50 hover:border-rose-200 flex items-center justify-center text-sm transition shadow-2xs cursor-pointer active:scale-95"
                                        title="Hapus Konfigurasi">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 px-4 text-center bg-white rounded-2xl border border-dashed border-slate-300">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-3xl mb-3">
                    <i class="ti ti-git-merge"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-800">Belum Ada Alur Persetujuan</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 mb-4">
                    Belum ada konfigurasi approval layer yang ditentukan untuk filter ini. Silakan buat alur baru.
                </p>
                @can('approvallayer.create')
                    <a href="{{ route('approvallayer.create', ['feature' => $feature]) }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition active:scale-95"
                       style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Konfigurasi Pertama</span>
                    </a>
                @endcan
            </div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.btnDeleteGroup').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            Swal.fire({
                title: 'Hapus Alur Approval?',
                text: "Konfigurasi alur approval untuk target ini akan dihapus secara permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-4 py-2 text-xs font-bold',
                    cancelButton: 'rounded-xl px-4 py-2 text-xs font-bold'
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
