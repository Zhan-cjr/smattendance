@extends('layouts.app')
@section('titlepage', 'Konfigurasi KPI Jabatan')

@section('content')
@section('navigasi')
    <span>Konfigurasi KPI Jabatan</span>
@endsection

<div class="row">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        @if (Session::get('success'))
                            <div class="alert alert-success">
                                {{ Session::get('success') }}
                            </div>
                        @endif
                        @if (Session::get('warning'))
                            <div class="alert alert-warning">
                                {{ Session::get('warning') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <form action="{{ route('kpi.jabatan.index') }}" method="GET">
                            <div class="row g-2">
                                <div class="col-lg-10 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Jabatan" value="{{ Request('nama_jabatan') }}" name="nama_jabatan"
                                        icon="ti ti-search" hideLabel />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <button class="btn btn-primary w-100"><i class="ti ti-icons ti-search me-1"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Jabatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jabatan as $d)
                                        <tr>
                                            <td>{{ $d->kode_jabatan }}</td>
                                            <td>{{ $d->nama_jabatan }}</td>
                                            <td>
                                                <a href="{{ route('kpi.jabatan.setting', $d->kode_jabatan) }}" class="btn btn-primary btn-sm">
                                                    <i class="ti ti-settings me-1"></i> Konfigurasi
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                         <div class="mt-3">
                            {{ $jabatan->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
