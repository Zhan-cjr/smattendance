@extends('v2.layouts.app')

@section('titlepage', 'Jenis Tunjangan')
@section('page_title', 'Master Jenis Tunjangan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalTunjangan = $jenistunjangan->count();
    $totalTetap = $jenistunjangan->where('status_tunjangan', '1')->count();
    $totalTidakTetap = $jenistunjangan->where('status_tunjangan', '0')->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-gift text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Jenis Tunjangan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola master komponen tunjangan karyawan (tunjangan tetap & tunjangan tidak tetap) untuk perhitungan payroll
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
                    <i class="ti ti-database text-sm"></i>
                    <span>Data Master</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Jenis Tunjangan</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('jenistunjangan.create')
                    <button type="button" id="btnCreateJenisTunjangan" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Jenis Tunjangan</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. GRID 2 KOLOM: DAFTAR KARTU & PANEL EDUKASI SIFAT TUNJANGAN ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- KOLOM KIRI (7/12): DAFTAR JENIS TUNJANGAN (CARD LIST) -->
        <div class="lg:col-span-7 space-y-4">
            
            <!-- Toolbar Counter / Summary -->
            <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-700">Total Komponen:</span>
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-black bg-slate-100 text-slate-800 border border-slate-200">
                        {{ $totalTunjangan }} Jenis
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>{{ $totalTetap }} Tetap</span>
                    </span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span>{{ $totalTidakTetap }} Tidak Tetap</span>
                    </span>
                </div>
            </div>

            <!-- List Card Components -->
            <div class="space-y-3">
                @forelse ($jenistunjangan as $d)
                    @php
                        $isTetap = ($d->status_tunjangan === '1');
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 p-4 sm:p-5 group flex items-center justify-between gap-4">
                        
                        <!-- Left: Info & Kode -->
                        <div class="flex items-start gap-3.5 min-w-0">
                            <!-- Icon / Kode Pill -->
                            <div class="w-11 h-11 rounded-xl flex flex-col items-center justify-center shrink-0 shadow-2xs {{ $isTetap ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/70' : 'bg-amber-50 text-amber-700 border border-amber-200/70' }}">
                                <i class="ti {{ $isTetap ? 'ti-circle-check' : 'ti-clock' }} text-lg"></i>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-slate-900 text-sm truncate group-hover:text-emerald-700 transition">
                                        {{ $d->jenis_tunjangan }}
                                    </h3>
                                    <span class="text-[11px] font-black px-2 py-0.5 rounded-md uppercase font-mono tracking-wider text-slate-600 bg-slate-100 border border-slate-200">
                                        {{ $d->kode_jenis_tunjangan }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 mt-1.5">
                                    @if ($isTetap)
                                        <span class="inline-flex items-center gap-1 text-[10.5px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Tunjangan Tetap</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10.5px] font-bold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            <span>Tunjangan Tidak Tetap</span>
                                        </span>
                                    @endif

                                    <span class="text-[11px] text-slate-400">
                                        • ID: {{ $loop->iteration }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Action Buttons -->
                        <div class="flex items-center gap-1 shrink-0">
                            @can('jenistunjangan.edit')
                                <button type="button" 
                                        class="w-8 h-8 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center justify-center transition active:scale-95 btnEditJenisTunjangan cursor-pointer shadow-2xs" 
                                        data-kode="{{ Crypt::encrypt($d->kode_jenis_tunjangan) }}"
                                        title="Edit Tunjangan">
                                    <i class="ti ti-edit text-sm"></i>
                                </button>
                            @endcan

                            @can('jenistunjangan.delete')
                                <form action="{{ route('jenistunjangan.delete', Crypt::encrypt($d->kode_jenis_tunjangan)) }}" method="POST" class="inline-block formDeleteJenisTunjangan">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs" 
                                            title="Hapus Tunjangan">
                                        <i class="ti ti-trash text-sm"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>

                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-10 text-center border border-slate-200/90 shadow-2xs">
                        <div class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3 shadow-2xs">
                            <i class="ti ti-gift-off text-2xl"></i>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Belum Ada Komponen Tunjangan</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">
                            Klik tombol Tambah Jenis Tunjangan di atas untuk mendaftarkan komponen baru.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- KOLOM KANAN (5/12): PANEL EDUKASI & KETENTUAN SIFAT TUNJANGAN -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                
                <!-- Panel Header -->
                <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2.5"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white shadow-2xs"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-info-circle text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 text-sm tracking-tight">
                            Informasi Sifat Tunjangan
                        </h3>
                        <p class="text-[11px] text-slate-500">
                            Pedoman klasifikasi tunjangan penggajian
                        </p>
                    </div>
                </div>

                <!-- Panel Content -->
                <div class="p-5 sm:p-6 flex flex-col gap-4 text-xs text-slate-600 leading-relaxed">
                    
                    <!-- Section: Tunjangan Tetap -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 space-y-2.5 shadow-2xs">
                        <div class="flex items-center gap-2 text-emerald-900 font-black text-xs">
                            <i class="ti ti-circle-check-filled text-emerald-600 text-base"></i>
                            <span>Tunjangan Tetap</span>
                        </div>
                        <p class="text-justify text-slate-600 leading-relaxed text-xs">
                            Pembayaran yang diberikan kepada pekerja secara teratur dengan jumlah yang tetap pada setiap periode penggajian, tanpa dipengaruhi oleh absensi harian maupun target performa.
                        </p>
                        <div class="pt-2 border-t border-emerald-200/70 text-[11px] text-emerald-800">
                            <strong>Contoh:</strong> Tunjangan Jabatan, Tunjangan Keluarga, Tunjangan Perumahan, Tunjangan Keahlian Khusus.
                        </div>
                    </div>

                    <!-- Section: Tunjangan Tidak Tetap -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-2.5 shadow-2xs">
                        <div class="flex items-center gap-2 text-amber-900 font-black text-xs">
                            <i class="ti ti-clock-filled text-amber-600 text-base"></i>
                            <span>Tunjangan Tidak Tetap</span>
                        </div>
                        <p class="text-justify text-slate-600 leading-relaxed text-xs">
                            Pembayaran yang nilainya fluktuatif atau dapat berubah-ubah setiap bulannya, umumnya dihitung berdasarkan jumlah kehadiran fisik (absensi), dinas lapangan, atau capaian insentif kerja.
                        </p>
                        <div class="pt-2 border-t border-amber-200/70 text-[11px] text-amber-800">
                            <strong>Contoh:</strong> Uang Makan Harian, Uang Transport Kehadiran, Insentif Pencapaian Target.
                        </div>
                    </div>

                    <!-- Footnote -->
                    <div class="flex items-start gap-2.5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-slate-500 text-[11px] shadow-2xs">
                        <i class="ti ti-help-circle text-slate-400 text-base shrink-0 mt-0.5"></i>
                        <span class="leading-relaxed">
                            Pengaturan sifat tunjangan ini akan menjadi dasar rumus penghitungan otomatis pada slip gaji bulanan karyawan.
                        </span>
                    </div>

                </div>

            </div>
        </div>

    </div>

</div>

<!-- ================= MODAL UNIVERSAL JENIS TUNJANGAN V2 ================= -->
<div id="modalJenisTunjangan" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-gift text-lg" id="modalIcon"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="modalTitle">
                        Jenis Tunjangan
                    </h3>
                    <p class="text-[11px] text-slate-500" id="modalSubtitle">
                        Formulir data komponen tunjangan
                    </p>
                </div>
            </div>
            <button type="button" id="btnCloseModal" 
                    class="w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body Form -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalContent">
            <div class="py-10 text-center text-slate-400">
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
        const $modal = $('#modalJenisTunjangan');
        const $modalTitle = $('#modalTitle');
        const $modalSubtitle = $('#modalSubtitle');
        const $modalIcon = $('#modalIcon');
        const $modalContent = $('#modalContent');

        function openModal(title, subtitle, icon) {
            $modalTitle.text(title);
            $modalSubtitle.text(subtitle);
            $modalIcon.attr('class', 'ti ' + (icon || 'ti-gift') + ' text-lg');
            $modalContent.html(`
                <div class="py-10 text-center text-slate-400">
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

        $('#btnCloseModal').on('click', closeModal);
        $modal.on('click', function(e) {
            if (e.target === this) closeModal();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$modal.hasClass('hidden')) {
                closeModal();
            }
        });

        // 1. Action: Tambah Jenis Tunjangan (Create)
        $('#btnCreateJenisTunjangan').on('click', function(e) {
            e.preventDefault();
            openModal('Tambah Jenis Tunjangan', 'Buat komponen tunjangan baru', 'ti-plus');

            $.ajax({
                url: "{{ route('jenistunjangan.create') }}",
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
                },
                error: function(xhr) {
                    $modalContent.html(`
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            <p class="font-bold">Gagal memuat form tambah.</p>
                            <p class="mt-1">${xhr.responseText || 'Terjadi kesalahan sistem.'}</p>
                        </div>
                    `);
                }
            });
        });

        // 2. Action: Edit Jenis Tunjangan
        $(document).on('click', '.btnEditJenisTunjangan', function(e) {
            e.preventDefault();
            const kode = $(this).data('kode');
            openModal('Edit Jenis Tunjangan', 'Perbarui data nama dan sifat tunjangan', 'ti-edit');

            $.ajax({
                url: `{{ url('/jenistunjangan') }}/${kode}/edit`,
                type: "GET",
                success: function(response) {
                    $modalContent.html(response);
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

        // 3. Action: Delete Jenis Tunjangan (SweetAlert2)
        $(document).on('submit', '.formDeleteJenisTunjangan', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Jenis Tunjangan?',
                text: 'Komponen tunjangan yang dihapus tidak dapat dipulihkan kembali!',
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
    });
</script>
@endpush
