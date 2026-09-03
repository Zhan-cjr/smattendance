@extends('layouts.app')
@section('titlepage', 'Denda')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Denda Keterlambatan
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen denda berdasarkan durasi keterlambatan.
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
                    <i class="ti ti-receipt-tax ti-xs me-1"></i> Denda
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="#" class="btn btn-primary" id="btnCreate">
                <i class="ti ti-plus me-1"></i> Tambah Denda
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-receipt-tax me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Data Denda</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3">DARI</th>
                                <th class="text-white py-3">SAMPAI</th>
                                <th class="text-white py-3 text-end">DENDA</th>
                                <th class="text-white py-3 text-center" style="width: 100px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($denda as $d)
                                <tr>
                                    <td class="py-2"><span class="badge bg-label-secondary">{{ $d->dari }} Menit</span></td>
                                    <td class="py-2"><span class="badge bg-label-secondary">{{ $d->sampai }} Menit</span></td>
                                    <td class="py-2 text-end fw-bold text-danger">Rp {{ formatAngka($d->denda) }}</td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            <a href="#" class="btn btn-sm btnEdit px-2 py-1 border-0 rounded-0"
                                                id="{{ Crypt::encrypt($d->id) }}" title="Edit"
                                                style="background: #f8f9fa;">
                                                <i class="ti ti-edit fs-6 text-primary"></i>
                                            </a>
                                            <form method="POST" name="deleteform" class="deleteform m-0"
                                                action="{{ route('denda.delete', Crypt::encrypt($d->id)) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm delete-confirm px-2 py-1 border-0 rounded-0 border-start"
                                                    title="Hapus" style="background: #f8f9fa;">
                                                    <i class="ti ti-trash fs-6 text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if($denda->isEmpty())
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

        $("#btnCreate").click(function(e) {
            e.preventDefault();
            loading();
            $('#modal').modal("show");
            $(".modal-title").text("Tambah Denda");
            $("#loadmodal").load('/denda/create');
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            loading();
            var id = $(this).attr("id");
            $('#modal').modal("show");
            $(".modal-title").text("Edit Denda");
            $("#loadmodal").load('/denda/' + id + '/edit');
        });
    });
</script>
@endpush
