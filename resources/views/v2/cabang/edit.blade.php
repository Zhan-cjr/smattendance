<form action="{{ route('cabang.update', Crypt::encrypt($cabang->kode_cabang)) }}" id="formeditCabangV2" method="POST" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <div class="space-y-4">
        <!-- 1. DATA IDENTITAS CABANG -->
        <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-3">
            <div class="flex items-center gap-2 pb-2 border-b border-slate-200/80">
                <i class="ti ti-building-community text-emerald-600 text-base"></i>
                <h6 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-0">Identitas Cabang</h6>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Kode Cabang (Readonly) -->
                <div class="space-y-1.5">
                    <label for="kode_cabang_edit" class="block text-xs font-bold text-slate-700">
                        Kode Cabang
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-barcode text-base"></i>
                        </span>
                        <input type="text" 
                               id="kode_cabang_edit" 
                               name="kode_cabang" 
                               value="{{ $cabang->kode_cabang }}"
                               readonly 
                               class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-500 bg-slate-100 border border-slate-300 rounded-xl outline-none cursor-not-allowed">
                    </div>
                </div>

                <!-- Nama Cabang -->
                <div class="space-y-1.5">
                    <label for="nama_cabang_edit" class="block text-xs font-bold text-slate-700">
                        Nama Cabang <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-building text-base"></i>
                        </span>
                        <input type="text" 
                               id="nama_cabang_edit" 
                               name="nama_cabang" 
                               value="{{ $cabang->nama_cabang }}"
                               maxlength="50" 
                               placeholder="Contoh: TASIKMALAYA" 
                               autocomplete="off"
                               class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wide text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                    </div>
                    <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                        <span>Maksimal 50 karakter</span>
                        <span id="namaCabangCountEdit">{{ strlen($cabang->nama_cabang) }}/50</span>
                    </div>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nama_cabang_edit"></div>
                </div>
            </div>

            <!-- Alamat & Telepon -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2 space-y-1.5">
                    <label for="alamat_cabang_edit" class="block text-xs font-bold text-slate-700">
                        Alamat Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-map-pin text-base"></i>
                        </span>
                        <input type="text" 
                               id="alamat_cabang_edit" 
                               name="alamat_cabang" 
                               value="{{ $cabang->alamat_cabang }}"
                               maxlength="100" 
                               placeholder="Contoh: Jln. Perintis Kemerdekaan No. 80" 
                               autocomplete="off"
                               class="w-full h-10 pl-10 pr-3.5 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                    </div>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_alamat_cabang_edit"></div>
                </div>

                <div class="space-y-1.5">
                    <label for="telepon_cabang_edit" class="block text-xs font-bold text-slate-700">
                        Telepon <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-phone text-base"></i>
                        </span>
                        <input type="text" 
                               id="telepon_cabang_edit" 
                               name="telepon_cabang" 
                               value="{{ $cabang->telepon_cabang }}"
                               maxlength="13" 
                               placeholder="Contoh: 0265311766" 
                               autocomplete="off"
                               class="w-full h-10 pl-10 pr-3.5 text-xs font-mono text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                    </div>
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_telepon_cabang_edit"></div>
                </div>
            </div>
        </div>

        <!-- 2. TITIK LOKASI GEOFENCE & PETA -->
        <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/80 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200/80">
                <div class="flex items-center gap-2">
                    <i class="ti ti-map text-emerald-600 text-base"></i>
                    <h6 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-0">Lokasi & Radius Presensi GPS</h6>
                </div>
                <span class="text-[11px] text-slate-400 font-medium">Klik peta atau drag pin marker</span>
            </div>

            <!-- Search Location Input on Map -->
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" 
                           id="searchLocationEditV2" 
                           placeholder="Cari nama tempat / kota (misal: Tasikmalaya, Jawa Barat)..." 
                           class="w-full h-9 pl-10 pr-3 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 outline-none transition">
                </div>
                <button type="button" 
                        id="btnSearchLocationEditV2" 
                        class="h-9 px-3.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold flex items-center gap-1 transition cursor-pointer">
                    <i class="ti ti-search text-xs"></i>
                    <span>Cari</span>
                </button>
            </div>

            <!-- Leaflet Map Container -->
            <div class="relative rounded-xl overflow-hidden border border-slate-300 shadow-2xs">
                <div id="mapCabangEdit" style="height: 300px; width: 100%; z-index: 1;"></div>
            </div>

            <!-- Coordinate, Radius & Timezone -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                <!-- Lokasi Koordinat -->
                <div class="space-y-1">
                    <label for="lokasi_cabang_edit" class="block text-xs font-bold text-slate-700">
                        Koordinat GPS (Lat, Long) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-rose-500 pointer-events-none flex items-center">
                            <i class="ti ti-location-filled text-sm"></i>
                        </span>
                        <input type="text" 
                               id="lokasi_cabang_edit" 
                               name="lokasi_cabang" 
                               value="{{ $cabang->lokasi_cabang }}" 
                               readonly 
                               class="w-full h-10 pl-8 pr-3 text-xs font-mono font-bold text-slate-800 bg-slate-100 border border-slate-300 rounded-xl outline-none"
                               required>
                    </div>
                </div>

                <!-- Radius Presensi -->
                <div class="space-y-1">
                    <label for="radius_cabang_edit" class="block text-xs font-bold text-slate-700">
                        Radius Presensi (Meter) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-radar text-base text-emerald-600"></i>
                        </span>
                        <input type="number" 
                               id="radius_cabang_edit" 
                               name="radius_cabang" 
                               value="{{ $cabang->radius_cabang }}" 
                               min="1" 
                               max="9999" 
                               placeholder="30" 
                               class="w-full h-10 pl-10 pr-3 text-xs font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 outline-none transition"
                               required>
                    </div>
                </div>

                <!-- Zona Waktu -->
                <div class="space-y-1">
                    <label for="timezone_edit" class="block text-xs font-bold text-slate-700">
                        Zona Waktu <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                            <i class="ti ti-clock text-base"></i>
                        </span>
                        <select name="timezone" 
                                id="timezone_edit" 
                                class="w-full h-10 pl-10 pr-3 text-xs font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 outline-none transition"
                                required>
                            <option value="Asia/Jakarta" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Jakarta')>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Makassar')>Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Jayapura')>Asia/Jayapura (WIT)</option>
                            <option value="Asia/Singapore" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Singapore')>Asia/Singapore</option>
                            <option value="Asia/Kuala_Lumpur" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Kuala_Lumpur')>Asia/Kuala_Lumpur</option>
                            <option value="Asia/Bangkok" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Bangkok')>Asia/Bangkok</option>
                            <option value="Asia/Manila" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'Asia/Manila')>Asia/Manila</option>
                            <option value="UTC" @selected(($cabang->timezone ?? $defaultTimezone ?? 'Asia/Jakarta') == 'UTC')>UTC</option>
                        </select>
                    </div>
                </div>
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
                id="btnSubmitCabangEditV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formeditCabangV2');
        const namaInput = document.getElementById('nama_cabang_edit');
        const telpInput = document.getElementById('telepon_cabang_edit');
        const lokasiInput = document.getElementById('lokasi_cabang_edit');
        const radiusInput = document.getElementById('radius_cabang_edit');
        const namaCount = document.getElementById('namaCabangCountEdit');

        if (namaInput) {
            namaInput.addEventListener('input', function(e) {
                let val = e.target.value.toUpperCase();
                if (val.length > 50) val = val.substring(0, 50);
                e.target.value = val;
                namaCount.textContent = `${val.length}/50`;
            });
        }

        if (telpInput) {
            telpInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 13);
            });
        }

        // Initialize Leaflet Map with Delay for Bootstrap Modal Rendering
        setTimeout(function() {
            const mapContainer = document.getElementById('mapCabangEdit');
            if (!mapContainer || typeof L === 'undefined') return;

            if (mapContainer._leaflet_id) {
                mapContainer.innerHTML = '';
                delete mapContainer._leaflet_id;
            }

            let defaultLat = -7.317623;
            let defaultLng = 108.199358;

            if (lokasiInput && lokasiInput.value) {
                const parts = lokasiInput.value.split(',');
                if (parts.length === 2) {
                    defaultLat = parseFloat(parts[0].trim()) || defaultLat;
                    defaultLng = parseFloat(parts[1].trim()) || defaultLng;
                }
            }

            const map = L.map('mapCabangEdit').setView([defaultLat, defaultLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
            let circle = L.circle([defaultLat, defaultLng], {
                color: '#059669',
                fillColor: '#10b981',
                fillOpacity: 0.25,
                radius: parseInt(radiusInput.value) || 30
            }).addTo(map);

            function updateCoords(lat, lng) {
                const str = `${lat.toFixed(6)},${lng.toFixed(6)}`;
                lokasiInput.value = str;
                marker.setLatLng([lat, lng]);
                circle.setLatLng([lat, lng]);
            }

            marker.on('dragend', function(e) {
                const p = marker.getLatLng();
                updateCoords(p.lat, p.lng);
            });

            map.on('click', function(e) {
                updateCoords(e.latlng.lat, e.latlng.lng);
            });

            radiusInput.addEventListener('input', function() {
                const rad = parseInt(this.value) || 30;
                circle.setRadius(rad);
            });

            // Search Location
            $('#btnSearchLocationEditV2').on('click', function() {
                const q = $('#searchLocationEditV2').val().trim();
                if (!q) return;

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lon = parseFloat(data[0].lon);
                            map.setView([lat, lon], 16);
                            updateCoords(lat, lon);
                        } else {
                            if (window.toast) window.toast.warning('Lokasi tidak ditemukan');
                        }
                    })
                    .catch(() => {
                        if (window.toast) window.toast.error('Gagal mencari lokasi');
                    });
            });

            map.invalidateSize();
        }, 300);

        $(form).on('submit', function(e) {
            const nama = namaInput ? namaInput.value.trim() : '';
            const alamat = document.getElementById('alamat_cabang_edit') ? document.getElementById('alamat_cabang_edit').value.trim() : '';
            const telp = telpInput ? telpInput.value.trim() : '';
            const lokasi = lokasiInput ? lokasiInput.value.trim() : '';
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $(namaInput).removeClass('border-rose-500');
            $('#alamat_cabang_edit').removeClass('border-rose-500');
            $(telpInput).removeClass('border-rose-500');

            if (!nama) {
                $('#err_nama_cabang_edit').text('Nama Cabang wajib diisi').removeClass('hidden');
                $(namaInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!alamat) {
                $('#err_alamat_cabang_edit').text('Alamat Cabang wajib diisi').removeClass('hidden');
                $('#alamat_cabang_edit').addClass('border-rose-500');
                hasError = true;
            }

            if (!telp) {
                $('#err_telepon_cabang_edit').text('No. Telepon Cabang wajib diisi').removeClass('hidden');
                $(telpInput).addClass('border-rose-500');
                hasError = true;
            }

            if (!lokasi) {
                if (window.toast) window.toast.warning('Titik koordinat lokasi GPS wajib ditentukan pada peta');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitCabangEditV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    })();
</script>
