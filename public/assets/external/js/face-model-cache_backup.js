/**
 * Face Recognition Model Caching Utility
 * Menggunakan IndexedDB untuk cache model face-api.js
 * 
 * Usage:
 * - Preload di dashboard: window.FaceModelCache.preloadFaceModels()
 * - Load di presensi: window.FaceModelCache.loadModelWithCache(net, path)
 */

(function() {
    'use strict';
    
    const DB_NAME = 'FaceRecognitionCache';
    const DB_VERSION = 2; // Increment untuk support descriptors
    const STORE_NAME = 'models';
    const DESCRIPTORS_STORE = 'descriptors';
    const CACHE_EXPIRY = 24 * 60 * 60 * 1000; // 24 jam
    
    let db = null;
    
    // Inisialisasi IndexedDB
    function initDB() {
        return new Promise((resolve, reject) => {
            if (db) {
                resolve(db);
                return;
            }
            
            const request = indexedDB.open(DB_NAME, DB_VERSION);
            
            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                db = request.result;
                resolve(db);
            };
            
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                const oldVersion = event.oldVersion;
                
                // Create models store (v1)
                if (oldVersion < 1 || !db.objectStoreNames.contains(STORE_NAME)) {
                    db.createObjectStore(STORE_NAME);
                }
                
                // Create descriptors store (v2)
                if (oldVersion < 2 || !db.objectStoreNames.contains(DESCRIPTORS_STORE)) {
                    db.createObjectStore(DESCRIPTORS_STORE);
                }
            };
        });
    }
    
    // Simpan model ke IndexedDB (untuk tracking)
    async function saveModelToCache(modelName, modelData) {
        try {
            const database = await initDB();
            const transaction = database.transaction([STORE_NAME], 'readwrite');
            const store = transaction.objectStore(STORE_NAME);
            
            const cacheData = {
                data: modelData,
                timestamp: Date.now()
            };
            
            await store.put(cacheData, modelName);
            console.log(`[FaceModelCache] Model ${modelName} cached successfully`);
        } catch (error) {
            console.warn(`[FaceModelCache] Failed to cache model ${modelName}:`, error);
        }
    }
    
    // Load model dari IndexedDB (untuk tracking)
    async function loadModelFromCache(modelName) {
        try {
            const database = await initDB();
            const transaction = database.transaction([STORE_NAME], 'readonly');
            const store = transaction.objectStore(STORE_NAME);
            
            return new Promise((resolve, reject) => {
                const request = store.get(modelName);
                request.onsuccess = () => {
                    const result = request.result;
                    if (result && (Date.now() - result.timestamp) < CACHE_EXPIRY) {
                        console.log(`[FaceModelCache] Model ${modelName} found in cache`);
                        resolve(result.data);
                    } else {
                        resolve(null);
                    }
                };
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.warn(`[FaceModelCache] Failed to load model ${modelName} from cache:`, error);
            return null;
        }
    }
    
    // Load model dengan caching (menggunakan browser cache + IndexedDB tracking)
    async function loadModelWithCache(net, modelPath) {
        // Cek IndexedDB untuk tracking
        const cached = await loadModelFromCache(modelPath);
        
        if (cached) {
            // Model sudah pernah di-load, browser cache akan handle
            console.log(`[FaceModelCache] Loading ${modelPath} (browser cache should be used)`);
        }
        
        try {
            // Load dari URI (browser akan gunakan cache jika ada)
            await net.loadFromUri(modelPath);
            
            // Update cache timestamp
            await saveModelToCache(modelPath, { loaded: true, lastLoad: Date.now() });
            
            if (cached) {
                console.log(`[FaceModelCache] Model ${modelPath} loaded from browser cache`);
            } else {
                console.log(`[FaceModelCache] Model ${modelPath} loaded from server and cached`);
            }
            
            return true;
        } catch (error) {
            console.error(`[FaceModelCache] Failed to load model ${modelPath}:`, error);
            return false;
        }
    }
    
    // Preload models di background (non-blocking)
    async function preloadFaceModels() {
        if (typeof faceapi === 'undefined') {
            console.log('[FaceModelCache] Face-api.js not loaded yet, skipping preload');
            return false;
        }
        
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        console.log('[FaceModelCache] Starting background preload of face recognition models...');
        
        try {
            if (isMobile) {
                await Promise.all([
                    loadModelWithCache(faceapi.nets.tinyFaceDetector, '/models'),
                    loadModelWithCache(faceapi.nets.faceRecognitionNet, '/models'),
                    loadModelWithCache(faceapi.nets.faceLandmark68Net, '/models')
                ]);
            } else {
                await Promise.all([
                    loadModelWithCache(faceapi.nets.ssdMobilenetv1, '/models'),
                    loadModelWithCache(faceapi.nets.faceRecognitionNet, '/models'),
                    loadModelWithCache(faceapi.nets.faceLandmark68Net, '/models')
                ]);
            }
            
            console.log('[FaceModelCache] Face recognition models preloaded successfully!');
            
            // Simpan flag bahwa model sudah di-load
            sessionStorage.setItem('faceModelsPreloaded', 'true');
            sessionStorage.setItem('faceModelsPreloadTime', Date.now().toString());
            
            return true;
        } catch (error) {
            console.warn('[FaceModelCache] Error preloading face models:', error);
            return false;
        }
    }
    
    // Simpan face descriptors ke IndexedDB
    async function saveDescriptors(nik, descriptors, wajahFiles) {
        try {
            const database = await initDB();
            const transaction = database.transaction([DESCRIPTORS_STORE], 'readwrite');
            const store = transaction.objectStore(DESCRIPTORS_STORE);
            
            const cacheData = {
                descriptors: descriptors,
                wajahFiles: wajahFiles,
                timestamp: Date.now()
            };
            
            await store.put(cacheData, nik);
            console.log(`[FaceModelCache] Descriptors for ${nik} cached successfully`);
        } catch (error) {
            console.warn(`[FaceModelCache] Failed to cache descriptors for ${nik}:`, error);
        }
    }
    
    // Load face descriptors dari IndexedDB
    async function loadDescriptors(nik) {
        try {
            const database = await initDB();
            const transaction = database.transaction([DESCRIPTORS_STORE], 'readonly');
            const store = transaction.objectStore(DESCRIPTORS_STORE);
            
            return new Promise((resolve, reject) => {
                const request = store.get(nik);
                request.onsuccess = () => {
                    const result = request.result;
                    if (result && (Date.now() - result.timestamp) < CACHE_EXPIRY) {
                        console.log(`[FaceModelCache] Descriptors for ${nik} found in cache`);
                        resolve(result);
                    } else {
                        resolve(null);
                    }
                };
                request.onerror = () => reject(request.error);
            });
        } catch (error) {
            console.warn(`[FaceModelCache] Failed to load descriptors for ${nik} from cache:`, error);
            return null;
        }
    }
    
    // Preload face descriptors di background
    async function preloadFaceDescriptors(nik, label) {
        if (typeof faceapi === 'undefined') {
            console.log('[FaceModelCache] Face-api.js not loaded yet, skipping descriptor preload');
            return false;
        }
        
        // Cek apakah sudah ada di cache
        const cached = await loadDescriptors(nik);
        if (cached) {
            console.log(`[FaceModelCache] Descriptors for ${nik} already cached`);
            return true;
        }
        
        console.log(`[FaceModelCache] Starting background preload of face descriptors for ${nik}...`);
        
        try {
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            const timestamp = new Date().getTime();
            
            // Fetch data wajah
            const response = await fetch(`/facerecognition/getwajah?t=${timestamp}`);
            const data = await response.json();
            
            if (!data || data.length === 0) {
                console.warn(`[FaceModelCache] No face data found for ${nik}`);
                return false;
            }
            
            // Process semua foto secara parallel
            const processPromises = data.slice(0, 5).map(async (faceData) => {
                try {
                    const randomBust = Math.random().toString(36).substring(7);
                    const imagePath = `/storage/uploads/facerecognition/${label}/${faceData.wajah}?t=${timestamp}&r=${randomBust}&v=${Date.now()}`;
                    
                    const img = await faceapi.fetchImage(imagePath);
                    if (!img) return null;
                    
                    let detections;
                    if (isMobile) {
                        detections = await faceapi.detectSingleFace(
                            img, new faceapi.TinyFaceDetectorOptions({
                                inputSize: 160,
                                scoreThreshold: 0.5
                            })
                        ).withFaceLandmarks().withFaceDescriptor();
                    } else {
                        detections = await faceapi.detectSingleFace(
                            img, new faceapi.SsdMobilenetv1Options({
                                minConfidence: 0.5
                            })
                        ).withFaceLandmarks().withFaceDescriptor();
                    }
                    
                    if (detections) {
                        return {
                            descriptor: detections.descriptor,
                            wajahFile: faceData.wajah
                        };
                    }
                } catch (err) {
                    console.warn(`[FaceModelCache] Error processing ${faceData.wajah}:`, err);
                }
                return null;
            });
            
            const results = await Promise.all(processPromises);
            const validResults = results.filter(r => r !== null);
            
            if (validResults.length > 0) {
                const descriptors = validResults.map(r => r.descriptor);
                const wajahFiles = validResults.map(r => r.wajahFile);
                await saveDescriptors(nik, descriptors, wajahFiles);
                console.log(`[FaceModelCache] Preloaded ${validResults.length} descriptors for ${nik}`);
                return true;
            }
            
            return false;
        } catch (error) {
            console.warn(`[FaceModelCache] Error preloading descriptors for ${nik}:`, error);
            return false;
        }
    }
    
    // Clear cache (untuk debugging/testing)
    async function clearCache() {
        try {
            const database = await initDB();
            const transaction = database.transaction([STORE_NAME, DESCRIPTORS_STORE], 'readwrite');
            await transaction.objectStore(STORE_NAME).clear();
            await transaction.objectStore(DESCRIPTORS_STORE).clear();
            console.log('[FaceModelCache] Cache cleared');
        } catch (error) {
            console.error('[FaceModelCache] Failed to clear cache:', error);
        }
    }
    
    // Clear descriptors untuk NIK tertentu (ketika wajah dihapus)
    async function clearDescriptors(nik) {
        try {
            const database = await initDB();
            const transaction = database.transaction([DESCRIPTORS_STORE], 'readwrite');
            const store = transaction.objectStore(DESCRIPTORS_STORE);
            await store.delete(nik);
            console.log(`[FaceModelCache] Descriptors cleared for ${nik}`);
            return true;
        } catch (error) {
            console.error(`[FaceModelCache] Failed to clear descriptors for ${nik}:`, error);
            return false;
        }
    }
    
    // Expose ke global scope
    window.FaceModelCache = {
        loadModelWithCache: loadModelWithCache,
        preloadFaceModels: preloadFaceModels,
        preloadFaceDescriptors: preloadFaceDescriptors,
        loadDescriptors: loadDescriptors,
        saveDescriptors: saveDescriptors,
        initDB: initDB,
        clearCache: clearCache,
        clearDescriptors: clearDescriptors
    };
    
    console.log('[FaceModelCache] Utility loaded');
})();

