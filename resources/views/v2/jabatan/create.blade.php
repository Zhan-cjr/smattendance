<form action="{{ route('jabatan.store') }}" method="POST" id="formJabatanV2" class="space-y-4" novalidate>
    @csrf

    <div class="space-y-4">
        <!-- Banner Info -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Ketentuan Kode & Nama Jabatan</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Kode jabatan terdiri dari maksimal 3 karakter alfanumerik kapital (misal: MGR, STF, DIR, SPV) dan bersifat unik.
                </p>
            </div>
        </div>

        <!-- Kode Jabatan -->
        <div class="space-y-1.5">
            <label for="kode_jabatan" class="block text-xs font-bold text-slate-700">
                Kode Jabatan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-barcode text-base"></i>
                </span>
                <input type="text" 
                       id="kode_jabatan" 
                       name="kode_jabatan" 
                       maxlength="3" 
                       placeholder="Contoh: MGR" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition uppercase">
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Gunakan huruf kapital & angka</span>
                <span id="kodeJabatanCount">0/3</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_jabatan"></div>
        </div>

        <!-- Nama Jabatan -->
        <div class="space-y-1.5">
            <label for="nama_jabatan" class="block text-xs font-bold text-slate-700">
                Nama Jabatan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-briefcase text-base"></i>
                </span>
                <input type="text" 
                       id="nama_jabatan" 
                       name="nama_jabatan" 
                       maxlength="30" 
                       placeholder="Contoh: Manager Operasional" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Maksimal 30 karakter</span>
                <span id="namaJabatanCount">0/30</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nama_jabatan"></div>
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
                id="btnSubmitJabatanV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Jabatan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formJabatanV2');
        const kodeInput = document.getElementById('kode_jabatan');
        const namaInput = document.getElementById('nama_jabatan');
        const kodeCount = document.getElementById('kodeJabatanCount');
        const namaCount = document.getElementById('namaJabatanCount');

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
                if (val.length > 30) val = val.substring(0, 30);
                e.target.value = val;
                namaCount.textContent = `${val.length}/30`;
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
                $('#err_kode_jabatan').text('Kode Jabatan wajib diisi').removeClass('hidden');
                $(kodeInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!nama) {
                $('#err_nama_jabatan').text('Nama Jabatan wajib diisi').removeClass('hidden');
                $(namaInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitJabatanV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
