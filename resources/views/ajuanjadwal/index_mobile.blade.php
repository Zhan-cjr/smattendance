@extends('layouts.mobile.modern')
@section('title', 'Ajuan Jadwal')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/15 text-white active:scale-90 transition-transform">
        <ion-icon name="chevron-back-outline" class="text-base"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background-color: #f8fafc !important; 
        }

        /* Replicating the minimal card style from histori */
        .card.press {
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            background: #fff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card.press:active {
            transform: scale(0.98);
            box-shadow: 0 1px 2px rgba(0,0,0,0.01);
            background: #f8fafc;
        }

        /* FAB matching modern theme */
        .fab-button.modern {
            position: fixed;
            bottom: 110px; /* raised from 95px */
            right: 20px;
            z-index: 999;
        }

        .fab-button.modern .fab {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 15px rgba(50, 116, 94, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fab-button.modern .fab:active {
            transform: scale(0.9);
            box-shadow: 0 2px 8px rgba(50, 116, 94, 0.3);
        }

        /* Base Typography Overrides for this view */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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

        /* Skeleton synced with dashboard/histori */
        .skeleton-loader {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
        }

        .skeleton-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-2 pb-24">

        {{-- Top Info / Feedback --}}
        @if (Session::get('success') || Session::has('warning'))
            <div class="mb-2 px-1 fade-up">
                @if (Session::get('success'))
                    <div class="bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-xl p-3 flex items-center gap-2 text-sm">
                        <ion-icon name="checkmark-circle-outline" class="text-lg"></ion-icon>
                        {{ Session::get('success') }}
                    </div>
                @endif
                @if (Session::get('warning'))
                    <div class="bg-rose-50 text-rose-600 border border-rose-200 rounded-xl p-3 flex items-center gap-2 text-sm">
                        <ion-icon name="alert-circle-outline" class="text-lg"></ion-icon>
                        {{ Session::get('warning') }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Skeleton Loading --}}
        <div id="skeleton-container" class="space-y-1 pt-1">
            @for ($i = 0; $i < 4; $i++)
                <div class="rounded-[10px] p-1 border shadow-sm" style="background: #fff; border-color: #f1f5f9;">
                    <div class="flex items-center gap-1.5">
                        <div class="skeleton-avatar flex-shrink-0" style="width: 38px; height: 38px; border-radius: 8px;"></div>
                        <div class="flex-1 space-y-2 py-1 flex flex-col justify-center">
                            <div class="flex justify-between items-center">
                                <div class="skeleton-loader h-3 w-32"></div>
                                <div class="skeleton-loader h-4 w-16 rounded"></div>
                            </div>
                            <div class="skeleton-loader h-2.5 w-48"></div>
                            <div class="skeleton-loader h-2 w-36"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Data List --}}
        <div id="data-container" style="display:none;" class="space-y-1 pt-1">
            @if ($ajuanjadwal->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 px-6 text-center fade-up" style="animation-delay: 0.1s">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4" style="background: #f1f5f9;">
                        <ion-icon name="calendar-outline" class="text-3xl" style="color: #cbd5e1;"></ion-icon>
                    </div>
                    <h3 class="text-[14px] font-bold mb-1" style="color: #334155;">Tidak Ada Pengajuan</h3>
                    <p class="text-[12px] leading-relaxed max-w-[220px]" style="color: #94a3b8;">Belum ada sejarah pengajuan perubahan jadwal kerja yang tercatat.</p>
                </div>
            @else
                @foreach ($ajuanjadwal as $index => $d)
                    @php
                        $namahari = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
                        $day_eng = date('D', strtotime($d->tanggal));
                        $day_indo = isset($namahari[$day_eng]) ? $namahari[$day_eng] : $day_eng;
                        $day_short = strtoupper(substr($day_indo, 0, 3));
                        $tgl = date('d', strtotime($d->tanggal));
                        
                        // Status Colors (Matching Histori Presensi Logic)
                        // 'p' = Pending (Orange)
                        // 'a' = Approved (Primary Green)
                        // 'r' = Rejected (Red)
                        if ($d->status == 'p') {
                            $badgeColor = '#d97706'; // amber-600
                            $badgeBg = '#fef3c7'; // amber-100
                            $badgeBorder = '#fde68a'; // amber-200
                            $statusText = 'PENDING';
                            
                            $dateTextColor = '#d97706';
                            $dateBgColor = 'rgba(245, 158, 11, 0.1)';
                        } elseif ($d->status == 'a') {
                            $badgeColor = '#059669'; // emerald-600
                            $badgeBg = '#d1fae5'; // emerald-100
                            $badgeBorder = '#a7f3d0'; // emerald-200
                            $statusText = 'DISETUJUI';
                            
                            $dateTextColor = '#32745e'; // match modern theme
                            $dateBgColor = 'rgba(50, 116, 94, 0.1)';
                        } else {
                            $badgeColor = '#e11d48'; // rose-600
                            $badgeBg = '#ffe4e6'; // rose-100
                            $badgeBorder = '#fecdd3'; // rose-200
                            $statusText = 'DITOLAK';
                            
                            $dateTextColor = '#e11d48';
                            $dateBgColor = 'rgba(225, 29, 72, 0.1)';
                        }
                    @endphp

                    <div class="fade-up card press overflow-hidden {{ $d->status == 'p' ? 'delete-confirm-wrapper' : '' }}" 
                         style="border-color: {{ $d->status == 'p' ? '#fcd34d' : ($d->status == 'a' ? 'rgba(50, 116, 94, 0.3)' : '#fecdd3') }}; animation-delay: {{ $index * 0.04 }}s;">
                        
                        @if ($d->status == 'p')
                            <form method="POST" class="deleteform m-0 p-0" action="{{ route('ajuanjadwal.delete', Crypt::encrypt($d->id)) }}">
                                @csrf
                                @method('DELETE')
                        @endif

                        <div class="card-body p-1 flex items-center gap-2 cursor-pointer" onclick="{{ $d->status == 'p' ? '$(this).closest(\'form\').find(\'.delete-btn\').click()' : '' }}">
                            
                            {{-- Date Badge matching histori.blade.php exactly --}}
                            <div class="flex-shrink-0 w-[40px] h-[40px] flex flex-col items-center justify-center rounded-[8px]"
                                 style="background: {{ $dateBgColor }}; color: {{ $dateTextColor }};">
                                <span class="text-[9px] font-bold leading-none">{{ $day_short }}</span>
                                <span class="text-[14px] font-extrabold leading-tight mt-0.5">{{ $tgl }}</span>
                            </div>

                            {{-- Info Section --}}
                            <div class="flex-1 min-w-0 pr-1 m-0">
                                <div class="flex items-center justify-between mb-0.5">
                                    <h3 class="text-[13px] font-semibold truncate" style="color: #333;">
                                        {{ DateToIndo($d->tanggal) }}
                                    </h3>
                                    <span class="inline-flex items-center text-[9px] font-bold px-1.5 py-0.5 rounded border"
                                          style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; border-color: {{ $badgeBorder }};">
                                        {{ $statusText }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-1.5 text-[10px] font-medium mb-1" style="color: #555;">
                                    <span>{{ $d->jamKerjaAwal ? $d->jamKerjaAwal->nama_jam_kerja : 'Tanpa Jadwal' }}</span>
                                    <ion-icon name="arrow-forward-outline" class="text-slate-300"></ion-icon>
                                    <span class="text-[#32745e] font-bold">{{ $d->jamKerjaTujuan->nama_jam_kerja }}</span>
                                </div>
                                
                                <p class="text-[10px] leading-tight text-slate-500 text-truncate-2">
                                    "{{ $d->keterangan }}"
                                </p>
                            </div>
                        </div>

                        @if ($d->status == 'p')
                            <button type="submit" class="delete-btn hidden"></button>
                            </form>
                        @endif
                    </div>
                @endforeach
                
                <div class="mt-4 flex justify-center">
                    {{ $ajuanjadwal->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

        {{-- FAB strictly matching modern layout spacing --}}
        <div class="fab-button modern">
            <a href="{{ route('ajuanjadwal.create') }}" class="fab bg-[#32745e]">
                <ion-icon name="add-outline" class="text-2xl"></ion-icon>
            </a>
        </div>

    </div>
@endsection

@push('myscript')
    <script>
        $(document).ready(function() {
            // Smooth skeleton removal
            setTimeout(function() {
                $('#skeleton-container').fadeOut(200, function() {
                    $('#data-container').fadeIn(300);
                });
            }, 600);

            // Cancel Confirmation
            $(".delete-btn").click(function(e) {
                var form = $(this).closest("form");
                e.preventDefault();
                e.stopPropagation(); // Prevent triggering row click twice
                
                Swal.fire({
                    title: 'Batalkan Pengajuan?',
                    text: 'Pengajuan jadwal ini akan dihapus dan tidak dapat dikembalikan.',
                    imageUrl: 'https://cdn-icons-png.flaticon.com/512/3688/3688686.png',
                    imageWidth: 80,
                    imageHeight: 80,
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Tutup',
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl text-sm px-4 py-2',
                        cancelButton: 'rounded-xl text-sm px-4 py-2'
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
