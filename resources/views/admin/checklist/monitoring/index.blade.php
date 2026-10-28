@extends('layouts.app')
@section('titlepage', 'Monitoring Laporan Opening & Closing')

@section('content')
@section('navigasi')
    <span>Monitoring Checklist Toko</span>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center pb-2">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="ti ti-report-analytics me-2 text-primary"></i>Laporan Buka & Tutup Toko</h5>
                    <small class="text-muted">Pantau kedisiplinan pemeriksaan keamanan fisik, anti-penyusup, dan inventaris toko seluruh cabang</small>
                </div>
                <div>
                    <a href="{{ route('admin.checklist.checkpoints.index') }}" class="btn btn-outline-primary">
                        <i class="ti ti-settings me-1"></i>Master Checkpoint
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Filter Form -->
                <form action="{{ route('admin.checklist.monitoring.index') }}" method="GET" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <input type="date" name="tanggal_mulai" class="form-control form-control-sm" value="{{ request('tanggal_mulai') }}" placeholder="Tanggal Mulai">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="tanggal_akhir" class="form-control form-control-sm" value="{{ request('tanggal_akhir') }}" placeholder="Tanggal Akhir">
                        </div>
                        <div class="col-md-2">
                            <select name="kode_cabang" class="form-select form-select-sm">
                                <option value="">Semua Cabang</option>
                                @foreach($cabangs as $cb)
                                    <option value="{{ $cb->kode_cabang }}" {{ request('kode_cabang') == $cb->kode_cabang ? 'selected' : '' }}>
                                        {{ $cb->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="kode_dept" class="form-select form-select-sm">
                                <option value="">Semua Departemen</option>
                                @foreach($departemens as $dp)
                                    <option value="{{ $dp->kode_dept }}" {{ request('kode_dept') == $dp->kode_dept ? 'selected' : '' }}>
                                        {{ $dp->nama_dept }} ({{ $dp->kode_dept }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="tipe" class="form-select form-select-sm">
                                <option value="">Semua Tipe</option>
                                <option value="opening" {{ request('tipe') == 'opening' ? 'selected' : '' }}>Opening Toko</option>
                                <option value="closing" {{ request('tipe') == 'closing' ? 'selected' : '' }}>Closing Toko</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="d-flex gap-1">
                                <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari Petugas/Kode" value="{{ request('q') }}">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="ti ti-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Kode & Waktu Submit</th>
                                <th>Cabang & Departemen</th>
                                <th>Petugas</th>
                                <th style="width: 110px;">Sesi</th>
                                <th style="width: 140px;">Pemeriksaan</th>
                                <th style="width: 100px;">Grid WA</th>
                                <th style="width: 100px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporans as $idx => $lap)
                                <tr>
                                    <td>{{ $laporans->firstItem() + $idx }}</td>
                                    <td>
                                        <div class="fw-bold font-monospace text-primary">{{ $lap->kode_laporan }}</div>
                                        <small class="text-muted">
                                            <i class="ti ti-calendar me-1"></i>{{ \Carbon\Carbon::parse($lap->tanggal)->translatedFormat('d M Y') }} &bull; {{ date('H:i', strtotime($lap->jam)) }} WIB
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $lap->cabang->nama_cabang ?? $lap->kode_cabang }}</div>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 text-xs">
                                            <i class="ti ti-tag me-0.5"></i>{{ $lap->departemen->nama_dept ?? $lap->kode_dept ?? 'Umum' }}
                                        </span>
                                        @if($lap->lokasi_nama)
                                            <small class="text-muted d-block mt-0.5">{{ $lap->lokasi_nama }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $lap->karyawan->nama_karyawan ?? $lap->nik }}</div>
                                        <small class="text-muted">NIK: {{ $lap->nik }}</small>
                                    </td>
                                    <td>
                                        @if($lap->tipe == 'opening')
                                            <span class="badge text-white px-2.5 py-1.5 fw-bold" style="background-color: #d97706;"><i class="ti ti-door-enter me-1"></i>Opening</span>
                                        @else
                                            <span class="badge text-white px-2.5 py-1.5 fw-bold" style="background-color: #3730a3;"><i class="ti ti-lock me-1"></i>Closing</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="ti ti-check me-1"></i>{{ $lap->total_checkpoint }} Titik OK
                                        </span>
                                        @if($lap->disclaimer_agreed)
                                            <small class="text-success d-block text-xs mt-0.5"><i class="ti ti-shield-check me-0.5"></i>Pernyataan Sah</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lap->collages->isNotEmpty())
                                            <a href="{{ asset('storage/' . $lap->collages->first()->foto_collage) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $lap->collages->first()->foto_collage) }}" class="rounded shadow-xs border" style="width: 60px; height: 60px; object-fit: cover;">
                                            </a>
                                        @else
                                            <span class="text-muted text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('admin.checklist.monitoring.show', $lap->id) }}" class="btn btn-sm btn-icon btn-outline-info" title="Lihat Detail & Foto">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.checklist.monitoring.delete', $lap->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini beserta semua fotonya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus Laporan">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="ti ti-files-off fs-2 d-block mb-1"></i>
                                        Tidak ada laporan checklist ditemukan untuk filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $laporans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
