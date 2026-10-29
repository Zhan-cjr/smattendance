@php
    $lama = hitungHariKerja($izindinas->nik, $izindinas->dari, $izindinas->sampai)['total_hari'];
@endphp

<form action="{{ isset($isDelegation) && $isDelegation ? route('karyawan-approval.izindinas.storeapprove', Crypt::encrypt($izindinas->kode_izin_dinas)) : route('izindinas.storeapprove', Crypt::encrypt($izindinas->kode_izin_dinas)) }}" 
      method="POST" 
      id="formApproveIzinDinasV2" 
      class="space-y-4">
    @csrf

    <!-- 1. Header Profil Karyawan (Clean & Modern Card) -->
    <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-gradient-to-r from-emerald-50/60 via-slate-50/80 to-transparent border border-emerald-100/70">
        <!-- Avatar Foto -->
        <div class="relative shrink-0">
            @if (!empty($izindinas->foto) && Storage::disk('public')->exists('/karyawan/' . $izindinas->foto))
                <img src="{{ getfotoKaryawan($izindinas->foto) }}" alt="{{ $izindinas->nama_karyawan }}" class="w-12 h-13 rounded-xl object-cover border border-slate-200 shadow-2xs">
            @else
                <div class="w-12 h-13 rounded-xl bg-emerald-600 text-white border border-emerald-700/30 flex items-center justify-center font-black text-sm shadow-2xs">
                    {{ strtoupper(substr($izindinas->nama_karyawan, 0, 1)) }}
                </div>
            @endif
        </div>

        <!-- Info Detail Nama & Tag -->
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 flex-wrap">
                <h5 class="text-sm font-bold text-slate-900 truncate">
                    {{ $izindinas->nama_karyawan }}
                </h5>
                <span class="font-mono text-[10.5px] font-bold text-slate-600 bg-white px-2 py-0.5 rounded-md border border-slate-200/90 shadow-2xs">
                    {{ $izindinas->nik_show ?? $izindinas->nik }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1 flex-wrap">
                <span class="inline-flex items-center gap-1 text-slate-700 font-semibold">
                    <i class="ti ti-briefcase text-slate-400"></i> {{ $izindinas->nama_jabatan }}
                </span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-600">{{ $izindinas->nama_dept }}</span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-600">{{ $izindinas->nama_cabang }}</span>
            </div>
        </div>
    </div>

    <!-- 2. Rincian Utama Pengajuan (Clean 2-Col Specs List) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 divide-y divide-slate-100 text-xs shadow-2xs overflow-hidden">
        <!-- Baris 1: Kode Izin Dinas & Tanggal Dibuat -->
        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
            <div class="p-3 sm:px-3.5 flex items-center justify-between gap-3">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i class="ti ti-barcode text-slate-400"></i> Kode Dinas
                </span>
                <span class="font-mono font-bold text-slate-800 text-right">{{ $izindinas->kode_izin_dinas }}</span>
            </div>
            <div class="p-3 sm:px-3.5 flex items-center justify-between gap-3">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i class="ti ti-calendar text-slate-400"></i> Tanggal Pengajuan
                </span>
                <span class="font-semibold text-slate-700 text-right">{{ DateToIndo($izindinas->tanggal ?? $izindinas->dari) }}</span>
            </div>
        </div>

        <!-- Baris 2: Periode Tanggal & Durasi Hari Kerja -->
        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-slate-50/40">
            <div class="p-3 sm:px-3.5 flex items-center justify-between gap-3">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i class="ti ti-calendar-event text-slate-400"></i> Periode Dinas
                </span>
                <span class="font-bold text-slate-800 text-right">
                    {{ date('d M Y', strtotime($izindinas->dari)) }} <span class="text-slate-400 font-normal">s/d</span> {{ date('d M Y', strtotime($izindinas->sampai)) }}
                </span>
            </div>
            <div class="p-3 sm:px-3.5 flex items-center justify-between gap-3">
                <span class="text-slate-400 font-medium flex items-center gap-1.5 shrink-0">
                    <i class="ti ti-clock text-slate-400"></i> Durasi Kerja
                </span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 font-black text-xs border border-emerald-200/80 shrink-0">
                    <i class="ti ti-check text-[11px]"></i> {{ $lama }} Hari Kerja
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Keperluan / Agenda Dinas -->
    <div class="space-y-1.5">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
            Keperluan / Agenda Dinas
        </label>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-medium text-slate-700 italic leading-relaxed">
            "{{ $izindinas->keterangan ?? '-' }}"
        </div>
    </div>

    <!-- 4. Catatan Tambahan Approver (Input) -->
    <div class="space-y-1.5">
        <label for="catatan_dinas_approve" class="block text-xs font-bold text-slate-700">
            Catatan Approval <span class="text-slate-400 font-normal">(Opsional)</span>
        </label>
        <textarea name="catatan" 
                  id="catatan_dinas_approve" 
                  rows="2" 
                  placeholder="Berikan catatan persetujuan atau alasan penolakan..."
                  class="w-full p-3 text-xs sm:text-sm text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition placeholder:text-slate-400"></textarea>
    </div>

    <!-- 5. Action Buttons (Approve & Tolak) -->
    <div class="pt-3 flex flex-col sm:flex-row items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>

        <button type="submit" 
                name="tolak" 
                value="tolak" 
                id="btnTolakDinas"
                class="w-full sm:w-auto px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs active:scale-95 cursor-pointer">
            <i class="ti ti-thumb-down text-sm"></i>
            <span>Tolak Dinas</span>
        </button>

        <button type="submit" 
                name="approve" 
                value="approve" 
                id="btnApproveDinas"
                class="w-full sm:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-thumb-up text-sm"></i>
            <span>Setujui Izin Dinas</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = $('#formApproveIzinDinasV2');
        let isSubmitting = false;

        $('#btnApproveDinas').on('click', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
            
            if (form.find('input[name="approve"]').length === 0) {
                form.append('<input type="hidden" name="approve" value="approve">');
            }

            const $btn = $(this);
            const $btnTolak = $('#btnTolakDinas');
            
            $btn.prop('disabled', true).addClass('opacity-80 cursor-not-allowed pointer-events-none');
            $btnTolak.prop('disabled', true).addClass('opacity-60 cursor-not-allowed pointer-events-none');
            
            $btn.html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Memproses...</span>
            `);

            form.trigger('submit');
        });

        $('#btnTolakDinas').on('click', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;

            if (form.find('input[name="tolak"]').length === 0) {
                form.append('<input type="hidden" name="tolak" value="tolak">');
            }

            const $btn = $(this);
            const $btnApprove = $('#btnApproveDinas');

            $btn.prop('disabled', true).addClass('opacity-80 cursor-not-allowed pointer-events-none');
            $btnApprove.prop('disabled', true).addClass('opacity-60 cursor-not-allowed pointer-events-none');

            $btn.html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Memproses...</span>
            `);

            form.trigger('submit');
        });
    })();
</script>
