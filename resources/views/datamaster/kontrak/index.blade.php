@extends('layouts.app')
@section('titlepage', 'Kontrak')

@section('content')
@section('navigasi')
    <span>Kontrak</span>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                @can('kontrak.create')
                    <a href="javascript:void(0);" class="btn btn-primary" id="btnCreateKontrak">
                        <i class="fa fa-plus me-2"></i> Tambah Kontrak
                    </a>
                    <a href="{{ route('kontrak.template') }}" class="btn btn-info">
                        <i class="ti ti-settings me-2"></i> Konfigurasi Template
                    </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body p-3">
                        <form action="{{ route('kontrak.index') }}">
                            <div class="row g-2 align-items-center">
                                <div class="col-lg-4 col-sm-12">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                                        <input type="text" class="form-control" name="nama_karyawan" value="{{ request('nama_karyawan') }}" placeholder="Cari Nama Karyawan...">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <select class="form-select" name="kode_cabang">
                                        <option value="">Semua Cabang</option>
                                        @foreach ($cabangs as $cabang)
                                            <option value="{{ $cabang->kode_cabang }}"
                                                {{ request('kode_cabang') == $cabang->kode_cabang ? 'selected' : '' }}>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <select class="form-select" name="kode_dept">
                                        <option value="">Semua Departemen</option>
                                        @foreach ($departemens as $dept)
                                            <option value="{{ $dept->kode_dept }}" {{ request('kode_dept') == $dept->kode_dept ? 'selected' : '' }}>
                                                {{ $dept->nama_dept }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 col-sm-12">
                                    <button class="btn btn-primary w-100" type="submit">
                                        <i class="ti ti-filter me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        @forelse ($kontraks as $kontrak)
                            <div class="card mb-2 shadow-sm border">
                                <div class="card-body p-2">
                                    <div class="row align-items-center">
                                        <!-- Icon -->
                                        <div class="col-md-1 text-center">
                                            <div class="avatar bg-info-subtle text-info rounded px-2 py-2 d-flex align-items-center justify-content-center mx-auto" style="width: 40px; height: 40px; border: 1px solid #e9ecef;">
                                                <i class="ti ti-file-description fs-3"></i>
                                            </div>
                                        </div>
                                        <!-- Identity -->
                                        <!-- Identity -->
                                        <div class="col-md-4">
                                            <div class="fw-bold text-dark" style="font-size: 14px;">
                                                {{ $kontrak->nama_karyawan ?? '-' }}
                                                <span class="text-muted fw-normal" style="font-size: 12px;">({{ $kontrak->nik_show ?? $kontrak->nik }})</span>
                                            </div>
                                            <div class="mt-1">
                                                <span class="badge bg-label-secondary" style="font-size: 10px;">{{ $kontrak->no_kontrak }}</span>
                                                <span class="badge bg-label-primary" style="font-size: 10px;">{{ $kontrak->nama_jabatan ?? '-' }}</span>
                                                <span class="badge bg-label-info" style="font-size: 10px;">{{ $kontrak->nama_dept ?? '-' }}</span>
                                                <span class="badge bg-label-warning" style="font-size: 10px;">{{ $kontrak->nama_cabang ?? '-' }}</span>
                                            </div>
                                        </div>
                                        <!-- Period -->
                                        <div class="col-md-3 border-start border-end d-none d-md-block text-center">
                                            <div class="fw-bold text-dark" style="font-size: 13px;">
                                                @if ($kontrak->jenis_kontrak == 'T')
                                                    -
                                                @elseif($kontrak->dari && $kontrak->sampai)
                                                    {{ date('d-m-Y', strtotime($kontrak->dari)) }} s/d {{ date('d-m-Y', strtotime($kontrak->sampai)) }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            <div class="text-muted" style="font-size: 11px;">
                                                Masa Kontrak
                                            </div>
                                        </div>
                                        <!-- Status & Type -->
                                        <div class="col-md-2 text-center">
                                            @if ($kontrak->status_kontrak == '1')
                                                <span class="badge bg-success py-1 px-2 mb-1" style="font-size: 11px;">Aktif</span>
                                            @else
                                                <span class="badge bg-danger py-1 px-2 mb-1" style="font-size: 11px;">Non Aktif</span>
                                            @endif
                                            
                                            <div class="mt-1">
                                                @php
                                                    $jenis_kontrak_text = $kontrak->jenis_kontrak == 'K' ? 'Kontrak' : ($kontrak->jenis_kontrak == 'T' ? 'Tetap' : $kontrak->jenis_kontrak);
                                                @endphp
                                                <span class="badge @if($kontrak->jenis_kontrak == 'T') bg-label-success @else bg-label-primary @endif" style="font-size: 10px;">{{ $jenis_kontrak_text }}</span>
                                            </div>
                                        </div>
                                        <!-- Actions -->
                                        <div class="col-md-2 text-end">
                                            <div class="btn-group shadow-sm" role="group">
                                                <a href="{{ route('kontrak.print', Crypt::encrypt($kontrak->id)) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" title="Cetak">
                                                    <i class="ti ti-printer"></i>
                                                </a>
                                                @if ($kontrak->status_kontrak == '1')
                                                    @can('kontrak.edit')
                                                        <a href="#" class="btn btn-sm btn-outline-success btnEditKontrak py-1 px-2" data-id="{{ Crypt::encrypt($kontrak->id) }}" title="Edit">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                    @endcan
                                                    @can('kontrak.delete')
                                                        <form method="POST" name="deleteform" class="deleteform d-inline"
                                                            action="{{ route('kontrak.delete', Crypt::encrypt($kontrak->id)) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-confirm rounded-0 rounded-end py-1 px-2" title="Hapus">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="ti ti-info-circle me-2 fs-4"></i>
                                <div>
                                    Belum ada data kontrak yang tersedia.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="mt-3">
                    {{ $kontraks->links('pagination::bootstrap-5') }}
                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<x-modal-form id="modalKontrak" show="loadModalKontrak" />
@endsection

@push('myscript')
<script>
    $(function() {
        const modal = $("#modalKontrak");

        function loadingModal() {
            $("#loadModalKontrak").html(`<div class="sk-wave sk-primary mx-auto">
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                    <div class="sk-wave-rect"></div>
                </div>`);
        }

        $("#btnCreateKontrak").on('click', function() {
            loadingModal();
            modal.modal('show');
            $(".modal-title").text('Tambah Kontrak');
            $("#loadModalKontrak").load("{{ route('kontrak.create') }}");
        });

        $(".btnEditKontrak").on('click', function() {
            const id = $(this).data('id');
            loadingModal();
            modal.modal('show');
            $(".modal-title").text('Edit Kontrak');
            $("#loadModalKontrak").load(`/kontrak/${id}/edit`);
        });

        $(".delete-confirm").on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus kontrak?',
                text: 'Data yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>
@endpush
