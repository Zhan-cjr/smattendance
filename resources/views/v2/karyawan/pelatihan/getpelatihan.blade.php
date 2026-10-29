<div class="overflow-x-auto w-full" style="background-color: var(--theme-color-1, #0a5c41);">
    <table class="w-full text-left text-xs sm:text-sm border-0 border-collapse">
        <!-- Seamless Solid / Matching Table Header -->
        <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
               style="background-color: var(--theme-color-1, #0a5c41);">
            <tr class="border-0 border-t-0 text-white/90">
                <th class="py-2.5 px-4 text-center w-14 text-white/90 border-0 border-t-0">No</th>
                <th class="py-2.5 px-4 w-40 text-white/90 border-0 border-t-0">Tanggal</th>
                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Nama Pelatihan</th>
                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Penyelenggara</th>
                <th class="py-2.5 px-4 text-center w-28 text-white/90 border-0 border-t-0">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700 font-medium bg-white">
            @if ($pelatihan->isEmpty())
                <tr>
                    <td colspan="5" class="text-center py-12 px-4 text-slate-400">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-2.5">
                            <i class="ti ti-school-off"></i>
                        </div>
                        <p class="font-bold text-slate-700 text-sm">Belum Ada Data Pelatihan</p>
                        <p class="text-xs text-slate-400 mt-1">Klik tombol <span class="font-semibold text-slate-600">"+ Tambah Pelatihan"</span> di atas untuk menambahkan riwayat pelatihan baru.</p>
                    </td>
                </tr>
            @else
                @foreach ($pelatihan as $d)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <!-- No Index -->
                        <td class="py-3 px-4 text-center text-slate-400 font-bold text-xs">
                            <span class="w-6 h-6 rounded-md bg-slate-100 group-hover:bg-emerald-50 group-hover:text-emerald-700 group-hover:border group-hover:border-emerald-200 inline-flex items-center justify-center font-bold text-slate-600 transition text-[11px]">
                                {{ $loop->iteration }}
                            </span>
                        </td>

                        <!-- Tanggal Pelatihan -->
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200/80 font-mono text-xs">
                                <i class="ti ti-calendar text-slate-400"></i>
                                {{ date('d-m-Y', strtotime($d->tanggal_pelatihan)) }}
                            </span>
                        </td>

                        <!-- Nama Pelatihan -->
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900 block text-xs sm:text-sm">{{ $d->nama_pelatihan }}</span>
                        </td>

                        <!-- Penyelenggara -->
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center gap-1.5 text-slate-600 font-medium text-xs">
                                <i class="ti ti-building text-slate-400 text-sm"></i>
                                {{ $d->penyelenggara }}
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if ($d->foto)
                                    <a href="{{ asset('storage/pelatihan/' . $d->foto) }}" 
                                       target="_blank"
                                       class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center justify-center transition active:scale-95 shadow-2xs"
                                       title="Lihat Sertifikat / Dokumen">
                                        <i class="ti ti-file-certificate text-base"></i>
                                    </a>
                                @else
                                    <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-300 border border-slate-200 flex items-center justify-center cursor-not-allowed"
                                          title="Tidak ada berkas sertifikat">
                                        <i class="ti ti-file-off text-base"></i>
                                    </span>
                                @endif

                                <form action="{{ route('pelatihan.delete', Crypt::encrypt($d->id)) }}" 
                                      method="POST" 
                                      class="delete-training-form inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition active:scale-95 btn-delete-training cursor-pointer shadow-2xs" 
                                            title="Hapus Pelatihan">
                                        <i class="ti ti-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
