@php
    $in_out = $status == 'in' ? 'Masuk' : 'Pulang';
    $foto = $status == 'in' ? $presensi->foto_in : $presensi->foto_out;
    $jam = $status == 'in' ? $presensi->jam_in : $presensi->jam_out;
    $lokasi_user = $status == 'in' ? $presensi->lokasi_in : $presensi->lokasi_out;
    $map_id = $status == 'in' ? 'map' : 'map_out';
@endphp

<style>
    .presensi-detail-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: #fff;
        margin-bottom: 20px;
    }
    .card-header-custom {
        padding: 12px 20px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 8px 8px 0 0;
    }
    .info-table th {
        width: 40%;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        background-color: #fafafa;
        padding: 10px 15px;
        border: 1px solid #f1f5f9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-table td {
        padding: 10px 15px;
        border: 1px solid #f1f5f9;
        color: #1e293b;
        font-weight: 500;
    }
    .attendance-img {
        width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .machine-info-box {
        background-color: #f0f7ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 20px;
    }
    .machine-title {
        color: #0369a1;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1rem;
    }
    #{{ $map_id }} {
        height: 450px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    .status-pill {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.8rem;
    }
</style>

<div class="container-fluid p-0">
    <div class="row g-3">
        <!-- Kolom Kiri: Foto & Detail Karyawan -->
        <div class="col-md-5">
            <div class="presensi-detail-card">
                <div class="card-header-custom">
                    <i class="ti ti-camera"></i> Foto Presensi {{ $in_out }}
                </div>
                <div class="p-3 text-center">
                    @if (!empty($foto) && Storage::disk('public')->exists('/uploads/absensi/' . $foto))
                        <img src="{{ url('/storage/uploads/absensi/' . $foto) }}" class="attendance-img" alt="Foto Presensi">
                    @else
                        <div class="py-5 bg-light rounded d-flex flex-column align-items-center justify-content-center border">
                            <i class="ti ti-camera-off text-muted fs-1 mb-2"></i>
                            <span class="text-muted small">Tidak ada lampiran foto</span>
                        </div>
                    @endif
                    <div class="mt-3">
                        <span class="status-pill {{ $status == 'in' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                            <i class="ti {{ $status == 'in' ? 'ti-circle-check' : 'ti-circle-x' }} me-1"></i>
                            PRESENSI {{ strtoupper($in_out) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="presensi-detail-card">
                <div class="card-header-custom">
                    <i class="ti ti-user-circle"></i> Informasi Detail
                </div>
                <table class="table info-table mb-0">
                    <tr>
                        <th>NPP / NIK</th>
                        <td>{{ $presensi->nik }}</td>
                    </tr>
                    <tr>
                        <th>Nama Karyawan</th>
                        <td>{{ $presensi->nama_karyawan }}</td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <td>{{ $presensi->nama_jabatan }}</td>
                    </tr>
                    <tr>
                        <th>Departemen</th>
                        <td>{{ $presensi->nama_dept }}</td>
                    </tr>
                    <tr>
                        <th>Kantor / Cabang</th>
                        <td>{{ $presensi->nama_cabang }}</td>
                    </tr>
                    <tr>
                        <th>Waktu Presensi</th>
                        <td>{{ DateToIndo($presensi->tanggal) }} / <span class="text-primary fw-bold">{{ date('H:i:s', strtotime($jam)) }}</span></td>
                    </tr>
                    <tr>
                        <th>Jarak Radius</th>
                        <td>
                            @php
                                $meters = 0;
                                if (!empty($lokasi_user)) {
                                    $lok = explode(',', $lokasi_user);
                                    $dist = HitungJarak($latitude, $longitude, $lok[0], $lok[1]);
                                    $meters = $dist['meters'];
                                }
                            @endphp
                            <span class="{{ $meters > $cabang->radius_cabang ? 'text-danger' : 'text-success' }} fw-bold">
                                {{ formatAngkaDesimal($meters) }} Meter
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Kolom Kanan: Peta & Detail Mesin -->
        <div class="col-md-7">
            @if ($presensi->id_mesin != null)
                <div class="machine-info-box mb-3 shadow-sm">
                    <div class="machine-title">
                        <i class="ti ti-fingerprint fs-4"></i> Data Mesin Fingerprint (ADMS)
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted d-block fw-bold text-uppercase">Nama Perangkat</label>
                            <span class="fw-bold">{{ $presensi->mesinfingerprint->nama_mesin }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted d-block fw-bold text-uppercase">Serial Number</label>
                            <span class="font-monospace">{{ $presensi->mesinfingerprint->sn }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="small text-muted d-block fw-bold text-uppercase">Brand / Merk</label>
                            <span>{{ $presensi->mesinfingerprint->merk ?? '-' }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="small text-muted d-block fw-bold text-uppercase">Lokasi Fisik</label>
                            <span>{{ $presensi->mesinfingerprint->lokasi ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="presensi-detail-card">
                <div class="card-header-custom justify-content-between">
                    <span><i class="ti ti-map-pin"></i> Plotting Lokasi Presensi</span>
                    @if($lokasi_user)
                        <small class="text-muted fw-normal">Koordinat: {{ $lokasi_user }}</small>
                    @endif
                </div>
                <div class="p-0">
                    @if (!empty($lokasi_user))
                        <div id="{{ $map_id }}"></div>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center p-5 bg-light" style="height: 450px;">
                            <i class="ti ti-map-off text-muted fs-1 mb-2"></i>
                            <span class="text-muted">Titik koordinat tidak ditemukan</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if (!empty($lokasi_user))
<script>
    var lokasi = "{{ $lokasi_user }}";
    var lok = lokasi.split(",");
    var latitude_user = lok[0];
    var longitude_user = lok[1];

    var latitude_kantor = "{{ $latitude }}";
    var longitude_kantor = "{{ $longitude }}";
    var rd = "{{ $cabang->radius_cabang }}";
    
    var {{ $map_id }} = L.map('{{ $map_id }}', {
        center: [latitude_user, longitude_user],
        zoom: 16
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo({{ $map_id }});

    var marker = L.marker([latitude_user, longitude_user]).addTo({{ $map_id }});
    marker.bindPopup("<b>Lokasi Karyawan</b>").openPopup();

    var circle = L.circle([latitude_kantor, longitude_kantor], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.2,
        radius: rd
    }).addTo({{ $map_id }});
    
    // Fit bounds to show both user and office
    var group = new L.featureGroup([marker, circle]);
    {{ $map_id }}.fitBounds(group.getBounds().pad(0.1));

    setInterval(function() {
        {{ $map_id }}.invalidateSize();
    }, 500);
</script>
@endif
