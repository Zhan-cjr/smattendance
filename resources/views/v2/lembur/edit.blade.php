@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $lama = hitungJam($lembur->lembur_mulai, $lembur->lembur_selesai);
@endphp

<form action="{{ route('lembur.update', Crypt::encrypt($lembur->id)) }}" method="POST" id="formEditLemburV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Info Banner -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-edit"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Edit Jadwal & Koreksi Realisasi Lembur</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Sesuaikan jam rencana lembur atau input manual realisasi jam lembur (IN & OUT) jika diperlukan.
            </p>
        </div>
    </div>

    <!-- Pilih Karyawan -->
    <div class="space-y-1.5 field-group">
        <label for="nik_lembur_edit" class="block text-xs font-bold text-slate-700">
            Karyawan <span class="text-rose-500 font-bold">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                <i class="ti ti-user text-base"></i>
            </span>
            <select name="nik" id="nik_lembur_edit" class="w-full h-10 pl-10 pr-4 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer select2Nik">
                <option value="">-- Pilih Karyawan --</option>
                @foreach ($karyawan as $d)
                    <option {{ $d->nik == $lembur->nik ? 'selected' : '' }} value="{{ $d->nik }}">{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Waktu Rencana Lembur (Dari & Sampai) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Waktu Mulai -->
        <div class="space-y-1.5 field-group">
            <label for="dari_lembur_edit" class="block text-xs font-bold text-slate-700">
                Waktu Mulai Lembur <span class="text-rose-500 font-bold">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar-time text-base"></i>
                </span>
                <input type="text" 
                       id="dari_lembur_edit" 
                       name="dari" 
                       value="{{ $lembur->lembur_mulai }}" 
                       placeholder="YYYY-MM-DD HH:mm"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer flatpickr-datetime">
            </div>
        </div>

        <!-- Waktu Selesai -->
        <div class="space-y-1.5 field-group">
            <label for="sampai_lembur_edit" class="block text-xs font-bold text-slate-700">
                Waktu Selesai Lembur <span class="text-rose-500 font-bold">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar-time text-base"></i>
                </span>
                <input type="text" 
                       id="sampai_lembur_edit" 
                       name="sampai" 
                       value="{{ $lembur->lembur_selesai }}" 
                       placeholder="YYYY-MM-DD HH:mm"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer flatpickr-datetime">
            </div>
        </div>
    </div>

    <!-- Estimasi Durasi Lembur -->
    <div class="space-y-1.5">
        <label for="jml_jam_lembur_edit" class="block text-xs font-bold text-slate-700">
            Estimasi Durasi Jam Lembur
        </label>
        <div class="relative flex items-center">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                <i class="ti ti-clock text-base"></i>
            </span>
            <input type="text" 
                   id="jml_jam_lembur_edit" 
                   name="jml_jam" 
                   value="{{ round($lama, 2) }}" 
                   readonly 
                   class="w-full h-10 pl-10 pr-14 text-xs sm:text-sm font-bold text-slate-700 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
            <span class="absolute right-3.5 text-xs font-bold text-slate-400 pointer-events-none">
                Jam
            </span>
        </div>
    </div>

    <!-- Keterangan / Pekerjaan -->
    <div class="space-y-1.5 field-group">
        <label for="keterangan_lembur_edit" class="block text-xs font-bold text-slate-700">
            Uraian Tugas / Keterangan Lembur <span class="text-rose-500 font-bold">*</span>
        </label>
        <textarea id="keterangan_lembur_edit" 
                  name="keterangan" 
                  rows="3" 
                  class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">{{ $lembur->keterangan }}</textarea>
    </div>

    <!-- Koreksi Realisasi Presensi Lembur (IN & OUT) -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-3">
        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800">
            <i class="ti ti-fingerprint text-emerald-600"></i>
            <span>Koreksi Realisasi Absensi Lembur (Opsional)</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-600">Lembur IN</label>
                <input type="text" 
                       name="lembur_in" 
                       value="{{ $lembur->lembur_in }}" 
                       placeholder="YYYY-MM-DD HH:mm:ss"
                       class="w-full h-9 px-3 text-xs bg-white border border-slate-300 rounded-lg flatpickr-datetime-sec outline-none transition">
            </div>
            <div class="space-y-1">
                <label class="block text-[11px] font-semibold text-slate-600">Lembur OUT</label>
                <input type="text" 
                       name="lembur_out" 
                       value="{{ $lembur->lembur_out }}" 
                       placeholder="YYYY-MM-DD HH:mm:ss"
                       class="w-full h-9 px-3 text-xs bg-white border border-slate-300 rounded-lg flatpickr-datetime-sec outline-none transition">
            </div>
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
                id="btnSubmitEditLemburV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = $('#formEditLemburV2');

        // Flatpickr DateTime
        if (typeof flatpickr !== 'undefined') {
            const elDari = document.getElementById('dari_lembur_edit');
            const elSampai = document.getElementById('sampai_lembur_edit');
            
            if (elDari) {
                flatpickr(elDari, {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    minuteIncrement: 1,
                    allowInput: true,
                    onChange: function() {
                        hitungDurasi();
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
                    }
                });
            }

            document.querySelectorAll('#formEditLemburV2 .flatpickr-datetime-sec').forEach(function(el) {
                flatpickr(el, {
                    enableTime: true,
                    enableSeconds: true,
                    dateFormat: "Y-m-d H:i:S",
                    time_24hr: true,
                    allowInput: true
                });
            });
        }

        // Select2 Karyawan
        if (typeof $.fn.select2 !== 'undefined') {
            form.find('.select2Nik').select2({
                placeholder: '-- Pilih Karyawan --',
                dropdownParent: $('#modalLembur')
            });
        }

        function hitungDurasi() {
            const dariVal = form.find('#dari_lembur_edit').val();
            const sampaiVal = form.find('#sampai_lembur_edit').val();

            if (dariVal && sampaiVal) {
                const start = new Date(dariVal);
                const end = new Date(sampaiVal);
                const diffMs = end - start;

                if (diffMs > 0) {
                    const hours = diffMs / (1000 * 3600);
                    form.find('#jml_jam_lembur_edit').val(hours.toFixed(2));
                } else {
                    form.find('#jml_jam_lembur_edit').val('0');
                }
            } else {
                form.find('#jml_jam_lembur_edit').val('0');
            }
        }

        form.on('submit', function(e) {
            const nik = form.find('#nik_lembur_edit').val();
            const dari = form.find('#dari_lembur_edit').val();
            const sampai = form.find('#sampai_lembur_edit').val();
            const keterangan = form.find('#keterangan_lembur_edit').val().trim();

            if (!nik || !dari || !sampai || !keterangan || sampai <= dari) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulir Belum Lengkap',
                    text: 'Pastikan seluruh data wajib diisi dengan benar.',
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
                return false;
            }

            const $btn = form.find('#btnSubmitEditLemburV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
