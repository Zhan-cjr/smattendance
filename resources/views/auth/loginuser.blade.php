<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign in & Sign up Form</title>

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="SM-Attendance GPS V2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SM-Attendance">
    <meta name="description" content="Aplikasi Presensi GPS untuk Karyawan">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#696cff">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/assets/img/icons/pwa/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/assets/img/icons/pwa/icon-512x512.png">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <link rel="stylesheet" href="{{ asset('assets/login/css/style.css') }}" />
    <style>
        :root {
            /* Dynamic Theme Colors */
            --theme-color-1: {{ $general_setting->theme_color_1 ?? '#053b22' }};
            --theme-color-2: {{ $general_setting->theme_color_2 ?? '#0b6a3a' }};
        }

        .sign-btn {
            background-color: var(--theme-color-1) !important;
        }

        .sign-btn:hover {
            background-color: var(--theme-color-2) !important;
        }

        .bullets span.active {
            background-color: var(--theme-color-1) !important;
        }

        .carousel {
            background: var(--theme-color-1) !important;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            animation: slideIn 0.5s ease-out;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .text-group h2 {
            color: #ffffff !important;
        }
    </style>

</head>

<body>
    <main>
        <div class="box">
            <div class="inner-box">
                <div class="forms-wrap">
                    <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="logo">
                            @if (!empty($general_setting->logo) && Storage::disk('public')->exists('logo/' . $general_setting->logo))
                                <img src="{{ asset('storage/logo/' . $general_setting->logo) }}" alt="Company Logo" style="height: auto; width: 80px; margin-bottom: 20px;" />
                            @else
                                <img src="{{ asset('assets/login/images/logoweb-1.png') }}" alt="easyclass" />
                            @endif
                            <h4>SMATT V3</h4>
                        </div>

                        <div class="heading">
                            <h3>Assalamualaikum!</h3>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </div>
                        @endif

                        <div class="actual-form">
                            <div class="input-wrap">
                                <input type="text" minlength="4" class="input-field @error('id_user') is-invalid @enderror" name="id_user"
                                    value="{{ old('id_user') }}" autocomplete="off" placeholder="Username / Email" required />
                                {{-- @error('id_user')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror --}}
                            </div>

                            <div class="input-wrap">
                                <input type="password" minlength="4" name="password" class="input-field @error('password') is-invalid @enderror"
                                    autocomplete="off" placeholder="Password" required />
                                {{-- @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror --}}
                            </div>

                            <div class="checkbox-wrap">
                                <input type="checkbox" id="remember" name="remember" style="margin-right: 8px; width: 16px; height: 16px;">
                                <label for="remember" style="color: #666; font-size: 14px; cursor: pointer; margin-left: 20px;">Remember Me</label>
                            </div>

                            <input type="submit" value="Sign In" class="sign-btn" />

                            <p class="text">
                                Butuh bantuan login atau lupa password?
                                <a href="https://wa.me/6285861094485?text=Halo%20Admin%20ZhanSoft%2C%20saya%20butuh%20bantuan%20terkait%20login%20aplikasi%20SMATT3" target="_blank" style="color: #25D366; font-weight: bold;">Get help (WhatsApp)</a>
                            </p>

                            <div style="text-align: center; margin-top: 20px; font-size: 11px; color: #888;">
                                Dikembangkan oleh <a href="https://www.instagram.com/amn4ll/?utm_source=ig_web_button_share_sheet" target="_blank" style="color: var(--theme-color-1); font-weight: bold; text-decoration: none;">ZhanSoft - Amnal</a>
                            </div>

                        </div>
                    </form>

                </div>

                <div class="carousel">
                    <div class="images-wrapper">
                        <img src="./img/image1.png" class="image img-1 show" alt="" />
                        <img src="./img/image2.png" class="image img-2" alt="" />
                        <img src="./img/image3.png" class="image img-3" alt="" />
                    </div>

                    <div class="text-slider">
                        <div class="text-wrap">
                            <div class="text-group">
                                <h2>Awali dengan Bismillah!</h2>
                                <h2>InsyaAllah menjadi Berkah!</h2>
                                <h2>Jangan lupa Absen!</h2>
                            </div>
                        </div>

                        <div class="bullets">
                            <span class="active" data-value="1"></span>
                            <span data-value="2"></span>
                            <span data-value="3"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Javascript file -->
    <script src="{{ asset('assets/login/scripts/app.js') }}"></script>

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>

    <!-- PWA Install Prompt - Only on Login Page -->
    @include('components.pwa-install-prompt')
</body>

</html>
