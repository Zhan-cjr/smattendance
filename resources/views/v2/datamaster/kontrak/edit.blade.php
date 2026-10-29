@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('kontrak.update', Crypt::encrypt($kontrak->id)) }}" method="POST" id="formEditKontrakV2" enctype="multipart/form-data" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- SECTION 1: INFORMASI KARYAWAN & ADMINISTRASI -->
    <div class="space-y-4">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-user-check text-xs"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-800">1. Informasi Karyawan & Administrasi ({{ $kontrak->no_kontrak }})</h4>
                <p class="text-[11px] text-slate-400">Pilih karyawan dan register dokumen kontrak</p>
            </div>
        </div>

        <div class="space-y-3.5">
            <!-- Karyawan (1 Baris Penuh) -->
            <div class="space-y-1.5">
                <label for="edit_nik" class="block text-xs font-bold text-slate-700">
                    Karyawan <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select name="nik" id="edit_nik" required
                            class="w-full px-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all font-semibold cursor-pointer select2Nik"
                            style="--tw-ring-color: {{ $themeColor1 }}; width: 100%;">
                        @foreach ($karyawans as $karyawan)
                            <option value="{{ $karyawan->nik }}" 
                                    data-kode_cabang="{{ $karyawan->kode_cabang }}"
                                    data-kode_dept="{{ $karyawan->kode_dept }}" 
                                    data-kode_jabatan="{{ $karyawan->kode_jabatan }}"
                                    @selected(old('nik', $kontrak->nik) == $karyawan->nik)>
                                {{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="error-feedback text-[11px] text-rose-500 font-semibold hidden mt-1"></div>
            </div>

            <!-- No Kontrak & No Dokumen (50:50) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-600">No. Kontrak</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-file-certificate text-sm"></i>
                        </span>
                        <input type="text" disabled value="{{ $kontrak->no_kontrak }}"
                               class="w-full h-10 pl-10 pr-3.5 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-700 font-mono font-bold">
                        <input type="hidden" name="no_kontrak" value="{{ old('no_kontrak', $kontrak->no_kontrak) }}">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="edit_no_dokumen" class="block text-xs font-bold text-slate-600">No. Dokumen Fisik (Opsional)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-hash text-sm"></i>
                        </span>
                        <input type="text" name="no_dokumen" id="edit_no_dokumen" value="{{ old('no_dokumen', $kontrak->no_dokumen) }}"
                               placeholder="Contoh: 001/HRD/PKWT/2026"
                               class="w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all font-semibold">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 2: PERIODE & PENEMPATAN KERJA -->
    <div class="space-y-4">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-calendar-event text-xs"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-800">2. Masa Kontrak & Penempatan Kerja</h4>
                <p class="text-[11px] text-slate-400">Atur periode masa berlaku kontrak dan penempatan organisasi</p>
            </div>
        </div>

        <div class="space-y-3.5">
            <!-- Tanggal Kontrak & Jenis Status (50:50) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="edit_tanggal" class="block text-xs font-bold text-slate-700">
                        Tanggal Kontrak <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                            <i class="ti ti-calendar text-sm"></i>
                        </span>
                        <input type="text" name="tanggal" id="edit_tanggal" required value="{{ old('tanggal', $kontrak->tanggal) }}"
                               placeholder="Pilih Tanggal..."
                               class="flatpickr-date w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all font-semibold cursor-pointer">
                    </div>
                    <div class="error-feedback text-[11px] text-rose-500 font-semibold hidden mt-1"></div>
                </div>

                <div class="space-y-1.5">
                    <label for="edit_jenis_kontrak" class="block text-xs font-bold text-slate-700">
                        Jenis Status <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                            <i class="ti ti-badge text-sm"></i>
                        </span>
                        <select name="jenis_kontrak" id="edit_jenis_kontrak" required
                            class="w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all font-semibold cursor-pointer">
                            <option value="K" @selected(old('jenis_kontrak', $kontrak->jenis_kontrak) == 'K')>PKWT (Kontrak)</option>
                            <option value="T" @selected(old('jenis_kontrak', $kontrak->jenis_kontrak) == 'T')>PKWTT (Tetap)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tanggal Mulai & Tanggal Selesai (50:50) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5 edit_periode_field {{ $kontrak->jenis_kontrak === 'T' ? 'opacity-40 pointer-events-none' : '' }}">
                    <label for="edit_dari" class="block text-xs font-bold text-slate-700">
                        Mulai Berlaku <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                            <i class="ti ti-calendar-plus text-sm"></i>
                        </span>
                        <input type="text" name="dari" id="edit_dari" value="{{ old('dari', $kontrak->dari) }}"
                               placeholder="Tanggal Mulai..."
                               class="flatpickr-date w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all font-semibold cursor-pointer"
                               {{ $kontrak->jenis_kontrak === 'T' ? 'disabled' : '' }}>
                    </div>
                    <div class="error-feedback text-[11px] text-rose-500 font-semibold hidden mt-1"></div>
                </div>

                <div class="space-y-1.5 edit_periode_field {{ $kontrak->jenis_kontrak === 'T' ? 'opacity-40 pointer-events-none' : '' }}">
                    <label for="edit_sampai" class="block text-xs font-bold text-slate-700">
                        Selesai Kontrak <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                            <i class="ti ti-calendar-due text-sm"></i>
                        </span>
                        <input type="text" name="sampai" id="edit_sampai" value="{{ old('sampai', $kontrak->sampai) }}"
                               placeholder="Tanggal Selesai..."
                               class="flatpickr-date w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all font-semibold cursor-pointer"
                               {{ $kontrak->jenis_kontrak === 'T' ? 'disabled' : '' }}>
                    </div>
                    <div class="error-feedback text-[11px] text-rose-500 font-semibold hidden mt-1"></div>
                </div>
            </div>

            <!-- Penempatan Cabang & Departemen (50:50) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="edit_kode_cabang" class="block text-xs font-bold text-slate-700">
                        Cabang <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-map-pin text-sm"></i>
                        </span>
                        <select name="kode_cabang" id="edit_kode_cabang" required
                                class="w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all font-semibold cursor-pointer">
                            <option value="">Pilih Cabang</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->kode_cabang }}" @selected(old('kode_cabang', $kontrak->kode_cabang) == $cabang->kode_cabang)>
                                    {{ $cabang->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="error-feedback text-[11px] text-rose-500 font-semibold hidden mt-1"></div>
                </div>

                <div class="space-y-1.5">
                    <label for="edit_kode_dept" class="block text-xs font-bold text-slate-700">
                        Departemen <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-building text-sm"></i>
                        </span>
                        <select name="kode_dept" id="edit_kode_dept" required
                                class="w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all font-semibold cursor-pointer">
                            <option value="">Pilih Departemen</option>
                            @foreach ($departemens as $dept)
                                <option value="{{ $dept->kode_dept }}" @selected(old('kode_dept', $kontrak->kode_dept) == $dept->kode_dept)>
                                    {{ $dept->nama_dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="error-feedback text-[11px] text-rose-500 font-semibold hidden mt-1"></div>
                </div>
            </div>

            <!-- Penempatan Jabatan (1 Baris Penuh) -->
            <div class="space-y-1.5">
                <label for="edit_kode_jabatan" class="block text-xs font-bold text-slate-700">
                    Jabatan <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-briefcase text-sm"></i>
                    </span>
                    <select name="kode_jabatan" id="edit_kode_jabatan" required
                            class="w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all font-semibold cursor-pointer">
                        <option value="">Pilih Jabatan</option>
                        @foreach ($jabatans as $jabatan)
                            <option value="{{ $jabatan->kode_jabatan }}" @selected(old('kode_jabatan', $kontrak->kode_jabatan) == $jabatan->kode_jabatan)>
                                {{ $jabatan->nama_jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                    <div class="error-feedback text-[11px] text-rose-500 font-semibold hidden mt-1"></div>
            </div>
        </div>
    </div>

    <!-- SECTION 3: KOMPENSASI GAJI & TUNJANGAN -->
    <div class="space-y-4">
        <div class="flex items-center gap-2.5 pb-2 border-b border-slate-100">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs font-bold shrink-0"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-coin text-xs"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-slate-800">3. Paket Kompensasi & Gaji</h4>
                <p class="text-[11px] text-slate-400">Perbarui gaji pokok dan nominal rincian tunjangan</p>
            </div>
        </div>

        <input type="hidden" name="status_kontrak" value="{{ old('status_kontrak', $kontrak->status_kontrak) }}">
        <input type="hidden" name="kode_gaji" id="edit_kode_gaji" value="{{ old('kode_gaji', $kontrak->kode_gaji) }}">
        <input type="hidden" name="kode_tunjangan" id="edit_kode_tunjangan" value="{{ old('kode_tunjangan', $kontrak->kode_tunjangan) }}">

        <div class="space-y-3.5">
            <!-- Gaji Pokok (1 Baris Penuh) -->
            <div class="space-y-1.5">
                <label for="edit_nominal_gaji" class="block text-xs font-bold text-slate-700">
                    Gaji Pokok (Rp)
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center font-bold text-xs">
                        Rp
                    </span>
                    <input type="text" name="nominal_gaji" id="edit_nominal_gaji" 
                           value="{{ old('nominal_gaji', $kontrak->nominal_gaji ? number_format($kontrak->nominal_gaji, 0, ',', '.') : '') }}"
                           placeholder="0"
                           class="money-input w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 font-bold text-right focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
                </div>
            </div>

            <!-- Rincian Tunjangan (1 Baris Penuh) -->
            @if ($jenisTunjangans->count())
                <div class="space-y-3.5">
                    @foreach ($jenisTunjangans as $index => $jenis)
                        @php
                            $val = $tunjanganValues[$jenis->kode_jenis_tunjangan] ?? null;
                            $displayVal = $val ? number_format($val, 0, ',', '.') : '';
                        @endphp
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700 truncate" title="{{ $jenis->jenis_tunjangan }}">
                                {{ $jenis->jenis_tunjangan }} (Rp)
                            </label>
                            <input type="hidden" name="kode_jenis_tunjangan[]" value="{{ $jenis->kode_jenis_tunjangan }}">
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center font-bold text-xs">
                                    Rp
                                </span>
                                <input type="text" name="nominal_tunjangan_detail[]" 
                                       value="{{ old('nominal_tunjangan_detail.' . $index, $displayVal) }}"
                                       placeholder="0"
                                       class="money-input w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 font-bold text-right focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- SECTION 4: LAMPIRAN DOKUMEN -->
    <div class="space-y-2 pt-1 border-t border-slate-100">
        <label for="edit_dokumen" class="block text-xs font-bold text-slate-700">
            Upload Berkas Kontrak Baru (PDF, Opsional)
        </label>
        <input type="file" name="dokumen" id="edit_dokumen" accept=".pdf"
               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-600 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-slate-700 hover:file:bg-slate-300 transition cursor-pointer">
        @if ($kontrak->dokumen)
            <div class="flex items-center gap-1.5 text-xs text-slate-600 mt-1 p-2 rounded-xl bg-white border border-slate-300">
                <i class="ti ti-file-text text-sky-600 text-sm"></i>
                <span>Berkas saat ini:</span>
                <a href="{{ asset('storage/uploads/kontrak/' . $kontrak->dokumen) }}" target="_blank" class="text-sky-600 font-bold hover:underline truncate">
                    {{ $kontrak->dokumen }}
                </a>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
        <button type="button" class="btnCloseModalKontrak px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit" id="btnUpdateKontrakV2"
                class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 text-white font-bold rounded-xl text-xs shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-xs"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<style>
    /* Mengatasi lebar flatpickr wrapper yang menyusut akibat mode static */
    .flatpickr-wrapper {
        display: block !important;
        width: 100% !important;
    }
</style>

<script>
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2Nik').select2({
            placeholder: '-- Pilih Karyawan --',
            allowClear: true,
            dropdownParent: $('#modalKontrakV2')
        }).on('change', function() {
            $(this).removeClass('border-rose-500');
            $(this).next('.select2-container').find('.select2-selection').removeClass('border-rose-500 ring-2 ring-rose-500/20');
            $(this).closest('.space-y-1.5').find('.error-feedback').addClass('hidden').text('');
        });
    }

    $('input, select').on('input change', function() {
        $(this).removeClass('border-rose-500');
        $(this).closest('.space-y-1.5').find('.error-feedback').addClass('hidden').text('');
    });

    // Inisialisasi Flatpickr Datepicker pada semua input tanggal
    if (typeof flatpickr !== 'undefined') {
        $('.flatpickr-date').flatpickr({
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            altInputClass: "w-full h-10 pl-10 pr-3.5 bg-white border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-emerald-500 focus:ring-emerald-500/20 transition-all font-semibold cursor-pointer",
            allowInput: true,
            static: true,
            locale: (typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default'
        });
    }

    // Format Rupiah on typing
    $('.money-input').on('keyup input', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        if (val) {
            $(this).val(new Intl.NumberFormat('id-ID').format(val));
        } else {
            $(this).val('');
        }
    });

    // Toggle periode input according to contract type
    $('#edit_jenis_kontrak').on('change', function() {
        if ($(this).val() === 'T') {
            $('.edit_periode_field').addClass('opacity-40 pointer-events-none');
            $('#edit_dari, #edit_sampai').prop('disabled', true);
        } else {
            $('.edit_periode_field').removeClass('opacity-40 pointer-events-none');
            $('#edit_dari, #edit_sampai').prop('disabled', false);
        }
    });

    // Auto-fill placement on employee change
    $('#edit_nik').on('change', function() {
        const selected = $(this).find(':selected');
        const cabang = selected.data('kode_cabang');
        const dept = selected.data('kode_dept');
        const jabatan = selected.data('kode_jabatan');

        if (cabang) $('#edit_kode_cabang').val(cabang).trigger('change');
        if (dept) $('#edit_kode_dept').val(dept).trigger('change');
        if (jabatan) $('#edit_kode_jabatan').val(jabatan).trigger('change');
    });

    // Form Submission Validation
    $('#formEditKontrakV2').on('submit', function(e) {
        let isValid = true;
        $('.error-feedback').addClass('hidden').text('');
        $('input, select').removeClass('border-rose-500');

        const nik = $('#edit_nik').val();
        if (!nik) {
            $('#edit_nik').addClass('border-rose-500');
            $('#edit_nik').next('.select2-container').find('.select2-selection').addClass('border-rose-500 ring-2 ring-rose-500/20');
            $('#edit_nik').closest('.space-y-1.5').find('.error-feedback').removeClass('hidden').text('Silakan pilih karyawan.');
            isValid = false;
        }

        const tgl = $('#edit_tanggal').val();
        if (!tgl) {
            $('#edit_tanggal').closest('.space-y-1.5').find('input.flatpickr-input, input:visible').addClass('border-rose-500');
            $('#edit_tanggal').closest('.space-y-1.5').find('.error-feedback').removeClass('hidden').text('Tanggal kontrak wajib diisi.');
            isValid = false;
        }

        if ($('#edit_jenis_kontrak').val() === 'K') {
            const dari = $('#edit_dari').val();
            const sampai = $('#edit_sampai').val();
            if (!dari) {
                $('#edit_dari').closest('.space-y-1.5').find('input.flatpickr-input, input:visible').addClass('border-rose-500');
                $('#edit_dari').closest('.space-y-1.5').find('.error-feedback').removeClass('hidden').text('Tanggal mulai wajib diisi.');
                isValid = false;
            }
            if (!sampai) {
                $('#edit_sampai').closest('.space-y-1.5').find('input.flatpickr-input, input:visible').addClass('border-rose-500');
                $('#edit_sampai').closest('.space-y-1.5').find('.error-feedback').removeClass('hidden').text('Tanggal selesai wajib diisi.');
                isValid = false;
            } else if (dari && sampai < dari) {
                $('#edit_sampai').closest('.space-y-1.5').find('input.flatpickr-input, input:visible').addClass('border-rose-500');
                $('#edit_sampai').closest('.space-y-1.5').find('.error-feedback').removeClass('hidden').text('Tanggal selesai tidak valid.');
                isValid = false;
            }
        }

        const cabang = $('#edit_kode_cabang').val();
        if (!cabang) {
            $('#edit_kode_cabang').addClass('border-rose-500');
            $('#edit_kode_cabang').closest('.space-y-1.5').find('.error-feedback').removeClass('hidden').text('Cabang wajib dipilih.');
            isValid = false;
        }

        const dept = $('#edit_kode_dept').val();
        if (!dept) {
            $('#edit_kode_dept').addClass('border-rose-500');
            $('#edit_kode_dept').closest('.space-y-1.5').find('.error-feedback').removeClass('hidden').text('Departemen wajib dipilih.');
            isValid = false;
        }

        const jabatan = $('#edit_kode_jabatan').val();
        if (!jabatan) {
            $('#edit_kode_jabatan').addClass('border-rose-500');
            $('#edit_kode_jabatan').closest('.space-y-1.5').find('.error-feedback').removeClass('hidden').text('Jabatan wajib dipilih.');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }

        const btn = $('#btnUpdateKontrakV2');
        btn.prop('disabled', true).html(`
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Menyimpan Perubahan...
        `);
    });
});
</script>
