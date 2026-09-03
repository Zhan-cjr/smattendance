@extends('layouts.app')
@section('titlepage', 'Detail Update')

@section('content')
@section('navigasi')
    <span>Detail Update</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('admin.update.index') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left me-2"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Versi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-tag"></i></span>
                                <input type="text" class="form-control" value="{{ $update->version }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-heading"></i></span>
                                <input type="text" class="form-control" value="{{ $update->title }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control" rows="3" readonly>{{ $update->description }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Changelog</label>
                            <pre class="bg-light p-3 rounded border" style="max-height: 300px; overflow-y: auto;">{{ $update->changelog }}</pre>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">File URL</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-link"></i></span>
                                <input type="text" class="form-control" value="{{ $update->file_url }}" readonly>
                            </div>
                            <a href="{{ $update->file_url }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                <i class="ti ti-external-link me-1"></i> Buka Link
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ukuran File</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-file-zip"></i></span>
                                <input type="text" class="form-control" 
                                    value="{{ $update->file_size ? number_format($update->file_size / 1024 / 1024, 2) . ' MB' : '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Checksum</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-key"></i></span>
                                <input type="text" class="form-control" value="{{ $update->checksum ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Rilis</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                <input type="text" class="form-control" 
                                    value="{{ $update->released_at ? $update->released_at->format('d/m/Y H:i') : '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Migrations</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-database"></i></span>
                                <input type="text" class="form-control" 
                                    value="{{ is_array($update->migrations) ? implode(', ', $update->migrations) : '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Seeders</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-seeding"></i></span>
                                <input type="text" class="form-control" 
                                    value="{{ is_array($update->seeders) ? implode(', ', $update->seeders) : '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <div>
                                @if($update->is_active)
                                    <span class="badge bg-success fs-6">Aktif</span>
                                @else
                                    <span class="badge bg-secondary fs-6">Nonaktif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe Update</label>
                            <div>
                                @if($update->is_major)
                                    <span class="badge bg-danger fs-6">Major Update</span>
                                @else
                                    <span class="badge bg-info fs-6">Minor Update</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <a href="{{ route('admin.update.edit', Crypt::encrypt($update->id)) }}" class="btn btn-warning">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('admin.update.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection











