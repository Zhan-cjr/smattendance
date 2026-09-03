@extends('layouts.mobile.modern')
@section('title', 'KPI Saya')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: #f8fafc !important; /* light slate background */
        }
        
        .px-wrapper {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        /* Profile Card match letter-card */
        .profile-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid #f1f5f9;
            padding: 16px;
            font-family: 'Inter', Arial, sans-serif;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .profile-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }

        .profile-avatar-fallback {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #32745e, #4b9b82);
            color: white;
            font-size: 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(50, 116, 94, 0.2);
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .profile-role {
            font-size: 12px;
            color: #64748b;
        }

        .period-badge {
            background: #f0fdf4;
            color: #32745e;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid #bbf7d0;
        }

        /* Score Card Premium Gradient */
        .score-card {
            background: linear-gradient(135deg, #32745e 0%, #1e4b3c 100%);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 20px -5px rgba(50, 116, 94, 0.4);
            position: relative;
            overflow: hidden;
        }

        /* Decorative circles */
        .score-card::before {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .score-card::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: 20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }

        .score-label {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .score-value {
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
        }

        .grade-badge {
            background: white;
            color: #32745e;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 800;
            display: inline-block;
            margin-bottom: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .status-text {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        /* Indicator Card mapping slip-card-modern */
        .indicator-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        .indicator-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .indicator-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
            max-width: 80%;
        }

        .bobot-badge {
            background: #f8fafc;
            color: #475569;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid #e2e8f0;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .metric-box {
            display: flex;
            flex-direction: column;
        }

        .metric-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .metric-value-wrapper {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .metric-value {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }

        .metric-unit {
            font-size: 11px;
            color: #64748b;
        }

        .score-success { color: #10b981; }
        .score-danger { color: #f43f5e; }

        .input-group-modern {
            display: flex;
            align-items: stretch;
            background: #f8fafc;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .input-group-modern:focus-within {
            border-color: #32745e;
            box-shadow: 0 0 0 3px rgba(50, 116, 94, 0.1);
        }

        .input-modern {
            flex: 1;
            border: none;
            background: transparent;
            padding: 10px 14px;
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            outline: none;
        }

        .unit-addon {
            background: #f1f5f9;
            padding: 0 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            border-left: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 24px 0 12px 4px;
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
    <div class="px-wrapper pt-2 pb-24">
        
        @if (Session::get('success'))
            <div class="bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-xl p-3 mb-3 flex items-center gap-2 text-sm fade-up">
                <ion-icon name="checkmark-circle-outline" class="text-lg"></ion-icon>
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::get('warning') || Session::get('error'))
            <div class="bg-rose-50 text-rose-600 border border-rose-200 rounded-xl p-3 mb-3 flex items-center gap-2 text-sm fade-up">
                <ion-icon name="alert-circle-outline" class="text-lg"></ion-icon>
                {{ Session::get('warning') ?? Session::get('error') }}
            </div>
        @endif

        {{-- Employee Profile Card --}}
        <div class="profile-card fade-up">
            @if(!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                <img src="{{ getfotoKaryawan($karyawan->foto) }}" class="profile-avatar">
            @else
                <div class="profile-avatar-fallback">
                    {{ substr($karyawan->nama_karyawan, 0, 1) }}
                </div>
            @endif
            <div class="profile-info">
                <div class="profile-name">{{ $karyawan->nama_karyawan }}</div>
                <div class="profile-role">{{ $karyawan->jabatan->nama_jabatan }}</div>
            </div>
            <div>
                <span class="period-badge">{{ $period->nama_periode }}</span>
            </div>
        </div>

        @if(isset($kpi_employee))
            {{-- Score Card Gradient --}}
            <div class="score-card fade-up" style="animation-delay: 0.1s">
                <div class="position-relative" style="z-index: 2;">
                    <div class="score-label">Total Score</div>
                    <div class="score-value">{{ number_format($kpi_employee->total_nilai, 2) }}</div>
                </div>
                <div class="text-right position-relative" style="z-index: 2; text-align: right;">
                    <div class="grade-badge">Grade {{ $kpi_employee->grade ?? '-' }}</div>
                    <div class="status-text">{{ strtoupper($kpi_employee->status) }}</div>
                </div>
            </div>

            <form action="{{ route('kpi.transactions.update', $kpi_employee->id) }}" method="POST">
                @csrf
                
                <h6 class="section-title fade-up" style="animation-delay: 0.15s">Indikator Penilaian</h6>

                @foreach ($kpi_employee->details as $index => $detail)
                    <div class="indicator-card fade-up" style="animation-delay: {{ 0.2 + ($index * 0.05) }}s">
                        {{-- Header --}}
                        <div class="indicator-header">
                            <div class="indicator-title">{{ $detail->indicator->nama_indikator }}</div>
                            <div class="bobot-badge">{{ $detail->bobot }}%</div>
                        </div>
                        
                        {{-- Metrics Grid --}}
                        <div class="metrics-grid">
                            <div class="metric-box">
                                <div class="metric-label">Target Pencapaian</div>
                                <div class="metric-value-wrapper">
                                    <span class="metric-value">{{ $detail->target }}</span>
                                    <span class="metric-unit">{{ $detail->indicator->satuan }}</span>
                                </div>
                            </div>
                            <div class="metric-box" style="align-items: flex-end;">
                                <div class="metric-label">Skor Akhir</div>
                                <div class="metric-value-wrapper">
                                    <span class="metric-value {{ $detail->skor >= 70 ? 'score-success' : 'score-danger' }}">
                                        {{ number_format($detail->skor, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Realisasi Input --}}
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Realisasi</label>
                            <input type="hidden" name="detail_id[]" value="{{ $detail->id }}">
                            <div class="input-group-modern">
                                <input type="number" step="0.01" class="input-modern" name="realisasi[]" 
                                       value="{{ $detail->realisasi }}" required 
                                       {{ $kpi_employee->status == 'approved' || $detail->indicator->mode == 'auto' ? 'readonly' : '' }} 
                                       placeholder="0">
                                <div class="unit-addon">{{ $detail->indicator->satuan }}</div>
                            </div>
                            
                            @if($detail->indicator->mode == 'auto')
                                <div class="flex items-center gap-1 text-xs text-[#32745e] font-semibold mt-2 bg-[#f0fdf4] rounded-lg p-2 border border-[#bbf7d0]">
                                    <ion-icon name="sync-outline"></ion-icon>
                                    Auto Sync: {{ $detail->indicator->metric_source }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                
                @if($kpi_employee->status != 'approved')
                    <div class="mt-6 text-center fade-up" style="animation-delay: {{ 0.2 + (count($kpi_employee->details) * 0.05) }}s">
                        <button type="submit" class="w-full py-3.5 bg-[#32745e] text-white rounded-xl font-bold text-[15px] shadow-lg shadow-[#32745e]/20 active:scale-95 transition-all flex justify-center items-center gap-2">
                            <ion-icon name="save-outline" class="text-xl"></ion-icon> Simpan Realisasi
                        </button>
                    </div>
                @endif
            </form>

        @else
            {{-- Target Mode (Initial Setup) --}}
             <form action="{{ route('kpi.transactions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="nik" value="{{ $karyawan->nik }}">
                <input type="hidden" name="kpi_period_id" value="{{ $period->id }}">

                 @if ($indicators->isEmpty())
                    <div class="bg-amber-50 text-amber-700 border border-amber-200 rounded-xl p-3 mb-3 flex items-center gap-2 text-sm fade-up">
                        <ion-icon name="warning-outline" class="text-lg"></ion-icon>
                        Indikator belum disetting oleh Admin.
                    </div>
                @else
                    <h6 class="section-title fade-up" style="animation-delay: 0.1s">Set Target KPI</h6>
                    @php $total_bobot = 0; @endphp
                    
                    @foreach ($indicators as $index => $indicator)
                        <div class="indicator-card fade-up" style="animation-delay: {{ 0.1 + ($index * 0.05) }}s">
                            <div class="indicator-header">
                                <div class="indicator-title">{{ $loop->iteration }}. {{ $indicator->nama_indikator }}</div>
                                <input type="hidden" name="indicator_id[]" value="{{ $indicator->id }}">
                                <div class="bobot-badge text-[#32745e] bg-[#f0fdf4] border-[#bbf7d0]">{{ $indicator->bobot }}%</div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-1 rounded border border-slate-200 uppercase">
                                    {{ strtoupper($indicator->jenis_target) }} TARGET
                                </span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tentukan Target</label>
                                <div class="input-group-modern">
                                    <input type="number" step="0.01" class="input-modern" name="target[]" 
                                           value="{{ $indicator->target }}" required placeholder="Input Target">
                                    <div class="unit-addon">{{ $indicator->satuan }}</div>
                                </div>
                                <input type="hidden" name="bobot[]" value="{{ $indicator->bobot }}">
                            </div>
                        </div>
                        @php $total_bobot += $indicator->bobot; @endphp
                    @endforeach

                    <div class="indicator-card bg-slate-50 border-dashed border-2 fade-up" style="animation-delay: {{ 0.1 + (count($indicators) * 0.05) }}s">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-600">Total Keseluruhan Bobot</span>
                            <span class="text-lg font-black {{ $total_bobot != 100 ? 'text-rose-500' : 'text-emerald-500' }}">
                                {{ $total_bobot }}%
                            </span>
                        </div>
                        @if($total_bobot != 100)
                            <div class="text-[10px] text-rose-500 mt-1">Total bobot komulatif harus tepat 100%. Hubungi Admin jika ini salah.</div>
                        @endif
                    </div>

                    <div class="mt-6 text-center fade-up" style="animation-delay: {{ 0.15 + (count($indicators) * 0.05) }}s">
                        <button type="submit" {{ $total_bobot != 100 ? 'disabled' : '' }} class="w-full py-3.5 bg-[#32745e] text-white rounded-xl font-bold text-[15px] shadow-lg shadow-[#32745e]/20 active:scale-95 transition-all flex justify-center items-center gap-2 disabled:opacity-50 disabled:scale-100 disabled:cursor-not-allowed">
                            <ion-icon name="checkmark-circle-outline" class="text-xl"></ion-icon> Simpan Target
                        </button>
                    </div>
                @endif
             </form>
        @endif
    </div>
@endsection
