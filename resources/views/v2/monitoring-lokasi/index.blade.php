@extends('v2.layouts.app')

@section('titlepage', 'Monitoring Lokasi Real-Time')
@section('page_title', 'Monitoring Lokasi Real-Time')

@section('content')
@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ti ti-current-location text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Monitoring Lokasi Real-Time</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Tracking lokasi karyawan secara live dan riwayat titik koordinat GPS
            </p>
        </div>
    </div>

    <!-- Filter & Status Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4">
            <label class="text-xs font-bold text-slate-700 block mb-1.5">Pilih Karyawan</label>
            <select id="employeeSelect" onchange="handleEmployeeChange(this.value)"
                    class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                <option value="">-- Pilih Karyawan untuk Monitoring --</option>
                @foreach ($spyEmployees as $employee)
                    <option value="{{ $employee->nik }}" @if ($selectedNik == $employee->nik) selected @endif>
                        {{ $employee->nama_karyawan }} - {{ $employee->jabatan->nama_jabatan ?? '-' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 flex flex-col justify-center">
            <label class="text-xs font-bold text-slate-700 block mb-1.5">Status Monitoring</label>
            <div class="px-3.5 py-2.5 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-xs font-semibold flex items-center gap-2">
                <i class="ti ti-info-circle text-base"></i>
                <span id="monitoringStatus">Pilih karyawan untuk memulai monitoring</span>
            </div>
        </div>
    </div>

    <!-- Monitoring Settings Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div>
                    <h6 class="text-xs font-bold text-slate-800">Live Location Monitoring</h6>
                    <span class="text-[11px] text-slate-500">Status pengaturan monitoring</span>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-white {{ $generalSetting && $generalSetting->enable_live_location_monitoring ? 'bg-emerald-600' : 'bg-rose-600' }}">
                        <i class="ti {{ $generalSetting && $generalSetting->enable_live_location_monitoring ? 'ti-circle-check' : 'ti-circle-x' }}"></i>
                        {{ $generalSetting && $generalSetting->enable_live_location_monitoring ? 'AKTIF' : 'NON-AKTIF' }}
                    </span>
                    <div class="text-[11px] text-slate-500 mt-1">
                        Mode: {{ $generalSetting && $generalSetting->monitoring_live_location_mode == 0 ? '24 Jam' : 'Jam Kerja' }}
                    </div>
                </div>
            </div>
            @if($generalSetting && $generalSetting->enable_live_location_monitoring)
                <div class="mt-2.5 text-[11px] text-emerald-700 font-medium">
                    <i class="ti ti-info-circle mr-1"></i>
                    @if($generalSetting->monitoring_live_location_mode == 0)
                        Monitoring aktif 24/7
                    @else
                        Monitoring aktif hanya saat jam kerja (setelah absen masuk, sebelum absen pulang)
                    @endif
                </div>
            @else
                <div class="mt-2.5 text-[11px] text-rose-600 font-medium">
                    <i class="ti ti-alert-circle mr-1"></i>Monitoring live location dinonaktifkan
                </div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 flex items-center justify-between">
            <div>
                <h6 class="text-xs font-bold text-slate-800">Karyawan "Mobile"</h6>
                <span class="text-[11px] text-slate-500">Total karyawan yang dimonitor</span>
            </div>
            <div class="text-right">
                <h4 class="text-2xl font-black text-emerald-700">{{ $spyEmployees->count() }}</h4>
                <span class="text-[11px] text-slate-500">orang</span>
            </div>
        </div>
    </div>

    @if ($selectedEmployee)
        <!-- Employee Info, Current Location & Controls Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Employee Info Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Informasi Karyawan</span>
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                        <i class="ti ti-user text-sm"></i>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-16 h-16 rounded-full overflow-hidden border border-slate-200 shrink-0">
                        @php
                            $defaultProfileImage = asset('assets/template/img/sample/avatar/avatar1.jpg');
                            $selectedEmployeePhotoUrl = $defaultProfileImage;

                            if (!empty($selectedEmployee->foto) && Storage::disk('public')->exists('/karyawan/' . $selectedEmployee->foto)) {
                                $selectedEmployeePhotoUrl = getfotoKaryawan($selectedEmployee->foto);
                            } else {
                                $firstFace = $selectedEmployee->facerecognition->first();
                                if ($firstFace) {
                                    $namaFolder = $selectedEmployee->nik . '-' . getNamaDepan(strtolower($selectedEmployee->nama_karyawan));
                                    $facePath = 'uploads/facerecognition/' . $namaFolder . '/' . $firstFace->wajah;

                                    if (Storage::disk('public')->exists($facePath)) {
                                        $encodedFolder = str_replace('%2F', '/', rawurlencode($namaFolder));
                                        $encodedFileName = rawurlencode($firstFace->wajah);
                                        try {
                                            $fileTimestamp = Storage::disk('public')->lastModified($facePath);
                                        } catch (\Exception $e) {
                                            $fileTimestamp = now()->timestamp;
                                        }
                                        $selectedEmployeePhotoUrl = url('/storage/uploads/facerecognition/' . $encodedFolder . '/' . $encodedFileName . '?v=' . $fileTimestamp);
                                    }
                                }
                            }
                        @endphp
                        <img src="{{ $selectedEmployeePhotoUrl }}" alt="Foto {{ $selectedEmployee->nama_karyawan }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h6 class="text-sm font-bold text-slate-800">{{ $selectedEmployee->nama_karyawan }}</h6>
                        <p class="text-[11px] text-slate-500">{{ $selectedEmployee->jabatan->nama_jabatan ?? '-' }}</p>
                        <p class="text-[11px] text-slate-500">{{ $selectedEmployee->cabang->nama_cabang ?? '-' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-[10.5px] text-slate-400 block">NIK</span>
                        <span class="font-mono font-bold text-slate-800">{{ $selectedEmployee->nik }}</span>
                    </div>
                    <div>
                        <span class="text-[10.5px] text-slate-400 block">Departemen</span>
                        <span class="font-bold text-slate-800">{{ $selectedEmployee->departemen->nama_dept ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Current Location Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Lokasi Terkini</span>
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center">
                        <i class="ti ti-map-pin text-sm"></i>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-[10.5px] text-slate-400 block">Latitude</span>
                        <span class="font-mono font-semibold text-slate-800" id="currentLatitude">-</span>
                    </div>
                    <div>
                        <span class="text-[10.5px] text-slate-400 block">Longitude</span>
                        <span class="font-mono font-semibold text-slate-800" id="currentLongitude">-</span>
                    </div>
                    <div>
                        <span class="text-[10.5px] text-slate-400 block">Akurasi GPS</span>
                        <span class="font-semibold text-slate-800" id="currentAccuracy">-</span>
                    </div>
                    <div>
                        <span class="text-[10.5px] text-slate-400 block">Waktu Tracking</span>
                        <span class="font-semibold text-slate-800 block" id="currentTime">-</span>
                        <span class="text-[10.5px] text-slate-400 block" id="currentTimeAgo">-</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10.5px] text-slate-400 block">Nama Lokasi</span>
                        <span class="font-semibold text-slate-800 block truncate" id="currentLocation">-</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-700">Status Monitoring:</span>
                        <span class="px-2 py-0.5 rounded-md bg-slate-500 text-white text-[10.5px] font-bold" id="employeeMonitoringStatus">Memeriksa...</span>
                    </div>
                    <span class="text-[10.5px] text-slate-500 block mt-1" id="employeeMonitoringDetail">
                        @if($generalSetting && $generalSetting->enable_live_location_monitoring)
                            {{ $generalSetting->monitoring_live_location_mode == 0 ? 'Mode 24 jam aktif' : 'Mode jam kerja aktif' }}
                        @else
                            Monitoring dinonaktifkan
                        @endif
                    </span>
                </div>
            </div>

            <!-- Refresh Controls Card -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-400">Kontrol Monitoring</span>
                    <div class="w-8 h-8 rounded-full bg-sky-600 text-white flex items-center justify-center">
                        <i class="ti ti-settings text-sm"></i>
                    </div>
                </div>
                <div>
                    <label class="text-[11px] font-bold text-slate-700 block mb-1">Interval Auto-Refresh</label>
                    <select id="refreshInterval" onchange="changeRefreshInterval(this.value)"
                            class="w-full h-9 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800">
                        <option value="3000">Setiap 3 Detik (Rekomendasi)</option>
                        <option value="5000" selected>Setiap 5 Detik</option>
                        <option value="10000">Setiap 10 Detik</option>
                        <option value="30000">Setiap 30 Detik</option>
                        <option value="60000">Setiap 1 Menit</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <button type="button" onclick="refreshLocation()"
                            class="w-full py-2 rounded-xl text-white text-xs font-bold shadow-2xs cursor-pointer flex items-center justify-center gap-1.5"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-refresh"></i> Refresh Sekarang
                    </button>
                    <button type="button" onclick="stopMonitoring()"
                            class="w-full py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-2xs cursor-pointer flex items-center justify-center gap-1.5">
                        <i class="ti ti-player-stop"></i> Hentikan Monitoring
                    </button>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h6 class="text-xs font-bold text-slate-800">Peta Lokasi Real-Time</h6>
                    <span class="text-[11px] text-slate-500" id="monitoringDateLabel">Tanggal monitoring: {{ date('Y-m-d') }}</span>
                </div>
            </div>
            <div id="map" style="height: 400px;"></div>
        </div>

        <!-- Location History Section -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
                <h6 class="text-xs font-bold text-slate-800">Riwayat Lokasi (Last 24 Hours)</h6>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-2.5 w-1/6">Waktu</th>
                            <th class="px-4 py-2.5 w-1/5">Latitude</th>
                            <th class="px-4 py-2.5 w-1/5">Longitude</th>
                            <th class="px-4 py-2.5">Lokasi</th>
                            <th class="px-4 py-2.5 w-1/6">Akurasi</th>
                        </tr>
                    </thead>
                    <tbody id="historyTableBody" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
            <div class="p-3 flex justify-center v2-pagination" id="historyPagination"></div>
        </div>

        <!-- Histori Lokasi dengan Filter Section -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden" x-data="{ openHistoryFilter: false }">
            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <h6 class="text-xs font-bold text-slate-800">Histori Lokasi dengan Filter</h6>
                <button type="button" @click="openHistoryFilter = !openHistoryFilter; setTimeout(() => { if (historyMap) historyMap.invalidateSize(); }, 200)"
                        class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer">
                    <i class="ti ti-filter"></i> Filter & Peta Histori
                </button>
            </div>
            <div x-show="openHistoryFilter" x-cloak class="p-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Mulai</label>
                        <input type="date" id="startDate" value="{{ date('Y-m-d', strtotime('-7 days')) }}"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Tanggal Akhir</label>
                        <input type="date" id="endDate" value="{{ date('Y-m-d') }}"
                               class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="loadHistoryMap()"
                                class="flex-1 h-10 rounded-xl text-white text-xs font-bold shadow-2xs cursor-pointer inline-flex items-center justify-center gap-1.5"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-map"></i> Tampilkan di Peta
                        </button>
                        <button type="button" onclick="clearHistoryMap()"
                                class="px-3.5 h-10 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold cursor-pointer inline-flex items-center justify-center gap-1">
                            <i class="ti ti-x"></i> Bersihkan
                        </button>
                    </div>
                </div>
                <div id="historyMap" class="rounded-xl border border-slate-200" style="height: 400px;"></div>
                <div>
                    <h6 class="text-xs font-bold text-slate-800 mb-2">Riwayat Lokasi (Sesuai Peta)</h6>
                    <div class="overflow-x-auto border border-slate-200 rounded-xl">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-2.5 w-1/6">Waktu</th>
                                    <th class="px-4 py-2.5 w-1/5">Latitude</th>
                                    <th class="px-4 py-2.5 w-1/5">Longitude</th>
                                    <th class="px-4 py-2.5">Lokasi</th>
                                </tr>
                            </thead>
                            <tbody id="historyMapTableBody" class="divide-y divide-slate-100">
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                                        Klik "Tampilkan di Peta" untuk melihat riwayat lokasi
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 flex justify-center v2-pagination" id="historyFilterPagination"></div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-12 text-center">
            <i class="ti ti-map-search text-5xl text-slate-300 block mb-3"></i>
            <h5 class="text-sm font-bold text-slate-700">Pilih Karyawan untuk Monitoring</h5>
            <p class="text-xs text-slate-500 mt-1">Silakan pilih salah satu karyawan dari dropdown di atas untuk memulai monitoring lokasi real-time</p>
        </div>
    @endif
</div>

<script>
    let map;
    let currentMarker;
    let pathPolyline;
    let historyMarkers = [];
    let refreshIntervalId;
    let mapRefreshIntervalId;
    let currentRefreshInterval = 5000;
    let selectedNik = {!! json_encode($selectedEmployee ? $selectedEmployee->nik : '') !!};
    const todayDate = new Date().toISOString().split('T')[0];

    let historyMap;
    let historyPathPolyline;
    let historyMapMarkers = [];
    let currentHistoryPage = 1;
    let currentFilterPage = 1;

    function createPulsingIcon() {
        return L.divIcon({
            className: 'pulsing-marker',
            html: '<div class="pulse"></div><div class="marker"></div>',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });
    }

    function initializeMap(lat, lng) {
        try {
            if (map) {
                map.remove();
            }
            map = L.map('map').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            if (selectedNik) {
                loadLocationHistory();
            }
        } catch (error) {
            console.error('Error initializing map:', error);
        }
    }

    function loadLocationHistory() {
        if (!selectedNik || !map) return;

        const startDate = todayDate;
        const endDate = todayDate;
        updateMonitoringDateLabel(startDate, endDate);

        fetch(`{{ route('monitoring-lokasi.getLocationHistory') }}?nik=${selectedNik}&start_date=${startDate}&end_date=${endDate}&order=asc&all=1`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(historyData => {
            if (!historyData.status || historyData.count === 0) return;

            historyMapMarkers.forEach(marker => map.removeLayer(marker));
            historyMapMarkers = [];
            if (historyPathPolyline) map.removeLayer(historyPathPolyline);

            const pathCoordinates = historyData.data.map(loc => [parseFloat(loc.latitude), parseFloat(loc.longitude)]);
            historyPathPolyline = L.polyline(pathCoordinates, {
                color: '#3388ff',
                weight: 3,
                opacity: 0.8,
                dashArray: '5, 5'
            }).addTo(map);

            historyData.data.forEach((loc, index) => {
                const marker = L.marker([parseFloat(loc.latitude), parseFloat(loc.longitude)], {
                    icon: L.divIcon({
                        className: 'history-marker',
                        html: '<div class="blue-circle"></div>',
                        iconSize: [12, 12],
                        iconAnchor: [6, 6]
                    })
                }).addTo(map).bindPopup(`<strong>Titik Historis ${index + 1}</strong><br>Waktu: ${loc.tracked_at}<br>Lokasi: ${loc.lokasi || '-'}`);
                historyMapMarkers.push(marker);
            });

            const lastLocation = historyData.data[historyData.data.length - 1];
            const latestMarker = L.marker([parseFloat(lastLocation.latitude), parseFloat(lastLocation.longitude)], {
                icon: createPulsingIcon(),
                zIndexOffset: 1000
            }).addTo(map).bindPopup(`<strong>Lokasi Terkini</strong><br>Waktu: ${lastLocation.tracked_at}<br>Lokasi: ${lastLocation.lokasi || '-'}`);
            historyMapMarkers.push(latestMarker);

            const bounds = L.featureGroup(historyMapMarkers).getBounds();
            if (bounds.isValid()) {
                map.fitBounds(bounds.pad(0.1));
            }
        })
        .catch(error => console.error('Error loading location history:', error));
    }

    function refreshLocation() {
        if (!selectedNik) {
            updateStatus('Pilih karyawan untuk memulai monitoring', 'info');
            return;
        }

        fetch(`{{ route('monitoring-lokasi.getData') }}?nik=${selectedNik}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 404) {
                    throw new Error('Data lokasi belum tersedia untuk karyawan ini.');
                } else if (response.status === 403) {
                    throw new Error('Anda tidak memiliki permission untuk mengakses data ini');
                } else {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            }
            return response.json();
        })
        .then(data => {
            if (data.status) {
                const location = data.latestLocation;
                if (document.getElementById('currentLatitude')) {
                    document.getElementById('currentLatitude').textContent = location.latitude;
                    document.getElementById('currentLongitude').textContent = location.longitude;
                    document.getElementById('currentLocation').textContent = location.lokasi || '-';
                    document.getElementById('currentAccuracy').textContent = location.accuracy ? location.accuracy.toFixed(2) + ' meters' : '-';
                    document.getElementById('currentTime').textContent = location.tracked_at;
                    document.getElementById('currentTimeAgo').textContent = location.time_ago;
                }
                updateStatus('Monitoring Aktif - Update: ' + location.time_ago, 'success');
                if (data.history && data.history.length > 0) {
                    loadLocationHistory();
                }
                updateEmployeeMonitoringStatus();
            } else {
                updateStatus(data.message || 'Terjadi kesalahan saat mengambil data', 'warning');
                updateEmployeeMonitoringStatus();
            }
        })
        .catch(error => {
            updateStatus(error.message || 'Terjadi kesalahan saat mengambil data lokasi', 'danger');
            updateEmployeeMonitoringStatus();
        });
    }

    function updateStatus(message, type = 'info') {
        const statusEl = document.getElementById('monitoringStatus');
        if (statusEl) {
            const icons = {
                'info': 'ti-info-circle',
                'success': 'ti-circle-filled',
                'warning': 'ti-alert-circle',
                'danger': 'ti-alert-triangle'
            };
            const colors = {
                'info': '#0284c7',
                'success': '#059669',
                'warning': '#d97706',
                'danger': '#e11d48'
            };
            const icon = icons[type] || icons['info'];
            const color = colors[type] || colors['info'];
            statusEl.innerHTML = `<i class="ti ${icon}" style="color: ${color};"></i> ${message}`;
        }
    }

    function updateEmployeeMonitoringStatus() {
        const statusEl = document.getElementById('employeeMonitoringStatus');
        const detailEl = document.getElementById('employeeMonitoringDetail');
        if (!statusEl || !detailEl) return;

        if (!selectedNik) {
            statusEl.className = 'px-2 py-0.5 rounded-md bg-slate-500 text-white text-[10.5px] font-bold';
            statusEl.textContent = 'Tidak Dipilih';
            detailEl.textContent = 'Pilih karyawan terlebih dahulu';
            return;
        }

        fetch(`{{ route('presensi.checkStatus') }}?nik=${selectedNik}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            const monitoringEnabled = {{ $generalSetting && $generalSetting->enable_live_location_monitoring ? 'true' : 'false' }};
            const monitoringMode = {{ $generalSetting ? $generalSetting->monitoring_live_location_mode : 0 }};

            if (!monitoringEnabled) {
                statusEl.className = 'px-2 py-0.5 rounded-md bg-rose-600 text-white text-[10.5px] font-bold';
                statusEl.textContent = 'Monitoring Non-Aktif';
                detailEl.textContent = 'Monitoring live location dinonaktifkan di pengaturan';
                return;
            }

            if (data.hasCheckedInToday) {
                statusEl.className = 'px-2 py-0.5 rounded-md bg-emerald-600 text-white text-[10.5px] font-bold';
                statusEl.textContent = monitoringMode == 0 ? 'Aktif 24 Jam' : 'Aktif (Jam Kerja)';
                detailEl.textContent = monitoringMode == 0 ? 'Tracking aktif terus menerus' : 'Tracking aktif karena sudah absen masuk';
            } else {
                if (monitoringMode == 0) {
                    statusEl.className = 'px-2 py-0.5 rounded-md bg-amber-500 text-white text-[10.5px] font-bold';
                    statusEl.textContent = 'Aktif 24 Jam';
                    detailEl.textContent = 'Tracking aktif meskipun belum absen masuk';
                } else {
                    statusEl.className = 'px-2 py-0.5 rounded-md bg-slate-500 text-white text-[10.5px] font-bold';
                    statusEl.textContent = 'Menunggu Absen Masuk';
                    detailEl.textContent = 'Tracking akan aktif setelah absen masuk';
                }
            }
        })
        .catch(() => {
            statusEl.className = 'px-2 py-0.5 rounded-md bg-amber-500 text-white text-[10.5px] font-bold';
            statusEl.textContent = 'Error';
            detailEl.textContent = 'Gagal memeriksa status';
        });
    }

    function updateMonitoringDateLabel(startDate, endDate) {
        const label = document.getElementById('monitoringDateLabel');
        if (!label) return;
        if (startDate && endDate) {
            label.textContent = startDate === endDate ? `Tanggal monitoring: ${startDate}` : `Tanggal monitoring: ${startDate} sampai ${endDate}`;
        } else if (startDate) {
            label.textContent = `Tanggal monitoring: ${startDate}`;
        } else {
            label.textContent = `Tanggal monitoring: ${todayDate}`;
        }
    }

    function changeRefreshInterval(interval) {
        currentRefreshInterval = parseInt(interval);
        if (refreshIntervalId) clearInterval(refreshIntervalId);
        startAutoRefresh();
    }

    function startAutoRefresh() {
        refreshLocation();
        refreshIntervalId = setInterval(() => refreshLocation(), currentRefreshInterval);
    }

    function startMapAutoRefresh() {
        mapRefreshIntervalId = setInterval(() => {
            if (selectedNik && map) loadLocationHistory();
        }, 300000);
    }

    function stopMonitoring() {
        if (refreshIntervalId) clearInterval(refreshIntervalId);
        if (mapRefreshIntervalId) clearInterval(mapRefreshIntervalId);
        document.getElementById('employeeSelect').value = '';
        window.location.href = '{{ route('monitoring-lokasi.index') }}';
    }

    function handleEmployeeChange(nik) {
        if (nik) {
            window.location.href = '{{ route('monitoring-lokasi.index') }}?nik=' + nik;
        }
    }

    function initializeHistoryMap(lat, lng) {
        try {
            if (historyMap) historyMap.remove();
            historyMap = L.map('historyMap').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(historyMap);
        } catch (error) {
            console.error('Error initializing history map:', error);
        }
    }

    function loadHistoryMap(page = 1) {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        if (!selectedNik || !startDate || !endDate) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih karyawan dan tanggal terlebih dahulu' });
            return;
        }
        updateMonitoringDateLabel(startDate, endDate);

        fetch(`{{ route('monitoring-lokasi.getLocationHistory') }}?nik=${selectedNik}&start_date=${startDate}&end_date=${endDate}&all=1&order=asc`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.status || data.count === 0) {
                Swal.fire({ icon: 'info', title: 'Tidak ada data', text: 'Tidak ada data lokasi untuk rentang tanggal yang dipilih' });
                return;
            }

            const locations = data.data;
            const firstLoc = locations[0];
            if (!historyMap) {
                initializeHistoryMap(parseFloat(firstLoc.latitude), parseFloat(firstLoc.longitude));
            } else {
                historyMap.setView([parseFloat(firstLoc.latitude), parseFloat(firstLoc.longitude)], 13);
            }

            historyMapMarkers.forEach(marker => historyMap.removeLayer(marker));
            historyMapMarkers = [];
            if (historyPathPolyline) historyMap.removeLayer(historyPathPolyline);

            const pathCoordinates = locations.map(loc => [parseFloat(loc.latitude), parseFloat(loc.longitude)]);
            historyPathPolyline = L.polyline(pathCoordinates, {
                color: '#28a745',
                weight: 4,
                opacity: 0.8
            }).addTo(historyMap);

            locations.forEach((loc, index) => {
                const marker = L.marker([parseFloat(loc.latitude), parseFloat(loc.longitude)], {
                    icon: L.divIcon({
                        className: 'history-marker-green',
                        html: '<div class="green-circle"></div>',
                        iconSize: [14, 14],
                        iconAnchor: [7, 7]
                    })
                }).addTo(historyMap).bindPopup(`<strong>Titik Histori ${index + 1}</strong><br>Waktu: ${loc.tracked_at}<br>Lokasi: ${loc.lokasi || '-'}`);
                historyMapMarkers.push(marker);
            });

            const lastLoc = locations[locations.length - 1];
            const latestMarker = L.marker([parseFloat(lastLoc.latitude), parseFloat(lastLoc.longitude)], {
                icon: createPulsingIcon(),
                zIndexOffset: 1000
            }).addTo(historyMap).bindPopup(`<strong>Lokasi Terakhir</strong><br>Waktu: ${lastLoc.tracked_at}<br>Lokasi: ${lastLoc.lokasi || '-'}`);
            historyMapMarkers.push(latestMarker);

            const group = new L.featureGroup(historyMapMarkers);
            if (group.getBounds().isValid()) {
                historyMap.fitBounds(group.getBounds().pad(0.1));
            }

            loadHistoryFilterTable(page, startDate, endDate);
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan saat memuat histori lokasi' });
        });
    }

    function clearHistoryMap() {
        if (historyMap) {
            historyMapMarkers.forEach(marker => historyMap.removeLayer(marker));
            historyMapMarkers = [];
            if (historyPathPolyline) {
                historyMap.removeLayer(historyPathPolyline);
                historyPathPolyline = null;
            }
        }
        document.getElementById('historyMapTableBody').innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Klik "Tampilkan di Peta" untuk melihat riwayat lokasi</td></tr>';
    }

    function updateHistoryTable(locations) {
        const tbody = document.getElementById('historyMapTableBody');
        tbody.innerHTML = '';
        if (locations && locations.length > 0) {
            locations.forEach(location => {
                const row = `
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2.5">${location.tracked_at}</td>
                        <td class="px-4 py-2.5 font-mono">${location.latitude}</td>
                        <td class="px-4 py-2.5 font-mono">${location.longitude}</td>
                        <td class="px-4 py-2.5">${location.lokasi || '-'}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Tidak ada data lokasi</td></tr>';
        }
    }

    function loadHistoryFilterTable(page = 1, startDate = null, endDate = null) {
        if (!selectedNik) return;
        currentFilterPage = page;
        const fromDate = startDate || document.getElementById('startDate').value;
        const toDate = endDate || document.getElementById('endDate').value;

        fetch(`{{ route('monitoring-lokasi.getLocationHistory') }}?nik=${selectedNik}&start_date=${fromDate}&end_date=${toDate}&page=${page}&per_page=5&order=desc`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (!data.status || data.count === 0) {
                document.getElementById('historyMapTableBody').innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400">Tidak ada data lokasi</td></tr>';
                renderHistoryFilterPagination(0, 0);
                return;
            }
            updateHistoryTable(data.data);
            renderHistoryFilterPagination(data.current_page, data.total_pages);
        });
    }

    function renderHistoryFilterPagination(currentPage, totalPages) {
        const paginationEl = document.getElementById('historyFilterPagination');
        if (!paginationEl || totalPages <= 1) {
            if (paginationEl) paginationEl.innerHTML = '';
            return;
        }
        let html = '<nav><ul class="pagination">';
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="loadHistoryFilterTable(${currentPage - 1})">Prev</a></li>`;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 2) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="loadHistoryFilterTable(${i})">${i}</a></li>`;
            }
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="loadHistoryFilterTable(${currentPage + 1})">Next</a></li>`;
        html += '</ul></nav>';
        paginationEl.innerHTML = html;
    }

    function loadHistoryTable(page = 1) {
        if (!selectedNik) return;
        currentHistoryPage = page;

        fetch(`{{ route('monitoring-lokasi.getLocationHistory') }}?nik=${selectedNik}&minutes=1440&page=${page}&per_page=5&order=desc`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('historyTableBody');
            tbody.innerHTML = '';
            if (data.status && data.count > 0) {
                data.data.forEach(location => {
                    const row = `
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-2.5">${location.tracked_at}</td>
                            <td class="px-4 py-2.5 font-mono">${location.latitude}</td>
                            <td class="px-4 py-2.5 font-mono">${location.longitude}</td>
                            <td class="px-4 py-2.5">${location.lokasi || '-'}</td>
                            <td class="px-4 py-2.5">${location.accuracy ? location.accuracy.toFixed(2) + 'm' : '-'}</td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">Belum ada data lokasi</td></tr>';
            }
            renderPagination(data.current_page, data.total_pages);
        });
    }

    function renderPagination(currentPage, totalPages) {
        const paginationEl = document.getElementById('historyPagination');
        if (!paginationEl || totalPages <= 1) {
            if (paginationEl) paginationEl.innerHTML = '';
            return;
        }
        let html = '<nav><ul class="pagination">';
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="loadHistoryTable(${currentPage - 1})">Prev</a></li>`;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 2) {
                html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="loadHistoryTable(${i})">${i}</a></li>`;
            }
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="javascript:void(0)" onclick="loadHistoryTable(${currentPage + 1})">Next</a></li>`;
        html += '</ul></nav>';
        paginationEl.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (selectedNik) {
            const initialLat = @if ($selectedEmployee && $selectedEmployee->latestLiveLocation) parseFloat('{{ $selectedEmployee->latestLiveLocation->latitude }}') @else -6.2088 @endif;
            const initialLng = @if ($selectedEmployee && $selectedEmployee->latestLiveLocation) parseFloat('{{ $selectedEmployee->latestLiveLocation->longitude }}') @else 106.8456 @endif;
            initializeMap(initialLat, initialLng);
            loadHistoryTable();
            updateEmployeeMonitoringStatus();
            startAutoRefresh();
            startMapAutoRefresh();
        } else {
            updateEmployeeMonitoringStatus();
        }
    });
</script>

<style>
    .pulsing-marker { position: relative; }
    .pulse {
        width: 30px; height: 30px; border-radius: 50%;
        background: rgba(255, 0, 0, 0.6); position: absolute;
        animation: pulse 2s infinite;
    }
    .marker {
        width: 20px; height: 20px; border-radius: 50%;
        background: red; position: absolute; top: 5px; left: 5px;
    }
    .history-marker .blue-circle {
        width: 12px; height: 12px; border-radius: 50%;
        background: #007bff; border: 2px solid white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    .history-marker-green .green-circle {
        width: 14px; height: 14px; border-radius: 50%;
        background: #28a745; border: 2px solid white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    @keyframes pulse {
        0% { transform: scale(0.5); opacity: 1; }
        100% { transform: scale(1.5); opacity: 0; }
    }
</style>
@endsection

