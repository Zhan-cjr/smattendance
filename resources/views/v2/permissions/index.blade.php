@extends('v2.layouts.app')

@section('titlepage', 'Manajemen Permission')
@section('page_title', 'Manajemen Permission')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER (TITLE & ACTION BUTTONS) ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-key text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Manajemen Permission</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola daftar wewenang otoritas sistem dan pemetaan grup modul aplikasi
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
                <span class="font-bold text-slate-800">Permissions</span>
            </nav>

            <button type="button" id="btnCreatePermission"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-plus text-sm"></i>
                <span>Tambah Permission Baru</span>
            </button>
        </div>
    </div>

    <!-- ================= 2. FILTER TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('permissions.index') }}" method="GET" class="w-full">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 sm:gap-3 w-full items-center">
                <!-- Search Name -->
                <div class="sm:col-span-6 lg:col-span-7 relative w-full">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" name="name" id="name_search"
                           value="{{ request('name') }}"
                           placeholder="Cari nama permission (contoh: karyawan.index)..."
                           class="w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" />
                </div>

                <!-- Group Filter -->
                <div class="sm:col-span-3 lg:col-span-3">
                    <select name="id_permission_group" id="id_permission_group_filter"
                            class="w-full h-10 px-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300/90 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
                        <option value="">Semua Group Modul</option>
                        @foreach ($permission_groups as $group)
                            <option value="{{ $group->id }}" @selected(request('id_permission_group') == $group->id)>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search & Reset Actions -->
                <div class="sm:col-span-3 lg:col-span-2 flex items-center gap-2 w-full justify-end">
                    <button type="submit" 
                            class="flex-1 h-10 px-4 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95 active:scale-95 whitespace-nowrap"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-search text-sm"></i>
                        <span>Cari</span>
                    </button>
                    @if(request()->filled('name') || request()->filled('id_permission_group'))
                        <a href="{{ route('permissions.index') }}" 
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

    <!-- ================= 3. SEAMLESS SOLID TABLE ================= -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <!-- Seamless Solid Card Header -->
        <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
             style="background-color: {{ $themeColor1 }};">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                    <i class="ti ti-key"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-white tracking-tight">
                        Daftar Wewenang Hak Akses (Permissions)
                    </h3>
                </div>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                {{ $permissions->total() }} Total Permission
            </span>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-left text-xs border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-3.5 text-center w-14 text-white/90 border-0 border-t-0">No</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[250px]">Nama Permission</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0 min-w-[180px]">Group Modul</th>
                        <th class="py-2.5 px-3.5 text-center w-28 text-white/90 border-0 border-t-0">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                    @forelse ($permissions as $idx => $d)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <!-- No -->
                        <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold">
                            {{ $permissions->firstItem() + $idx }}
                        </td>

                        <!-- Nama Permission -->
                        <td class="py-2.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-xs text-white shrink-0 shadow-2xs"
                                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                    <i class="ti ti-key text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-mono font-extrabold text-slate-900 text-xs sm:text-[13px] tracking-tight text-emerald-800">
                                        {{ strtolower($d->name) }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        ID: <span class="font-mono text-slate-500">#{{ $d->id }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Group Modul -->
                        <td class="py-2.5 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="ti ti-folder text-xs"></i>
                                <span>{{ $d->group_name }}</span>
                            </span>
                        </td>

                        <!-- Aksi Buttons -->
                        <td class="py-2.5 px-3.5 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button type="button"
                                        class="btnEditPermission w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                        data-id="{{ Crypt::encrypt($d->id) }}"
                                        title="Edit Permission">
                                    <i class="ti ti-edit text-sm"></i>
                                </button>

                                <form method="POST" action="{{ route('permissions.delete', Crypt::encrypt($d->id)) }}" class="formDeletePermission m-0 inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btnDeletePermission w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 cursor-pointer"
                                            title="Hapus Permission">
                                        <i class="ti ti-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-slate-400">
                            <i class="ti ti-key text-3xl mb-2 block text-slate-300"></i>
                            <span>Tidak ada data permission yang sesuai dengan pencarian.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($permissions->hasPages())
            <div class="px-5 py-3.5 bg-white border-t border-slate-100">
                {{ $permissions->links('v2.components.pagination') }}
            </div>
        @endif
    </div>

</div>

<!-- ================= 4. MODAL FORM V2 CONTAINER ================= -->
<div class="modal fade" id="modalPermissionV2" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden bg-white">
            
            <!-- Standard White Header with Gradient Icon Badge -->
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between text-slate-900">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold text-white shadow-sm"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-key" id="modalPermissionIcon"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate mb-0" id="modalPermissionTitle">
                            Tambah Permission
                        </h5>
                        <p class="text-xs text-slate-500 mb-0 font-medium" id="modalPermissionSubtitle">
                            Formulir penambahan wewenang otoritas fitur sistem
                        </p>
                    </div>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer shrink-0" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>

            <!-- Modal Dynamic Body Container -->
            <div class="p-6" id="loadModalPermissionBody">
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
        $('#btnCreatePermission').on('click', function() {
            $('#modalPermissionTitle').text('Tambah Permission Baru');
            $('#modalPermissionSubtitle').text('Lengkapi formulir berikut untuk menambahkan hak akses permission baru');
            $('#modalPermissionIcon').attr('class', 'ti ti-key-plus');
            $('#modalPermissionV2').modal('show');
            $('#loadModalPermissionBody').html(`
                <div class="py-10 text-center">
                    <i class="ti ti-loader animate-spin text-2xl text-emerald-600"></i>
                    <p class="text-xs text-slate-500 mt-2">Memuat formulir permission...</p>
                </div>
            `);
            $('#loadModalPermissionBody').load('/permissions/create');
        });

        // Open Edit Modal
        $(document).on('click', '.btnEditPermission', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $('#modalPermissionTitle').text('Edit Data Permission');
            $('#modalPermissionSubtitle').text('Perbarui nama permission atau pemindahan group modul');
            $('#modalPermissionIcon').attr('class', 'ti ti-key-cog');
            $('#modalPermissionV2').modal('show');
            $('#loadModalPermissionBody').html(`
                <div class="py-10 text-center">
                    <i class="ti ti-loader animate-spin text-2xl text-emerald-600"></i>
                    <p class="text-xs text-slate-500 mt-2">Memuat data permission...</p>
                </div>
            `);
            $('#loadModalPermissionBody').load('/permissions/' + id + '/edit');
        });

        // SweetAlert2 Delete Confirmation
        $(document).on('click', '.btnDeletePermission', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');

            Swal.fire({
                title: 'Hapus Permission Ini?',
                text: 'Menghapus permission akan mencabut hak akses ini dari seluruh role dan user terkait.',
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
