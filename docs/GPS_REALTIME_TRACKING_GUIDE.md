# GPS Real-Time Tracking Implementation Guide

## Overview
Sistem tracking lokasi real-time karyawan bekerja dengan menggunakan browser geolocation API untuk menangkap GPS karyawan secara tersembunyi (background) saat login di dashboard. Lokasi ini kemudian ditampilkan secara live di monitoring lokasi oleh supervisor.

## Arsitektur Sistem

### 1. Employee Side (Background GPS Capture)
**Komponen**: `resources/views/components/live-location-tracker.blade.php`

- Otomatis berjalan saat karyawan dengan `spy=1` login
- Menangkap lokasi GPS setiap 30 detik
- Mengirim ke server: `POST /monitoring-lokasi/saveLocation`
- Berjalan di background tanpa interupsi ke employee workflow

### 2. Server Side (Location Storage)
**Controller**: `app/Http/Controllers/MonitoringLokasiController.php::saveLocation()`

- Menerima data GPS dari employee
- Validasi: latitude, longitude, accuracy
- Reverse geocoding (opsional): mencari nama lokasi dari koordinat
- Simpan ke database: `live_location` table dengan timestamp

### 3. Supervisor Side (Real-Time Display)
**View**: `resources/views/monitoring-lokasi/index.blade.php`

- Pilih karyawan dari dropdown
- Auto-refresh setiap 5 detik (adjustable 3s-1m)
- Tampilkan peta dengan marker lokasi terkini
- Riwayat lokasi: path garis di peta + tabel data

## Konfigurasi GPS Tracking

File: `resources/views/components/live-location-tracker.blade.php` (lines 18-23)

```javascript
trackingInterval: 30000,     // Interval tracking (ms) - 30 detik optimal
minAccuracy: 50,             // Accuracy threshold (m) - keseimbangan antara data valid dan CPU
maxAccuracy: 500,            // Max acceptable accuracy - jangan kirim jika lebih jelek
maxFailedAttempts: 5,        // Berhenti after N failures untuk save battery
```

### Penjelasan Nilai
- **trackingInterval (30s)**: Optimal untuk battery life & server load. Bisa dikurangi untuk tracking lebih real-time (3-10s) tapi lebih boros baterai
- **minAccuracy (50m)**: GPS consumer biasanya 20-50m urban, 50-100m outdoor. 50m adalah sweet spot
- **maxAccuracy (500m)**: Safety threshold. Jangan gunakan data GPS yang sangat tidak akurat

## Cara Kerja Detail

### Step 1: Employee Login
```
1. Employee login di dashboard karyawan
2. Component live-location-tracker auto-load
3. Check: apakah spy=1 di database karyawan?
   - Jika tidak → component tidak jalan
   - Jika ya → lanjut ke step 2
```

### Step 2: Permission & Initialization
```
1. Browser meminta permission akses lokasi ke employee
2. Tracking dimulai background (tidak perlu wait permission)
3. Setiap 30 detik: ambil GPS location
4. Jika accuracy OK (50-500m) → kirim ke server
5. Jika accuracy jelek atau timeout → skip, retry next cycle
```

### Step 3: Data Flow
```
Employee Browser
    ↓
navigator.geolocation.watchPosition()  [Browser API]
    ↓
GPS Data: {latitude, longitude, accuracy}
    ↓
POST /monitoring-lokasi/saveLocation
    ↓
Server Validation & Storage
    ↓
live_location table
    │
    └─→ ID | NIK | latitude | longitude | accuracy | lokasi | tracked_at | created_at | updated_at
```

### Step 4: Supervisor Monitoring
```
1. Supervisor buka: /monitoring-lokasi
2. Pilih karyawan dari dropdown
3. Map initialize dengan lokasi employee terbaru
4. Auto-refresh setiap 5 detik (bisa ubah interval)
5. Fetch: GET /monitoring-lokasi/getData?nik=xxxxx
   Response: {latestLocation, history}
6. Update marker di map
7. Draw path dari history locations
```

## Checklist Troubleshooting

### GPS Tidak Capture

**Gejala**: Marker tidak muncul atau stuck di lokasi lama

**Solusi**:
```
1. ✓ Cek karyawan punya spy=1 di database
   SELECT spy FROM karyawan WHERE nik='xxxxx';
   
2. ✓ Buka browser console (F12) saat employee dashboard
   Cari: "📍 Live Location Tracker initialized for NIK: xxxxx"
   Jika tidak ada → component tidak load
   
3. ✓ Cek GPS permission di browser
   Chrome: address bar lock icon → Permission Settings → Location: Allow
   
4. ✓ Lihat log tracking di console
   ✓ = berhasil send
   ⚠️ = accuracy issue / timeout
   ❌ = error
   
5. ✓ Test manual dengan curl:
   curl -X POST http://localhost/monitoring-lokasi/saveLocation \
     -H "Content-Type: application/json" \
     -H "X-CSRF-TOKEN: {token}" \
     -d '{
       "latitude": -6.2088,
       "longitude": 106.8456,
       "accuracy": 25
     }'
```

### Peta Tidak Update Real-Time

**Gejala**: Data di table riwayat ada, tapi peta tidak berubah

**Solusi**:
```
1. ✓ Cek interval refresh tidak terlalu lama
   Buka monitoring lokasi → lihat selector "Interval Auto-Refresh"
   Ubah ke "Setiap 3 Detik" untuk testing
   
2. ✓ Verifikasi API response:
   Browser DevTools → Network tab
   Filter: getData
   Lihat response: apakah status=true dan ada data terbaru?
   
3. ✓ Cek permission: `/monitoring-lokasi` memerlukan `presensi.index` permission
```

### Data Lama di Database

**Gejala**: Lokasi di monitoring tidak update meski sudah tracking

**Solusi**:
```
1. ✓ Check: ada data baru di live_location table?
   SELECT * FROM live_location WHERE nik='xxxxx' ORDER BY tracked_at DESC LIMIT 5;
   
2. ✓ Jika ada tapi monitoring tidak tampil:
   - Cek accuracy: apakah < 500m?
   - Cek timestamp: apakah recent? (checked dalam getData method)
   
3. ✓ Clear cache jika perlu:
   rm -rf storage/framework/cache/data/*
```

## Performance & Battery Optimization

### Untuk Smartphone Battery
```javascript
// Production setting (battery-friendly)
trackingInterval: 60000,      // 1 menit tracking
enableHighAccuracy: true,     // tapi fokus accuracy daripada sering
maxAccuracy: 100              // more strict = less data sent = less battery
```

### Untuk Real-Time Tracking (dalam kantor)
```javascript
// Testing/Demo setting
trackingInterval: 10000,      // 10 detik
minAccuracy: 30,              // lebih lenient
```

## Database Optimization

### Clean Old Data
```bash
# Run command ini secara regular (schedule)
php artisan command:CleanupOldLocationData --days=30

# Atau manual:
DELETE FROM live_location WHERE tracked_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Index untuk Performance
```sql
-- Pastikan ada indexes:
ALTER TABLE live_location ADD INDEX idx_nik_tracked (nik, tracked_at DESC);
ALTER TABLE live_location ADD INDEX idx_tracked_at (tracked_at DESC);
```

## API Endpoints Reference

### 1. Save Location (Employee)
```
POST /monitoring-lokasi/saveLocation
Headers: 
  - X-CSRF-TOKEN: {csrf_token}
  - Content-Type: application/json

Body:
{
  "latitude": -6.2088,
  "longitude": 106.8456,
  "accuracy": 25.5
}

Response:
{
  "status": true,
  "message": "Lokasi berhasil disimpan",
  "data": {
    "id": 123,
    "nik": "123456789",
    "latitude": "-6.2088",
    "longitude": "106.8456",
    "lokasi": "Jakarta, Indonesia",
    "accuracy": 25.5,
    "tracked_at": "2026-04-22 10:30:45"
  }
}
```

### 2. Get Latest Location (Supervisor)
```
GET /monitoring-lokasi/getData?nik=123456789
Headers:
  - Authorization: Bearer {token}

Response:
{
  "status": true,
  "latestLocation": {
    "latitude": "-6.2088",
    "longitude": "106.8456",
    "lokasi": "Jakarta, Indonesia",
    "accuracy": 25.5,
    "tracked_at": "2026-04-22 10:30:45",
    "time_ago": "5 minutes ago"
  },
  "history": [
    {"latitude": "-6.2080", "longitude": "106.8450", ...},
    ...
  ]
}
```

### 3. Get Location History
```
GET /monitoring-lokasi/getLocationHistory?nik=123456789
```

## Monitoring & Logging

### Browser Console Logs

#### Success (✓)
```
📍 Location sent: -6.208842, 106.845603 (accuracy: 25.45m)
```

#### Warning (⚠️)
```
⚠️ Accuracy 120.45m is worse than threshold 50m, but sending due to lack of better data
⚠️ Location tracking error: 2 (POSITION_UNAVAILABLE)
```

#### Error (❌)
```
❌ Location tracking failed: Permission denied
❌ Failed to send location: Network error
```

### Server Logging
```
# Check Laravel logs:
tail -f storage/logs/laravel.log | grep "live location"

# Should see:
[2026-04-22 10:30:45] local.INFO: Live location tracked for employee: 123456789
```

## Testing Integration

### Test 1: GPS Capture
```
1. Login as karyawan (dengan spy=1)
2. Open dashboard
3. Open DevTools Console (F12)
4. Ubah location di browser DevTools:
   - Chrome: More Tools → Sensors → Location
   - Set coordinate
5. Lihat console message: "📍 Location sent..."
```

### Test 2: Real-Time Monitoring
```
1. Buka monitoring-lokasi di browser baru
2. Select karyawan
3. Ubah GPS location di employee browser
4. Tunggu 3-5 detik
5. Marker di map harus bergerak
6. Timestamp di card harus update
```

### Test 3: Path History
```
1. Buka monitoring lokasi
2. Ubah GPS 5-10 kali dengan lokasi berbeda
3. Lihat polyline (garis path) harus nambah
4. Tabel riwayat harus ada 5-10 records baru
```

## FAQ

### Q: Berapa akurat GPS tracking?
A: Tergantung device & kondisi. Urban (20-50m), Outdoor (50-100m), Indoor (tidak akurat). GPS consumer bukan untuk presisi, tapi untuk area monitoring.

### Q: Berapa banyak battery yang dipakai?
A: Dengan interval 30 detik & enableHighAccuracy, sekitar 5-10% per jam tergantung device.

### Q: Bisa disable untuk employee tertentu?
A: Ya, set `spy=0` di karyawan table. Instant stop tracking.

### Q: Data lokasi disimpan berapa lama?
A: Default 30 hari. Edit di `.env` atau schedule command untuk cleanup.

### Q: Apakah HTTPS required?
A: Ya, geolocation hanya jalan di HTTPS (atau localhost untuk dev).

---

**Last Updated**: 2026-04-22
**Version**: 1.1
