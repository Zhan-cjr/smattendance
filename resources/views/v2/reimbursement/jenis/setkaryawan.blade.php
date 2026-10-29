@extends('v2.layouts.app')

@section('titlepage', 'Enrollment Karyawan - ' . $jenis->nama_jenis)
@section('page_title', 'Enrollment Karyawan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalEnrolled = $details->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle with Back Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('jenisreimbursement.index') }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('jenisreimbursement.index') }}" class="hover:text-emerald-600 transition-colors">Jenis Reimbursement</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Enrollment</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 mt-0.5">
                    <i class="ti ti-users text-2xl" style="color: {{ $themeColor1 }};"></i>
                    <span>Enrollment Karyawan: {{ $jenis->nama_jenis }}</span>
                </h1>
            </div>
        </div>

        <!-- Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('jenisreimbursement.index') }}" class="hover:text-slate-700 transition">Jenis Reimbursement</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Enrollment</span>
            </nav>

            <!-- Action Button -->
            <button type="button" id="btnEnrollKaryawanV2" 
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-user-plus text-sm"></i>
                <span>Daftarkan Karyawan</span>
            </button>
        </div>
    </div>

    <!-- ================= 2. BANNER INFO CATEGORY ================= -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl shrink-0 shadow-2xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-receipt-refund"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 border border-sky-200/80 text-[10.5px] font-mono font-black">
                            {{ $jenis->kode_jenis_reimburse }}
                        </span>
                        <h3 class="text-base font-black text-slate-900">{{ $jenis->nama_jenis }}</h3>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Plafon Global: 
                        <strong class="text-emerald-700 font-mono">{{ $jenis->batas_nominal ? 'Rp ' . number_format($jenis->batas_nominal, 0, ',', '.') : 'Unlimited' }}</strong> / klaim &bull;
                        <strong class="text-sky-700 font-mono">{{ $jenis->batas_nominal_bulanan ? 'Rp ' . number_format($jenis->batas_nominal_bulanan, 0, ',', '.') : 'Unlimited' }}</strong> / bulan
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700">
                    <strong class="text-emerald-700 text-sm font-black">{{ $totalEnrolled }}</strong> Karyawan Berhak
                </span>
            </div>
        </div>
    </div>

    <!-- ================= 3. ENROLLED KARYAWAN LIST (FULL WIDTH CARDS) ================= -->
    <div class="space-y-3">
        <!-- Section Header / Filter Toolbar Bar -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white shadow-2xs"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-users text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Daftar Karyawan Terdaftar</h3>
                        <p class="text-[11px] text-slate-500">Karyawan yang memiliki akses dan kuota pengajuan klaim ini</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold shrink-0">
                        <strong class="text-emerald-700 font-mono" id="enrolledCountText">{{ $totalEnrolled }}</strong> dari {{ $totalEnrolled }} Pegawai
                    </span>
                </div>
            </div>

            <!-- Filter Controls (Search + Dept + Cabang) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-2.5">
                <!-- Search Input -->
                <div class="sm:col-span-1 lg:col-span-2 relative">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                    <input type="text" id="searchEnrolledKaryawanV2" placeholder="Cari nama / NIK..." 
                           class="w-full h-9 pl-9 pr-3.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-200 rounded-xl outline-none focus:border-emerald-600 transition font-medium text-slate-800">
                </div>

                <!-- Filter Departemen -->
                <div>
                    <select id="filterDeptEnrolledV2" class="w-full h-9 px-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-200 rounded-xl outline-none focus:border-emerald-600 transition cursor-pointer font-medium text-slate-700">
                        <option value="">Semua Departemen</option>
                        @foreach($departemen as $d)
                            <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Cabang -->
                <div>
                    <select id="filterCabangEnrolledV2" class="w-full h-9 px-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-200 rounded-xl outline-none focus:border-emerald-600 transition cursor-pointer font-medium text-slate-700">
                        <option value="">Semua Cabang</option>
                        @foreach($cabang as $c)
                            <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- List Cards -->
        <div class="space-y-2.5" id="containerEnrolledCards">
            @forelse ($details as $index => $d)
                @php
                    $encryptedNik = Crypt::encrypt($d->nik);
                    $searchIndex = strtolower(($d->nik_show ?: $d->nik) . ' ' . $d->nama_karyawan . ' ' . ($d->nama_dept ?? '') . ' ' . ($d->nama_cabang ?? ''));
                @endphp
                <div class="cardEnrolledRow bg-white border border-slate-200/90 hover:border-emerald-300/80 rounded-2xl px-4 py-3 sm:px-4.5 sm:py-3 shadow-2xs hover:shadow-xs transition-all duration-200 group"
                     data-search="{{ $searchIndex }}"
                     data-dept="{{ $d->kode_dept }}"
                     data-cabang="{{ $d->kode_cabang }}">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                        
                        <!-- Left: Number, Avatar & Employee Identity -->
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <!-- Number Badge -->
                            <div class="hidden sm:flex w-6 h-6 rounded-md bg-slate-100 text-slate-500 text-[10.5px] font-bold items-center justify-center shrink-0 border border-slate-200/80">
                                {{ $index + 1 }}
                            </div>

                            <!-- Compact Photo Thumbnail with Status Indicator -->
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

                            <!-- Name, NIK, Dept & Branch -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono font-bold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded text-[10.5px]">
                                        {{ $d->nik_show ?: $d->nik }}
                                    </span>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition truncate" title="{{ $d->nama_karyawan }}">
                                        {{ $d->nama_karyawan }}
                                    </h4>
                                    
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

                                <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500 mt-0.5">
                                    <span class="text-slate-500 flex items-center gap-1">
                                        <i class="ti ti-calendar text-xs text-slate-400"></i>
                                        <span>{{ date('d/m/Y', strtotime($d->tanggal_mulai)) }}</span>
                                    </span>
                                    <span>&rarr;</span>
                                    <span class="text-slate-500 flex items-center gap-1">
                                        @if($d->tanggal_selesai)
                                            <span>{{ date('d/m/Y', strtotime($d->tanggal_selesai)) }}</span>
                                        @else
                                            <span class="text-emerald-600 font-bold">Selamanya</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Compact Plafon Badge & Delete Button -->
                        <div class="flex items-center justify-between md:justify-end gap-3 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-slate-100">
                            <!-- Plafon Status -->
                            <div class="text-left md:text-right">
                                <div class="flex items-center md:justify-end gap-1.5">
                                    @if($d->batas_nominal_override)
                                        <span class="text-xs sm:text-sm font-black text-rose-600 font-mono">
                                            Rp {{ number_format($d->batas_nominal_override, 0, ',', '.') }}
                                        </span>
                                        <span class="text-[9.5px] px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200/60 font-bold">
                                            Override
                                        </span>
                                    @else
                                        <span class="text-xs sm:text-sm font-black text-emerald-700 font-mono">
                                            {{ $jenis->batas_nominal ? 'Rp ' . number_format($jenis->batas_nominal, 0, ',', '.') : 'Unlimited' }}
                                        </span>
                                        <span class="text-[9.5px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-medium">
                                            Global
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium block">Plafon Per Klaim</span>
                            </div>

                            <!-- Action -->
                            <form action="{{ route('jenisreimbursement.destroykaryawan', Crypt::encrypt($d->id)) }}" method="POST" class="deleteform m-0 inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btnDeleteEnrolledV2 p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200/80 transition active:scale-95 cursor-pointer shadow-2xs flex items-center gap-1 text-xs font-bold"
                                        title="Hapus Dari Enrollment">
                                    <i class="ti ti-trash text-sm"></i>
                                    <span class="hidden lg:inline text-[11px]">Hapus</span>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl p-12 text-center text-slate-400 border border-slate-200/90 shadow-2xs">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-2xl">
                        <i class="ti ti-users-off"></i>
                    </div>
                    <h5 class="text-sm font-black text-slate-800">Belum Ada Karyawan Terdaftar</h5>
                    <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                        Daftarkan karyawan yang berhak mengajukan permohonan klaim pada jenis reimbursement ini.
                    </p>
                    <button type="button" onclick="$('#btnEnrollKaryawanV2').trigger('click')"
                            class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all active:scale-95 cursor-pointer"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-user-plus text-sm"></i>
                        <span>Daftarkan Karyawan Sekarang</span>
                    </button>
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- ================= MODAL ENROLL KARYAWAN ================= -->
<div id="modalEnrollKaryawanUniversalV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-3xl overflow-hidden transform transition-all duration-300 max-h-[92vh] flex flex-col my-auto">
        <!-- Header -->
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between relative shrink-0"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}15);">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-2xs text-white"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-user-plus text-lg"></i>
                </div>
                <div>
                    <h3 class="font-black text-slate-900 text-sm sm:text-base tracking-tight">
                        Daftarkan Karyawan
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        Skema Reimbursement: {{ $jenis->nama_jenis }}
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseModalEnroll w-8 h-8 rounded-xl bg-white/80 hover:bg-slate-100 text-slate-400 hover:text-slate-700 flex items-center justify-center border border-slate-200/60 transition shadow-2xs cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 custom-scrollbar" id="modalEnrollContent">
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
    $(function() {
        // Instant Multi-Filter for Enrolled Cards (Search + Dept + Cabang)
        function applyFilterEnrolled() {
            let search = $('#searchEnrolledKaryawanV2').val().toLowerCase().trim();
            let dept = $('#filterDeptEnrolledV2').val();
            let cabang = $('#filterCabangEnrolledV2').val();
            let visibleCount = 0;

            $('.cardEnrolledRow').each(function() {
                let itemSearch = $(this).data('search') || '';
                let itemDept = $(this).data('dept') || '';
                let itemCabang = $(this).data('cabang') || '';

                let matchSearch = !search || itemSearch.indexOf(search) > -1;
                let matchDept = !dept || itemDept == dept;
                let matchCabang = !cabang || itemCabang == cabang;

                let isVisible = matchSearch && matchDept && matchCabang;
                $(this).toggle(isVisible);
                if (isVisible) visibleCount++;
            });

            $('#enrolledCountText').text(visibleCount);
        }

        $('#searchEnrolledKaryawanV2').on('keyup', applyFilterEnrolled);
        $('#filterDeptEnrolledV2, #filterCabangEnrolledV2').on('change', applyFilterEnrolled);

        function showModalEnroll(url) {
            $('#modalEnrollKaryawanUniversalV2').removeClass('hidden');
            $('body').addClass('overflow-hidden');
            $('#modalEnrollContent').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat formulir...</p>
                </div>
            `);
            $('#modalEnrollContent').load(url, function() {
                if (typeof $.fn.maskMoney !== 'undefined') {
                    $('#modalEnrollContent .money-enroll').maskMoney({
                        thousands: '.', decimal: ',', precision: 0, allowZero: true
                    });
                }
                if (typeof flatpickr !== 'undefined') {
                    $('#modalEnrollContent .flatpickr-enroll').flatpickr({
                        dateFormat: "Y-m-d",
                        allowInput: true
                    });
                }
            });
        }

        function closeModalEnroll() {
            $('#modalEnrollKaryawanUniversalV2').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            $('#modalEnrollContent').html('');
        }

        $('#btnEnrollKaryawanV2').click(function(e) {
            e.preventDefault();
            showModalEnroll("{{ route('jenisreimbursement.addkaryawan', Crypt::encrypt($jenis->id)) }}");
        });

        $(document).on('click', '.btnCloseModalEnroll', closeModalEnroll);

        $('#modalEnrollKaryawanUniversalV2').click(function(e) {
            if (e.target === this) closeModalEnroll();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$('#modalEnrollKaryawanUniversalV2').hasClass('hidden')) {
                closeModalEnroll();
            }
        });

        // Delete Confirmation
        $(document).on('click', '.btnDeleteEnrolledV2', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Dari Enrollment?',
                text: 'Karyawan ini tidak lagi dapat mengajukan reimbursement pada kategori ini!',
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
