@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('penyesuaiangaji.updatekaryawan', [Crypt::encrypt($detailpenyesuaian->kode_penyesuaian_gaji), Crypt::encrypt($detailpenyesuaian->nik)]) }}" method="POST" id="formEditKaryawanV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Info Banner -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-edit"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Perbarui Penyesuaian Gaji Karyawan</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Sesuaikan nominal penambah, pengurang, atau catatan alasan penyesuaian gaji karyawan ini.
            </p>
        </div>
    </div>

    <!-- 1. Karyawan (Readonly Select) -->
    <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700">
            Karyawan
        </label>
        <div class="relative">
            <select name="nik_disabled" id="edit_karyawan_nik" class="w-full px-3.5 py-2.5 text-xs bg-slate-100/80 border border-slate-200 rounded-xl text-slate-600 font-bold outline-none cursor-not-allowed select2ModalNikEdit" disabled>
                @foreach ($karyawan as $d)
                    <option value="{{ $d->nik }}" {{ $d->nik == $detailpenyesuaian->nik ? 'selected' : '' }}>
                        {{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- 2. Grid Penambah & Pengurang -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Penambah -->
        <div class="space-y-1.5">
            <label for="edit_penambah" class="block text-xs font-bold text-emerald-700">
                Koreksi Penambah (Rp) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs pointer-events-none z-10">
                    +
                </span>
                <input type="text" 
                       id="edit_penambah" 
                       name="penambah" 
                       value="{{ formatAngka($detailpenyesuaian->penambah) }}"
                       placeholder="0"
                       autocomplete="off"
                       class="w-full h-10 pl-8 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition modal-money-input text-right" 
                       required>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_edit_penambah"></div>
        </div>

        <!-- Pengurang -->
        <div class="space-y-1.5">
            <label for="edit_pengurang" class="block text-xs font-bold text-rose-600">
                Koreksi Pengurang (Rp) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs pointer-events-none z-10">
                    -
                </span>
                <input type="text" 
                       id="edit_pengurang" 
                       name="pengurang" 
                       value="{{ formatAngka($detailpenyesuaian->pengurang) }}"
                       placeholder="0"
                       autocomplete="off"
                       class="w-full h-10 pl-8 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition modal-money-input text-right" 
                       required>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_edit_pengurang"></div>
        </div>
    </div>

    <!-- 3. Keterangan -->
    <div class="space-y-1.5">
        <label for="edit_keterangan" class="block text-xs font-bold text-slate-700">
            Keterangan Penyesuaian <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <textarea name="keterangan" 
                      id="edit_keterangan" 
                      rows="3" 
                      placeholder="Masukkan keterangan penyesuaian..."
                      class="w-full px-3.5 py-2.5 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition custom-scrollbar"
                      required>{{ $detailpenyesuaian->keterangan }}</textarea>
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_edit_keterangan"></div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                onclick="$('#modalSetKaryawan').addClass('hidden'); $('body').removeClass('overflow-hidden');">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitEditKaryawanV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Perbarui Karyawan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formEditKaryawanV2');
        const penambahInput = document.getElementById('edit_penambah');
        const pengurangInput = document.getElementById('edit_pengurang');
        const keteranganInput = document.getElementById('edit_keterangan');

        $(form).on('submit', function(e) {
            const penambah = $(penambahInput).val();
            const pengurang = $(pengurangInput).val();
            const keterangan = $(keteranganInput).val();
            let hasError = false;

            $('.error-feedback').addClass('hidden').text('');
            $(penambahInput).removeClass('border-rose-500');
            $(pengurangInput).removeClass('border-rose-500');
            $(keteranganInput).removeClass('border-rose-500');

            if (penambah === '' && pengurang === '') {
                $('#err_edit_penambah').text('Minimal isi salah satu nominal penambah atau pengurang').removeClass('hidden');
                $(penambahInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!keterangan || keterangan.trim() === '') {
                $('#err_edit_keterangan').text('Keterangan penyesuaian wajib diisi').removeClass('hidden');
                $(keteranganInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitEditKaryawanV2');
            $btn.prop('disabled', true).html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Menyimpan...</span>
            `);
        });
    })();
</script>
