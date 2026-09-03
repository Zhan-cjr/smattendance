@extends('layouts.mobile.modern')

@section('title', 'Aktivitas Saya')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@section('header_right')
    <a href="{{ route('aktivitaskaryawan.export.pdf', request()->query()) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all" target="_blank" title="Export PDF">
        <ion-icon name="document-text-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css" rel="stylesheet" type="text/css">
    <style>
        /* Modern Activity Cards Styling (Sync with History) */
        .transactions {
            padding-bottom: 100px;
        }

        .transactions .item {
            background: #ffffff;
            border-radius: 10px;
            padding: 4px; /* Matching p-1 feel from history */
            margin-bottom: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            border: 1px solid {{ $t['primary'] }};
            position: relative;
            display: flex;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            gap: 8px;
        }

        .transactions .item:active {
            transform: scale(0.98);
            background: #f8fafc;
        }

        .transactions .item .detail {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 0;
        }

        /* Date Badge Sync with Histori */
        .date-badge-modern {
            flex-shrink: 0;
            width: 45px;
            height: 45px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: {{ $t['primary'] }}15;
            color: {{ $t['primary'] }};
        }

        .date-badge-modern .day-short {
            text-[10px] font-bold leading-none uppercase;
            font-size: 10px;
        }

        .date-badge-modern .date-num {
            text-[16px] font-extrabold leading-tight mt-0.5;
            font-size: 16px;
        }

        .transactions .item .detail .info {
            flex: 1;
            min-width: 0;
            padding-right: 4px;
        }

        .transactions .item .detail .info strong {
            display: block;
            font-size: 14px; /* Compact like history */
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .transactions .item .detail .info p {
            margin: 0;
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .transactions .item .detail .info .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .transactions .item .detail .info .timestamp {
            color: #32745e;
            font-weight: 700;
            font-size: 11px;
            background: rgba(50, 116, 94, 0.05);
            padding: 2px 6px;
            border-radius: 6px;
        }

        /* Filter Styles (Sync with History) */
        .filter-modern {
            background: #ffffff;
            padding: 15px;
            border-bottom: 1px solid rgba(50, 116, 94, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .form-label-group {
            position: relative;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s;
        }

        .form-label-group:focus-within {
            border-color: #32745e;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(50, 116, 94, 0.1);
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #64748b;
            pointer-events: none;
        }

        .form-label-group input {
            width: 100%;
            height: 44px;
            padding: 0 12px 0 40px;
            border: none;
            background: transparent;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            outline: none;
        }

        .btn-search-modern {
            height: 44px;
            border-radius: 12px;
            background: #32745e;
            color: #ffffff;
            border: none;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(50, 116, 94, 0.2);
            transition: all 0.2s;
        }

        .btn-search-modern:active {
            transform: scale(0.96);
        }

        /* Modal Redesign - Center Position High End */
        #detailModal {
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        #detailModal.show {
            visibility: visible;
            opacity: 1 !important;
            pointer-events: auto;
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

        #detailModal.show .modal-card {
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

        #activityMap {
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

        .btn-modal-close:active {
            background: #e2e8f0;
        }

        /* FAB Button */
        .fab-modern {
            position: fixed;
            bottom: 110px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: #32745e;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: #ffffff;
            box-shadow: 0 10px 25px rgba(50, 116, 94, 0.4);
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .fab-modern:active {
            transform: scale(0.85) rotate(45deg);
        }
    </style>
@endpush

@section('content')
    {{-- ===== FILTER SYNC WITH HISTORI ===== --}}
    <form method="GET" action="{{ route('aktivitaskaryawan.index') }}" id="formAktivitas">
        <div class="mt-1 mb-2 rounded-xl overflow-hidden border"
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
                    {{-- Dari --}}
                    <div class="flex-1">
                        <input type="text" name="tanggal_awal" id="tanggal_awal" 
                            class="w-full rounded-lg py-1.5 px-2 text-[12px] font-medium text-center focus:outline-none transition-all datepicker"
                            style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;"
                            placeholder="Dari" value="{{ Request('tanggal_awal', date('Y-m-d')) }}" autocomplete="off" required readonly>
                    </div>
                    <div class="flex-shrink-0 w-4 flex items-center justify-center">
                        <div class="w-3 h-[1px]" style="background: #cbd5e1;"></div>
                    </div>
                    {{-- Sampai --}}
                    <div class="flex-1">
                        <input type="text" name="tanggal_akhir" id="tanggal_akhir" 
                            class="w-full rounded-lg py-1.5 px-2 text-[12px] font-medium text-center focus:outline-none transition-all datepicker"
                            style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;"
                            placeholder="Sampai" value="{{ Request('tanggal_akhir', date('Y-m-d')) }}" autocomplete="off" required readonly>
                    </div>
                    {{-- Button --}}
                    <button type="submit" id="btnCari"
                        class="flex-shrink-0 w-9 h-8 rounded-lg text-white flex items-center justify-center active:scale-90 transition-transform"
                        style="background: {{ $t['primary'] }};">
                        <ion-icon name="search-outline" class="text-base"></ion-icon>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Activities List (Width Sync: Removing transactions custom padding) -->
    <div class="transactions space-y-2 mt-2">
        @if ($aktivitas->count() > 0)
            @php
                $namahari = [
                    'Sun' => 'Min', 'Mon' => 'Sen', 'Tue' => 'Sel', 'Wed' => 'Rab',
                    'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab'
                ];
            @endphp
            @foreach ($aktivitas as $index => $item)
                @php
                    $day_eng = $item->created_at->format('D');
                    $day_short = $namahari[$day_eng] ?? $day_eng;
                @endphp
                <div class="fade-up item press mb-0"
                    style="animation-delay: {{ $index * 0.04 }}s;"
                    onclick="showDetailModal({{ $item->id }}, '{{ addslashes($item->aktivitas) }}', '{{ $item->created_at->format('d M Y') }}', '{{ $item->created_at->format('H:i') }}', '{{ $item->lokasi }}', '{{ $item->foto }}')">
                    <div class="detail">
                        {{-- Date Badge Sync --}}
                        <div class="date-badge-modern">
                            <span class="day-short">{{ strtoupper($day_short) }}</span>
                            <span class="date-num">{{ $item->created_at->format('d') }}</span>
                        </div>

                        <div class="info">
                            <div class="meta-row">
                                <strong>{{ DateToIndo($item->created_at->format('Y-m-d')) }}</strong>
                                <span class="timestamp">
                                    <ion-icon name="time-outline"></ion-icon>
                                    {{ $item->created_at->format('H:i') }}
                                </span>
                            </div>
                            <p class="truncate" style="color: #334155; font-weight: 600; margin-bottom: 2px;">
                                {{ Str::limit($item->aktivitas, 35) }}
                            </p>
                            @if ($item->lokasi)
                                <p style="font-size: 11px;">
                                    <ion-icon name="location-outline" style="color: {{ $t['primary'] }};"></ion-icon>
                                    <a href="https://www.google.com/maps?q={{ $item->lokasi }}" 
                                       target="_blank" 
                                       onclick="event.stopPropagation();"
                                       style="color: {{ $t['primary'] }}; font-weight: 600;">
                                        Lihat di Peta
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="price" onclick="event.stopPropagation();">
                        <form method="POST" name="deleteform" class="deleteform d-inline"
                            action="{{ route('aktivitaskaryawan.destroy', $item) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm text-danger p-0 delete-confirm" style="background: transparent; border: none;">
                                <ion-icon name="trash-outline" style="font-size: 20px;"></ion-icon>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center" style="padding: 100px 40px;">
                <div style="font-size: 80px; color: #e2e8f0; margin-bottom: 20px;">
                    <ion-icon name="calendar-clear-outline"></ion-icon>
                </div>
                <h4 style="color: #1e293b; font-weight: 800; margin-bottom: 8px;">Belum Ada Aktivitas</h4>
                <p style="color: #64748b; font-size: 14px;">Mulai catat aktivitas harian Anda untuk melacak produktivitas.</p>
                <a href="{{ route('aktivitaskaryawan.create') }}" class="btn-search-modern mt-4" style="text-decoration: none; width: 200px; margin: 0 auto;">
                    <ion-icon name="add-outline"></ion-icon> Tambah Aktivitas
                </a>
            </div>
        @endif

        {{-- Pagination --}}
        <div class="p-3">
            {{ $aktivitas->links() }}
        </div>
    </div>

    {{-- FAB Button --}}
    <a href="{{ route('aktivitaskaryawan.create') }}" class="fab-modern">
        <ion-icon name="add-outline"></ion-icon>
    </a>

    {{-- Modern Detail Modal (Synced with Kunjungan Style) --}}
    <div id="detailModal" class="fixed inset-0 z-[10000] bg-black/70 backdrop-blur-sm opacity-0 pointer-events-none">
        <div class="modal-card">
            <div class="modal-header-modern">
                <h3 class="modal-title-modern">Detail Aktivitas</h3>
                <ion-icon name="close-outline" class="modal-close-icon text-2xl text-slate-400 cursor-pointer" onclick="closeDetailModal()"></ion-icon>
            </div>
            
            <div class="modal-body-modern">
                {{-- Live Map integration (Leaflet) --}}
                <div class="modal-map-container" id="mapWrapper">
                    <div id="activityMap"></div>
                    <button type="button" class="map-refresh-btn shadow-sm active:scale-95 transition-all" onclick="refreshMap(event)">
                        <ion-icon name="refresh-outline"></ion-icon>
                    </button>
                </div>

                <div class="modal-img-container">
                    <img id="modalImg" src="" class="modal-img-full hidden" alt="Foto">
                    <div id="modalIconWrapper" class="flex flex-col items-center gap-2 py-4">
                        <ion-icon name="image-outline" class="text-5xl text-slate-200"></ion-icon>
                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Tidak ada foto</span>
                    </div>
                </div>

                <div class="modal-info-grid">
                    <div class="info-item">
                        <div class="info-icon-box">
                            <ion-icon name="calendar-outline"></ion-icon>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Tanggal</div>
                            <div id="modalDate" class="info-value text-[13px] font-semibold"></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon-box">
                            <ion-icon name="time-outline"></ion-icon>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Waktu Catat</div>
                            <div id="modalTime" class="info-value text-[13px] font-semibold"></div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon-box">
                            <ion-icon name="map-outline"></ion-icon>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Lokasi / Koordinat</div>
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
                            <div class="info-label">Deskripsi Aktivitas</div>
                            <div id="modalDescription" class="info-value"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer-modern">
                <button onclick="closeDetailModal()" class="btn-close-modern">Tutup Detail</button>
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
            // DatePicker configs
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

            function showDetailModal(id, aktivitas, tanggal, waktu, lokasi, foto) {
                $('#modalDate').text(tanggal);
                $('#modalTime').text(waktu);
                $('#modalDescription').text(aktivitas);
                
                let coords = null;
                if(lokasi && lokasi !== '' && lokasi !== '---') {
                    $('#modalLocation').text(lokasi);
                    $('#modalLocationLink').attr('href', 'https://www.google.com/maps?q=' + lokasi).removeClass('hidden');
                    $('#modalLocationEmpty').addClass('hidden');

                    const parts = lokasi.split(',');
                    if(parts.length === 2) {
                        coords = [parseFloat(parts[0]), parseFloat(parts[1])];
                    }
                } else {
                    $('#modalLocationLink').addClass('hidden');
                    $('#modalLocationEmpty').removeClass('hidden');
                }

                // Photo Handling
                if(foto && foto !== 'null' && foto !== '') {
                    $('#modalImg').attr('src', `{{ asset('storage/uploads/aktivitas/') }}/${foto}`).removeClass('hidden');
                    $('#modalIconWrapper').addClass('hidden');
                } else {
                    $('#modalImg').addClass('hidden');
                    $('#modalIconWrapper').removeClass('hidden');
                }

                const $modal = $('#detailModal');
                $modal.addClass('show').css({
                    'display': 'flex',
                    'pointer-events': 'auto'
                });

                // Handle Map
                if(coords) {
                    $('#mapWrapper').show();
                    if (!map) {
                        map = L.map('activityMap', {
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

                    // Brute-force stabilization (Matches Kunjungan & Presensi approach)
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

            window.showDetailModal = showDetailModal;

            function closeDetailModal() {
                if(mapInterval) {
                    clearInterval(mapInterval);
                    mapInterval = null;
                }
                const $modal = $('#detailModal');
                $modal.removeClass('opacity-100');
                setTimeout(() => {
                    $modal.removeClass('show').css({
                        'display': 'none',
                        'pointer-events': 'none'
                    });
                }, 300);
            }

            window.closeDetailModal = closeDetailModal;

            window.refreshMap = function(e) {
                if(e) e.stopPropagation();
                if(map) {
                    map.invalidateSize();
                    if(marker) map.setView(marker.getLatLng());
                }
            };

            // Close modal on backdrop click
            $('#detailModal').on('click', function(e) {
                if (e.target === this) closeDetailModal();
            });

            // Delete Confirmation
            $('.delete-confirm').click(function(e) {
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus Aktivitas?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush
