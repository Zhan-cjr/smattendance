<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kiosk Presensi</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/logo/' . ($active_colors['logo'] ?? 'logo.png')) }}" sizes="32x32">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: {{ $active_colors['primary'] ?? '#32745e' }};
            --primary-rgb: {{ $active_colors['rgb'] ?? '50, 116, 94' }};
            --secondary: {{ $active_colors['secondary'] ?? '#3ab58c' }};
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f0f2f5;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }
    </style>
    @stack('mystyle')
</head>
<body>
    @yield('content')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    @stack('myscript')
</body>
</html>
