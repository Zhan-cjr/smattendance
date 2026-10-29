<!DOCTYPE html>
<html lang="id" class="h-full {{ ($isDark ?? false) ? 'dark' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Login Mobile | {{ $general_setting->nama_aplikasi ?? 'SMATT_V4' }}</title>

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="{{ $general_setting->nama_aplikasi ?? 'SM-Attendance GPS' }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $general_setting->nama_aplikasi ?? 'SM-Attendance' }}">
    <meta name="description" content="Aplikasi Mobile Presensi GPS & HRMS Karyawan">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="{{ $t['primary'] ?? '#32745e' }}">
    <meta name="msapplication-navbutton-color" content="{{ $t['primary'] ?? '#32745e' }}">

    <!-- Favicon & Apple Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="manifest" href="/manifest.json?v={{ file_exists(public_path('manifest.json')) ? filemtime(public_path('manifest.json')) : time() }}">

    <!-- Sync Mobile Theme Scheme with LocalStorage -->
    <script>
        (function() {
            var serverScheme = "{{ $general_setting->mobile_theme_scheme ?? 'green' }}";
            var lastServerScheme = localStorage.getItem('smatt_server_scheme');
            if (lastServerScheme !== serverScheme) {
                localStorage.setItem('smatt_server_scheme', serverScheme);
                localStorage.setItem('smatt_theme', serverScheme === 'dark' ? 'dark' : 'light');
            }
            var savedTheme = localStorage.getItem('smatt_theme') || (serverScheme === 'dark' ? 'dark' : 'light');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome & Tabler Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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
        :root {
            --theme-primary: {{ $t['primary'] ?? '#32745e' }};
            --theme-light: {{ $t['primary_light'] ?? '#58907D' }};
            --theme-dark: {{ $t['primary_dark'] ?? '#1e4b3d' }};
            --theme-rgb: {{ $t['primary_rgb'] ?? '50, 116, 94' }};
            --theme-bg: {{ $t['bg_body'] ?? '#f8fafc' }};
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            height: 100%;
            width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overscroll-behavior: none;
            background-color: var(--theme-dark);
        }

        .safe-top {
            padding-top: max(env(safe-area-inset-top, 0px), 18px);
        }
        .safe-bottom {
            padding-bottom: max(env(safe-area-inset-bottom, 0px), 16px);
        }

        /* ===== Animated Hero Background ===== */
        .hero-bg {
            background: linear-gradient(145deg, var(--theme-light) 0%, var(--theme-primary) 50%, var(--theme-dark) 100%);
            position: relative;
            overflow: hidden;
        }

        html.dark .hero-bg {
            background: linear-gradient(145deg, #1e293b 0%, #0f172a 55%, #020617 100%);
        }

        /* Subtle grid pattern in hero */
        .hero-grid-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.16) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        /* Animated Floating Orbs */
        .orb-1 {
            position: absolute;
            top: -35px;
            right: -35px;
            width: 170px;
            height: 170px;
            background: radial-gradient(circle, rgba(255,255,255,0.28) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            animation: floatOrb1 7s ease-in-out infinite;
            pointer-events: none;
        }

        .orb-2 {
            position: absolute;
            bottom: 10px;
            left: -45px;
            width: 190px;
            height: 190px;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            animation: floatOrb2 9s ease-in-out infinite;
            pointer-events: none;
        }

        .morph-shape {
            position: absolute;
            top: 22%;
            right: 8%;
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(4px);
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            animation: morphBlob 10s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes floatOrb1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-20px, 18px) scale(1.12); }
        }

        @keyframes floatOrb2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(24px, -15px) scale(1.08); }
        }

        @keyframes morphBlob {
            0%, 100% {
                border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
                transform: rotate(0deg) scale(1);
            }
            50% {
                border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
                transform: rotate(180deg) scale(1.08);
            }
        }

        /* Floating Brand Logo Box */
        .brand-logo-box {
            animation: floatLogo 4.5s ease-in-out infinite;
            box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.25), 0 0 0 4px rgba(255, 255, 255, 0.16);
        }

        @keyframes floatLogo {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        /* ===== Bottom Sheet Card ===== */
        .login-sheet {
            background-color: #ffffff;
            border-radius: 34px 34px 0 0;
            box-shadow: 0 -12px 40px rgba(0, 0, 0, 0.18);
            position: relative;
            z-index: 20;
            animation: sheetSlideUp 0.55s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        html.dark .login-sheet {
            background: linear-gradient(180deg, #131d31 0%, #0b1120 100%);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 -12px 40px rgba(0, 0, 0, 0.6);
        }

        @keyframes sheetSlideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Staggered Element Entrance */
        .stagger-item {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeUpItem 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .stagger-1 { animation-delay: 0.08s; }
        .stagger-2 { animation-delay: 0.16s; }
        .stagger-3 { animation-delay: 0.24s; }
        .stagger-4 { animation-delay: 0.32s; }
        .stagger-5 { animation-delay: 0.40s; }
        .stagger-6 { animation-delay: 0.48s; }

        @keyframes fadeUpItem {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Input Styling & Focus Animation */
        .input-group-modern {
            position: relative;
            transition: transform 0.25s ease;
        }

        .input-group-modern:focus-within {
            transform: translateY(-2px);
        }

        .input-field-modern {
            width: 100%;
            height: 50px;
            padding-left: 46px;
            padding-right: 44px;
            border-radius: 16px;
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            font-size: 13.5px;
            font-weight: 600;
            color: #0f172a;
            transition: all 0.25s ease;
            outline: none;
        }

        .input-field-modern::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .input-field-modern:focus {
            background-color: #ffffff;
            border-color: var(--theme-primary);
            box-shadow: 0 8px 24px -4px rgba(var(--theme-rgb), 0.2), 0 0 0 3.5px rgba(var(--theme-rgb), 0.12);
        }

        .input-icon-left {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 19px;
            transition: all 0.25s ease;
            pointer-events: none;
        }

        .input-group-modern:focus-within .input-icon-left {
            color: var(--theme-primary);
            transform: translateY(-50%) scale(1.1);
        }

        html.dark .input-field-modern {
            background-color: #1e293b;
            border-color: rgba(255, 255, 255, 0.1);
            color: #f8fafc;
        }

        html.dark .input-field-modern:focus {
            background-color: #0f172a;
            border-color: var(--theme-light);
            box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.5), 0 0 0 3px rgba(var(--theme-rgb), 0.25);
        }

        /* Primary Submit Button with Shimmer */
        .btn-submit-theme {
            background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-light) 50%, var(--theme-dark) 100%);
            background-size: 200% auto;
            box-shadow: 0 10px 26px -4px rgba(var(--theme-rgb), 0.45);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-submit-theme::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 45%;
            height: 200%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.28) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: rotate(25deg);
            animation: shimmerSweep 3.8s infinite;
            pointer-events: none;
        }

        @keyframes shimmerSweep {
            0% { left: -60%; }
            45%, 100% { left: 130%; }
        }

        .btn-submit-theme:active {
            transform: scale(0.97);
            box-shadow: 0 5px 14px -2px rgba(var(--theme-rgb), 0.35);
        }

        /* WhatsApp Help Button */
        .btn-wa-help {
            background: rgba(37, 211, 102, 0.08);
            border: 1.5px solid rgba(37, 211, 102, 0.28);
            color: #15803d;
            transition: all 0.25s ease;
        }
        .btn-wa-help:hover, .btn-wa-help:active {
            background: rgba(37, 211, 102, 0.15);
            border-color: rgba(37, 211, 102, 0.45);
            transform: translateY(-1px);
        }
        html.dark .btn-wa-help {
            background: rgba(37, 211, 102, 0.12);
            border-color: rgba(37, 211, 102, 0.3);
            color: #4ade80;
        }

        /* Developer Badge Pill */
        .dev-credit-link {
            color: var(--theme-primary);
            transition: all 0.2s ease;
        }
        html.dark .dev-credit-link {
            color: #38bdf8;
        }
    </style>
</head>

<body class="h-full antialiased text-slate-800 dark:text-slate-100 flex flex-col justify-between min-h-screen relative overflow-x-hidden">

    <!-- MAIN APP CONTAINER -->
    <div class="relative z-10 flex flex-col flex-1 min-h-screen hero-bg">

        <!-- Animated Decorative Backdrop -->
        <div class="absolute inset-0 hero-grid-pattern pointer-events-none"></div>
        <div class="orb-1"></div>
        <div class="orb-2"></div>
        <div class="morph-shape"></div>

        <!-- ===== TOP HERO SECTION ===== -->
        <header class="safe-top px-6 pt-6 pb-8 flex flex-col justify-between text-white relative z-10">

            <!-- Brand Bar -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-white/95 p-1.5 flex items-center justify-center shrink-0 brand-logo-box">
                        @if (!empty($general_setting->logo) && Storage::disk('public')->exists('logo/' . $general_setting->logo))
                            <img src="{{ asset('storage/logo/' . $general_setting->logo) }}"
                                 alt="Logo"
                                 class="w-full h-full object-contain">
                        @else
                            <img src="{{ asset('assets/login/images/logoweb-1.png') }}"
                                 alt="Logo"
                                 class="w-full h-full object-contain"
                                 onerror="this.outerHTML='<i class=\'ti ti-fingerprint text-2xl\' style=\'color: var(--theme-primary)\'></i>'">
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h1 class="text-lg font-black tracking-tight leading-none text-white drop-shadow-xs">
                                {{ $general_setting->nama_aplikasi ?? 'SMATT_V4' }}
                            </h1>
                            <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded-md bg-white/20 backdrop-blur-md text-white uppercase tracking-wider">
                                PRO
                            </span>
                        </div>
                        <span class="text-[11px] font-semibold text-white/80 tracking-wide block mt-1">
                            {{ $general_setting->nama_perusahaan ?? 'SM-Attendance GPS' }}
                        </span>
                    </div>
                </div>

                <!-- Live Status Badge -->
                <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/20 backdrop-blur-md border border-white/20 text-[10.5px] font-bold text-white shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping absolute"></span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 relative"></span>
                    <span>GPS Ready</span>
                </div>
            </div>

            <!-- Greeting Headline -->
            <div class="mt-1 space-y-1.5 pr-4">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/15 backdrop-blur-md border border-white/15 text-[10.5px] font-bold text-white/95">
                    <span>👋 Assalamualaikum & Selamat Datang</span>
                </div>
                <h2 class="text-2xl font-black tracking-tight text-white leading-tight drop-shadow-xs">
                    Masuk ke Akun Anda
                </h2>
                <p class="text-xs text-white/85 font-medium leading-relaxed">
                    Silakan masuk menggunakan NIK, Username, atau Email karyawan untuk melakukan presensi.
                </p>
            </div>
        </header>

        <!-- ===== BOTTOM SHEET LOGIN CARD ===== -->
        <main class="flex-1 login-sheet px-6 pt-6 pb-5 flex flex-col justify-between">

            <div>
                <!-- Pull Handle Indicator -->
                <div class="w-11 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto -mt-2 mb-5"></div>

                <!-- Alert Notifications -->
                @if (session('error'))
                    <div class="mb-4 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800/70 flex items-start gap-2.5 text-xs text-rose-700 dark:text-rose-300 stagger-item stagger-1">
                        <i class="fa-solid fa-circle-exclamation text-sm text-rose-600 dark:text-rose-400 shrink-0 mt-0.5"></i>
                        <div class="font-bold flex-1">{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800/70 flex items-start gap-2.5 text-xs text-rose-700 dark:text-rose-300 stagger-item stagger-1">
                        <i class="fa-solid fa-circle-exclamation text-sm text-rose-600 dark:text-rose-400 shrink-0 mt-0.5"></i>
                        <div class="font-bold space-y-0.5 flex-1">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login') }}" method="POST" id="mobileLoginForm" class="space-y-4">
                    @csrf

                    <!-- Username / NIK / Email -->
                    <div class="space-y-1.5 stagger-item stagger-1">
                        <label for="id_user" class="text-xs font-extrabold text-slate-700 dark:text-slate-200 block">
                            Username / NIK / Email
                        </label>
                        <div class="input-group-modern">
                            <input type="text"
                                   name="id_user"
                                   id="id_user"
                                   value="{{ old('id_user') }}"
                                   placeholder="Masukkan NIK atau Email Anda"
                                   required
                                   autocomplete="username"
                                   class="input-field-modern">
                            <i class="ti ti-user input-icon-left"></i>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5 stagger-item stagger-2">
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-xs font-extrabold text-slate-700 dark:text-slate-200">
                                Kata Sandi
                            </label>
                        </div>
                        <div class="input-group-modern">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   placeholder="Masukkan kata sandi"
                                   required
                                   autocomplete="current-password"
                                   class="input-field-modern">
                            <i class="ti ti-lock input-icon-left"></i>
                            <button type="button"
                                    id="btnTogglePassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition cursor-pointer"
                                    tabindex="-1">
                                <i class="ti ti-eye text-lg" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password Row -->
                    <div class="flex items-center justify-between pt-0.5 stagger-item stagger-3">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember"
                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 focus:ring-0 cursor-pointer"
                                   style="accent-color: var(--theme-primary);">
                            <span class="text-xs text-slate-600 dark:text-slate-300 font-semibold">Ingat Saya</span>
                        </label>

                        <a href="https://wa.me/6285861094485?text=Halo%20Admin%20ZhanSoft%2C%20saya%20lupa%20password%20akun%20aplikasi%20SMATT3"
                           target="_blank"
                           class="text-xs font-bold hover:underline"
                           style="color: var(--theme-primary);">
                            Lupa Password?
                        </a>
                    </div>

                    <!-- Primary Login Submit Button -->
                    <div class="pt-1 stagger-item stagger-4">
                        <button type="submit"
                                id="btnSubmitLogin"
                                class="btn-submit-theme w-full h-12 rounded-2xl text-white font-extrabold text-sm flex items-center justify-center gap-2 cursor-pointer">
                            <span id="btnSubmitText">Masuk Sekarang</span>
                            <i class="ti ti-arrow-right text-lg" id="btnSubmitIcon"></i>
                        </button>
                    </div>
                </form>

                <!-- Divider Bantuan -->
                <div class="relative flex py-4 items-center stagger-item stagger-5">
                    <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
                    <span class="flex-shrink mx-3 text-[10.5px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        Butuh Bantuan Login?
                    </span>
                    <div class="flex-grow border-t border-slate-200 dark:border-slate-800"></div>
                </div>

                <!-- Get Help (WhatsApp) Button -->
                <div class="stagger-item stagger-5">
                    <a href="https://wa.me/6285861094485?text=Halo%20Admin%20ZhanSoft%2C%20saya%20butuh%20bantuan%20terkait%20login%20aplikasi%20SMATT3"
                       target="_blank"
                       class="btn-wa-help w-full py-2.5 px-4 rounded-2xl flex items-center justify-center gap-2.5 text-xs font-extrabold no-underline">
                        <i class="fa-brands fa-whatsapp text-lg text-[#25D366]"></i>
                        <span>Get Help (Hubungi WhatsApp)</span>
                    </a>
                </div>
            </div>

            <!-- Developer Credit Footer -->
            <footer class="safe-bottom pt-5 mt-4 border-t border-slate-100 dark:border-slate-800/80 text-center stagger-item stagger-6">
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                    Aplikasi ini dikembangkan oleh <br>
                    <a href="https://www.instagram.com/amn4ll/?utm_source=ig_web_button_share_sheet"
                       target="_blank"
                       class="dev-credit-link font-extrabold inline-flex items-center gap-1 mt-0.5 hover:underline">
                        <i class="fa-brands fa-instagram text-xs"></i>
                        <span>ZhanSoft - Amnal</span>
                    </a>
                </p>
            </footer>

        </main>
    </div>

    <!-- Interactive Scripts -->
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
                    eyeIcon.className = isPassword ? 'ti ti-eye-off text-lg' : 'ti ti-eye text-lg';
                });
            }

            // Submit Loading Feedback
            const loginForm = document.getElementById('mobileLoginForm');
            const btnSubmit = document.getElementById('btnSubmitLogin');
            const btnText = document.getElementById('btnSubmitText');
            const btnIcon = document.getElementById('btnSubmitIcon');

            if (loginForm && btnSubmit) {
                loginForm.addEventListener('submit', function() {
                    btnSubmit.style.opacity = '0.85';
                    btnSubmit.style.pointerEvents = 'none';
                    if (btnText) btnText.textContent = 'Memproses...';
                    if (btnIcon) btnIcon.className = 'fa-solid fa-circle-notch fa-spin text-sm';
                });
            }
        });

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        registration.update();
                    })
                    .catch(function(err) {
                        console.log('SW registration error:', err);
                    });
            });
        }
    </script>
    @include('components.pwa-install-prompt')
</body>

</html>
