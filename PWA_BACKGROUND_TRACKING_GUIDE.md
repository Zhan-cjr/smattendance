# PWA + Background Location Tracking Implementation

## 📱 Overview

Sistem tracking sekarang support **2 mode tracking**:

### 1️⃣ **Foreground Tracking** (Real-Time)
- ✅ Browser/app AKTIF (tab terbuka)
- ✅ Update lokasi **setiap 30 detik**
- ✅ Real-time di monitoring dashboard
- ✅ Support semua browser modern

### 2️⃣ **Background Tracking** (Periodic Sync) ⭐ NEW
- ✅ Browser/app DITUTUP
- ✅ Update lokasi **setiap 15-60 menit** (bergantung device)
- ✅ Automatic sync via Service Worker
- ⚠️ Hanya Chrome, Edge, Opera, Samsung Internet
- ⚠️ Perlu install PWA ke home screen

---

## 🔄 How It Works

```
SKENARIO 1: App/Browser Dibuka
├─ Live Location Tracker component aktif
├─ Real-time GPS tracking setiap 30 detik
├─ POST ke /monitoring-lokasi/saveLocation
└─ Data real-time di monitoring dashboard

SKENARIO 2: App/Browser Ditutup (PWA installed)
├─ Service Worker tetap aktif
├─ Periodic Background Sync trigger setiap 15-60 min
├─ Service Worker ambil GPS & send ke server
├─ POST ke /monitoring-lokasi/saveLocation
└─ Data update otomatis di monitoring
```

---

## 🚀 Installation & Setup

### Step 1: Files Sudah Dibuat
✅ Service Worker: `public/js/location-tracker-sw.js`
✅ PWA Component: `resources/views/components/pwa-service-worker-register.blade.php`
✅ Updated Dashboard: `resources/views/dashboard/karyawan.blade.php`
✅ Updated Manifest: `public/manifest.json`

### Step 2: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
```

### Step 3: User Install PWA
**Android Chrome/Edge:**
1. Buka aplikasi
2. Menu (⋮) → "Install app" atau "Add to Home Screen"
3. Confirm

**iOS Safari:**
1. Tap Share button
2. "Add to Home Screen"
3. Confirm

---

## 🧪 Testing

### Test 1: Verify Service Worker Registration
```
1. Login dashboard
2. DevTools → Console
3. Lihat pesan:
   ✓ "Service Worker registered successfully"
   ✓ "User data sent to Service Worker"
   ✓ "Periodic Background Sync registered"
```

### Test 2: Verify Real-Time Tracking (App Open)
```
1. Login dashboard
2. DevTools → Console
3. Lihat pesan setiap 30 detik:
   "✓ Location sent: lat, lng (accuracy: xxm)"
4. Check database:
   SELECT * FROM live_location WHERE nik='xxxxx' 
   ORDER BY tracked_at DESC LIMIT 10;
   # Harus ada data setiap ~30 detik
```

### Test 3: Verify Background Sync (App Closed)
```
1. Install app ke home screen (PWA)
2. Open app dari home screen
3. Close app completely (swipe away)
4. Tunggu 15-20 menit
5. Check database:
   SELECT * FROM live_location WHERE nik='xxxxx' 
   ORDER BY tracked_at DESC LIMIT 10;
   # Harus ada data baru setelah ~15+ min
6. Buka app kembali → verifikasi lebih banyak data
```

### Test 4: Check Service Worker in DevTools
**Chrome/Edge:**
1. DevTools → Application → Service Workers
2. Lihat: `location-tracker-sw.js` registered ✓
3. Check: "Offline" → should say "running"

**Check Periodic Sync Status:**
1. DevTools → Application → Manifest
2. Verify: "display": "standalone" ✓
3. DevTools → Application → Service Workers
4. Lihat background sync status

---

## 📊 Browser Compatibility

| Feature | Chrome | Edge | Firefox | Safari | Opera |
|---------|--------|------|---------|--------|-------|
| Service Worker | ✅ | ✅ | ✅ | ⚠️ | ✅ |
| Real-time Tracking | ✅ | ✅ | ✅ | ✅ | ✅ |
| Periodic Sync | ✅ 49+ | ✅ 18+ | ❌ | ❌ | ✅ |
| PWA Install | ✅ | ✅ | ⚠️ | ✅ | ✅ |

**Note:**
- Real-time tracking bekerja di semua browser (via foreground component)
- Background tracking HANYA di Chrome, Edge, Opera (via Periodic Sync)
- Firefox & Safari: Real-time only (tetap berjalan jika browser aktif di background)

---

## 🔐 Security

### CSRF Protection
- ✅ Service Worker menggunakan CSRF token dari meta tag
- ✅ Token di-refresh di setiap request

### User Authentication
- ✅ Service Worker hanya send location jika session valid
- ✅ User data di-store di IndexedDB dengan user ID
- ✅ Automatic logout stops tracking

### Data Privacy
- ✅ GPS coordinates hanya di-send ke server (tidak shared)
- ✅ Service Worker hanya aktif untuk authenticated users
- ✅ Background sync stopped saat user logout

---

## ⚙️ Configuration

### Adjust Tracking Interval

**File:** `resources/views/components/live-location-tracker.blade.php`

```javascript
// Line: trackingInterval: 30000
trackingInterval: 30000,  // 30 seconds (foreground)

// Untuk lebih sering:
trackingInterval: 10000,  // 10 seconds (lebih boros battery)

// Untuk lebih jarang:
trackingInterval: 60000,  // 1 minute (hemat battery)
```

### Adjust Background Sync Interval

**File:** `public/js/location-tracker-sw.js` 
dan `resources/views/components/pwa-service-worker-register.blade.php`

```javascript
// Line: minInterval: 15 * 60 * 1000
minInterval: 15 * 60 * 1000,  // 15 minutes minimum

// Untuk lebih sering:
minInterval: 5 * 60 * 1000,   // 5 minutes (browser controlled)

// Actual interval: 15-60 min (device dependent)
```

**Note:** Browser/device decide actual interval (15-60 min). Ini bukan exact.

---

## 🐛 Troubleshooting

### ❌ Service Worker Not Registering

**Problem:** DevTools tidak show service worker

**Solusi:**
1. Verify HTTPS enabled (Service Worker require HTTPS)
2. Check `public/js/location-tracker-sw.js` exists
3. Hard refresh: Ctrl+Shift+R
4. Clear Service Workers:
   - DevTools → Application → Service Workers
   - Klik "Unregister" → Refresh

### ❌ Periodic Sync Not Triggering

**Problem:** Background tracking tidak berjalan saat app ditutup

**Solusi:**
1. Install PWA ke home screen (requirement untuk periodic sync)
2. Open from home screen (tidak dari browser tab)
3. Enable notification permission
4. Check browser settings:
   - Chrome: Settings → Privacy → Site settings → Periodic background sync → Allow
5. Verify: DevTools → Application → Manifest → "display": "standalone"

### ❌ Location Data Not Sending (Background)

**Problem:** Background sync registered tapi data tidak terkirim

**Solusi:**
1. Verify user data stored in IndexedDB:
   ```javascript
   // Di DevTools Console:
   indexedDB.databases().then(dbs => console.log(dbs));
   // Harus ada LocationTrackerDB
   ```
2. Check Service Worker errors:
   - DevTools → Application → Service Workers
   - Klik "Show console messages"
3. Verify server endpoint accessible:
   - Test: `curl http://localhost/monitoring-lokasi/saveLocation`

### ❌ Tracking Works Foreground But Not Background

**Problem:** Real-time OK, tapi background sync tidak ada

**Solusi:**
1. Browser tidak support Periodic Sync? → Use Chrome/Edge
2. PWA tidak installed? → Install ke home screen
3. Device battery saver mode? → Disable untuk testing
4. Device storage full? → Free up space

---

## 📈 Monitoring

### Check Sync Queue
```javascript
// Di DevTools Console:
navigator.serviceWorker.ready.then(reg => {
  if ('sync' in reg) {
    console.log('Background Sync ready');
  }
});
```

### Verify Last Sync Time
```sql
-- Di MySQL:
SELECT 
  nik, 
  MAX(tracked_at) as last_sync,
  COUNT(*) as total_records,
  TIMESTAMPDIFF(MINUTE, MAX(tracked_at), NOW()) as min_ago
FROM live_location 
WHERE nik = 'xxxxx'
GROUP BY nik;
```

### Check Successful Syncs
```bash
# In Laravel logs:
tail -f storage/logs/laravel.log | grep "live location"

# Should see:
# [2026-04-23 10:15:30] local.INFO: Location sent via background sync
```

---

## 🎯 Best Practices

1. **For Employees:**
   - ✅ Install PWA ke home screen untuk background tracking
   - ✅ Biarkan GPS enabled sepanjang hari
   - ✅ Disable battery saver jika mau background sync reliable

2. **For Supervisors:**
   - ✅ Expect real-time updates (~30s) ketika employee app aktif
   - ✅ Background updates mungkin delay 15-60 menit (browser controlled)
   - ✅ Check monitoring dashboard regularly

3. **For Admins:**
   - ✅ Monitor database growth: `live_location` table bisa besar
   - ✅ Setup cleanup schedule: `php artisan schedule:run` untuk old data
   - ✅ Verify HTTPS enabled
   - ✅ Test periodically on real devices

---

## 📞 Support Notes

**For Users Who Don't Install PWA:**
- Still get real-time tracking (30s) while app open
- NO background tracking (normal browser behavior)

**For Users Who Install PWA:**
- Real-time tracking (30s) while app open
- Background tracking (15-60 min) when app closed
- Best experience for field workers

**For iOS Users:**
- PWA support limited (via Safari)
- Real-time tracking works ✅
- Background sync limited ⚠️

---

## 🚀 Next Steps

1. **Deploy & Test**
   - Deploy changes ke production
   - Test di real devices (Android phone recommended)
   - Collect feedback

2. **Monitor Performance**
   - Track database growth
   - Monitor battery impact
   - Adjust intervals if needed

3. **Iterate**
   - Adjust tracking intervals based on feedback
   - Fine-tune background sync timing
   - Optimize battery usage

---

**Status:** ✅ PWA + Background Tracking Implemented  
**Last Updated:** 2026-04-23
