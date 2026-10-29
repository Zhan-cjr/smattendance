@php
    $general_setting = \App\Models\Pengaturanumum::first();
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<!-- Search Bar & Filter Header -->
<div class="mb-4 flex flex-col sm:flex-row items-center justify-between gap-3">
    <div class="relative w-full sm:w-96">
        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
        <input type="text" 
               id="searchKaryawanNama" 
               class="w-full bg-slate-50 hover:bg-white focus:bg-white text-xs text-slate-800 placeholder-slate-400 pl-10 pr-4 py-2.5 rounded-xl border border-slate-200/90 focus:border-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-100 transition"
               placeholder="Cari berdasarkan NIK atau Nama Karyawan...">
    </div>
    
    <div class="flex items-center gap-2 self-end sm:self-auto text-xs text-slate-500 font-semibold bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200/80">
        <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
        <span>Total: <strong class="text-slate-800">{{ count($karyawanList) }}</strong> Orang</span>
    </div>
</div>

<!-- Table Card Container -->
<div class="border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs bg-white">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-700">
            <!-- Table Header with Solid Theme Gradient -->
            <thead class="text-white font-bold uppercase tracking-wider text-[11px]" 
                   style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <tr>
                    <th scope="col" class="py-3.5 px-4 text-center w-12 text-white/90">No</th>
                    <th scope="col" class="py-3.5 px-4 text-center w-16 text-white/90">Foto</th>
                    <th scope="col" class="py-3.5 px-4 w-28 text-white/90">NIK</th>
                    <th scope="col" class="py-3.5 px-4 text-white/90">Nama Karyawan</th>
                    <th scope="col" class="py-3.5 px-4 text-white/90">Jabatan & Penempatan</th>
                    @if($status == 'h')
                        <th scope="col" class="py-3.5 px-4 text-center text-white/90">Jam Masuk</th>
                        <th scope="col" class="py-3.5 px-4 text-center text-white/90">Jam Pulang</th>
                    @else
                        <th scope="col" class="py-3.5 px-4 text-white/90">Keterangan</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 font-medium">
                @forelse($karyawanList as $k)
                    <tr class="hover:bg-slate-50/80 transition-colors duration-150 group">
                        <td class="py-3 px-4 text-center text-slate-400 font-bold font-mono">
                            {{ $loop->iteration }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="w-9 h-9 rounded-full overflow-hidden border border-slate-200 shadow-2xs mx-auto bg-slate-100 flex items-center justify-center">
                                @if (!empty($k->foto) && Storage::disk('public')->exists('/karyawan/' . $k->foto))
                                    <img src="{{ getfotoKaryawan($k->foto) }}" alt="{{ $k->nama_karyawan }}" class="w-full h-full object-cover">
                                @else
                                    <span class="font-bold text-slate-600 text-xs">{{ strtoupper(substr($k->nama_karyawan, 0, 1)) }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-mono font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-[11px] border border-slate-200">
                                {{ $k->nik }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-slate-900 text-[13px] leading-tight">{{ $k->nama_karyawan }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-semibold text-slate-800">{{ $k->nama_jabatan ?? '-' }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                {{ $k->nama_dept ?? '-' }} <span class="text-slate-300">•</span> {{ $k->nama_cabang ?? '-' }}
                            </div>
                        </td>
                        @if($status == 'h')
                            <td class="py-3 px-4 text-center">
                                @if($k->jam_in)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                        <i data-lucide="log-in" class="w-3 h-3 text-emerald-600"></i>
                                        {{ $k->jam_in }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                                @if($k->jam_masuk)
                                    <div class="text-[10px] text-slate-400 mt-1 font-mono">Jadwal: {{ $k->jam_masuk }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($k->jam_out)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                                        <i data-lucide="log-out" class="w-3 h-3 text-rose-600"></i>
                                        {{ $k->jam_out }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-500">
                                        Belum Pulang
                                    </span>
                                @endif
                                @if($k->jam_pulang)
                                    <div class="text-[10px] text-slate-400 mt-1 font-mono">Jadwal: {{ $k->jam_pulang }}</div>
                                @endif
                            </td>
                        @else
                            <td class="py-3 px-4">
                                @if($k->keterangan)
                                    <div class="p-2 bg-slate-50 rounded-lg border border-slate-100 text-slate-700 text-xs">
                                        {{ $k->keterangan }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Tidak ada keterangan</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $status == 'h' ? '7' : '6' }}" class="py-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="user-x" class="w-6 h-6"></i>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700 mb-0.5">Tidak Ada Data Karyawan</h4>
                            <p class="text-[11px] text-slate-400">Tidak ada karyawan yang terdata untuk status kehadiran ini.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
