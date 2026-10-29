@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('lembur.store') }}" method="POST" id="formCreateLemburV2" class="space-y-4" novalidate>
    @csrf

    <!-- Info Banner -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-info-circle"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Ketentuan Pengajuan Lembur</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Pilih karyawan dan tentukan rentang waktu lembur (mulai hingga selesai). Durasi jam akan otomatis dihitung.
            </p>
        </div>
    </div>

    <!-- Pilih Karyawan -->
    <div class="space-y-1.5 field-group">
        <label for="nik_lembur" class="block text-xs font-bold text-slate-700">
            Pilih Karyawan <span class="text-rose-500 font-bold">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                <i class="ti ti-user text-base"></i>
            </span>
            <select name="nik" id="nik_lembur" class="w-full h-10 pl-10 pr-4 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer select2Nik">
                <option value="">-- Pilih Karyawan --</option>
                @foreach ($karyawan as $d)
                    <option value="{{ $d->nik }}">{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Waktu Mulai & Selesai Lembur -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Waktu Mulai -->
        <div class="space-y-1.5 field-group">
            <label for="dari_lembur" class="block text-xs font-bold text-slate-700">
                Waktu Mulai Lembur <span class="text-rose-500 font-bold">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar-time text-base"></i>
                </span>
                <input type="text" 
                       id="dari_lembur" 
                       name="dari" 
                       placeholder="YYYY-MM-DD HH:mm"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer flatpickr-datetime">
            </div>
        </div>

        <!-- Waktu Selesai -->
        <div class="space-y-1.5 field-group">
            <label for="sampai_lembur" class="block text-xs font-bold text-slate-700">
                Waktu Selesai Lembur <span class="text-rose-500 font-bold">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar-time text-base"></i>
                </span>
                <input type="text" 
                       id="sampai_lembur" 
                       name="sampai" 
                       placeholder="YYYY-MM-DD HH:mm"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer flatpickr-datetime">
            </div>
        </div>
    </div>

    <!-- Estimasi Durasi Lembur (Read Only) -->
    <div class="space-y-1.5">
        <label for="jml_jam_lembur" class="block text-xs font-bold text-slate-700">
            Estimasi Durasi Jam Lembur
        </label>
        <div class="relative flex items-center">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                <i class="ti ti-clock text-base"></i>
            </span>
            <input type="text" 
                   id="jml_jam_lembur" 
                   name="jml_jam" 
                   value="0" 
                   readonly 
                   class="w-full h-10 pl-10 pr-14 text-xs sm:text-sm font-bold text-slate-700 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
            <span class="absolute right-3.5 text-xs font-bold text-slate-400 pointer-events-none">
                Jam
            </span>
        </div>
    </div>

    <!-- Keterangan / Pekerjaan -->
    <div class="space-y-1.5 field-group">
        <label for="keterangan_lembur" class="block text-xs font-bold text-slate-700">
            Uraian Tugas / Keterangan Lembur <span class="text-rose-500 font-bold">*</span>
        </label>
        <textarea id="keterangan_lembur" 
                  name="keterangan" 
                  rows="3" 
                  placeholder="Jelaskan uraian pekerjaan atau proyek yang dikerjakan saat lembur..." 
                  class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"></textarea>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitLemburV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-send text-sm"></i>
            <span>Kirim Pengajuan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = $('#formCreateLemburV2');

        // Flatpickr DateTime
        if (typeof flatpickr !== 'undefined') {
            const elDari = document.getElementById('dari_lembur');
            const elSampai = document.getElementById('sampai_lembur');
            
            if (elDari) {
                flatpickr(elDari, {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    minuteIncrement: 1,
                    allowInput: true,
                    onChange: function() {
                        hitungDurasi();
                        validateField();
                    }
                });
            }

            if (elSampai) {
                flatpickr(elSampai, {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    minuteIncrement: 1,
                    allowInput: true,
                    onChange: function() {
                        hitungDurasi();
                        validateField();
                    }
                });
            }
        }

        // Select2 Karyawan
        if (typeof $.fn.select2 !== 'undefined') {
            form.find('.select2Nik').select2({
                placeholder: '-- Pilih Karyawan --',
                dropdownParent: $('#modalLembur')
            }).on('change', function() {
                validateField();
            });
        }

        function hitungDurasi() {
            const dariVal = form.find('#dari_lembur').val();
            const sampaiVal = form.find('#sampai_lembur').val();

            if (dariVal && sampaiVal) {
                const start = new Date(dariVal);
                const end = new Date(sampaiVal);
                const diffMs = end - start;

                if (diffMs > 0) {
                    const hours = diffMs / (1000 * 3600);
                    form.find('#jml_jam_lembur').val(hours.toFixed(2));
                } else {
                    form.find('#jml_jam_lembur').val('0');
                }
            } else {
                form.find('#jml_jam_lembur').val('0');
            }
        }

        function showError($el, message) {
            const $container = $el.closest('.field-group, .space-y-1\\.5');
            $el.css({
                'border-color': '#ef4444',
                'background-color': '#fff5f5',
                'box-shadow': '0 0 0 2px rgba(239, 68, 68, 0.2)'
            });
            $el.closest('.relative').find('i').css('color', '#ef4444');
            $container.find('.error-msg').remove();
            $container.append(`
                <p class="error-msg" style="font-size: 11px; font-weight: 600; color: #ef4444; margin: 4px 0 0 0; display: flex; align-items: center; gap: 4px;">
                    <i class="ti ti-alert-circle" style="font-size: 13px;"></i>
                    <span>${message}</span>
                </p>
            `);
        }

        function clearError($el) {
            const $container = $el.closest('.field-group, .space-y-1\\.5');
            $el.css({
                'border-color': '#cbd5e1',
                'background-color': '#ffffff',
                'box-shadow': 'none'
            });
            $el.closest('.relative').find('i').css('color', '#94a3b8');
            $container.find('.error-msg').remove();
        }

        function validateField() {
            const nik = form.find('#nik_lembur').val();
            const dari = form.find('#dari_lembur').val();
            const sampai = form.find('#sampai_lembur').val();
            const ket = form.find('#keterangan_lembur').val().trim();

            if (nik) clearError(form.find('#nik_lembur'));
            if (dari) clearError(form.find('#dari_lembur'));
            if (sampai) clearError(form.find('#sampai_lembur'));
            if (ket) clearError(form.find('#keterangan_lembur'));
        }

        form.on('input change blur', 'input, textarea, select', function() {
            hitungDurasi();
            validateField();
        });

        form.on('submit', function(e) {
            const nik = form.find('#nik_lembur').val();
            const dari = form.find('#dari_lembur').val();
            const sampai = form.find('#sampai_lembur').val();
            const keterangan = form.find('#keterangan_lembur').val().trim();
            let hasError = false;
            let firstInvalid = null;

            if (!nik) {
                showError(form.find('#nik_lembur'), 'Karyawan wajib dipilih');
                hasError = true;
                if (!firstInvalid) firstInvalid = form.find('#nik_lembur');
            }

            if (!dari) {
                showError(form.find('#dari_lembur'), 'Waktu mulai lembur wajib diisi');
                hasError = true;
                if (!firstInvalid) firstInvalid = form.find('#dari_lembur');
            }

            if (!sampai) {
                showError(form.find('#sampai_lembur'), 'Waktu selesai lembur wajib diisi');
                hasError = true;
                if (!firstInvalid) firstInvalid = form.find('#sampai_lembur');
            } else if (dari && sampai && sampai <= dari) {
                showError(form.find('#sampai_lembur'), 'Waktu selesai harus lebih besar dari waktu mulai');
                hasError = true;
                if (!firstInvalid) firstInvalid = form.find('#sampai_lembur');
            }

            if (!keterangan) {
                showError(form.find('#keterangan_lembur'), 'Uraian keterangan lembur wajib diisi');
                hasError = true;
                if (!firstInvalid) firstInvalid = form.find('#keterangan_lembur');
            }

            if (hasError) {
                e.preventDefault();
                e.stopPropagation();

                if (firstInvalid) firstInvalid.focus();

                Swal.fire({
                    icon: 'warning',
                    title: 'Formulir Belum Lengkap',
                    text: 'Silakan periksa kembali field yang bertanda merah.',
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
                return false;
            }

            const $btn = form.find('#btnSubmitLemburV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Mengirim...</span>');
        });
    })();
</script>
