@php
    $in_out = $status == 'in' ? 'Masuk' : 'Pulang';
    $foto = $status == 'in' ? $presensi->foto_in : $presensi->foto_out;
    $jam = $status == 'in' ? $presensi->jam_in : $presensi->jam_out;
    $lokasi_user = $status == 'in' ? $presensi->lokasi_in : $presensi->lokasi_out;
    $map_id = $status == 'in' ? 'map_v2_in' : 'map_v2_out';
@endphp

<div class="space-y-5">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- ================= KOLOM KIRI: FOTO & DETAIL KARYAWAN ================= -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Foto Presensi Card -->
            <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                        <i class="ti ti-camera text-emerald-600 text-base"></i>
                        <span>Foto Presensi {{ $in_out }}</span>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $status == 'in' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                        <i class="ti {{ $status == 'in' ? 'ti-circle-check' : 'ti-circle-x' }} text-xs"></i>
                        <span>Presensi {{ $in_out }}</span>
                    </span>
                </div>
                
                <div class="p-4 flex flex-col items-center justify-center">
                    @if (!empty($foto) && Storage::disk('public')->exists('/uploads/absensi/' . $foto))
                        <div class="relative w-full rounded-xl overflow-hidden border border-slate-200 shadow-xs group">
                            <img src="{{ url('/storage/uploads/absensi/' . $foto) }}?v={{ time() }}" 
                                 class="w-full h-64 object-cover transition duration-300 group-hover:scale-105" 
                                 alt="Foto Presensi {{ $in_out }}">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-3 text-white text-xs font-bold">
                                {{ date('d M Y H:i:s', strtotime($jam)) }}
                            </div>
                        </div>
                    @else
                        <div class="w-full h-56 rounded-xl bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 p-4">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl text-slate-400 mb-2">
                                <i class="ti ti-camera-off"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-600">Tidak ada lampiran foto</span>
                            <span class="text-[11px] text-slate-400 mt-0.5">Presensi tercatat tanpa unggahan gambar selfie</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detail Identitas Presensi -->
            <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-2 bg-slate-50/70 text-xs font-bold text-slate-800">
                    <i class="ti ti-id text-emerald-600 text-base"></i>
                    <span>Informasi Karyawan & Lokasi</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    <!-- NPP / NIK -->
                    <div class="flex items-center justify-between p-3 hover:bg-slate-50/50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-barcode text-slate-400 text-sm"></i>
                            <span>NPP / NIK</span>
                        </span>
                        <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $presensi->nik }}</span>
                    </div>

                    <!-- Nama Karyawan -->
                    <div class="flex items-center justify-between p-3 hover:bg-slate-50/50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-user text-slate-400 text-sm"></i>
                            <span>Nama Karyawan</span>
                        </span>
                        <div class="flex items-center gap-2">
                            <div class="relative shrink-0">
                                @if (!empty($presensi->foto_karyawan) && Storage::disk('public')->exists('/karyawan/' . $presensi->foto_karyawan))
                                    <img src="{{ getfotoKaryawan($presensi->foto_karyawan) }}" alt="{{ $presensi->nama_karyawan }}" class="w-7 h-8 rounded-md object-cover border border-slate-200">
                                @else
                                    <div class="w-7 h-8 rounded-md bg-orange-500 text-white border border-orange-600/30 flex items-center justify-center font-black text-[11px]">
                                        {{ strtoupper(substr($presensi->nama_karyawan, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <span class="font-bold text-slate-900">{{ $presensi->nama_karyawan }}</span>
                        </div>
                    </div>

                    <!-- Jabatan & Dept -->
                    <div class="flex items-center justify-between p-3 hover:bg-slate-50/50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-briefcase text-slate-400 text-sm"></i>
                            <span>Jabatan & Dept</span>
                        </span>
                        <span class="font-semibold text-slate-700 text-right">{{ $presensi->nama_jabatan }} <span class="text-slate-400 font-normal">({{ $presensi->nama_dept }})</span></span>
                    </div>

                    <!-- Kantor Cabang -->
                    <div class="flex items-center justify-between p-3 hover:bg-slate-50/50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-building text-slate-400 text-sm"></i>
                            <span>Kantor Cabang</span>
                        </span>
                        <span class="font-bold text-slate-800">{{ $presensi->nama_cabang }}</span>
                    </div>

                    <!-- Waktu Presensi -->
                    <div class="flex items-center justify-between p-3 hover:bg-slate-50/50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-clock text-slate-400 text-sm"></i>
                            <span>Waktu Presensi</span>
                        </span>
                        <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100">
                            {{ DateToIndo($presensi->tanggal) }} • {{ date('H:i:s', strtotime($jam)) }}
                        </span>
                    </div>

                    <!-- Jarak Radius -->
                    <div class="flex items-center justify-between p-3 hover:bg-slate-50/50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-radar text-slate-400 text-sm"></i>
                            <span>Jarak Radius</span>
                        </span>
                        @php
                            $meters = 0;
                            if (!empty($lokasi_user)) {
                                $lok = explode(',', $lokasi_user);
                                $dist = HitungJarak($latitude, $longitude, $lok[0], $lok[1]);
                                $meters = $dist['meters'];
                            }
                        @endphp
                        <span class="font-bold {{ $meters > $cabang->radius_cabang ? 'text-rose-600 bg-rose-50 border-rose-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200' }} px-2.5 py-0.5 rounded-full border text-[11px]">
                            {{ formatAngkaDesimal($meters) }} Meter (Maks {{ $cabang->radius_cabang }}m)
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= KOLOM KANAN: PETA & DETAIL MESIN ================= -->
        <div class="lg:col-span-7 space-y-4">
            
            @if ($presensi->id_mesin != null && $presensi->mesinfingerprint)
                <!-- Fingerprint ADMS Info Box -->
                <div class="bg-blue-50/70 rounded-2xl border border-blue-200/80 p-4 shadow-2xs">
                    <div class="flex items-center gap-2 text-xs font-bold text-blue-900 mb-3">
                        <i class="ti ti-fingerprint text-blue-600 text-base"></i>
                        <span>Data Mesin Fingerprint (ADMS Sync)</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-white/80 p-2.5 rounded-xl border border-blue-100">
                            <span class="text-[10.5px] font-semibold text-slate-500 flex items-center gap-1"><i class="ti ti-device-desktop text-xs"></i> Nama Perangkat</span>
                            <span class="font-bold text-slate-900 mt-0.5 block">{{ $presensi->mesinfingerprint->nama_mesin }}</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-xl border border-blue-100">
                            <span class="text-[10.5px] font-semibold text-slate-500 flex items-center gap-1"><i class="ti ti-hash text-xs"></i> Serial Number (SN)</span>
                            <span class="font-mono font-bold text-blue-700 mt-0.5 block">{{ $presensi->mesinfingerprint->sn }}</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-xl border border-blue-100">
                            <span class="text-[10.5px] font-semibold text-slate-500 flex items-center gap-1"><i class="ti ti-tag text-xs"></i> Merk / Tipe</span>
                            <span class="font-semibold text-slate-800 mt-0.5 block">{{ $presensi->mesinfingerprint->merk ?? '-' }}</span>
                        </div>
                        <div class="bg-white/80 p-2.5 rounded-xl border border-blue-100">
                            <span class="text-[10.5px] font-semibold text-slate-500 flex items-center gap-1"><i class="ti ti-map-pin text-xs"></i> Lokasi Penempatan</span>
                            <span class="font-semibold text-slate-800 mt-0.5 block">{{ $presensi->mesinfingerprint->lokasi ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Plotting Lokasi GPS Card -->
            <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
                        <i class="ti ti-map-pin text-emerald-600 text-base"></i>
                        <span>Plotting Lokasi Presensi GPS</span>
                    </div>
                    @if($lokasi_user)
                        <span class="font-mono text-[10.5px] text-slate-500 font-semibold bg-slate-100 px-2.5 py-0.5 rounded-md border border-slate-200">
                            {{ $lokasi_user }}
                        </span>
                    @endif
                </div>

                <div class="p-0">
                    @if (!empty($lokasi_user))
                        <div id="{{ $map_id }}" class="w-full h-[420px] rounded-b-2xl"></div>
                    @else
                        <div class="flex flex-col items-center justify-center p-12 bg-slate-50/50 h-[420px] text-slate-400">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-3xl text-slate-400 mb-2">
                                <i class="ti ti-map-off"></i>
                            </div>
                            <span class="text-xs font-bold text-slate-600">Titik koordinat GPS tidak ditemukan</span>
                            <span class="text-[11px] text-slate-400 mt-0.5">Presensi dilakukan tanpa izin akses lokasi atau melalui mesin fingerprint lokal</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>

@if (!empty($lokasi_user))
<script>
    (function() {
        function initMap() {
            if (typeof L === 'undefined') {
                setTimeout(initMap, 100);
                return;
            }

            var lokasi = "{{ $lokasi_user }}";
            var lok = lokasi.split(",");
            var latitude_user = parseFloat(lok[0]);
            var longitude_user = parseFloat(lok[1]);

            var latitude_kantor = parseFloat("{{ $latitude }}");
            var longitude_kantor = parseFloat("{{ $longitude }}");
            var rd = parseFloat("{{ $cabang->radius_cabang }}");
            var mapContainer = document.getElementById('{{ $map_id }}');
            
            if (!mapContainer) return;

            // Remove existing map if already initialized on container
            if (mapContainer._leaflet_id) {
                mapContainer._leaflet_id = null;
            }

            var mapInstance = L.map(mapContainer, {
                center: [latitude_user, longitude_user],
                zoom: 16
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(mapInstance);

            // Marker Karyawan
            var userMarker = L.marker([latitude_user, longitude_user]).addTo(mapInstance);
            userMarker.bindPopup("<div class='p-1 text-xs font-bold text-slate-800'>📍 Lokasi Presensi Karyawan</div>").openPopup();

            // Marker Kantor
            var officeIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
            var officeMarker = L.marker([latitude_kantor, longitude_kantor], { icon: officeIcon }).addTo(mapInstance);
            officeMarker.bindPopup("<div class='p-1 text-xs font-bold text-slate-800'>🏢 Kantor Cabang: {{ $cabang->nama_cabang }}</div>");

            // Radius Kantor Cabang
            L.circle([latitude_kantor, longitude_kantor], {
                color: '#10b981',
                fillColor: '#10b981',
                fillOpacity: 0.15,
                radius: rd,
                weight: 2
            }).addTo(mapInstance);

            // Polyline penghubung
            L.polyline([
                [latitude_user, longitude_user],
                [latitude_kantor, longitude_kantor]
            ], {
                color: '#3b82f6',
                weight: 2.5,
                dashArray: '6, 6',
                opacity: 0.8
            }).addTo(mapInstance);

            // Invalidate size on modal animation completion
            setTimeout(function() {
                mapInstance.invalidateSize();
            }, 300);
            setTimeout(function() {
                mapInstance.invalidateSize();
            }, 600);
        }

        initMap();
    })();
</script>
@endif
