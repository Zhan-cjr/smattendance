@extends('v2.layouts.app')

@section('titlepage', 'Tracking Kunjungan')
@section('page_title', 'Peta Rute & Tracking Kunjungan')

@push('mystyle')
<style>
    @keyframes pingSlow {
        0% { transform: scale(1); opacity: 1; }
        70% { transform: scale(1.6); opacity: 0; }
        100% { transform: scale(1.8); opacity: 0; }
    }
    .custom-tracking-marker {
        position: relative;
    }
    .marker-pulse-ring {
        position: absolute;
        inset: -4px;
        border-radius: 9999px;
        animation: pingSlow 2.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        pointer-events: none;
    }
    /* Leaflet popup customization */
    .leaflet-popup-content-wrapper {
        border-radius: 1rem !important;
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.15), 0 8px 10px -6px rgba(15, 23, 42, 0.1) !important;
        border: 1px solid #f1f5f9 !important;
        padding: 0 !important;
        overflow: hidden !important;
    }
    .leaflet-popup-content {
        margin: 0 !important;
        line-height: normal !important;
    }
    .leaflet-popup-tip {
        box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.1) !important;
    }
</style>
@endpush

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $selectedKaryawan = $nik ? $karyawans->firstWhere('nik', $nik) : null;
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-route text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Tracking Rute Kunjungan</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Visualisasi titik kunjungan berurutan, estimasi jarak tempuh antar lokasi, dan rute perjalanan karyawan
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('kunjungan.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-map-pin text-sm"></i>
                    <span>Kunjungan</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Tracking</span>
            </nav>

            <!-- Quick Link to Data Kunjungan -->
            <div class="flex items-center gap-2">
                <a href="{{ route('kunjungan.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-300/90 text-slate-700 hover:bg-slate-50 font-bold rounded-xl text-xs shadow-2xs transition active:scale-95 cursor-pointer">
                    <i class="ti ti-list text-sm"></i>
                    <span>Daftar Kunjungan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ================= 2. FILTER BAR STANDAR V2 ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('tracking-kunjungan.index') }}" method="GET" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2.5 sm:gap-3 items-center">
                
                <!-- Pilih Karyawan (Wajib untuk tracking rute) -->
                <div class="md:col-span-6">
                    <select name="nik" id="filter_nik_tracking" class="w-full px-3 py-2.5 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium shadow-2xs focus:outline-none focus:border-emerald-600 transition cursor-pointer select2NikTrackingV2" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach ($karyawans as $karyawan)
                            <option value="{{ $karyawan->nik }}" {{ $nik == $karyawan->nik ? 'selected' : '' }}>
                                {{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Kunjungan -->
                <div class="md:col-span-4 relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-sm"></i>
                    </span>
                    <input type="text" 
                           id="filter_tanggal_tracking" 
                           name="tanggal" 
                           value="{{ $tanggal }}" 
                           placeholder="Pilih Tanggal"
                           class="w-full h-10 pl-9 pr-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-300/90 rounded-xl text-slate-800 font-medium outline-none transition cursor-pointer" 
                           required>
                </div>

                <!-- Tombol Action Cari & Reset -->
                <div class="md:col-span-2 flex items-center gap-1.5">
                    <button type="submit" 
                            class="w-full px-4 py-2.5 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background-color: {{ $themeColor1 }};">
                        <i class="ti ti-search text-sm"></i>
                        <span>Tracking</span>
                    </button>
                    @if($nik || request('tanggal'))
                        <a href="{{ route('tracking-kunjungan.index') }}" 
                           class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center gap-1 active:scale-95 shrink-0"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <!-- Information / Status Sub-bar -->
        <div class="mt-3.5 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
            <div class="text-slate-500 text-[11.5px]">
                @if($selectedKaryawan)
                    Tracking Karyawan: <strong class="text-slate-800 font-bold">{{ $selectedKaryawan->nama_karyawan }}</strong> ({{ $selectedKaryawan->nik }})
                    <span class="text-slate-300 mx-1.5">|</span>
                    Tanggal: <strong class="text-emerald-700 font-bold">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>
                    <span class="text-slate-300 mx-1.5">|</span>
                    Total Titik: <strong class="text-sky-700 font-bold">{{ $kunjungans->count() }} Lokasi</strong>
                @else
                    Silakan pilih karyawan dan tanggal di atas untuk memulai tracking rute
                @endif
            </div>
            <div class="text-slate-400 text-[11px] font-medium">
                Pembaruan koordinat rute real-time
            </div>
        </div>
    </div>

    <!-- ================= 3. MAP DISPLAY CONTAINER ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        
        <!-- Header Top Toolbar on Map Card -->
        <div class="px-5 py-3.5 bg-white border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-base font-bold shadow-2xs">
                    <i class="ti ti-map"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 mb-0">Visualisasi Peta Tracking Kunjungan</h3>
                    <p class="text-[11px] text-slate-500 mb-0">Klik pada marker untuk melihat rincian kunjungan dan foto dokumentasi</p>
                </div>
            </div>

            <!-- Legend Badges on Map Header -->
            @if($nik && $kunjungans->count() > 0)
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px]">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Titik Awal
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 border border-sky-200 font-bold text-[11px]">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span> Titik Kunjungan
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 font-bold text-[11px]">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Titik Akhir
                    </span>
                </div>
            @endif
        </div>

        <!-- Map Canvas -->
        <div class="relative w-full">
            @if ($nik)
                <div id="trackingMap" style="height: 520px; width: 100%; z-index: 1;"></div>
            @else
                <div class="py-24 px-4 text-center flex flex-col items-center justify-center min-h-[420px] bg-slate-50/50">
                    <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center text-3xl mb-3 shadow-2xs">
                        <i class="ti ti-user-search"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-800 mb-1">Pilih Karyawan Terlebih Dahulu</h4>
                    <p class="text-xs text-slate-500 max-w-sm mb-0">
                        Silakan pilih nama karyawan dan tentukan tanggal pada form filter di atas untuk menampilkan jejak rute kunjungan.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- ================= 4. TIMELINE / TABEL DATA TITIK KUNJUNGAN ================= -->
    @if ($nik)
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
            <!-- Table Header -->
            <div class="px-5 py-4 bg-white border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-base font-bold">
                        <i class="ti ti-timeline"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-0">Kronologi Titik Kunjungan</h3>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $kunjungans->count() }} Titik Terdata
                </span>
            </div>

            <!-- Table Body -->
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200/80">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">Urutan</th>
                            <th class="py-3 px-4">Deskripsi / Kegiatan</th>
                            <th class="py-3 px-4">Koordinat Lokasi</th>
                            <th class="py-3 px-4 text-center">Jarak dari Sebelumnya</th>
                            <th class="py-3 px-4 text-center">Durasi Selang</th>
                            <th class="py-3 px-4 text-center">Waktu Input</th>
                            <th class="py-3 px-4 text-center w-20">Foto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($kunjungans as $index => $item)
                            @php
                                $pathFoto = 'uploads/kunjungan/' . $item->foto;
                                $hasFoto = !empty($item->foto) && Storage::disk('public')->exists($pathFoto);
                                $fotoUrl = $hasFoto ? asset('storage/' . $pathFoto) : '';
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Urutan -->
                                <td class="py-3 px-4 text-center">
                                    <span class="w-6 h-6 rounded-lg inline-flex items-center justify-center font-bold text-[11px] {{ $loop->first ? 'bg-emerald-600 text-white shadow-2xs' : ($loop->last ? 'bg-rose-600 text-white shadow-2xs' : 'bg-sky-100 text-sky-800') }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>

                                <!-- Deskripsi -->
                                <td class="py-3 px-4">
                                    <span class="font-bold text-slate-900 block">{{ $item->deskripsi ?: '-' }}</span>
                                </td>

                                <!-- Lokasi -->
                                <td class="py-3 px-4">
                                    @if ($item->lokasi)
                                        <button type="button" 
                                                onclick="focusMarker({{ $item->latitude ?? 0 }}, {{ $item->longitude ?? 0 }}, {{ $index }})" 
                                                class="inline-flex items-center gap-1 font-mono text-[11px] font-bold text-sky-700 hover:text-sky-900 hover:underline cursor-pointer"
                                                title="Fokuskan peta ke titik ini">
                                            <i class="ti ti-map-pin text-xs"></i>
                                            <span>{{ $item->lokasi }}</span>
                                        </button>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <!-- Jarak -->
                                <td class="py-3 px-4 text-center">
                                    @if ($item->distance_from_previous)
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10.5px]">
                                            {{ $item->distance_from_previous }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">{{ $loop->first ? 'Titik Awal' : '-' }}</span>
                                    @endif
                                </td>

                                <!-- Durasi -->
                                <td class="py-3 px-4 text-center">
                                    @if ($item->duration_from_previous)
                                        <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 font-bold text-[10.5px]">
                                            {{ $item->duration_from_previous }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>

                                <!-- Waktu Input -->
                                <td class="py-3 px-4 text-center font-mono text-slate-600 text-[11px]">
                                    {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('H:i:s') : '-' }}
                                </td>

                                <!-- Foto Thumbnail -->
                                <td class="py-3 px-4 text-center">
                                    @if ($hasFoto)
                                        <img src="{{ $fotoUrl }}" 
                                             alt="Foto" 
                                             class="w-8 h-8 rounded-lg object-cover border border-slate-200 mx-auto cursor-pointer hover:scale-110 transition-transform shadow-2xs"
                                             onclick="showPreviewPhoto('{{ $fotoUrl }}', 'Foto Kunjungan #{{ $index + 1 }}')">
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <i class="ti ti-map-pin-off text-3xl block mb-2 opacity-50"></i>
                                    <p class="text-xs font-semibold text-slate-600 mb-0">Tidak ada data koordinat kunjungan untuk karyawan ini pada tanggal yang dipilih</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

<!-- Modal Preview Foto -->
<div class="modal fade" id="modalTrackingPhoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-2xl border-0 shadow-2xl overflow-hidden bg-white">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-base font-bold">
                        <i class="ti ti-camera"></i>
                    </div>
                    <h5 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight mb-0" id="modalTrackingPhotoTitle">Foto Kunjungan</h5>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-4 sm:p-6 bg-slate-900/5 text-center flex flex-col items-center justify-center min-h-[260px]">
                <img id="trackingPhotoImg" src="" alt="Foto Kunjungan" class="max-h-[420px] w-auto max-w-full rounded-xl object-contain shadow-lg">
            </div>
            <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex items-center justify-end">
                <button type="button" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition cursor-pointer" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
let trackingLeafletMap = null;
let markerInstances = [];

function showPreviewPhoto(src, title) {
    document.getElementById('modalTrackingPhotoTitle').textContent = title || 'Foto Kunjungan';
    document.getElementById('trackingPhotoImg').src = src;
    const modal = new bootstrap.Modal(document.getElementById('modalTrackingPhoto'));
    modal.show();
}

function focusMarker(lat, lng, index) {
    if (!trackingLeafletMap || !lat || !lng) return;
    trackingLeafletMap.setView([lat, lng], 17, { animate: true });
    if (markerInstances[index]) {
        markerInstances[index].openPopup();
    }
}

$(document).ready(function() {
    // 1. Flatpickr Datepicker Native
    if (typeof flatpickr !== 'undefined') {
        const elTgl = document.getElementById('filter_tanggal_tracking');
        if (elTgl) {
            flatpickr(elTgl, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y'
            });
        }
    }

    // 2. Select2 Karyawan
    $('.select2NikTrackingV2').select2({
        placeholder: '-- Pilih Karyawan --',
        allowClear: true,
        width: '100%'
    });

    @if ($nik)
        // 3. Initialize Tracking Map
        const mapContainer = document.getElementById('trackingMap');
        if (mapContainer) {
            trackingLeafletMap = L.map('trackingMap').setView([-7.3256, 108.2140], 13);

            // Base Tile Layers
            const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            });
            const googleStreets = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });
            const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            });

            osm.addTo(trackingLeafletMap);

            L.control.layers({
                "OpenStreetMap": osm,
                "Google Streets": googleStreets,
                "Google Satelit Hybrid": googleHybrid
            }).addTo(trackingLeafletMap);

            // Parse Kunjungan JSON Data
            const kunjunganData = @json($kunjungans);
            const bounds = [];
            const routeCoordinates = [];

            function calculateBearing(lat1, lng1, lat2, lng2) {
                const dLng = (lng2 - lng1) * Math.PI / 180;
                const lat1Rad = lat1 * Math.PI / 180;
                const lat2Rad = lat2 * Math.PI / 180;
                const y = Math.sin(dLng) * Math.cos(lat2Rad);
                const x = Math.cos(lat1Rad) * Math.sin(lat2Rad) - Math.sin(lat1Rad) * Math.cos(lat2Rad) * Math.cos(dLng);
                return (Math.atan2(y, x) * 180 / Math.PI + 360) % 360;
            }

            kunjunganData.forEach(function(item, index) {
                if (item.latitude && item.longitude) {
                    const isFirst = index === 0;
                    const isLast = index === (kunjunganData.length - 1) && kunjunganData.length > 1;

                    let bgGradient = 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)';
                    let pulseColor = 'rgba(2, 132, 199, 0.4)';
                    let badgeLabel = index + 1;

                    if (isFirst) {
                        bgGradient = 'linear-gradient(135deg, #10b981 0%, #047857 100%)';
                        pulseColor = 'rgba(16, 185, 129, 0.4)';
                    } else if (isLast) {
                        bgGradient = 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)';
                        pulseColor = 'rgba(239, 68, 68, 0.4)';
                    }

                    const customIcon = L.divIcon({
                        className: 'custom-tracking-marker',
                        html: `
                            <div style="background: ${bgGradient}; width: 36px; height: 36px; border-radius: 50%; border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 800; font-size: 13px; position: relative;">
                                <div class="marker-pulse-ring" style="background: ${pulseColor};"></div>
                                <span>${badgeLabel}</span>
                            </div>
                        `,
                        iconSize: [36, 36],
                        iconAnchor: [18, 18]
                    });

                    const marker = L.marker([item.latitude, item.longitude], { icon: customIcon }).addTo(trackingLeafletMap);
                    markerInstances.push(marker);

                    // Popup Content
                    const popupFoto = item.foto ? `
                        <div style="margin-top: 8px; border-radius: 8px; overflow: hidden; max-height: 140px;">
                            <img src="/storage/uploads/kunjungan/${item.foto}" style="width: 100%; height: 120px; object-fit: cover; border-radius: 8px; cursor: pointer;" onclick="showPreviewPhoto('/storage/uploads/kunjungan/${item.foto}', 'Kunjungan #${index + 1}')">
                        </div>
                    ` : '';

                    const popupContent = `
                        <div style="padding: 12px; width: 260px; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px; margin-bottom: 8px;">
                                <span style="font-size: 11px; font-weight: 800; color: #0f172a;">Titik #${index + 1} ${isFirst ? '(Mulai)' : (isLast ? '(Akhir)' : '')}</span>
                                <span style="font-size: 10.5px; font-weight: 700; color: #64748b; font-family: monospace;">${item.created_at ? item.created_at.substring(11, 16) : ''}</span>
                            </div>
                            <div style="font-size: 12px; color: #334155; line-height: 1.4; margin-bottom: 6px;">
                                ${item.deskripsi || '-'}
                            </div>
                            <div style="font-size: 10.5px; color: #64748b; font-family: monospace; background: #f8fafc; padding: 4px 6px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                <i class="ti ti-map-pin" style="color: #0284c7; margin-right: 2px;"></i> ${item.lokasi || '-'}
                            </div>
                            ${popupFoto}
                        </div>
                    `;

                    marker.bindPopup(popupContent);
                    bounds.push([item.latitude, item.longitude]);
                    routeCoordinates.push([item.latitude, item.longitude]);
                }
            });

            // Draw Route Polyline with Animated Direction Arrows
            if (routeCoordinates.length > 1) {
                L.polyline(routeCoordinates, {
                    color: '{{ $themeColor1 }}',
                    weight: 4,
                    opacity: 0.85,
                    dashArray: '10, 6',
                    lineCap: 'round',
                    lineJoin: 'round'
                }).addTo(trackingLeafletMap);

                // Add arrow markers along the segments
                for (let i = 0; i < routeCoordinates.length - 1; i++) {
                    const p1 = routeCoordinates[i];
                    const p2 = routeCoordinates[i + 1];
                    const midLat = (p1[0] + p2[0]) / 2;
                    const midLng = (p1[1] + p2[1]) / 2;
                    const bearing = calculateBearing(p1[0], p1[1], p2[0], p2[1]);

                    const arrowIcon = L.divIcon({
                        className: 'arrow-track-marker',
                        html: `<div style="transform: rotate(${bearing}deg); color: {{ $themeColor1 }}; font-size: 18px; font-weight: 900; text-shadow: 0 1px 3px rgba(0,0,0,0.3); pointer-events: none;">➔</div>`,
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    });

                    L.marker([midLat, midLng], { icon: arrowIcon }).addTo(trackingLeafletMap);
                }
            }

            // Fit Bounds
            if (bounds.length > 0) {
                trackingLeafletMap.fitBounds(bounds, { padding: [40, 40] });
            }
        }
    @endif
});
</script>
@endpush
