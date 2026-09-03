@extends('layouts.app')
@section('titlepage', 'Update Aplikasi')

@section('content')
@section('navigasi')
    <span>Update Aplikasi</span>
@endsection

<div class="row mb-4">
    <!-- Status Card -->
    <div class="col-lg-12">
        <div class="card overflow-hidden shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-3 d-flex align-items-center mb-3 mb-md-0">
                        <div class="avatar avatar-md bg-label-primary rounded me-3">
                            <i class="ti ti-server fs-3"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small text-uppercase fw-bold">Status Aplikasi</span>
                            <span class="badge bg-success bg-opacity-10 text-success fs-6 fw-bold">
                                <i class="ti ti-circle-check me-1"></i> System Active
                            </span>
                        </div>
                    </div>
                    
                    <div class="col-md-5 text-center mb-3 mb-md-0 border-start border-end border-light">
                        <span class="d-block text-muted small text-uppercase fw-bold mb-1">Versi Saat Ini</span>
                        <h2 class="mb-0 fw-bold text-primary">{{ $currentVersion }}</h2>
                    </div>

                    <div class="col-md-4 text-end">
                        <div class="mb-2">
                             @php
                                $lastUpdate = $updateLogs->where('status', 'success')->first();
                            @endphp
                            <span class="text-muted small">Terakhir diupdate: 
                                <span class="fw-medium text-dark">
                                    {{ $lastUpdate ? ($lastUpdate->completed_at ? $lastUpdate->completed_at->format('d M Y H:i') : $lastUpdate->created_at->format('d M Y H:i')) : '-' }}
                                </span>
                            </span>
                        </div>
                        <button type="button" class="btn btn-primary px-4" onclick="checkUpdate()">
                            <i class="ti ti-refresh loading-icon me-2"></i> Cek Update
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Available Section (Hidden by Default) -->
<div class="row mb-4" id="updateInfo" style="display: none;">
    <div class="col-12">
        <div class="card border-0 shadow-sm border-start border-5 border-info">
            <div class="card-body p-4 bg-label-info bg-opacity-25" id="updateContent">
                <!-- Content injected via JS -->
            </div>
        </div>
    </div>
</div>

<!-- History Section -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom bg-transparent py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="ti ti-history me-2"></i>Riwayat Pembaruan</h5>
                <a href="{{ route('update.history') }}" class="btn btn-sm btn-label-secondary">
                    Lihat Semua
                </a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-bold">Versi</th>
                            <th class="fw-bold">Status</th>
                            <th class="fw-bold">Dilakukan Oleh</th>
                            <th class="fw-bold">Tanggal</th>
                            <th class="fw-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($updateLogs as $log)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $log->version }}</span>
                                        <small class="text-muted">Prev: {{ $log->previous_version ?? '-' }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if ($log->status == 'success')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Success</span>
                                    @elseif($log->status == 'failed')
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Failed</span>
                                    @elseif($log->status == 'downloading')
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">Downloading</span>
                                    @elseif($log->status == 'installing')
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Installing</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ substr($log->user->name ?? 'S', 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium text-heading">{{ $log->user->name ?? '-' }}</span>
                                            <small class="text-muted">Admin</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $log->created_at->format('d M Y, H:i') }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('update.log', $log->id) }}" class="btn btn-icon btn-label-info btn-sm rounded-circle" data-bs-toggle="tooltip" title="Log Detail">
                                        <i class="ti ti-file-text"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="bg-label-secondary p-3 rounded-circle mb-3">
                                            <i class="ti ti-list-details fs-1"></i>
                                        </div>
                                        <h6 class="text-muted mb-0">Belum ada riwayat pembaruan</h6>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Progress Modal (Polished) -->
<div class="modal fade" id="progressModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fs-5">
                    <i class="ti ti-refresh me-2"></i>System Update
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="closeProgressBtn" style="display: none;"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Progress & Status -->
                <div class="text-center mb-4">
                    <h4 class="mb-1" id="statusText">Inisialisasi...</h4>
                    <p class="text-muted small mb-3">Mohon jangan tutup halaman ini.</p>
                    
                    <div class="progress" style="height: 10px; border-radius: 10px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar" id="progressBar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="mt-2 text-end">
                        <span class="fw-bold text-primary" id="progressText">0%</span>
                    </div>
                </div>

                <!-- Terminal -->
                <div class="card bg-dark border-0">
                    <div class="card-header bg-transparent border-bottom border-secondary py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="text-white small font-monospace"><i class="ti ti-terminal me-2"></i>Console Log</span>
                        <button class="btn btn-xs btn-link text-secondary text-decoration-none p-0" onclick="clearTerminal()">Clear</button>
                    </div>
                    <div class="card-body p-3 bg-black rounded-bottom" id="terminalLog" style="height: 250px; overflow-y: auto; font-family: 'Consolas', 'Monaco', monospace; font-size: 13px; color: #a9b7c6;">
                        <div class="text-success">&gt; System initialized...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                 <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal" id="cancelBtn" style="display: none;">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    // --- Existing Core Logic Preserved ---

    function checkUpdate() {
        $('#progressModal').modal('show');
        updateProgress(0, 'Sedang mengecek pembaruan...', '');
        
        // Add loading state to button if needed, but modal covers it
        
        $.ajax({
            url: '{{ route('update.check') }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.has_update) {
                    $('#progressModal').modal('hide');
                    showUpdateInfo(response);
                } else {
                    $('#progressModal').modal('hide');
                    Swal.fire({
                        icon: 'info',
                        title: 'Sistem Mutakhir',
                        text: 'Aplikasi sudah menggunakan versi terbaru.',
                        confirmButtonText: 'OK',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    }).then(() => {
                         $('#progressModal').modal('hide');
                    });
                }
            },
            error: function(xhr) {
                $('#progressModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengecek',
                    text: xhr.responseJSON?.error || 'Tidak dapat terhubung ke server update.',
                    confirmButtonText: 'Tutup',
                     customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            }
        });
    }

    function showUpdateInfo(data) {
        let html = `
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1 text-info fw-bold">Update Tersedia: v${data.latest_version}</h4>
                     <p class="text-muted mb-0">Versi Anda saat ini: <span class="fw-bold">${data.current_version}</span></p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                     <span class="badge bg-label-info p-2 rounded">
                        <i class="ti ti-file-zip me-1"></i> ${formatFileSize(data.update.file_size)}
                     </span>
                </div>
            </div>
            <hr class="my-3 border-secondary border-opacity-10">
        `;

        if (data.update) {
            html += `
                <div class="mb-4">
                   <h6 class="fw-bold text-dark"><i class="ti ti-notes me-2"></i>${data.update.title || 'Catatan Rilis'}</h6>
                    ${data.update.description ? `<p class="text-muted mb-3">${data.update.description}</p>` : ''}
                    
                    ${data.update.changelog ? `
                        <div class="bg-body p-3 rounded border">
                            <h6 class="text-uppercase small fw-bold text-muted mb-2">Changelog</h6>
                            <pre class="mb-0 text-muted" style="white-space: pre-wrap; font-family: inherit; font-size: 0.9rem;">${data.update.changelog}</pre>
                        </div>
                    ` : ''}
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary" onclick="updateNow('${data.update.version}')">
                        <i class="ti ti-download me-1"></i> Update & Install
                    </button>
                     <button class="btn btn-label-secondary" onclick="downloadUpdate('${data.update.version}')">
                        <i class="ti ti-cloud-download me-1"></i> Download Only
                    </button>
                </div>
            `;
        }

        $('#updateContent').html(html);
        $('#updateInfo').slideDown(400); // Smooth animation
        
        $('html, body').animate({
            scrollTop: $('#updateInfo').offset().top - 100
        }, 600);
    }

    function formatFileSize(bytes) {
        if (!bytes) return '-';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }

    let progressInterval = null;
    let updateLogId = null;

    function updateNow(version) {
        Swal.fire({
            title: 'Konfirmasi Update',
            text: 'Pastikan Anda telah melakukan backup database sebelum melanjutkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                updateProgress(0, 'Memulai proses update...', '');
                $('#progressModal').modal('show');
                $('#cancelBtn').hide();
                $('#closeProgressBtn').hide();

                $.ajax({
                    url: `/update/${version}/update-now`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // FIX: Added 'already_updated' handling
                        if (response.already_updated) {
                            $('#progressModal').modal('hide');
                            Swal.fire({
                                icon: 'info',
                                title: 'Info',
                                text: response.message,
                                confirmButtonText: 'OK',
                                customClass: { confirmButton: 'btn btn-primary' },
                                buttonsStyling: false
                            }).then(() => {
                                $('#progressModal').modal('hide');
                            });
                        } else if (response.update_log_id) {
                            updateLogId = response.update_log_id;
                            startProgressPolling(updateLogId);
                        } else {
                            setTimeout(() => {
                                checkUpdateComplete(version);
                            }, 2000);
                        }
                    },
                    error: function(xhr) {
                         $('#progressModal').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memulai',
                            text: xhr.responseJSON?.error || 'Gagal mengupdate aplikasi',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'btn btn-primary' },
                            buttonsStyling: false
                        });
                    }
                });
            }
        });
    }

    function startProgressPolling(logId) {
        if (progressInterval) clearInterval(progressInterval);

        progressInterval = setInterval(() => {
            $.ajax({
                url: `/update/progress/${logId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        const data = response.data;
                        updateProgress(
                            data.progress_percentage || 0,
                            data.message || data.status,
                            data.progress_log || ''
                        );

                        if (data.status === 'success' || data.status === 'failed') {
                            clearInterval(progressInterval);
                            progressInterval = null;

                            if (data.status === 'success') {
                                $('#cancelBtn').show();
                                $('#closeProgressBtn').show();
                                setTimeout(() => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Update Berhasil!',
                                        text: 'Sistem telah diperbarui. Halaman akan dimuat ulang.',
                                        confirmButtonText: 'Reload Sekarang',
                                        allowOutsideClick: false,
                                        customClass: { confirmButton: 'btn btn-success' },
                                        buttonsStyling: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                }, 1000);
                            } else {
                                $('#cancelBtn').show();
                                $('#closeProgressBtn').show();
                                addTerminalLog('ERROR: PROSES GAGAL.', 'error');
                            }
                        }
                    }
                }
            });
        }, 1000);
    }

    function updateProgress(percentage, message, log) {
        $('#progressBar').css('width', percentage + '%').attr('aria-valuenow', percentage);
        $('#progressText').text(percentage + '%');
        $('#statusText').text(message || 'Memproses data...');

        if (log) {
            const lines = log.split('\n').filter(line => line.trim());
            lines.forEach(line => addTerminalLog(line));
        }
    }

    function addTerminalLog(message, type = 'info') {
        const terminal = $('#terminalLog');
        let className = 'text-light'; // Default white/grayish

        if (type === 'error' || message.includes('ERROR') || message.includes('Gagal')) {
            className = 'text-danger fw-bold';
        } else if (type === 'success' || message.includes('✓') || message.includes('selesai') || message.includes('Berhasil')) {
            className = 'text-success fw-bold';
        } else if (message.includes('Memulai') || message.includes('Meng') || message.includes('Stream')) {
            className = 'text-info';
        }

        // Add timestamp for "tech" feel
        const time = new Date().toLocaleTimeString('en-US', { hour12: false });
        terminal.append(`<div class="${className} mb-1"><span class="text-secondary me-2">[${time}]</span>${message}</div>`);
        
        const terminalEl = document.getElementById('terminalLog');
        terminalEl.scrollTop = terminalEl.scrollHeight;
    }

    function clearTerminal() {
        $('#terminalLog').html('<div class="text-success">&gt; Console cleared.</div>');
    }

    function checkUpdateComplete(version) {
        setTimeout(() => {
            $('#progressModal').modal('hide');
            location.reload();
        }, 10000);
    }
    
    function downloadUpdate(version) {
        // Logic same as updateNow but calls download endpoint
        $('#progressModal').modal('show');
        updateProgress(0, 'Mengunduh paket update...', '');

        $.ajax({
            url: `/update/${version}/download`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                $('#progressModal').modal('hide');
                if (response.success) {
                    Swal.fire({ icon: 'success', title: 'Unduhan Selesai', text: 'File update telah tersimpan di storage.', confirmButtonText: 'OK' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: response.message, confirmButtonText: 'OK' });
                }
            },
            error: function(xhr) {
                $('#progressModal').modal('hide');
                Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal mengunduh file.', confirmButtonText: 'OK' });
            }
        });
    }

    $('#progressModal').on('hidden.bs.modal', function() {
        if (progressInterval) {
            clearInterval(progressInterval);
            progressInterval = null;
        }
    });
</script>
@endpush

@if (Session::has('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: "{{ Session::get('info') }}",
                confirmButtonText: 'OK',
                 customClass: { confirmButton: 'btn btn-primary' },
                buttonsStyling: false
            });
        });
    </script>
@endif
