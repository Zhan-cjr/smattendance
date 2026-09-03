@extends('layouts.app')
@section('titlepage', 'Konfigurasi Template Kontrak')
@section('content')
@section('navigasi')
    <span>Konfigurasi Template Kontrak</span>
@endsection
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Edit Template Kontrak (PKWT)</h4>
                <form action="{{ route('kontrak.updateTemplate') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset template ke default? Perubahan Anda akan hilang.');">
                    @csrf
                    <input type="hidden" name="reset" value="true">
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="ti ti-refresh me-1"></i> Reset ke Default
                    </button>
                </form>
            </div>
            <div class="card-body">
                <form action="{{ route('kontrak.updateTemplate') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="konten" class="form-label">Isi Kontrak</label>
                        <textarea class="form-control" name="konten" id="konten">{{ $template->konten }}</textarea>
                    </div>
                    <div class="alert alert-info">
                        <strong>Kamus Kode (Placeholder):</strong><br>
                        Gunakan kode berikut agar data otomatis terisi saat dicetak:
                        <ul>
                            <li>@{{no_kontrak}} - Nomor Kontrak</li>
                            <li>@{{nama_karyawan}} - Nama Karyawan</li>
                            <li>@{{nik_karyawan}} - NIK</li>
                            <li>@{{jabatan}} - Jabatan</li>
                            <li>@{{cabang}} - Cabang</li>
                            <li>@{{nama_perusahaan}} - Nama Perusahaan</li>
                            <li>@{{nama_hrd}} - Nama HRD</li>
                            <li>@{{gaji_pokok}} - Gaji Pokok (Format Rupiah)</li>
                            <li>@{{tabel_tunjangan}} - Tabel List Tunjangan</li>
                            <li>@{{tanggal_mulai}} - Tanggal Mulai Kontrak</li>
                            <li>@{{tanggal_selesai}} - Tanggal Selesai Kontrak</li>
                            <li>@{{hari_ini}} - Hari Cetak</li>
                            <li>@{{tanggal_hari_ini}} - Tanggal Cetak</li>
                        </ul>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Summernote Assets --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#konten').summernote({
            placeholder: 'Tulis isi kontrak disini...',
            tabsize: 2,
            height: 600,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endpush
@endsection
