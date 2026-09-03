@extends('layouts.app')
@section('titlepage', 'Karyawan')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Karyawan
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data master karyawan, unit kerja, jabatan, dan konfigurasi akses operasional.
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
                    <i class="ti ti-users ti-xs me-1"></i> Karyawan
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @can('karyawan.create')
                    <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah Karyawan</a>
                    <a href="{{ route('karyawan.export', request()->query()) }}" class="btn btn-success"><i class="ti ti-file-export me-2"></i> Export Excel</a>
                    <a href="#" class="btn btn-success" id="btnImport"><i class="ti ti-file-import me-2"></i> Import Excel</a>
                    @can('users.create')
                        <a href="{{ route('karyawan.generatealluser') }}" class="btn btn-warning"><i class="ti ti-user-plus me-2"></i> Buat User (All)</a>
                        <a href="{{ route('karyawan.deletealluser') }}" class="btn btn-danger delete-all-user"><i class="ti ti-user-x me-2"></i> Reset All User</a>
                    @endcan
                @endcan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('karyawan.index') }}">
                            <div class="row g-2">
                                <div class="col-lg-5 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Nama Karyawan" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                                        icon="ti ti-search" hideLabel />
                                </div>
                                <div class="col-lg-3 col-sm-12 col-md-12">
                                    <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                        selected="{{ Request('kode_cabang') }}" hideLabel />
                                </div>
                                <div class="col-lg-3 col-sm-12 col-md-12">
                                    <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                        selected="{{ Request('kode_dept') }}" upperCase="true" hideLabel />
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
                            <div class="col-12">
                                @foreach ($karyawan as $d)
                                    <div class="card mb-2 shadow-sm border hover-elevate">
                                        <div class="card-body p-2 px-3">
                                            <div class="row align-items-center g-2">
                                                
                                                <!-- Avatar & Identity -->
                                                <div class="col-12 col-md-5 col-xl-4">
                                                    <div class="d-flex align-items-center gap-2.5">
                                                        <div class="flex-shrink-0 text-center">
                                                            @if (!empty($d->foto) && Storage::disk('public')->exists('/karyawan/' . $d->foto))
                                                                <img src="{{ getfotoKaryawan($d->foto) }}" alt="Avatar"
                                                                    class="rounded-circle shadow-xs"
                                                                    style="width: 40px; height: 40px; object-fit: cover; border: 1.5px solid #e2e8f0;">
                                                            @else
                                                                <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}"
                                                                    alt="No Image" class="rounded-circle shadow-xs"
                                                                    style="width: 40px; height: 40px; object-fit: cover; border: 1.5px solid #e2e8f0;">
                                                            @endif
                                                        </div>
                                                        <div class="min-w-0">
                                                            <div class="fw-bold text-dark text-truncate" style="font-size: 13.5px;">
                                                                {{ $d->nama_karyawan }}
                                                                <span class="text-muted fw-normal" style="font-size: 11.5px;">({{ $d->nik_show ?? $d->nik }})</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1 flex-wrap mt-0.5">
                                                                <span class="badge bg-label-primary py-0.5 px-1.5" style="font-size: 10px;">{{ $d->nama_jabatan }}</span>
                                                                <span class="badge bg-label-info py-0.5 px-1.5" style="font-size: 10px;">{{ $d->nama_dept }}</span>
                                                                <span class="badge bg-label-warning py-0.5 px-1.5" style="font-size: 10px;">{{ $d->nama_cabang }}</span>
                                                                @if ($d->status_karyawan)
                                                                    @php
                                                                        $status_karyawan_text = $d->status_karyawan == 'K' ? 'Kontrak' : ($d->status_karyawan == 'T' ? 'Tetap' : $d->status_karyawan);
                                                                        $badge_class = $d->status_karyawan == 'T' ? 'bg-label-success' : 'bg-label-primary';
                                                                    @endphp
                                                                    <span class="badge {{ $badge_class }} py-0.5 px-1.5" style="font-size: 10px;">{{ $status_karyawan_text }}</span>
                                                                @endif
                                                                @if ($d->jenis_upah)
                                                                    @php
                                                                        $upah_class = $d->jenis_upah == 'Harian' ? 'bg-label-info' : 'bg-label-success';
                                                                    @endphp
                                                                    <span class="badge {{ $upah_class }} py-0.5 px-1.5" style="font-size: 10px;">{{ $d->jenis_upah }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Status & Masa Kerja -->
                                                <div class="col-6 col-md-2 col-xl-2 border-start d-none d-md-block text-center">
                                                    <div class="mb-0.5">
                                                        @if ($d->status_aktif_karyawan == '1')
                                                            <span class="badge bg-success py-0.5 px-2 rounded-pill" style="font-size: 10px;">Aktif</span>
                                                        @else
                                                            <span class="badge bg-danger py-0.5 px-2 rounded-pill" style="font-size: 10px;">Non Aktif</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted" style="font-size: 10.5px;">
                                                        Masuk: {{ date('d-m-Y', strtotime($d->tanggal_masuk)) }}
                                                    </div>
                                                    <div class="text-muted fw-semibold" style="font-size: 10px;">
                                                        @php
                                                            $awal = new DateTime($d->tanggal_masuk);
                                                            $akhir = new DateTime();
                                                            $masa_kerja = $akhir->diff($awal);
                                                        @endphp
                                                        {{ $masa_kerja->y . ' Th ' . $masa_kerja->m . ' Bln' }}
                                                    </div>
                                                </div>

                                                <!-- Lock Toggles (Jam Kerja & Face Recognition) -->
                                                <div class="col-6 col-md-2 col-xl-2 border-start text-center d-flex justify-content-center align-items-center gap-2.5">
                                                    <div class="text-center">
                                                        @if ($d->lock_jam_kerja == '1')
                                                            <a href="{{ route('karyawan.lockunlockjamkerja', Crypt::encrypt($d->nik)) }}"
                                                                data-bs-toggle="tooltip" title="Unlock Jam Kerja">
                                                                <i class="ti ti-lock text-success" style="font-size: 15px;"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('karyawan.lockunlockjamkerja', Crypt::encrypt($d->nik)) }}"
                                                                data-bs-toggle="tooltip" title="Lock Jam Kerja">
                                                                <i class="ti ti-lock-open text-danger" style="font-size: 15px;"></i>
                                                            </a>
                                                        @endif
                                                        <div class="d-block text-muted" style="font-size: 8.5px; line-height: 1.1; margin-top: 1px;">Jam Kerja</div>
                                                    </div>
                                                    <div class="text-center">
                                                        @if ($d->is_niqab == '0')
                                                            <a href="{{ route('karyawan.lockunlockfacerecognition', Crypt::encrypt($d->nik)) }}"
                                                                data-bs-toggle="tooltip" title="Unlock Face Recognition">
                                                                <i class="ti ti-lock text-success" style="font-size: 15px;"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ route('karyawan.lockunlockfacerecognition', Crypt::encrypt($d->nik)) }}"
                                                                data-bs-toggle="tooltip" title="Lock Face Recognition">
                                                                <i class="ti ti-lock-open text-danger" style="font-size: 15px;"></i>
                                                            </a>
                                                        @endif
                                                        <div class="d-block text-muted" style="font-size: 8.5px; line-height: 1.1; margin-top: 1px;">Face Recognition</div>
                                                    </div>
                                                </div>

                                                <!-- Action Buttons & User Account Button -->
                                                <div class="col-12 col-md-3 col-xl-4 text-end d-flex flex-column align-items-end justify-content-center gap-1">
                                                    <div class="btn-group shadow-none" role="group">
                                                        @can('karyawan.setjamkerja')
                                                            <a href="#" class="btn btn-xs btn-outline-secondary btnSetJamkerja px-1.5 py-1"
                                                                nik="{{ Crypt::encrypt($d->nik) }}" title="Set Jam Kerja" style="font-size: 11px;">
                                                                <i class="ti ti-device-watch" style="font-size: 13px;"></i>
                                                            </a>
                                                        @endcan
                                                        @can('karyawan.setcabang')
                                                            <a href="#" class="btn btn-xs btn-outline-secondary btnSetCabang px-1.5 py-1"
                                                                nik="{{ Crypt::encrypt($d->nik) }}" title="Set Cabang" style="font-size: 11px;">
                                                                <i class="ti ti-map" style="font-size: 13px;"></i>
                                                            </a>
                                                        @endcan
                                                        @can('karyawan.edit')
                                                            <a href="#" class="btn btn-xs btn-outline-primary btnEdit px-1.5 py-1"
                                                                nik="{{ Crypt::encrypt($d->nik) }}" title="Edit" style="font-size: 11px;">
                                                                <i class="ti ti-edit" style="font-size: 13px;"></i>
                                                            </a>
                                                        @endcan
                                                        @can('karyawan.show')
                                                            <a href="{{ route('karyawan.show', Crypt::encrypt($d->nik)) }}"
                                                                class="btn btn-xs btn-outline-info px-1.5 py-1" title="Detail" style="font-size: 11px;">
                                                                <i class="ti ti-file-description" style="font-size: 13px;"></i>
                                                            </a>
                                                        @endcan
                                                        @can('karyawan.delete')
                                                            <form method="POST" name="deleteform" class="deleteform d-inline"
                                                                action="{{ route('karyawan.delete', Crypt::encrypt($d->nik)) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-xs btn-outline-danger delete-confirm rounded-0 rounded-end px-1.5 py-1"
                                                                    title="Delete" style="font-size: 11px;">
                                                                    <i class="ti ti-trash" style="font-size: 13px;"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>

                                                    @can('users.create')
                                                        @if (empty($d->id_user))
                                                            <a href="{{ route('karyawan.createuser', Crypt::encrypt($d->nik)) }}"
                                                                class="btn btn-xs btn-danger py-0.5 px-2 rounded-pill d-inline-flex align-items-center gap-1 shadow-none" style="font-size: 9.5px; line-height: 1.2;">
                                                                <i class="ti ti-user-plus" style="font-size: 10.5px;"></i> Buat User
                                                            </a>
                                                        @else
                                                            <a href="{{ route('karyawan.deleteuser', Crypt::encrypt($d->nik)) }}"
                                                                class="btn btn-xs btn-success py-0.5 px-2 rounded-pill d-inline-flex align-items-center gap-1 shadow-none" style="font-size: 9.5px; line-height: 1.2;">
                                                                <i class="ti ti-user" style="font-size: 10.5px;"></i> Hapus User
                                                            </a>
                                                        @endif
                                                    @endcan
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div style="float: right;">
                            {{ $karyawan->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-modal-form id="modal" show="loadmodal" />
<x-modal-form id="modalSetJamkerja" show="loadmodalSetJamkerja" size="modal-lg" title="Set Jam Kerja" />
<x-modal-form id="modalSetCabang" show="loadmodalSetCabang" size="modal-lg" title="Set Cabang Karyawan" />
<x-modal-form id="modalImport" show="loadmodalImport" size="modal-lg" title="Import Data Karyawan" />
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
            $(".modal-title").text("Tambah Data Karyawan");
            $("#loadmodal").load("{{ route('karyawan.create') }}");
        });

        $("#btnImport").click(function() {
            $("#modalImport").modal("show");
            $("#loadmodalImport").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);
            $("#loadmodalImport").load("{{ route('karyawan.import') }}");
        });

        $(".btnEdit").click(function() {
            loading();
            const nik = $(this).attr("nik");
            $("#modal").modal("show");
            $(".modal-title").text("Edit Data Karyawan");
            $("#loadmodal").load(`/karyawan/${nik}/edit`);
        });

        $(".btnSetJamkerja").click(function() {
            const nik = $(this).attr("nik");
            $("#modalSetJamkerja").modal("show");
            $("#loadmodalSetJamkerja").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);

            $("#loadmodalSetJamkerja").load(`/karyawan/${nik}/setjamkerja`);
        });

        $(".btnSetCabang").click(function() {
            const nik = $(this).attr("nik");
            $("#modalSetCabang").modal("show");
            $("#loadmodalSetCabang").html(`<div class="sk-wave sk-primary" style="margin:auto">
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            <div class="sk-wave-rect"></div>
            </div>`);

            $("#loadmodalSetCabang").load(`/karyawan/${nik}/setcabang`);
        });

        $(".delete-all-user").click(function(e) {
            e.preventDefault();
            var href = $(this).attr("href");
            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Semua User dengan Role Karyawan akan dihapus!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus Semua!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });

    });
</script>
@endpush
