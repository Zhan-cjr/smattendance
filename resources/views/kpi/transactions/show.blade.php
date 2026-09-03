@extends('layouts.app')
@section('titlepage', 'Detail & Realisasi KPI')
@section('content')
@section('navigasi')
    <span>Detail & Realisasi KPI</span>
@endsection
<div class="row">
    <div class="col-12">
        <form action="{{ route('kpi.transactions.update', $kpi_employee->id) }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-12">
                     <div class="card">

                        <div class="card-body p-3">
                            @php
                                $bgColor = !empty($general_setting->theme_color_1) ? $general_setting->theme_color_1 : '#18b76f';
                            @endphp
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    @if(!empty($kpi_employee->karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $kpi_employee->karyawan->foto))
                                        <img src="{{ getfotoKaryawan($kpi_employee->karyawan->foto) }}" class="avatar avatar-md rounded" style="object-fit: cover;">
                                    @else
                                        <span class="avatar avatar-md rounded d-flex justify-content-center align-items-center text-white fw-bold" 
                                              style="width: 46px; height: 46px; font-size: 20px; background-color: {{ $bgColor }};">
                                            {{ substr($kpi_employee->karyawan->nama_karyawan, 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="col">
                                    <div class="fw-bold text-dark">{{ $kpi_employee->karyawan->nama_karyawan }}</div>
                                    <div class="text-secondary small mb-1">{{ $kpi_employee->karyawan->jabatan->nama_jabatan }} | {{ $kpi_employee->karyawan->departemen->nama_dept ?? '-' }}</div>
                                    <div class="d-flex align-items-center flex-wrap gap-3 small text-secondary">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-id me-1"></i>
                                            {{ $kpi_employee->karyawan->nik_show ?? $kpi_employee->karyawan->nik }}
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-calendar-event me-1"></i>
                                            Join: {{ date('d M Y', strtotime($kpi_employee->karyawan->tanggal_masuk)) }}
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-hourglass me-1"></i>
                                            @php
                                                $awal = new DateTime($kpi_employee->karyawan->tanggal_masuk);
                                                $akhir = new DateTime();
                                                $masa_kerja = $akhir->diff($awal);
                                            @endphp
                                            {{ $masa_kerja->y . ' Th ' . $masa_kerja->m . ' Bln' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto border-start ps-3 d-none d-md-block">
                                    <div class="text-muted small">Periode KPI</div>
                                    <div class="fw-bold">{{ $kpi_employee->period->nama_periode }}</div>
                                    <div class="text-secondary small">
                                        {{ date('d M Y', strtotime($kpi_employee->period->start_date)) }} - {{ date('d M Y', strtotime($kpi_employee->period->end_date)) }}
                                    </div>
                                </div>
                                <div class="col-md-2 d-none d-md-block border-start ps-3">
                                    <div class="card border-0" style="background-color: {{ $bgColor }};">
                                        <div class="card-body p-2 text-center text-white">
                                            <div class="text-uppercase text-white-50 small fw-bold">Grade</div>
                                            <div class="display-6 fw-bold text-white">{{ $kpi_employee->grade ?? '-' }}</div>
                                            <div class="text-white-50 small">
                                                Nilai: <span class="fw-bold text-white">{{ number_format($kpi_employee->total_nilai, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 d-none d-md-block border-start ps-3 text-end">
                                    <div class="mb-2">
                                        @if ($kpi_employee->status == 'draft')
                                            <span class="badge bg-warning">Draft</span>
                                        @elseif ($kpi_employee->status == 'submitted')
                                            <span class="badge bg-info">Submitted</span>
                                        @elseif ($kpi_employee->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $kpi_employee->status }}</span>
                                        @endif
                                    </div>
                                    <div class="btn-list justify-content-end">
                                        <a href="{{ route('kpi.transactions.print', $kpi_employee->id) }}" target="_blank" class="btn btn-secondary btn-sm" title="Print">
                                            <i class="ti ti-printer"></i>
                                        </a>
                                        @can('kpi.transaction.approve')
                                            @if($kpi_employee->status == 'submitted')
                                            <button type="submit" formaction="{{ route('kpi.transactions.approve', $kpi_employee->id) }}" class="btn btn-success btn-sm" onclick="return confirm('Apakah Anda Yakin Ingin Menyetujui KPI Ini?');" title="Approve">
                                                <i class="ti ti-check"></i>
                                            </button>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                     <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Input Realisasi KPI</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive rounded-3 overflow-hidden">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 1%; white-space: nowrap;">No</th>
                                            <th>Nama Indikator</th>
                                            <th style="width: 1%; white-space: nowrap;">Satuan</th>
                                            <th style="width: 1%; white-space: nowrap;">Target</th>
                                            <th style="width: 1%; white-space: nowrap;">Bobot</th>
                                            <th style="width: 10%; white-space: nowrap;">Realisasi</th>
                                            <th style="width: 1%; white-space: nowrap;">Nilai</th>
                                        </tr>
                                    </thead>
                                <tbody>
                                    @foreach ($kpi_employee->details as $detail)
                                        <tr class="align-middle">
                                            <td>
                                                {{ $loop->iteration }}
                                                <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                                            </td>
                                            <td>
                                                {{ $detail->indicator->nama_indikator }} <br>
                                                <small class="text-muted">{{ $detail->indicator->deskripsi }}</small>
                                            </td>
                                            <td>{{ $detail->indicator->satuan }}</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    {{ $detail->target }}
                                                    @if ($detail->indicator->jenis_target == 'min')
                                                        <i class="ti ti-arrow-down text-danger" title="Minimal (Semakin Kecil Baik)"></i>
                                                    @else
                                                        <i class="ti ti-arrow-up text-success" title="Maksimal (Semakin Besar Baik)"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $detail->bobot }}</td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control" name="realisasi[]" value="{{ $detail->realisasi }}" required {{ $kpi_employee->status == 'approved' || $detail->indicator->mode == 'auto'  ? 'readonly' : '' }}>
                                                @if($detail->indicator->mode == 'auto')
                                                    <small class="text-muted fst-italic d-block mt-1">(Auto: {{ $detail->indicator->metric_source }})</small>
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($detail->skor, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                            
                            @if($kpi_employee->status != 'approved')
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                        <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                        <path d="M14 4l0 4l-6 0l0 -4"></path>
                                    </svg>
                                    Simpan Realisasi
                                </button>
                            </div>
                            @endif
                        </div>
                     </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
