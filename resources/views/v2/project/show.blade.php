@extends('v2.layouts.app')

@section('titlepage', 'Detail Project: ' . $project->nama_project)
@section('page_title', 'Detail Project')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $user = auth()->user();
    $categoryColor = $project->category->warna ?? $themeColor1;
    $isOverdue = ($project->status !== 'completed' && $project->end_date->isPast());
@endphp

<style>
    .kanban-task-card.dragging {
        opacity: 0.4;
        transform: scale(0.97);
    }
    .kanban-column.drag-over {
        background-color: rgba(16, 185, 129, 0.08) !important;
        border-color: #10b981 !important;
    }
</style>

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle with Back Button -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('project.index') }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('project.index') }}" class="hover:text-emerald-600 transition-colors">Project</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Detail & Board</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 mt-0.5 truncate">
                    <i class="ti ti-briefcase text-2xl shrink-0" style="color: {{ $themeColor1 }};"></i>
                    <span class="truncate">{{ $project->nama_project }}</span>
                </h1>
            </div>
        </div>

        <!-- Breadcrumbs Navigation & Actions -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('project.index') }}" class="hover:text-slate-700 transition">Project</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Detail</span>
            </nav>

            <div class="flex items-center gap-2">
                @can('project.edit')
                    <button type="button" 
                            class="btnEditProjectV2 inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-2xs cursor-pointer"
                            data-href="{{ route('project.edit', Crypt::encrypt($project->id)) }}">
                        <i class="ti ti-edit text-sm"></i>
                        <span>Edit Project</span>
                    </button>
                @endcan

                @can('project.task.create')
                    <button type="button" id="btnCreateTaskV2"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-plus text-sm"></i>
                        <span>Tambah Task Baru</span>
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- ================= 2. PROJECT OVERVIEW CARD (Solid Theme Card with Clean Watermark) ================= -->
    <div class="rounded-2xl p-5 sm:p-6 shadow-sm space-y-4 text-white relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
        
        <!-- Clean Minimalist Corner Watermark & Glow -->
        <div class="absolute -right-6 -bottom-6 w-56 h-56 rounded-full bg-white/5 pointer-events-none blur-xl"></div>
        <div class="absolute right-6 top-1/2 -translate-y-1/2 text-white/[0.07] pointer-events-none select-none">
            <i class="ti ti-briefcase text-[160px] leading-none"></i>
        </div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Info (8 cols) -->
            <div class="lg:col-span-8 space-y-4">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="px-3 py-1 rounded-lg text-xs font-extrabold text-white shadow-2xs"
                          style="background-color: {{ $categoryColor }};">
                        {{ $project->category ? $project->category->nama_kategori : 'Umum' }}
                    </span>
                    <span class="px-3 py-1 rounded-lg text-xs font-black bg-white/20 text-white backdrop-blur-md border border-white/30 shadow-2xs">
                        {{ $project->kode_project }}
                    </span>

                    @if ($project->status === 'planning')
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-white text-slate-800 shadow-2xs">Planning</span>
                    @elseif ($project->status === 'in_progress')
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-white/20 text-white backdrop-blur-md border border-white/30 shadow-2xs">In Progress</span>
                    @elseif ($project->status === 'completed')
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-400 text-white shadow-2xs">Completed</span>
                    @elseif ($project->status === 'on_hold')
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-400 text-slate-900 shadow-2xs">On Hold</span>
                    @elseif ($project->status === 'cancelled')
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-rose-500 text-white shadow-2xs">Cancelled</span>
                    @endif

                    @if ($isOverdue)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-black bg-rose-500 text-white shadow-2xs">
                            <i class="ti ti-alert-triangle text-xs"></i> Overdue
                        </span>
                    @endif
                </div>

                @if(!empty($project->deskripsi))
                    <p class="text-xs sm:text-sm text-white/90 leading-relaxed">{{ $project->deskripsi }}</p>
                @endif

                <!-- Meta Specs Section with Modern Sleek Separators -->
                <div class="py-2 border-t border-b border-white/20">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-y-3 gap-x-4 lg:divide-x lg:divide-white/20">
                        
                        <!-- Tanggal Mulai -->
                        <div class="flex items-center gap-2.5 pr-2">
                            <div class="w-8 h-8 rounded-lg bg-white/15 backdrop-blur-md text-white flex items-center justify-center shrink-0 border border-white/25">
                                <i class="ti ti-calendar text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10.5px] font-medium text-white/70">Tanggal Mulai</span>
                                <span class="text-xs sm:text-sm font-bold text-white truncate block">
                                    {{ $project->start_date->format('d M Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- Target Deadline -->
                        <div class="flex items-center gap-2.5 lg:pl-3 pr-2">
                            <div class="w-8 h-8 rounded-lg {{ $isOverdue ? 'bg-rose-500 text-white' : 'bg-white/15 text-white' }} backdrop-blur-md border border-white/25 flex items-center justify-center shrink-0">
                                <i class="ti ti-calendar-due text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10.5px] font-medium text-white/70">Target Deadline</span>
                                <span class="text-xs sm:text-sm font-bold text-white truncate block">
                                    {{ $project->end_date->format('d M Y') }}
                                </span>
                            </div>
                        </div>

                        <!-- Departemen -->
                        <div class="flex items-center gap-2.5 lg:pl-3 pr-2">
                            <div class="w-8 h-8 rounded-lg bg-white/15 backdrop-blur-md text-white flex items-center justify-center shrink-0 border border-white/25">
                                <i class="ti ti-building text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10.5px] font-medium text-white/70">Departemen</span>
                                <span class="text-xs sm:text-sm font-bold text-white truncate block" title="{{ $project->departemen ? $project->departemen->nama_dept : 'Semua Departemen' }}">
                                    {{ $project->departemen ? $project->departemen->nama_dept : 'Semua Dept' }}
                                </span>
                            </div>
                        </div>

                        <!-- Anggaran Budget -->
                        <div class="flex items-center gap-2.5 lg:pl-3">
                            <div class="w-8 h-8 rounded-lg bg-white/15 backdrop-blur-md text-white flex items-center justify-center shrink-0 border border-white/25">
                                <i class="ti ti-coin text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10.5px] font-medium text-white/70">Anggaran Budget</span>
                                <span class="text-xs sm:text-sm font-bold text-white truncate block">
                                    {{ $project->budget ? 'Rp ' . formatRupiah($project->budget) : 'Rp 0' }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                @if(!empty($project->catatan))
                    <div class="p-3.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-xs text-white/95 leading-relaxed shadow-inner">
                        <span class="font-bold flex items-center gap-1.5 mb-1 text-white">
                            <i class="ti ti-note"></i> Catatan Khusus
                        </span>
                        <p class="text-white/90 leading-normal">{{ $project->catatan }}</p>
                    </div>
                @endif
            </div>

            <!-- Right Progress & Team Box (4 cols) -->
            <div class="lg:col-span-4 flex flex-col justify-between p-5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 shadow-inner space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-white/90">Capaian Progres Project</span>
                        <span class="text-lg font-black text-white">{{ $project->progress }}%</span>
                    </div>
                    <div class="w-full bg-white/20 h-3 rounded-full overflow-hidden border border-white/25">
                        <div class="h-full bg-white rounded-full transition-all duration-300"
                             style="width: {{ $project->progress }}%;">
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-white/80 font-medium mt-1.5">
                        <span>{{ $project->tasks->count() }} Total Tugas</span>
                        <span>{{ $project->tasks->where('status', 'completed')->count() }} Tugas Selesai</span>
                    </div>
                </div>

                <!-- Tim Member Preview -->
                <div class="pt-4 border-t border-white/20">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-bold text-white flex items-center gap-1.5">
                            <i class="ti ti-users text-white/80"></i> Tim Project ({{ $project->members->count() }})
                        </span>
                        @can('project.edit')
                            <button type="button" id="btnOpenAddMemberV2"
                                    class="text-xs font-bold text-white hover:text-white/80 transition flex items-center gap-1 cursor-pointer bg-white/20 hover:bg-white/30 px-2.5 py-1 rounded-lg border border-white/30">
                                <i class="ti ti-user-plus"></i> Tambah
                            </button>
                        @endcan
                    </div>

                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach ($project->members as $member)
                            @php
                                $isLeader = $member->role === 'leader';
                                $k = $member->karyawan;
                            @endphp
                            <div class="flex items-center justify-between p-2 rounded-xl bg-white/15 backdrop-blur-md border border-white/20 shadow-2xs">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 {{ $isLeader ? 'bg-amber-300 text-amber-950 font-black' : 'bg-white/25 text-white' }}">
                                        {{ $k ? substr($k->nama_karyawan, 0, 1) : '?' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-white truncate">
                                            {{ $k ? $k->nama_karyawan : 'Karyawan' }}
                                        </div>
                                        <div class="text-[10.5px] text-white/70 truncate">
                                            {{ $k && $k->jabatan ? $k->jabatan->nama_jabatan : 'Staf' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if ($isLeader)
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-amber-300 text-amber-950">
                                            Leader
                                        </span>
                                    @else
                                        @can('project.edit')
                                            <form action="{{ route('project.removemember', ['id' => Crypt::encrypt($project->id), 'nik' => $member->nik]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btnRemoveMemberV2 text-white/60 hover:text-rose-300 transition p-1 cursor-pointer" title="Keluarkan Anggota">
                                                    <i class="ti ti-trash text-sm"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ================= 3. TASK BOARD & LIST TABS ================= -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-2xs">
        
        <!-- Tab Navigation Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <button type="button" id="tabBtnList"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-2xs cursor-pointer tab-nav-btn active"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }}); color: white;">
                    <i class="ti ti-list-details me-1"></i>
                    <span>Daftar Tugas ({{ $project->tasks->count() }})</span>
                </button>
                <button type="button" id="tabBtnKanban"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition-all border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 cursor-pointer tab-nav-btn">
                    <i class="ti ti-layout-kanban me-1"></i>
                    <span>Kanban Board</span>
                </button>
            </div>

            @can('project.task.create')
                <button type="button" class="btnOpenCreateTaskModal inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold transition cursor-pointer">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Task</span>
                </button>
            @endcan
        </div>

        <!-- ================= 3A. VIEW: CARD LIST ================= -->
        <div id="viewTaskList" class="space-y-3 pt-4">
            @forelse ($project->tasks as $task)
                @php
                    $badgePrioritas = 'bg-slate-100 text-slate-700 border-slate-200';
                    if ($task->prioritas === 'high') $badgePrioritas = 'bg-amber-50 text-amber-700 border-amber-200';
                    elseif ($task->prioritas === 'critical') $badgePrioritas = 'bg-rose-50 text-rose-700 border-rose-200';
                    elseif ($task->prioritas === 'low') $badgePrioritas = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 transition-all duration-200 hover:border-slate-300 hover:shadow-2xs">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        
                        <!-- Left Info -->
                        <div class="flex items-start gap-3.5 flex-1 min-w-0">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 text-slate-700 bg-slate-100 border border-slate-200">
                                <i class="ti ti-checkbox text-base"></i>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('project.task.show', Crypt::encrypt($task->id)) }}" 
                                       class="text-sm font-bold text-slate-900 hover:text-emerald-700 transition">
                                        {{ $task->judul }}
                                    </a>
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/80">
                                        {{ $task->kode_task }}
                                    </span>
                                    @if($task->parent_id)
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                            Sub-task
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-slate-500 font-medium">
                                    <span class="flex items-center gap-1">
                                        <i class="ti ti-users text-slate-400"></i>
                                        {{ $task->members->count() }} Anggota Tim
                                    </span>
                                    <span class="text-slate-300">•</span>
                                    <span class="flex items-center gap-1">
                                        <i class="ti ti-calendar-due text-slate-400"></i>
                                        Deadline: {{ $task->due_date ? $task->due_date->format('d M Y') : 'Tanpa deadline' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Center Metrik -->
                        <div class="grid grid-cols-2 sm:flex sm:items-center gap-4 py-2 lg:py-0 border-y lg:border-y-0 lg:border-x border-slate-100 lg:px-5">
                            <div>
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status</span>
                                @if ($task->status === 'todo')
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">To Do</span>
                                @elseif ($task->status === 'in_progress')
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-sky-50 text-sky-700 border border-sky-200">In Progress</span>
                                @elseif ($task->status === 'review')
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">Review</span>
                                @elseif ($task->status === 'completed')
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Completed</span>
                                @elseif ($task->status === 'cancelled')
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">Cancelled</span>
                                @endif
                            </div>

                            <div>
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Prioritas</span>
                                <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold border {{ $badgePrioritas }}">
                                    {{ ucfirst($task->prioritas) }}
                                </span>
                            </div>

                            <!-- Progress mini bar -->
                            <div class="w-full sm:w-28 col-span-2 sm:col-span-1">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-bold text-slate-400">Progres</span>
                                    <span class="text-xs font-bold text-slate-800">{{ $task->progress }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $task->progress }}%;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div class="flex items-center justify-end gap-2 shrink-0">
                            <a href="{{ route('project.task.show', Crypt::encrypt($task->id)) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-2xs">
                                <i class="ti ti-eye text-sm"></i>
                                <span>Detail</span>
                            </a>

                            @can('project.task.edit')
                                <button type="button" 
                                        class="btnEditTaskV2 w-8 h-8 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                        data-href="{{ route('project.task.edit', Crypt::encrypt($task->id)) }}"
                                        title="Edit Task">
                                    <i class="ti ti-edit text-sm"></i>
                                </button>
                            @endcan
                        </div>

                    </div>
                </div>
            @empty
                <div class="p-8 text-center border border-dashed border-slate-200 rounded-2xl">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-2">
                        <i class="ti ti-checkbox text-2xl"></i>
                    </div>
                    <h4 class="text-xs sm:text-sm font-bold text-slate-800">Belum ada tugas terdaftar</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Mulai delegasikan pekerjaan dengan menambahkan task baru.</p>
                </div>
            @endforelse
        </div>

        <!-- ================= 3B. VIEW: KANBAN BOARD ================= -->
        <div id="viewTaskKanban" class="hidden pt-4">
            @php
                $columns = [
                    'todo' => ['title' => 'To Do', 'color' => 'slate', 'bg' => 'bg-slate-100', 'text' => 'text-slate-700'],
                    'in_progress' => ['title' => 'In Progress', 'color' => 'sky', 'bg' => 'bg-sky-50', 'text' => 'text-sky-700'],
                    'review' => ['title' => 'Review', 'color' => 'amber', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700'],
                    'completed' => ['title' => 'Completed', 'color' => 'emerald', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700']
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ($columns as $statusKey => $statusCfg)
                    @php
                        $statusTasks = $project->tasks->where('status', $statusKey);
                    @endphp
                    <div class="flex flex-col bg-slate-50/70 rounded-2xl p-3 border border-slate-200/80">
                        <!-- Column Header -->
                        <div class="flex items-center justify-between pb-3 px-1 border-b border-slate-200/60 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-{{ $statusCfg['color'] }}-500"></span>
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">{{ $statusCfg['title'] }}</h4>
                            </div>
                            <span class="px-2 py-0.5 rounded-lg text-xs font-extrabold {{ $statusCfg['bg'] }} {{ $statusCfg['text'] }}">
                                {{ $statusTasks->count() }}
                            </span>
                        </div>

                        <!-- Column Drop Area -->
                        <div class="space-y-2.5 min-h-[350px] max-h-[600px] overflow-y-auto p-1 kanban-column rounded-xl border-2 border-transparent transition-all"
                             data-status="{{ $statusKey }}">
                            @foreach ($statusTasks as $task)
                                @php
                                    $priorColor = 'bg-slate-100 text-slate-700 border-slate-200';
                                    if ($task->prioritas === 'high') $priorColor = 'bg-amber-50 text-amber-700 border-amber-200';
                                    elseif ($task->prioritas === 'critical') $priorColor = 'bg-rose-50 text-rose-700 border-rose-200';
                                    elseif ($task->prioritas === 'low') $priorColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                @endphp
                                <div class="bg-white rounded-xl p-3.5 border border-slate-200/90 shadow-2xs cursor-grab active:cursor-grabbing hover:border-slate-300 transition kanban-task-card"
                                     draggable="true" data-id="{{ Crypt::encrypt($task->id) }}">
                                    
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <a href="{{ route('project.task.show', Crypt::encrypt($task->id)) }}" 
                                           class="text-[11px] font-bold text-emerald-800 hover:underline">
                                            {{ $task->kode_task }}
                                        </a>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $priorColor }}">
                                            {{ strtoupper($task->prioritas) }}
                                        </span>
                                    </div>

                                    <h5 class="text-xs font-bold text-slate-800 line-clamp-2 mb-1.5">
                                        {{ $task->judul }}
                                    </h5>

                                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[10px] text-slate-400 font-medium">
                                        <span class="flex items-center gap-1">
                                            <i class="ti ti-calendar text-xs"></i>
                                            {{ $task->due_date ? $task->due_date->format('d/m') : '-' }}
                                        </span>
                                        <span class="font-bold text-slate-700">{{ $task->progress }}%</span>
                                    </div>
                                </div>
                            @endforeach

                            @if($statusTasks->isEmpty())
                                <div class="text-center py-8 text-slate-400 text-xs font-medium kanban-empty-placeholder">
                                    Belum ada tugas
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

<!-- ================= 4. MODAL ADD MEMBER V2 ================= -->
@can('project.edit')
<div id="modalAddMemberV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all my-8">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}12);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-user-plus text-lg"></i>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-800 tracking-tight">Tambah Anggota Project</h2>
                    <p class="text-xs text-slate-500 font-medium">Pilih satu atau beberapa karyawan untuk ditambahkan</p>
                </div>
            </div>
            <button type="button" class="btnCloseModalMember w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 flex items-center justify-center transition cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <form action="{{ route('project.addmember', Crypt::encrypt($project->id)) }}" method="POST" id="formAddMemberV2" class="p-6 space-y-4">
            @csrf
            @php
                $assignedNiks = $project->members->pluck('nik')->toArray();
                $nonMembers = \App\Models\Karyawan::with('jabatan')
                    ->where('status_aktif_karyawan', 1)
                    ->whereNotIn('nik', $assignedNiks)
                    ->orderBy('nama_karyawan')
                    ->get();
            @endphp

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700">
                        Pilih Karyawan <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnSelectAllAddMembers" class="text-[10px] font-bold text-emerald-700 hover:underline cursor-pointer">
                            Pilih Semua
                        </button>
                        <span class="text-slate-300 text-[10px]">|</span>
                        <button type="button" id="btnDeselectAllAddMembers" class="text-[10px] font-bold text-slate-500 hover:text-rose-600 cursor-pointer">
                            Reset
                        </button>
                    </div>
                </div>
                
                <!-- Quick Search Input -->
                <div class="relative mb-2">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-search text-xs"></i>
                    </span>
                    <input type="text" id="searchAddMemberInput" placeholder="Cari nama karyawan, NIK, jabatan..."
                           class="w-full pl-8 pr-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:border-transparent transition-all"
                           style="--tw-ring-color: {{ $themeColor1 }};">
                </div>

                <!-- Checklist Box -->
                <div class="border border-slate-200 rounded-2xl bg-slate-50/70 p-2.5 max-h-56 overflow-y-auto space-y-1.5" id="addMemberChecklistContainer">
                    @forelse ($nonMembers as $nm)
                        <label class="add-member-item flex items-center gap-2.5 p-2 rounded-xl bg-white border border-slate-200/80 hover:bg-slate-50 transition cursor-pointer text-xs select-none shadow-2xs"
                               data-search="{{ strtolower($nm->nama_karyawan . ' ' . $nm->nik . ' ' . ($nm->nik_show ?? '') . ' ' . ($nm->jabatan ? $nm->jabatan->nama_jabatan : '')) }}">
                            <input type="checkbox" name="members[]" value="{{ $nm->nik }}"
                                   class="add-member-checkbox rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300">
                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-[11px] shrink-0">
                                {{ substr($nm->nama_karyawan, 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-slate-800 block truncate">{{ $nm->nama_karyawan }}</span>
                                <span class="text-[10px] text-slate-400 block truncate">{{ !empty($nm->nik_show) ? $nm->nik_show : $nm->nik }} • {{ $nm->jabatan ? $nm->jabatan->nama_jabatan : 'Staf' }}</span>
                            </div>
                        </label>
                    @empty
                        <div class="text-xs text-slate-400 text-center py-6">
                            <i class="ti ti-users-check text-xl block mb-1"></i>
                            Semua karyawan aktif sudah tergabung dalam project ini
                        </div>
                    @endforelse
                    <div id="noAddMemberFound" class="hidden text-xs text-slate-400 text-center py-4">
                        <i class="ti ti-user-x text-base block mb-1"></i>
                        Tidak ada karyawan yang cocok dengan pencarian
                    </div>
                </div>
                <span class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_add_members"></span>
            </div>

            <div class="space-y-1 pt-1">
                <label for="member_role" class="block text-xs font-bold text-slate-700">
                    Peran dalam Project <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-shield-check text-sm"></i>
                    </span>
                    <select name="role" id="member_role" required
                            class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold cursor-pointer"
                            style="--tw-ring-color: {{ $themeColor1 }};">
                        <option value="member" selected>Anggota Tim (Member)</option>
                        <option value="leader">Pemimpin Project (Leader)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" class="btnCloseModalMember px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-2xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitAddMemberV2"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambahkan Anggota</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

<!-- ================= 5. DYNAMIC AJAX MODAL TASK & EDIT PROJECT ================= -->
<div id="modalTaskV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-5xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all my-6 sm:my-8">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}12);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-checkbox text-lg" id="modalTaskIcon"></i>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-800 tracking-tight" id="modalTaskTitle">Task Project</h2>
                    <p class="text-xs text-slate-500 font-medium" id="modalTaskSubtitle">Atur detail tugas dan penanggung jawab</p>
                </div>
            </div>
            <button type="button" class="btnCloseModalTask w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 flex items-center justify-center transition cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <div class="p-6 max-h-[75vh] overflow-y-auto" id="modalTaskBody">
            <div class="py-12 text-center text-slate-400">
                <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-semibold">Memuat Form Task...</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    // Tab switching between Card List & Kanban
    $('#tabBtnList').on('click', function() {
        $(this).css({
            'background': 'linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }})',
            'color': 'white'
        }).addClass('shadow-2xs');
        $('#tabBtnKanban').css({'background': 'white', 'color': '#475569'}).removeClass('shadow-2xs');
        $('#viewTaskList').removeClass('hidden');
        $('#viewTaskKanban').addClass('hidden');
    });

    $('#tabBtnKanban').on('click', function() {
        $(this).css({
            'background': 'linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }})',
            'color': 'white'
        }).addClass('shadow-2xs');
        $('#tabBtnList').css({'background': 'white', 'color': '#475569'}).removeClass('shadow-2xs');
        $('#viewTaskKanban').removeClass('hidden');
        $('#viewTaskList').addClass('hidden');
    });

    // Add Member Modal
    $('#btnOpenAddMemberV2').on('click', function() {
        $('#modalAddMemberV2').removeClass('hidden');
        $('body').addClass('overflow-hidden');
        $('.error-feedback').addClass('hidden').text('');
        $('select, input').removeClass('border-rose-500');
        $('#searchAddMemberInput').val('').trigger('input');
        setTimeout(() => $('#searchAddMemberInput').focus(), 100);
    });

    $('.btnCloseModalMember').on('click', function() {
        $('#modalAddMemberV2').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    });

    // Real-time Search Filter for Add Member Modal Checklist
    $('#searchAddMemberInput').on('input', function() {
        const query = $(this).val().toLowerCase().trim();
        let matchCount = 0;

        $('#addMemberChecklistContainer .add-member-item').each(function() {
            const text = $(this).data('search') || $(this).text().toLowerCase();
            if (!query || text.includes(query)) {
                $(this).removeClass('hidden').show();
                matchCount++;
            } else {
                $(this).addClass('hidden').hide();
            }
        });

        if (matchCount === 0 && query) {
            $('#noAddMemberFound').removeClass('hidden');
        } else {
            $('#noAddMemberFound').addClass('hidden');
        }
    });

    // Select All / Deselect All Add Members
    $('#btnSelectAllAddMembers').on('click', function() {
        $('#addMemberChecklistContainer .add-member-item:visible .add-member-checkbox').prop('checked', true);
        $('#err_add_members').addClass('hidden').text('');
    });

    $('#btnDeselectAllAddMembers').on('click', function() {
        $('#addMemberChecklistContainer .add-member-checkbox').prop('checked', false);
    });

    $('#formAddMemberV2').on('submit', function(e) {
        let checkedCount = $('#addMemberChecklistContainer .add-member-checkbox:checked').length;
        let hasError = false;

        $('.error-feedback').addClass('hidden').text('');
        $('select, input').removeClass('border-rose-500');

        if (checkedCount === 0) {
            $('#err_add_members').text('Silakan pilih minimal 1 karyawan dengan mencentang checklist di atas.').removeClass('hidden');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Karyawan',
                    text: 'Silakan centang minimal satu karyawan yang ingin ditambahkan.',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl'
                    }
                });
            }
            return false;
        }

        const btn = $('#btnSubmitAddMemberV2');
        btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed').html(`
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Menambahkan...</span>
        `);
    });

    // Remove Member Confirmation
    $(document).on('click', '.btnRemoveMemberV2', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Keluarkan Anggota?',
            text: 'Anggota ini tidak akan lagi memiliki akses tugas dalam project ini.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Keluarkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Dynamic Task & Project Edit Modal
    function openTaskModal() {
        $('#modalTaskV2').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    }

    function closeTaskModal() {
        $('#modalTaskV2').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    }

    $(document).on('click', '.btnCloseModalTask', closeTaskModal);

    function showTaskLoading() {
        $('#modalTaskBody').html(`
            <div class="py-12 text-center text-slate-400">
                <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-semibold">Memuat Form...</span>
            </div>
        `);
    }

    // Open Create Task Modal
    $('#btnCreateTaskV2, .btnOpenCreateTaskModal').on('click', function() {
        $('#modalTaskTitle').text('Tambah Task Baru');
        $('#modalTaskSubtitle').text('Daftarkan tugas baru dan delegasikan ke anggota tim');
        $('#modalTaskIcon').attr('class', 'ti ti-plus text-lg');
        showTaskLoading();
        openTaskModal();
        $('#modalTaskBody').load("{{ route('project.task.create', Crypt::encrypt($project->id)) }}");
    });

    // Open Edit Task Modal
    $(document).on('click', '.btnEditTaskV2', function() {
        const href = $(this).data('href');
        $('#modalTaskTitle').text('Edit Task');
        $('#modalTaskSubtitle').text('Perbarui detail tugas, progres, atau delegasi personil');
        $('#modalTaskIcon').attr('class', 'ti ti-edit text-lg');
        showTaskLoading();
        openTaskModal();
        $('#modalTaskBody').load(href);
    });

    // Open Edit Project Modal
    $(document).on('click', '.btnEditProjectV2', function() {
        const href = $(this).data('href');
        $('#modalTaskTitle').text('Edit Project');
        $('#modalTaskSubtitle').text('Perbarui detail spesifikasi project');
        $('#modalTaskIcon').attr('class', 'ti ti-briefcase text-lg');
        showTaskLoading();
        openTaskModal();
        $('#modalTaskBody').load(href);
    });

    // Kanban Drag & Drop
    const taskCards = document.querySelectorAll('.kanban-task-card');
    const columns = document.querySelectorAll('.kanban-column');

    taskCards.forEach(card => {
        card.addEventListener('dragstart', () => card.classList.add('dragging'));
        card.addEventListener('dragend', () => card.classList.remove('dragging'));
    });

    columns.forEach(column => {
        column.addEventListener('dragover', e => {
            e.preventDefault();
            const draggingCard = document.querySelector('.dragging');
            if (draggingCard) {
                const placeholder = column.querySelector('.kanban-empty-placeholder');
                if (placeholder) placeholder.style.display = 'none';
                column.appendChild(draggingCard);
            }
        });

        column.addEventListener('dragenter', e => {
            e.preventDefault();
            column.classList.add('drag-over');
        });

        column.addEventListener('dragleave', () => {
            column.classList.remove('drag-over');
            const cards = column.querySelectorAll('.kanban-task-card');
            const placeholder = column.querySelector('.kanban-empty-placeholder');
            if (cards.length === 0 && placeholder) placeholder.style.display = 'block';
        });

        column.addEventListener('drop', e => {
            e.preventDefault();
            column.classList.remove('drag-over');
            const card = document.querySelector('.dragging');
            if (!card) return;

            const taskId = card.getAttribute('data-id');
            const newStatus = column.getAttribute('data-status');

            $.ajax({
                url: `/project/task/${taskId}/status`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'bottom-end',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: response.message });
                        setTimeout(() => window.location.reload(), 1500);
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal memperbarui status tugas.' });
                    setTimeout(() => window.location.reload(), 1500);
                }
            });
        });
    });
});
</script>
@endpush
