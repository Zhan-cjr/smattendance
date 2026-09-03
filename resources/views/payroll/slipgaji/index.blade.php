@extends('layouts.app')
@section('titlepage', 'Slip Gaji')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Slip Gaji
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen penerbitan dan pengiriman slip gaji karyawan.
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
                        <i class="ti ti-cash ti-xs me-1"></i> Payroll
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-file-description ti-xs me-1"></i> Slip Gaji
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <!-- Kolom Kiri: Slip Gaji Bulanan -->
    <div class="col-lg-5 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('slipgaji.create')
                <a href="#" class="btn btn-primary" id="btnCreate">
                    <i class="ti ti-plus me-1"></i> Buat Slip Gaji
                </a>
            @endcan
        </div>
        <form action="{{ route('slipgaji.index') }}">
            <div class="row g-2 mb-3">
                <div class="col-lg-10 col-md-10 col-sm-12">
                    <div class="form-group">
                        <select name="tahun" id="tahun" class="form-select select2">
                            <option value="">Pilih Tahun</option>
                            @for ($t = $start_year; $t <= date('Y'); $t++)
                                <option {{ date('Y') == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-layout-grid me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Data Slip Gaji Bulanan</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3">KODE</th>
                                <th class="text-white py-3">BULAN</th>
                                <th class="text-white py-3">TAHUN</th>
                                <th class="text-white py-3 text-center">STATUS</th>
                                <th class="text-white py-3 text-center" style="width: 100px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slipgaji as $d)
                                <tr>
                                    <td class="py-2 fw-medium text-primary">{{ $d->kode_slip_gaji }}</td>
                                    <td class="py-2">{{ getNamabulan($d->bulan) }}</td>
                                    <td class="py-2">{{ $d->tahun }}</td>
                                    <td class="py-2 text-center">
                                        @if ($d->status == 0)
                                            <span class="badge bg-label-warning px-3 rounded-pill">Pending</span>
                                        @else
                                            <span class="badge bg-label-success px-3 rounded-pill">Published</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('slipgaji.edit')
                                                <a href="#" class="btn btn-sm btnEdit px-2 py-1 border-0 rounded-0"
                                                    kode_slip_gaji="{{ Crypt::encrypt($d->kode_slip_gaji) }}" title="Edit"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-edit fs-6 text-success"></i>
                                                </a>
                                            @endcan
                                            @can('slipgaji.delete')
                                                <form method="POST" name="deleteform" class="deleteform m-0"
                                                    action="{{ route('slipgaji.delete', Crypt::encrypt($d->kode_slip_gaji)) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm delete-confirm px-2 py-1 border-0 rounded-0 border-start"
                                                        title="Hapus" style="background: #f8f9fa;">
                                                        <i class="ti ti-trash fs-6 text-danger"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($slipgaji->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Slip Gaji Harian -->
    <div class="col-lg-7 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('slipgaji.create')
                <a href="#" class="btn btn-primary" id="btnCreateHarian">
                    <i class="ti ti-plus me-1"></i> Buat Slip Gaji Harian
                </a>
            @endcan
        </div>
        <form action="{{ route('slipgaji.index') }}">
            <div class="row g-2 mb-3">
                <div class="col-lg-10 col-md-10 col-sm-12">
                    <div class="form-group">
                        <select name="tahun_harian" id="tahun_harian" class="form-select">
                            <option value="">Pilih Tahun</option>
                            @for ($t = $start_year; $t <= date('Y'); $t++)
                                <option {{ request('tahun_harian') == $t ? 'selected' : (empty(request('tahun_harian')) && date('Y') == $t ? 'selected' : '') }} value="{{ $t }}">{{ $t }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-layout-grid me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Data Slip Gaji Harian</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3">KODE</th>
                                <th class="text-white py-3">TGL SLIP</th>
                                <th class="text-white py-3">DARI</th>
                                <th class="text-white py-3">SAMPAI</th>
                                <th class="text-white py-3 text-center">KARYAWAN</th>
                                <th class="text-white py-3 text-center">STATUS</th>
                                <th class="text-white py-3 text-center" style="width: 100px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slipgaji_harian as $d)
                                <tr>
                                    <td class="py-2 fw-medium text-primary">{{ $d->kode_slip_gaji_harian }}</td>
                                    <td class="py-2">{{ $d->tanggal_slip ? date('d/m/Y', strtotime($d->tanggal_slip)) : '-' }}</td>
                                    <td class="py-2">{{ date('d/m/Y', strtotime($d->dari)) }}</td>
                                    <td class="py-2">{{ date('d/m/Y', strtotime($d->sampai)) }}</td>
                                    <td class="py-2 text-center">
                                        <a href="javascript:void(0)" class="badge bg-label-info px-3 rounded-pill btnShowDetailHarian" 
                                            kode_slip="{{ Crypt::encrypt($d->kode_slip_gaji_harian) }}">
                                            {{ $d->detail_count }} orang
                                        </a>
                                    </td>
                                    <td class="py-2 text-center">
                                        @if ($d->status == 0)
                                            <span class="badge bg-label-warning px-3 rounded-pill">Pending</span>
                                        @else
                                            <span class="badge bg-label-success px-3 rounded-pill">Published</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('slipgaji.edit')
                                                <a href="#" class="btn btn-sm btnEditHarian px-2 py-1 border-0 rounded-0"
                                                    kode_slip="{{ Crypt::encrypt($d->kode_slip_gaji_harian) }}" title="Edit"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-edit fs-6 text-success"></i>
                                                </a>
                                            @endcan
                                            @can('slipgaji.delete')
                                                <form method="POST" name="deleteform" class="deleteform m-0"
                                                    action="{{ route('slipgajiharian.delete', Crypt::encrypt($d->kode_slip_gaji_harian)) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm delete-confirm px-2 py-1 border-0 rounded-0 border-start"
                                                        title="Hapus" style="background: #f8f9fa;">
                                                        <i class="ti ti-trash fs-6 text-danger"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($slipgaji_harian->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />
<x-modal-form id="modalHarian" size="modal-lg" show="loadmodalHarian" />
@endsection

@push('myscript')
<script>
    $(function() {
        // === Slip Gaji Bulanan ===
        $("#btnCreate").click(function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $("#modal").find(".modal-title").text("Buat Slip Gaji");
            $("#loadmodal").load(`/slipgaji/create`);
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            var kode_slip_gaji = $(this).attr("kode_slip_gaji");
            $("#modal").modal("show");
            $("#modal").find(".modal-title").text("Edit Slip Gaji");
            $("#loadmodal").load(`/slipgaji/${kode_slip_gaji}/edit`);
        });

        // === Slip Gaji Harian ===
        $("#btnCreateHarian").click(function(e) {
            e.preventDefault();
            $("#modalHarian").modal("show");
            $("#modalHarian").find(".modal-title").text("Buat Slip Gaji Harian");
            $("#loadmodalHarian").load(`/slipgajiharian/create`);
        });

        $(".btnEditHarian").click(function(e) {
            e.preventDefault();
            var kode_slip = $(this).attr("kode_slip");
            $("#modalHarian").modal("show");
            $("#modalHarian").find(".modal-title").text("Edit Slip Gaji Harian");
            $("#loadmodalHarian").load(`/slipgajiharian/${kode_slip}/edit`);
        });

        $(".btnShowDetailHarian").click(function(e) {
            e.preventDefault();
            var kode_slip = $(this).attr("kode_slip");
            $("#modalHarian").modal("show");
            $("#modalHarian").find(".modal-title").text("Detail Karyawan");
            $("#loadmodalHarian").load(`/slipgajiharian/${kode_slip}/show`);
        });
    });
</script>
@endpush
