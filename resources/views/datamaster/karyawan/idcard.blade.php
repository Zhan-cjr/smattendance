@extends('layouts.mobile.modern')

@section('title', 'ID Card - ' . $karyawan->nama_karyawan)

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --card-primary: {{ $t['primary'] ?? '#0f766e' }};
            --card-light: {{ $t['primary_light'] ?? '#14b8a6' }};
            --card-dark: {{ $t['primary_dark'] ?? '#115e59' }};
            --card-rgb: {{ $t['primary_rgb'] ?? '15, 118, 110' }};
            --card-bg: {{ $t['bg_body'] ?? '#f1f5f9' }};
            --card-gold: #f59e0b;
        }

        .idcard-page-layout {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px 12px 60px 12px;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
        }

        /* ===== Template Style Selector (3 Designer Styles) ===== */
        .template-selector-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 10px;
            width: 100%;
            max-width: 340px;
        }

        .template-chip {
            flex: 1;
            padding: 7px 8px;
            border-radius: 12px;
            font-size: 10.5px;
            font-weight: 800;
            color: #64748b;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }

        .template-chip.active {
            border-color: var(--card-primary);
            background: rgba(var(--card-rgb), 0.08);
            color: var(--card-primary);
        }

        html.dark .template-chip {
            background: #131d31;
            border-color: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
        }

        html.dark .template-chip.active {
            border-color: var(--card-light);
            background: rgba(var(--card-rgb), 0.2);
            color: #ffffff;
        }

        /* ===== Front / Back Mode Switcher Tabs ===== */
        .card-view-tabs {
            display: flex;
            background: #ffffff;
            padding: 4px;
            border-radius: 14px;
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(0, 0, 0, 0.04);
            margin-bottom: 14px;
            gap: 4px;
            width: 100%;
            max-width: 340px;
        }

        .card-view-tab {
            flex: 1;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 11.5px;
            font-weight: 800;
            color: #64748b;
            border: none;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.25s ease;
        }

        .card-view-tab.active {
            background: var(--card-primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(var(--card-rgb), 0.3);
        }

        /* ===== Lanyard Hardware Clip ===== */
        .lanyard-clip-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: -12px;
            z-index: 10;
            position: relative;
        }

        .lanyard-strap {
            width: 24px;
            height: 16px;
            background: linear-gradient(90deg, #0f172a 0%, #334155 50%, #0f172a 100%);
            border-radius: 3px 3px 0 0;
        }

        .lanyard-buckle {
            width: 38px;
            height: 14px;
            background: linear-gradient(180deg, #e2e8f0 0%, #94a3b8 55%, #64748b 100%);
            border-radius: 4px;
            border: 1px solid #475569;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lanyard-buckle-inner {
            width: 20px;
            height: 5px;
            background: #1e293b;
            border-radius: 2px;
        }

        .lanyard-clear-strap {
            width: 14px;
            height: 14px;
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(148, 163, 184, 0.6);
            border-radius: 2px;
            margin-top: -2px;
        }

        /* ===== 3D Flip Card Container ===== */
        .card-flip-container {
            perspective: 1400px;
            width: 340px;
            max-width: 100%;
            margin: 0 auto;
            position: relative;
        }

        .card-flipper {
            width: 340px;
            height: 540px;
            transition: transform 0.65s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            position: relative;
        }

        .card-flip-container.flipped .card-flipper {
            transform: rotateY(180deg);
        }

        /* Capture Mode Override: Flattens 3D completely for 100% accurate html2canvas rendering */
        .card-flip-container.capturing-mode {
            perspective: none !important;
        }
        .card-flip-container.capturing-mode .card-flipper {
            transform: none !important;
            transition: none !important;
            transform-style: flat !important;
        }
        .card-flip-container.capturing-mode .idcard-face {
            backface-visibility: visible !important;
            -webkit-backface-visibility: visible !important;
            transform: none !important;
            box-shadow: none !important;
        }
        .card-flip-container.capturing-mode .idcard-face-back {
            position: relative !important;
            transform: none !important;
        }

        /* ===== Card Face Base (340px x 540px) ===== */
        .idcard-face {
            width: 340px;
            height: 540px;
            border-radius: 24px;
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 22px 50px -12px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(15, 23, 42, 0.06);
            position: absolute;
            top: 0;
            left: 0;
            overflow: hidden;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        .idcard-face-back {
            transform: rotateY(180deg);
        }

        /* Lanyard Slot Punch Hole */
        .card-lanyard-hole {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 46px;
            height: 9px;
            border-radius: 10px;
            background: rgba(15, 23, 42, 0.28);
            border: 1.5px solid rgba(255, 255, 255, 0.55);
            z-index: 25;
        }

        .idcard-face-back .card-lanyard-hole {
            background: #e2e8f0;
            border-color: #94a3b8;
        }

        /* Subtle Guilloche Watermark Pattern on White Area */
        .card-watermark-grid {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(var(--card-rgb), 0.06) 1px, transparent 1px);
            background-size: 14px 14px;
            pointer-events: none;
            z-index: 1;
        }

        /* ==============================================================
           HEADER GRAPHICS FOR THE 3 TEMPLATES
           ============================================================== */
        .card-header-art {
            position: relative;
            width: 100%;
            height: 195px;
            flex-shrink: 0;
            z-index: 2;
        }

        .header-svg-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .svg-tpl-1, .svg-tpl-2, .svg-tpl-3 {
            display: none;
        }
        .card-tpl-prism .svg-tpl-1 { display: block; }
        .card-tpl-glass .svg-tpl-2 { display: block; }
        .card-tpl-wave  .svg-tpl-3 { display: block; }

        /* Top Brand Row inside Header */
        .card-brand-row {
            position: relative;
            z-index: 10;
            padding: 26px 20px 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .brand-left-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .brand-logo-box {
            width: 42px;
            height: 42px;
            background: #ffffff;
            border-radius: 12px;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
            flex-shrink: 0;
        }

        .brand-logo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
        }

        /* IMPORTANT: Never use overflow:hidden on text nodes so html2canvas never clips letters */
        .brand-titles {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
        }

        .brand-comp-name {
            font-size: 13.5px;
            font-weight: 900;
            color: #ffffff;
            text-transform: uppercase;
            line-height: 1.2;
            padding: 0;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .brand-comp-sub {
            font-size: 8.5px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.88);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            line-height: 1.2;
            margin-top: 2px;
        }

        .brand-chip-badge {
            width: 32px;
            height: 24px;
            border-radius: 5px;
            background: linear-gradient(135deg, #fde68a 0%, #f59e0b 50%, #d97706 100%);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            flex-shrink: 0;
        }

        .brand-chip-badge::before {
            content: '';
            position: absolute;
            inset: 4px 6px;
            border: 1px solid rgba(120, 53, 15, 0.45);
            border-radius: 2px;
        }

        /* ==============================================================
           AVATAR PORTRAIT FRAME
           ============================================================== */
        .card-avatar-zone {
            position: absolute;
            top: 90px;
            left: 50%;
            transform: translateX(-50%);
            width: 126px;
            height: 126px;
            z-index: 15;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-outer-shell {
            width: 126px;
            height: 126px;
            border-radius: 50%;
            background: #ffffff;
            padding: 5px;
            box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.24);
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        /* Style 1 (Executive Prism) uses a modern Squircle / Super-ellipse frame */
        .card-tpl-prism .avatar-outer-shell {
            border-radius: 34px;
            transform: rotate(0deg);
        }
        .card-tpl-prism .avatar-inner-ring,
        .card-tpl-prism .avatar-photo-img {
            border-radius: 28px;
        }

        .avatar-inner-ring {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid var(--card-primary);
            background: #f1f5f9;
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
        }

        .avatar-photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: block;
        }

        .avatar-status-dot {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #10b981;
            border: 3px solid #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            z-index: 20;
        }

        /* ==============================================================
           FRONT BODY CONTENT (ZERO CLIPPING ARCHITECTURE)
           ============================================================== */
        .card-front-content {
            flex: 1;
            padding: 30px 20px 14px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 10;
            box-sizing: border-box;
        }

        .identity-headline-box {
            text-align: center;
            width: 100%;
            padding-top: 2px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .emp-full-name {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            line-height: 1.35;
            padding: 2px 4px;
            margin: 0;
            letter-spacing: 0.2px;
            word-break: break-word;
        }

        .emp-role-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 5px;
            padding: 0 16px;
            height: 24px;
            border-radius: 999px;
            background: rgba(var(--card-rgb), 0.1);
            border: 1px solid rgba(var(--card-rgb), 0.25);
            color: var(--card-primary);
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            line-height: 1;
            box-sizing: border-box;
            vertical-align: middle;
        }

        .emp-role-pill span {
            display: inline-block;
            line-height: 1;
            padding: 0;
            margin: 0;
            vertical-align: middle;
        }

        /* Structured Info Panel */
        .emp-info-panel {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 8px 14px;
            box-sizing: border-box;
        }

        .emp-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .emp-info-table td {
            padding: 3.5px 0;
            vertical-align: middle;
            line-height: 1.35;
        }

        .emp-info-table tr:not(:last-child) td {
            border-bottom: 1px dashed #e2e8f0;
        }

        .info-td-label {
            width: 92px;
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: left;
        }

        .info-td-sep {
            width: 12px;
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            text-align: center;
        }

        .info-td-val {
            font-size: 11.5px;
            font-weight: 800;
            color: #0f172a;
            text-align: left;
            padding-left: 2px;
        }

        .info-td-nik {
            font-family: 'JetBrains Mono', Courier, monospace;
            font-size: 12px;
            font-weight: 800;
            color: var(--card-primary);
            letter-spacing: 0.5px;
        }

        /* Barcode Dock */
        .card-barcode-dock {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 2px;
        }

        .barcode-bars-wrap {
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barcode-nik-text {
            font-family: 'JetBrains Mono', Courier, monospace;
            font-size: 10px;
            font-weight: 700;
            color: #475569;
            letter-spacing: 2.5px;
            line-height: 1.3;
            margin-top: 3px;
        }

        /* Bottom Accent Strip */
        .card-bottom-accent {
            height: 7px;
            width: 100%;
            background: linear-gradient(90deg, var(--card-primary) 0%, #0f172a 65%, var(--card-gold) 100%);
            flex-shrink: 0;
        }

        /* ==============================================================
           BACK SIDE ARCHITECTURE
           ============================================================== */
        .card-back-content {
            flex: 1;
            padding: 32px 22px 14px 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            z-index: 10;
            height: 100%;
            box-sizing: border-box;
        }

        .back-header-title {
            font-size: 11px;
            font-weight: 900;
            color: var(--card-primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
            line-height: 1.3;
        }

        .back-header-dot {
            width: 8px;
            height: 8px;
            border-radius: 2px;
            background: var(--card-primary);
            display: inline-block;
        }

        .back-terms-list {
            display: flex;
            flex-direction: column;
            gap: 7px;
            text-align: left;
        }

        .back-term-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 10.5px;
            color: #334155;
            line-height: 1.45;
            font-weight: 600;
        }

        .back-term-bullet {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--card-primary);
            flex-shrink: 0;
            margin-top: 5px;
        }

        .back-validity-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 9px 14px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .validity-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            line-height: 1.35;
        }

        .validity-label {
            font-weight: 700;
            color: #475569;
        }

        .validity-val {
            font-family: 'JetBrains Mono', Courier, monospace;
            font-weight: 800;
            color: #0f172a;
        }

        .validity-active-badge {
            color: #059669;
            font-weight: 900;
            letter-spacing: 0.4px;
        }

        /* Centered QR Code Box */
        .back-qr-center-zone {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-bottom: 68px;
        }

        .qr-frame-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            padding: 12px 16px 9px 16px;
            box-shadow: 0 8px 22px -4px rgba(15, 23, 42, 0.08);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .qr-box-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
        }

        .qr-caption-label {
            font-size: 9.5px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-top: 6px;
            line-height: 1.3;
        }

        /* Back Footer Wave */
        .card-footer-art {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 82px;
            z-index: 2;
        }

        .card-footer-art svg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .footer-brand-lockup {
            position: absolute;
            bottom: 12px;
            right: 18px;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .footer-logo-mini {
            width: 28px;
            height: 28px;
            background: #ffffff;
            border-radius: 7px;
            padding: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .footer-logo-mini img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .footer-text-mini {
            text-align: right;
            color: #ffffff;
        }

        .footer-comp-title {
            font-size: 11.5px;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.25;
            letter-spacing: 0.3px;
        }

        .footer-comp-tag {
            font-size: 8px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: 1px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        /* ===== Controls & Action Buttons ===== */
        .flip-hint-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            margin-top: 14px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .idcard-action-bar {
            width: 340px;
            max-width: 100%;
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-action-primary {
            background: linear-gradient(135deg, var(--card-primary) 0%, var(--card-dark) 100%);
            color: #ffffff;
            padding: 13px 18px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 25px -4px rgba(var(--card-rgb), 0.4);
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
        }

        .btn-action-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .btn-action-secondary {
            background: #ffffff;
            color: #334155;
            padding: 11px 12px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            cursor: pointer;
        }

        @media print {
            body { background: #ffffff !important; padding: 0 !important; margin: 0 !important; }
            .header-nav, .template-selector-bar, .card-view-tabs, .idcard-action-bar, .flip-hint-pill, #bottom-nav, .appHeader, .lanyard-clip-container {
                display: none !important;
            }
            .idcard-page-layout { padding: 0 !important; background: transparent !important; }
            .card-flip-container { perspective: none !important; width: 100% !important; }
            .card-flipper {
                transform: none !important;
                display: flex !important;
                flex-direction: row !important;
                gap: 20px !important;
                justify-content: center !important;
                width: auto !important;
            }
            .idcard-face {
                position: relative !important;
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
            }
            .idcard-face-back { transform: none !important; }
        }
    </style>
@endpush

@section('content')
    <div class="idcard-page-layout">

        <!-- PEMILIH MODEL DESAIN (3 PILIHAN DESAIN PROFESIONAL) -->
        <div class="template-selector-bar">
            <button type="button" class="template-chip active" id="chip-prism" onclick="switchCardTemplate('prism')">
                <i class="fa-solid fa-gem text-[10px]"></i>
                <span>Executive Prism</span>
            </button>
            <button type="button" class="template-chip" id="chip-glass" onclick="switchCardTemplate('glass')">
                <i class="fa-solid fa-layer-group text-[10px]"></i>
                <span>Neo-Tech Glass</span>
            </button>
            <button type="button" class="template-chip" id="chip-wave" onclick="switchCardTemplate('wave')">
                <i class="fa-solid fa-bezier-curve text-[10px]"></i>
                <span>Signature Wave</span>
            </button>
        </div>

        <!-- VIEW TOGGLE TABS (DEPAN / BELAKANG) -->
        <div class="card-view-tabs">
            <button type="button" id="tab-front" class="card-view-tab active" onclick="showSide('front')">
                <ion-icon name="card-outline" class="text-base"></ion-icon>
                <span>Tampak Depan</span>
            </button>
            <button type="button" id="tab-back" class="card-view-tab" onclick="showSide('back')">
                <ion-icon name="qr-code-outline" class="text-base"></ion-icon>
                <span>Tampak Belakang</span>
            </button>
        </div>

        <!-- Lanyard Clip -->
        <div class="lanyard-clip-container">
            <div class="lanyard-strap"></div>
            <div class="lanyard-buckle">
                <div class="lanyard-buckle-inner"></div>
            </div>
            <div class="lanyard-clear-strap"></div>
        </div>

        <!-- 3D FLIPPABLE ID CARD -->
        <div class="card-flip-container card-tpl-prism" id="flipContainer" onclick="toggleFlipCard()">
            <div class="card-flipper" id="cardFlipper">

                <!-- ==================== TAMPAK DEPAN ==================== -->
                <div class="idcard-face" id="idcard-front-area">
                    <div class="card-watermark-grid"></div>
                    <div class="card-lanyard-hole"></div>

                    <!-- Header Graphic Section -->
                    <div class="card-header-art">
                        <!-- Template 1: Executive Geometric Prism SVG -->
                        <svg class="header-svg-layer svg-tpl-1" viewBox="0 0 340 195" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                            <path d="M0 0H340V135L205 182L0 140V0Z" fill="var(--card-primary)" />
                            <path d="M340 70L185 175L205 182L340 135V70Z" fill="#0f172a" />
                            <path d="M0 122L205 172L340 125V132L205 180L0 130V122Z" fill="#f59e0b" />
                            <polygon points="260,0 340,0 340,75" fill="rgba(255,255,255,0.08)" />
                            <circle cx="40" cy="30" r="65" fill="rgba(255,255,255,0.06)" />
                        </svg>

                        <!-- Template 2: Neo-Tech Luxury Mesh & Diagonal Cut SVG -->
                        <svg class="header-svg-layer svg-tpl-2" viewBox="0 0 340 195" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                            <path d="M0 0H340V155C260 155 180 185 0 155V0Z" fill="#0f172a" />
                            <path d="M0 0H340V125C240 165 110 115 0 150V0Z" fill="var(--card-primary)" />
                            <circle cx="290" cy="40" r="70" fill="rgba(255,255,255,0.1)" />
                            <circle cx="60" cy="110" r="50" fill="rgba(255,255,255,0.07)" />
                            <path d="M30 148C130 125 230 160 310 132" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" />
                        </svg>

                        <!-- Template 3: Classic Signature Wave SVG -->
                        <svg class="header-svg-layer svg-tpl-3" viewBox="0 0 340 195" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                            <path d="M0 0H340V130C280 170 195 150 135 165C70 180 30 170 0 183V0Z" fill="var(--card-primary)" />
                            <path d="M340 85C260 125 185 140 125 160C185 145 255 135 340 150V85Z" fill="#1e1e24" />
                            <path d="M0 170C50 155 110 160 165 150C115 147 50 153 0 163V170Z" fill="rgba(255,255,255,0.35)" />
                        </svg>

                        <!-- Top Brand Row -->
                        <div class="card-brand-row">
                            <div class="brand-left-group">
                                <div class="brand-logo-box">
                                    @if (!empty($generalsetting->logo) && Storage::disk('public')->exists('logo/' . $generalsetting->logo))
                                        <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo">
                                    @else
                                        <img src="{{ asset('assets/login/images/logoweb-1.png') }}" onerror="this.src='{{ asset('assets/template/img/sample/brand/1.png') }}'" alt="Logo">
                                    @endif
                                </div>
                                <div class="brand-titles">
                                    <div class="brand-comp-name">{{ $generalsetting->nama_perusahaan ?? 'SM-ATTENDANCE' }}</div>
                                    <div class="brand-comp-sub">Official Identity Pass</div>
                                </div>
                            </div>
                            <div class="brand-chip-badge" title="Smart Chip"></div>
                        </div>
                    </div>

                    <!-- Portrait Photo Frame -->
                    <div class="card-avatar-zone">
                        <div class="avatar-outer-shell">
                            <div class="avatar-inner-ring">
                                @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                                    <img src="{{ getfotoKaryawan($karyawan->foto) }}" class="avatar-photo-img" alt="{{ $karyawan->nama_karyawan }}">
                                @else
                                    <img src="{{ asset('assets/template/img/sample/avatar/avatar1.jpg') }}" class="avatar-photo-img" alt="Avatar">
                                @endif
                            </div>
                        </div>
                        <div class="avatar-status-dot"></div>
                    </div>

                    <!-- Employee Details & Barcode -->
                    <div class="card-front-content">
                        <div class="identity-headline-box">
                            <h2 class="emp-full-name">{{ textUpperCase($karyawan->nama_karyawan) }}</h2>
                            <div class="emp-role-pill"><span>{{ $karyawan->nama_jabatan ?? 'KARYAWAN' }}</span></div>
                        </div>

                        <!-- Structured Info Panel -->
                        <div class="emp-info-panel">
                            <table class="emp-info-table">
                                <tr>
                                    <td class="info-td-label">ID No / NIK</td>
                                    <td class="info-td-sep">:</td>
                                    <td class="info-td-val info-td-nik">{{ $karyawan->nik }}</td>
                                </tr>
                                <tr>
                                    <td class="info-td-label">Departemen</td>
                                    <td class="info-td-sep">:</td>
                                    <td class="info-td-val">{{ $karyawan->nama_dept ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="info-td-label">Penempatan</td>
                                    <td class="info-td-sep">:</td>
                                    <td class="info-td-val">{{ $karyawan->nama_cabang ?? 'Kantor Pusat' }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Scannable Barcode Dock -->
                        <div class="card-barcode-dock">
                            <div class="barcode-bars-wrap">
                                {!! DNS1D::getBarcodeHTML($karyawan->nik, 'C128', 1.8, 34, 'black') !!}
                            </div>
                            <div class="barcode-nik-text">{{ $karyawan->nik }}</div>
                        </div>
                    </div>

                    <div class="card-bottom-accent"></div>
                </div>

                <!-- ==================== TAMPAK BELAKANG ==================== -->
                <div class="idcard-face idcard-face-back" id="idcard-back-area">
                    <div class="card-watermark-grid"></div>
                    <div class="card-lanyard-hole"></div>

                    <div class="card-back-content">
                        <!-- Ketentuan Kartu -->
                        <div>
                            <div class="back-header-title">
                                <span class="back-header-dot"></span>
                                <span>Ketentuan Penggunaan Kartu</span>
                            </div>
                            <div class="back-terms-list">
                                <div class="back-term-row">
                                    <div class="back-term-bullet"></div>
                                    <div>Kartu identitas ini adalah tanda pengenal resmi karyawan dan wajib dikenakan selama bertugas.</div>
                                </div>
                                <div class="back-term-row">
                                    <div class="back-term-bullet"></div>
                                    <div>Dilarang menyalahgunakan atau memindahtangankan kartu ini kepada pihak lain tanpa izin tertulis.</div>
                                </div>
                                <div class="back-term-row">
                                    <div class="back-term-bullet"></div>
                                    <div>Apabila kartu ini hilang atau ditemukan oleh pihak ketiga, mohon segera dikembalikan ke HRD / Manajemen.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Masa Berlaku & Status -->
                        <div class="back-validity-card">
                            <div class="validity-row">
                                <span class="validity-label">Tgl. Terbit (Issued)</span>
                                <span class="validity-val">{{ date('d / m / Y') }}</span>
                            </div>
                            <div class="validity-row">
                                <span class="validity-label">Status Keanggotaan</span>
                                <span class="validity-val validity-active-badge">VERIFIED ACTIVE</span>
                            </div>
                        </div>

                        <!-- QR Code Presisi di Tengah -->
                        <div class="back-qr-center-zone">
                            <div class="qr-frame-card">
                                <div class="qr-box-inner">
                                    @if(class_exists('DNS2D'))
                                        {!! DNS2D::getBarcodeHTML($karyawan->nik, 'QRCODE', 4.3, 4.3, 'black') !!}
                                    @else
                                        {!! DNS1D::getBarcodeHTML($karyawan->nik, 'C128', 1.6, 42, 'black') !!}
                                    @endif
                                </div>
                                <div class="qr-caption-label">SCAN VERIFIKASI</div>
                            </div>
                        </div>
                    </div>

                    <!-- Gelombang Grafis Bawah -->
                    <div class="card-footer-art">
                        <svg viewBox="0 0 340 82" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                            <path d="M0 36C95 10 185 50 340 16V82H0V36Z" fill="#0f172a" />
                            <path d="M0 50C105 20 205 46 340 26V82H0V50Z" fill="var(--card-primary)" />
                            <path d="M0 44L175 28L340 22" stroke="#f59e0b" stroke-width="1.5" stroke-opacity="0.7" />
                        </svg>

                        <div class="footer-brand-lockup">
                            <div class="footer-text-mini">
                                <div class="footer-comp-title">{{ $generalsetting->nama_perusahaan ?? 'SM-ATTENDANCE' }}</div>
                                <div class="footer-comp-tag">OFFICIAL IDENTITY CARD</div>
                            </div>
                            <div class="footer-logo-mini">
                                @if (!empty($generalsetting->logo) && Storage::disk('public')->exists('logo/' . $generalsetting->logo))
                                    <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" alt="Logo">
                                @else
                                    <img src="{{ asset('assets/login/images/logoweb-1.png') }}" onerror="this.src='{{ asset('assets/template/img/sample/brand/1.png') }}'" alt="Logo">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Petunjuk Balik Kartu -->
        <div class="flip-hint-pill" onclick="toggleFlipCard()">
            <ion-icon name="sync-outline" class="text-base text-slate-600"></ion-icon>
            <span>Ketuk kartu atau klik di sini untuk membalik</span>
        </div>

        <!-- Tombol Aksi Download -->
        <div class="idcard-action-bar">
            <button type="button" id="btn-download-front" class="btn-action-primary active:scale-95 transition-all">
                <ion-icon name="cloud-download-outline" class="text-xl"></ion-icon>
                <span>SIMPAN KARTU DEPAN (HD)</span>
            </button>

            <div class="btn-action-group">
                <button type="button" id="btn-download-back" class="btn-action-secondary active:scale-95 transition-all">
                    <ion-icon name="download-outline" class="text-lg text-slate-600"></ion-icon>
                    <span>Simpan Belakang</span>
                </button>
                <button type="button" onclick="window.print()" class="btn-action-secondary active:scale-95 transition-all">
                    <ion-icon name="print-outline" class="text-lg text-slate-600"></ion-icon>
                    <span>Cetak / Print</span>
                </button>
            </div>
        </div>

    </div>
@endsection

@push('myscript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        var isFlipped = false;

        // Ganti gaya desain (Executive Prism / Neo-Tech Glass / Signature Wave)
        function switchCardTemplate(tpl) {
            var container = document.getElementById('flipContainer');
            container.classList.remove('card-tpl-prism', 'card-tpl-glass', 'card-tpl-wave');
            container.classList.add('card-tpl-' + tpl);

            document.querySelectorAll('.template-chip').forEach(function(btn) {
                btn.classList.remove('active');
            });
            var activeChip = document.getElementById('chip-' + tpl);
            if (activeChip) activeChip.classList.add('active');

            localStorage.setItem('smatt_idcard_tpl', tpl);
        }

        // Restore pilihan template terakhir
        (function() {
            var savedTpl = localStorage.getItem('smatt_idcard_tpl');
            if (savedTpl && ['prism', 'glass', 'wave'].includes(savedTpl)) {
                switchCardTemplate(savedTpl);
            }
        })();

        function showSide(side) {
            var container = document.getElementById('flipContainer');
            var tabFront = document.getElementById('tab-front');
            var tabBack = document.getElementById('tab-back');

            if (side === 'back') {
                container.classList.add('flipped');
                tabBack.classList.add('active');
                tabFront.classList.remove('active');
                isFlipped = true;
            } else {
                container.classList.remove('flipped');
                tabFront.classList.add('active');
                tabBack.classList.remove('active');
                isFlipped = false;
            }
        }

        function toggleFlipCard() {
            if (isFlipped) {
                showSide('front');
            } else {
                showSide('back');
            }
        }

        // Export / Download Function - Captures Live Element Without 3D Transform or Text Clipping
        function downloadCardElement(elementId, filename, buttonEl) {
            var target = document.getElementById(elementId);
            var container = document.getElementById('flipContainer');
            var frontEl = document.getElementById('idcard-front-area');
            var backEl = document.getElementById('idcard-back-area');
            if (!target || !container) return;

            var originalBtnHtml = buttonEl ? buttonEl.innerHTML : '';
            if (buttonEl) {
                buttonEl.innerHTML = '<ion-icon name="sync-outline" class="animate-spin text-xl"></ion-icon><span>MEMPROSES HD...</span>';
                buttonEl.disabled = true;
            }

            // Matikan sementara efek 3D flip pada elemen asli agar foto, font, dan SVG ter-capture 100% utuh
            var otherEl = (elementId === 'idcard-front-area') ? backEl : frontEl;
            container.classList.add('capturing-mode');
            if (otherEl) otherEl.style.display = 'none';
            target.style.display = 'flex';

            var finishCapture = function() {
                container.classList.remove('capturing-mode');
                if (otherEl) otherEl.style.display = '';
                target.style.display = '';
                if (buttonEl) {
                    buttonEl.innerHTML = originalBtnHtml;
                    buttonEl.disabled = false;
                }
            };

            // Tunggu font & layout stabil sebelum merender kanvas
            var readyPromise = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();
            readyPromise.then(function() {
                setTimeout(function() {
                    html2canvas(target, {
                        backgroundColor: '#ffffff',
                        scale: 3,
                        useCORS: true,
                        logging: false,
                        onclone: function(clonedDoc) {
                            var clonedTarget = clonedDoc.getElementById(elementId);
                            if (clonedTarget) {
                                clonedTarget.querySelectorAll('h2, span, div, td, p').forEach(function(el) {
                                    if (!el.classList.contains('avatar-inner-ring') && !el.classList.contains('idcard-face')) {
                                        el.style.overflow = 'visible';
                                    }
                                });

                                // 1. Koreksi presisi teks nama perusahaan di samping logo
                                clonedTarget.querySelectorAll('.brand-titles').forEach(function(bt) {
                                    bt.style.position = 'relative';
                                    bt.style.top = '-6px';
                                });

                                // 2. Koreksi presisi posisi teks badge jabatan khusus render canvas
                                clonedTarget.querySelectorAll('.emp-role-pill').forEach(function(pill) {
                                    var pillSpan = pill.querySelector('span');
                                    if (pillSpan) {
                                        pillSpan.style.display = 'inline-block';
                                        pillSpan.style.position = 'relative';
                                        pillSpan.style.top = '-5.5px';
                                    }
                                });

                                // 3. Koreksi presisi tabel info karyawan dalam kotak abu-abu agar tepat di tengah
                                clonedTarget.querySelectorAll('.emp-info-panel').forEach(function(panel) {
                                    panel.style.paddingTop = '3px';
                                    panel.style.paddingBottom = '15px';
                                });

                                // 4. Koreksi presisi teks footer di samping logo mini belakang
                                clonedTarget.querySelectorAll('.footer-text-mini').forEach(function(ft) {
                                    ft.style.position = 'relative';
                                    ft.style.top = '-3px';
                                });
                            }
                        }
                    }).then(function(canvas) {
                        finishCapture();

                        var link = document.createElement('a');
                        link.download = filename;
                        link.href = canvas.toDataURL('image/png');
                        link.click();

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tersimpan!',
                                text: 'File ' + filename + ' berhasil diunduh ke galeri.',
                                showConfirmButton: false,
                                timer: 2200,
                                customClass: { popup: 'rounded-[1.5rem]' }
                            });
                        }
                    }).catch(function(e) {
                        console.error(e);
                        finishCapture();
                        alert('Gagal mengunduh ID Card: ' + e.message);
                    });
                }, 80);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            var btnFront = document.getElementById('btn-download-front');
            if (btnFront) {
                btnFront.addEventListener('click', function(e) {
                    e.stopPropagation();
                    downloadCardElement('idcard-front-area', 'IDCard_Depan_{{ $karyawan->nik }}.png', btnFront);
                });
            }

            var btnBack = document.getElementById('btn-download-back');
            if (btnBack) {
                btnBack.addEventListener('click', function(e) {
                    e.stopPropagation();
                    downloadCardElement('idcard-back-area', 'IDCard_Belakang_{{ $karyawan->nik }}.png', btnBack);
                });
            }
        });
    </script>
@endpush
