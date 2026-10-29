@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('lemburaturan.storekhusus') }}" method="POST" id="formLemburKhusus" class="space-y-4" novalidate>
    @csrf

    <!-- Banner Info -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-user-plus"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Lembur Khusus Karyawan</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Karyawan yang didaftarkan pada lembur khusus akan dihitung dengan tarif per jam flat (tanpa formula pengali standar).
            </p>
        </div>
    </div>

    <!-- Pilih Karyawan -->
    <div class="space-y-1.5">
        <label for="nik_khusus" class="block text-xs font-bold text-slate-700">
            Pilih Karyawan <span class="text-rose-500">*</span>
        </label>
        <select name="nik" id="nik_khusus" class="w-full text-xs select2-khusus-v2" required>
            <option value="">Cari Nama / NIK Karyawan...</option>
        </select>
        <p class="text-[10.5px] text-slate-400">Ketik minimal 2-3 huruf untuk mencari nama karyawan</p>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nik_khusus"></div>
    </div>

    <!-- Upah Lembur / Jam -->
    <div class="space-y-1.5">
        <label for="upah_perjam_khusus" class="block text-xs font-bold text-slate-700">
            Tarif Upah Lembur / Jam (Nominal) <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-xs pointer-events-none">Rp</span>
            <input type="text" name="upah_perjam" id="upah_perjam_khusus" 
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition money" 
                   placeholder="Contoh: 25.000" required>
        </div>
        <p class="text-[10.5px] text-slate-400">Nominal flat ini akan dikalikan langsung dengan JAM AKTUAL lembur.</p>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_upah_khusus"></div>
    </div>

    <!-- Keterangan -->
    <div class="space-y-1.5">
        <label for="keterangan" class="block text-xs font-bold text-slate-700">
            Keterangan / Alasan Khusus
        </label>
        <textarea name="keterangan" id="keterangan" rows="2" 
                  class="w-full px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                  placeholder="Contoh: Kesepakatan flat rate driver / security / HRD"></textarea>
    </div>

    <!-- Submit Button -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" id="btnSimpanKhusus" 
                class="px-5 py-2 text-xs font-bold text-white rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer hover:opacity-95 active:scale-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Lembur Khusus</span>
        </button>
    </div>
</form>

<script>
    (function() {
        // Initialize Select2 with AJAX in V2 modal
        $(".select2-khusus-v2").select2({
            dropdownParent: $("#modalV2Lembur"),
            placeholder: 'Cari Nama / NIK Karyawan...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ route('karyawan.getkaryawan') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        nama_karyawan: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.nik,
                                text: item.nik + ' - ' + item.nama_karyawan + (item.nama_jabatan ? ' (' + item.nama_jabatan + ')' : '')
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        // Initialize MaskMoney
        if ($.isFunction($.fn.maskMoney)) {
            $('.money').maskMoney({
                thousands: '.',
                decimal: ',',
                precision: 0,
                allowZero: true,
                suffix: ''
            });
        }

        const form = document.getElementById('formLemburKhusus');
        const nikSelect = document.getElementById('nik_khusus');
        const upahInput = document.getElementById('upah_perjam_khusus');

        $(form).on('submit', function(e) {
            const nik = $(nikSelect).val();
            const upah = upahInput.value.trim();
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $(upahInput).removeClass('border-rose-500');

            if (!nik) {
                $('#err_nik_khusus').text('Silakan pilih karyawan terlebih dahulu').removeClass('hidden');
                hasError = true;
            }

            if (!upah) {
                $('#err_upah_khusus').text('Tarif Upah per jam wajib diisi').removeClass('hidden');
                $(upahInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSimpanKhusus');
            $btn.prop('disabled', true).html('<i class="ti ti-loader animate-spin text-sm"></i><span>Menyimpan...</span>');
        });
    })();
</script>
