# 📡 API Documentation - Update System

## Base URL
```
http://your-domain.com/api/update
```

## Authentication
Beberapa endpoint memerlukan authentication menggunakan Laravel Sanctum. Kirim token di header:
```
Authorization: Bearer {your-token}
```

---

## Endpoints

### 1. Check Update
Cek apakah ada update tersedia.

**Endpoint:** `GET /api/update/check`

**Authentication:** Tidak diperlukan

**Query Parameters:**
- `update_server_url` (optional): URL server update eksternal

**Response Success (200):**
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
            "file_url": "https://domain.com/updates/update_1.0.1.zip",
            "file_size": "5242880",
            "is_major": false,
            "released_at": "2024-01-15T10:00:00.000000Z"
        }
    }
}
```

**Response No Update (200):**
```json
{
    "success": true,
    "data": {
        "has_update": false,
        "current_version": "1.0.1",
        "latest_version": "1.0.1"
    }
}
```

---

### 2. Get Current Version
Mendapatkan versi aplikasi saat ini.

**Endpoint:** `GET /api/update/version`

**Authentication:** Tidak diperlukan

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "version": "1.0.0"
    }
}
```

---

### 3. List Available Updates
Mendapatkan daftar semua update yang tersedia.

**Endpoint:** `GET /api/update/list`

**Query Parameters:**
- `active` (boolean, optional): Hanya update aktif (default: true)
- `major` (boolean, optional): Hanya update major

**Response Success (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "version": "1.0.1",
            "title": "Update Minor",
            "description": "Deskripsi update",
            "is_major": false,
            "is_active": true,
            "released_at": "2024-01-15T10:00:00.000000Z"
        }
    ],
    "count": 1
}
```

---

### 4. Get Update Detail
Mendapatkan detail update berdasarkan versi.

**Endpoint:** `GET /api/update/{version}`

**Authentication:** Tidak diperlukan

**Parameters:**
- `version` (string): Versi update (contoh: 1.0.1)

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "version": "1.0.1",
        "title": "Update Minor",
        "description": "Deskripsi update",
        "changelog": "- Fix bug\n- New feature",
        "file_url": "https://domain.com/updates/update_1.0.1.zip",
        "file_size": "5242880",
        "checksum": "a1b2c3d4e5f6...",
        "is_major": false,
        "is_active": true,
        "migrations": ["2024_01_01_new_migration.php"],
        "seeders": ["NewSeeder"],
        "released_at": "2024-01-15T10:00:00.000000Z"
    }
}
```

**Response Error (404):**
```json
{
    "success": false,
    "message": "Update tidak ditemukan"
}
```

---

### 5. Download Update
Download file update (tidak menginstall).

**Endpoint:** `POST /api/update/{version}/download`

**Authentication:** Disarankan (Sanctum)

**Parameters:**
- `version` (string): Versi update

**Body (optional):**
```json
{
    "user_id": 1
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "File berhasil diunduh",
    "data": {
        "update_log_id": 1,
        "version": "1.0.1",
        "status": "downloading"
    }
}
```

---

### 6. Install Update
Install update yang sudah didownload.

**Endpoint:** `POST /api/update/{version}/install`

**Authentication:** Disarankan (Sanctum)

**Parameters:**
- `version` (string): Versi update

**Body (optional):**
```json
{
    "user_id": 1
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Update berhasil diinstall",
    "data": {
        "update_log": {
            "id": 1,
            "version": "1.0.1",
            "status": "success",
            "previous_version": "1.0.0",
            "completed_at": "2024-01-15T10:30:00.000000Z"
        },
        "current_version": "1.0.1"
    }
}
```

---

### 7. Update Now (Download + Install)
Download dan install update langsung.

**Endpoint:** `POST /api/update/{version}/update-now`

**Authentication:** Disarankan (Sanctum)

**Parameters:**
- `version` (string): Versi update

**Body (optional):**
```json
{
    "user_id": 1
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Update berhasil diinstall",
    "data": {
        "update_log": {
            "id": 1,
            "version": "1.0.1",
            "status": "success",
            "previous_version": "1.0.0"
        },
        "current_version": "1.0.1",
        "previous_version": "1.0.0"
    }
}
```

---

### 8. Get Update History
Mendapatkan riwayat semua update yang pernah dilakukan.

**Endpoint:** `GET /api/update/history`

**Authentication:** Disarankan (Sanctum)

**Query Parameters:**
- `page` (integer, optional): Halaman (default: 1)
- `per_page` (integer, optional): Item per halaman (default: 10)

**Response Success (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "version": "1.0.1",
            "previous_version": "1.0.0",
            "status": "success",
            "message": "Update berhasil diinstall",
            "user": {
                "id": 1,
                "name": "Admin"
            },
            "started_at": "2024-01-15T10:00:00.000000Z",
            "completed_at": "2024-01-15T10:30:00.000000Z",
            "created_at": "2024-01-15T10:00:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1
    }
}
```

---

### 9. Get Update Log Detail
Mendapatkan detail log update tertentu.

**Endpoint:** `GET /api/update/log/{id}`

**Authentication:** Disarankan (Sanctum)

**Parameters:**
- `id` (integer): ID log update

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "version": "1.0.1",
        "previous_version": "1.0.0",
        "status": "success",
        "message": "Update berhasil diinstall",
        "error_log": null,
        "user": {
            "id": 1,
            "name": "Admin"
        },
        "started_at": "2024-01-15T10:00:00.000000Z",
        "completed_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

---

### 10. Get Update Status/Progress
Mendapatkan status dan progress update yang sedang berjalan.

**Endpoint:** `GET /api/update/status/{logId}`

**Authentication:** Disarankan (Sanctum)

**Parameters:**
- `logId` (integer): ID log update

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "version": "1.0.1",
        "status": "installing",
        "message": "Sedang menginstall update...",
        "started_at": "2024-01-15T10:00:00.000000Z",
        "completed_at": null,
        "progress": 75
    }
}
```

**Progress Values:**
- `0`: Pending
- `25`: Downloading
- `75`: Installing
- `100`: Success/Failed

---

## Error Responses

Semua endpoint mengembalikan error dalam format berikut:

```json
{
    "success": false,
    "message": "Pesan error",
    "error": "Detail error (optional)"
}
```

**HTTP Status Codes:**
- `200`: Success
- `400`: Bad Request
- `401`: Unauthorized
- `404`: Not Found
- `500`: Internal Server Error

---

## Contoh Penggunaan

### JavaScript (Fetch API)

```javascript
// Check update
fetch('http://your-domain.com/api/update/check')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.has_update) {
            console.log('Update tersedia:', data.data.latest_version);
        }
    });

// Update sekarang
fetch('http://your-domain.com/api/update/1.0.1/update-now', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Update berhasil!');
    }
});
```

### cURL

```bash
# Check update
curl -X GET http://your-domain.com/api/update/check

# Update sekarang
curl -X POST http://your-domain.com/api/update/1.0.1/update-now \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client();

// Check update
$response = $client->get('http://your-domain.com/api/update/check');
$data = json_decode($response->getBody(), true);

if ($data['success'] && $data['data']['has_update']) {
    // Update tersedia
}

// Update sekarang
$response = $client->post('http://your-domain.com/api/update/1.0.1/update-now', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
    ]
]);
```

---

## Rate Limiting

API menggunakan rate limiting default Laravel:
- **60 requests per minute** per IP/user

---

## Notes

1. **Authentication**: Endpoint yang memerlukan authentication akan mengembalikan `401 Unauthorized` jika token tidak valid.

2. **File Size**: Ukuran file dalam bytes. Untuk konversi:
   - KB: `file_size / 1024`
   - MB: `file_size / (1024 * 1024)`

3. **Version Format**: Gunakan Semantic Versioning (1.0.0, 1.0.1, 1.1.0, 2.0.0)

4. **Status Values**:
   - `pending`: Menunggu
   - `downloading`: Sedang mengunduh
   - `installing`: Sedang menginstall
   - `success`: Berhasil
   - `failed`: Gagal

---

## Support

Untuk pertanyaan atau masalah, hubungi administrator sistem.











