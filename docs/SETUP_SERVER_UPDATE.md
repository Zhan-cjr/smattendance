# 🔧 Setup Check Update dari Server Eksternal

Panduan untuk mengatur sistem agar user melakukan check update ke server `https://presensi.Amnal.my.id`.

---

## 🎯 Tujuan

Agar ketika user melakukan **"Cek Update"**, sistem akan mengecek update dari server `https://presensi.Amnal.my.id` yang sudah Anda setup.

---

## 📋 Langkah-Langkah Setup

### **Langkah 1: Set URL Server Update di Config**

Tambahkan setting di file `.env`:

```env
UPDATE_SERVER_URL=https://presensi.Amnal.my.id
```

**Atau** edit file `config/update.php`:

```php
'server_url' => env('UPDATE_SERVER_URL', 'https://presensi.Amnal.my.id'),
```

### **Langkah 2: Pastikan Server Update Punya API Endpoint**

Server `https://presensi.Amnal.my.id` harus punya endpoint API berikut:

#### **Endpoint 1: `/api/update/check` (Recommended)**

**Request:**
```
GET https://presensi.Amnal.my.id/api/update/check?current_version=1.0.0
```

**Response:**
```json
{
    "success": true,
    "data": {
        "has_update": true,
        "current_version": "1.0.0",
        "latest_version": "1.0.1",
        "update": {
            "id": 1,
            "version": "1.0.1",
            "title": "Update Minor - Perbaikan Bug",
            "description": "Update ini memperbaiki beberapa bug",
            "changelog": "- Perbaikan bug presensi\n- Update UI",
            "file_url": "https://presensi.Amnal.my.id/updates/update_1.0.1.zip",
            "file_size": "5242880",
            "is_major": false,
            "released_at": "2024-01-15T10:00:00.000000Z"
        }
    }
}
```

#### **Endpoint 2: `/api/update/list` (Fallback)**

**Request:**
```
GET https://presensi.Amnal.my.id/api/update/list?active=true
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "version": "1.0.1",
            "title": "Update Minor",
            "description": "Deskripsi update",
            "changelog": "- Fix bug\n- New feature",
            "file_url": "https://presensi.Amnal.my.id/updates/update_1.0.1.zip",
            "file_size": "5242880",
            "is_major": false,
            "is_active": true,
            "released_at": "2024-01-15T10:00:00.000000Z"
        }
    ],
    "count": 1
}
```

### **Langkah 3: Setup Update di Server `https://presensi.Amnal.my.id`**

Di server `https://presensi.Amnal.my.id`, pastikan:

1. **Update sudah diinput** di halaman `/admin/update`
2. **File URL sudah diisi** dengan URL lengkap ke file ZIP
   - Contoh: `https://presensi.Amnal.my.id/updates/update_1.0.1.zip`
3. **Status update aktif** (`is_active = 1`)
4. **File ZIP sudah di-upload** ke server dan bisa diakses

### **Langkah 4: Test Check Update**

1. **Di aplikasi user (bukan server update):**
   - Buka halaman `/update`
   - Klik tombol **"Cek Update"**
   - Sistem akan otomatis mengecek ke `https://presensi.Amnal.my.id`

2. **Cek log jika ada error:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## 🔍 Cara Kerja Sistem

### **Alur Check Update:**

1. User klik **"Cek Update"** di halaman `/update`
2. Sistem membaca `UPDATE_SERVER_URL` dari config (atau `.env`)
3. Sistem request ke: `https://presensi.Amnal.my.id/api/update/check`
4. Server return data update
5. Sistem tampilkan update jika ada

### **Alur Download & Install:**

1. User klik **"Update Sekarang"**
2. Sistem download file dari URL yang ada di `file_url` (dari response API)
3. Sistem install update seperti biasa

---

## ⚙️ Konfigurasi Lengkap

### **File `.env`**

```env
# URL Server Update
UPDATE_SERVER_URL=https://presensi.Amnal.my.id

# Auto Check Update (opsional)
UPDATE_AUTO_CHECK=false

# Check Interval dalam hari (opsional)
UPDATE_CHECK_INTERVAL=7

# Backup sebelum update (opsional)
UPDATE_BACKUP_BEFORE_UPDATE=true
```

### **File `config/update.php`**

```php
return [
    'server_url' => env('UPDATE_SERVER_URL', 'https://presensi.Amnal.my.id'),
    'auto_check' => env('UPDATE_AUTO_CHECK', false),
    'check_interval' => env('UPDATE_CHECK_INTERVAL', 7),
    'backup_before_update' => env('UPDATE_BACKUP_BEFORE_UPDATE', true),
];
```

---

## 📝 Contoh Setup Lengkap

### **Di Server Update (`https://presensi.Amnal.my.id`):**

1. **Upload file update:**
   ```bash
   # Upload ke server
   scp update_1.0.1.zip user@server:/var/www/html/public/updates/
   ```

2. **Input update di database:**
   ```sql
   INSERT INTO updates (
       version, 
       title, 
       description,
       changelog,
       file_url, 
       is_active, 
       released_at
   ) VALUES (
       '1.0.1',
       'Update Minor - Perbaikan Bug',
       'Update ini memperbaiki beberapa bug',
       '- Perbaikan bug presensi\n- Update UI',
       'https://presensi.Amnal.my.id/updates/update_1.0.1.zip',
       1,
       NOW()
   );
   ```

3. **Pastikan API endpoint bisa diakses:**
   - Test: `https://presensi.Amnal.my.id/api/update/check`
   - Harus return JSON dengan format yang benar

### **Di Aplikasi User (yang akan update):**

1. **Set URL server di `.env`:**
   ```env
   UPDATE_SERVER_URL=https://presensi.Amnal.my.id
   ```

2. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Test check update:**
   - Buka `/update` → Klik "Cek Update"
   - Sistem akan mengecek ke `https://presensi.Amnal.my.id`

---

## ✅ Checklist Setup

### **Di Server Update (`https://presensi.Amnal.my.id`):**

- [ ] File update sudah di-upload ke server
- [ ] Update sudah diinput di `/admin/update`
- [ ] File URL sudah diisi dengan URL lengkap
- [ ] Status update aktif (`is_active = 1`)
- [ ] API endpoint `/api/update/check` bisa diakses
- [ ] API endpoint `/api/update/list` bisa diakses (opsional)
- [ ] File ZIP bisa di-download dari URL

### **Di Aplikasi User:**

- [ ] `UPDATE_SERVER_URL` sudah di-set di `.env`
- [ ] Config sudah di-clear (`php artisan config:clear`)
- [ ] Test check update berhasil
- [ ] Update bisa di-download dan di-install

---

## 🛠️ Troubleshooting

### **Error: "Gagal menghubungi server update"**

**Penyebab:**
- Server tidak bisa diakses
- URL salah
- Server tidak punya endpoint API

**Solusi:**
1. **Test URL di browser:**
   ```
   https://presensi.Amnal.my.id/api/update/check
   ```
   - Harus return JSON, bukan HTML

2. **Cek `.env`:**
   ```env
   UPDATE_SERVER_URL=https://presensi.Amnal.my.id
   ```
   - Pastikan tidak ada spasi
   - Pastikan menggunakan HTTPS (jika server support)

3. **Cek koneksi:**
   ```bash
   curl https://presensi.Amnal.my.id/api/update/check
   ```

### **Error: "Update tidak ditemukan"**

**Penyebab:**
- Server tidak return data update
- Format response tidak sesuai

**Solusi:**
1. **Cek response API:**
   ```bash
   curl https://presensi.Amnal.my.id/api/update/check?current_version=1.0.0
   ```

2. **Pastikan format response sesuai:**
   ```json
   {
       "success": true,
       "data": {
           "has_update": true,
           "latest_version": "1.0.1",
           "update": {...}
       }
   }
   ```

### **Error: "File tidak bisa di-download"**

**Penyebab:**
- URL file tidak bisa diakses
- File tidak ada di server

**Solusi:**
1. **Test URL file di browser:**
   ```
   https://presensi.Amnal.my.id/updates/update_1.0.1.zip
   ```
   - Harus bisa download file

2. **Cek file di server:**
   ```bash
   ls -lh /var/www/html/public/updates/update_1.0.1.zip
   ```

3. **Cek permission:**
   ```bash
   chmod 644 /var/www/html/public/updates/update_1.0.1.zip
   ```

---

## 💡 Tips

1. **Gunakan HTTPS** untuk keamanan
2. **Test API endpoint** sebelum digunakan
3. **Monitor log** jika ada masalah
4. **Backup database** sebelum update
5. **Test di staging** sebelum production

---

## 📞 Kesimpulan

**Setup sederhana:**

1. ✅ Set `UPDATE_SERVER_URL=https://presensi.Amnal.my.id` di `.env`
2. ✅ Pastikan server punya endpoint `/api/update/check`
3. ✅ Update sudah diinput di server dengan file URL yang benar
4. ✅ User tinggal klik "Cek Update" → Sistem otomatis cek ke server

**Selesai!** 🎉

---

**Selamat! Sistem update Anda sekarang sudah terhubung dengan server `https://presensi.Amnal.my.id`!**

