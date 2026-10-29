@extends('v2.layouts.app')

@section('title', 'Detail Karyawan - ' . $karyawan->nama_karyawan)

@section('content')
<div class="space-y-6" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <!-- Breadcrumb & Back Navigation -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('karyawan.index') }}" 
               class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-sm">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('karyawan.index') }}" class="hover:text-emerald-600 transition-colors">Data Karyawan</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Detail Profil</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight mt-0.5">Profil Karyawan</h1>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('karyawan.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 shadow-sm transition-all flex items-center gap-1.5">
                <i class="ti ti-list text-base"></i>
                <span>Daftar Karyawan</span>
            </a>
        </div>
    </div>

    <!-- Full Background Theme Hero Card with Elegant Luxury Ornaments -->
    <div class="rounded-2xl border border-white/15 shadow-xl overflow-hidden relative" 
         style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41) 0%, var(--theme-color-2, #117956) 100%);">
        
        <!-- Luxury Abstract Geometric SVG Ornaments & Wave Patterns -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden select-none opacity-15">
            <!-- Concentric Circular Waves Right -->
            <svg class="absolute -right-20 -top-24 w-96 h-96 text-white" viewBox="0 0 400 400" fill="none" stroke="currentColor" stroke-width="1.2">
                <circle cx="200" cy="200" r="60" opacity="0.3" />
                <circle cx="200" cy="200" r="110" opacity="0.5" />
                <circle cx="200" cy="200" r="160" opacity="0.4" />
                <circle cx="200" cy="200" r="210" opacity="0.3" />
            </svg>
        </div>

        <!-- Ambient Glow Spheres -->
        <div class="absolute -right-10 -top-10 w-72 h-72 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -bottom-10 w-64 h-64 bg-black/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="p-6 sm:p-8 relative z-10 space-y-6">
            <!-- Top Row: Avatar, Identity, and Status Pills -->
            <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-5 text-center sm:text-left">
                <!-- Avatar & Identity -->
                <div class="flex flex-col sm:flex-row items-center sm:items-center gap-5">
                    <div class="relative shrink-0">
                        @if (Storage::disk('public')->exists('/karyawan/' . $karyawan->foto) && !empty($karyawan->foto))
                            <img src="{{ getfotoKaryawan($karyawan->foto) }}" 
                                 alt="{{ $karyawan->nama_karyawan }}" 
                                 class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-2 border-white/40 shadow-xl bg-white/10 backdrop-blur-md">
                        @else
                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl border-2 border-white/40 shadow-xl flex items-center justify-center text-3xl font-bold text-white uppercase bg-white/15 backdrop-blur-md">
                                {{ substr($karyawan->nama_karyawan ?? 'K', 0, 2) }}
                            </div>
                        @endif
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white {{ $karyawan->status_aktif_karyawan === '1' ? 'bg-emerald-400' : 'bg-rose-400' }}"></span>
                    </div>

                    <div class="space-y-1.5 text-white">
                        <div class="flex items-center justify-center sm:justify-start gap-2.5 flex-wrap">
                            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white drop-shadow-xs">{{ textCamelCase($karyawan->nama_karyawan) }}</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-white/20 text-white border border-white/30 backdrop-blur-md">
                                {{ $karyawan->nik_show ?? $karyawan->nik }}
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm font-medium text-white/85 flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                            <span class="flex items-center gap-1"><i class="ti ti-user-circle text-white/70 text-base"></i> {{ textCamelCase($karyawan->nama_jabatan ?? 'Staf') }}</span>
                            <span class="text-white/40">•</span>
                            <span class="flex items-center gap-1"><i class="ti ti-briefcase text-white/70 text-base"></i> {{ textUpperCase($karyawan->nama_dept ?? '-') }}</span>
                            <span class="text-white/40">•</span>
                            <span class="flex items-center gap-1"><i class="ti ti-building text-white/70 text-base"></i> {{ textUpperCase($karyawan->nama_cabang ?? '-') }}</span>
                        </p>
                    </div>
                </div>

                <!-- Status Badges -->
                <div class="flex items-center gap-2 flex-wrap justify-center shrink-0">
                    @if ($karyawan->status_aktif_karyawan === '1')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white/15 text-white border border-white/25 backdrop-blur-md shadow-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Karyawan Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-500/20 text-rose-100 border border-rose-300/30 backdrop-blur-md">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                            Nonaktif
                        </span>
                    @endif

                    @if (($karyawan->status_akses_web ?? '1') !== '0')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white/15 text-white border border-white/25 backdrop-blur-md shadow-xs">
                            <i class="ti ti-world text-sm"></i>
                            Akses Web & Mobile
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500/20 text-amber-100 border border-amber-300/30 backdrop-blur-md">
                            <i class="ti ti-device-mobile text-sm"></i>
                            Hanya Mobile
                        </span>
                    @endif
                </div>
            </div>

            <!-- Bottom Row: Sleek Metadata Bar with Tapered Vertical Dividers -->
            <div class="pt-5 border-t border-white/15 grid grid-cols-2 lg:grid-cols-4 gap-y-4 lg:gap-y-0">
                <!-- Stat 1 -->
                <div class="px-2 sm:px-4 text-white relative">
                    <span class="text-white/70 block text-[11px] font-medium flex items-center gap-1.5">
                        <i class="ti ti-calendar text-white/60 text-sm"></i> Tanggal Masuk
                    </span>
                    <span class="font-bold text-sm text-white block mt-1 tracking-tight">
                        {{ !empty($karyawan->tanggal_masuk) ? DateToIndo($karyawan->tanggal_masuk) : '-' }}
                    </span>
                    <!-- Tapered Sharp Divider -->
                    <div class="hidden lg:block absolute right-0 top-1/2 -translate-y-1/2 w-[1px] h-8 bg-gradient-to-b from-transparent via-white/35 to-transparent"></div>
                </div>

                <!-- Stat 2 -->
                <div class="px-2 sm:px-4 text-white relative">
                    <span class="text-white/70 block text-[11px] font-medium flex items-center gap-1.5">
                        <i class="ti ti-heart-handshake text-white/60 text-sm"></i> Status Kawin
                    </span>
                    <span class="font-bold text-sm text-white block mt-1 tracking-tight truncate" title="{{ $karyawan->status_kawin }}">
                        {{ $karyawan->status_kawin ?? '-' }}
                    </span>
                    <!-- Tapered Sharp Divider -->
                    <div class="hidden lg:block absolute right-0 top-1/2 -translate-y-1/2 w-[1px] h-8 bg-gradient-to-b from-transparent via-white/35 to-transparent"></div>
                </div>

                <!-- Stat 3 -->
                <div class="px-2 sm:px-4 text-white relative">
                    <span class="text-white/70 block text-[11px] font-medium flex items-center gap-1.5">
                        <i class="ti ti-school text-white/60 text-sm"></i> Pendidikan
                    </span>
                    <span class="font-bold text-sm text-white block mt-1 tracking-tight truncate" title="{{ $karyawan->pendidikan_terakhir }} {{ !empty($karyawan->jurusan) ? '('.$karyawan->jurusan.')' : '' }}">
                        {{ $karyawan->pendidikan_terakhir ?? '-' }} {{ !empty($karyawan->jurusan) ? '('.$karyawan->jurusan.')' : '' }}
                    </span>
                    <!-- Tapered Sharp Divider -->
                    <div class="hidden lg:block absolute right-0 top-1/2 -translate-y-1/2 w-[1px] h-8 bg-gradient-to-b from-transparent via-white/35 to-transparent"></div>
                </div>

                <!-- Stat 4 -->
                <div class="px-2 sm:px-4 text-white">
                    <span class="text-white/70 block text-[11px] font-medium flex items-center gap-1.5">
                        <i class="ti ti-phone text-white/60 text-sm"></i> No. Handphone
                    </span>
                    <span class="font-bold text-sm text-white block mt-1 tracking-tight">
                        {{ $karyawan->no_hp ?? '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content 2-Column Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Personal Data & User Account (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Card 1: Data Pribadi -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-base font-bold">
                        <i class="ti ti-id"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Data Pribadi</h3>
                        <p class="text-[11px] text-slate-500">Identitas & kontak karyawan</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-start gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-id text-slate-400 text-sm"></i> No. KTP
                        </span>
                        <span class="font-mono font-bold text-slate-800 text-right">{{ $karyawan->no_ktp ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between items-start gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-map-pin text-slate-400 text-sm"></i> Tempat, Tgl Lahir
                        </span>
                        <span class="font-semibold text-slate-800 text-right">
                            {{ textCamelCase($karyawan->tempat_lahir ?? '-') }}, {{ !empty($karyawan->tanggal_lahir) ? DateToIndo($karyawan->tanggal_lahir) : '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-start gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-gender-intergender text-slate-400 text-sm"></i> Jenis Kelamin
                        </span>
                        <span class="font-semibold text-slate-800">{{ $karyawan->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan' }}</span>
                    </div>

                    <div class="flex justify-between items-start gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-mail text-slate-400 text-sm"></i> Email
                        </span>
                        <span class="font-semibold text-slate-800 text-right truncate max-w-[180px]" title="{{ $karyawan->email }}">{{ $karyawan->email ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between items-start gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-phone-call text-slate-400 text-sm"></i> Kontak Darurat
                        </span>
                        <span class="font-semibold text-slate-800 text-right">
                            {{ $karyawan->kontak_darurat ?? '-' }}
                            @if(!empty($karyawan->hubungan_kontak_darurat))
                                <span class="text-[10.5px] text-slate-500 block">({{ textCamelCase($karyawan->hubungan_kontak_darurat) }})</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between items-start gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-building-bank text-slate-400 text-sm"></i> Rekening Bank
                        </span>
                        <span class="font-semibold text-slate-800 text-right">
                            {{ $karyawan->no_rekening ?? '-' }}
                            @if(!empty($karyawan->nama_bank))
                                <span class="text-[10.5px] text-emerald-700 font-bold block">{{ strtoupper($karyawan->nama_bank) }}</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between items-start gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-user-check text-slate-400 text-sm"></i> Nama di Rekening
                        </span>
                        <span class="font-semibold text-slate-800 text-right">{{ $karyawan->nama_rekening ?? '-' }}</span>
                    </div>

                    <div class="flex justify-between items-start gap-2 py-1.5 border-b border-slate-50">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <i class="ti ti-receipt-tax text-slate-400 text-sm"></i> Hitung PPh 21
                        </span>
                        <span>
                            @if (($karyawan->hitung_pph21 ?? 1) == 1)
                                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Ya</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Tidak</span>
                            @endif
                        </span>
                    </div>

                    <div class="pt-1.5">
                        <span class="text-slate-500 font-medium flex items-center gap-1.5 mb-1.5">
                            <i class="ti ti-map-2 text-slate-400 text-sm"></i> Alamat Domisili
                        </span>
                        <p class="font-medium text-slate-700 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">{{ textCamelCase($karyawan->alamat ?? '-') }}</p>
                    </div>
                </div>
            </div>

            <!-- Card 2: Akun Pengguna -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center text-base font-bold">
                        <i class="ti ti-user-shield"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Akun Pengguna Sistem</h3>
                        <p class="text-[11px] text-slate-500">Kredensial login aplikasi</p>
                    </div>
                </div>

                @if ($user)
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                            <span class="text-slate-500 font-medium">Username</span>
                            <span class="font-mono font-bold text-slate-800">{{ $user->username }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                            <span class="text-slate-500 font-medium">Email Akun</span>
                            <span class="font-semibold text-slate-800">{{ $user->email }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <span class="text-slate-500 font-medium">Akses Login</span>
                            <span>
                                @if (($karyawan->status_akses_web ?? '1') !== '0')
                                    <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Web & Mobile</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Hanya Mobile</span>
                                @endif
                            </span>
                        </div>
                    </div>
                @else
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2.5">
                        <i class="ti ti-alert-circle text-lg shrink-0"></i>
                        <span>Akun pengguna belum dibuat untuk karyawan ini.</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Interactive Tabbed Modules (8 cols) -->
        <div class="lg:col-span-8 space-y-4">
            
            <!-- Tab Switcher Navigation Pills -->
            <div class="bg-white p-1.5 rounded-2xl border border-slate-200/90 shadow-sm flex flex-wrap gap-1">
                <button type="button" 
                        onclick="showTabV2('face')" 
                        id="tab-btn-face"
                        class="tab-nav-v2 active flex-1 min-w-[120px] py-2.5 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all">
                    <i class="ti ti-face-id text-base"></i>
                    <span>Data Wajah</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-800">{{ $karyawan_wajah->count() }}</span>
                </button>

                <button type="button" 
                        onclick="showTabV2('mutation')" 
                        id="tab-btn-mutation"
                        class="tab-nav-v2 flex-1 min-w-[120px] py-2.5 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all">
                    <i class="ti ti-arrows-transfer-down text-base"></i>
                    <span>Riwayat Mutasi</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-700">{{ $mutasi->count() }}</span>
                </button>

                <button type="button" 
                        onclick="showTabV2('training')" 
                        id="tab-btn-training"
                        class="tab-nav-v2 flex-1 min-w-[120px] py-2.5 px-3 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all">
                    <i class="ti ti-school text-base"></i>
                    <span>Pelatihan</span>
                </button>
            </div>

            <!-- Tab 1: Face Recognition -->
            <div id="tab-pane-face" class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-5">
                <div class="flex items-center justify-between flex-wrap gap-3 pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Dataset Pengenalan Wajah (Face ID)</h3>
                        <p class="text-xs text-slate-500">Model foto yang digunakan untuk verifikasi biometrik absensi</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                id="btnAddface" 
                                class="px-3.5 py-2 text-xs font-bold text-white rounded-xl shadow-sm transition-transform active:scale-95 flex items-center gap-1.5"
                                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
                            <i class="ti ti-plus text-sm"></i>
                            <span>Tambah Wajah</span>
                        </button>
                        @if ($karyawan_wajah->count() > 0)
                            <form id="formHapusSemuaWajah" method="POST" action="{{ route('facerecognition.destroyAll', Crypt::encrypt($karyawan->nik)) }}" class="inline">
                                @csrf
                                <button type="button" 
                                        id="btnHapusSemuaWajah"
                                        class="px-3 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition-colors flex items-center gap-1.5">
                                    <i class="ti ti-trash text-sm"></i>
                                    <span>Hapus Semua</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if ($karyawan_wajah->isEmpty())
                    <div class="text-center py-12 px-4 border border-dashed border-slate-200 rounded-2xl bg-slate-50">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center text-2xl mb-3">
                            <i class="ti ti-face-id"></i>
                        </div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1">Belum Ada Dataset Wajah</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">Tambahkan foto wajah karyawan agar dapat melakukan presensi dengan pengenalan wajah.</p>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach ($karyawan_wajah as $d)
                            @php
                                $folder = $karyawan->nik . '-' . getNamaDepan(strtolower($karyawan->nama_karyawan));
                                $url = url('/storage/uploads/facerecognition/' . $folder . '/' . $d->wajah);
                                $timestamp = time();
                                $urlWithTimestamp = $url . '?v=' . $timestamp;
                            @endphp
                            <div class="group relative rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-slate-100 aspect-square">
                                <img src="{{ $urlWithTimestamp }}" 
                                     class="w-full h-full object-cover cursor-pointer group-hover:scale-105 transition-transform duration-300" 
                                     alt="Dataset Wajah"
                                     data-bs-toggle="modal"
                                     data-bs-target="#modalFotoWajah" 
                                     data-image="{{ $urlWithTimestamp }}">
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-between p-2.5">
                                    <span class="text-[10px] text-white/90 font-mono">#{{ $loop->iteration }}</span>
                                    <form method="POST" class="deleteform inline" action="{{ route('facerecognition.delete', Crypt::encrypt($d->id)) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center hover:bg-rose-700 shadow-md transition-colors" title="Hapus Wajah">
                                            <i class="ti ti-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Tab 2: Mutation History -->
            <div id="tab-pane-mutation" class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 space-y-4" style="display: none;">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Riwayat Mutasi, Promosi & Demosi</h3>
                        <p class="text-xs text-slate-500">Catatan perpindahan cabang, divisi, atau kenaikan jabatan</p>
                    </div>
                </div>

                @if($mutasi->isEmpty())
                    <div class="text-center py-10 px-4 border border-dashed border-slate-200 rounded-2xl bg-slate-50">
                        <i class="ti ti-arrows-transfer-down text-3xl text-slate-400 mb-2 block"></i>
                        <p class="text-xs font-semibold text-slate-600">Belum ada riwayat mutasi atau perubahan status kerja.</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($mutasi as $d)
                            <div class="p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition-all bg-slate-50/50 space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                            <i class="ti ti-calendar text-slate-400"></i>
                                            {{ date('d-m-Y', strtotime($d->tanggal_mutasi)) }}
                                        </span>
                                        @if ($d->jenis_mutasi == 'MUTASI')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">Mutasi</span>
                                        @elseif ($d->jenis_mutasi == 'PROMOSI')
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Promosi</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Demosi</span>
                                        @endif
                                    </div>
                                    @if ($d->doc_sk)
                                        <a href="{{ asset('storage/uploads/mutasi/' . $d->doc_sk) }}" target="_blank" class="text-xs font-bold text-emerald-700 hover:underline flex items-center gap-1">
                                            <i class="ti ti-file-text"></i>
                                            <span>Lihat Dokumen SK</span>
                                        </a>
                                    @endif
                                </div>

                                <!-- From / To Comparison Row -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200/60 text-xs">
                                    <div class="p-3 bg-white rounded-lg border border-slate-200">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Posisi Sebelumnya</span>
                                        <p class="font-bold text-slate-800">{{ $d->jabatanLama->nama_jabatan ?? '-' }}</p>
                                        <p class="text-slate-500 text-[11px]">{{ $d->deptLama->nama_dept ?? '-' }} • {{ $d->cabangLama->nama_cabang ?? '-' }}</p>
                                    </div>
                                    <div class="p-3 bg-white rounded-lg border border-emerald-200">
                                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Posisi Baru</span>
                                        <p class="font-bold text-slate-900">{{ $d->jabatanBaru->nama_jabatan ?? '-' }}</p>
                                        <p class="text-slate-600 text-[11px]">{{ $d->deptBaru->nama_dept ?? '-' }} • {{ $d->cabangBaru->nama_cabang ?? '-' }}</p>
                                    </div>
                                </div>

                                @if (!empty($d->keterangan))
                                    <p class="text-xs text-slate-500 italic bg-white px-3 py-2 rounded-lg border border-slate-100">
                                        "{{ $d->keterangan }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Tab 3: Training Module -->
            <div id="tab-pane-training" class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden" style="display: none;">
                <!-- Seamless Solid Card Header matching /karyawan table style -->
                <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-white border-0"
                     style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-white/20 text-white flex items-center justify-center text-sm font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                            <i class="ti ti-school"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-extrabold text-white tracking-tight">Riwayat Pelatihan & Sertifikasi</h3>
                            </div>
                            <p class="text-xs text-white/85 font-medium mt-0.5">Program pelatihan dan sertifikasi keahlian yang telah diikuti karyawan</p>
                        </div>
                    </div>
                    <button type="button" 
                            id="btnAddTrainingV2" 
                            class="px-3.5 py-2 text-xs font-bold text-slate-800 bg-white hover:bg-slate-100 rounded-xl shadow-sm flex items-center justify-center gap-1.5 transition active:scale-95 shrink-0">
                        <i class="ti ti-plus text-sm" style="color: var(--theme-color-1, #0a5c41);"></i>
                        <span style="color: var(--theme-color-1, #0a5c41);">Tambah Pelatihan</span>
                    </button>
                </div>

                <!-- Responsive Table Container (Full Width) -->
                <div id="load-training-v2" class="min-h-[140px] w-full">
                    <!-- Training data loaded via AJAX -->
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Foto Wajah Lightbox -->
<div class="modal fade" id="modalFotoWajah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-2xl bg-slate-900 overflow-hidden text-white">
            <div class="p-4 flex items-center justify-between border-b border-slate-800">
                <h5 class="text-sm font-bold text-white mb-0 flex items-center gap-2">
                    <i class="ti ti-face-id text-emerald-400"></i>
                    <span>Pratinjau Foto Wajah</span>
                </h5>
                <button type="button" class="text-slate-400 hover:text-white" data-bs-dismiss="modal">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="p-3 bg-black/40 flex items-center justify-center">
                <img src="" id="modalImage" class="max-h-[420px] rounded-xl object-contain shadow-md" alt="Foto Wajah">
            </div>
        </div>
    </div>
</div>

<!-- Reusable Modal Form -->
<div class="modal fade" id="modalV2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border border-slate-200/90 shadow-2xl rounded-2xl bg-white overflow-hidden">
            <div class="px-6 py-4 bg-white border-b border-slate-200/90 flex items-center justify-between">
                <h5 class="modal-title text-base font-bold text-slate-900 tracking-tight" id="modalV2Title">Modal Title</h5>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center" data-bs-dismiss="modal">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>
            <div class="modal-body p-6 bg-white" id="loadmodalV2">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@push('myscript')
<script src="{{ asset('assets/external/js/face-model-cache.js') }}"></script>
<script>
    function showTabV2(tab) {
        $('#tab-pane-face, #tab-pane-mutation, #tab-pane-training').hide();
        $('.tab-nav-v2').removeClass('active');

        if (tab === 'face') {
            $('#tab-pane-face').show();
            $('#tab-btn-face').addClass('active');
        } else if (tab === 'mutation') {
            $('#tab-pane-mutation').show();
            $('#tab-btn-mutation').addClass('active');
        } else if (tab === 'training') {
            $('#tab-pane-training').show();
            $('#tab-btn-training').addClass('active');
            loadTrainingV2();
        }
    }

    function loadTrainingV2() {
        const nik = "{{ Crypt::encrypt($karyawan->nik) }}";
        $('#load-training-v2').html('<div class="text-center py-6 text-slate-500 text-xs"><i class="ti ti-loader animate-spin text-lg block mb-1"></i>Memuat data pelatihan...</div>');
        $('#load-training-v2').load('/pelatihan/' + nik + '/index');
    }

    $(document).on('click', '#btnAddTrainingV2', function(e) {
        e.preventDefault();
        const nik = "{{ Crypt::encrypt($karyawan->nik) }}";
        $('#modalV2Title').text("Tambah Pelatihan");
        $('#loadmodalV2').html('<div class="text-center py-8 text-slate-500"><i class="ti ti-loader animate-spin text-xl block mb-2"></i>Memuat form...</div>');
        const modal = new bootstrap.Modal(document.getElementById('modalV2'));
        modal.show();
        $('#loadmodalV2').load('/pelatihan/' + nik + '/create');
    });

    $(document).on('click', '#btnAddface', function(e) {
        e.preventDefault();
        $('#modalV2Title').text("Perekaman Data Wajah");
        $('#loadmodalV2').html('<div class="text-center py-8 text-slate-500"><i class="ti ti-loader animate-spin text-xl block mb-2"></i>Memuat kamera...</div>');
        const modal = new bootstrap.Modal(document.getElementById('modalV2'));
        modal.show();
        $('#loadmodalV2').load('/facerecognition/' + '{{ Crypt::encrypt($karyawan->nik) }}' + '/create');
    });

    // Lightbox image preview
    const modalFotoWajah = document.getElementById('modalFotoWajah');
    if (modalFotoWajah) {
        modalFotoWajah.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const imageUrl = button.getAttribute('data-image');
            const modalImage = this.querySelector('#modalImage');
            modalImage.src = imageUrl;
        });
    }

    // Delete Single Face
    $(document).on('submit', '.deleteform', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Wajah?',
            text: 'Foto wajah ini akan dihapus dari dataset verifikasi.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((res) => {
            if (res.isConfirmed) {
                const nik = '{{ $karyawan->nik }}';
                if (window.FaceModelCache && typeof window.FaceModelCache.clearDescriptors === 'function') {
                    try { window.FaceModelCache.clearDescriptors(nik); } catch (err) {}
                }
                form.submit();
            }
        });
    });

    // Delete Training Confirmation (SweetAlert2)
    $(document).on('submit', '.delete-training-form', function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Hapus Pelatihan?',
            text: 'Data riwayat pelatihan ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((res) => {
            if (res.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
@endsection
