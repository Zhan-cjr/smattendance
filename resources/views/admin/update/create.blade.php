@extends('layouts.app')
@section('titlepage', 'Tambah Update')

@section('content')
@section('navigasi')
    <span>Tambah Update</span>
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
                <form action="{{ route('admin.update.store') }}" method="POST" id="formUpdate" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <x-input-with-icon label="Versi" name="version" icon="ti ti-tag" required />
                            <small class="text-muted">Format: 1.0.0, 1.0.1, 1.1.0, 2.0.0</small>
                        </div>
                        <div class="col-md-6">
                            <x-input-with-icon label="Judul Update" name="title" icon="ti ti-heading" required />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <x-textarea-label label="Deskripsi" name="description" icon="ti ti-file-text" />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <x-textarea-label label="Changelog" name="changelog" icon="ti ti-list" />
                            <small class="text-muted">Gunakan format list, contoh: - Fix bug\n- New feature</small>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold"><i class="ti ti-upload me-2"></i>File Update (ZIP)</label>
                                <input type="file" class="form-control" name="file_upload" accept=".zip" required>
                                <small class="text-muted">Upload file .zip update aplikasi. Checksum dan ukuran file akan dihitung otomatis.</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-input-with-icon label="Tanggal Rilis" name="released_at" icon="ti ti-calendar" type="date" />
                        </div>
                         <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold d-block">&nbsp;</label>
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_major" id="is_major" value="1">
                                    <label class="form-check-label" for="is_major">
                                        <strong>Major Update</strong>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">
                                        <strong>Aktif</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="ti ti-send me-1"></i> Simpan Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection











