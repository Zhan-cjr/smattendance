# Monitoring Lokasi - Panduan Testing & Troubleshooting

## Masalah: Lokasi Tidak Terdeteksi (HTTP 404)

### Root Cause
- Data lokasi belum ada di table `live_location`
- Karyawan belum mengirim tracking data via mobile app
- Permission issue pada user yang login

### Solusi 1: Generate Test Data (Development)
```bash
# Generate test data untuk semua karyawan dengan spy=1
php artisan location:generate-test

# Generate test data untuk karyawan tertentu
php artisan location:generate-test 11.11.111
```

### Solusi 2: Dari Mobile App (Production)
1. Pastikan mobile app memiliki permission GPS
2. Pastikan karyawan membuka mobile app dengan GPS enabled
3. Data akan otomatis ter-track ke database setiap interval tertentu
4. Monitor melalui halaman Admin → Monitoring Lokasi

### Verifikasi Setup

#### 1. Check Karyawan dengan Spy Enabled
```bash
php artisan tinker
> App\Models\Karyawan::where('spy', 1)->count()
```

#### 2. Check Data Lokasi di Database
```bash
# Terminal/Command Prompt
mysql -u root -p smatt3

# Query
SELECT nik, COUNT(*) as total_records, MAX(tracked_at) as latest 
FROM live_location 
GROUP BY nik 
ORDER BY latest DESC;

# Untuk karyawan tertentu
SELECT * FROM live_location 
WHERE nik = '11.11.111' 
ORDER BY tracked_at DESC LIMIT 10;
```

#### 3. Check Permission di Database
```bash
mysql -u root -p smatt3

# Cek apakah user punya permission presensi.index
SELECT DISTINCT r.name as role, p.name as permission 
FROM roles r 
JOIN role_has_permissions rhp ON r.id = rhp.role_id
JOIN permissions p ON rhp.permission_id = p.id 
WHERE p.name LIKE '%presensi%';

# Cek user role
SELECT u.name, r.name as role 
FROM users u 
JOIN model_has_roles mhr ON u.id = mhr.model_id 
JOIN roles r ON mhr.role_id = r.id 
WHERE u.email = 'admin@example.com';
```

### API Endpoints Testing

**Route Protected:** Memerlukan authentication + permission `presensi.index`

| Endpoint | Method | Parameter | Response |
|----------|--------|-----------|----------|
| `/monitoring-lokasi` | GET | `?nik=11.11.111` | Show page |
| `/monitoring-lokasi/getData` | GET | `?nik=11.11.111` | JSON latest location + history |
| `/monitoring-lokasi/getLocationHistory` | GET | `?nik=11.11.111&minutes=60` | JSON location history |
| `/monitoring-lokasi/getSpyEmployees` | GET | - | JSON list employees with spy=1 |

### Test dengan cURL
```bash
# Pastikan sudah login di browser terlebih dahulu, ambil cookie
# Then test API
curl -b "LARAVEL_SESSION=xxx" \
  "http://localhost/monitoring-lokasi/getData?nik=11.11.111" \
  -H "X-CSRF-TOKEN: token_value"
```

### Browser Console Debug
Open Developer Tools (F12) → Console dan jalankan:
```javascript
// Check selectedNik variable
console.log('selectedNik:', selectedNik);

// Manually test getData endpoint
fetch('/monitoring-lokasi/getData?nik=11.11.111', {
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
})
.then(r => r.json())
.then(d => console.log('API Response:', d))
.catch(e => console.error('Error:', e));
```

### Common Issues & Solutions

| Error | Cause | Solution |
|-------|-------|----------|
| 404 Not Found | Data lokasi tidak ada | Jalankan `php artisan location:generate-test 11.11.111` |
| 403 Unauthorized | Permission denied | Grant role dengan permission `presensi.index` |
| Map tidak muncul | Leaflet CDN error | Check internet connection atau proxy |
| Data tidak update | Auto-refresh tidak berjalan | Check browser console for JS errors |
| Lokasi lama | Data belum masuk | Pastikan mobile app mengirim data dengan benar |

### File-file Penting
- **Controller:** `app/Http/Controllers/MonitoringLokasiController.php`
- **View:** `resources/views/monitoring-lokasi/index.blade.php`
- **Model:** `app/Models/LiveLocation.php`
- **Migration:** `database/migrations/2026_04_22_000001_create_live_location_table.php`
- **Routes:** `routes/web.php` (line 744-748)
- **Command:** `app/Console/Commands/GenerateTestLocationData.php`

### Deployment Notes
- Pastikan table `live_location` sudah di-migrate di production
- Setup cron job untuk cleanup old location data (optional):
  ```php
  // In App\Console\Kernel
  $schedule->command('location:cleanup')->daily();
  ```
- Monitor disk space jika tracking rate tinggi (banyak pengguna dengan spy=1)
