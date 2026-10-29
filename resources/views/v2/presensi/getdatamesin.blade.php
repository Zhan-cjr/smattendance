<div class="space-y-5">
    
    <!-- Table 1: Cloud Attlog (Fingerspot IO) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs">
        <div class="px-5 py-3.5 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-cloud-download text-emerald-400 text-base"></i>
                <span class="text-xs font-black uppercase tracking-wider">Log Mesin Fingerprint (Cloud API)</span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-white/10 text-slate-300 border border-white/20">
                {{ count($filtered_array) }} Log Ditemukan
            </span>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[550px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                        <th class="py-3 px-4 w-28">PIN</th>
                        <th class="py-3 px-4 w-36">STATUS SCAN</th>
                        <th class="py-3 px-4">WAKTU SCAN</th>
                        <th class="py-3 px-4 text-center w-36">AKSI AMBIL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse ($filtered_array as $d)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                {{ $d->pin }}
                            </td>
                            <td class="py-3 px-4">
                                @if($d->status_scan % 2 == 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-black tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ti ti-login text-xs"></i> IN ({{ $d->status_scan }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-black tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="ti ti-logout text-xs"></i> OUT ({{ $d->status_scan }})
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ date('d M Y H:i:s', strtotime($d->scan_date)) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if(isset($is_locked) && $is_locked)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-rose-600 bg-rose-50 text-[11px] font-bold">
                                        <i class="ti ti-lock"></i> Terkunci
                                    </span>
                                @else
                                    <div class="inline-flex items-center gap-1">
                                        <form method="POST" class="m-0" action="{{ route('presensi.updatefrommachine', [Crypt::encrypt($d->pin), 0]) }}">
                                            @csrf
                                            <input type="hidden" name="scan_date" value="{{ date('Y-m-d H:i:s', strtotime($d->scan_date)) }}">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1 active:scale-95 cursor-pointer" title="Ambil sebagai Jam Masuk">
                                                <i class="ti ti-login"></i> Masuk
                                            </button>
                                        </form>
                                        <form method="POST" class="m-0" action="{{ route('presensi.updatefrommachine', [Crypt::encrypt($d->pin), 1]) }}">
                                            @csrf
                                            <input type="hidden" name="scan_date" value="{{ date('Y-m-d H:i:s', strtotime($d->scan_date)) }}">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1 active:scale-95 cursor-pointer" title="Ambil sebagai Jam Pulang">
                                                <i class="ti ti-logout"></i> Pulang
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400">
                                <i class="ti ti-cloud-off text-2xl mb-1 block"></i>
                                <span class="font-medium text-xs">Tidak ada log presensi baru di cloud server mesin.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Table 2: Local Server Log History -->
    <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs">
        <div class="px-5 py-3.5 bg-slate-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-server-cog text-sky-400 text-base"></i>
                <span class="text-xs font-black uppercase tracking-wider">Histori Sinkronisasi Mesin (Lokal Database)</span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-white/10 text-slate-300 border border-white/20">
                {{ count($log_lokal) }} Riwayat
            </span>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-600 text-[11px] font-black uppercase tracking-wider">
                        <th class="py-3 px-4">IDENTITAS MESIN</th>
                        <th class="py-3 px-4 w-32">STATUS SCAN</th>
                        <th class="py-3 px-4 w-40">WAKTU ABSEN</th>
                        <th class="py-3 px-4 w-28">STATUS</th>
                        <th class="py-3 px-4">KETERANGAN</th>
                        <th class="py-3 px-4 text-center w-36">AKSI AMBIL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse ($log_lokal as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-4">
                                @if($log->nama_mesin)
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center font-bold shrink-0">
                                            <i class="ti ti-fingerprint text-base"></i>
                                        </div>
                                        <div>
                                            <h6 class="font-bold text-slate-900 text-xs mb-0">{{ $log->nama_mesin }}</h6>
                                            <span class="text-[10.5px] font-mono text-slate-400">SN: {{ $log->sn }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">{{ $log->id_mesin ?? 'Tidak Teridentifikasi' }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if(is_numeric($log->status_scan))
                                    @if($log->status_scan % 2 == 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="ti ti-login"></i> IN ({{ $log->status_scan }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                            <i class="ti ti-logout"></i> OUT ({{ $log->status_scan }})
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold text-[11px]">{{ $log->status_scan }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ date('d M Y', strtotime($log->jam_absen)) }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ date('H:i:s', strtotime($log->jam_absen)) }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($log->status == 1)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10.5px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="ti ti-check"></i> Sukses
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10.5px] font-black bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="ti ti-x"></i> Gagal
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-slate-500 text-[11.5px] block max-w-xs truncate" title="{{ $log->keterangan }}">
                                    {{ $log->keterangan ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if(isset($is_locked) && $is_locked)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-rose-600 bg-rose-50 text-[11px] font-bold">
                                        <i class="ti ti-lock"></i> Terkunci
                                    </span>
                                @else
                                    <div class="inline-flex items-center gap-1">
                                        <form method="POST" class="m-0" action="{{ route('presensi.updatefrommachine', [Crypt::encrypt($log->pin), 0]) }}">
                                            @csrf
                                            <input type="hidden" name="scan_date" value="{{ date('Y-m-d H:i:s', strtotime($log->jam_absen)) }}">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1 active:scale-95 cursor-pointer" title="Set sebagai Jam Masuk">
                                                <i class="ti ti-login"></i> Masuk
                                            </button>
                                        </form>
                                        <form method="POST" class="m-0" action="{{ route('presensi.updatefrommachine', [Crypt::encrypt($log->pin), 1]) }}">
                                            @csrf
                                            <input type="hidden" name="scan_date" value="{{ date('Y-m-d H:i:s', strtotime($log->jam_absen)) }}">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] shadow-2xs transition flex items-center gap-1 active:scale-95 cursor-pointer" title="Set sebagai Jam Pulang">
                                                <i class="ti ti-logout"></i> Pulang
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                <i class="ti ti-database-off text-2xl mb-1 block"></i>
                                <span class="font-medium text-xs">Belum ada riwayat sinkronisasi log mesin pada server lokal.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
