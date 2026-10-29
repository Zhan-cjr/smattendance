@extends('v2.layouts.app')

@section('titlepage', 'Pengaturan Umum (General Settings)')
@section('page_title', 'General Settings')

@section('content')

@php
    use Illuminate\Support\Facades\Storage;
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER (TITLE & BREADCRUMBS) ================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ti ti-settings-cog text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Pengaturan Sistem &amp; Preferensi Umum</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Konfigurasi identitas aplikasi, kebijakan absensi &amp; payroll, tema warna, integrasi WhatsApp, dan sistem login
            </p>
        </div>

        <!-- Right Side: Breadcrumbs -->
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-settings text-sm"></i>
                    <span>Pengaturan</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">General Settings</span>
            </nav>
        </div>
    </div>

    <!-- ================= 2. MAIN FORM CONTAINER ================= -->
    <form action="{{ route('generalsetting.update', Crypt::encrypt($setting->id)) }}" method="POST" enctype="multipart/form-data" id="formGeneralSettings" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

            <!-- ================= COLUMN 1: IDENTITAS & LAPORAN & GLOBAL JAM KERJA ================= -->
            <div class="space-y-4">
                
                <!-- 1.1 Informasi Perusahaan & Identitas Aplikasi -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center text-white" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-building text-xs"></i>
                            </div>
                            <h2 class="text-xs font-bold text-slate-800 tracking-tight">Informasi Perusahaan &amp; Aplikasi</h2>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <!-- Nama Aplikasi -->
                        <div class="space-y-1">
                            <label for="nama_aplikasi" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-brand-appgallery text-slate-400 text-xs"></i>
                                <span>Nama Aplikasi <span class="text-rose-500">*</span></span>
                            </label>
                            <input type="text" name="nama_aplikasi" id="nama_aplikasi" value="{{ old('nama_aplikasi', $setting->nama_aplikasi ?? '') }}" required
                                class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                        </div>

                        <!-- Nama Perusahaan -->
                        <div class="space-y-1">
                            <label for="nama_perusahaan" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-home text-slate-400 text-xs"></i>
                                <span>Nama Perusahaan / Organisasi <span class="text-rose-500">*</span></span>
                            </label>
                            <input type="text" name="nama_perusahaan" id="nama_perusahaan" value="{{ old('nama_perusahaan', $setting->nama_perusahaan ?? '') }}" required
                                class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                        </div>

                        <!-- Alamat Perusahaan -->
                        <div class="space-y-1">
                            <label for="alamat" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-map-pin text-slate-400 text-xs"></i>
                                <span>Alamat Lengkap Perusahaan <span class="text-rose-500">*</span></span>
                            </label>
                            <textarea name="alamat" id="alamat" rows="2" required
                                class="w-full p-2.5 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">{{ old('alamat', $setting->alamat ?? '') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Telepon -->
                            <div class="space-y-1">
                                <label for="telepon" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-phone text-slate-400 text-xs"></i>
                                    <span>No. Telepon / Hotline <span class="text-rose-500">*</span></span>
                                </label>
                                <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $setting->telepon ?? '') }}" required
                                    class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                            </div>

                            <!-- Nama HRD -->
                            <div class="space-y-1">
                                <label for="nama_hrd" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                    <i class="ti ti-user-check text-slate-400 text-xs"></i>
                                    <span>Nama Pimpinan / HRD</span>
                                </label>
                                <input type="text" name="nama_hrd" id="nama_hrd" value="{{ old('nama_hrd', $setting->nama_hrd ?? '') }}"
                                    class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                            </div>
                        </div>

                        <!-- Logo Perusahaan -->
                        <div class="space-y-1.5 pt-1">
                            <label class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-photo text-slate-400 text-xs"></i>
                                <span>Logo Perusahaan (Header Laporan &amp; Login)</span>
                            </label>
                            <div class="flex items-center gap-3">
                                <div class="w-16 h-16 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0 shadow-2xs p-1">
                                    @if ($setting->logo && Storage::exists('public/logo/' . $setting->logo))
                                        <img src="{{ asset('storage/logo/' . $setting->logo) }}" alt="Logo" class="max-h-full max-w-full object-contain">
                                    @else
                                        <i class="ti ti-photo-off text-2xl text-slate-300"></i>
                                    @endif
                                </div>
                                <div class="flex-1 space-y-1">
                                    <input type="file" name="logo" id="logo" accept="image/*"
                                        class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer">
                                    <p class="text-[10px] text-slate-400">Format PNG/JPG transparan direkomendasikan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1.2 Pengaturan Periode Laporan & Payroll -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center text-white" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-calendar-stats text-xs"></i>
                            </div>
                            <h2 class="text-xs font-bold text-slate-800 tracking-tight">Periode Cut-Off Laporan &amp; Gaji</h2>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label for="periode_laporan_dari" class="text-[11px] font-bold text-slate-700">Tanggal Mulai (Cut-Off)</label>
                                <input type="number" name="periode_laporan_dari" id="periode_laporan_dari" min="1" max="31" value="{{ old('periode_laporan_dari', $setting->periode_laporan_dari ?? '1') }}" required
                                    class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                            </div>
                            <div class="space-y-1">
                                <label for="periode_laporan_sampai" class="text-[11px] font-bold text-slate-700">Tanggal Akhir (Cut-Off)</label>
                                <input type="number" name="periode_laporan_sampai" id="periode_laporan_sampai" min="1" max="31" value="{{ old('periode_laporan_sampai', $setting->periode_laporan_sampai ?? '31') }}" required
                                    class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="periode_laporan_next_bulan" class="text-[11px] font-bold text-slate-700">Metode Rentang Lintas Bulan</label>
                            <select name="periode_laporan_next_bulan" id="periode_laporan_next_bulan"
                                class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                <option value="0" {{ ($setting->periode_laporan_next_bulan ?? 0) == 0 ? 'selected' : '' }}>Satu Bulan yang Sama (Contoh: 1 s/d 30 Mei)</option>
                                <option value="1" {{ ($setting->periode_laporan_next_bulan ?? 0) == 1 ? 'selected' : '' }}>Bulan Sebelumnya ke Bulan Saat Ini (Contoh: 21 April - 20 Mei)</option>
                                <option value="2" {{ ($setting->periode_laporan_next_bulan ?? 0) == 2 ? 'selected' : '' }}>Bulan Saat Ini ke Bulan Berikutnya (Contoh: 26 Mei - 25 Juni)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 1.3 Pengaturan Domain Email & Jadwal Global -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center text-white" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-mail-forward text-xs"></i>
                            </div>
                            <h2 class="text-xs font-bold text-slate-800 tracking-tight">Domain Email &amp; Jadwal Kerja Global</h2>
                        </div>
                    </div>
                    <div class="p-4 space-y-3.5">
                        <div class="space-y-1">
                            <label for="domain_email" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-at text-slate-400 text-xs"></i>
                                <span>Domain Email Karyawan <span class="text-rose-500">*</span></span>
                            </label>
                            <input type="text" name="domain_email" id="domain_email" placeholder="contoh: perusahaan.co.id" value="{{ old('domain_email', $setting->domain_email ?? '') }}" required
                                class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                        </div>

                        <!-- Toggle Jadwal Global -->
                        @if(isset($jamkerja_list))
                        <div class="pt-2 border-t border-slate-100 space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">Aktifkan Jadwal Kerja Global</span>
                                    <span class="text-[10.5px] text-slate-400">Jadwal baku jika pegawai tidak memiliki jadwal spesifik</span>
                                </div>
                                <label class="v2-switch">
                                    <input type="checkbox" name="global_jamkerja_aktif" id="global_jamkerja_aktif" value="1" @checked($setting->global_jamkerja_aktif ?? false)>
                                    <span class="v2-slider"></span>
                                </label>
                            </div>

                            <div id="global_jamkerja_container" class="space-y-2 pt-2" style="{{ ($setting->global_jamkerja_aktif ?? false) ? '' : 'display: none;' }}">
                                <div class="rounded-lg border border-slate-200 overflow-hidden">
                                    <table class="w-full text-[11px]">
                                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                                            <tr>
                                                <th class="py-1.5 px-3 text-left w-24">Hari</th>
                                                <th class="py-1.5 px-3 text-left">Pilihan Shift Jam Kerja</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @php
                                                $daftarHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                                            @endphp
                                            @foreach($daftarHari as $hari)
                                                <tr>
                                                    <td class="py-1.5 px-3 font-semibold text-slate-700">{{ $hari }}</td>
                                                    <td class="py-1 px-3">
                                                        <select name="global_jamkerja[{{ $hari }}]" class="w-full h-7 px-2 bg-white border border-slate-200 rounded text-[11px] font-medium text-slate-700">
                                                            <option value="">-- Libur Rutin --</option>
                                                            @foreach($jamkerja_list as $jk)
                                                                <option value="{{ $jk->kode_jam_kerja }}"
                                                                    @selected(isset($global_jamkerja[$hari]) && $global_jamkerja[$hari]->kode_jam_kerja == $jk->kode_jam_kerja)>
                                                                    {{ $jk->nama_jam_kerja }} ({{ substr($jk->jam_masuk,0,5) }} - {{ substr($jk->jam_pulang,0,5) }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- ================= COLUMN 2: PENGATURAN PRESENSI & INTEGRASI WA ================= -->
            <div class="space-y-4">
                
                <!-- 2.1 Konfigurasi Jam Kerja & Presensi -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center text-white" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-fingerprint text-xs"></i>
                            </div>
                            <h2 class="text-xs font-bold text-slate-800 tracking-tight">Kebijakan Presensi &amp; Absensi</h2>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Total Jam Kerja Bulan -->
                            <div class="space-y-1">
                                <label for="total_jam_bulan" class="text-[11px] font-bold text-slate-700">Total Jam Kerja / Bulan</label>
                                <input type="number" name="total_jam_bulan" id="total_jam_bulan" value="{{ old('total_jam_bulan', $setting->total_jam_bulan ?? '173') }}" required
                                    class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                            </div>

                            <!-- Sistem Hari Kerja -->
                            <div class="space-y-1">
                                <label for="sistem_hari_kerja" class="text-[11px] font-bold text-slate-700">Sistem Hari Kerja <span class="text-rose-500">*</span></label>
                                <select name="sistem_hari_kerja" id="sistem_hari_kerja" required
                                    class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                    <option value="6" @selected(($setting->sistem_hari_kerja ?? '6') == '6')>6 Hari Kerja (Senin - Sabtu)</option>
                                    <option value="5" @selected(($setting->sistem_hari_kerja ?? '6') == '5')>5 Hari Kerja (Senin - Jumat)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Face Recognition Settings -->
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-bold text-slate-800 block">Face Recognition (Biometrik Wajah)</span>
                                    <span class="text-[10.5px] text-slate-500">Verifikasi wajah saat presensi masuk &amp; pulang</span>
                                </div>
                                <label class="v2-switch">
                                    <input type="checkbox" name="face_recognition" id="face_recognition_toggle" value="1" @checked($setting->face_recognition ?? false)>
                                    <span class="v2-slider"></span>
                                </label>
                            </div>

                            <div id="face_recognition_method_container" style="{{ ($setting->face_recognition ?? false) ? '' : 'display:none;' }}" class="pt-2 border-t border-slate-200/60 space-y-1">
                                <label class="text-[11px] font-bold text-slate-700">Metode Pengenalan Wajah</label>
                                <select name="face_recognition_method" id="face_recognition_method" class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                    <option value="faceapi" {{ ($setting->face_recognition_method ?? 'faceapi') == 'faceapi' ? 'selected' : '' }}>
                                        Client-Side / On-Device (Browser &amp; Mobile ML Kit - Cepat &amp; Ringan)
                                    </option>
                                    <option value="opencv" {{ ($setting->face_recognition_method ?? 'faceapi') == 'opencv' ? 'selected' : '' }}>
                                        Server-Side (OpenCV Python - Memerlukan Python Server)
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Toggles List: Denda, Potongan, Multi Lokasi, Slip Rate -->
                        <div class="space-y-2.5 pt-1">
                            <!-- Potongan Jam Kerja -->
                            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                                <span class="text-[11.5px] font-semibold text-slate-700">Potongan Keterlambatan / Jam Kerja</span>
                                <label class="v2-switch v2-switch-sm">
                                    <input type="checkbox" name="status_potongan_jam" value="1" @checked($setting->status_potongan_jam ?? true)>
                                    <span class="v2-slider"></span>
                                </label>
                            </div>

                            <!-- Denda Keterlambatan -->
                            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                                <span class="text-[11.5px] font-semibold text-slate-700">Denda Finansial Terlambat</span>
                                <label class="v2-switch v2-switch-sm">
                                    <input type="checkbox" name="denda" value="1" @checked($setting->denda ?? false)>
                                    <span class="v2-slider"></span>
                                </label>
                            </div>

                            <!-- Tampilkan Rate Slip Gaji -->
                            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                                <span class="text-[11.5px] font-semibold text-slate-700">Tampilkan Rate / Jam di Slip Gaji</span>
                                <label class="v2-switch v2-switch-sm">
                                    <input type="checkbox" name="show_rate_slip" value="1" @checked($setting->show_rate_slip ?? true)>
                                    <span class="v2-slider"></span>
                                </label>
                            </div>

                            <!-- Multi Lokasi Presensi -->
                            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                                <span class="text-[11.5px] font-semibold text-slate-700">Multi Lokasi (Boleh Absen Lintas Cabang)</span>
                                <label class="v2-switch v2-switch-sm">
                                    <input type="checkbox" name="multi_lokasi" value="1" @checked($setting->multi_lokasi ?? false)>
                                    <span class="v2-slider"></span>
                                </label>
                            </div>

                            <!-- Absen Istirahat & Potongan Istirahat -->
                            <div class="flex items-center justify-between py-1 border-b border-slate-100">
                                <span class="text-[11.5px] font-semibold text-slate-700">Presensi Istirahat (Keluar - Masuk)</span>
                                <label class="v2-switch v2-switch-sm">
                                    <input type="checkbox" name="absen_istirahat" value="1" @checked($setting->absen_istirahat ?? false)>
                                    <span class="v2-slider"></span>
                                </label>
                            </div>

                            <div class="flex items-center justify-between py-1">
                                <span class="text-[11.5px] font-semibold text-slate-700">Potongan Keterlambatan Istirahat</span>
                                <label class="v2-switch v2-switch-sm">
                                    <input type="checkbox" name="potongan_istirahat" value="1" @checked($setting->potongan_istirahat ?? false)>
                                    <span class="v2-slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Batasan Jam & Kuota Izin -->
                        <div class="pt-3 border-t border-slate-100 space-y-3">
                            <!-- Batasi Jam Presensi -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800">Batasi Waktu Presensi Terlalu Awal</span>
                                    <label class="v2-switch v2-switch-sm">
                                        <input type="checkbox" name="batasi_absen" value="1" @checked($setting->batasi_absen ?? false)>
                                        <span class="v2-slider"></span>
                                    </label>
                                </div>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="text-[10.5px] text-slate-500 block mb-0.5">Batas Awal Masuk (Jam)</label>
                                        <input type="number" step="0.5" name="batas_jam_absen" value="{{ old('batas_jam_absen', $setting->batas_jam_absen ?? '') }}" placeholder="Contoh: 1"
                                            class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                    </div>
                                    <div>
                                        <label class="text-[10.5px] text-slate-500 block mb-0.5">Batas Awal Pulang (Jam)</label>
                                        <input type="number" step="0.5" name="batas_jam_absen_pulang" value="{{ old('batas_jam_absen_pulang', $setting->batas_jam_absen_pulang ?? '') }}" placeholder="Contoh: 1"
                                            class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                    </div>
                                </div>
                            </div>

                            <!-- Batasan Hari Izin & Sakit -->
                            <div class="grid grid-cols-2 gap-2.5 pt-2 border-t border-slate-100">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[10.5px] font-bold text-slate-700">Batasi Izin Absen</label>
                                        <label class="v2-switch v2-switch-sm">
                                            <input type="checkbox" name="batasi_hari_izin" value="1" @checked($setting->batasi_hari_izin ?? false)>
                                            <span class="v2-slider"></span>
                                        </label>
                                    </div>
                                    <input type="number" name="jml_hari_izin_max" value="{{ old('jml_hari_izin_max', $setting->jml_hari_izin_max ?? '') }}" placeholder="Maks Hari"
                                        class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                </div>
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[10.5px] font-bold text-slate-700">Batasi Izin Sakit</label>
                                        <label class="v2-switch v2-switch-sm">
                                            <input type="checkbox" name="batasi_hari_izin_sakit" value="1" @checked($setting->batasi_hari_izin_sakit ?? false)>
                                            <span class="v2-slider"></span>
                                        </label>
                                    </div>
                                    <input type="number" name="jml_hari_izin_sakit_max" value="{{ old('jml_hari_izin_sakit_max', $setting->jml_hari_izin_sakit_max ?? '') }}" placeholder="Maks Hari"
                                        class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                </div>
                            </div>

                            <!-- Batas Lintas Hari -->
                            <div class="space-y-1">
                                <label for="batas_presensi_lintashari" class="text-[11px] font-bold text-slate-700">Batas Waktu Presensi Lintas Hari (Shift Malam)</label>
                                <input type="text" name="batas_presensi_lintashari" id="batas_presensi_lintashari" placeholder="HH:MM (Contoh: 08:00)" value="{{ old('batas_presensi_lintashari', $setting->batas_presensi_lintashari ?? '') }}"
                                    class="w-full h-8 px-3 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                            </div>

                            <!-- Monitoring Live Location (SMATT3) -->
                            <div class="pt-2 border-t border-slate-100 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xs font-bold text-slate-800 block">Monitoring Lokasi (Live Location)</span>
                                        <span class="text-[10.5px] text-slate-400">Aktifkan pelacakan lokasi secara real-time</span>
                                    </div>
                                    <label class="v2-switch v2-switch-sm">
                                        <input type="checkbox" name="enable_live_location_monitoring" value="1" @checked($setting->enable_live_location_monitoring ?? true)>
                                        <span class="v2-slider"></span>
                                    </label>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10.5px] font-bold text-slate-700">Mode Monitoring Lokasi</label>
                                    <div class="flex items-center gap-4 text-xs text-slate-700">
                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="monitoring_live_location_mode" value="0" @checked(($setting->monitoring_live_location_mode ?? 0) == 0) class="text-emerald-600">
                                            <span>Semua Karyawan</span>
                                        </label>
                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                            <input type="radio" name="monitoring_live_location_mode" value="1" @checked(($setting->monitoring_live_location_mode ?? 0) == 1) class="text-emerald-600">
                                            <span>Berdasarkan Spy</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2.2 Integrasi WhatsApp Gateway & Mesin Fingerprint (DIPINDAH KE SINI) -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center text-white" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-brand-whatsapp text-xs"></i>
                            </div>
                            <h2 class="text-xs font-bold text-slate-800 tracking-tight">Integrasi WA Gateway &amp; Fingerprint</h2>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <!-- Fingerprint Cloud -->
                        <div class="grid grid-cols-2 gap-2">
                            <div class="space-y-1">
                                <label class="text-[10.5px] font-bold text-slate-700">Cloud ID Fingerprint</label>
                                <input type="text" name="cloud_id" value="{{ old('cloud_id', $setting->cloud_id ?? '') }}" placeholder="Cloud ID"
                                    class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10.5px] font-bold text-slate-700">API Key Fingerprint</label>
                                <input type="text" name="api_key" value="{{ old('api_key', $setting->api_key ?? '') }}" placeholder="API Key"
                                    class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800">
                            </div>
                        </div>

                        <!-- WhatsApp Gateway -->
                        <div class="pt-2 border-t border-slate-100 space-y-2.5">
                            <div class="grid grid-cols-2 gap-2">
                                <div class="space-y-1">
                                    <label class="text-[10.5px] font-bold text-slate-700">Provider WA Gateway</label>
                                    <select name="provider_wa" id="provider_wa" class="w-full h-8 px-2 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                        <option value="ig" @selected(($setting->provider_wa ?? 'ig') == 'ig')>Internal Gateway</option>
                                        <option value="fe" @selected(($setting->provider_wa ?? 'ig') == 'fe')>Fonnte</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10.5px] font-bold text-slate-700">Aktifkan Notifikasi</label>
                                    <div class="pt-1">
                                        <label class="v2-switch v2-switch-sm">
                                            <input type="checkbox" name="notifikasi_wa" value="1" @checked($setting->notifikasi_wa ?? false)>
                                            <span class="v2-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10.5px] font-bold text-slate-700">Tujuan Notifikasi WA</label>
                                <div class="flex items-center gap-4 text-xs text-slate-700">
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="tujuan_notifikasi_wa" id="tujuan_karyawan" value="0" @checked(($setting->tujuan_notifikasi_wa ?? 0) == 0) class="text-emerald-600">
                                        <span>Ke Karyawan</span>
                                    </label>
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="tujuan_notifikasi_wa" id="tujuan_grup" value="1" @checked(($setting->tujuan_notifikasi_wa ?? 0) == 1) class="text-emerald-600">
                                        <span>Ke Group WA</span>
                                    </label>
                                </div>
                            </div>

                            <div id="group_wa_input" style="display: none;" class="space-y-1">
                                <label class="text-[10.5px] font-bold text-slate-700">ID Group WhatsApp</label>
                                <input type="text" name="id_group_wa" value="{{ old('id_group_wa', $setting->id_group_wa ?? '') }}" placeholder="Contoh: 1203630234567890@g.us"
                                    class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10.5px] font-bold text-slate-700">Domain / URL WA Gateway</label>
                                <input type="text" name="domain_wa_gateway" value="{{ old('domain_wa_gateway', $setting->domain_wa_gateway ?? '') }}" placeholder="https://wa.domain.com"
                                    class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10.5px] font-bold text-slate-700">API Key / Token WA Gateway</label>
                                <input type="text" name="wa_api_key" value="{{ old('wa_api_key', $setting->wa_api_key ?? '') }}" placeholder="API Token"
                                    class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-mono text-slate-800">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ================= COLUMN 3: SISTEM, MENU & PERMISSION ================= -->
            <div class="space-y-4">
                
                <!-- 3.1 Pengaturan Sistem, Sesi & Login Admin -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center text-white" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-server text-xs"></i>
                            </div>
                            <h2 class="text-xs font-bold text-slate-800 tracking-tight">Sistem &amp; Keamanan Login Admin</h2>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <!-- Timezone -->
                        <div class="space-y-1">
                            <label for="timezone" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-world text-slate-400 text-xs"></i>
                                <span>Zona Waktu Default (Timezone) <span class="text-rose-500">*</span></span>
                            </label>
                            <select name="timezone" id="timezone" required
                                class="w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                <option value="Asia/Jakarta" @selected(($setting->timezone ?? 'Asia/Jakarta') == 'Asia/Jakarta')>Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar" @selected(($setting->timezone ?? 'Asia/Jakarta') == 'Asia/Makassar')>Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura" @selected(($setting->timezone ?? 'Asia/Jakarta') == 'Asia/Jayapura')>Asia/Jayapura (WIT)</option>
                                <option value="Asia/Singapore" @selected(($setting->timezone ?? 'Asia/Jakarta') == 'Asia/Singapore')>Asia/Singapore</option>
                                <option value="Asia/Kuala_Lumpur" @selected(($setting->timezone ?? 'Asia/Jakarta') == 'Asia/Kuala_Lumpur')>Asia/Kuala_Lumpur</option>
                                <option value="Asia/Bangkok" @selected(($setting->timezone ?? 'Asia/Jakarta') == 'Asia/Bangkok')>Asia/Bangkok</option>
                                <option value="UTC" @selected(($setting->timezone ?? 'Asia/Jakarta') == 'UTC')>UTC</option>
                            </select>
                        </div>

                        <!-- Session Duration & Expired -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label for="session_time" class="text-[11px] font-bold text-slate-700">Durasi Sesi Login</label>
                                <div class="relative">
                                    <input type="number" name="session_time" id="session_time" value="{{ old('session_time', $setting->session_time ?? '120') }}"
                                        class="w-full h-9 pl-3 pr-10 bg-white border border-slate-300/90 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition shadow-2xs">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10.5px] text-slate-400 font-bold">Hari</span>
                                </div>
                            </div>
                            @if(auth()->user()->hasRole('master admin'))
                                <div class="space-y-1">
                                    <label for="expired" class="text-[11px] font-bold text-slate-700">Expired Lisensi</label>
                                    <input type="text" name="expired" id="expired" value="{{ old('expired', $setting->expired ?? '') }}"
                                        class="flatpickr-date w-full h-9 px-3 bg-white border border-slate-300/90 rounded-lg text-xs font-bold text-slate-800">
                                </div>
                            @endif
                        </div>

                        <!-- Tema Warna Web & Mobile -->
                        <div class="pt-2 border-t border-slate-100 space-y-2.5">
                            <label class="text-[11px] font-bold text-slate-700 block">Tema &amp; Kustomisasi Tampilan</label>

                            <!-- Pilihan Tema Dashboard Admin (V2 vs Klasik) -->
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-2">
                                <label for="admin_theme" class="text-[11px] font-bold text-slate-800 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5">
                                        <i class="ti ti-layout-dashboard text-emerald-600"></i>
                                        <span>Tema Tampilan Dashboard Admin</span>
                                    </span>
                                    <span class="text-[9.5px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold uppercase tracking-wider">Super Admin</span>
                                </label>
                                <select name="admin_theme" id="admin_theme" class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                                    <option value="v2" @selected(($setting->admin_theme ?? 'v2') == 'v2')>✨ Dashboard Modern V2 (Rekomendasi - Responsive & Clean)</option>
                                    <option value="classic" @selected(($setting->admin_theme ?? 'v2') == 'classic')>🏛️ Dashboard Klasik / Legacy (Tabler Bootstrap)</option>
                                </select>
                                <p class="text-[10px] text-slate-500 leading-tight">
                                    Menentukan tampilan layout panel administrator yang digunakan oleh seluruh admin.
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 block">Warna Utama</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="theme_color_1" id="theme_color_1" value="{{ $setting->theme_color_1 ?? '#0a5c41' }}"
                                            class="w-9 h-8 p-0.5 rounded-lg border border-slate-300 bg-white cursor-pointer">
                                        <span class="text-[11px] font-mono text-slate-700">{{ $setting->theme_color_1 ?? '#0a5c41' }}</span>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 block">Warna Sekunder</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="theme_color_2" id="theme_color_2" value="{{ $setting->theme_color_2 ?? '#117956' }}"
                                            class="w-9 h-8 p-0.5 rounded-lg border border-slate-300 bg-white cursor-pointer">
                                        <span class="text-[11px] font-mono text-slate-700">{{ $setting->theme_color_2 ?? '#117956' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label for="mobile_theme_scheme" class="text-[10px] text-slate-500 block">Tema Aplikasi Mobile</label>
                                <select name="mobile_theme_scheme" id="mobile_theme_scheme" class="w-full h-8 px-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800">
                                    <option value="green" @selected(($setting->mobile_theme_scheme ?? 'green') == 'green')>Green (Emerald Nature)</option>
                                    <option value="blue" @selected(($setting->mobile_theme_scheme ?? 'green') == 'blue')>Blue (Ocean Corporate)</option>
                                    <option value="red" @selected(($setting->mobile_theme_scheme ?? 'green') == 'red')>Red (Passion)</option>
                                    <option value="orange" @selected(($setting->mobile_theme_scheme ?? 'green') == 'orange')>Orange (Sunset)</option>
                                    <option value="purple" @selected(($setting->mobile_theme_scheme ?? 'green') == 'purple')>Purple (Royal)</option>
                                    <option value="rose" @selected(($setting->mobile_theme_scheme ?? 'green') == 'rose')>Rose (Blossom)</option>
                                    <option value="dark" @selected(($setting->mobile_theme_scheme ?? 'green') == 'dark')>Dark Night</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pengaturan Keamanan Login URL Admin -->
                        <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-xs font-bold text-amber-950 block">Proteksi Login Admin Khusus</span>
                                    <span class="text-[10px] text-amber-800">Kunci login admin dari halaman root (/)</span>
                                </div>
                                <label class="v2-switch v2-switch-sm v2-switch-amber">
                                    <input type="checkbox" name="block_admin_login" id="block_admin_login" value="1" @checked($setting->block_admin_login ?? false)>
                                    <span class="v2-slider"></span>
                                </label>
                            </div>

                            <div id="admin_login_url_container" style="{{ ($setting->block_admin_login ?? false) ? '' : 'display: none;' }}" class="space-y-1 pt-1.5 border-t border-amber-200">
                                <label for="admin_login_url" class="text-[10.5px] font-bold text-amber-900">Custom Path URL Admin</label>
                                <div class="flex items-center">
                                    <span class="h-8 px-2.5 bg-amber-100/90 border border-r-0 border-amber-300 rounded-l-lg text-[11px] font-mono text-amber-900 flex items-center">
                                        {{ url('/') }}/
                                    </span>
                                    <input type="text" name="admin_login_url" id="admin_login_url" value="{{ old('admin_login_url', $setting->admin_login_url ?? 'panel') }}"
                                        class="h-8 px-2.5 flex-1 bg-white border border-amber-300 rounded-r-lg text-xs font-bold font-mono text-amber-900 focus:outline-none focus:ring-1 focus:ring-amber-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3.2 Menu Karyawan Mobile / Portal -->
                @if(isset($karyawan_menus))
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center text-white" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-layout-grid text-xs"></i>
                            </div>
                            <h2 class="text-xs font-bold text-slate-800 tracking-tight">Menu Karyawan Aktif</h2>
                        </div>
                        <span class="text-[10px] text-slate-400">Portal / Mobile</span>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
                            @foreach ($karyawan_menus as $menu)
                                <label class="flex items-center justify-between p-2 rounded-lg bg-slate-50 hover:bg-slate-100/80 border border-slate-100 transition cursor-pointer">
                                    <span class="text-[11.5px] font-semibold text-slate-700 truncate pr-2">{{ $menu->nama_menu }}</span>
                                    <label class="v2-switch v2-switch-sm pointer-events-none">
                                        <input type="checkbox" name="karyawan_menu[{{ $menu->kode_menu }}]" value="1" @checked($menu->status == 1)>
                                        <span class="v2-slider"></span>
                                    </label>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- 3.3 PWA Settings & Icon Generator -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-md flex items-center justify-center text-white" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-device-mobile text-xs"></i>
                            </div>
                            <h2 class="text-xs font-bold text-slate-800 tracking-tight">PWA &amp; Icon Mobile App</h2>
                        </div>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold border border-emerald-200/60">PWA</span>
                    </div>
                    <div class="p-4 space-y-3">
                        <!-- File Upload -->
                        <div class="space-y-1">
                            <label for="pwa_icon" class="text-[11px] font-bold text-slate-700 flex items-center gap-1">
                                <i class="ti ti-photo-up text-slate-400 text-xs"></i>
                                <span>Upload Icon Master (1080x1080px)</span>
                            </label>
                            <input type="file" name="pwa_icon" id="pwa_icon" accept="image/*"
                                class="w-full text-xs text-slate-600 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer border border-slate-300 rounded-lg cursor-pointer bg-white">
                            <p class="text-[10px] text-slate-500 leading-tight">
                                Upload icon beresolusi tinggi persegi (minimal 1080x1080px). Sistem otomatis men-generate seluruh varian icon PWA &amp; manifest.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <button type="button" id="btnGenerateIcons"
                                class="w-full h-8 px-2.5 rounded-lg text-white font-bold text-xs shadow-2xs transition active:scale-95 cursor-pointer flex items-center justify-center gap-1.5"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-wand text-sm"></i>
                                <span>Generate Icon</span>
                            </button>
                            <button type="button" id="btnPreviewIcons"
                                class="w-full h-8 px-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-2xs transition active:scale-95 cursor-pointer flex items-center justify-center gap-1.5">
                                <i class="ti ti-eye text-sm"></i>
                                <span>Preview Icon</span>
                            </button>
                        </div>

                        <!-- Progress Bar -->
                        <div id="progressContainer" style="display: none;" class="space-y-1.5 pt-1">
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200">
                                <div id="pwaProgressBar" class="h-2 rounded-full transition-all duration-300" style="width: 0%; background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});"></div>
                            </div>
                            <p class="text-[10px] text-slate-500 text-center font-medium" id="progressText">Memulai proses...</p>
                        </div>

                        <!-- Generated Icons Preview -->
                        <div id="iconsPreview" style="display: none;" class="pt-2 border-t border-slate-100 space-y-2">
                            <h6 class="text-[11px] font-bold text-slate-700">Hasil Generate Baru:</h6>
                            <div class="grid grid-cols-4 gap-2" id="iconsGrid"></div>
                        </div>

                        <!-- Current PWA Icons -->
                        <div class="pt-2 border-t border-slate-100 space-y-2">
                            <div class="flex items-center justify-between">
                                <h6 class="text-[11px] font-bold text-slate-700">Icon PWA Terpasang:</h6>
                                <span class="text-[10px] text-slate-400 font-mono">public/assets/img/icons/pwa</span>
                            </div>
                            <div class="grid grid-cols-4 gap-2" id="currentIconsGrid">
                                @php
                                    $iconDir = public_path('assets/img/icons/pwa');
                                    $currentIcons = [];
                                    if (file_exists($iconDir)) {
                                        $files = glob($iconDir . '/icon-*.png');
                                        foreach ($files as $file) {
                                            $filename = basename($file);
                                            $size = str_replace(['icon-', '.png'], '', $filename);
                                            $currentIcons[] = [
                                                'size' => $size,
                                                'path' => 'assets/img/icons/pwa/' . $filename,
                                            ];
                                        }
                                    }
                                @endphp

                                @forelse ($currentIcons as $icon)
                                    <div class="text-center p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                        <img src="{{ asset($icon['path']) }}?v={{ time() }}" alt="{{ $icon['size'] }}"
                                            class="w-7 h-7 mx-auto rounded shadow-2xs object-contain">
                                        <span class="block text-[9px] font-mono text-slate-500 mt-1 truncate">{{ $icon['size'] }}</span>
                                    </div>
                                @empty
                                    <div class="col-span-4 text-center py-2 text-[10.5px] text-slate-400 italic">
                                        Belum ada icon PWA yang di-generate.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3.4 Storage Permissions Fixer Card -->
                <div class="bg-amber-50/70 rounded-xl border border-amber-200/80 p-3.5 shadow-2xs space-y-2">
                    <div class="flex items-center gap-2 text-amber-900 font-bold text-xs">
                        <i class="ti ti-folder-lock text-sm"></i>
                        <span>Perbaikan Permission Folder Storage</span>
                    </div>
                    <p class="text-[10.5px] text-amber-800 leading-relaxed">
                        Jika file upload foto/nota tidak muncul atau error 403, jalankan reset permission folder storage menjadi 775.
                    </p>
                    <button type="button" id="btnFixPermissions" class="w-full h-8 px-3 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-2xs transition active:scale-95 cursor-pointer flex items-center justify-center gap-1.5">
                        <i class="ti ti-tool"></i>
                        <span>Perbaiki Permission Storage</span>
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" id="btnSimpanSettings"
                            class="w-full h-11 text-white font-bold rounded-xl text-xs shadow-sm transition-all duration-200 active:scale-[0.98] cursor-pointer hover:opacity-95 flex items-center justify-center gap-2"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-device-floppy text-base"></i>
                        <span>Simpan Perubahan Pengaturan</span>
                    </button>
                </div>

            </div>

        </div>
    </form>

</div>

@endsection

@push('myscript')
<script>
    $(function() {
        // Initialize Flatpickr Datepicker
        if (typeof flatpickr !== 'undefined') {
            $('.flatpickr-date').flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true,
                locale: "id"
            });
        }

        // Toggle Group WA Input
        function toggleGroupInput() {
            const tujuanGrup = $('#tujuan_grup').is(':checked');
            if (tujuanGrup) {
                $('#group_wa_input').slideDown(150);
            } else {
                $('#group_wa_input').slideUp(150);
            }
        }
        toggleGroupInput();
        $('input[name="tujuan_notifikasi_wa"]').on('change', toggleGroupInput);

        // Toggle Global Jadwal Kerja Container
        function toggleGlobalJamkerja() {
            if ($('#global_jamkerja_aktif').is(':checked')) {
                $('#global_jamkerja_container').slideDown(150);
            } else {
                $('#global_jamkerja_container').slideUp(150);
            }
        }
        $('#global_jamkerja_aktif').on('change', toggleGlobalJamkerja);

        // Toggle Admin Login URL input
        function toggleAdminLoginUrl() {
            if ($('#block_admin_login').is(':checked')) {
                $('#admin_login_url_container').slideDown(150);
            } else {
                $('#admin_login_url_container').slideUp(150);
            }
        }
        $('#block_admin_login').on('change', toggleAdminLoginUrl);

        // Sanitize Admin Login URL input
        $('#admin_login_url').on('input', function() {
            let val = $(this).val().replace(/[^a-zA-Z0-9-_]/g, '');
            $(this).val(val);
        });

        // Toggle Face Recognition Method
        $('#face_recognition_toggle').on('change', function() {
            if ($(this).is(':checked')) {
                $('#face_recognition_method_container').slideDown(150);
            } else {
                $('#face_recognition_method_container').slideUp(150);
            }
        });

        // Fix Permissions AJAX Trigger
        $('#btnFixPermissions').on('click', function() {
            Swal.fire({
                title: 'Perbaiki Permission Storage?',
                text: "Sistem akan mengatur ulang hak akses folder storage menjadi 775 secara rekursif.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Jalankan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memperbarui hak akses folder storage.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ \Illuminate\Support\Facades\Route::has("generalsetting.fix-permissions") ? route("generalsetting.fix-permissions") : "#" }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menghubungi server. Pastikan hak akses CLI web server mengizinkan chmod.'
                            });
                        }
                    });
                }
            });
        });

        // PWA Icon Generator & Preview
        $('#btnGenerateIcons').click(function() {
            const fileInput = document.getElementById('pwa_icon');
            const file = fileInput ? fileInput.files[0] : null;

            if (!file) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih file icon terlebih dahulu!'
                });
                return;
            }

            // Validate file size (max 10MB)
            if (file.size > 10 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ukuran file terlalu besar! Maksimal 10MB.'
                });
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'File harus berupa gambar!'
                });
                return;
            }

            generatePwaIcons(file);
        });

        $('#btnPreviewIcons').click(function() {
            previewCurrentPwaIcons();
        });

        function updatePwaProgress(percent, text) {
            $('#pwaProgressBar').css('width', percent + '%');
            $('#progressText').text(text);
        }

        function generatePwaIcons(file) {
            const formData = new FormData();
            formData.append('icon', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}');

            $('#progressContainer').slideDown(150);
            $('#btnGenerateIcons').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            updatePwaProgress(10, 'Memulai proses upload...');

            $.ajax({
                url: '{{ route('pwa.generate-icons') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            const percentComplete = Math.round((evt.loaded / evt.total) * 70);
                            updatePwaProgress(percentComplete, 'Mengunggah & memproses ukuran...');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    updatePwaProgress(100, 'Berhasil selesai!');

                    setTimeout(() => {
                        $('#progressContainer').slideUp(150);
                        $('#btnGenerateIcons').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: `Berhasil generate ${response.count} icon PWA!`
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    }, 800);
                },
                error: function(xhr) {
                    $('#progressContainer').slideUp(150);
                    $('#btnGenerateIcons').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');

                    let errorMessage = 'Terjadi kesalahan saat generate icons!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        }

        function previewCurrentPwaIcons() {
            $.ajax({
                url: '{{ route('pwa.preview-icons') }}',
                type: 'GET',
                success: function(response) {
                    if (response && response.length > 0) {
                        let html = '';
                        response.forEach(function(icon) {
                            html += `
                                <div class="text-center p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                    <img src="${icon.url}?t=${new Date().getTime()}"
                                         alt="${icon.size}"
                                         class="w-8 h-8 mx-auto rounded shadow-2xs object-contain">
                                    <span class="block text-[9px] font-mono text-slate-500 mt-1 truncate">${icon.size}</span>
                                </div>
                            `;
                        });

                        $('#iconsGrid').html(html);
                        $('#iconsPreview').slideDown(150);

                        Swal.fire({
                            icon: 'info',
                            title: 'Preview Icons',
                            text: `Ditemukan ${response.length} icon PWA yang sudah di-generate.`
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'Info',
                            text: 'Belum ada icon PWA yang di-generate.'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Gagal memuat preview icons!'
                    });
                }
            });
        }
    });
</script>
@endpush
