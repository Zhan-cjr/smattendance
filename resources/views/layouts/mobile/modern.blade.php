<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="{{ $t['primary'] }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    {{-- Template CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/template/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/styleform.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />


    <style>
        :root {
            --color-nav: {{ $t['primary'] }};
            --color-nav-active: {{ $t['primary_light'] }};
            --bg-indicator: {{ $t['primary'] }};
            --color-nav-hover: {{ $t['primary_light'] }};
            --bg-nav: #ffffff;
        }
        html { background: {{ $t['primary'] }}; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif !important;
            background: {{ $t['bg_body'] }} !important;
            min-height: 100vh;
            -webkit-tap-highlight-color: transparent;
            color: #1e293b;
        }

        /* PWA Header handling */
        header { 
            padding-top: env(safe-area-inset-top);
            background: {{ $t['primary'] }};
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
        }

        /* Override template resets that conflict */
        @keyframes shimmer {
            0% { background-position: -400px 0; }
            100% { background-position: 400px 0; }
        }
        .sk {
            background: linear-gradient(90deg,
                #e2e8f0 0%,
                #f1f5f9 40%,
                #f8fafc 50%,
                #f1f5f9 60%,
                #e2e8f0 100%);
            background-size: 800px 100%;
            animation: shimmer 1.5s infinite linear;
        }

        .press { transition: transform 0.15s ease; }
        .press:active { transform: scale(0.97); }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.3s ease forwards; opacity: 0; }
        ::-webkit-scrollbar { width: 0; height: 0; }
        
        /* Global skeleton styles */
        .skeleton-avatar { width: 45px; height: 45px; border-radius: 12px; }
        .skeleton-text { height: 12px; border-radius: 4px; }

        .air-datepicker { font-family: 'Inter', sans-serif !important; border-radius: 16px !important; border: none !important; box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important; }
        .air-datepicker-cell.-selected- { background: {{ $t['primary'] }} !important; }
        .air-datepicker-cell.-current- { color: {{ $t['primary'] }} !important; }
        .air-datepicker-button { color: {{ $t['primary'] }} !important; }
    </style>

    @stack('mystyle')
</head>
<body>
    <header>
        <div class="flex items-center justify-between px-4 h-14">
            <div class="left">
                @yield('header_left')
            </div>
            <h1 class="text-[14px] font-bold text-white tracking-wide">@yield('title')</h1>
            <div class="right w-8">
                @yield('header_right')
            </div>
        </div>
    </header>

    <main class="pt-[calc(4rem+env(safe-area-inset-top))] pb-24 px-3 max-w-lg mx-auto">
        @yield('content')
    </main>

    @include('layouts.mobile.bottomNav')

    {{-- Core JS dependencies from old layout --}}
    <script src="{{ asset('assets/template/js/lib/popper.min.js') }}"></script>
    <script src="{{ asset('assets/template/js/lib/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/template/js/base.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" defer></script>

    {{-- Session Flash Notifications --}}
    <style>.toast-bottom-full-width { bottom: 5rem }</style>
    @if ($message = Session::get('success'))
        <script>
            toastr.options.showEasing = 'swing'; toastr.options.hideEasing = 'linear';
            toastr.options.progressBar = true; toastr.options.positionClass = 'toast-bottom-full-width';
            toastr.success("Berhasil", "{{ $message }}", { timeOut: 3000 });
        </script>
    @endif
    @if ($message = Session::get('error'))
        <script>
            toastr.options.showEasing = 'swing'; toastr.options.hideEasing = 'linear';
            toastr.options.progressBar = true; toastr.options.positionClass = 'toast-bottom-full-width';
            toastr.error("Gagal", "{{ $message }}", { timeOut: 3000 });
        </script>
    @endif
    @if ($message = Session::get('warning'))
        <script>
            toastr.options.showEasing = 'swing'; toastr.options.hideEasing = 'linear';
            toastr.options.progressBar = true;
            toastr.warning("Warning", "{{ $message }}", { timeOut: 3000 });
        </script>
    @endif
    @if ($errors->any())
        @php $err = ''; @endphp
        @foreach ($errors->all() as $error) @php $err .= $error; @endphp @endforeach
        <script>
            toastr.options.showEasing = 'swing'; toastr.options.hideEasing = 'linear';
            toastr.options.progressBar = true;
            toastr.error("Gagal", "{{ $err }}", { timeOut: 3000 });
        </script>
    @endif

    @stack('myscript')
</body>
</html>
