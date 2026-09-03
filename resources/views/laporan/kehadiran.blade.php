@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Laporan Kehadiran</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.kehadiran') }}" class="row g-3 mb-3">
            @php
                $tanggalMulai = request('tanggal_mulai', date('Y-m-01'));
                $tanggalAkhir = request('tanggal_akhir', date('Y-m-t'));
            @endphp
            <div class="col-md-2">
                <label>Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggalMulai }}" required>
            </div>
            <div class="col-md-2">
                <label>Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir }}" required>
            </div>
            <div class="col-md-2">
                <label>Departemen</label>
                <select name="kode_dept" id="kodeDept" class="form-control">
                    <option value="">-- Pilih Departemen --</option>
                    @foreach($departemen as $dept)
                        <option value="{{ $dept->kode_dept }}" {{ request('kode_dept') == $dept->kode_dept ? 'selected' : '' }}>
                            {{ $dept->nama_dept }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Cabang</label>
                <select name="kode_cabang" id="kodeCabang" class="form-control">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabangs as $cabang)
                        <option value="{{ $cabang->kode_cabang }}" {{ request('kode_cabang') == $cabang->kode_cabang ? 'selected' : '' }}>
                            {{ $cabang->nama_cabang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>Nama Karyawan</label>
                <select name="nama_karyawan" id="namaKaryawan" class="form-control">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $karyawan)
                        <option value="{{ $karyawan->nama_karyawan }}" {{ request('nama_karyawan') == $karyawan->nama_karyawan ? 'selected' : '' }}>
                            {{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <label>Mode</label>
                <select name="mode" class="form-control">
                    <option value="detail" {{ request('mode', 'detail') == 'detail' ? 'selected' : '' }}>Detail</option>
                    <option value="rekap" {{ request('mode') == 'rekap' ? 'selected' : '' }}>Rekap</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
        <div class="mb-3">
            <a href="{{ route('laporan.kehadiran.cetak', request()->all()) }}" target="_blank" class="btn btn-secondary">Cetak</a>
            <a href="{{ route('laporan.kehadiran.export', request()->all()) }}" class="btn btn-success">Export Excel</a>
        </div>

        @if($mode === 'rekap')
            <div class="alert alert-info">Mode <strong>Rekap</strong> aktif: laporan akan menampilkan total per karyawan.</div>
        @endif

        <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                @if($mode === 'rekap')
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th class="fw-bold">NIK</th>
                                <th class="fw-bold">Nama Karyawan</th>
                                <th class="fw-bold">Kode Cabang</th>
                                <th class="fw-bold">Nama Cabang</th>
                                <th class="fw-bold">Departemen</th>
                                <th class="fw-bold">Jumlah Hari</th>
                                <th class="fw-bold">Lembur</th>
                                <th class="fw-bold">Kelebihan Menit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekapPresensis as $rekap)
                                <tr>
                                    <td>{{ $rekap['nik'] ?? '-' }}</td>
                                    <td>{{ $rekap['nama_karyawan'] ?? '-' }}</td>
                                    <td>{{ $rekap['kode_cabang'] ?? '-' }}</td>
                                    <td>{{ $rekap['nama_cabang'] ?? '-' }}</td>
                                    <td>{{ $rekap['nama_dept'] ?? '-' }}</td>
                                    <td class="text-center">{{ intval($rekap['jumlah_hari'] ?? 0) }}</td>
                                    <td class="text-center">{{ intval($rekap['lembur'] ?? 0) }}</td>
                                    <td class="text-end">{{ intval($rekap['kelebihan_menit'] ?? 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th class="fw-bold">NIK</th>
                                <th class="fw-bold">Nama Karyawan</th>
                                <th class="fw-bold">Kode Cabang</th>
                                <th class="fw-bold">Nama Cabang</th>
                                <th class="fw-bold">Tanggal</th>
                                <th class="fw-bold">Hari</th>
                                <th class="fw-bold">Status</th>
                                <th class="fw-bold">Jam Masuk</th>
                                <th class="fw-bold">Jam Pulang</th>
                                <th class="fw-bold">Jumlah Hari</th>
                                <th class="fw-bold">Lembur</th>
                                <th class="fw-bold">Kelebihan Menit</th>
                            </tr>
                        </thead>
                    <tbody>
                        @forelse($presensis as $presensi)
                            @php
                                $jam_in = $presensi->jam_in ?? $presensi->jam_masuk ?? null;
                                $jam_out = $presensi->jam_out ?? $presensi->jam_pulang ?? null;
                                $tanggal = $presensi->tanggal ?? '';
                                $status = $presensi->status ?? '-';
                                $durasi = 0;
                                $jumlah_hari = 0;
                                $lembur = 0;
                                $kelebihan_menit = 0;
                                $has_empty = false;
                                
                                // Status mapping
                                $status_map = [
                                    'i' => 'Izin Absen',
                                    's' => 'Izin Sakit',
                                    'c' => 'Cuti',
                                    'd' => 'Izin Dinas'
                                ];
                                
                                // Check if this is an izin record
                                $is_izin = in_array($status, ['i', 's', 'c', 'd']);
                                
                                // Hitung durasi jika jam masuk dan jam pulang ada (dan bukan izin)
                                if (!$is_izin && $jam_in && $jam_out) {
                                    $time_in = strtotime($jam_in);
                                    $time_out = strtotime($jam_out);
                                    
                                    // Jika jam pulang lebih kecil dari jam masuk, berarti lintas hari
                                    if ($time_out < $time_in) {
                                        $time_out = strtotime('+1 day', $time_out);
                                    }
                                    
                                    $durasi = ($time_out - $time_in) / 60; // convert to minutes
                                    
                                    // Hitung Jumlah Hari: 1 jika durasi >= 420 menit (7 jam)
                                    $jumlah_hari = ($durasi >= 420) ? 1 : 0;
                                    
                                    // Hitung Lembur: 1 jika durasi >= 720 menit (12 jam)
                                    $lembur = ($durasi >= 720) ? 1 : 0;
                                    
                                    // Hitung Kelebihan Menit
                                    if ($jumlah_hari == 1 && $lembur == 1) {
                                        // Jika hari 1 dan lembur 1: durasi - 780
                                        $kelebihan_menit = $durasi - 780;
                                    } else if ($jumlah_hari == 1 && $lembur == 0) {
                                        // Jika hari 1 dan lembur 0: durasi - 480
                                        $kelebihan_menit = $durasi - 480;
                                    } else if ($jumlah_hari == 0 && $lembur == 0) {
                                        // Jika hari 0 dan lembur 0: durasi dalam menit
                                        $kelebihan_menit = $durasi;
                                    } else {
                                        // Untuk kasus lainnya
                                        $kelebihan_menit = 0;
                                    }
                                } elseif ($is_izin) {
                                    // Untuk izin, don't count as working day
                                    $has_empty = true;
                                } else {
                                    $has_empty = true;
                                }
                                
                                // Hitung hari dari tanggal
                                $hari_arr = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
                                $day_short = $hari_arr[date('D', strtotime($tanggal))] ?? '-';
                            @endphp
                            <tr>
                                <td>{{ $presensi->nik ?? '-' }}</td>
                                <td>{{ $presensi->nama_karyawan ?? '-' }}</td>
                                <td>{{ $presensi->kode_cabang ?? '-' }}</td>
                                <td>{{ $presensi->nama_cabang ?? '-' }}</td>
                                <td>{{ $tanggal }}</td>
                                <td class="text-center">{{ $day_short }}</td>
                                <td class="text-center">{{ $is_izin ? $status_map[$status] ?? $status : $status }}</td>
                                <td class="{{ !$jam_in && !$is_izin ? 'table-danger text-center' : 'text-center' }}">
                                    @if($is_izin)
                                        {{ $status_map[$status] ?? $status }}
                                    @else
                                        {{ $jam_in ? date('H:i', strtotime($jam_in)) : 'TIDAK ABSEN' }}
                                    @endif
                                </td>
                                <td class="{{ !$jam_out && !$is_izin ? 'table-danger text-center' : 'text-center' }}">
                                    @if($is_izin)
                                        {{ $status_map[$status] ?? $status }}
                                    @else
                                        {{ $jam_out ? date('H:i', strtotime($jam_out)) : 'TIDAK ABSEN' }}
                                    @endif
                                </td>
                                <td class="text-center">{{ $has_empty ? '-' : $jumlah_hari }}</td>
                                <td class="text-center">{{ $has_empty ? '-' : $lembur }}</td>
                                <td class="text-end">{{ $has_empty ? '-' : intval($kelebihan_menit) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $presensis->withQueryString()->links() }}
                @endif
            </div>
        </div>
    </div>
        <div class="mt-2 ms-4">
        <small>
            <b>Keterangan Status:</b>
            <span>h = Hadir, i = Izin, c = Cuti, s = Sakit</span>
        </small>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kodeCabangSelect = document.getElementById('kodeCabang');
    const kodeDeptSelect = document.getElementById('kodeDept');
    const namaKaryawanSelect = document.getElementById('namaKaryawan');
    const selectedNamaKaryawan = '{{ request("nama_karyawan") }}';
    
    // Function to load karyawan based on selected cabang and/or departemen
    function loadKaryawanByFilter() {
        const kodeCabang = kodeCabangSelect.value;
        const kodeDept = kodeDeptSelect.value;

        if (!kodeCabang && !kodeDept) {
            loadAllKaryawan();
            return;
        }

        let url = '/laporan/kehadiran/api/karyawan';
        const params = new URLSearchParams();

        if (kodeCabang) {
            params.append('kode_cabang', kodeCabang);
        }

        if (kodeDept) {
            params.append('kode_dept', kodeDept);
        }

        if (params.toString()) {
            url += '?' + params.toString();
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                namaKaryawanSelect.innerHTML = '<option value="">-- Pilih Karyawan --</option>';

                data.forEach(karyawan => {
                    const option = document.createElement('option');
                    option.value = karyawan.nama_karyawan;
                    option.textContent = karyawan.nik + ' - ' + karyawan.nama_karyawan;

                    if (karyawan.nama_karyawan === selectedNamaKaryawan) {
                        option.selected = true;
                    }

                    namaKaryawanSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Function to load all karyawan
    function loadAllKaryawan() {
        namaKaryawanSelect.innerHTML = '<option value="">-- Pilih Karyawan --</option>';
        
        // Add all karyawan that were passed from the server
        @foreach($karyawans as $karyawan)
            const option = document.createElement('option');
            option.value = '{{ $karyawan->nama_karyawan }}';
            option.textContent = '{{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}';
            
            @if(request('nama_karyawan') == $karyawan->nama_karyawan)
                option.selected = true;
            @endif
            
            namaKaryawanSelect.appendChild(option);
        @endforeach
    }
    
    // Load karyawan when page loads
    if (kodeCabangSelect.value || kodeDeptSelect.value) {
        loadKaryawanByFilter();
    } else {
        loadAllKaryawan();
    }
    
    // Listen to cabang selection change
    kodeCabangSelect.addEventListener('change', function() {
        loadKaryawanByFilter();
    });
    
    // Listen to departemen selection change
    kodeDeptSelect.addEventListener('change', function() {
        loadKaryawanByFilter();
    });
});
</script>
@endsection