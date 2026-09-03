@extends('layouts.mobile.app')
@section('content')
    <style>
        /* Konfigurasi Header */
        #header-section {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        /* Konfigurasi Konten agar tidak kosong saat data tidak ada */
        #content-section {
            margin-top: 70px;
            padding-top: 5px;
            padding-bottom: 150px; /* Jarak bawah agar tidak tertutup FAB */
            min-height: 90vh; /* Memaksa konten minimal hampir setinggi layar */
            position: relative;
        }

        /* PERBAIKAN UTAMA: FAB BUTTON */
        /* Kita gunakan fixed agar posisi terkunci di layar browser, bukan di dalam div konten */
        .fab-button.bottom-left {
            position: fixed !important;
            bottom: 110px !important; /* Naikkan ini jika masih tertutup (misal 120px) */
            left: 20px !important;
            top: auto !important;
            right: auto !important;
            z-index: 9999 !important;
            transform: none !important; /* Menghapus efek transform yang sering bikin posisi kacau */
        }

        /* Mengatur agar menu dropdown muncul ke atas */
        .fab-button .dropdown-menu {
            position: absolute !important;
            bottom: 60px !important;
            top: auto !important;
            left: 0 !important;
            transform: none !important;
            margin-bottom: 5px;
        }

        /* Styling tambahan untuk list */
        .avatar { position: relative; width: 2.5rem; height: 2.5rem; cursor: pointer; }
        .rounded-circle { border-radius: 50% !important; }

        /* Skeleton Loader Styles */
        .skeleton { background-color: #e0e0e0; border-radius: 4px; position: relative; overflow: hidden; }
        .skeleton::after {
            content: ""; position: absolute; top: 0; right: 0; bottom: 0; left: 0;
            transform: translateX(-100%);
            background-image: linear-gradient(90deg, rgba(255,255,255,0) 0, rgba(255,255,255,0.2) 20%, rgba(255,255,255,0.5) 60%, rgba(255,255,255,0));
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer { 100% { transform: translateX(100%); } }
        .skeleton-circle { border-radius: 50%; }
        .skeleton-text { height: 10px; margin-bottom: 6px; }
        .skeleton-badge { height: 20px; border-radius: 10px; }
        .content-hide { display: none; }
    </style>

    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Pengajuan Izin</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="content-section">
        <div id="skeleton-loader">
            <div class="row" style="margin-top:10px">
                <div class="col">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="item mb-2 p-2" style="border-bottom: 1px solid #f0f0f0; display: flex; align-items: center;">
                            <div class="skeleton skeleton-circle me-3" style="width: 45px; height: 45px;"></div>
                            <div style="flex: 1;">
                                <div class="skeleton skeleton-text" style="width: 40%;"></div>
                                <div class="skeleton skeleton-text" style="width: 70%;"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <div id="real-content" class="content-hide">
            <div class="row" style="margin-top: 10px">
                <div class="col">
                    @forelse ($pengajuan_izin as $d)
                        @php
                            // Logika penentuan route dan teks
                            if ($d->ket == 'i') { $route = 'izinabsen.delete'; $ket_text = 'Izin Absen'; }
                            elseif ($d->ket == 's') { $route = 'izinsakit.delete'; $ket_text = 'Izin Sakit'; }
                            elseif ($d->ket == 'c') { $route = 'izincuti.delete'; $ket_text = 'Izin Cuti'; }
                            elseif ($d->ket == 'd') { $route = 'izindinas.delete'; $ket_text = 'Izin Dinas'; }
                            
                            $tgl = date('d', strtotime($d->dari));
                            $day_short = strtoupper(substr(date('D', strtotime($d->dari)), 0, 3));
                            $status_text = $d->status_izin == 0 ? 'Pending' : ($d->status_izin == 1 ? 'Disetujui' : 'Ditolak');
                            $text_color = $d->status_izin == 0 ? '#ff9f40' : ($d->status_izin == 1 ? '#1DAB47' : '#e74c3c');
                        @endphp
                        
                        <form method="POST" action="{{ route($route, Crypt::encrypt($d->kode)) }}" class="deleteform">
                            @csrf
                            @method('DELETE')
                            <div class="card mb-1" style="border: 1px solid #eee; border-radius: 10px;">
                                <div class="card-body p-2 d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center" 
                                        style="width: 45px; height: 45px; border-radius: 10px; background: {{ $text_color }}20; color: {{ $text_color }};">
                                        <div class="text-center">
                                            <small style="font-size: 9px; font-weight: bold; display: block;">{{ $day_short }}</small>
                                            <strong style="font-size: 16px; display: block; margin-top: -3px;">{{ $tgl }}</strong>
                                        </div>
                                    </div>
                                    <div class="ms-2" style="flex: 1;">
                                        <div class="d-flex justify-content-between">
                                            <h4 class="mb-0" style="font-size: 14px;">{{ $ket_text }}</h4>
                                            <span class="badge {{ $d->status_izin == 1 ? 'badge-success' : ($d->status_izin == 2 ? 'badge-danger' : 'badge-warning') }}" style="font-size: 9px;">
                                                {{ $status_text }}
                                            </span>
                                        </div>
                                        <small class="text-muted">{{ date('d M Y', strtotime($d->dari)) }}</small>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @empty
                        <div class="text-center p-5">
                            <ion-icon name="document-outline" style="font-size: 40px; color: #ddd;"></ion-icon>
                            <p class="text-muted">Data Kosong</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="fab-button animate bottom-left dropdown">
        <a href="#" class="fab bg-primary" data-toggle="dropdown">
            <ion-icon name="add-outline"></ion-icon>
        </a>
        <div class="dropdown-menu">
            <a class="dropdown-item bg-primary" href="{{ route('izinabsen.create') }}">
                <ion-icon name="document-outline"></ion-icon>
                <p>Izin Absen</p>
            </a>
            <a class="dropdown-item bg-primary" href="{{ route('izinsakit.create') }}">
                <ion-icon name="bag-add-outline"></ion-icon>
                <p>Izin Sakit</p>
            </a>
            <a class="dropdown-item bg-primary" href="{{ route('izincuti.create') }}">
                <ion-icon name="document-text-outline"></ion-icon>
                <p>Izin Cuti</p>
            </a>
            <a class="dropdown-item bg-primary" href="{{ route('izindinas.create') }}">
                <ion-icon name="airplane-outline"></ion-icon>
                <p>Izin Dinas</p>
            </a>
        </div>
    </div>

@endsection

@push('myscript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.getElementById('skeleton-loader').style.display = 'none';
                document.getElementById('real-content').classList.remove('content-hide');
            }, 500);
        });
    </script>
@endpush