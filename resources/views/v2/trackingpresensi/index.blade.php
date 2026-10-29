@extends('v2.layouts.app')

@section('titlepage', 'Tracking Presensi GPS')
@section('page_title', 'Tracking Presensi GPS')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $selectedDate = !empty($tanggal) ? $tanggal : date('Y-m-d');
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMBS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-map-2 text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Tracking Presensi GPS</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Visualisasi pemetaan lokasi presensi real-time, sebaran marker foto selfie, dan radius kantor cabang
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Quick Date Info -->
        <div class="flex flex-col md:items-end gap-2">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-activity text-sm"></i>
                    <span>Presensi</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Tracking GPS</span>
            </nav>

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs">
                <i class="ti ti-calendar text-emerald-600"></i>
                <span>Tanggal: <strong class="text-slate-900">{{ DateToIndo($selectedDate) }}</strong></span>
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER & ACTION TOOLBAR (CONSISTENT WITH V2 STANDARD) ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form id="filterTrackingForm" class="w-full">
            <div class="flex flex-col lg:flex-row items-center gap-2.5 sm:gap-3 w-full">
                
                <!-- Tanggal Filter -->
                <div class="w-full sm:w-auto lg:w-56 shrink-0 relative">
                    <i class="ti ti-calendar absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base pointer-events-none z-10"></i>
                    <input type="text" 
                           name="tanggal" 
                           id="tanggal" 
                           value="{{ $tanggal }}" 
                           placeholder="Pilih Tanggal"
                           class="w-full pl-11 pr-3.5 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-semibold shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer">
                </div>

                <!-- Cabang Filter -->
                <div class="w-full sm:w-auto lg:w-60 shrink-0">
                    <select name="kode_cabang" id="kode_cabang" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition">
                        <option value="">Semua Cabang Kantor</option>
                        @foreach ($cabangs as $cabang)
                            <option value="{{ $cabang->kode_cabang }}">{{ $cabang->nama_cabang }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Spacer / Quick Stats Pills -->
                <div class="hidden xl:flex items-center gap-2 flex-1 justify-end">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-xs font-semibold">
                        <i class="ti ti-map-pin text-emerald-600 text-sm"></i>
                        <span>Total Marker: <strong class="text-slate-900" id="total-markers">{{ $presensis->count() }}</strong></span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-xs font-semibold">
                        <i class="ti ti-radar text-blue-600 text-sm"></i>
                        <span>Lokasi Unik: <strong class="text-slate-900" id="unique-locations">0</strong></span>
                    </div>
                </div>

                <!-- Action Buttons: Filter, Reset, Toggle Radius -->
                <div class="flex items-center gap-1.5 shrink-0 w-full lg:w-auto">
                    <button type="button" 
                            id="btn-filter"
                            class="w-full sm:w-auto px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-sm"></i>
                        <span>Terapkan</span>
                    </button>
                    
                    <button type="button" 
                            id="btn-reset"
                            class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1 cursor-pointer"
                            title="Reset Filter Tanggal">
                        <i class="ti ti-rotate text-sm"></i>
                    </button>

                    <button type="button" 
                            id="btn-toggle-radius"
                            class="px-3.5 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 text-xs font-bold rounded-xl transition inline-flex items-center gap-1.5 cursor-pointer whitespace-nowrap"
                            title="Tampilkan / Sembunyikan Lingkaran Radius Cabang">
                        <i class="ti ti-circle-check text-sm"></i>
                        <span id="radius-btn-text">Radius Aktif</span>
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- ================= 3. MAP CANVAS & FLOATING CONTROL PANEL ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden relative">
        <!-- Leaflet Map Container -->
        <div id="map_tracking" class="w-full h-[620px] min-h-[620px] z-0" style="height: 620px; min-height: 620px;"></div>

        <!-- Floating Legend & Quick Tips (Glassmorphism) -->
        <div class="absolute bottom-4 left-4 z-10 max-w-sm hidden sm:block pointer-events-auto">
            <div class="bg-white/95 backdrop-blur-md rounded-xl p-3 border border-slate-200/80 shadow-lg text-[11px] text-slate-600 space-y-1.5">
                <div class="flex items-center justify-between font-bold text-slate-800 pb-1 border-b border-slate-100">
                    <span class="flex items-center gap-1.5">
                        <i class="ti ti-info-circle text-emerald-600 text-xs"></i>
                        <span>Petunjuk Peta Presensi</span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono">GPS Tracking</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500 border-2 border-white shadow-2xs shrink-0"></span>
                    <span>Marker Biru: Lokasi presensi individual</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-rose-500 border-2 border-white shadow-2xs shrink-0"></span>
                    <span>Marker Merah: Titik koordinat berulang / ganda</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500/20 border border-emerald-500 shrink-0"></span>
                    <span>Lingkaran Hijau: Zona radius kantor cabang</span>
                </div>
                <p class="text-[10.5px] text-slate-400 pt-1 border-t border-slate-100 mb-0">
                    Klik marker untuk melihat detail & foto selfie. Klik area kosong peta untuk cek titik koordinat.
                </p>
            </div>
        </div>
    </div>

</div>

<!-- ================= 4. MODAL PREVIEW FOTO PRESENSI ================= -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-5 py-3.5 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <i class="ti ti-photo"></i>
                    </div>
                    <h5 class="modal-title text-sm font-bold text-slate-800 mb-0" id="imageModalTitle">Foto Presensi Karyawan</h5>
                </div>
                <button type="button" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer" data-bs-dismiss="modal">
                    <i class="ti ti-x text-base"></i>
                </button>
            </div>
            <div class="p-4 text-center bg-slate-50 flex items-center justify-center min-h-[260px]">
                <img id="modalImage" src="" alt="Foto Presensi" class="w-full max-h-[65vh] object-contain rounded-xl shadow-xs border border-slate-200">
            </div>
            <div class="px-5 py-3 bg-white border-t border-slate-100 flex items-center justify-between">
                <button type="button" class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer" data-bs-dismiss="modal">
                    Tutup
                </button>
                <a id="downloadImage" href="" download class="px-3.5 py-1.5 rounded-xl text-white text-xs font-bold transition flex items-center gap-1.5 shadow-2xs hover:opacity-95"
                   style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-download text-sm"></i>
                    <span>Unduh Foto</span>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<style>
    .custom-marker {
        background: transparent !important;
        border: none !important;
    }
    .custom-marker div {
        pointer-events: none;
    }
    .leaflet-marker-icon {
        z-index: 1000 !important;
    }
    .marker-label {
        font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
    }
    .custom-marker img {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        pointer-events: auto;
    }
    .custom-marker:hover img {
        transform: scale(1.15);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.4);
    }
    .leaflet-popup-content-wrapper {
        border-radius: 16px !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        padding: 0 !important;
        overflow: hidden;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
    }
    .leaflet-popup-content {
        margin: 0 !important;
        line-height: 1.4 !important;
        width: 270px !important;
    }
    .leaflet-popup-tip-container {
        margin-top: -1px;
    }
    .leaflet-popup-tip {
        background: white !important;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05) !important;
    }
    .leaflet-container a.leaflet-popup-close-button {
        top: 12px !important;
        right: 12px !important;
        width: 20px !important;
        height: 20px !important;
        border-radius: 6px !important;
        color: #94a3b8 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 14px !important;
        font-weight: normal !important;
        transition: all 0.15s ease !important;
        padding: 0 !important;
    }
    .leaflet-container a.leaflet-popup-close-button:hover {
        background: #f1f5f9 !important;
        color: #334155 !important;
    }
</style>

<script>
    (function() {
        function startTrackingMap() {
            if (typeof L === 'undefined' || typeof $ === 'undefined') {
                setTimeout(startTrackingMap, 50);
                return;
            }

            var mapEl = document.getElementById('map_tracking');
            if (!mapEl) {
                setTimeout(startTrackingMap, 50);
                return;
            }

            if (mapEl._leaflet_id) {
                mapEl._leaflet_id = null;
            }

            // Initialize Flatpickr for Date Input (Native JS call)
            if (typeof flatpickr !== 'undefined' && document.querySelector("#tanggal")) {
                flatpickr("#tanggal", {
                    locale: "id",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d F Y",
                    defaultDate: "{{ $tanggal }}"
                });
            }

            // Initialize Leaflet Map
            var map = L.map('map_tracking', {
                zoomControl: true
            }).setView([-6.2088, 106.8456], 11);

        // Tile Layers (HTTPS only)
        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        });

        var googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        var googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        var googleSat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
        });

        // Add default layer
        osm.addTo(map);

        // Layer Switcher Control
        var baseMaps = {
            "OpenStreetMap": osm,
            "Google Streets": googleStreets,
            "Google Hybrid": googleHybrid,
            "Google Satellite": googleSat
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

        // Ensure map renders properly after load
        setTimeout(function() {
            map.invalidateSize();
        }, 200);
        setTimeout(function() {
            map.invalidateSize();
        }, 500);

        // Click empty area on map to inspect coordinates
        map.on('click', function(e) {
            var lat = e.latlng.lat.toFixed(6);
            var lng = e.latlng.lng.toFixed(6);

            L.popup()
                .setLatLng(e.latlng)
                .setContent(`
                    <div class="p-3.5 text-center min-w-[210px] font-sans">
                        <div class="flex items-center justify-center gap-1.5 text-xs font-bold text-slate-800 mb-2">
                            <i class="ti ti-map-pin text-emerald-600"></i>
                            <span>Koordinat Titik Peta</span>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-200 text-xs font-mono text-slate-700 mb-2.5">
                            <div>Lat: <strong>${lat}</strong></div>
                            <div>Lng: <strong>${lng}</strong></div>
                        </div>
                        <div class="flex items-center justify-center gap-1.5">
                            <button class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer" onclick="copyToClipboard('${lat}, ${lng}')">
                                <i class="ti ti-copy text-xs"></i> Salin
                            </button>
                            <button class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition flex items-center gap-1 cursor-pointer" onclick="addCustomMarker(${lat}, ${lng})">
                                <i class="ti ti-plus text-xs"></i> Pasang Pin
                            </button>
                        </div>
                    </div>
                `)
                .openOn(map);
        });

        var markers = [];
        var radiusCircles = [];
        var customMarkers = [];
        var presensiData = @json($presensis);
        var cabangRadiusData = @json($cabangRadius);

        // Add Radius Circles to Map
        function addRadiusCirclesToMap(data) {
            radiusCircles.forEach(function(circle) {
                map.removeLayer(circle);
            });
            radiusCircles = [];

            if (data && data.length > 0) {
                data.forEach(function(cabang) {
                    if (cabang.latitude && cabang.longitude && cabang.radius_cabang) {
                        var circle = L.circle([cabang.latitude, cabang.longitude], {
                            color: '#10b981',
                            fillColor: '#10b981',
                            fillOpacity: 0.12,
                            radius: cabang.radius_cabang,
                            weight: 2
                        }).addTo(map);

                        circle.bindPopup(`
                            <div class="p-3.5 min-w-[220px] font-sans">
                                <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-800 pb-2 border-b border-slate-100">
                                    <i class="ti ti-building text-base text-emerald-600"></i>
                                    <span>Zona Kantor Cabang</span>
                                </div>
                                <div class="mt-2 space-y-1 text-xs">
                                    <div class="flex justify-between"><span class="text-slate-400">Cabang:</span><strong class="text-slate-800">${cabang.nama_cabang}</strong></div>
                                    <div class="flex justify-between"><span class="text-slate-400">Kode:</span><span class="font-mono text-slate-700">${cabang.kode_cabang}</span></div>
                                    <div class="flex justify-between"><span class="text-slate-400">Radius:</span><strong class="text-emerald-700">${cabang.radius_cabang} meter</strong></div>
                                </div>
                            </div>
                        `);

                        radiusCircles.push(circle);
                    }
                });
            }
        }

        // Center Map on Selected Cabang
        function centerMapOnCabang(cabangData) {
            if (cabangData && cabangData.length > 0) {
                if (cabangData.length === 1) {
                    var cabang = cabangData[0];
                    if (cabang.latitude && cabang.longitude) {
                        var zoomLevel = 15;
                        if (cabang.radius_cabang) {
                            if (cabang.radius_cabang <= 100) zoomLevel = 18;
                            else if (cabang.radius_cabang <= 500) zoomLevel = 16;
                            else if (cabang.radius_cabang <= 1000) zoomLevel = 14;
                            else zoomLevel = 12;
                        }

                        map.setView([cabang.latitude, cabang.longitude], zoomLevel);

                        setTimeout(function() {
                            radiusCircles.forEach(function(circle) {
                                if (circle.getLatLng().lat === cabang.latitude &&
                                    circle.getLatLng().lng === cabang.longitude) {
                                    circle.openPopup();
                                }
                            });
                        }, 400);
                    }
                } else {
                    var bounds = L.latLngBounds();
                    cabangData.forEach(function(cabang) {
                        if (cabang.latitude && cabang.longitude) {
                            bounds.extend([cabang.latitude, cabang.longitude]);
                        }
                    });

                    if (!bounds.isEmpty()) {
                        map.fitBounds(bounds, { padding: [30, 30] });
                    }
                }
            }
        }

        // Add Markers to Map
        function addMarkersToMap(data) {
            markers.forEach(function(marker) {
                map.removeLayer(marker);
            });
            markers = [];

            if (data.length === 0) {
                updateStatistics(0, 0, 0);
                return;
            }

            var bounds = L.latLngBounds();
            var uniqueLocations = new Set();
            var overlapCount = 0;

            data.forEach(function(presensi) {
                if (presensi.latitude && presensi.longitude) {
                    var isOverlap = presensi.marker_count > 1;
                    var markerBorderColor = isOverlap ? '#ef4444' : '#3b82f6';
                    var badgeBgColor = isOverlap ? '#ef4444' : '#10b981';
                    if (isOverlap) overlapCount++;

                    var coordKey = presensi.original_latitude + ',' + presensi.original_longitude;
                    uniqueLocations.add(coordKey);

                    var customIcon = L.divIcon({
                        className: 'custom-marker',
                        html: `
                            <div class="relative inline-block group">
                                <div class="relative w-11 h-11">
                                    <div class="w-11 h-11 rounded-full flex items-center justify-center font-black text-white text-xs shadow-md border-2 overflow-hidden relative"
                                         style="background-color: ${markerBorderColor}; border-color: white;">
                                        ${presensi.foto_in ? `
                                            <img src="/storage/uploads/absensi/${presensi.foto_in}"
                                                 class="absolute inset-0 w-full h-full object-cover cursor-pointer"
                                                 onclick="showImageModal('/storage/uploads/absensi/${presensi.foto_in}', 'Foto Masuk - ${presensi.nama_karyawan}')"
                                                 onerror="this.remove();">
                                        ` : ''}
                                        <span>${presensi.nama_karyawan.charAt(0).toUpperCase()}</span>
                                    </div>
                                    <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full flex items-center justify-center text-white text-[8px] ring-1.5 ring-white shadow-2xs"
                                         style="background-color: ${badgeBgColor};">
                                        <i class="ti ti-check"></i>
                                    </div>
                                </div>
                                <div class="marker-label absolute -top-7 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-md bg-slate-900/90 text-white text-[10px] font-bold whitespace-nowrap shadow-md pointer-events-none z-50">
                                    ${presensi.nama_karyawan}
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900/90"></div>
                                </div>
                            </div>
                        `,
                        iconSize: [44, 44],
                        iconAnchor: [22, 44]
                    });

                    var jamInShow = presensi.jam_in ? new Date(presensi.jam_in).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}).replace(/\./g, ':') : '-';
                    var jamOutShow = presensi.jam_out ? new Date(presensi.jam_out).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}).replace(/\./g, ':') : '-';

                    var marker = L.marker([presensi.latitude, presensi.longitude], {
                        icon: customIcon
                    }).addTo(map).bindPopup(`
                        <div class="font-sans bg-white p-3.5 space-y-3">
                            
                            <!-- Header Identitas Karyawan (Clean & Modern) -->
                            <div class="pr-5 pb-2.5 border-b border-slate-100 flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h6 class="text-xs font-bold text-slate-900 truncate mb-1">${presensi.nama_karyawan}</h6>
                                    <div class="flex items-center gap-1.5 text-[10px]">
                                        <span class="font-mono font-medium text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded border border-slate-200">${presensi.nik}</span>
                                        <span class="font-medium text-slate-500 flex items-center gap-0.5">
                                            <i class="ti ti-building text-slate-400 text-xs"></i> ${presensi.nama_cabang}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Waktu Masuk & Pulang (Dengan Icon In & Out + Warna Hijau/Merah) -->
                            <div class="grid grid-cols-2 gap-2">
                                <!-- Box Masuk (Hijau) -->
                                <div class="p-2 rounded-xl bg-emerald-50/70 border border-emerald-100/90 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs shrink-0 font-bold">
                                        <i class="ti ti-login"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[9px] font-bold text-emerald-600 block uppercase leading-none">Masuk</span>
                                        <span class="font-bold text-xs ${presensi.jam_in ? 'text-emerald-800 font-mono' : 'text-slate-400'} block mt-0.5 leading-none">
                                            ${jamInShow}
                                        </span>
                                    </div>
                                </div>

                                <!-- Box Pulang (Merah) -->
                                <div class="p-2 rounded-xl bg-rose-50/70 border border-rose-100/90 flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-xs shrink-0 font-bold">
                                        <i class="ti ti-logout"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[9px] font-bold text-rose-600 block uppercase leading-none">Pulang</span>
                                        <span class="font-bold text-xs ${presensi.jam_out ? 'text-rose-800 font-mono' : 'text-slate-400'} block mt-0.5 leading-none">
                                            ${jamOutShow}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            ${presensi.marker_count > 1 ? `
                                <div class="text-[10px] font-medium text-amber-800 bg-amber-50 px-2 py-1 rounded-lg border border-amber-200 flex items-center gap-1.5">
                                    <i class="ti ti-alert-triangle text-xs text-amber-600"></i>
                                    <span>${presensi.marker_count} karyawan pada titik ini</span>
                                </div>
                            ` : ''}

                            <!-- Foto Presensi Masuk & Pulang (Dengan Icon & Header Berwarna) -->
                            <div class="grid grid-cols-2 gap-2 pt-0.5">
                                <!-- Foto Masuk -->
                                <div>
                                    <span class="text-[9.5px] font-bold text-emerald-700 flex items-center gap-1 mb-1">
                                        <i class="ti ti-login text-[11px]"></i> Foto Masuk
                                    </span>
                                    ${presensi.foto_in ? `
                                        <div class="relative w-full h-20 rounded-lg overflow-hidden border border-emerald-200/80 shadow-2xs hover:border-emerald-500 transition cursor-pointer"
                                             onclick="showImageModal('/storage/uploads/absensi/${presensi.foto_in}', 'Foto Masuk - ${presensi.nama_karyawan}')">
                                            <img src="/storage/uploads/absensi/${presensi.foto_in}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    ` : `
                                        <div class="w-full h-20 bg-slate-50 border border-dashed border-slate-200 rounded-lg flex flex-col items-center justify-center text-slate-400">
                                            <i class="ti ti-photo-off text-base text-slate-300 mb-0.5"></i>
                                            <span class="text-[9px]">Kosong</span>
                                        </div>
                                    `}
                                </div>

                                <!-- Foto Pulang -->
                                <div>
                                    <span class="text-[9.5px] font-bold text-rose-700 flex items-center gap-1 mb-1">
                                        <i class="ti ti-logout text-[11px]"></i> Foto Pulang
                                    </span>
                                    ${presensi.foto_out ? `
                                        <div class="relative w-full h-20 rounded-lg overflow-hidden border border-rose-200/80 shadow-2xs hover:border-rose-500 transition cursor-pointer"
                                             onclick="showImageModal('/storage/uploads/absensi/${presensi.foto_out}', 'Foto Pulang - ${presensi.nama_karyawan}')">
                                            <img src="/storage/uploads/absensi/${presensi.foto_out}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    ` : `
                                        <div class="w-full h-20 bg-slate-50 border border-dashed border-slate-200 rounded-lg flex flex-col items-center justify-center text-slate-400">
                                            <i class="ti ti-photo-off text-base text-slate-300 mb-0.5"></i>
                                            <span class="text-[9px]">Kosong</span>
                                        </div>
                                    `}
                                </div>
                            </div>

                        </div>
                    `);

                    markers.push(marker);
                    bounds.extend([presensi.latitude, presensi.longitude]);
                }
            });

            updateStatistics(markers.length, uniqueLocations.size, overlapCount);

            if (markers.length > 0) {
                map.fitBounds(bounds, { padding: [40, 40] });
            }
        }

        function updateStatistics(totalMarkers, uniqueLocations, overlapLocations) {
            $('#total-markers').text(totalMarkers);
            $('#unique-locations').text(uniqueLocations);
        }

        // Global function: Show Image Modal
        window.showImageModal = function(imageSrc, title) {
            $('#imageModalTitle').text(title);
            $('#modalImage').attr('src', imageSrc);
            $('#downloadImage').attr('href', imageSrc);
            $('#imageModal').modal('show');
        };

        // Global function: Copy Clipboard
        window.copyToClipboard = function(text) {
            navigator.clipboard.writeText(text).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disalin!',
                    text: 'Koordinat lokasi telah disalin ke clipboard: ' + text,
                    timer: 1800,
                    showConfirmButton: false
                });
            });
        };

        // Global function: Add Custom Marker
        window.addCustomMarker = function(lat, lng) {
            var customPinIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-gold.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34]
            });

            var marker = L.marker([lat, lng], { icon: customPinIcon }).addTo(map)
                .bindPopup(`
                    <div class="p-3 text-center min-w-[180px] font-sans">
                        <span class="text-xs font-bold text-amber-800 block mb-1">📍 Custom Pin Marker</span>
                        <div class="text-[11px] font-mono text-slate-500 mb-2">${lat}, ${lng}</div>
                        <button class="px-2.5 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-bold transition cursor-pointer" onclick="removeCustomMarker(${customMarkers.length})">
                            Hapus Pin
                        </button>
                    </div>
                `);

            customMarkers.push(marker);
            Swal.fire({
                icon: 'success',
                title: 'Pin Ditambahkan!',
                text: 'Pin koordinat kustom berhasil dipasang di peta.',
                timer: 1500,
                showConfirmButton: false
            });
        };

        // Global function: Remove Custom Marker
        window.removeCustomMarker = function(index) {
            if (customMarkers[index]) {
                map.removeLayer(customMarkers[index]);
                customMarkers.splice(index, 1);
            }
        };

        // Initialize Map Content
        addMarkersToMap(presensiData);
        addRadiusCirclesToMap(cabangRadiusData);

        // Filter Action
        $('#btn-filter').click(function() {
            var tanggal = $('#tanggal').val();
            var kode_cabang = $('#kode_cabang').val();

            if (!tanggal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Silakan pilih tanggal terlebih dahulu',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Memuat Data GPS...',
                text: 'Mengambil plotting presensi & koordinat cabang',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route('trackingpresensi.getData') }}',
                type: 'GET',
                data: {
                    tanggal: tanggal,
                    kode_cabang: kode_cabang
                },
                success: function(response) {
                    Swal.close();
                    addMarkersToMap(response.presensis);
                    addRadiusCirclesToMap(response.cabangRadius);

                    if (kode_cabang) {
                        centerMapOnCabang(response.cabangRadius);
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memuat data tracking presensi',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Reset Filter
        $('#btn-reset').click(function() {
            $('#tanggal').val('{{ date('Y-m-d') }}');
            $('#kode_cabang').val('');
            $('#btn-filter').click();
        });

        // Toggle Radius
        var radiusVisible = true;
        $('#btn-toggle-radius').click(function() {
            if (radiusVisible) {
                radiusCircles.forEach(function(circle) {
                    map.removeLayer(circle);
                });
                $('#radius-btn-text').text('Radius Nonaktif');
                $(this).removeClass('bg-emerald-50 text-emerald-700 border-emerald-200/80')
                       .addClass('bg-slate-100 text-slate-600 border-slate-200');
                radiusVisible = false;
            } else {
                addRadiusCirclesToMap(cabangRadiusData);
                $('#radius-btn-text').text('Radius Aktif');
                $(this).removeClass('bg-slate-100 text-slate-600 border-slate-200')
                       .addClass('bg-emerald-50 text-emerald-700 border-emerald-200/80');
                radiusVisible = true;
            }
        });

        // Auto pan on Cabang Change
        $('#kode_cabang').change(function() {
            var selectedCabang = $(this).val();
            if (selectedCabang) {
                var selectedCabangData = cabangRadiusData.filter(function(cabang) {
                    return cabang.kode_cabang === selectedCabang;
                });
                if (selectedCabangData.length > 0) {
                    centerMapOnCabang(selectedCabangData);
                }
            } else {
                map.setView([-6.2088, 106.8456], 11);
            }
        });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startTrackingMap);
        } else {
            startTrackingMap();
        }
    })();
</script>
@endpush
