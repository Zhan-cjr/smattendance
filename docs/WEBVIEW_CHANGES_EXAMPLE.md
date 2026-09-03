// FILE: smattendance-mobile/app/webview.tsx
// PERUBAHAN YANG DIPERLUKAN UNTUK LIVE LOCATION BACKGROUND TRACKING

// ============================================
// STEP 1: TAMBAHKAN IMPORT DI BAGIAN ATAS
// ============================================

import { useLocationValidation } from '@/hooks/useLocationValidation';
import { locationValidationScript } from '@/utils/locationValidation';
import { backgroundLocationService } from '@/utils/backgroundLocation'; // ← ADD THIS
import AsyncStorage from '@react-native-async-storage/async-storage'; // ← ADD THIS
import { Ionicons } from '@expo/vector-icons';
import { Camera } from 'expo-camera';
import * as Location from 'expo-location';
import React, { useEffect, useRef, useState } from 'react';
import { ActivityIndicator, Alert, BackHandler, StatusBar, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { WebView, WebViewNavigation } from 'react-native-webview';

export default function WebViewScreen() {
  const [isLoading, setIsLoading] = useState(true);
  const [hasError, setHasError] = useState(false);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [locationWarnings, setLocationWarnings] = useState<string[]>([]);
  const webViewRef = useRef<WebView>(null);
  const { checkFakeGPS } = useLocationValidation();
  
  // Interval untuk monitoring berkelanjutan
  const monitoringIntervalRef = useRef<number | null>(null);
  
  // Default URL jika tidak ada parameter
  const webUrl = 'https://smattendancev2.amnal.site/';

  // JavaScript to enable camera and location permissions + validation
  const injectedJavaScript = `
    // ... existing code ...
    ${locationValidationScript}
    true;
  `;

  // ============================================
  // STEP 2: TAMBAHKAN FUNGSI-FUNGSI BARU
  // ============================================

  /**
   * Setup background location tracking saat user login dengan spy=1
   */
  const setupBackgroundTracking = async () => {
    try {
      console.log('🔍 Checking spy status for background tracking...');

      // Get auth token dari AsyncStorage (biasanya di-set saat login)
      const authToken = await AsyncStorage.getItem('auth_token');
      if (!authToken) {
        console.log('ℹ️ No auth token found, background tracking not setup');
        return;
      }

      // Check spy status dari server
      const response = await fetch(`${webUrl}/api/check-spy-status`, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${authToken}`,
          'Accept': 'application/json',
        },
      });

      if (!response.ok) {
        console.warn(`❌ Failed to check spy status: ${response.status}`);
        return;
      }

      const data = await response.json();
      console.log('📍 Spy status response:', data);

      // Jika user punya spy=1, setup background tracking
      if (data.spy === 1 && data.nik) {
        console.log('✅ User has spy=1, setting up background tracking');
        await backgroundLocationService.setupTracking(data.nik, authToken);
      } else {
        console.log('ℹ️ User does not have spy=1, background tracking not needed');
        // Pastikan tidak ada tracking yang berjalan
        await backgroundLocationService.stopTracking();
      }
    } catch (error) {
      console.error('⚠️ Error setting up background tracking:', error);
      // Continue anyway - foreground tracking will still work
    }
  };

  /**
   * Handle logout - stop background tracking
   */
  const handleLogout = async () => {
    try {
      console.log('🛑 User logged out, stopping background tracking');
      await backgroundLocationService.stopTracking();
      await AsyncStorage.removeItem('auth_token');
      console.log('✅ Background location tracking deactivated');
    } catch (error) {
      console.error('❌ Error stopping background tracking:', error);
    }
  };

  /**
   * Debug: Check current tracking status (optional)
   */
  const checkTrackingStatus = async () => {
    try {
      const status = await backgroundLocationService.getTrackingStatus();
      console.log('📊 Current tracking status:', status);
      Alert.alert(
        'Tracking Status',
        `Task Registered: ${status.isTaskRegistered}\nUser: ${status.userNik}\nFG Permission: ${status.foregroundPermission}\nBG Permission: ${status.backgroundPermission}`
      );
    } catch (error) {
      console.error('Error checking status:', error);
    }
  };

  // Request permissions for camera and location
  const requestPermissions = async () => {
    try {
      // Request camera permission
      const cameraPermission = await Camera.requestCameraPermissionsAsync();
      
      // Request location permission
      const locationPermission = await Location.requestForegroundPermissionsAsync();
      
      if (cameraPermission.status === 'granted' && locationPermission.status === 'granted') {
        console.log('✅ All permissions granted');
      } else {
        console.log('Camera permission:', cameraPermission.status);
        console.log('Location permission:', locationPermission.status);
      }
    } catch (error) {
      console.error('Error requesting permissions:', error);
    }
  };

  // Existing function: Fungsi untuk monitoring fake GPS secara berkelanjutan
  const startContinuousMonitoring = () => {
    if (monitoringIntervalRef.current) {
      clearInterval(monitoringIntervalRef.current);
    }

    monitoringIntervalRef.current = setInterval(async () => {
      try {
        const isFake = await checkFakeGPS();
        if (isFake) {
          console.log('🚨 Fake GPS detected during continuous monitoring!');
          setLocationWarnings(prev => [...prev, 'Fake GPS detected during monitoring']);
          
          Alert.alert(
            '🚨 Fake GPS Terdeteksi!',
            'Sistem mendeteksi penggunaan fake GPS. Silakan nonaktifkan fake GPS.',
            [
              { 
                text: 'Coba Lagi', 
                onPress: () => handleRetry()
              },
              { 
                text: 'Keluar Aplikasi', 
                style: 'destructive',
                onPress: () => BackHandler.exitApp()
              }
            ]
          );
        }
      } catch (error) {
        console.error('Error in continuous monitoring:', error);
      }
    }, 10000);
  };

  const stopContinuousMonitoring = () => {
    if (monitoringIntervalRef.current) {
      clearInterval(monitoringIntervalRef.current);
      monitoringIntervalRef.current = null;
    }
  };

  const handleRetry = async () => {
    const isFake = await checkFakeGPS();
    if (!isFake) {
      startContinuousMonitoring();
    } else {
      Alert.alert(
        '🚨 Fake GPS Terdeteksi!',
        'Sistem mendeteksi penggunaan fake GPS. Silakan nonaktifkan fake GPS.',
        [
          { text: 'Coba Lagi', onPress: () => handleRetry() },
          { 
            text: 'Keluar Aplikasi', 
            style: 'destructive',
            onPress: () => BackHandler.exitApp()
          }
        ]
      );
    }
  };

  // ============================================
  // STEP 3: UPDATE handleMessage
  // ============================================

  const handleMessage = (event: any) => {
    try {
      const data = JSON.parse(event.nativeEvent.data);
      
      // Existing handlers...
      if (data.type === 'LOCATION_VALIDATION_FAILED') {
        console.log('🚨 Location validation failed:', data.issues);
        setLocationWarnings(prev => [...prev, ...data.issues]);
        stopContinuousMonitoring();
        
        Alert.alert(
          '🚨 Fake GPS Terdeteksi!',
          'Sistem mendeteksi penggunaan fake GPS. Silakan nonaktifkan fake GPS.',
          [
            { text: 'Coba Lagi', onPress: () => handleRetry() },
            { 
              text: 'Keluar Aplikasi', 
              style: 'destructive',
              onPress: () => BackHandler.exitApp()
            }
          ]
        );
      }

      // ← ADD THESE NEW HANDLERS

      /**
       * Handle user login - save credentials and setup background tracking
       */
      if (data.type === 'USER_LOGIN') {
        console.log('✅ User login detected:', data.user);
        // Save auth token dari web
        if (data.authToken) {
          AsyncStorage.setItem('auth_token', data.authToken);
        }
        // Setup background tracking
        setupBackgroundTracking();
      }

      /**
       * Handle user logout - clear credentials and stop background tracking
       */
      if (data.type === 'USER_LOGOUT') {
        console.log('🚪 User logout detected');
        handleLogout();
      }

      /**
       * Handle spy status change
       */
      if (data.type === 'SPY_STATUS_CHANGED') {
        console.log('📍 Spy status changed:', data.spy);
        if (data.spy === 1) {
          setupBackgroundTracking();
        } else {
          backgroundLocationService.stopTracking();
        }
      }

      /**
       * Debug command untuk test tracking status (optional)
       */
      if (data.type === 'DEBUG_TRACKING_STATUS') {
        checkTrackingStatus();
      }
      
    } catch (error) {
      console.log('Error parsing WebView message:', error);
    }
  };

  const handleRefresh = () => {
    setIsRefreshing(true);
    setHasError(false);
    
    if (webViewRef.current) {
      webViewRef.current.reload();
    }
    
    setTimeout(() => {
      setIsRefreshing(false);
    }, 1000);
  };

  // ============================================
  // STEP 4: UPDATE useEffect HOOKS
  // ============================================

  // Request permissions when component mounts
  React.useEffect(() => {
    requestPermissions();
  }, []);

  // Mulai monitoring dan setup background tracking saat mount
  useEffect(() => {
    // Mulai fake GPS monitoring setelah 5 detik
    const startTimer = setTimeout(() => {
      startContinuousMonitoring();
    }, 5000);

    // Setup background tracking jika user sudah login
    setupBackgroundTracking();

    // Cleanup saat komponen unmount
    return () => {
      clearTimeout(startTimer);
      stopContinuousMonitoring();
    };
  }, []);

  // ... rest of existing code ...

  const handleLoadStart = () => {
    setIsLoading(true);
    setHasError(false);
  };

  const handleLoadEnd = () => {
    setIsLoading(false);
  };

  const handleError = () => {
    setIsLoading(false);
    setHasError(true);
  };

  if (hasError) {
    return (
      <SafeAreaView style={styles.errorContainer}>
        <StatusBar barStyle="dark-content" backgroundColor="transparent" translucent />
        <Text style={styles.errorText}>Gagal memuat halaman web</Text>
        <Text style={styles.errorSubtext}>Periksa koneksi internet Anda</Text>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="dark-content" backgroundColor="transparent" translucent />
      <WebView
        ref={webViewRef}
        source={{ uri: webUrl }}
        style={styles.webview}
        startInLoadingState={true}
        javaScriptEnabled={true}
        domStorageEnabled={true}
        allowsInlineMediaPlayback={true}
        mediaPlaybackRequiresUserAction={false}
        allowsProtectedMedia={true}
        allowsFullscreenVideo={true}
        scalesPageToFit={true}
        bounces={true}
        scrollEnabled={true}
        showsHorizontalScrollIndicator={false}
        showsVerticalScrollIndicator={false}
        userAgent="Mozilla/5.0 (Linux; Android 10; Mobile) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36"
        injectedJavaScript={injectedJavaScript}
        onLoadStart={handleLoadStart}
        onLoadEnd={handleLoadEnd}
        onError={handleError}
        onShouldStartLoadWithRequest={(request: WebViewNavigation) => true}
        onMessage={handleMessage}
      />
      
      {/* Refresh Button */}
      <TouchableOpacity
        style={styles.refreshButton}
        onPress={handleRefresh}
        disabled={isRefreshing}
      >
        <Ionicons
          name={isRefreshing ? "refresh" : "refresh-outline"}
          size={24}
          color="#ffffff"
        />
      </TouchableOpacity>

      {isLoading && (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#1e3a8a" />
          <Text style={styles.loadingText}>Memuat...</Text>
        </View>
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#000000',
  },
  webview: {
    flex: 1,
  },
  loadingContainer: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 255, 255, 0.9)',
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: '#1e3a8a',
    fontWeight: '500',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f8f9fa',
  },
  errorText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#dc3545',
    textAlign: 'center',
    marginBottom: 8,
  },
  errorSubtext: {
    fontSize: 14,
    color: '#6c757d',
    textAlign: 'center',
  },
  refreshButton: {
    position: 'absolute',
    bottom: 100,
    right: 20,
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: 'rgba(30, 58, 138, 0.9)',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
    elevation: 5,
    zIndex: 1000,
  },
});
