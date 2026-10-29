@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('slipgaji.store') }}" method="POST" id="formSlipGajiCreateV2" class="space-y-4" novalidate>
    @csrf

    <!-- Info Banner -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-file-certificate"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Penerbitan Slip Gaji Bulanan</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Tentukan bulan, tahun, kelompok cut-off dan status penerbitan agar slip gaji dapat dilihat oleh karyawan.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Bulan -->
        <div class="space-y-1.5">
            <label for="create_slip_bulan" class="block text-xs font-bold text-slate-700">
                Bulan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar text-base"></i>
                </span>
                <select name="bulan" id="create_slip_bulan" class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer" required>
                    <option value="">-- Pilih Bulan --</option>
                    @foreach ($list_bulan as $d)
                        <option value="{{ $d['kode_bulan'] }}" {{ date('m') == $d['kode_bulan'] ? 'selected' : '' }}>
                            {{ $d['nama_bulan'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_create_slip_bulan"></div>
        </div>

        <!-- Tahun -->
        <div class="space-y-1.5">
            <label for="create_slip_tahun" class="block text-xs font-bold text-slate-700">
                Tahun <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar-event text-base"></i>
                </span>
                <select name="tahun" id="create_slip_tahun" class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer" required>
                    <option value="">-- Pilih Tahun --</option>
                    @for ($t = $start_year; $t <= date('Y'); $t++)
                        <option value="{{ $t }}" {{ date('Y') == $t ? 'selected' : '' }}>
                            Tahun {{ $t }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_create_slip_tahun"></div>
        </div>
    </div>

    <!-- Kelompok Periode Gaji -->
    <div class="space-y-1.5">
        <label for="create_slip_kode_periode_gaji" class="block text-xs font-bold text-slate-700">
            Kelompok Periode Cut-Off <span class="text-slate-400 font-normal">(Opsional)</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                <i class="ti ti-clock text-base"></i>
            </span>
            <select name="kode_periode_gaji" id="create_slip_kode_periode_gaji" class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                <option value="">- Tanpa Periode (Default Global) -</option>
                @foreach ($periode_gaji as $p)
                    <option value="{{ $p->kode_periode_gaji }}">
                        {{ $p->kode_periode_gaji }} - {{ $p->nama_periode }} (Tgl {{ $p->tanggal_dari }}-{{ $p->tanggal_sampai }})
                    </option>
                @endforeach
            </select>
        </div>
        <p class="text-[10.5px] text-slate-400 mt-1">Pilih jika ingin mempublish khusus kelompok karyawan periode cut-off tertentu.</p>
    </div>

    <!-- Status Penerbitan -->
    <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700">
            Status Penerbitan <span class="text-rose-500">*</span>
        </label>
        <div class="grid grid-cols-2 gap-3">
            <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50 cursor-pointer transition">
                <input type="radio" name="status" value="1" checked class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                <div>
                    <span class="block text-xs font-bold text-emerald-900">Publish (Live)</span>
                    <span class="block text-[10.5px] text-emerald-700">Dapat dilihat karyawan</span>
                </div>
            </label>
            <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-amber-200 bg-amber-50/50 hover:bg-amber-50 cursor-pointer transition">
                <input type="radio" name="status" value="0" class="w-4 h-4 text-amber-600 focus:ring-amber-500 border-slate-300">
                <div>
                    <span class="block text-xs font-bold text-amber-900">Pending (Draft)</span>
                    <span class="block text-[10.5px] text-amber-700">Draft internal admin</span>
                </div>
            </label>
        </div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="btnCloseModalBulanan px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitSlipGajiCreateV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-send text-sm"></i>
            <span>Terbitkan Slip Gaji</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formSlipGajiCreateV2');
        const bulanSelect = document.getElementById('create_slip_bulan');
        const tahunSelect = document.getElementById('create_slip_tahun');

        $(form).on('submit', function(e) {
            const bulan = $(bulanSelect).val();
            const tahun = $(tahunSelect).val();
            let hasError = false;

            $('.error-feedback').addClass('hidden').text('');
            $(bulanSelect).removeClass('border-rose-500');
            $(tahunSelect).removeClass('border-rose-500');

            if (!bulan) {
                $('#err_create_slip_bulan').text('Bulan periode wajib dipilih').removeClass('hidden');
                $(bulanSelect).addClass('border-rose-500');
                hasError = true;
            }

            if (!tahun) {
                $('#err_create_slip_tahun').text('Tahun periode wajib dipilih').removeClass('hidden');
                $(tahunSelect).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const btn = document.getElementById('btnSubmitSlipGajiCreateV2');
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Menyimpan...</span>
            `;
        });
    })();
</script>
