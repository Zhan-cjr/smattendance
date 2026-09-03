@extends('layouts.app')
@section('titlepage', 'Penyesuaian Gaji')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Penyesuaian Gaji
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data penyesuaian gaji bulanan.
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
                    <i class="ti ti-adjustments-horizontal ti-xs me-1"></i> Penyesuaian Gaji
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-5 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('penyesuaiangaji.create')
                <a href="#" class="btn btn-primary" id="btnCreate">
                    <i class="ti ti-plus me-1"></i> Tambah Penyesuaian Gaji
                </a>
            @endcan
        </div>
        <form action="{{ route('penyesuaiangaji.index') }}">
            <div class="row g-2 mb-3">
                <div class="col-lg-10 col-md-10 col-sm-12">
                    <div class="form-group">
                        <select name="tahun" id="tahun" class="form-select select2">
                            <option value="">Pilih Tahun</option>
                            @for ($t = $start_year; $t <= date('Y'); $t++)
                                <option {{ date('Y') == $t ? 'selected' : '' }} value="{{ $t }}">{{ $t }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-12">
                    <button class="btn btn-primary w-100"><i class="ti ti-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-5 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-layout-grid me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Data Penyesuaian Gaji</h6>
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
                                <th class="text-white py-3 text-center" style="width: 120px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penyesuaiangaji as $d)
                                <tr>
                                    <td class="py-2 fw-medium text-primary">{{ $d->kode_penyesuaian_gaji }}</td>
                                    <td class="py-2">{{ getNamabulan($d->bulan) }}</td>
                                    <td class="py-2 text-center">{{ $d->tahun }}</td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('penyesuaiangaji.edit')
                                                <a href="{{ route('penyesuaiangaji.setkaryawan', Crypt::encrypt($d->kode_penyesuaian_gaji)) }}"
                                                    class="btn btn-sm px-2 py-1 border-0 rounded-0" title="Set Karyawan"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-users fs-6 text-primary"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btnEdit px-2 py-1 border-0 rounded-0 border-start"
                                                    kode_penyesuaian_gaji="{{ Crypt::encrypt($d->kode_penyesuaian_gaji) }}" title="Edit"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-edit fs-6 text-success"></i>
                                                </a>
                                            @endcan
                                            @can('penyesuaiangaji.delete')
                                                <form method="POST" name="deleteform" class="deleteform m-0"
                                                    action="{{ route('penyesuaiangaji.delete', Crypt::encrypt($d->kode_penyesuaian_gaji)) }}">
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
                            @if($penyesuaiangaji->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
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
@endsection

@push('myscript')
<script>
    $(function() {
        function loading() {
            $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
        };
        loading();

        $("#btnCreate").click(function(e) {
            e.preventDefault();
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Penyesuaian Gaji");
            $("#loadmodal").html('');
            $("#loadmodal").load("{{ route('penyesuaiangaji.create') }}");
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            loading();
            const kode_penyesuaian_gaji = $(this).attr("kode_penyesuaian_gaji");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Penyesuaian Gaji");
            $("#loadmodal").load(`/penyesuaiangaji/${kode_penyesuaian_gaji}/edit`);
        });
    });
</script>
@endpush
