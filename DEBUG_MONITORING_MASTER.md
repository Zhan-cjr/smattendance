# Debug Monitoring Master

## Langkah-langkah Debug

### 1. Buka Browser Console
Tekan `F12` untuk membuka Developer Tools, lalu pilih tab `Console`

### 2. Test Fitur Pencarian
- Buka modal "Tambah Karyawan"
- Ketik minimal 2 karakter di input search
- Lihat console untuk log:
  - "Search input changed: [searchTerm]"
  - "Fetching employees with search term: [searchTerm]"
  - "Fetch URL: [actual-url]"
  - "Fetch response received: 200"
  - "Response data: [data-structure]"

Jika ada error, akan terlihat di console dengan:
- "Fetch error: [error-message]"

### 3. Test Tombol Tambah
- Klik tombol "Tambah" di hasil pencarian
- Lihat console untuk log:
  - "addEmployee called with nik: [nik] nama: [nama]"
  - "Posting to URL: [url]"
  - "Add response status: 200"
  - "Add response data: {status: true, message: ...}"

### 4. Test Tombol X (Delete)
- Di tabel karyawan yang dipantau, klik tombol X (hapus)
- Lihat console untuk log:
  - "removeEmployee called with nik: [nik] nama: [nama]"
  - "Deleting from URL: [url]"
  - "Delete response status: 200"
  - "Delete response data: {status: true, message: ...}"

## Informasi yang Diperlukan jika Ada Masalah

Bila masih ada masalah, berikan info:
1. Screenshot console log
2. Network tab di Developer Tools (Tab "Network")
3. Response dari server (klik request di Network tab)
4. User yang sedang login
5. Pesan error yang muncul
