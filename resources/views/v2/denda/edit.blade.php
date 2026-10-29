@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('denda.update', ['id' => Crypt::encrypt($denda->id)]) }}" method="POST" id="formDendaUpdate" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Banner Info -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-edit"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Edit Skema Denda</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Sesuaikan batas menit keterlambatan atau perbarui besaran nominal denda.
            </p>
        </div>
    </div>

    <!-- Rentang Menit Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Dari (Menit) -->
        <div class="space-y-1.5">
            <label for="dari_edit" class="block text-xs font-bold text-slate-700">
                Dari (Menit) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-clock-play text-base"></i>
                </span>
                <input type="number" min="0" name="dari" id="dari_edit" 
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                       value="{{ $denda->dari }}" placeholder="Contoh: 1" required>
            </div>
            <p class="text-[10.5px] text-slate-400">Mulai menit keterlambatan</p>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_dari_edit"></div>
        </div>

        <!-- Sampai (Menit) -->
        <div class="space-y-1.5">
            <label for="sampai_edit" class="block text-xs font-bold text-slate-700">
                Sampai (Menit) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-clock-stop text-base"></i>
                </span>
                <input type="number" min="0" name="sampai" id="sampai_edit" 
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                       value="{{ $denda->sampai }}" placeholder="Contoh: 15" required>
            </div>
            <p class="text-[10.5px] text-slate-400">Sampai dengan menit ke-berapa</p>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_sampai_edit"></div>
        </div>
    </div>

    <!-- Nominal Denda -->
    <div class="space-y-1.5">
        <label for="denda_edit" class="block text-xs font-bold text-slate-700">
            Nominal Potongan Denda <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-xs pointer-events-none">Rp</span>
            <input type="text" name="denda" id="denda_edit" 
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition money" 
                   value="{{ formatAngka($denda->denda) }}" placeholder="Contoh: 10.000" required>
        </div>
        <p class="text-[10.5px] text-slate-400">Nominal potongan per kejadian keterlambatan pada tier ini</p>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_denda_edit"></div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" 
                id="btnUpdateDenda" 
                class="px-5 py-2 text-xs font-bold text-white rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer hover:opacity-95 active:scale-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Perbarui Denda</span>
        </button>
    </div>
</form>

<script>
    (function() {
        // Initialize MaskMoney
        if ($.isFunction($.fn.maskMoney)) {
            $('.money').maskMoney({
                thousands: '.',
                decimal: ',',
                precision: 0,
                allowZero: true,
                suffix: ''
            });
            $('.money').maskMoney('mask');
        }

        const form = document.getElementById('formDendaUpdate');
        const dariInput = document.getElementById('dari_edit');
        const sampaiInput = document.getElementById('sampai_edit');
        const dendaInput = document.getElementById('denda_edit');

        $(form).on('submit', function(e) {
            const dari = dariInput.value.trim();
            const sampai = sampaiInput.value.trim();
            const denda = dendaInput.value.trim();
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $(dariInput).removeClass('border-rose-500');
            $(sampaiInput).removeClass('border-rose-500');
            $(dendaInput).removeClass('border-rose-500');

            if (!dari) {
                $('#err_dari_edit').text('Field Dari (Menit) wajib diisi').removeClass('hidden');
                $(dariInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!sampai) {
                $('#err_sampai_edit').text('Field Sampai (Menit) wajib diisi').removeClass('hidden');
                $(sampaiInput).addClass('border-rose-500');
                hasError = true;
            }

            if (dari && sampai && parseFloat(dari) > parseFloat(sampai)) {
                $('#err_sampai_edit').text('Menit Sampai harus lebih besar atau sama dengan Menit Dari').removeClass('hidden');
                $(sampaiInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!denda) {
                $('#err_denda_edit').text('Field Nominal Denda wajib diisi').removeClass('hidden');
                $(dendaInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            $('#btnUpdateDenda').prop('disabled', true).html(`
                <i class="ti ti-loader animate-spin text-sm"></i>
                <span>Memperbarui...</span>
            `);
        });
    })();
</script>
