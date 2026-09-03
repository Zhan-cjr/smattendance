@extends('layouts.app')
@section('titlepage', 'Tunjangan')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Tunjangan
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data tunjangan karyawan.
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
                    <i class="ti ti-gift ti-xs me-1"></i> Tunjangan
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('tunjangan.create')
                <a href="#" class="btn btn-primary" id="btnCreate">
                    <i class="ti ti-plus me-1"></i> Tambah Tunjangan
                </a>
            @endcan
        </div>
        <form action="{{ route('tunjangan.index') }}">
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
                    <h6 class="card-title mb-0 text-white">Data Tunjangan</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3" rowspan="2">KODE</th>
                                <th class="text-white py-3" rowspan="2">NIK</th>
                                <th class="text-white py-3" rowspan="2">NAMA KARYAWAN</th>
                                <th class="text-white py-3" rowspan="2">DEPT</th>
                                <th class="text-white py-3" rowspan="2">CABANG</th>
                                <th class="text-white py-2 text-center" colspan="{{ count($jenis_tunjangan) }}">TUNJANGAN</th>
                                <th class="text-white py-3" rowspan="2">BERLAKU</th>
                                <th class="text-white py-3 text-center" rowspan="2" style="width: 100px;">#</th>
                            </tr>
                            <tr>
                                @foreach ($jenis_tunjangan as $d)
                                    <th class="text-white py-2 text-center small" style="background-color: rgba(255,255,255,0.1);">{{ strtoupper($d->jenis_tunjangan) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tunjangan as $d)
                                <tr>
                                    <td class="py-2">{{ $d->kode_tunjangan }}</td>
                                    <td class="py-2">{{ $d->nik_show ?? $d->nik }}</td>
                                    <td class="py-2 fw-bold text-primary">{{ $d->nama_karyawan }}</td>
                                    <td class="py-2">{{ $d->kode_dept }}</td>
                                    <td class="py-2">{{ $d->kode_cabang }}</td>
                                    @foreach ($jenis_tunjangan as $j)
                                        <td class="py-2 text-end fw-semibold">{{ formatAngka($d->{"jumlah_$j->kode_jenis_tunjangan"}) }}</td>
                                    @endforeach
                                    <td class="py-2">{{ date('d-m-Y', strtotime($d->tanggal_berlaku)) }}</td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('tunjangan.edit')
                                                <a href="#" class="btn btn-sm btnEdit px-2 py-1 border-0 rounded-0"
                                                    kode_tunjangan="{{ Crypt::encrypt($d->kode_tunjangan) }}" title="Edit"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-edit fs-6 text-primary"></i>
                                                </a>
                                            @endcan

                                            @can('tunjangan.delete')
                                                <form method="POST" name="deleteform" class="deleteform m-0"
                                                    action="{{ route('tunjangan.delete', Crypt::encrypt($d->kode_tunjangan)) }}">
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
                            @if($tunjangan->isEmpty())
                                <tr>
                                    <td colspan="{{ 7 + count($jenis_tunjangan) }}" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3 d-flex justify-content-end">
            {{ $tunjangan->links() }}
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
            $(".modal-title").text("Tambah Data Tunjangan");
            $("#loadmodal").load("{{ route('tunjangan.create') }}");
        });

        $(".btnEdit").click(function() {
            loading();
            const kode_tunjangan = $(this).attr("kode_tunjangan");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Tunjangan");
            $("#loadmodal").load(`/tunjangan/${kode_tunjangan}/edit`);
        });
    });
</script>
@endpush
