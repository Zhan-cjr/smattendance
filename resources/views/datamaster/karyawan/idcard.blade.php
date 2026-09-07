@extends('layouts.mobile.modern')

@section('title', 'ID Card - ' . $karyawan->nama_karyawan)

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap');

        :root {
            --id-primary: {{ $t['primary'] ?? '#1e3a8a' }};
            --id-secondary: {{ $t['primary_light'] ?? '#3b82f6' }};
            --id-accent: #f59e0b;
        }

        .idcard-page-layout {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 16px 50px 16px;
            background: {{ $t['bg_body'] ?? '#f8fafc' }};
            min-height: 100vh;
        }

        /* Mode Switcher Bar */
        .card-view-tabs {
            display: flex;
            background: #ffffff;
            padding: 4px;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(0, 0, 0, 0.04);
            margin-bottom: 16px;
            gap: 4px;
            width: 100%;
            max-width: 350px;
        }

        .card-view-tab {
            flex: 1;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            border: none;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-view-tab.active {
            background: var(--id-primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* 3D Card Flipper Container */
        .card-flip-container {
            perspective: 1400px;
            width: 340px;
            max-width: 100%;
            margin: 0 auto;
            position: relative;
        }

        .card-flipper {
            width: 100%;
            transition: transform 0.65s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            position: relative;
        }

        .card-flip-container.flipped .card-flipper {
            transform: rotateY(180deg);
        }

        /* Common Card Face Styles (Standard CR80 Ratio & Rounded Corner) */
        .idcard-face {
            width: 340px;
            max-width: 100%;
            min-height: 540px;
            border-radius: 26px;
            background: #ffffff;
            box-shadow: 
                0 25px 60px -15px rgba(0, 0, 0, 0.12),
                0 0 0 1px rgba(0, 0, 0, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .idcard-face-back {
            transform: rotateY(180deg);
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 100%;
        }

        /* Background Luxury Textures & Mesh */
        .card-bg-mesh {
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .mesh-circle-1 {
            position: absolute;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--id-primary) 0%, rgba(255,255,255,0) 70%);
            top: -90px;
            right: -80px;
            opacity: 0.12;
        }

        .mesh-circle-2 {
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--id-secondary) 0%, rgba(255,255,255,0) 70%);
            bottom: -50px;
            left: -60px;
            opacity: 0.15;
        }

        .security-guilloche-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.035;
            background-image: radial-gradient(var(--id-primary) 1px, transparent 1px), radial-gradient(var(--id-primary) 1px, transparent 1px);
            background-size: 16px 16px;
            background-position: 0 0, 8px 8px;
            pointer-events: none;
        }

        /* Lanyard Slot Hole Accent */
        .lanyard-slot-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: center;
            padding-top: 12px;
            padding-bottom: 4px;
        }

        .lanyard-slot {
            width: 52px;
            height: 11px;
            border-radius: 20px;
            background: #e2e8f0;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.25), 0 1px 0 rgba(255, 255, 255, 0.9);
            border: 1px solid #cbd5e1;
            position: relative;
        }

        .lanyard-slot::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 38px;
            height: 3px;
            border-radius: 10px;
            background: #94a3b8;
        }

        /* Header Branding */
        .card-header-bar {
            position: relative;
            z-index: 2;
            padding: 8px 20px 4px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-cluster {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo-emblem {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04);
            padding: 3px;
            flex-shrink: 0;
        }

        .brand-logo-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .brand-title-wrap {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .brand-name-text {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
            line-height: 1.15;
            max-width: 155px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-tag-text {
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            color: var(--id-primary);
            text-transform: uppercase;
        }

        .smart-contactless-badge {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 3px;
        }

        .nfc-icon-wrapper {
            color: #94a3b8;
            font-size: 1.15rem;
            line-height: 1;
        }

        .live-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 12px;
            font-size: 0.55rem;
            font-weight: 800;
            color: #059669;
            letter-spacing: 0.5px;
        }

        .live-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            animation: pulse-dot 1.8s infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.75); }
        }

        /* Smart IC Chip Graphic */
        .smart-ic-row {
            position: relative;
            z-index: 2;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 4px;
        }

        .smart-ic-chip {
            width: 38px;
            height: 28px;
            border-radius: 6px;
            background: linear-gradient(135deg, #d4af37 0%, #f6e58d 40%, #c59b27 70%, #f9ca24 100%);
            position: relative;
            box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.7), 0 2px 5px rgba(0, 0, 0, 0.12);
            border: 1px solid #b8860b;
            overflow: hidden;
        }

        .chip-line-horiz {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(139, 90, 0, 0.45);
        }

        .chip-line-vert {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 45%;
            width: 1px;
            background: rgba(139, 90, 0, 0.45);
        }

        .chip-inner-circuit {
            position: absolute;
            top: 5px;
            left: 7px;
            width: 12px;
            height: 16px;
            border-radius: 3px;
            border: 1px solid rgba(139, 90, 0, 0.5);
        }

        .hologram-security-badge {
            font-size: 0.6rem;
            font-weight: 800;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(226, 232, 240, 0.6));
            border: 1px solid rgba(203, 213, 225, 0.8);
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        /* Avatar Section */
        .card-avatar-section {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-top: 4px;
            padding: 0 20px;
        }

        .avatar-frame {
            width: 110px;
            height: 110px;
            margin: 0 auto;
            position: relative;
        }

        .avatar-glow-ring {
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--id-primary), var(--id-secondary), var(--id-accent));
            box-shadow: 0 10px 25px -4px rgba(var(--color-nav-rgb, 30, 58, 138), 0.35);
        }

        .avatar-img-mask {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: #ffffff;
            padding: 3px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-img-el {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #f1f5f9;
        }

        .avatar-verified-badge {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #ffffff;
            padding: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        .verified-inner {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        /* Identity Details */
        .card-identity-block {
            margin-top: 10px;
            text-align: center;
        }

        .employee-name {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.22rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            letter-spacing: -0.4px;
            margin: 0;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .designation-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 6px;
            padding: 4px 14px;
            background: linear-gradient(135deg, rgba(var(--color-nav-rgb, 30, 58, 138), 0.08), rgba(var(--color-nav-rgb, 30, 58, 138), 0.03));
            color: var(--id-primary);
            font-size: 0.75rem;
            font-weight: 800;
            border-radius: 12px;
            border: 1px solid rgba(var(--color-nav-rgb, 30, 58, 138), 0.15);
            letter-spacing: 0.2px;
        }

        /* Bento Information Grid */
        .card-info-bento {
            position: relative;
            z-index: 2;
            margin: 10px 20px 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }

        .bento-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 7px 10px;
            text-align: left;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .bento-card.full-width {
            grid-column: span 2;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .bento-label {
            font-size: 0.58rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            line-height: 1;
            margin-bottom: 3px;
        }

        .bento-val {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bento-val-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--id-primary);
            letter-spacing: 0.5px;
        }

        /* Barcode & Security Base */
        .card-security-footer {
            position: relative;
            z-index: 2;
            padding: 10px 20px 14px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .barcode-container {
            background: #ffffff;
            padding: 6px 12px 4px 12px;
            border-radius: 12px;
            border: 1px dashed #cbd5e1;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
        }

        .barcode-graphic {
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .barcode-caption {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 3px;
            margin-top: 4px;
        }

        /* Rainbow / Holographic Finish Bottom Ribbon */
        .card-holo-strip {
            height: 6px;
            width: 100%;
            background: linear-gradient(90deg, 
                var(--id-primary) 0%, 
                var(--id-secondary) 30%, 
                var(--id-accent) 60%, 
                var(--id-primary) 100%
            );
        }

        /* ==================== BACK SIDE OF THE CARD ==================== */
        .back-magstripe {
            height: 40px;
            width: 100%;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            margin-top: 6px;
            position: relative;
        }

        .back-magstripe::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.08);
        }

        .back-content-body {
            position: relative;
            z-index: 2;
            padding: 12px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
            justify-content: space-between;
        }

        .qr-section-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #ffffff;
            border-radius: 18px;
            padding: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            position: relative;
        }

        .qr-corner-tl { position: absolute; top: 6px; left: 6px; width: 10px; height: 10px; border-top: 2px solid var(--id-primary); border-left: 2px solid var(--id-primary); border-top-left-radius: 4px; }
        .qr-corner-tr { position: absolute; top: 6px; right: 6px; width: 10px; height: 10px; border-top: 2px solid var(--id-primary); border-right: 2px solid var(--id-primary); border-top-right-radius: 4px; }
        .qr-corner-bl { position: absolute; bottom: 6px; left: 6px; width: 10px; height: 10px; border-bottom: 2px solid var(--id-primary); border-left: 2px solid var(--id-primary); border-bottom-left-radius: 4px; }
        .qr-corner-br { position: absolute; bottom: 6px; right: 6px; width: 10px; height: 10px; border-bottom: 2px solid var(--id-primary); border-right: 2px solid var(--id-primary); border-bottom-right-radius: 4px; }

        .qr-render-area {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
        }

        .qr-caption {
            font-size: 0.62rem;
            font-weight: 800;
            color: #64748b;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-top: 6px;
        }

        .card-terms-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 10px;
            border: 1px solid #f1f5f9;
            width: 100%;
            text-align: left;
            margin: 8px 0;
        }

        .terms-title {
            font-size: 0.62rem;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .terms-list {
            font-size: 0.58rem;
            color: #64748b;
            line-height: 1.4;
            margin: 0;
            padding-left: 14px;
        }

        .company-contact-summary {
            text-align: center;
            width: 100%;
            border-top: 1px dashed #e2e8f0;
            padding-top: 8px;
        }

        .comp-address {
            font-size: 0.62rem;
            color: #64748b;
            font-weight: 600;
            line-height: 1.3;
            max-width: 280px;
            margin: 0 auto;
        }

        .comp-phone {
            font-size: 0.65rem;
            color: #334155;
            font-weight: 700;
            margin-top: 2px;
        }

        /* Action Buttons Grid */
        .idcard-action-bar {
            width: 340px;
            max-width: 100%;
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-action-primary {
            background: var(--id-primary);
            color: #ffffff;
            padding: 14px 18px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 0.92rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 25px -4px rgba(var(--color-nav-rgb, 30, 58, 138), 0.4);
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
        }

        .btn-action-primary:active {
            transform: scale(0.97);
            opacity: 0.92;
        }

        .btn-action-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .btn-action-secondary {
            background: #ffffff;
            color: #334155;
            padding: 12px 14px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.78rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-action-secondary:active {
            transform: scale(0.97);
            background: #f8fafc;
        }

        /* Flip Hint Animation */
        .flip-hint-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #64748b;
            margin-top: 14px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            backdrop-filter: blur(8px);
            transition: all 0.2s ease;
        }

        .flip-hint-pill:active {
            transform: scale(0.95);
        }

        /* Print Media Styles (High Quality PVC / Paper) */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .header-nav, .card-view-tabs, .idcard-action-bar, .flip-hint-pill, #bottom-nav, .appHeader {
                display: none !important;
            }
            .idcard-page-layout {
                padding: 0 !important;
                background: transparent !important;
            }
            .card-flip-container {
                perspective: none !important;
                width: 100% !important;
            }
            .card-flipper {
                transform: none !important;
                display: flex !important;
                flex-direction: row !important;
                gap: 20px !important;
                justify-content: center !important;
            }
            .idcard-face {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                page-break-inside: avoid;
            }
            .idcard-face-back {
                position: relative !important;
                transform: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="idcard-page-layout">

        <!-- VIEW TOGGLE TABS -->
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

        <!-- 3D FLIPPABLE ID CARD -->
        <div class="card-flip-container" id="flipContainer" onclick="toggleFlipCard()">
            <div class="card-flipper">

                <!-- ==================== FRONT SIDE ==================== -->
                <div class="idcard-face" id="idcard-front-area">
                    <!-- Background Art -->
                    <div class="card-bg-mesh">
                        <div class="mesh-circle-1"></div>
                        <div class="mesh-circle-2"></div>
                        <div class="security-guilloche-pattern"></div>
                    </div>

                    <!-- Top Lanyard Slot -->
                    <div class="lanyard-slot-wrapper">
                        <div class="lanyard-slot"></div>
                    </div>

                    <!-- Header with Company Logo & Contactless -->
                    <div class="card-header-bar">
                        <div class="brand-cluster">
                            <div class="brand-logo-emblem">
                                @if (!empty($generalsetting->logo) && Storage::exists('public/logo/' . $generalsetting->logo))
                                    <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" class="brand-logo-img" alt="Logo">
                                @else
                                    <img src="{{ asset('assets/template/img/sample/brand/1.png') }}" onerror="this.src='https://placehold.co/100x100?text=LOGO'" class="brand-logo-img" alt="Logo">
                                @endif
                            </div>
                            <div class="brand-title-wrap">
                                <span class="brand-name-text">{{ $generalsetting->nama_perusahaan ?? 'SM-ATTENDANCE' }}</span>
                                <span class="brand-tag-text">Official Identity Pass</span>
                            </div>
                        </div>

                        <div class="smart-contactless-badge">
                            <div class="nfc-icon-wrapper">
                                <ion-icon name="wifi-outline" style="transform: rotate(90deg);"></ion-icon>
                            </div>
                            <div class="live-status-pill">
                                <div class="live-dot"></div>
                                <span>AKTIF</span>
                            </div>
                        </div>
                    </div>

                    <!-- Smart IC Chip & Security Foil -->
                    <div class="smart-ic-row">
                        <div class="smart-ic-chip">
                            <div class="chip-line-horiz"></div>
                            <div class="chip-line-vert"></div>
                            <div class="chip-inner-circuit"></div>
                        </div>
                        <div class="hologram-security-badge">
                            <ion-icon name="shield-checkmark-outline" class="text-emerald-600"></ion-icon>
                            <span>SECURE PASS</span>
                        </div>
                    </div>

                    <!-- Avatar / Employee Portrait -->
                    <div class="card-avatar-section">
                        <div class="avatar-frame">
                            <div class="avatar-glow-ring"></div>
                            <div class="avatar-img-mask">
                                @if (!empty($karyawan->foto))
                                    <img src="{{ getfotoKaryawan($karyawan->foto) }}" class="avatar-img-el" alt="{{ $karyawan->nama_karyawan }}" crossorigin="anonymous">
                                @else
                                    <img src="{{ asset('assets/template/img/sample/avatar/avatar1.jpg') }}" class="avatar-img-el" alt="Default Avatar" crossorigin="anonymous">
                                @endif
                            </div>
                            <div class="avatar-verified-badge">
                                <div class="verified-inner">
                                    <ion-icon name="checkmark-sharp"></ion-icon>
                                </div>
                            </div>
                        </div>

                        <div class="card-identity-block">
                            <h2 class="employee-name" title="{{ $karyawan->nama_karyawan }}">{{ textUpperCase($karyawan->nama_karyawan) }}</h2>
                            <div class="designation-badge">
                                <ion-icon name="briefcase-outline"></ion-icon>
                                <span>{{ $karyawan->nama_jabatan ?? 'Karyawan' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Information Bento Grid -->
                    <div class="card-info-bento">
                        <div class="bento-card">
                            <span class="bento-label">NIK / No. Induk</span>
                            <span class="bento-val bento-val-mono">{{ $karyawan->nik }}</span>
                        </div>
                        <div class="bento-card">
                            <span class="bento-label">Departemen</span>
                            <span class="bento-val">{{ $karyawan->nama_dept ?? '-' }}</span>
                        </div>
                        <div class="bento-card full-width">
                            <div>
                                <span class="bento-label">Penempatan / Cabang</span>
                                <span class="bento-val">{{ $karyawan->nama_cabang ?? 'Kantor Pusat' }}</span>
                            </div>
                            <ion-icon name="location-outline" class="text-slate-400 text-base"></ion-icon>
                        </div>
                    </div>

                    <!-- Barcode Section -->
                    <div class="card-security-footer">
                        <div class="barcode-container">
                            <div class="barcode-graphic">
                                {!! DNS1D::getBarcodeHTML($karyawan->nik, 'C128', 1.8, 36, 'black') !!}
                            </div>
                            <span class="barcode-caption">{{ $karyawan->nik }}</span>
                        </div>
                    </div>

                    <!-- Bottom Accent Bar -->
                    <div class="card-holo-strip"></div>
                </div>

                <!-- ==================== BACK SIDE ==================== -->
                <div class="idcard-face idcard-face-back" id="idcard-back-area">
                    <!-- Background Art -->
                    <div class="card-bg-mesh">
                        <div class="mesh-circle-1" style="left: -80px; top: auto; bottom: -80px;"></div>
                        <div class="mesh-circle-2" style="right: -60px; top: -50px; left: auto;"></div>
                        <div class="security-guilloche-pattern"></div>
                    </div>

                    <!-- Top Lanyard Slot -->
                    <div class="lanyard-slot-wrapper">
                        <div class="lanyard-slot"></div>
                    </div>

                    <!-- Magnetic Stripe -->
                    <div class="back-magstripe"></div>

                    <!-- Body of Back Side -->
                    <div class="back-content-body">
                        <!-- QR Code Frame -->
                        <div class="qr-section-box">
                            <div class="qr-corner-tl"></div>
                            <div class="qr-corner-tr"></div>
                            <div class="qr-corner-bl"></div>
                            <div class="qr-corner-br"></div>
                            <div class="qr-render-area">
                                @if(class_exists('DNS2D'))
                                    {!! DNS2D::getBarcodeHTML($karyawan->nik, 'QRCODE', 4.2, 4.2, 'black') !!}
                                @else
                                    {!! DNS1D::getBarcodeHTML($karyawan->nik, 'C128', 1.6, 40, 'black') !!}
                                @endif
                            </div>
                            <span class="qr-caption">Scan Untuk Verifikasi</span>
                        </div>

                        <!-- Card Terms -->
                        <div class="card-terms-box">
                            <div class="terms-title">
                                <ion-icon name="information-circle-outline"></ion-icon>
                                <span>Ketentuan Penggunaan</span>
                            </div>
                            <ol class="terms-list">
                                <li>Kartu ini adalah tanda pengenal resmi karyawan dan wajib digunakan saat bertugas.</li>
                                <li>Tidak dapat dipindahtangankan kepada pihak lain.</li>
                                <li>Jika menemukan kartu ini, harap segera mengembalikan kepada pihak manajemen/HRD.</li>
                            </ol>
                        </div>

                        <!-- Company Contact Summary -->
                        <div class="company-contact-summary">
                            <p class="comp-address">
                                {{ $generalsetting->alamat ?? 'Alamat Perusahaan Belum Diatur' }}
                            </p>
                            @if(!empty($generalsetting->telepon))
                                <p class="comp-phone">Telp: {{ $generalsetting->telepon }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Accent Bar -->
                    <div class="card-holo-strip"></div>
                </div>

            </div>
        </div>

        <!-- FLIP HINT PILL -->
        <div class="flip-hint-pill" onclick="toggleFlipCard()">
            <ion-icon name="sync-outline" class="text-base text-blue-600"></ion-icon>
            <span>Ketuk kartu atau klik di sini untuk membalik</span>
        </div>

        <!-- ACTION BUTTONS -->
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

        // Export / Download Function
        function downloadCardElement(elementId, filename, buttonEl) {
            var target = document.getElementById(elementId);
            if (!target) return;

            var originalBtnHtml = buttonEl ? buttonEl.innerHTML : '';
            if (buttonEl) {
                buttonEl.innerHTML = '<ion-icon name="sync-outline" class="animate-spin text-xl"></ion-icon><span>MEMPROSES HD...</span>';
                buttonEl.disabled = true;
            }

            // Render high-res image
            html2canvas(target, {
                backgroundColor: null,
                scale: 4, // 4x Ultra HD rendering
                useCORS: true,
                allowTaint: true,
                logging: false,
                borderRadius: 26
            }).then(function(canvas) {
                var link = document.createElement('a');
                link.download = filename;
                link.href = canvas.toDataURL('image/png');
                link.click();

                if (buttonEl) {
                    buttonEl.innerHTML = originalBtnHtml;
                    buttonEl.disabled = false;
                }

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
                if (buttonEl) {
                    buttonEl.innerHTML = originalBtnHtml;
                    buttonEl.disabled = false;
                }
                alert('Gagal mengunduh ID Card: ' + e.message);
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
