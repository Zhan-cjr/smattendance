@extends('v2.layouts.app')

@section('titlepage', 'Tarif Progresif PPh 21')
@section('page_title', 'Tarif Progresif PPh 21')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalRates = $rates->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & SUB-NAVIGASI) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-chart-bar text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Tarif Progresif PPh 21</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Tarif Pajak Penghasilan Pasal 17 ayat (1) huruf a UU PPh (UU Harmonisasi Peraturan Perpajakan / UU HPP)
            </p>
        </div>

        <!-- Breadcrumb Navigation -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('pph21.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-receipt-tax text-sm"></i>
                    <span>PPh 21</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Tarif Progresif</span>
            </nav>
        </div>
    </div>

    <!-- Sub-navigasi Tabs PPh 21 -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-200">
        <a href="{{ route('pph21.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-settings text-sm"></i>
            <span>Pengaturan Global</span>
        </a>
        <a href="{{ route('pph21.formula') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-function text-sm"></i>
            <span>Formula Komponen</span>
        </a>
        <a href="{{ route('pph21.ter') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-table text-sm"></i>
            <span>Tabel TER</span>
        </a>
        <a href="{{ route('pph21.progresif') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95 text-white"
           style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-chart-bar text-sm"></i>
            <span>Tarif Progresif</span>
        </a>
        <a href="{{ route('pph21.simulasi') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-emerald-50 text-emerald-700 border border-emerald-200 bg-white">
            <i class="ti ti-calculator text-sm"></i>
            <span>Simulasi Kalkulator</span>
        </a>
    </div>

    <!-- Info Banner -->
    <div class="bg-gradient-to-r from-emerald-50/70 via-teal-50/40 to-slate-50 border border-emerald-200/70 rounded-2xl p-4 flex items-start gap-3.5 shadow-2xs">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 text-emerald-700 bg-white shadow-xs border border-emerald-100">
            <i class="ti ti-info-circle text-xl"></i>
        </div>
        <div class="flex-1 min-w-0 text-xs text-slate-600 leading-relaxed">
            <span class="font-bold text-slate-800 block mb-0.5">Penggunaan Tarif Progresif Pasal 17:</span>
            Tarif lapisan Penghasilan Kena Pajak (PKP) ini digunakan pada perhitungan PPh 21 masa pajak terakhir (Bulan Desember) untuk penyesuaian tahunan, atau bagi skema perhitungan PPh 21 Progresif Manual.
        </div>
    </div>

    <!-- ================= 2. SEAMLESS SOLID TABLE VIEW ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden max-w-4xl">
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex items-center justify-between text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-chart-bar"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">Lapisan Penghasilan Kena Pajak (PKP)</h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                {{ $totalRates }} Lapisan Tarif
            </span>
        </div>

        <!-- Table Container with Matching Solid Header -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">#</th>
                        <th class="py-2.5 px-4 text-end text-white/90 border-0 border-t-0">PKP Dari (Rp)</th>
                        <th class="py-2.5 px-4 text-end text-white/90 border-0 border-t-0">Sampai Dengan (Rp)</th>
                        <th class="py-2.5 px-4 text-center w-36 text-white/90 border-0 border-t-0">Tarif Pajak (%)</th>
                        <th class="py-2.5 px-3 text-center w-20 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse($rates as $idx => $r)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold">
                            {{ $idx + 1 }}
                        </td>
                        <td class="py-2.5 px-4 text-end font-bold text-slate-900 font-mono text-xs sm:text-sm">
                            Rp {{ number_format($r->pkp_dari, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-4 text-end font-semibold text-slate-800 font-mono text-xs sm:text-sm">
                            @if($r->pkp_sampai)
                                Rp {{ number_format($r->pkp_sampai, 0, ',', '.') }}
                            @else
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-600 font-sans">Tak Terbatas</span>
                            @endif
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-black bg-blue-100 text-blue-700 border border-blue-200">
                                {{ number_format($r->tarif_persen, 0) }}%
                            </span>
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            <button type="button" 
                                    class="btnEditProgresif w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition active:scale-95 cursor-pointer mx-auto"
                                    data-id="{{ $r->id }}"
                                    data-tarif="{{ $r->tarif_persen }}"
                                    data-dari="{{ $r->pkp_dari }}"
                                    data-sampai="{{ $r->pkp_sampai }}"
                                    title="Edit Lapisan Tarif">
                                <i class="ti ti-edit text-sm"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">
                            Belum ada data tarif progresif.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= 3. MODAL EDIT TARIF PROGRESIF (V2 STANDARD) ================= -->
<div class="modal fade" id="modalEditProgresif" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
            
            <!-- Standard V2 Modal Header -->
            <div class="px-5 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-chart-bar"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0">
                            Edit Lapisan Progresif
                        </h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium">
                            Pasal 17 UU PPh / UU HPP
                        </p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="formUpdateProgresif" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <!-- PKP Dari -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">
                        PKP Mulai Dari (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="pkp_dari" id="edit_dari"
                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
                    <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_pkp_dari"></p>
                </div>

                <!-- PKP Sampai -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">
                        PKP Sampai Dengan (Rp)
                    </label>
                    <input type="number" name="pkp_sampai" id="edit_sampai" placeholder="Kosongkan jika tak terbatas"
                           class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                    <p class="text-[11px] text-slate-400">Kosongkan jika batas atas tidak terhingga (> 5 Miliar).</p>
                </div>

                <!-- Input Tarif Persen -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">
                        Tarif Pajak (%) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" name="tarif_persen" id="edit_tarif_prog"
                               class="w-full h-10 pl-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                    </div>
                    <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_tarif_prog"></p>
                </div>

                <!-- Action Buttons -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitProg"
                            class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-device-floppy text-sm"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
$(function () {
    // Open modal edit progresif
    $(document).on('click', '.btnEditProgresif', function () {
        let id = $(this).data('id');
        let tarif = $(this).data('tarif');
        let dari = $(this).data('dari');
        let sampai = $(this).data('sampai');

        $('#edit_tarif_prog').val(tarif).removeClass('border-rose-500');
        $('#edit_dari').val(dari).removeClass('border-rose-500');
        $('#edit_sampai').val(sampai);
        $('.error-feedback').addClass('hidden').text('');
        $('#formUpdateProgresif').attr('action', `/pph21/progresif/${id}`);
        $('#modalEditProgresif').modal('show');
    });

    // Form submit validation & loading state
    $('#formUpdateProgresif').on('submit', function (e) {
        $('.error-feedback').addClass('hidden').text('');
        $('input').removeClass('border-rose-500');

        let isValid = true;
        let dari = $('#edit_dari').val();
        let tarif = $('#edit_tarif_prog').val();

        if (dari === '' || isNaN(dari) || parseInt(dari) < 0) {
            $('#edit_dari').addClass('border-rose-500');
            $('#err_pkp_dari').removeClass('hidden').text('PKP Mulai Dari wajib diisi');
            isValid = false;
        }

        if (tarif === '' || isNaN(tarif) || parseFloat(tarif) < 0 || parseFloat(tarif) > 100) {
            $('#edit_tarif_prog').addClass('border-rose-500');
            $('#err_tarif_prog').removeClass('hidden').text('Tarif harus berada di rentang 0% - 100%');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }

        let btn = $('#btnSubmitProg');
        btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed')
           .html('<i class="ti ti-loader animate-spin text-sm"></i><span>Menyimpan...</span>');
    });
});
</script>
@endpush
