@extends('v2.layouts.app')

@section('titlepage', 'Atur Karyawan Libur')
@section('page_title', 'Penetapan Karyawan Hari Libur')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMB) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-calendar-user text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Penetapan Karyawan Libur</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola daftar karyawan yang diliburkan pada tanggal <strong class="text-slate-800">{{ DateToIndo($harilibur->tanggal) }}</strong> (Cabang {{ $harilibur->nama_cabang }})
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Action -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('harilibur.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-calendar-off text-sm"></i>
                    <span>Hari Libur</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Atur Karyawan</span>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('harilibur.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 hover:bg-slate-50 text-slate-700 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95">
                    <i class="ti ti-arrow-left text-sm"></i>
                    <span>Kembali</span>
                </a>

                @can('harilibur.setharilibur')
                    <button type="button" id="btnAturKaryawan" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-user-plus text-sm"></i>
                        <span>Tambah / Pilih Karyawan</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. EVENT DETAILS CARD & SUMMARY ================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Event Details Box (2 Columns) -->
        <div class="md:col-span-2 bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">
                            <i class="ti ti-info-circle text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Detail Informasi Libur</h3>
                            <p class="text-[11px] text-slate-400 font-medium">Informasi kalender hari libur aktif</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200">
                        {{ $harilibur->kode_libur }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium block">Tanggal:</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 flex items-center gap-1.5">
                            <i class="ti ti-calendar text-emerald-600"></i>
                            {{ DateToIndo($harilibur->tanggal) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Cabang / Unit:</span>
                        <span class="font-bold text-slate-900 text-sm mt-0.5 flex items-center gap-1.5">
                            <i class="ti ti-building text-sky-600"></i>
                            {{ $harilibur->nama_cabang }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Keterangan:</span>
                        <span class="font-bold text-slate-800 text-xs mt-0.5 block truncate" title="{{ $harilibur->keterangan }}">
                            {{ $harilibur->keterangan }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2 text-[11.5px] text-slate-500">
                <i class="ti ti-bulb text-amber-500 text-sm"></i>
                <span>Karyawan pada daftar di bawah tidak wajib presensi dan dibebaskan dari denda pada tanggal ini.</span>
            </div>
        </div>

        <!-- Quick Summary Card (1 Column) -->
        <div class="text-white rounded-2xl p-5 border border-white/20 shadow-md flex flex-col justify-between relative overflow-hidden"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <div class="absolute -right-6 -bottom-6 text-white/10 pointer-events-none">
                <i class="ti ti-users text-[120px]"></i>
            </div>
            <div>
                <span class="text-[11px] font-black uppercase tracking-wider text-white/80">Total Terdaftar Libur</span>
                <div class="text-3xl font-black text-white mt-1" id="countKaryawanLibur">
                    <i class="ti ti-loader animate-spin text-xl"></i>
                </div>
            </div>
            <div class="pt-3 border-t border-white/20 flex items-center justify-between text-xs text-white/90">
                <span>Status Sinkronisasi</span>
                <span class="px-2.5 py-0.5 rounded-lg bg-white/20 text-white border border-white/30 font-bold text-[10px] backdrop-blur-xs">
                    Presensi Realtime
                </span>
            </div>
        </div>
    </div>

    <!-- ================= 3. SEAMLESS SOLID TABLE VIEW (KARYAWAN LIBUR) ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-users"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Karyawan Diliburkan</h3>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="loadliburkaryawan()" 
                        class="px-2.5 py-1 rounded-lg text-xs font-bold bg-white/20 hover:bg-white/30 text-white border border-white/25 transition cursor-pointer flex items-center gap-1">
                    <i class="ti ti-refresh text-xs"></i>
                    <span>Refresh</span>
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 w-40 text-white/90 border-0 border-t-0">NIK</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Nama Karyawan</th>
                        <th class="py-2.5 px-4 w-44 text-white/90 border-0 border-t-0">Departemen</th>
                        <th class="py-2.5 px-3.5 text-center w-24 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white" id="loadliburkaryawan">
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400 bg-white">
                            <div class="w-8 h-8 border-3 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mx-auto mb-2"></div>
                            <p class="text-xs font-medium">Memuat data karyawan libur...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Universal V2 Modal for Setting Employees -->
<div class="modal fade" id="modalV2AturKaryawan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900 shrink-0">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-user-plus"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalV2AturTitle">Pilih Karyawan Libur</h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium">Centang atau masukkan karyawan ke daftar hari libur</p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6 bg-white overflow-y-auto" id="loadmodalV2AturKaryawan">
                <!-- Dynamic form loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    function loadliburkaryawan() {
        const kode_libur = "{{ Crypt::encrypt($harilibur->kode_libur) }}";
        $("#loadliburkaryawan").html(`
            <tr>
                <td colspan="5" class="py-10 text-center text-slate-400 bg-white">
                    <div class="w-8 h-8 border-3 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mx-auto mb-2"></div>
                    <p class="text-xs font-medium">Memuat data karyawan libur...</p>
                </td>
            </tr>
        `);
        $.ajax({
            url: `/harilibur/${kode_libur}/getkaryawanlibur`,
            type: 'GET',
            success: function(response) {
                $("#loadliburkaryawan").html(response);
                const count = $("#loadliburkaryawan tr.data-row").length;
                $("#countKaryawanLibur").text(count + ' Orang');
            },
            error: function() {
                $("#loadliburkaryawan").html(`
                    <tr>
                        <td colspan="5" class="py-8 text-center text-rose-500 bg-white font-medium text-xs">
                            Gagal memuat data karyawan libur.
                        </td>
                    </tr>
                `);
            }
        });
    }

    $(function() {
        loadliburkaryawan();

        const modalEl = document.getElementById('modalV2AturKaryawan');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoadingSpinner() {
            $('#loadmodalV2AturKaryawan').html(`
                <div class="py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mb-3"></div>
                    <p class="text-xs text-slate-500 font-medium">Memuat daftar master karyawan...</p>
                </div>
            `);
        }

        // Open Modal Atur Karyawan
        $('#btnAturKaryawan').click(function(e) {
            e.preventDefault();
            const kode_libur = "{{ Crypt::encrypt($harilibur->kode_libur) }}";
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2AturKaryawan').load(`/harilibur/${kode_libur}/aturkaryawan`);
        });

        // Delete Karyawan from Libur
        $(document).on('click', '.delete-karyawan-libur', function(e) {
            e.preventDefault();
            const kode_libur = "{{ $harilibur->kode_libur }}";
            const nik = $(this).data("nik");
            const nama = $(this).data("nama") || "karyawan ini";

            Swal.fire({
                title: 'Hapus dari Hari Libur?',
                text: `${nama} (${nik}) akan dikembalikan ke jadwal kerja normal.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: `/harilibur/deletekaryawanlibur`,
                        data: {
                            _token: "{{ csrf_token() }}",
                            kode_libur: kode_libur,
                            nik: nik
                        },
                        cache: false,
                        success: function(respond) {
                            if (respond.success == true) {
                                loadliburkaryawan();
                            } else {
                                Swal.fire('Gagal!', respond.message || 'Terjadi kesalahan sistem', 'error');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
