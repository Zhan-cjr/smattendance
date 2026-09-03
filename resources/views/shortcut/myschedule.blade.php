@extends('layouts.mobile.modern')
@section('title', 'Jadwal Saya')

@section('header_left')
    <a href="{{ route('shortcut.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background-color: #f8fafc;
        }

        .schedule-card {
            border: 1px solid #32745e; /* Matching primary color theme */
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            margin-bottom: 4px;
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .schedule-card:active {
            transform: scale(0.98);
        }

        .date-badge {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .month-selector {
            background: #ffffff;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-2 pb-24">
        {{-- Month Selector --}}
        <div class="month-selector fade-in">
            <form action="{{ route('myschedule.index') }}" method="GET" id="monthForm">
                <div class="flex items-center justify-between gap-2">
                    <select name="bulan" class="form-select flex-1 rounded-lg border-slate-200 text-xs font-semibold focus:ring-emerald-500 focus:border-emerald-500 py-1.5" onchange="this.form.submit()">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="tahun" class="form-select flex-1 rounded-lg border-slate-200 text-xs font-semibold focus:ring-emerald-500 focus:border-emerald-500 py-1.5" onchange="this.form.submit()">
                        @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>

        {{-- Schedule List --}}
        <div class="space-y-1">
            @php
                $today = date('Y-m-d');
                $delay = 0.1;
            @endphp
            @foreach ($schedule as $date => $info)
                @php
                    $isToday = $date == $today;
                    $dayFormat = Carbon\Carbon::parse($date);
                    $delay += 0.04;
                    
                    $isHoliday = $dayFormat->isWeekend() || (isset($info->nama_jam_kerja) && str_contains(strtolower($info->nama_jam_kerja), 'libur'));
                    
                    // Priority for color: 
                    // 1. Info color (jam kerja)
                    // 2. Default red for holiday
                    // 3. Default emerald for work
                    $baseColor = '#10b981'; // Default Emerald
                    if ($isHoliday) {
                        $baseColor = '#ef4444'; // Default Red
                    }
                    if (isset($info->color) && !empty($info->color)) {
                        $baseColor = $info->color;
                    }

                    $badgeBg = $baseColor;
                    $badgeText = '#fff';
                @endphp
                <div class="schedule-card fade-in" style="animation-delay: {{ $delay }}s; {{ $isToday ? 'border: 2px solid '.$baseColor.';' : '' }}">
                    <div class="card-body p-1 flex items-center gap-2">
                        {{-- Date Display --}}
                        <div class="date-badge flex-shrink-0" style="background: {{ $badgeBg }}; color: {{ $badgeText }};">
                            <span class="text-[10px] font-bold uppercase leading-none">{{ $dayFormat->translatedFormat('D') }}</span>
                            <span class="text-[16px] font-extrabold leading-tight mt-0.5">{{ $dayFormat->format('d') }}</span>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0 pr-1">
                            @if ($info)
                                <div class="flex items-center justify-between mb-0.5">
                                    <h3 class="text-[14px] font-semibold truncate text-slate-800">
                                        {{ $info->nama_jam_kerja }}
                                    </h3>
                                    @if ($isToday)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-500 text-white">HARI INI</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 text-[12px] font-medium text-slate-600">
                                        <span>{{ $info->jam_masuk ? date('H:i', strtotime($info->jam_masuk)) : '__:__' }}</span>
                                        <span class="text-slate-300">-</span>
                                        <span>{{ $info->jam_pulang ? date('H:i', strtotime($info->jam_pulang)) : '__:__' }}</span>
                                    </div>
                                    @if($info->color)
                                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $info->color }}"></div>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center justify-between mb-0.5">
                                    <h3 class="text-[14px] font-semibold truncate text-slate-400 italic">Tidak ada jadwal</h3>
                                    @if ($isToday)
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-500 text-white">HARI INI</span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400">Silahkan hubungi admin</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
