@extends('layouts.app')
@section('titlepage', 'Data Mutasi, Promosi & Demosi')

@section('content')
@section('navigasi')
    <span>Mutasi, Promosi & Demosi</span>
@endsection
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <a href="#" class="btn btn-primary" id="btnTambah"><i class="fa fa-plus me-2"></i> Tambah Data</a>
            </div>

            <!-- Modal Tambah Data -->
            <div class="modal fade" id="modalInput" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Data Mutasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="load-form">
                            <!-- Form content will be loaded here via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('mutasi.index') }}" method="GET">
                            <div class="row g-2">
                                <div class="col-lg-11 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Nama Karyawan" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
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
                        @foreach ($mutasi as $d)
                        <div class="card mb-2 shadow-sm border">
                            <div class="card-body p-2">
                                <div class="row align-items-center">
                                    <!-- Avatar -->
                                    <div class="col-md-1 text-center">
                                        @if (!empty($d->karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $d->karyawan->foto))
                                            <img src="{{ getfotoKaryawan($d->karyawan->foto) }}" alt="Avatar"
                                                class="rounded-circle"
                                                style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #e9ecef;">
                                        @else
                                            <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}"
                                                alt="No Image" class="rounded-circle"
                                                style="width: 40px; height: 40px; object-fit: cover; border: 1px solid #e9ecef;">
                                        @endif
                                    </div>
                                    <!-- Identity -->
                                    <div class="col-md-3">
                                        <div class="fw-bold text-dark" style="font-size: 14px;">
                                            {{ $d->karyawan->nama_karyawan }}
                                            <span class="text-muted fw-normal" style="font-size: 12px;">({{ $d->nik }})</span>
                                        </div>
                                        <div class="mt-1">
                                            <span class="text-muted" style="font-size: 11px;">
                                                <i class="ti ti-calendar me-1"></i> {{ date('d-m-Y', strtotime($d->tanggal_mutasi)) }}
                                            </span>
                                        </div>
                                        <div class="mt-1">
                                            @if ($d->jenis_mutasi == 'MUTASI')
                                                <span class="badge bg-info" style="font-size: 10px;">Mutasi</span>
                                            @elseif ($d->jenis_mutasi == 'PROMOSI')
                                                <span class="badge bg-success" style="font-size: 10px;">Promosi</span>
                                            @else
                                                <span class="badge bg-warning" style="font-size: 10px;">Demosi</span>
                                            @endif
                                        </div>
                                    </div>
                                    <!-- Mutation Details -->
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center justify-content-start" style="font-size: 11px;">
                                            <div class="text-secondary text-end pe-2" style="width: 45%;">
                                                <div class="fw-bold">{{ $d->cabangLama->nama_cabang ?? '-' }}</div>
                                                <div>{{ $d->deptLama->nama_dept ?? '-' }}</div>
                                                <div>{{ $d->jabatanLama->nama_jabatan ?? '-' }}</div>
                                            </div>
                                            <div class="text-center px-1" style="width: 10%;">
                                                <i class="ti ti-arrow-right text-primary"></i>
                                            </div>
                                            <div class="text-primary ps-2" style="width: 45%;">
                                                <div class="fw-bold">{{ $d->cabangBaru->nama_cabang ?? '-' }}</div>
                                                <div>{{ $d->deptBaru->nama_dept ?? '-' }}</div>
                                                <div>{{ $d->jabatanBaru->nama_jabatan ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Keterangan & SK -->
                                    <div class="col-md-3">
                                        <div class="text-muted mb-1" style="font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                            <em>{{ $d->keterangan ?? 'Tidak ada keterangan' }}</em>
                                        </div>
                                        @if ($d->doc_sk)
                                            <a href="{{ asset('storage/' . $d->doc_sk) }}" target="_blank" class="text-primary" style="font-size: 11px;">
                                                <i class="ti ti-file-text me-1"></i> Lihat SK
                                            </a>
                                        @endif
                                    </div>
                                    <!-- Action -->
                                    <div class="col-md-1 text-end">
                                        <form method="POST" action="{{ route('mutasi.destroy', $d->id) }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm py-1 px-2" title="Hapus">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <div class="d-flex justify-content-end mt-3">
                            {{ $mutasi->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $(".flatpickr-date").flatpickr();

        $(".delete-confirm").click(function(e){
            e.preventDefault();
            var form = $(this).closest("form");
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
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

        $("#btnTambah").click(function(e) {
            e.preventDefault();
            $("#load-form").html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
            $("#modalInput").modal("show");
            $("#load-form").load("{{ route('mutasi.create') }}");
        });
    });
</script>
@endpush
