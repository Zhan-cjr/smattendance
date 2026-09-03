@extends('layouts.app')
@section('titlepage', 'Ajuan Jadwal')

@section('content')
@section('navigasi')
    <span>Ajuan Jadwal</span>
@endsection
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="nav-align-top nav-tabs-shadow mb-4">
            @include('layouts.navigation.nav_pengajuan_absen')
            <div class="tab-content">
                <div class="tab-pane fade active show" id="navs-justified-home" role="tabpanel">
                    @can('ajuanjadwal.create')
                        <a href="{{ route('ajuanjadwal.create') }}" class="btn btn-primary" id="btnCreate"><i class="fa fa-plus me-2"></i>
                            Tambah Data</a>
                    @endcan
                    <div class="row mt-2">
                        <div class="col-12">
                            <form action="{{ route('ajuanjadwal.index') }}">
                                <div class="row g-2">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <x-input-with-icon label="Dari" value="{{ Request('dari') }}" name="dari" icon="ti ti-calendar"
                                            datepicker="flatpickr-date" hideLabel />
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <x-input-with-icon label="Sampai" value="{{ Request('sampai') }}" name="sampai" icon="ti ti-calendar"
                                            datepicker="flatpickr-date" hideLabel />
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <x-input-with-icon label="Nama Karyawan" value="{{ Request('nama_karyawan') }}" name="nama_karyawan"
                                            icon="ti ti-search" hideLabel />
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <select name="status" id="status" class="form-select">
                                                <option value="">Status</option>
                                                <option value="p" {{ Request('status') === 'p' ? 'selected' : '' }}>Pending</option>
                                                <option value="a" {{ Request('status') === 'a' ? 'selected' : '' }}>Disetujui</option>
                                                <option value="r" {{ Request('status') === 'r' ? 'selected' : '' }}>Ditolak</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <x-select label="Cabang" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang"
                                            selected="{{ Request('kode_cabang') }}" upperCase="true" hideLabel />
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-12">
                                        <x-select label="Departemen" name="kode_dept" :data="$departemen" key="kode_dept" textShow="nama_dept"
                                            selected="{{ Request('kode_dept') }}" upperCase="true" hideLabel />
                                    </div>
                                    <div class="col-lg-1 col-md-12 col-sm-12">
                                        <button class="btn btn-primary w-100"><i class="ti ti-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            @forelse ($ajuanjadwal as $d)
                                <div class="card mb-2 shadow-sm border">
                                    <div class="card-body p-2">
                                        <div class="row align-items-center">
                                            <!-- Avatar -->
                                            <div class="col-md-1 text-center" style="width: 60px;">
                                                @php
                                                    $path = Storage::url('karyawan/'.$d->karyawan->foto);
                                                @endphp
                                                @if (!empty($d->karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $d->karyawan->foto))
                                                    <img src="{{ url($path) }}" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="No Image" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                            </div>
                                            <!-- Identity -->
                                            <div class="col-md-4">
                                                <div class="fw-bold text-dark" style="font-size: 14px;">{{ $d->karyawan->nama_karyawan }} <span class="text-muted fw-normal" style="font-size: 12px;">({{ $d->nik }})</span></div>
                                                <div class="mt-1">
                                                    <span class="badge bg-label-success" style="font-size: 10px;">{{ $d->karyawan->jabatan->nama_jabatan }}</span>
                                                    <span class="badge bg-label-info" style="font-size: 10px;">{{ $d->karyawan->departemen->nama_dept }}</span>
                                                    <span class="badge bg-label-warning" style="font-size: 10px;">{{ $d->karyawan->cabang->nama_cabang }}</span>
                                                </div>
                                            </div>
                                            <!-- Schedule Change Details -->
                                            <div class="col-md-4 border-start border-end d-none d-md-block text-center">
                                                 <div class="fw-bold text-dark" style="font-size: 13px;">{{ date('d-m-Y', strtotime($d->tanggal)) }}</div>
                                                 <div class="text-muted" style="font-size: 11px;">
                                                    <span class="text-danger">{{ $d->jamKerjaAwal ? $d->jamKerjaAwal->nama_jam_kerja : '-' }}</span> 
                                                    <i class="ti ti-arrow-right mx-1"></i> 
                                                    <span class="text-success">{{ $d->jamKerjaTujuan->nama_jam_kerja }}</span>
                                                 </div>
                                                 <div class="text-muted fst-italic" style="font-size: 10px;">"{{ $d->keterangan }}"</div>
                                            </div>
                                            
                                            <!-- Status -->
                                            <div class="col-md-2 text-center">
                                                @if ($d->status == 'p')
                                                    <span class="badge bg-label-warning py-1 px-2" style="font-size: 11px;">
                                                        <i class="ti ti-hourglass-empty me-1"></i> Pending
                                                    </span>
                                                @elseif ($d->status == 'a')
                                                    <span class="badge bg-success py-1 px-2" style="font-size: 11px;">Disetujui</span>
                                                @elseif ($d->status == 'r')
                                                    <span class="badge bg-danger py-1 px-2" style="font-size: 11px;">Ditolak</span>
                                                @endif
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="col-md-1 text-end">
                                                <div class="btn-group shadow-sm" role="group">
                                                    @if ($d->status == 'p')
                                                        @can('ajuanjadwal.approve')
                                                            <form action="{{ route('ajuanjadwal.approve', $d->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button class="btn btn-sm btn-outline-success py-1 px-2" onclick="return confirm('Apakah Anda Yakin Ingin Menyetujui?')" title="Approve">
                                                                    <i class="ti ti-check"></i>
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('ajuanjadwal.reject', $d->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button class="btn btn-sm btn-outline-danger py-1 px-2" onclick="return confirm('Apakah Anda Yakin Ingin Menolak?')" title="Reject">
                                                                    <i class="ti ti-x"></i>
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    @else
                                                        @if ($d->status == 'a')
                                                            @can('ajuanjadwal.approve')
                                                                <form action="{{ route('ajuanjadwal.cancelapprove', $d->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button class="btn btn-sm btn-danger py-1 px-2" onclick="return confirm('Apakah Anda Yakin Ingin Membatalkan Ajuan Ini?')" title="Batalkan Approval">
                                                                        <i class="ti ti-rotate-2"></i> Batalkan
                                                                    </button>
                                                                </form>
                                                            @endcan
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="card">
                                    <div class="card-body text-center p-5">
                                        <div class="mb-3">
                                            <i class="ti ti-file-x text-muted" style="font-size: 6rem;"></i>
                                        </div>
                                        <h4 class="mb-1 text-muted">Belum ada data</h4>
                                        <p class="text-secondary">Data ajuan jadwal belum tersedia untuk periode atau filter yang dipilih.</p>
                                    </div>
                                </div>
                            @endforelse
                            <div style="float: right;">
                                {{ $ajuanjadwal->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
