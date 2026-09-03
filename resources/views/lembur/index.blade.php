@extends('layouts.app')
@section('titlepage', 'Lembur')

@section('content')
@section('navigasi')
    <span>Lembur</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="nav-align-top nav-tabs-shadow mb-4">
            <div class="card-header p-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="m-0 font-weight-bold text-primary">Data Lembur</h5>
                @can('lembur.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah Data</a>
                @endcan
            </div>
            
            <div class="card-body p-3 bg-white">
                <form action="{{ route('lembur.index') }}">
                    <div class="row g-2">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <x-input-with-icon label="Dari" value="{{ Request('dari') }}" name="dari" icon="ti ti-calendar"
                                datepicker="flatpickr-date" hideLabel />
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <x-input-with-icon label="Sampai" value="{{ Request('sampai') }}" name="sampai" icon="ti ti-calendar"
                                datepicker="flatpickr-date" hideLabel />
                        </div>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-12">
                            <x-input-with-icon label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                                icon="ti ti-search" hideLabel />
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="form-group">
                                <select name="status" id="status" class="form-select">
                                    <option value="">Status</option>
                                    <option value="0" {{ Request('status') === '0' ? 'selected' : '' }}>Pending</option>
                                    <option value="1" {{ Request('status') === '1' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="2" {{ Request('status') === '2' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                             <select name="kode_cabang" id="kode_cabang" class="form-select">
                                <option value="">Semua Cabang</option>
                                @foreach ($cabang as $d)
                                    <option value="{{ $d->kode_cabang }}" {{ Request('kode_cabang') == $d->kode_cabang ? 'selected' : '' }}>
                                        {{ textUpperCase($d->nama_cabang) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <select name="kode_dept" id="kode_dept" class="form-select">
                                <option value="">Semua Departemen</option>
                                @foreach ($departemen as $d)
                                    <option value="{{ $d->kode_dept }}" {{ Request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                        {{ textUpperCase($d->nama_dept) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-12 col-sm-12">
                            <button class="btn btn-primary w-100"><i class="ti ti-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                @forelse ($lembur as $d)
                    @php
                        $start = strtotime($d->lembur_mulai);
                        $end = strtotime($d->lembur_selesai);
                        $diff = $end - $start;
                        $hours = floor($diff / 3600);
                        $minutes = floor(($diff % 3600) / 60);
                        $duration = $hours . "j " . ($minutes > 0 ? $minutes . "m" : "");
                        
                         /* Realisasi Duration */
                        $real_duration = "-";
                         if($d->lembur_in && $d->lembur_out) {
                            $real_duration = ROUND(hitungJam($d->lembur_in, $d->lembur_out), 2) . "j";
                        }
                    @endphp
                    <div class="card mb-2 shadow-sm border">
                        <div class="card-body p-2">
                            <div class="row align-items-center">
                                <!-- Avatar -->
                                <div class="col-md-1 text-center" style="width: 60px;">
                                    @php
                                        $path = Storage::url('karyawan/'.$d->foto);
                                    @endphp
                                    @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                        <img src="{{ url($path) }}" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="No Image" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif
                                </div>
                                <!-- Identity -->
                                <div class="col-md-4">
                                    <div class="fw-bold text-dark" style="font-size: 14px;">{{ $d->nama_karyawan }} <span class="text-muted fw-normal" style="font-size: 12px;">({{ $d->nik_show ?? $d->nik }})</span></div>
                                    <div class="mt-1">
                                        <span class="badge bg-label-success" style="font-size: 10px;">{{ $d->nama_jabatan }}</span>
                                        <span class="badge bg-label-info" style="font-size: 10px;">{{ $d->nama_dept }}</span>
                                        <span class="badge bg-label-warning" style="font-size: 10px;">{{ $d->nama_cabang }}</span>
                                    </div>
                                </div>
                                <!-- Date & Duration -->
                                <div class="col-md-3 border-start border-end d-none d-md-block text-center">
                                     <div class="fw-bold text-dark" style="font-size: 13px;">{{ DateToIndo($d->tanggal) }}</div>
                                     <div class="text-muted" style="font-size: 11px;">
                                        <span class="text-success">{{ date('H:i', strtotime($d->lembur_mulai)) }}</span> - 
                                        <span class="text-danger">{{ date('H:i', strtotime($d->lembur_selesai)) }}</span>
                                        <span class="mx-1">•</span> {{ $duration }}
                                     </div>
                                      @if($d->lembur_in && $d->lembur_out)
                                        <div class="badge bg-primary mt-1" style="font-size: 10px;">Aktual: {{ $real_duration }}</div>
                                      @endif
                                </div>
                                
                                <!-- Status -->
                                <div class="col-md-2 text-center">
                                    @if ($d->status == 0)
                                        <span class="badge bg-label-warning py-1 px-2" style="font-size: 11px;">
                                            <i class="ti ti-hourglass-empty me-1"></i> Pending
                                        </span>
                                    @elseif ($d->status == 1)
                                        <span class="badge bg-success py-1 px-2" style="font-size: 11px;">Disetujui</span>
                                    @elseif ($d->status == 2)
                                        <span class="badge bg-danger py-1 px-2" style="font-size: 11px;">Ditolak</span>
                                    @endif
                                </div>
                                
                                <!-- Actions -->
                                <div class="col-md-2 text-end">
                                    <div class="btn-group shadow-sm" role="group">
                                        @can('lembur.approve')
                                            @if ($d->status == 0)
                                                <a href="#" class="btn btn-sm btn-outline-primary btnApprove py-1 px-2"
                                                    id="{{ Crypt::encrypt($d->id) }}" title="Approve">
                                                    <i class="ti ti-external-link"></i>
                                                </a>
                                            @elseif($d->status == 1 || $d->status == 2)
                                                <form method="POST" action="{{ route('lembur.cancelapprove', Crypt::encrypt($d->id)) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning cancel-confirm rounded-0 py-1 px-2" title="Batalkan Approval">
                                                        <i class="ti ti-arrow-back-up"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                        
                                        @can('lembur.edit')
                                             <a href="#" class="btn btn-sm btn-outline-success btnEdit py-1 px-2" id="{{ Crypt::encrypt($d->id) }}" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                        @endcan
                                        
                                        @can('lembur.index')
                                            <a href="#" class="btn btn-sm btn-outline-info btnShow py-1 px-2" id="{{ Crypt::encrypt($d->id) }}" title="Detail">
                                                <i class="ti ti-file-description"></i>
                                            </a>
                                        @endcan
                                        
                                        @can('lembur.delete')
                                            @if ($d->status == 0)
                                                <form method="POST" action="{{ route('lembur.delete', Crypt::encrypt($d->id)) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm rounded-0 rounded-end py-1 px-2" title="Hapus">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card">
                        <div class="card-body text-center p-5">
                            <div class="mb-3">
                                <i class="ti ti-file-x text-muted" style="font-size: 6rem;"></i>
                            </div>
                            <h4 class="mb-1 text-muted">Belum ada data</h4>
                            <p class="text-secondary">Data lembur belum tersedia untuk periode atau filter yang dipilih.</p>
                        </div>
                    </div>
                @endforelse
                <div style="float: right;">
                    {{ $lembur->links() }}
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
        const loading = () => {
             $("#loadmodal").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
        };

        // Initialize Flatpickr
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            allowInput: true
        });

        $("#btnCreate").click(function() {
            $("#modal").modal("show");
            $(".modal-title").text("Tambah Data Lembur");
            loading();
            $("#loadmodal").load("{{ route('lembur.create') }}");
        });

        $(".btnEdit").click(function() {
            const id = $(this).attr("id");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Lembur");
            loading();
            $("#loadmodal").load(`/lembur/${id}/edit`);
        });

        $(".btnApprove").click(function(e) {
            e.preventDefault();
            let id = $(this).attr("id");
            $("#modal").modal("show");
            $(".modal-title").text("Persetujuan Lembur");
            loading();
            $("#loadmodal").load(`/lembur/${id}/approve`);
        });
        
         $(".btnShow").click(function(e) {
            e.preventDefault();
            let id = $(this).attr("id");
            $("#modal").modal("show");
            loading();
            $("#modal").find(".modal-title").text("Detail Lembur");
            $("#loadmodal").load(`/lembur/${id}/show`);
        });

        // Delete Confirm
        $(".delete-confirm").click(function(e) {
            var form = $(this).closest("form");
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });

        // Cancel Confirm
        $(".cancel-confirm").click(function(e) {
            var form = $(this).closest("form");
            e.preventDefault();
            Swal.fire({
                title: 'Batalkan Persetujuan?',
                text: "Status akan kembali menjadi Pending!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Batalkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endpush
