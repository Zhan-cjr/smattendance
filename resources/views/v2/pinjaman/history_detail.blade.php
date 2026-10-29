@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $total = 0;
@endphp

<div class="space-y-4">
    <!-- Header Summary Card -->
    <div class="rounded-2xl p-4 sm:p-5 text-white shadow-2xs relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
        <div class="relative z-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100 block mb-0.5">Kode Generate</span>
                <h4 class="text-sm sm:text-base font-black text-white font-mono">{{ $history->kode_generate ?? 'BATCH-' . $history->id }}</h4>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100 block mb-0.5">Periode Pemotongan</span>
                <h4 class="text-sm sm:text-base font-black text-white">{{ config('global.nama_bulan')[(int)$history->bulan] }} {{ $history->tahun }}</h4>
            </div>
            <div class="sm:text-right">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-100 block mb-0.5">Diproses Oleh</span>
                <h4 class="text-xs sm:text-sm font-black text-white truncate">{{ $history->user?->name ?? 'System' }}</h4>
                <span class="text-[10.5px] text-emerald-100/90 block mt-0.5">{{ date('d M Y H:i', strtotime($history->created_at)) }}</span>
            </div>
        </div>
    </div>

    <!-- Table Detail Batch -->
    <div class="rounded-2xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto max-h-[380px] custom-scrollbar w-full" style="background-color: {{ $themeColor1 }};">
            <table class="w-full text-xs text-left border-0 border-collapse">
                <thead class="text-white font-bold uppercase tracking-wider text-[11px] sticky top-0 z-10 border-0 border-t-0 border-b border-black/15 whitespace-nowrap"
                       style="background-color: {{ $themeColor1 }};">
                    <tr class="border-0 border-t-0 text-white/90">
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">NIK</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Nama Karyawan</th>
                        <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">No. Pinjaman</th>
                        <th class="py-2.5 px-4 text-center text-white/90 border-0 border-t-0">Cicilan Ke</th>
                        <th class="py-2.5 px-4 text-right text-white/90 border-0 border-t-0">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($history->pembayaran_pinjaman as $pay)
                        @php $total += $pay->jumlah_bayar; @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-700">{{ $pay->pinjaman->nik }}</td>
                            <td class="py-2.5 px-4">
                                <span class="font-extrabold text-slate-900 block">{{ $pay->pinjaman->karyawan->nama_karyawan }}</span>
                                <span class="text-[11px] text-slate-400 block">{{ $pay->pinjaman->karyawan->jabatan->nama_jabatan ?? '-' }}</span>
                            </td>
                            <td class="py-2.5 px-4">
                                <span class="font-mono text-[11px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $pay->pinjaman->no_pinjaman }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                    #{{ $pay->rencana_cicilan ? $pay->rencana_cicilan->cicilan_ke : '-' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-extrabold text-emerald-700">
                                Rp {{ number_format($pay->jumlah_bayar, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/90 border-t-2 border-slate-200 font-bold sticky bottom-0 z-10">
                    <tr>
                        <td colspan="4" class="py-3 px-4 text-xs font-black text-slate-700 uppercase tracking-wider text-right">
                            Total Batch Pembayaran
                        </td>
                        <td class="py-3 px-4 text-right">
                            <span class="text-sm font-black text-emerald-700">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Footer Modal Action -->
    <div class="pt-3 flex justify-end">
        <button type="button" class="btnCloseModalHistoryDetail px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition active:scale-95 cursor-pointer">
            Tutup Detail
        </button>
    </div>
</div>
