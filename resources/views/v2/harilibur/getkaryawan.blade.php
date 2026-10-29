@forelse ($karyawan as $d)
    <tr class="hover:bg-slate-50/80 transition-colors group">
        <!-- No Index -->
        <td class="py-2.5 px-3 text-center text-slate-400 font-bold text-xs">
            <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                {{ $loop->iteration }}
            </span>
        </td>

        <!-- NIK -->
        <td class="py-2.5 px-3 whitespace-nowrap">
            <span class="inline-flex items-center gap-1 font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 font-mono text-xs">
                {{ $d->nik_show ?? $d->nik }}
            </span>
        </td>

        <!-- Nama Karyawan -->
        <td class="py-2.5 px-3 whitespace-nowrap">
            <span class="font-bold text-slate-900 text-xs block">{{ formatName2($d->nama_karyawan) }}</span>
        </td>

        <!-- Departemen -->
        <td class="py-2.5 px-3 whitespace-nowrap">
            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                {{ $d->nama_dept }}
            </span>
        </td>

        <!-- Status Button (Add / Remove) -->
        <td class="py-2.5 px-3 text-center">
            @if (empty($d->ceklibur))
                <button type="button" 
                        nik="{{ $d->nik }}" 
                        class="updateLibur inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition active:scale-95 cursor-pointer shadow-2xs"
                        title="Tambahkan Libur">
                    <i class="ti ti-plus text-xs"></i>
                    <span>Liburkan</span>
                </button>
            @else
                <button type="button" 
                        nik="{{ $d->nik }}" 
                        class="updateLibur inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition active:scale-95 cursor-pointer shadow-2xs"
                        title="Batalkan Libur">
                    <i class="ti ti-circle-minus text-xs"></i>
                    <span>Diliburkan</span>
                </button>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="py-10 text-center text-slate-400 bg-white">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg mb-2">
                <i class="ti ti-users-off"></i>
            </div>
            <p class="font-bold text-slate-700 text-xs">Tidak ada karyawan ditemukan</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Coba sesuaikan filter departemen atau pencarian nama.</p>
        </td>
    </tr>
@endforelse
