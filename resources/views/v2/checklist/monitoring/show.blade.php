@extends('v2.layouts.app')
@section('titlepage', 'Detail Laporan Checklist ' . $laporan->kode_laporan)
@section('page_title', 'Detail Laporan Checklist')

@section('content')
@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">
    <!-- Top Back Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.checklist.monitoring.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold shadow-2xs transition">
            <i class="ti ti-arrow-left"></i>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        <!-- Kolom Kiri: Metadata Laporan & Grid WA -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 text-white" style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-md bg-white/20 text-white text-[10.5px] font-bold uppercase">
                            {{ $laporan->tipe }} Toko
                        </span>
                        <span class="text-[11px] font-mono text-white/80">{{ $laporan->kode_laporan }}</span>
                    </div>
                    <h5 class="text-base font-extrabold mt-2">{{ $laporan->cabang->nama_cabang ?? $laporan->kode_cabang }}</h5>
                </div>
                <div class="p-4 divide-y divide-slate-100 text-xs">
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="text-slate-500">Petugas Pemeriksa</span>
                        <strong class="text-slate-800">{{ $laporan->karyawan->nama_karyawan ?? $laporan->nik }}</strong>
                    </div>
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="text-slate-500">Departemen</span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px]">
                            {{ $laporan->departemen->nama_dept ?? $laporan->kode_dept ?? 'Umum' }}
                        </span>
                    </div>
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="text-slate-500">NIK</span>
                        <span class="font-mono font-semibold text-slate-700">{{ $laporan->nik }}</span>
                    </div>
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="text-slate-500">Tanggal Selesai</span>
                        <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="text-slate-500">Jam Pemeriksaan</span>
                        <span class="font-bold text-slate-800">{{ date('H:i:s', strtotime($laporan->jam)) }} WIB</span>
                    </div>
                    <div class="py-2.5 flex items-center justify-between">
                        <span class="text-slate-500">Total Titik</span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-600 text-white font-bold text-[11px]">
                            {{ $laporan->total_checkpoint }} Titik Terverifikasi
                        </span>
                    </div>
                    @if($laporan->latitude && $laporan->longitude)
                        <div class="py-3">
                            <span class="text-slate-500 block mb-1.5">Koordinat GPS:</span>
                            <a href="https://maps.google.com/?q={{ $laporan->latitude }},{{ $laporan->longitude }}" target="_blank"
                               class="w-full py-2 px-3 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 font-bold text-center inline-flex items-center justify-center gap-1.5 transition">
                                <i class="ti ti-map-pin"></i>
                                <span>Lihat di Google Maps ({{ $laporan->latitude }}, {{ $laporan->longitude }})</span>
                            </a>
                        </div>
                    @endif
                    <div class="py-3">
                        <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-2.5">
                            <i class="ti ti-shield-check text-lg text-emerald-600 shrink-0 mt-0.5"></i>
                            <div>
                                <strong class="block text-[11px] font-bold text-emerald-900">Pernyataan Tanggung Jawab Ditandatangani</strong>
                                <span class="text-[10.5px] text-emerald-700 leading-relaxed block mt-0.5">
                                    Petugas menyatakan bertanggung jawab penuh atas kebenaran seluruh area yang diperiksa.
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($laporan->catatan)
                        <div class="py-3">
                            <span class="text-slate-500 block mb-1">Catatan Tambahan Petugas:</span>
                            <div class="p-2.5 bg-slate-50 rounded-lg border border-slate-200 text-slate-800 font-medium">
                                {{ $laporan->catatan }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Foto Gabungan Grid 4-in-1 (WhatsApp Ready) -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/60">
                    <h6 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="ti ti-layout-grid text-emerald-600"></i>
                        <span>Foto Grid 4-in-1 (Format WA)</span>
                    </h6>
                </div>
                <div class="p-3 space-y-2.5">
                    @foreach($laporan->collages as $col)
                        <div class="rounded-xl p-2.5 bg-slate-900 text-white space-y-2">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-bold">Grid Bagian #{{ $col->batch_no }} ({{ $col->total_items }} Titik)</span>
                                <a href="{{ asset('storage/' . $col->foto_collage) }}" download
                                   class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold inline-flex items-center gap-1">
                                    <i class="ti ti-download"></i> Download
                                </a>
                            </div>
                            <a href="{{ asset('storage/' . $col->foto_collage) }}" target="_blank" class="block">
                                <img src="{{ asset('storage/' . $col->foto_collage) }}" class="w-full rounded-lg">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Foto Satuan Tiap Checkpoint -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/60">
                    <h5 class="text-xs sm:text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="ti ti-photo text-emerald-600 text-base"></i>
                        <span>Detail Foto Tiap Checkpoint ({{ $laporan->details->count() }})</span>
                    </h5>
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10.5px] font-bold">
                        Semua Foto Telah Di-Watermark
                    </span>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-3.5">
                    @foreach($laporan->details as $idx => $det)
                        <div class="rounded-xl border border-slate-200 overflow-hidden flex flex-col bg-white shadow-2xs">
                            <div class="px-3.5 py-2.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-md bg-emerald-600 text-white text-[10px] font-extrabold inline-flex items-center justify-center">
                                        {{ $idx + 1 }}
                                    </span>
                                    <span>{{ $det->nama_checkpoint }}</span>
                                </span>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-600 text-white text-[10px] font-bold">OK</span>
                            </div>
                            <div class="bg-slate-900 relative" style="aspect-ratio: 4/3; overflow: hidden;">
                                <a href="{{ asset('storage/' . $det->foto) }}" target="_blank" title="Klik untuk perbesar">
                                    <img src="{{ asset('storage/' . $det->foto) }}" alt="{{ $det->nama_checkpoint }}"
                                         class="w-full h-full object-cover hover:scale-105 transition duration-200">
                                </a>
                            </div>
                            @if($det->deskripsi || $det->catatan)
                                <div class="p-3 space-y-1.5 text-xs">
                                    @if($det->deskripsi)
                                        <p class="text-[11px] text-slate-500">
                                            <i class="ti ti-info-circle mr-1"></i>{{ $det->deskripsi }}
                                        </p>
                                    @endif
                                    @if($det->catatan)
                                        <div class="p-2 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-[11px]">
                                            <strong>Catatan:</strong> {{ $det->catatan }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

