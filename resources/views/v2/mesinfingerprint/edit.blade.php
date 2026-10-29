@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('mesin-fingerprint.update', Crypt::encrypt($mesinfinger->id)) }}" method="POST" id="formMesinFingerprintEditV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <div class="space-y-4">
        <!-- Banner Info -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-edit"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Edit Data Mesin Fingerprint</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Perubahan Serial Number atau konfigurasi mesin akan disinkronkan ke penerima log absensi otomatis.
                </p>
            </div>
        </div>

        <!-- Row 1: Serial Number & Merek -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            <!-- Serial Number -->
            <div class="sm:col-span-7 space-y-1.5">
                <label for="sn_edit" class="block text-xs font-bold text-slate-700">
                    Serial Number (SN / Dev ID) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-barcode text-base"></i>
                    </span>
                    <input type="text" 
                           id="sn_edit" 
                           name="sn" 
                           value="{{ $mesinfinger->sn }}"
                           placeholder="Contoh: C2609075E32F282D" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition uppercase">
                </div>
                <p class="text-[10.5px] text-slate-400">Kode unik perangkat sidik jari</p>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_sn_edit"></div>
            </div>

            <!-- Merek Mesin -->
            <div class="sm:col-span-5 space-y-1.5">
                <label for="nama_mesin_edit" class="block text-xs font-bold text-slate-700">
                    Merek Mesin <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-device-desktop text-base"></i>
                    </span>
                    <select name="nama_mesin" id="nama_mesin_edit" 
                            class="w-full h-10 pl-10 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                        <option value="">Pilih Merek</option>
                        <option value="Fingerspot" {{ $mesinfinger->nama_mesin == 'Fingerspot' ? 'selected' : '' }}>Fingerspot</option>
                        <option value="Solution" {{ $mesinfinger->nama_mesin == 'Solution' ? 'selected' : '' }}>Solution</option>
                        <option value="ZKTeco" {{ $mesinfinger->nama_mesin == 'ZKTeco' ? 'selected' : '' }}>ZKTeco</option>
                    </select>
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-chevron-down text-xs"></i>
                    </span>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nama_mesin_edit"></div>
            </div>
        </div>

        <!-- Row 2: Type Mesin & Status -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
            <!-- Type Model -->
            <div class="sm:col-span-7 space-y-1.5">
                <label for="merk_edit" class="block text-xs font-bold text-slate-700">
                    Type / Model Mesin
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-tag text-base"></i>
                    </span>
                    <input type="text" 
                           id="merk_edit" 
                           name="merk" 
                           value="{{ $mesinfinger->merk }}"
                           placeholder="Contoh: Revo-151 / X100-C" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                </div>
                <p class="text-[10.5px] text-slate-400">Tipe spesifik perangkat</p>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_merk_edit"></div>
            </div>

            <!-- Status Aktif / Nonaktif -->
            <div class="sm:col-span-5 space-y-1.5">
                <label for="status_edit" class="block text-xs font-bold text-slate-700">
                    Status Operasional <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-power text-base"></i>
                    </span>
                    <select name="status" id="status_edit" 
                            class="w-full h-10 pl-10 pr-8 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition appearance-none cursor-pointer">
                        <option value="Aktif" {{ $mesinfinger->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ $mesinfinger->status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-chevron-down text-xs"></i>
                    </span>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_status_edit"></div>
            </div>
        </div>

        <!-- Row 3: Lokasi Penempatan -->
        <div class="space-y-1.5">
            <label for="lokasi_edit" class="block text-xs font-bold text-slate-700">
                Lokasi Penempatan Mesin
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-map-pin text-base"></i>
                </span>
                <input type="text" 
                       id="lokasi_edit" 
                       name="lokasi" 
                       value="{{ $mesinfinger->lokasi }}"
                       placeholder="Contoh: Lobi Utama Lantai 1 / Pos Security Depan" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <p class="text-[10.5px] text-slate-400">Deskripsi letak fisik mesin presensi</p>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_lokasi_edit"></div>
        </div>

        <!-- Row 4: Titik Koordinat GPS -->
        <div class="space-y-1.5">
            <label for="titik_koordinat_edit" class="block text-xs font-bold text-slate-700">
                Titik Koordinat (Latitude, Longitude)
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-map-2 text-base"></i>
                </span>
                <input type="text" 
                       id="titik_koordinat_edit" 
                       name="titik_koordinat" 
                       value="{{ $mesinfinger->titik_koordinat }}"
                       placeholder="Contoh: -6.200000, 106.816666" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-mono text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <p class="text-[10.5px] text-slate-400">Opsional: Titik koordinat GPS penempatan mesin</p>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_titik_koordinat_edit"></div>
        </div>
    </div>

    <!-- Action Buttons (Consistent with Departemen) -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitMesinEditV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formMesinFingerprintEditV2');
        const snInput = document.getElementById('sn_edit');
        const namaMesinSelect = document.getElementById('nama_mesin_edit');
        const statusSelect = document.getElementById('status_edit');

        if (snInput) {
            snInput.addEventListener('input', function(e) {
                this.value = this.value.toUpperCase();
            });
        }

        $(form).on('submit', function(e) {
            let hasError = false;
            $('.error-feedback').addClass('hidden').text('');
            $(form).find('input, select').removeClass('border-rose-500');

            const sn = snInput ? snInput.value.trim() : '';
            const namaMesin = namaMesinSelect ? namaMesinSelect.value : '';
            const status = statusSelect ? statusSelect.value : '';

            if (!sn) {
                hasError = true;
                $(snInput).addClass('border-rose-500');
                $('#err_sn_edit').text('Serial Number (SN) wajib diisi').removeClass('hidden');
            }

            if (!namaMesin) {
                hasError = true;
                $(namaMesinSelect).addClass('border-rose-500');
                $('#err_nama_mesin_edit').text('Merek Mesin wajib dipilih').removeClass('hidden');
            }

            if (!status) {
                hasError = true;
                $(statusSelect).addClass('border-rose-500');
                $('#err_status_edit').text('Status operasional wajib ditentukan').removeClass('hidden');
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            $('#btnSubmitMesinEditV2').prop('disabled', true).html(`
                <i class="ti ti-loader animate-spin text-sm"></i>
                <span>Memperbarui...</span>
            `);
        });
    })();
</script>
