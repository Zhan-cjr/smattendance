@extends('layouts.app')
@section('titlepage', 'Detail Update Log')

@section('content')
@section('navigasi')
    <span>Detail Update Log</span>
@endsection

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Update Log</h5>
                <a href="{{ route('update.history') }}" class="btn btn-sm btn-outline-primary">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <strong>Versi:</strong> {{ $updateLog->version }}
                        </div>
                        <div class="mb-2">
                            <strong>Versi Sebelumnya:</strong> {{ $updateLog->previous_version ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <strong>Status:</strong>
                            @if($updateLog->status == 'success')
                                <span class="badge bg-success">Berhasil</span>
                            @elseif($updateLog->status == 'failed')
                                <span class="badge bg-danger">Gagal</span>
                            @elseif($updateLog->status == 'downloading')
                                <span class="badge bg-info">Mengunduh</span>
                            @elseif($updateLog->status == 'installing')
                                <span class="badge bg-warning">Menginstall</span>
                            @else
                                <span class="badge bg-secondary">Pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <strong>User:</strong> {{ $updateLog->user->name ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <strong>Mulai:</strong> {{ $updateLog->started_at ? $updateLog->started_at->format('d/m/Y H:i:s') : '-' }}
                        </div>
                        <div class="mb-2">
                            <strong>Selesai:</strong> {{ $updateLog->completed_at ? $updateLog->completed_at->format('d/m/Y H:i:s') : '-' }}
                        </div>
                        <div class="mb-2">
                            <strong>Durasi:</strong>
                            @if($updateLog->started_at && $updateLog->completed_at)
                                {{ $updateLog->started_at->diffForHumans($updateLog->completed_at, true) }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>

                @if($updateLog->message)
                    <div class="mb-3">
                        <strong>Pesan:</strong>
                        <div class="alert alert-info mt-2">
                            {{ $updateLog->message }}
                        </div>
                    </div>
                @endif

                @if($updateLog->error_log)
                    <div class="mb-3">
                        <strong>Error Log:</strong>
                        <pre class="bg-dark text-light p-3 rounded" style="max-height: 400px; overflow-y: auto;">{{ $updateLog->error_log }}</pre>
                    </div>
                @endif

                <div class="mb-2">
                    <strong>Dibuat:</strong> {{ $updateLog->created_at->format('d/m/Y H:i:s') }}
                </div>
                <div class="mb-2">
                    <strong>Diupdate:</strong> {{ $updateLog->updated_at->format('d/m/Y H:i:s') }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

