@extends('v2.layouts.app')

@section('titlepage', 'Edit Profile')
@section('page_title', 'Pengaturan Akun & Profil')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $userPhoto = null;
    if ($karyawan && $karyawan->foto && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto)) {
        $userPhoto = getfotoKaryawan($karyawan->foto);
    }
@endphp

<div class="space-y-6">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-user-circle text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Edit Profil Pengguna</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola informasi akun login, identitas profil, foto profil, dan keamanan kata sandi Anda
            </p>
        </div>

        <!-- Breadcrumbs -->
        <nav class="flex items-center text-xs text-slate-400 font-medium">
            <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                <i class="ti ti-home text-sm"></i>
                <span>Dashboard</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="font-bold text-slate-800">Edit Profil</span>
        </nav>
    </div>

    <!-- ================= 2. MAIN PROFILE FORM CONTAINER ================= -->
    <form action="{{ route('profile.updateprofile') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- LEFT COLUMN: Profile Card & Photo Uploader (4 Cols) -->
            <div class="lg:col-span-4 space-y-5">
                
                <!-- Avatar Card -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-2xs text-center relative overflow-hidden flex flex-col items-center">
                    
                    <!-- Decorative Background Header -->
                    <div class="w-full h-24 rounded-2xl mb-[-42px]"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});"></div>

                    <!-- Profile Image with Edit Overlay -->
                    <div class="relative group/avatar mt-2">
                        <div class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-md bg-white flex items-center justify-center text-2xl font-black text-slate-700">
                            <img id="avatarPreview" 
                                 src="{{ $userPhoto ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0a5c41&color=fff&size=200' }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-full h-full object-cover">
                        </div>

                        <!-- Upload Photo Trigger Badge -->
                        <label for="fotoInput" 
                               class="absolute -bottom-1 -right-1 w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-700 shadow-sm flex items-center justify-center cursor-pointer hover:bg-slate-50 active:scale-95 transition"
                               title="Ganti Foto Profil">
                            <i data-lucide="camera" class="w-4 h-4 text-emerald-600"></i>
                        </label>
                        <input type="file" id="fotoInput" name="foto" accept="image/*" class="hidden" onchange="previewImage(event)">
                    </div>

                    <!-- User Meta Details -->
                    <h3 class="text-base font-extrabold text-slate-800 mt-3 leading-tight">{{ $user->name }}</h3>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-bold text-white shadow-2xs"
                              style="background-color: {{ $themeColor1 }};">
                            {{ $user->getRoleNames()->first() ?? 'User' }}
                        </span>
                        @if($karyawan && $karyawan->nama_dept)
                            <span class="text-xs text-slate-500 font-medium">• {{ $karyawan->nama_dept }}</span>
                        @endif
                    </div>

                    <div class="w-full mt-5 pt-4 border-t border-slate-100 text-left space-y-2.5 text-xs text-slate-600">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Username:</span>
                            <span class="font-bold text-slate-800">{{ $user->username }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 font-medium">Email:</span>
                            <span class="font-bold text-slate-800 truncate max-w-[180px]">{{ $user->email ?? '-' }}</span>
                        </div>
                        @if($karyawan)
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">NIK:</span>
                                <span class="font-bold text-slate-800 font-mono">{{ $karyawan->nik }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Cabang:</span>
                                <span class="font-bold text-slate-800">{{ $karyawan->nama_cabang ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 font-medium">Jabatan:</span>
                                <span class="font-bold text-slate-800">{{ $karyawan->nama_jabatan ?? '-' }}</span>
                            </div>
                        @endif
                    </div>

                    <p class="text-[10px] text-slate-400 mt-4 leading-relaxed">
                        Format gambar: JPG, PNG, WEBP (Maks. 2MB)
                    </p>
                </div>

                <!-- Account Security Tip Card -->
                <div class="bg-gradient-to-br from-emerald-50 via-teal-50/50 to-white rounded-3xl p-5 border border-emerald-100 text-xs text-slate-600 space-y-2">
                    <div class="flex items-center gap-2 text-emerald-800 font-bold">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Keamanan Akun</span>
                    </div>
                    <p class="text-slate-500 leading-relaxed text-[11px]">
                        Gunakan kombinasi password yang kuat dengan perpaduan huruf besar, huruf kecil, angka, dan karakter simbol untuk menjaga keamanan akun Anda.
                    </p>
                </div>

            </div>

            <!-- RIGHT COLUMN: Form Fields (8 Cols) -->
            <div class="lg:col-span-8 space-y-5">
                
                <!-- Card 1: Informasi Akun Utama -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-2xs space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-sm">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 leading-tight">Informasi Akun</h3>
                            <span class="text-[11px] text-slate-400">Data identitas utama yang digunakan pada sistem</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Nama Lengkap -->
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-bold text-slate-700">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $user->name) }}" 
                                       required
                                       placeholder="Nama Lengkap User" 
                                       class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                            </div>
                            @error('name')
                                <span class="text-[11px] text-rose-500 font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="space-y-1.5">
                            <label for="username" class="block text-xs font-bold text-slate-700">
                                Username <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <i data-lucide="at-sign" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="text" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username', $user->username) }}" 
                                       required
                                       placeholder="Username Login" 
                                       class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                            </div>
                            @error('username')
                                <span class="text-[11px] text-rose-500 font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5 sm:col-span-2">
                            <label for="email" class="block text-xs font-bold text-slate-700">
                                Alamat Email
                            </label>
                            <div class="relative">
                                <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       placeholder="email@example.com" 
                                       class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                            </div>
                            @error('email')
                                <span class="text-[11px] text-rose-500 font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        @if($karyawan)
                            <!-- No HP Karyawan -->
                            <div class="space-y-1.5">
                                <label for="no_hp" class="block text-xs font-bold text-slate-700">
                                    No. Handphone / WhatsApp
                                </label>
                                <div class="relative">
                                    <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                    <input type="text" 
                                           id="no_hp" 
                                           name="no_hp" 
                                           value="{{ old('no_hp', $karyawan->no_hp) }}" 
                                           placeholder="08123456789" 
                                           class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="space-y-1.5 sm:col-span-2">
                                <label for="alamat" class="block text-xs font-bold text-slate-700">
                                    Alamat Domisili
                                </label>
                                <div class="relative">
                                    <textarea id="alamat" 
                                              name="alamat" 
                                              rows="2"
                                              placeholder="Alamat lengkap..." 
                                              class="w-full p-3 text-xs bg-slate-50 focus:bg-white border border-slate-200/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">{{ old('alamat', $karyawan->alamat) }}</textarea>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Card 2: Keamanan & Ganti Password -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-2xs space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                            <i data-lucide="key-round" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 leading-tight">Ubah Kata Sandi (Password)</h3>
                            <span class="text-[11px] text-slate-400">Kosongkan kolom password jika tidak ingin mengubah kata sandi</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Password Baru -->
                        <div class="space-y-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700">
                                Password Baru
                            </label>
                            <div class="relative">
                                <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Minimal 6 karakter" 
                                       class="w-full pl-10 pr-10 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                                <button type="button" 
                                        onclick="togglePassword('password', 'toggleIcon1')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition">
                                    <i data-lucide="eye" id="toggleIcon1" class="w-4 h-4"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="text-[11px] text-rose-500 font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="space-y-1.5">
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700">
                                Konfirmasi Password Baru
                            </label>
                            <div class="relative">
                                <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Ulangi password baru" 
                                       class="w-full pl-10 pr-10 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200/90 rounded-xl text-slate-800 font-medium placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                                <button type="button" 
                                        onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 transition">
                                    <i data-lucide="eye" id="toggleIcon2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Action Submit Button Bar -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('dashboard.index') }}" 
                       class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 shadow-2xs">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-2 transition active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

            </div>

        </div>
    </form>

</div>

@endsection

@push('myscript')
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('avatarPreview');
        if (output) {
            output.src = reader.result;
        }
    };
    if (event.target.files && event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input) return;

    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.setAttribute('data-lucide', 'eye-off');
            if (window.lucide) lucide.createIcons();
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.setAttribute('data-lucide', 'eye');
            if (window.lucide) lucide.createIcons();
        }
    }
}
</script>
@endpush
