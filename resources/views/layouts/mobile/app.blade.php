<!doctype html>
<html lang="en">

@php
    $scheme = $general_setting->mobile_theme_scheme ?? 'green';
    $colors = [
        'green' => [
            'bg_body' => '#dff9fb',
            'bg_nav' => '#ffffff',
            'color_nav' => '#32745e',
            'color_nav_active' => '#58907D',
            'bg_indicator' => '#32745e',
            'color_nav_hover' => '#3ab58c',
        ],
        'blue' => [
            'bg_body' => '#e3f2fd',
            'bg_nav' => '#ffffff',
            'color_nav' => '#0d47a1',
            'color_nav_active' => '#1976d2',
            'bg_indicator' => '#0d47a1',
            'color_nav_hover' => '#2196f3',
        ],
        'red' => [
            'bg_body' => '#ffebee',
            'bg_nav' => '#ffffff',
            'color_nav' => '#b71c1c',
            'color_nav_active' => '#d32f2f',
            'bg_indicator' => '#b71c1c',
            'color_nav_hover' => '#ef5350',
        ],
        'purple' => [
            'bg_body' => '#f3e5f5',
            'bg_nav' => '#ffffff',
            'color_nav' => '#4a148c',
            'color_nav_active' => '#7b1fa2',
            'bg_indicator' => '#4a148c',
            'color_nav_hover' => '#ab47bc',
        ],
        'orange' => [
            'bg_body' => '#fff3e0',
            'bg_nav' => '#ffffff',
            'color_nav' => '#e65100',
            'color_nav_active' => '#f57c00',
            'bg_indicator' => '#e65100',
            'color_nav_hover' => '#ff9800',
        ],
        'dark' => [
            'bg_body' => '#121212',
            'bg_nav' => '#1e1e1e',
            'color_nav' => '#e0e0e0', // Light text
            'color_nav_active' => '#bb86fc', // Purple accent
            'bg_indicator' => '#bb86fc',
            'color_nav_hover' => '#cf6679',
        ],
    ];
    $c = $colors[$scheme] ?? $colors['green'];

    if (!function_exists('hexToRgb')) {
        function hexToRgb($hex)
        {
            $hex = str_replace('#', '', $hex);
            if (strlen($hex) == 3) {
                $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
            } else {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
            }
            return "$r, $g, $b";
        }
    }
@endphp
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="{{ $c['color_nav'] }}">
    <title>Dashboard</title>
    <meta name="description" content="Mobilekit HTML Mobile UI Kit">
    <meta name="keywords" content="bootstrap 4, mobile template, cordova, phonegap, mobile, html" />

    <!-- DNS Prefetch untuk external resources -->
    <link rel="dns-prefetch" href="https://ajax.googleapis.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://unpkg.com">
    <link rel="dns-prefetch" href="https://cdn.amcharts.com">

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/styleform.css') }}">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />


    <style>
        :root {
            --bg-body: {{ $c['bg_body'] }};
            --bg-nav: {{ $c['bg_nav'] }};
            --color-nav: {{ $c['color_nav'] }};
            --color-nav-rgb: {{ hexToRgb($c['color_nav']) }};
            --color-nav-active: {{ $c['color_nav_active'] }};
            --color-nav-active-rgb: {{ hexToRgb($c['color_nav_active']) }};
            --bg-indicator: {{ $c['bg_indicator'] }};
            --color-nav-hover: {{ $c['color_nav_hover'] }};
        }

        /* Apply background to body if needed, currently set in :root usually consumed by body style */
        body {
            background-color: var(--bg-body);
        }

        /* Smart scroll fix: Hanya apply jika ada #content-section (halaman dengan header fixed) */
        #content-section {
            min-height: 0;
            height: auto;
            max-height: calc(100vh - 70px - 80px);
            overflow-y: auto;
            overflow-x: hidden;
            box-sizing: border-box;
        }

        /* Dynamic Theme Overrides */
        .bg-primary {
            background-color: var(--color-nav) !important;
        }

        .btn-primary {
            background-color: var(--color-nav) !important;
            border-color: var(--color-nav) !important;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: var(--color-nav-active) !important;
            border-color: var(--color-nav-active) !important;
        }

        .text-primary {
            color: var(--color-nav) !important;
        }

        .historicontent {
            display: flex;
            justify-content: space-between;
            padding: 20px
        }

        .historibordergreen {
            border: 1px solid var(--color-nav) !important;
        }

        .historiborderred {
            border: 1px solid rgb(171, 18, 18);
        }

        /* FAB Button Overrides */
        .fab-button .fab {
            background-color: var(--color-nav) !important;
        }

        .fab-button .fab:hover,
        .fab-button .fab:active {
            background-color: var(--color-nav-active) !important;
        }

        .fab-button .dropdown-menu .dropdown-item {
            background-color: var(--color-nav) !important;
        }

        .fab-button .dropdown-menu .dropdown-item:hover,
        .fab-button .dropdown-menu .dropdown-item:active {
            background-color: var(--color-nav-active) !important;
        }

        /* Nav Tabs Overrides */
        .nav-tabs.style1 .nav-item .nav-link.active {
            color: var(--color-nav) !important;
        }

        .nav-tabs.style1 .nav-item .nav-link {
            color: var(--color-nav);
            opacity: 0.7;
        }

        /* Card Text Override */
        .card-body {
            color: var(--color-nav);
        }

        .historidetail1 {
            display: flex;
        }

        .historidetail2 h4 {
            margin-bottom: 0;
        }



        .datepresence {
            margin-left: 10px;
        }

        .datepresence h4 {
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 0;
        }

        .timepresence {
            font-size: 14px;
        }

        /* ===================================
           PRELOADER / LOADING ANIMATION STYLES
           =================================== */

        /* Preloader Overlay */
        .preloader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(3px);
        }

        .preloader-overlay.active {
            display: flex;
        }

        /* Preloader Container - REMOVED */
        .preloader-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            animation: slideInUp 0.4s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Spinner Styles */
        .spinner {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0;
        }

        /* Modern Circular Spinner */
        .spinner-circular {
            width: 80px;
            height: 80px;
            border: 5px solid rgba(255, 255, 255, 0.2);
            border-top: 5px solid white;
            border-right: 5px solid white;
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Dots Spinner (Alternative) */
        .spinner-dots {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60px;
        }

        .spinner-dots .dot {
            width: 12px;
            height: 12px;
            margin: 0 6px;
            background-color: var(--color-nav);
            border-radius: 50%;
            animation: bounce 1.4s infinite ease-in-out both;
        }

        .spinner-dots .dot:nth-child(1) {
            animation-delay: -0.32s;
        }

        .spinner-dots .dot:nth-child(2) {
            animation-delay: -0.16s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0);
                opacity: 0.5;
            }

            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Pulse Spinner */
        .spinner-pulse {
            width: 60px;
            height: 60px;
            margin: 0 auto;
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.5;
            }

            100% {
                transform: scale(0.8);
                opacity: 1;
            }
        }

        /* Loading Text */
        .preloader-text {
            font-size: 16px;
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin: 0;
            text-align: center;
        }

        .preloader-text.animated::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
        }

        @keyframes dots {

            0%,
            20% {
                content: '';
            }

            40% {
                content: '.';
            }

            60% {
                content: '..';
            }

            80%,
            100% {
                content: '...';
            }
        }

        /* Preloader Subtitle */
        .preloader-subtitle {
            display: none;
        }

        /* Different Spinner Types */
        .spinner-bars {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 60px;
            margin: 0 auto;
        }

        .spinner-bars .bar {
            position: absolute;
            width: 4px;
            height: 24px;
            background-color: var(--color-nav);
            border-radius: 2px;
            left: 50%;
            top: 50%;
            margin-left: -2px;
            margin-top: -12px;
            transform-origin: 50% 28px;
            animation: spinBars 1.2s linear infinite;
        }

        .spinner-bars .bar:nth-child(1) {
            animation-delay: -0.975s;
        }

        .spinner-bars .bar:nth-child(2) {
            animation-delay: -0.850s;
            transform: rotate(30deg);
        }

        .spinner-bars .bar:nth-child(3) {
            animation-delay: -0.725s;
            transform: rotate(60deg);
        }

        .spinner-bars .bar:nth-child(4) {
            animation-delay: -0.600s;
            transform: rotate(90deg);
        }

        .spinner-bars .bar:nth-child(5) {
            animation-delay: -0.475s;
            transform: rotate(120deg);
        }

        .spinner-bars .bar:nth-child(6) {
            animation-delay: -0.350s;
            transform: rotate(150deg);
        }

        .spinner-bars .bar:nth-child(7) {
            animation-delay: -0.225s;
            transform: rotate(180deg);
        }

        .spinner-bars .bar:nth-child(8) {
            animation-delay: -0.100s;
            transform: rotate(210deg);
        }

        .spinner-bars .bar:nth-child(9) {
            animation-delay: 0.025s;
            transform: rotate(240deg);
        }

        .spinner-bars .bar:nth-child(10) {
            animation-delay: 0.150s;
            transform: rotate(270deg);
        }

        .spinner-bars .bar:nth-child(11) {
            animation-delay: 0.275s;
            transform: rotate(300deg);
        }

        .spinner-bars .bar:nth-child(12) {
            animation-delay: 0.400s;
            transform: rotate(330deg);
        }

        @keyframes spinBars {
            0% {
                opacity: 0.85;
            }

            50% {
                opacity: 0.25;
            }

            100% {
                opacity: 0.85;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .spinner {
                width: 70px;
                height: 70px;
            }
            .spinner-circular {
                width: 70px;
                height: 70px;
                border-width: 4px;
            }
            .preloader-text {
                font-size: 14px;
            }
            #appCapsule {
                padding-bottom: 80px !important;
                min-height: calc(100vh - 70px - 80px);
            }
        }

        @media (max-width: 400px) {
            html, body {
                font-size: 13px;
            }
            #appCapsule {
                padding-bottom: 90px !important;
            }
            .nav-tabs.style1 .nav-item .nav-link {
                font-size: 13px;
            }
        }

        /* Ensure bottom nav always visible and not overlapped */
        #appCapsule {
            padding-bottom: 80px;
            box-sizing: border-box;
            min-height: calc(100vh - 70px - 80px);
        }
    </style>
    {{-- <style>
        .selectmaterialize,
        textarea {
            display: block;
            background-color: transparent !important;
            border: 0px !important;
            border-bottom: 1px solid #9e9e9e !important;
            border-radius: 0 !important;
            outline: none !important;
            height: 3rem !important;
            width: 100% !important;
            font-size: 16px !important;
            margin: 0 0 8px 0 !important;
            padding: 0 !important;
            color: #495057;

        }

        textarea {
            height: 80px !important;
            color: #495057 !important;
            padding: 20px !important;
        }
    </style> --}}
</head>

<body>



    @yield('header')

    <!-- App Capsule -->
    <div id="appCapsule">
        @yield('content')
    </div>
    <!-- * App Capsule -->


    @include('layouts.mobile.bottomNav')


    @include('layouts.mobile.script')

    <!-- Preloader Component -->
    <div class="preloader-overlay" id="preloaderOverlay">
        <div class="preloader-container">
            <!-- Spinner -->
            <div class="spinner-circular"></div>
            <!-- Text -->
            <div class="preloader-text animated">Mohon tunggu</div>
        </div>
    </div>

    @include('components.push-notification-banner')

</body>

</html>
