@extends('layouts.app')
@section('titlepage', 'Pengumuman')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Pengumuman
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Manajemen data pengumuman internal.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="ti ti-home-2 ti-xs"></i>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-speakerphone ti-xs me-1"></i> Pengumuman
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            @can('pengumuman.create')
                <a href="{{ route('pengumuman.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Tambah Pengumuman
                </a>
            @endcan
        </div>
        <form action="{{ route('pengumuman.index') }}">
            <div class="row g-2 mb-3">
                <div class="col-lg-10 col-md-9 col-sm-12">
                    <x-input-with-icon label="Cari Judul Pengumuman" value="{{ Request('judul') }}" name="judul"
                        icon="ti ti-search" hideLabel />
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
                    <h6 class="card-title mb-0 text-white">Data Pengumuman</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3" style="width: 60px;">NO.</th>
                                <th class="text-white py-3">JUDUL</th>
                                <th class="text-white py-3">ISI PENGUMUMAN</th>
                                <th class="text-white py-3">TANGGAL</th>
                                <th class="text-white py-3 text-center" style="width: 100px;">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pengumuman as $d)
                                <tr>
                                    <td class="py-2 text-center">{{ $loop->iteration + $pengumuman->firstItem() - 1 }}</td>
                                    <td class="py-2"><strong>{{ $d->judul }}</strong></td>
                                    <td class="py-2">{{ Str::limit(strip_tags($d->isi), 80) }}</td>
                                    <td class="py-2">{{ date('d-m-Y H:i', strtotime($d->created_at)) }}</td>
                                    <td class="py-2 text-center">
                                        <div class="d-inline-flex border rounded overflow-hidden shadow-xs">
                                            @can('pengumuman.delete')
                                                <form method="POST" name="deleteform" class="deleteform m-0"
                                                    action="{{ route('pengumuman.delete', $d->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm delete-confirm px-2 py-1 border-0 rounded-0"
                                                        title="Hapus" style="background: #f8f9fa;">
                                                        <i class="ti ti-trash fs-6 text-danger"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if($pengumuman->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3 d-flex justify-content-end">
            {{ $pengumuman->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $(".delete-confirm").click(function(e) {
            var form = $(this).closest("form");
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data pengumuman ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
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
