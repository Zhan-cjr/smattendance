<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-semi-dark.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

<!-- Vendors CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />

<!-- Modern 2026 UI/UX Theme Enhancements -->
<style>
    :root {
        --theme-color-1: {{ $general_setting->theme_color_1 ?? '#0f766e' }};
        --theme-color-2: {{ $general_setting->theme_color_2 ?? '#14b8a6' }};
        --theme-color-1-rgb: 15, 118, 110;
        --theme-color-2-rgb: 20, 184, 166;
        
        --bs-primary: var(--theme-color-1);
        --bs-primary-rgb: var(--theme-color-1-rgb);
        --bs-body-font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        --bs-body-bg: #f8fafc;
    }

    body {
        font-family: var(--bs-body-font-family) !important;
        background-color: #f8fafc !important;
        color: #1e293b;
        letter-spacing: -0.01em;
    }

    /* Layout & Container */
    .layout-page, .content-wrapper {
        background-color: #f8fafc !important;
    }

    /* Modern Glassmorphic Navbar */
    .layout-navbar {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05) !important;
        transition: all 0.3s ease;
    }

    /* Modern Sidebar Enhancements */
    #layout-menu {
        background: var(--theme-color-1) !important;
        box-shadow: 4px 0 24px rgba(15, 23, 42, 0.06) !important;
    }

    #layout-menu .app-brand {
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    #layout-menu .app-brand-link,
    #layout-menu .app-brand-text,
    #layout-menu .menu-link,
    #layout-menu .menu-link .menu-icon,
    #layout-menu .menu-header,
    #layout-menu .menu-sub .menu-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
    }

    #layout-menu .menu-item {
        margin: 2px 10px !important;
    }

    #layout-menu .menu-link {
        border-radius: 12px !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        padding: 0.65rem 1rem !important;
    }

    #layout-menu .menu-link:hover {
        background: rgba(255, 255, 255, 0.12) !important;
        color: #ffffff !important;
        transform: translateX(3px);
    }

    #layout-menu .menu-inner > .menu-item.active > .menu-link,
    #layout-menu .menu-inner > .menu-item.open > .menu-link,
    #layout-menu .menu-sub > .menu-item.active > .menu-link {
        background: var(--theme-color-2) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2) !important;
    }

    #layout-menu .menu-sub {
        background: rgba(0, 0, 0, 0.12) !important;
        border-radius: 14px !important;
        margin: 4px 8px !important;
        padding: 6px 0 !important;
    }

    #layout-menu .menu-sub .menu-link {
        padding-left: 2.5rem !important;
        font-size: 0.875rem;
    }

    /* 2026 Card Aesthetics (Bento / Modern) */
    .card {
        background: #ffffff !important;
        border-radius: 20px !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 10px 30px -4px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02) !important;
        transition: transform 0.25s ease, box-shadow 0.25s ease !important;
    }

    .card:hover {
        box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.07), 0 4px 12px -1px rgba(15, 23, 42, 0.03) !important;
    }

    .card .card-header {
        border-bottom: 1px solid rgba(241, 245, 249, 1) !important;
        padding: 1.25rem 1.5rem !important;
        font-weight: 700;
        background: transparent !important;
    }

    .card .card-body {
        padding: 1.5rem !important;
    }

    /* Buttons */
    .btn {
        border-radius: 12px !important;
        font-weight: 600 !important;
        padding: 0.6rem 1.25rem !important;
        letter-spacing: -0.01em;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .btn:active {
        transform: scale(0.97) !important;
    }

    .btn-primary {
        background-color: var(--theme-color-1) !important;
        border-color: var(--theme-color-1) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(var(--theme-color-1-rgb), 0.35) !important;
    }

    .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
        background-color: var(--theme-color-2) !important;
        border-color: var(--theme-color-2) !important;
        box-shadow: 0 6px 18px rgba(var(--theme-color-2-rgb), 0.45) !important;
        transform: translateY(-1px);
    }

    .btn-outline-secondary {
        border-color: #cbd5e1 !important;
        color: #475569 !important;
        background: #ffffff !important;
    }

    .btn-outline-secondary:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
        border-color: #94a3b8 !important;
    }

    /* Modern Badges */
    .badge {
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 0.4em 0.75em !important;
        letter-spacing: 0.02em;
    }

    /* Modern Form Controls */
    .form-control, .form-select {
        border-radius: 12px !important;
        border: 1.5px solid #e2e8f0 !important;
        padding: 0.65rem 1rem !important;
        font-size: 0.925rem !important;
        transition: all 0.2s ease !important;
        background-color: #fcfdfd !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--theme-color-2) !important;
        box-shadow: 0 0 0 4px rgba(var(--theme-color-2-rgb), 0.15) !important;
        background-color: #ffffff !important;
    }

    /* Modern Tables */
    .table {
        margin-bottom: 0 !important;
    }

    .table thead th {
        font-size: 0.78rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        font-weight: 700 !important;
        color: #64748b !important;
        background-color: #f8fafc !important;
        border-bottom: 1.5px solid #e2e8f0 !important;
        padding: 0.9rem 1rem !important;
    }

    .table tbody td {
        padding: 1rem !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9 !important;
        font-size: 0.9rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(241, 245, 249, 0.7) !important;
    }

    /* Modal Polish */
    .modal-content {
        border-radius: 24px !important;
        border: none !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
        overflow: hidden;
    }

    .modal-header {
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 1.25rem 1.5rem !important;
    }

    .modal-footer {
        border-top: 1px solid #f1f5f9 !important;
        padding: 1rem 1.5rem !important;
    }

    /* SweetAlert Polish */
    .swal2-popup {
        border-radius: 24px !important;
        padding: 1.5rem !important;
        font-family: var(--bs-body-font-family) !important;
    }

    .swal2-container {
        z-index: 99999 !important;
    }

    .swal2-confirm {
        background-color: var(--theme-color-1) !important;
        border-radius: 12px !important;
        font-weight: 600 !important;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
