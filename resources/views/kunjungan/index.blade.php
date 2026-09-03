@extends('layouts.app')
@section('titlepage', 'Kunjungan')

@section('content')
@section('navigasi')
    <span>Kunjungan</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('kunjungan.create')
                    <a href="{{ route('kunjungan.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-2"></i>Tambah Kunjungan
                    </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('kunjungan.index') }}">
                            <div class="row">
                                @if (!auth()->user()->hasRole('karyawan'))
                                    <div class="col-lg-3 col-sm-12 col-md-12">
                                        <div class="form-group mb-3">
                                            <select name="nik" id="nik" class="form-select select2Nik">
                                                <option value="">Semua Karyawan</option>
                                                @foreach ($karyawans as $karyawan)
                                                    <option value="{{ $karyawan->nik }}" {{ Request('nik') == $karyawan->nik ? 'selected' : '' }}>
                                                        {{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="{{ auth()->user()->hasRole('karyawan') ? 'col-lg-4' : 'col-lg-3' }} col-sm-12 col-md-12">
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-calendar"></i></span>
                                            <input type="text" class="form-control flatpickr-date" id="tanggal_awal" name="tanggal_awal"
                                                placeholder="Tanggal Awal" value="{{ Request('tanggal_awal') }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="{{ auth()->user()->hasRole('karyawan') ? 'col-lg-4' : 'col-lg-3' }} col-sm-12 col-md-12">
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-calendar"></i></span>
                                            <input type="text" class="form-control flatpickr-date" id="tanggal_akhir" name="tanggal_akhir"
                                                placeholder="Tanggal Akhir" value="{{ Request('tanggal_akhir') }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="{{ auth()->user()->hasRole('karyawan') ? 'col-lg-4' : 'col-lg-3' }} col-sm-12 col-md-12">
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-primary"><i class="ti ti-search me-1"></i>Cari</button>
                                        @can('kunjungan.index')
                                            @if (!auth()->user()->hasRole('karyawan'))
                                                @if (request('nik'))
                                                    <a href="{{ route('kunjungan.export.pdf', request()->query()) }}" class="btn btn-danger"
                                                        target="_blank">
                                                        <i class="ti ti-file-export me-1"></i>Export
                                                    </a>
                                                @else
                                                    <button class="btn btn-danger" disabled title="Pilih karyawan terlebih dahulu">
                                                        <i class="ti ti-file-export me-1"></i>Export
                                                    </button>
                                                @endif
                                            @else
                                                <a href="{{ route('kunjungan.export.pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
                                                    <i class="ti ti-file-export me-1"></i>Export
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                @forelse($kunjungan as $item)
                                    <div class="card mb-2 shadow-sm border">
                                        <div class="card-body p-2">
                                            <div class="row align-items-center">
                                                <!-- Foto -->
                                                <div class="col-md-1 text-center">
                                                    @php
                                                        $path = 'uploads/kunjungan/'.$item->foto;
                                                    @endphp
                                                    @if ($item->foto && Storage::disk('public')->exists($path))
                                                        <img src="{{ asset('storage/' . $path) }}" alt="Foto"
                                                            class="rounded-circle cursor-pointer"
                                                            style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #e9ecef;"
                                                            onclick="showImageModal('{{ asset('storage/' . $path) }}', 'Foto Kunjungan - {{ $item->karyawan->nama_karyawan ?? $item->nik }}')">
                                                    @else
                                                        <div class="avatar bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px; border: 1px solid #e9ecef;">
                                                            <i class="ti ti-photo-off text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- Identity -->
                                                <div class="col-md-3">
                                                    <div class="fw-bold text-dark" style="font-size: 14px;">{{ $item->nama_karyawan ?? 'N/A' }}</div>
                                                    <div class="text-muted" style="font-size: 12px;">{{ $item->nik }}</div>
                                                </div>
                                                <!-- Description -->
                                                <div class="col-md-3">
                                                    <div class="fw-bold text-dark mb-1" style="font-size: 11px;">Deskripsi:</div>
                                                    <div class="text-dark" style="font-size: 13px; line-height: 1.2;">
                                                        {{ Str::limit($item->deskripsi, 100) }}
                                                    </div>
                                                </div>
                                                <!-- Map Icon -->
                                                <div class="col-md-1 text-center">
                                                    @if ($item->lokasi)
                                                        <a href="javascript:void(0)" onclick="showMapModal('{{ $item->lokasi }}', 'Lokasi - {{ $item->karyawan->nama_karyawan ?? $item->nik }}')" class="text-primary fs-3" title="Lihat Lokasi">
                                                            <i class="ti ti-map-2"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted" style="font-size: 10px;">-</span>
                                                    @endif
                                                </div>
                                                <!-- Date & Time -->
                                                <div class="col-md-2 text-center">
                                                    <div class="text-muted" style="font-size: 11px;">
                                                        {{ $item->tanggal_kunjungan->format('d/m/Y') }}
                                                    </div>
                                                    <div class="text-muted" style="font-size: 10px;">
                                                        {{ $item->created_at->format('H:i:s') }}
                                                    </div>
                                                </div>
                                                <!-- Actions -->
                                                <div class="col-md-2 text-end">
                                                    <div class="btn-group shadow-sm" role="group">
                                                        @can('kunjungan.index')
                                                            <a href="{{ route('kunjungan.show', $item) }}" class="btn btn-sm btn-outline-info py-1 px-2" title="Detail">
                                                                <i class="ti ti-eye"></i>
                                                            </a>
                                                        @endcan
                                                        @can('kunjungan.edit')
                                                            <a href="{{ route('kunjungan.edit', $item) }}" class="btn btn-sm btn-outline-success py-1 px-2" title="Edit">
                                                                <i class="ti ti-edit"></i>
                                                            </a>
                                                        @endcan
                                                        @can('kunjungan.delete')
                                                            <form method="POST" name="deleteform" class="deleteform d-inline"
                                                                action="{{ route('kunjungan.destroy', $item) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm rounded-0 rounded-end py-1 px-2" title="Hapus">
                                                                    <i class="ti ti-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="alert alert-info d-flex align-items-center" role="alert">
                                        <i class="ti ti-inbox me-2 fs-4"></i>
                                        <div>
                                            Tidak ada data kunjungan.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                        <div style="float: right;">
                            {{ $kunjungan->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Foto Kunjungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Foto Kunjungan" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <a id="downloadImage" href="" download class="btn btn-primary">
                    <i class="ti ti-download me-2"></i>Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- Map Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Lokasi Kunjungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="map" style="height: 400px; width: 100%;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        // Initialize select2 for karyawan
        const select2Nik = $(".select2Nik");
        if (select2Nik.length) {
            select2Nik.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Semua Karyawan',
                    allowClear: true,
                    dropdownParent: $this.parent()
                });
            });
        }

        // Initialize flatpickr for date inputs
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true
        });

        function showImageModal(imageSrc, title) {
            document.getElementById('imageModalLabel').textContent = title;
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('downloadImage').href = imageSrc;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        }

        // Make function global
        window.showImageModal = showImageModal;

        // Map Modal Logic
        let map = null;
        let marker = null;

        function showMapModal(lokasi, title) {
            document.getElementById('mapModalLabel').textContent = title;
            const myModal = new bootstrap.Modal(document.getElementById('mapModal'));
            myModal.show();

            const [lat, long] = lokasi.split(',');
            
            // Wait for modal to fully show before initializing/resizing map
            document.getElementById('mapModal').addEventListener('shown.bs.modal', function () {
                if (map) {
                    map.remove(); // Clean up existing map instance
                }

                map = L.map('map').setView([lat, long], 16);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                marker = L.marker([lat, long]).addTo(map)
                    .bindPopup(title)
                    .openPopup();
                
                map.invalidateSize(); // Ensure map renders correctly
            }, { once: true });
        }
        window.showMapModal = showMapModal;

        $('.delete-confirm').click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data kunjungan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Dynamic export button based on NIK selection
    function updateExportButton() {
        const nikSelect = document.getElementById('nik');
        const exportButton = document.querySelector('button[title="Pilih karyawan terlebih dahulu"]');
        const exportLink = document.querySelector('a[href*="export.pdf"]');

        if (nikSelect && nikSelect.value) {
            // Enable export button
            if (exportButton) {
                exportButton.disabled = false;
                exportButton.removeAttribute('title');
                exportButton.innerHTML = '<i class="ti ti-file-export me-1"></i>Export';
                exportButton.onclick = function() {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('nik', nikSelect.value);
                    window.open(currentUrl.toString().replace('/kunjungan', '/kunjungan/export/pdf'), '_blank');
                };
            }
        } else {
            // Disable export button
            if (exportButton) {
                exportButton.disabled = true;
                exportButton.setAttribute('title', 'Pilih karyawan terlebih dahulu');
                exportButton.innerHTML = '<i class="ti ti-file-export me-1"></i>Export';
                exportButton.onclick = null;
            }
        }
    }

    // Initialize export button state
    updateExportButton();

    // Update export button when NIK selection changes
    document.getElementById('nik').addEventListener('change', updateExportButton);
</script>
@endpush
