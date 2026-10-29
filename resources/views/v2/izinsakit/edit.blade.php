<form action="{{ route('izinsakit.update', Crypt::encrypt($izinsakit->kode_izin_sakit)) }}" method="POST" id="formIzinSakitEditV2" class="space-y-4" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    <div class="space-y-3.5">
        <!-- Kode Izin (Readonly) -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
                Kode Izin Sakit
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-barcode text-base"></i>
                </span>
                <input type="text" 
                       value="{{ $izinsakit->kode_izin_sakit }}" 
                       disabled 
                       class="w-full h-10 pl-10 pr-3.5 text-xs font-mono font-bold text-slate-700 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
            </div>
        </div>

        <!-- Pilih Karyawan -->
        <div class="space-y-1.5">
            <label for="nik_sakit_edit" class="block text-xs font-bold text-slate-700">
                Karyawan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="nik" id="nik_sakit_edit" class="w-full select2-v2" style="width: 100%;">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawan as $d)
                        <option value="{{ $d->nik }}" {{ $izinsakit->nik == $d->nik ? 'selected' : '' }}>
                            {{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nik_sakit_edit"></div>
        </div>

        <!-- Rentang Tanggal Izin Sakit (Dari & Sampai) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Dari -->
            <div class="space-y-1.5">
                <label for="dari_sakit_edit" class="block text-xs font-bold text-slate-700">
                    Dari Tanggal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="dari_sakit_edit" 
                           name="dari" 
                           value="{{ $izinsakit->dari }}"
                           placeholder="Pilih tanggal mulai" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_dari_sakit_edit"></div>
            </div>

            <!-- Sampai -->
            <div class="space-y-1.5">
                <label for="sampai_sakit_edit" class="block text-xs font-bold text-slate-700">
                    Sampai Tanggal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="sampai_sakit_edit" 
                           name="sampai" 
                           value="{{ $izinsakit->sampai }}"
                           placeholder="Pilih tanggal selesai" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_sampai_sakit_edit"></div>
            </div>
        </div>

        <!-- Jumlah Hari Kerja (Hitung Otomatis) -->
        <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-100 flex items-center justify-between">
            <div class="flex items-center gap-2 text-emerald-800 text-xs font-bold">
                <i class="ti ti-calendar-time text-base text-emerald-600"></i>
                <span>Jumlah Hari Kerja Terhitung:</span>
            </div>
            <div class="flex items-center gap-1">
                <input type="text" 
                       id="jml_hari_sakit_display_edit" 
                       readonly 
                       value="0" 
                       class="w-12 text-center text-sm font-black text-emerald-700 bg-white border border-emerald-200 rounded-lg py-0.5">
                <span class="text-xs font-bold text-emerald-700">Hari</span>
            </div>
        </div>

        <!-- Keterangan / Diagnosis Sakit -->
        <div class="space-y-1.5">
            <label for="keterangan_sakit_edit" class="block text-xs font-bold text-slate-700">
                Alasan / Keterangan Sakit <span class="text-rose-500">*</span>
            </label>
            <textarea name="keterangan" 
                      id="keterangan_sakit_edit" 
                      rows="3" 
                      placeholder="Jelaskan kondisi medis atau keterangan sakit..."
                      class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">{{ $izinsakit->keterangan }}</textarea>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_keterangan_sakit_edit"></div>
        </div>

        <!-- Upload & Berkas Lampiran SID -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
                Surat Dokter (SID)
            </label>
            
            <!-- Existing SID Preview if Available -->
            @if (!empty($izinsakit->doc_sid))
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-3 mb-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        @if (Storage::disk('public')->exists('/uploads/sid/' . $izinsakit->doc_sid))
                            <a href="{{ asset('storage/uploads/sid/' . $izinsakit->doc_sid) }}?t={{ time() }}" target="_blank">
                                <img src="{{ asset('storage/uploads/sid/' . $izinsakit->doc_sid) }}?t={{ time() }}" alt="SID" class="w-12 h-12 object-cover rounded-lg border border-slate-200 hover:opacity-80 transition">
                            </a>
                            <div class="min-w-0 text-xs">
                                <span class="font-bold text-slate-700 block truncate">Surat Dokter Terlampir</span>
                                <a href="{{ asset('storage/uploads/sid/' . $izinsakit->doc_sid) }}?t={{ time() }}" target="_blank" class="text-[11px] text-emerald-600 font-semibold hover:underline">
                                    <i class="ti ti-external-link"></i> Buka Gambar Penuh
                                </a>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center border border-rose-200">
                                <i class="ti ti-file-x text-lg"></i>
                            </div>
                            <span class="text-xs text-rose-500 font-medium">File lampiran lama tidak ditemukan</span>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 bg-white px-2 py-1 rounded border border-slate-200">File Saat Ini</span>
                </div>
            @endif

            <!-- Upload New SID File -->
            <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-4 text-center transition bg-slate-50/60 hover:bg-emerald-50/30 group cursor-pointer"
                 onclick="document.getElementById('file_sid_edit').click()">
                <input type="file" 
                       name="sid" 
                       id="file_sid_edit" 
                       accept="image/jpeg,image/png,image/jpg,image/webp" 
                       class="hidden">
                <div class="flex flex-col items-center justify-center gap-1.5">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-slate-500 group-hover:text-emerald-600 transition">
                        <i class="ti ti-upload text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-700 group-hover:text-emerald-700" id="file_sid_name_edit">
                            {{ !empty($izinsakit->doc_sid) ? 'Ganti berkas / unggah foto SID baru' : 'Klik untuk memilih foto/berkas SID' }}
                        </p>
                        <p class="text-[11px] text-slate-400">Format: JPG, PNG, JPEG (Maks. 10MB)</p>
                    </div>
                </div>
            </div>
            <!-- Preview Thumbnail New File -->
            <div id="preview_sid_container_edit" class="hidden mt-2 p-2 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                <img id="preview_sid_img_edit" src="" alt="Preview SID Baru" class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                <div class="flex-1 min-w-0 text-xs">
                    <span class="font-bold text-slate-700 block truncate" id="preview_sid_filename_edit">nama_file.jpg</span>
                    <span class="text-[10px] text-slate-400" id="preview_sid_filesize_edit">0 KB</span>
                </div>
                <button type="button" id="btnRemoveSidEdit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Batalkan Berkas Baru">
                    <i class="ti ti-trash text-base"></i>
                </button>
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
                id="btnSubmitSakitEditV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-refresh text-sm"></i>
            <span>Perbarui Izin Sakit</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formIzinSakitEditV2');
        const batasi_hari_izin_sakit = "{{ $general_setting->batasi_hari_izin_sakit ?? 0 }}";
        const jml_hari_izin_sakit_max = "{{ $general_setting->jml_hari_izin_sakit_max ?? 0 }}";

        // Init Flatpickr
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#dari_sakit_edit", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                defaultDate: "{{ $izinsakit->dari }}",
                onChange: function() {
                    getHitungHari();
                }
            });

            flatpickr("#sampai_sakit_edit", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                defaultDate: "{{ $izinsakit->sampai }}",
                onChange: function() {
                    getHitungHari();
                }
            });
        }

        // Init Select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#nik_sakit_edit').select2({
                theme: 'v2',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
                dropdownParent: $('#modalDialogV2')
            }).on('change', function() {
                getHitungHari();
            });
        } else {
            $('#nik_sakit_edit').on('change', function() {
                getHitungHari();
            });
        }

        // File SID Preview Handler
        $('#file_sid_edit').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 10 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ukuran File Terlalu Besar',
                        text: 'Maksimal ukuran file lampiran adalah 10MB!'
                    });
                    $(this).val('');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_sid_img_edit').attr('src', e.target.result);
                    $('#preview_sid_filename_edit').text(file.name);
                    $('#preview_sid_filesize_edit').text((file.size / 1024).toFixed(1) + ' KB');
                    $('#preview_sid_container_edit').removeClass('hidden');
                    $('#file_sid_name_edit').text(file.name);
                }
                reader.readAsDataURL(file);
            }
        });

        $('#btnRemoveSidEdit').on('click', function() {
            $('#file_sid_edit').val('');
            $('#preview_sid_container_edit').addClass('hidden');
            $('#file_sid_name_edit').text("{{ !empty($izinsakit->doc_sid) ? 'Ganti berkas / unggah foto SID baru' : 'Klik untuk memilih foto/berkas SID' }}");
        });

        function getHitungHari() {
            const dari = $("#dari_sakit_edit").val();
            const sampai = $("#sampai_sakit_edit").val();
            const nik = $("#nik_sakit_edit").val();

            if (dari && sampai && nik) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('hitung-hari-kerja') }}",
                    data: { nik: nik, dari: dari, sampai: sampai },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            $("#jml_hari_sakit_display_edit").val(res.total_hari);
                        }
                    }
                });
            }
        }

        getHitungHari();

        // Validation Rules like form karyawan
        const validationRules = {
            'nik': { required: true, message: 'Silakan pilih karyawan terlebih dahulu' },
            'dari': { required: true, message: 'Tanggal mulai izin sakit wajib diisi' },
            'sampai': { required: true, message: 'Tanggal selesai izin sakit wajib diisi' },
            'keterangan': { required: true, message: 'Keterangan sakit wajib diisi' }
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

            const dari = $("#dari_sakit_edit").val();
            const sampai = $("#sampai_sakit_edit").val();
            if (dari && sampai && sampai < dari) {
                showError($("#sampai_sakit_edit"), 'Tanggal selesai tidak boleh sebelum tanggal mulai');
                isValid = false;
                if (!firstInvalidEl) firstInvalidEl = $("#sampai_sakit_edit");
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

            const jml_hari = parseInt($("#jml_hari_sakit_display_edit").val()) || 0;
            const nik = $("#nik_sakit_edit").val();

            if (jml_hari <= 0 && dari && sampai && nik) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Rentang tanggal yang dipilih tidak memiliki hari kerja aktif (seluruhnya hari libur / off)!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            if (batasi_hari_izin_sakit == 1 && parseInt(jml_hari_izin_sakit_max) > 0 && jml_hari > parseInt(jml_hari_izin_sakit_max)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Hari Terlampaui',
                    text: 'Periode Izin Sakit (' + jml_hari + ' Hari Kerja) tidak boleh lebih dari ' + jml_hari_izin_sakit_max + ' hari!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            const $btn = $('#btnSubmitSakitEditV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Memperbarui...</span>');
        });
    })();
</script>
