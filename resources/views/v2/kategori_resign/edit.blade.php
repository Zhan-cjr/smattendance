<form action="{{ route('kategori_resign.update', Crypt::encrypt($kategori_resign->kode_kategori)) }}" method="POST" id="formKategoriResignEditV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <div class="space-y-4">
        <!-- Banner Info -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-edit"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Edit Kategori Resign</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Perubahan nama kategori resign akan otomatis disinkronkan ke riwayat penghentian kerja karyawan.
                </p>
            </div>
        </div>

        <!-- Kode Kategori (Readonly) -->
        <div class="space-y-1.5">
            <label for="kode_kategori_edit" class="block text-xs font-bold text-slate-700">
                Kode Kategori
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-barcode text-base"></i>
                </span>
                <input type="text" 
                       id="kode_kategori_edit" 
                       name="kode_kategori" 
                       value="{{ $kategori_resign->kode_kategori }}"
                       readonly 
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500 bg-slate-100 border border-slate-300 rounded-xl outline-none cursor-not-allowed">
            </div>
        </div>

        <!-- Nama Kategori Resign -->
        <div class="space-y-1.5">
            <label for="nama_kategori_edit" class="block text-xs font-bold text-slate-700">
                Nama Kategori Resign <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-user-x text-base"></i>
                </span>
                <input type="text" 
                       id="nama_kategori_edit" 
                       name="nama_kategori" 
                       value="{{ $kategori_resign->nama_kategori }}"
                       maxlength="50" 
                       placeholder="Contoh: Mengundurkan Diri, PHK Efisiensi" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Maksimal 50 karakter</span>
                <span id="namaKategoriCountEdit">{{ strlen($kategori_resign->nama_kategori) }}/50</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nama_kategori_edit"></div>
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
                id="btnSubmitKategoriResignEditV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formKategoriResignEditV2');
        const namaInput = document.getElementById('nama_kategori_edit');
        const namaCount = document.getElementById('namaKategoriCountEdit');

        if (namaInput) {
            namaInput.addEventListener('input', function(e) {
                let val = e.target.value;
                if (val.length > 50) val = val.substring(0, 50);
                e.target.value = val;
                namaCount.textContent = `${val.length}/50`;
            });
        }

        $(form).on('submit', function(e) {
            const nama = namaInput ? namaInput.value.trim() : '';
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $(namaInput).removeClass('border-rose-500');

            if (!nama) {
                $('#err_nama_kategori_edit').text('Nama Kategori Resign wajib diisi').removeClass('hidden');
                $(namaInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitKategoriResignEditV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
