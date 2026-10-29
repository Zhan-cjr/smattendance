@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('penyesuaiangaji.storekaryawan', Crypt::encrypt($kode_penyesuaian_gaji)) }}" method="POST" id="formAddKaryawanV2" class="space-y-4" novalidate>
    @csrf

    <!-- Info Banner -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-user-plus"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Tambah Penyesuaian Upah Karyawan</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Pilih karyawan, isi nominal penambah (bonus/revisi lebih) atau pengurang (potongan lain), beserta keterangannya.
            </p>
        </div>
    </div>

    <!-- 1. Pilih Karyawan -->
    <div class="space-y-1.5">
        <label for="add_karyawan_nik" class="block text-xs font-bold text-slate-700">
            Pilih Karyawan <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <select name="nik" id="add_karyawan_nik" class="w-full px-3 py-2.5 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer select2ModalNik" required>
                <option value="">-- Pilih Karyawan --</option>
                @foreach ($karyawan as $d)
                    <option value="{{ $d->nik }}">
                        {{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_add_karyawan_nik"></div>
    </div>

    <!-- 2. Grid Penambah & Pengurang -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Penambah -->
        <div class="space-y-1.5">
            <label for="add_penambah" class="block text-xs font-bold text-emerald-700">
                Koreksi Penambah (Rp) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs pointer-events-none z-10">
                    +
                </span>
                <input type="text" 
                       id="add_penambah" 
                       name="penambah" 
                       value="0"
                       placeholder="0"
                       autocomplete="off"
                       class="w-full h-10 pl-8 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition modal-money-input text-right" 
                       required>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_add_penambah"></div>
        </div>

        <!-- Pengurang -->
        <div class="space-y-1.5">
            <label for="add_pengurang" class="block text-xs font-bold text-rose-600">
                Koreksi Pengurang (Rp) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs pointer-events-none z-10">
                    -
                </span>
                <input type="text" 
                       id="add_pengurang" 
                       name="pengurang" 
                       value="0"
                       placeholder="0"
                       autocomplete="off"
                       class="w-full h-10 pl-8 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition modal-money-input text-right" 
                       required>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_add_pengurang"></div>
        </div>
    </div>

    <!-- 3. Keterangan -->
    <div class="space-y-1.5">
        <label for="add_keterangan" class="block text-xs font-bold text-slate-700">
            Keterangan Penyesuaian <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <textarea name="keterangan" 
                      id="add_keterangan" 
                      rows="3" 
                      placeholder="Contoh: Koreksi kekurangan lembur bulan lalu, penggantian transport tugas luar..."
                      class="w-full px-3.5 py-2.5 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition custom-scrollbar"
                      required></textarea>
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_add_keterangan"></div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                onclick="$('#modalSetKaryawan').addClass('hidden'); $('body').removeClass('overflow-hidden');">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitAddKaryawanV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Karyawan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formAddKaryawanV2');
        const nikSelect = document.getElementById('add_karyawan_nik');
        const penambahInput = document.getElementById('add_penambah');
        const pengurangInput = document.getElementById('add_pengurang');
        const keteranganInput = document.getElementById('add_keterangan');

        $(form).on('submit', function(e) {
            const nik = $(nikSelect).val();
            const penambah = $(penambahInput).val();
            const pengurang = $(pengurangInput).val();
            const keterangan = $(keteranganInput).val();
            let hasError = false;

            $('.error-feedback').addClass('hidden').text('');
            $(nikSelect).removeClass('border-rose-500');
            $(penambahInput).removeClass('border-rose-500');
            $(pengurangInput).removeClass('border-rose-500');
            $(keteranganInput).removeClass('border-rose-500');

            if (!nik) {
                $('#err_add_karyawan_nik').text('Karyawan wajib dipilih').removeClass('hidden');
                hasError = true;
            }

            if (penambah === '' && pengurang === '') {
                $('#err_add_penambah').text('Minimal isi salah satu nominal penambah atau pengurang').removeClass('hidden');
                $(penambahInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!keterangan || keterangan.trim() === '') {
                $('#err_add_keterangan').text('Keterangan penyesuaian wajib diisi').removeClass('hidden');
                $(keteranganInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitAddKaryawanV2');
            $btn.prop('disabled', true).html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Menyimpan...</span>
            `);
        });
    })();
</script>
