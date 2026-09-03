@extends('layouts.app')
@section('titlepage', 'Detail Kunjungan')

@section('content')
@section('navigasi')
    <span class="text-muted">Kunjungan</span> / <span>Detail</span>
@endsection

<div class="row">
    <div class="col-md-7 mb-3">
        <div class="card h-100 shadow-sm border">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Informasi Kunjungan</h5>
                <div>
                    <a href="{{ route('kunjungan.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i>Kembali
                    </a>
                    @can('kunjungan.edit')
                        <a href="{{ route('kunjungan.edit', $kunjungan) }}" class="btn btn-sm btn-primary ms-1">
                            <i class="ti ti-edit me-1"></i>Edit
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <!-- Identity -->
                <div class="d-flex align-items-center mb-4 p-3 rounded bg-light">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 20px;">
                        {{ substr($kunjungan->karyawan->nama_karyawan ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $kunjungan->karyawan->nama_karyawan ?? 'Tidak Diketahui' }}</h6>
                        <small class="text-muted">{{ $kunjungan->nik }}</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted fs-6">Tanggal</label>
                        <div class="fw-bold text-dark">{{ $kunjungan->tanggal_kunjungan->format('d F Y') }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted fs-6">Waktu Input</label>
                        <div class="fw-bold text-dark">{{ $kunjungan->created_at->format('H:i:s') }}</div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label text-muted fs-6">Deskripsi</label>
                        <div class="p-3 rounded border bg-light text-dark">
                            {{ $kunjungan->deskripsi ?: '-' }}
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div>
                    <label class="form-label text-muted fs-6 mb-2">Lokasi Kunjungan</label>
                    @if ($kunjungan->lokasi)
                        <div id="map" class="border rounded" style="height: 300px; width: 100%;"></div>
                        <small class="text-muted mt-1 d-block"><i class="ti ti-map-pin me-1"></i>{{ $kunjungan->lokasi }}</small>
                    @else
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="ti ti-map-off me-2"></i> No location data available.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5 mb-3">
        <div class="card h-100 shadow-sm border">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">Foto Dokumentasi</h5>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center bg-light">
                @php
                    $path = 'uploads/kunjungan/'.$kunjungan->foto;
                @endphp
                @if ($kunjungan->foto && Storage::disk('public')->exists($path))
                    <img src="{{ asset('storage/' . $path) }}" alt="Foto Kunjungan"
                        class="img-fluid rounded shadow-sm mb-3" 
                        style="max-height: 400px; width: 100%; object-fit: contain; cursor: pointer;"
                        onclick="showImageModal('{{ asset('storage/' . $path) }}', 'Foto Kunjungan')">
                    
                    <a href="{{ asset('storage/' . $path) }}" download class="btn btn-primary w-100">
                        <i class="ti ti-download me-2"></i>Download Foto
                    </a>
                @else
                    <div class="text-center text-muted p-5">
                        <div class="mb-3">
                             <i class="ti ti-photo-off" style="font-size: 64px; opacity: 0.3;"></i>
                        </div>
                        <h6 class="text-muted">Tidak ada foto tersedia</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center bg-light">
                <img id="modalImage" src="" alt="Preview" class="img-fluid rounded shadow-sm">
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    // Image Modal
    function showImageModal(imageSrc, title) {
        document.getElementById('imageModalLabel').textContent = title;
        document.getElementById('modalImage').src = imageSrc;
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }

    // Map Initialization
    document.addEventListener('DOMContentLoaded', function() {
        @if ($kunjungan->lokasi)
            const lokasi = "{{ $kunjungan->lokasi }}";
            const [lat, long] = lokasi.split(',');

            const map = L.map('map').setView([lat, long], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            L.marker([lat, long]).addTo(map)
                .bindPopup("{{ $kunjungan->karyawan->nama_karyawan ?? 'Lokasi Kunjungan' }}")
                .openPopup();
        @endif
    });
</script>
@endpush
