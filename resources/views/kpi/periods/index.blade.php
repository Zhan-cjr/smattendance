@extends('layouts.app')
@section('titlepage', 'Data Periode KPI')

@section('content')
@section('navigasi')
    <span>Data Periode KPI</span>
@endsection
<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <a href="#" class="btn btn-primary" id="btnTambahPeriode"><i class="fa fa-plus me-2"></i> Tambah Periode</a>
            </div>
            <div class="card-body">
                
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('kpi.periods.index') }}" method="GET">
                            <div class="row g-2">
                                <div class="col-lg-11 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Nama Periode" value="{{ Request('nama_periode') }}" name="nama_periode"
                                        icon="ti ti-search" hideLabel />
                                </div>
                                <div class="col-lg-1 col-sm-12 col-md-12">
                                    <button class="btn btn-primary w-100"><i class="ti ti-icons ti-search me-1"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="row">
                            @forelse ($kpi_periods as $item)
                                <div class="card mb-2 shadow-sm border">
                                    <div class="card-body p-2">
                                        <div class="row align-items-center">
                                            <!-- Icon -->
                                            <div class="col-md-1 text-center">
                                                <div class="avatar avatar-md bg-primary-lt">
                                                    <i class="ti ti-calendar-stats fs-2"></i>
                                                </div>
                                            </div>
                                            <!-- Period Name -->
                                            <div class="col-md-4">
                                                <div class="fw-bold text-dark" style="font-size: 14px;">
                                                    {{ $item->nama_periode }}
                                                </div>
                                            </div>
                                            <!-- Dates -->
                                            <div class="col-md-4 border-start border-end d-none d-md-block text-center">
                                                <div class="d-flex justify-content-center gap-4">
                                                    <div class="text-center">
                                                        <div class="text-muted" style="font-size: 10px;">Mulai</div>
                                                        <div class="fw-bold text-dark" style="font-size: 12px;">{{ date('d-m-Y', strtotime($item->start_date)) }}</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-muted" style="font-size: 10px;">Selesai</div>
                                                        <div class="fw-bold text-dark" style="font-size: 12px;">{{ date('d-m-Y', strtotime($item->end_date)) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Status -->
                                            <div class="col-md-1 text-center">
                                                @if ($item->is_active)
                                                    <span class="badge bg-success" style="font-size: 10px;">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary" style="font-size: 10px;">Non Aktif</span>
                                                @endif
                                            </div>
                                            <!-- Actions -->
                                            <div class="col-md-2 text-end">
                                                <div class="d-flex justify-content-end gap-1">
                                                    <a href="#" class="btn btn-sm btn-outline-primary edit px-2 py-1" id="{{ $item->id }}">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('kpi.periods.delete', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm px-2 py-1">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body text-center p-5">
                                            <div class="mb-3">
                                                <i class="ti ti-calendar-stats text-muted" style="font-size: 6rem;"></i>
                                            </div>
                                            <h4 class="mb-1 text-muted">Belum ada Periode Penilaian</h4>
                                            <p class="text-secondary">Data periode penilaian KPI masih kosong. Silahkan buat periode baru untuk memulai penilaian.</p>
                                            <div class="mt-3">
                                                 <a href="#" class="btn btn-primary" id="btnTambahPeriodeEmpty">
                                                    <i class="ti ti-plus me-2"></i> Tambah Periode KPI
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div class="mt-3" style="float: right;">
                             {{ $kpi_periods->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modal-inputperiode" show="loadmodalinputperiode" title="Tambah Periode Penilaian" />

{{-- Modal Edit --}}
<div class="modal modal-blur fade" id="modal-editperiode" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Periode Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="loadeditform">
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $("#btnTambahPeriode, #btnTambahPeriodeEmpty").click(function() {
            $("#modal-inputperiode").modal("show");
            $("#loadmodalinputperiode").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
            $("#loadmodalinputperiode").load("{{ route('kpi.periods.create') }}");
        });

        $(".edit").click(function() {
            var id = $(this).attr('id');
            $.ajax({
                type: 'POST',
                url: '{{ route("kpi.periods.edit") }}',
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(respond) {
                    $("#loadeditform").html(respond);
                    $("#modal-editperiode").modal("show");
                }
            });
        });

        $(".delete-confirm").click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin Data Ini Akan Dihapus ?',
                text: "Jika Dihapus Maka Data Akan Hilang ",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus Saja!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endpush
