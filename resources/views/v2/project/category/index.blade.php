@extends('v2.layouts.app')

@section('titlepage', 'Kategori Project')
@section('page_title', 'Kategori Project')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $user = auth()->user();
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle -->
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="ti ti-tags text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Kategori Project</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola label kategori dan klasifikasi proyek organisasi
            </p>
        </div>

        <!-- Breadcrumbs Navigation & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('project.index') }}" class="hover:text-slate-700 transition">Project</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Kategori</span>
            </nav>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('project.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-slate-900 text-xs font-bold transition shadow-2xs cursor-pointer">
                    <i class="ti ti-briefcase text-sm text-sky-600"></i>
                    <span>Daftar Project</span>
                </a>

                @can('projectcategory.create')
                    <button type="button" id="btnCreateCategoryV2"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Kategori</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (Session::get('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-circle-check text-emerald-600 text-base"></i>
                <span class="font-semibold">{{ Session::get('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900"><i class="ti ti-x"></i></button>
        </div>
    @endif
    @if (Session::get('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-900 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-alert-circle text-rose-600 text-base"></i>
                <span class="font-semibold">{{ Session::get('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900"><i class="ti ti-x"></i></button>
        </div>
    @endif

    <!-- ================= 2. CATEGORY LIST & METRIC CARDS ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Left Table: Daftar Kategori (8 Cols) -->
        <div class="lg:col-span-8 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <!-- Seamless Solid Card Header Sesuai Pengaturan Warna Tema -->
                <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-white border-0"
                     style="background-color: {{ $themeColor1 }};">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                            <i class="ti ti-tags"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-white tracking-tight">Daftar Kategori Project</h3>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs self-start sm:self-auto">
                        {{ $categories->count() }} data
                    </span>
                </div>

                <!-- Responsive Table Container with Matching Solid Table Header -->
                <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
                    <table class="w-full text-left text-xs border-0 border-collapse">
                        <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                               style="background-color: {{ $themeColor1 }};">
                            <tr class="border-0 border-t-0 text-white/90">
                                <th class="py-2.5 px-3.5 text-center w-12 text-white/90 border-0 border-t-0">No</th>
                                <th class="py-2.5 px-3.5 w-28 text-white/90 border-0 border-t-0">Warna Label</th>
                                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Nama Kategori</th>
                                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Deskripsi</th>
                                <th class="py-2.5 px-3.5 text-center w-24 text-white/90 border-0 border-t-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
                            @forelse ($categories as $cat)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
                                        <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                            {{ $loop->iteration }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3.5">
                                        <div class="flex items-center gap-2">
                                            <span class="w-4 h-4 rounded-full shadow-2xs shrink-0 border border-black/10" style="background-color: {{ $cat->warna }};"></span>
                                            <span class="font-mono text-[11px] font-bold text-slate-600">{{ $cat->warna }}</span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-4">
                                        <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-bold text-white shadow-2xs inline-block"
                                              style="background-color: {{ $cat->warna }};">
                                            {{ $cat->nama_kategori }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-500 text-xs leading-relaxed">
                                        {{ $cat->deskripsi ?? '-' }}
                                    </td>
                                    <td class="py-2.5 px-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            @can('projectcategory.edit')
                                                <button type="button" 
                                                        class="btnEditCategoryV2 w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer"
                                                        data-href="{{ route('projectcategory.edit', Crypt::encrypt($cat->id)) }}"
                                                        title="Edit Kategori">
                                                    <i class="ti ti-edit text-xs"></i>
                                                </button>
                                            @endcan

                                            @can('projectcategory.delete')
                                                <form action="{{ route('projectcategory.delete', Crypt::encrypt($cat->id)) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btnDeleteCategoryV2 w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 shadow-2xs cursor-pointer" title="Hapus Kategori">
                                                        <i class="ti ti-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-10 text-slate-400 bg-white">
                                        <i class="ti ti-tags-off text-3xl block mb-1 text-slate-300"></i>
                                        Belum ada kategori project terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Section: Info & Panduan Penggunaan (4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Quick Summary Card -->
            <div class="rounded-2xl p-5 shadow-sm text-white relative overflow-hidden space-y-3"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                
                <div class="absolute -right-6 -bottom-6 w-40 h-40 rounded-full bg-white/5 pointer-events-none blur-xl"></div>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-white/[0.08] pointer-events-none select-none">
                    <i class="ti ti-tags text-[110px] leading-none"></i>
                </div>

                <div class="relative z-10">
                    <span class="text-[11px] font-medium text-white/80 uppercase tracking-wider block mb-1">Total Kategori</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-white">{{ $categories->count() }} Kategori</h2>
                    <p class="text-xs text-white/90 leading-relaxed mt-2">
                        Warna label kategori akan ditampilkan pada kartu project, kanban board, dan penanda detail tugas.
                    </p>
                </div>
            </div>

            <!-- Panduan Box -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs space-y-3">
                <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">
                        <i class="ti ti-bulb"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800">Tips Penggunaan</h4>
                    </div>
                </div>
                <ul class="text-xs text-slate-600 space-y-2 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <i class="ti ti-check text-emerald-600 text-sm shrink-0 mt-0.5"></i>
                        <span>Pilihlah warna kontras agar badge kategori mudah dibedakan secara visual.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="ti ti-check text-emerald-600 text-sm shrink-0 mt-0.5"></i>
                        <span>Kategori dapat mencerminkan divisi tim, jenis platform (Web, Mobile), atau model kontrak.</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>

</div>

<!-- ================= 3. DYNAMIC AJAX MODAL CATEGORY ================= -->
<div id="modalCategoryV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all my-6">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}12);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white shadow-xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-tags text-base" id="modalCategoryIcon"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-800 tracking-tight" id="modalCategoryTitle">Kategori Project</h2>
                    <p class="text-[11px] text-slate-500 font-medium" id="modalCategorySubtitle">Atur nama dan warna label kategori</p>
                </div>
            </div>
            <button type="button" class="btnCloseModalCategory w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 flex items-center justify-center transition cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <div class="p-6 max-h-[75vh] overflow-y-auto" id="modalCategoryBody">
            <div class="py-12 text-center text-slate-400">
                <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-semibold">Memuat Form...</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    function openModal() {
        $('#modalCategoryV2').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    }

    function closeModal() {
        $('#modalCategoryV2').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    }

    $(document).on('click', '.btnCloseModalCategory', closeModal);

    $('#modalCategoryV2').on('click', function(e) {
        if (e.target === this) closeModal();
    });

    function showLoading() {
        $('#modalCategoryBody').html(`
            <div class="py-12 text-center text-slate-400">
                <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-semibold">Memuat Form...</span>
            </div>
        `);
    }

    // Create Modal
    $('#btnCreateCategoryV2').on('click', function() {
        $('#modalCategoryTitle').text('Tambah Kategori Project');
        $('#modalCategorySubtitle').text('Tambahkan jenis kategori baru untuk pengelompokan project');
        $('#modalCategoryIcon').attr('class', 'ti ti-plus text-base');
        showLoading();
        openModal();
        $('#modalCategoryBody').load("{{ route('projectcategory.create') }}");
    });

    // Edit Modal
    $(document).on('click', '.btnEditCategoryV2', function() {
        const href = $(this).data('href');
        $('#modalCategoryTitle').text('Edit Kategori Project');
        $('#modalCategorySubtitle').text('Perbarui nama, warna label, atau deskripsi kategori');
        $('#modalCategoryIcon').attr('class', 'ti ti-edit text-base');
        showLoading();
        openModal();
        $('#modalCategoryBody').load(href);
    });

    // Delete Confirmation
    $(document).on('click', '.btnDeleteCategoryV2', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Kategori Ini?',
            text: 'Project yang terhubung dengan kategori ini mungkin akan kehilangan label!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-slate-100',
                confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold',
                cancelButton: 'px-4 py-2 rounded-xl text-xs font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
