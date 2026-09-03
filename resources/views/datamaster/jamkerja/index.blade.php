@extends('layouts.app')
@section('titlepage', 'Jam Kerja')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Jam Kerja
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data jadwal jam kerja organisasi.
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
                    <i class="ti ti-clock ti-xs me-1"></i> Jam Kerja
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('jamkerja.create')
                <a href="#" class="btn btn-primary" id="btnCreate">
                    <i class="ti ti-plus me-1"></i> Tambah Jam Kerja
                </a>
            @endcan
        </div>
        <form action="{{ route('jamkerja.index') }}">
            <div class="row g-2 mb-3">
                <div class="col-lg-10 col-md-9 col-sm-12">
                    <x-input-with-icon label="Cari Nama Jam Kerja" value="{{ Request('nama_jam_kerja_search') }}"
                        name="nama_jam_kerja_search" icon="ti ti-search" hideLabel />
                </div>
                <div class="col-lg-2 col-md-3 col-sm-12">
                    <button class="btn btn-primary w-100"><i class="ti ti-search me-1"></i> Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-clock me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Data Jam Kerja</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3" style="width: 60px;">NO.</th>
                                <th class="text-white py-3">KODE</th>
                                <th class="text-white py-3">NAMA JAM KERJA</th>
                                <th class="text-white py-3">MASUK</th>
                                <th class="text-white py-3">PULANG</th>
                                <th class="text-white py-3 text-center">ISTIRAHAT</th>
                                <th class="text-white py-3">MULAI</th>
                                <th class="text-white py-3">AKHIR</th>
                                <th class="text-white py-3 text-center">LINTAS</th>
                                <th class="text-white py-3 text-center">TOTAL</th>
                                <th class="text-white py-3 text-center">WARNA</th>
                                <th class="text-white py-3 text-center" style="width: 100px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($jamkerja as $d)
                                <tr>
                                    <td class="py-2">{{ $loop->iteration }}</td>
                                    <td class="fw-bold py-2">{{ $d->kode_jam_kerja }}</td>
                                    <td class="py-2">{{ $d->nama_jam_kerja }}</td>
                                    <td class="py-2">{{ $d->jam_masuk }}</td>
                                    <td class="py-2">{{ $d->jam_pulang }}</td>
                                    <td class="py-2 text-center">
                                        @if ($d->istirahat == 1)
                                            <i class="ti ti-checks text-success fs-5"></i>
                                        @else
                                            <i class="ti ti-square-x text-danger fs-5"></i>
                                        @endif
                                    </td>
                                    <td class="py-2 text-muted" style="font-size: 0.85rem;">{{ $d->jam_awal_istirahat != null ? date('H:i', strtotime($d->jam_awal_istirahat)) : '-' }}</td>
                                    <td class="py-2 text-muted" style="font-size: 0.85rem;">{{ $d->jam_akhir_istirahat != null ? date('H:i', strtotime($d->jam_akhir_istirahat)) : '-' }}</td>
                                    <td class="py-2 text-center">
                                        @if ($d->lintashari == 1)
                                            <i class="ti ti-checks text-success fs-5"></i>
                                        @else
                                            <i class="ti ti-square-x text-danger fs-5"></i>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center fw-bold">{{ $d->total_jam }}j</td>
                                    <td class="py-2 text-center">
                                        <div class="mx-auto" style="width: 24px; height: 24px; background-color: {{ $d->color }}; border: 2px solid white; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                    </td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('jamkerja.edit')
                                                <a href="#" class="btn btn-sm btnEdit px-2 py-1 border-0 rounded-0"
                                                    kode_jam_kerja="{{ Crypt::encrypt($d->kode_jam_kerja) }}" title="Edit"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-edit fs-6 text-primary"></i>
                                                </a>
                                            @endcan

                                            @can('jamkerja.delete')
                                                <form method="POST" name="deleteform" class="deleteform m-0"
                                                    action="{{ route('jamkerja.delete', Crypt::encrypt($d->kode_jam_kerja)) }}">
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
                            @if($jamkerja->isEmpty())
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
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
            $(".modal-title").text("Tambah Jam Kerja");
            $("#loadmodal").load("{{ route('jamkerja.create') }}");
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            loading();
            var kode_jam_kerja = $(this).attr("kode_jam_kerja");
            $('#modal').modal("show");
            $(".modal-title").text("Edit Jam Kerja");
            $("#loadmodal").load('/jamkerja/' + kode_jam_kerja + '/edit');
        });
    });
</script>
@endpush
