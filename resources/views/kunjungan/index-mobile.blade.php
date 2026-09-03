@extends('layouts.mobile.modern')

@section('title', 'Kunjungan Saya')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@section('header_right')
    <a href="{{ route('kunjungan.export.pdf', request()->query()) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all" target="_blank">
        <ion-icon name="document-text-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            background: #f0fdf9 !important;
        }

        /* Timeline Centered Redesign */
        .timeline-modern {
            position: relative;
            padding: 30px 0;
            margin-top: 10px;
            overflow: hidden;
        }

        .timeline-modern::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #32745e 0%, rgba(50, 116, 94, 0.1) 100%);
            transform: translateX(-50%);
            border-radius: 2px;
            z-index: 1;
        }

        .timeline-item-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 25px;
            display: flex;
            justify-content: flex-end; /* Default Right */
        }

        .timeline-item-wrapper.left {
            justify-content: flex-start;
        }

        .timeline-card {
            position: relative;
            width: 45%;
            background: #ffffff;
            border-radius: 16px;
            padding: 10px;
            box-shadow: 0 4px 15px rgba(50, 116, 94, 0.08);
            border: 1px solid rgba(50, 116, 110, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 2;
        }

        .timeline-item-wrapper.right .timeline-card {
            margin-right: 5px;
        }
        
        .timeline-item-wrapper.left .timeline-card {
            margin-left: 5px;
        }

        .timeline-card:active {
            transform: scale(0.98);
        }

        .timeline-dot {
            position: absolute;
            left: 50%;
            top: 20px;
            width: 16px;
            height: 16px;
            background: #ffffff;
            border: 4px solid #32745e;
            border-radius: 50%;
            z-index: 3;
            transform: translateX(-50%);
            box-shadow: 0 0 0 4px rgba(50, 116, 94, 0.15);
        }

        /* Card Content Styling */
        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .timeline-time {
            font-size: 11px;
            font-weight: 800;
            color: #32745e;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .timeline-date {
            font-size: 9px;
            font-weight: 600;
            color: #6a9c89;
        }

        .timeline-body {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .img-container {
            width: 100%;
            aspect-ratio: 1/1;
            border-radius: 12px;
            overflow: hidden;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #f1f5f9;
        }

        .timeline-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .no-img-icon {
            font-size: 32px;
            color: #cbd5e1;
        }

        .timeline-info {
            flex: 1;
            min-width: 0;
        }

        .timeline-desc {
            font-size: 12px;
            color: #334155;
            line-height: 1.4;
            margin-bottom: 4px;
            font-weight: 500;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .timeline-loc {
            font-size: 10px;
            font-weight: 600;
            color: #32745e;
            display: flex;
            align-items: center;
            gap: 3px;
            margin-top: 2px;
            text-decoration: none;
        }

        .timeline-loc:active {
            opacity: 0.7;
            text-decoration: underline;
        }

        .timeline-loc span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .delete-btn-modern {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            background: #ef4444;
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
            border: 2px solid #fff;
            transition: all 0.2s;
            z-index: 10;
        }

        .delete-btn-modern:active {
            transform: scale(0.9);
        }

        /* FAB Button */
        .fab-modern {
            position: fixed;
            bottom: 110px;
            right: 20px;
            width: 56px;
            height: 56px;
            background: #32745e;
            color: white;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            box-shadow: 0 8px 25px rgba(50, 116, 94, 0.3);
            z-index: 100;
            transition: all 0.3s;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6a9c89;
        }

        /* Modal Redesign - High End */
        #visitDetailModal {
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        #visitDetailModal.show {
            visibility: visible;
            opacity: 1 !important;
        }

        .modal-card {
            background: #ffffff;
            border-radius: 28px;
            width: 92%;
            max-width: 380px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            opacity: 0;
            background: #fff;
        }

        #visitDetailModal.show .modal-card {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .modal-header-modern {
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .modal-title-modern {
            font-size: 16px;
            font-weight: 800;
            color: #1e293b;
        }

        .modal-close-icon {
            font-size: 24px;
            color: #94a3b8;
            cursor: pointer;
        }

        .modal-body-modern {
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }

        /* Embedded Map */
        .modal-map-container {
            width: 100%;
            height: 180px;
            border-radius: 18px;
            margin-bottom: 16px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background: #f1f5f9;
            position: relative;
        }

        #visitMap {
            width: 100%;
            height: 100%;
            z-index: 10;
        }

        .map-refresh-btn {
            position: absolute;
            bottom: 8px;
            right: 8px;
            z-index: 20;
            background: #fff;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            color: #32745e;
            font-size: 16px;
            border: none;
        }

        /* Fix Leaflet tile leakage/misalignment */
        .leaflet-container img.leaflet-tile {
            max-width: none !important;
            max-height: none !important;
            box-shadow: none !important;
            visibility: inherit !important;
        }
        
        .leaflet-container {
            cursor: grab !important;
        }

        .modal-img-container {
            width: 100%;
            border-radius: 18px;
            overflow: hidden;
            margin-bottom: 16px;
            border: 1px solid #f1f5f9;
            background: #f8fafc;
        }

        .modal-img-full {
            width: 100%;
            height: auto;
            max-height: 180px;
            object-fit: cover;
        }

        .modal-info-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .info-icon-box {
            width: 34px;
            height: 34px;
            background: rgba(50, 116, 94, 0.08);
            color: #32745e;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }

        .info-value {
            font-size: 13px;
            color: #1e293b;
            font-weight: 600;
            line-height: 1.4;
        }

        .modal-footer-modern {
            padding: 12px 20px 20px;
        }

        .btn-close-modern {
            width: 100%;
            padding: 12px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            border: none;
        }
    </style>
@endpush

@section('content')
    <div class="fade-up px-1">
        {{-- ===== FILTER (Same as Histori) ===== --}}
        <form method="GET" action="{{ route('kunjungan.index') }}" id="formHistori">
            <div class="mt-1 mb-4 rounded-xl overflow-hidden border"
                 style="background: #fff; border-color: #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
                {{-- Filter Header --}}
                <div class="flex items-center gap-2 px-3 py-2" style="border-bottom: 1px solid #f1f5f9;">
                    <div class="w-6 h-6 rounded flex items-center justify-center" style="background: {{ $t['primary'] }}15;">
                        <ion-icon name="calendar-outline" class="text-[12px]" style="color: {{ $t['primary'] }};"></ion-icon>
                    </div>
                    <span class="text-[12px] font-semibold" style="color: #475569;">Pilih Rentang Tanggal</span>
                </div>
                {{-- Filter Inputs --}}
                <div class="px-3 py-2.5">
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <input type="text" name="tanggal_awal" id="tanggal_awal" 
                                class="w-full rounded-lg py-1.5 px-3 text-[12px] font-medium text-center focus:outline-none transition-all"
                                style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;"
                                placeholder="Dari" value="{{ Request('tanggal_awal', date('Y-m-d')) }}" autocomplete="off" required readonly>
                        </div>
                        <div class="flex-shrink-0 w-4 flex items-center justify-center">
                            <div class="w-2 h-[1px]" style="background: #cbd5e1;"></div>
                        </div>
                        <div class="flex-1">
                            <input type="text" name="tanggal_akhir" id="tanggal_akhir" 
                                class="w-full rounded-lg py-1.5 px-3 text-[12px] font-medium text-center focus:outline-none transition-all"
                                style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;"
                                placeholder="Sampai" value="{{ Request('tanggal_akhir', date('Y-m-d')) }}" autocomplete="off" required readonly>
                        </div>
                        <button type="submit" id="btnCari"
                            class="flex-shrink-0 w-9 h-8 rounded-lg text-white flex items-center justify-center active:scale-90 transition-transform"
                            style="background: {{ $t['primary'] }};">
                            <ion-icon name="search-outline" class="text-base"></ion-icon>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Timeline List --}}
        <h2 class="px-1 text-[11px] font-bold text-[#32745e] uppercase tracking-widest mb-2 opacity-80">Aktivitas Kunjungan</h2>
        
        <div class="timeline-modern">
            @if ($kunjungan->count() > 0)
                @foreach ($kunjungan as $index => $item)
                    @php
                        $isLeft = $index % 2 == 0;
                        $photoUrl = null;
                        if ($item->foto) {
                            $checkPath = public_path('storage/uploads/kunjungan/' . $item->foto);
                            if (file_exists($checkPath)) {
                                $photoUrl = asset('storage/uploads/kunjungan/' . $item->foto);
                            }
                        }
                    @endphp

                    <div class="timeline-item-wrapper {{ $isLeft ? 'left' : 'right' }}">
                        <div class="timeline-dot"></div>
                        
                        <div class="timeline-card" onclick="showVisitDetail({{ $item->id }}, '{{ addslashes($item->deskripsi) }}', '{{ $item->tanggal_kunjungan->format('d M Y') }}', '{{ $item->created_at->format('H:i') }}', '{{ addslashes($item->lokasi) }}', '{{ $photoUrl }}')">
                            
                            {{-- Admin Actions - Cleaned Up --}}
                            <div class="absolute" style="top: -8px; {{ $isLeft ? 'right: -8px;' : 'left: -8px;' }}" onclick="event.stopPropagation()">
                                <form method="POST" action="{{ route('kunjungan.destroy', $item) }}" class="inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn-modern">
                                        <ion-icon name="trash-outline" class="text-[12px]"></ion-icon>
                                    </button>
                                </form>
                            </div>

                            <div class="timeline-header">
                                <div class="timeline-time">
                                    <ion-icon name="time-outline"></ion-icon>
                                    <span>{{ $item->created_at->format('H:i') }}</span>
                                </div>
                                <div class="timeline-date">{{ $item->tanggal_kunjungan->format('d M Y') }}</div>
                            </div>

                            <div class="timeline-body">
                                <div class="img-container">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" class="timeline-img" alt="Foto">
                                    @else
                                        <ion-icon name="image-outline" class="no-img-icon"></ion-icon>
                                    @endif
                                </div>
                                <div class="timeline-info">
                                    <div class="timeline-desc">{{ $item->deskripsi }}</div>
                                    @if($item->lokasi)
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $item->lokasi }}" target="_blank" class="timeline-loc" onclick="event.stopPropagation()">
                                            <ion-icon name="map-outline"></ion-icon>
                                            <span>{{ $item->lokasi }}</span>
                                        </a>
                                    @else
                                        <div class="timeline-loc opacity-50">
                                            <ion-icon name="location-outline"></ion-icon>
                                            <span>No Location</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <ion-icon name="map-outline" class="text-4xl mb-2 opacity-50"></ion-icon>
                    <p class="font-bold text-sm">Belum ada kunjungan</p>
                    <p class="text-[11px] opacity-70">Tekan tombol + untuk menambah baru.</p>
                </div>
            @endif
        </div>

        {{-- Pagination --}}
        @if ($kunjungan->hasPages())
            <div class="px-2 mt-4 pb-24">
                {{ $kunjungan->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- FAB Button --}}
    <a href="{{ route('kunjungan.create') }}" class="fab-modern">
        <ion-icon name="add-outline"></ion-icon>
    </a>

    {{-- Detail Modal Container --}}
    <div id="visitDetailModal" class="fixed inset-0 z-[110] bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none">
        <div class="modal-card">
            <div class="modal-header-modern">
                <h3 class="modal-title-modern">Detail Kunjungan</h3>
                <ion-icon name="close-outline" class="modal-close-icon" onclick="closeVisitDetail()"></ion-icon>
            </div>
            
            <div class="modal-body-modern">
                {{-- Live Map --}}
                <div class="modal-map-container" id="mapWrapper">
                    <div id="visitMap"></div>
                    <button type="button" class="map-refresh-btn shadow-sm active:scale-95 transition-all" onclick="refreshMap(event)">
                        <ion-icon name="refresh-outline"></ion-icon>
                    </button>
                </div>

                <div class="modal-img-container">
                    <img id="modalImg" src="" class="modal-img-full hidden" alt="Foto">
                    <div id="modalIconWrapper" class="flex flex-col items-center gap-2 py-4">
                        <ion-icon name="image-outline" class="text-5xl text-slate-200"></ion-icon>
                        <span class="text-[10px] font-bold text-slate-300">Tidak ada foto</span>
                    </div>
                </div>

                <div class="modal-info-grid">
                    <div class="info-item">
                        <div class="info-icon-box">
                            <ion-icon name="time-outline"></ion-icon>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Waktu & Tanggal</div>
                            <div id="modalDateTime" class="info-value"></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon-box">
                            <ion-icon name="map-outline"></ion-icon>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Lokasi Kunjungan</div>
                            <div id="modalLocationWrapper">
                                <a id="modalLocationLink" href="" target="_blank" class="info-value flex items-center gap-1 text-teal-600 underline">
                                    <span id="modalLocation"></span>
                                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon>
                                </a>
                                <div id="modalLocationEmpty" class="info-value text-slate-400 hidden">Lokasi tidak tersedia</div>
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon-box">
                            <ion-icon name="chatbox-ellipses-outline"></ion-icon>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Deskripsi Kunjungan</div>
                            <div id="modalDescription" class="info-value"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer-modern">
                <button onclick="closeVisitDetail()" class="btn-close-modern">Tutup Detail</button>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map = null;
        let marker = null;
        let mapInterval = null;

        $(function() {
            // Datepicker Config
            const localeIndo = {
                days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
                months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                today: 'Hari ini', clear: 'Hapus', dateFormat: 'yyyy-MM-dd', timeFormat: 'HH:mm', firstDay: 1
            };
            const dpOpt = { locale: localeIndo, autoClose: true, isMobile: true, buttons: ['today', 'clear'], position: 'bottom center' };
            new AirDatepicker('#tanggal_awal', dpOpt);
            new AirDatepicker('#tanggal_akhir', dpOpt);

            // Delete confirmation
            $('.delete-form').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus Kunjungan?',
                    text: "Data ini akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) this.submit();
                });
            });

            // Backdrop click close
            $('#visitDetailModal').on('click', function(e) {
                if (e.target === this) closeVisitDetail();
            });
        });

        function showVisitDetail(id, desc, date, time, loc, img) {
            $('#modalDateTime').text(time + ' - ' + date);
            $('#modalDescription').text(desc);
            
            let coords = null;
            if(loc && loc !== '' && loc !== '---') {
                $('#modalLocation').text(loc);
                $('#modalLocationLink').attr('href', 'https://www.google.com/maps/search/?api=1&query=' + loc);
                $('#modalLocationLink').removeClass('hidden');
                $('#modalLocationEmpty').addClass('hidden');

                const parts = loc.split(',');
                if(parts.length === 2) {
                    coords = [parseFloat(parts[0]), parseFloat(parts[1])];
                }
            } else {
                $('#modalLocationLink').addClass('hidden');
                $('#modalLocationEmpty').removeClass('hidden');
            }
            
            if(img && img !== 'null' && img !== '') {
                $('#modalImg').attr('src', img).removeClass('hidden');
                $('#modalIconWrapper').addClass('hidden');
            } else {
                $('#modalImg').addClass('hidden');
                $('#modalIconWrapper').removeClass('hidden');
            }

            const $modal = $('#visitDetailModal');
            $modal.addClass('show').css({
                'display': 'flex',
                'pointer-events': 'auto'
            });

            // Handle Map
            if(coords) {
                $('#mapWrapper').show();
                
                // Initialize map if first time
                if (!map) {
                    map = L.map('visitMap', {
                        zoomControl: false,
                        attributionControl: false,
                        fadeAnimation: true,
                        markerZoomAnimation: true
                    }).setView(coords, 18);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        updateWhenIdle: true,
                        keepBuffer: 2
                    }).addTo(map);
                } else {
                    map.setView(coords, 18);
                }
                
                if (marker) map.removeLayer(marker);
                marker = L.marker(coords).addTo(map);

                // Brute-force stabilization (Matches Presensi Show approach)
                if(mapInterval) clearInterval(mapInterval);
                mapInterval = setInterval(function() {
                    if(map) map.invalidateSize();
                }, 100);
            } else {
                $('#mapWrapper').hide();
                if(mapInterval) {
                    clearInterval(mapInterval);
                    mapInterval = null;
                }
            }

            setTimeout(() => {
                $modal.addClass('opacity-100');
            }, 50);
        }

        function refreshMap(e) {
            if(e) e.stopPropagation();
            if(map) {
                map.invalidateSize();
                // Reposition if marker exists
                if(marker) map.setView(marker.getLatLng());
            }
        }

        function closeVisitDetail() {
            if(mapInterval) {
                clearInterval(mapInterval);
                mapInterval = null;
            }
            const $modal = $('#visitDetailModal');
            $modal.removeClass('opacity-100');
            setTimeout(() => {
                $modal.removeClass('show').css({
                    'display': 'none',
                    'pointer-events': 'none'
                });
            }, 300);
        }
    </script>
@endpush
