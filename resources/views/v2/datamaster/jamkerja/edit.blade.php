<form action="{{ route('jamkerja.update', Crypt::encrypt($jamkerja->kode_jam_kerja)) }}" method="POST" id="formJamKerjaEditV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <div class="space-y-4">
        <!-- Banner Info -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-edit"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Edit Data Jam Kerja</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Sesuaikan nama, jadwal jam masuk/pulang, istirahat, dan batas lintas hari jam kerja.
                </p>
            </div>
        </div>

        <!-- Row 1: Kode Jam Kerja & Nama Jam Kerja -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            <div class="sm:col-span-4 space-y-1.5">
                <label for="kode_jam_kerja_edit" class="block text-xs font-bold text-slate-700">Kode Jam Kerja</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-barcode text-base"></i>
                    </span>
                    <input type="text" name="kode_jam_kerja" id="kode_jam_kerja_edit" value="{{ $jamkerja->kode_jam_kerja }}" readonly
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500 bg-slate-100 border border-slate-300 rounded-xl cursor-not-allowed uppercase" />
                </div>
            </div>

            <div class="sm:col-span-8 space-y-1.5">
                <label for="nama_jam_kerja_edit" class="block text-xs font-bold text-slate-700">
                    Nama Jam Kerja <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-clock text-base"></i>
                    </span>
                    <input type="text" name="nama_jam_kerja" id="nama_jam_kerja_edit" maxlength="50" value="{{ $jamkerja->nama_jam_kerja }}" autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" />
                </div>
                <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                    <span>Maksimal 50 karakter</span>
                    <span id="namaJamKerjaCountEdit">{{ strlen($jamkerja->nama_jam_kerja) }}/50</span>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nama_jam_kerja_edit"></div>
            </div>
        </div>

        <!-- Row 2: Jam Masuk & Jam Pulang -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="space-y-1.5">
                <label for="jam_masuk_edit" class="block text-xs font-bold text-slate-700">
                    Jam Masuk (HH:mm) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-600 pointer-events-none flex items-center">
                        <i class="ti ti-clock-play text-base"></i>
                    </span>
                    <input type="text" name="jam_masuk" id="jam_masuk_edit" value="{{ $jamkerja->jam_masuk }}" autocomplete="off"
                           class="time-mask w-full h-10 pl-10 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>
                <p class="text-[10.5px] text-slate-400">Format waktu 24 jam</p>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_masuk_edit"></div>
            </div>

            <div class="space-y-1.5">
                <label for="jam_pulang_edit" class="block text-xs font-bold text-slate-700">
                    Jam Pulang (HH:mm) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-rose-600 pointer-events-none flex items-center">
                        <i class="ti ti-clock-stop text-base"></i>
                    </span>
                    <input type="text" name="jam_pulang" id="jam_pulang_edit" value="{{ $jamkerja->jam_pulang }}" autocomplete="off"
                           class="time-mask w-full h-10 pl-10 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>
                <p class="text-[10.5px] text-slate-400">Format waktu 24 jam</p>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_pulang_edit"></div>
            </div>
        </div>

        <!-- Row 3: Istirahat Toggle & Times -->
        <div class="p-3.5 bg-slate-50/80 border border-slate-200/90 rounded-2xl space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-start">
                <div class="sm:col-span-4 space-y-1.5">
                    <label for="istirahat_edit" class="block text-xs font-bold text-slate-700">
                        Istirahat? <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="istirahat" id="istirahat_edit" 
                                class="w-full h-10 pl-3.5 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                            <option value="">Pilih Status</option>
                            <option value="1" {{ $jamkerja->istirahat == 1 ? 'selected' : '' }}>Ya (Ada Istirahat)</option>
                            <option value="0" {{ $jamkerja->istirahat == 0 ? 'selected' : '' }}>Tidak</option>
                        </select>
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-chevron-down text-xs"></i>
                        </span>
                    </div>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_istirahat_edit"></div>
                </div>

                <!-- Jam Awal & Akhir Istirahat (Conditional) -->
                <div id="sectionIstirahatEdit" class="sm:col-span-8 grid grid-cols-2 gap-3" style="{{ $jamkerja->istirahat == 1 ? '' : 'display: none;' }}">
                    <div class="space-y-1.5">
                        <label for="jam_awal_istirahat_edit" class="block text-xs font-bold text-slate-700">Mulai Istirahat <span class="text-rose-500">*</span></label>
                        <input type="text" name="jam_awal_istirahat" id="jam_awal_istirahat_edit" 
                               value="{{ $jamkerja->jam_awal_istirahat ? date('H:i', strtotime($jamkerja->jam_awal_istirahat)) : '' }}"
                               placeholder="12:00" autocomplete="off"
                               class="time-mask w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_awal_istirahat_edit"></div>
                    </div>
                    <div class="space-y-1.5">
                        <label for="jam_akhir_istirahat_edit" class="block text-xs font-bold text-slate-700">Selesai Istirahat <span class="text-rose-500">*</span></label>
                        <input type="text" name="jam_akhir_istirahat" id="jam_akhir_istirahat_edit" 
                               value="{{ $jamkerja->jam_akhir_istirahat ? date('H:i', strtotime($jamkerja->jam_akhir_istirahat)) : '' }}"
                               placeholder="13:00" autocomplete="off"
                               class="time-mask w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_akhir_istirahat_edit"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3B: Istirahat Lembur Toggle & Times -->
        <div class="p-3.5 bg-slate-50/80 border border-slate-200/90 rounded-2xl space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-start">
                <div class="sm:col-span-4 space-y-1.5">
                    <label for="istirahatlembur_edit" class="block text-xs font-bold text-slate-700">
                        Istirahat Lembur? <span class="text-[10.5px] font-normal text-slate-400">(Shift Lembur)</span>
                    </label>
                    <div class="relative">
                        <select name="istirahatlembur" id="istirahatlembur_edit"
                                class="w-full h-10 pl-3.5 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                            <option value="0" {{ ($jamkerja->istirahatlembur ?? 0) == 0 ? 'selected' : '' }}>Tidak</option>
                            <option value="1" {{ ($jamkerja->istirahatlembur ?? 0) == 1 ? 'selected' : '' }}>Ya (Ada Istirahat Lembur)</option>
                        </select>
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-chevron-down text-xs"></i>
                        </span>
                    </div>
                </div>

                <div id="sectionIstirahatLemburEdit" class="sm:col-span-8 grid grid-cols-2 gap-3" style="{{ ($jamkerja->istirahatlembur ?? 0) == 1 ? '' : 'display: none;' }}">
                    <div class="space-y-1.5">
                        <label for="jam_awal_istirahatlembur_edit" class="block text-xs font-bold text-slate-700">Mulai Istirahat Lembur <span class="text-rose-500">*</span></label>
                        <input type="text" name="jam_awal_istirahatlembur" id="jam_awal_istirahatlembur_edit"
                               value="{{ $jamkerja->jam_awal_istirahatlembur ? date('H:i', strtotime($jamkerja->jam_awal_istirahatlembur)) : '' }}"
                               placeholder="18:00" autocomplete="off"
                               class="time-mask w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                    </div>
                    <div class="space-y-1.5">
                        <label for="jam_akhir_istirahatlembur_edit" class="block text-xs font-bold text-slate-700">Selesai Istirahat Lembur <span class="text-rose-500">*</span></label>
                        <input type="text" name="jam_akhir_istirahatlembur" id="jam_akhir_istirahatlembur_edit"
                               value="{{ $jamkerja->jam_akhir_istirahatlembur ? date('H:i', strtotime($jamkerja->jam_akhir_istirahatlembur)) : '' }}"
                               placeholder="19:00" autocomplete="off"
                               class="time-mask w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Total Jam & Warna -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="space-y-1.5">
                <label for="total_jam_edit" class="block text-xs font-bold text-slate-700">
                    Total Jam Kerja Efektif <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-hourglass-empty text-base"></i>
                    </span>
                    <input type="number" name="total_jam" id="total_jam_edit" min="1" max="24" value="{{ $jamkerja->total_jam }}"
                           class="w-full h-10 pl-10 pr-12 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 pointer-events-none">
                        Jam
                    </span>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_total_jam_edit"></div>
            </div>

            <div class="space-y-1.5">
                <label for="color_edit" class="block text-xs font-bold text-slate-700">Warna Label Laporan</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="color" id="color_edit" value="{{ $jamkerja->color ?: '#0a5c41' }}"
                           class="w-14 h-10 p-1 bg-white border border-slate-300 rounded-xl cursor-pointer" />
                    <span class="text-xs text-slate-500 font-medium">Warna pembeda di rekap presensi</span>
                </div>
            </div>
        </div>

        <!-- Row 5: Lintas Hari Toggle -->
        <div class="p-3.5 bg-slate-50/80 border border-slate-200/90 rounded-2xl space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-start">
                <div class="sm:col-span-4 space-y-1.5">
                    <label for="lintashari_edit" class="block text-xs font-bold text-slate-700">
                        Lintas Hari (Shift Malam)? <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="lintashari" id="lintashari_edit" 
                                class="w-full h-10 pl-3.5 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                            <option value="">Pilih Status</option>
                            <option value="1" {{ $jamkerja->lintashari == 1 ? 'selected' : '' }}>Ya (Lintas Hari)</option>
                            <option value="0" {{ $jamkerja->lintashari == 0 ? 'selected' : '' }}>Tidak</option>
                        </select>
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-chevron-down text-xs"></i>
                        </span>
                    </div>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_lintashari_edit"></div>
                </div>

                <div id="sectionLintasHariEdit" class="sm:col-span-8 space-y-1.5" style="{{ $jamkerja->lintashari == 1 ? '' : 'display: none;' }}">
                    <label for="batas_presensi_pulang_edit" class="block text-xs font-bold text-slate-700">
                        Batas Jam Pulang Lintas Hari
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-clock-pause text-base"></i>
                        </span>
                        <input type="text" name="batas_presensi_pulang" id="batas_presensi_pulang_edit" 
                               value="{{ $jamkerja->batas_presensi_pulang ? date('H:i', strtotime($jamkerja->batas_presensi_pulang)) : '' }}"
                               placeholder="Contoh: 10:00" autocomplete="off"
                               class="time-mask w-full h-10 pl-10 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                    </div>
                    <p class="text-[10.5px] text-slate-400">Kosongkan jika menggunakan batas default sistem</p>
                </div>
            </div>
        </div>

        <!-- Row 6: Keterangan -->
        <div class="space-y-1.5">
            <label for="keterangan_edit" class="block text-xs font-bold text-slate-700">Keterangan / Catatan</label>
            <textarea name="keterangan" id="keterangan_edit" rows="2" maxlength="255"
                      placeholder="Catatan tambahan jam kerja (opsional)..."
                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition resize-none">{{ $jamkerja->keterangan }}</textarea>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" id="btnSubmitJamKerjaEditV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formJamKerjaEditV2');
        const namaInput = document.getElementById('nama_jam_kerja_edit');
        const namaCount = document.getElementById('namaJamKerjaCountEdit');

        const masukInput = document.getElementById('jam_masuk_edit');
        const pulangInput = document.getElementById('jam_pulang_edit');
        const istirahatSelect = document.getElementById('istirahat_edit');
        const awalIstInput = document.getElementById('jam_awal_istirahat_edit');
        const akhirIstInput = document.getElementById('jam_akhir_istirahat_edit');
        const totalJamInput = document.getElementById('total_jam_edit');
        const lintashariSelect = document.getElementById('lintashari_edit');

        // Masking time HH:mm with fallback
        if ($.isFunction($.fn.mask)) {
            $(".time-mask").mask("00:00");
        } else {
            $(".time-mask").on("input", function() {
                let v = this.value.replace(/[^0-9]/g, '');
                if (v.length > 2) {
                    this.value = v.substring(0, 2) + ':' + v.substring(2, 4);
                } else {
                    this.value = v;
                }
            });
        }

        // Istirahat handler
        $("#istirahat_edit").on("change", function() {
            if ($(this).val() === "1") {
                $("#sectionIstirahatEdit").fadeIn(200);
            } else {
                $("#sectionIstirahatEdit").fadeOut(200);
                $("#jam_awal_istirahat_edit, #jam_akhir_istirahat_edit").val("");
            }
        });

        $("#istirahatlembur_edit").on("change", function() {
            if ($(this).val() === "1") {
                $("#sectionIstirahatLemburEdit").fadeIn(200);
            } else {
                $("#sectionIstirahatLemburEdit").fadeOut(200);
                $("#jam_awal_istirahatlembur_edit, #jam_akhir_istirahatlembur_edit").val("");
            }
        });

        // Lintas Hari handler
        $("#lintashari_edit").on("change", function() {
            if ($(this).val() === "1") {
                $("#sectionLintasHariEdit").fadeIn(200);
            } else {
                $("#sectionLintasHariEdit").fadeOut(200);
                $("#batas_presensi_pulang_edit").val("");
            }
        });

        // Character count for nama
        if (namaInput) {
            namaInput.addEventListener('input', function(e) {
                let val = e.target.value;
                if (val.length > 50) val = val.substring(0, 50);
                e.target.value = val;
                namaCount.textContent = `${val.length}/50`;
            });
        }

        // Form Submit Validation
        $(form).on("submit", function(e) {
            let hasError = false;
            $(".error-feedback").addClass("hidden").text("");
            $(form).find("input, select, textarea").removeClass("border-rose-500");

            const timeRegex = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;

            const nama = namaInput ? namaInput.value.trim() : "";
            const masuk = masukInput ? masukInput.value.trim() : "";
            const pulang = pulangInput ? pulangInput.value.trim() : "";
            const istirahat = istirahatSelect ? istirahatSelect.value : "";
            const awalIst = awalIstInput ? awalIstInput.value.trim() : "";
            const akhirIst = akhirIstInput ? akhirIstInput.value.trim() : "";
            const totalJam = totalJamInput ? (parseInt(totalJamInput.value) || 0) : 0;
            const lintashari = lintashariSelect ? lintashariSelect.value : "";

            if (!nama) {
                hasError = true;
                $(namaInput).addClass("border-rose-500");
                $("#err_nama_jam_kerja_edit").removeClass("hidden").text("Nama Jam Kerja wajib diisi.");
            }

            if (!masuk || !timeRegex.test(masuk)) {
                hasError = true;
                $(masukInput).addClass("border-rose-500");
                $("#err_jam_masuk_edit").removeClass("hidden").text("Format Jam Masuk harus HH:mm (contoh: 08:00).");
            }

            if (!pulang || !timeRegex.test(pulang)) {
                hasError = true;
                $(pulangInput).addClass("border-rose-500");
                $("#err_jam_pulang_edit").removeClass("hidden").text("Format Jam Pulang harus HH:mm (contoh: 17:00).");
            }

            if (istirahat === "") {
                hasError = true;
                $(istirahatSelect).addClass("border-rose-500");
                $("#err_istirahat_edit").removeClass("hidden").text("Status istirahat wajib dipilih.");
            } else if (istirahat === "1") {
                if (!awalIst || !timeRegex.test(awalIst)) {
                    hasError = true;
                    $(awalIstInput).addClass("border-rose-500");
                    $("#err_jam_awal_istirahat_edit").removeClass("hidden").text("Format mulai istirahat harus HH:mm.");
                }
                if (!akhirIst || !timeRegex.test(akhirIst)) {
                    hasError = true;
                    $(akhirIstInput).addClass("border-rose-500");
                    $("#err_jam_akhir_istirahat_edit").removeClass("hidden").text("Format selesai istirahat harus HH:mm.");
                }
            }

            if (totalJam < 1 || totalJam > 24) {
                hasError = true;
                $(totalJamInput).addClass("border-rose-500");
                $("#err_total_jam_edit").removeClass("hidden").text("Total jam kerja efektif harus antara 1 s/d 24 jam.");
            }

            if (lintashari === "") {
                hasError = true;
                $(lintashariSelect).addClass("border-rose-500");
                $("#err_lintashari_edit").removeClass("hidden").text("Status lintas hari wajib dipilih.");
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            $("#btnSubmitJamKerjaEditV2").prop("disabled", true).html(`
                <i class="ti ti-loader animate-spin text-sm"></i>
                <span>Memperbarui...</span>
            `);
        });
    })();
</script>
