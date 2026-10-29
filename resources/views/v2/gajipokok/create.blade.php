@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('gajipokok.store') }}" method="POST" id="formGajiPokokCreateV2" class="space-y-4" novalidate>
    @csrf

    <!-- Info Banner -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-cash"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Pendaftaran Gaji Pokok Karyawan</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Tentukan karyawan, nominal besaran upah pokok, skema pembayaran (bulanan / harian), dan tanggal mulai berlakunya gaji.
            </p>
        </div>
    </div>

    <!-- 1. Pilih Karyawan -->
    <div class="space-y-1.5">
        <label for="create_modal_nik" class="block text-xs font-bold text-slate-700">
            Pilih Karyawan <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <select name="nik" id="create_modal_nik" class="w-full px-3 py-2.5 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer select2GajiModal" required>
                <option value="">-- Pilih Karyawan --</option>
                @foreach ($karyawan as $d)
                    <option value="{{ $d->nik }}">
                        {{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_create_modal_nik"></div>
    </div>

    <!-- 2. Nominal Gaji Pokok -->
    <div class="space-y-1.5">
        <label for="create_modal_jumlah" class="block text-xs font-bold text-slate-700">
            Besaran Gaji Pokok (Rp) <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs pointer-events-none z-10">
                Rp
            </span>
            <input type="text" 
                   id="create_modal_jumlah" 
                   name="jumlah" 
                   placeholder="Contoh: 5.000.000"
                   autocomplete="off"
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition modal-money-input text-right" 
                   required>
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_create_modal_jumlah"></div>
    </div>

    <!-- 3. Grid: Jenis Upah & Tanggal Berlaku -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Jenis Upah -->
        <div class="space-y-1.5">
            <label for="create_modal_jenis_upah" class="block text-xs font-bold text-slate-700">
                Jenis Upah <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="jenis_upah" id="create_modal_jenis_upah" 
                        class="w-full h-10 px-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer" 
                        required>
                    <option value="Bulanan">Bulanan</option>
                    <option value="Harian">Harian</option>
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_create_modal_jenis_upah"></div>
        </div>

        <!-- Tanggal Berlaku -->
        <div class="space-y-1.5">
            <label for="create_modal_tanggal_berlaku" class="block text-xs font-bold text-slate-700">
                Tanggal Berlaku <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar text-base"></i>
                </span>
                <input type="text" 
                       id="create_modal_tanggal_berlaku" 
                       name="tanggal_berlaku" 
                       value="{{ date('Y-m-d') }}" 
                       placeholder="Pilih Tanggal Berlaku"
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer modal-flatpickr-date" 
                       required>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_create_modal_tanggal_berlaku"></div>
        </div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                onclick="$('#modalGajiPokok').addClass('hidden'); $('body').removeClass('overflow-hidden');">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitGajiPokokCreateV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Gaji Pokok</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formGajiPokokCreateV2');
        const nikSelect = document.getElementById('create_modal_nik');
        const jumlahInput = document.getElementById('create_modal_jumlah');
        const jenisUpahSelect = document.getElementById('create_modal_jenis_upah');
        const tanggalInput = document.getElementById('create_modal_tanggal_berlaku');

        // Form Validation & Submit Handler
        $(form).on('submit', function(e) {
            const nik = $(nikSelect).val();
            const jumlah = $(jumlahInput).val();
            const jenis_upah = $(jenisUpahSelect).val();
            const tanggal_berlaku = $(tanggalInput).val();
            let hasError = false;

            // Reset error feedback states
            $('.error-feedback').addClass('hidden').text('');
            $(nikSelect).removeClass('border-rose-500');
            $(jumlahInput).removeClass('border-rose-500');
            $(jenisUpahSelect).removeClass('border-rose-500');
            $(tanggalInput).removeClass('border-rose-500');

            if (!nik) {
                $('#err_create_modal_nik').text('Karyawan wajib dipilih').removeClass('hidden');
                hasError = true;
            }

            if (!jumlah || jumlah.trim() === '' || jumlah === '0') {
                $('#err_create_modal_jumlah').text('Gaji pokok wajib diisi dan minimal 1').removeClass('hidden');
                $(jumlahInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!jenis_upah) {
                $('#err_create_modal_jenis_upah').text('Jenis upah wajib dipilih').removeClass('hidden');
                $(jenisUpahSelect).addClass('border-rose-500');
                hasError = true;
            }

            if (!tanggal_berlaku) {
                $('#err_create_modal_tanggal_berlaku').text('Tanggal berlaku wajib diisi').removeClass('hidden');
                $(tanggalInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            // Button spinner loading
            const $btn = $('#btnSubmitGajiPokokCreateV2');
            $btn.prop('disabled', true).html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Menyimpan...</span>
            `);
        });
    })();
</script>
