# 🔧 GPS Tracking Debugging Guide

## ✅ What Was Fixed

1. **CSRF Token Missing** → Added `<meta name="csrf-token">` to dashboard
2. **Duplicate Tracking Code** → Removed conflicting JavaScript from dashboard
3. **Wrong Relation Access** → Fixed component to use `$user->userkaryawan->karyawan` instead of `$user->karyawan`

---

## 🧪 Testing Steps

### Step 1: Verify Database Setup
```bash
# SSH ke server, then run MySQL:
mysql> SELECT nik, nama_karyawan, spy FROM karyawan WHERE spy = 1 LIMIT 5;
# Should show: karyawan with spy=1
```

### Step 2: Verify User Login Structure
```bash
# In MySQL:
mysql> SELECT 
  u.id, u.name, uk.nik, k.spy, k.nama_karyawan
FROM users u
LEFT JOIN user_karyawan uk ON u.id = uk.id_user
LEFT JOIN karyawan k ON uk.nik = k.nik
WHERE u.id = (your_employee_user_id);

# Result harus menunjukkan:
# - user punya user_karyawan record
# - user_karyawan punya nik
# - karyawan punya spy=1
```

### Step 3: Employee Dashboard Testing
```
1. Login sebagai karyawan (dengan spy=1)
2. Buka DevTools → Console (F12)
3. Lihat pesan:
   ✓ "📍 Live Location Tracker initialized for NIK: xxxxx" 
     → Component load dengan benar
   ✓ "✓ Location permission granted"
     → GPS permission allowed
   ✓ "✓ Location sent: -6.xxxx, 106.xxxx (accuracy: xx.xxm)"
     → Data berhasil dikirim ke server
```

### Step 4: Verify Database Penyimpanan
```bash
# Tunggu 30 detik setelah login, lalu check:
mysql> SELECT 
  nik, latitude, longitude, accuracy, lokasi, tracked_at
FROM live_location 
WHERE nik = 'xxxxx'
ORDER BY tracked_at DESC 
LIMIT 5;

# Should show fresh records (last 30 seconds)
```

### Step 5: Monitoring Dashboard Test
```
1. Login sebagai supervisor
2. Buka /monitoring-lokasi
3. Select karyawan dari dropdown
4. Verifikasi:
   ✓ Map muncul
   ✓ Marker terlihat
   ✓ Lokasi info card terisi
   ✓ Riwayat table punya data
```

---

## 🐛 Troubleshooting

### ❌ "Data lokasi belum tersedia" di monitoring

**Kemungkinan Penyebab:**

1. **Component tidak load**
   ```javascript
   // Di console employee dashboard, ketik:
   window.LocationTracker
   // Jika undefined → component tidak load
   ```
   
   **Solusi:**
   - Verifikasi `spy=1` di database untuk karyawan
   - Verifikasi relation `user->userkaryawan->karyawan` sudah benar
   - Cek apakah component included di dashboard

2. **GPS Permission Denied**
   ```javascript
   // Console log akan tampil:
   // ⚠️ Location permission denied
   ```
   
   **Solusi di Chrome:**
   - Klik lock icon di address bar
   - Settings → Location → Allow
   - Refresh page

3. **CSRF Token Hilang**
   ```javascript
   // Di console:
   document.querySelector('meta[name="csrf-token"]')?.content
   // Jika null → CSRF meta tidak ada
   ```
   
   **Solusi:** Verifikasi sudah tambah `<meta name="csrf-token">` ke dashboard

4. **Data tidak tersimpan di database**
   ```bash
   # Check server logs:
   tail -f storage/logs/laravel.log | grep live_location
   
   # Jika ada error, akan tampil di sini
   ```

5. **NIK tidak terdeteksi di component**
   ```javascript
   // Di console dashboard:
   console.log(window.LocationTracker.nik)
   // Jika empty string → component tidak bisa akses karyawan data
   ```
   
   **Solusi:**
   - Verify di MySQL:
   ```sql
   SELECT u.id, u.name, uk.nik
   FROM users u
   LEFT JOIN user_karyawan uk ON u.id = uk.id_user
   WHERE u.id = (logged_in_user_id);
   ```
   - Jika nik NULL → user tidak link ke karyawan

---

## 📊 Complete Data Flow Debugging

### Trace 1: Employee Browser → Server
```
Browser DevTools Console:
├─ "📍 Live Location Tracker initialized" 
├─ "✓ Location permission granted"
├─ "✓ Location sent: lat, lng"
└─ Network tab: POST /monitoring-lokasi/saveLocation → 200 OK
```

### Trace 2: Database Verification
```bash
# Right after POST request:
mysql> SELECT * FROM live_location 
WHERE nik='xxxxx' 
ORDER BY created_at DESC LIMIT 1;
# Should see new record with exact lat/lng/accuracy from console
```

### Trace 3: Monitoring Dashboard → Display
```
Browser DevTools Console:
├─ Network tab: GET /monitoring-lokasi/getData?nik=xxxxx → 200 OK
├─ Response preview: {"status":true, "latestLocation": {...}}
└─ Map updates with marker

OR errors:
├─ 404: Tidak ada data lokasi
├─ 403: Permission denied (spy not enabled)
└─ 400: NIK tidak diberikan
```

---

## 🔍 Advanced Debugging

### Enable Verbose Logging
Edit `resources/views/components/live-location-tracker.blade.php` line 35:
```javascript
// Find section:
handleLocationError(error) {
    console.error('❌ Location tracking error:', {
        code: error.code,
        message: error.message,
        // Add more details:
        codeNames: {1:'PERMISSION_DENIED', 2:'POSITION_UNAVAILABLE', 3:'TIMEOUT'}
    });
}
```

### Manual Test via cURL
```bash
# Test API endpoint manually:
curl -X POST http://localhost/monitoring-lokasi/saveLocation \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -H "Cookie: XSRF-TOKEN=your_token; laravel_session=your_session" \
  -d '{
    "latitude": -6.2088,
    "longitude": 106.8456,
    "accuracy": 25
  }'

# Should return:
# {"status":true,"message":"Lokasi berhasil disimpan",...}
```

### Check Browser Geolocation Permissions
```javascript
// In console:
navigator.permissions.query({name: 'geolocation'})
  .then(result => console.log('Permission:', result.state))
  // state: 'granted', 'denied', or 'prompt'
```

---

## ✅ Verification Checklist

- [ ] Dashboard karyawan show component render (right-click → View Source → search `liveLocationTracker`)
- [ ] DevTools Console show "📍 Live Location Tracker initialized"
- [ ] DevTools Console show "✓ Location sent" message
- [ ] Database `live_location` table punya data terbaru
- [ ] Monitoring lokasi bisa select karyawan
- [ ] Map muncul dengan marker
- [ ] Riwayat lokasi table punya data
- [ ] Marker bergerak saat GPS berubah

---

## 📞 When Nothing Works

1. **Clear cache & restart:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   # Refresh browser
   ```

2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   # Look for any errors related to live_location
   ```

3. **Verify migrations:**
   ```bash
   mysql> DESCRIBE live_location;
   # Should have: id, nik, latitude, longitude, accuracy, lokasi, tracked_at, created_at, updated_at
   ```

4. **Hard refresh browser:**
   - Windows/Linux: `Ctrl+Shift+R`
   - Mac: `Cmd+Shift+R`

5. **Test in different browser:**
   - Firefox, Safari (geolocation implementation varies)

---

**Last Updated**: 2026-04-22  
**Status**: Fixed & Ready for Testing
