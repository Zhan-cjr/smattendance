@extends('layouts.app')
@section('titlepage', 'Transaksi KPI')
@section('content')
@section('navigasi')
    <span>Transaksi KPI</span>
@endsection

<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                @if ($active_period)
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div>
                            <i class="ti ti-calendar me-2"></i> Periode Aktif: <strong>{{ $active_period->nama_periode }}</strong>
                            <span class="text-muted ms-2">({{ date('d M Y', strtotime($active_period->start_date)) }} - {{ date('d M Y', strtotime($active_period->end_date)) }})</span>
                        </div>
                    </div>
                @else
                    <div class="text-danger"><i class="ti ti-alert-triangle me-2"></i> Belum ada Periode KPI yang Aktif</div>
                @endif
            </div>
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
                        <form action="{{ route('kpi.transactions.index') }}" method="GET">
                            <div class="row g-2">
                                <div class="col-lg-6 col-sm-12 col-md-12">
                                    <x-input-with-icon label="Cari Nama Karyawan" value="{{ Request('nama_karyawan') }}"
                                        name="nama_karyawan" icon="ti ti-search" hideLabel />
                                </div>
                                <div class="col-lg-4 col-sm-12 col-md-12">
                                    <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                        selected="{{ Request('kode_dept') }}" upperCase="true" hideLabel />
                                </div>
                                <div class="col-lg-2 col-sm-12 col-md-12">
                                    <button class="btn btn-primary w-100"><i class="ti ti-icons ti-search me-1"></i> Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="table-responsive rounded-3 overflow-hidden">
                            <table class="table table-bordered table-striped table-hover table-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Nama Karyawan</th>
                                        <th>Jabatan</th>
                                        <th>Departemen</th>
                                        <th>Status KPI</th>
                                        <th>Nilai</th>
                                        <th>Grade</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($karyawan as $item)
                                        <tr>
                                            <td>{{ $loop->iteration + $karyawan->firstItem() - 1 }}</td>
                                            <td>{{ $item->nik }}</td>
                                            <td>{{ $item->nama_karyawan }}</td>
                                            <td>{{ $item->nama_jabatan }}</td>
                                            <td>{{ $item->nama_dept }}</td>
                                            <td>
                                                @if (empty($item->kpi_status))
                                                    <span class="badge bg-secondary">Belum Diset</span>
                                                @elseif ($item->kpi_status == 'draft')
                                                    <span class="badge bg-warning">Draft</span>
                                                @elseif ($item->kpi_status == 'submitted')
                                                    <span class="badge bg-info">Submitted</span>
                                                @elseif ($item->kpi_status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($item->total_nilai))
                                                    {{ number_format($item->total_nilai, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($item->grade))
                                                    {{ $item->grade }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($active_period)
                                                    <div class="btn-group">
                                                        @if (empty($item->kpi_id))
                                                            <a href="{{ route('kpi.transactions.settarget', $item->nik) }}" class="btn btn-sm btn-primary">
                                                                <i class="ti ti-target me-1"></i> Set Target
                                                            </a>
                                                        @else
                                                            <a href="{{ route('kpi.transactions.show', $item->kpi_id) }}" class="btn btn-sm btn-success">
                                                                <i class="ti ti-file-analytics me-1"></i> Lihat KPI
                                                            </a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">Periode Non-Aktif</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {{ $karyawan->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
