# 📘 Panduan Update Aplikasi - Untuk Pemilik Aplikasi

## 🎯 Pengenalan

Dokumen ini menjelaskan cara melakukan update aplikasi Presensi GPS v2 dari sisi Anda sebagai pemilik aplikasi. Sistem update ini memungkinkan Anda untuk:

- ✅ Melihat versi aplikasi saat ini
- ✅ Mengecek update terbaru yang tersedia
- ✅ Menginstall update dengan mudah
- ✅ Melihat riwayat update yang pernah dilakukan
- ✅ Mengelola update yang akan dirilis ke user

---

## 📍 Lokasi Fitur Update

### 1. Halaman Update (Untuk Melakukan Update)
**URL:** `/update`  
**Akses:** Hanya Super Admin

Di halaman ini Anda bisa:
- Melihat versi aplikasi saat ini
- Mengecek update terbaru
- Menginstall update
- Melihat riwayat update

### 2. Halaman Manage Update (Untuk Mengelola Update)
**URL:** `/admin/update`  
**Akses:** Hanya Super Admin

Di halaman ini Anda bisa:
- Menambah update baru
- Mengedit update yang sudah ada
- Mengaktifkan/nonaktifkan update
- Melihat daftar semua update

---

## 🚀 Cara Melakukan Update Aplikasi

### Langkah 1: Login sebagai Super Admin
1. Login ke aplikasi dengan akun Super Admin
2. Pastikan Anda memiliki akses penuh

### Langkah 2: Akses Halaman Update
1. Buka menu atau langsung akses: `https://domain-anda.com/update`
2. Anda akan melihat:
   - **Versi Saat Ini:** Versi aplikasi yang sedang digunakan
   - **Tombol "Cek Update":** Untuk mengecek update terbaru
   - **Riwayat Update:** Daftar update yang pernah dilakukan

### Langkah 3: Cek Update Terbaru
1. Klik tombol **"Cek Update"**
2. Sistem akan mengecek apakah ada update tersedia
3. Jika ada update, akan muncul informasi:
   - Versi terbaru
   - Judul update
   - Deskripsi
   - Changelog (daftar perubahan)
   - Ukuran file

### Langkah 4: Install Update
1. Setelah melihat informasi update, klik tombol **"Update Sekarang"**
2. Konfirmasi dialog akan muncul
3. Klik **"Ya"** untuk melanjutkan
4. **Sistem akan otomatis melakukan:**
   - ✅ Backup database (otomatis)
   - ✅ Download file update
   - ✅ Extract dan copy file
   - ✅ Menjalankan migration database (jika ada)
   - ✅ Update versi aplikasi
   - ✅ Clear cache

### Langkah 5: Verifikasi Update
1. Setelah proses selesai, halaman akan reload
2. Cek versi baru di halaman update
3. Pastikan aplikasi berjalan normal
4. Test fitur-fitur penting

---

## 📋 Checklist Sebelum Update

Sebelum melakukan update, pastikan:

- [ ] **Backup Manual (Opsional):** Meskipun sistem otomatis backup, disarankan backup manual database
- [ ] **Cek Changelog:** Baca perubahan yang ada di update
- [ ] **Waktu Update:** Lakukan di waktu sepi (jika memungkinkan)
- [ ] **Notifikasi User:** Beri tahu user jika update akan mempengaruhi mereka
- [ ] **Test di Staging:** Jika ada environment staging, test dulu di sana

---

## 🔧 Cara Mengelola Update (Untuk Developer/Admin)

Jika Anda ingin menambahkan update baru untuk dirilis ke user:

### Langkah 1: Siapkan File Update
1. Buat file ZIP berisi file yang diupdate
2. Struktur ZIP harus sesuai struktur aplikasi:
   ```
   update_1.0.1.zip
   ├── app/
   │   └── Http/Controllers/
   │       └── (file yang diupdate)
   ├── database/migrations/
   │   └── (migration baru jika ada)
   ├── resources/views/
   │   └── (view yang diupdate)
   └── routes/
       └── (route yang diupdate)
   ```

### Langkah 2: Upload File
1. Upload file ZIP ke server (misal: `public/updates/`)
2. Atau upload ke cloud storage (Google Drive, Dropbox, dll)
3. Dapatkan URL file yang bisa diakses

### Langkah 3: Tambahkan Data Update
1. Akses: `/admin/update`
2. Klik **"Tambah Update"**
3. Isi form:
   - **Versi:** Contoh: `1.0.1`
   - **Judul:** Contoh: `Update Minor - Perbaikan Bug`
   - **Deskripsi:** Penjelasan singkat update
   - **Changelog:** Daftar perubahan (gunakan format list)
   - **File URL:** URL file ZIP yang sudah diupload
   - **Ukuran File:** Ukuran file dalam bytes
   - **Checksum:** MD5 checksum file (opsional, untuk keamanan)
   - **Tipe Update:** Major atau Minor
   - **Status:** Aktif atau Nonaktif
   - **Tanggal Rilis:** Tanggal rilis update
4. Klik **"Simpan"**

### Langkah 4: Aktifkan Update
1. Set **Status** menjadi **"Aktif"**
2. Update akan muncul di halaman `/update` untuk user
3. User bisa melakukan update

---

## 📊 Melihat Riwayat Update

### Via Web Interface
1. Akses: `/update`
2. Scroll ke bagian **"Riwayat Update"**
3. Anda akan melihat:
   - Versi yang diinstall
   - Status (success/failed)
   - User yang melakukan update
   - Waktu update
   - Pesan/error (jika ada)

### Via Detail Log
1. Klik pada salah satu riwayat update
2. Atau akses: `/update/log/{id}`
3. Anda akan melihat detail lengkap:
   - Status setiap tahap
   - Error log (jika ada)
   - Waktu mulai dan selesai

---

## ⚠️ Troubleshooting

### Update Gagal?

**1. Cek Log Error**
- Akses: `/update/log/{id}` untuk melihat detail error
- Cek file: `storage/logs/laravel.log`

**2. Cek Permissions**
- Pastikan folder `storage/app/updates` bisa ditulis
- Pastikan folder `storage/app/backups` bisa ditulis
- Pastikan folder aplikasi (`app/`, `resources/`, dll) bisa ditulis

**3. Cek Database Backup**
- Backup otomatis ada di: `storage/app/backups/`
- Jika update gagal, bisa restore dari backup

**4. Cek File Update**
- Pastikan URL file bisa diakses
- Pastikan file ZIP tidak corrupt
- Pastikan checksum sesuai (jika ada)

### File Tidak Terupdate?

**1. Cek Struktur ZIP**
- Pastikan struktur folder di ZIP sesuai dengan aplikasi
- Pastikan path file benar

**2. Cek Permission**
- Pastikan folder aplikasi bisa ditulis
- Cek permission folder `app/`, `resources/`, dll

### Migration Error?

**1. Cek Migration File**
- Pastikan file migration ada di ZIP
- Pastikan nama migration benar

**2. Cek Database**
- Pastikan koneksi database normal
- Pastikan user database punya permission untuk migrate

---

## 🔄 Rollback (Kembali ke Versi Sebelumnya)

Jika update menyebabkan masalah:

### Opsi 1: Restore Database
1. Cari backup database di: `storage/app/backups/`
2. Restore database dari backup
3. File aplikasi tetap versi baru, tapi data kembali ke sebelum update

### Opsi 2: Manual Rollback
1. Restore file dari backup (jika ada)
2. Restore database dari backup
3. Update file `VERSION` ke versi sebelumnya

---

## 📝 Best Practices

### Untuk Pemilik Aplikasi:
1. **Selalu Backup Sebelum Update**
   - Meskipun sistem otomatis backup, buat backup manual juga

2. **Baca Changelog**
   - Pahami perubahan yang ada sebelum update

3. **Test Setelah Update**
   - Pastikan semua fitur berjalan normal
   - Test fitur-fitur penting

4. **Update di Waktu Sepi**
   - Jika memungkinkan, lakukan update di waktu sepi
   - Beri tahu user jika update akan mempengaruhi mereka

5. **Monitor Setelah Update**
   - Pantau aplikasi setelah update
   - Cek log jika ada masalah

### Untuk Developer/Admin:
1. **Versioning**
   - Gunakan Semantic Versioning (1.0.0, 1.0.1, 1.1.0, 2.0.0)
   - Major (2.0.0): Breaking changes
   - Minor (1.1.0): Fitur baru, backward compatible
   - Patch (1.0.1): Bug fix

2. **File Update**
   - Hanya include file yang berubah
   - Jangan include `.env`, `vendor/`, `node_modules/`
   - Test ZIP sebelum upload

3. **Changelog**
   - Tulis changelog yang jelas
   - Jelaskan fitur baru, bug fix, breaking changes

4. **Testing**
   - Test update di development dulu
   - Test di staging sebelum release ke production

---

## 📞 Bantuan

Jika mengalami masalah:

1. **Cek Dokumentasi:**
   - `PANDUAN_UPDATE.md` - Panduan lengkap setup
   - `ALUR_UPDATE.md` - Alur update detail
   - `API_UPDATE_DOCUMENTATION.md` - Dokumentasi API

2. **Cek Log:**
   - `storage/logs/laravel.log` - Log aplikasi
   - `/update/log/{id}` - Log update spesifik

3. **Hubungi Developer:**
   - Jika masalah tidak bisa diselesaikan sendiri
   - Sertakan log error dan detail masalah

---

## 🎯 Quick Reference

### URL Penting:
- **Halaman Update:** `/update`
- **Manage Update:** `/admin/update`
- **Riwayat Update:** `/update/history`
- **Detail Log:** `/update/log/{id}`

### File Penting:
- **Versi Aplikasi:** `VERSION` (di root project)
- **Backup Database:** `storage/app/backups/`
- **File Update:** `storage/app/updates/`
- **Log Aplikasi:** `storage/logs/laravel.log`

### Command Penting (Jika Akses SSH):
```bash
# Cek versi saat ini
cat VERSION

# Cek log update
tail -f storage/logs/laravel.log

# Clear cache manual
php artisan optimize:clear
```

---

## ✅ Checklist Update

### Sebelum Update:
- [ ] Backup database manual (opsional)
- [ ] Baca changelog
- [ ] Pilih waktu yang tepat
- [ ] Notifikasi user (jika perlu)

### Saat Update:
- [ ] Login sebagai Super Admin
- [ ] Akses halaman `/update`
- [ ] Klik "Cek Update"
- [ ] Review informasi update
- [ ] Klik "Update Sekarang"
- [ ] Tunggu proses selesai

### Setelah Update:
- [ ] Verifikasi versi baru
- [ ] Test fitur-fitur penting
- [ ] Cek log jika ada error
- [ ] Monitor aplikasi

---

**Selamat Update! 🎉**

Jika ada pertanyaan atau masalah, jangan ragu untuk menghubungi tim developer.

