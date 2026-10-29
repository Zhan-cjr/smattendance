@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('lemburaturan.store') }}" method="POST" id="formAturanLembur" class="space-y-4" novalidate>
    @csrf
    <input type="hidden" name="tipe_hari" value="{{ $tipe_hari }}">

    <!-- Banner Info -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start justify-between gap-3">
        <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg {{ $tipe_hari == 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center text-sm shrink-0">
                <i class="ti {{ $tipe_hari == 1 ? 'ti-briefcase' : 'ti-calendar-off' }}"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Aturan {{ $tipe_hari == 1 ? 'Hari Kerja (Senin - Sabtu)' : 'Hari Libur (Minggu & Nasional)' }}</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Tentukan batas rentang jam lembur dan nilai faktor pengali (multiplier) untuk jam netto.
                </p>
            </div>
        </div>
        <span class="font-bold {{ $tipe_hari == 1 ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-amber-700 bg-amber-50 border border-amber-200' }} px-2.5 py-0.5 rounded-md text-[10.5px] shrink-0">
            {{ $tipe_hari == 1 ? 'Hari Kerja' : 'Hari Libur' }}
        </span>
    </div>

    <!-- Rentang Jam Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Mulai Jam -->
        <div class="space-y-1.5">
            <label for="jam_dari" class="block text-xs font-bold text-slate-700">
                Mulai Jam <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-clock-play text-base"></i>
                </span>
                <input type="number" step="0.5" min="0" name="jam_dari" id="jam_dari" 
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                       placeholder="Contoh: 0" required>
            </div>
            <p class="text-[10.5px] text-slate-400">Mulai dari jam ke berapa (0 = jam pertama)</p>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_dari"></div>
        </div>

        <!-- Sampai Jam -->
        <div class="space-y-1.5">
            <label for="jam_sampai" class="block text-xs font-bold text-slate-700">
                Sampai Jam
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-clock-stop text-base"></i>
                </span>
                <input type="number" step="0.5" min="0" name="jam_sampai" id="jam_sampai" 
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                       placeholder="Contoh: 1">
            </div>
            <p class="text-[10.5px] text-slate-400">Kosongkan bila berlaku untuk seterusnya</p>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_sampai"></div>
        </div>
    </div>

    <!-- Faktor Pengali -->
    <div class="space-y-1.5">
        <label for="faktor" class="block text-xs font-bold text-slate-700">
            Faktor Pengali (Multiplier) <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                <i class="ti ti-percentage text-base"></i>
            </span>
            <input type="number" step="0.05" min="0" name="faktor" id="faktor" 
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                   placeholder="Contoh: 1.5 atau 2.0" required>
        </div>
        <p class="text-[10.5px] text-slate-400">Nilai pengali jam lembur aktual menjadi jam netto</p>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_faktor"></div>
    </div>

    <!-- Submit Button -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" id="btnSimpanAturan" 
                class="px-5 py-2 text-xs font-bold text-white rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer hover:opacity-95 active:scale-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Aturan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formAturanLembur');
        const jamDariInput = document.getElementById('jam_dari');
        const jamSampaiInput = document.getElementById('jam_sampai');
        const faktorInput = document.getElementById('faktor');

        $(form).on('submit', function(e) {
            const jamDari = jamDariInput.value.trim();
            const jamSampai = jamSampaiInput.value.trim();
            const faktor = faktorInput.value.trim();
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $(jamDariInput).removeClass('border-rose-500');
            $(jamSampaiInput).removeClass('border-rose-500');
            $(faktorInput).removeClass('border-rose-500');

            if (jamDari === "") {
                $('#err_jam_dari').text('Mulai Jam wajib diisi').removeClass('hidden');
                $(jamDariInput).addClass('border-rose-500');
                hasError = true;
            }

            if (jamDari !== "" && jamSampai !== "" && parseFloat(jamDari) >= parseFloat(jamSampai)) {
                $('#err_jam_sampai').text('Sampai Jam harus lebih besar dari Mulai Jam').removeClass('hidden');
                $(jamSampaiInput).addClass('border-rose-500');
                hasError = true;
            }

            if (faktor === "") {
                $('#err_faktor').text('Faktor Pengali wajib diisi').removeClass('hidden');
                $(faktorInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSimpanAturan');
            $btn.prop('disabled', true).html('<i class="ti ti-loader animate-spin text-sm"></i><span>Menyimpan...</span>');
        });
    })();
</script>
