<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kehadiran Export</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background-color: #024a75; color: white; font-weight: bold; }
        .red-bg { background-color: #ff6b6b; color: white; font-weight: bold; }
        .center { text-align: center; }
        .number { text-align: right; padding-right: 10px; }
    </style>
</head>
<body>
    <h3 style="text-align: center; margin-bottom: 10px;">LAPORAN KEHADIRAN KARYAWAN</h3>
    
    @if(($mode ?? 'detail') === 'rekap')
        <table>
            <thead>
                <tr>
                    <th><strong>NIK</strong></th>
                    <th><strong>Nama Karyawan</strong></th>
                    <th><strong>Kode Cabang</strong></th>
                    <th><strong>Nama Cabang</strong></th>
                    <th><strong>Departemen</strong></th>
                    <th><strong>Jumlah Hari</strong></th>
                    <th><strong>Lembur</strong></th>
                    <th><strong>Kelebihan Menit</strong></th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensis as $presensi)
                    <tr>
                        <td>{{ $presensi['nik'] ?? '-' }}</td>
                        <td>{{ $presensi['nama_karyawan'] ?? '-' }}</td>
                        <td>{{ $presensi['kode_cabang'] ?? '-' }}</td>
                        <td>{{ $presensi['nama_cabang'] ?? '-' }}</td>
                        <td>{{ $presensi['nama_dept'] ?? '-' }}</td>
                        <td class="text-center">{{ intval($presensi['jumlah_hari'] ?? 0) }}</td>
                        <td class="text-center">{{ intval($presensi['lembur'] ?? 0) }}</td>
                        <td class="text-end">{{ intval($presensi['kelebihan_menit'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th><strong>NIK</strong></th>
                    <th><strong>Nama Karyawan</strong></th>
                    <th><strong>Kode Cabang</strong></th>
                    <th><strong>Nama Cabang</strong></th>
                    <th><strong>Tanggal</strong></th>
                    <th><strong>Hari</strong></th>
                    <th><strong>Status</strong></th>
                    <th><strong>Jam Masuk</strong></th>
                    <th><strong>Jam Pulang</strong></th>
                    <th><strong>Jumlah Hari</strong></th>
                    <th><strong>Lembur</strong></th>
                    <th><strong>Kelebihan Menit</strong></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $prev_nik = null;
                    $subtotal_jumlah_hari = 0;
                    $subtotal_lembur = 0;
                    $subtotal_kelebihan_menit = 0;
                    $prev_nama_karyawan = '';
                    $items = $presensis->toArray();
                    $total_items = count($items);
                    $index = 0;
                @endphp
            @forelse($items as $presensi)
                @php
                    $current_nik = $presensi['nik'] ?? '-';
                    $jam_in = $presensi['jam_in'] ?? $presensi['jam_masuk'] ?? null;
                    $jam_out = $presensi['jam_out'] ?? $presensi['jam_pulang'] ?? null;
                    $tanggal = $presensi['tanggal'] ?? '';
                    $status = $presensi['status'] ?? '-';
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
                        
                        // Hitung Lembur: 1 jika durasi >= 720 menit
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
                    
                    // Check if NIK changed, jika iya maka tampilkan subtotal untuk NIK sebelumnya
                    if ($prev_nik !== null && $prev_nik !== $current_nik) {
                        // Display subtotal for previous employee
                        echo '<tr style="background-color: #e8e8e8; font-weight: bold;">';
                        echo '<td colspan="9"><strong>SUBTOTAL ' . htmlspecialchars($prev_nama_karyawan) . '</strong></td>';
                        echo '<td class="center"><strong>' . intval($subtotal_jumlah_hari) . '</strong></td>';
                        echo '<td class="center"><strong>' . intval($subtotal_lembur) . '</strong></td>';
                        echo '<td class="number"><strong>' . intval($subtotal_kelebihan_menit) . '</strong></td>';
                        echo '</tr>';
                        
                        // Reset subtotal
                        $subtotal_jumlah_hari = 0;
                        $subtotal_lembur = 0;
                        $subtotal_kelebihan_menit = 0;
                    }
                    
                    // Update subtotal (hanya untuk data presensi, bukan izin)
                    if (!$has_empty && !$is_izin) {
                        $subtotal_jumlah_hari += $jumlah_hari;
                        $subtotal_lembur += $lembur;
                        $subtotal_kelebihan_menit += $kelebihan_menit;
                    }
                    
                    $prev_nik = $current_nik;
                    $prev_nama_karyawan = $presensi['nama_karyawan'] ?? '-';
                    $index++;
                @endphp
                <tr>
                    <td>{{ $presensi['nik'] ?? '-' }}</td>
                    <td>{{ $presensi['nama_karyawan'] ?? '-' }}</td>
                    <td>{{ $presensi['kode_cabang'] ?? '-' }}</td>
                    <td>{{ $presensi['nama_cabang'] ?? '-' }}</td>
                    <td>{{ $tanggal }}</td>
                    <td class="center">{{ $day_short }}</td>
                    <td class="center">{{ $is_izin ? $status_map[$status] ?? $status : $status }}</td>
                    <td class="{{ !$jam_in && !$is_izin ? 'red-bg center' : 'center' }}">
                        @if($is_izin)
                            {{ $status_map[$status] ?? $status }}
                        @else
                            {{ $jam_in ? date('H:i', strtotime($jam_in)) : 'TIDAK ABSEN' }}
                        @endif
                    </td>
                    <td class="{{ !$jam_out && !$is_izin ? 'red-bg center' : 'center' }}">
                        @if($is_izin)
                            {{ $status_map[$status] ?? $status }}
                        @else
                            {{ $jam_out ? date('H:i', strtotime($jam_out)) : 'TIDAK ABSEN' }}
                        @endif
                    </td>
                    <td class="center">{{ $has_empty ? '-' : $jumlah_hari }}</td>
                    <td class="center">{{ $has_empty ? '-' : $lembur }}</td>
                    <td class="number">{{ $has_empty ? '-' : intval($kelebihan_menit) }}</td>
                </tr>
                @if($index == $total_items)
                    <tr style="background-color: #e8e8e8; font-weight: bold;">
                        <td colspan="9"><strong>SUBTOTAL {{ $prev_nama_karyawan }}</strong></td>
                        <td class="center"><strong>{{ intval($subtotal_jumlah_hari) }}</strong></td>
                        <td class="center"><strong>{{ intval($subtotal_lembur) }}</strong></td>
                        <td class="number"><strong>{{ intval($subtotal_kelebihan_menit) }}</strong></td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="12" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <div style="margin-top: 30px; text-align: right; padding-right: 50px;">
        <p>{{ date('d-m-Y') }}</p>
        <p style="margin-top: 50px;">Mengetahui,</p>
        <p style="margin-top: 30px;">_____________________</p>
    </div>
</body>
</html>

