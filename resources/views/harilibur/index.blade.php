@extends('layouts.app')
@section('titlepage', 'Hari Libur')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Hari Libur
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data hari libur nasional dan khusus.
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
                    <i class="ti ti-calendar ti-xs me-1"></i> Hari Libur
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('harilibur.create')
                <a href="#" class="btn btn-primary" id="btnCreate">
                    <i class="ti ti-plus me-1"></i> Tambah Hari Libur
                </a>
            @endcan
        </div>
        <form action="{{ route('harilibur.index') }}" method="GET">
            <div class="row g-2 mb-3">
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <x-input-with-icon icon="ti ti-calendar" label="Dari" name="dari" datepicker="flatpickr-date"
                        :value="Request('dari')" hideLabel />
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <x-input-with-icon icon="ti ti-calendar" label="Sampai" name="sampai" datepicker="flatpickr-date"
                        :value="Request('sampai')" hideLabel />
                </div>
                @if ($user->hasRole(['super admin', 'gm administrasi']))
                    <div class="col-lg-4 col-md-9 col-sm-12">
                        <select name="kode_cabang_search" id="kode_cabang_search" class="form-select select2Kodecabangsearch">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}"
                                    {{ Request('kode_cabang_search') == $c->kode_cabang ? 'selected' : '' }}>
                                    {{ textUpperCase($c->nama_cabang) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-lg-2 col-md-3 col-sm-12">
                    <button class="btn btn-primary w-100" id="btnSearch"><i class="ti ti-search me-1"></i>Cari</button>
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
                    <i class="ti ti-calendar me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Data Hari Libur</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3" style="width: 60px;">NO.</th>
                                <th class="text-white py-3">KODE</th>
                                <th class="text-white py-3">TANGGAL</th>
                                <th class="text-white py-3">CABANG</th>
                                <th class="text-white py-3" style="width: 30%">KETERANGAN</th>
                                <th class="text-white py-3 text-center" style="width: 120px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($harilibur as $d)
                                <tr>
                                    <td class="py-2">{{ $loop->iteration + $harilibur->firstItem() - 1 }}</td>
                                    <td class="fw-bold py-2 text-primary">{{ $d->kode_libur }}</td>
                                    <td class="py-2">{{ formatIndo($d->tanggal) }}</td>
                                    <td class="py-2"><span class="badge bg-label-info text-uppercase">{{ $d->nama_cabang }}</span></td>
                                    <td class="py-2">{{ $d->keterangan }}</td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('harilibur.edit')
                                                <a href="#" class="btn btn-sm btnEdit px-2 py-1 border-0 rounded-0"
                                                    kode_libur="{{ Crypt::encrypt($d->kode_libur) }}" title="Edit"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-edit fs-6 text-primary"></i>
                                                </a>
                                            @endcan
                                            @can('harilibur.setharilibur')
                                                <a href="{{ route('harilibur.aturharilibur', Crypt::encrypt($d->kode_libur)) }}" 
                                                    class="btn btn-sm px-2 py-1 border-0 rounded-0 border-start" title="Atur"
                                                    style="background: #f8f9fa;">
                                                    <i class="ti ti-settings-cog fs-6 text-info"></i>
                                                </a>
                                            @endcan
                                            @can('harilibur.delete')
                                                <form method="POST" name="deleteform" class="deleteform m-0"
                                                    action="{{ route('harilibur.delete', Crypt::encrypt($d->kode_libur)) }}">
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
                            @if($harilibur->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-2">
            {{ $harilibur->links() }}
        </div>
    </div>
</div>

<x-modal-form id="modal" show="loadmodal" />
@endsection

@push('myscript')
<script>
    $(function() {
        const select2Kodecabangsearch = $(".select2Kodecabangsearch");
        if (select2Kodecabangsearch.length > 0) {
            select2Kodecabangsearch.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Semua Cabang',
                    allowClear: true,
                    dropdownParent: $this.parent()
                });
            });
        }

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
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Hari Libur");
            $("#loadmodal").load(`/harilibur/create`);
            $("#modal").find(".modal-dialog").removeClass("modal-lg");
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            const kode_libur = $(this).attr("kode_libur");
            loading();
            $("#modal").modal("show");
            $(".modal-title").text("Edit Hari Libur");
            $("#loadmodal").load(`/harilibur/${kode_libur}/edit`);
            $("#modal").find(".modal-dialog").removeClass("modal-lg");
        });
    });
</script>
@endpush
