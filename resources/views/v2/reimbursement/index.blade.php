@extends('v2.layouts.app')

@section('titlepage', 'Pengajuan Reimbursement')
@section('page_title', 'Pengajuan Reimbursement')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $userRole = auth()->user()->getRoleNames()->first();
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-receipt-refund text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Pengajuan Reimbursement</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola dan pantau histori pengajuan klaim biaya operasional, medis, dan dinas karyawan
            </p>
        </div>

        <!-- Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Reimbursement</span>
            </nav>

            <!-- Actions Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('approvallayer.index')
                    <a href="{{ route('approvallayer.index', ['feature' => 'REIMBURSEMENT']) }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-slate-900 text-xs font-bold transition shadow-2xs cursor-pointer">
                        <i class="ti ti-settings text-slate-500"></i>
                        <span>Konfigurasi Approval</span>
                    </a>
                @endcan

                @can('reimbursement.create')
                    <a href="{{ route('reimbursement.create') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                       style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Buat Pengajuan Baru</span>
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('reimbursement.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2.5">
                
                <!-- Search Input -->
                <div class="{{ $userRole == 'karyawan' ? 'lg:col-span-3' : 'lg:col-span-2' }} relative">
                    <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <input type="text" name="nama_karyawan" value="{{ request('nama_karyawan') }}" 
                           placeholder="{{ $userRole == 'karyawan' ? 'Cari keterangan / no reimbursement...' : 'Cari Nama Karyawan / NIK...' }}" 
                           class="w-full h-10 pl-9 pr-3.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                @if($userRole != 'karyawan')
                    <!-- Cabang -->
                    <div>
                        <select name="kode_cabang" class="w-full h-10 px-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}" {{ request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                    {{ $c->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Departemen -->
                    <div>
                        <select name="kode_dept" class="w-full h-10 px-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                            <option value="">Semua Dept</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                    {{ $d->nama_dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Status Filter -->
                <div>
                    <select name="status" class="w-full h-10 px-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="P" {{ request('status') == 'P' ? 'selected' : '' }}>Pending</option>
                        <option value="A" {{ request('status') == 'A' ? 'selected' : '' }}>Approved</option>
                        <option value="D" {{ request('status') == 'D' ? 'selected' : '' }}>Dibayar (Lunas)</option>
                        <option value="R" {{ request('status') == 'R' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Action Button Filter & Reset -->
                <div class="flex items-center gap-1.5">
                    <button type="submit" 
                            class="flex-1 h-10 inline-flex items-center justify-center gap-1.5 px-4 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if (request()->hasAny(['nama_karyawan', 'kode_cabang', 'kode_dept', 'status', 'dari', 'sampai', 'nik']))
                        <a href="{{ route('reimbursement.index') }}" 
                           class="h-10 w-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer shrink-0"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Menampilkan <strong>{{ $reimbursement->count() }}</strong> dari <strong>{{ $reimbursement->total() }}</strong> total pengajuan reimbursement</span>
        </div>
    </div>

    <!-- ================= 3. LIST CARD FULL WIDTH ================= -->
    <div class="space-y-2.5">
        @forelse ($reimbursement as $index => $d)
            @php
                $encryptedId = Crypt::encrypt($d->id);
                $statusBadge = 'bg-amber-50 text-amber-700 border-amber-200/80';
                $statusText = 'Pending';
                if ($d->status == 'A') {
                    $statusBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200/80';
                    $statusText = 'Approved';
                } elseif ($d->status == 'D') {
                    $statusBadge = 'bg-sky-50 text-sky-700 border-sky-200/80';
                    $statusText = 'Dibayar';
                } elseif ($d->status == 'R') {
                    $statusBadge = 'bg-rose-50 text-rose-700 border-rose-200/80';
                    $statusText = 'Ditolak';
                } elseif ($d->status == 'B') {
                    $statusBadge = 'bg-slate-100 text-slate-600 border-slate-200';
                    $statusText = 'Batal';
                }
            @endphp
            <div class="bg-white rounded-2xl px-4 py-3 sm:px-4.5 sm:py-3.5 border border-slate-200/90 hover:border-emerald-300/80 shadow-2xs hover:shadow-xs transition-all duration-200 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    
                    <!-- Left: Identity & Employee Info -->
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <!-- Number Badge -->
                        <div class="hidden sm:flex w-6 h-6 rounded-md bg-slate-100 text-slate-500 text-[10.5px] font-bold items-center justify-center shrink-0 border border-slate-200/80">
                            {{ $loop->iteration + $reimbursement->firstItem() - 1 }}
                        </div>

                        <!-- Employee Photo Thumbnail -->
                        <div class="relative shrink-0">
                            @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                <img src="{{ getfotoKaryawan($d->foto) }}" alt="{{ $d->nama_karyawan }}" 
                                     class="w-10 h-11 sm:w-11 sm:h-12 rounded-xl object-cover border border-slate-200 shadow-2xs group-hover:scale-102 transition duration-200">
                            @else
                                <div class="w-10 h-11 sm:w-11 sm:h-12 rounded-xl bg-slate-100 border border-slate-200/80 flex flex-col items-center justify-center text-slate-700 font-bold text-sm shadow-2xs">
                                    {{ strtoupper(substr($d->nama_karyawan, 0, 1)) }}
                                </div>
                            @endif

                            @if(($d->status_aktif_karyawan ?? '1') == '1')
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[8px] ring-2 ring-white" title="Status Aktif">
                                    <i class="ti ti-check"></i>
                                </span>
                            @else
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-rose-500 text-white flex items-center justify-center text-[8px] ring-2 ring-white" title="Status Nonaktif">
                                    <i class="ti ti-x"></i>
                                </span>
                            @endif
                        </div>

                        <!-- Name, NIK, No Reimbursement, Dept & Cabang Badges -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="font-mono font-black text-emerald-800 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded text-[11px]">
                                    {{ $d->no_reimbursement }}
                                </span>
                                
                                <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition truncate" title="{{ $d->nama_karyawan }}">
                                    {{ $d->nama_karyawan }}
                                </h4>

                                <span class="font-mono text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded text-[10.5px]">
                                    {{ $d->nik_show ?: $d->nik }}
                                </span>

                                @if(!empty($d->nama_cabang))
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200/80">
                                        {{ $d->nama_cabang }}
                                    </span>
                                @endif

                                @if(!empty($d->nama_dept))
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200/70">
                                        {{ $d->nama_dept }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500 mt-1">
                                <span class="text-slate-500 flex items-center gap-1">
                                    <i class="ti ti-calendar text-xs text-slate-400"></i>
                                    Tgl Pengajuan: <strong class="text-slate-700 font-semibold">{{ date('d/m/Y', strtotime($d->tanggal_pengajuan)) }}</strong>
                                </span>
                                @if($d->catatan)
                                    <span>&bull;</span>
                                    <span class="text-slate-500 italic truncate max-w-xs sm:max-w-md">
                                        "{{ $d->catatan }}"
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right: Total Nominal, Status Badge & Actions -->
                    <div class="flex items-center justify-between lg:justify-end gap-3 shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        <!-- Nominal -->
                        <div class="text-left lg:text-right">
                            <span class="text-xs sm:text-sm font-black font-mono text-emerald-700 block">
                                Rp {{ number_format($d->total_nominal, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium block">Total Klaim</span>
                        </div>

                        <!-- Status Badge -->
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-bold border {{ $statusBadge }}">
                            @if($d->status == 'P')
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            @elseif($d->status == 'A')
                                <i class="ti ti-check text-xs"></i>
                            @elseif($d->status == 'D')
                                <i class="ti ti-cash text-xs"></i>
                            @elseif($d->status == 'R')
                                <i class="ti ti-x text-xs"></i>
                            @endif
                            {{ $statusText }}
                        </span>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-1">
                            <!-- Detail Modal Trigger -->
                            <button type="button" 
                                    class="btnShowDetailReimbursement p-2 rounded-xl bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200/80 transition active:scale-95 cursor-pointer shadow-2xs flex items-center gap-1 text-xs font-bold"
                                    data-id="{{ $encryptedId }}"
                                    title="Lihat Detail Pengajuan">
                                <i class="ti ti-eye text-sm"></i>
                                <span class="hidden xl:inline text-[11px]">Detail</span>
                            </button>

                            <!-- Print PDF -->
                            <a href="{{ route('reimbursement.print', $encryptedId) }}" target="_blank"
                               class="p-2 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200 transition active:scale-95 cursor-pointer shadow-2xs"
                               title="Cetak Formulir">
                                <i class="ti ti-printer text-sm"></i>
                            </a>

                            @if($d->status == 'P')
                                @can('reimbursement.delete')
                                    <form action="{{ route('reimbursement.delete', $encryptedId) }}" method="POST" class="deleteform m-0 inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btnDeleteReimbursementV2 p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                                title="Hapus Pengajuan">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center text-slate-400 border border-slate-200/90 shadow-2xs">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                    <i class="ti ti-receipt-off"></i>
                </div>
                <h5 class="text-sm font-black text-slate-800">Belum Ada Pengajuan Reimbursement</h5>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Belum ditemukan histori pengajuan yang sesuai dengan kriteria filter pencarian.
                </p>
                @can('reimbursement.create')
                    <a href="{{ route('reimbursement.create') }}" 
                       class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all active:scale-95 cursor-pointer"
                       style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Buat Pengajuan Baru</span>
                    </a>
                @endcan
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($reimbursement->hasPages())
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            {{ $reimbursement->links('v2.components.pagination') }}
        </div>
    @endif

</div>

<!-- ================= UNIVERSAL MODAL DETAIL & APPROVAL REIMBURSEMENT ================= -->
<div id="modalDetailReimbursementV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-4xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-3.5 border-b border-slate-200/80 bg-white flex items-center justify-between relative shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-700 bg-slate-100 border border-slate-200">
                    <i class="ti ti-receipt text-base"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base" id="modalDetailReimbursementTitle">
                        Detail pengajuan reimbursement
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        Rincian item klaim, bukti nota, dan riwayat persetujuan
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseModalDetailReimbursement w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center border border-slate-200 transition cursor-pointer">
                <i class="ti ti-x text-sm"></i>
            </button>
        </div>

        <!-- Body Content (Loaded via AJAX) -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalDetailReimbursementContent">
            <div class="py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                <p class="text-xs font-semibold">Memuat rincian pengajuan...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        function showModalDetail(url) {
            $('#modalDetailReimbursementV2').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('#modalDetailReimbursementContent').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat rincian pengajuan...</p>
                </div>
            `);
            $('#modalDetailReimbursementContent').load(url);
        }

        function closeModalDetail() {
            $('#modalDetailReimbursementV2').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            $('#modalDetailReimbursementContent').html('');
        }

        // Click Event to open modal
        $(document).on('click', '.btnShowDetailReimbursement', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            showModalDetail(`/reimbursement/${id}/show`);
        });

        $(document).on('click', '.btnCloseModalDetailReimbursement', closeModalDetail);

        $('#modalDetailReimbursementV2').click(function(e) {
            if (e.target === this) closeModalDetail();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$('#modalDetailReimbursementV2').hasClass('hidden')) {
                closeModalDetail();
            }
        });

        // Cancel Approval Confirmation
        $(document).on('click', '.btnCancelApproveReimbursement', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Batalkan Persetujuan?',
                text: 'Status approval terakhir akan dibatalkan dan pengajuan dikembalikan ke tahap sebelumnya.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Kembali',
                reverseButtons: true,
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
        });

        // Delete Confirmation
        $(document).on('click', '.btnDeleteReimbursementV2', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Pengajuan Reimbursement?',
                text: 'Pengajuan yang dihapus tidak dapat dipulihkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '{{ $themeColor1 }}',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
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
        });
    });
</script>
@endpush
