<form action="{{ route('koreksi.store') }}" method="POST" id="formKoreksiCreateV2" class="space-y-4" novalidate>
    @csrf

    <div class="space-y-3.5">
        <!-- Kode Koreksi (Auto) -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
                Kode Koreksi Presensi
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-barcode text-base"></i>
                </span>
                <input type="text" 
                       value="Otomatis Digenerate Sistem (KP...)" 
                       disabled 
                       class="w-full h-10 pl-10 pr-3.5 text-xs font-mono font-medium text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
            </div>
        </div>

        <!-- Pilih Karyawan (Jika Admin) -->
        @if(isset($karyawan) && count($karyawan) > 0)
        <div class="space-y-1.5">
            <label for="nik_koreksi_create" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="nik" id="nik_koreksi_create" class="w-full select2-v2" style="width: 100%;">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawan as $d)
                        <option value="{{ $d->nik }}">{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nik_koreksi"></div>
        </div>
        @endif

        <!-- Tanggal Absen -->
        <div class="space-y-1.5">
            <label for="tanggal_koreksi_create" class="block text-xs font-bold text-slate-700">
                Tanggal Absen <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar text-base"></i>
                </span>
                <input type="text" 
                       id="tanggal_koreksi_create" 
                       name="tanggal" 
                       placeholder="Pilih tanggal absen" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_tanggal_koreksi"></div>
        </div>

        <!-- Pilih Jadwal / Jam Kerja -->
        <div class="space-y-1.5">
            <label for="kode_jam_kerja_koreksi" class="block text-xs font-bold text-slate-700">
                Jadwal / Jam Kerja <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="kode_jam_kerja" id="kode_jam_kerja_koreksi" class="w-full select2-v2" style="width: 100%;">
                    <option value="">-- Pilih Jadwal / Jam Kerja --</option>
                    @foreach ($jamkerja as $d)
                        <option value="{{ $d->kode_jam_kerja }}">
                            {{ $d->kode_jam_kerja }} - {{ $d->nama_jam_kerja }} ({{ $d->jam_masuk }} - {{ $d->jam_pulang }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_kerja_koreksi"></div>
        </div>

        <!-- Jam Masuk & Jam Pulang -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Jam Masuk -->
            <div class="space-y-1.5">
                <label for="jam_in_koreksi" class="block text-xs font-bold text-slate-700">
                    Koreksi Jam Masuk
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-login text-base text-emerald-600"></i>
                    </span>
                    <input type="time" 
                           id="jam_in_koreksi" 
                           name="jam_in" 
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
            </div>

            <!-- Jam Pulang -->
            <div class="space-y-1.5">
                <label for="jam_out_koreksi" class="block text-xs font-bold text-slate-700">
                    Koreksi Jam Pulang
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-logout text-base text-rose-600"></i>
                    </span>
                    <input type="time" 
                           id="jam_out_koreksi" 
                           name="jam_out" 
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
            </div>
        </div>
        <p class="text-[11px] text-slate-400 italic">
            * Isi minimal salah satu: Jam Masuk atau Jam Pulang.
        </p>

        <!-- Alasan Koreksi -->
        <div class="space-y-1.5">
            <label for="keterangan_koreksi_create" class="block text-xs font-bold text-slate-700">
                Alasan Koreksi Presensi <span class="text-rose-500">*</span>
            </label>
            <textarea name="keterangan" 
                      id="keterangan_koreksi_create" 
                      rows="3" 
                      placeholder="Jelaskan alasan koreksi (contoh: Lupa absen saat masuk karena tugas darurat)..."
                      class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"></textarea>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_keterangan_koreksi"></div>
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
                id="btnSubmitKoreksiCreateV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-send text-sm"></i>
            <span>Simpan Pengajuan Koreksi</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formKoreksiCreateV2');

        // Init Flatpickr
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#tanggal_koreksi_create", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y"
            });
        }

        // Init Select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#nik_koreksi_create').select2({
                theme: 'v2',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
                dropdownParent: $('#modalDialogV2')
            });

            $('#kode_jam_kerja_koreksi').select2({
                theme: 'v2',
                placeholder: '-- Pilih Jadwal / Jam Kerja --',
                allowClear: true,
                dropdownParent: $('#modalDialogV2')
            });
        }

        // ── Reset all error states ──────────────────────────────────────────────
        function resetErrors() {
            $('.error-feedback').addClass('hidden').text('');
            $('#nik_koreksi_create, #kode_jam_kerja_koreksi').next('.select2-container').find('.select2-selection').removeClass('border-rose-500');
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

            const nik        = $('#nik_koreksi_create').length ? $('#nik_koreksi_create').val() : 'exists';
            const tanggal    = $('#tanggal_koreksi_create').val();
            const jamKerja   = $('#kode_jam_kerja_koreksi').val();
            const jamIn      = $('#jam_in_koreksi').val();
            const jamOut     = $('#jam_out_koreksi').val();
            const keterangan = $('#keterangan_koreksi_create').val().trim();

            if ($('#nik_koreksi_create').length && !$('#nik_koreksi_create').val()) {
                showFieldError('#nik_koreksi_create', 'err_nik_koreksi', 'Silakan pilih karyawan terlebih dahulu');
                hasError = true;
            }
            if (!tanggal) {
                showFieldError('#tanggal_koreksi_create', 'err_tanggal_koreksi', 'Tanggal absen wajib diisi');
                hasError = true;
            }
            if (!jamKerja) {
                showFieldError('#kode_jam_kerja_koreksi', 'err_jam_kerja_koreksi', 'Jadwal / Jam kerja wajib dipilih');
                hasError = true;
            }
            if (!jamIn && !jamOut) {
                showFieldError('#jam_in_koreksi', 'err_jam_koreksi', 'Jam Masuk atau Jam Pulang wajib diisi minimal salah satu');
                hasError = true;
            }
            if (!keterangan) {
                showFieldError('#keterangan_koreksi_create', 'err_keterangan_koreksi', 'Alasan koreksi presensi wajib diisi');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitKoreksiCreateV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
