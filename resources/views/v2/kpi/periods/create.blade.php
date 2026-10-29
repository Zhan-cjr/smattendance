<form action="{{ route('kpi.periods.store') }}" id="formCreateKpiPeriodV2" method="POST" class="space-y-4">
    @csrf

    @php
        $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
        $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    @endphp

    <!-- Nama Periode -->
    <div class="space-y-1.5">
        <label for="nama_periode" class="block text-xs font-bold text-slate-700">
            Nama Periode <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                <i class="ti ti-file-text text-base"></i>
            </span>
            <input type="text" name="nama_periode" id="nama_periode" 
                   placeholder="Contoh: Periode Semester 1 2026 / Januari 2026"
                   autocomplete="off"
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nama_periode"></div>
    </div>

    <!-- Rentang Tanggal: Mulai & Selesai -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Tanggal Mulai -->
        <div class="space-y-1.5">
            <label for="start_date" class="block text-xs font-bold text-slate-700">
                Tanggal Mulai <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-calendar text-base"></i>
                </span>
                <input type="text" name="start_date" id="start_date" 
                       value="{{ date('Y-m-01') }}"
                       placeholder="YYYY-MM-DD"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition flatpickr-kpi cursor-pointer">
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_start_date"></div>
        </div>

        <!-- Tanggal Selesai -->
        <div class="space-y-1.5">
            <label for="end_date" class="block text-xs font-bold text-slate-700">
                Tanggal Selesai <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-calendar-event text-base"></i>
                </span>
                <input type="text" name="end_date" id="end_date" 
                       value="{{ date('Y-m-t') }}"
                       placeholder="YYYY-MM-DD"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition flatpickr-kpi cursor-pointer">
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_end_date"></div>
        </div>
    </div>

    <!-- Switch Status Aktif -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
        <div>
            <span class="block text-xs font-bold text-slate-800">Status Periode</span>
            <span class="block text-[11px] text-slate-500">Jadikan sebagai periode penilaian aktif utama</span>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
        </label>
    </div>

    <!-- Tombol Aksi Modal -->
    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
        <button type="button" class="btnCloseModalKpiPeriod px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit" id="btnSubmitKpiPeriod" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-xs transition active:scale-95 cursor-pointer flex items-center gap-1.5"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Periode</span>
        </button>
    </div>
</form>

<script>
    $(function() {
        if (typeof flatpickr !== 'undefined') {
            $('.flatpickr-kpi').flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });
        }

        $('#formCreateKpiPeriodV2').submit(function(e) {
            let nama = $('#nama_periode').val().trim();
            let start = $('#start_date').val();
            let end = $('#end_date').val();
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $('#nama_periode, #start_date, #end_date').removeClass('border-rose-500');

            if (!nama) {
                $('#err_nama_periode').text('Nama periode penilaian wajib diisi.').removeClass('hidden');
                $('#nama_periode').addClass('border-rose-500');
                hasError = true;
            }

            if (!start) {
                $('#err_start_date').text('Tanggal mulai wajib diisi.').removeClass('hidden');
                $('#start_date').addClass('border-rose-500');
                hasError = true;
            }

            if (!end) {
                $('#err_end_date').text('Tanggal selesai wajib diisi.').removeClass('hidden');
                $('#end_date').addClass('border-rose-500');
                hasError = true;
            }

            if (!hasError && start > end) {
                $('#err_end_date').text('Tanggal selesai tidak boleh lebih awal dari tanggal mulai.').removeClass('hidden');
                $('#end_date').addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            $('#btnSubmitKpiPeriod').html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>').prop('disabled', true);
            return true;
        });
    });
</script>
