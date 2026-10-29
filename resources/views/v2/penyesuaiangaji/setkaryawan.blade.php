@extends('v2.layouts.app')

@section('titlepage', 'Set Karyawan Penyesuaian Gaji')
@section('page_title', 'Rincian Penyesuaian Gaji Karyawan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalKaryawan = $detailpenyesuaian->count();
    $totalPenambah = $detailpenyesuaian->sum('penambah');
    $totalPengurang = $detailpenyesuaian->sum('pengurang');
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTIONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('penyesuaiangaji.index') }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition shadow-2xs">
                    <i class="ti ti-arrow-left text-sm"></i>
                </a>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Penyesuaian Gaji: {{ getNamabulan($penyesuaiangaji->bulan) }} {{ $penyesuaiangaji->tahun }}</span>
                </h1>
            </div>
            <p class="text-xs text-slate-500 ml-8">
                Kode Periode: <strong class="text-slate-800 font-mono">{{ $penyesuaiangaji->kode_penyesuaian_gaji }}</strong> &bull; Kelola daftar nama karyawan yang menerima koreksi upah penambah atau pemotong
            </p>
        </div>

        <!-- Right Side: Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            @can('penyesuaiangaji.edit')
                <!-- Tambah Karyawan -->
                <button type="button" id="btnAddKaryawan" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Karyawan</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE STATISTICS BANNER (CONSISTENT WITH V2 STANDARD) ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        <!-- Background watermark icon -->
        <div class="absolute -right-8 -bottom-10 text-white/5 pointer-events-none select-none">
            <i class="ti ti-adjustments-horizontal text-[190px]"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            <!-- Segment 1: Total Karyawan -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Karyawan
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25">
                            Orang
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($totalKaryawan) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-users text-xs opacity-80"></i>
                        <span>Penerima Penyesuaian</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        Terdaftar
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: Total Koreksi Penambah -->
            <div class="flex-1 min-w-[150px] sm:min-w-[180px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Penambah
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-400/25 text-emerald-100 border border-emerald-300/30">
                            (+) Tambah
                        </span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-white tracking-tight font-mono mt-1.5">
                        Rp {{ formatAngka($totalPenambah) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-plus text-xs opacity-80"></i>
                        <span>Koreksi Tambahan Upah</span>
                    </span>
                    <span class="font-black text-emerald-200 text-[10px] bg-emerald-500/20 px-2 py-0.5 rounded-md">
                        Penambah
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Total Koreksi Pemotong -->
            <div class="flex-1 min-w-[150px] sm:min-w-[180px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-rose-200 truncate">
                            Total Pemotong
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500/25 text-rose-100 border border-rose-400/30">
                            (-) Potong
                        </span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-black text-rose-100 tracking-tight font-mono mt-1.5">
                        Rp {{ formatAngka($totalPengurang) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-rose-200/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-minus text-xs opacity-80"></i>
                        <span>Koreksi Potongan Upah</span>
                    </span>
                    <span class="font-black text-rose-200 text-[10px] bg-rose-500/20 px-2 py-0.5 rounded-md">
                        Pengurang
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. TABEL DATA RINCIAN PENYESUAIAN GAJI KARYAWAN ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        
        <!-- Table Top Banner Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-users"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Karyawan Penerima Penyesuaian Gaji</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $totalKaryawan }} data terdaftar
            </span>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse">
                <!-- Table Header -->
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15 whitespace-nowrap"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-3.5 w-28 text-white/90 border-0 border-t-0">NIK</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Nama Karyawan</th>
                        <th class="py-2.5 px-4 text-right w-36 text-white/90 border-0 border-t-0">Penambah (Rp)</th>
                        <th class="py-2.5 px-4 text-right w-36 text-white/90 border-0 border-t-0">Pengurang (Rp)</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Keterangan</th>
                        <th class="py-2.5 px-3 text-center w-24 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white whitespace-nowrap">
                    @forelse ($detailpenyesuaian as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- Nomor Baris -->
                            <td class="py-2.5 px-3 text-center text-slate-400 font-bold text-xs">
                                <span class="w-5 h-5 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <!-- NIK -->
                            <td class="py-2.5 px-3.5">
                                <span class="text-xs font-semibold text-slate-600 font-mono">
                                    {{ $item->nik_show ?? $item->nik }}
                                </span>
                            </td>

                            <!-- Nama Karyawan -->
                            <td class="py-2.5 px-4">
                                <span class="font-bold text-slate-900 group-hover:text-emerald-700 transition text-xs sm:text-sm">
                                    {{ $item->nama_karyawan }}
                                </span>
                            </td>

                            <!-- Penambah -->
                            <td class="py-2.5 px-4 text-right">
                                @if ($item->penambah > 0)
                                    <span class="font-bold text-emerald-700 font-mono text-xs">
                                        + Rp {{ formatAngka($item->penambah) }}
                                    </span>
                                @else
                                    <span class="text-slate-300 font-mono text-xs">0</span>
                                @endif
                            </td>

                            <!-- Pengurang -->
                            <td class="py-2.5 px-4 text-right">
                                @if ($item->pengurang > 0)
                                    <span class="font-bold text-rose-600 font-mono text-xs">
                                        - Rp {{ formatAngka($item->pengurang) }}
                                    </span>
                                @else
                                    <span class="text-slate-300 font-mono text-xs">0</span>
                                @endif
                            </td>

                            <!-- Keterangan -->
                            <td class="py-2.5 px-4">
                                <span class="text-slate-600 text-xs truncate max-w-xs block" title="{{ $item->keterangan }}">
                                    {{ $item->keterangan ?? '-' }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="py-2.5 px-3 text-center">
                                <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                    @can('penyesuaiangaji.edit')
                                        <button type="button" 
                                                class="w-6.5 h-6.5 rounded-md hover:bg-white text-emerald-600 hover:text-emerald-700 flex items-center justify-center transition-all cursor-pointer btnEditKaryawan" 
                                                data-nik="{{ Crypt::encrypt($item->nik) }}"
                                                title="Edit Rincian">
                                            <i class="ti ti-edit text-xs"></i>
                                        </button>
                                    @endcan

                                    @can('penyesuaiangaji.delete')
                                        <form action="{{ route('penyesuaiangaji.deletekaryawan', [Crypt::encrypt($penyesuaiangaji->kode_penyesuaian_gaji), Crypt::encrypt($item->nik)]) }}" method="POST" class="inline m-0 formDeleteKaryawan">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-6.5 h-6.5 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition-all cursor-pointer" 
                                                    title="Hapus Karyawan">
                                                <i class="ti ti-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 px-4 text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-3">
                                    <i class="ti ti-users-off"></i>
                                </div>
                                <h4 class="text-xs font-bold text-slate-800">Belum ada karyawan yang ditambahkan</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5 max-w-sm mx-auto">
                                    Tambahkan karyawan yang menerima penyesuaian nominal gaji untuk periode {{ getNamabulan($penyesuaiangaji->bulan) }} {{ $penyesuaiangaji->tahun }}.
                                </p>
                                @can('penyesuaiangaji.edit')
                                    <button type="button" id="btnEmptyAddKaryawan" 
                                            class="mt-3 inline-flex items-center gap-1.5 px-3.5 py-1.5 text-white font-bold rounded-xl text-xs shadow-xs transition active:scale-95 cursor-pointer"
                                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                        <i class="ti ti-plus text-xs"></i>
                                        <span>Tambah Karyawan</span>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= MODAL UNIVERSAL PENYESUAIAN GAJI KARYAWAN V2 ================= -->
<div id="modalSetKaryawan" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-user-plus text-lg" id="modalKaryawanIcon"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="modalKaryawanTitle">
                        Penyesuaian Gaji Karyawan
                    </h3>
                    <p class="text-[11px] text-slate-500" id="modalKaryawanSubtitle">
                        Formulir penyesuaian penambah & pengurang gaji
                    </p>
                </div>
            </div>
            <button type="button" id="btnCloseModalKaryawan" 
                    class="w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body Content -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalKaryawanContent">
            <div class="py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                <p class="text-xs font-semibold">Memuat formulir...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        const kodePenyesuaian = "{{ Crypt::encrypt($penyesuaiangaji->kode_penyesuaian_gaji) }}";
        const $modal = $('#modalSetKaryawan');
        const $modalTitle = $('#modalKaryawanTitle');
        const $modalSubtitle = $('#modalKaryawanSubtitle');
        const $modalIcon = $('#modalKaryawanIcon');
        const $modalContent = $('#modalKaryawanContent');

        function openModal(title, subtitle, icon) {
            $modalTitle.text(title);
            $modalSubtitle.text(subtitle);
            $modalIcon.attr('class', 'ti ' + (icon || 'ti-user-plus') + ' text-lg');
            $modalContent.html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
                </div>
            `);
            $modal.removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeModal() {
            $modal.addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        $('#btnCloseModalKaryawan').on('click', closeModal);
        $modal.on('click', function(e) {
            if (e.target === this) closeModal();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$modal.hasClass('hidden')) {
                closeModal();
            }
        });

        // 1. Tambah Karyawan Modal
        $('#btnAddKaryawan, #btnEmptyAddKaryawan').on('click', function(e) {
            e.preventDefault();
            openModal('Tambah Karyawan', 'Tentukan nominal penambah, pengurang dan keterangannya', 'ti-user-plus');

            $.ajax({
                url: `{{ url('/penyesuaiangaji') }}/${kodePenyesuaian}/addkaryawan`,
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
                    initPlugins();
                },
                error: function(xhr) {
                    $modalContent.html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat form tambah karyawan.</p>
                            <p class="mt-1">${xhr.responseText || 'Terjadi kesalahan.'}</p>
                        </div>
                    `);
                }
            });
        });

        // 2. Edit Karyawan Modal
        $(document).on('click', '.btnEditKaryawan', function(e) {
            e.preventDefault();
            const nik = $(this).data('nik');
            openModal('Edit Penyesuaian Karyawan', 'Perbarui nilai penambah, pengurang atau keterangan', 'ti-edit');

            $.ajax({
                url: `{{ url('/penyesuaiangaji') }}/${kodePenyesuaian}/${nik}/editkaryawan`,
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
                    initPlugins();
                },
                error: function(xhr) {
                    $modalContent.html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat form edit.</p>
                            <p class="mt-1">${xhr.responseText || 'Data tidak ditemukan.'}</p>
                        </div>
                    `);
                }
            });
        });

        // 3. Delete Single Karyawan (SweetAlert2)
        $(document).on('submit', '.formDeleteKaryawan', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Penyesuaian Karyawan?',
                text: 'Data penyesuaian gaji karyawan ini akan dihapus permanen dari periode ini!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Data',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl font-bold text-xs px-4 py-2.5',
                    cancelButton: 'rounded-xl font-bold text-xs px-4 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        function initPlugins() {
            if ($.fn.select2) {
                $('.select2ModalNik').select2({
                    placeholder: '-- Pilih Karyawan --',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modalSetKaryawan')
                });

                $('.select2ModalNikEdit').select2({
                    width: '100%',
                    dropdownParent: $('#modalSetKaryawan')
                });
            }

            if ($.fn.maskMoney) {
                $('.modal-money-input').maskMoney({
                    thousands: '.',
                    decimal: ',',
                    precision: 0,
                    allowZero: true
                });
            }
        }
    });
</script>
@endpush
