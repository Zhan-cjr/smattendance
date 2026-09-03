@extends('layouts.app')
@section('titlepage', 'Set Target KPI')
@section('content')
@section('navigasi')
    <span>Set Target KPI</span>
@endsection
<div class="page-body">
    <div class="container-xl">
        <form action="{{ route('kpi.transactions.store') }}" method="POST">
            @csrf
            <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
            <input type="hidden" name="kpi_period_id" value="{{ $period->id }}">
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                               
                                <div class="col-auto">
                                    @if(!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                                        <img src="{{ getfotoKaryawan($karyawan->foto) }}" class="avatar avatar-md rounded" style="object-fit: cover;">
                                    @else
                                        @php
                                            $bgColor = !empty($general_setting->theme_color_1) ? $general_setting->theme_color_1 : '#18b76f';
                                        @endphp
                                        <span class="avatar avatar-md rounded d-flex justify-content-center align-items-center text-white fw-bold" 
                                              style="width: 46px; height: 46px; font-size: 20px; background-color: {{ $bgColor }};">
                                            {{ substr($karyawan->nama_karyawan, 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="col">
                                    <div class="fw-bold text-dark">{{ $karyawan->nama_karyawan }}</div>
                                    <div class="text-secondary small mb-1">{{ $karyawan->jabatan->nama_jabatan }} | {{ $karyawan->departemen->nama_dept ?? '-' }}</div>
                                    <div class="d-flex align-items-center flex-wrap gap-3 small text-secondary">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-id me-1"></i>
                                            {{ $karyawan->nik_show ?? $karyawan->nik }}
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-calendar-event me-1"></i>
                                            Join: {{ date('d M Y', strtotime($karyawan->tanggal_masuk)) }}
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-hourglass me-1"></i>
                                            @php
                                                $awal = new DateTime($karyawan->tanggal_masuk);
                                                $akhir = new DateTime();
                                                $masa_kerja = $akhir->diff($awal);
                                            @endphp
                                            {{ $masa_kerja->y . ' Th ' . $masa_kerja->m . ' Bln' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto border-start ps-3 d-none d-md-block">
                                    <div class="text-muted small">Periode KPI</div>
                                    <div class="fw-bold">{{ $period->nama_periode }}</div>
                                </div>
                                <div class="col-auto d-none d-md-block">
                                    <div class="text-secondary small">
                                        {{ date('d M Y', strtotime($period->start_date)) }} - {{ date('d M Y', strtotime($period->end_date)) }}
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
                       
                        <div class="card-body">
                            @if ($indicators->isEmpty())
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class="ti ti-alert-triangle me-2 fs-2"></i>
                                    <div>
                                        <strong>Belum ada Indikator KPI!</strong><br>
                                        Indikator KPI untuk Jabatan dan Departemen ini belum dikonfigurasi. Silakan hubungi HRD/Admin untuk melakukan konfigurasi terlebih dahulu.
                                    </div>
                                </div>
                            @else
                                <div class="table-responsive rounded-3 overflow-hidden">
                                    <table class="table table-bordered table-striped table-hover table-sm">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Indikator</th>
                                                <th>Satuan</th>
                                                <th>Jenis Target</th>
                                                <th>Deskripsi</th>
                                                <th style="width: 15%">Target</th>
                                                <th style="width: 10%">Bobot</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $total_bobot = 0; @endphp
                                            @foreach ($indicators as $index => $indicator)
                                                <tr>
                                                    <td>
                                                        {{ $loop->iteration }}
                                                        <input type="hidden" name="indicator_id[]" value="{{ $indicator->id }}">
                                                    </td>
                                                    <td>{{ $indicator->nama_indikator }}</td>
                                                    <td>{{ $indicator->satuan }}</td>
                                                    <td>{{ strtoupper($indicator->jenis_target) }}</td>
                                                    <td><small>{{ $indicator->deskripsi }}</small></td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control" name="target[]" value="{{ $indicator->target }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" class="form-control bobot-input" name="bobot[]" value="{{ $indicator->bobot }}">
                                                    </td>
                                                </tr>
                                                @php $total_bobot += $indicator->bobot; @endphp
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6" class="text-end fw-bold">Total Bobot</td>
                                                <td class="fw-bold" id="totalBobot">{{ $total_bobot }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path>
                                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M14 4l0 4l-6 0l0 -4"></path>
                                        </svg>
                                        Simpan Target
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

@section('custom_script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bobotInputs = document.querySelectorAll('.bobot-input');
        const totalBobotDisplay = document.getElementById('totalBobot');

        function updateTotalBobot() {
            let total = 0;
            bobotInputs.forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            totalBobotDisplay.textContent = total.toFixed(2);
            
            if (total !== 100) {
                 totalBobotDisplay.classList.add('text-danger');
                 totalBobotDisplay.classList.remove('text-success');
            } else {
                 totalBobotDisplay.classList.add('text-success');
                 totalBobotDisplay.classList.remove('text-danger');
            }
        }

        bobotInputs.forEach(input => {
            input.addEventListener('input', updateTotalBobot);
        });
        
        // Initial check
        updateTotalBobot();
    });
</script>
@endsection
