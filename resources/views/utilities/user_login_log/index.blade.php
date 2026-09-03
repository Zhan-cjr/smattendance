@extends('layouts.app')
@section('titlepage', 'Log Login User')

@section('content')
@section('navigasi')
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            Log Login User
            <div class="text-muted mt-1" style="font-size: 0.75rem; font-weight: normal; text-transform: none; letter-spacing: 0px;">
                Monitoring riwayat login pengguna sistem ke dalam aplikasi.
            </div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block" style="font-size: 0.75rem;">
            <ol class="breadcrumb breadcrumb-style1 mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard.index') }}">
                        <i class="ti ti-home-2 ti-xs"></i>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">
                        <i class="ti ti-adjustments-alt ti-xs me-1"></i> Utilities
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="ti ti-login ti-xs me-1"></i> Log Login
                </li>
            </ol>
        </nav>
    </div>
@endsection

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <form action="{{ route('userloginlog.index') }}" method="GET">
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-lg-3 col-md-4 col-sm-12">
                    <x-input-with-icon label="Nama User" value="{{ request('nama_user') }}" name="nama_user"
                        icon="ti ti-user" hideLabel placeholder="Cari Nama User..." />
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <x-input-with-icon label="Tanggal Awal" value="{{ request('tanggal_awal') }}" name="tanggal_awal"
                        icon="ti ti-calendar" hideLabel placeholder="Tanggal Awal" datepicker="flatpickr-date" />
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12">
                    <x-input-with-icon label="Tanggal Akhir" value="{{ request('tanggal_akhir') }}" name="tanggal_akhir"
                        icon="ti ti-calendar" hideLabel placeholder="Tanggal Akhir" datepicker="flatpickr-date" />
                </div>
                <div class="col-lg-3 col-md-2 col-sm-12 mb-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="ti ti-search me-1"></i> Cari
                        </button>
                        <a href="{{ route('userloginlog.index') }}" class="btn btn-label-secondary p-2" title="Reset">
                            <i class="ti ti-refresh"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2" style="background-color: var(--theme-color-1) !important; color: white !important; min-height: 50px;">
                <div class="d-flex align-items-center">
                    <i class="ti ti-login me-2 fs-5"></i>
                    <h6 class="card-title mb-0 text-white">Data Log Login</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: var(--theme-color-1) !important; color: white !important;">
                            <tr>
                                <th class="text-white py-3" width="50">NO.</th>
                                <th class="text-white py-3">PENGGUNA</th>
                                <th class="text-white py-3 border-start">INFO AKUN</th>
                                <th class="text-white py-3 border-start">IP ADDRESS</th>
                                <th class="text-white py-3 border-start">DEVICE / AGENT</th>
                                <th class="text-white py-3 border-start">WAKTU LOGIN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td class="py-2">{{ $logs->firstItem() + $loop->index }}</td>
                                    <td class="py-2 fw-bold">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary shadow-sm" style="font-size: 0.75rem;">
                                                    {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                                </span>
                                            </div>
                                            {{ $log->user->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="py-2 border-start">
                                        <small class="text-muted d-block">{{ $log->user->email ?? '-' }}</small>
                                        <span class="badge bg-label-secondary border-0" style="font-size: 0.65rem;">
                                            <i class="ti ti-id-badge me-1" style="font-size: 0.75rem;"></i>{{ $log->user->username ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-2 border-start">
                                        <span class="badge bg-label-info border shadow-xs" style="font-size: 0.75rem;">{{ $log->ip_address }}</span>
                                    </td>
                                    <td class="py-2 border-start" style="max-width: 350px;">
                                        <div class="text-wrap">
                                            <small class="text-muted opacity-75" style="font-size: 0.7rem; line-height: 1;">{{ $log->user_agent }}</small>
                                        </div>
                                    </td>
                                    <td class="py-2 border-start fw-bold">
                                        <div class="d-flex flex-column">
                                            <span>{{ \Carbon\Carbon::parse($log->login_at)->format('d/m/Y') }}</span>
                                            <small class="text-primary" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($log->login_at)->format('H:i:s') }} WIB</small>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center text-muted">
                                            <i class="ti ti-info-circle fs-1 mb-2"></i>
                                            <p class="mb-0">Tidak ada riwayat login yang ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    });
</script>
@endpush
