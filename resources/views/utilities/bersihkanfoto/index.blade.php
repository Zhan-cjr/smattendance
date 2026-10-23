@extends('layouts.app')
@section('titlepage', 'Bersihkan Foto & Retensi')

@section('content')
@section('navigasi')
    <span>Utilities > Bersihkan Foto</span>
@endsection

<!-- Section 1: Pengaturan Pembersihan Otomatis (Auto-Prune Scheduler) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-left: 4px solid #0d9488 !important;">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-3 bg-teal-100 text-teal-700 d-flex align-items-center justify-center" style="background-color: #ccfbf1; color: #0f766e;">
                            <i class="ti ti-clock-bolt" style="font-size: 28px;"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark fw-bold d-flex align-items-center gap-2">
                                Auto Bersihkan Foto Berkala (Scheduler)
                                <span id="autoBadge" class="badge {{ ($setting && $setting->auto_hapus_foto) ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ($setting && $setting->auto_hapus_foto) ? 'Aktif (Setiap Hari 02:30)' : 'Nonaktif' }}
                                </span>
                            </h5>
                            <p class="text-muted mb-0 small">
                                Secara otomatis menghapus file fisik foto yang lebih lama dari batasan hari (<strong>default 60 hari</strong>). 
                                <em>Data rekapitulasi, riwayat transaksi, dan audit trail di database tetap 100% aman</em>.
                            </p>
                        </div>
                    </div>

                    <!-- Toggle & Days input -->
                    <div class="d-flex flex-wrap align-items-center gap-3 bg-white p-2.5 rounded-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <label for="auto_days" class="small fw-semibold text-muted mb-0">Batas Retensi:</label>
                            <div class="input-group input-group-sm" style="width: 110px;">
                                <input type="number" id="auto_days" class="form-control text-center fw-bold" 
                                    value="{{ $setting->auto_hapus_foto_hari ?? 60 }}" min="7" max="365">
                                <span class="input-group-text small">hari</span>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="toggleAutoClean" 
                                {{ ($setting && $setting->auto_hapus_foto) ? 'checked' : '' }} style="cursor: pointer; width: 2.75rem; height: 1.4rem;">
                            <label class="form-check-label fw-bold small ms-1" for="toggleAutoClean" id="toggleLabel" style="cursor: pointer;">
                                {{ ($setting && $setting->auto_hapus_foto) ? 'Auto Bersihkan Aktif' : 'Aktifkan Auto' }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="small text-muted">
                        <i class="ti ti-info-circle me-1 text-info"></i>
                        Meliputi: <strong>Foto Absensi Masuk/Pulang/Istirahat, Foto Kunjungan, Foto Aktivitas, dan Foto Checklist & Grid Kolase</strong>.
                    </span>

                    <button type="button" class="btn btn-sm btn-outline-danger fw-semibold" id="btnQuickClean60">
                        <i class="ti ti-trash-x me-1"></i>Bersihkan Foto > 60 Hari Sekarang (1-Klik)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 2: Pembersihan Manual Berdasarkan Periode -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white pb-2 border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="ti ti-calendar-time me-1 text-primary"></i>Pembersihan Foto Manual Berdasarkan Periode
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('bersihkanfoto.destroy') }}" id="deleteForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">Pilih Jenis Foto yang Akan Dihapus:</label>
                            <div class="d-flex flex-wrap gap-4 p-3 bg-light rounded-3 border">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="check_presensi" name="jenis[]" value="presensi" checked>
                                    <label class="form-check-label user-select-none fw-semibold" for="check_presensi">
                                        <i class="ti ti-user-check me-1 text-success"></i>Foto Presensi (Masuk, Keluar, Istirahat)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="check_kunjungan" name="jenis[]" value="kunjungan" checked>
                                    <label class="form-check-label user-select-none fw-semibold" for="check_kunjungan">
                                        <i class="ti ti-map-pin me-1 text-warning"></i>Foto Kunjungan
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="check_aktivitas" name="jenis[]" value="aktivitas" checked>
                                    <label class="form-check-label user-select-none fw-semibold" for="check_aktivitas">
                                        <i class="ti ti-activity me-1 text-info"></i>Foto Aktivitas Karyawan
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="check_checklist" name="jenis[]" value="checklist" checked>
                                    <label class="form-check-label user-select-none fw-semibold" for="check_checklist">
                                        <i class="ti ti-list-check me-1 text-teal"></i>Foto Checklist & Grid Kolase 4-in-1
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="tanggal_awal" class="form-label fw-semibold">Tanggal Awal</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                <input type="text" class="form-control flatpickr-date" id="tanggal_awal" name="tanggal_awal"
                                    placeholder="Pilih tanggal awal" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_akhir" class="form-label fw-semibold">Tanggal Akhir</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                                <input type="text" class="form-control flatpickr-date" id="tanggal_akhir" name="tanggal_akhir"
                                    placeholder="Pilih tanggal akhir" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-danger w-100 fw-bold" id="btn-delete-photos">
                                <i class="ti ti-trash me-2"></i>Hapus File Foto Periode Terpilih
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Info Box -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start gap-2 mb-0" style="background-color: #fffbeb; color: #b45309;">
                            <i class="ti ti-alert-triangle fs-4 mt-0.5"></i>
                            <div>
                                <strong>Catatan Penting Kebijakan Retensi:</strong>
                                <ul class="mb-0 ps-3 mt-1 small">
                                    <li>Tindakan ini <strong>HANYA menghapus berkas fisik file foto</strong> di server storage untuk menghemat ruang penyimpanan disk.</li>
                                    <li>Data absensi, data kunjungan, data aktivitas, dan data checklist <strong>TIDAK AKAN HAPUS</strong> dari database dan tetap utuh sebagai riwayat laporan resmi.</li>
                                    <li>File foto yang sudah dihapus tidak dapat dipulihkan kembali.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal konfirmasi manual -->
<div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-labelledby="modalKonfirmasiLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalKonfirmasiLabel">Konfirmasi Penghapusan Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="konfirmasiText">Apakah Anda yakin ingin menghapus foto-foto ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger fw-bold" id="btnKonfirmasiHapus">Ya, Hapus File Foto</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        // Initialize flatpickr for date inputs
        $('.flatpickr-date').flatpickr({
            dateFormat: 'Y-m-d'
        });

        // 1. Toggle Auto Clean Handler
        $('#toggleAutoClean').on('change', function() {
            var isChecked = $(this).is(':checked');
            var days = $('#auto_days').val() || 60;

            saveAutoCleanSettings(isChecked, days);
        });

        $('#auto_days').on('change', function() {
            var isChecked = $('#toggleAutoClean').is(':checked');
            var days = $(this).val();
            if (days < 7) {
                $(this).val(7);
                days = 7;
            }
            saveAutoCleanSettings(isChecked, days);
        });

        function saveAutoCleanSettings(isActive, days) {
            $.ajax({
                url: '{{ route('bersihkanfoto.toggle-auto') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    auto_hapus_foto: isActive ? 1 : 0,
                    auto_hapus_foto_hari: days
                },
                dataType: 'json',
                success: function(res) {
                    if (res && res.success) {
                        if (res.status) {
                            $('#autoBadge').removeClass('bg-secondary').addClass('bg-success').text('Aktif (Setiap Hari 02:30)');
                            $('#toggleLabel').text('Auto Bersihkan Aktif');
                        } else {
                            $('#autoBadge').removeClass('bg-success').addClass('bg-secondary').text('Nonaktif');
                            $('#toggleLabel').text('Aktifkan Auto');
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Pengaturan Disimpan!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: res.error || 'Terjadi kesalahan saat menyimpan pengaturan'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan jaringan atau server.'
                    });
                }
            });
        }

        // 2. Quick Clean 60 Days Button
        $('#btnQuickClean60').on('click', function() {
            var cutoffDate = '{{ $cutoffDate }}';
            var defaultDays = $('#auto_days').val() || 60;

            Swal.fire({
                title: 'Bersihkan Foto > ' + defaultDays + ' Hari?',
                html: 'Tindakan ini akan menghapus semua file fisik foto (Presensi, Kunjungan, Aktivitas, Checklist) yang diambil <strong>sebelum atau pada tanggal ' + cutoffDate + '</strong>.<br><br><span class="text-success fw-bold">✓ Semua data rekapitulasi database tetap aman 100%.</span>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Bersihkan Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Set inputs and trigger delete
                    $('#tanggal_awal').val('2020-01-01');
                    $('#tanggal_akhir').val(cutoffDate);
                    // Ensure all checkboxes checked
                    $('input[name="jenis[]"]').prop('checked', true);

                    deletePhotos();
                }
            });
        });

        // 3. Delete photos button manual
        $('#btn-delete-photos').click(function() {
            var tanggalAwal = $('#tanggal_awal').val();
            var tanggalAkhir = $('#tanggal_akhir').val();
            
            var jenis = [];
            $('input[name="jenis[]"]:checked').each(function() {
                jenis.push($(this).val());
            });

            if (!tanggalAwal || !tanggalAkhir) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih tanggal awal dan tanggal akhir terlebih dahulu',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (jenis.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih minimal satu jenis foto yang akan dihapus',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $('#konfirmasiText').html('Apakah Anda yakin ingin menghapus <strong>FILE FOTO</strong> jenis (<strong>' + jenis.join(', ') + '</strong>) ' +
                'dalam periode <strong>' + tanggalAwal + '</strong> sampai <strong>' + tanggalAkhir + '</strong>?<br><br>' +
                '<span class="text-success fw-bold">✓ Data record tabel database tetap tersimpan utuh.</span> Tindakan ini tidak dapat dibatalkan!'
            );
            $('#modalKonfirmasi').modal('show');

            $('#btnKonfirmasiHapus').off('click').on('click', function() {
                deletePhotos();
            });
        });

        function deletePhotos() {
            $('#modalKonfirmasi').modal('hide');

            var jenis = [];
            $('input[name="jenis[]"]:checked').each(function() {
                jenis.push($(this).val());
            });

            var formData = {
                _token: '{{ csrf_token() }}',
                tanggal_awal: $('#tanggal_awal').val(),
                tanggal_akhir: $('#tanggal_akhir').val(),
                jenis: jenis
            };

            Swal.fire({
                title: 'Sedang Membersihkan Foto...',
                text: 'Mohon tunggu, sistem sedang memeriksa dan menghapus file dari storage.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route('bersihkanfoto.destroy') }}',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.success,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#tanggal_awal').val('');
                                $('#tanggal_akhir').val('');
                            }
                        });
                    } else if (response && response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.error,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan!',
                            text: 'Response tidak valid',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr) {
                    var response = xhr.responseJSON;
                    if (response && response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.error,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus foto. Status: ' + xhr.status,
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        }
    });
</script>
@endpush
