# 🔄 Alur Fitur Update Otomatis

## 📋 Daftar Isi
1. [Alur Admin (Membuat & Release Update)](#alur-admin)
2. [Alur User (Melakukan Update)](#alur-user)
3. [Diagram Alur](#diagram-alur)
4. [Contoh Skenario](#contoh-skenario)

---

## 👨‍💼 Alur Admin (Membuat & Release Update)

### Step 1: Development
```
1. Developer membuat perubahan di code
   - Update controller, model, view, dll
   - Buat migration baru (jika perlu)
   - Buat seeder baru (jika perlu)
   - Test perubahan di development
```

### Step 2: Buat Update Package
```
2. Siapkan file update dalam format ZIP
   Struktur:
   update_1.0.1.zip
   ├── app/
   │   └── Http/Controllers/NewController.php
   ├── database/migrations/
   │   └── 2024_01_01_new_migration.php
   ├── resources/views/
   │   └── new-view.blade.php
   └── routes/
       └── web.php (jika ada perubahan)
```

### Step 3: Upload File
```
3. Upload file ZIP ke server
   - Upload ke: public/updates/update_1.0.1.zip
   - Atau upload ke cloud storage (Google Drive, Dropbox, dll)
   - Dapatkan URL: https://domain.com/updates/update_1.0.1.zip
```

### Step 4: Tambahkan Data ke Database
```
4. Insert data update ke tabel `updates`
   
   Via SQL:
   INSERT INTO updates (version, title, description, changelog, 
                        file_url, file_size, is_active, released_at)
   VALUES ('1.0.1', 'Update Minor', 'Deskripsi', 'Changelog', 
           'https://domain.com/updates/update_1.0.1.zip', 
           '5242880', 1, NOW());
   
   Atau via Tinker:
   php artisan tinker
   \App\Models\Update::create([...]);
```

### Step 5: Aktifkan Update
```
5. Set is_active = 1
   - Update akan muncul di halaman /update
   - User bisa melihat update tersedia
```

---

## 👤 Alur User (Melakukan Update)

### Skenario A: Via Web Interface

#### Step 1: Akses Halaman Update
```
User login → Buka halaman /update
- Halaman menampilkan:
  - Versi saat ini: 1.0.0
  - Status aplikasi
  - Riwayat update
```

#### Step 2: Cek Update
```
User klik tombol "Cek Update"
→ Sistem mengecek:
  1. Versi saat ini dari file VERSION
  2. Query database untuk update aktif
  3. Bandingkan versi (version_compare)
  4. Return hasil: ada update atau tidak
```

#### Step 3: Lihat Update Tersedia
```
Jika ada update:
- Tampilkan card "Update Tersedia"
- Menampilkan:
  - Versi terbaru: 1.0.1
  - Judul update
  - Deskripsi
  - Changelog
  - Ukuran file
  - Tombol "Update Sekarang" atau "Download Saja"
```

#### Step 4: User Klik "Update Sekarang"
```
User klik "Update Sekarang"
→ Konfirmasi dialog muncul
→ User konfirmasi
→ Proses update dimulai
```

#### Step 5: Proses Update (Otomatis)
```
Sistem melakukan:

1. CREATE UpdateLog
   - Status: pending
   - Version: 1.0.1
   - User ID: current user

2. DOWNLOAD File
   - Status: downloading
   - Download dari file_url
   - Simpan ke: storage/app/updates/update_1.0.1.zip
   - Validasi checksum (jika ada)

3. BACKUP Database
   - Backup ke: storage/app/backups/backup_YYYY-MM-DD_HHMMSS_1.0.1.sql
   - Menggunakan mysqldump

4. EXTRACT File
   - Extract ZIP ke: storage/app/updates/extract_1.0.1/
   - Status: installing

5. COPY Files
   - Copy app/ → base_path('app')
   - Copy database/ → base_path('database')
   - Copy resources/ → base_path('resources')
   - Copy routes/ → base_path('routes')
   - Copy config/ → base_path('config')
   - Copy public/ → base_path('public')

6. RUN Migrations
   - php artisan migrate --force
   - Atau run migration spesifik dari array migrations

7. RUN Seeders (jika ada)
   - php artisan db:seed --class=SeederName --force

8. UPDATE Version
   - Update file VERSION: 1.0.1

9. CLEAR Cache
   - php artisan optimize:clear
   - php artisan config:cache
   - php artisan route:cache
   - php artisan view:cache

10. CLEANUP
    - Hapus file ZIP
    - Hapus folder extract

11. UPDATE Log
    - Status: success
    - Previous version: 1.0.0
    - Completed at: now()
```

#### Step 6: Selesai
```
- Tampilkan notifikasi "Update berhasil"
- Reload halaman
- Versi baru: 1.0.1
```

---

### Skenario B: Via API

#### Step 1: Check Update
```http
GET /api/update/check

Response:
{
    "success": true,
    "data": {
        "has_update": true,
        "current_version": "1.0.0",
        "latest_version": "1.0.1",
        "update": {...}
    }
}
```

#### Step 2: Update Now
```http
POST /api/update/1.0.1/update-now
Authorization: Bearer {token}

Response:
{
    "success": true,
    "message": "Update berhasil diinstall",
    "data": {
        "update_log": {...},
        "current_version": "1.0.1"
    }
}
```

#### Step 3: Check Status (Optional)
```http
GET /api/update/status/{logId}

Response:
{
    "success": true,
    "data": {
        "status": "installing",
        "progress": 75
    }
}
```

---

## 📊 Diagram Alur

### Alur Admin
```
[Development] 
    ↓
[Buat ZIP File]
    ↓
[Upload ke Server]
    ↓
[Insert ke Database]
    ↓
[Set is_active = 1]
    ↓
[Update Tersedia untuk User]
```

### Alur User (Web)
```
[User Login]
    ↓
[Akses /update]
    ↓
[Klik "Cek Update"]
    ↓
{Ada Update?}
    ├─ NO → [Tampilkan "Sudah Versi Terbaru"]
    └─ YES → [Tampilkan Info Update]
              ↓
         [Klik "Update Sekarang"]
              ↓
         [Konfirmasi]
              ↓
         [Proses Update]
              ├─ [Download File]
              ├─ [Backup DB]
              ├─ [Extract & Copy]
              ├─ [Run Migration]
              ├─ [Run Seeder]
              ├─ [Update Version]
              └─ [Clear Cache]
              ↓
         [Selesai - Reload]
```

### Alur User (API)
```
[Client App]
    ↓
[GET /api/update/check]
    ↓
{has_update?}
    ├─ NO → [Tidak ada update]
    └─ YES → [POST /api/update/{version}/update-now]
              ↓
         [Proses Update]
              ↓
         [GET /api/update/status/{logId}] (polling)
              ↓
         [Status: success]
```

---

## 🎯 Contoh Skenario Lengkap

### Skenario: Update dari 1.0.0 ke 1.0.1

#### **Admin Side:**

1. **Developer membuat perubahan:**
   ```php
   // app/Http/Controllers/NewController.php
   // database/migrations/2024_01_01_add_column.php
   ```

2. **Buat ZIP:**
   ```bash
   zip -r update_1.0.1.zip app/Http/Controllers/NewController.php database/migrations/2024_01_01_add_column.php
   ```

3. **Upload:**
   ```bash
   # Upload ke server
   scp update_1.0.1.zip user@server:/var/www/html/public/updates/
   ```

4. **Insert Database:**
   ```sql
   INSERT INTO updates (version, title, file_url, is_active, released_at)
   VALUES ('1.0.1', 'Update Minor', 'https://domain.com/updates/update_1.0.1.zip', 1, NOW());
   ```

#### **User Side:**

1. **User buka halaman `/update`**
   - Lihat versi saat ini: 1.0.0

2. **Klik "Cek Update"**
   - Sistem query: `SELECT * FROM updates WHERE is_active = 1 ORDER BY version DESC`
   - Ditemukan: versi 1.0.1
   - `version_compare('1.0.1', '1.0.0', '>')` = true
   - Return: `has_update = true`

3. **Tampilkan Update Info:**
   ```
   Update Tersedia!
   Versi Terbaru: 1.0.1
   Judul: Update Minor
   Changelog: - Fix bug presensi
   ```

4. **Klik "Update Sekarang"**
   - Konfirmasi dialog
   - User klik "Ya"

5. **Proses Update:**
   ```
   [1] Create UpdateLog (status: pending)
   [2] Download file (status: downloading)
       → storage/app/updates/update_1.0.1.zip
   [3] Backup database (status: installing)
       → storage/app/backups/backup_2024-01-15_103000_1.0.1.sql
   [4] Extract ZIP
       → storage/app/updates/extract_1.0.1/
   [5] Copy files
       → app/Http/Controllers/NewController.php
       → database/migrations/2024_01_01_add_column.php
   [6] Run migration
       → php artisan migrate --force
   [7] Update VERSION file
       → 1.0.1
   [8] Clear cache
       → php artisan optimize:clear
   [9] Cleanup
       → Hapus ZIP dan extract folder
   [10] Update log (status: success)
   ```

6. **Selesai:**
   - Notifikasi: "Update berhasil diinstall"
   - Halaman reload
   - Versi baru: 1.0.1

---

## 🔍 Detail Proses Update

### 1. Download Process
```
Request → UpdateService::downloadUpdate()
    ↓
Create UpdateLog (status: pending)
    ↓
Update status: downloading
    ↓
HTTP GET file_url
    ↓
Save to: storage/app/updates/update_{version}.zip
    ↓
Validate checksum (if exists)
    ↓
Update log: "File berhasil diunduh"
```

### 2. Install Process
```
Request → UpdateService::installUpdate()
    ↓
Update status: installing
    ↓
Backup database
    ↓
Extract ZIP
    ↓
Copy files to application
    ↓
Run migrations
    ↓
Run seeders (if any)
    ↓
Update VERSION file
    ↓
Clear cache
    ↓
Cleanup temporary files
    ↓
Update log: status = success
```

### 3. Error Handling
```
Jika error terjadi:
    ↓
Update log: status = failed
    ↓
Save error message
    ↓
Save error log (stack trace)
    ↓
Attempt rollback (if backup exists)
    ↓
Return error response
```

---

## 📝 Checklist Alur

### Admin Checklist:
- [ ] Code sudah diupdate dan ditest
- [ ] Migration dibuat (jika perlu)
- [ ] ZIP file sudah dibuat dengan struktur benar
- [ ] File sudah di-upload ke server
- [ ] Data sudah di-insert ke database
- [ ] `is_active = 1` untuk mengaktifkan
- [ ] URL file bisa diakses

### User Checklist:
- [ ] Login sebagai Super Admin
- [ ] Akses halaman `/update`
- [ ] Klik "Cek Update"
- [ ] Lihat info update
- [ ] Backup database (otomatis dilakukan sistem)
- [ ] Klik "Update Sekarang"
- [ ] Tunggu proses selesai
- [ ] Verifikasi versi baru

---

## ⚠️ Catatan Penting

1. **Backup Otomatis:**
   - Sistem otomatis backup database sebelum update
   - Backup disimpan di: `storage/app/backups/`

2. **Rollback:**
   - Jika update gagal, sistem akan attempt rollback
   - Manual rollback bisa dilakukan dari backup

3. **Version Management:**
   - Versi disimpan di file `VERSION` di root project
   - Format: Semantic Versioning (1.0.0, 1.0.1, 1.1.0, 2.0.0)

4. **Logging:**
   - Semua proses update di-log di tabel `update_logs`
   - Bisa dilihat di halaman `/update/history`

5. **Security:**
   - Hanya Super Admin yang bisa akses halaman update
   - API endpoints bisa menggunakan Sanctum auth

---

## 🚀 Quick Start

### Untuk Admin:
1. Buat perubahan di code
2. Buat ZIP file
3. Upload ke server
4. Insert ke database
5. Set `is_active = 1`

### Untuk User:
1. Buka `/update`
2. Klik "Cek Update"
3. Klik "Update Sekarang"
4. Selesai!

---

**Alur ini sudah otomatis, user hanya perlu klik tombol dan sistem akan melakukan semua prosesnya!** 🎉











