<style>
    /* App Bottom Menu 2026 SuperApp Glassmorphism */
    .appBottomMenu {
        height: 64px;
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: space-around;
        z-index: 9999;
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border-top: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 -4px 20px rgba(15, 23, 42, 0.05) !important;
        padding: 0 8px !important;
    }

    .appBottomMenu .item {
        flex: 1;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.15s ease;
        text-decoration: none !important;
    }
    .appBottomMenu .item:active {
        transform: scale(0.9);
    }

    .appBottomMenu .item .col {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
    }

    .appBottomMenu .item ion-icon {
        font-size: 22px;
        color: #94a3b8 !important;
        transition: color 0.2s, transform 0.2s;
    }
    .appBottomMenu .item strong {
        font-size: 10px;
        font-weight: 600;
        color: #64748b !important;
        letter-spacing: -0.01em;
        transition: color 0.2s;
    }

    /* Active State */
    .appBottomMenu .item.active ion-icon {
        color: {{ $t['primary'] ?? '#0f766e' }} !important;
        transform: translateY(-1px);
    }
    .appBottomMenu .item.active strong {
        color: {{ $t['primary'] ?? '#0f766e' }} !important;
        font-weight: 800 !important;
    }

    /* Center Floating Action Button (Absen) */
    .appBottomMenu .item .action-button.large {
        width: 54px !important;
        height: 54px !important;
        border-radius: 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #0d9488 100%) !important;
        box-shadow: 0 8px 20px rgba(15, 118, 110, 0.4) !important;
        position: relative !important;
        top: -14px !important;
        border: 3px solid #ffffff !important;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s !important;
    }
    .appBottomMenu .item .action-button.large ion-icon {
        color: #ffffff !important;
        font-size: 28px !important;
    }
    .appBottomMenu .item:active .action-button.large {
        transform: scale(0.92) translateY(2px) !important;
        box-shadow: 0 4px 12px rgba(15, 118, 110, 0.3) !important;
    }

    /* Dark Mode Bottom Navigation */
    .dark .appBottomMenu, html.dark .appBottomMenu {
        background: rgba(15, 23, 42, 0.92) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 -4px 30px rgba(0, 0, 0, 0.6) !important;
    }
    .dark .appBottomMenu .item ion-icon {
        color: #64748b !important;
    }
    .dark .appBottomMenu .item strong {
        color: #94a3b8 !important;
    }
    .dark .appBottomMenu .item.active ion-icon,
    .dark .appBottomMenu .item.active strong {
        color: #10b981 !important;
    }
    .dark .appBottomMenu .item .action-button.large {
        border: 3px solid #0f172a !important;
        background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45) !important;
    }
</style>

<div class="appBottomMenu">
    <a href="/dashboard" class="item {{ request()->is('dashboard') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="{{ request()->is('dashboard') ? 'home' : 'home-outline' }}"></ion-icon>
            <strong>Home</strong>
        </div>
    </a>
    <a href="{{ route('presensi.histori') }}" class="item {{ request()->is('presensi/histori') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="{{ request()->is('presensi/histori') ? 'document-text' : 'document-text-outline' }}"></ion-icon>
            <strong>Histori</strong>
        </div>
    </a>

    <a href="/presensi/create" class="item">
        <div class="col">
            <div class="action-button large">
                <ion-icon name="finger-print"></ion-icon>
            </div>
        </div>
    </a>

    <a href="{{ route('pengajuanizin.index') }}" class="item {{ request()->is('pengajuanizin') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="{{ request()->is('pengajuanizin') ? 'calendar' : 'calendar-outline' }}"></ion-icon>
            <strong>Izin</strong>
        </div>
    </a>

    <a href="{{ route('profile.index') }}" class="item {{ request()->is('profile*') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="{{ request()->is('profile*') ? 'person' : 'person-outline' }}"></ion-icon>
            <strong>Profil</strong>
        </div>
    </a>
</div>
