# Page Preloader - Advanced Examples

## 🎓 Advanced Usage Examples

### 1. Disable Preloader for Anchor Links

```html
<!-- These won't trigger preloader -->
<a href="#top" data-no-preloader>Go to Top</a>
<a href="#section-2" data-no-preloader>Section 2</a>

<!-- These will trigger preloader -->
<a href="/dashboard">Dashboard</a>
<a href="/profile">Profile</a>
```

---

### 2. Disable Preloader for External Links

```html
<!-- External link - disable preloader -->
<a href="https://google.com" data-no-preloader target="_blank"> Google </a>

<!-- Internal link - enable preloader -->
<a href="/about">About Us</a>
```

---

### 3. Custom AJAX with Preloader

```javascript
// Method 1: Manual control
$.ajax({
    url: "/api/users",
    type: "GET",
    beforeSend: function () {
        Preloader.show();
    },
    success: function (data) {
        console.log(data);
    },
    error: function (error) {
        console.error(error);
    },
    complete: function () {
        Preloader.hide();
    },
});

// Method 2: Automatic (jQuery handles it)
// Preloader shows/hides automatically on AJAX requests
$.get("/api/users", function (data) {
    console.log(data);
});
```

---

### 4. Show Preloader for Time-Consuming Operations

```javascript
async function processData() {
    Preloader.show();

    try {
        // Simulate long operation
        await new Promise((resolve) => setTimeout(resolve, 2000));

        console.log("Data processed");

        // Optionally delay hide
        setTimeout(() => {
            Preloader.hide();
        }, 500);
    } catch (error) {
        console.error(error);
        Preloader.hide();
    }
}

// Trigger
document.querySelector("#processBtn").addEventListener("click", processData);
```

---

### 5. Preloader with Progress Updates

```javascript
async function uploadFile(file) {
    Preloader.show();

    const xhr = new XMLHttpRequest();
    const formData = new FormData();
    formData.append("file", file);

    xhr.upload.addEventListener("progress", (e) => {
        if (e.lengthComputable) {
            const progress = (e.loaded / e.total) * 100;
            console.log(`Upload ${progress}% complete`);

            // Update preloader text if needed
            document.querySelector(".preloader-text").textContent =
                `Uploading ${Math.round(progress)}%`;
        }
    });

    xhr.addEventListener("load", () => {
        Preloader.hide();
    });

    xhr.open("POST", "/upload");
    xhr.send(formData);
}
```

---

### 6. Conditional Preloader (Based on Page Size)

```javascript
// Only show preloader if file size is large
document.querySelectorAll("a[href]").forEach((link) => {
    link.addEventListener("click", (e) => {
        const href = link.getAttribute("href");
        const isHeavyPage =
            href.includes("/reports") || href.includes("/analytics");

        if (isHeavyPage) {
            Preloader.show();
        }
    });
});
```

---

### 7. Preloader with Custom Timeout

```javascript
function showPreloaderWithTimeout(duration = 3000) {
    Preloader.show();

    setTimeout(() => {
        Preloader.hide();
    }, duration);
}

// Usage
document.querySelector("#saveBtn").addEventListener("click", () => {
    showPreloaderWithTimeout(2000); // Show for 2 seconds
});
```

---

### 8. Disable Preloader for All Links on Page

```javascript
// Add data-no-preloader to all links on page
document.querySelectorAll("a[href]").forEach((link) => {
    link.setAttribute("data-no-preloader", "");
});

// Then manually control specific operations
```

---

### 9. Custom Preloader Text Based on Action

```javascript
function showCustomPreloader(message) {
    Preloader.show();
    document.querySelector(".preloader-text").textContent = message;
    document.querySelector(".preloader-subtitle").textContent =
        "Mohon tunggu...";
}

// Usage
document.querySelector("#deleteBtn").addEventListener("click", () => {
    showCustomPreloader("Menghapus data");
});

document.querySelector("#downloadBtn").addEventListener("click", () => {
    showCustomPreloader("Mengunduh file");
});

document.querySelector("#searchBtn").addEventListener("click", () => {
    showCustomPreloader("Mencari data");
});
```

---

### 10. Preloader with Form Validation

```javascript
document.querySelector("form").addEventListener("submit", (e) => {
    // Manual validation
    const email = document.querySelector('input[name="email"]');

    if (!email.value.includes("@")) {
        e.preventDefault();
        alert("Invalid email");
        return;
    }

    // Validation passed, preloader will show automatically
});
```

---

### 11. Chain Multiple Requests with Preloader

```javascript
async function chainRequests() {
    Preloader.show();

    try {
        // Request 1
        const res1 = await fetch("/api/step1");
        const data1 = await res1.json();
        console.log("Step 1 complete");

        // Request 2
        const res2 = await fetch(`/api/step2/${data1.id}`);
        const data2 = await res2.json();
        console.log("Step 2 complete");

        // Request 3
        const res3 = await fetch(`/api/step3/${data2.id}`);
        const data3 = await res3.json();
        console.log("All steps complete");
    } catch (error) {
        console.error(error);
    } finally {
        Preloader.hide();
    }
}
```

---

### 12. Toggle Different Spinner Styles

```javascript
// Change spinner style dynamically
function changeSpinner(style) {
    const spinners = document.querySelectorAll('[class^="spinner-"]');
    spinners.forEach((spinner) => {
        spinner.style.display = "none";
    });

    const selectedSpinner = document.querySelector(`.spinner-${style}`);
    if (selectedSpinner) {
        selectedSpinner.style.display = "block";
    }
}

// Usage
changeSpinner("dots"); // Show dots spinner
changeSpinner("pulse"); // Show pulse spinner
changeSpinner("bars"); // Show bars spinner
changeSpinner("circular"); // Show circular spinner (default)
```

---

### 13. Preloader with Retry Logic

```javascript
async function fetchWithRetry(url, maxRetries = 3) {
    for (let i = 0; i < maxRetries; i++) {
        Preloader.show();

        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error("Failed");

            const data = await response.json();
            Preloader.hide();
            return data;
        } catch (error) {
            console.error(`Attempt ${i + 1} failed:`, error);

            if (i < maxRetries - 1) {
                // Retry after delay
                await new Promise((resolve) => setTimeout(resolve, 1000));
            } else {
                Preloader.hide();
                throw error;
            }
        }
    }
}

// Usage
fetchWithRetry("/api/data")
    .then((data) => console.log(data))
    .catch((error) => console.error("Failed after retries:", error));
```

---

### 14. Preloader with Error Handling

```javascript
document.querySelector("#processBtn").addEventListener("click", async () => {
    Preloader.show();

    try {
        const response = await fetch("/api/process", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ data: "test" }),
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const result = await response.json();
        console.log("Success:", result);
    } catch (error) {
        console.error("Error:", error);

        // Show error message
        alert(`Error: ${error.message}`);
    } finally {
        Preloader.hide();
    }
});
```

---

### 15. Accessibility - Announcement for Screen Readers

```javascript
// Add ARIA label for screen readers
const overlay = document.getElementById("preloaderOverlay");
overlay.setAttribute("role", "status");
overlay.setAttribute("aria-live", "polite");
overlay.setAttribute("aria-label", "Halaman sedang dimuat");

// Update announcement when preloader shows
const originalShow = PreloaderManager.show.bind(PreloaderManager);
PreloaderManager.show = function () {
    originalShow();
    overlay.setAttribute("aria-busy", "true");
};

// Update announcement when preloader hides
const originalHide = PreloaderManager.hide.bind(PreloaderManager);
PreloaderManager.hide = function () {
    originalHide();
    overlay.setAttribute("aria-busy", "false");
};
```

---

## 🎯 Tips & Best Practices

### ✅ DO

- Keep loading messages short and clear
- Use consistent messaging across the app
- Test on slow network connections
- Hide preloader in error cases
- Use for long-running operations

### ❌ DON'T

- Show preloader for every interaction
- Use for very fast operations (<500ms)
- Block user interaction with excessive preloader time
- Show unclear messages like "Loading..."
- Forget to hide preloader in error scenarios

---

## 🔗 Related Files

- Documentation: `docs/PAGE_PRELOADER_DOCUMENTATION.md`
- Implementation: `resources/views/layouts/mobile/app.blade.php`
- Script: `resources/views/layouts/mobile/script.blade.php`

---

**Last Updated:** February 22, 2026
