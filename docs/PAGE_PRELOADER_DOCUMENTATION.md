# Page Loading Preloader Documentation

## 📋 Overview

Preloader/Loading animation telah ditambahkan ke layout mobile untuk memberikan feedback visual kepada user saat halaman sedang loading atau navigasi.

**File yang dimodifikasi:**

- `resources/views/layouts/mobile/app.blade.php` (CSS & HTML)
- `resources/views/layouts/mobile/script.blade.php` (JavaScript)

---

## 🎯 Features

### Automatic Preloader Activation

Preloader akan otomatis muncul ketika:

1. **Navigasi Link** - User mengklik link internal
2. **Form Submission** - User submit form
3. **AJAX Requests** - Ajax request dimulai (otomatis jika jQuery digunakan)

### Auto-Hide Functionality

- Preloader otomatis hilang setelah halaman selesai loading (DOMContentLoaded)
- Minimum display time: 300ms (untuk UX yang lebih baik)
- Maximum display time: 10 seconds (failsafe)

### Multiple Spinner Styles

Available spinner designs:

- **Circular** (Default) - Modern rotating circle
- **Dots** - Bouncing dots animation
- **Pulse** - Pulsing circle
- **Bars** - Rotating bars loader

---

## 💻 Usage

### Default Behavior (Automatic)

Tidak perlu setup apapun, preloader akan otomatis bekerja untuk semua link dan form pada halaman.

```html
<!-- Preloader akan muncul saat link diklik -->
<a href="/dashboard">Dashboard</a>

<!-- Preloader akan muncul saat form disubmit -->
<form action="/save" method="POST">
    <button type="submit">Save</button>
</form>
```

### Disable Preloader (Jika Diperlukan)

Tambahkan attribute `data-no-preloader` untuk menonaktifkan preloader pada link atau form tertentu:

```html
<!-- Preloader tidak akan muncul untuk link ini -->
<a href="#section" data-no-preloader>Link Anchor</a>

<!-- Preloader tidak akan muncul untuk form ini -->
<form action="/search" method="GET" data-no-preloader>
    <button type="submit">Search</button>
</form>
```

### Manual Control

Gunakan global `Preloader` object untuk kontrol manual:

```javascript
// Tampilkan preloader
Preloader.show();

// Sembunyikan preloader
Preloader.hide();
```

---

## 🎨 Customization

### Mengubah Spinner Style

Edit file `resources/views/layouts/mobile/app.blade.php` - section Preloader Component:

**Current (Circular):**

```html
<div class="spinner-circular"></div>
```

**Ganti dengan Dots:**

```html
<div class="spinner-dots">
    <div class="dot"></div>
    <div class="dot"></div>
    <div class="dot"></div>
</div>
```

**Ganti dengan Pulse:**

```html
<div class="spinner-pulse"></div>
```

**Ganti dengan Bars:**

```html
<div class="spinner-bars">
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
</div>
```

### Mengubah Text

Edit text di dalam preloader container:

```html
<div class="preloader-text animated">Sedang Memproses</div>
<div class="preloader-subtitle">Harap tunggu sebentar...</div>
```

### Mengubah Warna

Preloader menggunakan theme color `var(--color-nav)` yang sudah di-set di root CSS. Tidak perlu custom color karena akan mengikuti theme yang dipilih.

### Mengubah Duration

Edit nilai di JavaScript:

```javascript
// Minimum display time (ms)
minDuration: 300,

// Maximum auto-hide time (ms)
autoHideDelay: 10000,
```

---

## 🔧 CSS Classes

### Main Classes

| Class                       | Purpose                  |
| --------------------------- | ------------------------ |
| `.preloader-overlay`        | Main container backdrop  |
| `.preloader-overlay.active` | Visible state            |
| `.preloader-container`      | Content wrapper          |
| `.spinner-*`                | Different spinner styles |
| `.preloader-text`           | Loading text             |
| `.preloader-subtitle`       | Subtitle text            |

---

## 📱 Responsive Design

Preloader responsif untuk semua ukuran layar:

- **Desktop/Tablet**: Normal size (60px spinner)
- **Mobile (<576px)**: Optimized size (50px spinner)

---

## 🎞️ Animation Details

### Spinner Animations

- **Circular**: 1.2s rotation loop
- **Dots**: 1.4s bounce loop with stagger
- **Pulse**: 2s scale animation
- **Bars**: 1.2s opacity animation

### Container Animation

- **Entry**: 0.4s slide-in from bottom
- **Backdrop**: Blur effect (2px)

### Text Animation

- **Dots animation**: "Memuat", "Memuat.", "Memuat..", "Memuat..."

---

## ⚙️ JavaScript API

### PreloaderManager Object

```javascript
// Initialize
PreloaderManager.init();

// Show preloader
PreloaderManager.show();

// Hide preloader
PreloaderManager.hide();

// Schedule hide with minimum duration
PreloaderManager.scheduleHide();
```

### Properties

```javascript
PreloaderManager.overlay; // Reference to overlay element
PreloaderManager.timeout; // Current timeout ID
PreloaderManager.minDuration; // Minimum show time (300ms)
PreloaderManager.autoHideDelay; // Max show time (10s)
```

---

## 🐛 Troubleshooting

### Preloader tidak muncul

- Pastikan JavaScript aktif di browser
- Periksa browser console untuk errors
- Verifikasi element `#preloaderOverlay` ada di DOM

### Preloader stuck

- Preloader akan auto-hide setelah 10 detik
- Manual hide: `Preloader.hide()`
- Check network requests di DevTools

### Preloader muncul di AJAX

- Ini adalah behavior yang diinginkan
- Untuk disable: `$('form').attr('data-no-preloader', true)`

---

## 📊 Performance Impact

- **CSS**: Minimal (~8KB minified)
- **JavaScript**: Minimal (~3KB minified)
- **Animation Performance**: GPU-accelerated (transform & opacity only)
- **No external dependencies** (selain jQuery untuk AJAX detection)

---

## 🔄 Browser Support

| Browser | Support                              |
| ------- | ------------------------------------ |
| Chrome  | ✅ Full                              |
| Firefox | ✅ Full                              |
| Safari  | ✅ Full                              |
| Edge    | ✅ Full                              |
| IE 11   | ⚠️ Partial (no backdrop-filter blur) |

---

## 📝 Examples

### Example 1: Show preloader on button click

```html
<button onclick="Preloader.show()">Start Process</button>
```

### Example 2: Disable preloader for external links

```html
<a href="https://example.com" data-no-preloader target="_blank">
    External Site
</a>
```

### Example 3: Manual control for AJAX

```javascript
$.ajax({
    url: "/api/data",
    beforeSend: function () {
        Preloader.show();
    },
    complete: function () {
        Preloader.hide();
    },
});
```

### Example 4: Change spinner dynamically

```javascript
// Update spinner element
document.querySelector(".spinner-circular").style.display = "none";
document.querySelector(".spinner-pulse").style.display = "block";
```

---

## 🎯 Best Practices

1. **Keep text short** - Lebih baik 1-2 kata saja
2. **Use consistent messaging** - Jangan terlalu banyak varian text
3. **Mind the timing** - Jangan terlalu lama (~2-3 detik ideal)
4. **Test on slow connections** - Verify behavior dengan network throttle
5. **Disable when not needed** - Gunakan `data-no-preloader` untuk non-navigation links

---

**Last Updated:** February 22, 2026
**Status:** Production Ready ✅
