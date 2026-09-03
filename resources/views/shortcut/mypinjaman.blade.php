@extends('layouts.mobile.modern')
@section('title', 'Pinjaman (PJP)')

@section('header_left')
    <a href="{{ route('shortcut.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background-color: #f1f5f9;
        }

        .balance-card {
            background: linear-gradient(135deg, var(--color-nav) 0%, #1e293b 100%);
            border-radius: 14px;
            padding: 14px 18px;
            color: white;
            margin-bottom: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* ATM Card Ornaments */
        .card-chip {
            width: 34px;
            height: 24px;
            background: linear-gradient(135deg, #fef3c7 0%, #f59e0b 100%);
            border-radius: 4px;
            margin-bottom: 8px;
            position: relative;
            box-shadow: inset 0 0 2px rgba(0,0,0,0.1);
        }

        .card-chip::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(0,0,0,0.03);
        }
        
        .card-chip::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 50%;
            width: 1px;
            background: rgba(0,0,0,0.03);
        }

        .card-logo {
            position: absolute;
            top: 14px;
            right: 18px;
            opacity: 0.3;
            font-size: 16px;
        }

        .card-wave {
            position: absolute;
            bottom: -15px;
            right: -15px;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.015);
            border-radius: 50%;
        }

        .card-wave-2 {
            position: absolute;
            bottom: -20px;
            right: 10px;
            width: 80px;
            height: 80px;
            background: transparent;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.02);
        }

        /* Slider Styles */
        .loan-slider-container {
            margin: 0 -12px; /* Pull to edges */
            padding-bottom: 12px;
        }

        .loan-slider {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            padding: 0 16px;
            gap: 12px;
        }

        .loan-slider::-webkit-scrollbar {
            display: none;
        }

        .loan-item-slide {
            flex-shrink: 0;
            width: 88vw;
            max-width: 400px;
            scroll-snap-align: center;
        }

        .loan-item {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            height: 100%;
        }

        .loan-header {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .loan-body {
            padding: 12px 16px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .info-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }

        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }

        .schedule-table {
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
            padding: 12px 16px;
            max-height: 250px;
            overflow-y: auto;
        }

        .schedule-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .schedule-row:last-child {
            border-bottom: none;
        }

        .status-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .status-A { background: #dcfce7; color: #15803d; }
        .status-L { background: #dbeafe; color: #1d4ed8; }
        .status-B { background: #fee2e2; color: #b91c1c; }

        /* Pagination Indicators */
        .indicator-container {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: 12px;
        }

        .indicator-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #cbd5e1;
            transition: all 0.3s ease;
        }

        .indicator-dot.active {
            background: var(--color-nav);
            width: 14px;
            border-radius: 10px;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-1 pb-24">
        
        {{-- ATM Style Balance Card --}}
        <div class="balance-card fade-in">
            <div class="card-logo"><ion-icon name="wallet"></ion-icon></div>
            <div class="card-wave"></div>
            <div class="card-wave-2"></div>
            <div class="card-chip"></div>
            
            <div class="mb-6 relative z-10">
                <p class="text-[10px] font-bold text-white/60 uppercase tracking-[1px]">Sisa Saldo Pinjaman</p>
                <h2 class="text-2xl font-black mt-1 tracking-tight text-white">
                    Rp {{ number_format($sisa_pinjaman, 0, ',', '.') }}
                </h2>
            </div>
            <div class="flex justify-between items-end pt-4 border-t border-white/10 relative z-10">
                <div>
                    <p class="text-[9px] text-white/50 uppercase font-bold mb-1">Total Loan</p>
                    <p class="text-[13px] font-bold">Rp {{ number_format($total_pinjaman, 0, ',', '.') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[9px] text-white/50 uppercase font-bold mb-1">Paid Amount</p>
                    <p class="text-[13px] font-bold">Rp {{ number_format($total_dibayar, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <h4 class="text-[12px] font-bold text-slate-500 px-1 mb-2 uppercase tracking-wide">Riwayat Pinjaman</h4>
            
            <div class="loan-slider-container fade-in">
                <div class="loan-slider" id="loanSlider">
                    @forelse ($pinjaman as $index => $item)
                        <div class="loan-item-slide" data-index="{{ $index }}">
                            <div class="loan-item">
                                <div class="loan-header">
                                    <div>
                                        <span class="text-[13px] font-bold text-slate-800">{{ $item->no_pinjaman }}</span>
                                        <p class="text-[11px] text-slate-400">{{ date('d M Y', strtotime($item->tanggal_pinjaman)) }}</p>
                                    </div>
                                    <span class="status-badge status-{{ $item->status }}">
                                        {{ $item->status == 'A' ? 'AKTIF' : ($item->status == 'L' ? 'LUNAS' : 'BATAL') }}
                                    </span>
                                </div>

                                <div class="loan-body">
                                    <div class="info-row">
                                        <span class="info-label">Jumlah Pinjaman</span>
                                        <span class="info-value">Rp {{ number_format($item->jumlah_pinjaman, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Sisa Saldo</span>
                                        <span class="info-value text-emerald-600">Rp {{ number_format($item->sisa_pinjaman, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Tenor</span>
                                        <span class="info-value">{{ $item->jumlah_cicilan }} Bulan</span>
                                    </div>
                                </div>

                                @if($item->rencana_cicilan->count() > 0)
                                    <div class="schedule-table">
                                        <p class="text-[10px] font-bold text-slate-400 mb-2 uppercase tracking-tight">Jadwal Angsuran</p>
                                        @foreach($item->rencana_cicilan as $cicilan)
                                            <div class="schedule-row">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-[10px] font-bold text-slate-300 w-4">{{ $cicilan->cicilan_ke }}</span>
                                                    <span class="text-[12px] font-medium text-slate-600">{{ getNamabulan($cicilan->bulan) }} {{ $cicilan->tahun }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[12px] font-bold text-slate-800">Rp {{ number_format($cicilan->jumlah_cicilan, 0, ',', '.') }}</span>
                                                    @if($cicilan->status == 'S')
                                                        <ion-icon name="checkmark-circle" class="text-emerald-500 text-base"></ion-icon>
                                                    @else
                                                        <ion-icon name="ellipse-outline" class="text-slate-200 text-base"></ion-icon>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Payment History --}}
                                @if($item->pembayaran_pinjaman->count() > 0)
                                    <div class="schedule-table" style="border-top: 1px dashed #e2e8f0; background-color: #fff;">
                                        <p class="text-[10px] font-bold text-emerald-600 mb-2 uppercase tracking-tight">Histori Pembayaran</p>
                                        @foreach($item->pembayaran_pinjaman as $bayar)
                                            <div class="schedule-row">
                                                <div class="flex flex-col">
                                                    <span class="text-[11px] font-bold text-slate-700">{{ date('d M Y', strtotime($bayar->tanggal_bayar)) }}</span>
                                                    <span class="text-[9px] text-slate-400">{{ $bayar->no_bukti }}</span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-[12px] font-bold text-emerald-600">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="w-full py-12 text-center fade-in">
                            <p class="text-slate-400 text-[12px]">Belum ada riwayat pinjaman aktif.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Slider Indicators --}}
                @if($pinjaman->count() > 1)
                    <div class="indicator-container" id="sliderIndicators">
                        @foreach($pinjaman as $index => $item)
                            <div class="indicator-dot {{ $index == 0 ? 'active' : '' }}" data-dot="{{ $index }}"></div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('myscript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('loanSlider');
        const dots = document.querySelectorAll('.indicator-dot');

        if (slider && dots.length > 0) {
            slider.addEventListener('scroll', () => {
                const scrollLeft = slider.scrollLeft;
                const itemWidth = slider.querySelector('.loan-item-slide').offsetWidth + 12; // Width + gap
                const index = Math.round(scrollLeft / itemWidth);

                dots.forEach((dot, i) => {
                    if (i === index) {
                        dot.classList.add('active');
                    } else {
                        dot.classList.remove('active');
                    }
                });
            });
        }
    });
</script>
@endpush
