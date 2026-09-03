# IMPLEMENTATION CHECKLIST - Live Location Monitoring Feature

## 📋 Status: COMPLETED ✅

---

## 1. DATABASE & MODELS ✅

### Migrations Created:
- [x] `database/migrations/2026_04_22_000001_add_spy_to_karyawan_table.php`
  - Tambah field `spy` TINYINT(1) DEFAULT 0 ke tabel `karyawan`
  - Comment: "1 = Enable live location monitoring, 0 = Disable"

- [x] `database/migrations/2026_04_22_000002_create_live_location_table.php`
  - Buat tabel `live_location` untuk menyimpan lokasi tracking
  - Columns: id, nik, latitude, longitude, lokasi, accuracy, tracked_at, created_at, updated_at
  - FK: nik → karyawan.nik (CASCADE DELETE)
  - Indexes: nik, tracked_at

### Models Updated:
- [x] `app/Models/Karyawan.php`
  - Add relationship: `liveLocations()` (hasMany)
  - Add relationship: `latestLiveLocation()` (hasOne, latest)

- [x] `app/Models/LiveLocation.php` (New)
  - Model untuk tabel `live_location`
  - Fillable: nik, latitude, longitude, lokasi, accuracy, tracked_at
  - Relationship: `karyawan()` (belongsTo)

---

## 2. CONTROLLERS ✅

### API Controllers:
- [x] `app/Http/Controllers/Api/LiveLocationController.php` (New)
  - `store()` - Simpan lokasi dari mobile (POST /api/live-location/store)
  - `getLatest()` - Ambil lokasi terakhir (GET /api/live-location/{nik}/latest)
  - `getHistory()` - Ambil riwayat lokasi (GET /api/live-location/{nik}/history)
  - `getAllSpyLocations()` - Ambil semua spy karyawan (GET /api/live-location/all/spy-locations)
  - `cleanupOldData()` - Hapus data lama (DELETE /api/live-location/cleanup/old-data)

### Web Controllers:
- [x] `app/Http/Controllers/MonitoringLokasiController.php` (New)
  - `index()` - Dashboard monitoring lokasi (GET /monitoring-lokasi)
  - `getData()` - AJAX endpoint untuk real-time update (GET /monitoring-lokasi/getData)
  - `getSpyEmployees()` - Daftar karyawan spy (GET /monitoring-lokasi/getSpyEmployees)
  - `getLocationHistory()` - History lokasi (GET /monitoring-lokasi/getLocationHistory)

---

## 3. ROUTES ✅

### Web Routes (routes/web.php):
- [x] Import MonitoringLokasiController
- [x] Add middleware group for monitoring-lokasi:
  - `GET /monitoring-lokasi` → index
  - `GET /monitoring-lokasi/getData` → getData
  - `GET /monitoring-lokasi/getSpyEmployees` → getSpyEmployees
  - `GET /monitoring-lokasi/getLocationHistory` → getLocationHistory
  - Permission: `presensi.index`

### API Routes (routes/api.php):
- [x] Add live-location prefix group:
  - `POST /api/live-location/store` → store
  - `GET /api/live-location/{nik}/latest` → getLatest
  - `GET /api/live-location/{nik}/history` → getHistory
  - `GET /api/live-location/all/spy-locations` → getAllSpyLocations
  - `DELETE /api/live-location/cleanup/old-data` → cleanupOldData

---

## 4. VIEWS ✅

### Dashboard Views:
- [x] `resources/views/monitoring-lokasi/index.blade.php` (New)
  - Filter dropdown: Pilih karyawan (spy=1)
  - Map section: Leaflet.js dengan marker & polyline
  - Employee info card: Nama, NIK, Jabatan, Dept, Cabang
  - Current location card: Lat, Lng, Lokasi, Akurasi, Waktu
  - Refresh controls: Interval selection, Manual refresh, Stop button
  - History table: Riwayat lokasi (last 24 hours)
  - Real-time updates: Auto-refresh every 5s

### Mobile Views:
- [x] `resources/views/dashboard/karyawan.blade.php` (Updated)
  - Add JavaScript functions:
    - `initializeLiveLocationTracking()` - Initialize tracking
    - `startLocationTracking()` - Start tracking
    - `sendLocationToServer()` - Send location
    - `sendToAPI()` - API call to store location
    - `calculateDistance()` - Haversine formula
    - `stopLocationTracking()` - Stop tracking
  - Auto-detect: Check `spy=1` from auth()->user()->karyawan->spy
  - Geolocation: navigator.geolocation.getCurrentPosition()
  - Frequency: Every 30 seconds
  - Smart send: Only if distance > 50 meters

### Sidebar:
- [x] `resources/views/layouts/sidebar.blade.php` (Updated)
  - Add monitoring-lokasi to open/active check
  - Add submenu: "Monitoring Lokasi" with map-pin icon
  - Permission: `presensi.index`

---

## 5. JAVASCRIPT/FRONTEND ✅

### Libraries Used:
- [x] Leaflet.js 1.9.4 (Maps)
  - OpenStreetMap tile layer
  - Custom markers
  - Polyline path drawing
  - Bounds fitting

- [x] Geolocation API
  - navigator.geolocation.getCurrentPosition()
  - enableHighAccuracy: false
  - timeout: 10 seconds
  - maximumAge: 0

- [x] Fetch API
  - POST to /api/live-location/store
  - GET to /monitoring-lokasi/getData
  - CSRF token handling

- [x] jQuery
  - AJAX calls
  - DOM manipulation

---

## 6. COMMANDS ✅

### Console Commands:
- [x] `app/Console/Commands/CleanupOldLocationData.php` (New)
  - Command: `php artisan location:cleanup`
  - Option: `--days=N` (default: 7)
  - Delete records older than N days

---

## 7. DOCUMENTATION ✅

- [x] `docs/MONITORING_LOKASI_DOCUMENTATION.md`
  - Pengenalan fitur
  - Cara kerja (diagram alur)
  - Konfigurasi database
  - Implementasi lengkap
  - API endpoints documentation
  - Database schema & queries
  - Penggunaan step-by-step
  - Maintenance & troubleshooting
  - Performance benchmark

---

## 8. FEATURES IMPLEMENTED ✅

### Mobile (Karyawan Dashboard):
- [x] Automatic location detection saat page load
- [x] Geolocation permission request
- [x] Check spy=1 dari database
- [x] Send location every 30 seconds
- [x] Smart distance check (only send if > 50m)
- [x] Background tracking (even if tab not active)
- [x] Automatic cleanup on logout/page unload

### Backoffice (Admin Dashboard):
- [x] Menu: Presensi → Monitoring Lokasi
- [x] Filter dropdown: List karyawan dengan spy=1
- [x] Map display: Leaflet dengan marker lokasi terkini
- [x] Path display: Polyline dari riwayat pergerakan
- [x] Current location card: Lat, Lng, Akurasi, Waktu
- [x] Employee info card: Semua data karyawan
- [x] History table: Riwayat lokasi 24 jam
- [x] Auto-refresh: Configurable interval (3s - 1 menit)
- [x] Manual refresh: Button untuk update sekarang
- [x] Stop monitoring: Button untuk stop tracking

### API Features:
- [x] Store lokasi dengan validation
- [x] Get latest location
- [x] Get history dengan time range
- [x] Get all spy employees
- [x] Cleanup old data
- [x] Error handling & logging

---

## 9. IMPLEMENTATION STEPS ✅

### Untuk Deploy:

```bash
# 1. Run migrations
php artisan migrate

# 2. Clear cache (optional)
php artisan cache:clear
php artisan config:clear

# 3. Setup scheduled cleanup (optional)
# Edit: app/Console/Kernel.php
# Add: $schedule->command('location:cleanup --days=7')->dailyAt('01:00');

# 4. Test API endpoints
curl -X POST http://localhost:8000/api/live-location/store \
  -H "Content-Type: application/json" \
  -d '{"nik":"001234567","latitude":"-6.2234","longitude":"106.7890","accuracy":8.5}'

# 5. Access dashboard
# http://localhost:8000/monitoring-lokasi
```

---

## 10. PERMISSION REQUIREMENTS ✅

### Required Permission:
- [x] `presensi.index` - Access monitoring lokasi dashboard
  - Assign ke roles: Admin, HRD, Supervisor

### Suggestion:
- Consider adding new permission: `monitoring-lokasi.view` untuk granular control

---

## 11. DATABASE FIELDS REFERENCE ✅

### Karyawan Table (Updated):
```sql
ALTER TABLE karyawan ADD spy TINYINT DEFAULT 0;
```

**Usage**:
```sql
-- Enable tracking untuk karyawan
UPDATE karyawan SET spy = 1 WHERE nik = '001234567';

-- Disable tracking
UPDATE karyawan SET spy = 0 WHERE nik = '001234567';

-- List semua spy karyawan
SELECT nik, nama_karyawan FROM karyawan WHERE spy = 1 AND status_aktif_karyawan = 1;
```

### Live_Location Table (New):
```sql
CREATE TABLE live_location (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nik CHAR(9) NOT NULL,
    latitude VARCHAR(20) NOT NULL,
    longitude VARCHAR(20) NOT NULL,
    lokasi VARCHAR(255),
    accuracy FLOAT(8,2),
    tracked_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nik) REFERENCES karyawan(nik) ON DELETE CASCADE,
    INDEX (nik),
    INDEX (tracked_at)
);
```

---

## 12. FILES CREATED/MODIFIED ✅

### New Files:
1. `database/migrations/2026_04_22_000001_add_spy_to_karyawan_table.php`
2. `database/migrations/2026_04_22_000002_create_live_location_table.php`
3. `app/Models/LiveLocation.php`
4. `app/Http/Controllers/Api/LiveLocationController.php`
5. `app/Http/Controllers/MonitoringLokasiController.php`
6. `app/Console/Commands/CleanupOldLocationData.php`
7. `resources/views/monitoring-lokasi/index.blade.php`
8. `docs/MONITORING_LOKASI_DOCUMENTATION.md`

### Modified Files:
1. `app/Models/Karyawan.php` - Add relationships
2. `routes/web.php` - Add monitoring-lokasi routes + import
3. `routes/api.php` - Add live-location API routes
4. `resources/views/layouts/sidebar.blade.php` - Add submenu
5. `resources/views/dashboard/karyawan.blade.php` - Add location tracking JS

---

## 13. TESTING CHECKLIST ✅

### Manual Testing:

```bash
# 1. Test Migration
php artisan migrate

# 2. Test API Endpoints
POST /api/live-location/store
GET /api/live-location/{nik}/latest
GET /api/live-location/{nik}/history
GET /api/live-location/all/spy-locations

# 3. Test Dashboard
- Navigate to /monitoring-lokasi
- Pilih karyawan dari dropdown
- Verify map loads dengan marker
- Verify history table populate
- Test auto-refresh
- Test manual refresh
- Test stop button

# 4. Test Mobile
- Login ke dashboard karyawan
- Check geolocation permission
- Check if location sent to API
- Verify in database: live_location table

# 5. Test Cleanup Command
php artisan location:cleanup --days=7
```

### Browser Console Tests:

```javascript
// Check if spy value loaded
console.log('Spy:', {{ auth()->user()->karyawan->spy }});

// Check permission
navigator.permissions.query({name: 'geolocation'}).then(r => console.log(r.state));

// Get current position
navigator.geolocation.getCurrentPosition(pos => {
    console.log('Lat:', pos.coords.latitude);
    console.log('Lng:', pos.coords.longitude);
    console.log('Accuracy:', pos.coords.accuracy);
});

// Check API response
fetch('/api/live-location/all/spy-locations').then(r => r.json()).then(d => console.log(d));
```

---

## 14. PERFORMANCE OPTIMIZATION ✅

### Implemented:
- [x] Index on `nik` untuk fast lookup
- [x] Index on `tracked_at` untuk time-based queries
- [x] Soft location send (50m threshold)
- [x] AJAX polling instead of WebSocket (simpler)
- [x] Data cleanup schedule (7-day retention)
- [x] Limit history fetch (50 records max)

### Future Optimization:
- [ ] Implement WebSocket untuk real-time updates
- [ ] Redis caching untuk latest location
- [ ] Data archiving untuk old records
- [ ] Batch API calls (send multiple locations)

---

## 15. SECURITY CONSIDERATIONS ✅

### Implemented:
- [x] Permission check: `presensi.index`
- [x] Server-side validation: Check spy=1 before saving
- [x] CSRF token requirement
- [x] HTTPS recommended (for production)
- [x] No sensitive data in logs

### Recommendations:
- [ ] Encrypt location data at rest
- [ ] Rate limiting on API endpoints
- [ ] Audit log untuk access monitoring dashboard
- [ ] User consent notification saat spy=1

---

## 16. BROWSER COMPATIBILITY ✅

### Supported:
- [x] Chrome/Edge (Modern)
- [x] Firefox (Modern)
- [x] Safari (Modern)
- [x] Mobile browsers (iOS Safari, Chrome Android)

### Requires:
- [x] Geolocation API support
- [x] Fetch API support
- [x] LocalStorage support
- [x] JavaScript enabled

---

## 17. PRODUCTION CHECKLIST ✅

Before going live:
- [ ] Run all migrations: `php artisan migrate`
- [ ] Set `spy=1` untuk karyawan yang ingin di-monitor
- [ ] Test with real devices (mobile)
- [ ] Test geolocation on 4G/WiFi/offline
- [ ] Monitor database size
- [ ] Setup scheduled cleanup command
- [ ] Setup error logging/monitoring
- [ ] Test API throttling (if implemented)
- [ ] Document for end users
- [ ] Train admin users

---

## 18. SUMMARY

| Component | Status | Files |
|-----------|--------|-------|
| Database | ✅ | 2 migrations |
| Models | ✅ | 2 models |
| Controllers | ✅ | 2 controllers |
| Routes | ✅ | 2 route files |
| Views | ✅ | 3 blade files |
| Commands | ✅ | 1 command |
| Documentation | ✅ | 1 comprehensive guide |
| **TOTAL** | **✅ COMPLETE** | **13 files** |

---

**Implementation Date**: 22 April 2026
**Version**: 1.0
**Status**: ✅ READY FOR DEPLOYMENT

---

## Quick Start Guide

```bash
# 1. Pull latest code
git pull origin main

# 2. Run migrations
php artisan migrate

# 3. Enable spy untuk test karyawan
# MySQL: UPDATE karyawan SET spy = 1 WHERE nik = '001234567';

# 4. Clear cache
php artisan cache:clear

# 5. Test
# Mobile: Akses /dashboard (karyawan.blade.php)
# Admin: Akses /monitoring-lokasi (monitoring dashboard)

# 6. Setup maintenance (optional)
# Edit app/Console/Kernel.php:
# $schedule->command('location:cleanup --days=7')->dailyAt('01:00')->onOneServer();

# Done! 🎉
```

---

## Support

Untuk questions atau issues:
1. Lihat dokumentasi lengkap: `docs/MONITORING_LOKASI_DOCUMENTATION.md`
2. Check browser console untuk errors
3. Check database untuk location records
4. Verify permission: `presensi.index`
5. Verify spy status: `SELECT spy FROM karyawan WHERE nik = '...';`
