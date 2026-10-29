<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Portal | {{ $general_setting->nama_aplikasi ?? 'SMATT_V4' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}" />

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tabler Icons Fallback -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        svg.lucide { display: inline-block; vertical-align: middle; }
    </style>
</head>

<body class="h-full font-sans antialiased text-white flex flex-col justify-between min-h-screen selection:bg-white/20 selection:text-white relative overflow-x-hidden"
      style="background-color: {{ $general_setting->theme_color_1 ?? '#0a5c41' }};">

    <!-- Background Image with Smooth Gradient Mask (Left/Hero text side is SOLID theme color, fading to very subtle faint image on right) -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden"
         style="background: linear-gradient(135deg, {{ $general_setting->theme_color_1 ?? '#0a5c41' }} 0%, {{ $general_setting->theme_color_2 ?? '#117956' }} 100%);">
        <img src="{{ asset('assets/login/images/bg-login-v2.jpg') }}" 
             alt="Office Background" 
             class="w-full h-full object-cover object-center transform scale-105"
             style="opacity: 0.12; -webkit-mask-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0) 35%, rgba(0,0,0,0.4) 70%, rgba(0,0,0,1) 100%); mask-image: linear-gradient(to right, rgba(0,0,0,0) 0%, rgba(0,0,0,0) 35%, rgba(0,0,0,0.4) 70%, rgba(0,0,0,1) 100%);">
        <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-transparent to-transparent"></div>
    </div>

    <!-- TOP HEADER -->
    <header class="w-full max-w-7xl mx-auto px-6 py-6 sm:py-8 flex items-center justify-between relative z-10">
        <div class="flex items-center gap-4">
            @if (!empty($general_setting->logo) && Storage::disk('public')->exists('logo/' . $general_setting->logo))
                <img src="{{ asset('storage/logo/' . $general_setting->logo) }}" alt="Logo" class="max-h-12 sm:max-h-14 max-w-[56px] w-auto h-auto object-contain shrink-0 drop-shadow-sm">
            @else
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                    <i class="ti ti-layers text-2xl sm:text-3xl text-white"></i>
                </div>
            @endif
            <div>
                <span class="font-extrabold text-xl sm:text-2xl tracking-tight text-white block leading-tight">
                    {{ $general_setting->nama_aplikasi ?? 'SMATT_V4' }}
                </span>
                <span class="text-xs sm:text-[13px] font-semibold text-white/85 uppercase tracking-wider block mt-0.5">
                    {{ $general_setting->nama_perusahaan ?? 'Enterprise HR & Attendance' }}
                </span>
            </div>
        </div>
    </header>

    <!-- SPLIT CONTENT: HERO & LOGIN FORM -->
    <main class="flex-1 flex items-center justify-center px-4 sm:px-6 py-6 relative z-10">
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            <!-- LEFT COLUMN: Brand Hero Info -->
            <div class="lg:col-span-7 space-y-5 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-white text-xs font-bold">
                    <i class="ti ti-circle-check text-sm text-white/80"></i>
                    <span>Sistem Presensi &amp; Manajemen Karyawan</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.2]">
                    <span>{{ $general_setting->nama_perusahaan ?? ($general_setting->nama_aplikasi ?? 'SMATT_V4') }}</span> <br class="hidden sm:block">
                    <span id="animatedHeroText" class="inline-block transition-all duration-500 ease-out transform opacity-100 text-white drop-shadow-xs text-2xl sm:text-3xl lg:text-4xl font-bold mt-1">
                        Kinerja Hebat &amp; Efisien.
                    </span>
                </h1>

                <p class="text-sm sm:text-base text-white/80 font-normal max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Kelola absensi GPS real-time, pengajuan cuti &amp; izin instan, slip gaji, serta verifikasi biometrik dalam satu platform terpadu.
                </p>

                <!-- Feature Highlights (Clean White Transparent Cards) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 max-w-lg mx-auto lg:mx-0">
                    <div class="p-3.5 rounded-xl bg-white/10 border border-white/15 text-left">
                        <div class="w-7 h-7 rounded-lg bg-white/15 text-white flex items-center justify-center mb-2">
                            <i class="ti ti-map-pin text-base"></i>
                        </div>
                        <h4 class="text-xs font-bold text-white">Geofencing GPS</h4>
                        <p class="text-[10.5px] text-white/70 mt-0.5">Validasi radius presisi</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-white/10 border border-white/15 text-left">
                        <div class="w-7 h-7 rounded-lg bg-white/15 text-white flex items-center justify-center mb-2">
                            <i class="ti ti-scan text-base"></i>
                        </div>
                        <h4 class="text-xs font-bold text-white">Face Biometrics</h4>
                        <p class="text-[10.5px] text-white/70 mt-0.5">Verifikasi anti titip absen</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-white/10 border border-white/15 text-left col-span-2 sm:col-span-1">
                        <div class="w-7 h-7 rounded-lg bg-white/15 text-white flex items-center justify-center mb-2">
                            <i class="ti ti-receipt text-base"></i>
                        </div>
                        <h4 class="text-xs font-bold text-white">E-Slip Gaji</h4>
                        <p class="text-[10.5px] text-white/70 mt-0.5">Kalkulasi payroll &amp; lembur</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Crisp White Login Card -->
            <div class="lg:col-span-5 w-full max-w-md mx-auto">
                <div class="rounded-2xl bg-white text-slate-800 p-6 sm:p-8 shadow-2xl border border-white/20">
                    
                    <!-- Card Header -->
                    <div class="mb-5">
                        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Selamat Datang</h2>
                        <p class="text-xs text-slate-500 mt-1">Masuk untuk mengakses portal dan akun Anda</p>
                    </div>

                    <!-- Error Alert -->
                    @if (session('error'))
                        <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-xs text-rose-700">
                            <i class="ti ti-alert-circle text-base text-rose-600 shrink-0 mt-0.5"></i>
                            <div class="font-medium">{{ session('error') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-xs text-rose-700">
                            <i class="ti ti-alert-circle text-base text-rose-600 shrink-0 mt-0.5"></i>
                            <div class="font-medium space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Username / Email Input -->
                        <div class="space-y-1.5">
                            <label for="id_user" class="text-xs font-bold text-slate-700 block">
                                Username / NIK / Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i class="ti ti-user text-base"></i>
                                </div>
                                <input type="text" 
                                       name="id_user" 
                                       id="id_user" 
                                       value="{{ old('id_user') }}" 
                                       placeholder="Masukkan NIK atau email..." 
                                       required 
                                       autocomplete="username"
                                       class="w-full h-10 pl-9 pr-3.5 bg-slate-50 border border-slate-300 focus:border-slate-800 rounded-xl text-xs font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-800 transition">
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="password" class="text-xs font-bold text-slate-700">Password</label>
                                <a href="javascript:void(0)" onclick="alert('Silakan hubungi bagian HRD / Administrator untuk bantuan reset password.')" class="text-[11px] font-semibold text-slate-500 hover:text-slate-800">
                                    Lupa Password?
                                </a>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i class="ti ti-lock text-base"></i>
                                </div>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       placeholder="••••••••••••" 
                                       required 
                                       autocomplete="current-password"
                                       class="w-full h-10 pl-9 pr-10 bg-slate-50 border border-slate-300 focus:border-slate-800 rounded-xl text-xs font-medium text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-slate-800 transition">
                                <button type="button" 
                                        id="btnTogglePassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 transition cursor-pointer"
                                        tabindex="-1">
                                    <i class="ti ti-eye text-base" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center justify-between pt-0.5">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" 
                                       name="remember" 
                                       id="remember" 
                                       class="w-4 h-4 rounded border-slate-300 text-slate-800 focus:ring-0 cursor-pointer">
                                <span class="text-xs text-slate-600 font-medium">Ingat sesi saya</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full h-10 rounded-xl text-white font-bold text-xs flex items-center justify-center gap-2 transition duration-150 cursor-pointer shadow-xs hover:opacity-95 active:scale-[0.99] mt-1"
                                style="background-color: {{ $general_setting->theme_color_1 ?? '#0a5c41' }};">
                            <span>Masuk ke Akun</span>
                            <i class="ti ti-arrow-right text-base"></i>
                        </button>
                    </form>

                    <!-- Presensi Scan RFID & Get Help WhatsApp Links -->
                    <div class="mt-4 pt-3.5 border-t border-slate-100 flex flex-wrap items-center justify-center gap-2">
                        <a href="{{ route('public.presensi.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700 hover:text-slate-900 transition">
                            <i class="ti ti-nfc text-base text-slate-600"></i>
                            <span>Presensi Scan RFID</span>
                        </a>
                        <a href="https://wa.me/6285861094485?text=Halo%20Admin%20ZhanSoft%2C%20saya%20butuh%20bantuan%20terkait%20login%20aplikasi%20SMATT3"
                           target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-xs font-bold text-emerald-700 hover:text-emerald-800 transition">
                            <i class="ti ti-brand-whatsapp text-base text-emerald-600"></i>
                            <span>Get Help (WhatsApp)</span>
                        </a>
                    </div>

                    <div class="mt-3 pt-2.5 border-t border-slate-100 text-center text-[11px] text-slate-500">
                        Aplikasi ini dikembangkan oleh
                        <a href="https://www.instagram.com/amn4ll/?utm_source=ig_web_button_share_sheet" target="_blank" class="font-bold hover:underline" style="color: {{ $general_setting->theme_color_1 ?? '#0a5c41' }};">
                            ZhanSoft - Amnal
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="w-full max-w-7xl mx-auto px-6 py-4 text-center text-xs text-white/70">
        &copy; {{ date('Y') }} <strong>{{ $general_setting->nama_aplikasi ?? 'SMATT_V4' }}</strong> &bull; Aplikasi ini dikembangkan oleh
        <a href="https://www.instagram.com/amn4ll/?utm_source=ig_web_button_share_sheet" target="_blank" class="text-white font-bold hover:underline">ZhanSoft - Amnal</a>
    </footer>

    <!-- Password visibility toggle script & icon render -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password Show/Hide Toggle
            const btnToggle = document.getElementById('btnTogglePassword');
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (btnToggle && passInput && eyeIcon) {
                btnToggle.addEventListener('click', function() {
                    const isPassword = passInput.type === 'password';
                    passInput.type = isPassword ? 'text' : 'password';
                    eyeIcon.className = isPassword ? 'ti ti-eye-off text-base' : 'ti ti-eye text-base';
                });
            }

            // Elegant Smooth Rotating Headline Animation
            const heroTextEl = document.getElementById('animatedHeroText');
            if (heroTextEl) {
                const phrases = [
                    'Kinerja Hebat & Efisien.',
                    'Absen Cepat, Tanpa Ribet.',
                    'Manajemen SDM Lebih Mudah.',
                    'Produktivitas Meningkat.'
                ];
                let currentIdx = 0;

                setInterval(() => {
                    // Fade & Slide Out
                    heroTextEl.classList.remove('opacity-100', 'translate-y-0');
                    heroTextEl.classList.add('opacity-0', '-translate-y-2');

                    setTimeout(() => {
                        currentIdx = (currentIdx + 1) % phrases.length;
                        heroTextEl.textContent = phrases[currentIdx];
                        // Fade & Slide In
                        heroTextEl.classList.remove('opacity-0', '-translate-y-2');
                        heroTextEl.classList.add('opacity-100', 'translate-y-0');
                    }, 400);
                }, 3800);
            }
        });
    </script>
</body>
</html>
