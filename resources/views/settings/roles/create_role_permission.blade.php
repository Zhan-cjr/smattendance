@extends('layouts.app')
@section('titlepage', 'Set Role Permission')

@section('content')
@section('navigasi')
    <span class="text-muted fw-light">Roles</span> / Set Role Permission <span class="fw-bold">{{ ucwords($role->name) }}</span>
@endsection
<div class="row align-items-center mb-3">
    <div class="col-12 d-flex flex-column flex-sm-row justify-content-between align-items-center">
        <h4 class="mb-3 mb-sm-0">
            Set Permissions for <span class="text-primary">{{ ucwords($role->name) }}</span>
        </h4>
        <div class="form-check form-switch form-check-inline mb-0 pe-0">
            <input class="form-check-input" type="checkbox" id="checkAllGlobal" style="cursor: pointer; width: 2.5em; height: 1.25em;">
            <label class="form-check-label fw-bold text-primary mt-1 ms-1" for="checkAllGlobal" style="cursor: pointer;">Pilih Semua Otoritas</label>
        </div>
    </div>
</div>

<form action="{{ route('roles.storerolepermission', Crypt::encrypt($role->id)) }}" method="POST">
    @csrf
    <div class="row" id="permission-container">
        @php
            $id_permission_group = '';
        @endphp
        @foreach ($permissions as $key => $d)
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center py-3" style="background-color: var(--theme-color-1); border-bottom: 3px solid rgba(0,0,0,0.1);">
                        <h6 class="m-0 fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px; color: #fff;">{{ $d->group_name }}</h6>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input group-check-all" type="checkbox" id="checkGroup{{ $key }}" style="cursor: pointer; width: 2em; height: 1em; border-color: rgba(255,255,255,0.5);" title="Pilih semua di {{ $d->group_name }}">
                        </div>
                    </div>
                    <div class="card-body pt-4">
                        @php
                            $list_permissions = explode(',', $d->permissions);
                        @endphp
                        @foreach ($list_permissions as $p)
                            @php
                                $permission = explode('-', $p);
                                $permission_id = $permission[0];
                                $permission_name = $permission[1];
                                $cek = in_array($permission_name, $rolepermissions);
                            @endphp
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input permission-checkbox" type="checkbox" name="permission[]"
                                    value="{{ $permission_name }}" id="defaultCheck{{ $permission_id }}"
                                    {{ $cek > 0 ? 'checked' : '' }} style="cursor: pointer;">
                                <label class="form-check-label text-secondary mt-1 ms-1" for="defaultCheck{{ $permission_id }}" style="font-size: 0.9rem; cursor: pointer;">
                                    {{ $permission_name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @php
                $id_permission_group = $d->id_permission_group;
            @endphp
        @endforeach
    </div>
    <div class="row mt-1">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-body d-flex flex-column flex-sm-row justify-content-between align-items-center py-3">
                    <div class="mb-3 mb-sm-0 text-center text-sm-start">
                        <span class="text-muted"><i class="ti ti-info-circle me-1"></i> Memastikan setiap role mendapatkan hak akses yang tepat sangatlah disarankan.</span>
                    </div>
                    <button class="btn btn-primary px-5 fw-bold" type="submit" style="white-space: nowrap;">
                        <ion-icon name="save-outline" class="me-1"></ion-icon>
                        Update Permissions
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('myscript')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const checkAllGlobal = document.getElementById('checkAllGlobal');
        const groupCheckAlls = document.querySelectorAll('.group-check-all');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

        // Update the state of "check all" switches based on actual checked permissions
        function updateCheckAllStatus() {
            let totalChecked = 0;
            
            groupCheckAlls.forEach(groupCheck => {
                const card = groupCheck.closest('.card');
                const cardCheckboxes = card.querySelectorAll('.permission-checkbox');
                let cardChecked = 0;

                cardCheckboxes.forEach(checkbox => {
                    const label = checkbox.nextElementSibling;
                    if (checkbox.checked) {
                        cardChecked++;
                        label.classList.remove('text-secondary', 'fw-normal');
                        label.classList.add('text-dark', 'fw-bold');
                    } else {
                        label.classList.remove('text-dark', 'fw-bold');
                        label.classList.add('text-secondary', 'fw-normal');
                    }
                });
                
                groupCheck.checked = (cardCheckboxes.length === cardChecked && cardCheckboxes.length > 0);
                totalChecked += cardChecked;
            });

            if (checkAllGlobal) {
                checkAllGlobal.checked = (permissionCheckboxes.length === totalChecked && permissionCheckboxes.length > 0);
            }
        }

        // Initialize view based on pre-checked boxes
        updateCheckAllStatus();

        // Global Check All Switch Event
        if(checkAllGlobal) {
            checkAllGlobal.addEventListener('change', function () {
                const isChecked = this.checked;
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                groupCheckAlls.forEach(groupCheck => {
                    groupCheck.checked = isChecked;
                });
            });
        }

        // Group Check All Switch Event
        groupCheckAlls.forEach(groupCheck => {
            groupCheck.addEventListener('change', function () {
                const isChecked = this.checked;
                const card = this.closest('.card');
                const cardCheckboxes = card.querySelectorAll('.permission-checkbox');
                
                cardCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                
                // Recalculate global switch status after changing a group
                updateCheckAllStatus();
            });
        });

        // Individual Checkbox Event
        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                updateCheckAllStatus();
            });
        });
    });
</script>
@endpush
