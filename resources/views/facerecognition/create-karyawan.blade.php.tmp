<script>
    // Fungsi untuk memuat face-api.js
    function loadFaceApiScript() {
        return new Promise((resolve, reject) => {
            if (typeof faceapi !== 'undefined') {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Fungsi untuk memuat model face-api
    async function loadFaceApiModels() {
        try {
            await Promise.all([
                faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/models')
            ]);
            console.log('Face API models loaded successfully');
            return true;
        } catch (error) {
            console.error('Error loading face-api models:', error);
            return false;
        }
    }

    // Fungsi untuk memulai video
    function startVideo() {
        Webcam.set({
            width: 1280,
            height: 720,
            image_format: 'jpeg',
            jpeg_quality: 95,
            fps: 30,
            constraints: {
                video: {
                    facingMode: "user",
                    width: {
                        ideal: 1280,
                        min: 640
                    },
                    height: {
                        ideal: 720,
                        min: 480
                    },
                    frameRate: {
                        ideal: 30,
                        min: 24
                    }
                }
            }
        });

        Webcam.attach('.webcam-capture');

        // Update container dan info setelah video ready
        setTimeout(() => {
            const video = document.querySelector('.webcam-capture video');
            if (video && video.videoWidth > 0 && video.videoHeight > 0) {
                const actualWidth = video.videoWidth;
                const actualHeight = video.videoHeight;
                const actualAspectRatio = actualWidth / actualHeight;

        console.log('Webcam started successfully');
                console.log(`Camera native resolution: ${actualWidth}x${actualHeight}`);
                console.log(`Camera native aspect ratio: ${actualAspectRatio.toFixed(2)}:1`);

                // Simpan resolusi video untuk threshold dinamis
                videoWidth = actualWidth;
                videoHeight = actualHeight;

                // Hitung threshold berdasarkan resolusi
                const thresholds = getFaceSizeThresholds();
                console.log('Face size thresholds (dynamic):', thresholds);
                console.log(`Video resolution: ${actualWidth}x${actualHeight}`);
                console.log(`Min face width: ${thresholds.minWidth}px (${(thresholds.minWidth/actualWidth*100).toFixed(1)}%)`);
                console.log(`Max face width: ${thresholds.maxWidth}px (${(thresholds.maxWidth/actualWidth*100).toFixed(1)}%)`);

                // Update container untuk fullscreen sesuai aspect ratio natural
                const container = document.querySelector('.webcam-container');
                const webcamCapture = document.querySelector('.webcam-capture');
                if (container && webcamCapture) {
                    webcamCapture.style.aspectRatio = `${actualWidth} / ${actualHeight}`;

                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;
                    const viewportAspectRatio = viewportWidth / viewportHeight;

                    if (viewportAspectRatio > actualAspectRatio) {
                        container.style.height = '100vh';
                        container.style.width = `${100 * actualAspectRatio / viewportAspectRatio}vw`;
                        container.style.marginLeft = 'auto';
                        container.style.marginRight = 'auto';
                    } else {
                        container.style.width = '100vw';
                        container.style.height = `${100 * viewportAspectRatio / actualAspectRatio}vh`;
                        container.style.marginTop = 'auto';
                        container.style.marginBottom = 'auto';
                    }

                    console.log(`Container adjusted to fullscreen with camera aspect ratio: ${actualAspectRatio.toFixed(2)}:1`);
                }

                // Update info panel
                const infoPanel = document.getElementById('infoPanel');
                if (infoPanel) {
                    const resolutionItem = infoPanel.querySelector('.resolution-info');
                    const aspectRatioItem = infoPanel.querySelector('.aspect-ratio-info');

                    if (resolutionItem) {
                        resolutionItem.textContent = `${actualWidth}x${actualHeight}`;
                    }
                    if (aspectRatioItem) {
                        let ratioText = '';
                        if (Math.abs(actualAspectRatio - (16 / 9)) < 0.1) {
                            ratioText = '16:9';
                        } else if (Math.abs(actualAspectRatio - (4 / 3)) < 0.1) {
                            ratioText = '4:3';
                        } else if (Math.abs(actualAspectRatio - (3 / 2)) < 0.1) {
                            ratioText = '3:2';
                        } else {
                            ratioText = `${actualAspectRatio.toFixed(2)}:1`;
                        }
                        aspectRatioItem.innerHTML = `<i class="ti ti-aspect-ratio"></i><span>${ratioText}</span>`;
                    }
                }
            }
        }, 1000);
    }

    // ============================================
    // MULTI-CAPTURE LOGIC
    // ============================================
    const DIRECTIONS = [{
            key: 'front',
            label: 'Lurus ke depan',
            step: 2
        },
        {
            key: 'left',
            label: 'Tengok ke kiri',
            step: 3
        },
        {
            key: 'right',
            label: 'Tengok ke kanan',
            step: 4
        },
        {
            key: 'up',
            label: 'Tengok ke atas',
            step: 5
        },
        {
            key: 'down',
            label: 'Tengok ke bawah',
            step: 6
        }
    ];
    const IMAGES_PER_DIRECTION = 1;
    const TOTAL_IMAGES = 5;
    let capturedImages = [];
    let currentDirectionIndex = 0;
    let currentImageInDirection = 0;
    let isMultiCaptureActive = false;

    // Update step indicator
    function updateStepIndicator(step) {
        const stepItems = document.querySelectorAll('.step-item');
        stepItems.forEach((item, index) => {
            const itemStep = parseInt(item.dataset.step);
            item.classList.remove('active', 'completed');

            if (itemStep < step) {
                item.classList.add('completed');
            } else if (itemStep === step) {
                item.classList.add('active');
            }
        });
    }

    function showDirectionInstruction() {
        const guideText = document.getElementById('guideText');
        if (currentDirectionIndex < DIRECTIONS.length) {
            const direction = DIRECTIONS[currentDirectionIndex];
            updateStepIndicator(direction.step);

            if (guideText) {
                guideText.textContent = `📸 ${direction.label} (${currentImageInDirection + 1}/${IMAGES_PER_DIRECTION})`;
                guideText.classList.remove('ready');
            }
        } else {
            if (guideText) {
                guideText.textContent = '✅ Selesai! Menyimpan gambar...';
                guideText.classList.add('ready');
            }
        }
    }

    function startMultiCapture() {
        capturedImages = [];
        currentDirectionIndex = 0;
        currentImageInDirection = 0;
        isMultiCaptureActive = true;

        // Reset face descriptors untuk session baru
        previousFaceDescriptors = [];

        // Sembunyikan tombol start
        const btnStart = document.getElementById('btnMulaiRekam');
        if (btnStart) {
            btnStart.style.display = 'none';
        }

        // Update step indicator
        updateStepIndicator(1);
        showDirectionInstruction();
    }

    function stopMultiCapture() {
        isMultiCaptureActive = false;
        showDirectionInstruction();
    }

    // ============================================
    // QUALITY VALIDATION FUNCTIONS
    // ============================================

    /**
     * Deteksi blur menggunakan Laplacian variance (Optimized)
     * @param {ImageData} imageData - Image data dari canvas
     * @returns {number} - Blur score (semakin tinggi = semakin sharp)
     */
    function detectBlur(imageData) {
        const width = imageData.width;
        const height = imageData.height;
        const data = imageData.data;

        // Optimasi: Sample setiap 2 pixel untuk performa lebih cepat
        const step = 2;
        let laplacianSum = 0;
        let laplacianSquaredSum = 0;
        let pixelCount = 0;

        for (let y = 1; y < height - 1; y += step) {
            for (let x = 1; x < width - 1; x += step) {
                const idx = (y * width + x) * 4;

                // Ambil nilai grayscale
                const center = (data[idx] + data[idx + 1] + data[idx + 2]) / 3;
                const right = (data[idx + 4] + data[idx + 5] + data[idx + 6]) / 3;
                const left = (data[idx - 4] + data[idx - 3] + data[idx - 2]) / 3;
                const bottom = (data[idx + width * 4] + data[idx + width * 4 + 1] + data[idx + width * 4 + 2]) / 3;
                const top = (data[idx - width * 4] + data[idx - width * 4 + 1] + data[idx - width * 4 + 2]) / 3;

                // Hitung Laplacian (second derivative) - lebih akurat
                const laplacian = Math.abs(center * 4 - (left + right + top + bottom));

                laplacianSum += laplacian;
                laplacianSquaredSum += laplacian * laplacian;
                pixelCount++;
            }
        }

        if (pixelCount === 0) return 0;

        // Hitung variance dalam satu pass (Welford's algorithm simplified)
        const mean = laplacianSum / pixelCount;
        const meanSquared = laplacianSquaredSum / pixelCount;
        const variance = meanSquared - (mean * mean);

        return Math.max(0, variance); // Pastikan non-negative
    }

    /**
     * Validasi exposure (pencahayaan)
     * @param {ImageData} imageData - Image data dari canvas
     * @returns {Object} - {isValid: boolean, brightness: number, message: string}
     */
    function checkExposure(imageData) {
        const data = imageData.data;
        let totalBrightness = 0;
        let pixelCount = 0;

        // Hitung rata-rata brightness (gunakan grayscale)
        for (let i = 0; i < data.length; i += 4) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            const brightness = (r + g + b) / 3;
            totalBrightness += brightness;
            pixelCount++;
        }

        const avgBrightness = totalBrightness / pixelCount;

        // Threshold: 80-220 (0-255 scale)
        const minBrightness = 80;
        const maxBrightness = 220;

        if (avgBrightness < minBrightness) {
            return {
                isValid: false,
                brightness: avgBrightness,
                message: 'Pencahayaan terlalu gelap. Pastikan area terang atau gunakan lampu.'
            };
        } else if (avgBrightness > maxBrightness) {
            return {
                isValid: false,
                brightness: avgBrightness,
                message: 'Pencahayaan terlalu terang. Kurangi cahaya atau pindah ke area lebih gelap.'
            };
        }

        return {
            isValid: true,
            brightness: avgBrightness,
            message: 'Pencahayaan optimal'
        };
    }

    /**
     * Validasi sharpness menggunakan edge detection
     * @param {ImageData} imageData - Image data dari canvas
     * @param {Object} faceBox - Face detection box
     * @returns {Object} - {isValid: boolean, sharpness: number, message: string}
     */
    function checkSharpness(imageData, faceBox) {
        if (!faceBox) {
            return {
                isValid: false,
                sharpness: 0,
                message: 'Wajah tidak terdeteksi'
            };
        }

        // Fokus ke area wajah saja (lebih akurat)
        const width = imageData.width;
        const height = imageData.height;
        const data = imageData.data;

        // Crop area wajah dengan padding
        const padding = 20;
        const startX = Math.max(0, Math.floor(faceBox.x) - padding);
        const startY = Math.max(0, Math.floor(faceBox.y) - padding);
        const endX = Math.min(width, Math.floor(faceBox.x + faceBox.width) + padding);
        const endY = Math.min(height, Math.floor(faceBox.y + faceBox.height) + padding);

        let edgeSum = 0;
        let edgeCount = 0;

        // Hitung edge strength di area wajah menggunakan Sobel operator
        for (let y = startY + 1; y < endY - 1; y++) {
            for (let x = startX + 1; x < endX - 1; x++) {
                const idx = (y * width + x) * 4;

                // Grayscale
                const center = (data[idx] + data[idx + 1] + data[idx + 2]) / 3;
                const right = (data[idx + 4] + data[idx + 5] + data[idx + 6]) / 3;
                const bottom = (data[(y + 1) * width * 4 + x * 4] +
                    data[(y + 1) * width * 4 + x * 4 + 1] +
                    data[(y + 1) * width * 4 + x * 4 + 2]) / 3;

                // Sobel edge detection (simplified)
                const gx = right - center;
                const gy = bottom - center;
                const edge = Math.sqrt(gx * gx + gy * gy);

                edgeSum += edge;
                edgeCount++;
            }
        }

        const avgSharpness = edgeSum / edgeCount;
        // Threshold lebih fleksibel untuk webcam (diperlonggar dari 15 ke 10)
        const threshold = 10; // Threshold untuk sharpness

        if (avgSharpness < threshold) {
            return {
                isValid: false,
                sharpness: avgSharpness,
                message: 'Gambar tidak cukup tajam. Pastikan kamera stabil dan fokus ke wajah.'
            };
        }

        return {
            isValid: true,
            sharpness: avgSharpness,
            message: 'Ketajaman optimal'
        };
    }

    /**
     * Validasi kualitas gambar lengkap
     * @param {string} imageUri - Base64 image URI
     * @param {Object} faceBox - Face detection box (optional)
     * @returns {Promise<Object>} - {isValid: boolean, errors: Array, warnings: Array}
     */
    async function validateImageQuality(imageUri, faceBox = null) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);

                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

                const errors = [];
                const warnings = [];

                // 1. Deteksi Blur
                const blurScore = detectBlur(imageData);
                // Threshold lebih fleksibel untuk webcam (bisa adjust sesuai kebutuhan)
                // Untuk webcam: 50-70, untuk mobile: 100
                const blurThreshold = 70; // Diperlonggar dari 100 ke 70 untuk webcam
                if (blurScore < blurThreshold) {
                    errors.push({
                        type: 'blur',
                        message: `Gambar terlalu kabur (score: ${blurScore.toFixed(1)}, minimal: ${blurThreshold}). Pastikan kamera stabil, fokus ke wajah, dan pencahayaan cukup.`,
                        score: blurScore
                    });
                } else {
                    warnings.push(`Blur score: ${blurScore.toFixed(1)} (threshold: ${blurThreshold})`);
                }

                // 2. Validasi Exposure
                const exposureCheck = checkExposure(imageData);
                if (!exposureCheck.isValid) {
                    errors.push({
                        type: 'exposure',
                        message: `${exposureCheck.message} (brightness: ${exposureCheck.brightness.toFixed(1)}). Pastikan pencahayaan cukup dan merata.`,
                        brightness: exposureCheck.brightness
                    });
                } else {
                    warnings.push(`Brightness: ${exposureCheck.brightness.toFixed(1)} (optimal)`);
                }

                // 3. Validasi Sharpness (jika ada faceBox)
                if (faceBox) {
                    const sharpnessCheck = checkSharpness(imageData, faceBox);
                    // Threshold sharpness lebih fleksibel untuk webcam
                    if (!sharpnessCheck.isValid) {
                        errors.push({
                            type: 'sharpness',
                            message: `${sharpnessCheck.message} (score: ${sharpnessCheck.sharpness.toFixed(1)}). Pastikan kamera fokus dengan baik.`,
                            sharpness: sharpnessCheck.sharpness
                        });
                    } else {
                        warnings.push(`Sharpness: ${sharpnessCheck.sharpness.toFixed(1)} (optimal)`);
                    }
                }

                resolve({
                    isValid: errors.length === 0,
                    errors: errors,
                    warnings: warnings,
                    scores: {
                        blur: blurScore,
                        brightness: exposureCheck.brightness,
                        sharpness: faceBox ? checkSharpness(imageData, faceBox).sharpness : null
                    }
                });
            };
            img.onerror = function() {
                resolve({
                    isValid: false,
                    errors: [{
                        type: 'load',
                        message: 'Gagal memuat gambar untuk validasi'
                    }],
                    warnings: [],
                    scores: null
                });
            };
            img.src = imageUri;
        });
    }

    // Fungsi untuk mengambil foto dengan validasi kualitas
    async function capturePhoto() {
        if (isProcessing || !isFaceDetected) return;
        if (!isMultiCaptureActive) return;

        const currentTime = Date.now();
        if (currentTime - lastCaptureTime < 400) {
            return;
        }

        isProcessing = true;
        lastCaptureTime = currentTime;
        const loadingOverlay = document.getElementById('loadingOverlay');
        const captureCounter = document.getElementById('captureCounter');
        const captureText = document.getElementById('captureText');

        loadingOverlay.classList.add('active');
        captureCounter.classList.add('active');
        captureText.textContent = `Mengambil foto ${capturedImages.length + 1}/${TOTAL_IMAGES}: ${DIRECTIONS[currentDirectionIndex].label}`;

        Webcam.snap(async function(uri) {
            try {
                // Dapatkan face box untuk validasi sharpness
                const video = document.querySelector('.webcam-capture video');
                let faceBox = null;
                if (video && isModelsLoaded) {
                    try {
                        const detection = await faceapi.detectSingleFace(video, new faceapi.SsdMobilenetv1Options({
                            minConfidence: 0.5
                        })).withFaceLandmarks();
                        if (detection) {
                            faceBox = detection.detection.box;
                        }
                    } catch (e) {
                        console.warn('Tidak bisa deteksi wajah untuk validasi:', e);
                    }
                }

                // Validasi kualitas gambar
                captureText.textContent = 'Memvalidasi kualitas gambar...';
                const qualityCheck = await validateImageQuality(uri, faceBox);

                if (!qualityCheck.isValid) {
                    // Tampilkan overlay kualitas langsung di kamera
                    showQualityOverlay(qualityCheck, uri);
                    isProcessing = false;
                    return; // Reject foto (kecuali user pilih "Tetap Simpan")
                }

                // Validasi konsistensi wajah (Phase 2)
                captureText.textContent = 'Memvalidasi konsistensi wajah...';
                const consistencyCheck = await validateFaceConsistency(uri);

                if (!consistencyCheck.isValid) {
                    // Gabungkan error consistency dengan quality errors
                    const combinedErrors = [
                        ...qualityCheck.errors || [],
                        ...consistencyCheck.errors
                    ];

                    showQualityOverlay({
                        isValid: false,
                        errors: combinedErrors,
                        scores: qualityCheck.scores
                    }, uri);
                    isProcessing = false;
                    return; // Reject foto (kecuali user pilih "Tetap Simpan")
                }

                // Jika semua valid, simpan foto
                // Note: Descriptor sudah disimpan di validateFaceConsistency jika valid
                proceedWithSave(uri, qualityCheck.scores, false);
            } catch (error) {
                console.error('Error dalam validasi kualitas:', error);
                captureCounter.classList.remove('active');
                loadingOverlay.classList.remove('active');

                // Tampilkan error di overlay juga
                showQualityOverlay({
                    isValid: false,
                    errors: [{
                        type: 'error',
                        message: 'Terjadi kesalahan saat memvalidasi gambar. Silakan coba lagi.'
                    }],
                    scores: null
                }, uri);
            } finally {
                isProcessing = false;
            }
        });

        // Timeout fallback
        setTimeout(() => {
            if (isProcessing) {
                isProcessing = false;
                loadingOverlay.classList.remove('active');
                captureCounter.classList.remove('active');
            }
        }, 5000);
    }

    // ============================================
    // QUALITY OVERLAY FUNCTIONS (Global Scope)
    // ============================================

    // Variabel global untuk menyimpan rejected image
    let currentRejectedImageUri = null;
    let currentRejectedImageScores = null;

    // ============================================
    // FACE CONSISTENCY VALIDATION (Phase 2)
    // ============================================

    // Variabel untuk menyimpan face descriptors dari foto sebelumnya
    let previousFaceDescriptors = [];
    const FACE_CONSISTENCY_THRESHOLD = 0.6; // Distance threshold (semakin kecil = lebih strict)

    // ============================================
    // FACE CROPPING (Phase 3 - Preprocessing)
    // ============================================

    /**
     * Crop wajah dari gambar dengan padding
     * @param {string} imageUri - Base64 image URI
     * @param {Object} faceBox - Face detection box {x, y, width, height}
     * @param {number} paddingPercent - Padding dalam persen (default: 20%)
     * @param {number} minSize - Ukuran minimum untuk crop (default: 224x224)
     * @returns {Promise<string>} - Base64 cropped image URI
     */
    async function cropFaceFromImage(imageUri, faceBox, paddingPercent = 20, minSize = 224) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = function() {
                try {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // Hitung padding dalam pixel
                    const paddingX = (faceBox.width * paddingPercent) / 100;
                    const paddingY = (faceBox.height * paddingPercent) / 100;

                    // Hitung area crop dengan padding
                    let cropX = Math.max(0, faceBox.x - paddingX);
                    let cropY = Math.max(0, faceBox.y - paddingY);
                    let cropWidth = Math.min(img.width - cropX, faceBox.width + (paddingX * 2));
                    let cropHeight = Math.min(img.height - cropY, faceBox.height + (paddingY * 2));

                    // Pastikan ukuran minimum
                    if (cropWidth < minSize) {
                        const diff = minSize - cropWidth;
                        cropX = Math.max(0, cropX - diff / 2);
                        cropWidth = minSize;
                    }
                    if (cropHeight < minSize) {
                        const diff = minSize - cropHeight;
                        cropY = Math.max(0, cropY - diff / 2);
                        cropHeight = minSize;
                    }

                    // Pastikan tidak melebihi batas gambar
                    if (cropX + cropWidth > img.width) {
                        cropWidth = img.width - cropX;
                    }
                    if (cropY + cropHeight > img.height) {
                        cropHeight = img.height - cropY;
                    }

                    // Set canvas size
                    canvas.width = cropWidth;
                    canvas.height = cropHeight;

                    // Draw cropped image
                    ctx.drawImage(
                        img,
                        cropX, cropY, cropWidth, cropHeight, // Source
                        0, 0, cropWidth, cropHeight // Destination
                    );

                    // Convert ke base64
                    const croppedUri = canvas.toDataURL('image/jpeg', 0.95);
                    resolve(croppedUri);
                } catch (error) {
                    console.error('Error cropping face:', error);
                    reject(error);
                }
            };
            img.onerror = function() {
                reject(new Error('Failed to load image for cropping'));
            };
            img.src = imageUri;
        });
    }

    /**
     * Mendapatkan face box dari image URI
     * @param {string} imageUri - Base64 image URI
     * @returns {Promise<Object|null>} - Face box {x, y, width, height} atau null
     */
    async function getFaceBoxFromImage(imageUri) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = async function() {
                try {
                    if (!isModelsLoaded) {
                        console.warn('Face API models belum dimuat');
                        resolve(null);
                        return;
                    }

                    // Deteksi wajah
                    const detection = await faceapi
                        .detectSingleFace(img, new faceapi.SsdMobilenetv1Options({
                            minConfidence: 0.5
                        }))
                        .withFaceLandmarks();

                    if (detection && detection.detection) {
                        resolve(detection.detection.box);
                    } else {
                        resolve(null);
                    }
                } catch (error) {
                    console.error('Error detecting face for cropping:', error);
                    resolve(null);
                }
            };
            img.onerror = function() {
                resolve(null);
            };
            img.src = imageUri;
        });
    }

    /**
     * Mendapatkan face descriptor dari image URI
     * @param {string} imageUri - Base64 image URI
     * @returns {Promise<Float32Array|null>} - Face descriptor atau null jika tidak ada wajah
     */
    async function getFaceDescriptor(imageUri) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = async function() {
                try {
                    if (!isModelsLoaded) {
                        console.warn('Face API models belum dimuat');
                        resolve(null);
                        return;
                    }

                    // Deteksi wajah dan dapatkan descriptor
                    const detection = await faceapi
                        .detectSingleFace(img, new faceapi.SsdMobilenetv1Options({
                            minConfidence: 0.5
                        }))
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (detection && detection.descriptor) {
                        resolve(detection.descriptor);
                    } else {
                        console.warn('Tidak ada wajah terdeteksi untuk consistency check');
                        resolve(null);
                    }
                } catch (error) {
                    console.error('Error mendapatkan face descriptor:', error);
                    resolve(null);
                }
            };
            img.onerror = function() {
                console.error('Error memuat gambar untuk consistency check');
                resolve(null);
            };
            img.src = imageUri;
        });
    }

    /**
     * Membandingkan face descriptor baru dengan descriptor sebelumnya
     * @param {Float32Array} newDescriptor - Face descriptor dari foto baru
     * @param {Array<Float32Array>} previousDescriptors - Array face descriptors dari foto sebelumnya
     * @returns {Object} - {isConsistent: boolean, maxDistance: number, message: string}
     */
    function compareFaceDescriptors(newDescriptor, previousDescriptors) {
        if (!newDescriptor || previousDescriptors.length === 0) {
            return {
                isConsistent: true, // Skip validation jika tidak ada data
                maxDistance: 0,
                message: 'Tidak ada data untuk dibandingkan'
            };
        }

        let maxDistance = 0;
        let minDistance = Infinity;

        // Bandingkan dengan semua foto sebelumnya
        previousDescriptors.forEach((prevDescriptor, index) => {
            if (prevDescriptor) {
                const distance = faceapi.euclideanDistance(newDescriptor, prevDescriptor);
                maxDistance = Math.max(maxDistance, distance);
                minDistance = Math.min(minDistance, distance);
                console.log(`Distance dengan foto ${index + 1}: ${distance.toFixed(3)}`);
            }
        });

        const isConsistent = maxDistance <= FACE_CONSISTENCY_THRESHOLD;

        return {
            isConsistent: isConsistent,
            maxDistance: maxDistance,
            minDistance: minDistance,
            message: isConsistent ?
                `Wajah konsisten (distance: ${maxDistance.toFixed(3)})` :
                `Wajah tidak konsisten (distance: ${maxDistance.toFixed(3)}, maksimal: ${FACE_CONSISTENCY_THRESHOLD}). Pastikan semua foto adalah wajah yang sama.`
        };
    }

    /**
     * Validasi konsistensi wajah
     * @param {string} imageUri - Base64 image URI
     * @returns {Promise<Object>} - {isValid: boolean, errors: Array, warnings: Array}
     */
    async function validateFaceConsistency(imageUri) {
        const errors = [];
        const warnings = [];

        // Dapatkan face descriptor dari foto baru
        const newDescriptor = await getFaceDescriptor(imageUri);

        if (!newDescriptor) {
            // Jika tidak ada wajah terdeteksi, skip validation (sudah di-handle di quality check)
            return {
                isValid: true,
                errors: [],
                warnings: ['Tidak bisa memvalidasi konsistensi (wajah tidak terdeteksi)']
            };
        }

        // Jika ini foto pertama, langsung accept dan simpan descriptor
        if (previousFaceDescriptors.length === 0) {
            previousFaceDescriptors.push(newDescriptor);
            warnings.push('Foto pertama - tidak ada perbandingan');
            return {
                isValid: true,
                errors: [],
                warnings: warnings
            };
        }

        // Bandingkan dengan foto sebelumnya
        const comparison = compareFaceDescriptors(newDescriptor, previousFaceDescriptors);

        if (!comparison.isConsistent) {
            errors.push({
                type: 'consistency',
                message: comparison.message,
                distance: comparison.maxDistance
            });
        } else {
            // Jika konsisten, simpan descriptor untuk perbandingan berikutnya
            previousFaceDescriptors.push(newDescriptor);
            warnings.push(comparison.message);
        }

        return {
            isValid: errors.length === 0,
            errors: errors,
            warnings: warnings
        };
    }

    // Fungsi untuk menampilkan quality overlay
    function showQualityOverlay(qualityCheck, imageUri) {
        const overlay = document.getElementById('qualityOverlay');
        const errorsContainer = document.getElementById('qualityErrors');
        const scoresContainer = document.getElementById('qualityScoresList');

        // Simpan data untuk retry/skip
        currentRejectedImageUri = imageUri;
        currentRejectedImageScores = qualityCheck.scores;

        // Sembunyikan loading
        document.getElementById('loadingOverlay').classList.remove('active');
        document.getElementById('captureCounter').classList.remove('active');

        // Tampilkan error messages
        errorsContainer.innerHTML = '';
        qualityCheck.errors.forEach(error => {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'quality-error-item';
            const icon = document.createElement('i');
            icon.className = 'ti ti-alert-circle me-2';
            errorDiv.appendChild(icon);
            errorDiv.appendChild(document.createTextNode(error.message));
            errorsContainer.appendChild(errorDiv);
        });

        // Tampilkan quality scores
        scoresContainer.innerHTML = '';
        if (qualityCheck.scores) {
            const scores = qualityCheck.scores;

            // Blur score
            if (scores.blur !== undefined) {
                const blurItem = document.createElement('div');
                blurItem.className = 'quality-score-item';
                const blurStatus = scores.blur >= 70 ? 'good' : 'bad';

                const labelDiv = document.createElement('div');
                labelDiv.className = 'quality-score-label';
                const icon1 = document.createElement('i');
                icon1.className = 'ti ti-focus';
                labelDiv.appendChild(icon1);
                const span1 = document.createElement('span');
                span1.textContent = 'Ketajaman (Blur)';
                labelDiv.appendChild(span1);

                const valueDiv = document.createElement('div');
                valueDiv.className = 'quality-score-value ' + blurStatus;
                valueDiv.textContent = scores.blur.toFixed(1) + ' / 70';

                blurItem.appendChild(labelDiv);
                blurItem.appendChild(valueDiv);
                scoresContainer.appendChild(blurItem);
            }

            // Brightness score
            if (scores.brightness !== undefined) {
                const brightnessItem = document.createElement('div');
                brightnessItem.className = 'quality-score-item';
                let brightnessStatus = 'good';
                if (scores.brightness < 80) brightnessStatus = 'bad';
                else if (scores.brightness > 220) brightnessStatus = 'bad';
                else if (scores.brightness < 100 || scores.brightness > 200) brightnessStatus = 'warning';

                const labelDiv = document.createElement('div');
                labelDiv.className = 'quality-score-label';
                const icon2 = document.createElement('i');
                icon2.className = 'ti ti-brightness';
                labelDiv.appendChild(icon2);
                const span2 = document.createElement('span');
                span2.textContent = 'Pencahayaan';
                labelDiv.appendChild(span2);

                const valueDiv = document.createElement('div');
                valueDiv.className = 'quality-score-value ' + brightnessStatus;
                valueDiv.textContent = scores.brightness.toFixed(1) + ' / 80-220';

                brightnessItem.appendChild(labelDiv);
                brightnessItem.appendChild(valueDiv);
                scoresContainer.appendChild(brightnessItem);
            }

            // Sharpness score
            if (scores.sharpness !== undefined) {
                const sharpnessItem = document.createElement('div');
                sharpnessItem.className = 'quality-score-item';
                const sharpnessStatus = scores.sharpness >= 10 ? 'good' : 'bad';

                const labelDiv = document.createElement('div');
                labelDiv.className = 'quality-score-label';
                const icon3 = document.createElement('i');
                icon3.className = 'ti ti-focus-2';
                labelDiv.appendChild(icon3);
                const span3 = document.createElement('span');
                span3.textContent = 'Ketajaman Detail';
                labelDiv.appendChild(span3);

                const valueDiv = document.createElement('div');
                valueDiv.className = 'quality-score-value ' + sharpnessStatus;
                valueDiv.textContent = scores.sharpness.toFixed(1) + ' / 10';

                sharpnessItem.appendChild(labelDiv);
                sharpnessItem.appendChild(valueDiv);
                scoresContainer.appendChild(sharpnessItem);
            }
        }

        // Tampilkan overlay
        overlay.classList.add('active');
    }

    // Fungsi helper untuk menyimpan foto setelah validasi
    async function proceedWithSave(uri, qualityScores, skipConsistencyCheck = false) {
        const captureText = document.getElementById('captureText');
        captureText.textContent = `Memproses foto ${capturedImages.length + 1}/${TOTAL_IMAGES}...`;

        // Jika skip consistency check (user pilih "Tetap Simpan"), jangan simpan descriptor
        // karena wajah tidak konsisten dan akan merusak validasi berikutnya
        // Note: Jika valid, descriptor sudah disimpan di validateFaceConsistency
        if (skipConsistencyCheck) {
            console.log('Skipping descriptor save (user chose to skip validation)');
        }

        // Crop wajah dari gambar (Phase 3 - Preprocessing)
        let finalImageUri = uri;
        try {
            captureText.textContent = `Memotong area wajah ${capturedImages.length + 1}/${TOTAL_IMAGES}...`;
            const faceBox = await getFaceBoxFromImage(uri);

            if (faceBox) {
                // Crop wajah dengan padding 20%
                finalImageUri = await cropFaceFromImage(uri, faceBox, 20, 224);
                console.log('Face cropped successfully');
            } else {
                console.warn('Tidak bisa detect wajah untuk crop, menggunakan gambar asli');
            }
        } catch (error) {
            console.error('Error cropping face:', error);
            // Jika error, gunakan gambar asli
            finalImageUri = uri;
        }

        captureText.textContent = `Foto ${capturedImages.length + 1}/${TOTAL_IMAGES} berhasil!`;

            capturedImages.push({
                direction: DIRECTIONS[currentDirectionIndex].key,
            image: finalImageUri, // Simpan cropped image
            quality: qualityScores // Simpan quality scores untuk reference
            });
            currentImageInDirection++;

            if (currentImageInDirection >= IMAGES_PER_DIRECTION) {
                currentDirectionIndex++;
                currentImageInDirection = 0;
            }
            showDirectionInstruction();

            if (capturedImages.length >= TOTAL_IMAGES) {
                sendImagesToBackend();
                stopMultiCapture();
        } else {
            // Update counter untuk foto berikutnya
        setTimeout(() => {
                const captureCounter = document.getElementById('captureCounter');
                const loadingOverlay = document.getElementById('loadingOverlay');
                captureCounter.classList.remove('active');
                loadingOverlay.classList.remove('active');
            }, 800);
        }
    }

    function sendImagesToBackend() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        const captureCounter = document.getElementById('captureCounter');
        loadingOverlay.classList.add('active');
        captureCounter.classList.add('active');
        document.getElementById('captureText').textContent = 'Menyimpan gambar ke server...';

        $.ajax({
            type: 'POST',
            url: "{{ route('facerecognition.store') }}",
            data: {
                _token: "{{ csrf_token() }}",
                nik: "{{ isset($nik) ? $nik : '' }}",
                images: JSON.stringify(capturedImages),
            },
            success: function(data) {
                loadingOverlay.classList.remove('active');
                captureCounter.classList.remove('active');

                swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '5 Gambar wajah berhasil disimpan',
                    showConfirmButton: false,
                    timer: 2000,
                }).then(function() {
                    location.reload();
                });
            },
            error: function(xhr) {
                loadingOverlay.classList.remove('active');
                captureCounter.classList.remove('active');

                swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal menyimpan gambar',
                    showConfirmButton: true,
                });
            }
        });
    }

    // ============================================
    // FACE DETECTION
    // ============================================
    async function detectFace() {
        if (!isModelsLoaded) {
            return;
        }

        try {
            const video = document.querySelector('.webcam-capture video');
            if (!video) {
                return false;
            }

            const detection = await faceapi.detectSingleFace(video, new faceapi.SsdMobilenetv1Options({
                minConfidence: 0.5,
                maxResults: 1
            })).withFaceLandmarks();

            if (detection) {
                const box = detection.detection.box;

                // Update video dimensions jika berubah
                if (video.videoWidth > 0 && video.videoHeight > 0) {
                    videoWidth = video.videoWidth;
                    videoHeight = video.videoHeight;
                }

                const centerX = videoWidth / 2;
                const centerY = videoHeight / 2;
                const faceCenterX = box.x + box.width / 2;
                const faceCenterY = box.y + box.height / 2;

                // Gunakan threshold dinamis berdasarkan resolusi video
                const thresholds = getFaceSizeThresholds();

                // Threshold posisi juga dinamis (5% dari dimensi video)
                const positionThresholdX = videoWidth * 0.05;
                const positionThresholdY = videoHeight * 0.05;

                const isInPosition =
                    Math.abs(faceCenterX - centerX) < positionThresholdX &&
                    Math.abs(faceCenterY - centerY) < positionThresholdY &&
                    box.width >= thresholds.minWidth && box.width <= thresholds.maxWidth &&
                    box.height >= thresholds.minHeight && box.height <= thresholds.maxHeight;

                const statusIndicator = document.getElementById('statusIndicator');
                const guideText = document.getElementById('guideText');
                const faceGuide = document.getElementById('faceGuide');
                const progressContainer = document.getElementById('progressContainer');
                const progressFill = document.getElementById('progressFill');
                const progressText = document.getElementById('progressText');
                const infoPanel = document.getElementById('infoPanel');

                if (isInPosition) {
                    consecutiveGoodPositions++;
                    statusIndicator.classList.add('ready');
                    faceGuide.classList.add('ready');
                    progressContainer.classList.add('active');

                    const progress = Math.min(100, (consecutiveGoodPositions / REQUIRED_CONSECUTIVE_POSITIONS) * 100);
                    progressFill.style.width = progress + '%';

                    if (consecutiveGoodPositions >= REQUIRED_CONSECUTIVE_POSITIONS) {
                        guideText.textContent = '✅ Posisi wajah sudah tepat, mengambil foto...';
                        guideText.classList.add('ready');
                        progressText.textContent = 'Stabil! Mengambil foto...';
                        infoPanel.classList.add('active');
                    isFaceDetected = true;

                    if (consecutiveGoodPositions >= REQUIRED_CONSECUTIVE_POSITIONS) {
                        if (!autoCaptureTimeout) {
                            autoCaptureTimeout = setTimeout(() => {
                                capturePhoto();
                                autoCaptureTimeout = null;
                                }, 1000);
                        }
                        }
                    } else {
                        guideText.textContent = `Posisi wajah sudah tepat, tunggu sebentar... (${Math.round(progress)}%)`;
                        guideText.classList.remove('ready');
                        progressText.textContent = `Stabilitas: ${Math.round(progress)}%`;
                    }
                } else {
                    consecutiveGoodPositions = 0;
                    statusIndicator.classList.remove('ready');
                    faceGuide.classList.remove('ready');
                    progressContainer.classList.remove('active');
                    progressFill.style.width = '0%';

                    // Gunakan threshold dinamis untuk pesan error
                    const thresholds = getFaceSizeThresholds();
                    const positionThresholdX = videoWidth * 0.05;
                    const positionThresholdY = videoHeight * 0.05;

                    let guideMessage = 'Posisikan wajah Anda di dalam kotak panduan hijau';

                    // Debug info (bisa dihapus nanti)
                    const faceSizePercent = ((box.width / videoWidth) * 100).toFixed(1);

                    if (box.width < thresholds.minWidth) {
                        guideMessage =
                            `⚠️ Mendekatlah ke kamera (wajah ${faceSizePercent}%, perlu minimal ${(thresholds.minWidth/videoWidth*100).toFixed(1)}%)`;
                    } else if (box.width > thresholds.maxWidth) {
                        guideMessage =
                            `⚠️ Menjauhlah dari kamera (wajah ${faceSizePercent}%, maksimal ${(thresholds.maxWidth/videoWidth*100).toFixed(1)}%)`;
                    } else if (box.height < thresholds.minHeight) {
                        guideMessage = '⚠️ Posisikan wajah lebih vertikal';
                    } else if (box.height > thresholds.maxHeight) {
                        guideMessage = '⚠️ Posisikan wajah lebih vertikal';
                    } else if (Math.abs(faceCenterX - centerX) > positionThresholdX) {
                        guideMessage = faceCenterX < centerX ? '← Geser ke kanan' : 'Geser ke kiri →';
                    } else if (Math.abs(faceCenterY - centerY) > positionThresholdY) {
                        guideMessage = faceCenterY < centerY ? '↑ Geser ke bawah' : 'Geser ke atas ↓';
                    }

                    if (guideText) {
                        guideText.textContent = guideMessage;
                        guideText.classList.remove('ready');
                    }
                    isFaceDetected = false;
                }
            } else {
                consecutiveGoodPositions = 0;
                const statusIndicator = document.getElementById('statusIndicator');
                const guideText = document.getElementById('guideText');
                const faceGuide = document.getElementById('faceGuide');
                const progressContainer = document.getElementById('progressContainer');

                statusIndicator.classList.remove('ready');
                faceGuide.classList.remove('ready');
                progressContainer.classList.remove('active');

                if (guideText) {
                    guideText.textContent = '❌ Wajah tidak terdeteksi. Pastikan wajah menghadap kamera';
                    guideText.classList.remove('ready');
                }
                isFaceDetected = false;
            }
        } catch (error) {
            console.error('Error detecting face:', error);
            const guideText = document.getElementById('guideText');
            if (guideText) {
                guideText.textContent = '❌ Terjadi kesalahan dalam deteksi wajah';
                guideText.classList.remove('ready');
            }
        }
    }

    // ============================================
    // INITIALIZATION
    // ============================================
    let isFaceDetected = false;
    let isProcessing = false;
    let autoCaptureTimeout = null;
    let consecutiveGoodPositions = 0;
    const REQUIRED_CONSECUTIVE_POSITIONS = 10;
    let lastCaptureTime = 0;
    const MIN_CAPTURE_INTERVAL = 2000;
    let isModelsLoaded = false;

    // Dynamic threshold berdasarkan resolusi video
    let videoWidth = 1280; // Default, akan diupdate saat video ready
    let videoHeight = 720; // Default, akan diupdate saat video ready

    // Fungsi untuk mendapatkan threshold dinamis
    function getFaceSizeThresholds() {
        // Threshold berdasarkan persentase dari resolusi video
        // Lebih fleksibel untuk berbagai aspect ratio
        // Diperlonggar untuk mengakomodasi berbagai ukuran wajah dan aspect ratio
        const minWidthPercent = 0.10; // 10% dari lebar video (minimum) - lebih longgar
        const maxWidthPercent = 0.35; // 35% dari lebar video (maximum) - lebih longgar
        const minHeightPercent = 0.12; // 12% dari tinggi video (minimum) - lebih longgar
        const maxHeightPercent = 0.45; // 45% dari tinggi video (maximum) - lebih longgar

        return {
            minWidth: Math.floor(videoWidth * minWidthPercent),
            maxWidth: Math.floor(videoWidth * maxWidthPercent),
            minHeight: Math.floor(videoHeight * minHeightPercent),
            maxHeight: Math.floor(videoHeight * maxHeightPercent)
        };
    }

    async function initializeFaceRecognition() {
        try {
            await loadFaceApiScript();
            isModelsLoaded = await loadFaceApiModels();

            if (isModelsLoaded) {
                startVideo();
                setInterval(detectFace, 50);

                $("#btnMulaiRekam").click(function() {
                    $("#btnMulaiRekam").prop("disabled", true);
                    startMultiCapture();
                });
            } else {
                swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat model pengenalan wajah. Silakan muat ulang halaman.',
                    showConfirmButton: true
                });
            }
        } catch (error) {
            console.error('Error initializing face recognition:', error);
            swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menginisialisasi pengenalan wajah.',
                showConfirmButton: true
            });
        }
    }

    // ============================================
    // EVENT HANDLERS FOR QUALITY OVERLAY
    // ============================================

    // Event handlers untuk tombol quality overlay
    function setupQualityOverlayHandlers() {
        const btnRetry = document.getElementById('qualityBtnRetry');
        const btnSkip = document.getElementById('qualityBtnSkip');
        const qualityOverlay = document.getElementById('qualityOverlay');

        if (btnRetry) {
            btnRetry.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Retry button clicked');
                if (qualityOverlay) {
                    qualityOverlay.classList.remove('active');
                }
                // Reset variabel - user akan otomatis retry saat posisi wajah tepat lagi
                currentRejectedImageUri = null;
                currentRejectedImageScores = null;
            });
        }

        if (btnSkip) {
            btnSkip.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Skip button clicked', currentRejectedImageUri);
                if (qualityOverlay) {
                    qualityOverlay.classList.remove('active');
                }
                // Simpan foto meskipun kualitas/konsistensi tidak memenuhi
                // skipConsistencyCheck = true karena user memilih skip
                if (currentRejectedImageUri) {
                    console.log('Proceeding with save (skipping validation)...');
                    proceedWithSave(currentRejectedImageUri, currentRejectedImageScores, true);
                    currentRejectedImageUri = null;
                    currentRejectedImageScores = null;
                } else {
                    console.warn('No rejected image to save');
                }
            });
        }
    }

    // Initialize
    // Setup event handlers saat DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupQualityOverlayHandlers);
    } else {
        // DOM sudah ready, langsung setup
        setupQualityOverlayHandlers();
    }

    initializeFaceRecognition();
    updateStepIndicator(1);
</script>
