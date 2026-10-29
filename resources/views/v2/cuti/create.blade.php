<form action="{{ route('cuti.store') }}" method="POST" id="formCutiV2" class="space-y-4" novalidate>
    @csrf

    <div class="space-y-4">
        <!-- Banner Info -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Ketentuan Kode & Jenis Cuti</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Kode cuti maksimal 3 karakter alfanumerik (misal: C01, C02, CTN) dan bersifat unik. Jumlah hari merupakan batas alokasi kuota per pengajuan/tahun.
                </p>
            </div>
        </div>

        <!-- Kode Cuti -->
        <div class="space-y-1.5">
            <label for="kode_cuti" class="block text-xs font-bold text-slate-700">
                Kode Cuti <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-barcode text-base"></i>
                </span>
                <input type="text" 
                       id="kode_cuti" 
                       name="kode_cuti" 
                       maxlength="3" 
                       placeholder="Contoh: C01" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition uppercase">
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Huruf kapital & angka</span>
                <span id="kodeCutiCount">0/3</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_cuti"></div>
        </div>

        <!-- Jenis Cuti -->
        <div class="space-y-1.5">
            <label for="jenis_cuti" class="block text-xs font-bold text-slate-700">
                Jenis Cuti <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-calendar-event text-base"></i>
                </span>
                <input type="text" 
                       id="jenis_cuti" 
                       name="jenis_cuti" 
                       maxlength="50" 
                       placeholder="Contoh: Cuti Tahunan, Cuti Melahirkan" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Maksimal 50 karakter</span>
                <span id="jenisCutiCount">0/50</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jenis_cuti"></div>
        </div>

        <!-- Jumlah Hari -->
        <div class="space-y-1.5">
            <label for="jumlah_hari" class="block text-xs font-bold text-slate-700">
                Jumlah Hari / Kuota (Hari) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-clock-hour-4 text-base"></i>
                </span>
                <input type="number" 
                       id="jumlah_hari" 
                       name="jumlah_hari" 
                       min="1" 
                       max="365" 
                       placeholder="Contoh: 12" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Minimal 1 hari, maksimal 365 hari</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jumlah_hari"></div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitCutiV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Cuti</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formCutiV2');
        const kodeInput = document.getElementById('kode_cuti');
        const jenisInput = document.getElementById('jenis_cuti');
        const hariInput = document.getElementById('jumlah_hari');
        const kodeCount = document.getElementById('kodeCutiCount');
        const jenisCount = document.getElementById('jenisCutiCount');

        if (kodeInput) {
            kodeInput.addEventListener('input', function(e) {
                let val = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                if (val.length > 3) val = val.substring(0, 3);
                e.target.value = val;
                kodeCount.textContent = `${val.length}/3`;
            });
        }

        if (jenisInput) {
            jenisInput.addEventListener('input', function(e) {
                let val = e.target.value;
                if (val.length > 50) val = val.substring(0, 50);
                e.target.value = val;
                jenisCount.textContent = `${val.length}/50`;
            });
        }

        $(form).on('submit', function(e) {
            const kode = kodeInput ? kodeInput.value.trim() : '';
            const jenis = jenisInput ? jenisInput.value.trim() : '';
            const hari = hariInput ? hariInput.value.trim() : '';
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $(kodeInput).removeClass('border-rose-500');
            $(jenisInput).removeClass('border-rose-500');
            $(hariInput).removeClass('border-rose-500');

            if (!kode) {
                $('#err_kode_cuti').text('Kode Cuti wajib diisi').removeClass('hidden');
                $(kodeInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!jenis) {
                $('#err_jenis_cuti').text('Jenis Cuti wajib diisi').removeClass('hidden');
                $(jenisInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!hari || parseInt(hari) < 1 || parseInt(hari) > 365) {
                $('#err_jumlah_hari').text('Jumlah hari wajib diisi antara 1 - 365 hari').removeClass('hidden');
                $(hariInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitCutiV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
