@extends('layouts.mobile.modern')
@section('title', 'Daftar Pengumuman')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        .search-container {
            padding: 10px 5px;
        }

        /* Slip Card Styling */
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

        .slip-icon-box {
            width: 50px;
            height: 50px;
            background: rgba(50, 116, 94, 0.1);
            color: #32745e;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .slip-info {
            flex: 1;
            min-width: 0;
        }

        .slip-title {
            font-size: 14px;
            font-weight: 700;
            color: #2a6350;
            margin-bottom: 2px;
            line-height: 1.2;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .slip-period {
            font-size: 11px;
            font-weight: 600;
            color: #6a9c89;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .chevron-icon {
            color: #32745e;
            font-size: 20px;
            opacity: 0.5;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6a9c89;
        }

        /* Animations */
        .fade-up {
            animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .skeleton-loader {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 8px;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
@endpush

@section('content')
    {{-- Skeleton Loading Containers --}}
    <div id="skeleton-container" class="search-container pb-24">
        @for ($i = 0; $i < 4; $i++)
            <div class="slip-card-modern mb-3" style="box-shadow: none; border: 1px solid #f1f5f9;">
                <div class="skeleton-loader" style="width: 50px; height: 50px; border-radius: 12px; margin-right: 12px; flex-shrink: 0;"></div>
                <div style="flex-grow: 1;">
                    <div class="skeleton-loader mb-2" style="height: 16px; width: 60%;"></div>
                    <div class="skeleton-loader mb-2" style="height: 12px; width: 80%;"></div>
                    <div class="skeleton-loader" style="height: 10px; width: 40%;"></div>
                </div>
                <div class="skeleton-loader" style="width: 16px; height: 16px; border-radius: 50%; margin-left: 10px;"></div>
            </div>
        @endfor
    </div>

    <div id="data-container" style="display: none; padding-bottom: 100px;">
        <div class="search-container">
            
            @if ($pengumuman->isNotEmpty())
                @foreach ($pengumuman as $index => $d)
                    <a href="{{ route('pengumuman.show', Crypt::encrypt($d->id)) }}" class="block fade-up" style="animation-delay: {{ $index * 0.05 }}s">
                        <div class="slip-card-modern">
                            <div class="slip-icon-box">
                                <ion-icon name="megaphone-outline" class="text-2xl"></ion-icon>
                            </div>
                            <div class="slip-info">
                                <div class="slip-title">
                                    <span>{{ $d->judul }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 line-clamp-2 mt-0.5 mb-1 pr-2 leading-relaxed">
                                    {{ Str::limit(strip_tags($d->isi), 100) }}
                                </div>
                                <div class="slip-period">
                                    <ion-icon name="calendar-outline"></ion-icon>
                                    <span>{{ \Carbon\Carbon::parse($d->created_at)->translatedFormat('d F Y') }}</span>
                                </div>
                            </div>
                            <ion-icon name="chevron-forward-outline" class="chevron-icon"></ion-icon>
                        </div>
                    </a>
                @endforeach
            @else
                <div class="empty-state fade-up" style="animation-delay: 0.1s">
                    <ion-icon name="megaphone-outline" class="text-5xl mb-3" style="opacity: 0.5"></ion-icon>
                    <p style="font-size: 16px; font-weight: 700; color: #2a6350; margin-bottom: 5px;">Belum Ada Pengumuman</p>
                    <p style="font-size: 13px; color: #6a9c89;">Belum ada data pengumuman yang tercatat.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(document).ready(function() {
            // Simulate loading to show skeleton effect briefly for smooth transition
            setTimeout(function() {
                $('#skeleton-container').fadeOut(200, function() {
                    $('#data-container').fadeIn(300);
                });
            }, 500);
        });
    </script>
@endpush
