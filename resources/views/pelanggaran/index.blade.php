@extends('layouts.app')
@section('titlepage', 'Pelanggaran')

@section('content')
@section('navigasi')
    <span>Pelanggaran</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('pelanggaran.create')
                    <a href="#" class="btn btn-primary" id="btnCreatePelanggaran">
                        <i class="ti ti-plus me-2"></i>Tambah Pelanggaran
                    </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('pelanggaran.index') }}">
                            <div class="row">
                                <div class="col-lg-3 col-sm-12 col-md-12">
                                    <div class="form-group mb-3">
                                        <select name="nik" id="nik_search" class="form-select select2Nik">
                                            <option value="">Semua Karyawan</option>
                                            @foreach ($karyawans as $karyawan)
                                                <option value="{{ $karyawan->nik }}" {{ Request('nik') == $karyawan->nik ? 'selected' : '' }}>
                                                    {{ $karyawan->nik_show ?? $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-12 col-md-12">
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-calendar"></i></span>
                                            <input type="text" class="form-control flatpickr-date" id="dari_search" name="dari"
                                                placeholder="Dari" value="{{ Request('dari') }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-12 col-md-12">
                                    <div class="form-group mb-3">
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-calendar"></i></span>
                                            <input type="text" class="form-control flatpickr-date" id="sampai_search" name="sampai"
                                                placeholder="Sampai" value="{{ Request('sampai') }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-12 col-md-12">
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-primary"><i class="ti ti-search me-1"></i>Cari</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
@forelse($pelanggaran as $index => $item)
                                    @php
                                        $colorClass = '';
                                        $badgeClass = '';
                                        switch ($item->jenis_sp) {
                                            case 'ST':
                                                $colorClass = 'border border-info';
                                                $badgeClass = 'bg-info';
                                                break;
                                            case 'SP1':
                                                $colorClass = 'border border-success';
                                                $badgeClass = 'bg-success';
                                                break;
                                            case 'SP2':
                                                $colorClass = 'border border-warning';
                                                $badgeClass = 'bg-warning';
                                                break;
                                            case 'SP3':
                                                $colorClass = 'border border-danger';
                                                $badgeClass = 'bg-danger';
                                                break;
                                            default:
                                                $colorClass = 'border border-secondary';
                                                $badgeClass = 'bg-secondary';
                                                break;
                                        }
                                    @endphp
                                    <div class="card mb-2 shadow-sm {{ $colorClass }}">
                                        <div class="card-body p-2">
                                            <div class="row align-items-center">
                                                <!-- Identity -->
                                                <div class="col-md-4">
                                                    <div class="fw-bold text-dark" style="font-size: 14px;">
                                                        {{ $item->nama_karyawan ?? 'N/A' }}
                                                        <span class="text-muted fw-normal" style="font-size: 12px;">({{ $item->nik_show ?? $item->nik }})</span>
                                                    </div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-label-primary" style="font-size: 10px;">{{ $item->nama_jabatan }}</span>
                                                        <span class="badge bg-label-info" style="font-size: 10px;">{{ $item->nama_dept }}</span>
                                                    </div>
                                                </div>
                                                <!-- Violation Info -->
                                                <div class="col-md-6 border-start border-end">
                                                    <div class="d-flex justify-content-between px-2 mb-1">
                                                        <span class="fw-bold text-dark" style="font-size: 12px;">{{ $item->no_dokumen }}</span>
                                                        <span class="badge {{ $badgeClass }}" style="font-size: 12px;">{{ $item->jenis_sp }}</span>
                                                    </div>
                                                    <div class="d-flex gap-3 px-2">
                                                        <div class="text-muted" style="font-size: 11px;">
                                                            <i class="ti ti-calendar me-1"></i>Tgl: {{ date('d-m-Y', strtotime($item->tanggal)) }}
                                                        </div>
                                                        <div class="text-muted" style="font-size: 11px;">
                                                            <i class="ti ti-calendar-event me-1"></i>Periode: {{ date('d-m-Y', strtotime($item->dari)) }} s.d {{ date('d-m-Y', strtotime($item->sampai)) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Actions -->
                                                <div class="col-md-2 text-end">
                                                    <div class="btn-group shadow-sm" role="group">
                                                        @can('pelanggaran.index')
                                                            <a href="{{ route('pelanggaran.print', Crypt::encrypt($item->no_sp)) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" target="_blank" title="Cetak">
                                                                <i class="ti ti-printer"></i>
                                                            </a>
                                                            <a href="{{ route('pelanggaran.show', Crypt::encrypt($item->no_sp)) }}" class="btn btn-sm btn-outline-info py-1 px-2" title="Detail">
                                                                <i class="ti ti-file-description"></i>
                                                            </a>
                                                        @endcan
                                                        @can('pelanggaran.edit')
                                                            <a href="#" class="btn btn-sm btn-outline-primary editPelanggaran py-1 px-2" no_sp="{{ Crypt::encrypt($item->no_sp) }}" title="Edit">
                                                                <i class="ti ti-edit"></i>
                                                            </a>
                                                        @endcan
                                                        @can('pelanggaran.delete')
                                                            <form method="POST" name="deleteform" class="deleteform d-inline"
                                                                action="{{ route('pelanggaran.delete', Crypt::encrypt($item->no_sp)) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm rounded-0 rounded-end py-1 px-2" title="Hapus">
                                                                    <i class="ti ti-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="card">
                                        <div class="card-body text-center py-4">
                                            <div class="text-muted">
                                                <i class="ti ti-inbox" style="font-size: 48px; opacity: 0.3;"></i>
                                                <p class="mt-2">Tidak ada data pelanggaran</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                        <div style="float: right;">
                            {{ $pelanggaran->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form id="mdlCreatePelanggaran" size="" show="loadCreatePelanggaran" title="Tambah Pelanggaran" />
<x-modal-form id="mdlEditPelanggaran" size="" show="loadEditPelanggaran" title="Edit Pelanggaran" />
@endsection

@push('myscript')
<script>
    $(function() {
        // Initialize select2 for karyawan
        const select2Nik = $(".select2Nik");
        if (select2Nik.length) {
            select2Nik.each(function() {
                var $this = $(this);
                $this.wrap('<div class="position-relative"></div>').select2({
                    placeholder: 'Semua Karyawan',
                    allowClear: true,
                    dropdownParent: $this.parent()
                });
            });
        }

        // Initialize flatpickr for date inputs
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: false
        });

        $('.delete-confirm').click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data pelanggaran ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $("#btnCreatePelanggaran").click(function(e) {
            e.preventDefault();
            $('#mdlCreatePelanggaran').modal("show");
            $("#loadCreatePelanggaran").load('{{ route("pelanggaran.create") }}');
        });

        $(".editPelanggaran").click(function(e) {
            e.preventDefault();
            var no_sp = $(this).attr("no_sp");
            $('#mdlEditPelanggaran').modal("show");
            $("#loadEditPelanggaran").load('/pelanggaran/' + no_sp + '/edit');
        });
    });
</script>
@endpush

