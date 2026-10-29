@extends('v2.layouts.app')

@section('titlepage', 'Edit Project')
@section('page_title', 'Edit Project')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle with Back Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('project.index') }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('project.index') }}" class="hover:text-emerald-600 transition-colors">Project</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Edit Project</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 mt-0.5">
                    <i class="ti ti-edit text-2xl" style="color: {{ $themeColor1 }};"></i>
                    <span>Edit Project ({{ $project->kode_project }})</span>
                </h1>
            </div>
        </div>

        <!-- Breadcrumbs Navigation -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('project.index') }}" class="hover:text-slate-700 transition">Project</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Edit Project</span>
            </nav>
        </div>
    </div>

    <!-- ================= 2. FORM CONTAINER ================= -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs">
        @include('v2.project.edit_form')
    </div>

</div>

@endsection
