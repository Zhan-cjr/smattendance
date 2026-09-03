@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Monitoring Lokasi Real-Time</h5>
                        <small class="text-muted">Tracking lokasi karyawan secara live</small>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilih Karyawan</label>
                            <select id="employeeSelect" class="form-select" onchange="handleEmployeeChange(this.value)">
                                <option value="">-- Pilih Karyawan untuk Monitoring --</option>
                                @foreach ($spyEmployees as $employee)
                                    <option value="{{ $employee->nik }}" @if ($selectedNik == $employee->nik) selected @endif>
                                        {{ $employee->nama_karyawan }} - {{ $employee->jabatan->nama_jabatan ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status Monitoring</label>
                            <div class="alert alert-info mb-0">
                                <i class="tf-icons ti ti-info-circle"></i>
                                <span id="monitoringStatus">Pilih karyawan untuk memulai monitoring</span>
                            </div>
                        </div>
                    </div>

                    <!-- Monitoring Settings Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100 border shadow-sm">
                                <div class="card-body d-flex flex-column justify-content-between py-3">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <h6 class="mb-1 fw-bold">Live Location Monitoring</h6>
                                                <small class="text-muted">Status pengaturan monitoring</small>
                                            </div>
                                            <div class="text-end">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge {{ $generalSetting && $generalSetting->enable_live_location_monitoring ? 'bg-success' : 'bg-danger' }} me-2">
                                                        <i class="tf-icons ti {{ $generalSetting && $generalSetting->enable_live_location_monitoring ? 'ti-circle-check' : 'ti-circle-x' }}"></i>
                                                        {{ $generalSetting && $generalSetting->enable_live_location_monitoring ? 'AKTIF' : 'NON-AKTIF' }}
                                                    </span>
                                                </div>
                                                <small class="text-muted">
                                                    Mode: {{ $generalSetting && $generalSetting->monitoring_live_location_mode == 0 ? '24 Jam' : 'Jam Kerja' }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    @if($generalSetting && $generalSetting->enable_live_location_monitoring)
                                        <div class="mt-3">
                                            <small class="text-success">
                                                <i class="tf-icons ti ti-info-circle"></i>
                                                @if($generalSetting->monitoring_live_location_mode == 0)
                                                    Monitoring aktif 24/7
                                                @else
                                                    Monitoring aktif hanya saat jam kerja (setelah absen masuk, sebelum absen pulang)
                                                @endif
                                            </small>
                                        </div>
                                    @else
                                        <div class="mt-3">
                                            <small class="text-danger">
                                                <i class="tf-icons ti ti-alert-circle"></i>
                                                Monitoring live location dinonaktifkan
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border shadow-sm">
                                <div class="card-body d-flex flex-column justify-content-between py-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="mb-1 fw-bold">Karyawan "Mobile"</h6>
                                            <small class="text-muted">Total karyawan yang dimonitor</small>
                                        </div>
                                        <div class="text-end">
                                            <h4 class="mb-0 text-primary">{{ $spyEmployees->count() }}</h4>
                                            <small class="text-muted">orang</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($selectedEmployee)
                    <!-- Employee Info, Current Location & Controls Row -->
                    <div class="row mb-3">
                        <!-- Employee Info Card -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card monitoring-card h-100 border shadow-sm bg-white">
                                <div class="card-body py-2 px-3 position-relative">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <small class="text-uppercase text-secondary fw-semibold monitoring-card-title">Informasi Karyawan</small>
                                        </div>
                                        <div class="icon-circle bg-primary text-white">
                                            <i class="tf-icons ti ti-user fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="rounded-circle overflow-hidden border border-secondary" style="width:80px; height:80px;">
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
                                            <img src="{{ $selectedEmployeePhotoUrl }}" alt="Foto {{ $selectedEmployee->nama_karyawan }}" style="width:100%; height:100%; object-fit:cover;">
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-semibold">{{ $selectedEmployee->nama_karyawan }}</h6>
                                            <p class="text-muted small mb-0">{{ $selectedEmployee->jabatan->nama_jabatan ?? '-' }}</p>
                                            <p class="text-muted small mb-0">{{ $selectedEmployee->cabang->nama_cabang ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="row gx-2 gy-1">
                                        <div class="col-6">
                                            <p class="text-dark fw-semibold small mb-1">Nama</p>
                                            <p class="text-dark small mb-1">{{ $selectedEmployee->nama_karyawan }}</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-dark fw-semibold small mb-1">NIK</p>
                                            <p class="text-dark small mb-1">{{ $selectedEmployee->nik }}</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-dark fw-semibold small mb-1">Jabatan</p>
                                            <p class="text-dark small mb-1">{{ $selectedEmployee->jabatan->nama_jabatan ?? '-' }}</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-dark fw-semibold small mb-1">Departemen</p>
                                            <p class="text-dark small mb-1">{{ $selectedEmployee->departemen->nama_dept ?? '-' }}</p>
                                        </div>
                                        <div class="col-12">
                                            <p class="text-dark fw-semibold small mb-1">Cabang</p>
                                            <p class="text-dark small mb-0">{{ $selectedEmployee->cabang->nama_cabang ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Location Card -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card monitoring-card h-100 border shadow-sm bg-white">
                                <div class="card-body py-2 px-3 position-relative">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <small class="text-uppercase text-secondary fw-semibold monitoring-card-title">Lokasi Terkini</small>
                                        </div>
                                        <div class="icon-circle bg-success text-white">
                                            <i class="tf-icons ti ti-map-pin fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="row gx-2 gy-1">
                                        <div class="col-6">
                                            <p class="text-dark fw-semibold small mb-1">Latitude</p>
                                            <p class="text-dark small mb-2 font-monospace" id="currentLatitude">-</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-dark fw-semibold small mb-1">Longitude</p>
                                            <p class="text-dark small mb-2 font-monospace" id="currentLongitude">-</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-dark fw-semibold small mb-1">Akurasi GPS</p>
                                            <p class="text-dark small mb-2" id="currentAccuracy">-</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-dark fw-semibold small mb-1">Waktu Tracking</p>
                                            <p class="text-dark small mb-0" id="currentTime">-</p>
                                            <small class="text-muted d-block small" id="currentTimeAgo">-</small>
                                        </div>
                                        <div class="col-12">
                                            <p class="text-dark fw-semibold small mb-1">Nama Lokasi</p>
                                            <p class="text-dark small mb-0 text-truncate" id="currentLocation">-</p>
                                        </div>
                                    </div>
                                    <!-- Status Monitoring Karyawan -->
                                    <div class="mt-2 pt-2 border-top">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <small class="text-dark fw-semibold">Status Monitoring:</small>
                                            <span class="badge bg-secondary" id="employeeMonitoringStatus">Memeriksa...</span>
                                        </div>
                                        <small class="text-muted d-block mt-1" id="employeeMonitoringDetail">
                                            @if($generalSetting && $generalSetting->enable_live_location_monitoring)
                                                @if($generalSetting->monitoring_live_location_mode == 0)
                                                    Mode 24 jam aktif
                                                @else
                                                    Mode jam kerja aktif
                                                @endif
                                            @else
                                                Monitoring dinonaktifkan
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Refresh Controls Card -->
                        <div class="col-lg-4 col-md-12 mb-3">
                            <div class="card monitoring-card h-100 border shadow-sm bg-white">
                                <div class="card-body py-2 px-3 position-relative">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <small class="text-uppercase text-secondary fw-semibold monitoring-card-title">Kontrol Monitoring</small>
                                        </div>
                                        <div class="icon-circle bg-info text-white">
                                            <i class="tf-icons ti ti-settings fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <p class="text-dark fw-semibold small mb-1">Interval Auto-Refresh</p>
                                        <select id="refreshInterval" class="form-select form-select-sm" onchange="changeRefreshInterval(this.value)">
                                            <option value="3000">Setiap 3 Detik (Rekomendasi)</option>
                                            <option value="5000" selected>Setiap 5 Detik</option>
                                            <option value="10000">Setiap 10 Detik</option>
                                            <option value="30000">Setiap 30 Detik</option>
                                            <option value="60000">Setiap 1 Menit</option>
                                        </select>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-sm btn-primary" onclick="refreshLocation()">
                                            <i class="tf-icons ti ti-refresh"></i> Refresh Sekarang
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="stopMonitoring()">
                                            <i class="tf-icons ti ti-player-stop"></i> Hentikan Monitoring
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Section -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="mb-0">Peta Lokasi Real-Time</h6>
                                            <small class="text-muted" id="monitoringDateLabel">Tanggal monitoring: {{ date('Y-m-d') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="map" style="height: 400px; border-radius: 0 0 0.375rem 0.375rem;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location History Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Riwayat Lokasi (Last 24 Hours)</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="15%">Waktu</th>
                                                    <th width="20%">Latitude</th>
                                                    <th width="20%">Longitude</th>
                                                    <th width="30%">Lokasi</th>
                                                    <th width="15%">Akurasi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="historyTableBody">
                                                <!-- Data will be loaded via JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Pagination -->
                                    <div class="d-flex justify-content-center p-3" id="historyPagination">
                                        <!-- Pagination controls will be added here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Histori Lokasi dengan Filter Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Histori Lokasi dengan Filter</h6>
                                    <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#historyFilterCollapse" aria-expanded="false" aria-controls="historyFilterCollapse">
                                        <i class="tf-icons ti ti-filter"></i> Filter & Peta Histori
                                    </button>
                                </div>
                                <div class="collapse" id="historyFilterCollapse">
                                    <div class="card-body">
                                        <!-- Filter Form -->
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Tanggal Mulai</label>
                                                <input type="date" id="startDate" class="form-control" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Tanggal Akhir</label>
                                                <input type="date" id="endDate" class="form-control" value="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="col-md-4 d-flex align-items-end">
                                                <button class="btn btn-primary me-2" onclick="loadHistoryMap()">
                                                    <i class="tf-icons ti ti-map"></i> Tampilkan di Peta
                                                </button>
                                                <button class="btn btn-secondary" onclick="clearHistoryMap()">
                                                    <i class="tf-icons ti ti-x"></i> Bersihkan Peta
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">* Titik lokasi ditampilkan sesuai range tanggal yang dipilih</small>
                                        
                                        <!-- History Map -->
                                        <div class="mt-3">
                                            <div id="historyMap" style="height: 400px; border-radius: 0.375rem;"></div>
                                        </div>
                                        
                                        <!-- History Table -->
                                        <div class="mt-3">
                                            <h6 class="mb-2">Riwayat Lokasi (Sesuai Peta)</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th width="15%">Waktu</th>
                                                            <th width="20%">Latitude</th>
                                                            <th width="20%">Longitude</th>
                                                            <th width="45%">Lokasi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="historyMapTableBody">
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted py-3">
                                                                Klik "Tampilkan di Peta" untuk melihat riwayat lokasi
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="d-flex justify-content-center p-3" id="historyFilterPagination"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="tf-icons ti ti-map-search" style="font-size: 48px; color: #d0d0d0;"></i>
                        <h5 class="mt-3 text-muted">Pilih Karyawan untuk Monitoring</h5>
                        <p class="text-muted">Silakan pilih salah satu karyawan dari dropdown di atas untuk memulai monitoring lokasi real-time</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet-routing-machine.min.js"></script>

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

    // Create pulsing icon for current location
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

            // Current location marker (removed, now handled by history)
            // currentMarker = L.marker([lat, lng], {
            //     icon: L.icon({
            //         iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            //         shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            //         iconSize: [25, 41],
            //         iconAnchor: [12, 41],
            //         popupAnchor: [1, -34],
            //         shadowSize: [41, 41]
            //     })
            // }).addTo(map).bindPopup('<strong>Lokasi Terkini</strong><br>Update: <span id="markerTime">Memuat...</span>');

            // Draw path from history
            if (selectedNik) {
                loadLocationHistory();
            }
        } catch (error) {
            console.error('Error initializing map:', error);
        }
    }

    // Load location history and draw path for today's monitoring
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
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(historyData => {
            if (!historyData.status || historyData.count === 0) {
                console.warn('No today history location data');
                return;
            }

            // Remove existing history markers and path
            historyMapMarkers.forEach(marker => map.removeLayer(marker));
            historyMapMarkers = [];
            if (historyPathPolyline) {
                map.removeLayer(historyPathPolyline);
            }

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
        .catch(error => {
            console.error('Error loading location history:', error);
        });
    }

    // Refresh location data
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
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            if (!response.ok) {
                if (response.status === 404) {
                    throw new Error('Data lokasi belum tersedia untuk karyawan ini. Pastikan data lokasi sudah diinput/tracking via mobile app.');
                } else if (response.status === 403) {
                    throw new Error('Anda tidak memiliki permission untuk mengakses data ini');
                } else {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.status) {
                const location = data.latestLocation;
                
                // Update location display
                if (document.getElementById('currentLatitude')) {
                    document.getElementById('currentLatitude').textContent = location.latitude;
                    document.getElementById('currentLongitude').textContent = location.longitude;
                    document.getElementById('currentLocation').textContent = location.lokasi || '-';
                    document.getElementById('currentAccuracy').textContent = location.accuracy ? location.accuracy.toFixed(2) + ' meters' : '-';
                    document.getElementById('currentTime').textContent = location.tracked_at;
                    document.getElementById('currentTimeAgo').textContent = location.time_ago;
                }

                // Update marker position (removed, now handled by history reload)
                // const lat = parseFloat(location.latitude);
                // const lng = parseFloat(location.longitude);

                // if (map && currentMarker) {
                //     try {
                //         currentMarker.setLatLng([lat, lng]);
                //         currentMarker.getPopup().setContent(`<strong>Lokasi Terkini</strong><br>Update: ${location.tracked_at}`);
                //         map.setView([lat, lng], 16);
                //     } catch (e) {
                //         console.error('Error updating marker:', e);
                //         // Re-initialize map if there's an error
                //         initializeMap(lat, lng);
                //     }
                // }

                // Update marker time (removed)
                // if (document.getElementById('markerTime')) {
                //     document.getElementById('markerTime').textContent = location.time_ago;
                // }

                // Update status
                updateStatus('Monitoring Aktif - Update: ' + location.time_ago, 'success');

                // Update history if path exists
                if (data.history && data.history.length > 0) {
                    loadLocationHistory();
                }

                // Update employee monitoring status
                updateEmployeeMonitoringStatus();
            } else {
                updateStatus(data.message || 'Terjadi kesalahan saat mengambil data', 'warning');
                updateEmployeeMonitoringStatus();
            }
        })
        .catch(error => {
            console.error('Error fetching location:', error);
            updateStatus(error.message || 'Terjadi kesalahan saat mengambil data lokasi', 'danger');
            updateEmployeeMonitoringStatus();
        });
    }

    // Helper function to update status message
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
                'info': '#0dcaf0',
                'success': '#28a745',
                'warning': '#ffc107',
                'danger': '#dc3545'
            };
            const icon = icons[type] || icons['info'];
            const color = colors[type] || colors['info'];
            statusEl.innerHTML = `<i class="tf-icons ti ${icon}" style="color: ${color};"></i> ${message}`;
        }
    }

    // Update employee monitoring status based on presensi and settings
    function updateEmployeeMonitoringStatus() {
        if (!selectedNik) {
            document.getElementById('employeeMonitoringStatus').className = 'badge bg-secondary';
            document.getElementById('employeeMonitoringStatus').textContent = 'Tidak Dipilih';
            document.getElementById('employeeMonitoringDetail').textContent = 'Pilih karyawan terlebih dahulu';
            return;
        }

        // Check presensi status for selected employee
        fetch(`{{ route('presensi.checkStatus') }}?nik=${selectedNik}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            const statusEl = document.getElementById('employeeMonitoringStatus');
            const detailEl = document.getElementById('employeeMonitoringDetail');

            // Check if monitoring is enabled globally
            const monitoringEnabled = {{ $generalSetting && $generalSetting->enable_live_location_monitoring ? 'true' : 'false' }};
            const monitoringMode = {{ $generalSetting ? $generalSetting->monitoring_live_location_mode : 0 }};

            if (!monitoringEnabled) {
                statusEl.className = 'badge bg-danger';
                statusEl.textContent = 'Monitoring Non-Aktif';
                detailEl.textContent = 'Monitoring live location dinonaktifkan di pengaturan';
                return;
            }

            if (data.hasCheckedInToday) {
                if (monitoringMode == 0) {
                    // 24 jam mode
                    statusEl.className = 'badge bg-success';
                    statusEl.textContent = 'Aktif 24 Jam';
                    detailEl.textContent = 'Tracking aktif terus menerus';
                } else {
                    // Jam kerja mode
                    statusEl.className = 'badge bg-success';
                    statusEl.textContent = 'Aktif (Jam Kerja)';
                    detailEl.textContent = 'Tracking aktif karena sudah absen masuk';
                }
            } else {
                if (monitoringMode == 0) {
                    // 24 jam mode - still active even without check-in
                    statusEl.className = 'badge bg-warning';
                    statusEl.textContent = 'Aktif 24 Jam';
                    detailEl.textContent = 'Tracking aktif meskipun belum absen masuk';
                } else {
                    // Jam kerja mode - inactive until check-in
                    statusEl.className = 'badge bg-secondary';
                    statusEl.textContent = 'Menunggu Absen Masuk';
                    detailEl.textContent = 'Tracking akan aktif setelah absen masuk';
                }
            }
        })
        .catch(error => {
            console.error('Error checking presensi status:', error);
            document.getElementById('employeeMonitoringStatus').className = 'badge bg-warning';
            document.getElementById('employeeMonitoringStatus').textContent = 'Error';
            document.getElementById('employeeMonitoringDetail').textContent = 'Gagal memeriksa status';
        });
    }

    function updateMonitoringDateLabel(startDate, endDate) {
        const label = document.getElementById('monitoringDateLabel');
        if (!label) return;

        if (startDate && endDate) {
            label.textContent = startDate === endDate
                ? `Tanggal monitoring: ${startDate}`
                : `Tanggal monitoring: ${startDate} sampai ${endDate}`;
        } else if (startDate) {
            label.textContent = `Tanggal monitoring: ${startDate}`;
        } else {
            label.textContent = `Tanggal monitoring: ${todayDate}`;
        }
    }

    // Change refresh interval
    function changeRefreshInterval(interval) {
        currentRefreshInterval = parseInt(interval);
        if (refreshIntervalId) {
            clearInterval(refreshIntervalId);
        }
        startAutoRefresh();
    }

    // Start auto-refresh
    function startAutoRefresh() {
        refreshLocation(); // Initial call
        refreshIntervalId = setInterval(() => {
            refreshLocation();
        }, currentRefreshInterval);
    }

    // Start map auto-refresh (every 5 minutes)
    function startMapAutoRefresh() {
        mapRefreshIntervalId = setInterval(() => {
            if (selectedNik && map) {
                loadLocationHistory();
            }
        }, 300000); // 5 minutes = 300,000 milliseconds
    }

    // Stop monitoring
    function stopMonitoring() {
        if (refreshIntervalId) {
            clearInterval(refreshIntervalId);
        }
        if (mapRefreshIntervalId) {
            clearInterval(mapRefreshIntervalId);
        }
        document.getElementById('employeeSelect').value = '';
        window.location.href = '{{ route('monitoring-lokasi.index') }}';
    }

    // Handle employee change
    function handleEmployeeChange(nik) {
        if (nik) {
            window.location.href = '{{ route('monitoring-lokasi.index') }}?nik=' + nik;
        } else {
            // Reset status when no employee selected
            document.getElementById('employeeMonitoringStatus').className = 'badge bg-secondary';
            document.getElementById('employeeMonitoringStatus').textContent = 'Tidak Dipilih';
            document.getElementById('employeeMonitoringDetail').textContent = 'Pilih karyawan terlebih dahulu';
        }
    }

    // Filter locations by time interval
    function filterLocationsByInterval(locations, minutes) {
        if (!locations || locations.length === 0) return [];
        const intervalMs = minutes * 60 * 1000;
        const filtered = [locations[0]];
        let lastTime = new Date(locations[0].tracked_at).getTime();
        for (let i = 1; i < locations.length; i++) {
            const currentTime = new Date(locations[i].tracked_at).getTime();
            if (currentTime - lastTime >= intervalMs) {
                filtered.push(locations[i]);
                lastTime = currentTime;
            }
        }
        return filtered;
    }

    // Initialize history map
    function initializeHistoryMap(lat, lng) {
        try {
            if (historyMap) {
                historyMap.remove();
            }
            historyMap = L.map('historyMap').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(historyMap);
        } catch (error) {
            console.error('Error initializing history map:', error);
        }
    }

    // Load history map and paginated table for selected date range
    function loadHistoryMap(page = 1) {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        if (!selectedNik || !startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih karyawan dan tanggal terlebih dahulu'
            });
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
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.status || data.count === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak ada data',
                    text: 'Tidak ada data lokasi untuk rentang tanggal yang dipilih'
                });
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
            if (historyPathPolyline) {
                historyMap.removeLayer(historyPathPolyline);
            }

            const pathCoordinates = locations.map(loc => [parseFloat(loc.latitude), parseFloat(loc.longitude)]);
            historyPathPolyline = L.polyline(pathCoordinates, {
                color: '#28a745',
                weight: 4,
                opacity: 0.8,
                dashArray: null
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
        .catch(error => {
            console.error('Error loading history map:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memuat histori lokasi'
            });
        });
    }

    // Clear history map
    function clearHistoryMap() {
        if (historyMap) {
            historyMapMarkers.forEach(marker => historyMap.removeLayer(marker));
            historyMapMarkers = [];
            if (historyPathPolyline) {
                historyMap.removeLayer(historyPathPolyline);
                historyPathPolyline = null;
            }
        }
        // Clear table
        document.getElementById('historyMapTableBody').innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Klik "Tampilkan di Peta" untuk melihat riwayat lokasi</td></tr>';
    }

    // Update history table
    function updateHistoryTable(locations) {
        const tbody = document.getElementById('historyMapTableBody');
        tbody.innerHTML = '';

        if (locations && locations.length > 0) {
            locations.forEach((location, index) => {
                const row = `
                    <tr>
                        <td><small>${location.tracked_at}</small></td>
                        <td><small class="font-monospace">${location.latitude}</small></td>
                        <td><small class="font-monospace">${location.longitude}</small></td>
                        <td><small title="${location.lokasi}">${location.lokasi || '-'}</small></td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data lokasi</td></tr>';
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
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (!data.status || data.count === 0) {
                document.getElementById('historyMapTableBody').innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data lokasi</td></tr>';
                renderHistoryFilterPagination(0, 0);
                return;
            }

            updateHistoryTable(data.data);
            renderHistoryFilterPagination(data.current_page, data.total_pages);
        })
        .catch(error => {
            console.error('Error loading filter table:', error);
            document.getElementById('historyMapTableBody').innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Terjadi kesalahan saat memuat data</td></tr>';
            renderHistoryFilterPagination(0, 0);
        });
    }

    function renderHistoryFilterPagination(currentPage, totalPages) {
        const paginationEl = document.getElementById('historyFilterPagination');
        if (!paginationEl || totalPages <= 1) {
            if (paginationEl) paginationEl.innerHTML = '';
            return;
        }

        const pages = [];
        const maxPages = 7;
        const half = Math.floor(maxPages / 2);
        let start = Math.max(1, currentPage - half);
        let end = Math.min(totalPages, currentPage + half);

        if (totalPages <= maxPages) {
            start = 1;
            end = totalPages;
        } else if (currentPage - half <= 0) {
            start = 1;
            end = maxPages;
        } else if (currentPage + half > totalPages) {
            end = totalPages;
            start = totalPages - maxPages + 1;
        }

        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        let html = '<nav><ul class="pagination pagination-sm justify-content-center mb-0">';
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadHistoryFilterTable(${currentPage - 1})">Previous</a></li>`;

        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadHistoryFilterTable(1)">1</a></li>`;
            if (start > 2) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        pages.forEach(page => {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="loadHistoryFilterTable(${page})">${page}</a></li>`;
        });

        if (end < totalPages) {
            if (end < totalPages - 1) {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadHistoryFilterTable(${totalPages})">${totalPages}</a></li>`;
        }

        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="loadHistoryFilterTable(${currentPage + 1})">Next</a></li>`;
        html += '</ul></nav>';
        paginationEl.innerHTML = html;
    }

    // Load history table with pagination
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
                        <tr>
                            <td><small>${location.tracked_at}</small></td>
                            <td><small class="font-monospace">${location.latitude}</small></td>
                            <td><small class="font-monospace">${location.longitude}</small></td>
                            <td><small>${location.lokasi || '-'}</small></td>
                            <td><small>${location.accuracy ? location.accuracy.toFixed(2) + 'm' : '-'}</small></td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Belum ada data lokasi</td></tr>';
            }

            renderPagination(data.current_page, data.total_pages);
        })
        .catch(error => {
            console.error('Error loading history table:', error);
            document.getElementById('historyTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Terjadi kesalahan saat memuat data</td></tr>';
        });
    }

    // Render pagination controls
    function renderPagination(currentPage, totalPages) {
        const paginationEl = document.getElementById('historyPagination');
        if (totalPages <= 1) {
            paginationEl.innerHTML = '';
            return;
        }

        const maxPages = 10;
        const pages = [];

        if (totalPages <= maxPages) {
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            const half = Math.floor(maxPages / 2);
            let start = Math.max(1, currentPage - half);
            let end = Math.min(totalPages, currentPage + half);

            if (currentPage <= half) {
                start = 1;
                end = maxPages;
            } else if (currentPage + half > totalPages) {
                end = totalPages;
                start = totalPages - maxPages + 1;
            }

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            if (start > 1) {
                pages.unshift('...');
                pages.unshift(1);
            }
            if (end < totalPages) {
                pages.push('...');
                pages.push(totalPages);
            }
        }

        let html = '<nav><ul class="pagination pagination-sm justify-content-center mb-0">';
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadHistoryTable(${currentPage - 1})">Previous</a>
        </li>`;

        pages.forEach(page => {
            if (page === '...') {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            } else {
                html += `<li class="page-item ${page === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadHistoryTable(${page})">${page}</a>
                </li>`;
            }
        });

        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadHistoryTable(${currentPage + 1})">Next</a>
        </li>`;
        html += '</ul></nav>';
        paginationEl.innerHTML = html;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (selectedNik) {
            // Initialize map dengan lokasi default atau akan diperbarui saat refresh
            const initialLat = @if ($selectedEmployee && $selectedEmployee->latestLiveLocation) parseFloat('{{ $selectedEmployee->latestLiveLocation->latitude }}') @else -6.2088 @endif;
            const initialLng = @if ($selectedEmployee && $selectedEmployee->latestLiveLocation) parseFloat('{{ $selectedEmployee->latestLiveLocation->longitude }}') @else 106.8456 @endif;
            
            initializeMap(initialLat, initialLng);
            
            // Load history table
            loadHistoryTable();
            
            // Update employee monitoring status
            updateEmployeeMonitoringStatus();
            
            // Auto-start real-time refresh immediately
            startAutoRefresh();
            // Auto-start map refresh every 5 minutes
            startMapAutoRefresh();
        } else {
            // Update status for no employee selected
            updateEmployeeMonitoringStatus();
        }
    });
</script>

<style>
    .leaflet-popup-content {
        margin: 8px;
        min-width: 150px;
    }

    .leaflet-control-zoom {
        border: 1px solid #ccc;
    }

    .pulsing-marker {
        position: relative;
    }

    .pulse {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(255, 0, 0, 0.6);
        position: absolute;
        animation: pulse 2s infinite;
    }

    .marker {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: red;
        position: absolute;
        top: 5px;
        left: 5px;
    }

    .history-marker .blue-circle {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #007bff;
        border: 2px solid white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    .history-marker-green .green-circle {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #28a745;
        border: 2px solid white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    @keyframes pulse {
        0% {
            transform: scale(0.5);
            opacity: 1;
        }
        100% {
            transform: scale(1.5);
            opacity: 0;
        }
    }

    /* Compact card styling */
    .card-body.py-3 {
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }

    .card-body .mb-2 {
        margin-bottom: 0.5rem !important;
    }

    .card-body .mb-1 {
        margin-bottom: 0.25rem !important;
    }

    .card-body .mb-0 {
        margin-bottom: 0 !important;
    }

    .card-body p {
        line-height: 1.3;
    }

    .card-body small {
        line-height: 1.2;
    }

    .monitoring-card {
        border-radius: 1rem;
        overflow: hidden;
        border-color: #e7edf7 !important;
    }

    .monitoring-card .card-body {
        min-height: 160px;
        padding: 0.75rem !important;
    }

    .monitoring-card .card-body p,
    .monitoring-card .card-body small {
        line-height: 1.2;
    }
    .monitoring-card .card-body p {
        margin-bottom: 0.18rem;
    }

    .monitoring-card .card-body .mb-2 {
        margin-bottom: 0.5rem !important;
    }

    .monitoring-card .card-body .mb-0 {
        margin-bottom: 0 !important;
    }

    .monitoring-card .icon-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
    }

    .monitoring-card-title {
        font-size: 0.72rem;
        letter-spacing: 0.08em;
        color: #6c757d;
    }

    .monitoring-card .card-body p {
        color: #212529;
    }

    .monitoring-card .card-body .text-secondary {
        color: #6c757d !important;
    }

    /* Table styles for full location display */
    .table-responsive .table td {
        white-space: normal;
        word-wrap: break-word;
        vertical-align: top;
    }

    .table-responsive .table td small {
        word-wrap: break-word;
        white-space: normal;
    }
</style>

@endsection
