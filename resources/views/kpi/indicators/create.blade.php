@extends('layouts.app')
@section('titlepage', 'Tambah Indikator KPI')

@section('content')
@section('navigasi')
    <span>Tambah Indikator KPI</span>
@endsection
<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Konfigurasi KPI Jabatan</h4>
            </div>
            <div class="card-body">
                @if(($jabatan && $departemen) || isset($is_global))
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            @if(isset($is_global))
                                <div class="alert alert-primary">
                                    <strong>Konfigurasi KPI:</strong> GLOBAL (Semua Jabatan & Departemen)
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <strong>Jabatan:</strong> {{ $jabatan->nama_jabatan }} <br>
                                    <strong>Departemen:</strong> {{ $departemen->nama_dept }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-primary" id="btnTambahIndikator">
                                <i class="ti ti-plus me-1"></i> Tambah Indikator
                            </button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="table-responsive border rounded bg-white">
                                <table class="table table-striped table-hover mb-0" id="tableIndikator">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 1%; white-space: nowrap;">No</th>
                                            <th>Nama Indikator</th>
                                            <th style="width: 1%; white-space: nowrap;">Mode</th>
                                            <th>Deskripsi</th>
                                            <th style="width: 1%; white-space: nowrap;">Satuan</th>
                                            <th style="width: 1%; white-space: nowrap;">Jenis Target</th>
                                            <th style="width: 1%; white-space: nowrap;">Bobot (%)</th>
                                            <th style="width: 1%; white-space: nowrap;">Target</th>
                                            <th style="width: 1%; white-space: nowrap;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyIndikator">
                                        <tr id="emptyRow">
                                            <td colspan="9" class="text-center text-muted p-4">Belum ada indikator. Klik "Tambah Indikator" untuk menambahkan.</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light">
                                            <td colspan="6" class="text-end fw-bold">Total Bobot</td>
                                            <td class="fw-bold"><span id="totalBobot">0</span>%</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('kpi.indicators.store') }}" method="POST" id="formSimpanKonfigurasi">
                        @csrf
                        @if(isset($is_global))
                            <!-- Implicit null for jabatan/dept -->
                        @else
                            <input type="hidden" name="kode_jabatan" value="{{ $jabatan->kode_jabatan }}">
                            <input type="hidden" name="kode_dept" value="{{ $departemen->kode_dept }}">
                        @endif
                        <input type="hidden" name="indicators_data" id="indicators_data">
                        
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100" id="btnSimpanKonfigurasi">
                                    <i class="ti ti-send me-1"></i> Simpan Konfigurasi
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="alert alert-warning">
                                <strong>Peringatan:</strong> Jabatan tidak dipilih. Silakan kembali ke halaman index dan pilih jabatan terlebih dahulu.
                            </div>
                            <a href="{{ route('kpi.indicators.index') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Indikator -->
<div class="modal modal-blur fade" id="modalTambahIndikator" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Indikator KPI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formTambahIndikator">
                    <style>
                        .form-floating > .form-control:focus ~ label,
                        .form-floating > .form-control:not(:placeholder-shown) ~ label,
                        .form-floating > .form-select ~ label {
                            font-weight: bold;
                        }
                    </style>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="nama_indikator" placeholder="Contoh: Omset Penjualan">
                                <label for="nama_indikator">Nama Indikator <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="deskripsi" placeholder="Deskripsi indikator (opsional)" style="height: 100px"></textarea>
                                <label for="deskripsi">Deskripsi</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="satuan" placeholder="Contoh: Rupiah, %, Unit" required>
                                <label for="satuan">Satuan <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="jenis_target" required>
                                    <option value="">Pilih Jenis Target</option>
                                    <option value="max">Maksimal (Semakin Tinggi Semakin Baik)</option>
                                    <option value="min">Minimal (Semakin Rendah Semakin Baik)</option>
                                </select>
                                <label for="jenis_target">Jenis Target <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="bobot" placeholder="Contoh: 20" min="1" max="100" required>
                                <label for="bobot">Bobot (%) <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="target" placeholder="Contoh: 100" step="0.01" required>
                                <label for="target">Target <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-floating">
                                 <select class="form-select" id="mode">
                                     <option value="manual">Manual Input</option>
                                     <option value="auto">Otomatis (Sistem)</option>
                                 </select>
                                 <label for="mode">Mode Input</label>
                             </div>
                        </div>
                        <div class="col-md-6 d-none" id="metric_source_container">
                             <div class="form-floating">
                                 <select class="form-select" id="metric_source">
                                     <option value="">Pilih Sumber Data</option>
                                     <option value="attendance_sakit">Total Sakit (Hari)</option>
                                     <option value="attendance_izin">Total Izin (Hari)</option>
                                     <option value="attendance_alpa">Total Alpa (Hari)</option>
                                     <option value="attendance_cuti">Total Cuti (Hari)</option>
                                     <option value="attendance_terlambat">Total Keterlambatan (Menit)</option>
                                     <option value="attendance_hadir">Total Kehadiran (Hari)</option>
                                 </select>
                                 <label for="metric_source">Sumber Data Otomatis</label>
                             </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSimpanIndikator">
                    <i class="ti ti-check me-1"></i> Simpan Indikator
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    let indicators = [];
    let editIndex = -1;

    $(document).ready(function() {
        // Mode Change
        $('#mode').change(function() {
            if ($(this).val() == 'auto') {
                $('#metric_source_container').removeClass('d-none');
                $('#metric_source').prop('required', true);
            } else {
                $('#metric_source_container').addClass('d-none');
                $('#metric_source').prop('required', false);
                $('#metric_source').val('');
            }
        });

        // Buka modal tambah indikator
        $(document).on('click', '#btnTambahIndikator', function(e) {
            e.preventDefault();
            resetForm();
            editIndex = -1;
            $('#modalTambahIndikator').modal('show');
        });

        // Simpan indikator ke array
        $('#btnSimpanIndikator').click(function() {
            var nama_indikator = $('#nama_indikator').val();
            var deskripsi = $('#deskripsi').val();
            var satuan = $('#satuan').val();
            var jenis_target = $('#jenis_target').val();
            var bobot = $('#bobot').val();
            var target = $('#target').val();
            var mode = $('#mode').val();
            var metric_source = $('#metric_source').val();

            if (nama_indikator == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Nama Indikator harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'Ok',
                    didClose: function() {
                        $('#nama_indikator').focus();
                    }
                });
                return;
            }

            if (satuan == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Satuan harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'Ok',
                    didClose: function() {
                        $('#satuan').focus();
                    }
                });
                return;
            }

            if (jenis_target == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Jenis Target harus dipilih!',
                    icon: 'warning',
                    confirmButtonText: 'Ok',
                    didClose: function() {
                        $('#jenis_target').focus();
                    }
                });
                return;
            }

            if (bobot == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Bobot harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'Ok',
                    didClose: function() {
                        $('#bobot').focus();
                    }
                });
                return;
            }

            if (target == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Target harus diisi!',
                    icon: 'warning',
                    confirmButtonText: 'Ok',
                    didClose: function() {
                        $('#target').focus();
                    }
                });
                return;
            }

            if (mode == 'auto' && metric_source == "") {
                Swal.fire({
                    title: 'Warning!',  
                    text: 'Sumber Data Otomatis harus dipilih!',
                    icon: 'warning',
                    confirmButtonText: 'Ok',
                    didClose: function() {
                        $('#metric_source').focus();
                    }
                });
                return;
            }

            const indicator = {
                nama_indikator: nama_indikator,
                deskripsi: deskripsi,
                satuan: satuan,
                jenis_target: jenis_target,
                bobot: parseFloat(bobot),
                target: parseFloat(target),
                mode: mode,
                metric_source: metric_source
            };

            if (editIndex >= 0) {
                indicators[editIndex] = indicator;
            } else {
                indicators.push(indicator);
            }

            renderTable();
            $('#modalTambahIndikator').modal('hide');
            resetForm();
        });

        // Render tabel
        function renderTable() {
            const tbody = $('#bodyIndikator');
            tbody.empty();

            if (indicators.length === 0) {
                tbody.append(`
                    <tr id="emptyRow">
                        <td colspan="9" class="text-center text-muted">Belum ada indikator. Klik "Tambah Indikator" untuk menambahkan.</td>
                    </tr>
                `);
            } else {
                indicators.forEach((item, index) => {
                    const jenisTargetText = item.jenis_target === 'max' ? 'Maksimal' : 'Minimal';
                    let modeBadge = '';
                    if (item.mode === 'auto') {
                        modeBadge = `<span class="badge bg-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="${item.metric_source}">Auto</span>`;
                    } else {
                        modeBadge = `<span class="badge bg-secondary">Manual</span>`;
                    }
                    
                    tbody.append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.nama_indikator}</td>
                            <td>${modeBadge}</td>
                            <td>${item.deskripsi || '-'}</td>
                            <td>${item.satuan}</td>
                            <td>${jenisTargetText}</td>
                            <td>${item.bobot}</td>
                            <td>${item.target}</td>
                            <td>
                                <button type="button" class="btn btn-sm text-danger btn-delete p-0" data-index="${index}">
                                    <i class="ti ti-trash fs-4"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                  return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }

            hitungTotalBobot();
        }

        // Hapus indikator
        $(document).on('click', '.btn-delete', function() {
            const index = $(this).data('index');
            Swal.fire({
                title: 'Hapus Indikator?',
                text: "Indikator ini akan dihapus dari daftar",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    indicators.splice(index, 1);
                    renderTable();
                }
            });
        });

        // Hitung total bobot
        function hitungTotalBobot() {
            const total = indicators.reduce((sum, item) => sum + item.bobot, 0);
            $('#totalBobot').text(total);
        }

        // Reset form
        function resetForm() {
            var form = document.getElementById('formTambahIndikator');
            if(form) {
                form.reset();
            }
            $('#metric_source_container').addClass('d-none');
            $('#mode').val('manual');
        }

        // Submit form utama
        $('#formSimpanKonfigurasi').submit(function(e) {
            e.preventDefault();

            if (indicators.length === 0) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Tambahkan minimal 1 indikator!',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            const totalBobot = indicators.reduce((sum, item) => sum + item.bobot, 0);
            if (totalBobot !== 100) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Total bobot harus 100%! Saat ini: ' + totalBobot + '%',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
                return;
            }

            // Set data ke hidden input
            $('#indicators_data').val(JSON.stringify(indicators));
            
            // Submit form
            this.submit();
        });
    });
</script>
@endpush
