<form action="{{ route('ajuanjadwal.store') }}" method="POST" id="formAjuanJadwalCreateV2" class="space-y-4" novalidate>
    @csrf

    <div class="space-y-3.5">
        <!-- Pilih Karyawan (Jika Admin) -->
        @if(isset($karyawan) && count($karyawan) > 0)
        <div class="space-y-1.5">
            <label for="nik_ajuan_jadwal" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="nik" id="nik_ajuan_jadwal" class="w-full select2-v2" style="width: 100%;">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawan as $d)
                        <option value="{{ $d->nik }}">{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nik_ajuan"></div>
        </div>
        @endif

        <!-- Tanggal Perubahan Jadwal -->
        <div class="space-y-1.5">
            <label for="tanggal_ajuan_jadwal" class="block text-xs font-bold text-slate-700">
                Tanggal Perubahan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar text-base"></i>
                </span>
                <input type="text" 
                       id="tanggal_ajuan_jadwal" 
                       name="tanggal" 
                       placeholder="Pilih tanggal perubahan jadwal" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_tanggal_ajuan"></div>
        </div>

        <!-- Shift / Jam Kerja Tujuan -->
        <div class="space-y-1.5">
            <label for="kode_jam_kerja_tujuan" class="block text-xs font-bold text-slate-700">
                Shift / Jam Kerja Tujuan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="kode_jam_kerja_tujuan" id="kode_jam_kerja_tujuan" class="w-full select2-v2" style="width: 100%;">
                    <option value="">-- Pilih Shift / Jam Kerja --</option>
                    @foreach ($jamkerja as $d)
                        <option value="{{ $d->kode_jam_kerja }}">
                            {{ $d->nama_jam_kerja }} ({{ $d->jam_masuk }} - {{ $d->jam_pulang }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_kerja_tujuan"></div>
        </div>

        <!-- Keterangan / Alasan Perubahan -->
        <div class="space-y-1.5">
            <label for="keterangan_ajuan_jadwal" class="block text-xs font-bold text-slate-700">
                Keterangan / Alasan Perubahan <span class="text-rose-500">*</span>
            </label>
            <textarea name="keterangan" 
                      id="keterangan_ajuan_jadwal" 
                      rows="3" 
                      placeholder="Jelaskan alasan atau urgensi permohonan perubahan shift jadwal..."
                      class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"></textarea>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_keterangan_ajuan"></div>
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
                id="btnSubmitAjuanJadwalV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-send text-sm"></i>
            <span>Simpan Ajuan Jadwal</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formAjuanJadwalCreateV2');

        // Init Flatpickr
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#tanggal_ajuan_jadwal", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y"
            });
        }

        // Init Select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#nik_ajuan_jadwal').select2({
                theme: 'v2',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
                dropdownParent: $('#modalDialogV2')
            });

            $('#kode_jam_kerja_tujuan').select2({
                theme: 'v2',
                placeholder: '-- Pilih Shift / Jam Kerja --',
                allowClear: true,
                dropdownParent: $('#modalDialogV2')
            });
        }

        // ── Reset all error states ──────────────────────────────────────────────
        function resetErrors() {
            $('.error-feedback').addClass('hidden').text('');
            $('#nik_ajuan_jadwal, #kode_jam_kerja_tujuan').next('.select2-container').find('.select2-selection').removeClass('border-rose-500');
            $(form).find('input:not([disabled]), select, textarea, input.input').removeClass('border-rose-500');
        }

        function showFieldError(inputSelector, feedbackId, message) {
            const $input = $(inputSelector);
            $('#' + feedbackId).text(message).removeClass('hidden');
            $input.addClass('border-rose-500');
            $input.siblings('input.input').addClass('border-rose-500');
            if ($input.hasClass('select2-hidden-accessible')) {
                $input.next('.select2-container').find('.select2-selection').addClass('border-rose-500');
            }
        }

        // ── Submit validation ───────────────────────────────────────────────────
        $(form).on('submit', function(e) {
            resetErrors();
            let hasError = false;

            const nik        = $('#nik_ajuan_jadwal').length ? $('#nik_ajuan_jadwal').val() : 'exists';
            const tanggal    = $('#tanggal_ajuan_jadwal').val();
            const jamKerja   = $('#kode_jam_kerja_tujuan').val();
            const keterangan = $('#keterangan_ajuan_jadwal').val().trim();

            if ($('#nik_ajuan_jadwal').length && !$('#nik_ajuan_jadwal').val()) {
                showFieldError('#nik_ajuan_jadwal', 'err_nik_jadwal', 'Silakan pilih karyawan terlebih dahulu');
                hasError = true;
            }
            if (!tanggal) {
                showFieldError('#tanggal_ajuan_jadwal', 'err_tanggal_jadwal', 'Tanggal perubahan jadwal wajib diisi');
                hasError = true;
            }
            if (!jamKerja) {
                showFieldError('#kode_jam_kerja_tujuan', 'err_jam_kerja', 'Shift / Jam kerja tujuan wajib dipilih');
                hasError = true;
            }
            if (!keterangan) {
                showFieldError('#keterangan_ajuan_jadwal', 'err_keterangan_jadwal', 'Keterangan atau alasan perubahan wajib diisi');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitAjuanJadwalV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
