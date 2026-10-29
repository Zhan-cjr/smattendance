@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('periodegaji.update', $periode_gaji->kode_periode_gaji) }}" method="POST" id="formPeriodeEditV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <div class="space-y-4">
        <!-- Banner Info -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Edit Konfigurasi Periode</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Perubahan rentang cut-off atau metode siklus bulan akan mempengaruhi perhitungan absensi dan payroll periode mendatang.
                </p>
            </div>
        </div>

        <!-- Kode Periode (Readonly) & Nama Periode -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <!-- Kode Periode -->
            <div class="sm:col-span-4 space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">
                    Kode Periode
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-hash text-base"></i>
                    </span>
                    <input type="text" value="{{ $periode_gaji->kode_periode_gaji }}" 
                           readonly disabled
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-mono font-bold text-slate-700 bg-slate-100/80 border border-slate-200 rounded-xl cursor-not-allowed shadow-2xs">
                </div>
                <div class="text-[10.5px] text-slate-400">Kode unik tidak dapat diubah</div>
            </div>

            <!-- Nama Periode -->
            <div class="sm:col-span-8 space-y-1.5">
                <label for="v2_nama_periode_edit" class="block text-xs font-bold text-slate-700">
                    Nama Periode <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-tag text-base"></i>
                    </span>
                    <input type="text" 
                           id="v2_nama_periode_edit" 
                           name="nama_periode" 
                           value="{{ $periode_gaji->nama_periode }}"
                           maxlength="50"
                           placeholder="Nama Periode" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs">
                </div>
                <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                    <span>Maksimal 50 karakter</span>
                    <span id="namaPeriodeEditCount">{{ strlen($periode_gaji->nama_periode) }}/50</span>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nama_periode_edit"></div>
            </div>
        </div>

        <!-- Rentang Cut-Off (Tanggal Mulai s/d Tanggal Sampai) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Tanggal Dari -->
            <div class="space-y-1.5">
                <label for="v2_tanggal_dari_edit" class="block text-xs font-bold text-slate-700">
                    Tanggal Mulai (Cut-Off Dari) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-calendar-event text-base"></i>
                    </span>
                    <input type="number" 
                           id="v2_tanggal_dari_edit" 
                           name="tanggal_dari" 
                           min="1" 
                           max="31" 
                           value="{{ $periode_gaji->tanggal_dari }}" 
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs">
                </div>
                <div class="text-[10.5px] text-slate-400">Tanggal awal perhitungan (1 - 31)</div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_tanggal_dari_edit"></div>
            </div>

            <!-- Tanggal Sampai -->
            <div class="space-y-1.5">
                <label for="v2_tanggal_sampai_edit" class="block text-xs font-bold text-slate-700">
                    Tanggal Sampai (Cut-Off Sampai) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-calendar-time text-base"></i>
                    </span>
                    <input type="number" 
                           id="v2_tanggal_sampai_edit" 
                           name="tanggal_sampai" 
                           min="1" 
                           max="31" 
                           value="{{ $periode_gaji->tanggal_sampai }}" 
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs">
                </div>
                <div class="text-[10.5px] text-slate-400">Tanggal akhir perhitungan (1 - 31)</div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_tanggal_sampai_edit"></div>
            </div>
        </div>

        <!-- Metode Rentang Tanggal -->
        <div class="space-y-1.5">
            <label for="v2_metode_lintas_bulan_edit" class="block text-xs font-bold text-slate-700">
                Metode Rentang Tanggal (Siklus Bulan) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-arrows-left-right text-base"></i>
                </span>
                <select name="metode_lintas_bulan" id="v2_metode_lintas_bulan_edit"
                        class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs cursor-pointer">
                    <option value="0" {{ $periode_gaji->metode_lintas_bulan == 0 ? 'selected' : '' }}>0 - Satu Bulan yang Sama (Contoh: 1 s/d 31 bulan berjalan)</option>
                    <option value="1" {{ $periode_gaji->metode_lintas_bulan == 1 ? 'selected' : '' }}>1 - Bulan Lalu ke Bulan Berjalan (Contoh: 21 bulan lalu s/d 20 bulan ini)</option>
                    <option value="2" {{ $periode_gaji->metode_lintas_bulan == 2 ? 'selected' : '' }}>2 - Bulan Berjalan ke Bulan Depan (Contoh: 2 bulan ini s/d 1 bulan depan)</option>
                </select>
            </div>
            <div class="text-[10.5px] text-slate-400">Pola siklus penarikan data absensi dan payroll</div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_metode_lintas_bulan_edit"></div>
        </div>

        <!-- Switch Status Aktif -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-base shrink-0 border border-emerald-200">
                    <i class="ti ti-circle-check"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-slate-800">Status Periode Aktif</h5>
                    <p class="text-[11px] text-slate-500">Dapat digunakan pada penempatan karyawan & penggajian</p>
                </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $periode_gaji->is_active ? 'checked' : '' }} class="sr-only peer">
                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
            </label>
        </div>
    </div>

    <!-- Modal Footer Actions -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="btnCloseModalUniversal px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitPeriodeEditV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, {{ $themeColor1 }}), var(--theme-color-2, {{ $themeColor2 }}));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Update Periode</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formPeriodeEditV2');
        const namaInput = document.getElementById('v2_nama_periode_edit');
        const tglDariInput = document.getElementById('v2_tanggal_dari_edit');
        const tglSampaiInput = document.getElementById('v2_tanggal_sampai_edit');
        const metodeInput = document.getElementById('v2_metode_lintas_bulan_edit');
        const namaCount = document.getElementById('namaPeriodeEditCount');

        if (namaInput && namaCount) {
            namaInput.addEventListener('input', function(e) {
                let val = e.target.value;
                if (val.length > 50) val = val.substring(0, 50);
                e.target.value = val;
                namaCount.textContent = `${val.length}/50`;
            });
        }

        $(form).on('submit', function(e) {
            const nama = namaInput ? namaInput.value.trim() : '';
            const tglDari = tglDariInput ? parseInt(tglDariInput.value) : 0;
            const tglSampai = tglSampaiInput ? parseInt(tglSampaiInput.value) : 0;
            const metode = metodeInput ? metodeInput.value : '';
            let hasError = false;

            // Reset error states
            $(form).find('.error-feedback').addClass('hidden').text('');
            $(form).find('input, select').removeClass('border-rose-500');

            if (!nama) {
                $('#err_nama_periode_edit').text('Nama Periode wajib diisi').removeClass('hidden');
                $(namaInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!tglDari || isNaN(tglDari) || tglDari < 1 || tglDari > 31) {
                $('#err_tanggal_dari_edit').text('Tanggal mulai harus antara 1 s/d 31').removeClass('hidden');
                $(tglDariInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!tglSampai || isNaN(tglSampai) || tglSampai < 1 || tglSampai > 31) {
                $('#err_tanggal_sampai_edit').text('Tanggal sampai harus antara 1 s/d 31').removeClass('hidden');
                $(tglSampaiInput).addClass('border-rose-500');
                hasError = true;
            }

            if (metode === '' || metode === null) {
                $('#err_metode_lintas_bulan_edit').text('Pilih metode siklus bulan').removeClass('hidden');
                $(metodeInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitPeriodeEditV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Memperbarui...</span>');
        });
    })();
</script>
