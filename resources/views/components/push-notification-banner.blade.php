{{-- Floating Push Notification Banner --}}
<div id="pushNotificationBanner" style="display: none; position: fixed; bottom: 85px; left: 15px; right: 15px; z-index: 9999; max-width: 480px; margin: 0 auto;">
    <div class="card shadow-lg border-0" style="border-radius: 16px; background: linear-gradient(135deg, #1e293b, #0f172a); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.1) !important; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.3) !important;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 38px; height: 38px; background: rgba(59, 130, 246, 0.2); color: #60a5fa; flex-shrink: 0;">
                    <ion-icon name="notifications" style="font-size: 22px;"></ion-icon>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0 text-white fw-bold" style="font-size: 14px; letter-spacing: -0.2px;">Aktifkan Notifikasi</h6>
                    <p class="mb-0 text-white-50" style="font-size: 12px; line-height: 1.3;">Dapatkan pengingat absen & update status izin langsung di HP Anda.</p>
                </div>
                <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 10px; opacity: 0.6;" onclick="WebPushManager.dismissPromptBanner()" aria-label="Close"></button>
            </div>
            <div class="d-flex gap-2 mt-2 pt-1">
                <button type="button" class="btn btn-sm btn-primary flex-grow-1 fw-bold" style="border-radius: 10px; font-size: 12px; padding: 6px 12px; background: #2563eb; border: none;" onclick="WebPushManager.subscribeUser()">
                    🔔 Aktifkan Sekarang
                </button>
                <button type="button" class="btn btn-sm btn-outline-light text-white-50" style="border-radius: 10px; font-size: 12px; padding: 6px 12px; border-color: rgba(255,255,255,0.2);" onclick="WebPushManager.dismissPromptBanner()">
                    Nanti
                </button>
            </div>
        </div>
    </div>
</div>
