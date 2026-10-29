@extends('v2.layouts.app')

@section('titlepage', $periode->nama_periode)
@section('page_title', $periode->nama_periode)

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $bulanStr = \Carbon\Carbon::create($periode->tahun, $periode->bulan, 1)->translatedFormat('F Y');
@endphp

<div class="space-y-4">

    <!-- ================= 1. TOP HEADER & BREADCRUMB ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-1">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('jadwalperiode.index') }}" 
                   class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition active:scale-95 shadow-2xs">
                    <i class="ti ti-arrow-left text-sm"></i>
                </a>
                <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>{{ $periode->nama_periode }}</span>
                </h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Review matriks jadwal kerja bulanan, sesuaikan alokasi shift per sel karyawan x tanggal, lalu lakukan publish ke sistem.
            </p>
        </div>

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center text-xs text-slate-400 font-medium">
            <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                <i class="ti ti-home text-sm"></i>
                <span>Dashboard</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <a href="{{ route('jadwalperiode.index') }}" class="hover:text-slate-700 transition">
                <span>Periode Jadwal</span>
            </a>
            <span class="mx-2 text-slate-300">/</span>
            <span class="font-bold text-slate-800">Matriks Roster</span>
        </nav>
    </div>

    <!-- ================= 2. STATUS & ACTION CARD BAR ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Left Info & Status -->
            <div class="flex items-start sm:items-center gap-3.5 min-w-0">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0 text-white shadow-2xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-calendar-event"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center flex-wrap gap-2 mb-1">
                        <span class="text-sm font-black text-slate-900 tracking-tight">
                            {{ $bulanStr }}
                        </span>
                        
                        <!-- Status Badge -->
                        @if ($periode->status === 'draft')
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs inline-flex items-center gap-1">
                                <i class="ti ti-pencil text-[12px]"></i> STATUS: DRAFT
                            </span>
                        @elseif ($periode->status === 'published')
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs inline-flex items-center gap-1">
                                <i class="ti ti-check text-[12px]"></i> STATUS: PUBLISHED (AKTIF)
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-slate-100 text-slate-600 border border-slate-200 shadow-2xs inline-flex items-center gap-1">
                                <i class="ti ti-archive text-[12px]"></i> STATUS: ARCHIVED
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center flex-wrap gap-1.5 text-xs text-slate-500">
                        @if($periode->grup)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-100 font-bold text-[11px]">
                                <i class="ti ti-users text-emerald-600"></i>
                                <span>{{ $periode->grup->nama_grup }}</span>
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">
                            <i class="ti ti-building text-slate-400"></i>
                            <span>{{ $periode->departemen->nama_dept ?? 'Semua Dept' }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">
                            <i class="ti ti-map-pin text-slate-400"></i>
                            <span>{{ $periode->cabang->nama_cabang ?? 'Semua Cabang' }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <a href="{{ route('jadwalperiode.index') }}" 
                   class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition inline-flex items-center gap-1.5 active:scale-95 shadow-2xs">
                    <i class="ti ti-arrow-left text-sm"></i>
                    <span>Kembali</span>
                </a>

                @if ($periode->status === 'draft')
                    <form action="{{ route('jadwalperiode.publish', Crypt::encrypt($periode->id)) }}" method="POST" id="formPublish" class="m-0 inline">
                        @csrf
                        <button type="button" 
                                class="btn-publish px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-1.5 active:scale-95 cursor-pointer">
                            <i class="ti ti-send text-sm"></i>
                            <span>Publish Jadwal ke Sistem</span>
                        </button>
                    </form>
                @elseif ($periode->status === 'published')
                    <form action="{{ route('jadwalperiode.archive', Crypt::encrypt($periode->id)) }}" method="POST" id="formArchive" class="m-0 inline">
                        @csrf
                        <button type="button" 
                                class="btn-archive px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition inline-flex items-center gap-1.5 active:scale-95 cursor-pointer">
                            <i class="ti ti-archive text-sm"></i>
                            <span>Arsipkan Jadwal</span>
                        </button>
                    </form>
                @endif
            </div>

        </div>

        <!-- Understaffed Alert Warning -->
        @if (!empty($understaffedDays))
            <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5 text-amber-900">
                <i class="ti ti-alert-triangle text-lg text-amber-600 shrink-0 mt-0.5"></i>
                <div class="text-xs">
                    <span class="font-bold">Peringatan Minimum Personel:</span>
                    <span> Ditemukan {{ count($understaffedDays) }} shift yang kekurangan jumlah staf minimum (misal: </span>
                    @foreach (array_slice($understaffedDays, 0, 3) as $alert)
                        <span class="px-1.5 py-0.5 rounded bg-rose-100 text-rose-800 font-mono font-bold text-[10px] mx-0.5">
                            Tgl {{ $alert['hari'] }} {{ $alert['shift'] }}: {{ $alert['actual'] }}/{{ $alert['required'] }} org
                        </span>
                    @endforeach
                    @if(count($understaffedDays) > 3) <span class="font-semibold text-slate-500">... dan lainnya</span> @endif).
                </div>
            </div>
        @endif
    </div>

    <!-- ================= 3. QUICK SHIFT PALETTE TOOLBAR ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-3 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-xs font-bold text-slate-700 flex items-center gap-1 mr-1">
                    <i class="ti ti-palette text-emerald-600"></i>
                    <span>Pilih Shift Cepat:</span>
                </span>

                <!-- OFF / Libur Button -->
                <button type="button" 
                        class="shift-palette-btn active px-3 py-1.5 rounded-xl text-xs font-bold border transition inline-flex items-center gap-1.5 cursor-pointer active:scale-95 bg-slate-800 text-white border-slate-800 shadow-xs" 
                        data-code="" 
                        data-name="OFF" 
                        data-color="#64748b">
                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                    <span>OFF (Libur)</span>
                </button>

                <!-- Dynamic Jam Kerja List -->
                @foreach ($allJamKerja as $jk)
                    <button type="button" 
                            class="shift-palette-btn px-3 py-1.5 rounded-xl text-xs font-bold border border-slate-200 text-slate-700 bg-slate-50 hover:bg-slate-100 transition inline-flex items-center gap-1.5 cursor-pointer active:scale-95" 
                            data-code="{{ $jk->kode_jam_kerja }}" 
                            data-color="{{ $jk->color ?? '#007bff' }}" 
                            data-name="{{ $jk->nama_jam_kerja }}">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $jk->color ?? '#007bff' }};"></span>
                        <span>{{ $jk->nama_jam_kerja }}</span>
                        <span class="text-[10px] text-slate-400 font-normal">({{ substr($jk->jam_masuk, 0, 5) }}-{{ substr($jk->jam_pulang, 0, 5) }})</span>
                    </button>
                @endforeach
            </div>

            <div class="text-[11px] text-slate-400 flex items-center gap-1 shrink-0">
                <i class="ti ti-mouse text-slate-500"></i>
                <span>Klik sel karyawan untuk assign shift terpilih</span>
            </div>
        </div>
    </div>

    <!-- ================= 4. ROSTER MATRIX TABLE ================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="table-responsive" style="max-height: 650px; overflow: auto;">
            <table class="w-full text-center border-collapse roster-matrix-table" style="font-size: 11px;">
                
                <!-- THEAD -->
                <thead class="sticky top-0 z-20 bg-slate-800 text-white shadow-xs">
                    <tr>
                        <!-- Sticky Karyawan Column Header -->
                        <th class="sticky left-0 z-30 bg-slate-900 text-slate-100 font-bold px-3.5 py-3 text-left border-r border-slate-700" 
                            style="min-width: 200px; width: 200px; max-width: 200px;">
                            <div class="flex items-center gap-1.5">
                                <i class="ti ti-users text-emerald-400"></i>
                                <span>Karyawan</span>
                            </div>
                        </th>

                        <!-- Columns 1..N Day of Month -->
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dateObj = \Carbon\Carbon::create($periode->tahun, $periode->bulan, $d);
                                $isSunday = $dateObj->isSunday();
                                $dayName = $dateObj->translatedFormat('D');
                            @endphp
                            <th class="p-1 border-r border-slate-700 font-medium {{ $isSunday ? 'bg-rose-950 text-rose-200' : '' }}" 
                                style="min-width: 44px; width: 44px;">
                                <div class="text-[9px] uppercase tracking-wider opacity-75 leading-tight">{{ $dayName }}</div>
                                <div class="text-xs font-black">{{ $d }}</div>
                            </th>
                        @endfor

                        <!-- Summary Column -->
                        <th class="px-3 py-3 font-bold bg-slate-900 text-emerald-400 border-l border-slate-700" style="min-width: 65px; width: 65px;">
                            Total
                        </th>
                    </tr>
                </thead>

                <!-- TBODY -->
                <tbody class="divide-y divide-slate-200">
                    @forelse ($matrix as $nik => $row)
                        @php
                            $karyawan = $row['karyawan'];
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            
                            <!-- Sticky Karyawan Info Column -->
                            <td class="sticky left-0 z-10 bg-white hover:bg-slate-50 text-left px-3 py-2 border-r border-slate-200 shadow-sm" 
                                style="min-width: 200px; width: 200px; max-width: 200px;">
                                <div class="flex items-center gap-2 min-w-0">
                                    <!-- Avatar -->
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-700 overflow-hidden shrink-0 border border-slate-200">
                                        @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                                            <img src="{{ url('/storage/karyawan/' . $karyawan->foto) }}" alt="{{ $karyawan->nama_karyawan }}" class="w-full h-full object-cover">
                                        @else
                                            <span>{{ strtoupper(substr($karyawan->nama_karyawan ?? $nik, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-900 truncate text-xs leading-tight" title="{{ $karyawan->nama_karyawan ?? $nik }}">
                                            {{ $karyawan->nama_karyawan ?? $nik }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono truncate leading-none mt-0.5">
                                            {{ $nik }} &bull; {{ $karyawan->jabatan->nama_jabatan ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Day Matrix Cells -->
                            @for ($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $cell = $row['schedule'][$d] ?? null;
                                    $jk = $cell ? $cell['jamKerja'] : null;
                                    $targetDateStr = \Carbon\Carbon::create($periode->tahun, $periode->bulan, $d)->format('Y-m-d');
                                    $dateObj = \Carbon\Carbon::create($periode->tahun, $periode->bulan, $d);
                                    $isSunday = $dateObj->isSunday();
                                    
                                    $shiftColor = $jk->color ?? ($jk ? '#007bff' : null);
                                    $shiftName = $jk ? $jk->nama_jam_kerja : 'OFF';
                                @endphp
                                <td class="p-0.5 border-r border-slate-200 roster-cell relative select-none transition-transform" 
                                    data-nik="{{ $nik }}" 
                                    data-tanggal="{{ $targetDateStr }}"
                                    data-current-jk="{{ $jk ? $jk->kode_jam_kerja : '' }}"
                                    style="cursor: pointer;"
                                    title="{{ $karyawan->nama_karyawan ?? $nik }} (Tgl {{ $d }}): {{ $shiftName }}">
                                    
                                    <div class="cell-inner w-full h-8 rounded-md flex items-center justify-center font-bold text-[10px] transition-all duration-150"
                                         style="{{ $jk ? 'background-color: ' . $shiftColor . '; color: #ffffff;' : 'background-color: ' . ($isSunday ? '#fee2e2' : '#f1f5f9') . '; color: ' . ($isSunday ? '#b91c1c' : '#64748b') . ';' }}">
                                        <span class="truncate px-0.5">{{ $shiftName }}</span>
                                    </div>
                                </td>
                            @endfor

                            <!-- Summary Count Column -->
                            <td class="px-2 py-2 font-bold text-center border-l border-slate-200">
                                <span class="px-2 py-1 rounded-md text-[11px] font-mono font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 inline-block total-kerja-badge">
                                    {{ $row['stats']['total_kerja'] }}H
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $daysInMonth + 2 }}" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-2xl mx-auto mb-2 text-slate-400">
                                    <i class="ti ti-users-minus"></i>
                                </div>
                                <div class="font-bold text-xs text-slate-600">Tidak ada data karyawan pada periode ini</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Pastikan cakupan departemen/cabang/grup memiliki karyawan aktif</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
.roster-cell:hover .cell-inner {
    transform: scale(0.92);
    box-shadow: 0 0 0 2px #0a5c41;
}
.roster-cell:active .cell-inner {
    transform: scale(0.85);
}
</style>

@endsection

@push('myscript')
<script>
$(document).ready(function() {
    let selectedPaletteCode = '';
    let selectedPaletteName = 'OFF';
    let selectedPaletteColor = '#64748b';

    // Shift Palette Selection
    $('.shift-palette-btn').click(function() {
        $('.shift-palette-btn').removeClass('active bg-slate-800 text-white border-slate-800 shadow-xs')
            .addClass('border-slate-200 text-slate-700 bg-slate-50 hover:bg-slate-100');
        
        $(this).removeClass('border-slate-200 text-slate-700 bg-slate-50 hover:bg-slate-100')
            .addClass('active bg-slate-800 text-white border-slate-800 shadow-xs');

        selectedPaletteCode = $(this).data('code') || '';
        selectedPaletteName = $(this).data('name') || 'OFF';
        selectedPaletteColor = $(this).data('color') || '#64748b';
    });

    // Handle Click on Roster Cell to update shift
    $('.roster-cell').click(function() {
        let cell = $(this);
        let nik = cell.data('nik');
        let tanggal = cell.data('tanggal');
        let currentJk = cell.data('current-jk');

        // Check if no change
        if (currentJk === selectedPaletteCode) {
            return;
        }

        // Ganti visual langsung (optimistic UI)
        let innerDiv = cell.find('.cell-inner');
        if (selectedPaletteCode === '') {
            innerDiv.css({
                'background-color': '#f1f5f9',
                'color': '#64748b'
            }).html('<span class="truncate px-0.5">OFF</span>');
        } else {
            innerDiv.css({
                'background-color': selectedPaletteColor,
                'color': '#ffffff'
            }).html(`<span class="truncate px-0.5">${selectedPaletteName}</span>`);
        }
        
        cell.data('current-jk', selectedPaletteCode);

        // Recalculate row total
        let tr = cell.closest('tr');
        let totalKerja = 0;
        tr.find('.roster-cell').each(function() {
            if ($(this).data('current-jk') !== '') {
                totalKerja++;
            }
        });
        tr.find('.total-kerja-badge').text(totalKerja + 'H');

        // Kirim request AJAX update cell
        $.ajax({
            url: "{{ route('jadwalperiode.updateCell', Crypt::encrypt($periode->id)) }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                nik: nik,
                tanggal: tanggal,
                kode_jam_kerja: selectedPaletteCode
            },
            success: function(res) {
                // Success - cell updated
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Update Sel',
                    text: xhr.responseJSON ? xhr.responseJSON.error : 'Terjadi kesalahan saat menyimpan jadwal.',
                    customClass: { popup: 'rounded-2xl shadow-xl' }
                });
            }
        });
    });

    // Publish Confirmation
    $('.btn-publish').click(function() {
        Swal.fire({
            title: 'Publish Jadwal ke Sistem?',
            text: "Jadwal ini akan langsung disinkronkan ke kalender presensi harian seluruh karyawan pada periode ini.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Publish Sekarang!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl shadow-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formPublish').submit();
            }
        });
    });

    // Archive Confirmation
    $('.btn-archive').click(function() {
        Swal.fire({
            title: 'Arsipkan Jadwal?',
            text: "Jadwal yang diarsipkan tidak dapat diubah lagi secara instan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#64748b',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Arsipkan',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl shadow-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formArchive').submit();
            }
        });
    });
});
</script>
@endpush
