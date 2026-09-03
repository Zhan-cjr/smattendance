@extends('layouts.app')
@section('titlepage', 'WhatsApp Gateway')
@section('navigasi')
    <span>WhatsApp Gateway Dashboard</span>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">


            <!-- Form Add Device -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Tambah Device Baru</h5>
                        </div>
                        <div class="card-body">
                            <form id="addDeviceForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="sender" class="form-label">Nomor WhatsApp</label>
                                    <input type="text" class="form-control" id="sender" name="sender" placeholder="6282298859671" required>
                                    <small class="form-text text-muted">Masukkan nomor WhatsApp dengan format internasional (contoh:
                                        6282298859671)</small>
                                </div>
                                <button type="submit" class="btn btn-primary" id="btnAddDevice">
                                    <i class="ti ti-plus"></i> Tambah Device
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Informasi Konfigurasi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <p><strong>Domain WA Gateway:</strong></p>
                                    <code>{{ $generalsetting->domain_wa_gateway ?? 'Belum dikonfigurasi' }}</code>
                                </div>
                                <div class="col-12 mt-2">
                                    <p><strong>API Key:</strong></p>
                                    <code>{{ $generalsetting->wa_api_key ? '***' . substr($generalsetting->wa_api_key, -4) : 'Belum dikonfigurasi' }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Device -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Daftar Device</h5>
                            <div class="card-tools">
                                <a href="{{ route('wagateway.messages') }}" class="btn btn-sm btn-info me-2">
                                    <i class="ti ti-message"></i> Riwayat Pesan
                                </a>

                                <button class="btn btn-sm btn-primary" id="refreshDeviceInfo">
                                    <i class="ti ti-refresh"></i> Refresh Info
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="devicesTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor WhatsApp</th>
                                            <th>Status</th>
                                            <th>Status Koneksi</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($devices as $index => $device)
                                            <tr data-device-number="{{ $device->number }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $device->number }}</td>
                                                <td>
                                                    <span class="badge {{ $device->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $device->status == 1 ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary" id="connection-status-{{ $device->id }}">
                                                        <i class="ti ti-loader-2"></i> Checking...
                                                    </span>
                                                </td>
                                                <td>{{ $device->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <button
                                                        class="btn btn-sm {{ $device->status == 1 ? 'btn-warning' : 'btn-success' }} toggle-status me-1"
                                                        data-id="{{ $device->id }}" data-status="{{ $device->status }}">
                                                        <i class="ti {{ $device->status == 1 ? 'ti-toggle-left' : 'ti-toggle-right' }}"></i>
                                                        {{ $device->status == 1 ? 'Nonaktifkan' : 'Aktifkan' }}
                                                    </button>
                                                    <button class="btn btn-sm btn-info generate-qr me-1" data-device="{{ $device->number }}"
                                                        data-id="{{ $device->id }}">
                                                        <i class="ti ti-qrcode"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-warning test-send-message me-1" data-device="{{ $device->number }}"
                                                        data-id="{{ $device->id }}">
                                                        <i class="ti ti-send"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-success fetch-groups-device me-1" data-device="{{ $device->number }}"
                                                        data-id="{{ $device->id }}" title="Fetch Groups">
                                                        <i class="ti ti-users"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger disconnect-device me-1" data-device="{{ $device->number }}"
                                                        data-id="{{ $device->id }}">
                                                        <i class="ti ti-logout"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-device" data-device="{{ $device->number }}"
                                                        data-id="{{ $device->id }}" title="Hapus Device">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Belum ada device yang terdaftar</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal QR Code -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">Generate QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="qrCodeContent">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Sedang memproses QR Code...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="downloadQR" style="display: none;">
                        <i class="ti ti-download"></i> Download QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Test Kirim Pesan -->
    <div class="modal fade" id="testSendMessageModal" tabindex="-1" aria-labelledby="testSendMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testSendMessageModalLabel">Test Kirim Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="testSendMessageForm">
                        <div class="mb-3">
                            <label for="senderNumber" class="form-label">Nomor Pengirim</label>
                            <input type="text" class="form-control" id="senderNumber" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="targetNumber" class="form-label">Nomor Tujuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="targetNumber" placeholder="62888xxxx" required>
                        </div>
                        <div class="mb-3">
                            <label for="messageText" class="form-label">Pesan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="messageText" rows="3" placeholder="Masukkan pesan yang ingin dikirim..." required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnTestSendMessage">
                        <i class="ti ti-send"></i> Kirim Pesan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Disconnect Device -->
    <div class="modal fade" id="disconnectDeviceModal" tabindex="-1" aria-labelledby="disconnectDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disconnectDeviceModalLabel">Konfirmasi Disconnect Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle"></i>
                        <strong>Peringatan!</strong> Apakah Anda yakin ingin memutuskan koneksi device ini?
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Device:</label>
                        <input type="text" class="form-control" id="disconnectDeviceNumber" readonly>
                    </div>
                    <p class="text-muted">
                        Device akan terputus dari WhatsApp dan tidak dapat mengirim pesan lagi.
                        Untuk menggunakan kembali, Anda perlu melakukan scan QR code ulang.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDisconnect">
                        <i class="ti ti-logout"></i> Ya, Disconnect Device
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Device -->
    <div class="modal fade" id="deleteDeviceModal" tabindex="-1" aria-labelledby="deleteDeviceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDeviceModalLabel">Konfirmasi Hapus Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ti ti-alert-triangle"></i>
                        <strong>Peringatan!</strong> Apakah Anda yakin ingin menghapus device ini?
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Device:</label>
                        <input type="text" class="form-control" id="deleteDeviceNumber" readonly>
                        <input type="hidden" id="deleteDeviceId">
                    </div>
                    <p class="text-muted">
                        Device akan dihapus secara permanen dari sistem dan tidak dapat dikembalikan.
                        Tindakan ini akan memutuskan koneksi device dan menghapus semua data terkait.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                        <i class="ti ti-trash"></i> Ya, Hapus Device
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Groups -->
    <div class="modal fade" id="groupsModal" tabindex="-1" aria-labelledby="groupsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="groupsModalLabel">Daftar Groups WhatsApp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="groupsContent">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat groups...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <style>
        .ti-loader-2 {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .badge {
            font-size: 0.75rem;
        }
    </style>
    <script>
        $(document).ready(function() {
            // Load device info for all devices
            loadAllDeviceInfo();

            // Auto-refresh device info every 30 seconds
            setInterval(function() {
                loadAllDeviceInfo();
            }, 30000);

            // Handle refresh device info button
            $('#refreshDeviceInfo').on('click', function() {
                const btn = $(this);
                const originalHtml = btn.html();

                btn.html('<i class="ti ti-loader-2"></i> Refreshing...').prop('disabled', true);

                // Reset all status badges to loading
                $('[id^="connection-status-"]').removeClass('bg-success bg-danger').addClass('bg-secondary')
                    .html('<i class="ti ti-loader-2"></i> Checking...');

                // Load all device info
                loadAllDeviceInfo();

                // Re-enable button after 2 seconds
                setTimeout(function() {
                    btn.html(originalHtml).prop('disabled', false);
                }, 2000);
            });

            // Handle form submission untuk add device
            $('#addDeviceForm').on('submit', function(e) {
                e.preventDefault();

                const btnAddDevice = $('#btnAddDevice');
                const originalText = btnAddDevice.html();
                const form = $(this);

                // Disable button dan show loading
                btnAddDevice.prop('disabled', true).html('<i class="ti ti-loader-2"></i> Menambahkan...');

                // Loading sudah ditampilkan di button

                $.ajax({
                    url: '{{ route('wagateway.add-device') }}',
                    type: 'POST',
                    data: form.serialize(),
                    timeout: 30000, // 30 detik timeout
                    success: function(response) {
                        if (response.success) {
                            // Reset form
                            form[0].reset();
                            // Reload page to show new device
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menambahkan device';

                        if (xhr.status === 422) {
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = xhr.responseJSON.errors;
                                errorMessage = Object.values(errors).flat().join('\n');
                            }
                        } else if (xhr.status === 500) {
                            errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                        } else if (xhr.status === 0) {
                            errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        alert('Error: ' + errorMessage);
                    },
                    complete: function() {
                        // Re-enable button
                        btnAddDevice.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Handle toggle status device
            $('.toggle-status').on('click', function() {
                const deviceId = $(this).data('id');
                const currentStatus = $(this).data('status');
                const button = $(this);
                const originalText = button.html();

                // Disable button dan show loading
                button.prop('disabled', true).html('<i class="ti ti-loader-2"></i> Memproses...');

                $.ajax({
                    url: '{{ route('wagateway.toggle-device-status', ':id') }}'.replace(':id', deviceId),
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    timeout: 15000, // 15 detik timeout
                    success: function(response) {
                        if (response.success) {
                            // Update UI without refresh
                            updateDeviceStatusInUI(deviceId, response.device.status);
                        } else {
                            // Show error notification
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat mengubah status device';

                        if (xhr.status === 500) {
                            errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                        } else if (xhr.status === 0) {
                            errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        alert('Error: ' + errorMessage);
                    },
                    complete: function() {
                        // Re-enable button
                        button.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Handle generate QR code
            $('.generate-qr').on('click', function() {
                const deviceNumber = $(this).data('device');
                const deviceId = $(this).data('id');
                const button = $(this);
                const originalText = button.html();

                // Disable button dan show loading
                button.prop('disabled', true).html('<i class="ti ti-loader-2"></i>');

                // Show modal
                $('#qrCodeModal').modal('show');

                // Reset modal content
                $('#qrCodeContent').html(`
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Sedang memproses QR Code...</p>
                `);
                $('#downloadQR').hide();

                // Generate QR code
                generateQRCode(deviceNumber, deviceId, button, originalText);
            });

            // Function to generate QR code
            function generateQRCode(deviceNumber, deviceId, button, originalText) {
                $.ajax({
                    url: '{{ route('wagateway.generate-qr') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        device: deviceNumber
                    },
                    timeout: 60000, // 60 detik timeout
                    success: function(response) {
                        if (response.success) {
                            if (response.data.qrcode) {
                                // QR code generated successfully
                                $('#qrCodeContent').html(`
                                    <div class="alert alert-success">
                                        <i class="ti ti-check-circle"></i> ${response.data.message || 'QR Code berhasil dibuat'}
                                    </div>
                                    <img src="${response.data.qrcode}" class="img-fluid" style="max-width: 300px;" alt="QR Code">
                                    <p class="mt-2 text-muted">Scan QR Code ini dengan WhatsApp untuk menghubungkan device</p>
                                    <div class="mt-3">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Checking...</span>
                                        </div>
                                        <small class="text-muted ms-2">Menunggu koneksi...</small>
                                    </div>
                                `);
                                $('#downloadQR').show().off('click').on('click', function() {
                                    downloadQRCode(response.data.qrcode, deviceNumber);
                                });

                                // Start checking device status
                                checkDeviceStatus(deviceNumber, deviceId);
                            } else if (response.data.status === 'processing') {
                                // Still processing, check again after 2 seconds
                                setTimeout(() => {
                                    generateQRCode(deviceNumber, deviceId, button, originalText);
                                }, 2000);
                            } else if (response.data.status === 'connected' || response.data.msg === 'Device already connected!' ||
                                response.message === 'Device sudah terhubung') {
                                // Device already connected
                                let deviceInfoHtml = '';

                                if (response.data.device_info && response.data.device_info.info && response.data.device_info.info
                                    .length > 0) {
                                    const deviceInfo = response.data.device_info.info[0];
                                    deviceInfoHtml = `
                                        <div class="alert alert-success">
                                            <i class="ti ti-check-circle"></i> Device sudah terhubung!
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Nomor:</strong><br>
                                                <span class="text-primary">${deviceInfo.body || deviceNumber}</span>
                                            </div>
                                            <div class="col-6">
                                                <strong>Status:</strong><br>
                                                <span class="badge ${deviceInfo.status === 'Connected' ? 'bg-success' : 'bg-warning'}">${deviceInfo.status || 'Connected'}</span>
                                            </div>
                                        </div>
                                    `;
                                } else {
                                    deviceInfoHtml = `
                                        <div class="alert alert-info">
                                            <i class="ti ti-info-circle"></i> Device sudah terhubung!
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Nomor:</strong><br>
                                                <span class="text-primary">${deviceNumber}</span>
                                            </div>
                                            <div class="col-6">
                                                <strong>Status:</strong><br>
                                                <span class="badge bg-success">Connected</span>
                                            </div>
                                        </div>
                                    `;
                                }

                                $('#qrCodeContent').html(deviceInfoHtml);
                                $('#downloadQR').hide();
                            } else {
                                $('#qrCodeContent').html(`
                                    <div class="alert alert-warning">
                                        <i class="ti ti-alert-triangle"></i> ${response.data.msg || response.message || 'Terjadi kesalahan'}
                                    </div>
                                `);
                            }
                        } else {
                            $('#qrCodeContent').html(`
                                <div class="alert alert-danger">
                                    <i class="ti ti-x-circle"></i> ${response.message || 'Gagal generate QR Code'}
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat generate QR Code';

                        if (xhr.status === 500) {
                            errorMessage = 'Terjadi kesalahan server. Silakan coba lagi.';
                        } else if (xhr.status === 0) {
                            errorMessage = 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        $('#qrCodeContent').html(`
                            <div class="alert alert-danger">
                                <i class="ti ti-x-circle"></i> ${errorMessage}
                            </div>
                        `);
                    },
                    complete: function() {
                        // Re-enable button
                        button.prop('disabled', false).html(originalText);
                    }
                });
            }

            // Function to check device status
            function checkDeviceStatus(deviceNumber, deviceId) {
                $.ajax({
                    url: '{{ route('wagateway.check-device-status') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        device: deviceNumber
                    },
                    timeout: 30000,
                    success: function(response) {
                        if (response.success && response.data.device_info && response.data.device_info.info && response.data
                            .device_info.info.length > 0) {
                            const deviceInfo = response.data.device_info.info[0];

                            // Check if device is actually connected
                            if (deviceInfo.status === 'Connected') {
                                // Show device info instead of QR code
                                $('#qrCodeContent').html(`
                                    <div class="alert alert-success">
                                        <i class="ti ti-check-circle"></i> Device berhasil terhubung!
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Nomor:</strong><br>
                                            <span class="text-primary">${deviceInfo.body || deviceNumber}</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Status:</strong><br>
                                            <span class="badge ${deviceInfo.status === 'Connected' ? 'bg-success' : 'bg-warning'}">${deviceInfo.status || 'Connected'}</span>
                                        </div>
                                    </div>
                                `);
                                $('#downloadQR').hide();

                                // Update device status in table
                                updateDeviceStatusInTable(deviceId, deviceInfo.status);

                                // Stop checking status
                                return;
                            }
                        }

                        // Device not connected yet, check again after 3 seconds
                        setTimeout(() => {
                            checkDeviceStatus(deviceNumber, deviceId);
                        }, 3000);
                    },
                    error: function(xhr) {
                        // Continue checking even if there's an error
                        setTimeout(() => {
                            checkDeviceStatus(deviceNumber, deviceId);
                        }, 5000);
                    }
                });
            }

            // Function to update device status in table
            function updateDeviceStatusInTable(deviceId, status) {
                const row = $(`button[data-id="${deviceId}"]`).closest('tr');
                const statusBadge = row.find('.badge');

                if (status === 'Connected') {
                    statusBadge.removeClass('bg-danger').addClass('bg-success').text('Aktif');
                } else if (status === 'Disconnect') {
                    statusBadge.removeClass('bg-success').addClass('bg-danger').text('Tidak Aktif');
                }
            }

            // Function to update device status in UI without refresh
            function updateDeviceStatusInUI(deviceId, newStatus) {
                const row = $(`button[data-id="${deviceId}"]`).closest('tr');
                const statusBadge = row.find('.badge');
                const toggleButton = row.find('.toggle-status');

                // Update status badge
                if (newStatus == 1) {
                    statusBadge.removeClass('bg-danger').addClass('bg-success').text('Aktif');
                } else {
                    statusBadge.removeClass('bg-success').addClass('bg-danger').text('Tidak Aktif');
                }

                // Update toggle button
                if (newStatus == 1) {
                    toggleButton.removeClass('btn-success').addClass('btn-warning')
                        .data('status', 1)
                        .html('<i class="ti ti-toggle-left"></i> Nonaktifkan');
                } else {
                    toggleButton.removeClass('btn-warning').addClass('btn-success')
                        .data('status', 0)
                        .html('<i class="ti ti-toggle-right"></i> Aktifkan');
                }

                // Update all other devices to inactive if current device is active
                if (newStatus == 1) {
                    $('.toggle-status').not(toggleButton).each(function() {
                        const otherButton = $(this);
                        const otherRow = otherButton.closest('tr');
                        const otherBadge = otherRow.find('.badge');

                        otherBadge.removeClass('bg-success').addClass('bg-danger').text('Tidak Aktif');
                        otherButton.removeClass('btn-warning').addClass('btn-success')
                            .data('status', 0)
                            .html('<i class="ti ti-toggle-right"></i> Aktifkan');
                    });
                }
            }

            // Handle test send message button click
            $(document).on('click', '.test-send-message', function() {
                const deviceNumber = $(this).data('device');

                // Set sender number
                $('#senderNumber').val(deviceNumber);

                // Clear form
                $('#targetNumber').val('');
                $('#messageText').val('');

                // Show modal
                $('#testSendMessageModal').modal('show');
            });

            // Handle test send message form submission
            $('#btnTestSendMessage').on('click', function() {
                const btn = $(this);
                const originalHtml = btn.html();

                // Validate form
                const targetNumber = $('#targetNumber').val().trim();
                const messageText = $('#messageText').val().trim();

                if (!targetNumber || !messageText) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Nomor tujuan dan pesan harus diisi',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Show loading
                btn.html('<i class="ti ti-loader-2"></i> Mengirim...').prop('disabled', true);

                // Send message
                $.ajax({
                    url: '{{ route('wagateway.test-send-message') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sender: $('#senderNumber').val(),
                        number: targetNumber,
                        message: messageText
                    },
                    timeout: 30000,
                    success: function(response) {
                        btn.html(originalHtml).prop('disabled', false);

                        // Debug info
                        console.log('Success response:', response);

                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                timer: 5000,
                                timerProgressBar: true
                            });

                            // Close modal
                            $('#testSendMessageModal').modal('hide');
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat mengirim pesan',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.html(originalHtml).prop('disabled', false);

                        let errorMessage = 'Terjadi kesalahan saat mengirim pesan';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();
                            errorMessage = errorMessages.join(', ');
                        }

                        // Debug info
                        console.log('Error response:', xhr.responseJSON);
                        console.log('Status:', xhr.status);
                        console.log('Response text:', xhr.responseText);

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Handle disconnect device button click
            $(document).on('click', '.disconnect-device', function() {
                const deviceNumber = $(this).data('device');

                // Set device number
                $('#disconnectDeviceNumber').val(deviceNumber);

                // Show modal
                $('#disconnectDeviceModal').modal('show');
            });

            // Handle confirm disconnect button click
            $('#btnConfirmDisconnect').on('click', function() {
                const btn = $(this);
                const originalHtml = btn.html();
                const deviceNumber = $('#disconnectDeviceNumber').val();

                // Show loading
                btn.html('<i class="ti ti-loader-2"></i> Memutuskan...').prop('disabled', true);

                // Disconnect device
                $.ajax({
                    url: '{{ route('wagateway.disconnect-device') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sender: deviceNumber
                    },
                    timeout: 30000,
                    success: function(response) {
                        btn.html(originalHtml).prop('disabled', false);

                        // Debug info
                        console.log('Disconnect response:', response);

                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                timer: 5000,
                                timerProgressBar: true
                            });

                            // Close modal
                            $('#disconnectDeviceModal').modal('hide');

                            // Refresh device info
                            loadAllDeviceInfo();
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat memutuskan device',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.html(originalHtml).prop('disabled', false);

                        let errorMessage = 'Terjadi kesalahan saat memutuskan device';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();
                            errorMessage = errorMessages.join(', ');
                        }

                        // Debug info
                        console.log('Disconnect error response:', xhr.responseJSON);
                        console.log('Status:', xhr.status);
                        console.log('Response text:', xhr.responseText);

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Handle delete device button click
            $(document).on('click', '.delete-device', function() {
                const deviceNumber = $(this).data('device');
                const deviceId = $(this).data('id');

                // Set device number and ID
                $('#deleteDeviceNumber').val(deviceNumber);
                $('#deleteDeviceId').val(deviceId);

                // Show modal
                $('#deleteDeviceModal').modal('show');
            });

            // Handle confirm delete button click
            $('#btnConfirmDelete').on('click', function() {
                const btn = $(this);
                const originalHtml = btn.html();
                const deviceId = $('#deleteDeviceId').val();

                // Show loading
                btn.html('<i class="ti ti-loader-2"></i> Menghapus...').prop('disabled', true);

                // Delete device
                $.ajax({
                    url: '{{ route('wagateway.delete-device', ':id') }}'.replace(':id', deviceId),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    timeout: 30000,
                    success: function(response) {
                        btn.html(originalHtml).prop('disabled', false);

                        // Debug info
                        console.log('Delete response:', response);

                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                timer: 3000,
                                timerProgressBar: true
                            }).then(() => {
                                // Close modal
                                $('#deleteDeviceModal').modal('hide');

                                // Reload page to refresh device list
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message || 'Terjadi kesalahan saat menghapus device',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.html(originalHtml).prop('disabled', false);

                        let errorMessage = 'Terjadi kesalahan saat menghapus device';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();
                            errorMessage = errorMessages.join(', ');
                        } else if (xhr.status === 404) {
                            errorMessage = 'Device tidak ditemukan';
                        }

                        // Debug info
                        console.log('Delete error response:', xhr.responseJSON);
                        console.log('Status:', xhr.status);
                        console.log('Response text:', xhr.responseText);

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // Handle fetch groups per device button click
            $(document).on('click', '.fetch-groups-device', function() {
                const deviceNumber = $(this).data('device');
                const btn = $(this);
                const originalHtml = btn.html();

                // Show loading
                btn.html('<i class="ti ti-loader-2"></i>').prop('disabled', true);

                // Show modal
                $('#groupsModal').modal('show');

                // Fetch groups for specific device
                $.ajax({
                    url: '{{ route('wagateway.fetch-groups') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        number: deviceNumber
                    },
                    timeout: 30000,
                    success: function(response) {
                        btn.html(originalHtml).prop('disabled', false);

                        // Debug info
                        console.log('Fetch groups response:', response);

                        if (response.success && response.data) {
                            displayGroups(response.data);
                        } else {
                            $('#groupsContent').html(`
                                <div class="alert alert-danger">
                                    <i class="ti ti-x-circle"></i> ${response.message || 'Gagal mengambil groups'}
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        btn.html(originalHtml).prop('disabled', false);

                        let errorMessage = 'Terjadi kesalahan saat mengambil groups';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorMessages = Object.values(errors).flat();
                            errorMessage = errorMessages.join(', ');
                        }

                        // Debug info
                        console.log('Fetch groups error response:', xhr.responseJSON);
                        console.log('Status:', xhr.status);
                        console.log('Response text:', xhr.responseText);

                        $('#groupsContent').html(`
                            <div class="alert alert-danger">
                                <i class="ti ti-x-circle"></i> ${errorMessage}
                            </div>
                        `);
                    }
                });
            });

            // Function to load device info for all devices
            function loadAllDeviceInfo() {
                $('tr[data-device-number]').each(function() {
                    const deviceNumber = $(this).data('device-number');
                    const deviceId = $(this).find('.toggle-status').data('id');
                    loadDeviceInfo(deviceNumber, deviceId);
                });
            }

            // Function to load device info for a specific device
            function loadDeviceInfo(deviceNumber, deviceId) {
                $.ajax({
                    url: '{{ route('wagateway.check-device-status') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        device: deviceNumber
                    },
                    timeout: 10000,
                    success: function(response) {
                        if (response.success && response.data.device_info && response.data.device_info.info && response.data
                            .device_info.info.length > 0) {
                            const deviceInfo = response.data.device_info.info[0];

                            // Update connection status
                            const statusBadge = $(`#connection-status-${deviceId}`);
                            if (deviceInfo.status === 'Connected') {
                                statusBadge.removeClass('bg-secondary bg-danger').addClass('bg-success')
                                    .html('<i class="ti ti-check-circle"></i> Connected');
                            } else {
                                statusBadge.removeClass('bg-secondary bg-success').addClass('bg-danger')
                                    .html('<i class="ti ti-x-circle"></i> Disconnected');
                            }

                        } else {
                            // Device not found or error
                            $(`#connection-status-${deviceId}`).removeClass('bg-secondary bg-success').addClass('bg-danger')
                                .html('<i class="ti ti-x-circle"></i> Not Found');
                        }
                    },
                    error: function(xhr) {
                        // Error occurred
                        $(`#connection-status-${deviceId}`).removeClass('bg-secondary bg-success').addClass('bg-danger')
                            .html('<i class="ti ti-x-circle"></i> Error');
                    }
                });
            }

            // Handle fetch groups button click
            $('#fetchGroupsBtn').on('click', function() {
                const btn = $(this);
                const originalHtml = btn.html();

                // Show loading
                btn.html('<i class="ti ti-loader-2"></i> Loading...').prop('disabled', true);

                // Show modal
                $('#groupsModal').modal('show');

                // Get first available device number
                const firstDeviceNumber = $('tr[data-device-number]').first().data('device-number');

                if (!firstDeviceNumber) {
                    btn.html(originalHtml).prop('disabled', false);
                    $('#groupsContent').html(`
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle"></i> Tidak ada device yang tersedia untuk mengambil groups
                        </div>
                    `);
                    return;
                }

                // Fetch groups
                $.ajax({
                    url: '{{ route('wagateway.fetch-groups') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        number: firstDeviceNumber
                    },
                    timeout: 30000,
                    success: function(response) {
                        btn.html(originalHtml).prop('disabled', false);

                        // Debug info
                        console.log('Fetch groups response:', response);

                        if (response.success && response.data) {
                            displayGroups(response.data);
                        } else {
                            $('#groupsContent').html(`
                                <div class="alert alert-danger">
                                    <i class="ti ti-x-circle"></i> ${response.message || 'Gagal mengambil groups'}
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        btn.html(originalHtml).prop('disabled', false);

                        let errorMessage = 'Terjadi kesalahan saat mengambil groups';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        // Debug info
                        console.log('Fetch groups error response:', xhr.responseJSON);
                        console.log('Status:', xhr.status);
                        console.log('Response text:', xhr.responseText);

                        $('#groupsContent').html(`
                            <div class="alert alert-danger">
                                <i class="ti ti-x-circle"></i> ${errorMessage}
                            </div>
                        `);
                    }
                });
            });

            // Function to display groups
            function displayGroups(data) {
                let html = '';

                if (data.groups && data.groups.length > 0) {
                    html += `
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Total Groups:</strong> ${data.total_groups || data.groups.length}
                            </div>
                            <div class="col-6">
                                <strong>Device:</strong> ${data.device_number || 'Unknown'}
                            </div>
                            ${data.total_new_contacts !== undefined ? `
                                <div class="col-6 mt-2">
                                    <strong>New Contacts:</strong> ${data.total_new_contacts}
                                </div>
                                ` : ''}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Group ID</th>
                                        <th>Group Name</th>
                                        <th>Phonebook Name</th>
                                        <th>Phonebook ID</th>
                                        <th>Participants</th>
                                        <th>Contacts</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

                    data.groups.forEach((group, index) => {
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td><code>${group.group_id || '-'}</code></td>
                                <td><strong>${group.group_name || '-'}</strong></td>
                                <td>${group.phonebook_name || '-'}</td>
                                <td><span class="badge bg-info">${group.phonebook_id || '-'}</span></td>
                                <td><span class="badge bg-success">${group.total_participants || 0}</span></td>
                                <td><span class="badge bg-secondary">${group.contacts_count || 0}</span></td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i> Tidak ada groups ditemukan
                        </div>
                    `;
                }

                $('#groupsContent').html(html);
            }

            // Function to download QR code
            function downloadQRCode(qrCodeData, deviceNumber) {
                const link = document.createElement('a');
                link.href = qrCodeData;
                link.download = `qr-code-${deviceNumber}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    </script>
@endpush
