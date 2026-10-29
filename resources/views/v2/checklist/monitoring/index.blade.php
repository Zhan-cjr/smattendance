@extends('v2.layouts.app')
@section('titlepage', 'Monitoring Laporan Opening & Closing')
@section('page_title', 'Monitoring Checklist Toko')

@section('content')
@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<div class="space-y-4">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="ti ti-report-analytics text-xl" style="color: {{ $themeColor1 }};"></i>
                <span>Laporan Buka & Tutup Toko</span>
            </h1>
            <p class="text-[11px] text-slate-500 mt-0.5">
                Pantau kedisiplinan pemeriksaan keamanan fisik, anti-penyusup, dan inventaris toko seluruh cabang
            </p>
        </div>
        <div>
            <a href="{{ route('admin.checklist.checkpoints.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold shadow-2xs transition">
                <i class="ti ti-settings text-sm text-emerald-600"></i>
                <span>Master Checkpoint</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-4">
        <form action="{{ route('admin.checklist.monitoring.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2.5">
            <div>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600"
                       placeholder="Tanggal Mulai">
            </div>
            <div>
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                       class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600"
                       placeholder="Tanggal Akhir">
            </div>
            <div>
                <select name="kode_cabang" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangs as $cb)
                        <option value="{{ $cb->kode_cabang }}" {{ request('kode_cabang') == $cb->kode_cabang ? 'selected' : '' }}>
                            {{ $cb->nama_cabang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="kode_dept" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    <option value="">Semua Departemen</option>
                    @foreach($departemens as $dp)
                        <option value="{{ $dp->kode_dept }}" {{ request('kode_dept') == $dp->kode_dept ? 'selected' : '' }}>
                            {{ $dp->nama_dept }} ({{ $dp->kode_dept }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="tipe" class="w-full h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                    <option value="">Semua Tipe</option>
                    <option value="opening" {{ request('tipe') == 'opening' ? 'selected' : '' }}>Opening Toko</option>
                    <option value="closing" {{ request('tipe') == 'closing' ? 'selected' : '' }}>Closing Toko</option>
                </select>
            </div>
            <div class="flex gap-1.5">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari Petugas/Kode"
                       class="flex-1 h-10 px-3 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-emerald-600">
                <button type="submit"
                        class="w-10 h-10 rounded-xl text-white flex items-center justify-center shadow-2xs cursor-pointer shrink-0"
                        style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 w-12">No</th>
                        <th class="px-4 py-3">Kode & Waktu Submit</th>
                        <th class="px-4 py-3">Cabang & Departemen</th>
                        <th class="px-4 py-3">Petugas</th>
                        <th class="px-4 py-3 w-28">Sesi</th>
                        <th class="px-4 py-3 w-36">Pemeriksaan</th>
                        <th class="px-4 py-3 w-24">Grid WA</th>
                        <th class="px-4 py-3 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($laporans as $idx => $lap)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-semibold text-slate-500">{{ $laporans->firstItem() + $idx }}</td>
                            <td class="px-4 py-3">
                                <div class="font-bold font-mono text-emerald-700">{{ $lap->kode_laporan }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    <i class="ti ti-calendar mr-1"></i>{{ \Carbon\Carbon::parse($lap->tanggal)->translatedFormat('d M Y') }} &bull; {{ date('H:i', strtotime($lap->jam)) }} WIB
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $lap->cabang->nama_cabang ?? $lap->kode_cabang }}</div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10.5px] font-bold mt-0.5">
                                    <i class="ti ti-tag"></i>{{ $lap->departemen->nama_dept ?? $lap->kode_dept ?? 'Umum' }}
                                </span>
                                @if($lap->lokasi_nama)
                                    <div class="text-[10.5px] text-slate-400 mt-0.5">{{ $lap->lokasi_nama }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $lap->karyawan->nama_karyawan ?? $lap->nik }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">NIK: {{ $lap->nik }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($lap->tipe == 'opening')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-600 text-white text-[11px] font-bold">
                                        <i class="ti ti-door-enter"></i>Opening
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-800 text-white text-[11px] font-bold">
                                        <i class="ti ti-lock"></i>Closing
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold">
                                    <i class="ti ti-check"></i>{{ $lap->total_checkpoint }} Titik OK
                                </span>
                                @if($lap->disclaimer_agreed)
                                    <div class="text-[10.5px] text-emerald-600 font-semibold mt-0.5">
                                        <i class="ti ti-shield-check"></i> Pernyataan Sah
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($lap->collages->isNotEmpty())
                                    <a href="{{ asset('storage/' . $lap->collages->first()->foto_collage) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $lap->collages->first()->foto_collage) }}"
                                             class="w-14 h-14 rounded-lg object-cover border border-slate-200 shadow-2xs">
                                    </a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.checklist.monitoring.show', $lap->id) }}"
                                       class="w-8 h-8 rounded-lg bg-sky-50 hover:bg-sky-600 text-sky-600 hover:text-white border border-sky-200 inline-flex items-center justify-center transition"
                                       title="Lihat Detail & Foto">
                                        <i class="ti ti-eye text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.checklist.monitoring.delete', $lap->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini beserta semua fotonya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 inline-flex items-center justify-center transition cursor-pointer"
                                                title="Hapus Laporan">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-slate-400">
                                <i class="ti ti-files-off text-3xl block mb-1.5 opacity-50"></i>
                                <span class="text-xs font-semibold">Tidak ada laporan checklist ditemukan untuk filter ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-100 v2-pagination">
            {{ $laporans->links() }}
        </div>
    </div>
</div>
@endsection

