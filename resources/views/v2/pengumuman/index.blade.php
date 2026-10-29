@extends('v2.layouts.app')

@section('titlepage', 'Pengumuman')
@section('page_title', 'Pengumuman Internal')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalPengumuman = $pengumuman->total();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-speakerphone text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Data Pengumuman</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola informasi pengumuman, surat edaran, dan broadcast notifikasi kepada seluruh karyawan
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
                    <i class="ti ti-bell text-sm"></i>
                    <span>Informasi</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Pengumuman</span>
            </nav>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('pengumuman.create')
                    <button type="button" id="btnCreatePengumuman" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Pengumuman</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER & SEARCH TOOLBAR V2 ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('pengumuman.index') }}" method="GET" class="w-full">
            <div class="flex flex-col sm:flex-row items-center gap-2.5 sm:gap-3 w-full">
                
                <!-- Search Input -->
                <div class="flex-1 min-w-0 w-full relative">
                    <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none"></i>
                    <input type="text" 
                           name="judul" 
                           value="{{ request('judul') }}" 
                           placeholder="Cari Judul atau Topik Pengumuman..." 
                           class="w-full pl-11 pr-4 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <!-- Buttons: Cari & Reset -->
                <div class="flex items-center gap-1.5 shrink-0 w-full sm:w-auto">
                    <button type="submit" 
                            class="w-full sm:w-auto px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-sm"></i>
                        <span>Cari</span>
                    </button>
                    @if(request('judul'))
                        <a href="{{ route('pengumuman.index') }}" 
                           class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs inline-flex items-center justify-center"
                           title="Reset Pencarian">
                            <i class="ti ti-refresh text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <!-- Divider with Counter -->
        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <div class="text-slate-500 text-[11.5px]">
                Total: <strong>{{ $totalPengumuman }}</strong> Pengumuman Tersimpan
            </div>
        </div>
    </div>

    <!-- ================= 3. GRID CARDS VIEW ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse ($pengumuman as $item)
            @php
                $lampiranUrl = $item->lampiran ? asset('storage/uploads/pengumuman/' . $item->lampiran) : null;
                $fileExt = $item->lampiran ? strtolower(pathinfo($item->lampiran, PATHINFO_EXTENSION)) : '';
            @endphp
            <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-2xs hover:shadow-md hover:border-slate-300 transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <!-- Top Card: Header & Action Icons -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs"
                                 style="background: linear-gradient(135deg, {{ $themeColor1 }}15, {{ $themeColor2 }}25); color: {{ $themeColor1 }};">
                                <i class="ti ti-speakerphone text-base"></i>
                            </div>
                            <div>
                                <span class="text-[10.5px] font-bold text-slate-400 block font-mono">
                                    #{{ $loop->iteration + $pengumuman->firstItem() - 1 }}
                                </span>
                                <span class="text-[10px] text-slate-500 flex items-center gap-1">
                                    <i class="ti ti-calendar text-[11px]"></i>
                                    <span>{{ date('d M Y, H:i', strtotime($item->created_at)) }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons (Detail, Delete) -->
                        <div class="flex items-center gap-1">
                            <button type="button" 
                                    class="w-7 h-7 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 flex items-center justify-center transition active:scale-95 btnDetailPengumuman cursor-pointer shadow-2xs" 
                                    data-judul="{{ $item->judul }}"
                                    data-isi="{{ $item->isi }}"
                                    data-tanggal="{{ date('d F Y, H:i', strtotime($item->created_at)) }}"
                                    data-lampiran="{{ $lampiranUrl }}"
                                    data-lampiranname="{{ $item->lampiran }}"
                                    title="Lihat Detail">
                                <i class="ti ti-eye text-xs"></i>
                            </button>

                            @can('pengumuman.delete')
                                <form action="{{ route('pengumuman.delete', $item->id) }}" method="POST" class="inline-block formDeletePengumuman">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer shadow-2xs" 
                                            title="Hapus Pengumuman">
                                        <i class="ti ti-trash text-xs"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>

                    <!-- Title -->
                    <h3 class="font-bold text-slate-900 text-sm leading-snug line-clamp-2 group-hover:text-emerald-700 transition">
                        {{ $item->judul }}
                    </h3>

                    <!-- Excerpt Body -->
                    <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">
                        {{ Str::limit(strip_tags($item->isi), 140) }}
                    </p>
                </div>

                <!-- Card Footer: Lampiran Badge / Status -->
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    @if ($item->lampiran)
                        <a href="{{ $lampiranUrl }}" target="_blank" 
                           class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-200/80 transition cursor-pointer">
                            <i class="ti ti-paperclip text-xs"></i>
                            <span>Lihat Lampiran ({{ strtoupper($fileExt) }})</span>
                        </a>
                    @else
                        <span class="text-[11px] text-slate-400 italic flex items-center gap-1">
                            <i class="ti ti-info-circle text-xs"></i>
                            <span>Tanpa Lampiran</span>
                        </span>
                    @endif

                    <button type="button" 
                            class="text-[11px] font-bold text-slate-600 hover:text-slate-900 btnDetailPengumuman cursor-pointer"
                            data-judul="{{ $item->judul }}"
                            data-isi="{{ $item->isi }}"
                            data-tanggal="{{ date('d F Y, H:i', strtotime($item->created_at)) }}"
                            data-lampiran="{{ $lampiranUrl }}"
                            data-lampiranname="{{ $item->lampiran }}">
                        Selengkapnya &rarr;
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-slate-200/90 shadow-2xs">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3.5 shadow-2xs">
                    <i class="ti ti-speakerphone-off text-3xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Pengumuman</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Belum ada data pengumuman yang diterbitkan atau cocok dengan filter pencarian saat ini.
                </p>
                @can('pengumuman.create')
                    <button type="button" id="btnEmptyCreatePengumuman" 
                            class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition active:scale-95 cursor-pointer"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Pengumuman Baru</span>
                    </button>
                @endcan
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-2">
        {{ $pengumuman->links('v2.components.pagination') }}
    </div>

</div>

<!-- ================= MODAL TAMBAH PENGUMUMAN V2 ================= -->
<div id="modalCreatePengumuman" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-speakerphone text-lg"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                        Tambah Pengumuman Baru
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        Publikasikan informasi dan edaran penting untuk seluruh karyawan
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseCreateModal w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body Form -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar">
            <form action="{{ route('pengumuman.store') }}" method="POST" id="formPengumumanV2" enctype="multipart/form-data" class="space-y-4" novalidate>
                @csrf

                <!-- Banner Info -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                        <i class="ti ti-bell-ringing"></i>
                    </div>
                    <div>
                        <h5 class="text-xs font-bold text-slate-800">Notifikasi Otomatis</h5>
                        <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                            Pengumuman yang disimpan akan otomatis terkirim sebagai notifikasi lonceng internal dan push notification ke aplikasi karyawan.
                        </p>
                    </div>
                </div>

                <!-- Judul Pengumuman -->
                <div class="space-y-1.5">
                    <label for="judul_pengumuman" class="block text-xs font-bold text-slate-700">
                        Judul Pengumuman <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-speakerphone text-base"></i>
                        </span>
                        <input type="text" 
                               id="judul_pengumuman" 
                               name="judul" 
                               placeholder="Contoh: Jadwal Libur Nasional & Cuti Bersama" 
                               autocomplete="off"
                               class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                               required>
                    </div>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_judul_pengumuman"></div>
                </div>

                <!-- Isi Pengumuman -->
                <div class="space-y-1.5">
                    <label for="isi_pengumuman" class="block text-xs font-bold text-slate-700">
                        Isi Pengumuman <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <textarea id="isi_pengumuman" 
                                  name="isi" 
                                  rows="6" 
                                  placeholder="Tuliskan isi rincian pengumuman, pengarahan, atau surat edaran di sini..." 
                                  class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition custom-scrollbar" 
                                  required></textarea>
                    </div>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_isi_pengumuman"></div>
                </div>

                <!-- Lampiran File (Opsional) -->
                <div class="space-y-1.5">
                    <label for="lampiran_pengumuman" class="block text-xs font-bold text-slate-700">
                        Lampiran Dokumen <span class="text-slate-400 font-normal lowercase">(opsional, max 10MB)</span>
                    </label>
                    <div class="relative flex items-center">
                        <input type="file" 
                               id="lampiran_pengumuman" 
                               name="lampiran" 
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-300 rounded-xl p-1 bg-white cursor-pointer transition">
                    </div>
                    <p class="text-[10.5px] text-slate-400">Format yang didukung: PDF, JPG, PNG, DOC, DOCX</p>
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" 
                            class="btnCloseCreateModal px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            id="btnSubmitPengumumanV2" 
                            class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-send text-sm"></i>
                        <span>Simpan & Terbitkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL DETAIL PENGUMUMAN V2 ================= -->
<div id="modalDetailPengumuman" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-speakerphone text-lg"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight" id="detailJudul">
                        Detail Pengumuman
                    </h3>
                    <p class="text-[11px] text-slate-500" id="detailTanggal">
                        -
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseDetailModal w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body Content -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar space-y-4">
            <!-- Isi Pengumuman -->
            <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200/70 text-slate-800 text-xs sm:text-sm leading-relaxed whitespace-pre-wrap font-normal" id="detailIsi">
                -
            </div>

            <!-- Lampiran Box -->
            <div id="detailLampiranContainer" class="hidden p-3.5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200/60">
                        <i class="ti ti-file-text text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate" id="detailLampiranName">-</p>
                        <p class="text-[10.5px] text-slate-400">Berkas Lampiran Pengumuman</p>
                    </div>
                </div>

                <a href="" id="detailLampiranDownloadBtn" target="_blank" 
                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs border border-emerald-200/80 transition active:scale-95 shrink-0 cursor-pointer">
                    <i class="ti ti-download text-xs"></i>
                    <span>Unduh</span>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex justify-end">
            <button type="button" class="btnCloseDetailModal px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        const $modalCreate = $('#modalCreatePengumuman');
        const $modalDetail = $('#modalDetailPengumuman');

        function openCreateModal() {
            $modalCreate.removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeCreateModal() {
            $modalCreate.addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        function openDetailModal() {
            $modalDetail.removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeDetailModal() {
            $modalDetail.addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        $('#btnCreatePengumuman, #btnEmptyCreatePengumuman').on('click', openCreateModal);
        $('.btnCloseCreateModal').on('click', closeCreateModal);
        $modalCreate.on('click', function(e) {
            if (e.target === this) closeCreateModal();
        });

        $('.btnCloseDetailModal').on('click', closeDetailModal);
        $modalDetail.on('click', function(e) {
            if (e.target === this) closeDetailModal();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape') {
                if (!$modalCreate.hasClass('hidden')) closeCreateModal();
                if (!$modalDetail.hasClass('hidden')) closeDetailModal();
            }
        });

        // Detail Modal Click
        $(document).on('click', '.btnDetailPengumuman', function() {
            const judul = $(this).data('judul');
            const isi = $(this).data('isi');
            const tanggal = $(this).data('tanggal');
            const lampiran = $(this).data('lampiran');
            const lampiranname = $(this).data('lampiranname');

            $('#detailJudul').text(judul || 'Detail Pengumuman');
            $('#detailTanggal').text(tanggal ? `Diterbitkan pada ${tanggal}` : '-');
            $('#detailIsi').text(isi || '-');

            if (lampiran && lampiranname) {
                $('#detailLampiranName').text(lampiranname);
                $('#detailLampiranDownloadBtn').attr('href', lampiran);
                $('#detailLampiranContainer').removeClass('hidden');
            } else {
                $('#detailLampiranContainer').addClass('hidden');
            }

            openDetailModal();
        });

        // Delete Confirmation (SweetAlert2)
        $(document).on('submit', '.formDeletePengumuman', function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Pengumuman?',
                text: 'Data pengumuman dan berkas lampirannya akan dihapus permanen!',
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

        // Form Validation & Submit Handler
        $('#formPengumumanV2').on('submit', function(e) {
            const judul = $('#judul_pengumuman').val().trim();
            const isi = $('#isi_pengumuman').val().trim();
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $('#judul_pengumuman').removeClass('border-rose-500');
            $('#isi_pengumuman').removeClass('border-rose-500');

            if (!judul) {
                $('#err_judul_pengumuman').text('Judul pengumuman wajib diisi').removeClass('hidden');
                $('#judul_pengumuman').addClass('border-rose-500');
                hasError = true;
            }

            if (!isi) {
                $('#err_isi_pengumuman').text('Isi pengumuman wajib diisi').removeClass('hidden');
                $('#isi_pengumuman').addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitPengumumanV2');
            $btn.prop('disabled', true).html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Menerbitkan...</span>
            `);
        });
    });
</script>
@endpush
