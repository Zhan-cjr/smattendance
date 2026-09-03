# 🚀 Live Location Tracking - Quick Start Guide

## Apa Itu?
Sistem auto-tracking lokasi real-time untuk karyawan dengan `spy=1`. Seperti WhatsApp Live Location, tapi untuk monitoring karyawan di aplikasi web/mobile Anda.

## 📋 Checklist Setup

### ✅ Backend Sudah Siap
- [x] Controller method `saveLocation()` ditambahkan
- [x] Route `/monitoring-lokasi/saveLocation` (POST) dibuat
- [x] Database table `live_location` sudah ada
- [x] Cleanup command `location:cleanup` dibuat
- [x] Reverse geocoding untuk nama lokasi otomatis

### ⏳ TODO: Integrasi ke Frontend

## 🔧 Step-by-Step Integration

### Langkah 1: Include Components di Dashboard
Edit file dashboard Anda (misalnya `resources/views/dashboard.blade.php` atau `resources/views/presensi/index.blade.php`):

```blade
@extends('layouts.app')

@section('content')

{{-- ADD THIS LINE --}}
<x-live-location-tracker />

{{-- OPTIONAL: Add status badge --}}
<x-location-tracking-badge />

<div class="container-xxl">
    {{-- Your existing dashboard content --}}
</div>

@endsection
```

### Langkah 2: Verify User Model
Pastikan `app/Models/User.php` punya relationship ke Karyawan:

```php
public function karyawan()
{
    return $this->belongsTo(Karyawan::class, 'nik', 'nik');
}
```

### Langkah 3: Test Integrasi
1. Login dengan akun yang punya `spy=1`
2. Buka browser console (F12)
3. Lihat logs:
   ```
   📍 Live Location Tracker initialized for NIK: 11.11.111
   ✓ Location permission granted
   🟢 Location tracking started - updating every 10 seconds
   ```
4. Accept GPS permission popup

### Langkah 4: Verify Data Tersimpan
```bash
# Terminal
php artisan tinker

> DB::table('live_location')->where('nik', '11.11.111')->latest('tracked_at')->first();
```

Seharusnya ada data baru dengan timestamp terbaru.

## 📱 Cara Kerja (Flow)

```
USER LOGIN (spy=1)
    ↓
Dashboard loaded
    ↓
Component <x-live-location-tracker /> dimuat
    ↓
Auto-request GPS permission
    ↓
Start tracking: setiap 10 detik capture GPS location
    ↓
Send ke /monitoring-lokasi/saveLocation (POST)
    ↓
Server save ke live_location table
    ↓
Admin lihat di /monitoring-lokasi
```

## 🎛️ Konfigurasi

### Ubah Interval Tracking
Edit: `resources/views/components/live-location-tracker.blade.php`

Line 18:
```javascript
trackingInterval: 10000, // ubah ke nilai lain
// 5000 = 5 detik
// 30000 = 30 detik
// 60000 = 1 menit
```

### Ubah Minimum Accuracy
Line 20:
```javascript
minAccuracy: 100, // ubah threshold (meters)
// Semakin kecil = lebih ketat
// 50 = hanya kirim jika akurasi < 50m
```

### Disable Reverse Geocoding
Edit: `app/Http/Controllers/MonitoringLokasiController.php`

Di method `saveLocation()` line 295:
```php
// Comment line ini untuk disable
// $lokasi = $this->reverseGeocode($validated['latitude'], $validated['longitude']);
```

## 🧪 Testing

### Test 1: Check Component Loaded
```javascript
// Console
window.LocationTracker
// Should show object with methods
```

### Test 2: Manual Trigger Location Send
```javascript
// Console
LocationTracker.trackLocationNow()
// Check Network tab untuk POST request ke /monitoring-lokasi/saveLocation
```

### Test 3: Check Database
```bash
php artisan tinker
> \App\Models\LiveLocation::latest()->first()
```

### Test 4: View di Admin Panel
1. Login as Admin
2. Go to `/monitoring-lokasi`
3. Select employee dari dropdown
4. Should see live location on map

## 📊 Monitoring Data

### Via Admin Dashboard
```
URL: /monitoring-lokasi
```
- Select karyawan dari dropdown
- Lihat map real-time
- Lihat location history
- Auto-update setiap 5 detik

### Via Database
```sql
-- Cek lokasi terbaru per karyawan
SELECT nik, latitude, longitude, lokasi, accuracy, tracked_at
FROM live_location
WHERE tracked_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
ORDER BY tracked_at DESC;

-- Cek pergerakan satu karyawan
SELECT * FROM live_location
WHERE nik = '11.11.111'
ORDER BY tracked_at DESC
LIMIT 50;
```

## 🐛 Troubleshooting

### Problem: "Lokasi tidak muncul di monitoring page"
**Solusi:**
1. Verify `spy=1` di database: `SELECT spy FROM karyawan WHERE nik='11.11.111'`
2. Check browser console untuk GPS permission errors
3. Verify user relationship ke Karyawan model
4. Check network tab di DevTools untuk POST requests

### Problem: "GPS Permission Denied"
**Solusi:**
- Chrome: Klik lock icon di address bar → Permission → Allow Location
- Firefox: Allow notification yang muncul
- Safari: Settings → Privacy → Location → Allow
- Pastikan URL menggunakan HTTPS (production)

### Problem: "Accuracy terlalu jelek (> 100m)"
**Solusi:**
- User harus outdoor dengan line-of-sight ke langit
- Indoor accuracy biasanya 30-200m tergantung signal
- Decrease `minAccuracy` threshold di config

### Problem: "Lokasi selalu sama / tidak bergerak"
**Solusi:**
- Pastikan GPS location services enabled di device
- Walk lebih jauh (minimal 10m untuk trigger send)
- Check timestamp di database apakah ter-update

## 📈 Production Deployment

### 1. Enable HTTPS
Geolocation API hanya bekerja di HTTPS (kecuali localhost)

### 2. Setup Cleanup Job
Edit: `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Cleanup old location data (keep 30 days)
    $schedule->command('location:cleanup --days=30')->daily();
}
```

### 3. Database Indexing
Sudah included di migration, tapi verify:
```sql
SHOW INDEX FROM live_location;
-- Should have indexes on: nik, tracked_at
```

### 4. Rate Limiting (Optional)
Add di `app/Http/Controllers/MonitoringLokasiController.php`:
```php
public function saveLocation(Request $request)
{
    $this->validateRateLimit();
    // ... rest of code
}

private function validateRateLimit()
{
    $user = auth()->user();
    return RateLimiter::attempt(
        'location:' . $user->id,
        120, // 120 requests
        fn() => true,
        1 // per 1 second
    );
}
```

## 📚 Files Created/Modified

### Created:
- ✅ `resources/views/components/live-location-tracker.blade.php` - Main tracking component
- ✅ `resources/views/components/location-tracking-badge.blade.php` - Status badge
- ✅ `app/Console/Commands/LocationCleanup.php` - Data cleanup command
- ✅ `resources/views/dashboard-live-location-example.blade.php` - Example integration
- ✅ `docs/LIVE_LOCATION_TRACKING_SETUP.md` - Full documentation

### Modified:
- ✅ `app/Http/Controllers/MonitoringLokasiController.php` - Added saveLocation() method
- ✅ `routes/web.php` - Added POST route for saveLocation

## 🔗 Related Documentation
- [Full Setup Guide](docs/LIVE_LOCATION_TRACKING_SETUP.md)
- [Monitoring Lokasi Doc](docs/MONITORING_LOKASI_TROUBLESHOOTING.md)
- [Mobile App Integration Examples](docs/LIVE_LOCATION_TRACKING_SETUP.md#mobile-app-integration)

## 💡 Tips & Best Practices

1. **Adjust tracking interval** berdasarkan kebutuhan:
   - Field work: 10-30 detik
   - Office work: 1 menit
   - Night shift: 5 menit

2. **Monitor database size**:
   ```sql
   SELECT 
       ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
   FROM information_schema.TABLES 
   WHERE table_name = 'live_location';
   ```

3. **Battery optimization** untuk mobile:
   - Increase interval di malam hari
   - Disable saat indoor jika accuracy buruk
   - Stop tracking saat off-duty

4. **Privacy considerations**:
   - Only admins dapat lihat location
   - Log semua akses monitoring
   - Notify employees tracking aktif

## ✉️ Support
Jika ada masalah, check:
1. Browser console (F12) untuk JS errors
2. Server logs: `storage/logs/laravel.log`
3. Network tab untuk failed requests
4. Database untuk verify data tersimpan
