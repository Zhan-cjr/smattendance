@extends('v2.layouts.app')

@section('titlepage', 'Laporan Kehadiran')
@section('page_title', 'Laporan Kehadiran')

@section('content')
@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $tanggalMulai = request('tanggal_mulai', date('Y-m-01'));
    $tanggalAkhir = request('tanggal_akhir', date('Y-m-t'));
@endphp

<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ti ti-file-analytics text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Laporan Kehadiran</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Rekapitulasi detail & rekap kehadiran karyawan, lembur, dan kelebihan menit
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('laporan.kehadiran.cetak', request()->all()) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-2xs transition">
                <i class="ti ti-printer text-sm"></i>
                <span>Cetak</span>
            </a>
            <a href="{{ route('laporan.kehadiran.export', request()->all()) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-2xs transition">
                <i class="ti ti-file-spreadsheet text-sm"></i>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4">
        <form method="GET" action="{{ route('laporan.kehadiran') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3 items-end">
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-slate-700">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" required
                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
            </div>
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-slate-700">Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" required
                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
            </div>
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-slate-700">Departemen</label>
                <select name="kode_dept" id="kodeDept" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    <option value="">-- Semua Departemen --</option>
                    @foreach($departemen as $dept)
                        <option value="{{ $dept->kode_dept }}" {{ request('kode_dept') == $dept->kode_dept ? 'selected' : '' }}>
                            {{ $dept->nama_dept }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-slate-700">Cabang</label>
                <select name="kode_cabang" id="kodeCabang" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    <option value="">-- Semua Cabang --</option>
                    @foreach($cabangs as $cabang)
                        <option value="{{ $cabang->kode_cabang }}" {{ request('kode_cabang') == $cabang->kode_cabang ? 'selected' : '' }}>
                            {{ $cabang->nama_cabang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-slate-700">Nama Karyawan</label>
                <select name="nama_karyawan" id="namaKaryawan" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    <option value="">-- Semua Karyawan --</option>
                    @foreach($karyawans as $karyawan)
                        <option value="{{ $karyawan->nama_karyawan }}" {{ request('nama_karyawan') == $karyawan->nama_karyawan ? 'selected' : '' }}>
                            {{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-[11px] font-bold text-slate-700">Mode</label>
                <select name="mode" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    <option value="detail" {{ request('mode', 'detail') == 'detail' ? 'selected' : '' }}>Detail</option>
                    <option value="rekap" {{ request('mode') == 'rekap' ? 'selected' : '' }}>Rekap</option>
                </select>
            </div>
            <div>
                <button type="submit"
                        class="w-full h-10 rounded-xl text-white text-xs font-bold shadow-2xs transition hover:opacity-95 cursor-pointer flex items-center justify-center gap-1.5"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-filter text-sm"></i>
                    <span>Filter</span>
                </button>
            </div>
        </form>
    </div>

    @if($mode === 'rekap')
        <div class="px-4 py-3 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-xs font-semibold flex items-center gap-2">
            <i class="ti ti-info-circle text-base"></i>
            <span>Mode <strong>Rekap</strong> aktif: laporan menampilkan total akumulasi per karyawan.</span>
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            @if($mode === 'rekap')
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-800 text-white uppercase text-[11px] font-bold">
                        <tr>
                            <th class="px-3.5 py-3">NIK</th>
                            <th class="px-3.5 py-3">Nama Karyawan</th>
                            <th class="px-3.5 py-3">Kode Cabang</th>
                            <th class="px-3.5 py-3">Nama Cabang</th>
                            <th class="px-3.5 py-3">Departemen</th>
                            <th class="px-3.5 py-3 text-center">Jumlah Hari</th>
                            <th class="px-3.5 py-3 text-center">Lembur</th>
                            <th class="px-3.5 py-3 text-right">Kelebihan Menit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rekapPresensis as $rekap)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3.5 py-2.5 font-mono font-semibold text-slate-700">{{ $rekap['nik'] ?? '-' }}</td>
                                <td class="px-3.5 py-2.5 font-bold text-slate-800">{{ $rekap['nama_karyawan'] ?? '-' }}</td>
                                <td class="px-3.5 py-2.5 text-slate-600">{{ $rekap['kode_cabang'] ?? '-' }}</td>
                                <td class="px-3.5 py-2.5 text-slate-600">{{ $rekap['nama_cabang'] ?? '-' }}</td>
                                <td class="px-3.5 py-2.5 text-slate-600">{{ $rekap['nama_dept'] ?? '-' }}</td>
                                <td class="px-3.5 py-2.5 text-center font-bold text-emerald-700">{{ intval($rekap['jumlah_hari'] ?? 0) }}</td>
                                <td class="px-3.5 py-2.5 text-center font-bold text-amber-700">{{ intval($rekap['lembur'] ?? 0) }}</td>
                                <td class="px-3.5 py-2.5 text-right font-mono font-bold text-slate-800">{{ intval($rekap['kelebihan_menit'] ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-400 font-medium">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-800 text-white uppercase text-[11px] font-bold">
                        <tr>
                            <th class="px-3 py-3">NIK</th>
                            <th class="px-3 py-3">Nama Karyawan</th>
                            <th class="px-3 py-3">Kode Cabang</th>
                            <th class="px-3 py-3">Nama Cabang</th>
                            <th class="px-3 py-3">Tanggal</th>
                            <th class="px-3 py-3 text-center">Hari</th>
                            <th class="px-3 py-3 text-center">Status</th>
                            <th class="px-3 py-3 text-center">Jam Masuk</th>
                            <th class="px-3 py-3 text-center">Jam Pulang</th>
                            <th class="px-3 py-3 text-center">Jumlah Hari</th>
                            <th class="px-3 py-3 text-center">Lembur</th>
                            <th class="px-3 py-3 text-right">Kelebihan Menit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
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

                                $status_map = [
                                    'i' => 'Izin Absen',
                                    's' => 'Izin Sakit',
                                    'c' => 'Cuti',
                                    'd' => 'Izin Dinas'
                                ];

                                $is_izin = in_array($status, ['i', 's', 'c', 'd']);

                                if (!$is_izin && $jam_in && $jam_out) {
                                    $time_in = strtotime($jam_in);
                                    $time_out = strtotime($jam_out);
                                    if ($time_out < $time_in) {
                                        $time_out = strtotime('+1 day', $time_out);
                                    }
                                    $durasi = ($time_out - $time_in) / 60;
                                    $jumlah_hari = ($durasi >= 420) ? 1 : 0;
                                    $lembur = ($durasi >= 720) ? 1 : 0;

                                    if ($jumlah_hari == 1 && $lembur == 1) {
                                        $kelebihan_menit = $durasi - 780;
                                    } else if ($jumlah_hari == 1 && $lembur == 0) {
                                        $kelebihan_menit = $durasi - 480;
                                    } else if ($jumlah_hari == 0 && $lembur == 0) {
                                        $kelebihan_menit = $durasi;
                                    } else {
                                        $kelebihan_menit = 0;
                                    }
                                } else {
                                    $has_empty = true;
                                }

                                $hari_arr = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
                                $day_short = $hari_arr[date('D', strtotime($tanggal))] ?? '-';
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-3 py-2.5 font-mono font-semibold text-slate-700">{{ $presensi->nik ?? '-' }}</td>
                                <td class="px-3 py-2.5 font-bold text-slate-800">{{ $presensi->nama_karyawan ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $presensi->kode_cabang ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $presensi->nama_cabang ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-slate-600">{{ $tanggal }}</td>
                                <td class="px-3 py-2.5 text-center text-slate-600">{{ $day_short }}</td>
                                <td class="px-3 py-2.5 text-center font-semibold uppercase">{{ $is_izin ? ($status_map[$status] ?? $status) : $status }}</td>
                                <td class="px-3 py-2.5 text-center {{ !$jam_in && !$is_izin ? 'bg-rose-50 text-rose-700 font-bold' : '' }}">
                                    @if($is_izin)
                                        {{ $status_map[$status] ?? $status }}
                                    @else
                                        {{ $jam_in ? date('H:i', strtotime($jam_in)) : 'TIDAK ABSEN' }}
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center {{ !$jam_out && !$is_izin ? 'bg-rose-50 text-rose-700 font-bold' : '' }}">
                                    @if($is_izin)
                                        {{ $status_map[$status] ?? $status }}
                                    @else
                                        {{ $jam_out ? date('H:i', strtotime($jam_out)) : 'TIDAK ABSEN' }}
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-center font-bold">{{ $has_empty ? '-' : $jumlah_hari }}</td>
                                <td class="px-3 py-2.5 text-center font-bold">{{ $has_empty ? '-' : $lembur }}</td>
                                <td class="px-3 py-2.5 text-right font-mono font-semibold">{{ $has_empty ? '-' : intval($kelebihan_menit) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="px-4 py-8 text-center text-slate-400 font-medium">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4 border-t border-slate-100 v2-pagination">
                    {{ $presensis->withQueryString()->links() }}
                </div>
            @endif
        </div>
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-600">
            <strong>Keterangan Status:</strong> h = Hadir, i = Izin, c = Cuti, s = Sakit, d = Dinas
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kodeCabangSelect = document.getElementById('kodeCabang');
    const kodeDeptSelect = document.getElementById('kodeDept');
    const namaKaryawanSelect = document.getElementById('namaKaryawan');
    const selectedNamaKaryawan = '{{ request("nama_karyawan") }}';

    function loadKaryawanByFilter() {
        const kodeCabang = kodeCabangSelect.value;
        const kodeDept = kodeDeptSelect.value;

        if (!kodeCabang && !kodeDept) {
            loadAllKaryawan();
            return;
        }

        let url = '/laporan/kehadiran/api/karyawan';
        const params = new URLSearchParams();
        if (kodeCabang) params.append('kode_cabang', kodeCabang);
        if (kodeDept) params.append('kode_dept', kodeDept);
        if (params.toString()) url += '?' + params.toString();

        fetch(url)
            .then(response => response.json())
            .then(data => {
                namaKaryawanSelect.innerHTML = '<option value="">-- Semua Karyawan --</option>';
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

    function loadAllKaryawan() {
        namaKaryawanSelect.innerHTML = '<option value="">-- Semua Karyawan --</option>';
        @foreach($karyawans as $karyawan)
            {
                const option = document.createElement('option');
                option.value = '{{ $karyawan->nama_karyawan }}';
                option.textContent = '{{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}';
                @if(request('nama_karyawan') == $karyawan->nama_karyawan)
                    option.selected = true;
                @endif
                namaKaryawanSelect.appendChild(option);
            }
        @endforeach
    }

    if (kodeCabangSelect.value || kodeDeptSelect.value) {
        loadKaryawanByFilter();
    } else {
        loadAllKaryawan();
    }

    kodeCabangSelect.addEventListener('change', loadKaryawanByFilter);
    kodeDeptSelect.addEventListener('change', loadKaryawanByFilter);
});
</script>
@endsection

