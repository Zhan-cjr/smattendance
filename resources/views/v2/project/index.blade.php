@extends('v2.layouts.app')

@section('titlepage', 'Manajemen Project')
@section('page_title', 'Manajemen Project')

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
                <i class="ti ti-briefcase text-2xl" style="color: {{ $themeColor1 }};"></i>
                <span>Manajemen Project</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola pendelegasian tugas, koordinasi tim kerja, dan pantau progres capaian project
            </p>
        </div>

        <!-- Breadcrumbs & Action Buttons -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Project</span>
            </nav>

            <!-- Actions Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @can('projectcategory.index')
                    <a href="{{ route('projectcategory.index') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-slate-900 text-xs font-bold transition shadow-2xs cursor-pointer">
                        <i class="ti ti-category text-sm text-sky-600"></i>
                        <span>Kategori Project</span>
                    </a>
                @endcan

                @can('project.create')
                    <button type="button" id="btnCreateProjectV2"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Buat Project Baru</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. EXECUTIVE STATISTICS CARD (SOLID THEME SEAMLESS WITH TAPERED DIVIDERS) ================= -->
    <div class="rounded-2xl shadow-sm text-white p-5 sm:p-6 border relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); border-color: rgba(0,0,0,0.12);">
        
        <!-- Clean Minimalist Corner Watermark & Glow -->
        <div class="absolute -right-6 -bottom-6 w-56 h-56 rounded-full bg-white/5 pointer-events-none blur-xl"></div>
        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-white/[0.07] pointer-events-none select-none">
            <i class="ti ti-briefcase text-[180px] leading-none"></i>
        </div>

        <div class="relative z-10 flex flex-wrap md:flex-nowrap items-stretch justify-between gap-y-6">
            
            <!-- Segment 1: Total Project -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Total Project
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25 shadow-2xs">
                            Semua
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($stats['total'] ?? 0) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-folder text-xs opacity-80"></i>
                        <span>Portofolio Proyek</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        100%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 2: In Progress -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Sedang Berjalan
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-sky-400/30 text-white border border-sky-300/40 shadow-2xs">
                            Active
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($stats['in_progress'] ?? 0) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-loader text-xs opacity-80"></i>
                        <span>Proyek Aktif</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ ($stats['total'] ?? 0) > 0 ? round((($stats['in_progress'] ?? 0) / $stats['total']) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 3: Completed Project -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Project Selesai
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-400/40 text-white border border-emerald-300/50 shadow-2xs">
                            Done
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ number_format($stats['completed'] ?? 0) }}
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-circle-check text-xs opacity-80"></i>
                        <span>Tercapai Sukses</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        {{ ($stats['total'] ?? 0) > 0 ? round((($stats['completed'] ?? 0) / $stats['total']) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            <!-- Tapered Vertical Divider Line -->
            <div class="hidden md:block w-[1px] bg-gradient-to-b from-transparent via-white/35 to-transparent self-center h-16 shrink-0"></div>

            <!-- Segment 4: Rata-rata Progres -->
            <div class="flex-1 min-w-[130px] sm:min-w-[150px] px-3 sm:px-4 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-100 truncate">
                            Rata-Rata Capaian
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-white/20 text-white border border-white/25 shadow-2xs">
                            Progress
                        </span>
                    </div>
                    <div class="text-3xl sm:text-4xl font-black text-white tracking-tight mt-1.5">
                        {{ $stats['avg_progress'] ?? 0 }}%
                    </div>
                </div>
                <div class="mt-3.5 pt-2 flex items-center justify-between text-xs text-emerald-100/90 border-t border-white/15">
                    <span class="font-medium text-[11px] flex items-center gap-1">
                        <i class="ti ti-chart-pie text-xs opacity-80"></i>
                        <span>Efisiensi Proyek</span>
                    </span>
                    <span class="font-black text-white text-[10px] bg-white/15 px-2 py-0.5 rounded-md">
                        Rasio
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= 3. FILTER & SEARCH TOOLBAR ================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form action="{{ route('project.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <!-- Search Nama / Kode -->
                <div class="lg:col-span-4 relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-search text-base"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari kode atau nama project..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium"
                           style="--tw-ring-color: {{ $themeColor1 }};">
                </div>

                <!-- Kategori Filter -->
                <div class="lg:col-span-2">
                    <select name="category_id"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium cursor-pointer"
                            style="--tw-ring-color: {{ $themeColor1 }};">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="lg:col-span-2">
                    <select name="status"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium cursor-pointer"
                            style="--tw-ring-color: {{ $themeColor1 }};">
                        <option value="">Semua Status</option>
                        <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Departemen Filter -->
                <div class="lg:col-span-2">
                    <select name="kode_dept"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium cursor-pointer"
                            style="--tw-ring-color: {{ $themeColor1 }};">
                        <option value="">Semua Dept</option>
                        @foreach ($departements as $d)
                            <option value="{{ $d->kode_dept }}" {{ request('kode_dept') == $d->kode_dept ? 'selected' : '' }}>
                                {{ $d->nama_dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Button Actions -->
                <div class="lg:col-span-2 flex gap-1.5">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center p-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});"
                            title="Terapkan Filter">
                        <i class="ti ti-filter text-base me-1"></i>
                        <span>Filter</span>
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'status', 'kode_dept', 'kode_cabang']))
                        <a href="{{ route('project.index') }}"
                           class="inline-flex items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-800 text-xs font-bold transition shadow-2xs cursor-pointer"
                           title="Reset Filter">
                            <i class="ti ti-refresh text-base"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- ================= 3. PROJECT CARD LIST (FULL WIDTH) ================= -->
    <div class="space-y-3">
        @forelse ($projects as $proj)
            @php
                $leader = $proj->members->where('role', 'leader')->first();
                $leaderName = $leader && $leader->karyawan ? $leader->karyawan->nama_karyawan : 'Belum ditentukan';
                $isOverdue = ($proj->status !== 'completed' && $proj->end_date->isPast());
                $categoryColor = $proj->category->warna ?? $themeColor1;
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 transition-all duration-200 hover:border-slate-300 hover:shadow-sm">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Left: Category Initial + Title & Info -->
                    <div class="flex items-start sm:items-center gap-3.5 min-w-0 flex-1">
                        <div class="w-12 h-12 rounded-xl text-white font-extrabold flex items-center justify-center text-sm shrink-0 shadow-2xs"
                             style="background-color: {{ $categoryColor }};">
                            {{ strtoupper(substr($proj->nama_project, 0, 2)) }}
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('project.show', Crypt::encrypt($proj->id)) }}" 
                                   class="text-sm sm:text-base font-bold text-slate-900 hover:text-emerald-700 transition truncate">
                                    {{ $proj->nama_project }}
                                </a>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                                    {{ $proj->kode_project }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-slate-500 font-medium">
                                <span class="inline-flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $categoryColor }};"></span>
                                    {{ $proj->category ? $proj->category->nama_kategori : 'Umum' }}
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="flex items-center gap-1">
                                    <i class="ti ti-crown text-amber-500 text-xs"></i>
                                    Leader: <strong class="text-slate-700">{{ formatName($leaderName) }}</strong>
                                </span>
                                @if($proj->departemen)
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1">
                                        <i class="ti ti-building text-slate-400"></i>
                                        {{ $proj->departemen->nama_dept }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Center: Timeline, Status & Progress -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex items-center gap-4 sm:gap-6 py-3 lg:py-0 border-y lg:border-y-0 lg:border-x border-slate-100 lg:px-6">
                        <!-- Timeline & Status -->
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                @if ($proj->status === 'planning')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        Planning
                                    </span>
                                @elseif ($proj->status === 'in_progress')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                        In Progress
                                    </span>
                                @elseif ($proj->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Completed
                                    </span>
                                @elseif ($proj->status === 'on_hold')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        On Hold
                                    </span>
                                @elseif ($proj->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        Cancelled
                                    </span>
                                @endif

                                @if ($isOverdue)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="ti ti-alert-triangle text-[10px]"></i> Overdue
                                    </span>
                                @endif
                            </div>
                            <span class="block text-[11px] text-slate-500 font-medium">
                                {{ $proj->start_date->format('d M Y') }} - {{ $proj->end_date->format('d M Y') }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full sm:w-36 lg:w-40">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Progres</span>
                                <span class="text-xs font-black text-slate-800">{{ $proj->progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-300"
                                     style="width: {{ $proj->progress }}%; background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                </div>
                            </div>
                            <span class="block text-[10px] text-slate-400 mt-1 font-medium">
                                {{ $proj->tasks->count() }} Tugas ({{ $proj->tasks->where('status', 'completed')->count() }} Selesai)
                            </span>
                        </div>
                    </div>

                    <!-- Right: Action Buttons -->
                    <div class="flex items-center justify-end gap-2 shrink-0">
                        <a href="{{ route('project.show', Crypt::encrypt($proj->id)) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-xs cursor-pointer active:scale-95">
                            <i class="ti ti-layout-kanban text-sm"></i>
                            <span>Board & Tugas</span>
                        </a>

                        @can('project.edit')
                            <button type="button" 
                                    class="btnEditProjectV2 w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                    data-href="{{ route('project.edit', Crypt::encrypt($proj->id)) }}"
                                    title="Edit Project">
                                <i class="ti ti-edit text-base"></i>
                            </button>
                        @endcan

                        @can('project.delete')
                            <form action="{{ route('project.delete', Crypt::encrypt($proj->id)) }}" method="POST" class="deleteform inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        class="btnDeleteProjectV2 w-9 h-9 rounded-xl border border-rose-200/80 bg-rose-50/50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                        title="Hapus Project">
                                    <i class="ti ti-trash text-base"></i>
                                </button>
                            </form>
                        @endcan
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200/90 p-8 sm:p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                    <i class="ti ti-folder-off text-3xl"></i>
                </div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800">Tidak ada project yang ditemukan</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                    Belum ada project terdaftar atau tidak sesuai dengan kriteria filter yang diterapkan.
                </p>
                @can('project.create')
                    <div class="mt-4">
                        <button type="button" id="btnCreateProjectEmptyV2"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-plus text-sm"></i>
                            <span>Buat Project Pertama</span>
                        </button>
                    </div>
                @endcan
            </div>
        @endforelse
    </div>

    <!-- ================= 4. PAGINATION ================= -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
        <p class="text-xs text-slate-500 font-medium">
            Menampilkan {{ $projects->firstItem() ?? 0 }} - {{ $projects->lastItem() ?? 0 }} dari {{ $projects->total() }} project
        </p>
        <div>
            {{ $projects->links('pagination::tailwind') }}
        </div>
    </div>

</div>

<!-- ================= 5. MODAL FORM CREATE / EDIT PROJECT V2 ================= -->
<div id="modalProjectV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-5xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all my-6 sm:my-8">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}12);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-briefcase text-lg" id="modalProjectIcon"></i>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-800 tracking-tight" id="modalProjectTitle">Buat Project Baru</h2>
                    <p class="text-xs text-slate-500 font-medium" id="modalProjectSubtitle">Daftarkan project baru dan koordinasikan tim pengerjaan</p>
                </div>
            </div>
            <button type="button" class="btnCloseModalProject w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 flex items-center justify-center transition cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <!-- Modal Body Container (AJAX Loaded) -->
        <div class="p-6 max-h-[75vh] overflow-y-auto" id="modalProjectBody">
            <!-- Loading Spinner Placeholder -->
            <div class="py-12 text-center text-slate-400">
                <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-semibold">Memuat Form Project...</span>
            </div>
        </div>

    </div>
</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    function openModal() {
        $('#modalProjectV2').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    }

    function closeModal() {
        $('#modalProjectV2').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    }

    $(document).on('click', '.btnCloseModalProject', closeModal);

    $('#modalProjectV2').on('click', function(e) {
        if (e.target === this) closeModal();
    });

    $(document).keydown(function(e) {
        if (e.key === 'Escape' && !$('#modalProjectV2').hasClass('hidden')) {
            closeModal();
        }
    });

    function showLoading() {
        $('#modalProjectBody').html(`
            <div class="py-12 text-center text-slate-400">
                <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-semibold">Memuat Form Project...</span>
            </div>
        `);
    }

    // Open Create Modal
    $('#btnCreateProjectV2, #btnCreateProjectEmptyV2').on('click', function() {
        $('#modalProjectTitle').text('Buat Project Baru');
        $('#modalProjectSubtitle').text('Daftarkan project baru dan koordinasikan tim pengerjaan');
        $('#modalProjectIcon').attr('class', 'ti ti-plus text-lg');
        showLoading();
        openModal();
        $('#modalProjectBody').load("{{ route('project.create') }}");
    });

    // Open Edit Modal
    $(document).on('click', '.btnEditProjectV2', function() {
        const href = $(this).data('href');
        $('#modalProjectTitle').text('Edit Project');
        $('#modalProjectSubtitle').text('Perbarui detail project, target deadline, dan anggota tim');
        $('#modalProjectIcon').attr('class', 'ti ti-edit text-lg');
        showLoading();
        openModal();
        $('#modalProjectBody').load(href);
    });

    // SweetAlert2 Delete Confirmation
    $(document).on('click', '.btnDeleteProjectV2', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Project Ini?',
            text: 'Project beserta seluruh daftar tugas dan lampiran di dalamnya akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '{{ $themeColor1 }}',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-slate-100',
                confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold shadow-xs',
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
