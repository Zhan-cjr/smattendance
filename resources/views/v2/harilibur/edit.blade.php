@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('harilibur.update', ['kode_libur' => Crypt::encrypt($harilibur->kode_libur)]) }}" method="POST" id="formHariLiburUpdateV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Banner Info -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-edit"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Edit Hari Libur ({{ $harilibur->kode_libur }})</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Perbarui tanggal kalender, penempatan cabang, atau keterangan hari libur.
            </p>
        </div>
    </div>

    <!-- Tanggal Libur -->
    <div class="space-y-1.5">
        <label for="tanggal_libur_edit" class="block text-xs font-bold text-slate-700">
            Tanggal Libur <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                <i class="ti ti-calendar text-base"></i>
            </span>
            <input type="text" 
                   name="tanggal" 
                   id="tanggal_libur_edit" 
                   value="{{ $harilibur->tanggal }}"
                   placeholder="Pilih Tanggal Libur..." 
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition flatpickr-date-modal" 
                   required>
        </div>
        <p class="text-[10.5px] text-slate-400">Pilih tanggal kalender hari libur</p>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_tanggal_edit"></div>
    </div>

    <!-- Cabang -->
    @if ($user->hasRole(['super admin', 'admin pusat', 'gm administrasi']) || !$cabang->isEmpty())
        <div class="space-y-1.5">
            <label for="kode_cabang_libur_edit" class="block text-xs font-bold text-slate-700">
                Pilih Cabang / Unit Kerja <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="kode_cabang" id="kode_cabang_libur_edit" 
                        class="w-full h-10 px-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition select2-cabang-v2" required>
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $c)
                        <option value="{{ $c->kode_cabang }}" {{ $harilibur->kode_cabang == $c->kode_cabang ? 'selected' : '' }}>
                            {{ textUpperCase($c->nama_cabang) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <p class="text-[10.5px] text-slate-400">Pilih cabang yang menerapkan hari libur ini</p>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_cabang_edit"></div>
        </div>
    @endif

    <!-- Keterangan -->
    <div class="space-y-1.5">
        <label for="keterangan_libur_edit" class="block text-xs font-bold text-slate-700">
            Keterangan Hari Libur <span class="text-rose-500">*</span>
        </label>
        <textarea name="keterangan" id="keterangan_libur_edit" rows="2" 
                  class="w-full px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                  placeholder="Contoh: Libur Nasional Tahun Baru / Hari Raya Idul Fitri" required>{{ $harilibur->keterangan }}</textarea>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_keterangan_edit"></div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" 
                id="btnUpdateHariLibur" 
                class="px-5 py-2 text-xs font-bold text-white rounded-xl shadow-xs transition inline-flex items-center gap-2 cursor-pointer hover:opacity-95 active:scale-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Perbarui Hari Libur</span>
        </button>
    </div>
</form>

<script>
    (function() {
        // Flatpickr in modal
        $(".flatpickr-date-modal").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // Select2 inside modal
        if ($.isFunction($.fn.select2)) {
            $(".select2-cabang-v2").select2({
                dropdownParent: $("#modalV2HariLibur"),
                width: '100%',
                placeholder: 'Pilih Cabang'
            });
        }

        const form = document.getElementById('formHariLiburUpdateV2');
        const tanggalInput = document.getElementById('tanggal_libur_edit');
        const cabangSelect = document.getElementById('kode_cabang_libur_edit');
        const keteranganInput = document.getElementById('keterangan_libur_edit');

        $(form).on('submit', function(e) {
            const tanggal = tanggalInput.value.trim();
            const cabang = cabangSelect ? $(cabangSelect).val() : 'SKIP';
            const keterangan = keteranganInput.value.trim();
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $(tanggalInput).removeClass('border-rose-500');
            if (cabangSelect) $(cabangSelect).removeClass('border-rose-500');
            $(keteranganInput).removeClass('border-rose-500');

            if (!tanggal) {
                $('#err_tanggal_edit').text('Tanggal Libur wajib diisi').removeClass('hidden');
                $(tanggalInput).addClass('border-rose-500');
                hasError = true;
            }

            if (cabangSelect && !cabang) {
                $('#err_kode_cabang_edit').text('Cabang wajib dipilih').removeClass('hidden');
                hasError = true;
            }

            if (!keterangan) {
                $('#err_keterangan_edit').text('Keterangan Hari Libur wajib diisi').removeClass('hidden');
                $(keteranganInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            $('#btnUpdateHariLibur').prop('disabled', true).html(`
                <i class="ti ti-loader animate-spin text-sm"></i>
                <span>Memperbarui...</span>
            `);
        });
    })();
</script>
