@forelse ($detailharilibur as $d)
    <tr class="hover:bg-slate-50/80 transition-colors group data-row">
        <!-- No Index -->
        <td class="py-2.5 px-3.5 text-center text-slate-400 font-bold text-xs">
            <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                {{ $loop->iteration }}
            </span>
        </td>

        <!-- NIK -->
        <td class="py-2.5 px-4 whitespace-nowrap">
            <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 font-mono text-xs">
                {{ $d->nik_show ?? $d->nik }}
            </span>
        </td>

        <!-- Nama Karyawan -->
        <td class="py-2.5 px-4 whitespace-nowrap">
            <span class="font-bold text-slate-900 text-xs block">{{ formatName2($d->nama_karyawan) }}</span>
        </td>

        <!-- Departemen -->
        <td class="py-2.5 px-4 whitespace-nowrap">
            <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                {{ $d->kode_dept }}
            </span>
        </td>

        <!-- Action -->
        <td class="py-2.5 px-3.5 text-center">
            <button type="button" 
                    class="w-7 h-7 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200 inline-flex items-center justify-center transition active:scale-95 cursor-pointer delete-karyawan-libur shadow-2xs"
                    data-nik="{{ $d->nik }}" 
                    data-nama="{{ formatName2($d->nama_karyawan) }}"
                    title="Hapus Dari Hari Libur">
                <i class="ti ti-trash text-xs"></i>
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="py-10 text-center text-slate-400 bg-white">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg mb-2">
                <i class="ti ti-users-off"></i>
            </div>
            <p class="font-bold text-slate-700 text-xs">Belum ada karyawan yang ditambahkan</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol "Tambah / Pilih Karyawan" di atas untuk menambahkan personel.</p>
        </td>
    </tr>
@endforelse
