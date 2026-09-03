# 🔧 Fix Update Stuck / Loading Terus

Jika aplikasi loading terus setelah proses update terhenti, ikuti langkah-langkah berikut:

## 🚨 Langkah Cepat (Emergency Fix)

### 1. Clear Cache
```bash
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 2. Cek Status Update Terakhir
```bash
php artisan tinker
```

```php
use App\Models\UpdateLog;

// Cek update log terakhir
$lastLog = UpdateLog::latest()->first();
echo "Status: " . $lastLog->status . "\n";
echo "Version: " . $lastLog->version . "\n";
echo "Message: " . $lastLog->message . "\n";
```

### 3. Cleanup File Temporary
```bash
# Hapus file extract yang mungkin stuck
rm -rf storage/app/updates/extract_*

# Hapus file ZIP yang mungkin corrupt
rm -f storage/app/updates/update_*.zip
```

### 4. Rollback Versi (Jika Perlu)
```bash
php artisan tinker
```

```php
// Cek versi saat ini
$version = file_get_contents(base_path('VERSION'));
echo "Current version: " . $version . "\n";

// Rollback ke versi sebelumnya (ganti dengan versi yang benar)
$previousVersion = '1.0.0'; // Ganti dengan versi sebelumnya
file_put_contents(base_path('VERSION'), $previousVersion);
```

### 5. Restart Web Server
```bash
# Apache
sudo service apache2 restart

# Nginx + PHP-FPM
sudo service nginx restart
sudo service php8.1-fpm restart
```

## 🔍 Diagnosa Masalah

### Cek Log Update
```bash
tail -f storage/logs/laravel.log
```

### Cek Database
```sql
SELECT * FROM update_logs ORDER BY created_at DESC LIMIT 5;
```

### Cek File System
```bash
# Cek apakah ada file yang locked
ls -la storage/app/updates/

# Cek permission
chmod -R 755 storage/
```

## 🛠️ Solusi Lengkap

### Opsi 1: Mark Update sebagai Failed
```bash
php artisan tinker
```

```php
use App\Models\UpdateLog;

$lastLog = UpdateLog::where('status', 'installing')
    ->orWhere('status', 'downloading')
    ->latest()
    ->first();

if ($lastLog) {
    $lastLog->update([
        'status' => 'failed',
        'message' => 'Update terhenti karena refresh',
        'completed_at' => now(),
    ]);
    echo "Update log marked as failed\n";
}
```

### Opsi 2: Restore dari Backup
```bash
# Cek backup terakhir
ls -lh storage/app/backups/

# Restore database (ganti dengan nama file backup yang benar)
mysql -u username -p database_name < storage/app/backups/backup_YYYY-MM-DD_HHMMSS_version.sql
```

### Opsi 3: Manual Cleanup
```bash
# 1. Hapus semua file temporary update
rm -rf storage/app/updates/*

# 2. Clear semua cache
php artisan optimize:clear

# 3. Restart services
sudo service apache2 restart
# atau
sudo service nginx restart
```

## ✅ Checklist Recovery

- [ ] Clear semua cache
- [ ] Cek status update log
- [ ] Hapus file temporary
- [ ] Mark update sebagai failed (jika perlu)
- [ ] Restore database dari backup (jika perlu)
- [ ] Restart web server
- [ ] Test aplikasi

## 🚀 Prevent Future Issues

1. **Jangan refresh saat update berjalan**
2. **Backup database sebelum update**
3. **Monitor log saat update**
4. **Gunakan timeout yang cukup**

---

**Jika masih stuck, coba akses aplikasi via command line untuk bypass web server.**




