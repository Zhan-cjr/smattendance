@extends('v2.layouts.app')

@section('titlepage', 'Set Role Permission')
@section('page_title', 'Set Role Permission')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTIONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-shield-lock text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Hak Akses Role: <span class="text-emerald-700">{{ ucwords($role->name) }}</span></span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Mengatur hak akses dan wewenang (permissions) default untuk semua pengguna dengan role <strong>{{ ucwords($role->name) }}</strong>
            </p>
        </div>

        <!-- Breadcrumbs & Global Action -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-500 flex items-center gap-1">
                    <i class="ti ti-settings text-sm"></i>
                    <span>Pengaturan</span>
                </span>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('roles.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-shield text-sm"></i>
                    <span>Roles</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Set Permission</span>
            </nav>

            <div class="flex items-center gap-2">
                <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 shadow-2xs text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-50 transition">
                    <input type="checkbox" id="checkAllGlobal" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                    <span>Pilih Semua Otoritas</span>
                </label>
            </div>
        </div>
    </div>

    <!-- ================= 2. PERMISSION MATRIX CARDS ================= -->
    <form action="{{ route('roles.storerolepermission', Crypt::encrypt($role->id)) }}" method="POST" id="formRolePermissions" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="permission-container">
            @foreach ($permissions as $key => $d)
                <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden flex flex-col h-full hover:border-slate-300 transition-all">
                    <!-- Group Header -->
                    <div class="px-4 py-3 flex items-center justify-between text-white border-0"
                         style="background-color: {{ $themeColor1 }};">
                        <div class="flex items-center gap-2 min-w-0">
                            <i class="ti ti-folder text-sm text-white/80 shrink-0"></i>
                            <h4 class="text-xs font-extrabold text-white uppercase tracking-wider truncate mb-0" title="{{ $d->group_name }}">
                                {{ $d->group_name }}
                            </h4>
                        </div>
                        <label class="flex items-center gap-1.5 text-[10px] font-bold text-white/90 cursor-pointer bg-white/15 hover:bg-white/25 px-2 py-0.5 rounded-md transition">
                            <input type="checkbox" id="checkGroup{{ $key }}" class="group-check-all w-3.5 h-3.5 rounded text-white focus:ring-white border-white/40 cursor-pointer">
                            <span>Pilih</span>
                        </label>
                    </div>

                    <!-- Permission List Body -->
                    <div class="p-3.5 space-y-2 flex-1 bg-white">
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
                            <label class="flex items-start gap-2.5 text-xs text-slate-700 cursor-pointer p-1.5 rounded-lg hover:bg-slate-50 transition">
                                <input type="checkbox" name="permission[]" value="{{ $permission_name }}"
                                       id="perm_{{ $permission_id }}"
                                       class="permission-checkbox mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer"
                                       {{ $cek ? 'checked' : '' }}>
                                <div class="min-w-0">
                                    <span class="font-medium text-slate-800 block truncate leading-tight" title="{{ $permission_name }}">
                                        {{ $permission_name }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ================= 3. BOTTOM BAR (INFO & SUBMIT) ================= -->
        <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5 text-xs text-slate-500">
                <i class="ti ti-info-circle text-base text-emerald-600 shrink-0"></i>
                <span>Pastikan role <strong>{{ ucwords($role->name) }}</strong> hanya memiliki hak akses yang relevan dengan tugas operasionalnya.</span>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <a href="{{ route('roles.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 text-center">
                    Kembali
                </a>
                <button type="submit" id="btnSaveRolePermissions"
                        class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95 whitespace-nowrap"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-device-floppy text-sm"></i>
                    <span>Simpan Role Permissions</span>
                </button>
            </div>
        </div>

    </form>

</div>

@endsection

@push('myscript')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const checkAllGlobal = document.getElementById('checkAllGlobal');
    const groupCheckAlls = document.querySelectorAll('.group-check-all');
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

    function updateCheckAllStatus() {
        let totalChecked = 0;
        
        groupCheckAlls.forEach(groupCheck => {
            const card = groupCheck.closest('.bg-white');
            const cardCheckboxes = card.querySelectorAll('.permission-checkbox');
            let cardChecked = 0;

            cardCheckboxes.forEach(checkbox => {
                if (checkbox.checked) cardChecked++;
            });
            
            groupCheck.checked = (cardCheckboxes.length === cardChecked && cardCheckboxes.length > 0);
            totalChecked += cardChecked;
        });

        if (checkAllGlobal) {
            checkAllGlobal.checked = (permissionCheckboxes.length === totalChecked && permissionCheckboxes.length > 0);
        }
    }

    // Toggle per group
    groupCheckAlls.forEach(groupCheck => {
        groupCheck.addEventListener('change', function () {
            const card = this.closest('.bg-white');
            const cardCheckboxes = card.querySelectorAll('.permission-checkbox');
            cardCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateCheckAllStatus();
        });
    });

    // Toggle Global Check All
    if (checkAllGlobal) {
        checkAllGlobal.addEventListener('change', function () {
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            groupCheckAlls.forEach(groupCheck => {
                groupCheck.checked = this.checked;
            });
        });
    }

    // Individual checkbox change
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCheckAllStatus);
    });

    // Initial check
    updateCheckAllStatus();

    // Submit handler animation
    $('#formRolePermissions').on('submit', function() {
        let checkedCount = $('.permission-checkbox:checked').length;
        if (checkedCount === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih minimal satu permission untuk role ini.',
                confirmButtonColor: '{{ $themeColor1 }}'
            });
            return false;
        }

        $('#btnSaveRolePermissions').prop('disabled', true).html(`
            <i class="ti ti-loader animate-spin text-sm"></i>
            <span>Menyimpan...</span>
        `);
    });
});
</script>
@endpush
