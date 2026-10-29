<form action="{{ route('izincuti.store') }}" method="POST" id="formIzinCutiCreateV2" class="space-y-4" novalidate>
    @csrf

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
                       value="Otomatis Digenerate Sistem (IC...)" 
                       disabled 
                       class="w-full h-10 pl-10 pr-3.5 text-xs font-mono font-medium text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
            </div>
        </div>

        <!-- Pilih Karyawan -->
        <div class="space-y-1.5">
            <label for="nik_cuti_create" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="nik" id="nik_cuti_create" class="w-full select2-v2" style="width: 100%;">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawan as $d)
                        <option value="{{ $d->nik }}">{{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nik_cuti"></div>
        </div>

        <!-- Pilih Jenis Cuti -->
        <div class="space-y-1.5">
            <label for="kode_cuti_create" class="block text-xs font-bold text-slate-700">
                Jenis Cuti <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="kode_cuti" id="kode_cuti_create" class="w-full select2-v2" style="width: 100%;">
                    <option value="">-- Pilih Jenis Cuti --</option>
                    @foreach ($jenis_cuti as $d)
                        <option value="{{ $d->kode_cuti }}">{{ $d->jenis_cuti }} (Maks. {{ $d->jumlah_hari }} Hari)</option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_cuti"></div>
        </div>

        <!-- Rentang Tanggal Cuti (Dari & Sampai) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Dari -->
            <div class="space-y-1.5">
                <label for="dari_cuti_create" class="block text-xs font-bold text-slate-700">
                    Dari Tanggal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="dari_cuti_create" 
                           name="dari" 
                           placeholder="Pilih tanggal mulai" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_dari_cuti"></div>
            </div>

            <!-- Sampai -->
            <div class="space-y-1.5">
                <label for="sampai_cuti_create" class="block text-xs font-bold text-slate-700">
                    Sampai Tanggal <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="sampai_cuti_create" 
                           name="sampai" 
                           placeholder="Pilih tanggal selesai" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_sampai_cuti"></div>
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
                           id="jml_hari_cuti_display" 
                           value="0 Hari Kerja" 
                           disabled 
                           class="w-full h-10 pl-10 pr-3.5 text-xs font-bold text-emerald-700 bg-emerald-50/50 border border-emerald-200 rounded-xl cursor-not-allowed">
                </div>
            </div>

            <div class="space-y-1.5 sm:col-span-2">
                <label class="block text-xs font-bold text-slate-700">
                    Sisa Kuota Cuti
                </label>
                <div id="sisa_cuti_info_box" class="h-10 px-3.5 rounded-xl border border-slate-200 bg-slate-50 flex items-center gap-2 text-xs text-slate-600 font-medium truncate">
                    <i class="ti ti-info-circle text-slate-400 text-sm shrink-0"></i>
                    <span id="sisa_cuti_text" class="truncate">Pilih jenis cuti & tanggal untuk cek sisa kuota</span>
                </div>
            </div>
        </div>

        <!-- Pelimpahan Tugas & Kepala Divisi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <!-- Pelimpahan Tugas -->
            <div class="space-y-1.5">
                <label for="pelimpahan_tugas" class="block text-xs font-bold text-slate-700">
                    Pelimpahan Tugas Ke <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-user-share text-base"></i>
                    </span>
                    <input type="text" 
                           id="pelimpahan_tugas" 
                           name="pelimpahan_tugas" 
                           placeholder="Nama rekan kerja pengganti..." 
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_pelimpahan"></div>
            </div>

            <!-- Nama Kepala Divisi -->
            <div class="space-y-1.5">
                <label for="nama_kepala_divisi" class="block text-xs font-bold text-slate-700">
                    Nama Kepala Divisi / Atasan <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-user-check text-base"></i>
                    </span>
                    <input type="text" 
                           id="nama_kepala_divisi" 
                           name="nama_kepala_divisi" 
                           placeholder="Nama kepala divisi / manager..." 
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kepala_divisi"></div>
            </div>
        </div>

        <!-- Alasan Cuti -->
        <div class="space-y-1.5">
            <label for="keterangan_cuti_create" class="block text-xs font-bold text-slate-700">
                Alasan / Keperluan Cuti <span class="text-rose-500">*</span>
            </label>
            <textarea id="keterangan_cuti_create" 
                      name="keterangan" 
                      rows="3" 
                      placeholder="Jelaskan alasan atau keperluan permohonan cuti secara rinci..."
                      class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"></textarea>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_keterangan_cuti"></div>
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
                id="btnSubmitCutiCreateV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-send text-sm"></i>
            <span>Simpan Pengajuan Cuti</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formIzinCutiCreateV2');
        let currentSisaCuti = 999;
        let calculatedDays = 0;

        // Init Flatpickr
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#dari_cuti_create", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                onChange: function() {
                    checkSisaDanHari();
                }
            });

            flatpickr("#sampai_cuti_create", {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                onChange: function() {
                    checkSisaDanHari();
                }
            });
        }

        // Init Select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#nik_cuti_create, #kode_cuti_create').select2({
                theme: 'v2',
                allowClear: true,
                dropdownParent: $('#modalDialogV2')
            }).on('change', function() {
                checkSisaDanHari();
            });
        } else {
            $('#nik_cuti_create, #kode_cuti_create').on('change', function() {
                checkSisaDanHari();
            });
        }

        function checkSisaDanHari() {
            const nik = $("#nik_cuti_create").val();
            const kode_cuti = $("#kode_cuti_create").val();
            const dari = $("#dari_cuti_create").val();
            const sampai = $("#sampai_cuti_create").val();

            if (!nik || !kode_cuti) {
                $('#sisa_cuti_text').text('Pilih jenis cuti & karyawan untuk melihat kuota');
                $('#sisa_cuti_info_box').removeClass('bg-emerald-50 border-emerald-200 text-emerald-800 bg-rose-50 border-rose-200 text-rose-800').addClass('bg-slate-50 border-slate-200 text-slate-600');
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
                            $("#jml_hari_cuti_display").val(`${response.jml_hari} Hari Kerja`);
                        }

                        let textMsg = response.message || `Sisa kuota: ${response.sisa_cuti} hari`;
                        if (response.hari_libur_count > 0 || response.hari_off_count > 0) {
                            textMsg += ` (Libur/Off dilewati)`;
                        }

                        $('#sisa_cuti_text').text(textMsg);
                        $('#sisa_cuti_info_box').removeClass('bg-slate-50 border-slate-200 text-slate-600 bg-rose-50 border-rose-200 text-rose-800').addClass('bg-emerald-50 border-emerald-200 text-emerald-800 font-bold');
                    }
                }
            });
        }

        // ── Reset all error states ──────────────────────────────────────────────
        function resetErrors() {
            $('.error-feedback').addClass('hidden').text('');
            $('#nik_cuti_create, #kode_cuti_create').next('.select2-container').find('.select2-selection').removeClass('border-rose-500');
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

            const nik             = $('#nik_cuti_create').val();
            const kode_cuti       = $('#kode_cuti_create').val();
            const dari            = $('#dari_cuti_create').val();
            const sampai          = $('#sampai_cuti_create').val();
            const pelimpahan      = $('#pelimpahan_tugas').val().trim();
            const kepala_divisi   = $('#nama_kepala_divisi').val().trim();
            const keterangan      = $('#keterangan_cuti_create').val().trim();

            if (!nik) {
                showFieldError('#nik_cuti_create', 'err_nik_cuti', 'Silakan pilih karyawan terlebih dahulu');
                hasError = true;
            }
            if (!kode_cuti) {
                showFieldError('#kode_cuti_create', 'err_kode_cuti', 'Silakan pilih jenis cuti');
                hasError = true;
            }
            if (!dari) {
                showFieldError('#dari_cuti_create', 'err_dari_cuti', 'Tanggal mulai cuti wajib diisi');
                hasError = true;
            }
            if (!sampai) {
                showFieldError('#sampai_cuti_create', 'err_sampai_cuti', 'Tanggal selesai cuti wajib diisi');
                hasError = true;
            } else if (dari && sampai < dari) {
                showFieldError('#sampai_cuti_create', 'err_sampai_cuti', 'Tanggal selesai tidak boleh sebelum tanggal mulai');
                hasError = true;
            }
            if (!pelimpahan) {
                showFieldError('#pelimpahan_tugas', 'err_pelimpahan', 'Pelimpahan tugas wajib diisi');
                hasError = true;
            }
            if (!kepala_divisi) {
                showFieldError('#nama_kepala_divisi', 'err_kepala_divisi', 'Nama kepala divisi/atasan wajib diisi');
                hasError = true;
            }
            if (!keterangan) {
                showFieldError('#keterangan_cuti_create', 'err_keterangan_cuti', 'Alasan permohonan cuti wajib diisi');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

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

            if (calculatedDays > parseInt(currentSisaCuti)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Batas Kuota Terlampaui',
                    text: `Periode cuti (${calculatedDays} Hari Kerja) melebihi batas kuota (${currentSisaCuti} Hari)!`,
                    confirmButtonText: 'OK'
                });
                return false;
            }

            const $btn = $('#btnSubmitCutiCreateV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
