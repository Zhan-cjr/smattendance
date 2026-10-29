@extends('v2.layouts.app')

@section('titlepage', 'Manajemen User')
@section('page_title', 'Manajemen User')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $userType = request('user_type', 'biasa');
@endphp

<div class="space-y-5" x-data="{ viewMode: localStorage.getItem('user_view_mode') || 'table' }">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-users text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Manajemen User & Hak Akses</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola akun pengguna sistem, pembagian role, akses cabang & departemen, serta izin otoritas
            </p>
        </div>

        <!-- Right Side: Breadcrumbs & Action Buttons -->
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
                <span class="font-bold text-slate-800">Users</span>
            </nav>

            <button type="button" id="btnCreateUser"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-user-plus text-sm"></i>
                <span>Tambah User Baru</span>
            </button>
        </div>
    </div>

    <!-- ================= 2. USER CATEGORY TABS & VIEW SWITCHER ================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-2">
        <!-- Category Tabs -->
        <div class="flex items-center gap-2">
            <button type="button" onclick="switchUserTab('biasa')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer {{ $userType !== 'karyawan' ? 'text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 bg-white border border-slate-200' }}"
                    @if($userType !== 'karyawan') style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});" @endif>
                <i class="ti ti-shield-lock text-sm"></i>
                <span>User Administrator</span>
            </button>

            <button type="button" onclick="switchUserTab('karyawan')"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer {{ $userType === 'karyawan' ? 'text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100 bg-white border border-slate-200' }}"
                    @if($userType === 'karyawan') style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});" @endif>
                <i class="ti ti-user text-sm"></i>
                <span>User Akun Karyawan</span>
            </button>
        </div>

        <!-- View Mode Switcher (Table vs Card) -->
        <div class="flex items-center gap-1 bg-white border border-slate-200 p-1 rounded-xl shadow-2xs self-start sm:self-auto">
            <button type="button" 
                    @click="viewMode = 'table'; localStorage.setItem('user_view_mode', 'table')"
                    :class="viewMode === 'table' ? 'text-white shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'"
                    :style="viewMode === 'table' ? 'background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});' : ''"
                    class="px-2.5 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5"
                    title="Tampilan Tabel">
                <i class="ti ti-layout-list text-sm"></i>
                <span class="hidden sm:inline">Tabel</span>
            </button>
            <button type="button" 
                    @click="viewMode = 'card'; localStorage.setItem('user_view_mode', 'card')"
                    :class="viewMode === 'card' ? 'text-white shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'"
                    :style="viewMode === 'card' ? 'background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});' : ''"
                    class="px-2.5 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5"
                    title="Tampilan Kartu">
                <i class="ti ti-layout-grid text-sm"></i>
                <span class="hidden sm:inline">Kartu</span>
            </button>
        </div>
    </div>

    <!-- ================= 3. FILTER TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('users.index') }}" method="GET" id="filterUserForm" class="w-full">
            <input type="hidden" name="user_type" id="filter_user_type" value="{{ $userType }}">
            
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 sm:gap-3 w-full items-center">
                <!-- Search Input: Nama / Username / Email -->
                <div class="{{ $userType !== 'karyawan' ? 'sm:col-span-6 lg:col-span-7' : 'sm:col-span-9 lg:col-span-10' }} relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" name="name" id="name_search"
                           value="{{ request('name') }}"
                           placeholder="Cari berdasarkan nama, username, atau email..."
                           class="w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>

                <!-- Role Filter (Only for Administrator tab) -->
                @if($userType !== 'karyawan')
                <div class="sm:col-span-3 lg:col-span-3">
                    <select name="role_id" id="role_id_filter"
                            class="w-full h-10 px-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Role</option>
                        @foreach ($roles as $role)
                            @if (strtolower($role->name) !== 'karyawan')
                                <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>
                                    {{ ucwords($role->name) }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Search & Reset Actions -->
                <div class="sm:col-span-3 lg:col-span-2 flex items-center gap-2 w-full justify-end">
                    <button type="submit" 
                            class="flex-1 h-10 px-4 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-search text-sm"></i>
                        <span>Cari</span>
                    </button>
                    @if(request()->filled('name') || request()->filled('role_id'))
                        <a href="{{ route('users.index', ['user_type' => $userType]) }}" 
                           class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center justify-center border border-slate-200"
                           title="Reset Filter">
                            <i class="ti ti-rotate text-sm"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- ================= 4A. VIEW MODE 1: SEAMLESS SOLID TABLE ================= -->
    <div x-show="viewMode === 'table'" class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti {{ $userType === 'karyawan' ? 'ti-user' : 'ti-shield-lock' }}"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Daftar {{ $userType === 'karyawan' ? 'User Akun Karyawan' : 'User Administrator' }}
                    </h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $users->total() }} Total Akun
            </span>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[200px]">Identitas User</th>
                        <th class="py-2.5 px-3.5 text-white/90 border-0 border-t-0 min-w-[130px]">Role / Peran</th>
                        <th class="py-2.5 px-3.5 text-center text-white/90 border-0 border-t-0 w-32">Koneksi</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[180px]">Hak Akses / Otoritas</th>
                        <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($users as $idx => $d)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- No -->
                        <td class="py-2 px-3.5 text-center text-slate-400 font-bold">
                            {{ $users->firstItem() + $idx }}
                        </td>

                        <!-- Identitas User -->
                        <td class="py-2 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs text-white shrink-0 shadow-2xs"
                                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                    {{ strtoupper(substr($d->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-extrabold text-slate-900 text-xs sm:text-sm tracking-tight flex items-center gap-1.5 flex-wrap">
                                        <span>{{ $d->name }}</span>
                                        <span class="text-[11px] font-normal text-slate-400">({{ $d->username }})</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                        <i class="ti ti-mail text-xs text-slate-400"></i>
                                        <span class="truncate">{{ $d->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Role / Peran -->
                        <td class="py-2 px-3.5">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($d->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ ucwords($role->name) }}
                                    </span>
                                @empty
                                    <span class="text-[11px] text-slate-400 italic">Tanpa Role</span>
                                @endforelse
                            </div>
                        </td>

                        <!-- Status Koneksi Karyawan -->
                        <td class="py-2 px-3.5 text-center">
                            @if (!empty($d->nik))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <i class="ti ti-link text-[10px]"></i> Terhubung
                                </span>
                            @elseif($userType === 'karyawan')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200">
                                    <i class="ti ti-link-off text-[10px]"></i> Tidak Terhubung
                                </span>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>

                        <!-- Hak Akses Cabang & Departemen -->
                        <td class="py-2 px-4">
                            @if ($userType !== 'karyawan')
                                <div class="space-y-1">
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">Cabang:</span>
                                        @if ($d->hasRole('super admin') || $d->hasRole('master admin'))
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold bg-indigo-100 text-indigo-700 border border-indigo-200">Semua Cabang</span>
                                        @elseif ($d->cabangs && $d->cabangs->count() > 0)
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold bg-cyan-100 text-cyan-800 border border-cyan-200" title="{{ $d->cabangs->pluck('nama_cabang')->implode(', ') }}">
                                                {{ $d->cabangs->count() }} Cabang
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">Tanpa Akses</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">Departemen:</span>
                                        @if ($d->hasRole('super admin') || $d->hasRole('master admin'))
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold bg-indigo-100 text-indigo-700 border border-indigo-200">Semua Dept</span>
                                        @elseif ($d->departemens && $d->departemens->count() > 0)
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200" title="{{ $d->departemens->pluck('nama_dept')->implode(', ') }}">
                                                {{ $d->departemens->count() }} Dept
                                            </span>
                                        @else
                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">Tanpa Akses</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                @php
                                    $uk = \App\Models\Userkaryawan::where('id_user', $d->id)->first();
                                @endphp
                                @if($uk && $uk->approval_admin_id)
                                    @php
                                        $adminApprover = \App\Models\User::find($uk->approval_admin_id);
                                    @endphp
                                    <div class="text-[11px] text-slate-600 flex items-center gap-1">
                                        <i class="ti ti-shield-check text-xs text-amber-600"></i>
                                        <span>Delegasi: <strong class="text-slate-800">{{ $adminApprover->name ?? '-' }}</strong></span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Akses Standar Karyawan</span>
                                @endif
                            @endif
                        </td>

                        <!-- Aksi Buttons -->
                        <td class="py-2 px-3.5 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ (\Illuminate\Support\Facades\Route::has('users.createuserpermission') ? route('users.createuserpermission', Crypt::encrypt($d->id)) : '#') }}"
                                   class="w-7 h-7 rounded-lg bg-cyan-50 hover:bg-cyan-100 text-cyan-700 border border-cyan-200 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                   title="Set Direct Permissions">
                                    <i class="ti ti-shield-lock text-sm"></i>
                                </a>

                                <a href="{{ (\Illuminate\Support\Facades\Route::has('users.impersonate') ? route('users.impersonate', Crypt::encrypt($d->id)) : '#') }}"
                                   class="w-7 h-7 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                   title="Login Sebagai User Ini (Impersonate)">
                                    <i class="ti ti-eye text-sm"></i>
                                </a>

                                <button type="button"
                                        class="btnEditUser w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                        data-id="{{ Crypt::encrypt($d->id) }}"
                                        title="Edit User">
                                    <i class="ti ti-edit text-sm"></i>
                                </button>

                                <form method="POST" action="{{ route('users.delete', Crypt::encrypt($d->id)) }}" class="formDeleteUser m-0 inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btnDeleteUser w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                            title="Hapus User">
                                        <i class="ti ti-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            <i class="ti ti-users text-3xl mb-2 block text-slate-300"></i>
                            <span>Tidak ada data user yang sesuai dengan pencarian.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="px-5 py-3.5 bg-white border-t border-slate-100">
                {{ $users->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

    <!-- ================= 4B. VIEW MODE 2: COMPACT FULL-WIDTH CARD LIST ================= -->
    <div x-show="viewMode === 'card'" class="space-y-3">
        @forelse ($users as $idx => $d)
        <div class="bg-white border border-slate-200/90 rounded-xl shadow-2xs hover:border-slate-300 hover:shadow-xs transition-all px-4 sm:px-5 py-4 sm:py-4.5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">
                
                <!-- Left: Avatar & Identity -->
                <div class="flex items-center gap-3.5 min-w-0 lg:w-4/12">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs sm:text-sm text-white shrink-0 shadow-2xs"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        {{ strtoupper(substr($d->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-extrabold text-slate-900 text-xs sm:text-sm tracking-tight truncate mb-0">
                                {{ $d->name }}
                            </h3>
                            <span class="text-[11px] font-semibold text-slate-400 bg-slate-50 px-1.5 py-0.2 rounded border border-slate-200/60">
                                {{ $d->username }}
                            </span>
                            @forelse ($d->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.2 rounded text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ ucwords($role->name) }}
                                </span>
                            @empty
                            @endforelse
                        </div>
                        <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-1">
                            <i class="ti ti-mail text-xs text-slate-400"></i>
                            <span class="truncate">{{ $d->email }}</span>
                        </div>
                    </div>
                </div>

                <!-- Middle: Status & Akses (Single Row Compact) -->
                <div class="flex flex-wrap items-center gap-3.5 lg:w-5/12 text-xs">
                    <!-- Status Koneksi -->
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Koneksi:</span>
                        @if (!empty($d->nik))
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                <i class="ti ti-link text-[10px]"></i> NIK: {{ $d->nik }}
                            </span>
                        @elseif($userType === 'karyawan')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200">
                                <i class="ti ti-link-off text-[10px]"></i> Belum Terhubung
                            </span>
                        @else
                            <span class="text-[11px] text-slate-500 font-medium">Non-Karyawan</span>
                        @endif
                    </div>

                    <!-- Akses Cabang & Dept / Delegasi -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        @if ($userType !== 'karyawan')
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Akses:</span>
                            <!-- Cabang -->
                            @if ($d->hasRole('super admin') || $d->hasRole('master admin'))
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-indigo-100 text-indigo-700 border border-indigo-200">Semua Cabang</span>
                            @elseif ($d->cabangs && $d->cabangs->count() > 0)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-cyan-100 text-cyan-800 border border-cyan-200" title="{{ $d->cabangs->pluck('nama_cabang')->implode(', ') }}">
                                    {{ $d->cabangs->count() }} Cabang
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">No Cabang</span>
                            @endif

                            <!-- Dept -->
                            @if ($d->hasRole('super admin') || $d->hasRole('master admin'))
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-indigo-100 text-indigo-700 border border-indigo-200">Semua Dept</span>
                            @elseif ($d->departemens && $d->departemens->count() > 0)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200" title="{{ $d->departemens->pluck('nama_dept')->implode(', ') }}">
                                    {{ $d->departemens->count() }} Dept
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">No Dept</span>
                            @endif
                        @else
                            @php
                                $uk = \App\Models\Userkaryawan::where('id_user', $d->id)->first();
                            @endphp
                            @if($uk && $uk->approval_admin_id)
                                @php
                                    $adminApprover = \App\Models\User::find($uk->approval_admin_id);
                                @endphp
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Delegasi:</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1">
                                    <i class="ti ti-shield-check text-xs text-amber-600"></i>
                                    {{ $adminApprover->name ?? '-' }}
                                </span>
                            @else
                                <span class="text-slate-400 text-[11px] italic">Akses Standar</span>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Right: Action Buttons Group -->
                <div class="flex items-center justify-end gap-1.5 lg:w-3/12">
                    <a href="{{ (\Illuminate\Support\Facades\Route::has('users.createuserpermission') ? route('users.createuserpermission', Crypt::encrypt($d->id)) : '#') }}"
                       class="h-8 px-2.5 rounded-lg bg-cyan-50 hover:bg-cyan-100 text-cyan-700 border border-cyan-200 inline-flex items-center justify-center gap-1 text-[11px] font-bold transition active:scale-95 cursor-pointer"
                       title="Set Direct Permissions">
                        <i class="ti ti-shield-lock text-xs"></i>
                        <span>Permission</span>
                    </a>

                    <a href="{{ (\Illuminate\Support\Facades\Route::has('users.impersonate') ? route('users.impersonate', Crypt::encrypt($d->id)) : '#') }}"
                       class="h-8 px-2.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 inline-flex items-center justify-center gap-1 text-[11px] font-bold transition active:scale-95 cursor-pointer"
                       title="Login Sebagai User Ini">
                        <i class="ti ti-eye text-xs"></i>
                        <span>Login As</span>
                    </a>

                    <button type="button"
                            class="btnEditUser h-8 w-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 inline-flex items-center justify-center transition active:scale-95 cursor-pointer"
                            data-id="{{ Crypt::encrypt($d->id) }}"
                            title="Edit User">
                        <i class="ti ti-edit text-xs"></i>
                    </button>

                    <form method="POST" action="{{ route('users.delete', Crypt::encrypt($d->id)) }}" class="formDeleteUser m-0 inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                class="btnDeleteUser h-8 w-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 inline-flex items-center justify-center transition active:scale-95 cursor-pointer"
                                title="Hapus User">
                            <i class="ti ti-trash text-xs"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
        @empty
        <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs p-8 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 border border-slate-200/60 shadow-2xs">
                <i class="ti ti-users text-xl"></i>
            </div>
            <h3 class="text-xs font-extrabold text-slate-800">Tidak Ada Data User</h3>
            <p class="text-[11px] text-slate-500 max-w-sm mx-auto mt-0.5">
                Data pengguna tidak ditemukan untuk filter atau kategori yang dipilih.
            </p>
        </div>
        @endforelse

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="bg-white border border-slate-200/90 rounded-xl shadow-2xs p-3">
                {{ $users->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

</div>

<!-- ================= 5. MODAL FORM V2 CONTAINER ================= -->
<div class="modal fade" id="modalUserV2" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
            
            <!-- Standard White Header with Gradient Icon Badge -->
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-user" id="modalUserIcon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalUserTitle">
                            Tambah User
                        </h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalUserSubtitle">
                            Formulir data akun pengguna dan konfigurasi hak akses
                        </p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>

            <!-- Modal Dynamic Body Container -->
            <div class="p-6" id="loadModalUserBody">
                <div class="py-10 text-center">
                    <i class="ti ti-loader animate-spin text-2xl text-slate-400"></i>
                    <p class="text-xs text-slate-500 mt-2">Memuat formulir...</p>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    function switchUserTab(type) {
        $('#filter_user_type').val(type);
        $('#filterUserForm').submit();
    }

    $(function() {
        // Open Create Modal
        $('#btnCreateUser').on('click', function() {
            $('#modalUserTitle').text('Tambah User Baru');
            $('#modalUserSubtitle').text('Lengkapi form berikut untuk menambahkan pengguna ke sistem');
            $('#modalUserIcon').attr('class', 'ti ti-user-plus');
            $('#modalUserV2').modal('show');
            $('#loadModalUserBody').html(`
                <div class="py-10 text-center">
                    <i class="ti ti-loader animate-spin text-2xl text-emerald-600"></i>
                    <p class="text-xs text-slate-500 mt-2">Memuat formulir user...</p>
                </div>
            `);
            $('#loadModalUserBody').load('/users/create');
        });

        // Open Edit Modal
        $(document).on('click', '.btnEditUser', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $('#modalUserTitle').text('Edit Data User');
            $('#modalUserSubtitle').text('Perbarui informasi akun dan penyesuaian hak akses');
            $('#modalUserIcon').attr('class', 'ti ti-user-edit');
            $('#modalUserV2').modal('show');
            $('#loadModalUserBody').html(`
                <div class="py-10 text-center">
                    <i class="ti ti-loader animate-spin text-2xl text-emerald-600"></i>
                    <p class="text-xs text-slate-500 mt-2">Memuat data user...</p>
                </div>
            `);
            $('#loadModalUserBody').load('/users/' + id + '/edit');
        });

        // SweetAlert2 Delete Confirmation
        $(document).on('click', '.btnDeleteUser', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');

            Swal.fire({
                title: 'Hapus User Ini?',
                text: 'Akun user yang dihapus tidak akan dapat login lagi ke dalam sistem.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'swal2-confirm',
                    cancelButton: 'swal2-cancel'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
