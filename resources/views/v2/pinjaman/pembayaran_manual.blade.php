@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('pinjaman.storepembayaran', Crypt::encrypt($pinjaman->id)) }}" method="POST" id="formPembayaranV2" class="space-y-4" novalidate>
    @csrf

    <!-- Balance Info Card -->
    <div class="rounded-2xl p-4 sm:p-5 text-white shadow-2xs relative overflow-hidden" 
         style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
        <div class="relative z-10 flex items-center justify-between gap-3">
            <div>
                <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-emerald-100 block mb-0.5">Sisa Pinjaman</span>
                <div class="text-xl sm:text-2xl font-black text-white tracking-tight" id="v2_sisaPinjamanLabel">
                    Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                </div>
                <input type="hidden" id="v2_sisa_asli" value="{{ $pinjaman->sisa_pinjaman }}">
            </div>
            <div class="text-right">
                <span class="text-xs font-black text-white block truncate max-w-[180px]">{{ $pinjaman->karyawan->nama_karyawan }}</span>
                <span class="font-mono text-[11px] font-bold text-emerald-100 block mt-0.5">{{ $pinjaman->nik }} &bull; {{ $pinjaman->no_pinjaman }}</span>
            </div>
        </div>
    </div>

    <!-- Tanggal Bayar -->
    <div class="space-y-1.5">
        <label for="v2_tanggal_bayar" class="block text-xs font-bold text-slate-700">
            Tanggal Pembayaran <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                <i class="ti ti-calendar text-base"></i>
            </span>
            <input type="text" name="tanggal_bayar" id="v2_tanggal_bayar" 
                   value="{{ date('Y-m-d') }}"
                   placeholder="Pilih Tanggal Bayar"
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs flatpickr-pembayaran cursor-pointer">
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_tanggal_bayar"></div>
    </div>

    <!-- Jumlah Bayar -->
    <div class="space-y-1.5">
        <label for="v2_jumlah_bayar" class="block text-xs font-bold text-slate-700">
            Jumlah Pembayaran (Rp) <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none font-bold text-xs">
                Rp
            </span>
            <input type="text" name="jumlah_bayar" id="v2_jumlah_bayar" 
                   placeholder="0" autocomplete="off"
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm text-right font-extrabold text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs money-pembayaran">
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_jumlah_bayar"></div>
    </div>

    <!-- Jenis Pembayaran (Radio Box Options) -->
    <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700">
            Metode Pembayaran <span class="text-rose-500">*</span>
        </label>
        <div class="grid grid-cols-2 gap-3">
            <label class="relative flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100/80 cursor-pointer transition select-none has-checked:border-emerald-600 has-checked:bg-emerald-50/50 shadow-2xs">
                <input type="radio" name="jenis_pembayaran" value="M" id="v2_jenisManual" checked class="text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                <div>
                    <span class="text-xs font-bold text-slate-800 block">Bayar Angsuran</span>
                    <span class="text-[10.5px] text-slate-500 block">Manual (Alokasi FIFO)</span>
                </div>
            </label>

            <label class="relative flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-slate-100/80 cursor-pointer transition select-none has-checked:border-emerald-600 has-checked:bg-emerald-50/50 shadow-2xs">
                <input type="radio" name="jenis_pembayaran" value="P" id="v2_jenisPelunasan" class="text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                <div>
                    <span class="text-xs font-bold text-slate-800 block">Pelunasan Penuh</span>
                    <span class="text-[10.5px] text-slate-500 block">Tutup Sisa Pinjaman</span>
                </div>
            </label>
        </div>
    </div>

    <!-- Keterangan -->
    <div class="space-y-1.5">
        <label for="v2_pembayaran_keterangan" class="block text-xs font-bold text-slate-700">Keterangan Pembayaran (Opsional)</label>
        <textarea name="keterangan" id="v2_pembayaran_keterangan" rows="2" 
                  placeholder="Contoh: Pembayaran tunai via kasir / potong bonus tahunan"
                  class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs"></textarea>
    </div>

    <!-- Footer Modal Action -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" class="btnCloseModalPembayaran px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitPembayaranV2"
                class="px-5 py-2.5 text-white font-bold rounded-xl text-xs shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, var(--theme-color-1, {{ $themeColor1 }}), var(--theme-color-2, {{ $themeColor2 }}));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Pembayaran</span>
        </button>
    </div>
</form>

<script>
    $(function() {
        function initMask() {
            if (typeof $.fn.maskMoney !== 'undefined') {
                $(".money-pembayaran, #v2_jumlah_bayar").maskMoney({
                    thousands: '.', 
                    decimal: ',', 
                    precision: 0, 
                    allowZero: true
                });
            }
        }

        initMask();

        if (typeof flatpickr !== 'undefined') {
            $(".flatpickr-pembayaran").flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });
        }

        // Auto fill sisa saat pilih Pelunasan / Reset saat pilih Manual
        $('input[name="jenis_pembayaran"]').on('change', function() {
            if ($(this).val() === 'P') {
                let sisa = parseInt($('#v2_sisa_asli').val()) || 0;
                let $input = $('#v2_jumlah_bayar');
                $input.val(sisa.toLocaleString('id-ID'));
                if (typeof $.fn.maskMoney !== 'undefined') {
                    $input.maskMoney('mask');
                }
            } else {
                let $input = $('#v2_jumlah_bayar');
                $input.val('');
                if (typeof $.fn.maskMoney !== 'undefined') {
                    $input.maskMoney('mask');
                }
            }
        });

        // Form Submit Validation
        $('#formPembayaranV2').on('submit', function(e) {
            const tanggal = $('#v2_tanggal_bayar').val();
            const jumlahStr = $('#v2_jumlah_bayar').val().replace(/\./g, '');
            const jumlah = parseInt(jumlahStr) || 0;
            const sisa = parseInt($('#v2_sisa_asli').val()) || 0;
            let hasError = false;

            // Reset error feedback
            $('#formPembayaranV2 .error-feedback').addClass('hidden').text('');
            $('#formPembayaranV2 input').removeClass('border-rose-500');

            if (!tanggal) {
                $('#err_v2_tanggal_bayar').text('Tanggal pembayaran wajib diisi').removeClass('hidden');
                $('#v2_tanggal_bayar').addClass('border-rose-500');
                hasError = true;
            }

            if (!jumlah || jumlah <= 0) {
                $('#err_v2_jumlah_bayar').text('Jumlah pembayaran minimal Rp 1').removeClass('hidden');
                $('#v2_jumlah_bayar').addClass('border-rose-500');
                hasError = true;
            } else if (jumlah > sisa) {
                $('#err_v2_jumlah_bayar').text('Jumlah pembayaran melebihi sisa pinjaman (Rp ' + sisa.toLocaleString('id-ID') + ')').removeClass('hidden');
                $('#v2_jumlah_bayar').addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitPembayaranV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    });
</script>
