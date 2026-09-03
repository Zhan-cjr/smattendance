@extends('layouts.app')
@section('titlepage', 'Jam Kerja Departemen')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Jam Kerja Departemen
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data jam kerja per departemen dan cabang.
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
                    <i class="ti ti-clock ti-xs me-1"></i> Jam Kerja Departemen
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('jamkerja.create')
                <a href="#" class="btn btn-primary" id="btnCreate">
                    <i class="ti ti-plus me-1"></i> Tambah Jam Kerja
                </a>
            @endcan
        </div>
        <form action="{{ URL::current() }}">
            <div class="row g-2 mb-3">
                <div class="col-lg-10 col-md-9 col-sm-12">
                    <select name="kode_cabang" id="kode_cabang" class="form-select select2Kodecabang">
                        <option value="">Semua Cabang</option>
                        @foreach ($cabang as $c)
                            <option value="{{ $c->kode_cabang }}"
                                {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                {{ textUpperCase($c->nama_cabang) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12">
                    <button class="btn btn-primary w-100"><i class="ti ti-search me-1"></i> Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-layout-grid me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Data Jam Kerja Departemen</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3" style="width: 60px;">NO.</th>
                                <th class="text-white py-3">KODE</th>
                                <th class="text-white py-3">CABANG</th>
                                <th class="text-white py-3">DEPARTEMEN</th>
                                <th class="text-white py-3 text-center" style="width: 100px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jamkerjabydept as $d)
                                <tr>
                                    <td class="py-2">{{ $loop->iteration + $jamkerjabydept->firstItem() - 1 }}</td>
                                    <td class="fw-bold py-2">{{ $d->kode_jk_dept }}</td>
                                    <td class="py-2 text-uppercase">{{ $d->nama_cabang }}</td>
                                    <td class="py-2 text-uppercase">{{ $d->nama_dept }}</td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('jamkerjabydept.edit')
                                                <a href="#" class="btn btn-sm btnEdit px-2 py-1 border-0 rounded-0"
                                                    kode_jk_dept="{{ Crypt::encrypt($d->kode_jk_dept) }}" title="Edit"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-edit fs-6 text-primary"></i>
                                                </a>
                                            @endcan

                                            @can('jamkerjabydept.delete')
                                                <form method="POST" name="deleteform" class="deleteform m-0"
                                                    action="{{ route('jamkerjabydept.delete', Crypt::encrypt($d->kode_jk_dept)) }}">
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
                            @if($jamkerjabydept->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-2">
            {{ $jamkerjabydept->links() }}
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
            $(".modal-title").text("Tambah Jam Kerja");
            $("#loadmodal").load('/jamkerjabydept/create');
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            loading();
            var kode_jk_dept = $(this).attr("kode_jk_dept");
            $('#modal').modal("show");
            $(".modal-title").text("Edit Jam Kerja");
            $("#loadmodal").load('/jamkerjabydept/' + kode_jk_dept + '/edit');
        });
    });
</script>
@endpush
