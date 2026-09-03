@extends('layouts.mobile.modern')
@section('title', 'Data Pelanggaran')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: #e6fcf5 !important;
        }

        /* Animations */
        .fade-up {
            animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(15px);
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Slip Card Styling Adapted for Pelanggaran */
        .slip-card-modern {
            background: #fff;
            border: 1px solid #32745e20;
            border-radius: 16px;
            padding: 12px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 15px rgba(50, 116, 94, 0.05);
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .slip-card-modern:active {
            transform: scale(0.98);
            background: #f0fff4;
        }

        /* Icon Box Highlights based on SP type */
        .slip-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 24px;
        }

        .icon-sp1 { background: #fef9c3; color: #eab308; } /* Yellow */
        .icon-sp2 { background: #ffedd5; color: #f97316; } /* Orange */
        .icon-sp3 { background: #fee2e2; color: #ef4444; } /* Red */

        .slip-info {
            flex: 1;
            min-width: 0;
            padding-right: 10px;
        }

        .slip-title {
            font-size: 14px;
            font-weight: 700;
            color: #2a6350;
            margin-bottom: 3px;
            line-height: 1.2;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Description Line replacing period */
        .slip-desc {
            font-size: 11px;
            font-weight: 500;
            color: #6a9c89;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }

        .chevron-icon {
            color: #32745e;
            font-size: 20px;
            opacity: 0.5;
            flex-shrink: 0;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6a9c89;
        }

        /* Skeleton Loaders */
        .skeleton {
            background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
            border-radius: 5px;
            background-size: 200% 100%;
            animation: 1.5s shine linear infinite;
        }
        @keyframes shine { to { background-position-x: -200%; } }
    </style>
@endpush

@section('content')
    {{-- Sticky Skeleton Loading Initial View --}}
    <div id="skeleton-container" class="px-3" style="padding-top: 15px;">
        @for($i = 0; $i < 4; $i++)
            <div class="slip-card-modern border-0" style="background: #ffffff90;">
                <div class="skeleton w-[50px] h-[50px] rounded-xl flex-shrink-0"></div>
                <div class="slip-info space-y-2 py-1">
                    <div class="skeleton h-4 w-1/3"></div>
                    <div class="skeleton h-3 w-5/6"></div>
                    <div class="skeleton h-3 w-4/6"></div>
                </div>
            </div>
        @endfor
    </div>

    <div id="data-container" style="display: none; padding-bottom: 100px;">
        <div class="px-1 mt-2">
            <h2 class="text-[13px] font-bold text-[#32745e] uppercase tracking-wider mb-3 ml-2">Riwayat Pelanggaran</h2>

            @if (count($pelanggaran))
                @foreach ($pelanggaran as $index => $d)
                    @php
                        $iconClass = '';
                        if($d->jenis_sp == 'SP1') {
                            $iconClass = 'icon-sp1';
                        } elseif($d->jenis_sp == 'SP2') {
                            $iconClass = 'icon-sp2';
                        } elseif($d->jenis_sp == 'SP3') {
                            $iconClass = 'icon-sp3';
                        }
                    @endphp
                    <a href="{{ route('pelanggaran.show', Crypt::encrypt($d->no_sp)) }}" class="block fade-up" style="animation-delay: {{ $index * 0.05 }}s">
                        <div class="slip-card-modern">
                            <div class="slip-icon-box {{ $iconClass }}">
                                <ion-icon name="warning"></ion-icon>
                            </div>
                            <div class="slip-info">
                                <div class="slip-title">
                                    <span>{{ $d->jenis_sp }}</span>
                                    <span class="text-[11px] font-semibold text-slate-400">
                                        {{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                                <div class="slip-desc">
                                    {{ $d->keterangan }}
                                </div>
                            </div>
                            <ion-icon name="chevron-forward-outline" class="chevron-icon"></ion-icon>
                        </div>
                    </a>
                @endforeach
            @else
                <div class="empty-state fade-up" style="animation-delay: 0.1s">
                    <ion-icon name="document-text-outline" class="text-5xl mb-3 opacity-50"></ion-icon>
                    <p class="font-bold text-lg text-[#2a6350] mb-1">Tidak Ada Pelanggaran</p>
                    <p class="text-sm opacity-80">Anda memiliki catatan yang bersih.</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('myscript')
    <script>
        $(document).ready(function() { 
            setTimeout(function() {
                $('#skeleton-container').hide(); 
                $('#data-container').fadeIn(300);
            }, 400); 
        });
        
        $(window).on('load', function() { 
            setTimeout(function() {
                if($('#skeleton-container').is(':visible')) {
                    $('#skeleton-container').hide(); 
                    $('#data-container').fadeIn(300);
                }
            }, 250); 
        });
    </script>
@endpush
