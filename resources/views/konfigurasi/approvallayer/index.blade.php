@extends('layouts.app')
@section('titlepage', 'Konfigurasi Approval Layer')

@section('content')
@section('navigasi')
    <span>Approval Layer</span>
@endsection
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-end">
        @can('approvallayer.create')
            <a href="#" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i> Tambah Konfigurasi</a>
        @endcan
    </div>
</div>

@php
    $groupedLayers = $approvalLayers->groupBy(function($item) {
        return ($item->kode_cabang ?? 'ALL') . '|' . ($item->kode_dept ?? 'ALL') . '|' . ($item->kode_jabatan ?? 'ALL');
    });
@endphp

<div class="row">
    @forelse ($groupedLayers as $key => $layers)
        @php
            $first = $layers->first();
            
            $c = $cabangs->firstWhere('kode_cabang', $first->kode_cabang);
            $nama_cabang = $c ? $c->nama_cabang : 'Semua Cabang';
            
            $d = $departemens->firstWhere('kode_dept', $first->kode_dept);
            $nama_dept = $d ? $d->nama_dept : 'Semua Departemen';
            
            $j = $jabatans->firstWhere('kode_jabatan', $first->kode_jabatan);
            $nama_jabatan = $j ? $j->nama_jabatan : 'Semua Jabatan';
            
            $layers = $layers->sortBy('level');
        @endphp
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header p-3 d-flex align-items-center" style="background-color: var(--theme-color-1); border-bottom: 3px solid rgba(0,0,0,0.1);">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; flex-shrink: 0; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <i class="ti ti-git-merge" style="font-size: 1.5rem; color: var(--theme-color-1);"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 text-white fw-bold" style="letter-spacing: 0.5px;">{{ strtoupper($nama_cabang) }}</h5>
                        <div class="text-white d-flex align-items-center flex-wrap gap-2">
                            <span class="badge bg-light text-dark rounded-pill px-2 py-1" style="font-weight: 600;"><i class="ti ti-briefcase me-1" style="opacity: 0.7;"></i>{{ strtoupper($nama_dept) }}</span>
                            <span class="badge bg-light text-dark rounded-pill px-2 py-1" style="font-weight: 600;"><i class="ti ti-id-badge me-1" style="opacity: 0.7;"></i>{{ strtoupper($nama_jabatan) }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="timeline position-relative ps-2">
                        @foreach ($layers as $l)
                            <div class="timeline-item position-relative mb-3">
                                <!-- Hubungan Line -->
                                @if (!$loop->last)
                                    <div class="position-absolute" style="left: 13px; top: 28px; bottom: -24px; width: 1px; background-color: #d1d5db; z-index: 1;"></div>
                                @endif
                                
                                <div class="d-flex align-items-start">
                                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-3 z-3 position-relative mt-2" style="background-color: var(--theme-color-1); width: 28px; height: 28px; font-size: 13px; font-weight: bold; flex-shrink: 0; box-shadow: 0 0 0 4px #ffffff;">
                                        {{ $l->level }}
                                    </div>
                                    <div class="flex-grow-1 bg-white rounded p-3 border shadow-none" style="border-color: #e5e7eb !important; border-radius: 8px !important;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-bold text-dark d-block" style="font-size: 13px;">{{ $l->role_name }}</span>
                                                <span class="text-muted" style="font-size: 10px;">Level Persetujuan {{ $l->level }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Card Footer for Edit / Delete entire flow -->
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-end align-items-center">
                    @can('approvallayer.edit')
                        <a href="#" class="btnEdit btn btn-sm btn-outline-primary me-2" 
                           data-cabang="{{ $first->kode_cabang ?? 'ALL' }}" 
                           data-dept="{{ $first->kode_dept ?? 'ALL' }}" 
                           data-jabatan="{{ $first->kode_jabatan ?? 'ALL' }}">
                            <i class="ti ti-edit me-1"></i> Edit Alur
                        </a>
                    @endcan
                    @can('approvallayer.delete')
                        <form method="POST" action="{{ route('approvallayer.destroyGroup', ['cabang' => $first->kode_cabang ?? 'ALL', 'dept' => $first->kode_dept ?? 'ALL', 'jabatan' => $first->kode_jabatan ?? 'ALL']) }}" class="delete-form m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <a href="#" class="delete-confirm btn btn-sm btn-outline-danger">
                                <i class="ti ti-trash"></i>
                            </a>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center text-muted mt-5">
            <div class="p-5 bg-white rounded shadow-sm border">
                <i class="ti ti-layout-grid-add mb-3 text-primary" style="font-size: 4rem; opacity: 0.5;"></i>
                <h5 class="text-dark mb-1">Belum Ada Konfigurasi</h5>
                <p class="text-muted mb-4">Silakan tambah konfigurasi approval layer baru.</p>
                @can('approvallayer.create')
                    <a href="#" class="btn btn-primary btn-sm px-4" id="btnCreateEmpty"><i class="fa fa-plus me-2"></i> Tambah Sekarang</a>
                @endcan
            </div>
        </div>
    @endforelse
</div>

<x-modal-form id="mdlForm" size="" show="loadForm" title="" />

@endsection

@push('myscript')
<script>
    $(function() {
        $("#btnCreate, #btnCreateEmpty").click(function(e) {
            e.preventDefault();
            window.location.href = "{{ route('approvallayer.create') }}";
        });

        $(".btnEdit").click(function(e) {
            e.preventDefault();
            var cabang = $(this).attr("data-cabang");
            var dept = $(this).attr("data-dept");
            var jabatan = $(this).attr("data-jabatan");
            
            // Build query string
            var url = "{{ route('approvallayer.editGroup') }}?cabang=" + cabang + "&dept=" + dept + "&jabatan=" + jabatan;
            window.location.href = url;
        });
        
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
    });
</script>
@endpush
