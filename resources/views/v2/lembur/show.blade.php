@php
    $start = strtotime($lembur->lembur_mulai);
    $end = strtotime($lembur->lembur_selesai);
    $rencanaJam = round(hitungJam($lembur->lembur_mulai, $lembur->lembur_selesai), 2);
    
    $realisasiJam = "-";
    if ($lembur->lembur_in && $lembur->lembur_out) {
        $realisasiJam = round(hitungJam($lembur->lembur_in, $lembur->lembur_out), 2) . " Jam";
    }
@endphp

<div class="space-y-4">
    
    <!-- Profil Karyawan & Status -->
    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3.5">
            <div class="relative shrink-0">
                @if (!empty($lembur->foto) && Storage::disk('public')->exists('/karyawan/' . $lembur->foto))
                    <img src="{{ url('/storage/karyawan/' . $lembur->foto) }}" alt="{{ $lembur->nama_karyawan }}" class="w-12 h-14 rounded-xl object-cover border border-slate-200 shadow-2xs">
                @else
                    <div class="w-12 h-14 rounded-xl bg-slate-100 text-slate-700 border border-slate-200 flex items-center justify-center font-black text-sm shadow-2xs">
                        {{ strtoupper(substr($lembur->nama_karyawan, 0, 2)) }}
                    </div>
                @endif
            </div>
            <div>
                <h4 class="font-extrabold text-slate-900 text-sm mb-0.5">{{ $lembur->nama_karyawan }}</h4>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="font-mono font-bold text-slate-800">{{ $lembur->nik }}</span>
                    <span>•</span>
                    <span>{{ $lembur->nama_jabatan ?? '-' }}</span>
                </div>
                <div class="text-[11px] text-slate-400 mt-0.5">
                    {{ $lembur->nama_dept ?? '-' }} &bull; {{ $lembur->nama_cabang ?? '-' }}
                </div>
            </div>
        </div>

        <div class="sm:text-right sm:border-l sm:border-slate-200 sm:pl-4">
            <span class="text-[10.5px] font-bold text-slate-400 block uppercase">Status Surat</span>
            @if ($lembur->status === '0')
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs inline-flex items-center gap-1 mt-1">
                    <i class="ti ti-hourglass text-xs"></i> Pending
                </span>
            @elseif ($lembur->status === '1')
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs inline-flex items-center gap-1 mt-1">
                    <i class="ti ti-check text-xs"></i> Disetujui
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs inline-flex items-center gap-1 mt-1">
                    <i class="ti ti-x text-xs"></i> Ditolak
                </span>
            @endif
        </div>
    </div>

    <!-- Rincian Waktu & Keterangan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
        <div class="p-3.5 rounded-xl border border-slate-200/90 bg-white space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Rencana Lembur (SPL)</span>
            <div class="space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-500">Tanggal:</span>
                    <span class="font-bold text-slate-800">{{ DateToIndo($lembur->tanggal) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu:</span>
                    <span class="font-mono font-bold text-slate-800">{{ date('H:i', $start) }} - {{ date('H:i', $end) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Durasi Rencana:</span>
                    <span class="font-bold text-emerald-700">{{ $rencanaJam }} Jam</span>
                </div>
            </div>
        </div>

        <div class="p-3.5 rounded-xl border border-slate-200/90 bg-white space-y-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Realisasi Absensi Lembur</span>
            <div class="space-y-1">
                <div class="flex justify-between">
                    <span class="text-slate-500">Lembur IN:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $lembur->lembur_in ? date('d-m-Y H:i', strtotime($lembur->lembur_in)) : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Lembur OUT:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $lembur->lembur_out ? date('d-m-Y H:i', strtotime($lembur->lembur_out)) : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Total Jam Realisasi:</span>
                    <span class="font-bold text-emerald-700">{{ $realisasiJam }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Uraian Pekerjaan -->
    <div class="p-3.5 rounded-xl border border-slate-200/90 bg-white text-xs space-y-1">
        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Uraian Tugas / Keterangan</span>
        <p class="text-slate-700 leading-relaxed font-medium">
            {{ $lembur->keterangan ?? '-' }}
        </p>
    </div>

    <!-- Foto Bukti Absensi Lembur -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
        <!-- Foto IN -->
        <div class="p-3.5 rounded-xl border border-slate-200/90 bg-slate-50 text-center space-y-2">
            <span class="text-xs font-bold text-slate-700 block">Bukti Foto Masuk (IN)</span>
            @if (!empty($lembur->foto_lembur_in) && Storage::disk('public')->exists('/uploads/lembur/' . $lembur->foto_lembur_in))
                <img src="{{ url('/storage/uploads/lembur/' . $lembur->foto_lembur_in) }}" alt="Foto Lembur IN" class="w-full h-44 object-cover rounded-xl border border-slate-200 shadow-2xs mx-auto">
            @else
                <div class="w-full h-44 rounded-xl border border-dashed border-slate-300 bg-white flex flex-col items-center justify-center text-slate-400">
                    <i class="ti ti-camera-off text-3xl mb-1 text-slate-300"></i>
                    <span class="text-xs font-semibold">Tidak ada foto IN</span>
                </div>
            @endif
        </div>

        <!-- Foto OUT -->
        <div class="p-3.5 rounded-xl border border-slate-200/90 bg-slate-50 text-center space-y-2">
            <span class="text-xs font-bold text-slate-700 block">Bukti Foto Selesai (OUT)</span>
            @if (!empty($lembur->foto_lembur_out) && Storage::disk('public')->exists('/uploads/lembur/' . $lembur->foto_lembur_out))
                <img src="{{ url('/storage/uploads/lembur/' . $lembur->foto_lembur_out) }}" alt="Foto Lembur OUT" class="w-full h-44 object-cover rounded-xl border border-slate-200 shadow-2xs mx-auto">
            @else
                <div class="w-full h-44 rounded-xl border border-dashed border-slate-300 bg-white flex flex-col items-center justify-center text-slate-400">
                    <i class="ti ti-camera-off text-3xl mb-1 text-slate-300"></i>
                    <span class="text-xs font-semibold">Tidak ada foto OUT</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons (Print SPL) -->
    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
        <a href="{{ route('lembur.print', Crypt::encrypt($lembur->id)) }}" target="_blank" 
           class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-xs transition active:scale-95 inline-flex items-center gap-1.5 cursor-pointer">
            <i class="ti ti-printer text-sm"></i>
            <span>Cetak Surat Perintah Lembur (SPL)</span>
        </a>
        <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" data-bs-dismiss="modal">
            Tutup
        </button>
    </div>

</div>
