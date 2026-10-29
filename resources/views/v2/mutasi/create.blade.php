<form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data" id="formMutasi" class="space-y-4" novalidate>
    @csrf
    
    <div class="space-y-4">
        <!-- Banner Info Ketentuan -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Form Mutasi, Promosi & Demosi</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Pilih karyawan dan tentukan jenis perubahannya. Data jabatan dan unit kerja saat ini akan terisi otomatis dan diperbarui ke posisi baru.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            <!-- Tanggal Mutasi -->
            <div class="space-y-1.5">
                <label for="tanggal_mutasi" class="block text-xs font-bold text-slate-700">
                    Tanggal Efektif <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           name="tanggal_mutasi" 
                           id="tanggal_mutasi" 
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition flatpickr-mutasi" 
                           value="{{ date('Y-m-d') }}" 
                           placeholder="Pilih Tanggal">
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_tanggal_mutasi"></div>
            </div>

            <!-- Jenis Mutasi -->
            <div class="space-y-1.5">
                <label for="jenis_mutasi" class="block text-xs font-bold text-slate-700">
                    Jenis Perubahan <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select name="jenis_mutasi" id="jenis_mutasi" class="w-full h-10 px-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition appearance-none cursor-pointer">
                        <option value="">Pilih Jenis</option>
                        <option value="MUTASI">MUTASI (Pindah Cabang / Departemen)</option>
                        <option value="PROMOSI">PROMOSI (Kenaikan Jabatan)</option>
                        <option value="DEMOSI">DEMOSI (Penurunan Jabatan)</option>
                    </select>
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <i class="ti ti-chevron-down text-xs"></i>
                    </span>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jenis_mutasi"></div>
            </div>
        </div>

        <!-- Pilih Karyawan -->
        <div class="space-y-1.5" id="group_nik">
            <label for="nik" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan <span class="text-rose-500">*</span>
            </label>
            <select name="nik" id="nik" class="w-full select2-mutasi">
                <option value="">-- Cari NIK / Nama Karyawan --</option>
                @foreach ($karyawan as $k)
                    <option value="{{ $k->nik }}">{{ $k->nik }} - {{ $k->nama_karyawan }}</option>
                @endforeach
            </select>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nik"></div>
        </div>

        <!-- Info Data Saat Ini -->
        <div id="current_data" class="hidden rounded-xl bg-sky-50/70 border border-sky-200/80 p-3.5">
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="ti ti-id-badge text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs font-bold text-slate-800 mb-1">Posisi & Penempatan Saat Ini</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[11px] text-slate-600">
                        <div class="bg-white/80 rounded-lg p-2 border border-sky-100">
                            <span class="text-slate-400 block text-[10px]">Cabang:</span>
                            <span id="cur_cabang" class="font-bold text-slate-800 truncate block"></span>
                        </div>
                        <div class="bg-white/80 rounded-lg p-2 border border-sky-100">
                            <span class="text-slate-400 block text-[10px]">Departemen:</span>
                            <span id="cur_dept" class="font-bold text-slate-800 truncate block"></span>
                        </div>
                        <div class="bg-white/80 rounded-lg p-2 border border-sky-100">
                            <span class="text-slate-400 block text-[10px]">Jabatan:</span>
                            <span id="cur_jabatan" class="font-bold text-slate-800 truncate block"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider Posisi Baru -->
        <div class="relative py-2">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-slate-200"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-emerald-50 text-emerald-700 px-3.5 py-1 text-[11px] font-bold tracking-wider uppercase rounded-full border border-emerald-200/80 flex items-center gap-1.5 shadow-2xs">
                    <i class="ti ti-arrow-forward text-xs"></i>
                    <span>Penempatan & Posisi Baru</span>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            <!-- Cabang Baru -->
            <div class="space-y-1.5" id="group_kode_cabang_baru">
                <label for="kode_cabang_baru" class="block text-xs font-bold text-slate-700">
                    Cabang Baru <span class="text-rose-500">*</span>
                </label>
                <select name="kode_cabang_baru" id="kode_cabang_baru" class="w-full select2-mutasi">
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $c)
                        <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_cabang_baru"></div>
            </div>

            <!-- Departemen Baru -->
            <div class="space-y-1.5" id="group_kode_dept_baru">
                <label for="kode_dept_baru" class="block text-xs font-bold text-slate-700">
                    Departemen Baru <span class="text-rose-500">*</span>
                </label>
                <select name="kode_dept_baru" id="kode_dept_baru" class="w-full select2-mutasi">
                    <option value="">Pilih Departemen</option>
                    @foreach ($departemen as $d)
                        <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                    @endforeach
                </select>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_dept_baru"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
            <!-- Jabatan Baru -->
            <div class="space-y-1.5" id="group_kode_jabatan_baru">
                <label for="kode_jabatan_baru" class="block text-xs font-bold text-slate-700">
                    Jabatan Baru <span class="text-rose-500">*</span>
                </label>
                <select name="kode_jabatan_baru" id="kode_jabatan_baru" class="w-full select2-mutasi">
                    <option value="">Pilih Jabatan</option>
                    @foreach ($jabatan as $j)
                        <option value="{{ $j->kode_jabatan }}">{{ $j->nama_jabatan }}</option>
                    @endforeach
                </select>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_jabatan_baru"></div>
            </div>

            <!-- Status Karyawan Baru -->
            <div class="space-y-1.5">
                <label for="status_karyawan_baru" class="block text-xs font-bold text-slate-700">
                    Status Karyawan Baru <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select name="status_karyawan_baru" id="status_karyawan_baru" class="w-full select2-mutasi">
                    <option value="">Tetap (Tidak Berubah)</option>
                    @foreach ($status_karyawan as $s)
                        <option value="{{ $s->kode_status_karyawan }}">{{ $s->nama_status_karyawan }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Keterangan & Upload SK -->
        <div class="space-y-3.5">
            <div class="space-y-1.5">
                <label for="keterangan" class="block text-xs font-bold text-slate-700">Keterangan / Alasan Perubahan</label>
                <textarea name="keterangan" id="keterangan" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-800 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition resize-none placeholder:text-slate-400" rows="2" placeholder="Tuliskan alasan mutasi, promosi, atau demosi..."></textarea>
            </div>
            
            <div class="space-y-1.5">
                <label for="doc_sk" class="block text-xs font-bold text-slate-700 flex items-center justify-between">
                    <span>Upload Dokumen SK (Opsional)</span>
                    <span class="text-[10.5px] font-normal text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">PDF, JPG, PNG (Max 2MB)</span>
                </label>
                <input type="file" name="doc_sk" id="doc_sk" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_doc_sk"></div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" 
                id="btnSimpanMutasi" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Data Mutasi</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formMutasi');

        // Helper functions for validation styling
        function setError(element, message) {
            const $el = $(element);
            const $container = $el.closest('.space-y-1, .space-y-1.5, .space-y-2');
            
            const errorStyles = {
                'border-color': '#ef4444',
                'background-color': '#fff1f2',
                'box-shadow': '0 0 0 2px rgba(239, 68, 68, 0.2)'
            };

            $el.addClass('v2-input-error').css(errorStyles);

            // Handle Flatpickr altInput
            if ($el.next('.flatpickr-input').length > 0) {
                $el.next('.flatpickr-input').addClass('v2-input-error').css(errorStyles);
            }

            // Handle Select2
            if ($el.hasClass('select2-hidden-accessible')) {
                const $select2 = $el.next('.select2-container');
                $select2.addClass('v2-input-error');
                $select2.find('.select2-selection').addClass('v2-input-error').css(errorStyles);
            }

            const errorId = $el.attr('id') ? 'err_' + $el.attr('id') : '';
            if (errorId && $('#' + errorId).length > 0) {
                $('#' + errorId).text(message).removeClass('hidden');
            } else if ($container.length > 0) {
                $container.find('.error-feedback').remove();
                $container.append(`<div class="text-rose-500 text-[11px] font-medium error-feedback mt-1">${message}</div>`);
            }
        }

        function clearError(element) {
            const $el = $(element);
            const $container = $el.closest('.space-y-1, .space-y-1.5, .space-y-2');
            
            const clearStyles = {
                'border-color': '',
                'background-color': '',
                'box-shadow': ''
            };

            $el.removeClass('v2-input-error').css(clearStyles);

            // Handle Flatpickr altInput
            if ($el.next('.flatpickr-input').length > 0) {
                $el.next('.flatpickr-input').removeClass('v2-input-error').css(clearStyles);
            }

            // Handle Select2
            if ($el.hasClass('select2-hidden-accessible')) {
                const $select2 = $el.next('.select2-container');
                $select2.removeClass('v2-input-error');
                $select2.find('.select2-selection').removeClass('v2-input-error').css(clearStyles);
            }

            const errorId = $el.attr('id') ? 'err_' + $el.attr('id') : '';
            if (errorId && $('#' + errorId).length > 0) {
                $('#' + errorId).text('').addClass('hidden');
            }
            if ($container.length > 0) {
                $container.find('.error-feedback').addClass('hidden').text('');
            }
        }

        // Initialize Flatpickr for date
        if (typeof flatpickr !== 'undefined') {
            flatpickr(".flatpickr-mutasi", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateStr) {
                        clearError(instance.element);
                    }
                }
            });
        }

        // Initialize Select2 with V2 Theme
        $(".select2-mutasi").select2({
            dropdownParent: $('#modalInput'),
            width: '100%',
            theme: 'default'
        });

        // Trigger loading Karyawan info
        $("#nik").on('change', function() {
            var nik = $(this).val();
            if (nik) {
                clearError(this);

                $.ajax({
                    url: '/mutasi/' + nik + '/getKaryawan',
                    type: 'GET',
                    beforeSend: function() {
                        $("#current_data").removeClass('hidden');
                        $("#cur_cabang").text('Memuat...');
                        $("#cur_dept").text('Memuat...');
                        $("#cur_jabatan").text('Memuat...');
                    },
                    success: function(res) {
                        if (res) {
                            $("#cur_cabang").text(res.cabang ? res.cabang.nama_cabang : (res.kode_cabang || '-'));
                            $("#cur_dept").text(res.departemen ? res.departemen.nama_dept : (res.kode_dept || '-'));
                            $("#cur_jabatan").text(res.jabatan ? res.jabatan.nama_jabatan : (res.kode_jabatan || '-'));

                            // Auto fill new data with current as default
                            if (res.kode_cabang) {
                                $('#kode_cabang_baru').val(res.kode_cabang).trigger('change');
                                clearError('#kode_cabang_baru');
                            }
                            if (res.kode_dept) {
                                $('#kode_dept_baru').val(res.kode_dept).trigger('change');
                                clearError('#kode_dept_baru');
                            }
                            if (res.kode_jabatan) {
                                $('#kode_jabatan_baru').val(res.kode_jabatan).trigger('change');
                                clearError('#kode_jabatan_baru');
                            }
                            $('#status_karyawan_baru').val("").trigger('change');
                        }
                    },
                    error: function() {
                        $("#current_data").addClass('hidden');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data karyawan atau Anda tidak memiliki akses.',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold'
                            }
                        });
                    }
                });
            } else {
                $("#current_data").addClass('hidden');
                $('#kode_cabang_baru').val("").trigger('change');
                $('#kode_dept_baru').val("").trigger('change');
                $('#kode_jabatan_baru').val("").trigger('change');
                $('#status_karyawan_baru').val("").trigger('change');
            }
        });

        // Realtime clear validation on changes
        $('#tanggal_mutasi').on('input change', function() {
            if ($(this).val()) clearError(this);
        });

        $('#jenis_mutasi').on('change', function() {
            if ($(this).val()) clearError(this);
        });

        $('#kode_cabang_baru').on('change', function() {
            if ($(this).val()) clearError(this);
        });

        $('#kode_dept_baru').on('change', function() {
            if ($(this).val()) clearError(this);
        });

        $('#kode_jabatan_baru').on('change', function() {
            if ($(this).val()) clearError(this);
        });

        $('#doc_sk').on('change', function() {
            clearError(this);
        });

        // Form Submit Validation & Loading State
        $(form).on('submit', function(e) {
            const tanggal = $('#tanggal_mutasi').val().trim();
            const jenis = $('#jenis_mutasi').val();
            const nik = $('#nik').val();
            const cabangBaru = $('#kode_cabang_baru').val();
            const deptBaru = $('#kode_dept_baru').val();
            const jabatanBaru = $('#kode_jabatan_baru').val();
            const docSk = document.getElementById('doc_sk');

            let hasError = false;

            // Reset error states
            ['#tanggal_mutasi', '#jenis_mutasi', '#nik', '#kode_cabang_baru', '#kode_dept_baru', '#kode_jabatan_baru', '#doc_sk'].forEach(function(sel) {
                clearError(sel);
            });

            if (!tanggal) {
                setError('#tanggal_mutasi', 'Tanggal Efektif wajib diisi');
                hasError = true;
            }

            if (!jenis) {
                setError('#jenis_mutasi', 'Jenis Perubahan wajib dipilih');
                hasError = true;
            }

            if (!nik) {
                setError('#nik', 'Karyawan wajib dipilih');
                hasError = true;
            }

            if (!cabangBaru) {
                setError('#kode_cabang_baru', 'Cabang Baru wajib dipilih');
                hasError = true;
            }

            if (!deptBaru) {
                setError('#kode_dept_baru', 'Departemen Baru wajib dipilih');
                hasError = true;
            }

            if (!jabatanBaru) {
                setError('#kode_jabatan_baru', 'Jabatan Baru wajib dipilih');
                hasError = true;
            }

            if (docSk && docSk.files && docSk.files[0]) {
                const file = docSk.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    setError('#doc_sk', 'Ukuran file dokumen SK maksimal 2MB');
                    hasError = true;
                }
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const btn = $('#btnSimpanMutasi');
            btn.prop('disabled', true).html(`
                <svg class="animate-spin h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Menyimpan...</span>
            `);
        });
    })();
</script>
