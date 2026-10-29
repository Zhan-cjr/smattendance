@extends('v2.layouts.app')

@section('titlepage', 'Manajemen Role')
@section('page_title', 'Manajemen Role')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5" x-data="{ viewMode: localStorage.getItem('role_view_mode') || 'table' }">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-shield text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Manajemen Role & Peran</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola daftar peran pengguna dan pengaturan wewenang hak akses (permissions) sistem
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
                <span class="font-bold text-slate-800">Roles</span>
            </nav>

            <button type="button" id="btnCreateRole"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-plus text-sm"></i>
                <span>Tambah Role Baru</span>
            </button>
        </div>
    </div>

    <!-- ================= 2. TOOLBAR & VIEW SWITCHER ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Search Form -->
        <form action="{{ route('roles.index') }}" method="GET" class="w-full sm:w-8/12 lg:w-9/12 flex items-center gap-2">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-search text-base"></i>
                </span>
                <input type="text" name="name" id="name_search"
                       value="{{ request('name') }}"
                       placeholder="Cari berdasarkan nama role..."
                       class="w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
            </div>

            <button type="submit" 
                    class="h-10 px-4 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-search text-sm"></i>
                <span>Cari</span>
            </button>

            @if(request()->filled('name'))
                <a href="{{ route('roles.index') }}" 
                   class="h-10 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition inline-flex items-center justify-center border border-slate-200"
                   title="Reset Filter">
                    <i class="ti ti-rotate text-sm"></i>
                    <span class="hidden sm:inline">Reset</span>
                </a>
            @endif
        </form>

        <!-- View Mode Switcher (Table vs Card) -->
        <div class="flex items-center gap-1 bg-white border border-slate-200 p-1 rounded-xl shadow-2xs self-end sm:self-auto shrink-0">
            <button type="button" 
                    @click="viewMode = 'table'; localStorage.setItem('role_view_mode', 'table')"
                    :class="viewMode === 'table' ? 'text-white shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'"
                    :style="viewMode === 'table' ? 'background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});' : ''"
                    class="px-2.5 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5"
                    title="Tampilan Tabel">
                <i class="ti ti-layout-list text-sm"></i>
                <span class="hidden sm:inline">Tabel</span>
            </button>
            <button type="button" 
                    @click="viewMode = 'card'; localStorage.setItem('role_view_mode', 'card')"
                    :class="viewMode === 'card' ? 'text-white shadow-xs font-bold' : 'text-slate-500 hover:text-slate-800 font-medium'"
                    :style="viewMode === 'card' ? 'background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});' : ''"
                    class="px-2.5 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5"
                    title="Tampilan Kartu">
                <i class="ti ti-layout-grid text-sm"></i>
                <span class="hidden sm:inline">Kartu</span>
            </button>
        </div>
    </div>

    <!-- ================= 3A. VIEW MODE 1: SEAMLESS SOLID TABLE ================= -->
    <div x-show="viewMode === 'table'" class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-shield-lock"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Daftar Role & Peran Sistem
                    </h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $roles->total() }} Total Role
            </span>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-14 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[200px]">Nama Role</th>
                        <th class="py-2.5 px-3.5 text-center text-white/90 border-0 border-t-0 w-32">Guard</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[180px]">Total Permission</th>
                        <th class="py-2.5 px-3.5 text-center w-36 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($roles as $idx => $d)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- No -->
                        <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold">
                            {{ $roles->firstItem() + $idx }}
                        </td>

                        <!-- Nama Role -->
                        <td class="py-2.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs text-white shrink-0 shadow-2xs"
                                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                    <i class="ti ti-shield text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-extrabold text-slate-900 text-xs sm:text-sm tracking-tight">
                                        {{ ucwords($d->name) }}
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        Role Key: <span class="font-mono text-slate-600">{{ $d->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Guard -->
                        <td class="py-2.5 px-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $d->guard_name }}
                            </span>
                        </td>

                        <!-- Total Permission -->
                        <td class="py-2.5 px-4">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $d->permissions->count() }} Permission Aktif
                                </span>
                            </div>
                        </td>

                        <!-- Aksi Buttons -->
                        <td class="py-2.5 px-3.5 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('roles.createrolepermission', Crypt::encrypt($d->id)) }}"
                                   class="w-7 h-7 rounded-lg bg-cyan-50 hover:bg-cyan-100 text-cyan-700 border border-cyan-200 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                   title="Set Role Permissions">
                                    <i class="ti ti-shield-lock text-sm"></i>
                                </a>

                                <button type="button"
                                        class="btnEditRole w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                        data-id="{{ $d->id }}"
                                        title="Edit Role">
                                    <i class="ti ti-edit text-sm"></i>
                                </button>

                                @if(!in_array(strtolower($d->name), ['super admin', 'master admin', 'karyawan']))
                                <form method="POST" action="{{ route('roles.delete', Crypt::encrypt($d->id)) }}" class="formDeleteRole m-0 inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btnDeleteRole w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                            title="Hapus Role">
                                        <i class="ti ti-trash text-sm"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400">
                            <i class="ti ti-shield text-3xl mb-2 block text-slate-300"></i>
                            <span>Tidak ada data role yang sesuai dengan pencarian.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($roles->hasPages())
            <div class="px-5 py-3.5 bg-white border-t border-slate-100">
                {{ $roles->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

    <!-- ================= 3B. VIEW MODE 2: COMPACT FULL-WIDTH CARD LIST ================= -->
    <div x-show="viewMode === 'card'" class="space-y-3">
        @forelse ($roles as $idx => $d)
        <div class="bg-white border border-slate-200/90 rounded-xl shadow-2xs hover:border-slate-300 hover:shadow-xs transition-all px-4 sm:px-5 py-4 sm:py-4.5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5">
                
                <!-- Left: Identity & Key -->
                <div class="flex items-center gap-3.5 min-w-0 sm:w-5/12">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white shrink-0 shadow-2xs"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-shield text-base"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-extrabold text-slate-900 text-xs sm:text-sm tracking-tight truncate mb-0">
                                {{ ucwords($d->name) }}
                            </h3>
                            <span class="text-[11px] font-mono text-slate-500 bg-slate-50 px-1.5 py-0.2 rounded border border-slate-200/60">
                                {{ $d->name }}
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-1">
                            <span>Guard: <strong class="text-slate-600 font-semibold">{{ $d->guard_name }}</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Middle: Stats Badge -->
                <div class="flex items-center gap-2 sm:w-4/12">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <i class="ti ti-key text-xs text-emerald-600"></i>
                        <span>{{ $d->permissions->count() }} Permissions Aktif</span>
                    </span>
                </div>

                <!-- Right: Action Buttons Group -->
                <div class="flex items-center justify-end gap-1.5 sm:w-3/12">
                    <a href="{{ route('roles.createrolepermission', Crypt::encrypt($d->id)) }}"
                       class="h-8 px-3 rounded-lg bg-cyan-50 hover:bg-cyan-100 text-cyan-700 border border-cyan-200 inline-flex items-center justify-center gap-1.5 text-[11px] font-bold transition active:scale-95 cursor-pointer"
                       title="Set Role Permissions">
                        <i class="ti ti-shield-lock text-xs"></i>
                        <span>Permissions</span>
                    </a>

                    <button type="button"
                            class="btnEditRole h-8 w-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 inline-flex items-center justify-center transition active:scale-95 cursor-pointer"
                            data-id="{{ $d->id }}"
                            title="Edit Role">
                        <i class="ti ti-edit text-xs"></i>
                    </button>

                    @if(!in_array(strtolower($d->name), ['super admin', 'master admin', 'karyawan']))
                    <form method="POST" action="{{ route('roles.delete', Crypt::encrypt($d->id)) }}" class="formDeleteRole m-0 inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                                class="btnDeleteRole h-8 w-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 inline-flex items-center justify-center transition active:scale-95 cursor-pointer"
                                title="Hapus Role">
                            <i class="ti ti-trash text-xs"></i>
                        </button>
                    </form>
                    @endif
                </div>

            </div>
        </div>
        @empty
        <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs p-8 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 border border-slate-200/60 shadow-2xs">
                <i class="ti ti-shield text-xl"></i>
            </div>
            <h3 class="text-xs font-extrabold text-slate-800">Tidak Ada Data Role</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Data role tidak ditemukan atau belum ditambahkan ke sistem.</p>
        </div>
        @endforelse

        <!-- Pagination -->
        @if ($roles->hasPages())
            <div class="px-5 py-3.5 bg-white border border-slate-200/90 rounded-2xl shadow-xs">
                {{ $roles->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

</div>

<!-- ================= 4. MODAL FORM V2 CONTAINER ================= -->
<div class="modal fade" id="modalRoleV2" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
            
            <!-- Standard White Header with Gradient Icon Badge -->
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-shield" id="modalRoleIcon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalRoleTitle">
                            Tambah Role
                        </h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalRoleSubtitle">
                            Formulir data role dan konfigurasi hak akses
                        </p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>

            <!-- Modal Dynamic Body Container -->
            <div class="p-6" id="loadModalRoleBody">
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
    $(function() {
        // Open Create Modal
        $('#btnCreateRole').on('click', function() {
            $('#modalRoleTitle').text('Tambah Role Baru');
            $('#modalRoleSubtitle').text('Lengkapi form berikut untuk menambahkan role ke sistem');
            $('#modalRoleIcon').attr('class', 'ti ti-shield-plus');
            $('#modalRoleV2').modal('show');
            $('#loadModalRoleBody').html(`
                <div class="py-10 text-center">
                    <i class="ti ti-loader animate-spin text-2xl text-emerald-600"></i>
                    <p class="text-xs text-slate-500 mt-2">Memuat formulir role...</p>
                </div>
            `);
            $('#loadModalRoleBody').load('/roles/create');
        });

        // Open Edit Modal
        $(document).on('click', '.btnEditRole', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $('#modalRoleTitle').text('Edit Data Role');
            $('#modalRoleSubtitle').text('Perbarui nama role atau penyesuaian peran');
            $('#modalRoleIcon').attr('class', 'ti ti-shield-check');
            $('#modalRoleV2').modal('show');
            $('#loadModalRoleBody').html(`
                <div class="py-10 text-center">
                    <i class="ti ti-loader animate-spin text-2xl text-emerald-600"></i>
                    <p class="text-xs text-slate-500 mt-2">Memuat data role...</p>
                </div>
            `);
            $('#loadModalRoleBody').load('/roles/' + id + '/edit');
        });

        // SweetAlert2 Delete Confirmation
        $(document).on('click', '.btnDeleteRole', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');

            Swal.fire({
                title: 'Hapus Role Ini?',
                text: 'Role yang dihapus dapat mempengaruhi pengguna yang memiliki wewenang ini.',
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
