<form action="{{ route('grup.store') }}" method="POST" id="formGrupV2" class="space-y-4" novalidate>
    @csrf

    <div class="space-y-4">
        <!-- Banner Info -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Ketentuan Kode & Nama Grup</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Kode grup terdiri dari maksimal 3 karakter alfanumerik kapital (misal: G1, GR1, GP2) dan bersifat unik sebagai penanda regu kerja.
                </p>
            </div>
        </div>

        <!-- Kode Grup -->
        <div class="space-y-1.5">
            <label for="kode_grup" class="block text-xs font-bold text-slate-700">
                Kode Grup <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-barcode text-base"></i>
                </span>
                <input type="text" 
                       id="kode_grup" 
                       name="kode_grup" 
                       maxlength="3" 
                       placeholder="Contoh: GR1" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition uppercase">
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Gunakan huruf kapital & angka</span>
                <span id="kodeGrupCount">0/3</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_grup"></div>
        </div>

        <!-- Nama Grup -->
        <div class="space-y-1.5">
            <label for="nama_grup" class="block text-xs font-bold text-slate-700">
                Nama Grup <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-users-group text-base"></i>
                </span>
                <input type="text" 
                       id="nama_grup" 
                       name="nama_grup" 
                       maxlength="50" 
                       placeholder="Contoh: Regu A - Shift Produksi" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Maksimal 50 karakter</span>
                <span id="namaGrupCount">0/50</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nama_grup"></div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitGrupV2" 
                class="px-5 py-2 text-xs font-bold text-white rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer hover:opacity-95 active:scale-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 ?? '#0a5c41' }}, {{ $themeColor2 ?? '#117956' }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Grup</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formGrupV2');
        const kodeInput = document.getElementById('kode_grup');
        const namaInput = document.getElementById('nama_grup');
        const kodeCount = document.getElementById('kodeGrupCount');
        const namaCount = document.getElementById('namaGrupCount');

        if (kodeInput) {
            kodeInput.addEventListener('input', function(e) {
                let val = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                if (val.length > 3) val = val.substring(0, 3);
                e.target.value = val;
                kodeCount.textContent = `${val.length}/3`;
            });
        }

        if (namaInput) {
            namaInput.addEventListener('input', function(e) {
                let val = e.target.value;
                if (val.length > 50) val = val.substring(0, 50);
                e.target.value = val;
                namaCount.textContent = `${val.length}/50`;
            });
        }

        $(form).on('submit', function(e) {
            const kode = kodeInput.value.trim();
            const nama = namaInput.value.trim();
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $(kodeInput).removeClass('border-rose-500');
            $(namaInput).removeClass('border-rose-500');

            if (!kode) {
                $('#err_kode_grup').text('Kode Grup wajib diisi').removeClass('hidden');
                $(kodeInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!nama) {
                $('#err_nama_grup').text('Nama Grup wajib diisi').removeClass('hidden');
                $(namaInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            $('#btnSubmitGrupV2').prop('disabled', true).html(`
                <i class="ti ti-loader animate-spin text-sm"></i>
                <span>Menyimpan...</span>
            `);
        });
    })();
</script>
