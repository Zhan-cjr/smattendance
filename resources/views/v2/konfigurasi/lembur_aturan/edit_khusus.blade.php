@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('lemburaturan.updatekhusus', $khusus->id) }}" method="POST" id="formLemburKhususUpdate" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Banner Info / Identitas Pegawai -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-700 font-black text-sm flex items-center justify-center border border-slate-300 shrink-0 shadow-2xs">
            {{ strtoupper(substr($khusus->nama_karyawan, 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <h5 class="text-xs font-bold text-slate-900 truncate">{{ $khusus->nama_karyawan }}</h5>
            <p class="text-[11px] text-slate-500 font-mono mt-0.5">NIK: {{ $khusus->nik }}</p>
        </div>
        <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-white text-slate-700 border border-slate-200 shrink-0">
            Pegawai
        </span>
    </div>

    <!-- Upah Lembur / Jam -->
    <div class="space-y-1.5">
        <label for="upah_perjam_khusus_edit" class="block text-xs font-bold text-slate-700">
            Tarif Upah Lembur / Jam (Nominal) <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-xs pointer-events-none">Rp</span>
            <input type="text" name="upah_perjam" id="upah_perjam_khusus_edit" 
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition money" 
                   value="{{ number_format($khusus->upah_perjam, 0, ',', '.') }}" required>
        </div>
        <p class="text-[10.5px] text-slate-400">Nominal flat ini akan dikalikan langsung dengan JAM AKTUAL lembur.</p>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_upah_khusus_edit"></div>
    </div>

    <!-- Status -->
    <div class="space-y-1.5">
        <label for="status_khusus" class="block text-xs font-bold text-slate-700">
            Status Lembur Khusus <span class="text-rose-500">*</span>
        </label>
        <select name="status" id="status_khusus" class="w-full h-10 px-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            <option value="1" {{ $khusus->status == 1 ? 'selected' : '' }}>Aktif (Gunakan Flat Rate Khusus)</option>
            <option value="0" {{ $khusus->status == 0 ? 'selected' : '' }}>Non-Aktif (Kembali ke Aturan Standard)</option>
        </select>
    </div>

    <!-- Keterangan -->
    <div class="space-y-1.5">
        <label for="keterangan_edit" class="block text-xs font-bold text-slate-700">
            Keterangan / Alasan Khusus
        </label>
        <textarea name="keterangan" id="keterangan_edit" rows="2" 
                  class="w-full px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                  placeholder="Contoh: Kesepakatan flat rate driver / security / HRD">{{ $khusus->keterangan }}</textarea>
    </div>

    <!-- Submit Button -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" id="btnUpdateKhusus" 
                class="px-5 py-2 text-xs font-bold text-white rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer hover:opacity-95 active:scale-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Perbarui Lembur Khusus</span>
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

        const form = document.getElementById('formLemburKhususUpdate');
        const upahInput = document.getElementById('upah_perjam_khusus_edit');

        $(form).on('submit', function(e) {
            const upah = upahInput.value.trim();
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $(upahInput).removeClass('border-rose-500');

            if (!upah) {
                $('#err_upah_khusus_edit').text('Tarif Upah per jam wajib diisi').removeClass('hidden');
                $(upahInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnUpdateKhusus');
            $btn.prop('disabled', true).html('<i class="ti ti-loader animate-spin text-sm"></i><span>Memperbarui...</span>');
        });
    })();
</script>
