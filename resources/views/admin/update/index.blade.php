@extends('layouts.app')
@section('titlepage', 'Manage Update')

@section('content')
@section('navigasi')
    <span>Manage Update</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('admin.update.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-2"></i> Tambah Update
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('admin.update.index') }}">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Versi" value="{{ Request('version') }}" name="version"
                                        icon="ti ti-search" />
                                </div>
                                <div class="col-lg-3 col-sm-12 col-md-12">
                                    <label class="form-label">Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="">Semua</option>
                                        <option value="1" {{ Request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ Request('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <button class="btn btn-primary mt-4">
                                        <i class="ti ti-search me-1"></i>Cari
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive mb-2">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th>Versi</th>
                                        <th>Judul</th>
                                        <th>File URL</th>
                                        <th>Ukuran</th>
                                        <th>Major</th>
                                        <th>Status</th>
                                        <th>Rilis</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($updates as $update)
                                        <tr>
                                            <td>{{ $loop->iteration + $updates->firstItem() - 1 }}</td>
                                            <td>
                                                <strong>{{ $update->version }}</strong>
                                            </td>
                                            <td>{{ $update->title }}</td>
                                            <td>
                                                <a href="{{ $update->file_url }}" target="_blank" class="text-primary">
                                                    <i class="ti ti-external-link me-1"></i>Link
                                                </a>
                                            </td>
                                            <td>
                                                @if($update->file_size)
                                                    {{ number_format($update->file_size / 1024 / 1024, 2) }} MB
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($update->is_major)
                                                    <span class="badge bg-danger">Major</span>
                                                @else
                                                    <span class="badge bg-info">Minor</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($update->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $update->released_at ? $update->released_at->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.update.edit', Crypt::encrypt($update->id)) }}"
                                                        class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.update.show', Crypt::encrypt($update->id)) }}"
                                                        class="btn btn-sm btn-info" title="Detail">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route('admin.update.toggle-active', Crypt::encrypt($update->id)) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-{{ $update->is_active ? 'secondary' : 'success' }}"
                                                            title="{{ $update->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                            <i class="ti ti-{{ $update->is_active ? 'toggle-left' : 'toggle-right' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('admin.update.destroy', Crypt::encrypt($update->id)) }}"
                                                        class="d-inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger delete-confirm"
                                                            title="Hapus">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada data update</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2">
                            {{ $updates->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('myscript')
<script>
    $(document).ready(function() {
        $('.delete-confirm').click(function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            Swal.fire({
                title: 'Yakin Hapus?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush

@endsection











