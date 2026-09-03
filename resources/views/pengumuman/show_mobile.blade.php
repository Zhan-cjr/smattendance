@extends('layouts.mobile.modern')
@section('title', 'Detail Pengumuman')

@section('header_left')
    <a href="{{ route('pengumuman.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: #f8fafc !important; /* light slate background */
        }
        
        .letter-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
            padding: 24px 20px;
            font-family: 'Inter', Arial, sans-serif;
            color: #334155;
            position: relative;
            overflow: hidden;
        }

        /* Decorative top accent for the document */
        .letter-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #32745e, #4b9b82); /* Green theme */
        }

        .announcement-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .announcement-icon {
            width: 48px;
            height: 48px;
            background: #f0fdf4;
            color: #32745e;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 24px;
        }

        .announcement-meta {
            flex-grow: 1;
        }

        .announcement-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 4px 0;
            line-height: 1.3;
        }

        .announcement-date {
            font-size: 12px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .announcement-content {
            font-size: 14px;
            line-height: 1.7;
            color: #475569;
            text-align: justify;
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
    </style>
@endpush

@section('content')
    <div class="px-1 pt-2 pb-24">
        <div class="letter-card fade-up">
            <div class="announcement-header">
                <div class="announcement-icon">
                    <ion-icon name="megaphone-outline"></ion-icon>
                </div>
                <div class="announcement-meta">
                    <h3 class="announcement-title">{{ $pengumuman->judul }}</h3>
                    <div class="announcement-date">
                        <ion-icon name="time-outline"></ion-icon>
                        {{ \Carbon\Carbon::parse($pengumuman->created_at)->translatedFormat('d F Y, H:i') }}
                    </div>
                </div>
            </div>
            
            <div class="announcement-content">
                {!! nl2br(e($pengumuman->isi)) !!}
            </div>
        </div>
    </div>
@endsection
