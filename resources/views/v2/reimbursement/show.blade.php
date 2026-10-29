@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $encryptedNo = Crypt::encrypt($reimbursement->no_reimbursement);
@endphp

<div class="space-y-5">

    <!-- 1. Header Ringkasan & Profil Pemohon (Warna Tema) -->
    <div class="rounded-2xl p-4 sm:p-5 text-white shadow-2xs relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            
            <!-- Profil & Data Karyawan -->
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="relative shrink-0">
                    @if (!empty($reimbursement->foto) && Storage::disk('public')->exists('/karyawan/' . $reimbursement->foto))
                        <img src="{{ getfotoKaryawan($reimbursement->foto) }}" alt="{{ $reimbursement->nama_karyawan }}" 
                             class="w-12 h-13 sm:w-13 sm:h-14 rounded-xl object-cover border border-white/30 shadow-2xs">
                    @else
                        <div class="w-12 h-13 sm:w-13 sm:h-14 rounded-xl bg-white/20 text-white flex items-center justify-center font-bold text-base border border-white/30">
                            {{ strtoupper(substr($reimbursement->nama_karyawan, 0, 1)) }}
                        </div>
                    @endif
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-sm sm:text-base font-bold text-white truncate">
                            {{ $reimbursement->nama_karyawan }}
                        </h2>
                        <span class="font-mono text-xs text-white/90 bg-white/20 border border-white/20 px-1.5 py-0.5 rounded-md">
                            {{ $reimbursement->nik_show ?: $reimbursement->nik }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-emerald-100 mt-1 flex-wrap">
                        <span>{{ $reimbursement->nama_dept }}</span>
                        <span class="text-white/40">&bull;</span>
                        <span>Cabang {{ $reimbursement->nama_cabang }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Badge & Tanggal -->
            <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-1.5 pt-2 sm:pt-0 border-t sm:border-t-0 border-white/15 shrink-0">
                @if($reimbursement->status == 'P')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-amber-400 text-slate-900 shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-900"></span>
                        Menunggu persetujuan
                    </span>
                @elseif($reimbursement->status == 'A')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-white text-emerald-900 shadow-2xs">
                        <i class="ti ti-check text-xs"></i>
                        Disetujui
                    </span>
                @elseif($reimbursement->status == 'D')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-sky-300 text-sky-950 shadow-2xs">
                        <i class="ti ti-cash text-xs"></i>
                        Sudah dibayar
                    </span>
                @elseif($reimbursement->status == 'R')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-semibold bg-rose-400 text-slate-950 shadow-2xs">
                        <i class="ti ti-x text-xs"></i>
                        Ditolak
                    </span>
                @endif

                <span class="text-[11px] text-emerald-100/80">
                    Diajukan {{ date('d M Y', strtotime($reimbursement->tanggal_pengajuan)) }}
                </span>
            </div>

        </div>

        <!-- Detail Pengajuan / Keterangan Tambahan -->
        <div class="mt-3.5 pt-3 border-t border-white/20 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs relative z-10 text-emerald-50">
            <div>
                <span class="text-emerald-200">Nomor pengajuan:</span>
                <span class="font-mono font-bold text-white ml-1">{{ $reimbursement->no_reimbursement }}</span>
            </div>
            <div>
                <span class="text-emerald-200">Catatan pengajuan:</span>
                <span class="text-white ml-1">{{ $reimbursement->catatan ?: '-' }}</span>
            </div>
        </div>
    </div>

    <!-- 2. Rincian Item Nota (Clean Card List) -->
    <div class="space-y-3">
        <div class="flex items-center justify-between pb-1">
            <h4 class="text-xs font-bold text-slate-700">
                Rincian item nota ({{ count($details) }} item)
            </h4>
            <div class="text-xs text-slate-500">
                Total klaim: <span class="font-mono font-bold text-slate-900 text-sm ml-1">Rp {{ number_format($reimbursement->total_nominal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="space-y-2.5">
            @foreach ($details as $idx => $item)
                <div class="bg-white rounded-xl border border-slate-200 p-3.5 sm:p-4 hover:border-slate-300 transition-colors">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-slate-100">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="w-5 h-5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-bold flex items-center justify-center shrink-0">
                                {{ $idx + 1 }}
                            </span>
                            <span class="font-bold text-xs text-slate-800">
                                {{ $item->nama_jenis }}
                            </span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-xs text-slate-500">
                                Tgl nota: {{ date('d/m/Y', strtotime($item->tanggal_transaksi)) }}
                            </span>
                        </div>

                        <div class="font-mono font-bold text-slate-900 text-xs sm:text-sm">
                            Rp {{ number_format($item->nominal, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center pt-2.5 text-xs">
                        <div class="sm:col-span-8">
                            <span class="text-[11px] text-slate-400 block">Keterangan:</span>
                            <p class="text-slate-700 mt-0.5 leading-relaxed">
                                {{ $item->keterangan ?: '-' }}
                            </p>
                        </div>

                        <div class="sm:col-span-4 sm:text-right flex items-center sm:justify-end gap-2">
                            @if($item->bukti_file)
                                @php
                                    $ext = pathinfo($item->bukti_file, PATHINFO_EXTENSION);
                                    $isImg = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']);
                                    $isPdf = strtolower($ext) == 'pdf';
                                @endphp
                                <a href="{{ asset('storage/uploads/reimbursement/' . $item->bukti_file) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 transition">
                                    @if($isImg)
                                        <i class="ti ti-photo text-slate-500"></i>
                                        <span>Lihat nota</span>
                                    @elseif($isPdf)
                                        <i class="ti ti-file-text text-slate-500"></i>
                                        <span>Buka PDF</span>
                                    @else
                                        <i class="ti ti-download text-slate-500"></i>
                                        <span>Unduh file</span>
                                    @endif
                                </a>
                            @else
                                <span class="text-[11px] text-slate-400">Tidak ada lampiran</span>
                            @endif
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </div>

    <!-- 3. Riwayat Persetujuan / Timeline Approval -->
    @if($approvals->count() > 0)
        <div class="space-y-2.5 pt-1">
            <h4 class="text-xs font-bold text-slate-700">
                Riwayat persetujuan
            </h4>

            <div class="bg-white border border-slate-200 rounded-xl p-3.5 sm:p-4 space-y-3">
                @foreach($approvals as $app)
                    @php
                        $isApproved = $app->status == 'approved';
                    @endphp
                    <div class="flex items-start justify-between gap-3 text-xs pb-3 last:pb-0 border-b last:border-b-0 border-slate-100">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800">{{ $app->user_name }}</span>
                                <span class="text-[11px] text-slate-500 bg-slate-100 px-1.5 py-0.2 rounded">
                                    Tahap {{ $app->level }}
                                </span>
                            </div>
                            @if($app->keterangan)
                                <p class="text-slate-600 text-[11px] italic">
                                    "{{ $app->keterangan }}"
                                </p>
                            @endif
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center gap-1 font-semibold {{ $isApproved ? 'text-emerald-700' : 'text-rose-600' }}">
                                <i class="ti {{ $isApproved ? 'ti-check' : 'ti-x' }} text-xs"></i>
                                {{ $isApproved ? 'Disetujui' : 'Ditolak' }}
                            </span>
                            <span class="block text-[10.5px] text-slate-400 mt-0.5">
                                {{ date('d/m/Y H:i', strtotime($app->created_at)) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. Form Keputusan Approval (Jika User Berhak Approve) -->
    @if($can_approve && $reimbursement->status == 'P')
        <div class="bg-slate-50/70 border border-slate-200 rounded-2xl p-4 sm:p-5 space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-800">Form persetujuan</h4>
                    <p class="text-[11px] text-slate-500">Tentukan persetujuan atau penolakan pengajuan ini</p>
                </div>
                @if($is_super_admin)
                    <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-amber-100 text-amber-800">
                        Super admin bypass
                    </span>
                @endif
            </div>

            <form action="{{ route('reimbursement.storeapprove', $encryptedNo) }}" method="POST" id="fmApproveReimbursementV2" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Catatan persetujuan (opsional)
                    </label>
                    <textarea name="keterangan" rows="2" 
                              placeholder="Tuliskan catatan atau alasan di sini..."
                              class="w-full p-2.5 text-xs bg-white border border-slate-300 rounded-xl outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 transition resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200/80">
                    <button type="submit" name="tolak" value="tolak" 
                            class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-rose-600 text-xs font-bold transition active:scale-95 cursor-pointer">
                        Tolak pengajuan
                    </button>
                    <button type="submit" name="approve" value="approve" 
                            class="px-4 py-2 text-xs font-bold text-white rounded-xl shadow-xs transition active:scale-95 cursor-pointer"
                            style="background-color: {{ $themeColor1 }};">
                        {{ $is_super_admin ? 'Setujui langsung' : 'Setujui pengajuan' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- 5. Footer Actions & Cancel Approve -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-200/80">
        <div>
            @if($reimbursement->status != 'P' && ($is_super_admin || (isset($approvals->last()->user_id) && $approvals->last()->user_id == auth()->id())))
                <form action="{{ route('reimbursement.cancelapprove', $encryptedNo) }}" method="POST" id="fmCancelApproveReimbursement" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btnCancelApproveReimbursement inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition active:scale-95 cursor-pointer shadow-2xs">
                        <i class="ti ti-rotate-clockwise-2 text-sm"></i>
                        <span>Batalkan persetujuan</span>
                    </button>
                </form>
            @endif
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
            <a href="{{ route('reimbursement.print', Crypt::encrypt($reimbursement->id)) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition active:scale-95">
                <i class="ti ti-printer text-sm"></i>
                <span>Cetak formulir</span>
            </a>
            <button type="button" class="btnCloseModalDetailReimbursement px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold transition active:scale-95 cursor-pointer">
                Tutup
            </button>
        </div>
    </div>

</div>
