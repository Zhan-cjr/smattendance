@extends('layouts.mobile.modern')

@section('title', 'ID Card')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        /* Premium ID Card CSS v3 - Ultimate Centering & Design */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap');

        :root {
            --id-primary: {{ $t['primary'] }};
            --id-secondary: {{ $t['primary_light'] }};
        }

        .idcard-page-layout {
            width: 100%;
            display: block;
            text-align: center;
            padding: 15px 0 40px 0; /* Reduced top padding */
            background: {{ $t['bg_body'] }};
        }

        /* Bulletproof Centering */
        .idcard-wrapper {
            font-family: 'Plus Jakarta Sans', sans-serif;
            width: 350px; /* Increased width */
            margin: 0 auto 20px auto !important;
            background: #ffffff;
            border-radius: 28px;
            position: relative;
            box-shadow: 
                0 45px 110px -20px rgba(0, 0, 0, 0.12),
                0 0 0 1px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            z-index: 1;
            padding: 0;
            overflow: hidden;
            text-align: left;
        }

        /* Premium Visual Enhancements */
        .card-ambient-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(65px);
            z-index: 0;
            opacity: 0.15;
            pointer-events: none;
        }
        .blob-1 { width: 250px; height: 250px; background: var(--id-primary); top: -80px; right: -80px; }
        .blob-2 { width: 200px; height: 200px; background: var(--id-secondary); bottom: -60px; left: -60px; }

        /* Card Header */
        .idcard-top {
            position: relative;
            z-index: 2;
            padding: 20px 24px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .company-logo-img {
            height: 38px;
            width: auto;
            object-fit: contain;
            margin-bottom: 8px;
            filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1));
        }
        .brand-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.3px;
            text-transform: uppercase;
        }
        .brand-subtitle {
            font-size: 0.65rem;
            color: #94a3b8;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Hero Profile */
        .idcard-mid {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-top: 15px;
            padding: 0 24px;
        }
        .avatar-container {
            width: 115px;
            height: 115px;
            margin: 0 auto 12px;
            position: relative;
        }
        .avatar-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(135deg, var(--id-primary), var(--id-secondary));
            box-shadow: 0 10px 25px rgba(var(--color-nav-rgb), 0.2);
        }
        .avatar-mask {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #fff;
            padding: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #f8fafc;
        }
        
        .name-heading {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            line-height: 1;
            letter-spacing: -0.5px;
        }
        .job-title-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
            padding: 5px 15px;
            background: rgba(var(--color-nav-rgb), 0.05);
            color: var(--id-primary);
            font-size: 0.8rem;
            font-weight: 800;
            border-radius: 12px;
            border: 1px solid rgba(var(--color-nav-rgb), 0.1);
        }

        /* Information Grid */
        .idcard-bottom {
            position: relative;
            z-index: 2;
            margin-top: 18px;
            padding: 0 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .card-info-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 14px;
            background: #fcfdfe;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }
        .card-info-row:hover {
            border-color: rgba(var(--color-nav-rgb), 0.2);
            background: #fff;
        }
        .icon-box-modern {
            width: 34px;
            height: 34px;
            background: #fff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--id-primary);
            font-size: 1.15rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            flex-shrink: 0;
            border: 1px solid #f1f5f9;
        }
        .text-stack { display: flex; flex-direction: column; }
        .text-label { font-size: 0.6rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0px; }
        .text-value { font-size: 0.95rem; color: #334155; font-weight: 700; }

        /* Barcode Excellence */
        .barcode-footer {
            margin-top: 10px;
            text-align: center;
        }
        .barcode-box {
            background: #fff;
            padding: 10px;
            border-radius: 14px;
            display: inline-block;
            border: 1px dashed #e2e8f0;
        }
        .barcode-id-label {
            display: block;
            margin-top: 6px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 0.8rem;
            letter-spacing: 3px;
            color: #64748b;
            font-weight: 600;
        }

        /* Finishing Accents */
        .card-bottom-line {
            height: 8px;
            width: 100%;
            background: linear-gradient(90deg, var(--id-primary), var(--id-secondary));
        }

        /* Center the final button same as card */
        .btn-premium-save {
            width: 350px; /* Increased width */
            margin: 0 auto !important;
            background: var(--color-nav) !important; /* Forces theme color from layout */
            color: #ffffff !important;
            padding: 16px; 
            border-radius: 20px;
            font-weight: 800;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15); /* Reliable shadow */
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-premium-save:active {
            transform: scale(0.96);
            opacity: 0.9;
        }


    </style>
@endpush

@section('content')
    <div class="idcard-page-layout">
        <!-- THE CARD -->
        <div class="idcard-wrapper" id="idcard-area">
            <div class="card-ambient-blob blob-1"></div>
            <div class="card-ambient-blob blob-2"></div>

            <div class="idcard-top">
                @if ($generalsetting->logo && Storage::exists('public/logo/' . $generalsetting->logo))
                    <img src="{{ asset('storage/logo/' . $generalsetting->logo) }}" class="company-logo-img" alt="Logo">
                @else
                    <img src="https://placehold.co/100x100?text=LOGO" class="company-logo-img" alt="Logo">
                @endif
                <span class="brand-name">{{ $generalsetting->nama_perusahaan ?? 'SM-Attendance' }}</span>
                <span class="brand-subtitle">Employee Official Pass</span>
            </div>

            <div class="idcard-mid">
                <div class="avatar-container">
                    <div class="avatar-ring">
                        <div class="avatar-mask">
                            @if (!empty($karyawan->foto))
                                <img src="{{ getfotoKaryawan($karyawan->foto) }}" class="avatar-img" alt="Employee Photo">
                            @else
                                <img src="{{ asset('assets/template/img/sample/avatar/avatar1.jpg') }}" class="avatar-img" alt="Default Photo">
                            @endif
                        </div>
                    </div>
                </div>
                <h2 class="name-heading">{{ textUpperCase($karyawan->nama_karyawan) }}</h2>
                <div class="job-title-badge">
                    <ion-icon name="ribbon-outline"></ion-icon>
                    <span>{{ $karyawan->nama_jabatan }}</span>
                </div>
            </div>

            <div class="idcard-bottom">
                <div class="card-info-row">
                    <div class="icon-box-modern"><ion-icon name="finger-print-outline"></ion-icon></div>
                    <div class="text-stack">
                        <span class="text-label">Personal ID / NIK</span>
                        <span class="text-value">{{ $karyawan->nik }}</span>
                    </div>
                </div>
                <div class="card-info-row">
                    <div class="icon-box-modern"><ion-icon name="business-outline"></ion-icon></div>
                    <div class="text-stack">
                        <span class="text-label">Organization / Dept</span>
                        <span class="text-value">{{ $karyawan->nama_dept }}</span>
                    </div>
                </div>
                
                <div class="barcode-footer">
                    <div class="barcode-box">
                        {!! DNS1D::getBarcodeHTML($karyawan->nik, 'C128', 1.8, 42, 'black') !!}
                    </div>
                    <span class="barcode-id-label">{{ $karyawan->nik }}</span>
                </div>
            </div>

            <div class="card-bottom-line"></div>
        </div>

        <!-- THE BUTTON -->
        <button id="download-idcard" class="btn-premium-save active:scale-95 transition-all">
            <ion-icon name="cloud-download-outline" class="text-xl"></ion-icon>
            <span>SIMPAN KE GALERI</span>
        </button>
    </div>
@endsection

@push('myscript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('download-idcard');
            if (btn) {
                btn.addEventListener('click', function() {
                    var area = document.getElementById('idcard-area');
                    if (!area) return;
                    
                    var initialLabel = btn.innerHTML;
                    btn.innerHTML = '<ion-icon name="sync-outline" class="animate-spin text-xl"></ion-icon><span>MEMPROSES...</span>';
                    btn.disabled = true;

                    html2canvas(area, {
                        backgroundColor: null,
                        scale: 4, // Ultra high quality
                        useCORS: true,
                        logging: false,
                        borderRadius: 28 
                    }).then(function(canvas) {
                        var link = document.createElement('a');
                        link.download = 'IDCard_{{ $karyawan->nik }}.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                        
                        btn.innerHTML = initialLabel;
                        btn.disabled = false;
                        
                        if(typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Disimpan!',
                                text: 'ID Card Anda sudah aman di galeri.',
                                showConfirmButton: false,
                                timer: 2000,
                                customClass: { popup: 'rounded-[1.5rem]' }
                            });
                        }
                    }).catch(function(e) {
                        console.error(e);
                        btn.innerHTML = initialLabel;
                        btn.disabled = false;
                        alert('Gagal mengunduh ID Card: ' + e.message);
                    });
                });
            }
        });
    </script>
@endpush
