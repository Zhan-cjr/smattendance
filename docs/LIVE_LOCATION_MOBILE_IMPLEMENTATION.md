# 📱 LIVE LOCATION TRACKING - Mobile (APK) Implementation Guide

## Status Saat Ini

### ✅ Yang Sudah Berfungsi:
1. **Backend API** - Endpoint `/api/live-location/store` siap menerima lokasi
2. **Web Frontend** - Component `<x-live-location-tracker />` tracking lokasi di browser
3. **Mobile Permissions** - Camera & Location permission sudah di-request
4. **WebView Geolocation** - JavaScript di-inject untuk enable geolocation di WebView
5. **Fake GPS Monitoring** - Continuous monitoring untuk deteksi fake GPS setiap 10-15 detik

### ❌ Yang Masih Kurang:
**BACKGROUND TRACKING** - Live location hanya bekerja saat app aktif (foreground)

---

## 🎯 Masalah yang Dihadapi

Saat ini, live location tracking hanya berfungsi:
- ✅ Saat aplikasi aktif di foreground
- ✅ Saat user browsing di dashboard dengan WebView aktif
- ❌ Saat aplikasi di-minimize ke background
- ❌ Saat aplikasi ditutup sama sekali

**Solusinya**: Perlu background task untuk tracking lokasi secara kontinyu, seperti WhatsApp Live Location.

---

## 📋 Implementasi yang Dibutuhkan

### Tahap 1: Tambah Dependencies

**File: `smattendance-mobile/package.json`**

```json
{
  "dependencies": {
    // ... existing dependencies ...
    "expo-background-fetch": "^15.0.1",
    "expo-task-manager": "~14.0.0"
  }
}
```

**Install:**
```bash
cd smattendance-mobile
npm install expo-background-fetch expo-task-manager
```

---

### Tahap 2: Update app.json

**File: `smattendance-mobile/app.json`**

Tambahkan di dalam section `"android"`:
```json
{
  "expo": {
    "android": {
      // ... existing config ...
      "permissions": [
        "android.permission.CAMERA",
        "android.permission.ACCESS_FINE_LOCATION",
        "android.permission.ACCESS_COARSE_LOCATION",
        "android.permission.ACCESS_BACKGROUND_LOCATION",  // ← ADD THIS
        "android.permission.RECORD_AUDIO",
        "android.permission.QUERY_ALL_PACKAGES"
      ]
    },
    "ios": {
      // ... existing config ...
      "infoPlist": {
        // ... existing ...
        "NSLocationWhenInUseUsageDescription": "Aplikasi ini memerlukan akses lokasi untuk mencatat lokasi presensi",
        "NSLocationAlwaysAndWhenInUseUsageDescription": "Aplikasi ini memerlukan akses lokasi untuk mencatat lokasi presensi dalam background",  // ← ADD THIS
        "UIBackgroundModes": ["location"]  // ← ADD THIS
      }
    }
  }
}
```

---

### Tahap 3: Buat Background Location Service

**File: `smattendance-mobile/utils/backgroundLocation.ts`** (NEW FILE)

```typescript
import * as Location from 'expo-location';
import * as TaskManager from 'expo-task-manager';
import * as BackgroundFetch from 'expo-background-fetch';

const TASK_NAME = 'BACKGROUND_LOCATION_TRACKING';
const TRACKING_INTERVAL = 30000; // 30 seconds
const API_BASE_URL = 'https://smattendancev2.amnal.site'; // Change to your domain

// Task untuk background location tracking
TaskManager.defineTask(TASK_NAME, async () => {
  try {
    const location = await Location.getCurrentPositionAsync({
      accuracy: Location.Accuracy.High,
    });

    // Get user nik dari AsyncStorage (harus disimpan saat login)
    const AsyncStorage = require('@react-native-async-storage/async-storage').default;
    const userNik = await AsyncStorage.getItem('user_nik');
    const authToken = await AsyncStorage.getItem('auth_token');

    if (!userNik || !authToken) {
      return BackgroundFetch.BackgroundFetchResult.NoData;
    }

    // Send to API
    const response = await fetch(`${API_BASE_URL}/api/live-location/store`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${authToken}`,
      },
      body: JSON.stringify({
        nik: userNik,
        latitude: location.coords.latitude,
        longitude: location.coords.longitude,
        accuracy: location.coords.accuracy,
        tracked_at: new Date().toISOString(),
      }),
    });

    if (!response.ok) {
      console.log('Background location tracking error:', response.status);
      return BackgroundFetch.BackgroundFetchResult.Failed;
    }

    return BackgroundFetch.BackgroundFetchResult.NewData;
  } catch (error) {
    console.error('Background location tracking error:', error);
    return BackgroundFetch.BackgroundFetchResult.Failed;
  }
});

export const backgroundLocationService = {
  // Request background location permission (Android 11+)
  async requestBackgroundPermission(): Promise<boolean> {
    try {
      const { status } = await Location.requestBackgroundPermissionsAsync();
      return status === 'granted';
    } catch (error) {
      console.error('Error requesting background permission:', error);
      return false;
    }
  },

  // Check if task is already registered
  async isTaskRegistered(): Promise<boolean> {
    try {
      const tasks = await TaskManager.getRegisteredTasksAsync();
      return tasks.some(task => task.taskName === TASK_NAME);
    } catch (error) {
      console.error('Error checking task registration:', error);
      return false;
    }
  },

  // Register background fetch task
  async registerTask(): Promise<void> {
    try {
      await BackgroundFetch.registerTaskAsync(TASK_NAME, {
        minimumInterval: TRACKING_INTERVAL / 1000, // Convert to seconds
        stopOnTerminate: false,
        startOnBoot: true,
      });
      console.log('Background location tracking task registered');
    } catch (error) {
      console.error('Error registering background task:', error);
    }
  },

  // Unregister background fetch task
  async unregisterTask(): Promise<void> {
    try {
      await BackgroundFetch.unregisterTaskAsync(TASK_NAME);
      console.log('Background location tracking task unregistered');
    } catch (error) {
      console.error('Error unregistering background task:', error);
    }
  },

  // Setup background tracking (call from app login)
  async setupTracking(userNik: string, authToken: string): Promise<void> {
    try {
      // Save user info to AsyncStorage
      const AsyncStorage = require('@react-native-async-storage/async-storage').default;
      await AsyncStorage.setItem('user_nik', userNik);
      await AsyncStorage.setItem('auth_token', authToken);

      // Request foreground permission
      const fgPermission = await Location.requestForegroundPermissionsAsync();
      if (fgPermission.status !== 'granted') {
        throw new Error('Foreground location permission denied');
      }

      // Request background permission (Android 11+)
      const bgPermission = await this.requestBackgroundPermission();
      if (!bgPermission) {
        console.warn('Background location permission denied - foreground tracking will continue');
      }

      // Register task if not already registered
      const isRegistered = await this.isTaskRegistered();
      if (!isRegistered) {
        await this.registerTask();
      }

      console.log('Background location tracking setup completed');
    } catch (error) {
      console.error('Error setting up background location tracking:', error);
      throw error;
    }
  },

  // Cleanup when user logout
  async stopTracking(): Promise<void> {
    try {
      const AsyncStorage = require('@react-native-async-storage/async-storage').default;
      await AsyncStorage.removeItem('user_nik');
      await AsyncStorage.removeItem('auth_token');
      
      const isRegistered = await this.isTaskRegistered();
      if (isRegistered) {
        await this.unregisterTask();
      }

      console.log('Background location tracking stopped');
    } catch (error) {
      console.error('Error stopping background location tracking:', error);
    }
  },
};
```

**PENTING:** Tambahkan `@react-native-async-storage/async-storage` ke package.json:
```json
{
  "dependencies": {
    "@react-native-async-storage/async-storage": "^1.23.1"
  }
}
```

---

### Tahap 4: Update webview.tsx

**File: `smattendance-mobile/app/webview.tsx`**

Tambahkan import di bagian atas:
```typescript
import { backgroundLocationService } from '@/utils/backgroundLocation';
import AsyncStorage from '@react-native-async-storage/async-storage';
```

Tambahkan di dalam komponen `WebViewScreen`, setelah `handleMessage` function:
```typescript
  // Setup background location tracking saat user login
  const setupBackgroundTracking = async () => {
    try {
      // Get user info dari API atau AsyncStorage
      // Contoh: Kirim request ke server untuk check spy status
      const response = await fetch(`${webUrl}/api/check-spy-status`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${await AsyncStorage.getItem('auth_token')}`,
        },
      });

      if (response.ok) {
        const data = await response.json();
        
        // Jika user punya spy=1, setup background tracking
        if (data.spy === 1) {
          const userNik = data.nik;
          const authToken = await AsyncStorage.getItem('auth_token');
          
          if (userNik && authToken) {
            await backgroundLocationService.setupTracking(userNik, authToken);
            console.log('Background location tracking activated for spy user');
          }
        }
      }
    } catch (error) {
      console.error('Error setting up background tracking:', error);
      // Continue without background tracking - foreground tracking will still work
    }
  };

  // Handle logout - stop background tracking
  const handleLogout = async () => {
    try {
      await backgroundLocationService.stopTracking();
      console.log('Background location tracking deactivated');
    } catch (error) {
      console.error('Error stopping background tracking:', error);
    }
  };
```

Tambahkan useEffect untuk setup tracking:
```typescript
  // Setup background tracking when component mounts
  React.useEffect(() => {
    setupBackgroundTracking();

    // Listen untuk logout event (jika ada)
    // Ini bisa dari WebView message atau global event
  }, []);
```

Tambahkan handler untuk capture logout dari WebView:
```typescript
  const handleMessage = (event: any) => {
    try {
      const data = JSON.parse(event.nativeEvent.data);
      
      // ... existing message handlers ...

      // Handle logout
      if (data.type === 'USER_LOGOUT') {
        handleLogout();
      }

      // Handle spy status change
      if (data.type === 'SPY_STATUS_CHANGED') {
        if (data.spy === 1) {
          setupBackgroundTracking();
        } else {
          handleLogout();
        }
      }
    } catch (error) {
      console.log('Error parsing WebView message:', error);
    }
  };
```

---

### Tahap 5: Update Web Application (Laravel)

**File: `app/Http/Controllers/Api/LiveLocationController.php`**

Pastikan sudah ada method untuk check spy status:
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

**File: `routes/api.php`**

Tambahkan route:
```php
Route::get('/check-spy-status', [LiveLocationController::class, 'checkSpyStatus']);
```

---

## 🚀 Installation Steps

### Step 1: Install Dependencies
```bash
cd d:\APLIKASI PROJECT\smatt3\smattendance-mobile
npm install expo-background-fetch expo-task-manager @react-native-async-storage/async-storage
```

### Step 2: Update app.json
Tambahkan permissions sesuai Tahap 2 di atas.

### Step 3: Create backgroundLocation.ts
Buat file baru `utils/backgroundLocation.ts` dengan kode dari Tahap 3.

### Step 4: Update webview.tsx
Tambahkan import dan functions sesuai Tahap 4.

### Step 5: Update Backend
Tambahkan API endpoint untuk check spy status.

### Step 6: Build APK
```bash
eas build --platform android --release
```

Atau untuk development:
```bash
expo run:android
```

---

## 🧪 Testing

### Test 1: Verify Background Permission
```typescript
// Add this to check permissions
import { backgroundLocationService } from '@/utils/backgroundLocation';

const checkPermissions = async () => {
  const isRegistered = await backgroundLocationService.isTaskRegistered();
  console.log('Task registered:', isRegistered);
};
```

### Test 2: Monitor Background Task Execution
Buka Android Studio LogCat dan filter: `expo-task` atau `BACKGROUND_LOCATION`

### Test 3: Real-world Test
1. Login dengan akun spy=1
2. Buka dashboard aplikasi
3. Minimize aplikasi ke background
4. Tunggu 30-60 detik
5. Cek database `live_location` untuk melihat apakah lokasi terus ter-update

---

## ⚙️ Konfigurasi

### Interval Tracking
Di `utils/backgroundLocation.ts`, ubah:
```typescript
const TRACKING_INTERVAL = 30000; // milliseconds
// 30000 = 30 detik
// 60000 = 1 menit
// 10000 = 10 detik (lebih akurat tapi boros battery)
```

### API Endpoint
Di `utils/backgroundLocation.ts`, ubah:
```typescript
const API_BASE_URL = 'https://smattendancev2.amnal.site'; // ← Ganti dengan URL Anda
```

---

## 🔐 Security Considerations

1. **Store Auth Token Securely**
   - Gunakan Keychain/Keystore untuk menyimpan token
   - Jangan simpan di plain AsyncStorage untuk production

2. **Validate Background Requests**
   - Server harus verify token sebelum save lokasi
   - Check spy=1 status sebelum accept

3. **Rate Limiting**
   - Implement rate limiting di API untuk prevent abuse
   - Contoh: Max 1 location per 10 seconds per user

---

## 🐛 Troubleshooting

### Background Task Tidak Berjalan

**Masalah:** Location tidak ter-update saat app di background

**Solusi:**
1. Check Android settings: Settings → Apps → Permissions → Location → Always allow
2. Check battery optimization: disable battery optimization untuk app
3. Check task registration: gunakan `TaskManager.getRegisteredTasksAsync()` di console
4. Cek logcat untuk error messages

### High Battery Drain

**Masalah:** Battery turun cepat

**Solusi:**
1. Increase tracking interval: ubah 30000 menjadi 60000 (1 menit)
2. Check accuracy setting: Location.Accuracy.Balanced lebih hemat dari High
3. Disable background tracking saat battery low

### Permission Denied

**Masalah:** "Permission denied" error

**Solusi:**
1. Request foreground permission dulu, baru background
2. iOS: Periksa NSLocationAlwaysAndWhenInUseUsageDescription di Info.plist
3. Android 11+: Need separate request untuk background location

---

## 📊 API Integration

### Endpoint: POST `/api/live-location/store`

**Request Body:**
```json
{
  "nik": "001.001.0001",
  "latitude": -6.3157,
  "longitude": 106.8227,
  "accuracy": 15.5,
  "tracked_at": "2026-04-23T10:30:45Z"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Location saved successfully",
  "data": {
    "id": 1,
    "nik": "001.001.0001",
    "latitude": -6.3157,
    "longitude": 106.8227,
    "accuracy": 15.5,
    "lokasi": "Jakarta, Indonesia"
  }
}
```

---

## 📚 References

- [Expo Background Fetch Documentation](https://docs.expo.dev/versions/latest/sdk/background-fetch/)
- [Expo Task Manager Documentation](https://docs.expo.dev/versions/latest/sdk/task-manager/)
- [Expo Location Documentation](https://docs.expo.dev/versions/latest/sdk/location/)
- [React Native AsyncStorage](https://react-native-async-storage.github.io/async-storage/)

---

## 📝 Implementation Checklist

- [ ] Install dependencies
- [ ] Update app.json dengan permissions
- [ ] Create backgroundLocation.ts
- [ ] Update webview.tsx
- [ ] Add API endpoint di backend
- [ ] Test foreground location
- [ ] Test background location
- [ ] Test permission flows
- [ ] Monitor battery usage
- [ ] Build and test APK

