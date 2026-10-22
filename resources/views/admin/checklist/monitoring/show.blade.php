@extends('layouts.app')
@section('titlepage', 'Detail Laporan Checklist ' . $laporan->kode_laporan)

@section('content')
@section('navigasi')
    <a href="{{ route('admin.checklist.monitoring.index') }}">Monitoring Checklist</a> /
    <span>Detail Laporan</span>
@endsection

<div class="row g-3">
    <!-- Kolom Kiri: Metadata Laporan -->
    <div class="col-lg-4 col-md-5 col-12">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge bg-white text-primary text-uppercase">{{ $laporan->tipe }} Toko</span>
                    <small class="text-white-50">{{ $laporan->kode_laporan }}</small>
                </div>
                <h5 class="text-white fw-bold mt-2 mb-0">{{ $laporan->cabang->nama_cabang ?? $laporan->kode_cabang }}</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Petugas Pemeriksa</span>
                        <strong class="text-dark">{{ $laporan->karyawan->nama_karyawan ?? $laporan->nik }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">NIK</span>
                        <span class="font-monospace">{{ $laporan->nik }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Tanggal Selesai</span>
                        <span>{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('l, d F Y') }}</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Jam Pemeriksaan</span>
                        <span class="fw-bold">{{ date('H:i:s', strtotime($laporan->jam)) }} WIB</span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Titik</span>
                        <span class="badge bg-success">{{ $laporan->total_checkpoint }} Titik Terverifikasi</span>
                    </li>
                    @if($laporan->latitude && $laporan->longitude)
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Koordinat GPS:</span>
                            <a href="https://maps.google.com/?q={{ $laporan->latitude }},{{ $laporan->longitude }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                <i class="ti ti-map-pin me-1"></i>Lihat Titik di Google Maps ({{ $laporan->latitude }}, {{ $laporan->longitude }})
                            </a>
                        </li>
                    @endif
                    <li class="list-group-item px-0">
                        <div class="alert alert-success d-flex align-items-start gap-2 mb-0 py-2">
                            <i class="ti ti-shield-check fs-4 mt-0.5 text-success"></i>
                            <div>
                                <strong class="d-block text-xs">Pernyataan Tanggung Jawab Ditandatangani</strong>
                                <small class="text-muted" style="font-size: 11px;">
                                    Petugas menyatakan bertanggung jawab penuh atas kebenaran seluruh area yang diperiksa.
                                </small>
                            </div>
                        </div>
                    </li>
                    @if($laporan->catatan)
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Catatan Tambahan Petugas:</span>
                            <div class="p-2 bg-light rounded text-dark text-sm">
                                {{ $laporan->catatan }}
                            </div>
                        </li>
                    @endif
                </ul>

                <div class="mt-3">
                    <a href="{{ route('admin.checklist.monitoring.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="ti ti-arrow-left me-1"></i>Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        <!-- Foto Gabungan Grid 4-in-1 (WhatsApp Ready) -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 fw-bold"><i class="ti ti-layout-grid me-1 text-primary"></i>Foto Grid 4-in-1 (Format WA)</h6>
            </div>
            <div class="card-body p-2">
                @foreach($laporan->collages as $col)
                    <div class="border rounded p-2 mb-2 bg-dark">
                        <div class="d-flex justify-content-between align-items-center mb-1 text-white text-xs">
                            <span>Grid Bagian #{{ $col->batch_no }} ({{ $col->total_items }} Titik)</span>
                            <a href="{{ asset('storage/' . $col->foto_collage) }}" download class="badge bg-primary text-decoration-none">
                                <i class="ti ti-download me-1"></i>Download
                            </a>
                        </div>
                        <a href="{{ asset('storage/' . $col->foto_collage) }}" target="_blank">
                            <img src="{{ asset('storage/' . $col->foto_collage) }}" class="img-fluid rounded w-100">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Foto Satuan Tiap Checkpoint -->
    <div class="col-lg-8 col-md-7 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="ti ti-photo me-2 text-primary"></i>Detail Foto Tiap Checkpoint ({{ $laporan->details->count() }})</h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle">
                    Semua Foto Telah Di-Watermark
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($laporan->details as $idx => $det)
                        <div class="col-md-6 col-12">
                            <div class="card h-100 border shadow-2xs">
                                <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark text-sm">
                                        <span class="badge bg-primary me-1">{{ $idx + 1 }}</span>
                                        {{ $det->nama_checkpoint }}
                                    </span>
                                    <span class="badge bg-success">OK</span>
                                </div>
                                <div class="position-relative bg-dark" style="aspect-ratio: 4/3; overflow: hidden;">
                                    <a href="{{ asset('storage/' . $det->foto) }}" target="_blank" title="Klik untuk perbesar">
                                        <img src="{{ asset('storage/' . $det->foto) }}" alt="{{ $det->nama_checkpoint }}" class="w-100 h-100" style="object-fit: cover;">
                                    </a>
                                </div>
                                @if($det->deskripsi || $det->catatan)
                                    <div class="card-body p-2 bg-white">
                                        @if($det->deskripsi)
                                            <small class="text-muted d-block" style="font-size: 11px;">
                                                <i class="ti ti-info-circle me-1"></i>{{ $det->deskripsi }}
                                            </small>
                                        @endif
                                        @if($det->catatan)
                                            <div class="p-1 bg-warning-subtle text-dark rounded text-xs mt-1">
                                                <strong>Catatan:</strong> {{ $det->catatan }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
