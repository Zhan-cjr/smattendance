@extends('v2.layouts.app')

@section('titlepage', 'Konfigurasi Indikator KPI')
@section('page_title', 'Konfigurasi Indikator KPI')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $user = auth()->user();
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-target-arrow text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Konfigurasi Indikator KPI</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Atur matriks indikator, bobot penilaian, dan target pencapaian per jabatan & departemen
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
                <span class="text-slate-500">KPI</span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Indikator</span>
            </nav>

            <!-- Actions Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @if($user->isSuperAdmin())
                    <a href="{{ route('kpi.indicators.create', ['scope' => 'global']) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-slate-900 text-xs font-bold transition shadow-2xs cursor-pointer">
                        <i class="ti ti-world text-sm text-sky-600"></i>
                        <span>Konfigurasi Global</span>
                    </a>
                @endif

                <button type="button" id="btnTambahKonfigurasiV2"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Konfigurasi KPI</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('kpi.indicators.index') }}" method="GET">
            <div class="flex flex-col sm:flex-row items-center gap-2.5">
                
                <!-- Search Input -->
                <div class="relative flex-1 w-full">
                    <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <input type="text" name="nama_jabatan" value="{{ request('nama_jabatan') }}" 
                           placeholder="Cari nama jabatan / posisi..." 
                           class="w-full h-10 pl-9 pr-3.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 shadow-2xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <!-- Action Button Filter & Reset -->
                <div class="flex items-center gap-1.5 w-full sm:w-auto">
                    <button type="submit" 
                            class="flex-1 sm:flex-initial h-10 inline-flex items-center justify-center gap-1.5 px-4 text-white font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-filter text-sm"></i>
                        <span>Filter</span>
                    </button>
                    @if (request()->has('nama_jabatan') && request('nama_jabatan') != '')
                        <a href="{{ route('kpi.indicators.index') }}" 
                           class="h-10 w-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition active:scale-95 shadow-2xs cursor-pointer shrink-0"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
            <span>Menampilkan <strong>{{ $kpi_indicators->count() }}</strong> dari total <strong>{{ $kpi_indicators->total() }}</strong> paket konfigurasi KPI</span>
        </div>
    </div>

    <!-- ================= 3. LIST CARD FULL WIDTH ================= -->
    <div class="space-y-2.5">
        @forelse ($kpi_indicators as $index => $d)
            @php
                $isGlobal = empty($d->kode_jabatan) && empty($d->kode_dept);
                $detailCount = $d->details->count();
                $totalBobot = $d->details->sum('bobot');
            @endphp
            <div class="bg-white rounded-2xl px-4 py-3.5 sm:px-5 sm:py-4 border border-slate-200/90 hover:border-emerald-300/80 shadow-2xs hover:shadow-xs transition-all duration-200 group">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                    
                    <!-- Left: Icon & Scope/Jabatan Identity -->
                    <div class="flex items-center gap-3.5 flex-1 min-w-0">
                        <!-- Icon -->
                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center shrink-0 border transition-all duration-200 {{ $isGlobal ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200' }}">
                            @if ($isGlobal)
                                <i class="ti ti-world text-xl"></i>
                            @else
                                <i class="ti ti-briefcase text-xl"></i>
                            @endif
                        </div>

                        <!-- Name & Department Badges -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if ($isGlobal)
                                    <h3 class="text-xs sm:text-sm font-bold text-sky-800 truncate">
                                        Indikator KPI Global
                                    </h3>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                        Seluruh Karyawan
                                    </span>
                                @else
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition truncate">
                                        {{ $d->jabatan->nama_jabatan ?? 'Jabatan Tidak Diketahui' }}
                                    </h3>
                                    <span class="font-mono text-xs text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                        {{ $d->kode_jabatan }}
                                    </span>
                                    @if(!empty($d->departemen))
                                        <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                                            {{ $d->departemen->nama_dept }}
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs text-slate-500 mt-1">
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-list-check text-xs text-slate-400"></i>
                                    <span><strong>{{ $detailCount }}</strong> Indikator</span>
                                </span>
                                <span>&bull;</span>
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-percentage text-xs text-slate-400"></i>
                                    <span>Total Bobot: <strong class="{{ $totalBobot == 100 ? 'text-emerald-700' : 'text-amber-600' }}">{{ $totalBobot }}%</strong></span>
                                </span>
                                <span>&bull;</span>
                                <span class="text-slate-400 text-[11px]">
                                    Update: {{ date('d/m/Y', strtotime($d->updated_at)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Quick Preview & Action Buttons -->
                    <div class="flex items-center justify-between lg:justify-end gap-3 shrink-0 pt-2.5 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        <div class="text-left lg:text-right">
                            <span class="text-xs font-bold text-slate-800 block">
                                {{ $detailCount }} Item KPI
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium block">
                                {{ $isGlobal ? 'Scope Perusahaan' : 'Scope Jabatan' }}
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-1.5">
                            <!-- Edit Button -->
                            <a href="{{ route('kpi.indicators.edit', $d->id) }}" 
                               class="p-2 rounded-xl bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200 transition active:scale-95 cursor-pointer shadow-2xs flex items-center gap-1 text-xs font-bold"
                               title="Edit Konfigurasi KPI">
                                <i class="ti ti-edit text-sm"></i>
                                <span class="hidden sm:inline text-[11px]">Edit</span>
                            </a>

                            <!-- Delete Button -->
                            <form action="{{ route('kpi.indicators.destroy', $d->id) }}" method="POST" class="deleteform m-0 inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btnDeleteIndicatorV2 p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs"
                                        title="Hapus Konfigurasi">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center text-slate-400 border border-slate-200/90 shadow-2xs">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                    <i class="ti ti-target-off"></i>
                </div>
                <h5 class="text-sm font-bold text-slate-800">Belum Ada Konfigurasi Indikator KPI</h5>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    Data indikator KPI per jabatan masih kosong. Silakan buat konfigurasi baru untuk memulai penilaian.
                </p>
                <button type="button" id="btnTambahKonfigurasiEmptyV2"
                        class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all active:scale-95 cursor-pointer"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Konfigurasi KPI</span>
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($kpi_indicators->hasPages())
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            {{ $kpi_indicators->links('v2.components.pagination') }}
        </div>
    @endif

</div>

<!-- ================= MODAL PILIH JABATAN & DEPARTEMEN ================= -->
<div id="modalSelectJabatanV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300 flex flex-col my-auto">
        <!-- Header Modal -->
        <div class="px-5 sm:px-6 py-3.5 border-b border-slate-200/80 bg-white flex items-center justify-between relative shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-700 bg-slate-100 border border-slate-200">
                    <i class="ti ti-briefcase text-base"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">
                        Pilih Jabatan & Departemen
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        Tentukan target jabatan sebelum menyusun indikator
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseModalSelectJabatan w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center border border-slate-200 transition cursor-pointer">
                <i class="ti ti-x text-sm"></i>
            </button>
        </div>

        <!-- Body Content Modal -->
        <div class="p-5 sm:p-6">
            <form id="formPilihJabatanV2" class="space-y-4">
                <!-- Pilih Jabatan -->
                <div class="space-y-1.5">
                    <label for="select_kode_jabatan" class="block text-xs font-bold text-slate-700">
                        Jabatan <span class="text-rose-500">*</span>
                    </label>
                    <select name="kode_jabatan" id="select_kode_jabatan" class="w-full h-10 px-3 text-xs bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach ($jabatan_list as $item)
                            <option value="{{ $item->kode_jabatan }}">{{ $item->nama_jabatan }}</option>
                        @endforeach
                    </select>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_select_kode_jabatan"></div>
                </div>

                <!-- Pilih Departemen -->
                <div class="space-y-1.5">
                    <label for="select_kode_dept" class="block text-xs font-bold text-slate-700">
                        Departemen <span class="text-rose-500">*</span>
                    </label>
                    <select name="kode_dept" id="select_kode_dept" class="w-full h-10 px-3 text-xs bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                        <option value="">-- Pilih Departemen --</option>
                        @foreach ($departemen_list as $item)
                            <option value="{{ $item->kode_dept }}">{{ $item->nama_dept }}</option>
                        @endforeach
                    </select>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_select_kode_dept"></div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" class="btnCloseModalSelectJabatan px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-xs transition active:scale-95 cursor-pointer flex items-center gap-1.5"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <span>Lanjut Konfigurasi</span>
                        <i class="ti ti-arrow-right text-sm"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        function openSelectModal() {
            $('#modalSelectJabatanV2').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('.error-feedback').addClass('hidden').text('');
            $('#select_kode_jabatan, #select_kode_dept').removeClass('border-rose-500');
        }

        function closeSelectModal() {
            $('#modalSelectJabatanV2').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        }

        $('#btnTambahKonfigurasiV2, #btnTambahKonfigurasiEmptyV2').click(function(e) {
            e.preventDefault();
            openSelectModal();
        });

        $('.btnCloseModalSelectJabatan').click(closeSelectModal);

        $('#modalSelectJabatanV2').click(function(e) {
            if (e.target === this) closeSelectModal();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$('#modalSelectJabatanV2').hasClass('hidden')) {
                closeSelectModal();
            }
        });

        $('#formPilihJabatanV2').submit(function(e) {
            e.preventDefault();
            let kode_jabatan = $('#select_kode_jabatan').val();
            let kode_dept = $('#select_kode_dept').val();
            let hasError = false;

            $('.error-feedback').addClass('hidden').text('');
            $('#select_kode_jabatan, #select_kode_dept').removeClass('border-rose-500');

            if (!kode_jabatan) {
                $('#err_select_kode_jabatan').text('Silakan pilih jabatan terlebih dahulu.').removeClass('hidden');
                $('#select_kode_jabatan').addClass('border-rose-500');
                hasError = true;
            }

            if (!kode_dept) {
                $('#err_select_kode_dept').text('Silakan pilih departemen terlebih dahulu.').removeClass('hidden');
                $('#select_kode_dept').addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) return false;

            window.location.href = "{{ route('kpi.indicators.create') }}?kode_jabatan=" + kode_jabatan + "&kode_dept=" + kode_dept;
        });

        // Delete Confirmation
        $(document).on('click', '.btnDeleteIndicatorV2', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Paket KPI Ini?',
                text: 'Semua rincian indikator pada jabatan dan departemen ini akan dihapus permanen!',
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
