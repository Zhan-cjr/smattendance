<form action="{{ route('izincuti.update', Crypt::encrypt($izincuti->kode_izin_cuti)) }}" method="POST" id="formIzinCutiEditV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <div class="space-y-3.5">
        <!-- Kode Izin Cuti (Auto) -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
                Kode Izin Cuti
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-barcode text-base"></i>
                </span>
                <input type="text" 
                       value="{{ $izincuti->kode_izin_cuti }}" 
                       disabled 
                       class="w-full h-10 pl-10 pr-3.5 text-xs font-mono font-medium text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
            </div>
        </div>

        <!-- Pilih Karyawan -->
        <div class="space-y-1.5">
            <label for="nik_cuti_edit" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="nik" id="nik_cuti_edit" class="w-full select2-v2" style="width: 100%;">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawan as $d)
                        <option value="{{ $d->nik }}" @selected($izincuti->nik == $d->nik)>{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nik_cuti_edit"></div>
        </div>

        <!-- Pilih Jenis Cuti -->
        <div class="space-y-1.5">
            <label for="kode_cuti_edit" class="block text-xs font-bold text-slate-700">
                Jenis Cuti <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="kode_cuti" id="kode_cuti_edit" class="w-full select2-v2" style="width: 100%;">
                    <option value="">-- Pilih Jenis Cuti --</option>
                    @foreach ($jenis_cuti as $d)
                        <option value="{{ $d->kode_cuti }}" @selected($izincuti->kode_cuti == $d->kode_cuti)>{{ $d->jenis_cuti }} (Maks. {{ $d->jumlah_hari }} Hari)</option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_cuti_edit"></div>
        </div>

        <!-- Rentang Tanggal Cuti (Dari & Sampai) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Dari -->
            <div class="space-y-1.5">
                <label for="dari_cuti_edit" class="block text-xs font-bold text-slate-700">
                    Dari Tanggal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="dari_cuti_edit" 
                           name="dari" 
                           value="{{ $izincuti->dari }}"
                           placeholder="Pilih tanggal mulai" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_dari_cuti_edit"></div>
            </div>

            <!-- Sampai -->
            <div class="space-y-1.5">
                <label for="sampai_cuti_edit" class="block text-xs font-bold text-slate-700">
                    Sampai Tanggal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="sampai_cuti_edit" 
                           name="sampai" 
                           value="{{ $izincuti->sampai }}"
                           placeholder="Pilih tanggal selesai" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_sampai_cuti_edit"></div>
            </div>
        </div>

        <!-- Total Hari Kerja & Info Sisa Cuti Card -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="space-y-1.5 sm:col-span-1">
                <label class="block text-xs font-bold text-slate-700">
                    Total Durasi Kerja
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-600 pointer-events-none flex items-center">
                        <i class="ti ti-clock text-base"></i>
                    </span>
                    <input type="text" 
                           id="jml_hari_cuti_edit_display" 
                           value="Memuat..." 
                           disabled 
                           class="w-full h-10 pl-10 pr-3.5 text-xs font-bold text-emerald-700 bg-emerald-50/50 border border-emerald-200 rounded-xl cursor-not-allowed">
                </div>
            </div>

            <div class="space-y-1.5 sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700">
                    Sisa Kuota Cuti
                </label>
                <div id="sisa_cuti_info_box_edit" class="h-10 px-3.5 rounded-xl border border-slate-200 bg-slate-50 flex items-center gap-2 text-xs text-slate-600 font-medium truncate">
                    <i class="ti ti-info-circle text-slate-400 text-sm shrink-0"></i>
                    <span id="sisa_cuti_text_edit" class="truncate">Memeriksa kuota...</span>
                </div>
            </div>
        </div>

        <!-- Pelimpahan Tugas & Kepala Divisi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Pelimpahan Tugas -->
            <div class="space-y-1.5">
                <label for="pelimpahan_tugas_edit" class="block text-xs font-bold text-slate-700">
                    Pelimpahan Tugas Ke <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-user-share text-base"></i>
                    </span>
                    <input type="text" 
                           id="pelimpahan_tugas_edit" 
                           name="pelimpahan_tugas" 
                           value="{{ $izincuti->pelimpahan_tugas }}"
                           placeholder="Nama rekan kerja pengganti..." 
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_pelimpahan_edit"></div>
            </div>

            <!-- Nama Kepala Divisi -->
            <div class="space-y-1.5">
                <label for="nama_kepala_divisi_edit" class="block text-xs font-bold text-slate-700">
                    Nama Kepala Divisi / Atasan <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-user-check text-base"></i>
                    </span>
                    <input type="text" 
                           id="nama_kepala_divisi_edit" 
                           name="nama_kepala_divisi" 
                           value="{{ $izincuti->nama_kepala_divisi }}"
                           placeholder="Nama kepala divisi / manager..." 
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kepala_divisi_edit"></div>
            </div>
        </div>

        <!-- Alasan Cuti -->
        <div class="space-y-1.5">
            <label for="keterangan_cuti_edit" class="block text-xs font-bold text-slate-700">
                Alasan / Keperluan Cuti <span class="text-rose-500">*</span>
            </label>
            <textarea id="keterangan_cuti_edit" 
                      name="keterangan" 
                      rows="3" 
                      placeholder="Jelaskan alasan atau keperluan permohonan cuti secara rinci..."
                      class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">{{ $izincuti->keterangan }}</textarea>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_keterangan_cuti_edit"></div>
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
                id="btnSubmitCutiEditV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formIzinCutiEditV2');
        let currentSisaCuti = 999;
        let calculatedDays = 0;

        // Init Flatpickr
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#dari_cuti_edit", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                defaultDate: "{{ $izincuti->dari }}",
                onChange: function() {
                    checkSisaDanHari();
                }
            });

            flatpickr("#sampai_cuti_edit", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                defaultDate: "{{ $izincuti->sampai }}",
                onChange: function() {
                    checkSisaDanHari();
                }
            });
        }

        // Init Select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#nik_cuti_edit, #kode_cuti_edit').select2({
                theme: 'v2',
                allowClear: true,
                dropdownParent: $('#modalDialogV2')
            }).on('change', function() {
                checkSisaDanHari();
            });
        } else {
            $('#nik_cuti_edit, #kode_cuti_edit').on('change', function() {
                checkSisaDanHari();
            });
        }

        function checkSisaDanHari() {
            const nik = $("#nik_cuti_edit").val();
            const kode_cuti = $("#kode_cuti_edit").val();
            const dari = $("#dari_cuti_edit").val();
            const sampai = $("#sampai_cuti_edit").val();

            if (!nik || !kode_cuti) {
                $('#sisa_cuti_text_edit').text('Pilih jenis cuti & karyawan untuk melihat kuota');
                return;
            }

            $.ajax({
                type: 'GET',
                url: "{{ route('izincuti.getsisaharicuti') }}",
                data: {
                    kode_cuti: kode_cuti,
                    tanggal: dari || '',
                    dari: dari || '',
                    sampai: sampai || dari || '',
                    nik: nik
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        currentSisaCuti = response.sisa_cuti;
                        calculatedDays = response.jml_hari;

                        if (dari && sampai) {
                            $("#jml_hari_cuti_edit_display").val(`${response.jml_hari} Hari Kerja`);
                        }

                        let textMsg = response.message || `Sisa kuota: ${response.sisa_cuti} hari`;
                        if (response.hari_libur_count > 0 || response.hari_off_count > 0) {
                            textMsg += ` (Libur/Off dilewati)`;
                        }

                        $('#sisa_cuti_text_edit').text(textMsg);
                        $('#sisa_cuti_info_box_edit').removeClass('bg-slate-50 border-slate-200 text-slate-600 bg-rose-50 border-rose-200 text-rose-800').addClass('bg-emerald-50 border-emerald-200 text-emerald-800 font-bold');
                    }
                }
            });
        }

        // Run initial check
        checkSisaDanHari();

        // Validation Rules like form karyawan
        const validationRules = {
            'nik': { required: true, message: 'Silakan pilih karyawan terlebih dahulu' },
            'kode_cuti': { required: true, message: 'Silakan pilih jenis cuti' },
            'dari': { required: true, message: 'Tanggal mulai cuti wajib diisi' },
            'sampai': { required: true, message: 'Tanggal selesai cuti wajib diisi' },
            'pelimpahan_tugas': { required: true, message: 'Pelimpahan tugas wajib diisi' },
            'nama_kepala_divisi': { required: true, message: 'Nama kepala divisi/atasan wajib diisi' },
            'keterangan': { required: true, message: 'Alasan permohonan cuti wajib diisi' }
        };

        function showError(element, message) {
            const $el = $(element);
            const $container = $el.closest('.space-y-1, .space-y-1.5, .space-y-2');
            const $wrapper = $el.closest('.relative');
            
            const errorStyles = {
                'border-color': '#ef4444',
                'background-color': '#fff1f2',
                'box-shadow': '0 0 0 2px rgba(239, 68, 68, 0.2)'
            };

            $el.addClass('v2-input-error').css(errorStyles);
            
            if ($wrapper.length > 0) {
                $wrapper.find('input, select, textarea, .flatpickr-input').addClass('v2-input-error').css(errorStyles);
            }
            
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.next('.select2-container').addClass('v2-input-error');
                $el.next('.select2-container').find('.select2-selection').addClass('v2-input-error').css(errorStyles);
            }
            
            $wrapper.find('span i').css('color', '#ef4444');
            $container.find('.error-msg').remove();
            $container.append(`
                <p class="error-msg" style="font-size: 11px; font-weight: 600; color: #ef4444; margin: 4px 0 0 0; display: flex; align-items: center; gap: 4px;">
                    <i class="ti ti-alert-circle" style="font-size: 13px;"></i>
                    <span>${message}</span>
                </p>
            `);
        }

        function clearError(element) {
            const $el = $(element);
            const $container = $el.closest('.space-y-1, .space-y-1.5, .space-y-2');
            const $wrapper = $el.closest('.relative');
            
            const clearStyles = {
                'border-color': '',
                'background-color': '',
                'box-shadow': ''
            };

            $el.removeClass('v2-input-error').css(clearStyles);
            
            if ($wrapper.length > 0) {
                $wrapper.find('input, select, textarea, .flatpickr-input').removeClass('v2-input-error').css(clearStyles);
            }
            
            if ($el.hasClass('select2-hidden-accessible')) {
                $el.next('.select2-container').removeClass('v2-input-error');
                $el.next('.select2-container').find('.select2-selection').removeClass('v2-input-error').css(clearStyles);
            }
            
            $wrapper.find('span i').css('color', '');
            $container.find('.error-msg').remove();
        }

        function validateSingleField(el) {
            const $el = $(el);
            const name = $el.attr('name') || $el.attr('id');
            const val = ($el.val() || '').toString().trim();

            const rule = validationRules[name];
            if (!rule) {
                clearError($el);
                return true;
            }

            if (rule.required && !val) {
                showError($el, rule.message);
                return false;
            }

            clearError($el);
            return true;
        }

        // Realtime validation trigger
        $(form).on('input change blur', 'input, select, textarea', function(e) {
            const $this = $(this);
            const val = ($this.val() || '').toString().trim();
            if (e.type === 'blur' || val !== '') {
                validateSingleField(this);
            }
        });

        $(form).on('submit', function(e) {
            let isValid = true;
            let firstInvalidEl = null;

            Object.keys(validationRules).forEach(function(fieldName) {
                const $el = $(form).find(`[name="${fieldName}"]`);
                if ($el.length > 0) {
                    const valid = validateSingleField($el);
                    if (!valid) {
                        isValid = false;
                        if (!firstInvalidEl) {
                            firstInvalidEl = $el;
                        }
                    }
                }
            });

            const dari = $("#dari_cuti_edit").val();
            const sampai = $("#sampai_cuti_edit").val();
            if (dari && sampai && sampai < dari) {
                showError($("#sampai_cuti_edit"), 'Tanggal selesai tidak boleh sebelum tanggal mulai');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = $("#sampai_cuti_edit");
            }

            if (!isValid) {
                e.preventDefault();
                if (firstInvalidEl) {
                    firstInvalidEl.focus();
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Formulir Belum Lengkap',
                        text: 'Silakan periksa kembali field yang bertanda merah.',
                        customClass: {
                            popup: 'rounded-2xl shadow-xl'
                        }
                    });
                }
                return false;
            }

            const nik = $("#nik_cuti_edit").val();

            if (calculatedDays <= 0 && dari && sampai && nik) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Rentang tanggal yang dipilih tidak memiliki hari kerja aktif (seluruhnya hari libur / off)!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            const $btn = $('#btnSubmitCutiEditV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
