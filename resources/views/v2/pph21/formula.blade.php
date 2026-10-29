@extends('v2.layouts.app')

@section('titlepage', 'Formula Komponen PPh 21')
@section('page_title', 'Formula Komponen PPh 21')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $totalKomponen = $komponens->count();
    $totalAktif = $komponens->where('status_aktif', true)->count();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & SUB-NAVIGASI) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-function text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Formula Komponen PPh 21</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Tentukan komponen penghasilan atau potongan yang masuk dalam perhitungan Bruto PPh 21
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
                <span class="font-bold text-slate-800">Formula Komponen</span>
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
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95 text-white"
           style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-function text-sm"></i>
            <span>Formula Komponen</span>
        </a>
        <a href="{{ route('pph21.ter') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
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

    <!-- ================= 2. MAIN CONTENT ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- Left Column: Form Tambah Komponen (4 Cols) -->
        <div class="lg:col-span-5 xl:col-span-4 space-y-5">
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                <!-- Header -->
                <div class="px-5 py-3.5 flex items-center justify-between text-white"
                     style="background-color: {{ $themeColor1 }};">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                            <i class="ti ti-plus"></i>
                        </div>
                        <h3 class="text-sm font-extrabold text-white tracking-tight">Tambah Komponen</h3>
                    </div>
                </div>

                <!-- Form Body -->
                <form action="{{ route('pph21.formula.store') }}" method="POST" id="formAddFormula" class="p-5 space-y-4">
                    @csrf

                    <!-- Nama Komponen -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Nama Komponen <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_komponen" id="nama_komponen"
                               placeholder="contoh: Gaji Pokok, Tunjangan Makan"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                        <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_nama_komponen"></p>
                    </div>

                    <!-- Tipe -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Tipe Komponen <span class="text-rose-500">*</span>
                        </label>
                        <select name="tipe" id="tipe" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                            <option value="penambah">➕ Penambah (Masuk Bruto)</option>
                            <option value="pengurang">➖ Pengurang (Dikurangi dari Bruto)</option>
                        </select>
                        <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_tipe"></p>
                    </div>

                    <!-- Sumber Data -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Sumber Data <span class="text-rose-500">*</span>
                        </label>
                        <select name="sumber" id="selectSumber" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                            <option value="gaji_pokok">Gaji Pokok</option>
                            <option value="tunjangan">Tunjangan (Pilih Jenis)</option>
                            <option value="bpjs_kesehatan">BPJS Kesehatan (Perusahaan)</option>
                            <option value="bpjs_tenagakerja">BPJS Ketenagakerjaan (JKK/JKM)</option>
                            <option value="lembur">Upah Lembur</option>
                        </select>
                        <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_sumber"></p>
                    </div>

                    <!-- Spesifik Jenis Tunjangan -->
                    <div class="space-y-1" id="wrapKodeSumber" style="display: none;">
                        <label class="block text-xs font-bold text-slate-700">
                            Jenis Tunjangan Terkait
                        </label>
                        <select name="kode_sumber" id="kode_sumber" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                            <option value="">-- Semua Jenis Tunjangan --</option>
                            @foreach($jenistunjangan as $jt)
                                <option value="{{ $jt->kode_jenis_tunjangan }}">{{ $jt->jenis_tunjangan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="btnSubmitFormula"
                                class="w-full py-2.5 text-white font-bold text-xs rounded-xl shadow-md transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95 flex items-center justify-center gap-1.5"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-plus text-sm"></i>
                            <span>Tambah ke Formula</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Guide Banner -->
            <div class="bg-gradient-to-r from-emerald-50/80 to-teal-50/50 border border-emerald-200/80 rounded-2xl p-4 flex items-start gap-3 shadow-2xs">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-emerald-700 bg-white shadow-xs border border-emerald-100">
                    <i class="ti ti-info-circle text-lg"></i>
                </div>
                <div class="flex-1 min-w-0 text-xs text-slate-600 leading-relaxed">
                    <strong class="text-slate-800 block mb-0.5">Rumus Penghasilan Bruto PPh 21:</strong>
                    Bruto = &Sigma; Komponen Penambah &minus; &Sigma; Komponen Pengurang. Hanya komponen dengan status <strong>Aktif</strong> yang akan dikalkulasi oleh sistem.
                </div>
            </div>
        </div>

        <!-- Right Column: Daftar Komponen Table (8 Cols) -->
        <div class="lg:col-span-7 xl:col-span-8 space-y-5">
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                <!-- Header -->
                <div class="px-5 py-3.5 flex items-center justify-between text-white"
                     style="background-color: {{ $themeColor1 }};">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                            <i class="ti ti-list"></i>
                        </div>
                        <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Komponen Formula Aktif</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                            {{ $totalAktif }} Aktif
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-white/90 border border-white/20 shadow-2xs backdrop-blur-xs">
                            {{ $totalKomponen }} Total
                        </span>
                    </div>
                </div>

                <!-- Table Component -->
                <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
                    <table class="w-full text-left text-xs border-0 border-collapse">
                        <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                               style="background-color: {{ $themeColor1 }};">
                            <tr class="border-0 border-t-0 text-white/90">
                                <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">#</th>
                                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Nama Komponen</th>
                                <th class="py-2.5 px-3 text-center text-white/90 border-0 border-t-0">Tipe</th>
                                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Sumber Data</th>
                                <th class="py-2.5 px-3 text-center text-white/90 border-0 border-t-0">Status</th>
                                <th class="py-2.5 px-3 text-center w-24 text-white/90 border-0 border-t-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                            @forelse($komponens as $idx => $k)
                            <tr class="hover:bg-slate-50/70 transition-colors {{ !$k->status_aktif ? 'bg-slate-50/50 opacity-75' : '' }}">
                                <td class="py-2 px-3.5 text-center text-slate-400 font-bold">
                                    {{ $k->urutan ?? ($idx + 1) }}
                                </td>
                                <td class="py-2 px-4 font-bold text-slate-900">
                                    {{ $k->nama_komponen }}
                                </td>
                                <td class="py-2 px-3 text-center">
                                    @if($k->tipe === 'penambah')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            <i class="ti ti-plus text-[10px]"></i> Penambah
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200">
                                            <i class="ti ti-minus text-[10px]"></i> Pengurang
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 text-slate-600">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                        @if($k->sumber === 'gaji_pokok') 
                                            <i class="ti ti-cash text-xs text-slate-500"></i> Gaji Pokok
                                        @elseif($k->sumber === 'tunjangan') 
                                            <i class="ti ti-gift text-xs text-slate-500"></i> Tunjangan{{ $k->kode_sumber ? ' (' . $k->kode_sumber . ')' : ' (Semua)' }}
                                        @elseif($k->sumber === 'bpjs_kesehatan') 
                                            <i class="ti ti-heart-handshake text-xs text-slate-500"></i> BPJS Kesehatan
                                        @elseif($k->sumber === 'bpjs_tenagakerja') 
                                            <i class="ti ti-shield-check text-xs text-slate-500"></i> BPJS TK
                                        @elseif($k->sumber === 'lembur') 
                                            <i class="ti ti-clock-play text-xs text-slate-500"></i> Lembur
                                        @endif
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <form method="POST" action="{{ route('pph21.formula.toggle', $k->id) }}" class="inline-block m-0">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold transition cursor-pointer hover:opacity-85 active:scale-95 {{ $k->status_aktif ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}"
                                                title="Klik untuk ubah status">
                                            <i class="ti {{ $k->status_aktif ? 'ti-check' : 'ti-x' }} text-[10px]"></i>
                                            <span>{{ $k->status_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <div class="inline-flex items-center justify-center gap-1">
                                        <form method="POST" action="{{ route('pph21.formula.destroy', $k->id) }}" class="form-delete-formula m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                    class="btn-delete-formula w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                                    title="Hapus Komponen">
                                                <i class="ti ti-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <i class="ti ti-function-off text-3xl mb-2 block text-slate-300"></i>
                                    <span>Belum ada komponen formula yang didaftarkan.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('myscript')
<script>
$(function () {
    // Toggle field jenis tunjangan
    function toggleSumber() {
        if ($('#selectSumber').val() === 'tunjangan') {
            $('#wrapKodeSumber').slideDown(150);
        } else {
            $('#wrapKodeSumber').slideUp(150);
        }
    }
    
    $('#selectSumber').on('change', toggleSumber);
    toggleSumber();

    // Client-side Validation & submit handling
    $('#formAddFormula').on('submit', function (e) {
        $('.error-feedback').addClass('hidden').text('');
        $('input, select').removeClass('border-rose-500');

        let isValid = true;
        let nama = $('#nama_komponen').val().trim();

        if (!nama) {
            $('#nama_komponen').addClass('border-rose-500');
            $('#err_nama_komponen').removeClass('hidden').text('Nama komponen wajib diisi');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }

        let btn = $('#btnSubmitFormula');
        btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed')
           .html('<i class="ti ti-loader animate-spin text-sm"></i><span>Menyimpan...</span>');
    });

    // SweetAlert2 konfirmasi hapus formula
    $(document).on('click', '.btn-delete-formula', function (e) {
        e.preventDefault();
        let form = $(this).closest('form');
        
        Swal.fire({
            title: 'Hapus Komponen?',
            text: "Komponen ini tidak akan dihitung lagi dalam rumus Bruto PPh 21.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'swal2-confirm',
                cancelButton: 'swal2-cancel'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
