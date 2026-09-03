# 🔗 Panduan Menentukan URL Sumber Update

Panduan lengkap tentang bagaimana sistem menentukan URL sumber update dan cara mengaturnya.

---

## 📊 Cara Kerja Sistem Update

Sistem update memiliki **2 mode** untuk menentukan sumber update:

### **Mode 1: Database Lokal (Default) ✅**

Sistem akan mengecek update dari **tabel `updates` di database lokal** Anda.

**Alur:**
1. User klik "Cek Update"
2. Sistem query database: `SELECT * FROM updates WHERE is_active = 1 ORDER BY version DESC`
3. Sistem bandingkan versi dengan versi saat ini
4. Jika ada update, ambil data dari database termasuk **`file_url`**
5. File update di-download dari URL yang ada di kolom `file_url`

**Keuntungan:**
- ✅ Tidak perlu server eksternal
- ✅ Kontrol penuh atas update
- ✅ Lebih cepat (tidak perlu request ke server lain)
- ✅ Bisa digunakan untuk aplikasi self-hosted

### **Mode 2: Server Eksternal**

Sistem akan mengecek update dari **server eksternal** via API.

**Alur:**
1. User klik "Cek Update"
2. Sistem request ke: `{updateServerUrl}/api/check-update`
3. Server eksternal return data update
4. File update di-download dari URL yang diberikan server

**Keuntungan:**
- ✅ Update terpusat di satu server
- ✅ Mudah manage update untuk banyak aplikasi
- ✅ Bisa tracking update dari berbagai aplikasi

---

## 🎯 Menentukan URL File Update

### **Untuk Mode Database Lokal (Recommended)**

URL file update disimpan di **kolom `file_url`** di tabel `updates`. Ada beberapa cara untuk menentukan URL:

#### **Opsi 1: Server Sendiri (Paling Umum)**

Upload file ke server Anda sendiri:

```
URL Format: https://domain-anda.com/updates/update_1.0.1.zip
```

**Langkah:**
1. Upload file ZIP ke folder: `public/updates/`
2. Set URL di database: `https://domain-anda.com/updates/update_1.0.1.zip`
3. Pastikan file bisa diakses via browser

**Contoh:**
```sql
UPDATE updates 
SET file_url = 'https://presensigps.com/updates/update_1.0.1.zip'
WHERE version = '1.0.1';
```

#### **Opsi 2: Cloud Storage (Google Drive)**

1. Upload file ke Google Drive
2. Klik kanan file → Get link → Copy link
3. Ubah format URL:
   - **Dari:** `https://drive.google.com/file/d/FILE_ID/view?usp=sharing`
   - **Menjadi:** `https://drive.google.com/uc?export=download&id=FILE_ID`

**Contoh:**
```sql
UPDATE updates 
SET file_url = 'https://drive.google.com/uc?export=download&id=1ABC123XYZ456'
WHERE version = '1.0.1';
```

#### **Opsi 3: Cloud Storage (Dropbox)**

1. Upload file ke Dropbox
2. Klik kanan file → Copy link
3. Ubah `?dl=0` menjadi `?dl=1` di akhir URL

**Contoh:**
```sql
UPDATE updates 
SET file_url = 'https://www.dropbox.com/s/abc123xyz/update_1.0.1.zip?dl=1'
WHERE version = '1.0.1';
```

#### **Opsi 4: GitHub Releases**

1. Buat release di GitHub
2. Upload file ZIP sebagai asset
3. Copy direct download link

**Contoh:**
```sql
UPDATE updates 
SET file_url = 'https://github.com/username/repo/releases/download/v1.0.1/update_1.0.1.zip'
WHERE version = '1.0.1';
```

#### **Opsi 5: CDN atau Storage Lain**

Bisa menggunakan:
- AWS S3
- Cloudflare R2
- DigitalOcean Spaces
- Lainnya yang support direct download

**Contoh:**
```sql
UPDATE updates 
SET file_url = 'https://cdn.example.com/updates/update_1.0.1.zip'
WHERE version = '1.0.1';
```

---

## ⚙️ Setting Mode Update

### **Menggunakan Database Lokal (Default)**

**Tidak perlu setting apapun!** Sistem otomatis menggunakan database lokal jika tidak ada `UPDATE_SERVER_URL`.

**Cara kerja:**
- Sistem akan cek tabel `updates` di database
- Ambil update yang `is_active = 1`
- Download file dari kolom `file_url`

### **Menggunakan Server Eksternal**

Jika ingin menggunakan server eksternal, set di file `.env`:

```env
UPDATE_SERVER_URL=https://update-server.com
```

Atau via config `config/update.php`:

```php
'server_url' => env('UPDATE_SERVER_URL', null),
```

**Cara kerja:**
- Sistem akan request ke: `{UPDATE_SERVER_URL}/api/check-update`
- Server harus return format JSON:
  ```json
  {
    "has_update": true,
    "latest_version": "1.0.1",
    "update": {
      "version": "1.0.1",
      "file_url": "https://update-server.com/files/update_1.0.1.zip",
      "title": "Update Minor",
      "description": "...",
      "changelog": "..."
    }
  }
  ```

---

## 📝 Contoh Lengkap: Setup URL Update

### **Skenario: Update dari 1.0.0 ke 1.0.1**

#### **Langkah 1: Upload File ke Server**

```bash
# Upload via FTP/SFTP
scp update_1.0.1.zip user@server:/var/www/html/public/updates/
```

Atau via File Manager:
- Upload ke: `public/updates/update_1.0.1.zip`

#### **Langkah 2: Tentukan URL**

URL akan menjadi:
```
https://domain-anda.com/updates/update_1.0.1.zip
```

**Test URL:**
- Buka di browser: `https://domain-anda.com/updates/update_1.0.1.zip`
- Pastikan file bisa di-download

#### **Langkah 3: Simpan URL ke Database**

**Via Admin Panel:**
1. Login → `/admin/update` → "Tambah Update"
2. Isi form:
   - Versi: `1.0.1`
   - File URL: `https://domain-anda.com/updates/update_1.0.1.zip`
   - Status: Aktif
3. Simpan

**Via SQL:**
```sql
INSERT INTO updates (
    version, 
    title, 
    file_url, 
    is_active, 
    released_at
) VALUES (
    '1.0.1',
    'Update Minor',
    'https://domain-anda.com/updates/update_1.0.1.zip',
    1,
    NOW()
);
```

**Via Tinker:**
```bash
php artisan tinker
```

```php
use App\Models\Update;

Update::create([
    'version' => '1.0.1',
    'title' => 'Update Minor',
    'file_url' => 'https://domain-anda.com/updates/update_1.0.1.zip',
    'is_active' => true,
    'released_at' => now(),
]);
```

#### **Langkah 4: Verifikasi**

1. Buka `/update` → Klik "Cek Update"
2. Sistem akan:
   - Query database untuk update aktif
   - Bandingkan versi
   - Tampilkan update jika ada
3. Saat user klik "Update Sekarang":
   - Sistem download dari URL: `https://domain-anda.com/updates/update_1.0.1.zip`
   - Extract dan install

---

## 🔍 Cara Cek URL Update Saat Ini

### **Via Database**

```sql
SELECT version, title, file_url, is_active 
FROM updates 
WHERE is_active = 1 
ORDER BY version DESC;
```

### **Via Tinker**

```bash
php artisan tinker
```

```php
use App\Models\Update;

$updates = Update::where('is_active', 1)
    ->orderBy('version', 'desc')
    ->get(['version', 'title', 'file_url']);

foreach ($updates as $update) {
    echo "Version: {$update->version}\n";
    echo "URL: {$update->file_url}\n\n";
}
```

### **Via Admin Panel**

1. Login → `/admin/update`
2. Lihat daftar update
3. Klik detail untuk melihat URL lengkap

---

## ✅ Checklist URL Update

Sebelum publish update, pastikan:

- [ ] **File sudah di-upload** ke server/storage
- [ ] **URL bisa diakses** (test di browser)
- [ ] **URL sudah disimpan** di database (kolom `file_url`)
- [ ] **Status update aktif** (`is_active = 1`)
- [ ] **File tidak corrupt** (test download manual)
- [ ] **Permission file benar** (644 untuk file, 755 untuk folder)
- [ ] **Checksum sesuai** (jika menggunakan checksum)

---

## 🛠️ Troubleshooting URL Update

### **Error: "URL file update tidak tersedia"**

**Penyebab:**
- Kolom `file_url` di database kosong atau NULL

**Solusi:**
```sql
-- Cek data
SELECT version, file_url FROM updates WHERE version = '1.0.1';

-- Update jika kosong
UPDATE updates 
SET file_url = 'https://domain-anda.com/updates/update_1.0.1.zip'
WHERE version = '1.0.1' AND file_url IS NULL;
```

### **Error: "Gagal mengunduh file update"**

**Penyebab:**
- URL tidak bisa diakses
- File tidak ada di server
- Permission salah
- Server down

**Solusi:**
1. **Test URL di browser:**
   ```
   https://domain-anda.com/updates/update_1.0.1.zip
   ```
   - Jika bisa download = URL benar
   - Jika error 404 = file tidak ada
   - Jika error 403 = permission salah

2. **Cek file di server:**
   ```bash
   ls -lh public/updates/update_1.0.1.zip
   ```

3. **Cek permission:**
   ```bash
   chmod 644 public/updates/update_1.0.1.zip
   chmod 755 public/updates/
   ```

### **Error: "Checksum file tidak valid"**

**Penyebab:**
- File corrupt atau berubah
- Checksum di database tidak sesuai

**Solusi:**
1. **Hitung ulang checksum:**
   ```bash
   md5sum public/updates/update_1.0.1.zip
   ```

2. **Update checksum di database:**
   ```sql
   UPDATE updates 
   SET checksum = 'a1b2c3d4e5f6...'
   WHERE version = '1.0.1';
   ```

3. **Atau hapus checksum** (jika tidak perlu validasi):
   ```sql
   UPDATE updates 
   SET checksum = NULL
   WHERE version = '1.0.1';
   ```

### **URL Google Drive Tidak Bisa Download**

**Penyebab:**
- Format URL salah
- File terlalu besar (Google Drive batasi direct download)

**Solusi:**
1. **Pastikan format URL benar:**
   ```
   https://drive.google.com/uc?export=download&id=FILE_ID
   ```

2. **Untuk file besar, gunakan alternatif:**
   - Upload ke server sendiri
   - Gunakan Dropbox
   - Gunakan GitHub Releases

---

## 💡 Tips & Best Practices

### **1. Gunakan Server Sendiri (Recommended)**
- ✅ Kontrol penuh
- ✅ Tidak ada limit download
- ✅ Lebih cepat
- ✅ Lebih aman

### **2. Gunakan HTTPS**
- ✅ Lebih aman
- ✅ Tidak ada masalah mixed content
- ✅ Recommended untuk production

### **3. Organisasi File**
```
public/updates/
├── update_1.0.1.zip
├── update_1.0.2.zip
├── update_1.1.0.zip
└── update_2.0.0.zip
```

### **4. Backup URL**
Simpan URL di tempat aman, jika server bermasalah bisa cepat ganti URL.

### **5. Monitoring**
- Monitor apakah file masih bisa diakses
- Cek log download error
- Track berapa banyak download

---

## 📞 Kesimpulan

**Untuk aplikasi self-hosted (seperti Anda):**

1. ✅ **Gunakan Mode Database Lokal** (default, tidak perlu setting)
2. ✅ **Upload file ke server sendiri** → `public/updates/`
3. ✅ **Set URL di database** → `https://domain-anda.com/updates/update_X.X.X.zip`
4. ✅ **Aktifkan update** → Set `is_active = 1`

**Sistem akan otomatis:**
- Cek update dari database lokal
- Download file dari URL yang ada di kolom `file_url`
- Install update

**Tidak perlu server eksternal atau setting khusus!** 🎉

---

**Selamat! Sekarang Anda sudah paham bagaimana menentukan URL sumber update!**

