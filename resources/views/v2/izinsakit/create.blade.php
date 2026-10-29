<form action="{{ route('izinsakit.store') }}" method="POST" id="formIzinSakitCreateV2" class="space-y-4" enctype="multipart/form-data" novalidate>
    @csrf

    <div class="space-y-3.5">
        <!-- Kode Izin (Auto) -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
                Kode Izin Sakit
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-barcode text-base"></i>
                </span>
                <input type="text" 
                       value="Otomatis Digenerate Sistem (IS...)" 
                       disabled 
                       class="w-full h-10 pl-10 pr-3.5 text-xs font-mono font-medium text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
            </div>
        </div>

        <!-- Pilih Karyawan -->
        <div class="space-y-1.5">
            <label for="nik_sakit_create" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="nik" id="nik_sakit_create" class="w-full select2-v2" style="width: 100%;">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawan as $d)
                        <option value="{{ $d->nik }}">{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nik_sakit"></div>
        </div>

        <!-- Rentang Tanggal Izin Sakit (Dari & Sampai) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Dari -->
            <div class="space-y-1.5">
                <label for="dari_sakit_create" class="block text-xs font-bold text-slate-700">
                    Dari Tanggal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="dari_sakit_create" 
                           name="dari" 
                           placeholder="Pilih tanggal mulai" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_dari_sakit"></div>
            </div>

            <!-- Sampai -->
            <div class="space-y-1.5">
                <label for="sampai_sakit_create" class="block text-xs font-bold text-slate-700">
                    Sampai Tanggal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="sampai_sakit_create" 
                           name="sampai" 
                           placeholder="Pilih tanggal selesai" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_sampai_sakit"></div>
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
                       id="jml_hari_sakit_display" 
                       readonly 
                       value="0" 
                       class="w-12 text-center text-sm font-black text-emerald-700 bg-white border border-emerald-200 rounded-lg py-0.5">
                <span class="text-xs font-bold text-emerald-700">Hari</span>
            </div>
        </div>

        <!-- Keterangan / Diagnosis Sakit -->
        <div class="space-y-1.5">
            <label for="keterangan_sakit_create" class="block text-xs font-bold text-slate-700">
                Alasan / Keterangan Sakit <span class="text-rose-500">*</span>
            </label>
            <textarea name="keterangan" 
                      id="keterangan_sakit_create" 
                      rows="3" 
                      placeholder="Jelaskan kondisi medis atau keterangan sakit..."
                      class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"></textarea>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_keterangan_sakit"></div>
        </div>

        <!-- Upload Berkas Surat Izin Dokter (SID) -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
                Unggah Surat Dokter (SID) <span class="text-slate-400 font-normal">(Opsional / Gambar Max 10MB)</span>
            </label>
            <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-4 text-center transition bg-slate-50/60 hover:bg-emerald-50/30 group cursor-pointer"
                 onclick="document.getElementById('file_sid_create').click()">
                <input type="file" 
                       name="sid" 
                       id="file_sid_create" 
                       accept="image/jpeg,image/png,image/jpg,image/webp" 
                       class="hidden">
                <div class="flex flex-col items-center justify-center gap-1.5">
                    <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-2xs flex items-center justify-center text-slate-500 group-hover:text-emerald-600 transition">
                        <i class="ti ti-upload text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-700 group-hover:text-emerald-700" id="file_sid_name">
                            Klik untuk memilih foto/berkas SID
                        </p>
                        <p class="text-[11px] text-slate-400">Format: JPG, PNG, JPEG (Maks. 10MB)</p>
                    </div>
                </div>
            </div>
            <!-- Preview Thumbnail -->
            <div id="preview_sid_container" class="hidden mt-2 p-2 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                <img id="preview_sid_img" src="" alt="Preview SID" class="w-12 h-12 object-cover rounded-lg border border-slate-200">
                <div class="flex-1 min-w-0 text-xs">
                    <span class="font-bold text-slate-700 block truncate" id="preview_sid_filename">nama_file.jpg</span>
                    <span class="text-[10px] text-slate-400" id="preview_sid_filesize">0 KB</span>
                </div>
                <button type="button" id="btnRemoveSid" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition" title="Hapus Berkas">
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
                id="btnSubmitSakitCreateV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-send text-sm"></i>
            <span>Simpan Pengajuan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formIzinSakitCreateV2');
        const batasi_hari_izin_sakit = "{{ $general_setting->batasi_hari_izin_sakit ?? 0 }}";
        const jml_hari_izin_sakit_max = "{{ $general_setting->jml_hari_izin_sakit_max ?? 0 }}";

        // Init Flatpickr
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#dari_sakit_create", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                onChange: function() {
                    getHitungHari();
                }
            });

            flatpickr("#sampai_sakit_create", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                onChange: function() {
                    getHitungHari();
                }
            });
        }

        // Init Select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#nik_sakit_create').select2({
                theme: 'v2',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
                dropdownParent: $('#modalDialogV2')
            }).on('change', function() {
                getHitungHari();
            });
        } else {
            $('#nik_sakit_create').on('change', function() {
                getHitungHari();
            });
        }

        // File SID Preview Handler
        $('#file_sid_create').on('change', function(e) {
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
                    $('#preview_sid_img').attr('src', e.target.result);
                    $('#preview_sid_filename').text(file.name);
                    $('#preview_sid_filesize').text((file.size / 1024).toFixed(1) + ' KB');
                    $('#preview_sid_container').removeClass('hidden');
                    $('#file_sid_name').text(file.name);
                }
                reader.readAsDataURL(file);
            }
        });

        $('#btnRemoveSid').on('click', function() {
            $('#file_sid_create').val('');
            $('#preview_sid_container').addClass('hidden');
            $('#file_sid_name').text('Klik untuk memilih foto/berkas SID');
        });

        function getHitungHari() {
            const dari = $("#dari_sakit_create").val();
            const sampai = $("#sampai_sakit_create").val();
            const nik = $("#nik_sakit_create").val();

            if (dari && sampai && nik) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('hitung-hari-kerja') }}",
                    data: { nik: nik, dari: dari, sampai: sampai },
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            $("#jml_hari_sakit_display").val(res.total_hari);
                        }
                    }
                });
            }
        }

        // ── Reset all error states ──────────────────────────────────────────────
        function resetErrors() {
            $('.error-feedback').addClass('hidden').text('');
            $('#nik_sakit_create').next('.select2-container').find('.select2-selection').removeClass('border-rose-500');
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

            const nik        = $('#nik_sakit_create').val();
            const dari       = $('#dari_sakit_create').val();
            const sampai     = $('#sampai_sakit_create').val();
            const keterangan = $('#keterangan_sakit_create').val().trim();

            if (!nik) {
                showFieldError('#nik_sakit_create', 'err_nik_sakit', 'Silakan pilih karyawan terlebih dahulu');
                hasError = true;
            }
            if (!dari) {
                showFieldError('#dari_sakit_create', 'err_dari_sakit', 'Tanggal mulai izin sakit wajib diisi');
                hasError = true;
            }
            if (!sampai) {
                showFieldError('#sampai_sakit_create', 'err_sampai_sakit', 'Tanggal selesai izin sakit wajib diisi');
                hasError = true;
            } else if (dari && sampai < dari) {
                showFieldError('#sampai_sakit_create', 'err_sampai_sakit', 'Tanggal selesai tidak boleh sebelum tanggal mulai');
                hasError = true;
            }
            if (!keterangan) {
                showFieldError('#keterangan_sakit_create', 'err_keterangan_sakit', 'Keterangan sakit wajib diisi');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const jml_hari = parseInt($("#jml_hari_sakit_display").val()) || 0;
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

            const $btn = $('#btnSubmitSakitCreateV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
