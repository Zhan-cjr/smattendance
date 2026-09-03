@extends('layouts.app')
@section('titlepage', 'Reset Data')

@section('content')
@section('navigasi')
    <span>Utilities > Reset Data</span>
@endsection

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="font-weight-bold mb-1">
                        <i class="ti ti-database-off me-2 text-danger"></i>Reset Data
                    </h4>
                    <p class="text-muted mb-0">Hapus semua data dari sistem kecuali data penting</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning Alert -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-danger border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <i class="ti ti-alert-triangle fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="alert-heading mb-2">Peringatan Penting!</h5>
                        <p class="mb-2">Fitur ini akan menghapus <strong>SEMUA data</strong> dari database. Tindakan ini <strong>TIDAK DAPAT
                                DIBATALKAN</strong>!</p>
                        <hr class="my-2">
                        <p class="mb-0"><strong>Data yang TIDAK akan dihapus:</strong></p>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0 small">
                                    <li><i class="ti ti-shield-check text-success me-2"></i>Users (Pengguna)</li>
                                    <li><i class="ti ti-shield-check text-success me-2"></i>Roles (Peran)</li>
                                    <li><i class="ti ti-shield-check text-success me-2"></i>Permissions (Izin Akses)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled mb-0 small">
                                    <li><i class="ti ti-shield-check text-success me-2"></i>Permission Groups</li>
                                    <li><i class="ti ti-shield-check text-success me-2"></i>General Setting</li>
                                    <li><i class="ti ti-shield-check text-success me-2"></i>Status Kawin & Denda</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Data yang akan dihapus -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="ti ti-list-check me-2 text-primary fs-5"></i>
                        <span style="color: #212529; font-weight: 600;">Data yang Akan Dihapus</span>
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Data Master -->
                        <div class="col-md-6">
                            <div class="data-category-card h-100 p-3 rounded border"
                                style="background-color: #fff; border-color: #e9ecef !important; transition: all 0.3s ease;">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="category-icon rounded d-flex align-items-center justify-content-center"
                                            style="width: 3rem; height: 3rem; background-color: rgba(220, 53, 69, 0.15); border: 2px solid rgba(220, 53, 69, 0.2);">
                                            <i class="ti ti-trash text-danger" style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-2 font-weight-bold" style="color: #212529; font-size: 1rem;">Data Master</h6>
                                        <ul class="list-unstyled mb-0 category-list">
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Karyawan</span></li>
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Departemen</span></li>
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Cabang</span></li>
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Jabatan</span></li>
                                            <li class="mb-0"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Cuti & Grup</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Presensi -->
                        <div class="col-md-6">
                            <div class="data-category-card h-100 p-3 rounded border"
                                style="background-color: #fff; border-color: #e9ecef !important; transition: all 0.3s ease;">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="category-icon rounded d-flex align-items-center justify-content-center"
                                            style="width: 3rem; height: 3rem; background-color: rgba(255, 193, 7, 0.15); border: 2px solid rgba(255, 193, 7, 0.2);">
                                            <i class="ti ti-clock text-warning" style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-2 font-weight-bold" style="color: #212529; font-size: 1rem;">Data Presensi</h6>
                                        <ul class="list-unstyled mb-0 category-list">
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Presensi</span></li>
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Izin Absen/Sakit/Cuti</span></li>
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Jam Kerja</span></li>
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Hari Libur</span></li>
                                            <li class="mb-0"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Lembur</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Payroll -->
                        <div class="col-md-6">
                            <div class="data-category-card h-100 p-3 rounded border"
                                style="background-color: #fff; border-color: #e9ecef !important; transition: all 0.3s ease;">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="category-icon rounded d-flex align-items-center justify-content-center"
                                            style="width: 3rem; height: 3rem; background-color: rgba(13, 202, 240, 0.15); border: 2px solid rgba(13, 202, 240, 0.2);">
                                            <i class="ti ti-moneybag text-info" style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-2 font-weight-bold" style="color: #212529; font-size: 1rem;">Data Payroll</h6>
                                        <ul class="list-unstyled mb-0 category-list">
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Gaji Pokok</span></li>
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Tunjangan</span></li>
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">BPJS</span></li>
                                            <li class="mb-0"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Slip Gaji</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Lainnya -->
                        <div class="col-md-6">
                            <div class="data-category-card h-100 p-3 rounded border"
                                style="background-color: #fff; border-color: #e9ecef !important; transition: all 0.3s ease;">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="category-icon rounded d-flex align-items-center justify-content-center"
                                            style="width: 3rem; height: 3rem; background-color: rgba(108, 117, 125, 0.15); border: 2px solid rgba(108, 117, 125, 0.2);">
                                            <i class="ti ti-activity text-secondary" style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-2 font-weight-bold" style="color: #212529; font-size: 1rem;">Data Lainnya</h6>
                                        <ul class="list-unstyled mb-0 category-list">
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Kunjungan</span></li>
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Aktivitas Karyawan</span></li>
                                            <li class="mb-1"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Kontrak</span></li>
                                            <li class="mb-0"><i class="ti ti-circle-filled text-danger me-2"
                                                    style="font-size: 5px; vertical-align: middle;"></i><span
                                                    style="color: #495057; font-size: 0.875rem;">Face Recognition</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Form Konfirmasi -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-lg border-danger h-100" style="border-width: 2px;">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center text-white">
                        <i class="ti ti-alert-circle me-2 fs-5"></i>Konfirmasi Reset
                    </h5>
                </div>
                <div class="card-body bg-white p-4">
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <div class="avatar avatar-xl rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style="background-color: rgba(220, 53, 69, 0.15); width: 5rem; height: 5rem; border: 2px solid rgba(220, 53, 69, 0.2);">
                            <i class="ti ti-trash text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <p class="mb-0" style="color: #495057; font-size: 0.9rem; line-height: 1.5;">
                            Ketik <strong class="text-danger" style="font-weight: 700;">"RESET DATA"</strong> untuk mengaktifkan tombol reset
                        </p>
                    </div>

                    <form method="POST" action="{{ route('resetdata.reset') }}" id="resetForm">
                        @csrf
                        <div class="mb-4">
                            <label for="konfirmasi" class="form-label font-weight-semibold mb-2" style="color: #212529; font-size: 0.95rem;">
                                Konfirmasi
                            </label>
                            <input type="text" class="form-control form-control-lg" id="konfirmasi" name="konfirmasi" placeholder="RESET DATA"
                                autocomplete="off" style="font-weight: 500; letter-spacing: 0.5px;" required>
                            <div class="form-text mt-2" style="color: #6c757d; font-size: 0.85rem;">
                                <i class="ti ti-info-circle me-1"></i>Ketik persis: <code class="text-danger"
                                    style="background-color: #f8f9fa; padding: 2px 6px; border-radius: 3px; font-weight: 600;">RESET DATA</code>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger w-100 btn-lg font-weight-bold" id="btn-reset-data"
                            style="padding: 0.75rem 1.5rem; font-size: 1rem; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);"
                            disabled>
                            <i class="ti ti-trash me-2"></i>Reset Semua Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal konfirmasi -->
<div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-danger text-white border-0 py-3 px-4"
                style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <h5 class="modal-title d-flex align-items-center mb-0 text-white" id="modalKonfirmasiLabel"
                    style="font-weight: 600; font-size: 1.1rem;">
                    <i class="ti ti-alert-triangle me-2" style="font-size: 1.3rem;"></i>Konfirmasi Akhir
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"
                    style="opacity: 0.9;"></button>
            </div>
            <div class="modal-body p-5">
                <div class="text-center mb-4">
                    <div class="warning-icon-wrapper mx-auto mb-4 d-flex align-items-center justify-content-center"
                        style="width: 5rem; height: 5rem; background: linear-gradient(135deg, rgba(220, 53, 69, 0.15) 0%, rgba(220, 53, 69, 0.08) 100%); border-radius: 50%; border: 3px solid rgba(220, 53, 69, 0.2);">
                        <i class="ti ti-alert-triangle text-danger" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="font-weight-bold mb-3" style="color: #212529; font-size: 1.5rem;">Apakah Anda Yakin?</h4>
                    <p class="mb-0" style="color: #6c757d; font-size: 1rem; line-height: 1.6;">
                        Tindakan ini akan menghapus <strong class="text-danger" style="font-weight: 700;">SEMUA data</strong> dari sistem
                    </p>
                </div>
                <div class="alert alert-danger border-0 rounded-lg mb-0"
                    style="background-color: #fff5f5; border-left: 4px solid #dc3545 !important;">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <i class="ti ti-info-circle text-danger mt-1" style="font-size: 1.2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <strong class="d-block mb-1" style="color: #721c24; font-size: 0.95rem;">Perhatian Penting:</strong>
                            <p class="mb-0" style="color: #721c24; font-size: 0.9rem; line-height: 1.5;">
                                Proses ini <strong>tidak dapat dibatalkan</strong>. Pastikan Anda telah membackup data penting sebelum melanjutkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light px-4 py-3" style="background-color: #f8f9fa !important;">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="font-weight: 500;">
                    <i class="ti ti-x me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger btn-lg px-4 font-weight-bold" id="btnKonfirmasiReset"
                    style="box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3); transition: all 0.3s ease;">
                    <i class="ti ti-trash me-2"></i>Ya, Reset Data Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<style>
    .avatar {
        width: 2.5rem;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-sm {
        width: 2rem;
        height: 2rem;
    }

    .avatar-xl {
        width: 4rem;
        height: 4rem;
    }

    #konfirmasi {
        border: 2px solid #dee2e6;
        transition: all 0.3s ease;
    }

    #konfirmasi:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        outline: none;
    }

    #konfirmasi.is-valid {
        border-color: #28a745;
        background-color: #f8fff9;
    }

    #konfirmasi.is-invalid {
        border-color: #dc3545;
        background-color: #fff5f5;
    }

    #btn-reset-data:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        box-shadow: none !important;
    }

    #btn-reset-data:not(:disabled):hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4) !important;
    }

    .card-body.bg-white {
        background-color: #ffffff !important;
    }

    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .font-weight-semibold {
        font-weight: 600;
    }

    .data-category-card {
        transition: all 0.3s ease;
    }

    .data-category-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        border-color: #dee2e6 !important;
    }

    .category-icon {
        transition: transform 0.3s ease;
    }

    .data-category-card:hover .category-icon {
        transform: scale(1.1);
    }

    .category-list li {
        transition: padding-left 0.2s ease;
    }

    .category-list li:hover {
        padding-left: 4px;
    }

    .category-list li span {
        transition: color 0.2s ease;
    }

    .category-list li:hover span {
        color: #212529 !important;
        font-weight: 500;
    }

    .warning-icon-wrapper {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }

    #btnKonfirmasiReset:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4) !important;
    }

    #btnKonfirmasiReset:active {
        transform: translateY(0);
    }
</style>
<script>
    $(document).ready(function() {
        // Validasi input konfirmasi dengan visual feedback
        $('#konfirmasi').on('input', function() {
            var konfirmasi = $(this).val().trim();
            var btn = $('#btn-reset-data');

            if (konfirmasi === 'RESET DATA') {
                btn.prop('disabled', false).removeClass('opacity-50');
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                btn.prop('disabled', true).addClass('opacity-50');
                if (konfirmasi.length > 0) {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                } else {
                    $(this).removeClass('is-valid is-invalid');
                }
            }
        });

        // Button reset data
        $('#btn-reset-data').click(function() {
            if (!$(this).prop('disabled')) {
                $('#modalKonfirmasi').modal('show');
            }
        });

        // Konfirmasi reset
        $('#btnKonfirmasiReset').off('click').on('click', function() {
            resetData();
        });

        function resetData() {
            $('#modalKonfirmasi').modal('hide');

            // Show loading dengan animasi yang lebih baik
            Swal.fire({
                title: '<div class="spinner-border text-danger" role="status"><span class="visually-hidden">Loading...</span></div>',
                html: '<p class="mt-3">Sedang mereset data, mohon tunggu...</p><p class="text-muted small">Proses ini mungkin memakan waktu beberapa saat</p>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    // Custom loading animation
                }
            });

            var formData = {
                _token: '{{ csrf_token() }}',
                konfirmasi: $('#konfirmasi').val()
            };

            $.ajax({
                url: '{{ route('resetdata.reset') }}',
                type: 'POST',
                data: formData,
                dataType: 'json',
                timeout: 300000, // 5 menit timeout
                success: function(response) {
                    if (response && response.success) {
                        var message = response.success;
                        var detailMessage = '';

                        if (response.deleted_tables && response.deleted_tables.length > 0) {
                            detailMessage =
                                '<div class="text-start mt-3"><strong>Tabel yang dihapus:</strong><ul class="list-unstyled mt-2">';
                            response.deleted_tables.slice(0, 10).forEach(function(item) {
                                detailMessage += '<li><i class="ti ti-check text-success me-2"></i>' + item.table +
                                    ' <span class="text-muted">(' + item.count + ' records)</span></li>';
                            });
                            if (response.deleted_tables.length > 10) {
                                detailMessage += '<li class="text-muted">... dan ' + (response.deleted_tables.length - 10) +
                                    ' tabel lainnya</li>';
                            }
                            detailMessage += '</ul></div>';
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: '<p>' + response.success + '</p>' + detailMessage,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745',
                            allowOutsideClick: false,
                            width: '600px'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else if (response && response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.error,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    console.log('XHR Error:', xhr);
                    var response = xhr.responseJSON;
                    var errorMessage = 'Terjadi kesalahan saat mereset data.';

                    if (response && response.error) {
                        errorMessage = response.error;
                        if (response.warning) {
                            errorMessage += '<br><small class="text-warning">' + response.warning + '</small>';
                        }
                    } else {
                        errorMessage += ' Status: ' + xhr.status;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
</script>
@endpush
