# 📦 Panduan Setup Fitur Update Otomatis

## 1. Setup Awal (Wajib)

### A. Jalankan Migration
```bash
php artisan migrate
```
Ini akan membuat tabel `updates` dan `update_logs` di database.

### B. Pastikan File VERSION Ada
File `VERSION` sudah dibuat di root project dengan isi:
```
1.0.0
```

### C. Set Permission (Linux/Mac)
```bash
chmod 755 storage/app/updates
chmod 755 storage/app/backups
```

---

## 2. Cara Membuat Update Package

### A. Struktur File Update (ZIP)

File update harus dalam format ZIP dengan struktur berikut:

```
update_1.0.1.zip
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── (file yang diupdate)
│   └── Models/
│       └── (file yang diupdate)
├── database/
│   └── migrations/
│       └── (migration baru jika ada)
├── resources/
│   └── views/
│       └── (view yang diupdate)
├── routes/
│   └── web.php (jika ada perubahan route)
├── config/
│   └── (config yang diupdate)
└── public/
    └── (asset yang diupdate)
```

**PENTING:**
- Jangan include folder `vendor/`, `node_modules/`, `.env`, `storage/`
- Hanya include file yang berubah atau file baru
- Pastikan struktur folder sama dengan struktur aplikasi

### B. Cara Membuat ZIP

**Opsi 1: Manual**
1. Copy file yang diupdate ke folder temporary
2. Buat struktur folder sesuai di atas
3. Zip semua folder tersebut
4. Beri nama: `update_1.0.1.zip` (sesuai versi)

**Opsi 2: Script Otomatis (Buat file `create-update.sh`)**
```bash
#!/bin/bash
VERSION=$1
if [ -z "$VERSION" ]; then
    echo "Usage: ./create-update.sh 1.0.1"
    exit 1
fi

# Buat folder temporary
mkdir -p temp_update/app
mkdir -p temp_update/database/migrations
mkdir -p temp_update/resources/views
mkdir -p temp_update/routes
mkdir -p temp_update/config
mkdir -p temp_update/public

# Copy file yang diupdate (sesuaikan dengan file yang berubah)
# Contoh:
# cp app/Http/Controllers/NewController.php temp_update/app/Http/Controllers/
# cp database/migrations/2024_01_01_new_migration.php temp_update/database/migrations/

# Buat ZIP
cd temp_update
zip -r ../update_${VERSION}.zip .
cd ..
rm -rf temp_update

echo "Update package created: update_${VERSION}.zip"
```

---

## 3. Menambahkan Data Update ke Database

### Opsi A: Via Database Langsung

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
    'https://your-server.com/updates/update_1.0.1.zip', -- URL file update
    '5242880',                                  -- Ukuran file (bytes)
    'a1b2c3d4e5f6...',                          -- MD5 checksum (opsional)
    0,                                          -- 0 = minor, 1 = major
    1,                                          -- 1 = aktif, 0 = nonaktif
    '["2024_01_01_new_migration.php"]',        -- Array migration (JSON)
    '["NewSeeder"]',                           -- Array seeder (JSON)
    NOW(),                                      -- Tanggal rilis
    NOW(),
    NOW()
);
```

### Opsi B: Via Tinker (Laravel)

```bash
php artisan tinker
```

```php
use App\Models\Update;

Update::create([
    'version' => '1.0.1',
    'title' => 'Update Minor - Perbaikan Bug',
    'description' => 'Update ini memperbaiki beberapa bug',
    'changelog' => "- Perbaikan bug presensi\n- Penambahan fitur export",
    'file_url' => 'https://your-server.com/updates/update_1.0.1.zip',
    'file_size' => '5242880',
    'checksum' => md5_file('/path/to/update_1.0.1.zip'), // Opsional
    'is_major' => false,
    'is_active' => true,
    'migrations' => ['2024_01_01_new_migration.php'],
    'seeders' => [],
    'released_at' => now(),
]);
```

### Opsi C: Via Admin Panel (Akan dibuat)

Gunakan halaman admin untuk menambahkan update (akan dibuat di langkah berikutnya).

---

## 4. Menghitung Checksum File

Untuk keamanan, hitung MD5 checksum file update:

**Linux/Mac:**
```bash
md5sum update_1.0.1.zip
```

**Windows:**
```powershell
Get-FileHash update_1.0.1.zip -Algorithm MD5
```

**PHP:**
```php
$checksum = md5_file('update_1.0.1.zip');
```

---

## 5. Upload File Update

### Opsi A: Server Sendiri
1. Upload file ZIP ke server (misal: `public/updates/`)
2. Set URL di database: `https://your-domain.com/updates/update_1.0.1.zip`

### Opsi B: Cloud Storage
1. Upload ke Google Drive, Dropbox, atau storage cloud lainnya
2. Dapatkan direct download link
3. Set URL di database

### Opsi C: GitHub Releases
1. Buat release di GitHub
2. Upload file ZIP sebagai asset release
3. Gunakan direct download link dari GitHub

---

## 6. Testing Update

1. **Test di Environment Development:**
   - Buat backup database
   - Test update process
   - Verifikasi semua fitur berjalan

2. **Test di Staging:**
   - Deploy update ke staging
   - Test semua skenario
   - Pastikan tidak ada breaking changes

3. **Release ke Production:**
   - Set `is_active = 1` di database
   - User akan bisa melihat update di halaman `/update`

---

## 7. Checklist Sebelum Release Update

- [ ] File ZIP sudah dibuat dengan struktur yang benar
- [ ] File ZIP sudah di-upload dan URL bisa diakses
- [ ] Checksum sudah dihitung dan disimpan
- [ ] Migration sudah ditest
- [ ] Seeder sudah ditest (jika ada)
- [ ] Data update sudah ditambahkan ke database
- [ ] `is_active = 1` untuk mengaktifkan update
- [ ] Test update di environment development
- [ ] Backup database sebelum update (otomatis dilakukan sistem)
- [ ] Dokumentasi changelog sudah lengkap

---

## 8. Contoh Workflow Update

### Scenario: Update dari 1.0.0 ke 1.0.1

1. **Development:**
   ```bash
   # Buat perubahan di code
   # Buat migration jika perlu
   php artisan make:migration add_new_column_to_table
   
   # Test perubahan
   php artisan migrate
   ```

2. **Buat Update Package:**
   ```bash
   # Copy file yang berubah ke folder update
   # Buat ZIP
   zip -r update_1.0.1.zip app/ database/migrations/2024_01_01_*.php
   ```

3. **Upload & Setup:**
   ```bash
   # Upload ke server
   # Tambahkan data ke database (via admin panel atau SQL)
   ```

4. **Release:**
   - Set `is_active = 1`
   - User akan mendapat notifikasi update tersedia

5. **User Update:**
   - User klik "Cek Update" di halaman `/update`
   - Klik "Update Sekarang"
   - Sistem otomatis:
     - Backup database
     - Download file
     - Extract & copy file
     - Run migration
     - Update versi
     - Clear cache

---

## 9. Troubleshooting

### Update Gagal?
1. Cek log di `storage/logs/laravel.log`
2. Cek detail log di halaman `/update/log/{id}`
3. Pastikan:
   - File ZIP bisa diakses
   - Checksum sesuai
   - Permission folder benar
   - Database backup berhasil

### File Tidak Terupdate?
1. Pastikan struktur folder di ZIP benar
2. Pastikan file path sesuai dengan aplikasi
3. Cek permission folder `app/`, `resources/`, dll

### Migration Error?
1. Pastikan migration file ada di ZIP
2. Pastikan nama migration benar
3. Cek apakah migration sudah pernah dijalankan

---

## 10. Best Practices

1. **Versioning:**
   - Gunakan Semantic Versioning (1.0.0, 1.0.1, 1.1.0, 2.0.0)
   - Major (2.0.0): Breaking changes
   - Minor (1.1.0): Fitur baru, backward compatible
   - Patch (1.0.1): Bug fix

2. **File Update:**
   - Hanya include file yang berubah
   - Jangan include `.env`, `vendor/`, `node_modules/`
   - Test ZIP sebelum upload

3. **Database:**
   - Selalu backup sebelum update
   - Test migration di development dulu
   - Dokumentasikan perubahan database

4. **Changelog:**
   - Tulis changelog yang jelas
   - Jelaskan fitur baru, bug fix, breaking changes
   - Gunakan format yang mudah dibaca

---

## Next Steps

Setelah setup ini, user bisa:
1. Akses halaman `/update`
2. Klik "Cek Update"
3. Jika ada update, klik "Update Sekarang"
4. Sistem akan otomatis melakukan update

**Catatan:** Disarankan membuat admin panel untuk manage update agar lebih mudah (akan dibuat di langkah berikutnya).











