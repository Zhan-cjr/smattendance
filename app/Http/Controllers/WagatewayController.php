<?php

namespace App\Http\Controllers;

use App\Models\Pengaturanumum;
use App\Models\Device;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WagatewayController extends Controller
{
    public function index()
    {
        $data['generalsetting'] = Pengaturanumum::where('id', 1)->first();
        $data['devices'] = Device::orderBy('created_at', 'desc')->get();
        return view('wagateway.scanqr', $data);
    }

    public function addDevice(Request $request)
    {
        $request->validate([
            'sender' => 'required|string|max:20'
        ]);

        try {
            DB::beginTransaction();

            // Ambil data dari general setting
            $generalsetting = Pengaturanumum::where('id', 1)->first();

            if (!$generalsetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'General setting tidak ditemukan'
                ], 400);
            }

            // Cek apakah device sudah ada
            $existingDevice = Device::where('number', $request->sender)->first();
            if ($existingDevice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device dengan nomor ' . $request->sender . ' sudah terdaftar'
                ], 400);
            }

            // Siapkan data untuk API
            $apiData = [
                'api_key' => $generalsetting->wa_api_key,
                'sender' => $request->sender,
                'urlwebhook' => null
            ];

            // Ambil domain dari general setting
            $domain = $generalsetting->domain_wa_gateway;
            if (!$domain) {
                return response()->json([
                    'success' => false,
                    'message' => 'Domain WA Gateway belum dikonfigurasi'
                ], 400);
            }

            // Gunakan protokol yang sudah ada di domain_wa_gateway, jika tidak ada default ke http://
            if (!str_starts_with($domain, 'http://') && !str_starts_with($domain, 'https://')) {
                $domain = 'http://' . $domain;
            }
            $apiUrl = rtrim($domain, '/') . '/create-device';

            // Kirim request ke API
            $response = Http::timeout(30)->post($apiUrl, $apiData);

            if ($response->successful()) {
                $responseData = $response->json();

                // Simpan device ke database
                $device = Device::create([
                    'number' => $request->sender,
                    'status' => 1
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Device berhasil ditambahkan',
                    'device' => $device,
                    'api_response' => $responseData
                ]);
            } else {
                DB::rollback();

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan device. Error: ' . $response->body()
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleDeviceStatus(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $device = Device::findOrFail($id);
            $newStatus = $device->status == 1 ? 0 : 1;

            // Jika ingin mengaktifkan device, pastikan tidak ada device lain yang aktif
            if ($newStatus == 1) {
                // Nonaktifkan semua device lain
                Device::where('id', '!=', $id)->update(['status' => 0]);
            }

            // Update status device yang dipilih
            $device->status = $newStatus;
            $device->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status device berhasil diubah',
                'device' => $device
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateQR(Request $request)
    {
        $request->validate([
            'device' => 'required|string|max:20'
        ]);

        try {
            // Ambil data dari general setting
            $generalsetting = Pengaturanumum::where('id', 1)->first();

            if (!$generalsetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'General setting tidak ditemukan'
                ], 400);
            }

            // Siapkan data untuk API
            $apiData = [
                'device' => $request->device,
                'api_key' => $generalsetting->wa_api_key,
                'force' => true
            ];

            // Ambil domain dari general setting
            $domain = $generalsetting->domain_wa_gateway;
            if (!$domain) {
                return response()->json([
                    'success' => false,
                    'message' => 'Domain WA Gateway belum dikonfigurasi'
                ], 400);
            }

            // Gunakan protokol yang sudah ada di domain_wa_gateway, jika tidak ada default ke http://
            if (!str_starts_with($domain, 'http://') && !str_starts_with($domain, 'https://')) {
                $domain = 'http://' . $domain;
            }
            $apiUrl = rtrim($domain, '/') . '/generate-qr';

            // Kirim request ke API dengan JSON body (seperti di Postman)
            $response = Http::timeout(60)
                ->asJson()
                ->post($apiUrl, $apiData);

            if ($response->successful()) {
                $responseData = $response->json();

                // Cek jika device sudah terhubung
                if (isset($responseData['msg']) && $responseData['msg'] === 'Device already connected!') {
                    // Ambil info device
                    $deviceInfo = $this->getDeviceInfo($request->device, $generalsetting);

                    if ($deviceInfo['success']) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Device sudah terhubung',
                            'data' => [
                                'status' => 'connected',
                                'device_info' => $deviceInfo['data']
                            ]
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Device sudah terhubung tetapi gagal mengambil informasi device'
                        ], 400);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'QR Code berhasil dibuat',
                    'data' => $responseData
                ]);
            } else {
                $errorResponse = $response->json();

                // Cek jika device sudah terhubung dari error response
                if (isset($errorResponse['msg']) && $errorResponse['msg'] === 'Device already connected!') {
                    // Ambil info device
                    $deviceInfo = $this->getDeviceInfo($request->device, $generalsetting);

                    if ($deviceInfo['success']) {
                        return response()->json([
                            'success' => true,
                            'message' => 'Device sudah terhubung',
                            'data' => [
                                'status' => 'connected',
                                'device_info' => $deviceInfo['data']
                            ]
                        ]);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'Device sudah terhubung tetapi gagal mengambil informasi device'
                        ], 400);
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal generate QR Code. Error: ' . $response->body()
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkDeviceStatus(Request $request)
    {
        $request->validate([
            'device' => 'required|string|max:20'
        ]);

        try {
            // Ambil data dari general setting
            $generalsetting = Pengaturanumum::where('id', 1)->first();

            if (!$generalsetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'General setting tidak ditemukan'
                ], 400);
            }

            // Ambil info device
            $deviceInfo = $this->getDeviceInfo($request->device, $generalsetting);

            if ($deviceInfo['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status device berhasil diambil',
                    'data' => [
                        'device_info' => $deviceInfo['data']
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil status device'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getDeviceInfo($deviceNumber, $generalsetting)
    {
        try {
            // Ambil domain dari general setting
            $domain = $generalsetting->domain_wa_gateway;
            // Gunakan protokol yang sudah ada di domain_wa_gateway, jika tidak ada default ke http://
            if (!str_starts_with($domain, 'http://') && !str_starts_with($domain, 'https://')) {
                $domain = 'http://' . $domain;
            }
            $apiUrl = rtrim($domain, '/') . '/info-device';

            // Siapkan data untuk API
            $apiData = [
                'api_key' => $generalsetting->wa_api_key,
                'number' => $deviceNumber
            ];

            // Kirim request ke API menggunakan POST dengan JSON body (seperti di Postman)
            // Semua gateway seharusnya support POST dengan JSON
            $response = Http::timeout(30)
                ->asJson()
                ->post($apiUrl, $apiData);

            if ($response->successful()) {
                $responseData = $response->json();

                return [
                    'success' => true,
                    'data' => $responseData
                ];
            } else {
                // Jika POST dengan JSON gagal, coba dengan GET method (untuk kompatibilitas)
                if ($response->status() == 400) {
                    $errorResponse = $response->json();
                    if (isset($errorResponse['msg']) && strpos(strtolower($errorResponse['msg']), 'invalid') !== false) {
                        // Fallback ke GET method
                        $response = Http::timeout(30)->get($apiUrl, $apiData);
                        if ($response->successful()) {
                            $responseData = $response->json();
                            return [
                                'success' => true,
                                'data' => $responseData
                            ];
                        }
                    }
                }

                return [
                    'success' => false,
                    'message' => 'Gagal mengambil informasi device: ' . ($response->json()['msg'] ?? $response->body())
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    public function testSendMessage(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'sender' => 'required|string',
                'number' => 'required|string',
                'message' => 'required|string'
            ]);

            // Ambil general setting
            $generalsetting = Pengaturanumum::first();
            if (!$generalsetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'General setting tidak ditemukan'
                ], 400);
            }

            // Buat URL API
            // Gunakan protokol yang sudah ada di domain_wa_gateway, jika tidak ada default ke http://
            $domain = $generalsetting->domain_wa_gateway;
            if (!str_starts_with($domain, 'http://') && !str_starts_with($domain, 'https://')) {
                $domain = 'http://' . $domain;
            }
            $apiUrl = rtrim($domain, '/') . '/send-message';

            // Data untuk API
            $apiData = [
                'api_key' => $generalsetting->wa_api_key,
                'sender' => $request->sender,
                'number' => $request->number,
                'message' => $request->message
            ];

            // Kirim request ke API dengan JSON format untuk konsistensi dengan endpoint lain
            $response = Http::timeout(30)
                ->asJson()
                ->post($apiUrl, $apiData);

            // Debug logging
            Log::info('Test Send Message API Request', [
                'url' => $apiUrl,
                'data' => $apiData,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Simpan pesan berhasil ke database
                Message::create([
                    'pengirim' => $request->sender,
                    'penerima' => $request->number,
                    'pesan' => $request->message,
                    'status' => 'success',
                    'message_id' => $responseData['message_id'] ?? null,
                    'error_message' => null
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $responseData['message'] ?? 'Pesan berhasil dikirim',
                    'data' => $responseData
                ]);
            } else {
                $errorResponse = $response->json();
                $statusCode = $response->status();

                // Simpan pesan gagal ke database
                Message::create([
                    'pengirim' => $request->sender,
                    'penerima' => $request->number,
                    'pesan' => $request->message,
                    'status' => 'failed',
                    'message_id' => null,
                    'error_message' => $errorResponse['message'] ?? "Gagal mengirim pesan (Status: {$statusCode})"
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorResponse['message'] ?? "Gagal mengirim pesan (Status: {$statusCode})",
                    'debug' => [
                        'status_code' => $statusCode,
                        'response_body' => $response->body(),
                        'api_url' => $apiUrl
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function disconnectDevice(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'sender' => 'required|string'
            ]);

            // Ambil general setting
            $generalsetting = Pengaturanumum::first();
            if (!$generalsetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'General setting tidak ditemukan'
                ], 400);
            }

            // Buat URL API
            $domain = $generalsetting->domain_wa_gateway;
            if (!str_starts_with($domain, 'http://') && !str_starts_with($domain, 'https://')) {
                $domain = 'http://' . $domain;
            }
            $apiUrl = $domain . '/logout-device';

            // Data untuk API
            $apiData = [
                'api_key' => $generalsetting->wa_api_key,
                'sender' => $request->sender
            ];

            // Kirim request ke API
            $response = Http::timeout(30)->post($apiUrl, $apiData);

            // Debug logging
            Log::info('Disconnect Device API Request', [
                'url' => $apiUrl,
                'data' => $apiData,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                return response()->json([
                    'success' => true,
                    'message' => $responseData['message'] ?? 'Device berhasil diputuskan',
                    'data' => $responseData
                ]);
            } else {
                $errorResponse = $response->json();
                $statusCode = $response->status();

                return response()->json([
                    'success' => false,
                    'message' => $errorResponse['message'] ?? "Gagal memutuskan device (Status: {$statusCode})",
                    'debug' => [
                        'status_code' => $statusCode,
                        'response_body' => $response->body(),
                        'api_url' => $apiUrl
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function messages()
    {
        $messages = Message::orderBy('created_at', 'desc')->paginate(20);
        return view('wagateway.messages', compact('messages'));
    }

    public function fetchGroups(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'number' => 'required|string'
            ]);

            // Ambil general setting
            $generalsetting = Pengaturanumum::first();
            if (!$generalsetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'General setting tidak ditemukan'
                ], 400);
            }

            // Buat URL API
            $domain = $generalsetting->domain_wa_gateway;
            if (!str_starts_with($domain, 'http://') && !str_starts_with($domain, 'https://')) {
                $domain = 'http://' . $domain;
            }
            $apiUrl = $domain . '/fetch-contact-group';

            // Data untuk API
            $apiData = [
                'number' => $request->number,
                'api_key' => $generalsetting->wa_api_key
            ];

            // Kirim request ke API
            $response = Http::timeout(30)->post($apiUrl, $apiData);

            // Debug logging
            Log::info('Fetch Groups API Request', [
                'url' => $apiUrl,
                'data' => $apiData,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Handle response format baru: { status, msg, data }
                if (isset($responseData['status']) && $responseData['status'] === true) {
                return response()->json([
                    'success' => true,
                        'message' => $responseData['msg'] ?? 'Groups berhasil diambil',
                        'data' => $responseData['data'] ?? []
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $responseData['msg'] ?? 'Gagal mengambil groups',
                        'data' => []
                    ], 400);
                }
            } else {
                $errorResponse = $response->json();
                $statusCode = $response->status();

                return response()->json([
                    'success' => false,
                    'message' => $errorResponse['message'] ?? "Gagal mengambil groups (Status: {$statusCode})",
                    'debug' => [
                        'status_code' => $statusCode,
                        'response_body' => $response->body(),
                        'api_url' => $apiUrl
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteDevice($id)
    {
        try {
            DB::beginTransaction();

            // Cari device berdasarkan ID
            $device = Device::findOrFail($id);

            // Ambil general setting untuk disconnect device jika diperlukan
            $generalsetting = Pengaturanumum::first();

            // Coba disconnect device dari WA Gateway API jika terhubung
            if ($generalsetting && $generalsetting->domain_wa_gateway) {
                try {
                    $domain = $generalsetting->domain_wa_gateway;
                    if (!str_starts_with($domain, 'http://') && !str_starts_with($domain, 'https://')) {
                        $domain = 'http://' . $domain;
                    }
                    $apiUrl = $domain . '/logout-device';

                    $apiData = [
                        'api_key' => $generalsetting->wa_api_key,
                        'sender' => $device->number
                    ];

                    // Kirim request ke API untuk disconnect (non-blocking jika gagal)
                    Http::timeout(10)->post($apiUrl, $apiData);
                } catch (\Exception $e) {
                    // Log error tapi lanjutkan hapus dari database
                    Log::warning('Gagal disconnect device dari API sebelum hapus', [
                        'device_id' => $id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Hapus device dari database
            $deviceNumber = $device->number;
            $device->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Device berhasil dihapus',
                'device_number' => $deviceNumber
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Device tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
