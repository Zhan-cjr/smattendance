# 📱 QUICK SUMMARY - Live Location Mobile APK Implementation

## Status Sekarang ✅
- Backend API: **READY** 
- Web Frontend: **READY**
- Mobile: ✅ Permission lokasi ada, ❌ Background tracking BELUM

## Apa yang Perlu Ditambahkan?

### 1️⃣ Install 2 Packages
```bash
cd smattendance-mobile
npm install expo-background-fetch expo-task-manager @react-native-async-storage/async-storage
```

### 2️⃣ Update `app.json` 
Tambahkan permissions:
```json
"android": {
  "permissions": [
    // ... existing ...
    "android.permission.ACCESS_BACKGROUND_LOCATION"  // ← ADD
  ]
},
"ios": {
  "infoPlist": {
    "UIBackgroundModes": ["location"]  // ← ADD
  }
}
```

### 3️⃣ Create `utils/backgroundLocation.ts`
- Template sudah ada di file: `docs/LIVE_LOCATION_MOBILE_IMPLEMENTATION.md` (Tahap 3)
- Copy code dan paste ke: `smattendance-mobile/utils/backgroundLocation.ts`
- File sudah dibuat: **`smattendance-mobile/utils/backgroundLocation.ts`** ✅

### 4️⃣ Update `app/webview.tsx`
- Tambahkan import backgroundLocationService
- Tambahkan 3 fungsi baru: setupBackgroundTracking, handleLogout, checkTrackingStatus
- Update handleMessage untuk catch USER_LOGIN, USER_LOGOUT, SPY_STATUS_CHANGED
- Referensi: `docs/WEBVIEW_CHANGES_EXAMPLE.md`

### 5️⃣ Update Backend (Laravel)
Tambahkan API endpoint di `app/Http/Controllers/Api/LiveLocationController.php`:
```php
public function checkSpyStatus(Request $request)
{
    $user = auth()->user();
    $karyawan = Karyawan::where('nik', $user->nik)->first();
    
    return response()->json([
        'nik' => $user->nik,
        'spy' => $karyawan->spy ?? 0,
    ]);
}
```

Route di `routes/api.php`:
```php
Route::get('/check-spy-status', [LiveLocationController::class, 'checkSpyStatus']);
```

---

## Bagaimana Cara Kerjanya?

```
User Login (spy=1)
    ↓
Foreground location permission ✅
    ↓
Background location permission ✅
    ↓
Background task registered ✅
    ↓
Setiap 30 detik: Kirim lokasi ke API ✅
    ↓
Lokasi tersimpan di table live_location ✅
    ↓
Admin bisa monitor real-time di /monitoring-lokasi ✅
```

---

## Files yang Sudah Dibuat

| File | Deskripsi |
|------|-----------|
| `smattendance-mobile/utils/backgroundLocation.ts` | Service untuk background location tracking |
| `docs/LIVE_LOCATION_MOBILE_IMPLEMENTATION.md` | Dokumentasi lengkap dengan semua kode |
| `docs/WEBVIEW_CHANGES_EXAMPLE.md` | Contoh update webview.tsx |

---

## Testing Checklist

- [ ] Install packages → `npm install`
- [ ] Update `app.json` dengan permissions
- [ ] Create `backgroundLocation.ts` (sudah dibuat ✅)
- [ ] Update `webview.tsx`
- [ ] Add API endpoint di backend
- [ ] Build APK → `eas build --platform android --release`
- [ ] Test login dengan akun spy=1
- [ ] Check: Background task registered
- [ ] Check: Location terus ter-update di database (minimize app)
- [ ] Monitor: `/monitoring-lokasi` di web

---

## Important Notes

1. **Android 11+**: Butuh permission `ACCESS_BACKGROUND_LOCATION` yang terpisah
2. **Battery**: Default interval 30 detik. Bisa diubah di `backgroundLocation.ts` line 7
3. **iOS**: Butuh background mode "location" di Info.plist
4. **API**: Endpoint harus return JSON dengan format standard
5. **Token**: Harus disimpan di AsyncStorage saat login

---

## Konfigurasi

**Interval Tracking** (di `backgroundLocation.ts`):
```typescript
const TRACKING_INTERVAL = 30000; // Ubah di sini
// 10000 = 10 detik (akurat, boros battery)
// 30000 = 30 detik (balance)
// 60000 = 1 menit (hemat battery)
```

**API URL** (di `backgroundLocation.ts`):
```typescript
const API_BASE_URL = 'https://smattendancev2.amnal.site'; // Ubah di sini
```

---

## Resources

- 📘 [Full Documentation](./LIVE_LOCATION_MOBILE_IMPLEMENTATION.md)
- 💻 [WebView Changes Example](./WEBVIEW_CHANGES_EXAMPLE.md)
- 🔧 [Background Location Service](../smattendance-mobile/utils/backgroundLocation.ts)
- 📚 [Expo Background Fetch Docs](https://docs.expo.dev/versions/latest/sdk/background-fetch/)

---

## Support

Jika ada error:
1. Check Android Studio LogCat: filter `expo-task` atau `BACKGROUND_LOCATION`
2. Check permissions di Settings → Apps → Permissions → Location
3. Check battery optimization: disable untuk app
4. See troubleshooting section di dokumentasi lengkap

