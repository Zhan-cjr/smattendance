@php
    $lama = hitungHariKerja($izinsakit->nik, $izinsakit->dari, $izinsakit->sampai)['total_hari'];
    $batasi_hari_izin_sakit = $general_setting->batasi_hari_izin_sakit ?? false;
    $jml_hari_izin_sakit_max = $general_setting->jml_hari_izin_sakit_max ?? 0;
@endphp

<form action="{{ isset($isDelegation) && $isDelegation ? route('karyawan-approval.izinsakit.storeapprove', Crypt::encrypt($izinsakit->kode_izin_sakit)) : route('izinsakit.storeapprove', Crypt::encrypt($izinsakit->kode_izin_sakit)) }}" 
      method="POST" 
      id="formApproveIzinSakitV2" 
      class="space-y-4">
    @csrf

    <!-- 1. Header Profil Karyawan (Clean & Modern Card) -->
    <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-gradient-to-r from-emerald-50/60 via-slate-50/80 to-transparent border border-emerald-100/70">
        <!-- Avatar Foto -->
        <div class="relative shrink-0">
            @if (!empty($izinsakit->foto) && Storage::disk('public')->exists('/karyawan/' . $izinsakit->foto))
                <img src="{{ getfotoKaryawan($izinsakit->foto) }}" alt="{{ $izinsakit->nama_karyawan }}" class="w-12 h-13 rounded-xl object-cover border border-slate-200 shadow-2xs">
            @else
                <div class="w-12 h-13 rounded-xl bg-emerald-600 text-white border border-emerald-700/30 flex items-center justify-center font-black text-sm shadow-2xs">
                    {{ strtoupper(substr($izinsakit->nama_karyawan, 0, 1)) }}
                </div>
            @endif
        </div>

        <!-- Info Detail Nama & Tag -->
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 flex-wrap">
                <h5 class="text-sm font-bold text-slate-900 truncate">
                    {{ $izinsakit->nama_karyawan }}
                </h5>
                <span class="font-mono text-[10.5px] font-bold text-slate-600 bg-white px-2 py-0.5 rounded-md border border-slate-200/90 shadow-2xs">
                    {{ $izinsakit->nik_show ?? $izinsakit->nik }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1 flex-wrap">
                <span class="inline-flex items-center gap-1 text-slate-700 font-semibold">
                    <i class="ti ti-briefcase text-slate-400"></i> {{ $izinsakit->nama_jabatan }}
                </span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-600">{{ $izinsakit->nama_dept }}</span>
                <span class="text-slate-300">•</span>
                <span class="text-slate-600">{{ $izinsakit->nama_cabang }}</span>
            </div>
        </div>
    </div>

    <!-- 2. Rincian Utama Pengajuan (Clean 2-Col Specs List) -->
    <div class="bg-white rounded-2xl border border-slate-200/90 divide-y divide-slate-100 text-xs shadow-2xs overflow-hidden">
        <!-- Baris 1: Kode Izin & Tanggal Dibuat -->
        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
            <div class="p-3 flex items-center justify-between gap-2">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i class="ti ti-barcode text-slate-400"></i> Kode Pengajuan
                </span>
                <span class="font-mono font-bold text-slate-800">{{ $izinsakit->kode_izin_sakit }}</span>
            </div>
            <div class="p-3 flex items-center justify-between gap-2">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i class="ti ti-calendar text-slate-400"></i> Tanggal Pengajuan
                </span>
                <span class="font-semibold text-slate-700">{{ DateToIndo($izinsakit->tanggal) }}</span>
            </div>
        </div>

        <!-- Baris 2: Periode Tanggal & Durasi Hari Kerja -->
        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-slate-50/40">
            <div class="p-3 flex items-center justify-between gap-2">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i class="ti ti-calendar-event text-slate-400"></i> Periode Sakit
                </span>
                <span class="font-bold text-slate-800">
                    {{ date('d M Y', strtotime($izinsakit->dari)) }} <span class="text-slate-400 font-normal">s/d</span> {{ date('d M Y', strtotime($izinsakit->sampai)) }}
                </span>
            </div>
            <div class="p-3 flex items-center justify-between gap-2">
                <span class="text-slate-400 font-medium flex items-center gap-1.5">
                    <i class="ti ti-clock text-slate-400"></i> Durasi Kerja
                </span>
                <div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-black text-xs border border-emerald-200/80">
                        <i class="ti ti-check text-[11px]"></i> {{ $lama }} Hari Kerja
                    </span>
                    @if ($batasi_hari_izin_sakit == 1 && $jml_hari_izin_sakit_max > 0 && $lama > $jml_hari_izin_sakit_max)
                        <span class="text-[10px] text-amber-600 block mt-0.5">Max {{ $jml_hari_izin_sakit_max }} hari tercatat sakit</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Alasan / Keterangan Sakit -->
    <div class="space-y-1.5">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">
            Keterangan Sakit
        </label>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-medium text-slate-700 italic leading-relaxed">
            "{{ $izinsakit->keterangan ?? '-' }}"
        </div>
    </div>

    <!-- 4. Lampiran Surat Izin Dokter (SID) -->
    @if (!empty($izinsakit->doc_sid))
        <div class="p-3 rounded-xl bg-indigo-50/60 border border-indigo-100 flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="w-8 h-8 rounded-lg bg-white border border-indigo-200 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="ti ti-file-certificate text-base"></i>
                </span>
                <div class="min-w-0">
                    <span class="font-bold text-slate-800 block truncate">Surat Dokter (SID) Terlampir</span>
                    <span class="text-[10px] text-slate-500">Bukti surat keterangan dokter dari karyawan</span>
                </div>
            </div>
            @if (Storage::disk('public')->exists('/uploads/sid/' . $izinsakit->doc_sid))
                <a href="{{ asset('storage/uploads/sid/' . $izinsakit->doc_sid) }}?t={{ time() }}" 
                   target="_blank" 
                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-indigo-50 text-indigo-700 rounded-lg font-bold text-xs border border-indigo-200 shadow-2xs transition shrink-0">
                    <i class="ti ti-external-link"></i>
                    <span>Lihat Bukti</span>
                </a>
            @else
                <span class="text-[10px] text-rose-500 font-semibold shrink-0">Berkas tidak ditemukan</span>
            @endif
        </div>
    @endif

    <!-- 5. Catatan Tambahan Approver (Input) -->
    <div class="space-y-1.5">
        <label for="catatan_sakit" class="block text-xs font-bold text-slate-700">
            Catatan Approval <span class="text-slate-400 font-normal">(Opsional)</span>
        </label>
        <textarea name="catatan" 
                  id="catatan_sakit" 
                  rows="2" 
                  placeholder="Berikan catatan persetujuan atau alasan penolakan..."
                  class="w-full p-3 text-xs sm:text-sm text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition placeholder:text-slate-400"></textarea>
    </div>

    <!-- 6. Action Buttons (Approve & Tolak) -->
    <div class="pt-3 flex flex-col sm:flex-row items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>

        <button type="submit" 
                name="tolak" 
                value="tolak" 
                id="btnTolakIzinSakit"
                class="w-full sm:w-auto px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs active:scale-95 cursor-pointer">
            <i class="ti ti-thumb-down text-sm"></i>
            <span>Tolak Izin</span>
        </button>

        <button type="submit" 
                name="approve" 
                value="approve" 
                id="btnApproveIzinSakit"
                class="w-full sm:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-thumb-up text-sm"></i>
            <span>Setujui Izin Sakit</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = $('#formApproveIzinSakitV2');
        let isSubmitting = false;

        $('#btnApproveIzinSakit').on('click', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;

            // Tambahkan hidden input agar value 'approve' tetap terkirim saat form disubmit
            if (form.find('input[name="approve"]').length === 0) {
                form.append('<input type="hidden" name="approve" value="approve">');
            }

            const $btn = $(this);
            const $btnTolak = $('#btnTolakIzinSakit');

            $btn.prop('disabled', true).addClass('opacity-80 cursor-not-allowed pointer-events-none');
            $btnTolak.prop('disabled', true).addClass('opacity-60 cursor-not-allowed pointer-events-none');

            $btn.html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Memproses...</span>
            `);

            form.trigger('submit');
        });

        $('#btnTolakIzinSakit').on('click', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;

            // Tambahkan hidden input agar value 'tolak' tetap terkirim saat form disubmit
            if (form.find('input[name="tolak"]').length === 0) {
                form.append('<input type="hidden" name="tolak" value="tolak">');
            }

            const $btn = $(this);
            const $btnApprove = $('#btnApproveIzinSakit');

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
