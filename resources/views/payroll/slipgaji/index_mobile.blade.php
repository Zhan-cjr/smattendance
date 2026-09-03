@extends('layouts.mobile.modern')

@section('title', 'Slip Gaji')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: #e6fcf5 !important;
        }

        .search-container {
            padding: 10px 5px;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 12px;
            background: transparent !important;
            border: 1px solid #32745e;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 15px;
            top: 15px;
            font-size: 24px;
            color: #32745e;
            z-index: 10;
            pointer-events: none;
        }

        .form-label-group select {
            width: 100% !important;
            height: 54px;
            padding: 22px 15px 5px 52px !important;
            font-size: 16px;
            font-weight: 500;
            color: #2a6350;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            display: block !important;
            appearance: none;
        }

        .form-label-group label {
            position: absolute;
            top: 15px;
            left: 52px;
            font-size: 16px;
            color: #32745e;
            opacity: 0.8;
            pointer-events: none;
            transition: all 0.2s ease-in-out;
            margin-bottom: 0;
            z-index: 5;
        }

        .form-label-group select:focus ~ label,
        .form-label-group select:valid ~ label {
            top: 5px;
            left: 52px;
            font-size: 11px;
            font-weight: 600;
            color: #32745e;
        }

        .btn-search-modern {
            width: 100%;
            height: 48px;
            background: #32745e;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            margin-bottom: 15px;
        }

        .btn-search-modern:active {
            transform: scale(0.97);
            background: #2a6350;
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

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6a9c89;
        }
    </style>
@endpush

@section('content')
    <div class="fade-up search-container pb-24">
        {{-- Search Form (Temporarily Removed) --}}
        {{-- 
        <form action="{{ route('slipgaji.index') }}" method="GET" class="mb-4">
            ...
        </form>
        --}}

        @can('slipgaji.create')
            <div class="mb-4 px-1">
                <a href="#" class="flex items-center justify-center gap-2 w-full py-3 bg-[#32745e] text-white rounded-xl font-bold text-sm shadow-lg shadow-[#32745e]/20" id="btnCreate">
                    <ion-icon name="add-circle-outline" class="text-xl"></ion-icon>
                    <span>Buat Slip Gaji Baru</span>
                </a>
            </div>
        @endcan

        {{-- Slip Gaji List --}}
        <h2 class="px-1 text-[13px] font-bold text-[#32745e] uppercase tracking-wider mb-3">Daftar Slip Gaji</h2>
        
        @if (count($slipgaji))
            @foreach ($slipgaji as $d)
                @php
                    $periode_laporan_dari = $general_setting->periode_laporan_dari;
                    $periode_laporan_sampai = $general_setting->periode_laporan_sampai;
                    $periode_laporan_lintas_bulan = $general_setting->periode_laporan_next_bulan;

                    if ($periode_laporan_lintas_bulan == 1) {
                        if ($d->bulan == 1) {
                            $bulan = 12;
                            $tahun_dari = $d->tahun - 1;
                        } else {
                            $bulan = $d->bulan - 1;
                            $tahun_dari = $d->tahun;
                        }
                    } else {
                        $bulan = $d->bulan;
                        $tahun_dari = $d->tahun;
                    }

                    $bulan_pad = str_pad($bulan, 2, '0', STR_PAD_LEFT);
                    $bulan_next_pad = str_pad($d->bulan, 2, '0', STR_PAD_LEFT);
                    $periode_dari = $tahun_dari . '-' . $bulan_pad . '-' . $periode_laporan_dari;
                    $periode_sampai = $d->tahun . '-' . $bulan_next_pad . '-' . $periode_laporan_sampai;
                @endphp

                <a href="/laporan/cetakslipgaji?bulan={{ $d->bulan }}&tahun={{ $d->tahun }}&periode_laporan=1" class="block">
                    <div class="slip-card-modern">
                        <div class="slip-icon-box">
                            <ion-icon name="document-text-outline" class="text-2xl"></ion-icon>
                        </div>
                        <div class="slip-info">
                            <div class="slip-title">Slip Gaji {{ getNamabulan($d->bulan) }} {{ $d->tahun }}</div>
                            <div class="slip-period">
                                <ion-icon name="time-outline"></ion-icon>
                                <span>{{ date('d/m/y', strtotime($periode_dari)) }} - {{ date('d/m/y', strtotime($periode_sampai)) }}</span>
                            </div>
                        </div>
                        <ion-icon name="chevron-forward-outline" class="chevron-icon"></ion-icon>
                        
                        {{-- Admin Actions --}}
                        @if(auth()->user()->can('slipgaji.edit') || auth()->user()->can('slipgaji.delete'))
                            <div class="absolute bottom-2 right-10 flex gap-1 items-center" onclick="event.preventDefault()">
                                @can('slipgaji.edit')
                                    <button class="btnEdit p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" kode_slip_gaji="{{ Crypt::encrypt($d->kode_slip_gaji) }}">
                                        <ion-icon name="create-outline" class="text-base"></ion-icon>
                                    </button>
                                @endcan
                                @can('slipgaji.delete')
                                    <form method="POST" name="deleteform" class="deleteform inline" action="{{ route('slipgaji.delete', Crypt::encrypt($d->kode_slip_gaji)) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-confirm p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <ion-icon name="trash-outline" class="text-base"></ion-icon>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        @else
            <div class="empty-state">
                <ion-icon name="documents-outline" class="text-5xl mb-2 opacity-50"></ion-icon>
                <p class="font-bold">Tidak ada data slip gaji</p>
                <p class="text-xs opacity-70">Silakan pilih tahun lain atau hubungi admin.</p>
            </div>
        @endif
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            // Ripple effect simulated via CSS :active
            
            @can('slipgaji.create')
            $("#btnCreate").click(function(e) {
                e.preventDefault();
                // If there's a modal or specific redirect, handle it here.
                // For now, assuming standard creation flow or modal.
                Swal.fire({
                    title: 'Buat Slip Gaji?',
                    text: "Fitur ini biasanya diakses dari panel admin.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#32745e',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Lanjutkan'
                });
            });
            @endcan

            $(".delete-confirm").click(function(e) {
                var form = $(this).closest("form");
                e.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Data slip gaji ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#32745e',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            });
        });
    </script>
@endpush
