@extends('v2.layouts.app')

@section('titlepage', 'Simulasi Kalkulator PPh 21')
@section('page_title', 'Simulasi Kalkulator PPh 21')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER (TITLE & SUB-NAVIGASI) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-calculator text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Simulasi Kalkulator PPh 21</span>
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Hitung estimasi potongan PPh 21 secara interaktif berdasarkan parameter dan formula yang aktif
            </p>
        </div>

        <!-- Breadcrumb Navigation -->
        <div class="flex flex-col md:items-end gap-2">
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
                <span class="font-bold text-slate-800">Simulasi</span>
            </nav>
        </div>
    </div>

    <!-- Sub-navigasi Tabs PPh 21 -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-200">
        <a href="{{ route('pph21.index') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-settings text-sm"></i>
            <span>Pengaturan Global</span>
        </a>
        <a href="{{ route('pph21.formula') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-function text-sm"></i>
            <span>Formula Komponen</span>
        </a>
        <a href="{{ route('pph21.ter') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-table text-sm"></i>
            <span>Tabel TER</span>
        </a>
        <a href="{{ route('pph21.progresif') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all hover:bg-slate-100 text-slate-600 border border-slate-200 bg-white">
            <i class="ti ti-chart-bar text-sm"></i>
            <span>Tarif Progresif</span>
        </a>
        <a href="{{ route('pph21.simulasi') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold shadow-xs transition-all active:scale-95 text-white"
           style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-calculator text-sm"></i>
            <span>Simulasi Kalkulator</span>
        </a>
    </div>

    <!-- ================= 2. SIMULASI ENGINE (FORM & REAL-TIME RESULT) ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        
        <!-- Form Input Data Simulasi (5 Cols) -->
        <div class="lg:col-span-5 xl:col-span-5 space-y-4">
            <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                <!-- Header -->
                <div class="px-4 py-3 flex items-center justify-between text-white"
                     style="background-color: {{ $themeColor1 }};">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                            <i class="ti ti-calculator"></i>
                        </div>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-tight">Input Komponen Simulasi</h3>
                    </div>
                </div>

                <!-- Form Body -->
                <form id="formSimulasi" class="p-4 space-y-3">
                    @csrf
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <!-- Bulan Perhitungan -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">
                                Bulan Pajak <span class="text-rose-500">*</span>
                            </label>
                            <select name="bulan" id="selectBulan" class="w-full h-9 px-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                                @foreach(config('global.list_bulan') as $b)
                                <option value="{{ $b['kode_bulan'] }}" {{ $b['kode_bulan'] == date('n') ? 'selected' : '' }}>
                                    {{ $b['nama_bulan'] }} {{ $b['kode_bulan'] == 12 ? '(Masa Pajak Terakhir)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status PTKP -->
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">
                                Status PTKP <span class="text-rose-500">*</span>
                            </label>
                            <select name="kode_status_kawin" id="kode_status_kawin" class="w-full h-9 px-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                                @foreach($statuskawin as $sk)
                                <option value="{{ $sk->kode_status_kawin }}">
                                    {{ $sk->kode_status_kawin }} (TER {{ $sk->kategori_ter ?? '-' }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Divider: Komponen Penghasilan -->
                    <div class="pt-1">
                        <div class="flex items-center gap-1.5 pb-1.5 border-b border-slate-100 text-xs font-bold text-slate-800">
                            <i class="ti ti-coins text-emerald-600 text-sm"></i>
                            <span>Penghasilan Bruto</span>
                        </div>
                    </div>

                    <!-- Gaji Pokok -->
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-700">
                            Gaji Pokok <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="text" name="gaji_pokok" id="gaji_pokok" placeholder="0"
                                   class="w-full h-9 pl-8 pr-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition input-rupiah" required>
                        </div>
                        <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_gaji_pokok"></p>
                    </div>

                    <!-- Dynamic Tunjangan List -->
                    @if($jenistunjangan->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($jenistunjangan as $jt)
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700 truncate" title="{{ $jt->jenis_tunjangan }}">
                                {{ $jt->jenis_tunjangan }}
                            </label>
                            <div class="relative">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" name="tunjangan[{{ $jt->kode_jenis_tunjangan }}]" placeholder="0"
                                       class="w-full h-8.5 pl-8 pr-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition input-rupiah">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Divider: Potongan / BPJS -->
                    <div class="pt-1">
                        <div class="flex items-center gap-1.5 pb-1.5 border-b border-slate-100 text-xs font-bold text-slate-800">
                            <i class="ti ti-shield-check text-rose-600 text-sm"></i>
                            <span>Iuran BPJS (Premi / Potongan)</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">
                                BPJS Kesehatan
                            </label>
                            <div class="relative">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" name="bpjs_kesehatan" placeholder="0"
                                       class="w-full h-8.5 pl-8 pr-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition input-rupiah">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-700">
                                BPJS TK (JKK/JKM/JHT)
                            </label>
                            <div class="relative">
                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" name="bpjs_tenagakerja" placeholder="0"
                                       class="w-full h-8.5 pl-8 pr-2.5 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition input-rupiah">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2 border-t border-slate-100">
                        <button type="submit" id="btnHitung"
                                class="w-full py-2.5 text-white font-bold text-xs rounded-xl shadow-sm transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95 flex items-center justify-center gap-1.5"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-calculator text-sm"></i>
                            <span>Hitung Estimasi PPh 21</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Card Hasil Kalkulasi (7 Cols) -->
        <div class="lg:col-span-7 xl:col-span-7 space-y-4">
            
            <!-- Result Box -->
            <div id="cardHasil" class="hidden bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
                <div class="px-4 py-3 flex items-center justify-between text-white bg-emerald-700">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold">
                            <i class="ti ti-receipt-tax"></i>
                        </div>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-tight">Hasil Kalkulasi PPh 21</h3>
                    </div>
                    <span id="badgeBulanHasil" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs">
                        Bulan Berjalan
                    </span>
                </div>

                <div class="p-4 sm:p-5" id="hasilSimulasi">
                    <!-- Loaded dynamically via AJAX -->
                </div>
            </div>

            <!-- Loading Skeleton / Spinner State -->
            <div id="cardLoading" class="hidden bg-white border border-slate-200/90 rounded-2xl shadow-xs p-10 text-center">
                <div class="inline-flex p-3 rounded-2xl bg-emerald-50 text-emerald-600 mb-2.5 animate-pulse">
                    <i class="ti ti-loader animate-spin text-2xl"></i>
                </div>
                <h4 class="text-xs font-bold text-slate-800">Sedang Mengkalkulasi...</h4>
                <p class="text-[11px] text-slate-500 mt-0.5">Mencocokkan komponen formula dan tabel TER aktif</p>
            </div>

            <!-- Placeholder Ready State -->
            <div id="cardPlaceholder" class="bg-white border border-slate-200/90 rounded-2xl shadow-xs p-8 sm:p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 border border-slate-200/60 shadow-2xs">
                    <i class="ti ti-calculator text-2xl"></i>
                </div>
                <h3 class="text-sm font-extrabold text-slate-800">Kalkulator Siap Digunakan</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 leading-relaxed">
                    Silakan lengkapi data komponen gaji di form sebelah kiri lalu tekan tombol <strong>Hitung Estimasi PPh 21</strong>.
                </p>
            </div>

        </div>

    </div>

</div>

@endsection

@push('myscript')
<script>
$(function () {
    // Format Rupiah Input
    $('.input-rupiah').on('input', function () {
        let val = $(this).val().replace(/\D/g, '');
        $(this).val(val ? parseInt(val).toLocaleString('id-ID') : '');
    });

    function formatRp(num) {
        return 'Rp ' + parseInt(num || 0).toLocaleString('id-ID');
    }

    // Submit Simulation
    $('#formSimulasi').on('submit', function (e) {
        e.preventDefault();

        $('.error-feedback').addClass('hidden').text('');
        $('input').removeClass('border-rose-500');

        let gajiVal = $('#gaji_pokok').val().trim();
        if (!gajiVal) {
            $('#gaji_pokok').addClass('border-rose-500');
            $('#err_gaji_pokok').removeClass('hidden').text('Gaji pokok wajib diisi untuk kalkulasi');
            return false;
        }

        let formData = {};
        $(this).serializeArray().forEach(function (item) {
            let val = item.value.replace(/\./g, '').replace(/,/g, '.');
            formData[item.name] = isNaN(val) || val === '' ? item.value : val;
        });

        $('#cardPlaceholder').addClass('hidden');
        $('#cardHasil').addClass('hidden');
        $('#cardLoading').removeClass('hidden');

        $.ajax({
            url: '{{ route("pph21.simulasi.hitung") }}',
            method: 'POST',
            data: Object.assign(formData, { _token: '{{ csrf_token() }}' }),
            success: function (res) {
                $('#cardLoading').addClass('hidden');
                if (!res.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menghitung',
                        text: res.message || 'Terjadi kesalahan pada sistem kalkulasi.',
                        customClass: { confirmButton: 'swal2-confirm' },
                        buttonsStyling: false
                    });
                    $('#cardPlaceholder').removeClass('hidden');
                    return;
                }

                const d = res.data;
                const isDesember = d.bulan == 12;

                let kompHTML = '';
                (d.detail_komponen || []).forEach(function (k) {
                    kompHTML += `
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-1.5 px-3 text-slate-600 text-[11px]">${k.nama_komponen}</td>
                        <td class="py-1.5 px-3 text-end font-mono font-bold text-[11px] ${k.tipe==='penambah'?'text-emerald-600':'text-rose-600'}">
                            ${k.tipe==='penambah'?'+':'-'} ${formatRp(k.nilai)}
                        </td>
                    </tr>`;
                });

                let html = `
                <!-- Summary Badges Top -->
                <div class="grid grid-cols-2 gap-2.5 p-3 bg-slate-50 rounded-xl border border-slate-200 mb-4 text-center">
                    <div class="border-r border-slate-200 pr-2">
                        <span class="text-[10px] font-bold text-slate-400 block">Metode Perhitungan</span>
                        <div class="font-black text-slate-800 text-xs sm:text-sm mt-0.5 flex items-center justify-center gap-1.5">
                            <span>${d.metode}</span>
                            ${isDesember ? '<span class="px-1.5 py-0.2 rounded text-[10px] bg-rose-100 text-rose-700 font-extrabold border border-rose-200">Masa Des</span>' : ''}
                        </div>
                    </div>
                    <div class="pl-2">
                        <span class="text-[10px] font-bold text-slate-400 block">Kategori TER / Status</span>
                        <div class="font-black text-emerald-700 text-xs sm:text-sm mt-0.5">
                            Kategori ${d.kategori_ter} (${d.kode_status_kawin})
                        </div>
                    </div>
                </div>

                <!-- Breakdown Komponen Table -->
                <div class="space-y-1.5 mb-4">
                    <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="ti ti-list text-sm text-emerald-600"></i>
                        <span>Rincian Penghasilan Bruto</span>
                    </h5>
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold text-[10px]">
                                <tr>
                                    <th class="py-1.5 px-3">Komponen</th>
                                    <th class="py-1.5 px-3 text-end">Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">${kompHTML}</tbody>
                            <tfoot class="bg-slate-100/70 font-bold text-slate-900 border-t border-slate-200 text-xs">
                                <tr>
                                    <td class="py-2 px-3">Total Penghasilan Bruto</td>
                                    <td class="py-2 px-3 text-end font-mono font-black text-emerald-800">${formatRp(d.bruto)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Kalkulasi Pajak Terutang -->
                <div class="space-y-1.5 mb-4">
                    <h5 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="ti ti-receipt-tax text-sm text-rose-600"></i>
                        <span>Kalkulasi Pajak Terutang</span>
                    </h5>
                    <div class="border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-100 text-xs">
                        <div class="flex items-center justify-between p-2">
                            <span class="text-slate-600 font-medium">Penghasilan Bruto Masa Ini:</span>
                            <span class="font-bold text-slate-800 font-mono">${formatRp(d.bruto)}</span>
                        </div>
                        ${!isDesember ? `
                        <div class="flex items-center justify-between p-2">
                            <span class="text-slate-600 font-medium">Tarif TER (Kategori ${d.kategori_ter}):</span>
                            <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 font-mono">${d.tarif_ter_persen}%</span>
                        </div>` : `
                        <div class="flex items-center justify-between p-2">
                            <span class="text-slate-600 font-medium">Penghasilan Neto Setahun:</span>
                            <span class="font-bold text-slate-800 font-mono">${formatRp(d.pkp_setahun + d.ptkp)}</span>
                        </div>
                        <div class="flex items-center justify-between p-2">
                            <span class="text-slate-600 font-medium">PTKP (${d.kode_status_kawin}):</span>
                            <span class="font-bold text-rose-600 font-mono">- ${formatRp(d.ptkp)}</span>
                        </div>
                        <div class="flex items-center justify-between p-2">
                            <span class="text-slate-600 font-medium">Penghasilan Kena Pajak (PKP):</span>
                            <span class="font-bold text-blue-700 font-mono">${formatRp(d.pkp_setahun)}</span>
                        </div>`}
                        <div class="flex items-center justify-between p-3 bg-emerald-50 text-emerald-950">
                            <span class="font-bold text-xs sm:text-sm">Potongan PPh 21 Terutang:</span>
                            <span class="font-black text-sm sm:text-base font-mono text-emerald-800">${formatRp(d.pph21_terutang)}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Summary Info -->
                <div class="p-3 rounded-xl border ${d.pph21_terutang > 0 ? 'bg-amber-50/80 border-amber-200 text-amber-900' : 'bg-emerald-50/80 border-emerald-200 text-emerald-900'} flex items-start gap-2 text-xs leading-relaxed">
                    <i class="ti ${d.pph21_terutang > 0 ? 'ti-alert-circle text-amber-600' : 'ti-circle-check text-emerald-600'} text-base shrink-0 mt-0.5"></i>
                    <div>
                        Estimasi potongan PPh 21 bulan ini adalah <strong>${formatRp(d.pph21_terutang)}</strong>.
                        Perhitungan menggunakan basis status <strong>${d.kode_status_kawin}</strong> (${d.nama_status_kawin}) dengan PTKP <strong>${formatRp(d.nilai_ptkp)}</strong>.
                    </div>
                </div>`;

                $('#hasilSimulasi').html(html);
                $('#cardHasil').removeClass('hidden');
            },
            error: function (xhr) {
                $('#cardLoading').addClass('hidden');
                $('#cardPlaceholder').removeClass('hidden');
                let msg = 'Terjadi kesalahan sistem saat kalkulasi.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: msg,
                    customClass: { confirmButton: 'swal2-confirm' },
                    buttonsStyling: false
                });
            }
        });
    });
});
</script>
@endpush
