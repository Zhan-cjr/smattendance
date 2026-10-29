@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('jenistunjangan.update', Crypt::encrypt($jenistunjangan->kode_jenis_tunjangan)) }}" method="POST" id="formEditJenisTunjanganV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <div class="space-y-4">
        <!-- Banner Info -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-edit"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Edit Komponen Tunjangan</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Kode tunjangan bersifat permanen dan tidak dapat diubah agar tidak merusak relasi data penggajian yang telah tercatat.
                </p>
            </div>
        </div>

        <!-- Kode Jenis Tunjangan (Readonly) -->
        <div class="space-y-1.5">
            <label for="modal_kode_jenis_tunjangan_edit" class="block text-xs font-bold text-slate-700">
                Kode Jenis Tunjangan
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-barcode text-base"></i>
                </span>
                <input type="text" 
                       id="modal_kode_jenis_tunjangan_edit" 
                       name="kode_jenis_tunjangan" 
                       value="{{ $jenistunjangan->kode_jenis_tunjangan }}"
                       readonly 
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500 bg-slate-100 border border-slate-300 rounded-xl cursor-not-allowed outline-none font-mono">
            </div>
        </div>

        <!-- Nama Jenis Tunjangan -->
        <div class="space-y-1.5">
            <label for="modal_jenis_tunjangan_edit" class="block text-xs font-bold text-slate-700">
                Nama Jenis Tunjangan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-file-description text-base"></i>
                </span>
                <input type="text" 
                       id="modal_jenis_tunjangan_edit" 
                       name="jenis_tunjangan" 
                       value="{{ $jenistunjangan->jenis_tunjangan }}"
                       maxlength="50" 
                       placeholder="Contoh: Tunjangan Jabatan" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Maksimal 50 karakter</span>
                <span id="namaTunjanganEditCount">{{ strlen($jenistunjangan->jenis_tunjangan) }}/50</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jenis_tunjangan_edit"></div>
        </div>

        <!-- Sifat Tunjangan -->
        <div class="space-y-1.5">
            <label for="modal_status_tunjangan_edit" class="block text-xs font-bold text-slate-700">
                Sifat Tunjangan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-settings text-base"></i>
                </span>
                <select name="status_tunjangan" id="modal_status_tunjangan_edit" 
                        class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer" 
                        required>
                    <option value="1" {{ $jenistunjangan->status_tunjangan === '1' ? 'selected' : '' }}>Tetap (Jumlah Rutin Bulanan)</option>
                    <option value="0" {{ $jenistunjangan->status_tunjangan === '0' ? 'selected' : '' }}>Tidak Tetap (Fluktuatif / Berdasarkan Kehadiran)</option>
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_status_tunjangan_edit"></div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                onclick="$('#modalJenisTunjangan').addClass('hidden'); $('body').removeClass('overflow-hidden');">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitEditJenisTunjanganV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formEditJenisTunjanganV2');
        const namaInput = document.getElementById('modal_jenis_tunjangan_edit');
        const statusSelect = document.getElementById('modal_status_tunjangan_edit');
        const namaCount = document.getElementById('namaTunjanganEditCount');

        if (namaInput) {
            namaInput.addEventListener('input', function(e) {
                let val = e.target.value;
                if (val.length > 50) val = val.substring(0, 50);
                e.target.value = val;
                namaCount.textContent = `${val.length}/50`;
            });
        }

        $(form).on('submit', function(e) {
            const nama = $(namaInput).val().trim();
            const status = $(statusSelect).val();
            let hasError = false;

            // Reset error feedback states
            $('.error-feedback').addClass('hidden').text('');
            $(namaInput).removeClass('border-rose-500');

            if (!nama) {
                $('#err_jenis_tunjangan_edit').text('Nama Jenis Tunjangan wajib diisi').removeClass('hidden');
                $(namaInput).addClass('border-rose-500');
                hasError = true;
            } else if (nama.length > 50) {
                $('#err_jenis_tunjangan_edit').text('Nama Jenis Tunjangan maksimal 50 karakter').removeClass('hidden');
                $(namaInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            // Spinner Loading
            const $btn = $('#btnSubmitEditJenisTunjanganV2');
            $btn.prop('disabled', true).html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Menyimpan...</span>
            `);
        });
    })();
</script>
