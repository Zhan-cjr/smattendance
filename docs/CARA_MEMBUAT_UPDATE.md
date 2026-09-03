# 🚀 Cara Membuat & Mempublish Update untuk Pengguna

Panduan lengkap untuk membuat update baru yang bisa diakses oleh semua pengguna aplikasi.

---

## 📋 Langkah-Langkah Membuat Update

### **Langkah 1: Siapkan File yang Diupdate**

1. **Identifikasi file yang berubah:**
   - File controller yang diupdate
   - File model yang diupdate
   - File view yang diupdate
   - Migration baru (jika ada)
   - File config yang diupdate
   - File route yang diupdate

2. **Contoh file yang mungkin diupdate:**
   ```
   app/Http/Controllers/DashboardController.php
   app/Models/Karyawan.php
   database/migrations/2024_01_15_add_new_column.php
   resources/views/dashboard/index.blade.php
   routes/web.php
   ```

---

### **Langkah 2: Buat File ZIP Update Package**

#### **A. Struktur Folder Update**

Buat folder temporary dengan struktur seperti ini:

```
temp_update/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── DashboardController.php
│   └── Models/
│       └── Karyawan.php
├── database/
│   └── migrations/
│       └── 2024_01_15_add_new_column.php
├── resources/
│   └── views/
│       └── dashboard/
│           └── index.blade.php
└── routes/
    └── web.php
```

**PENTING:**
- ✅ Hanya include file yang **berubah** atau **baru**
- ❌ JANGAN include: `vendor/`, `node_modules/`, `.env`, `storage/`
- ✅ Struktur folder harus sama dengan struktur aplikasi

#### **B. Cara Membuat ZIP**

**Opsi 1: Manual (Windows)**
1. Buat folder `temp_update`
2. Copy file yang diupdate ke folder sesuai struktur di atas
3. Klik kanan folder `temp_update` → Send to → Compressed (zipped) folder
4. Rename menjadi: `update_1.0.1.zip` (sesuai versi)

**Opsi 2: Manual (Linux/Mac)**
```bash
# Buat folder temporary
mkdir -p temp_update/app/Http/Controllers
mkdir -p temp_update/database/migrations
mkdir -p temp_update/resources/views/dashboard
mkdir -p temp_update/routes

# Copy file yang diupdate
cp app/Http/Controllers/DashboardController.php temp_update/app/Http/Controllers/
cp database/migrations/2024_01_15_add_new_column.php temp_update/database/migrations/

# Buat ZIP
cd temp_update
zip -r ../update_1.0.1.zip .
cd ..
rm -rf temp_update
```

**Opsi 3: Script Otomatis (Buat file `create-update.sh` atau `create-update.bat`)**

**Windows (create-update.bat):**
```batch
@echo off
set VERSION=%1
if "%VERSION%"=="" (
    echo Usage: create-update.bat 1.0.1
    exit /b 1
)

echo Membuat update package versi %VERSION%...

REM Buat folder temporary
mkdir temp_update\app\Http\Controllers 2>nul
mkdir temp_update\database\migrations 2>nul
mkdir temp_update\resources\views 2>nul
mkdir temp_update\routes 2>nul

REM Copy file yang diupdate (sesuaikan dengan file Anda)
REM Contoh:
REM copy app\Http\Controllers\DashboardController.php temp_update\app\Http\Controllers\

REM Buat ZIP (gunakan 7-Zip atau WinRAR)
REM Jika pakai PowerShell:
powershell Compress-Archive -Path temp_update\* -DestinationPath update_%VERSION%.zip -Force

REM Hapus folder temporary
rmdir /s /q temp_update

echo Update package created: update_%VERSION%.zip
```

**Linux/Mac (create-update.sh):**
```bash
#!/bin/bash
VERSION=$1
if [ -z "$VERSION" ]; then
    echo "Usage: ./create-update.sh 1.0.1"
    exit 1
fi

echo "Membuat update package versi $VERSION..."

# Buat folder temporary
mkdir -p temp_update/app/Http/Controllers
mkdir -p temp_update/database/migrations
mkdir -p temp_update/resources/views
mkdir -p temp_update/routes

# Copy file yang diupdate (sesuaikan dengan file Anda)
# Contoh:
# cp app/Http/Controllers/DashboardController.php temp_update/app/Http/Controllers/
# cp database/migrations/2024_01_15_add_new_column.php temp_update/database/migrations/

# Buat ZIP
cd temp_update
zip -r ../update_${VERSION}.zip .
cd ..
rm -rf temp_update

echo "Update package created: update_${VERSION}.zip"
```

---

### **Langkah 3: Upload File ZIP ke Server**

#### **Opsi A: Upload ke Server Sendiri (Recommended)**

1. **Upload via FTP/SFTP:**
   - Upload file `update_1.0.1.zip` ke folder: `public/updates/`
   - Pastikan folder `public/updates/` sudah ada, jika belum buat dulu
   - URL akan menjadi: `https://domain-anda.com/updates/update_1.0.1.zip`

2. **Upload via SSH:**
   ```bash
   scp update_1.0.1.zip user@server:/var/www/html/public/updates/
   ```

3. **Set Permission (Linux):**
   ```bash
   chmod 644 public/updates/update_1.0.1.zip
   ```

#### **Opsi B: Upload ke Cloud Storage**

1. **Google Drive:**
   - Upload file ke Google Drive
   - Klik kanan file → Get link → Copy link
   - Ubah link menjadi direct download:
     - Ganti `https://drive.google.com/file/d/FILE_ID/view?usp=sharing`
     - Menjadi: `https://drive.google.com/uc?export=download&id=FILE_ID`

2. **Dropbox:**
   - Upload file ke Dropbox
   - Klik kanan file → Copy link
   - Ubah `?dl=0` menjadi `?dl=1` di akhir URL

3. **GitHub Releases:**
   - Buat release baru di GitHub
   - Upload file ZIP sebagai asset
   - Copy direct download link

#### **Opsi C: Upload via Admin Panel (Jika Fitur Upload Ada)**

1. Akses halaman admin update
2. Gunakan fitur upload file (jika tersedia)

---

### **Langkah 4: Hitung Ukuran File & Checksum (Opsional)**

#### **Ukuran File:**
- **Windows:** Klik kanan file → Properties → Size (dalam bytes)
- **Linux/Mac:** `ls -lh update_1.0.1.zip`
- **PHP:** `filesize('update_1.0.1.zip')`

#### **Checksum MD5 (Untuk Keamanan):**
- **Windows PowerShell:**
  ```powershell
  Get-FileHash update_1.0.1.zip -Algorithm MD5
  ```

- **Linux/Mac:**
  ```bash
  md5sum update_1.0.1.zip
  ```

- **PHP:**
  ```php
  $checksum = md5_file('update_1.0.1.zip');
  echo $checksum;
  ```

---

### **Langkah 5: Tambahkan Data Update ke Database**

#### **Cara 1: Via Admin Panel (Paling Mudah) ✅**

1. **Login sebagai Super Admin**
2. **Akses halaman:** `/admin/update`
3. **Klik tombol "Tambah Update"**
4. **Isi form dengan data berikut:**

   | Field | Contoh | Keterangan |
   |-------|--------|------------|
   | **Versi** | `1.0.1` | Versi update (wajib, harus unik) |
   | **Judul** | `Update Minor - Perbaikan Bug` | Judul update (wajib) |
   | **Deskripsi** | `Update ini memperbaiki beberapa bug dan menambahkan fitur baru` | Deskripsi singkat |
   | **Changelog** | `- Perbaikan bug presensi\n- Penambahan fitur export\n- Update UI dashboard` | Daftar perubahan (gunakan `\n` untuk baris baru) |
   | **File URL** | `https://domain-anda.com/updates/update_1.0.1.zip` | URL file ZIP (wajib) |
   | **Ukuran File** | `5242880` | Ukuran dalam bytes (opsional) |
   | **Checksum** | `a1b2c3d4e5f6...` | MD5 checksum (opsional) |
   | **Tipe Update** | ☐ Major / ☑ Minor | Centang jika major update |
   | **Status** | ☑ Aktif / ☐ Nonaktif | Centang untuk mengaktifkan |
   | **Tanggal Rilis** | `2024-01-15` | Tanggal rilis update |
   | **Migrations** | `2024_01_15_add_new_column.php` | Nama file migration (pisahkan dengan koma jika banyak) |
   | **Seeders** | `NewSeeder` | Nama seeder (pisahkan dengan koma jika banyak) |

5. **Klik "Simpan"**

#### **Cara 2: Via SQL (Manual)**

Jalankan query SQL berikut:

```sql
INSERT INTO `updates` (
    `version`, 
    `title`, 
    `description`, 
    `changelog`, 
    `file_url`, 
    `file_size`, 
    `checksum`, 
    `is_major`, 
    `is_active`, 
    `migrations`, 
    `seeders`, 
    `released_at`, 
    `created_at`, 
    `updated_at`
) VALUES (
    '1.0.1',                                    -- Versi update
    'Update Minor - Perbaikan Bug',            -- Judul
    'Update ini memperbaiki beberapa bug dan menambahkan fitur baru', -- Deskripsi
    '- Perbaikan bug presensi\n- Penambahan fitur export\n- Update UI', -- Changelog
    'https://domain-anda.com/updates/update_1.0.1.zip', -- URL file update
    '5242880',                                  -- Ukuran file (bytes)
    'a1b2c3d4e5f6...',                          -- MD5 checksum (opsional, bisa NULL)
    0,                                          -- 0 = minor, 1 = major
    1,                                          -- 1 = aktif, 0 = nonaktif
    '["2024_01_15_add_new_column.php"]',        -- Array migration (JSON, bisa NULL)
    '[]',                                       -- Array seeder (JSON, bisa NULL)
    NOW(),                                      -- Tanggal rilis
    NOW(),
    NOW()
);
```

#### **Cara 3: Via Laravel Tinker**

```bash
php artisan tinker
```

```php
use App\Models\Update;

Update::create([
    'version' => '1.0.1',
    'title' => 'Update Minor - Perbaikan Bug',
    'description' => 'Update ini memperbaiki beberapa bug dan menambahkan fitur baru',
    'changelog' => "- Perbaikan bug presensi\n- Penambahan fitur export\n- Update UI",
    'file_url' => 'https://domain-anda.com/updates/update_1.0.1.zip',
    'file_size' => '5242880',
    'checksum' => 'a1b2c3d4e5f6...', // Opsional
    'is_major' => false,
    'is_active' => true,
    'migrations' => ['2024_01_15_add_new_column.php'], // Array atau null
    'seeders' => [], // Array atau null
    'released_at' => now(),
]);
```

---

### **Langkah 6: Aktifkan Update**

#### **Via Admin Panel:**
1. Akses: `/admin/update`
2. Cari update yang baru dibuat
3. Klik tombol toggle untuk mengaktifkan (atau edit dan centang "Aktif")
4. Pastikan status menjadi **"Aktif"**

#### **Via SQL:**
```sql
UPDATE `updates` 
SET `is_active` = 1 
WHERE `version` = '1.0.1';
```

---

### **Langkah 7: Verifikasi Update Tersedia**

1. **Login sebagai Super Admin** (atau user lain)
2. **Akses halaman:** `/update`
3. **Klik tombol "Cek Update"**
4. **Pastikan update muncul:**
   - Versi terbaru: `1.0.1`
   - Judul update
   - Changelog
   - Tombol "Update Sekarang"

---

## 📝 Contoh Lengkap: Update dari 1.0.0 ke 1.0.1

### **Skenario:**
Anda sudah update file `DashboardController.php` dan menambah migration baru.

### **Langkah 1: Buat ZIP**
```bash
# Buat folder
mkdir -p temp_update/app/Http/Controllers
mkdir -p temp_update/database/migrations

# Copy file
cp app/Http/Controllers/DashboardController.php temp_update/app/Http/Controllers/
cp database/migrations/2024_01_15_add_new_column.php temp_update/database/migrations/

# ZIP
cd temp_update
zip -r ../update_1.0.1.zip .
cd ..
rm -rf temp_update
```

### **Langkah 2: Upload**
```bash
# Upload ke server
scp update_1.0.1.zip user@server:/var/www/html/public/updates/
```

### **Langkah 3: Tambahkan ke Database via Admin Panel**
1. Login → `/admin/update` → "Tambah Update"
2. Isi form:
   - Versi: `1.0.1`
   - Judul: `Update Minor - Perbaikan Dashboard`
   - Deskripsi: `Update ini memperbaiki bug di dashboard dan menambahkan kolom baru`
   - Changelog: `- Fix bug dashboard\n- Tambah kolom baru di tabel karyawan`
   - File URL: `https://domain-anda.com/updates/update_1.0.1.zip`
   - Ukuran File: `2048576` (2 MB)
   - Tipe: Minor
   - Status: **Aktif** ✅
   - Migrations: `2024_01_15_add_new_column.php`
3. Klik "Simpan"

### **Langkah 4: Test**
1. Buka `/update` di browser lain (atau user lain)
2. Klik "Cek Update"
3. Pastikan update `1.0.1` muncul

---

## ✅ Checklist Sebelum Publish Update

- [ ] File ZIP sudah dibuat dengan struktur yang benar
- [ ] File ZIP sudah di-upload dan URL bisa diakses
- [ ] Ukuran file sudah dihitung (opsional)
- [ ] Checksum sudah dihitung (opsional, untuk keamanan)
- [ ] Data update sudah ditambahkan ke database
- [ ] Status update sudah diaktifkan (`is_active = 1`)
- [ ] Test update di environment development/staging
- [ ] Changelog sudah lengkap dan jelas
- [ ] Migration sudah ditest (jika ada)
- [ ] Seeder sudah ditest (jika ada)

---

## 🔍 Troubleshooting

### **Update Tidak Muncul di Halaman `/update`?**

**Cek:**
1. ✅ Status `is_active = 1` di database
2. ✅ Versi update lebih besar dari versi saat ini (cek file `VERSION`)
3. ✅ File URL bisa diakses (test di browser)
4. ✅ Cache sudah di-clear

**Solusi:**
```bash
php artisan cache:clear
php artisan config:clear
```

### **File URL Tidak Bisa Diakses?**

**Cek:**
1. ✅ File sudah di-upload ke server
2. ✅ Permission file benar (644 untuk file)
3. ✅ Folder `public/updates/` ada dan bisa diakses
4. ✅ URL benar (test di browser)

### **Error Saat User Download Update?**

**Cek:**
1. ✅ File ZIP tidak corrupt
2. ✅ Checksum sesuai (jika ada)
3. ✅ Ukuran file sesuai
4. ✅ Server bisa download file dari URL tersebut

---

## 🎯 Tips & Best Practices

### **1. Versioning (Semantic Versioning)**
- **Major (2.0.0):** Breaking changes, perubahan besar
- **Minor (1.1.0):** Fitur baru, backward compatible
- **Patch (1.0.1):** Bug fix, perbaikan kecil

### **2. File Update**
- ✅ Hanya include file yang **berubah**
- ❌ Jangan include: `.env`, `vendor/`, `node_modules/`, `storage/`
- ✅ Test ZIP sebelum upload (extract dan cek struktur)

### **3. Changelog**
- Tulis changelog yang **jelas** dan **mudah dipahami**
- Gunakan format list (`- Item 1\n- Item 2`)
- Jelaskan: fitur baru, bug fix, breaking changes

### **4. Testing**
- ✅ Test update di development dulu
- ✅ Test di staging sebelum production
- ✅ Pastikan migration berjalan dengan benar
- ✅ Test semua fitur setelah update

### **5. Release Strategy**
- **Staged Release:** Aktifkan untuk beberapa user dulu, monitor, lalu aktifkan untuk semua
- **Rollback Plan:** Siapkan backup dan cara rollback jika ada masalah

---

## 📞 Bantuan

Jika ada masalah:
1. Cek log: `storage/logs/laravel.log`
2. Cek database: Pastikan data update benar
3. Test URL file: Pastikan bisa diakses
4. Hubungi developer jika perlu

---

**Selamat! Update Anda sekarang sudah bisa diakses oleh semua pengguna! 🎉**

