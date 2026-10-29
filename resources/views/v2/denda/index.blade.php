@extends('v2.layouts.app')

@section('titlepage', 'Denda Keterlambatan')
@section('page_title', 'Data Denda Keterlambatan')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalDenda = $denda->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTIONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-receipt-tax text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Denda Keterlambatan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Manajemen skema pemotongan denda berdasarkan durasi menit keterlambatan presensi masuk
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
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-database text-sm"></i>
                    <span>Master Data</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Denda</span>
            </nav>

            @can('denda.create')
                <button type="button" id="btnCreate"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Aturan Denda</span>
                </button>
            @endcan
        </div>
    </div>

    <!-- Info Banner -->
    <div class="bg-gradient-to-r from-emerald-50/70 via-teal-50/40 to-slate-50 border border-emerald-200/70 rounded-2xl p-4 flex items-start gap-3.5 shadow-2xs">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 text-emerald-700 bg-white shadow-xs border border-emerald-100">
            <i class="ti ti-info-circle text-xl"></i>
        </div>
        <div class="flex-1 min-w-0 text-xs text-slate-600 leading-relaxed">
            <span class="font-bold text-slate-800 block mb-0.5">Ketentuan Pemotongan Denda Keterlambatan</span>
            Denda dihitung secara otomatis saat presensi masuk karyawan melebihi jam jadwal kerja. Besaran denda dipotongkan langsung pada saat rekapitulasi gaji (payroll) bulanan.
        </div>
    </div>

    <!-- ================= 2. SEAMLESS SOLID TABLE VIEW ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-receipt-tax"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Skema Denda Keterlambatan</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $totalDenda }} Aturan
            </span>
        </div>

        <!-- Responsive Table Container with Matching Solid Header -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 w-44 text-white/90 border-0 border-t-0">Mulai Dari</th>
                        <th class="py-2.5 px-4 w-44 text-white/90 border-0 border-t-0">Sampai Dengan</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Rentang Durasi (Menit)</th>
                        <th class="py-2.5 px-4 text-end w-52 text-white/90 border-0 border-t-0">Nominal Denda</th>
                        <th class="py-2.5 px-3.5 text-center w-28 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($denda as $idx => $d)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <!-- No Index -->
                            <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
                                <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                    {{ $loop->iteration }}
                                </span>
                            </td>

                            <!-- Mulai Dari -->
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/80 font-mono text-[11.5px]">
                                    <i class="ti ti-clock-play text-slate-400"></i>
                                    {{ $d->dari }} Menit
                                </span>
                            </td>

                            <!-- Sampai Dengan -->
                            <td class="py-2.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/80 font-mono text-[11.5px]">
                                    <i class="ti ti-clock-stop text-slate-400"></i>
                                    {{ $d->sampai }} Menit
                                </span>
                            </td>

                            <!-- Rentang Deskripsi -->
                            <td class="py-2.5 px-4 text-slate-600 text-xs">
                                Terlambat antara <strong class="text-slate-800">{{ $d->dari }}</strong> s/d <strong class="text-slate-800">{{ $d->sampai }}</strong> menit
                            </td>

                            <!-- Nominal Denda -->
                            <td class="py-2.5 px-4 text-end">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black bg-rose-50 text-rose-700 border border-rose-200/80 shadow-2xs font-mono">
                                    Rp {{ formatAngka($d->denda) }}
                                </span>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-2.5 px-3.5 text-center">
                                <div class="inline-flex items-center gap-1 border border-slate-200 rounded-lg p-0.5 bg-slate-50 shadow-2xs">
                                    @can('denda.edit')
                                        <button type="button" 
                                                class="w-7 h-7 rounded-md hover:bg-white text-blue-600 hover:text-blue-700 flex items-center justify-center transition cursor-pointer btnEdit" 
                                                data-id="{{ Crypt::encrypt($d->id) }}" 
                                                title="Edit Denda">
                                            <i class="ti ti-pencil text-sm"></i>
                                        </button>
                                    @endcan
                                    @can('denda.delete')
                                        <form method="POST" class="delete-form inline m-0" action="{{ route('denda.delete', Crypt::encrypt($d->id)) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" 
                                                    class="w-7 h-7 rounded-md hover:bg-rose-50 text-rose-600 hover:text-rose-700 flex items-center justify-center transition cursor-pointer" 
                                                    title="Hapus Denda">
                                                <i class="ti ti-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 px-4 text-slate-400 bg-white">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg mb-2">
                                    <i class="ti ti-receipt-off"></i>
                                </div>
                                <p class="font-bold text-slate-700 text-xs">Belum ada data skema denda keterlambatan</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol "Tambah Aturan Denda" untuk mengatur besaran denda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Universal V2 Modal for Denda -->
<div class="modal fade" id="modalV2Denda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 flex items-center justify-between text-white border-0"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs shrink-0">
                        <i class="ti ti-receipt-tax"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm font-extrabold text-white tracking-tight truncate mb-0" id="modalV2Title">Form Aturan Denda</h5>
                    </div>
                </div>
                <button type="button" class="text-white/80 hover:text-white transition p-1 rounded-lg hover:bg-white/10 cursor-pointer" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-6 bg-white" id="loadmodalV2Denda">
                <!-- Dynamic form loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        const modalEl = document.getElementById('modalV2Denda');
        const modalInstance = new bootstrap.Modal(modalEl);

        function showLoadingSpinner() {
            $('#loadmodalV2Denda').html(`
                <div class="py-10 flex flex-col items-center justify-center text-center">
                    <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mb-3"></div>
                    <p class="text-xs text-slate-500 font-medium">Memuat form denda...</p>
                </div>
            `);
        }

        // 1. Tambah Denda
        $('#btnCreate').click(function(e) {
            e.preventDefault();
            $('#modalV2Title').text('Tambah Aturan Denda');
            $('#modalV2Subtitle').text('Tentukan rentang menit keterlambatan dan nominal denda');
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2Denda').load("{{ route('denda.create') }}");
        });

        // 2. Edit Denda
        $('.btnEdit').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            $('#modalV2Title').text('Edit Aturan Denda');
            $('#modalV2Subtitle').text('Perbarui rentang menit atau nominal denda');
            modalInstance.show();
            showLoadingSpinner();
            $('#loadmodalV2Denda').load('/denda/' + id + '/edit');
        });

        // 3. Delete Confirmation (SweetAlert2)
        $('.delete-form').submit(function(e) {
            e.preventDefault();
            const form = this;
            Swal.fire({
                title: 'Hapus Aturan Denda?',
                text: 'Data skema denda ini akan dihapus dari sistem.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
