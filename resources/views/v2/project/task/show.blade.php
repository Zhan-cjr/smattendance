@extends('v2.layouts.app')

@section('titlepage', 'Detail Task: ' . $task->kode_task)
@section('page_title', 'Detail Task')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $user = auth()->user();

    // Priority configuration
    $prioritasBadge = [
        'low' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => 'Rendah'],
        'medium' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Sedang'],
        'high' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'label' => 'Tinggi'],
        'critical' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Kritis'],
    ][$task->prioritas] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => $task->prioritas];

    // Status configuration
    $statusBadge = [
        'todo' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => 'To Do'],
        'in_progress' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'label' => 'In Progress'],
        'review' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Review'],
        'completed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Completed'],
        'cancelled' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Cancelled'],
    ][$task->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => $task->status];

    $isOverdue = ($task->status !== 'completed' && $task->due_date && $task->due_date->isPast());
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle with Back Button -->
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('project.show', Crypt::encrypt($project->id)) }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0"
               title="Kembali ke Project">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('project.index') }}" class="hover:text-emerald-600 transition-colors">Project</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <a href="{{ route('project.show', Crypt::encrypt($project->id)) }}" class="hover:text-emerald-600 transition-colors truncate max-w-[160px] inline-block align-bottom">{{ $project->nama_project }}</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">{{ $task->kode_task }}</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 mt-0.5 truncate">
                    <i class="ti ti-checkbox text-2xl shrink-0" style="color: {{ $themeColor1 }};"></i>
                    <span class="truncate">{{ $task->judul }}</span>
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
                <span class="font-bold text-slate-800">Detail Task</span>
            </nav>

            <div class="flex items-center gap-2">
                @can('project.task.edit')
                    <button type="button" 
                            class="btnEditTaskV2 inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-2xs cursor-pointer"
                            data-href="{{ route('project.task.edit', Crypt::encrypt($task->id)) }}">
                        <i class="ti ti-edit text-sm"></i>
                        <span>Edit Task</span>
                    </button>
                @endcan

                @can('project.task.create')
                    @if(!$task->parent_id)
                        <button type="button" id="btnCreateSubtaskV2"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-plus text-sm"></i>
                            <span>Tambah Sub-task</span>
                        </button>
                    @endif
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

    <!-- ================= 2. MAIN 2-COLUMN GRID ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Left Section (8 cols): Overview Task, Subtasks, Komentar, & Log Aktivitas -->
        <div class="lg:col-span-8 space-y-5">
            
            <!-- A. Task Overview & Meta Card (Solid Theme Card with Sleek Clean Watermark) -->
            <div class="rounded-2xl p-5 sm:p-6 shadow-sm space-y-4 text-white relative overflow-hidden"
                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                
                <!-- Clean Minimalist Corner Watermark & Glow -->
                <div class="absolute -right-6 -bottom-6 w-48 h-48 rounded-full bg-white/5 pointer-events-none blur-xl"></div>
                <div class="absolute right-6 top-1/2 -translate-y-1/2 text-white/[0.07] pointer-events-none select-none">
                    <i class="ti ti-checkbox text-[140px] leading-none"></i>
                </div>

                <div class="relative z-10 flex flex-wrap items-center justify-between gap-2.5 pb-3 border-b border-white/20">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-lg text-xs font-black bg-white/20 text-white backdrop-blur-md border border-white/30 shadow-2xs">
                            {{ $task->kode_task }}
                        </span>
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-white text-slate-800 shadow-2xs">
                            Prioritas {{ $prioritasBadge['label'] }}
                        </span>
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-white/20 text-white backdrop-blur-md border border-white/30 shadow-2xs">
                            {{ $statusBadge['label'] }}
                        </span>
                        @if($task->parent)
                            <a href="{{ route('project.task.show', Crypt::encrypt($task->parent->id)) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-white/20 text-white hover:bg-white/30 transition backdrop-blur-md border border-white/30 shadow-2xs">
                                <i class="ti ti-git-branch text-xs"></i> Sub-task: {{ $task->parent->kode_task }}
                            </a>
                        @endif
                        @if($isOverdue)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-black bg-rose-500 text-white shadow-2xs">
                                <i class="ti ti-alert-triangle text-xs"></i> Overdue
                            </span>
                        @endif
                    </div>

                    <div class="text-[11px] text-white/80 font-medium flex items-center gap-1">
                        <i class="ti ti-clock text-xs"></i>
                        <span>Dibuat {{ $task->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Modern Divider Separator for Task Meta (Crisp White on Solid Theme) -->
                <div class="relative z-10 py-2 border-b border-white/20">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-y-3 gap-x-4 sm:divide-x sm:divide-white/20">
                        
                        <!-- Tanggal Mulai -->
                        <div class="flex items-center gap-2.5 pr-2">
                            <div class="w-8 h-8 rounded-lg bg-white/15 backdrop-blur-md text-white flex items-center justify-center shrink-0 border border-white/25">
                                <i class="ti ti-calendar text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10.5px] font-medium text-white/70">Tanggal Mulai</span>
                                <span class="text-xs sm:text-sm font-bold text-white truncate block">
                                    {{ $task->start_date ? $task->start_date->format('d M Y') : '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- Target Deadline -->
                        <div class="flex items-center gap-2.5 sm:pl-3 pr-2">
                            <div class="w-8 h-8 rounded-lg {{ $isOverdue ? 'bg-rose-500 text-white' : 'bg-white/15 text-white' }} backdrop-blur-md border border-white/25 flex items-center justify-center shrink-0">
                                <i class="ti ti-calendar-due text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10.5px] font-medium text-white/70">Target Deadline</span>
                                <span class="text-xs sm:text-sm font-bold text-white truncate block">
                                    {{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}
                                </span>
                            </div>
                        </div>

                        <!-- Dibuat Oleh -->
                        <div class="flex items-center gap-2.5 sm:pl-3 pr-2">
                            <div class="w-8 h-8 rounded-lg bg-white/15 backdrop-blur-md text-white flex items-center justify-center shrink-0 border border-white/25">
                                <i class="ti ti-user text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10.5px] font-medium text-white/70">Dibuat Oleh</span>
                                <span class="text-xs sm:text-sm font-bold text-white truncate block" title="{{ $task->creator ? $task->creator->nama_karyawan : 'Admin' }}">
                                    {{ $task->creator ? $task->creator->nama_karyawan : 'Admin' }}
                                </span>
                            </div>
                        </div>

                        <!-- Urutan Tampil -->
                        <div class="flex items-center gap-2.5 sm:pl-3">
                            <div class="w-8 h-8 rounded-lg bg-white/15 backdrop-blur-md text-white flex items-center justify-center shrink-0 border border-white/25">
                                <i class="ti ti-arrows-sort text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-[10.5px] font-medium text-white/70">Urutan Prioritas</span>
                                <span class="text-xs sm:text-sm font-bold text-white truncate block">
                                    Urutan ke-{{ $task->urutan }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Deskripsi Tugas -->
                <div class="relative z-10 space-y-1.5 pt-1">
                    <span class="text-xs font-bold text-white/90 flex items-center gap-1.5">
                        <i class="ti ti-align-left text-white"></i> Deskripsi & Kriteria Pengerjaan
                    </span>
                    <div class="p-3.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-xs text-white/95 leading-relaxed whitespace-pre-line shadow-inner">
                        {{ !empty($task->deskripsi) ? $task->deskripsi : 'Tidak ada deskripsi detail untuk tugas ini.' }}
                    </div>
                </div>
            </div>

            <!-- B. Sub-tasks Listing Card (If applicable) -->
            @if($task->subtasks->isNotEmpty() || !$task->parent_id)
                <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                <i class="ti ti-git-merge text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-800">Daftar Sub-task ({{ $task->subtasks->count() }})</h3>
                                <p class="text-[10.5px] text-slate-400">Pecahan rincian tugas pendukung</p>
                            </div>
                        </div>

                        @can('project.task.create')
                            @if(!$task->parent_id)
                                <button type="button" id="btnCreateSubtaskV2Header"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-white text-xs font-bold shadow-xs transition hover:opacity-95 cursor-pointer"
                                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                    <i class="ti ti-plus text-xs"></i>
                                    <span>Tambah Sub-task</span>
                                </button>
                            @endif
                        @endcan
                    </div>

                    <div class="space-y-2">
                        @forelse($task->subtasks as $sub)
                            @php
                                $subStatus = [
                                    'todo' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => 'To Do'],
                                    'in_progress' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'label' => 'In Progress'],
                                    'review' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Review'],
                                    'completed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Completed'],
                                    'cancelled' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Cancelled'],
                                ][$sub->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'label' => $sub->status];
                            @endphp
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-xl bg-slate-50/70 border border-slate-200/80 hover:bg-slate-50 transition">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[11px] font-bold text-slate-400 font-mono">{{ $sub->kode_task }}</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold {{ $subStatus['bg'] }} {{ $subStatus['text'] }} border {{ $subStatus['border'] }}">
                                            {{ $subStatus['label'] }}
                                        </span>
                                    </div>
                                    <a href="{{ route('project.task.show', Crypt::encrypt($sub->id)) }}" class="text-xs font-bold text-slate-800 hover:text-emerald-600 transition truncate block">
                                        {{ $sub->judul }}
                                    </a>
                                </div>

                                <div class="flex items-center gap-4 shrink-0">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-slate-200 h-2 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full" style="width: {{ $sub->progress }}%; background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});"></div>
                                        </div>
                                        <span class="text-[11px] font-bold text-slate-700">{{ $sub->progress }}%</span>
                                    </div>
                                    <a href="{{ route('project.task.show', Crypt::encrypt($sub->id)) }}"
                                       class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-100 text-xs font-bold transition shadow-2xs">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 border border-dashed border-slate-200 rounded-xl bg-slate-50/50 text-xs">
                                <i class="ti ti-git-merge text-2xl block mb-1"></i>
                                Belum ada sub-task terdaftar untuk tugas ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- C. Diskusi & Komentar Section -->
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-messages text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Diskusi & Komentar ({{ $task->comments->count() }})</h3>
                        <p class="text-[10.5px] text-slate-400">Koordinasi dan catatan kerja personil</p>
                    </div>
                </div>

                <!-- Add Comment Form -->
                <form action="{{ route('project.task.comment', Crypt::encrypt($task->id)) }}" method="POST" class="space-y-2.5" id="formAddCommentV2">
                    @csrf
                    <textarea name="komentar" rows="2" required placeholder="Tuliskan tanggapan, update hasil pekerjaan, atau koordinasikan tugas..."
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium"
                              style="--tw-ring-color: {{ $themeColor1 }};"></textarea>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-send text-xs"></i>
                            <span>Kirim Komentar</span>
                        </button>
                    </div>
                </form>

                <!-- Comments List Timeline -->
                <div class="space-y-3 pt-2 border-t border-slate-100 max-h-96 overflow-y-auto pr-1">
                    @forelse($task->comments->sortByDesc('created_at') as $comment)
                        @php
                            $cKaryawan = $comment->karyawan;
                        @endphp
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50/60 border border-slate-200/70">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 text-white shadow-2xs"
                                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                {{ $cKaryawan ? substr($cKaryawan->nama_karyawan, 0, 1) : 'A' }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="text-xs font-bold text-slate-800 truncate">
                                        {{ $cKaryawan ? $cKaryawan->nama_karyawan : 'Admin / System' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 shrink-0">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $comment->komentar }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            <i class="ti ti-messages-off text-2xl block mb-1"></i>
                            Belum ada komentar dalam tugas ini. Jadilah yang pertama berkomentar!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- D. Log & Riwayat Aktivitas Section (Timeline Style) -->
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-history text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Riwayat Aktivitas & Log Task</h3>
                        <p class="text-[10.5px] text-slate-400">Catatan kronologis perubahan status dan data</p>
                    </div>
                </div>

                <div class="relative pl-3 max-h-80 overflow-y-auto pr-2">
                    @forelse($task->logs->sortByDesc('created_at') as $log)
                        @php
                            $logCreator = $log->karyawan;
                            
                            // Accent Icon and Colors according to action type
                            $iconClass = 'ti ti-point';
                            $iconBg = 'bg-slate-100 text-slate-700 border-slate-200';
                            
                            if ($log->aksi === 'created') {
                                $iconClass = 'ti ti-plus';
                                $iconBg = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                            } elseif ($log->aksi === 'status_changed') {
                                $iconClass = 'ti ti-arrows-right';
                                $iconBg = 'bg-amber-50 text-amber-600 border-amber-200';
                            } elseif ($log->aksi === 'progress_updated') {
                                $iconClass = 'ti ti-chart-bar';
                                $iconBg = 'bg-sky-50 text-sky-600 border-sky-200';
                            } elseif ($log->aksi === 'attachment_added') {
                                $iconClass = 'ti ti-paperclip';
                                $iconBg = 'bg-indigo-50 text-indigo-600 border-indigo-200';
                            } elseif ($log->aksi === 'attachment_removed') {
                                $iconClass = 'ti ti-trash';
                                $iconBg = 'bg-rose-50 text-rose-600 border-rose-200';
                            } elseif ($log->aksi === 'member_added' || $log->aksi === 'member_assigned') {
                                $iconClass = 'ti ti-user-plus';
                                $iconBg = 'bg-teal-50 text-teal-600 border-teal-200';
                            }
                        @endphp
                        
                        <div class="relative flex items-start gap-3.5 pb-5 last:pb-1">
                            <!-- Connecting Vertical Line -->
                            @if(!$loop->last)
                                <div class="absolute left-3.5 top-6 bottom-0 w-0.5 bg-slate-200/80 -ml-px"></div>
                            @endif

                            <!-- Timeline Node Icon -->
                            <div class="relative z-10 w-7 h-7 rounded-xl flex items-center justify-center shrink-0 border shadow-2xs {{ $iconBg }}">
                                <i class="{{ $iconClass }} text-xs"></i>
                            </div>

                            <!-- Timeline Content Box -->
                            <div class="min-w-0 flex-1 p-3 rounded-xl bg-slate-50/70 border border-slate-200/70 hover:bg-slate-50 transition shadow-2xs">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <span class="text-xs font-bold text-slate-800 leading-snug">
                                        {{ $log->keterangan }}
                                    </span>
                                    <span class="text-[10px] font-semibold text-slate-400 shrink-0">
                                        {{ $log->created_at->format('d M, H:i') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 text-[10.5px] text-slate-500">
                                    <i class="ti ti-user text-[11px] text-slate-400"></i>
                                    <span>Dilakukan oleh: <strong class="text-slate-700">{{ $logCreator ? $logCreator->nama_karyawan : 'Admin / System' }}</strong></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            <i class="ti ti-history-off text-2xl block mb-1"></i>
                            Belum ada log aktivitas untuk tugas ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Section (4 cols): Quick Status Form, Assigned Members, & Attachments -->
        <div class="lg:col-span-4 space-y-5">
            
            <!-- 1. Quick Status & Progress Update Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 pb-2.5 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-chart-donut-2 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Status & Progres Kerja</h3>
                        <p class="text-[10.5px] text-slate-400">Perbarui progres penyelesaian</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-slate-600">Capaian Progres</span>
                        <span class="text-sm font-black text-slate-900" id="current_progress_label">{{ $task->progress }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden border border-slate-200/60">
                        <div class="h-full rounded-full transition-all duration-300"
                             style="width: {{ $task->progress }}%; background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        </div>
                    </div>
                </div>

                <!-- Form Ubah Status -->
                <form action="{{ route('project.task.status', Crypt::encrypt($task->id)) }}" method="POST" class="space-y-2 pt-2 border-t border-slate-100">
                    @csrf
                    <label for="quick_status" class="block text-xs font-bold text-slate-700">Ubah Status</label>
                    <div class="flex items-center gap-2">
                        <select name="status" id="quick_status"
                                class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:border-transparent transition-all cursor-pointer"
                                style="--tw-ring-color: {{ $themeColor1 }};">
                            <option value="todo" {{ $task->status === 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="review" {{ $task->status === 'review' ? 'selected' : '' }}>Review</option>
                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $task->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button type="submit" class="px-4 py-2 rounded-xl text-white font-bold text-xs shadow-xs transition-all active:scale-95 cursor-pointer shrink-0 hover:opacity-95"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            Simpan
                        </button>
                    </div>
                </form>

                <!-- Form Geser Progres Slider -->
                <form action="{{ route('project.task.progress', Crypt::encrypt($task->id)) }}" method="POST" class="space-y-2 pt-2 border-t border-slate-100">
                    @csrf
                    <div class="flex items-center justify-between">
                        <label for="progress_slider_v2" class="text-xs font-bold text-slate-700">Geser Progres</label>
                        <span id="slider_val_v2" class="text-xs font-black" style="color: {{ $themeColor1 }};">{{ $task->progress }}%</span>
                    </div>
                    <input type="range" name="progress" id="progress_slider_v2" min="0" max="100" value="{{ $task->progress }}" step="5"
                           class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer"
                           style="accent-color: {{ $themeColor1 }};">
                    <button type="submit" class="w-full py-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition shadow-2xs cursor-pointer">
                        Update Progres (%)
                    </button>
                </form>
            </div>

            <!-- 2. Assigned Team Members Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs space-y-3.5">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-users text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800">Tim Ditugaskan ({{ $task->members->count() }})</h3>
                            <p class="text-[10.5px] text-slate-400">Penanggung jawab tugas</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    @forelse ($task->members as $member)
                        @php
                            $tmKaryawan = $member->karyawan;
                        @endphp
                        <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50/70 border border-slate-200/80">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0 text-white shadow-2xs"
                                 style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                                {{ $tmKaryawan ? substr($tmKaryawan->nama_karyawan, 0, 1) : '?' }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-xs font-bold text-slate-800 block truncate">{{ $tmKaryawan ? $tmKaryawan->nama_karyawan : 'Karyawan' }}</span>
                                <span class="text-[10px] text-slate-400 block truncate">{{ !empty($tmKaryawan->nik_show) ? $tmKaryawan->nik_show : $member->nik }} • {{ $tmKaryawan && $tmKaryawan->jabatan ? $tmKaryawan->jabatan->nama_jabatan : 'Staf' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-slate-400 text-xs">Tugas ini belum ditugaskan ke personil manapun.</div>
                    @endforelse
                </div>
            </div>

            <!-- 3. Attachments & Dokumen Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs space-y-4">
                <div class="flex items-center gap-2.5 pb-2.5 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-paperclip text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Lampiran & File ({{ $task->attachments->count() }})</h3>
                        <p class="text-[10.5px] text-slate-400">Dokumen pendukung pekerjaan</p>
                    </div>
                </div>

                <!-- Upload Attachment Form -->
                <form action="{{ route('project.task.attachment', Crypt::encrypt($task->id)) }}" method="POST" enctype="multipart/form-data" class="space-y-2.5 pb-3 border-b border-slate-100">
                    @csrf
                    <div>
                        <input type="file" name="file" id="file" required
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    </div>
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-1.5 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-upload text-xs"></i>
                        <span>Unggah Lampiran</span>
                    </button>
                </form>

                <!-- Attachment List -->
                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @forelse($task->attachments as $attach)
                        @php
                            $attKaryawan = $attach->karyawan;
                        @endphp
                        <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50/70 border border-slate-200/80 gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                                     style="background-color: {{ $themeColor1 }}15; color: {{ $themeColor1 }};">
                                    <i class="ti ti-file-text text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold text-slate-800 truncate block" title="{{ $attach->nama_file }}">
                                        {{ $attach->nama_file }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 block">
                                        {{ round($attach->ukuran / 1024, 1) }} KB • {{ $attKaryawan ? $attKaryawan->nama_karyawan : 'Admin' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <a href="{{ asset('storage/' . $attach->path) }}" target="_blank"
                                   class="p-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:text-emerald-700 transition shadow-2xs" title="Download File">
                                    <i class="ti ti-download text-xs"></i>
                                </a>
                                <form action="{{ route('project.task.attachment.delete', Crypt::encrypt($attach->id)) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btnDeleteAttachV2 p-1.5 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-600 transition shadow-2xs cursor-pointer" title="Hapus File">
                                        <i class="ti ti-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-slate-400 text-xs">Belum ada lampiran file.</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

<!-- ================= 3. DYNAMIC AJAX MODAL TASK & EDIT ================= -->
<div id="modalTaskDetailV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative w-full max-w-5xl bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all my-6 sm:my-8">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"
             style="background: linear-gradient(135deg, {{ $themeColor1 }}08, {{ $themeColor2 }}12);">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shadow-xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-checkbox text-lg" id="modalTaskDetailIcon"></i>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-800 tracking-tight" id="modalTaskDetailTitle">Task Project</h2>
                    <p class="text-xs text-slate-500 font-medium" id="modalTaskDetailSubtitle">Atur rincian tugas dan penanggung jawab</p>
                </div>
            </div>
            <button type="button" class="btnCloseModalTaskDetail w-8 h-8 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-800 flex items-center justify-center transition cursor-pointer">
                <i class="ti ti-x text-base"></i>
            </button>
        </div>

        <div class="p-6 max-h-[75vh] overflow-y-auto" id="modalTaskDetailBody">
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
    // Slider Value live indicator
    $('#progress_slider_v2').on('input', function() {
        $('#slider_val_v2').text($(this).val() + '%');
    });

    // Dynamic Modal Handlers
    function openModal() {
        $('#modalTaskDetailV2').removeClass('hidden');
        $('body').addClass('overflow-hidden');
    }

    function closeModal() {
        $('#modalTaskDetailV2').addClass('hidden');
        $('body').removeClass('overflow-hidden');
    }

    $(document).on('click', '.btnCloseModalTaskDetail, .btnCloseModalTask', closeModal);

    $('#modalTaskDetailV2').on('click', function(e) {
        if (e.target === this) closeModal();
    });

    function showLoading() {
        $('#modalTaskDetailBody').html(`
            <div class="py-12 text-center text-slate-400">
                <svg class="animate-spin h-8 w-8 text-emerald-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span class="text-xs font-semibold">Memuat Form...</span>
            </div>
        `);
    }

    // Edit Task Modal Trigger
    $(document).on('click', '.btnEditTaskV2', function() {
        const href = $(this).data('href');
        $('#modalTaskDetailTitle').text('Edit Task');
        $('#modalTaskDetailSubtitle').text('Perbarui detail tugas, progres, atau delegasi tim');
        $('#modalTaskDetailIcon').attr('class', 'ti ti-edit text-lg');
        showLoading();
        openModal();
        $('#modalTaskDetailBody').load(href);
    });

    // Create Sub-task Modal Trigger
    $('#btnCreateSubtaskV2, #btnCreateSubtaskV2Header').on('click', function() {
        $('#modalTaskDetailTitle').text('Tambah Sub-task Baru');
        $('#modalTaskDetailSubtitle').text('Buat pecahan rincian tugas di bawah {{ $task->kode_task }}');
        $('#modalTaskDetailIcon').attr('class', 'ti ti-plus text-lg');
        showLoading();
        openModal();
        $('#modalTaskDetailBody').load("{{ route('project.task.create', ['projectId' => Crypt::encrypt($project->id)]) }}?parent_id={{ $task->id }}");
    });

    // Delete Attachment Confirmation
    $(document).on('click', '.btnDeleteAttachV2', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Lampiran Ini?',
            text: 'File lampiran akan dihapus permanen dari sistem!',
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
