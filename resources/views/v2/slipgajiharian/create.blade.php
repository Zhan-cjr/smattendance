@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('slipgajiharian.store') }}" method="POST" id="formSlipGajiHarianCreateV2" class="space-y-4" novalidate>
    @csrf

    <!-- Info Banner -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-calendar-plus"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Penerbitan Slip Gaji Harian (Upah Lepas)</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Tentukan rentang tanggal kerja, tanggal slip dan pilih daftar karyawan upah harian penerima slip.
            </p>
        </div>
    </div>

    <!-- Tanggal Slip -->
    <div class="space-y-1.5">
        <label for="create_harian_tanggal_slip" class="block text-xs font-bold text-slate-700">
            Tanggal Slip Gaji <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                <i class="ti ti-calendar text-base"></i>
            </span>
            <input type="text" 
                   id="create_harian_tanggal_slip" 
                   name="tanggal_slip" 
                   value="{{ date('Y-m-d') }}" 
                   placeholder="Pilih Tanggal Slip" 
                   autocomplete="off"
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer modal-flatpickr-date" 
                   required>
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_create_harian_tanggal_slip"></div>
    </div>

    <!-- Rentang Periode Dari - Sampai -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <div class="space-y-1.5">
            <label for="create_harian_dari" class="block text-xs font-bold text-slate-700">
                Dari Tanggal <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar-event text-base"></i>
                </span>
                <input type="text" 
                       id="create_harian_dari" 
                       name="dari" 
                       placeholder="Dari Tanggal" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer modal-flatpickr-date" 
                       required>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_create_harian_dari"></div>
        </div>

        <div class="space-y-1.5">
            <label for="create_harian_sampai" class="block text-xs font-bold text-slate-700">
                Sampai Tanggal <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar-event text-base"></i>
                </span>
                <input type="text" 
                       id="create_harian_sampai" 
                       name="sampai" 
                       placeholder="Sampai Tanggal" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer modal-flatpickr-date" 
                       required>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_create_harian_sampai"></div>
        </div>
    </div>

    <!-- Pilih Karyawan Harian -->
    <div class="space-y-1.5">
        <div class="flex items-center justify-between">
            <label for="create_harian_nik" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan Harian Lepas <span class="text-rose-500">*</span>
            </label>
            <button type="button" id="btnSelectAllKaryawanHarian" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 transition cursor-pointer">
                Pilih Semua
            </button>
        </div>
        <select name="nik[]" id="create_harian_nik" class="w-full text-xs" multiple="multiple" data-placeholder="Pilih Karyawan Harian..." style="width: 100%;">
            <!-- Loaded via AJAX -->
        </select>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_create_harian_nik"></div>
    </div>

    <!-- Status Penerbitan -->
    <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700">
            Status Penerbitan <span class="text-rose-500">*</span>
        </label>
        <div class="grid grid-cols-2 gap-3">
            <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50 cursor-pointer transition">
                <input type="radio" name="status" value="1" checked class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                <div>
                    <span class="block text-xs font-bold text-emerald-900">Publish (Live)</span>
                    <span class="block text-[10.5px] text-emerald-700">Dapat dilihat karyawan</span>
                </div>
            </label>
            <label class="relative flex items-center gap-2.5 p-3 rounded-xl border border-amber-200 bg-amber-50/50 hover:bg-amber-50 cursor-pointer transition">
                <input type="radio" name="status" value="0" class="w-4 h-4 text-amber-600 focus:ring-amber-500 border-slate-300">
                <div>
                    <span class="block text-xs font-bold text-amber-900">Pending (Draft)</span>
                    <span class="block text-[10.5px] text-amber-700">Draft internal admin</span>
                </div>
            </label>
        </div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="btnCloseModalHarian px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitSlipGajiHarianCreateV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-send text-sm"></i>
            <span>Terbitkan Slip Harian</span>
        </button>
    </div>
</form>

<script>
    (function() {
        $(".modal-flatpickr-date").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: true
        });

        $("#create_harian_nik").select2({
            placeholder: "Pilih Karyawan Harian...",
            allowClear: true,
            dropdownParent: $('#modalHarian')
        });

        function loadKaryawanHarian() {
            var dari = $("#create_harian_dari").val();
            $.ajax({
                type: "GET",
                url: "/karyawan/getkaryawan",
                data: {
                    jenis_upah: 'Harian',
                    tanggal: dari || null
                },
                cache: false,
                success: function(respond) {
                    var target = $("#create_harian_nik");
                    target.empty();
                    respond.forEach(function(item) {
                        target.append(`<option value="${item.nik}">${item.nik} - ${item.nama_karyawan}</option>`);
                    });
                    target.trigger('change');
                }
            });
        }

        $("#create_harian_dari").on("change", function() {
            loadKaryawanHarian();
        });

        loadKaryawanHarian();

        $('#btnSelectAllKaryawanHarian').click(function() {
            let allValues = [];
            $('#create_harian_nik option').each(function() {
                allValues.push($(this).val());
            });
            $('#create_harian_nik').val(allValues).trigger('change');
        });

        const form = document.getElementById('formSlipGajiHarianCreateV2');
        $(form).on('submit', function(e) {
            let tglSlip = $('#create_harian_tanggal_slip').val();
            let dari = $('#create_harian_dari').val();
            let sampai = $('#create_harian_sampai').val();
            let nik = $('#create_harian_nik').val();
            let hasError = false;

            $('.error-feedback').addClass('hidden').text('');
            $('input, select').removeClass('border-rose-500');

            if (!tglSlip) {
                $('#err_create_harian_tanggal_slip').text('Tanggal slip gaji wajib diisi').removeClass('hidden');
                $('#create_harian_tanggal_slip').addClass('border-rose-500');
                hasError = true;
            }

            if (!dari) {
                $('#err_create_harian_dari').text('Tanggal awal periode wajib diisi').removeClass('hidden');
                $('#create_harian_dari').addClass('border-rose-500');
                hasError = true;
            }

            if (!sampai) {
                $('#err_create_harian_sampai').text('Tanggal akhir periode wajib diisi').removeClass('hidden');
                $('#create_harian_sampai').addClass('border-rose-500');
                hasError = true;
            }

            if (!nik || nik.length === 0) {
                $('#err_create_harian_nik').text('Pilih minimal satu karyawan harian').removeClass('hidden');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const btn = document.getElementById('btnSubmitSlipGajiHarianCreateV2');
            btn.disabled = true;
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Menyimpan...</span>
            `;
        });
    })();
</script>
