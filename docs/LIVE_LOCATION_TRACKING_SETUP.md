# Live Location Tracking Implementation Guide

## Overview
Live Location Tracking memungkinkan karyawan dengan `spy=1` untuk secara otomatis membagikan lokasi GPS mereka ke sistem monitoring real-time, mirip seperti WhatsApp Live Location.

## Cara Kerja

### 1. **Auto-Tracking di Dashboard**
Ketika karyawan login ke dashboard:
- Sistem mendeteksi jika mereka punya `spy=1`
- Otomatis meminta permission GPS
- Mulai tracking lokasi setiap 10 detik
- Mengirim koordinat ke server untuk disimpan

### 2. **Flow Data**
```
Karyawan Login
    ↓
Check spy=1 di database
    ↓
Auto-request GPS Permission
    ↓
Continuous Location Tracking (setiap 10 detik)
    ↓
Send ke /monitoring-lokasi/saveLocation (POST)
    ↓
Server save ke table live_location
    ↓
Admin monitor via /monitoring-lokasi
```

## Integrasi ke Dashboard

### Step 1: Include Component di Dashboard View
Edit file dashboard karyawan Anda (misalnya `resources/views/dashboard.blade.php`):

```blade
{{-- Add this at the top of your dashboard --}}
<x-live-location-tracker />

{{-- Add tracking status badge (optional) --}}
<x-location-tracking-badge />

{{-- Rest of your dashboard content --}}
<div class="container-xxl">
    ...
</div>
```

### Step 2: Verify Routes
Routes sudah ditambahkan secara otomatis di `routes/web.php`:
```php
Route::post('/monitoring-lokasi/saveLocation', 'saveLocation')->name('monitoring-lokasi.saveLocation');
```

### Step 3: Check User Model Relationship
Pastikan User model memiliki relationship ke Karyawan:

```php
// app/Models/User.php
public function karyawan()
{
    return $this->belongsTo(Karyawan::class, 'nik', 'nik');
}
```

## Konfigurasi

### Tracking Interval
Edit di `resources/views/components/live-location-tracker.blade.php`:

```javascript
trackingInterval: 10000, // ubah ke angka berbeda (milliseconds)
// Contoh:
// 5000 = setiap 5 detik
// 30000 = setiap 30 detik
// 60000 = setiap 1 menit
```

### Minimum Accuracy Threshold
```javascript
minAccuracy: 100, // ubah threshold akurasi (meters)
// Default: hanya kirim jika akurasi lebih baik dari 100m
// Semakin kecil = lebih ketat
```

### Distance Threshold
```javascript
// Line: if (distance < 10)
// Ubah 10 ke nilai lain untuk minimum jarak pergerakan
// Default: hanya kirim jika bergerak minimal 10m
```

## Browser Requirements

### GPS Permission Required
- Chrome, Firefox, Safari, Edge mendukung Geolocation API
- HTTPS required (tidak berfungsi di HTTP)
- User harus grant permission GPS

### Checking GPS Support
```javascript
// Di browser console
navigator.geolocation ? "✓ Supported" : "✗ Not supported"
```

## Mobile App Integration

### Untuk React Native (example)
```javascript
import * as Location from 'expo-location';

const startTracking = async () => {
    const { status } = await Location.requestPermissionsAsync();
    if (status !== 'granted') return;
    
    setInterval(async () => {
        const location = await Location.getCurrentPositionAsync({
            accuracy: Location.Accuracy.High
        });
        
        // Send ke API
        fetch('/monitoring-lokasi/saveLocation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                latitude: location.coords.latitude,
                longitude: location.coords.longitude,
                accuracy: location.coords.accuracy
            })
        });
    }, 10000); // 10 detik
};
```

### Untuk Flutter (example)
```dart
import 'package:geolocator/geolocator.dart';

void startTracking() {
    Geolocator.getPositionStream(
        locationSettings: LocationSettings(
            accuracy: LocationAccuracy.high,
            distanceFilter: 10, // minimum 10 meters
        ),
    ).listen((Position position) {
        sendLocationToServer(position.latitude, position.longitude, position.accuracy);
    });
}

Future<void> sendLocationToServer(double lat, double lng, double accuracy) async {
    final response = await http.post(
        Uri.parse('/monitoring-lokasi/saveLocation'),
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: jsonEncode({
            'latitude': lat,
            'longitude': lng,
            'accuracy': accuracy,
        }),
    );
    
    if (response.statusCode != 200) {
        print('Failed to save location');
    }
}
```

## Monitoring & Admin Dashboard

Admin dapat melihat:
1. **Real-time locations** - `/monitoring-lokasi` (halaman sudah ada)
2. **Location history** - `/monitoring-lokasi/getLocationHistory`
3. **Live path** - Garis tracking pergerakan karyawan

## Database Storage

Data disimpan di table `live_location`:
```sql
SELECT * FROM live_location 
WHERE nik = '11.11.111' 
ORDER BY tracked_at DESC;
```

Fields:
- `nik` - Employee ID
- `latitude` - Koordinat lintang
- `longitude` - Koordinat bujur
- `accuracy` - Akurasi GPS (meters)
- `lokasi` - Nama lokasi (reverse geocoding)
- `tracked_at` - Timestamp tracking

## Troubleshooting

### 1. "Location Permission Denied"
**Solusi:**
- Chrome: Klik lock icon di address bar → Permission → Allow Location
- Firefox: Klik permission notification
- Safari: Settings → Privacy → Location Services → Allow
- Atau buka `chrome://settings/content/location` untuk manage permissions

### 2. Lokasi tidak ter-update
**Cek:**
```javascript
// Di console
navigator.geolocation.getCurrentPosition(
    pos => console.log(pos),
    err => console.error(err)
)
```

### 3. CORS Error saat Reverse Geocoding
**Solusi:**
- Nominatim API memiliki rate limit
- Atau set `lokasi: null` untuk disable reverse geocoding

### 4. Accuracy terlalu buruk
**Solusi:**
- Pastikan user outdoor dengan akses langsung ke langit
- Indoor accuracy biasanya 30-100m
- Decrease `minAccuracy` threshold jika diperlukan

## API Endpoints

### Save Location (POST)
```
POST /monitoring-lokasi/saveLocation

Headers:
  Content-Type: application/json
  X-CSRF-TOKEN: {csrf_token}

Body:
  {
    "latitude": -6.8044,
    "longitude": 107.1439,
    "accuracy": 25.5,
    "lokasi": "Cibeber Cianjur" (optional)
  }

Response (Success):
  {
    "status": true,
    "message": "Lokasi berhasil disimpan",
    "data": {
      "id": 123,
      "nik": "11.11.111",
      "latitude": -6.8044,
      "longitude": 107.1439,
      "lokasi": "Cibeber Cianjur",
      "accuracy": 25.5,
      "tracked_at": "2026-04-22 14:30:45"
    }
  }

Response (Error):
  {
    "status": false,
    "message": "Error message here"
  }
```

### Get Latest Location (GET)
```
GET /monitoring-lokasi/getData?nik=11.11.111

Headers:
  X-CSRF-TOKEN: {csrf_token}

Response:
  {
    "status": true,
    "latestLocation": {
      "latitude": -6.8044,
      "longitude": 107.1439,
      ...
    }
  }
```

### Get Location History (GET)
```
GET /monitoring-lokasi/getLocationHistory?nik=11.11.111&minutes=60

Parameters:
  - nik: Employee NIK
  - minutes: Rentang waktu (default: 60)

Response:
  {
    "status": true,
    "count": 50,
    "data": [...]
  }
```

## Security & Privacy

### Considerations
1. **Data Privacy** - Lokasi adalah data sensitive
   - Hanya admin yang dapat melihat
   - Log akses monitoring
   - GDPR compliant

2. **GPS Accuracy**
   - Tidak selalu akurat di dalam ruangan
   - Minimum 5-10 meter di outdoor
   - Device-dependent

3. **Battery Usage**
   - Tracking berkelanjutan menggunakan baterai
   - Consider scheduling non-tracking hours
   - Background tracking perlu careful management

### Rate Limiting (Recommended)
Tambahkan di middleware untuk mencegah spam:
```php
// app/Http/Middleware/RateLimitTracking.php
public function handle($request, Closure $next)
{
    return RateLimiter::attempt(
        'location-tracking:' . auth()->id(),
        60, // 60 requests
        fn() => $next($request),
        1 // per 1 second
    );
}
```

## Performance Tips

1. **Adjust Tracking Interval**
   - Start with 30 seconds if concerned about server load
   - Can reduce to 10 seconds for critical tracking

2. **Cleanup Old Data**
   ```bash
   php artisan location:cleanup --days=30
   ```
   (Command untuk di-cron regularly)

3. **Database Indexing**
   Already indexed on `nik` and `tracked_at` for performance

4. **Archive Strategy**
   - Keep last 30 days in main table
   - Archive older data to separate table

## Testing

### Manual Test di Browser Console
```javascript
// Test 1: Check component loaded
window.LocationTracker

// Test 2: Manual location send
LocationTracker.trackLocationNow()

// Test 3: Stop tracking
LocationTracker.stop()

// Test 4: Check last sent location
console.log({
    lastLat: LocationTracker.lastLatitude,
    lastLng: LocationTracker.lastLongitude
})
```

### Test API Endpoint
```bash
# Using cURL
curl -X POST http://localhost/monitoring-lokasi/saveLocation \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -H "Cookie: LARAVEL_SESSION=your_session" \
  -d '{
    "latitude": -6.8044,
    "longitude": 107.1439,
    "accuracy": 25.5
  }'
```

## Next Steps

1. ✅ Include component di dashboard
2. ✅ Test GPS permission workflow
3. ✅ Monitor location data di admin panel
4. ✅ Setup mobile app integration (jika ada)
5. ✅ Configure interval & accuracy sesuai kebutuhan
6. ✅ Setup cleanup jobs untuk old data
7. ✅ Add rate limiting untuk production
