@extends('layouts.app')
@section('titlepage', 'Riwayat Update')

@section('content')
@section('navigasi')
    <span>Riwayat Update</span>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Riwayat Update</h5>
                <a href="{{ route('update.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Versi</th>
                                <th>Versi Sebelumnya</th>
                                <th>Status</th>
                                <th>User</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Pesan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($updateLogs as $log)
                                <tr>
                                    <td>{{ $log->version }}</td>
                                    <td>{{ $log->previous_version ?? '-' }}</td>
                                    <td>
                                        @if($log->status == 'success')
                                            <span class="badge bg-success">Berhasil</span>
                                        @elseif($log->status == 'failed')
                                            <span class="badge bg-danger">Gagal</span>
                                        @elseif($log->status == 'downloading')
                                            <span class="badge bg-info">Mengunduh</span>
                                        @elseif($log->status == 'installing')
                                            <span class="badge bg-warning">Menginstall</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->user->name ?? '-' }}</td>
                                    <td>{{ $log->started_at ? $log->started_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td>{{ $log->completed_at ? $log->completed_at->format('d/m/Y H:i') : '-' }}</td>
                                    <td>{{ Str::limit($log->message ?? '-', 50) }}</td>
                                    <td>
                                        <a href="{{ route('update.log', $log->id) }}" class="btn btn-sm btn-info">
                                            <i class="ti ti-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada riwayat update</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $updateLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

