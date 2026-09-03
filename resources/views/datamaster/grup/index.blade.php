@extends('layouts.app')
@section('titlepage', 'Grup')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Grup
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data grup dan unit kerja.
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
                    <i class="ti ti-users ti-xs me-1"></i> Grup
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('grup.create')
                <a href="#" class="btn btn-primary" id="btnCreate">
                    <i class="ti ti-plus me-1"></i> Tambah Grup
                </a>
            @endcan
        </div>
        <form action="{{ route('grup.index') }}">
            <div class="row g-2 mb-3">
                <div class="col-lg-10 col-md-9 col-sm-12">
                    <x-input-with-icon label="Cari Grup" value="{{ Request('nama_grup') }}" name="nama_grup"
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
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-layout-grid me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Data Grup</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3" style="width: 60px;">NO.</th>
                                <th class="text-white py-3">KODE</th>
                                <th class="text-white py-3">NAMA GRUP</th>
                                <th class="text-white py-3 text-center" style="width: 150px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($grup as $g)
                                <tr>
                                    <td class="py-2">{{ $loop->iteration }}</td>
                                    <td class="fw-bold py-2">{{ $g->kode_grup }}</td>
                                    <td class="py-2">{{ $g->nama_grup }}</td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('grup.edit')
                                                <a href="#" class="btn btn-sm btnEdit px-2 py-1 border-0 rounded-0"
                                                    kode_grup="{{ Crypt::encrypt($g->kode_grup) }}" title="Edit"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-edit fs-6 text-primary"></i>
                                                </a>
                                            @endcan

                                            @can('grup.detail')
                                                <a href="#" class="btn btn-sm btnDetail px-2 py-1 border-0 rounded-0 border-start"
                                                    kode_grup="{{ Crypt::encrypt($g->kode_grup) }}" title="Detail"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-users fs-6 text-info"></i>
                                                </a>
                                            @endcan

                                            @can('grup.setJamKerja')
                                                <a href="#" class="btn btn-sm btnSetJamKerja px-2 py-1 border-0 rounded-0 border-start"
                                                    kode_grup="{{ Crypt::encrypt($g->kode_grup) }}" title="Set Jam Kerja"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-clock-plus fs-6 text-warning"></i>
                                                </a>
                                            @endcan

                                            @can('grup.delete')
                                                <form method="POST" name="deleteform" class="deleteform m-0"
                                                    action="{{ route('grup.delete', Crypt::encrypt($g->kode_grup)) }}">
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
                            @if($grup->isEmpty())
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

        $("#btnCreate").click(function() {
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data Grup");
            $("#loadmodal").load("{{ route('grup.create') }}");
        });


        $(".btnEdit").click(function() {
            loading();
            const kode_grup = $(this).attr("kode_grup");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Grup");
            $("#loadmodal").load(`/grup/${kode_grup}`);
        });

        $(".btnDetail").click(function() {
            const kode_grup = $(this).attr("kode_grup");
            window.location.href = `/grup/${kode_grup}/detail`;
        });

        $(".btnSetJamKerja").click(function() {
            const kode_grup = $(this).attr("kode_grup");
            window.location.href = `/grup/${kode_grup}/set-jam-kerja`;
        });
    });
</script>
@endpush
