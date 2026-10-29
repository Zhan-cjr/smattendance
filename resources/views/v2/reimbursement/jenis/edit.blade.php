@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('jenisreimbursement.update', Crypt::encrypt($jenis->id)) }}" method="POST" id="formJenisReimburseEditV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Banner Info -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-edit"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Edit Parameter Jenis Reimbursement</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Perbarui nama kategori, batas nominal plafon (per klaim/bulan/tahun), serta ketentuan wajib nota lampiran.
            </p>
        </div>
    </div>

    <!-- Kode & Nama Jenis -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
                Kode Jenis
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-barcode text-base"></i>
                </span>
                <input type="text" value="{{ $jenis->kode_jenis_reimburse }}" readonly
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-600 bg-slate-100 border border-slate-300 rounded-xl cursor-not-allowed">
            </div>
        </div>

        <div class="sm:col-span-2 space-y-1.5">
            <label for="v2_nama_jenis_edit" class="block text-xs font-bold text-slate-700">
                Nama Jenis Reimbursement <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-receipt-2 text-base"></i>
                </span>
                <input type="text" name="nama_jenis" id="v2_nama_jenis_edit" 
                       value="{{ $jenis->nama_jenis }}"
                       maxlength="100"
                       placeholder="Contoh: Transportasi & Operasional Luar Kota" autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Maksimal 100 karakter</span>
                <span id="namaJenisEditCount">{{ strlen($jenis->nama_jenis) }}/100</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_nama_jenis_edit"></div>
        </div>
    </div>

    <!-- Deskripsi -->
    <div class="space-y-1.5">
        <label for="v2_deskripsi_edit" class="block text-xs font-bold text-slate-700">Deskripsi / Keterangan (Opsional)</label>
        <textarea name="deskripsi" id="v2_deskripsi_edit" rows="2" 
                  placeholder="Penjelasan kriteria klaim yang dapat diajukan..."
                  class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs">{{ $jenis->deskripsi }}</textarea>
    </div>

    <!-- Batas Plafon (Per Klaim, Per Bulan, Per Tahun) -->
    <div class="space-y-1.5 pt-1">
        <label class="block text-xs font-bold text-slate-700">
            Pengaturan Batas Nominal Plafon (Rp)
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Per Klaim -->
            <div class="space-y-1">
                <span class="text-[11px] font-semibold text-slate-500 block">Plafon Per Klaim</span>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none font-bold text-xs">Rp</span>
                    <input type="text" name="batas_nominal" id="v2_batas_nominal_edit" 
                           value="{{ $jenis->batas_nominal ? number_format($jenis->batas_nominal, 0, ',', '.') : '' }}"
                           placeholder="Unlimited" autocomplete="off"
                           class="w-full h-10 pl-9 pr-3.5 text-xs sm:text-sm text-right font-extrabold text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition money">
                </div>
            </div>

            <!-- Per Bulan -->
            <div class="space-y-1">
                <span class="text-[11px] font-semibold text-slate-500 block">Plafon Per Bulan</span>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none font-bold text-xs">Rp</span>
                    <input type="text" name="batas_nominal_bulanan" id="v2_batas_nominal_bulanan_edit" 
                           value="{{ $jenis->batas_nominal_bulanan ? number_format($jenis->batas_nominal_bulanan, 0, ',', '.') : '' }}"
                           placeholder="Unlimited" autocomplete="off"
                           class="w-full h-10 pl-9 pr-3.5 text-xs sm:text-sm text-right font-extrabold text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition money">
                </div>
            </div>

            <!-- Per Tahun -->
            <div class="space-y-1">
                <span class="text-[11px] font-semibold text-slate-500 block">Plafon Per Tahun</span>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none font-bold text-xs">Rp</span>
                    <input type="text" name="batas_nominal_tahunan" id="v2_batas_nominal_tahunan_edit" 
                           value="{{ $jenis->batas_nominal_tahunan ? number_format($jenis->batas_nominal_tahunan, 0, ',', '.') : '' }}"
                           placeholder="Unlimited" autocomplete="off"
                           class="w-full h-10 pl-9 pr-3.5 text-xs sm:text-sm text-right font-extrabold text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition money">
                </div>
            </div>
        </div>
        <p class="text-[10.5px] text-slate-400 italic">Biarkan kosong jika tidak ada batasan nominal plafon (Unlimited).</p>
    </div>

    <!-- Switches: Wajib Bukti & Status Aktif -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
        <label class="relative flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100/80 cursor-pointer transition select-none shadow-2xs">
            <input type="checkbox" name="wajib_bukti" value="1" id="v2_wajib_bukti_edit" {{ $jenis->wajib_bukti ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
            <div>
                <span class="text-xs font-bold text-slate-800 block">Wajib Upload Bukti Nota</span>
                <span class="text-[10.5px] text-slate-500 block">Karyawan harus melampirkan foto nota/struk</span>
            </div>
        </label>

        <label class="relative flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100/80 cursor-pointer transition select-none shadow-2xs">
            <input type="checkbox" name="status" value="1" id="v2_status_edit" {{ $jenis->status ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
            <div>
                <span class="text-xs font-bold text-slate-800 block">Status Kategori Aktif</span>
                <span class="text-[10.5px] text-slate-500 block">Dapat dipilih pada pengajuan klaim baru</span>
            </div>
        </label>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" class="btnCloseModalJenis px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitJenisEditV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, {{ $themeColor1 }}), var(--theme-color-2, {{ $themeColor2 }}));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    $(function() {
        if (typeof $.fn.maskMoney !== 'undefined') {
            $('#formJenisReimburseEditV2 .money').maskMoney({
                thousands: '.', decimal: ',', precision: 0, allowZero: true
            });
        }

        const namaEditInput = document.getElementById('v2_nama_jenis_edit');
        const namaEditCount = document.getElementById('namaJenisEditCount');

        if (namaEditInput) {
            namaEditInput.addEventListener('input', function(e) {
                let val = e.target.value;
                if (val.length > 100) val = val.substring(0, 100);
                e.target.value = val;
                if (namaEditCount) namaEditCount.textContent = `${val.length}/100`;
                if (val.trim()) {
                    $('#err_v2_nama_jenis_edit').addClass('hidden').text('');
                    $(namaEditInput).removeClass('border-rose-500');
                }
            });
        }

        $('#formJenisReimburseEditV2').on('submit', function(e) {
            const nama = namaEditInput ? namaEditInput.value.trim() : '';
            let hasError = false;

            $('#formJenisReimburseEditV2 .error-feedback').addClass('hidden').text('');
            $('#formJenisReimburseEditV2 input').removeClass('border-rose-500');

            if (!nama) {
                $('#err_v2_nama_jenis_edit').text('Nama Jenis wajib diisi').removeClass('hidden');
                $(namaEditInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitJenisEditV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    });
</script>
