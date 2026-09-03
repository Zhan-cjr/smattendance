@extends('layouts.app')
@section('titlepage', 'BPJS Tenaga Kerja')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            BPJS Tenaga Kerja
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data iuran BPJS Tenaga Kerja karyawan.
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
                    <i class="ti ti-shield-check ti-xs me-1"></i> BPJS Tenaga Kerja
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('bpjstenagakerja.create')
                <a href="#" class="btn btn-primary" id="btnCreate">
                    <i class="ti ti-plus me-1"></i> Tambah BPJS Tenaga Kerja
                </a>
            @endcan
        </div>
        <form action="{{ route('bpjstenagakerja.index') }}">
            <div class="row g-2 mb-3">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <x-input-with-icon label="Cari Nama Karyawan" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                        icon="ti ti-search" hideLabel />
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                        selected="{{ Request('kode_cabang') }}" hideLabel />
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                        selected="{{ Request('kode_dept') }}" upperCase="true" hideLabel />
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12">
                    <button class="btn btn-primary w-100"><i class="ti ti-search me-1"></i> Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-layout-grid me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Data BPJS Tenaga Kerja</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3">KODE</th>
                                <th class="text-white py-3">NIK</th>
                                <th class="text-white py-3">NAMA KARYAWAN</th>
                                <th class="text-white py-3">DEPT</th>
                                <th class="text-white py-3">CABANG</th>
                                <th class="text-white py-3 text-end">JUMLAH</th>
                                <th class="text-white py-3">BERLAKU</th>
                                <th class="text-white py-3 text-center" style="width: 100px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bpjstenagakerja as $d)
                                <tr>
                                    <td class="py-2">{{ $d->kode_bpjs_tk }}</td>
                                    <td class="py-2">{{ $d->nik_show ?? $d->nik }}</td>
                                    <td class="py-2 fw-bold text-primary">{{ $d->nama_karyawan }}</td>
                                    <td class="py-2">{{ $d->kode_dept }}</td>
                                    <td class="py-2">{{ $d->kode_cabang }}</td>
                                    <td class="py-2 text-end fw-bold">{{ formatAngka($d->jumlah) }}</td>
                                    <td class="py-2">{{ date('d-m-Y', strtotime($d->tanggal_berlaku)) }}</td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('bpjstenagakerja.edit')
                                                <a href="#" class="btn btn-sm btnEdit px-2 py-1 border-0 rounded-0"
                                                    kode_bpjs_tk="{{ Crypt::encrypt($d->kode_bpjs_tk) }}" title="Edit"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-edit fs-6 text-primary"></i>
                                                </a>
                                            @endcan

                                            @can('bpjstenagakerja.delete')
                                                <form method="POST" name="deleteform" class="deleteform m-0"
                                                    action="{{ route('bpjstenagakerja.delete', Crypt::encrypt($d->kode_bpjs_tk)) }}">
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
                            @if($bpjstenagakerja->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3 d-flex justify-content-end">
            {{ $bpjstenagakerja->links() }}
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
            $(".modal-title").text("Tambah BPJS Tenaga Kerja");
            $("#loadmodal").load("{{ route('bpjstenagakerja.create') }}");
        });

        $(".btnEdit").click(function() {
            loading();
            const kode_bpjs_tk = $(this).attr("kode_bpjs_tk");
            $("#modal").modal("show");
            $(".modal-title").text("Edit BPJS Tenaga Kerja");
            $("#loadmodal").load(`/bpjstenagakerja/${kode_bpjs_tk}/edit`);
        });
    });
</script>
@endpush
