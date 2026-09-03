@extends('layouts.mobile.modern')
@section('title', 'Riwayat Lembur')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@section('header_right')
    @can('lembur.create')
    <a href="{{ route('lembur.create') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="add-outline" class="text-xl"></ion-icon>
    </a>
    @endcan
@endsection

@push('mystyle')

    <style>
        body {
            background-color: #f8fafc;
        }

        /* Filter Section matching Histori */
        .filter-section {
            background: #ffffff;
            border-bottom: 1px solid #e1e8f0;
            padding: 12px 16px;
            position: sticky;
            top: 60px;
            z-index: 40;
        }

        .date-input-wrapper {
            position: relative;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            height: 42px;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .date-input-wrapper:focus-within {
            border-color: #0f172a;
            box-shadow: 0 0 0 2px rgba(15, 23, 42, 0.1);
        }

        .date-input-wrapper ion-icon {
            position: absolute;
            left: 12px;
            color: #64748b;
            font-size: 18px;
        }

        .date-input-wrapper input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            padding: 0 12px 0 38px;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            outline: none;
        }

        .btn-filter {
            height: 42px;
            border-radius: 10px;
            background: #0f172a;
            color: white;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            transition: all 0.2s;
        }

        .btn-filter:active {
            background: #1e293b;
            transform: scale(0.98);
        }

        /* Modern Card List */
        .card.press {
            border: none;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            margin-bottom: 10px;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .card.press:active {
            transform: scale(0.98);
            box-shadow: 0 0 0 transparent;
            background: #f8fafc;
        }

        /* Color Coded Date Badges */
        .date-badge {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .date-badge .day { font-size: 15px; font-weight: 700; }
        .date-badge .month { font-size: 10px; font-weight: 600; text-transform: uppercase; margin-top: 1px; }

        .date-badge.blue { background: #e0f2fe; color: #0284c7; }
        .date-badge.amber { background: #fef3c7; color: #d97706; }
        .date-badge.rose { background: #ffe4e6; color: #e11d48; }
        .date-badge.emerald { background: #dcfce7; color: #16a34a; }
        .date-badge.indigo { background: #e0e7ff; color: #4f46e5; }
        .date-badge.purple { background: #f3e8ff; color: #9333ea; }
        .date-badge.orange { background: #ffedd5; color: #ea580c; }
        .date-badge.teal { background: #ccfbf1; color: #0d9488; }
        .date-badge.cyan { background: #cffafe; color: #0891b2; }

        /* Status Badge */
        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-badge.pending { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .status-badge.approved { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .status-badge.rejected { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        /* Skeleton Loader */
        #skeleton-container { display: block; }
        #real-content { display: none; }
        
        .skeleton {
            background: #e2e8f0;
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* FAB matching modern theme */
        .fab-button.modern {
            position: fixed;
            bottom: 110px;
            right: 20px;
            z-index: 50;
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: {{ $t['primary'] }};
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -5px {{ $t['primary'] }}40, 0 8px 10px -6px {{ $t['primary'] }}20;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fab-button.modern:active {
            transform: scale(0.92) translateY(2px);
            box-shadow: 0 5px 15px -3px {{ $t['primary'] }}30;
            background: {{ $t['primary_light'] }};
        }

        .fab-button.modern ion-icon {
            font-size: 28px;
            transition: transform 0.3s ease;
        }
        
        /* Delete Action styling */
        .delete-btn {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fef2f2;
            color: #dc2626;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.2s ease;
            border: 1px solid #fecaca;
        }
        
        .delete-btn:active {
            background: #fee2e2;
            transform: scale(0.9);
        }
    </style>
@endpush

@section('content')

    <form method="GET" action="{{ route('lembur.index') }}" id="filterForm">
        <div class="mt-1 mb-1 rounded-xl overflow-hidden border mx-1"
             style="background: #fff; border-color: #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
            {{-- Filter Header --}}
            <div class="flex items-center gap-2 px-3 py-2" style="border-bottom: 1px solid #f1f5f9;">
                <div class="w-6 h-6 rounded flex items-center justify-center" style="background: {{ $t['primary'] }}15;">
                    <ion-icon name="calendar-outline" class="text-[12px]" style="color: {{ $t['primary'] }};"></ion-icon>
                </div>
                <span class="text-[12px] font-semibold" style="color: #475569;">Pilih Rentang Tanggal</span>
            </div>
            
            {{-- Filter Inputs --}}
            <div class="px-3 py-2.5">
                <div class="flex items-center gap-2">
                    {{-- Dari --}}
                    <div class="flex-1">
                        <input type="text" name="dari" id="datePicker" 
                            class="w-full rounded-lg py-1.5 px-3 text-[12px] font-medium text-center focus:outline-none transition-all"
                            style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;"
                            placeholder="Dari" value="{{ Request('dari') }}" autocomplete="off" required readonly>
                    </div>
                    <div class="flex-shrink-0 w-4 flex items-center justify-center">
                        <div class="w-3 h-[1px]" style="background: #cbd5e1;"></div>
                    </div>
                    {{-- Sampai --}}
                    <div class="flex-1">
                        <input type="text" name="sampai" id="datePicker2" 
                            class="w-full rounded-lg py-1.5 px-3 text-[12px] font-medium text-center focus:outline-none transition-all"
                            style="background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;"
                            placeholder="Sampai" value="{{ Request('sampai') }}" autocomplete="off" required readonly>
                    </div>
                    {{-- Button --}}
                    <button type="submit" id="btnCari"
                        class="flex-shrink-0 w-9 h-8 rounded-lg text-white flex items-center justify-center active:scale-90 transition-transform"
                        style="background: {{ $t['primary'] }};">
                        <ion-icon name="search-outline" class="text-base"></ion-icon>
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Skeleton Loader (Shown first) --}}
    <div id="skeleton-container" class="px-1 pt-1 pb-24">
        @for($i = 0; $i < 5; $i++)
            <div class="card press mb-2 border-0">
                <div class="card-body p-1.5 flex gap-3">
                    <div class="skeleton" style="width: 40px; height: 40px; border-radius: 8px; flex-shrink: 0;"></div>
                    <div class="flex-1 py-1">
                        <div class="skeleton h-4 w-3/4 mb-2"></div>
                        <div class="skeleton h-3 w-1/2 mb-2"></div>
                        <div class="skeleton h-3 w-2/3"></div>
                    </div>
                    <div class="flex flex-col items-end justify-between py-1">
                        <div class="skeleton" style="width: 50px; height: 18px; border-radius: 6px;"></div>
                        <div class="skeleton" style="width: 40px; height: 12px; margin-top: 5px;"></div>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    {{-- Real Content (Hidden until loaded) --}}
    <div id="real-content" class="px-1 pt-1 pb-24">
        
        @if (Session::get('success') || Session::has('warning') || Session::has('error'))
            <div class="mb-2 px-2">
                @if (Session::get('success'))
                    <div class="bg-green-100 border border-green-200 text-green-700 px-3 py-2 rounded-lg text-xs font-medium flex items-center gap-2">
                        <ion-icon name="checkmark-circle" class="text-base"></ion-icon>
                        {{ Session::get('success') }}
                    </div>
                @elseif(Session::get('warning'))
                    <div class="bg-yellow-100 border border-yellow-200 text-yellow-700 px-3 py-2 rounded-lg text-xs font-medium flex items-center gap-2">
                        <ion-icon name="warning" class="text-base"></ion-icon>
                        {{ Session::get('warning') }}
                    </div>
                @elseif(Session::get('error'))
                    <div class="bg-red-100 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-xs font-medium flex items-center gap-2">
                        <ion-icon name="alert-circle" class="text-base"></ion-icon>
                        {{ Session::get('error') }}
                    </div>
                @endif
            </div>
        @endif

        @forelse ($lembur as $d)
            @php
                $colors = ['blue', 'amber', 'rose', 'emerald', 'indigo', 'purple', 'orange', 'teal', 'cyan'];
                $monthNum = (int)date('m', strtotime($d->tanggal));
                $colorIndex = ($monthNum - 1) % count($colors);
                $badgeColor = $colors[$colorIndex];
                
                $tglParts = explode("-", $d->tanggal);
                $day = $tglParts[2];
                $shortMonths = ["", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
                $monthStr = $shortMonths[(int)$tglParts[1]];
                
                // Status Logic
                $statusClass = 'pending';
                $statusText = 'Pending';
                $statusIcon = 'time-outline';

                if ($d->status == 1) {
                    $statusClass = 'approved';
                    $statusText = 'Disetujui';
                    $statusIcon = 'checkmark-circle-outline';
                } elseif ($d->status == 2) {
                    $statusClass = 'rejected';
                    $statusText = 'Ditolak';
                    $statusIcon = 'close-circle-outline';
                }
                
                // Duration Calculation
                $start = strtotime($d->lembur_mulai);
                $end = strtotime($d->lembur_selesai);
                $diff = $end - $start;
                $hours = floor($diff / 3600);
                $minutes = floor(($diff % 3600) / 60);
                $duration = $hours . "j " . ($minutes > 0 ? $minutes . "m" : "");

                /* Realisasi Duration */
                $real_duration = null;
                if($d->lembur_in && $d->lembur_out) {
                    $real_duration = ROUND(hitungJam($d->lembur_in, $d->lembur_out), 2) . "j";
                }
            @endphp
            
            <div class="card press mb-2">
                <div class="card-body p-1.5 flex gap-2">
                    <!-- Date Badge -->
                    <div class="flex-shrink-0">
                        <div class="date-badge {{ $badgeColor }}">
                            <span class="day">{{ $day }}</span>
                            <span class="month">{{ $monthStr }}</span>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                    <!-- Title (Keterangan) -->
                        <div class="flex justify-between items-start mb-0.5">
                            <h3 class="text-[13px] font-bold text-slate-800 leading-tight truncate pr-2">
                                {{ $d->keterangan }}
                            </h3>
                        </div>
                        
                        <!-- Subtitle (Times) -->
                        <div class="flex items-center gap-1.5 text-[11px] font-medium text-slate-500 mb-1">
                            <div class="flex items-center gap-1">
                                <ion-icon name="time-outline" class="text-[12px]"></ion-icon>
                                <span>{{ date('H:i', strtotime($d->lembur_mulai)) }} - {{ date('H:i', strtotime($d->lembur_selesai)) }}</span>
                            </div>
                            <span class="w-[3px] h-[3px] rounded-full bg-slate-300"></span>
                            <span class="text-slate-600 font-semibold">{{ $duration }}</span>
                        </div>
                        
                        <!-- Real Duration (if available) -->
                        @if($real_duration)
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500">
                            <ion-icon name="play-circle-outline" class="text-emerald-500"></ion-icon>
                            <span>Aktual: {{ date('H:i', strtotime($d->lembur_in)) }} - {{ date('H:i', strtotime($d->lembur_out)) }}</span>
                            <span class="w-[3px] h-[3px] rounded-full bg-slate-300"></span>
                            <span class="text-emerald-600 font-semibold">{{ $real_duration }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Right Actions/Status -->
                    <div class="flex flex-col items-end justify-between flex-shrink-0 ml-1">
                        <div class="status-badge {{ $statusClass }}">
                            <ion-icon name="{{ $statusIcon }}"></ion-icon>
                            <span>{{ $statusText }}</span>
                        </div>
                        
                        @if($d->status == 0)
                            <form method="POST" action="{{ route('lembur.delete', Crypt::encrypt($d->id)) }}" class="delete-form mt-1" id="deleteForm-{{ $d->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="delete-btn delete-confirm-btn" data-id="{{ $d->id }}">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            
        @empty
            <div class="flex flex-col items-center justify-center py-10 opacity-60">
                <ion-icon name="document-text-outline" class="text-6xl text-slate-300 mb-3"></ion-icon>
                <h4 class="text-sm font-semibold text-slate-600">Belum ada data lembur</h4>
                <p class="text-xs text-slate-400 mt-1 text-center px-6">Belum ada riwayat lembur yang ditemukan.</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($lembur->hasPages())
            <div class="mt-4 flex justify-center pb-4">
                {{ $lembur->links() }}
            </div>
        @endif
    </div>

    @can('lembur.create')
    <a href="{{ route('lembur.create') }}" class="fab-button modern">
        <ion-icon name="add-outline"></ion-icon>
    </a>
    @endcan

@endsection

@push('myscript')
    <script src="https://cdn.jsdelivr.net/npm/air-datepicker@3.5.0/air-datepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fake loading for skeleton
            setTimeout(() => {
                document.getElementById('skeleton-container').style.display = 'none';
                document.getElementById('real-content').style.display = 'block';
            }, 600);

            const localeIndo = {
                days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
                months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                today: 'Hari ini', clear: 'Hapus', dateFormat: 'yyyy-MM-dd', timeFormat: 'HH:mm', firstDay: 1
            };
            const dpOpt = { locale: localeIndo, autoClose: true, isMobile: true, buttons: ['today', 'clear'], position: 'bottom center' };
            new AirDatepicker('#datePicker', dpOpt);
            new AirDatepicker('#datePicker2', dpOpt);

            // Modern Delete Confirmation using SweetAlert2
            $(".delete-confirm-btn").click(function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const form = $(`#deleteForm-${id}`);
                
                Swal.fire({
                    title: 'Hapus Ajuan Lembur?',
                    text: "Data lembur yang belum disetujui akan dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0f172a',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl text-sm font-semibold px-4 py-2',
                        cancelButton: 'rounded-xl text-sm font-semibold px-4 py-2'
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
