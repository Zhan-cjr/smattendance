<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f8fafc]">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('titlepage', 'Dashboard') | {{ $general_setting->nama_aplikasi ?? 'GAWE' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('logo.png') }}" />

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Vite Compiled Tailwind CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tabler Icons (Local Asset & CDN Webfont) -->
    <link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <!-- Leaflet Map CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Tailwind CDN for full utility class runtime support in dynamically loaded AJAX modals -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    <script>
        // Dark Mode dinonaktifkan: Selalu pastikan menggunakan light mode
        (function() {
            try {
                document.documentElement.classList.remove('dark');
                if (localStorage.getItem('v2_theme') === 'dark') {
                    localStorage.setItem('v2_theme', 'light');
                }
            } catch (e) {}
        })();
    </script>
    <!-- jQuery & Bootstrap Bundle -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set global modal default options IMMEDIATELY so any script uses static backdrop
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.Default = Object.assign(bootstrap.Modal.Default || {}, {
                backdrop: 'static',
                keyboard: false
            });
        }
        if (typeof jQuery !== 'undefined' && jQuery.fn.modal) {
            if (jQuery.fn.modal.Constructor && jQuery.fn.modal.Constructor.Default) {
                jQuery.fn.modal.Constructor.Default.backdrop = 'static';
                jQuery.fn.modal.Constructor.Default.keyboard = false;
            }
        }
    </script>

    <!-- Flatpickr Datepicker CSS & JS (Loaded AFTER jQuery with jQuery Plugin Adapter) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script>
        // Ensure jQuery plugin $.fn.flatpickr exists even if native flatpickr was loaded
        if (typeof jQuery !== 'undefined' && typeof flatpickr !== 'undefined') {
            jQuery.fn.flatpickr = function(options) {
                return this.each(function() {
                    flatpickr(this, options);
                });
            };
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Popper.js & Tippy.js (Modern Floating Tooltips) -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />

    <!-- Select2 4.1.0 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- jQuery Mask Plugin -->
    <script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>

    <!-- MaskMoney jQuery Plugin -->
    <script src="{{ asset('assets/vendor/js/jquery.maskMoney.js') }}"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('mystyle')
    @stack('style')

    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            @php
                $tc1 = $general_setting->theme_color_1 ?? '#0a5c41';
                $tc2 = $general_setting->theme_color_2 ?? '#117956';
                $hex = ltrim($tc1, '#');
                if (strlen($hex) == 3) {
                    $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                    $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                    $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
                } else if (strlen($hex) == 6) {
                    $r = hexdec(substr($hex, 0, 2));
                    $g = hexdec(substr($hex, 2, 2));
                    $b = hexdec(substr($hex, 4, 2));
                } else {
                    $r = 10; $g = 92; $b = 65;
                }
                $tc_rgb = "$r, $g, $b";
            @endphp
            --theme-color-1: {{ $tc1 }};
            --theme-color-2: {{ $tc2 }};
            --theme-color-rgb: {{ $tc_rgb }};
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f9;
        }

        /* Prevent FOUC on uninitialized Select2 elements across all pages */
        select.select2:not(.select2-hidden-accessible),
        select.select2-v2:not(.select2-hidden-accessible),
        select[class*="select2"]:not(.select2-hidden-accessible),
        select[class*="Select2"]:not(.select2-hidden-accessible) {
            display: block;
            width: 100%;
            height: 42px;
            min-height: 42px;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            padding: 0 2.25rem 0 0.875rem;
            font-size: 0.785rem;
            font-weight: 600;
            color: #0f172a;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25em 1.25em;
        }

        /* Responsive Layout Rules - Ensure Sidebar is 100% Floating Overlay on Mobile */
        @media (max-width: 1023.98px) {
            aside#v2Sidebar {
                position: fixed !important;
                top: 0 !important;
                bottom: 0 !important;
                left: 0 !important;
                z-index: 50 !important;
                max-width: 280px !important;
                width: 80% !important;
            }
            #v2MainContent {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                flex: 1 1 100% !important;
            }
        }
        @media (min-width: 1024px) {
            aside#v2Sidebar {
                position: static !important;
                transform: none !important;
                width: 16rem !important;
                flex-shrink: 0 !important;
            }
        }

        /* Essential Modal Engine Styles (Tailwind Compatibility & Smooth Transitions like siprenpas) */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1055;
            display: none;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
            outline: 0;
        }
        .modal.fade {
            transition: opacity 0.15s linear;
        }
        .modal.show {
            display: block !important;
        }
        .modal.fade .modal-dialog {
            transition: transform 0.2s ease-out;
            transform: translate(0, -20px);
        }
        .modal.show .modal-dialog {
            transform: none;
        }
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            width: 100vw;
            height: 100vh;
            background-color: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
        }
        .modal-backdrop.fade {
            opacity: 0;
            transition: opacity 0.15s linear;
        }
        .modal-backdrop.show {
            opacity: 1;
        }
        .modal-dialog {
            position: relative;
            width: auto;
            margin: 1.75rem auto;
            pointer-events: none;
            max-width: 540px;
            padding: 0 1rem;
        }
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 3.5rem);
        }
        .modal-dialog-scrollable {
            height: calc(100% - 3.5rem);
        }
        .modal-dialog-scrollable .modal-content {
            max-height: 100%;
            overflow: hidden;
        }
        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
        }
        .modal-sm { max-width: 380px; }
        .modal-md { max-width: 540px; }
        .modal-lg { max-width: 720px; }
        .modal-xl { max-width: 1140px; }
        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #ffffff;
            background-clip: padding-box;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
            outline: 0;
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
        }

        /* =========================================================
           FLATPICKR ULTRA-CLEAN MODERN TAILWIND DESIGN SYSTEM
           ========================================================= */
        .flatpickr-calendar {
            z-index: 999999 !important;
            width: 320px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 1.25rem !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(15, 23, 42, 0.04) !important;
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important;
            padding: 1rem !important;
            box-sizing: border-box !important;
            margin-top: 6px !important;
        }

        .flatpickr-calendar::before,
        .flatpickr-calendar::after {
            display: none !important;
        }

        .flatpickr-calendar.open {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Top Month / Year Navigation Header */
        .flatpickr-months {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            position: relative !important;
            background: transparent !important;
            padding: 0 0 0.75rem 0 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            margin-bottom: 0.75rem !important;
            height: auto !important;
            border-top-left-radius: 0 !important;
            border-top-right-radius: 0 !important;
        }

        .flatpickr-months .flatpickr-month {
            height: auto !important;
            background: transparent !important;
            color: #0f172a !important;
            fill: #0f172a !important;
            line-height: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: visible !important;
        }

        .flatpickr-current-month {
            position: static !important;
            width: auto !important;
            height: auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.35rem !important;
            padding: 0 !important;
            font-size: 0.95rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            transform: none !important;
            left: auto !important;
        }

        .flatpickr-current-month .cur-month {
            font-weight: 800 !important;
            color: #0f172a !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            appearance: none !important;
            -webkit-appearance: none !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            font-size: 0.875rem !important;
            padding: 0.35rem 0.65rem !important;
            cursor: pointer !important;
            outline: none !important;
            transition: all 0.15s ease !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months option {
            background: #ffffff !important;
            color: #0f172a !important;
            font-weight: 600 !important;
        }

        .flatpickr-current-month .numInputWrapper {
            width: 72px !important;
            display: inline-flex !important;
            align-items: center !important;
        }

        .flatpickr-current-month input.cur-year {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            color: #0f172a !important;
            font-weight: 700 !important;
            font-size: 0.875rem !important;
            padding: 0.35rem 0.5rem !important;
            text-align: center !important;
            outline: none !important;
            transition: all 0.15s ease !important;
        }

        .flatpickr-current-month input.cur-year:hover,
        .flatpickr-current-month input.cur-year:focus {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }

        .flatpickr-current-month .numInputWrapper span.arrowUp,
        .flatpickr-current-month .numInputWrapper span.arrowDown {
            display: none !important;
        }

        /* Prev / Next Navigation Arrows */
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            position: static !important;
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            color: #475569 !important;
            cursor: pointer !important;
            transition: all 0.15s ease-in-out !important;
            z-index: 10 !important;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            background: rgba(var(--theme-color-rgb, 10, 92, 65), 0.08) !important;
            border-color: var(--theme-color-1, #059669) !important;
            color: var(--theme-color-1, #059669) !important;
        }

        .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-months .flatpickr-next-month svg {
            width: 14px !important;
            height: 14px !important;
            stroke-width: 2 !important;
            fill: currentColor !important;
        }

        /* Days & Inner Container */
        .flatpickr-innerContainer {
            display: block !important;
            width: 100% !important;
        }

        .flatpickr-rContainer {
            width: 100% !important;
            display: block !important;
        }

        /* Weekdays Header */
        .flatpickr-weekdays {
            display: flex !important;
            width: 100% !important;
            background: transparent !important;
            margin-bottom: 0.35rem !important;
            height: auto !important;
            overflow: hidden !important;
        }

        .flatpickr-weekdaycontainer {
            display: flex !important;
            width: 100% !important;
            justify-content: space-between !important;
        }

        span.flatpickr-weekday {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex: 1 1 0% !important;
            width: 14.28% !important;
            height: 28px !important;
            color: #94a3b8 !important;
            font-size: 0.725rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.025em !important;
            background: transparent !important;
        }

        /* Days Grid */
        .flatpickr-days {
            width: 100% !important;
            display: block !important;
        }

        .dayContainer {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: space-between !important;
            box-sizing: border-box !important;
            padding: 0 !important;
            outline: 0 !important;
        }

        .flatpickr-day {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 38px !important;
            height: 38px !important;
            max-width: 38px !important;
            flex-basis: 14.28% !important;
            margin: 2px 0 !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            border-radius: 0.625rem !important;
            border: 1px solid transparent !important;
            background: transparent !important;
            cursor: pointer !important;
            transition: all 0.12s ease-in-out !important;
            line-height: 1 !important;
            box-sizing: border-box !important;
        }

        .flatpickr-day:hover {
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #e2e8f0 !important;
        }

        /* Today's Day */
        .flatpickr-day.today {
            border-color: var(--theme-color-1, #059669) !important;
            background: rgba(var(--theme-color-rgb, 10, 92, 65), 0.08) !important;
            color: var(--theme-color-1, #059669) !important;
            font-weight: 800 !important;
        }

        .flatpickr-day.today:hover {
            background: rgba(var(--theme-color-rgb, 10, 92, 65), 0.15) !important;
            color: var(--theme-color-1, #047857) !important;
        }

        /* Selected Day */
        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected:hover,
        .flatpickr-day.selected:focus {
            background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956)) !important;
            border-color: var(--theme-color-1, #0a5c41) !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            box-shadow: 0 4px 10px -1px rgba(var(--theme-color-rgb, 10, 92, 65), 0.4) !important;
        }

        /* In Range Days */
        .flatpickr-day.inRange {
            background: rgba(var(--theme-color-rgb, 10, 92, 65), 0.1) !important;
            border-color: rgba(var(--theme-color-rgb, 10, 92, 65), 0.2) !important;
            color: var(--theme-color-1, #065f46) !important;
            border-radius: 0 !important;
        }

        /* Outside Month Days */
        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #cbd5e1 !important;
            background: transparent !important;
            font-weight: 400 !important;
        }

        .flatpickr-day.prevMonthDay:hover,
        .flatpickr-day.nextMonthDay:hover {
            background: #f8fafc !important;
            color: #94a3b8 !important;
        }

        /* Sleek Modern Dark Tooltip */
        .tippy-box {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1) !important;
            padding: 3px 6px !important;
            letter-spacing: 0.01em !important;
        }
        /* SweetAlert2 Modal & Backdrop Layering */
        .swal2-container {
            z-index: 1000000 !important;
        }
        .swal2-popup {
            border-radius: 1rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .swal2-actions {
            margin-top: 1.25rem !important;
            gap: 0.5rem !important;
            width: 100% !important;
            justify-content: flex-end !important;
            padding: 0 0.5rem !important;
        }
        .swal2-styled.swal2-confirm {
            border-radius: 0.5rem !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            padding: 0.55rem 1.25rem !important;
            background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956)) !important;
            background-color: var(--theme-color-1, #0a5c41) !important;
            color: #ffffff !important;
            border: 1px solid var(--theme-color-1, #0a5c41) !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        .swal2-styled.swal2-cancel {
            border-radius: 0.5rem !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
            padding: 0.55rem 1.25rem !important;
            background: #f1f5f9 !important;
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        .swal2-styled.swal2-cancel:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* =========================================================
           V2 ULTRA-CLEAN TOGGLE SWITCH COMPONENT
           ========================================================= */
        .v2-switch {
            position: relative;
            display: inline-block;
            width: 38px;
            height: 22px;
            flex-shrink: 0;
            cursor: pointer;
            user-select: none;
            margin-bottom: 0;
        }

        .v2-switch input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        .v2-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 9999px;
        }

        .v2-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .v2-switch input:checked + .v2-slider {
            background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));
            background-color: var(--theme-color-1, #0a5c41);
        }

        .v2-switch input:checked + .v2-slider:before {
            transform: translateX(16px);
        }

        .v2-switch input:focus + .v2-slider {
            box-shadow: 0 0 0 2px rgba(10, 92, 65, 0.2);
        }

        /* Small Variant */
        .v2-switch.v2-switch-sm {
            width: 32px;
            height: 18px;
        }
        .v2-switch.v2-switch-sm .v2-slider:before {
            height: 12px;
            width: 12px;
            left: 3px;
            bottom: 3px;
        }
        .v2-switch.v2-switch-sm input:checked + .v2-slider:before {
            transform: translateX(14px);
        }

        /* Amber / Warning Variant */
        .v2-switch.v2-switch-amber input:checked + .v2-slider {
            background: #d97706 !important;
        }

        /* =========================================================
           SELECT2 V2 ULTRA-CLEAN & MODERN DESIGN SYSTEM
           Applies to both custom theme 'v2' and default select2 in V2 pages
           ========================================================= */
        .select2-container--v2,
        .select2-container--default {
            display: block !important;
            width: 100% !important;
        }

        /* Trigger Button / Input Box */
        .select2-container--v2 .select2-selection--single,
        .select2-container--default .select2-selection--single {
            position: relative !important;
            height: 42px !important;
            min-height: 42px !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.75rem !important;
            padding: 0 2.25rem 0 0.875rem !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.15s ease-in-out !important;
            outline: none !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04) !important;
        }

        .select2-container--v2.select2-container--open .select2-selection--single,
        .select2-container--v2.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--theme-color-1, #0a5c41) !important;
            box-shadow: 0 0 0 3px rgba(var(--theme-color-rgb, 10, 92, 65), 0.15) !important;
        }

        .select2-container--v2 .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0f172a !important;
            font-size: 0.785rem !important;
            font-weight: 600 !important;
            padding: 0 !important;
            line-height: normal !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .select2-container--v2 .select2-selection--single .select2-selection__placeholder,
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
            font-weight: 500 !important;
            font-size: 0.785rem !important;
        }

        .select2-container--v2 .select2-selection--single .select2-selection__arrow,
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            position: absolute !important;
            top: 0 !important;
            right: 0.75rem !important;
            width: 20px !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            pointer-events: none !important;
        }

        .select2-container--v2 .select2-selection--single .select2-selection__arrow b,
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            display: block !important;
            position: relative !important;
            top: auto !important;
            left: auto !important;
            margin: 0 !important;
            width: 7px !important;
            height: 7px !important;
            border: none !important;
            border-right: 2px solid #94a3b8 !important;
            border-bottom: 2px solid #94a3b8 !important;
            transform: rotate(45deg) translateY(-2px) !important;
            transition: transform 0.2s ease, border-color 0.2s ease !important;
        }

        .select2-container--v2.select2-container--open .select2-selection--single .select2-selection__arrow b,
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: var(--theme-color-1, #0a5c41) !important;
            transform: rotate(-135deg) translateY(-2px) !important;
        }

        .select2-container--v2 .select2-selection--single .select2-selection__clear,
        .select2-container--default .select2-selection--single .select2-selection__clear {
            position: absolute !important;
            right: 2.25rem !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #94a3b8 !important;
            font-size: 1.1rem !important;
            font-weight: 700 !important;
            margin: 0 !important;
            float: none !important;
            cursor: pointer !important;
            line-height: 1 !important;
        }

        .select2-container--v2 .select2-selection--single .select2-selection__clear:hover,
        .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            color: #ef4444 !important;
        }

        /* Modern Floating Dropdown Popup */
        .select2-dropdown,
        .select2-dropdown.select2-dropdown--v2 {
            border: 1px solid #e2e8f0 !important;
            border-radius: 1rem !important;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.08) !important;
            background-color: #ffffff !important;
            overflow: hidden !important;
            z-index: 1060 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            padding: 0.5rem !important;
            margin-top: 6px !important;
        }

        /* Search Box in Dropdown */
        .select2-search--dropdown {
            padding: 0.25rem !important;
            position: relative !important;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.625rem !important;
            padding: 0.55rem 0.875rem !important;
            font-size: 0.785rem !important;
            font-weight: 500 !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            outline: none !important;
            background: #f8fafc !important;
            color: #0f172a !important;
            width: 100% !important;
            box-sizing: border-box !important;
            transition: all 0.15s ease !important;
        }

        .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--theme-color-1, #0a5c41) !important;
            background: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(var(--theme-color-rgb, 10, 92, 65), 0.12) !important;
        }

        /* Results List & Options */
        .select2-results__options {
            max-height: 250px !important;
            padding: 0.35rem 0.15rem !important;
            margin: 0 !important;
            overflow-y: auto !important;
            scrollbar-width: thin !important;
            scrollbar-color: #cbd5e1 #f8fafc !important;
        }

        .select2-results__options::-webkit-scrollbar {
            width: 6px;
        }
        .select2-results__options::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }
        .select2-results__options::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .select2-results__options::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .select2-results__option {
            padding: 0.55rem 0.85rem !important;
            font-size: 0.785rem !important;
            font-weight: 600 !important;
            color: #334155 !important;
            border-radius: 0.625rem !important;
            margin-bottom: 3px !important;
            transition: all 0.12s ease-in-out !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            line-height: 1.35 !important;
        }

        .select2-results__option:last-child {
            margin-bottom: 0 !important;
        }

        /* Hover / Highlighted State */
        .select2-results__option--highlighted[aria-selected],
        .select2-results__option:hover {
            background-color: rgba(var(--theme-color-rgb, 10, 92, 65), 0.08) !important;
            color: var(--theme-color-1, #0a5c41) !important;
            transform: translateX(2px) !important;
        }

        /* Selected State */
        .select2-results__option[aria-selected="true"],
        .select2-results__option--selected {
            background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956)) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 6px rgba(var(--theme-color-rgb, 10, 92, 65), 0.25) !important;
            transform: none !important;
        }

        .select2-results__message {
            padding: 1rem !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            color: #94a3b8 !important;
            text-align: center !important;
        }

        /* Multiple Selection Styling */
        .select2-container--v2 .select2-selection--multiple,
        .select2-container--default .select2-selection--multiple {
            min-height: 42px !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 0.75rem !important;
            padding: 4px 8px !important;
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 4px !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04) !important;
            transition: all 0.15s ease-in-out !important;
        }

        .select2-container--v2.select2-container--open .select2-selection--multiple,
        .select2-container--v2.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--open .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: var(--theme-color-1, #0a5c41) !important;
            box-shadow: 0 0 0 3px rgba(var(--theme-color-rgb, 10, 92, 65), 0.15) !important;
        }

        .select2-container--v2 .select2-selection--multiple .select2-selection__rendered,
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }

        .select2-container--v2 .select2-selection--multiple .select2-selection__choice,
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: #ecfdf5 !important;
            border: 1px solid #a7f3d0 !important;
            border-radius: 0.5rem !important;
            color: #065f46 !important;
            font-size: 0.725rem !important;
            font-weight: 700 !important;
            padding: 2px 8px !important;
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        .select2-container--v2 .select2-selection--multiple .select2-selection__choice__remove,
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #059669 !important;
            margin-right: 4px !important;
            border: none !important;
            background: transparent !important;
            font-size: 13px !important;
            font-weight: bold !important;
            cursor: pointer !important;
            padding: 0 !important;
            transition: color 0.15s ease !important;
        }

        .select2-container--v2 .select2-selection--multiple .select2-selection__choice__remove:hover,
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ef4444 !important;
        }

        .select2-container--v2 .select2-search--inline,
        .select2-container--default .select2-search--inline {
            margin: 0 !important;
            padding: 0 !important;
            flex: 1 1 auto !important;
        }

        .select2-container--v2 .select2-search--inline .select2-search__field,
        .select2-container--default .select2-search--inline .select2-search__field {
            font-size: 0.785rem !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            color: #0f172a !important;
            margin: 0 !important;
            padding: 4px 6px !important;
            height: auto !important;
            width: 100% !important;
        }

        /* Select2 & Flatpickr Validation Error States */
        .select2-container--v2 .select2-selection.border-rose-500,
        .select2-container--default .select2-selection.border-rose-500,
        .select2-selection.border-rose-500 {
            border-color: #f43f5e !important;
            background-color: #fff1f2 !important;
            box-shadow: 0 0 0 1px #f43f5e !important;
        }

        input.flatpickr-input.border-rose-500,
        input.form-control.border-rose-500 {
            border-color: #f43f5e !important;
            background-color: #fff1f2 !important;
            box-shadow: 0 0 0 1px #f43f5e !important;
        }

        /* =========================================================
           REUSABLE V2 UI COMPONENTS & UTILITIES
           ========================================================= */

        /* 1. Custom Switch Slider Component */
        .custom-switch-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #cbd5e1;
            transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 24px;
        }
        .custom-switch-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50%;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.15);
        }
        input:checked + .custom-switch-slider {
            background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));
        }
        input:checked + .custom-switch-slider:before {
            transform: translateX(20px);
        }

        /* 2. Modal Body V2 Tables & Tabs */
        .modal-body-v2 table {
            width: 100%;
            font-size: 0.8125rem;
            border-collapse: collapse;
        }
        .modal-body-v2 table th,
        .modal-body-v2 table td {
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
        }
        .modal-body-v2 table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #475569;
        }
        .modal-body-v2 .nav-tabs {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1rem;
            gap: 0.5rem;
        }
        .modal-body-v2 .nav-tabs .nav-link {
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #64748b;
            border-radius: 0.5rem 0.5rem 0 0;
            border: 1px solid transparent;
            cursor: pointer;
        }
        .modal-body-v2 .nav-tabs .nav-link.active {
            color: var(--theme-color-1, #0a5c41);
            border-color: #e2e8f0 #e2e8f0 #ffffff;
            background-color: #ffffff;
        }

        /* 3. Reusable V2 Tab Navigation Pill */
        .tab-nav-v2 {
            background: transparent;
            color: #64748b;
            transition: all 0.2s ease;
        }
        .tab-nav-v2.active {
            background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956)) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 6px -1px rgba(var(--theme-color-rgb, 10, 92, 65), 0.3);
        }
        .tab-nav-v2.active span.bg-emerald-100,
        .tab-nav-v2.active span.bg-slate-100 {
            background: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
        .tab-nav-v2:hover:not(.active) {
            color: #0f172a;
            background: #f8fafc;
        }

        /* 4. Selection Card Hover Effects (Cabang, Dept, etc.) */
        .v2-select-card,
        .cabang-select-card {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .v2-select-card:hover,
        .cabang-select-card:hover {
            border-color: var(--theme-color-1, #0a5c41) !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* 5. Reusable V2 Pagination Styling */
        .v2-pagination nav {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .v2-pagination .pagination {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .v2-pagination .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            color: #475569;
            text-decoration: none;
            transition: all 0.15s;
        }
        .v2-pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));
            border-color: var(--theme-color-1, #0a5c41);
            color: #ffffff;
        }
        .v2-pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .v2-input-error,
        input.v2-input-error,
        select.v2-input-error,
        textarea.v2-input-error,
        .flatpickr-input.v2-input-error {
            border-color: #ef4444 !important;
            background-color: #fff1f2 !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2) !important;
        }
        .v2-input-error + .select2-container .select2-selection,
        .select2-container.v2-input-error .select2-selection,
        .select2-container--v2.v2-input-error .select2-selection,
        .select2-container--default.v2-input-error .select2-selection,
        .select2-selection.v2-input-error {
            border-color: #ef4444 !important;
            background-color: #fff1f2 !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2) !important;
        }

        /* =========================================================
           GLOBAL DARK MODE THEME ENGINE & COMPONENT OVERRIDES
           ========================================================= */
        html.dark body {
            background-color: #0b1120 !important;
            color: #cbd5e1 !important;
        }

        /* Surface cards, containers & panels */
        html.dark .bg-white,
        html.dark .bg-slate-50,
        html.dark .bg-slate-50\/50,
        html.dark .bg-slate-50\/60,
        html.dark .bg-slate-50\/70,
        html.dark .bg-slate-50\/80 {
            background-color: #1e293b !important;
        }

        html.dark .bg-\[\#f4f6f9\],
        html.dark .bg-\[\#f8fafc\] {
            background-color: #0b1120 !important;
        }

        /* Borders in dark mode */
        html.dark .border-slate-100,
        html.dark .border-slate-200,
        html.dark .border-slate-200\/60,
        html.dark .border-slate-200\/80,
        html.dark .border-slate-200\/90,
        html.dark .border-slate-300,
        html.dark .border-slate-300\/90,
        html.dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]),
        html.dark .divide-slate-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: #334155 !important;
        }

        /* Typography colors */
        html.dark .text-slate-900,
        html.dark .text-slate-800,
        html.dark .text-slate-700 {
            color: #f1f5f9 !important;
        }

        html.dark .text-slate-600,
        html.dark .text-slate-500 {
            color: #94a3b8 !important;
        }

        html.dark .text-slate-400 {
            color: #64748b !important;
        }

        /* Input fields, textareas, selects */
        html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"]),
        html.dark select:not([class*="select2"]),
        html.dark textarea {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        html.dark input::placeholder,
        html.dark textarea::placeholder {
            color: #64748b !important;
        }

        /* Hover states */
        html.dark .hover\:bg-slate-50:hover,
        html.dark .hover\:bg-slate-100:hover,
        html.dark .hover\:bg-slate-100\/60:hover,
        html.dark .hover\:bg-slate-100\/80:hover {
            background-color: #334155 !important;
        }

        /* Select2 in Dark Mode */
        html.dark .select2-container--v2 .select2-selection--single,
        html.dark .select2-container--default .select2-selection--single,
        html.dark select.select2:not(.select2-hidden-accessible),
        html.dark select.select2-v2:not(.select2-hidden-accessible),
        html.dark select[class*="select2"]:not(.select2-hidden-accessible),
        html.dark select[class*="Select2"]:not(.select2-hidden-accessible) {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        html.dark .select2-container--v2 .select2-selection--single .select2-selection__rendered,
        html.dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f1f5f9 !important;
        }

        html.dark .select2-dropdown,
        html.dark .select2-dropdown.select2-dropdown--v2 {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5) !important;
        }

        html.dark .select2-search--dropdown .select2-search__field {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        html.dark .select2-results__option {
            color: #cbd5e1 !important;
        }

        html.dark .select2-results__option:hover,
        html.dark .select2-results__option--highlighted[aria-selected] {
            background-color: #334155 !important;
            color: #ffffff !important;
        }

        /* Modals in Dark Mode */
        html.dark .modal-content {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #cbd5e1 !important;
        }

        html.dark .modal-body-v2 table th {
            background-color: #0f172a !important;
            color: #94a3b8 !important;
            border-color: #334155 !important;
        }

        html.dark .modal-body-v2 table td {
            border-color: #334155 !important;
        }

        /* Flatpickr Calendar in Dark Mode */
        html.dark .flatpickr-calendar {
            background: #1e293b !important;
            border-color: #334155 !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.6) !important;
            color: #f1f5f9 !important;
        }

        html.dark .flatpickr-month,
        html.dark .flatpickr-weekdays,
        html.dark span.flatpickr-weekday {
            background: #1e293b !important;
            color: #94a3b8 !important;
            fill: #94a3b8 !important;
        }

        html.dark .flatpickr-day {
            color: #cbd5e1 !important;
        }

        html.dark .flatpickr-day:hover {
            background: #334155 !important;
        }

        html.dark .flatpickr-day.selected {
            background: var(--theme-color-1, #0a5c41) !important;
            border-color: var(--theme-color-1, #0a5c41) !important;
            color: #ffffff !important;
        }

        /* Pagination in Dark Mode */
        html.dark .v2-pagination .page-item .page-link {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #cbd5e1 !important;
        }

        html.dark .v2-pagination .page-item:hover:not(.active) .page-link {
            background-color: #334155 !important;
        }
    </style>
    @stack('styles')
</head>

<body class="h-screen max-h-screen overflow-hidden bg-[#f4f6f9] dark:bg-slate-950 text-slate-800 dark:text-slate-200 antialiased flex relative w-full" x-data="{ mobileSidebarOpen: false }">

    <!-- Modern Floating Toast Container (Fixed Top-Right) -->
    <div id="v2-toast-container" class="fixed top-5 right-5 z-[99999] flex flex-col gap-2.5 pointer-events-none max-w-sm sm:max-w-md w-full px-4 sm:px-0"></div>

    <!-- Sidebar Component (Responsive Drawer on Mobile, Static on Desktop) -->
    @include('v2.layouts.sidebar')

    <!-- Main Content Container -->
    <div id="v2MainContent" class="flex-1 flex flex-col min-w-0 w-full h-screen max-h-screen overflow-y-auto overflow-x-hidden bg-[#f4f6f9] dark:bg-slate-950">
        <!-- Top Navbar -->
        @include('v2.layouts.navbar')

        <!-- Page Content -->
        <main class="flex-1 p-3.5 sm:p-6 lg:p-8 max-w-[1600px] w-full mx-auto">
            @yield('content')
        </main>

        <!-- Global Developer Footer -->
        <footer class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-3.5 border-t border-slate-200/80 dark:border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500 dark:text-slate-400">
            <div class="text-center sm:text-left">
                &copy; {{ date('Y') }} <strong class="text-slate-700 dark:text-slate-200">{{ $general_setting->nama_aplikasi ?? 'SMATT_V4' }}</strong> &bull; Aplikasi ini dikembangkan oleh
                <a href="https://www.instagram.com/amn4ll/?utm_source=ig_web_button_share_sheet"
                   target="_blank"
                   class="font-bold hover:underline"
                   style="color: var(--theme-color-1, #0a5c41);">
                    ZhanSoft - Amnal
                </a>
            </div>
            <div>
                <a href="https://wa.me/6285861094485?text=Halo%20Admin%20ZhanSoft%2C%20saya%20butuh%20bantuan%20support%20aplikasi%20SMATT3"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 border border-emerald-200 dark:border-emerald-800 text-[11px] font-bold text-emerald-700 dark:text-emerald-300 transition">
                    <i class="ti ti-brand-whatsapp text-sm text-emerald-600 dark:text-emerald-400"></i>
                    <span>Bantuan / Support</span>
                </a>
            </div>
        </footer>
    </div>

    <!-- Modern Toast Notification Engine -->
    <script>
        const Toast = (() => {
            const getContainer = () => document.getElementById('v2-toast-container');

            const styles = {
                success: {
                    cardBg: 'bg-emerald-600 text-white border-emerald-500 shadow-[0_14px_38px_-6px_rgba(5,150,105,0.45)]',
                    iconBg: 'bg-white/20 text-white border-white/25',
                    titleColor: 'text-white',
                    textColor: 'text-emerald-50',
                    closeBtn: 'text-white/80 hover:text-white hover:bg-white/20',
                    progressTrack: 'bg-emerald-700/50',
                    progressBar: 'bg-white',
                    iconSvg: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>`
                },
                error: {
                    cardBg: 'bg-rose-600 text-white border-rose-500 shadow-[0_14px_38px_-6px_rgba(225,29,72,0.45)]',
                    iconBg: 'bg-white/20 text-white border-white/25',
                    titleColor: 'text-white',
                    textColor: 'text-rose-50',
                    closeBtn: 'text-white/80 hover:text-white hover:bg-white/20',
                    progressTrack: 'bg-rose-700/50',
                    progressBar: 'bg-white',
                    iconSvg: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>`
                },
                warning: {
                    cardBg: 'bg-amber-500 text-slate-950 border-amber-400 shadow-[0_14px_38px_-6px_rgba(217,119,6,0.45)]',
                    iconBg: 'bg-black/15 text-slate-950 border-black/15',
                    titleColor: 'text-slate-950',
                    textColor: 'text-slate-900',
                    closeBtn: 'text-slate-900/80 hover:text-slate-950 hover:bg-black/10',
                    progressTrack: 'bg-amber-600/50',
                    progressBar: 'bg-slate-950',
                    iconSvg: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 18.75h.007v.008H12v-.008z" /></svg>`
                },
                info: {
                    cardBg: 'bg-sky-600 text-white border-sky-500 shadow-[0_14px_38px_-6px_rgba(2,132,199,0.45)]',
                    iconBg: 'bg-white/20 text-white border-white/25',
                    titleColor: 'text-white',
                    textColor: 'text-sky-50',
                    closeBtn: 'text-white/80 hover:text-white hover:bg-white/20',
                    progressTrack: 'bg-sky-700/50',
                    progressBar: 'bg-white',
                    iconSvg: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>`
                }
            };

            const titles = {
                success: 'Berhasil!',
                error: 'Terjadi Kesalahan!',
                warning: 'Peringatan!',
                info: 'Informasi'
            };

            function show({ type = 'info', title = null, message = '', duration = 4000 }) {
                const container = getContainer();
                if (!container) return;

                const toastType = styles[type] ? type : 'info';
                const st = styles[toastType];
                const finalTitle = title || titles[toastType];

                const card = document.createElement('div');
                card.className = `toast-card pointer-events-auto group relative flex items-start gap-3.5 p-4 rounded-2xl border ${st.cardBg} backdrop-blur-md overflow-hidden transition-all duration-300 ease-out transform translate-x-full opacity-0 scale-95 hover:scale-[1.02]`;

                card.innerHTML = `
                    <div class="w-9 h-9 rounded-xl ${st.iconBg} border flex items-center justify-center shrink-0 shadow-2xs backdrop-blur-xs">
                        ${st.iconSvg}
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <h4 class="text-[13px] font-bold ${st.titleColor} leading-snug tracking-tight">${finalTitle}</h4>
                        <p class="text-xs ${st.textColor} mt-0.5 leading-relaxed font-medium break-words">${message}</p>
                    </div>
                    <button type="button" class="toast-close shrink-0 ${st.closeBtn} rounded-lg p-1.5 transition -mr-1 -mt-1 cursor-pointer" aria-label="Tutup">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <div class="absolute bottom-0 left-0 right-0 h-[2.5px] ${st.progressTrack}">
                        <div class="toast-progress-active h-full ${st.progressBar}" style="width: 100%; animation-duration: ${duration}ms;"></div>
                    </div>
                `;

                container.appendChild(card);

                // Smooth enter animation
                requestAnimationFrame(() => {
                    card.classList.remove('translate-x-full', 'opacity-0', 'scale-95');
                    card.classList.add('translate-x-0', 'opacity-100', 'scale-100');
                });

                let timer;
                let startTime = Date.now();
                let remaining = duration;

                function dismiss() {
                    clearTimeout(timer);
                    card.classList.remove('translate-x-0', 'opacity-100', 'scale-100');
                    card.classList.add('translate-x-full', 'opacity-0', 'scale-95');
                    setTimeout(() => {
                        if (card.parentElement) {
                            card.parentElement.removeChild(card);
                        }
                    }, 320);
                }

                function startTimer() {
                    startTime = Date.now();
                    timer = setTimeout(dismiss, remaining);
                }

                function pauseTimer() {
                    clearTimeout(timer);
                    remaining -= (Date.now() - startTime);
                    if (remaining < 300) remaining = 300;
                }

                card.querySelector('.toast-close').addEventListener('click', dismiss);
                card.addEventListener('mouseenter', pauseTimer);
                card.addEventListener('mouseleave', startTimer);

                startTimer();

                return { dismiss };
            }

            return {
                show,
                success: (msg, title) => show({ type: 'success', message: msg, title }),
                error: (msg, title) => show({ type: 'error', message: msg, title }),
                warning: (msg, title) => show({ type: 'warning', message: msg, title }),
                info: (msg, title) => show({ type: 'info', message: msg, title })
            };
        })();

        window.toast = Toast;

        // Auto-trigger flash messages from Laravel session
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                window.toast.success({!! json_encode(session('success')) !!});
            @endif

            @if(session('error'))
                window.toast.error({!! json_encode(session('error')) !!});
            @endif

            @if(session('warning'))
                window.toast.warning({!! json_encode(session('warning')) !!});
            @endif

            @if(session('info'))
                window.toast.info({!! json_encode(session('info')) !!});
            @endif

            @if(session('status'))
                window.toast.info({!! json_encode(session('status')) !!});
            @endif

            @if(isset($errors) && $errors->any())
                @foreach($errors->all() as $err)
                    window.toast.error({!! json_encode($err) !!}, 'Validasi Gagal');
                @endforeach
            @endif
        });
    </script>

    <!-- Initialize Lucide Icons & Floating Tooltips -->
    <script>
        function initTooltips() {
            if (window.tippy) {
                tippy('[data-tippy-content]', {
                    arrow: true,
                    animation: 'fade',
                    duration: [150, 100],
                    placement: 'top',
                });
                tippy('[data-tippy-title]', {
                    arrow: true,
                    animation: 'fade',
                    duration: [150, 100],
                    placement: 'top',
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
            initTooltips();

            // Auto-initialize flatpickr on any element with .flatpickr-date
            if (typeof flatpickr !== 'undefined') {
                flatpickr('.flatpickr-date', {
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    locale: typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.id ? 'id' : 'default'
                });
            }

            // Set global modal default options: Static backdrop & no escape key close
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.Default = Object.assign(bootstrap.Modal.Default || {}, {
                    backdrop: 'static',
                    keyboard: false
                });
            }

            // Ensure all modals in DOM have static backdrop attributes
            document.querySelectorAll('.modal').forEach(modalEl => {
                modalEl.setAttribute('data-bs-backdrop', 'static');
                modalEl.setAttribute('data-bs-keyboard', 'false');
            });
        });

        // Hard guarantee: Catch any click on modal backdrop directly and cancel dismissal
        if (typeof $ !== 'undefined') {
            $(document).on('show.bs.modal', '.modal', function(e) {
                $(this).attr('data-bs-backdrop', 'static');
                $(this).attr('data-bs-keyboard', 'false');
                const instance = bootstrap.Modal.getInstance(this);
                if (instance && instance._config) {
                    instance._config.backdrop = 'static';
                    instance._config.keyboard = false;
                }
            });

            // Prevent closing when clicking outside modal-dialog container
            $(document).on('click', '.modal', function(e) {
                if ($(e.target).hasClass('modal') && !$(e.target).closest('.modal-dialog').length) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        }

        document.addEventListener('alpine:initialized', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
            initTooltips();
        });
        window.initTooltips = initTooltips;
    </script>

    <!-- ================= QUICK MENU SEARCH COMMAND PALETTE (MODAL & ENGINE) ================= -->
    <div id="quickMenuSearchModal" class="fixed inset-0 z-[99999] hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-start justify-center p-3 sm:p-6 pt-12 sm:pt-20">
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all duration-200 flex flex-col"
             onclick="event.stopPropagation()">
            
            <!-- Search Header Bar -->
            <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center gap-3 bg-slate-50/50">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0 shadow-2xs"
                     style="background: linear-gradient(135deg, {{ $general_setting->theme_color_1 ?? '#0a5c41' }}, {{ $general_setting->theme_color_2 ?? '#117956' }});">
                    <i data-lucide="search" class="w-5 h-5"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <input type="text" 
                           id="quickMenuSearchInput" 
                           autocomplete="off"
                           placeholder="Ketik nama menu, laporan, atau fitur yang dicari..." 
                           class="w-full bg-transparent border-0 text-slate-800 font-semibold text-sm sm:text-base placeholder-slate-400 focus:outline-none focus:ring-0 p-0">
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <span class="text-[10px] font-bold text-slate-400 bg-white px-2 py-1 rounded-lg border border-slate-200 shadow-2xs">ESC to close</span>
                    <button type="button" 
                            id="btnCloseQuickMenuSearch"
                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Category / Filter Quick Pills -->
            <div class="px-5 py-2.5 bg-slate-50/70 border-b border-slate-100 flex items-center gap-2 overflow-x-auto text-[11px] font-bold text-slate-600">
                <span class="text-[10px] text-slate-400 uppercase tracking-wider shrink-0 mr-1 font-extrabold">Filter:</span>
                <button type="button" class="quick-filter-pill active px-2.5 py-1 rounded-lg bg-emerald-600 text-white shadow-2xs transition" data-category="all">Semua</button>
                <button type="button" class="quick-filter-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 transition" data-category="Data Master">Data Master</button>
                <button type="button" class="quick-filter-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 transition" data-category="Presensi">Presensi</button>
                <button type="button" class="quick-filter-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 transition" data-category="Payroll">Payroll</button>
                <button type="button" class="quick-filter-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 transition" data-category="Laporan">Laporan</button>
                <button type="button" class="quick-filter-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 transition" data-category="Konfigurasi">Pengaturan</button>
            </div>

            <!-- Search Results List Container -->
            <div id="quickSearchResultsList" class="p-3 sm:p-4 max-h-[380px] overflow-y-auto space-y-1.5 custom-scrollbar">
                <!-- Dynamic Items Loaded via JS -->
            </div>

            <!-- Footer Shortcuts Hint -->
            <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <div class="flex items-center gap-3">
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded font-mono text-[10px] text-slate-600 font-bold shadow-2xs">↑</kbd>
                        <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded font-mono text-[10px] text-slate-600 font-bold shadow-2xs">↓</kbd>
                        <span>Navigasi</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded font-mono text-[10px] text-slate-600 font-bold shadow-2xs">↵ Enter</kbd>
                        <span>Pilih Menu</span>
                    </span>
                </div>
                <span id="quickMenuResultCount" class="font-bold text-slate-400">0 Menu Tersedia</span>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const quickSearchModal = document.getElementById('quickMenuSearchModal');
        const quickSearchInput = document.getElementById('quickMenuSearchInput');
        const quickResultsList = document.getElementById('quickSearchResultsList');
        const quickResultCount = document.getElementById('quickMenuResultCount');
        const btnClose = document.getElementById('btnCloseQuickMenuSearch');
        const filterPills = document.querySelectorAll('.quick-filter-pill');

        let allMenuItems = [];
        let activeCategory = 'all';
        let selectedIndex = -1;

        // 1. Fetch menu items from search-vertical.json
        fetch('{{ asset("assets/json/search-vertical.json") }}')
            .then(res => res.json())
            .then(data => {
                if (data && Array.isArray(data.pages)) {
                    allMenuItems = data.pages;
                }
            })
            .catch(() => {
                // Fallback built-in menus if JSON fetch fails
                allMenuItems = [
                    { name: "Dashboard", icon: "ti-home", url: "/dashboard" },
                    { name: "Data Master - Karyawan", icon: "ti-users", url: "/karyawan" },
                    { name: "Data Master - Departemen", icon: "ti-building", url: "/departemen" },
                    { name: "Data Master - Jabatan", icon: "ti-id", url: "/jabatan" },
                    { name: "Data Master - Cabang", icon: "ti-building-skyscraper", url: "/cabang" },
                    { name: "Presensi - Monitoring Presensi", icon: "ti-device-desktop", url: "/presensi" },
                    { name: "Presensi - Tracking Presensi", icon: "ti-map-2", url: "/trackingpresensi" },
                    { name: "Payroll - Gaji Pokok", icon: "ti-moneybag", url: "/gajipokok" },
                    { name: "Payroll - Slip Gaji", icon: "ti-receipt", url: "/slipgaji" },
                    { name: "Pengajuan Izin / Cuti / Sakit", icon: "ti-folder-check", url: "/izinabsen" },
                    { name: "Laporan - Laporan Presensi", icon: "ti-report", url: "/laporan/presensi" },
                    { name: "Laporan - Laporan Gaji", icon: "ti-report-money", url: "/laporan/gaji" },
                    { name: "Konfigurasi - General Setting", icon: "ti-settings", url: "/generalsetting" }
                ];
            });

        // Open Modal Function
        window.openQuickMenuSearch = function () {
            if (!quickSearchModal) return;
            quickSearchModal.classList.remove('hidden');
            if (quickSearchInput) {
                quickSearchInput.value = '';
                setTimeout(() => quickSearchInput.focus(), 50);
            }
            activeCategory = 'all';
            updateActivePill();
            renderMenuItems('');
            if (window.lucide) lucide.createIcons();
        };

        // Close Modal Function
        window.closeQuickMenuSearch = function () {
            if (!quickSearchModal) return;
            quickSearchModal.classList.add('hidden');
        };

        if (btnClose) {
            btnClose.addEventListener('click', window.closeQuickMenuSearch);
        }

        if (quickSearchModal) {
            quickSearchModal.addEventListener('click', function (e) {
                if (e.target === quickSearchModal) {
                    window.closeQuickMenuSearch();
                }
            });
        }

        // Keyboard Shortcuts (Alt+Space, Ctrl+/, Cmd+K, Ctrl+K, ESC)
        document.addEventListener('keydown', function (e) {
            // Check Cmd+K or Ctrl+K or Alt+Space or Ctrl+/
            const isCmdK = (e.metaKey || e.ctrlKey) && (e.key === 'k' || e.key === 'K');
            const isAltSpace = e.altKey && (e.code === 'Space' || e.key === ' ');
            const isCtrlSlash = e.ctrlKey && (e.key === '/' || e.code === 'Slash');

            if (isCmdK || isAltSpace || isCtrlSlash) {
                e.preventDefault();
                window.openQuickMenuSearch();
            } else if (e.key === 'Escape' && quickSearchModal && !quickSearchModal.classList.contains('hidden')) {
                e.preventDefault();
                window.closeQuickMenuSearch();
            }
        });

        // Search Input Listener
        if (quickSearchInput) {
            quickSearchInput.addEventListener('input', function () {
                renderMenuItems(this.value.trim());
            });

            // Keyboard navigation in list
            quickSearchInput.addEventListener('keydown', function (e) {
                const items = quickResultsList.querySelectorAll('.quick-menu-item');
                if (!items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = (selectedIndex + 1) % items.length;
                    highlightItem(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                    highlightItem(items);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (selectedIndex >= 0 && items[selectedIndex]) {
                        items[selectedIndex].click();
                    } else if (items[0]) {
                        items[0].click();
                    }
                }
            });
        }

        // Filter Pills
        filterPills.forEach(pill => {
            pill.addEventListener('click', function () {
                activeCategory = this.getAttribute('data-category');
                updateActivePill();
                renderMenuItems(quickSearchInput ? quickSearchInput.value.trim() : '');
            });
        });

        function updateActivePill() {
            filterPills.forEach(p => {
                if (p.getAttribute('data-category') === activeCategory) {
                    p.className = 'quick-filter-pill active px-2.5 py-1 rounded-lg bg-emerald-600 text-white shadow-2xs transition';
                } else {
                    p.className = 'quick-filter-pill px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-600 transition';
                }
            });
        }

        function highlightItem(items) {
            items.forEach((item, index) => {
                if (index === selectedIndex) {
                    item.classList.add('bg-emerald-50', 'border-emerald-300', 'ring-1', 'ring-emerald-400');
                    item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                } else {
                    item.classList.remove('bg-emerald-50', 'border-emerald-300', 'ring-1', 'ring-emerald-400');
                }
            });
        }

        function renderMenuItems(query) {
            if (!quickResultsList) return;
            selectedIndex = -1;

            let filtered = allMenuItems;

            // Filter by Category
            if (activeCategory !== 'all') {
                filtered = filtered.filter(item => item.name.toLowerCase().includes(activeCategory.toLowerCase()));
            }

            // Filter by query string
            if (query) {
                const q = query.toLowerCase();
                filtered = filtered.filter(item => item.name.toLowerCase().includes(q) || (item.url && item.url.toLowerCase().includes(q)));
            }

            if (quickResultCount) {
                quickResultCount.textContent = `${filtered.length} Menu Ditemukan`;
            }

            if (filtered.length === 0) {
                quickResultsList.innerHTML = `
                    <div class="text-center py-10 text-slate-400">
                        <i data-lucide="search-x" class="w-10 h-10 mx-auto mb-2 text-slate-300"></i>
                        <h4 class="text-xs font-bold text-slate-700">Menu Tidak Ditemukan</h4>
                        <p class="text-[11px] text-slate-400 mt-0.5">Coba cari dengan kata kunci lain seperti 'Karyawan', 'Presensi', 'Slip Gaji', atau 'Setting'.</p>
                    </div>
                `;
                if (window.lucide) lucide.createIcons();
                return;
            }

            let html = '';
            filtered.forEach((item, idx) => {
                const parts = item.name.split(' - ');
                const mainName = parts.length > 1 ? parts[1] : parts[0];
                const categoryBadge = parts.length > 1 ? parts[0] : 'Menu Utama';
                const tablerIcon = item.icon || 'ti-circle';

                html += `
                    <a href="${item.url}" 
                       class="quick-menu-item flex items-center justify-between p-2.5 sm:p-3 rounded-2xl bg-slate-50/70 hover:bg-emerald-50/60 border border-slate-200/80 hover:border-emerald-300 transition-all duration-150 group cursor-pointer"
                       data-index="${idx}">
                        <div class="flex items-center gap-3 truncate min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-white text-emerald-700 border border-slate-200 flex items-center justify-center text-lg shrink-0 shadow-2xs group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600 transition">
                                <i class="ti ${tablerIcon}"></i>
                            </div>
                            <div class="truncate">
                                <div class="flex items-center gap-1.5">
                                    <h5 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-emerald-800 transition truncate">${mainName}</h5>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium truncate block">${item.url}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 pl-2">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-white text-slate-600 border border-slate-200 group-hover:border-emerald-300 group-hover:text-emerald-700 shadow-2xs">
                                ${categoryBadge}
                            </span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-transform"></i>
                        </div>
                    </a>
                `;
            });

            quickResultsList.innerHTML = html;
            if (window.lucide) lucide.createIcons();
        }
    })();
    </script>
    <script>
        // Global Theme Toggle Function
        window.toggleDarkMode = function() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('v2_theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('v2_theme', 'dark');
            }
            // Trigger custom event if any chart or plugin needs to adapt
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: isDark ? 'light' : 'dark' } }));
        };
    </script>
    @stack('myscript')
    @stack('scripts')
</body>

</html>
