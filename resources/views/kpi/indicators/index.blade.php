@extends('layouts.app')
@section('titlepage', 'Konfigurasi Indikator KPI')

@section('content')
@section('navigasi')
    <span>Konfigurasi Indikator KPI</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <a href="#" class="btn btn-primary" id="btnTambahKonfigurasi">
                    <i class="ti ti-plus me-2"></i> Tambah Konfigurasi KPI
                </a>
                <a href="{{ route('kpi.indicators.create', ['scope' => 'global']) }}" class="btn btn-info">
                    <i class="ti ti-world me-2"></i> Konfigurasi Global
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('kpi.indicators.index') }}">
                            <div class="row g-2">
                                <div class="col-lg-10 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Nama Jabatan" value="{{ Request('nama_jabatan') }}"
                                        name="nama_jabatan" icon="ti ti-search" hideLabel />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <button class="btn btn-primary w-100"><i class="ti ti-icons ti-search me-1"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                @forelse ($kpi_indicators as $d)
                                    <div class="card mb-2 shadow-sm border">
                                        <div class="card-body p-2">
                                            <div class="row align-items-center">
                                                <!-- Icon -->
                                                <div class="col-md-1 text-center d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-briefcase text-primary" style="font-size: 32px;"></i>
                                                </div>
                                                <!-- Identity -->
                                                <div class="col-md-4">
                                                    @if($d->kode_jabatan == null)
                                                        <div class="fw-bold text-primary" style="font-size: 14px;">INDIKATOR GLOBAL</div>
                                                        <div class="text-muted" style="font-size: 12px;">
                                                            <i class="ti ti-world me-1"></i> Semua Jabatan & Departemen
                                                        </div>
                                                    @else
                                                        <div class="fw-bold text-dark" style="font-size: 14px;">
                                                            {{ $d->jabatan->nama_jabatan ?? '-' }}
                                                            <span class="text-muted fw-normal" style="font-size: 12px;">({{ $d->kode_jabatan }})</span>
                                                        </div>
                                                        <div class="text-muted" style="font-size: 12px;">
                                                            <i class="ti ti-building me-1"></i> {{ $d->departemen->nama_dept ?? '-' }}
                                                        </div>
                                                    @endif
                                                    <div class="mt-1">
                                                        <span class="badge bg-label-success" style="font-size: 10px;">Terkonfigurasi</span>
                                                        <span class="badge bg-label-info" style="font-size: 10px;">{{ $d->details->count() }} Indikator</span>
                                                    </div>
                                                </div>
                                                <!-- Status & Date -->
                                                <div class="col-md-3 border-start border-end d-none d-md-block text-center">
                                                    <div class="mb-1">
                                                        <span class="badge bg-success py-1 px-2" style="font-size: 10px;">Aktif</span>
                                                    </div>
                                                    <div class="text-muted" style="font-size: 11px;">
                                                        Update: {{ date('d-m-Y', strtotime($d->updated_at)) }}
                                                    </div>
                                                    <div class="text-muted" style="font-size: 10px;">
                                                        {{ $d->details->count() }} Item KPI
                                                    </div>
                                                </div>
                                                <!-- Actions -->
                                                <div class="col-md-4 text-end">
                                                    <div class="d-flex flex-column align-items-end gap-1">
                                                        <div class="btn-group shadow-sm" role="group">
                                                            <a href="{{ route('kpi.indicators.edit', $d->id) }}"
                                                                class="btn btn-sm btn-outline-primary py-1 px-2" title="Edit Indikator">
                                                                <i class="ti ti-edit"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 delete-btn"
                                                                data-id="{{ $d->id }}" title="Hapus">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="card">
                                        <div class="card-body text-center py-5">
                                            <i class="ti ti-folder-off text-muted" style="font-size: 48px;"></i>
                                            <p class="text-muted mt-2">Data Jabatan Kosong</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $kpi_indicators->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Konfigurasi -->
<div class="modal modal-blur fade" id="modal-tambahkonfigurasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Jabatan dan Departemen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('kpi.indicators.store') }}" method="POST" id="formPilihJabatan">
                    @csrf
                    <div class="form-floating mb-3">
                        <select name="kode_jabatan" id="kode_jabatan" class="form-select" required>
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach ($jabatan_list as $item)
                                <option value="{{ $item->kode_jabatan }}">{{ $item->nama_jabatan }}</option>
                            @endforeach
                        </select>
                        <label for="kode_jabatan">Pilih Jabatan</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select name="kode_dept" id="kode_dept" class="form-select" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach ($departemen_list as $item)
                                <option value="{{ $item->kode_dept }}">{{ $item->nama_dept }}</option>
                            @endforeach
                        </select>
                        <label for="kode_dept">Pilih Departemen</label>
                    </div>

                    <div class="modal-footer px-0 pb-0 border-top-0">
                        <button type="button" class="btn btn-text-secondary me-auto" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            Lanjut Konfigurasi <i class="ti ti-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form Delete (Hidden) -->
<form id="formDelete" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('myscript')
    <script>
        $(document).ready(function() {
            $('#btnTambahKonfigurasi').click(function(e) {
                e.preventDefault();
                $('#modal-tambahkonfigurasi').modal('show');
            });

            $('#formPilihJabatan').submit(function(e) {
                e.preventDefault();
                var kode_jabatan = $('#kode_jabatan').val();
                var kode_dept = $('#kode_dept').val();
                if (kode_jabatan && kode_dept) {
                    window.location.href = "{{ route('kpi.indicators.create') }}?kode_jabatan=" + kode_jabatan + "&kode_dept=" + kode_dept;
                } else {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Pilih Jabatan dan Departemen terlebih dahulu!',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                }
            });

            $('.delete-btn').click(function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Data KPI untuk jabatan ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = $('#formDelete');
                        form.attr('action', "{{ route('kpi.indicators.destroy', ':id') }}".replace(':id', id));
                        form.submit();
                    }
                })
            });
        });
    </script>
@endpush
