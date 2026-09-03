<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\LiveLocation;
use App\Models\Pengaturanumum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MonitoringLokasiController extends Controller
{
    /**
     * Display monitoring lokasi dashboard
     */
    public function index(Request $request)
    {
        // Check permission
        if (!Gate::allows('presensi.index')) {
            abort(403, 'Anda tidak memiliki akses untuk fitur ini');
        }

        // Get all employees with spy enabled
        $spyEmployees = Karyawan::where('spy', 1)
            ->where('status_aktif_karyawan', 1)
            ->with(['latestLiveLocation', 'jabatan', 'departemen', 'cabang'])
            ->orderBy('nama_karyawan', 'asc')
            ->get();

        // Filter by selected employee if provided
        $selectedNik = $request->input('nik');
        $selectedEmployee = null;
        $locationHistory = collect();

        if ($selectedNik) {
            $selectedEmployee = Karyawan::with(['facerecognition'])->find($selectedNik);
            if ($selectedEmployee && $selectedEmployee->spy == 1) {
                // Get location history (last 50 records)
                $locationHistory = LiveLocation::where('nik', $selectedNik)
                    ->orderBy('tracked_at', 'desc')
                    ->limit(50)
                    ->get();
            }
        }

        // Get theme color
        $t = config('theme.colors') ?? [
            'primary' => '#2d5a4c',
            'secondary' => '#f0f0f0',
            'bg_body' => '#e8f0ed'
        ];

        // Get general settings for monitoring status
        $generalSetting = Pengaturanumum::where('id', 1)->first();

        return view('monitoring-lokasi.index', compact(
            'spyEmployees',
            'selectedEmployee',
            'selectedNik',
            'locationHistory',
            't',
            'generalSetting'
        ));
    }

    /**
     * Get location data via AJAX for real-time updates
     */
    public function getData(Request $request)
    {
        // Check permission
        if (!Gate::allows('presensi.index')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $selectedNik = $request->input('nik');

        if (!$selectedNik) {
            return response()->json([
                'status' => false,
                'message' => 'NIK tidak diberikan'
            ], 400);
        }

        $employee = Karyawan::find($selectedNik);

        if (!$employee || $employee->spy != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Karyawan tidak ditemukan atau spy tidak diaktifkan'
            ], 404);
        }

        // Get latest location
        $latestLocation = $employee->latestLiveLocation;

        if (!$latestLocation) {
            return response()->json([
                'status' => false,
                'message' => 'Belum ada data lokasi'
            ], 404);
        }

        // Get location history (last 20 records for path)
        $locationHistory = LiveLocation::where('nik', $selectedNik)
            ->orderBy('tracked_at', 'desc')
            ->limit(20)
            ->get()
            ->reverse();

        return response()->json([
            'status' => true,
            'employee' => [
                'nik' => $employee->nik,
                'nama_karyawan' => $employee->nama_karyawan,
                'nama_jabatan' => $employee->jabatan->nama_jabatan ?? '-',
                'nama_dept' => $employee->departemen->nama_dept ?? '-',
                'nama_cabang' => $employee->cabang->nama_cabang ?? '-'
            ],
            'latestLocation' => [
                'latitude' => $latestLocation->latitude,
                'longitude' => $latestLocation->longitude,
                'lokasi' => $latestLocation->lokasi,
                'accuracy' => $latestLocation->accuracy,
                'tracked_at' => $latestLocation->tracked_at->format('Y-m-d H:i:s'),
                'time_ago' => $latestLocation->tracked_at->diffForHumans()
            ],
            'history' => $locationHistory->map(function ($location) {
                return [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'lokasi' => $location->lokasi,
                    'accuracy' => $location->accuracy,
                    'tracked_at' => $location->tracked_at->format('Y-m-d H:i:s')
                ];
            })
        ], 200);
    }

    /**
     * Get all spy employees for filter dropdown
     */
    public function getSpyEmployees(Request $request)
    {
        // Check permission
        if (!Gate::allows('presensi.index')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $spyEmployees = Karyawan::where('spy', 1)
            ->where('status_aktif_karyawan', 1)
            ->with(['jabatan', 'departemen'])
            ->orderBy('nama_karyawan', 'asc')
            ->get()
            ->map(function ($employee) {
                return [
                    'nik' => $employee->nik,
                    'nama_karyawan' => $employee->nama_karyawan,
                    'nama_jabatan' => $employee->jabatan->nama_jabatan ?? '-',
                    'nama_dept' => $employee->departemen->nama_dept ?? '-'
                ];
            });

        return response()->json([
            'status' => true,
            'count' => count($spyEmployees),
            'data' => $spyEmployees
        ], 200);
    }

    /**
     * Get location history within a time range
     */
    public function getLocationHistory(Request $request)
    {
        // Check permission
        if (!Gate::allows('presensi.index')) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $nik = $request->query('nik');
        $minutes = $request->query('minutes', 60);
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $page = (int) $request->query('page', 1);
        $perPage = (int) $request->query('per_page', 5);

        if (!$nik) {
            return response()->json([
                'status' => false,
                'message' => 'NIK tidak diberikan'
            ], 400);
        }

        // Verify employee exists and has spy enabled
        $employee = Karyawan::find($nik);
        if (!$employee || $employee->spy != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Karyawan tidak ditemukan atau spy tidak diaktifkan'
            ], 404);
        }

        $query = LiveLocation::where('nik', $nik);

        if ($startDate && $endDate) {
            $query->whereBetween('tracked_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } else {
            $query->where('tracked_at', '>=', now()->subMinutes($minutes));
        }

        $order = $request->query('order', 'asc');
        $locations = $query->orderBy('tracked_at', $order)->get();
        $total = $locations->count();
        $all = $request->boolean('all');

        if ($all) {
            return response()->json([
                'status' => true,
                'count' => $total,
                'total' => $total,
                'current_page' => 1,
                'total_pages' => 1,
                'data' => $locations->map(function ($location) {
                    return [
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                        'lokasi' => $location->lokasi,
                        'accuracy' => $location->accuracy,
                        'tracked_at' => $location->tracked_at->format('Y-m-d H:i:s')
                    ];
                })
            ], 200);
        }

        $perPage = max(1, $perPage);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $paginatedData = collect($locations)->forPage($page, $perPage)->values();

        return response()->json([
            'status' => true,
            'count' => $paginatedData->count(),
            'total' => $total,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'data' => $paginatedData->map(function ($location) {
                return [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'lokasi' => $location->lokasi,
                    'accuracy' => $location->accuracy,
                    'tracked_at' => $location->tracked_at->format('Y-m-d H:i:s')
                ];
            })
        ], 200);
    }

    /**
     * Save location from client (mobile app / web app)
     * Called periodically from client to track real-time location
     */
    public function saveLocation(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'lokasi' => 'nullable|string'
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized - User not authenticated'
            ], 401);
        }

        // Get employee by authenticated user (through userkaryawan relation)
        $userKaryawan = $user->userkaryawan;
        if (!$userKaryawan) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak memiliki data karyawan'
            ], 400);
        }

        $employee = $userKaryawan->karyawan;
        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Data karyawan tidak ditemukan'
            ], 400);
        }

        // Check if employee has spy enabled
        if ($employee->spy != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Karyawan ini tidak diaktifkan untuk tracking lokasi'
            ], 403);
        }

        // Get location name from coordinates using reverse geocoding
        $lokasi = $validated['lokasi'] ?? null;
        
        // If client provided location and it's detailed (more than just coordinates or short name), use it
        if ($lokasi && strlen($lokasi) > 10 && !preg_match('/^-?\d+\.\d+,\s*-?\d+\.\d+$/', $lokasi)) {
            // Use client-provided location if it's detailed
        } else {
            // Otherwise, try reverse geocoding
            $reverseGeocoded = $this->reverseGeocode($validated['latitude'], $validated['longitude']);
            if ($reverseGeocoded) {
                $lokasi = $reverseGeocoded;
            } elseif (!$lokasi) {
                $lokasi = 'Lokasi Unknown';
            }
        }

        try {
            // Save location to database
            $location = LiveLocation::create([
                'nik' => $employee->nik,
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'accuracy' => $validated['accuracy'] ?? null,
                'lokasi' => $lokasi,
                'tracked_at' => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Lokasi berhasil disimpan',
                'data' => [
                    'id' => $location->id,
                    'nik' => $location->nik,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'lokasi' => $location->lokasi,
                    'accuracy' => $location->accuracy,
                    'tracked_at' => $location->tracked_at->format('Y-m-d H:i:s')
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error saving location: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal menyimpan lokasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simple reverse geocoding using OpenStreetMap Nominatim API
     * Returns location name from coordinates
     */
    private function reverseGeocode($lat, $lng)
    {
        // Try Google Maps API first if key is available
        $googleKey = config('services.google_maps.api_key');
        if ($googleKey) {
            try {
                $response = \Http::timeout(10)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'latlng' => $lat . ',' . $lng,
                    'key' => $googleKey,
                    'language' => 'id',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['results'][0]['formatted_address'])) {
                        return $data['results'][0]['formatted_address'];
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Google Maps reverse geocoding failed: ' . $e->getMessage());
            }
        }

        // Fallback to OpenStreetMap Nominatim
        try {
            $response = \Http::timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lng,
                'addressdetails' => 1,
                'accept-language' => 'id',
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $address = $data['address'] ?? [];
            $parts = [];

            if (!empty($address['house_number'])) {
                $parts[] = trim(($address['road'] ?? '') . ' ' . $address['house_number']);
            } elseif (!empty($address['road'])) {
                $parts[] = $address['road'];
            }

            foreach (['suburb', 'neighbourhood', 'village', 'hamlet', 'town', 'city_district', 'city', 'county', 'state'] as $key) {
                if (!empty($address[$key]) && !in_array($address[$key], $parts, true)) {
                    $parts[] = $address[$key];
                }
            }

            $formatted = implode(', ', array_filter($parts));
            return $formatted ?: ($data['display_name'] ?? null);
        } catch (\Exception $e) {
            \Log::warning('OpenStreetMap reverse geocoding failed: ' . $e->getMessage());
            return null;
        }
    }
}

