@extends('layouts.app')
@section('titlepage', 'Cabang')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Cabang
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data cabang dan lokasi presensi.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="ti ti-home-2 ti-xs"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">
                        <i class="ti ti-database ti-xs me-1"></i> Data Master
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-building-community ti-xs me-1"></i> Cabang
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('cabang.create')
                <a href="#" class="btn btn-primary" id="btncreateCabang">
                    <i class="ti ti-plus me-1"></i> Tambah Cabang
                </a>
            @endcan
        </div>
        <form action="{{ route('cabang.index') }}">
            <div class="row g-2 mb-4">
                <div class="col-lg-10 col-md-9 col-sm-12">
                    <x-input-with-icon label="Cari Nama Cabang" value="{{ Request('nama_cabang') }}" name="nama_cabang"
                        icon="ti ti-search" hideLabel />
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12">
                    <button class="btn btn-primary w-100"><i class="ti ti-search me-1"></i> Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    @foreach ($cabang as $d)
        <div class="col-12 mb-2">
            <div class="card shadow-none border rounded-3 hover-shadow-sm transition-all">
                <div class="card-body py-3 px-4">
                    <div class="row align-items-center">
                        <!-- Main Info -->
                        <div class="col-lg-4 col-md-12 mb-3 mb-lg-0">
                            <div class="d-flex align-items-center">
                                <div class="bg-label-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; min-width: 42px;">
                                    <i class="ti ti-building-community fs-4"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="mb-0 fw-bold">{{ textUpperCase($d->nama_cabang) }}</h6>
                                        <span class="badge bg-label-secondary text-xs px-2 py-0" style="font-size: 10px;">{{ $d->kode_cabang }}</span>
                                    </div>
                                    <div class="text-muted small text-truncate d-flex align-items-center">
                                        <i class="ti ti-map-pin me-1 text-xs"></i>
                                        <span class="text-truncate" title="{{ $d->alamat_cabang }}">{{ $d->alamat_cabang }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Technical Details -->
                        <div class="col-lg-5 col-md-12 mb-3 mb-lg-0">
                            <div class="row g-0 align-items-center">
                                <div class="col-md-4 border-end-md px-lg-3">
                                    <div class="d-flex flex-column">
                                        <small class="text-muted mb-0" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Timezone</small>
                                        <span class="fw-semibold small text-dark">{{ $d->timezone }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 border-end-md px-lg-3">
                                    <div class="d-flex flex-column">
                                        <small class="text-muted mb-0" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Radius</small>
                                        <span class="fw-semibold small text-dark">{{ $d->radius_cabang }}m</span>
                                    </div>
                                </div>
                                <div class="col-md-5 px-lg-3">
                                    <div class="d-flex flex-column">
                                        <small class="text-muted mb-0" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Coordinates</small>
                                        <span class="fw-semibold small text-primary text-truncate cursor-pointer" title="{{ $d->lokasi_cabang }}">
                                            <i class="ti ti-location-filled me-1" style="font-size: 10px;"></i>{{ $d->lokasi_cabang }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="col-lg-3 col-md-12 text-lg-end">
                            <div class="d-flex align-items-center justify-content-lg-end gap-2">
                                @can('cabang.edit')
                                    <a href="#" class="btn btn-sm btn-icon btn-label-primary editCabang shadow-none"
                                        kode_cabang="{{ Crypt::encrypt($d->kode_cabang) }}" title="Edit">
                                        <i class="ti ti-edit fs-5"></i>
                                    </a>
                                @endcan

                                @can('cabang.delete')
                                    <form method="POST" name="deleteform" class="deleteform m-0"
                                        action="{{ route('cabang.delete', Crypt::encrypt($d->kode_cabang)) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-label-danger delete-confirm shadow-none"
                                            title="Hapus">
                                            <i class="ti ti-trash fs-5"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if($cabang->isEmpty())
        <div class="col-12 text-center py-5">
            <div class="mb-3">
                <i class="ti ti-building-community fs-1 text-muted opacity-25"></i>
            </div>
            <h6 class="text-muted fw-normal">Data cabang tidak ditemukan.</h6>
        </div>
    @endif
</div>

<div class="d-flex justify-content-end mt-3">
    {{ $cabang->links('pagination::bootstrap-5') }}
</div>

<style>
    .hover-shadow-sm:hover {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        border-color: rgba(var(--bs-primary-rgb), 0.3) !important;
    }
    .transition-all {
        transition: all 0.2s ease-in-out;
    }
    @media (min-width: 992px) {
        .border-end-md {
            border-right: 1px solid #e9ecef !important;
        }
    }
    .btn-label-primary {
        background-color: #f0f4ff;
        color: var(--bs-primary);
        border: none;
    }
    .btn-label-primary:hover {
        background-color: var(--bs-primary);
        color: white;
    }
    .btn-label-danger {
        background-color: #fff5f5;
        color: var(--bs-danger);
        border: none;
    }
    .btn-label-danger:hover {
        background-color: var(--bs-danger);
        color: white;
    }
</style>


<x-modal-form id="mdlcreateCabang" size="" show="loadcreateCabang" title="Tambah Cabang" />
<x-modal-form id="mdleditCabang" size="" show="loadeditCabang" title="Edit Cabang" />
@endsection

@push('myscript')
<script>
    $(function() {
        $("#btncreateCabang").click(function(e) {
            $('#mdlcreateCabang').modal("show");
            $("#loadcreateCabang").load('/cabang/create');
        });

        $(".editCabang").click(function(e) {
            var kode_cabang = $(this).attr("kode_cabang");
            e.preventDefault();
            $('#mdleditCabang').modal("show");
            $("#loadeditCabang").load('/cabang/' + kode_cabang);
        });
    });
</script>
@endpush
