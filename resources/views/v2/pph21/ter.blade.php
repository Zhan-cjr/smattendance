@extends('v2.layouts.app')

@section('titlepage', 'Tabel Tarif TER PPh 21')
@section('page_title', 'Tabel TER PPh 21')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & SUB-NAVIGASI) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-table text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Tabel Tarif Efektif Rata-rata (TER)</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Tarif Efektif Bulanan Kategori A, B, dan C sesuai Peraturan Pemerintah (PP) No. 58 Tahun 2023
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
                <span class="font-bold text-slate-800">Tabel TER</span>
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
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95 text-white"
           style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-table text-sm"></i>
            <span>Tabel TER</span>
        </a>
        <a href="{{ route('pph21.progresif') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-chart-bar text-sm"></i>
            <span>Tarif Progresif</span>
        </a>
        <a href="{{ route('pph21.simulasi') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-emerald-50 text-emerald-700 border border-emerald-200 bg-white">
            <i class="ti ti-calculator text-sm"></i>
            <span>Simulasi Kalkulator</span>
        </a>
    </div>

    <!-- Info Banner Ringkas -->
    <div class="bg-gradient-to-r from-blue-50/70 via-indigo-50/40 to-slate-50 border border-blue-200/70 rounded-2xl p-4 flex items-start gap-3.5 shadow-2xs">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 text-blue-700 bg-white shadow-xs border border-blue-100">
            <i class="ti ti-info-circle text-xl"></i>
        </div>
        <div class="flex-1 min-w-0 text-xs text-slate-600 leading-relaxed">
            <span class="font-bold text-slate-800 block mb-0.5">Penetapan Kategori Tarif Efektif Rata-rata (TER) Bulanan PP 58/2023:</span>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-1.5 font-medium">
                <div class="p-2 rounded-lg bg-white/90 border border-blue-100">
                    <strong class="text-blue-700">Kategori A:</strong> TK/0 (54 jt), TK/1 (58.5 jt), K/0 (58.5 jt)
                </div>
                <div class="p-2 rounded-lg bg-white/90 border border-amber-100">
                    <strong class="text-amber-700">Kategori B:</strong> TK/2, TK/3, K/1, K/2 (63 jt - 72 jt)
                </div>
                <div class="p-2 rounded-lg bg-white/90 border border-rose-100">
                    <strong class="text-rose-700">Kategori C:</strong> K/3 (72 jt + 3 tanggungan = 77 jt)
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 2. 3 COLUMNS SIDE-BY-SIDE TABLES ================= -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 items-start">
        
        @php
            $tables = [
                [
                    'cat'       => 'A',
                    'title'     => 'Kategori A',
                    'ptkp_info' => 'TK/0, TK/1, K/0',
                    'badge_bg'  => 'bg-blue-500/20 border-blue-300/40 text-white',
                    'badge_pill'=> 'bg-blue-100 text-blue-800 border-blue-200',
                    'data'      => $terA
                ],
                [
                    'cat'       => 'B',
                    'title'     => 'Kategori B',
                    'ptkp_info' => 'TK/2, TK/3, K/1, K/2',
                    'badge_bg'  => 'bg-amber-500/20 border-amber-300/40 text-white',
                    'badge_pill'=> 'bg-amber-100 text-amber-800 border-amber-200',
                    'data'      => $terB
                ],
                [
                    'cat'       => 'C',
                    'title'     => 'Kategori C',
                    'ptkp_info' => 'K/3',
                    'badge_bg'  => 'bg-rose-500/20 border-rose-300/40 text-white',
                    'badge_pill'=> 'bg-rose-100 text-rose-800 border-rose-200',
                    'data'      => $terC
                ],
            ];
        @endphp

        @foreach($tables as $t)
        <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
            <!-- Card Header -->
            <div class="px-4 py-3 flex items-center justify-between text-white border-0"
                 style="background-color: {{ $themeColor1 }};">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20">
                        {{ $t['cat'] }}
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-tight leading-tight">{{ $t['title'] }}</h3>
                        <p class="text-[10px] text-white/80 font-medium mb-0">{{ $t['ptkp_info'] }}</p>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border backdrop-blur-xs shadow-2xs {{ $t['badge_bg'] }}">
                    {{ $t['data']->count() }} Lapisan
                </span>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
                <table class="w-full text-left text-xs border-0 border-collapse">
                    <thead class="text-white font-bold uppercase tracking-wider text-[10px] border-0 border-t-0 border-b border-black/15"
                           style="background-color: {{ $themeColor1 }};">
                        <tr class="border-0 border-t-0 text-white/90">
                            <th class="py-2 px-2 text-center w-8 text-white/90 border-0 border-t-0">#</th>
                            <th class="py-2 px-2.5 text-end text-white/90 border-0 border-t-0">Dari (Rp)</th>
                            <th class="py-2 px-2.5 text-end text-white/90 border-0 border-t-0">Sampai (Rp)</th>
                            <th class="py-2 px-2 text-center w-16 text-white/90 border-0 border-t-0">Tarif</th>
                            <th class="py-2 px-2 text-center w-10 text-white/90 border-0 border-t-0">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white text-[11px]">
                        @forelse($t['data'] as $idx => $r)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-1.5 px-2 text-center text-slate-400 font-bold">
                                {{ $idx + 1 }}
                            </td>
                            <td class="py-1.5 px-2.5 text-end font-bold text-slate-900 font-mono whitespace-nowrap">
                                {{ number_format($r->penghasilan_dari, 0, ',', '.') }}
                            </td>
                            <td class="py-1.5 px-2.5 text-end font-medium text-slate-700 font-mono whitespace-nowrap">
                                @if($r->penghasilan_sampai)
                                    {{ number_format($r->penghasilan_sampai, 0, ',', '.') }}
                                @else
                                    <span class="text-[10px] font-bold text-slate-400 font-sans">Tak Terbatas</span>
                                @endif
                            </td>
                            <td class="py-1.5 px-2 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-1.5 py-0.2 rounded-md text-[10px] font-black {{ $r->tarif_persen > 0 ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                    {{ number_format($r->tarif_persen, 2) }}%
                                </span>
                            </td>
                            <td class="py-1.5 px-2 text-center">
                                <button type="button" 
                                        class="btnEditTer w-6 h-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition active:scale-95 cursor-pointer mx-auto"
                                        data-id="{{ $r->id }}"
                                        data-tarif="{{ $r->tarif_persen }}"
                                        data-kategori="{{ $t['cat'] }}"
                                        data-dari="Rp {{ number_format($r->penghasilan_dari, 0, ',', '.') }}"
                                        data-sampai="{{ $r->penghasilan_sampai ? 'Rp ' . number_format($r->penghasilan_sampai, 0, ',', '.') : 'Tak Terbatas' }}"
                                        title="Edit Tarif">
                                    <i class="ti ti-edit text-xs"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400 text-xs">
                                Belum ada data tarif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach

    </div>

</div>

<!-- ================= 3. MODAL EDIT TARIF TER (V2 STANDARD) ================= -->
<div class="modal fade" id="modalEditTer" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
            
            <!-- Standard V2 Modal Header -->
            <div class="px-5 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-percentage"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0">
                            Edit Tarif TER
                        </h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalSubTitle">
                            Kategori A
                        </p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="formUpdateTer" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <!-- Range Info Card -->
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                    <div class="flex justify-between text-slate-500">
                        <span>Rentang Bruto:</span>
                    </div>
                    <div class="font-bold text-slate-800 text-xs flex items-center justify-between">
                        <span id="label_dari">-</span>
                        <span class="text-slate-400">&rarr;</span>
                        <span id="label_sampai">-</span>
                    </div>
                </div>

                <!-- Input Tarif Persen -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-slate-700">
                        Tarif Persentase (%) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0" max="100" name="tarif_persen" id="edit_tarif"
                               class="w-full h-10 pl-3 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                    </div>
                    <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_tarif_persen"></p>
                </div>

                <!-- Action Buttons -->
                <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" id="btnSubmitTer"
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
    // Open modal edit TER
    $(document).on('click', '.btnEditTer', function () {
        let id = $(this).data('id');
        let tarif = $(this).data('tarif');
        let kategori = $(this).data('kategori');
        let dari = $(this).data('dari');
        let sampai = $(this).data('sampai');

        $('#edit_tarif').val(tarif).removeClass('border-rose-500');
        $('#err_tarif_persen').addClass('hidden').text('');
        $('#modalSubTitle').text('Kategori ' + kategori);
        $('#label_dari').text(dari);
        $('#label_sampai').text(sampai);
        $('#formUpdateTer').attr('action', `/pph21/ter/${id}`);
        $('#modalEditTer').modal('show');
    });

    // Form submit validation & loading state
    $('#formUpdateTer').on('submit', function (e) {
        let val = $('#edit_tarif').val();
        if (val === '' || isNaN(val) || parseFloat(val) < 0 || parseFloat(val) > 100) {
            e.preventDefault();
            $('#edit_tarif').addClass('border-rose-500');
            $('#err_tarif_persen').removeClass('hidden').text('Tarif harus berada di rentang 0% - 100%');
            return false;
        }

        let btn = $('#btnSubmitTer');
        btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed')
           .html('<i class="ti ti-loader animate-spin text-sm"></i><span>Menyimpan...</span>');
    });
});
</script>
@endpush
