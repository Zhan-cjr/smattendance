@extends('v2.layouts.app')

@section('titlepage', 'Set User Permission')
@section('page_title', 'Set User Permission')

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
                <span>Direct Permissions: <span class="text-emerald-700">{{ $user->name }}</span></span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Mengatur hak akses otoritas langsung (spesifik) untuk user <strong>{{ $user->username }}</strong>
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
                <a href="{{ route('users.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-users text-sm"></i>
                    <span>Users</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Direct Permission</span>
            </nav>

            <div class="flex items-center gap-2">
                <label class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-slate-200 shadow-2xs text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-50">
                    <input type="checkbox" id="checkAllGlobal" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <span>Pilih Semua Otoritas</span>
                </label>
            </div>
        </div>
    </div>

    <!-- ================= 2. PERMISSION MATRIX CARDS ================= -->
    <form action="{{ route('users.storeuserpermission', Crypt::encrypt($user->id)) }}" method="POST" id="formPermissions" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" id="permission-container">
            @foreach ($permissions as $key => $d)
                <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden flex flex-col h-full">
                    <!-- Group Header -->
                    <div class="px-4 py-3 flex items-center justify-between text-white border-0"
                         style="background-color: {{ $themeColor1 }};">
                        <h4 class="text-xs font-extrabold text-white uppercase tracking-wider truncate mb-0" title="{{ $d->group_name }}">
                            {{ $d->group_name }}
                        </h4>
                        <label class="flex items-center gap-1 text-[10px] font-bold text-white/90 cursor-pointer">
                            <input type="checkbox" id="checkGroup{{ $key }}" class="group-check-all w-3.5 h-3.5 rounded text-white focus:ring-white border-white/40">
                            <span>Pilih</span>
                        </label>
                    </div>

                    <!-- Permission List Body -->
                    <div class="p-3.5 space-y-2.5 flex-1 bg-white">
                        @php
                            $list_permissions = explode(',', $d->permissions);
                        @endphp
                        @foreach ($list_permissions as $p)
                            @php
                                $permission = explode('-', $p);
                                $permission_id = $permission[0];
                                $permission_name = $permission[1];
                                $cek = in_array($permission_name, $userpermissions);
                                $rolesGranting = $permissionRoleMap[$permission_name] ?? null;
                            @endphp
                            <label class="flex items-start gap-2.5 text-xs text-slate-700 cursor-pointer p-1.5 rounded-lg hover:bg-slate-50 transition {{ !empty($rolesGranting) ? 'opacity-70 cursor-not-allowed' : '' }}">
                                <input type="checkbox" name="permission[]" value="{{ $permission_name }}"
                                       id="perm_{{ $permission_id }}"
                                       class="permission-checkbox mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                                       {{ $cek ? 'checked' : '' }}
                                       {{ !empty($rolesGranting) ? 'disabled' : '' }}>
                                <div class="min-w-0">
                                    <span class="font-medium text-slate-800 block truncate leading-tight" title="{{ $permission_name }}">
                                        {{ $permission_name }}
                                    </span>
                                    @if(!empty($rolesGranting))
                                        <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200" title="Otomatis aktif dari role: {{ implode(', ', $rolesGranting) }}">
                                            Role: {{ implode(', ', $rolesGranting) }}
                                        </span>
                                    @endif
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
                <span>Hak akses yang dicentang di sini akan menjadi hak akses spesifik yang melekat langsung pada user <strong>{{ $user->name }}</strong>.</span>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 text-center">
                    Kembali
                </a>
                <button type="submit" id="btnSavePermissions"
                        class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95 whitespace-nowrap"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-device-floppy text-sm"></i>
                    <span>Simpan Direct Permissions</span>
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
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox:not(:disabled)');

    function updateCheckAllStatus() {
        let totalChecked = 0;
        
        groupCheckAlls.forEach(groupCheck => {
            const card = groupCheck.closest('.bg-white');
            const cardCheckboxes = card.querySelectorAll('.permission-checkbox:not(:disabled)');
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

    updateCheckAllStatus();

    // Global Check All
    if (checkAllGlobal) {
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

    // Group Check All
    groupCheckAlls.forEach(groupCheck => {
        groupCheck.addEventListener('change', function () {
            const isChecked = this.checked;
            const card = this.closest('.bg-white');
            const cardCheckboxes = card.querySelectorAll('.permission-checkbox:not(:disabled)');
            
            cardCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            
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
