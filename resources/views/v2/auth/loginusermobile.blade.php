<!DOCTYPE html>
<html lang="id" class="h-full dark">

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
    <meta name="theme-color" id="metaThemeColor" content="{{ $t['primary'] ?? '#32745e' }}">
    <meta name="msapplication-navbutton-color" content="{{ $t['primary'] ?? '#32745e' }}">

    <!-- Favicon & Apple Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}" />
    <link rel="apple-touch-icon" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="manifest" href="/manifest.json?v={{ file_exists(public_path('manifest.json')) ? filemtime(public_path('manifest.json')) : time() }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Font Awesome & Tabler Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

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

    @php
        $initialScheme = $scheme ?? ($general_setting->mobile_theme_scheme ?? 'orange');
    @endphp

    <style>
        :root {
            --theme-primary: {{ $t['primary'] ?? '#e65100' }};
            --theme-light: {{ $t['primary_light'] ?? '#f57c00' }};
            --theme-dark: {{ $t['primary_dark'] ?? '#993600' }};
            --theme-rgb: {{ $t['primary_rgb'] ?? '230, 81, 0' }};
            --lamp-glow: #fbbf24;
            --lamp-glow-rgb: 251, 191, 36;
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html, body {
            min-height: 100%;
            width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overscroll-behavior: none;
            background-color: #080a0f;
            color: #f8fafc;
        }

        .safe-top {
            padding-top: max(env(safe-area-inset-top, 0px), 14px);
        }
        .safe-bottom {
            padding-bottom: max(env(safe-area-inset-bottom, 0px), 16px);
        }

        /* ===== Room & Lamp Ambient Lighting ===== */
        .room-stage {
            background: radial-gradient(circle at 50% 18%, #131823 0%, #090b10 60%, #050608 100%);
            position: relative;
            overflow-x: hidden;
            transition: background 0.6s ease;
        }

        .room-ambient-glow {
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: 0;
            background: radial-gradient(
                ellipse 90% 65% at 50% 24%,
                rgba(var(--lamp-glow-rgb), 0.24) 0%,
                rgba(var(--lamp-glow-rgb), 0.08) 45%,
                rgba(0, 0, 0, 0) 75%
            );
            transition: opacity 0.55s cubic-bezier(0.22, 1, 0.36, 1);
            z-index: 1;
        }

        body.lamp-on .room-ambient-glow {
            opacity: 1;
        }

        /* ===== Desk Lamp Container & Light Cone ===== */
        .lamp-stage {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 15;
            user-select: none;
            touch-action: none;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        body:not(.lamp-on) .lamp-stage {
            transform: translateY(14vh) scale(1.12);
        }

        body.lamp-on .lamp-stage {
            transform: translateY(0) scale(1);
        }

        .lamp-light-cone {
            position: absolute;
            top: 92px;
            left: 50%;
            transform: translateX(-50%) scaleY(0.85);
            width: 340px;
            height: 360px;
            background: linear-gradient(
                180deg,
                rgba(var(--lamp-glow-rgb), 0.36) 0%,
                rgba(var(--lamp-glow-rgb), 0.14) 40%,
                rgba(var(--lamp-glow-rgb), 0.02) 80%,
                rgba(var(--lamp-glow-rgb), 0) 100%
            );
            clip-path: polygon(36% 0%, 64% 0%, 100% 100%, 0% 100%);
            filter: blur(10px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.45s ease, transform 0.55s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: -1;
        }

        body.lamp-on .lamp-light-cone {
            opacity: 1;
            transform: translateX(-50%) scaleY(1);
        }

        .lamp-bulb-core {
            fill: #2d3340;
            transition: fill 0.3s ease, filter 0.3s ease;
        }

        body.lamp-on .lamp-bulb-core {
            fill: var(--lamp-glow);
            filter: drop-shadow(0 0 14px var(--lamp-glow)) drop-shadow(0 0 28px var(--lamp-glow));
        }

        .lamp-shade-inner {
            fill: #181c24;
            transition: fill 0.35s ease;
        }

        body.lamp-on .lamp-shade-inner {
            fill: rgba(var(--lamp-glow-rgb), 0.45);
        }

        .lamp-cord-group {
            cursor: grab;
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .lamp-cord-group:active {
            cursor: grabbing;
        }

        .lamp-bead {
            fill: #d4a359;
            transition: fill 0.3s ease, filter 0.3s ease;
        }

        body.lamp-on .lamp-bead {
            fill: var(--lamp-glow);
            filter: drop-shadow(0 0 8px rgba(var(--lamp-glow-rgb), 0.8));
        }

        /* Hint when lamp is off */
        .lamp-hint {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.35s ease, transform 0.35s ease;
        }

        body.lamp-on .lamp-hint {
            opacity: 0;
            transform: translateY(-8px);
            pointer-events: none;
            height: 0;
            margin: 0 !important;
            overflow: hidden;
        }

        @keyframes gentleBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(4px); }
        }
        .animate-hint-bounce {
            animation: gentleBounce 1.8s ease-in-out infinite;
        }

        /* ===== Illuminated Login Card ===== */
        .login-card-wrap {
            position: relative;
            z-index: 20;
            opacity: 0;
            transform: translateY(34px) scale(0.95);
            pointer-events: none;
            max-height: 0;
            overflow: hidden;
            transition:
                opacity 0.55s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.6s cubic-bezier(0.16, 1, 0.3, 1),
                max-height 0.55s ease;
        }

        body.lamp-on .login-card-wrap {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
            max-height: 1200px;
            overflow: visible;
        }

        .login-glass-card {
            background: linear-gradient(180deg, rgba(24, 29, 40, 0.94) 0%, rgba(15, 19, 27, 0.97) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-top: 1.5px solid rgba(var(--lamp-glow-rgb), 0.45);
            border-radius: 26px;
            box-shadow:
                0 -8px 36px -10px rgba(var(--lamp-glow-rgb), 0.22),
                0 24px 50px -12px rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(16px);
            transition: border-color 0.4s ease, box-shadow 0.4s ease;
        }

        /* ===== Inputs ===== */
        .input-group-lamp {
            position: relative;
            transition: transform 0.22s ease;
        }
        .input-group-lamp:focus-within {
            transform: translateY(-1px);
        }

        .input-field-lamp {
            width: 100%;
            height: 48px;
            padding-left: 44px;
            padding-right: 42px;
            border-radius: 14px;
            background-color: rgba(255, 255, 255, 0.04);
            border: 1.5px solid rgba(255, 255, 255, 0.1);
            font-size: 13.5px;
            font-weight: 600;
            color: #f8fafc;
            transition: all 0.25s ease;
            outline: none;
        }

        .input-field-lamp::placeholder {
            color: #64748b;
            font-weight: 500;
        }

        .input-field-lamp:focus {
            background-color: rgba(255, 255, 255, 0.07);
            border-color: var(--lamp-glow);
            box-shadow: 0 0 0 3px rgba(var(--lamp-glow-rgb), 0.18), 0 8px 20px -6px rgba(var(--lamp-glow-rgb), 0.28);
        }

        .input-icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 18px;
            transition: color 0.25s ease, transform 0.25s ease;
            pointer-events: none;
        }

        .input-group-lamp:focus-within .input-icon-left {
            color: var(--lamp-glow);
            transform: translateY(-50%) scale(1.08);
        }

        /* ===== Submit Button ===== */
        .btn-lamp-submit {
            background: linear-gradient(135deg, var(--lamp-glow) 0%, var(--theme-primary) 100%);
            box-shadow: 0 10px 26px -6px rgba(var(--lamp-glow-rgb), 0.5);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-lamp-submit::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -60%;
            width: 45%;
            height: 200%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.28) 50%, rgba(255,255,255,0) 100%);
            transform: rotate(25deg);
            animation: shimmerSweep 3.8s infinite;
            pointer-events: none;
        }

        @keyframes shimmerSweep {
            0% { left: -60%; }
            45%, 100% { left: 130%; }
        }

        .btn-lamp-submit:active {
            transform: scale(0.97);
        }

        /* ===== Lamp Color Swatches ===== */
        .color-swatch {
            width: 24px;
            height: 24px;
            border-radius: 9999px;
            border: 2px solid rgba(255, 255, 255, 0.22);
            cursor: pointer;
            transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
            position: relative;
        }

        .color-swatch:active {
            transform: scale(0.9);
        }

        .color-swatch.active {
            transform: scale(1.18);
            border-color: #ffffff;
            box-shadow: 0 0 12px currentColor;
        }

        /* WhatsApp Help */
        .btn-wa-help {
            background: rgba(37, 211, 102, 0.1);
            border: 1px solid rgba(37, 211, 102, 0.28);
            color: #4ade80;
            transition: all 0.25s ease;
        }
        .btn-wa-help:active {
            background: rgba(37, 211, 102, 0.18);
            transform: scale(0.98);
        }
    </style>
</head>

<body class="min-h-screen antialiased room-stage flex flex-col justify-between relative">
    <!-- Ambient Lamp Room Glow -->
    <div class="room-ambient-glow"></div>

    <!-- TOP BRAND BAR -->
    <header class="safe-top px-5 pt-4 pb-2 flex items-center justify-between relative z-20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 p-1.5 flex items-center justify-center shrink-0 backdrop-blur-md">
                @if (!empty($general_setting->logo) && Storage::disk('public')->exists('logo/' . $general_setting->logo))
                    <img src="{{ asset('storage/logo/' . $general_setting->logo) }}" alt="Logo" class="w-full h-full object-contain">
                @else
                    <img src="{{ asset('assets/login/images/logoweb-1.png') }}"
                         alt="Logo"
                         class="w-full h-full object-contain"
                         onerror="this.outerHTML='<i class=\'ti ti-fingerprint text-xl\' style=\'color: var(--lamp-glow)\'></i>'">
                @endif
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="text-sm font-extrabold tracking-tight text-white">
                        {{ $general_setting->nama_aplikasi ?? 'SMATT_V4' }}
                    </span>
                    <span class="text-[8.5px] font-extrabold px-1.5 py-0.5 rounded bg-white/15 text-white/90 uppercase tracking-wider">
                        PRO
                    </span>
                </div>
                <span class="text-[10.5px] font-medium text-slate-400 block">
                    {{ $general_setting->nama_perusahaan ?? 'SM-Attendance GPS' }}
                </span>
            </div>
        </div>

        <!-- Lamp Status Pill -->
        <button type="button"
                id="btnToggleLampPill"
                class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[10.5px] font-bold text-slate-300 backdrop-blur-md transition active:scale-95">
            <span id="lampStatusDot" class="w-2 h-2 rounded-full bg-slate-500 transition-colors duration-300"></span>
            <span id="lampStatusText">Lampu Mati</span>
        </button>
    </header>

    <!-- CENTER STAGE: INTERACTIVE DESK LAMP + LOGIN CARD -->
    <main class="flex-1 flex flex-col items-center justify-center px-5 pb-4 relative z-10 w-full max-w-md mx-auto">

        <!-- Interactive Desk Lamp -->
        <div class="lamp-stage w-full flex flex-col items-center mb-3" id="lampStage">
            <!-- Light Cone -->
            <div class="lamp-light-cone" id="lampLightCone"></div>

            <!-- SVG Articulated Desk Lamp with Pull Cord -->
            <svg width="170" height="145" viewBox="0 0 170 145" fill="none" xmlns="http://www.w3.org/2000/svg" class="overflow-visible select-none" id="deskLampSvg">
                <!-- Base Shadow -->
                <ellipse cx="72" cy="136" rx="34" ry="5.5" fill="rgba(0,0,0,0.65)" />
                <!-- Base -->
                <path d="M44 134 C44 127, 100 127, 100 134 L104 137 L40 137 Z" fill="#1e2430" stroke="#333c4d" stroke-width="1.2"/>
                <rect x="64" y="124" width="16" height="6" rx="3" fill="#2a3140"/>

                <!-- Lower Arm -->
                <line x1="72" y1="125" x2="54" y2="76" stroke="#2c3444" stroke-width="6" stroke-linecap="round"/>
                <line x1="72" y1="125" x2="54" y2="76" stroke="#475569" stroke-width="1.5" stroke-linecap="round"/>
                <!-- Lower Joint -->
                <circle cx="72" cy="125" r="5.5" fill="#d4a359" stroke="#181c24" stroke-width="1.5"/>

                <!-- Upper Arm -->
                <line x1="54" y1="76" x2="86" y2="34" stroke="#2c3444" stroke-width="5.5" stroke-linecap="round"/>
                <line x1="54" y1="76" x2="86" y2="34" stroke="#475569" stroke-width="1.4" stroke-linecap="round"/>
                <!-- Elbow Joint -->
                <circle cx="54" cy="76" r="5" fill="#d4a359" stroke="#181c24" stroke-width="1.5"/>

                <!-- Lamp Neck -->
                <circle cx="86" cy="34" r="4.5" fill="#d4a359" stroke="#181c24" stroke-width="1.5"/>
                <path d="M86 34 L102 40" stroke="#334155" stroke-width="5" stroke-linecap="round"/>

                <!-- Lamp Shade -->
                <g transform="rotate(14, 106, 48)">
                    <!-- Shade Inner Glow -->
                    <ellipse cx="108" cy="72" rx="26" ry="9" class="lamp-shade-inner"/>
                    <!-- Bulb -->
                    <circle cx="108" cy="70" r="8.5" class="lamp-bulb-core"/>
                    <!-- Shade Dome -->
                    <path d="M80 71 C80 42, 136 42, 136 71 C124 75, 92 75, 80 71 Z" fill="url(#shadeGrad)" stroke="#3b4456" stroke-width="1.2"/>
                    <!-- Top Cap -->
                    <rect x="102" y="39" width="12" height="7" rx="3" fill="#28303e"/>
                </g>

                <!-- Pull Cord (Interactive Hit Area + Animated Group) -->
                <g class="lamp-cord-group" id="lampCordGroup">
                    <!-- Invisible wider hit target for easy mobile tapping/pulling -->
                    <rect x="86" y="70" width="38" height="68" fill="transparent" />
                    <!-- Cord Line -->
                    <line id="cordLine" x1="105" y1="73" x2="105" y2="112" stroke="#cbd5e1" stroke-width="1.8" stroke-linecap="round"/>
                    <!-- Hanging Bead -->
                    <circle id="cordBead" cx="105" cy="115" r="6" class="lamp-bead" stroke="#0f172a" stroke-width="1.5"/>
                </g>

                <defs>
                    <linearGradient id="shadeGrad" x1="80" y1="42" x2="136" y2="74" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#333c4d"/>
                        <stop offset="50%" stop-color="#1e2430"/>
                        <stop offset="100%" stop-color="#11151d"/>
                    </linearGradient>
                </defs>
            </svg>

            <!-- Hint when lamp is OFF -->
            <div class="lamp-hint mt-2 text-center space-y-1.5">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-semibold text-slate-300 backdrop-blur-md">
                    <i class="ti ti-bulb text-amber-400 animate-hint-bounce"></i>
                    <span>Tarik tali atau ketuk lampu untuk login</span>
                </div>
                <p class="text-[11px] text-slate-500">Pilih warna lampu untuk mengatur tema aplikasi Anda</p>
            </div>

            <!-- Lamp Color Picker (Syncs User Mobile Theme!) -->
            <div class="mt-2.5 flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md" id="lampColorPicker">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-0.5">Tema</span>
                <button type="button" class="color-swatch" data-scheme="orange" style="background:#f59e0b; color:#f59e0b;" title="Amber (Warm)"></button>
                <button type="button" class="color-swatch" data-scheme="green" style="background:#10b981; color:#10b981;" title="Emerald (Green)"></button>
                <button type="button" class="color-swatch" data-scheme="blue" style="background:#3b82f6; color:#3b82f6;" title="Ocean (Blue)"></button>
                <button type="button" class="color-swatch" data-scheme="red" style="background:#ef4444; color:#ef4444;" title="Crimson (Red)"></button>
                <button type="button" class="color-swatch" data-scheme="purple" style="background:#a855f7; color:#a855f7;" title="Royal (Purple)"></button>
                <button type="button" class="color-swatch" data-scheme="rose" style="background:#ec4899; color:#ec4899;" title="Blossom (Rose)"></button>
                <button type="button" class="color-swatch" data-scheme="dark" style="background:#475569; color:#94a3b8;" title="Midnight (Dark)"></button>
            </div>
        </div>

        <!-- ===== ILLUMINATED LOGIN CARD ===== -->
        <div class="login-card-wrap w-full" id="loginCardWrap">
            <div class="login-glass-card p-5 sm:p-6">

                <!-- Header -->
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest block mb-0.5" style="color: var(--lamp-glow);">
                            YOUR WORKSPACE
                        </span>
                        <h2 class="text-xl font-extrabold text-white tracking-tight leading-tight">
                            Selamat Datang
                        </h2>
                        <p class="text-[11.5px] text-slate-400 font-medium mt-0.5">
                            Masuk dengan NIK, Username, atau Email Anda
                        </p>
                    </div>
                    <span id="activeThemeBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-white/5 border border-white/10 text-slate-300 capitalize">
                        {{ $initialScheme }}
                    </span>
                </div>

                <!-- Alert Notifications -->
                @if (session('error'))
                    <div class="mb-3.5 p-3 rounded-xl bg-rose-950/60 border border-rose-700/60 flex items-start gap-2.5 text-xs text-rose-200">
                        <i class="fa-solid fa-circle-exclamation text-sm text-rose-400 shrink-0 mt-0.5"></i>
                        <div class="font-bold flex-1">{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-3.5 p-3 rounded-xl bg-rose-950/60 border border-rose-700/60 flex items-start gap-2.5 text-xs text-rose-200">
                        <i class="fa-solid fa-circle-exclamation text-sm text-rose-400 shrink-0 mt-0.5"></i>
                        <div class="font-bold space-y-0.5 flex-1">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login') }}" method="POST" id="mobileLoginForm" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="mobile_theme_scheme" id="selected_mobile_theme_scheme" value="">

                    <!-- Username / NIK / Email -->
                    <div class="space-y-1">
                        <label for="id_user" class="text-[11.5px] font-bold text-slate-300 block">
                            Username / NIK / Email
                        </label>
                        <div class="input-group-lamp">
                            <input type="text"
                                   name="id_user"
                                   id="id_user"
                                   value="{{ old('id_user') }}"
                                   placeholder="Masukkan NIK atau Email Anda"
                                   required
                                   autocomplete="username"
                                   class="input-field-lamp">
                            <i class="ti ti-user input-icon-left"></i>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-[11.5px] font-bold text-slate-300">
                                Kata Sandi
                            </label>
                            <a href="https://wa.me/6285861094485?text=Halo%20Admin%20ZhanSoft%2C%20saya%20lupa%20password%20akun%20aplikasi%20SMATT3"
                               target="_blank"
                               class="text-[11px] font-bold hover:underline"
                               style="color: var(--lamp-glow);">
                                Lupa Password?
                            </a>
                        </div>
                        <div class="input-group-lamp">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   placeholder="Masukkan kata sandi"
                                   required
                                   autocomplete="current-password"
                                   class="input-field-lamp">
                            <i class="ti ti-lock input-icon-left"></i>
                            <button type="button"
                                    id="btnTogglePassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white transition cursor-pointer"
                                    tabindex="-1">
                                <i class="ti ti-eye text-lg" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox"
                                   name="remember"
                                   id="remember"
                                   class="w-4 h-4 rounded border-slate-600 bg-slate-800 focus:ring-0 cursor-pointer"
                                   style="accent-color: var(--lamp-glow);">
                            <span class="text-xs text-slate-300 font-semibold">Ingat Saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-1">
                        <button type="submit"
                                id="btnSubmitLogin"
                                class="btn-lamp-submit w-full h-11 rounded-xl text-white font-extrabold text-sm flex items-center justify-center gap-2 cursor-pointer">
                            <span id="btnSubmitText">Masuk Sekarang</span>
                            <i class="ti ti-arrow-right text-lg" id="btnSubmitIcon"></i>
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="relative flex py-3.5 items-center">
                    <div class="flex-grow border-t border-white/10"></div>
                    <span class="flex-shrink mx-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                        Butuh Bantuan?
                    </span>
                    <div class="flex-grow border-t border-white/10"></div>
                </div>

                <!-- WhatsApp Help Button -->
                <a href="https://wa.me/6285861094485?text=Halo%20Admin%20ZhanSoft%2C%20saya%20butuh%20bantuan%20terkait%20login%20aplikasi%20SMATT3"
                   target="_blank"
                   class="btn-wa-help w-full py-2.5 px-4 rounded-xl flex items-center justify-center gap-2 text-xs font-extrabold no-underline">
                    <i class="fa-brands fa-whatsapp text-base text-[#25D366]"></i>
                    <span>Get Help (Hubungi WhatsApp)</span>
                </a>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="safe-bottom px-5 pt-2 pb-3 text-center relative z-10">
        <p class="text-[10.5px] text-slate-500 font-medium">
            Aplikasi ini dikembangkan oleh
            <a href="https://www.instagram.com/amn4ll/?utm_source=ig_web_button_share_sheet"
               target="_blank"
               class="font-bold inline-flex items-center gap-1 hover:underline ml-0.5"
               style="color: var(--lamp-glow);">
                <i class="fa-brands fa-instagram text-xs"></i>
                <span>ZhanSoft - Amnal</span>
            </a>
        </p>
    </footer>

    <!-- Interactive Lamp, Audio & Per-User Theme Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme palettes matching Globalprovider & layouts/mobile/theme.blade.php
            const LAMP_THEMES = {
                orange: {
                    label: 'Amber',
                    primary: '#e65100',
                    light: '#f57c00',
                    dark: '#993600',
                    rgb: '230, 81, 0',
                    glow: '#fbbf24',
                    glowRgb: '251, 191, 36'
                },
                green: {
                    label: 'Emerald',
                    primary: '#32745e',
                    light: '#58907D',
                    dark: '#1e4b3d',
                    rgb: '50, 116, 94',
                    glow: '#34d399',
                    glowRgb: '52, 211, 153'
                },
                blue: {
                    label: 'Ocean',
                    primary: '#0d47a1',
                    light: '#1976d2',
                    dark: '#072a60',
                    rgb: '13, 71, 161',
                    glow: '#60a5fa',
                    glowRgb: '96, 165, 250'
                },
                red: {
                    label: 'Crimson',
                    primary: '#b71c1c',
                    light: '#d32f2f',
                    dark: '#7f1212',
                    rgb: '183, 28, 28',
                    glow: '#f87171',
                    glowRgb: '248, 113, 113'
                },
                purple: {
                    label: 'Royal',
                    primary: '#4a148c',
                    light: '#7b1fa2',
                    dark: '#2c0b54',
                    rgb: '74, 20, 140',
                    glow: '#c084fc',
                    glowRgb: '192, 132, 252'
                },
                rose: {
                    label: 'Blossom',
                    primary: '#be185d',
                    light: '#ec4899',
                    dark: '#831843',
                    rgb: '190, 24, 93',
                    glow: '#f472b6',
                    glowRgb: '244, 114, 182'
                },
                dark: {
                    label: 'Midnight',
                    primary: '#0f172a',
                    light: '#334155',
                    dark: '#020617',
                    rgb: '15, 23, 42',
                    glow: '#94a3b8',
                    glowRgb: '148, 163, 184'
                }
            };

            const body = document.body;
            const lampStage = document.getElementById('lampStage');
            const deskLampSvg = document.getElementById('deskLampSvg');
            const lampCordGroup = document.getElementById('lampCordGroup');
            const cordLine = document.getElementById('cordLine');
            const cordBead = document.getElementById('cordBead');
            const btnToggleLampPill = document.getElementById('btnToggleLampPill');
            const lampStatusDot = document.getElementById('lampStatusDot');
            const lampStatusText = document.getElementById('lampStatusText');
            const swatches = document.querySelectorAll('.color-swatch');
            const hiddenThemeInput = document.getElementById('selected_mobile_theme_scheme');
            const activeThemeBadge = document.getElementById('activeThemeBadge');
            const metaThemeColor = document.getElementById('metaThemeColor');

            let isLampOn = false;
            let audioCtx = null;

            // Play Web Audio API click + warm hum sound
            function playLampSound(turningOn) {
                try {
                    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContextClass) return;
                    if (!audioCtx) {
                        audioCtx = new AudioContextClass();
                    }
                    if (audioCtx.state === 'suspended') {
                        audioCtx.resume();
                    }

                    const now = audioCtx.currentTime;

                    // Crisp mechanical switch click
                    const clickOsc = audioCtx.createOscillator();
                    const clickGain = audioCtx.createGain();
                    clickOsc.type = 'triangle';
                    clickOsc.frequency.setValueAtTime(turningOn ? 900 : 520, now);
                    clickOsc.frequency.exponentialRampToValueAtTime(120, now + 0.035);
                    clickGain.gain.setValueAtTime(0.18, now);
                    clickGain.gain.exponentialRampToValueAtTime(0.001, now + 0.04);
                    clickOsc.connect(clickGain);
                    clickGain.connect(audioCtx.destination);
                    clickOsc.start(now);
                    clickOsc.stop(now + 0.045);

                    // Warm electrical hum when turning ON
                    if (turningOn) {
                        const hum = audioCtx.createOscillator();
                        const humGain = audioCtx.createGain();
                        hum.type = 'sine';
                        hum.frequency.setValueAtTime(120, now + 0.05);
                        humGain.gain.setValueAtTime(0.001, now);
                        humGain.gain.linearRampToValueAtTime(0.06, now + 0.08);
                        humGain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);

                        hum.connect(humGain);
                        humGain.connect(audioCtx.destination);
                        hum.start(now + 0.05);
                        hum.stop(now + 0.38);
                    }
                } catch (e) {
                    // Ignore audio errors if autoplay policy restricts
                }
            }

            // Apply lamp & app theme scheme
            function applyLampTheme(scheme, persist = false) {
                const theme = LAMP_THEMES[scheme] || LAMP_THEMES.orange;
                const root = document.documentElement;

                root.style.setProperty('--theme-primary', theme.primary);
                root.style.setProperty('--theme-light', theme.light);
                root.style.setProperty('--theme-dark', theme.dark);
                root.style.setProperty('--theme-rgb', theme.rgb);
                root.style.setProperty('--lamp-glow', theme.glow);
                root.style.setProperty('--lamp-glow-rgb', theme.glowRgb);

                if (metaThemeColor) metaThemeColor.setAttribute('content', theme.primary);
                if (activeThemeBadge) activeThemeBadge.textContent = theme.label;

                swatches.forEach(s => {
                    s.classList.toggle('active', s.dataset.scheme === scheme);
                });

                if (isLampOn && lampStatusDot) {
                    lampStatusDot.style.backgroundColor = theme.glow;
                    lampStatusDot.style.boxShadow = `0 0 8px ${theme.glow}`;
                }

                if (persist) {
                    if (hiddenThemeInput) hiddenThemeInput.value = scheme;
                    localStorage.setItem('smatt_user_mobile_scheme', scheme);
                    localStorage.setItem('smatt_theme', scheme === 'dark' ? 'dark' : 'light');
                    document.cookie = `smatt_user_mobile_scheme=${scheme}; path=/; max-age=31536000; SameSite=Lax`;
                }
            }

            // Toggle Lamp ON / OFF
            function setLampState(on, playSound = true) {
                isLampOn = on;
                body.classList.toggle('lamp-on', on);

                if (lampStatusText) {
                    lampStatusText.textContent = on ? 'Lampu Menyala' : 'Lampu Mati';
                }
                if (lampStatusDot) {
                    const activeScheme = localStorage.getItem('smatt_user_mobile_scheme') || '{{ $initialScheme }}';
                    const theme = LAMP_THEMES[activeScheme] || LAMP_THEMES.orange;
                    lampStatusDot.style.backgroundColor = on ? theme.glow : '#64748b';
                    lampStatusDot.style.boxShadow = on ? `0 0 8px ${theme.glow}` : 'none';
                }

                if (playSound) {
                    playLampSound(on);
                }
            }

            // Initialize saved user/server theme
            const savedUserScheme = localStorage.getItem('smatt_user_mobile_scheme') || '{{ $initialScheme }}';
            applyLampTheme(LAMP_THEMES[savedUserScheme] ? savedUserScheme : 'orange', false);

            // Auto-open lamp if there are validation or session errors
            const hasErrors = {{ ($errors->any() || session('error')) ? 'true' : 'false' }};
            if (hasErrors) {
                setLampState(true, false);
            }

            // Pull-cord drag & tap physics
            let isDraggingCord = false;
            let startY = 0;
            let currentPull = 0;

            function updateCordVisual(pullPx) {
                const clamped = Math.max(0, Math.min(28, pullPx));
                if (cordLine) cordLine.setAttribute('y2', String(112 + clamped));
                if (cordBead) cordBead.setAttribute('cy', String(115 + clamped));
            }

            function startCordDrag(clientY) {
                isDraggingCord = true;
                startY = clientY;
                currentPull = 0;
            }

            function moveCordDrag(clientY) {
                if (!isDraggingCord) return;
                currentPull = Math.max(0, clientY - startY);
                updateCordVisual(currentPull);
            }

            function endCordDrag() {
                if (!isDraggingCord) return;
                isDraggingCord = false;
                updateCordVisual(0);
                // Toggle if pulled >= 10px or tapped cleanly
                setLampState(!isLampOn, true);
            }

            if (lampCordGroup) {
                lampCordGroup.addEventListener('mousedown', (e) => {
                    e.stopPropagation();
                    startCordDrag(e.clientY);
                });
                lampCordGroup.addEventListener('touchstart', (e) => {
                    e.stopPropagation();
                    if (e.touches[0]) startCordDrag(e.touches[0].clientY);
                }, { passive: true });
            }

            window.addEventListener('mousemove', (e) => moveCordDrag(e.clientY));
            window.addEventListener('touchmove', (e) => {
                if (isDraggingCord && e.touches[0]) moveCordDrag(e.touches[0].clientY);
            }, { passive: true });

            window.addEventListener('mouseup', endCordDrag);
            window.addEventListener('touchend', endCordDrag);

            // Also allow tapping anywhere on the lamp SVG (outside cord) to toggle
            if (deskLampSvg) {
                deskLampSvg.addEventListener('click', (e) => {
                    if (e.target.closest('#lampCordGroup')) return;
                    setLampState(!isLampOn, true);
                });
            }

            if (btnToggleLampPill) {
                btnToggleLampPill.addEventListener('click', () => {
                    setLampState(!isLampOn, true);
                });
            }

            // Color swatch click handler
            swatches.forEach(swatch => {
                swatch.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const scheme = swatch.dataset.scheme;
                    applyLampTheme(scheme, true);
                    if (!isLampOn) {
                        setLampState(true, true);
                    } else {
                        playLampSound(false);
                    }
                });
            });

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
