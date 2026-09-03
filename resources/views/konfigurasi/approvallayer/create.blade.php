@extends('layouts.app')

@section('title')
    Konfigurasi Approval
@endsection

@section('page-title')
    Konfigurasi Approval
@endsection

@section('navigasi')
    <span>Approval Layer</span>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1 text-primary fw-bold">Rancang Alur Persetujuan</h4>
                <p class="text-muted mb-0">Rancang alur persetujuan secara dinamis berdasarkan target karyawan.</p>
            </div>
            <a href="{{ route('approvallayer.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<form action="{{ route('approvallayer.store') }}" method="POST" id="form-builder">
    @csrf
    <div class="row">
        <!-- Bagian Kiri: Target Karyawan -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom p-3 d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="ti ti-target"></i>
                    </div>
                    <h6 class="mb-0 fw-bold">Target Karyawan</h6>
                </div>
                <div class="card-body p-4 bg-light" style="background-color: #fcfcfc !important;">
                    <div class="form-group mb-3">
                        <label for="kode_cabang" class="form-label fw-bold text-dark" style="font-size: 13px;">Cabang</label>
                        <select name="kode_cabang" id="kode_cabang" class="form-select">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabang as $c)
                                <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="kode_dept" class="form-label fw-bold text-dark" style="font-size: 13px;">Departemen</label>
                        <select name="kode_dept" id="kode_dept" class="form-select">
                            <option value="">Semua Departemen</option>
                            @foreach ($departemen as $d)
                                <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="kode_jabatan" class="form-label fw-bold text-dark" style="font-size: 13px;">Jabatan Spesifik</label>
                        <select name="kode_jabatan" id="kode_jabatan" class="form-select">
                            <option value="">Semua Jabatan</option>
                            @foreach ($jabatan as $j)
                                <option value="{{ $j->kode_jabatan }}">{{ $j->nama_jabatan }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1" style="font-size: 11px;">Biarkan kosong jika berlaku untuk semua jabatan di departemen tersebut.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Kanan: Alur Persetujuan -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                            <i class="ti ti-git-merge"></i>
                        </div>
                        <h6 class="mb-0 fw-bold">Alur Persetujuan</h6>
                    </div>
                    <span class="badge bg-primary rounded-pill py-2 px-3 fw-bold" id="step-counter">0 Steps</span>
                </div>
                
                <div class="card-body p-4 bg-light" style="background-color: #f4f6f8 !important;">
                    <!-- Input Role Picker -->
                    <div class="d-flex mb-4 gap-2">
                        <select id="role_picker" class="form-select border-primary shadow-sm" style="height: 45px;">
                            <option value="">Pilih Role Pemberi Persetujuan</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" id="btn-add-role" style="height: 45px; white-space: nowrap;">
                            <i class="ti ti-plus me-1"></i> Tambah Ke Alur
                        </button>
                    </div>

                    <!-- Flow Container -->
                    <div id="flow-container" class="mt-4">
                        <!-- Dynamic Roles will be appended here -->
                        <div class="text-center text-muted empty-state py-5">
                            <i class="ti ti-drag-drop mb-2 d-block" style="font-size: 3rem; opacity: 0.3;"></i>
                            Pilih dan tambah role dari atas untuk memulai merancang alur.
                        </div>
                    </div>
                </div>

                <!-- Footer / Submit Area -->
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-end gap-2 align-items-center">
                    <a href="{{ route('approvallayer.index') }}" class="btn btn-light px-4">Batal</a>
                    <button type="button" class="btn btn-primary px-4" id="btn-submit-config">
                        <i class="ti ti-device-floppy me-1"></i> Simpan Konfigurasi
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('myscript')
<script>
    $(document).ready(function() {
        // Fetch Jabatan data using ajax since it wasn't passed by the controller initially.
        // Or actually, there's `jabatan` in the database, let's load it.
        $.ajax({
            type: 'GET',
            url: "{{ route('jabatan.index') }}", // Not a json endpoint, this returns html! 
            // Well, I should modify ApprovalLayerController to pass $jabatans.
            // For now, let's just make it empty and I'll modify the controller next.
        });

        // Flow Builder Logic
        let currentRoles = [];
        const flowContainer = $('#flow-container');
        const emptyState = $('.empty-state');
        const rolePicker = $('#role_picker');
        const btnAddRole = $('#btn-add-role');
        const stepCounter = $('#step-counter');

        function renderFlow() {
            flowContainer.find('.flow-item').remove();
            
            if (currentRoles.length === 0) {
                emptyState.show();
                stepCounter.text('0 Steps');
                return;
            }

            emptyState.hide();
            stepCounter.text(currentRoles.length + ' Steps');

            currentRoles.forEach((role, index) => {
                const level = index + 1;
                const html = `
                    <div class="flow-item card border-0 shadow-sm mb-3 position-relative" data-index="${index}" style="border-radius: 8px;">
                        <input type="hidden" name="role_names[]" value="${role}">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white fw-bold rounded d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; font-size: 14px;">
                                    ${level}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">${role}</h6>
                                    <span class="text-muted" style="font-size: 11px;">Pemberi Persetujuan</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-icon btn-sm btn-light text-danger btn-remove-role hover-bg-danger transition-all">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                flowContainer.append(html);
            });
        }

        btnAddRole.click(function() {
            const selectedRole = rolePicker.val();
            if (!selectedRole) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Silakan pilih Role Pemberi Persetujuan terlebih dahulu!',
                });
                return;
            }

            // Optional: prevent duplicate identical adjacent roles, but sometimes business logic allows it.
            // We'll just push it.
            currentRoles.push(selectedRole);
            rolePicker.val(''); // Reset
            renderFlow();
        });

        flowContainer.on('click', '.btn-remove-role', function() {
            const item = $(this).closest('.flow-item');
            const index = item.data('index');
            currentRoles.splice(index, 1);
            renderFlow();
        });

        $('#btn-submit-config').click(function(e) {
            e.preventDefault();
            if (currentRoles.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Alur persetujuan minimal harus memiliki 1 pemberi persetujuan!',
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyimpan konfigurasi alur ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0d6efd',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form-builder').submit();
                }
            });
        });
    });
</script>
<style>
    .hover-bg-danger:hover {
        background-color: #dc3545 !important;
        color: white !important;
    }
</style>
@endpush
