@extends('layouts.app')
@section('titlepage', 'Generate Pembayaran Pinjaman')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Generate Pembayaran Pinjaman
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Otomatisasi pemotongan angsuran pinjaman untuk semua karyawan pada periode gaji tertentu.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="ti ti-home-2 ti-xs"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('pinjaman.index') }}">Pinjaman</a></li>
                <li class="breadcrumb-item active">Generate</li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <i class="ti ti-settings-automation me-2 fs-4"></i>
                    <h6 class="card-title mb-0 text-white">Form Generate Massal</h6>
                </div>
            </div>
            <div class="card-body pt-4">
                <div class="alert alert-info d-flex" role="alert">
                    <span class="badge badge-center rounded-pill bg-info border-label-info p-3 me-2">
                        <i class="ti ti-info-circle ti-xs"></i>
                    </span>
                    <div class="d-flex flex-column ps-1">
                        <h6 class="alert-heading mb-1 text-info">Informasi Penting</h6>
                        <span>Sistem akan mencari semua <b>Rencana Cicilan</b> yang jatuh tempo pada bulan & tahun yang dipilih, lalu membuat record pembayaran secara otomatis.</span>
                    </div>
                </div>

                <form action="{{ route('pinjaman.prosesgenerate') }}" method="POST" id="formGenerate">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-7">
                            <label class="form-label fw-bold">Bulan Gaji</label>
                            <select name="bulan" id="bulan" class="form-select select2">
                                @foreach(config('global.list_bulan') as $d)
                                    <option value="{{ $d['kode_bulan'] }}" {{ date('m') == $d['kode_bulan'] ? 'selected' : '' }}>{{ $d['nama_bulan'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Tahun Gaji</label>
                            <select name="tahun" id="tahun" class="form-select select2">
                                @for($t = $start_year; $t <= date('Y'); $t++)
                                    <option value="{{ $t }}" {{ date('Y') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="btnProses">
                            <i class="ti ti-rotate me-1"></i> Mulai Generate Pembayaran
                        </button>
                        <a href="{{ route('pinjaman.index') }}" class="btn btn-label-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    $(function() {
        $("#formGenerate").submit(function(e) {
            let bulan = $("#bulan option:selected").text();
            let tahun = $("#tahun").val();
            
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Generate',
                text: "Sistem akan membuat record pembayaran untuk periode " + bulan + " " + tahun + ". Lanjutkan?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7367f0',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Generate Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#btnProses").prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...');
                    this.submit();
                }
            });
        });
    });
</script>
@endpush
