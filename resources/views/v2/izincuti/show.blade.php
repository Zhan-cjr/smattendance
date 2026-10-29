<div class="space-y-4 text-xs">
    <!-- 1. Header Profil Karyawan (Clean & Modern Card) -->
    <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-gradient-to-r from-emerald-50/60 via-slate-50/80 to-transparent border border-emerald-100/70">
        <!-- Avatar Foto -->
        <div class="relative shrink-0">
            @if (!empty($izincuti->foto) && Storage::disk('public')->exists('/karyawan/' . $izincuti->foto))
                <img src="{{ getfotoKaryawan($izincuti->foto) }}" alt="{{ $izincuti->nama_karyawan }}" class="w-12 h-13 rounded-xl object-cover border border-slate-200 shadow-2xs">
            @else
                <div class="w-12 h-13 rounded-xl bg-emerald-600 text-white border border-emerald-700/30 flex items-center justify-center font-black text-sm shadow-2xs">
                    {{ strtoupper(substr($izincuti->nama_karyawan, 0, 1)) }}
                </div>
            @endif
        </div>

        <!-- Info Detail Nama & Tag -->
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 flex-wrap">
                <h5 class="text-sm font-bold text-slate-900 truncate">
                    {{ $izincuti->nama_karyawan }}
                </h5>
                <span class="font-mono text-[10.5px] font-bold text-slate-600 bg-white px-2 py-0.5 rounded-md border border-slate-200/90 shadow-2xs">
                    {{ $izincuti->nik_show ?? $izincuti->nik }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1 flex-wrap">
                <span class="inline-flex items-center gap-1 text-slate-700 font-semibold">
                    <i class="ti ti-briefcase text-slate-400"></i> {{ $izincuti->nama_jabatan }}
                </span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-600">{{ $izincuti->nama_dept }}</span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-600">{{ $izincuti->nama_cabang }}</span>
            </div>
        </div>
    </div>

    <!-- 2. Rincian Utama Pengajuan (Clean Specs List) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 divide-y divide-slate-100 shadow-2xs overflow-hidden">
        <!-- Baris 1: Kode Izin & Jenis Cuti -->
        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
            <div class="p-3 sm:px-3.5 flex items-center justify-between gap-3">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i class="ti ti-barcode text-slate-400"></i> Kode Pengajuan
                </span>
                <span class="font-mono font-bold text-slate-800 text-right">{{ $izincuti->kode_izin_cuti }}</span>
            </div>
            <div class="p-3 sm:px-3.5 flex items-center justify-between gap-3">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i class="ti ti-calendar-event text-slate-400"></i> Jenis Cuti
                </span>
                <span class="font-bold text-teal-700 text-right">{{ $izincuti->jenis_cuti ?? ($izincuti->keterangan ?? 'Cuti') }}</span>
            </div>
        </div>

        <!-- Baris 2: Periode Tanggal & Durasi Hari Kerja -->
        @php
            $lama = hitungHariCuti($izincuti->nik, $izincuti->dari, $izincuti->sampai)['total_hari'];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-slate-50/40">
            <div class="p-3 sm:px-3.5 flex items-center justify-between gap-3">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i class="ti ti-calendar-time text-slate-400"></i> Periode Cuti
                </span>
                <span class="font-bold text-slate-800 text-right">
                    {{ date('d M Y', strtotime($izincuti->dari)) }} <span class="text-slate-400 font-normal">s/d</span> {{ date('d M Y', strtotime($izincuti->sampai)) }}
                </span>
            </div>
            <div class="p-3 sm:px-3.5 flex items-center justify-between gap-3">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i class="ti ti-clock text-slate-400"></i> Durasi Efektif
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 font-black text-xs border border-emerald-200/80 shrink-0">
                    <i class="ti ti-check text-[11px]"></i> {{ $lama }} Hari Kerja
                </span>
            </div>
        </div>

        <!-- Baris 3: Pelimpahan Tugas & Kepala Divisi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
            <div class="p-3 sm:px-3.5 flex items-center justify-between gap-3">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i class="ti ti-user-share text-slate-400"></i> Pelimpahan Tugas
                </span>
                <span class="font-semibold text-slate-800 text-right">{{ $izincuti->pelimpahan_tugas ?? '-' }}</span>
            </div>
            <div class="p-3 sm:px-3.5 flex items-center justify-between gap-3">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i class="ti ti-user-check text-slate-400"></i> Kepala Divisi
                </span>
                <span class="font-semibold text-slate-800 text-right">{{ $izincuti->nama_kepala_divisi ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- 3. Alasan / Keperluan Cuti -->
    <div class="space-y-1.5">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
            Alasan / Keperluan Cuti
        </label>
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-medium text-slate-700 italic leading-relaxed">
            "{{ $izincuti->keterangan ?? '-' }}"
        </div>
    </div>

    <!-- 4. Close & Print Action -->
    <div class="pt-3 flex items-center justify-between border-t border-slate-100">
        <a href="{{ route('izincuti.print', Crypt::encrypt($izincuti->kode_izin_cuti)) }}" 
           target="_blank"
           class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            <i class="ti ti-printer text-sm"></i>
            <span>Cetak Formulir</span>
        </a>

        <button type="button" 
                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer" 
                data-bs-dismiss="modal">
            Tutup
        </button>
    </div>
</div>
