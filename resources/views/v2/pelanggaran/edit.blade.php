@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';

    $keteranganList = is_string($pelanggaran->keterangan) 
        ? json_decode($pelanggaran->keterangan, true) 
        : ($pelanggaran->keterangan ?? []);
    if (empty($keteranganList)) {
        $keteranganList = !empty($pelanggaran->keterangan) ? [$pelanggaran->keterangan] : [''];
    }
    if (!is_array($keteranganList)) {
        $keteranganList = [$keteranganList];
    }

    $pasalList = is_string($pelanggaran->pasal_pelanggaran) 
        ? json_decode($pelanggaran->pasal_pelanggaran, true) 
        : ($pelanggaran->pasal_pelanggaran ?? []);
    if (empty($pasalList)) {
        $pasalList = [''];
    }
    if (!is_array($pasalList)) {
        $pasalList = [$pasalList];
    }

    $uuList = is_string($pelanggaran->dasar_uu) 
        ? json_decode($pelanggaran->dasar_uu, true) 
        : ($pelanggaran->dasar_uu ?? []);
    if (empty($uuList)) {
        $uuList = [''];
    }
    if (!is_array($uuList)) {
        $uuList = [$uuList];
    }
@endphp

<form action="{{ route('pelanggaran.update', Crypt::encrypt($pelanggaran->no_sp)) }}" method="POST" id="formEditPelanggaranV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <div class="space-y-4">
        
        <!-- Banner Info Ketentuan Pelanggaran -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-edit"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Perbarui Surat Peringatan & Sanksi</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Ubah data sanksi kedisiplinan, masa berlaku, nomor dokumen, maupun butir tindakan pelanggaran yang telah tercatat.
                </p>
            </div>
        </div>

        <!-- 1. Pilih Karyawan -->
        <div class="space-y-1.5">
            <label for="modal_nik_pelanggaran_edit" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="nik" id="modal_nik_pelanggaran_edit" class="w-full px-3 py-2.5 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer select2PelanggaranModal" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawans as $karyawan)
                        <option value="{{ $karyawan->nik }}" {{ old('nik', $pelanggaran->nik) == $karyawan->nik ? 'selected' : '' }}>
                            {{ $karyawan->nik_show ?? $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_nik_pelanggaran_edit"></div>
        </div>

        <!-- 2. Grid Jenis SP & Tanggal Surat -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <!-- Jenis SP -->
            <div class="space-y-1.5">
                <label for="modal_jenis_sp_edit" class="block text-xs font-bold text-slate-700">
                    Jenis Sanksi / SP <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <select name="jenis_sp" id="modal_jenis_sp_edit" 
                            class="w-full h-10 px-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer" 
                            required>
                        <option value="">-- Pilih Jenis SP --</option>
                        <option value="ST" {{ old('jenis_sp', $pelanggaran->jenis_sp) == 'ST' ? 'selected' : '' }}>Surat Teguran (ST)</option>
                        <option value="SP1" {{ old('jenis_sp', $pelanggaran->jenis_sp) == 'SP1' ? 'selected' : '' }}>Surat Peringatan 1 (SP1)</option>
                        <option value="SP2" {{ old('jenis_sp', $pelanggaran->jenis_sp) == 'SP2' ? 'selected' : '' }}>Surat Peringatan 2 (SP2)</option>
                        <option value="SP3" {{ old('jenis_sp', $pelanggaran->jenis_sp) == 'SP3' ? 'selected' : '' }}>Surat Peringatan 3 (SP3)</option>
                    </select>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_jenis_sp_edit"></div>
            </div>

            <!-- Tanggal Surat -->
            <div class="space-y-1.5">
                <label for="modal_tanggal_sp_edit" class="block text-xs font-bold text-slate-700">
                    Tanggal Surat <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="modal_tanggal_sp_edit" 
                           name="tanggal" 
                           value="{{ old('tanggal', is_string($pelanggaran->tanggal) ? $pelanggaran->tanggal : $pelanggaran->tanggal->format('Y-m-d')) }}" 
                           placeholder="Pilih Tanggal Surat"
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer modal-flatpickr-date" 
                           required>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_tanggal_sp_edit"></div>
            </div>
        </div>

        <!-- 3. Grid Masa Berlaku (Dari & Sampai) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <!-- Dari -->
            <div class="space-y-1.5">
                <label for="modal_dari_sp_edit" class="block text-xs font-bold text-slate-700">
                    Masa Berlaku Dari <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar-event text-base"></i>
                    </span>
                    <input type="text" 
                           id="modal_dari_sp_edit" 
                           name="dari" 
                           value="{{ old('dari', is_string($pelanggaran->dari) ? $pelanggaran->dari : $pelanggaran->dari->format('Y-m-d')) }}" 
                           placeholder="Pilih Tanggal Mulai"
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer modal-flatpickr-date" 
                           required>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_dari_sp_edit"></div>
            </div>

            <!-- Sampai -->
            <div class="space-y-1.5">
                <label for="modal_sampai_sp_edit" class="block text-xs font-bold text-slate-700">
                    Masa Berlaku Sampai <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar-event text-base"></i>
                    </span>
                    <input type="text" 
                           id="modal_sampai_sp_edit" 
                           name="sampai" 
                           value="{{ old('sampai', is_string($pelanggaran->sampai) ? $pelanggaran->sampai : $pelanggaran->sampai->format('Y-m-d')) }}" 
                           placeholder="Pilih Tanggal Berakhir"
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer modal-flatpickr-date" 
                           required>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_sampai_sp_edit"></div>
            </div>
        </div>

        <!-- 4. Grid No Dokumen & No Kontrak -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <!-- No Dokumen -->
            <div class="space-y-1.5">
                <label for="modal_no_dokumen_edit" class="block text-xs font-bold text-slate-700">
                    Nomor Dokumen SP <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-file-text text-base"></i>
                    </span>
                    <input type="text" 
                           id="modal_no_dokumen_edit" 
                           name="no_dokumen" 
                           value="{{ old('no_dokumen', $pelanggaran->no_dokumen) }}" 
                           placeholder="Contoh: 001/HRD-SP/X/2026"
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
            </div>

            <!-- No Kontrak -->
            <div class="space-y-1.5">
                <label for="modal_no_kontrak_edit" class="block text-xs font-bold text-slate-700">
                    No. Perjanjian / Kontrak Kerja <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-file-certificate text-base"></i>
                    </span>
                    <input type="text" 
                           id="modal_no_kontrak_edit" 
                           name="no_kontrak" 
                           value="{{ old('no_kontrak', $pelanggaran->no_kontrak) }}" 
                           placeholder="Contoh: PKWT/042/HRD/2025"
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
            </div>
        </div>

        <!-- ================= REPEATER 1: KETERANGAN / TINDAKAN PELANGGARAN ================= -->
        <div class="bg-slate-50/70 border border-slate-200/90 rounded-2xl p-3.5 sm:p-4 space-y-3">
            <div class="flex items-center justify-between gap-2 border-b border-slate-200/70 pb-2.5">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-xs">
                        <i class="ti ti-alert-triangle"></i>
                    </div>
                    <span class="text-xs font-black text-slate-800">
                        Keterangan / Butir Tindakan Pelanggaran <span class="text-rose-500">*</span>
                    </span>
                </div>
                <button type="button" id="btn-tambah-keterangan-edit" 
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white hover:bg-slate-100 text-emerald-700 font-bold rounded-lg text-xs border border-slate-200 shadow-2xs transition active:scale-95 cursor-pointer">
                    <i class="ti ti-plus text-xs"></i>
                    <span>Tambah Butir</span>
                </button>
            </div>

            <div class="space-y-2.5" id="container-keterangan-edit">
                @foreach ($keteranganList as $index => $k)
                    <div class="flex items-start gap-2 keterangan-item">
                        <div class="flex-1">
                            <textarea name="keterangan[]" rows="2" 
                                      class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition custom-scrollbar input-keterangan-edit" 
                                      placeholder="Contoh: Meninggalkan pos penjagaan tanpa izin..." required>{{ $k }}</textarea>
                        </div>
                        <button type="button" class="btn-hapus-keterangan p-2.5 bg-white text-slate-400 hover:text-rose-600 rounded-xl border border-slate-200 shadow-2xs transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer shrink-0" {{ count($keteranganList) <= 1 ? 'disabled' : '' }}>
                            <i class="ti ti-trash text-sm"></i>
                        </button>
                    </div>
                @endforeach
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_keterangan_edit"></div>
        </div>

        <!-- ================= REPEATER 2: PASAL PELANGGARAN ================= -->
        <div class="bg-slate-50/70 border border-slate-200/90 rounded-2xl p-3.5 sm:p-4 space-y-3">
            <div class="flex items-center justify-between gap-2 border-b border-slate-200/70 pb-2.5">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">
                        <i class="ti ti-gavel"></i>
                    </div>
                    <span class="text-xs font-black text-slate-800">
                        A. Poin / Pasal Pelanggaran (Peraturan Perusahaan)
                    </span>
                </div>
                <button type="button" id="btn-tambah-pasal-edit" 
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white hover:bg-slate-100 text-amber-700 font-bold rounded-lg text-xs border border-slate-200 shadow-2xs transition active:scale-95 cursor-pointer">
                    <i class="ti ti-plus text-xs"></i>
                    <span>Tambah Pasal</span>
                </button>
            </div>

            <div class="space-y-2.5" id="container-pasal-edit">
                @foreach ($pasalList as $index => $p)
                    @php
                        $pasalVal = is_array($p) ? ($p['pasal'] ?? '') : $p;
                    @endphp
                    <div class="flex items-start gap-2 pasal-item">
                        <div class="flex-1">
                            <textarea name="pasal[]" rows="2" 
                                      class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition custom-scrollbar" 
                                      placeholder="Contoh: Pasal 6 Poin 7h: Dengan ceroboh merusak barang milik perusahaan...">{{ $pasalVal }}</textarea>
                        </div>
                        <button type="button" class="btn-hapus-pasal p-2.5 bg-white text-slate-400 hover:text-rose-600 rounded-xl border border-slate-200 shadow-2xs transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer shrink-0" {{ count($pasalList) <= 1 ? 'disabled' : '' }}>
                            <i class="ti ti-trash text-sm"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ================= REPEATER 3: DASAR PERUNDANG-UNDANGAN ================= -->
        <div class="bg-slate-50/70 border border-slate-200/90 rounded-2xl p-3.5 sm:p-4 space-y-3">
            <div class="flex items-center justify-between gap-2 border-b border-slate-200/70 pb-2.5">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                        <i class="ti ti-scale"></i>
                    </div>
                    <span class="text-xs font-black text-slate-800">
                        B. Dasar Peraturan Perundang-undangan (Opsional)
                    </span>
                </div>
                <button type="button" id="btn-tambah-uu-edit" 
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white hover:bg-slate-100 text-indigo-700 font-bold rounded-lg text-xs border border-slate-200 shadow-2xs transition active:scale-95 cursor-pointer">
                    <i class="ti ti-plus text-xs"></i>
                    <span>Tambah UU</span>
                </button>
            </div>

            <div class="space-y-2.5" id="container-uu-edit">
                @foreach ($uuList as $index => $uu)
                    <div class="flex items-start gap-2 uu-item">
                        <div class="flex-1">
                            <textarea name="dasar_uu[]" rows="2" 
                                      class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition custom-scrollbar" 
                                      placeholder="Contoh: Undang-Undang Nomor 13 Tahun 2003 tentang Ketenagakerjaan...">{{ $uu }}</textarea>
                        </div>
                        <button type="button" class="btn-hapus-uu p-2.5 bg-white text-slate-400 hover:text-rose-600 rounded-xl border border-slate-200 shadow-2xs transition active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer shrink-0" {{ count($uuList) <= 1 ? 'disabled' : '' }}>
                            <i class="ti ti-trash text-sm"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                onclick="$('#modalPelanggaran').addClass('hidden'); $('body').removeClass('overflow-hidden');">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitEditPelanggaranV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formEditPelanggaranV2');
        const nikSelect = document.getElementById('modal_nik_pelanggaran_edit');
        const jenisSpSelect = document.getElementById('modal_jenis_sp_edit');
        const tanggalInput = document.getElementById('modal_tanggal_sp_edit');
        const dariInput = document.getElementById('modal_dari_sp_edit');
        const sampaiInput = document.getElementById('modal_sampai_sp_edit');

        // Initialize Select2 if available
        if ($.fn.select2) {
            $('.select2PelanggaranModal').select2({
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalPelanggaran')
            });
        }

        // Initialize Flatpickr if available
        if (typeof flatpickr !== 'undefined') {
            $('.modal-flatpickr-date').flatpickr({
                dateFormat: 'Y-m-d',
                allowInput: false
            });
        }

        // Form Validation & Submit Handler
        $(form).on('submit', function(e) {
            const nik = $(nikSelect).val();
            const jenis_sp = $(jenisSpSelect).val();
            const tanggal = $(tanggalInput).val();
            const dari = $(dariInput).val();
            const sampai = $(sampaiInput).val();
            let hasError = false;

            // Reset error feedback states
            $('.error-feedback').addClass('hidden').text('');
            $(nikSelect).removeClass('border-rose-500');
            $(jenisSpSelect).removeClass('border-rose-500');
            $(tanggalInput).removeClass('border-rose-500');
            $(dariInput).removeClass('border-rose-500');
            $(sampaiInput).removeClass('border-rose-500');
            $('.input-keterangan-edit').removeClass('border-rose-500');

            if (!nik) {
                $('#err_modal_nik_pelanggaran_edit').text('Karyawan wajib dipilih').removeClass('hidden');
                hasError = true;
            }

            if (!jenis_sp) {
                $('#err_modal_jenis_sp_edit').text('Jenis sanksi / SP wajib dipilih').removeClass('hidden');
                $(jenisSpSelect).addClass('border-rose-500');
                hasError = true;
            }

            if (!tanggal) {
                $('#err_modal_tanggal_sp_edit').text('Tanggal surat wajib diisi').removeClass('hidden');
                $(tanggalInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!dari) {
                $('#err_modal_dari_sp_edit').text('Tanggal mulai masa berlaku wajib diisi').removeClass('hidden');
                $(dariInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!sampai) {
                $('#err_modal_sampai_sp_edit').text('Tanggal berakhir masa berlaku wajib diisi').removeClass('hidden');
                $(sampaiInput).addClass('border-rose-500');
                hasError = true;
            } else if (dari && sampai < dari) {
                $('#err_modal_sampai_sp_edit').text('Tanggal akhir tidak boleh lebih awal dari tanggal mulai').removeClass('hidden');
                $(sampaiInput).addClass('border-rose-500');
                hasError = true;
            }

            const firstKet = $('.input-keterangan-edit').first().val();
            if (!firstKet || firstKet.trim() === '') {
                $('#err_modal_keterangan_edit').text('Minimal 1 butir tindakan / keterangan pelanggaran wajib diisi').removeClass('hidden');
                $('.input-keterangan-edit').first().addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            // Button spinner loading
            const $btn = $('#btnSubmitEditPelanggaranV2');
            $btn.prop('disabled', true).html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Menyimpan...</span>
            `);
        });

        // Repeater Keterangan
        $('#btn-tambah-keterangan-edit').click(function(e) {
            e.preventDefault();
            const item = `
                <div class="flex items-start gap-2 keterangan-item">
                    <div class="flex-1">
                        <textarea name="keterangan[]" rows="2" 
                                  class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition custom-scrollbar input-keterangan-edit" 
                                  placeholder="Masukkan butir keterangan pelanggaran..." required></textarea>
                    </div>
                    <button type="button" class="btn-hapus-keterangan p-2.5 bg-white text-slate-400 hover:text-rose-600 rounded-xl border border-slate-200 shadow-2xs transition active:scale-95 cursor-pointer shrink-0">
                        <i class="ti ti-trash text-sm"></i>
                    </button>
                </div>
            `;
            $('#container-keterangan-edit').append(item);
            toggleBtnState('#container-keterangan-edit .keterangan-item', '#container-keterangan-edit .btn-hapus-keterangan');
        });

        $(document).on('click', '#container-keterangan-edit .btn-hapus-keterangan', function(e) {
            e.preventDefault();
            $(this).closest('.keterangan-item').remove();
            toggleBtnState('#container-keterangan-edit .keterangan-item', '#container-keterangan-edit .btn-hapus-keterangan');
        });

        // Repeater Pasal
        $('#btn-tambah-pasal-edit').click(function(e) {
            e.preventDefault();
            const item = `
                <div class="flex items-start gap-2 pasal-item">
                    <div class="flex-1">
                        <textarea name="pasal[]" rows="2" 
                                  class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition custom-scrollbar" 
                                  placeholder="Contoh: Pasal / Ketentuan yang dilanggar..."></textarea>
                    </div>
                    <button type="button" class="btn-hapus-pasal p-2.5 bg-white text-slate-400 hover:text-rose-600 rounded-xl border border-slate-200 shadow-2xs transition active:scale-95 cursor-pointer shrink-0">
                        <i class="ti ti-trash text-sm"></i>
                    </button>
                </div>
            `;
            $('#container-pasal-edit').append(item);
            toggleBtnState('#container-pasal-edit .pasal-item', '#container-pasal-edit .btn-hapus-pasal');
        });

        $(document).on('click', '#container-pasal-edit .btn-hapus-pasal', function(e) {
            e.preventDefault();
            $(this).closest('.pasal-item').remove();
            toggleBtnState('#container-pasal-edit .pasal-item', '#container-pasal-edit .btn-hapus-pasal');
        });

        // Repeater Dasar UU
        $('#btn-tambah-uu-edit').click(function(e) {
            e.preventDefault();
            const item = `
                <div class="flex items-start gap-2 uu-item">
                    <div class="flex-1">
                        <textarea name="dasar_uu[]" rows="2" 
                                  class="w-full px-3 py-2 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 placeholder-slate-400 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition custom-scrollbar" 
                                  placeholder="Contoh: Peraturan perundang-undangan terkait..."></textarea>
                    </div>
                    <button type="button" class="btn-hapus-uu p-2.5 bg-white text-slate-400 hover:text-rose-600 rounded-xl border border-slate-200 shadow-2xs transition active:scale-95 cursor-pointer shrink-0">
                        <i class="ti ti-trash text-sm"></i>
                    </button>
                </div>
            `;
            $('#container-uu-edit').append(item);
            toggleBtnState('#container-uu-edit .uu-item', '#container-uu-edit .btn-hapus-uu');
        });

        $(document).on('click', '#container-uu-edit .btn-hapus-uu', function(e) {
            e.preventDefault();
            $(this).closest('.uu-item').remove();
            toggleBtnState('#container-uu-edit .uu-item', '#container-uu-edit .btn-hapus-uu');
        });

        function toggleBtnState(itemSelector, btnSelector) {
            const count = $(itemSelector).length;
            if (count <= 1) {
                $(btnSelector).prop('disabled', true);
            } else {
                $(btnSelector).prop('disabled', false);
            }
        }
    })();
</script>
