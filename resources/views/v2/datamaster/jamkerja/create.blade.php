@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('jamkerja.store') }}" method="POST" id="formJamKerjaV2" class="space-y-4" novalidate>
    @csrf

    <div class="space-y-4">
        <!-- Banner Info -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Ketentuan Master Jam Kerja</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Tentukan kode jam kerja (maksimal 4 karakter alfanumerik kapital), jadwal jam masuk/pulang, durasi istirahat, serta lintas hari jika ada.
                </p>
            </div>
        </div>

        <!-- Row 1: Kode Jam Kerja & Nama Jam Kerja -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            <div class="sm:col-span-4 space-y-1.5">
                <label for="kode_jam_kerja" class="block text-xs font-bold text-slate-700">
                    Kode Jam Kerja <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-barcode text-base"></i>
                    </span>
                    <input type="text" name="kode_jam_kerja" id="kode_jam_kerja" maxlength="4" placeholder="Contoh: JK01" autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition uppercase" />
                </div>
                <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                    <span>Maksimal 4 karakter</span>
                    <span id="kodeJamKerjaCount">0/4</span>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_jam_kerja"></div>
            </div>

            <div class="sm:col-span-8 space-y-1.5">
                <label for="nama_jam_kerja" class="block text-xs font-bold text-slate-700">
                    Nama Jam Kerja <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-clock text-base"></i>
                    </span>
                    <input type="text" name="nama_jam_kerja" id="nama_jam_kerja" maxlength="50" placeholder="Contoh: Reguler Pagi / Shift 1" autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" />
                </div>
                <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                    <span>Maksimal 50 karakter</span>
                    <span id="namaJamKerjaCount">0/50</span>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nama_jam_kerja"></div>
            </div>
        </div>

        <!-- Row 2: Jam Masuk & Jam Pulang -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="space-y-1.5">
                <label for="jam_masuk" class="block text-xs font-bold text-slate-700">
                    Jam Masuk (HH:mm) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-600 pointer-events-none flex items-center">
                        <i class="ti ti-clock-play text-base"></i>
                    </span>
                    <input type="text" name="jam_masuk" id="jam_masuk" placeholder="08:00" autocomplete="off"
                           class="time-mask w-full h-10 pl-10 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>
                <p class="text-[10.5px] text-slate-400">Format waktu 24 jam</p>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_masuk"></div>
            </div>

            <div class="space-y-1.5">
                <label for="jam_pulang" class="block text-xs font-bold text-slate-700">
                    Jam Pulang (HH:mm) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-rose-600 pointer-events-none flex items-center">
                        <i class="ti ti-clock-stop text-base"></i>
                    </span>
                    <input type="text" name="jam_pulang" id="jam_pulang" placeholder="17:00" autocomplete="off"
                           class="time-mask w-full h-10 pl-10 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>
                <p class="text-[10.5px] text-slate-400">Format waktu 24 jam</p>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_pulang"></div>
            </div>
        </div>

        <!-- Row 3: Istirahat Toggle & Times -->
        <div class="p-3.5 bg-slate-50/80 border border-slate-200/90 rounded-2xl space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-start">
                <div class="sm:col-span-4 space-y-1.5">
                    <label for="istirahat" class="block text-xs font-bold text-slate-700">
                        Istirahat? <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="istirahat" id="istirahat" 
                                class="w-full h-10 pl-3.5 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                            <option value="">Pilih Status</option>
                            <option value="1">Ya (Ada Istirahat)</option>
                            <option value="0">Tidak</option>
                        </select>
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-chevron-down text-xs"></i>
                        </span>
                    </div>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_istirahat"></div>
                </div>

                <!-- Jam Awal & Akhir Istirahat (Conditional) -->
                <div id="sectionIstirahat" class="sm:col-span-8 grid grid-cols-2 gap-3" style="display: none;">
                    <div class="space-y-1.5">
                        <label for="jam_awal_istirahat" class="block text-xs font-bold text-slate-700">Mulai Istirahat <span class="text-rose-500">*</span></label>
                        <input type="text" name="jam_awal_istirahat" id="jam_awal_istirahat" placeholder="12:00" autocomplete="off"
                               class="time-mask w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_awal_istirahat"></div>
                    </div>
                    <div class="space-y-1.5">
                        <label for="jam_akhir_istirahat" class="block text-xs font-bold text-slate-700">Selesai Istirahat <span class="text-rose-500">*</span></label>
                        <input type="text" name="jam_akhir_istirahat" id="jam_akhir_istirahat" placeholder="13:00" autocomplete="off"
                               class="time-mask w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_jam_akhir_istirahat"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3B: Istirahat Lembur Toggle & Times -->
        <div class="p-3.5 bg-slate-50/80 border border-slate-200/90 rounded-2xl space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-start">
                <div class="sm:col-span-4 space-y-1.5">
                    <label for="istirahatlembur" class="block text-xs font-bold text-slate-700">
                        Istirahat Lembur? <span class="text-[10.5px] font-normal text-slate-400">(Shift Lembur)</span>
                    </label>
                    <div class="relative">
                        <select name="istirahatlembur" id="istirahatlembur"
                                class="w-full h-10 pl-3.5 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                            <option value="0">Tidak</option>
                            <option value="1">Ya (Ada Istirahat Lembur)</option>
                        </select>
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-chevron-down text-xs"></i>
                        </span>
                    </div>
                </div>

                <div id="sectionIstirahatLembur" class="sm:col-span-8 grid grid-cols-2 gap-3" style="display: none;">
                    <div class="space-y-1.5">
                        <label for="jam_awal_istirahatlembur" class="block text-xs font-bold text-slate-700">Mulai Istirahat Lembur <span class="text-rose-500">*</span></label>
                        <input type="text" name="jam_awal_istirahatlembur" id="jam_awal_istirahatlembur" placeholder="18:00" autocomplete="off"
                               class="time-mask w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                    </div>
                    <div class="space-y-1.5">
                        <label for="jam_akhir_istirahatlembur" class="block text-xs font-bold text-slate-700">Selesai Istirahat Lembur <span class="text-rose-500">*</span></label>
                        <input type="text" name="jam_akhir_istirahatlembur" id="jam_akhir_istirahatlembur" placeholder="19:00" autocomplete="off"
                               class="time-mask w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Total Jam & Warna -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <div class="space-y-1.5">
                <label for="total_jam" class="block text-xs font-bold text-slate-700">
                    Total Jam Kerja Efektif <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-hourglass-empty text-base"></i>
                    </span>
                    <input type="number" name="total_jam" id="total_jam" min="1" max="24" placeholder="8"
                           class="w-full h-10 pl-10 pr-12 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 pointer-events-none">
                        Jam
                    </span>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_total_jam"></div>
            </div>

            <div class="space-y-1.5">
                <label for="color" class="block text-xs font-bold text-slate-700">Warna Label Laporan</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="color" id="color" value="#0a5c41"
                           class="w-14 h-10 p-1 bg-white border border-slate-300 rounded-xl cursor-pointer" />
                    <span class="text-xs text-slate-500 font-medium">Warna pembeda di rekap presensi</span>
                </div>
            </div>
        </div>

        <!-- Row 5: Lintas Hari Toggle -->
        <div class="p-3.5 bg-slate-50/80 border border-slate-200/90 rounded-2xl space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-start">
                <div class="sm:col-span-4 space-y-1.5">
                    <label for="lintashari" class="block text-xs font-bold text-slate-700">
                        Lintas Hari (Shift Malam)? <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="lintashari" id="lintashari" 
                                class="w-full h-10 pl-3.5 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                            <option value="">Pilih Status</option>
                            <option value="1">Ya (Lintas Hari)</option>
                            <option value="0">Tidak</option>
                        </select>
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-chevron-down text-xs"></i>
                        </span>
                    </div>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_lintashari"></div>
                </div>

                <div id="sectionLintasHari" class="sm:col-span-8 space-y-1.5" style="display: none;">
                    <label for="batas_presensi_pulang" class="block text-xs font-bold text-slate-700">
                        Batas Jam Pulang Lintas Hari
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-clock-pause text-base"></i>
                        </span>
                        <input type="text" name="batas_presensi_pulang" id="batas_presensi_pulang" placeholder="Contoh: 10:00" autocomplete="off"
                               class="time-mask w-full h-10 pl-10 pr-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                    </div>
                    <p class="text-[10.5px] text-slate-400">Kosongkan jika menggunakan batas default sistem</p>
                </div>
            </div>
        </div>

        <!-- Row 6: Keterangan -->
        <div class="space-y-1.5">
            <label for="keterangan" class="block text-xs font-bold text-slate-700">Keterangan / Catatan</label>
            <textarea name="keterangan" id="keterangan" rows="2" maxlength="255"
                      placeholder="Catatan tambahan jam kerja (opsional)..."
                      class="w-full p-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition resize-none"></textarea>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" id="btnSubmitJamKerjaV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Jam Kerja</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formJamKerjaV2');
        const kodeInput = document.getElementById('kode_jam_kerja');
        const namaInput = document.getElementById('nama_jam_kerja');
        const kodeCount = document.getElementById('kodeJamKerjaCount');
        const namaCount = document.getElementById('namaJamKerjaCount');

        const masukInput = document.getElementById('jam_masuk');
        const pulangInput = document.getElementById('jam_pulang');
        const istirahatSelect = document.getElementById('istirahat');
        const awalIstInput = document.getElementById('jam_awal_istirahat');
        const akhirIstInput = document.getElementById('jam_akhir_istirahat');
        const totalJamInput = document.getElementById('total_jam');
        const lintashariSelect = document.getElementById('lintashari');

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
        $("#istirahat").on("change", function() {
            if ($(this).val() === "1") {
                $("#sectionIstirahat").fadeIn(200);
            } else {
                $("#sectionIstirahat").fadeOut(200);
                $("#jam_awal_istirahat, #jam_akhir_istirahat").val("");
            }
        });

        $("#istirahatlembur").on("change", function() {
            if ($(this).val() === "1") {
                $("#sectionIstirahatLembur").fadeIn(200);
            } else {
                $("#sectionIstirahatLembur").fadeOut(200);
                $("#jam_awal_istirahatlembur, #jam_akhir_istirahatlembur").val("");
            }
        });

        // Lintas Hari handler
        $("#lintashari").on("change", function() {
            if ($(this).val() === "1") {
                $("#sectionLintasHari").fadeIn(200);
            } else {
                $("#sectionLintasHari").fadeOut(200);
                $("#batas_presensi_pulang").val("");
            }
        });

        // Auto uppercase & alphanumeric character count for kode
        if (kodeInput) {
            kodeInput.addEventListener('input', function(e) {
                let val = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                if (val.length > 4) val = val.substring(0, 4);
                e.target.value = val;
                kodeCount.textContent = `${val.length}/4`;
            });
        }

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

            const kode = kodeInput ? kodeInput.value.trim() : "";
            const nama = namaInput ? namaInput.value.trim() : "";
            const masuk = masukInput ? masukInput.value.trim() : "";
            const pulang = pulangInput ? pulangInput.value.trim() : "";
            const istirahat = istirahatSelect ? istirahatSelect.value : "";
            const awalIst = awalIstInput ? awalIstInput.value.trim() : "";
            const akhirIst = akhirIstInput ? akhirIstInput.value.trim() : "";
            const totalJam = totalJamInput ? (parseInt(totalJamInput.value) || 0) : 0;
            const lintashari = lintashariSelect ? lintashariSelect.value : "";

            if (!kode) {
                hasError = true;
                $(kodeInput).addClass("border-rose-500");
                $("#err_kode_jam_kerja").removeClass("hidden").text("Kode Jam Kerja wajib diisi.");
            }

            if (!nama) {
                hasError = true;
                $(namaInput).addClass("border-rose-500");
                $("#err_nama_jam_kerja").removeClass("hidden").text("Nama Jam Kerja wajib diisi.");
            }

            if (!masuk || !timeRegex.test(masuk)) {
                hasError = true;
                $(masukInput).addClass("border-rose-500");
                $("#err_jam_masuk").removeClass("hidden").text("Format Jam Masuk harus HH:mm (contoh: 08:00).");
            }

            if (!pulang || !timeRegex.test(pulang)) {
                hasError = true;
                $(pulangInput).addClass("border-rose-500");
                $("#err_jam_pulang").removeClass("hidden").text("Format Jam Pulang harus HH:mm (contoh: 17:00).");
            }

            if (istirahat === "") {
                hasError = true;
                $(istirahatSelect).addClass("border-rose-500");
                $("#err_istirahat").removeClass("hidden").text("Status istirahat wajib dipilih.");
            } else if (istirahat === "1") {
                if (!awalIst || !timeRegex.test(awalIst)) {
                    hasError = true;
                    $(awalIstInput).addClass("border-rose-500");
                    $("#err_jam_awal_istirahat").removeClass("hidden").text("Format mulai istirahat harus HH:mm.");
                }
                if (!akhirIst || !timeRegex.test(akhirIst)) {
                    hasError = true;
                    $(akhirIstInput).addClass("border-rose-500");
                    $("#err_jam_akhir_istirahat").removeClass("hidden").text("Format selesai istirahat harus HH:mm.");
                }
            }

            if (totalJam < 1 || totalJam > 24) {
                hasError = true;
                $(totalJamInput).addClass("border-rose-500");
                $("#err_total_jam").removeClass("hidden").text("Total jam kerja efektif harus antara 1 s/d 24 jam.");
            }

            if (lintashari === "") {
                hasError = true;
                $(lintashariSelect).addClass("border-rose-500");
                $("#err_lintashari").removeClass("hidden").text("Status lintas hari wajib dipilih.");
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            $("#btnSubmitJamKerjaV2").prop("disabled", true).html(`
                <i class="ti ti-loader animate-spin text-sm"></i>
                <span>Menyimpan...</span>
            `);
        });
    })();
</script>
