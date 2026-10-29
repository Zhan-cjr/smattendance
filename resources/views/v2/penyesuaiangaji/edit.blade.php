@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('penyesuaiangaji.update', Crypt::encrypt($penyesuaiangaji->kode_penyesuaian_gaji)) }}" method="POST" id="formPenyesuaianEditV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Info Banner -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-edit"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Perbarui Periode ({{ $penyesuaiangaji->kode_penyesuaian_gaji }})</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Ubah bulan atau tahun periode penyesuaian gaji.
            </p>
        </div>
    </div>

    <!-- 1. Pilihan Bulan -->
    <div class="space-y-1.5">
        <label for="edit_penyesuaian_bulan" class="block text-xs font-bold text-slate-700">
            Bulan Penyesuaian <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <select name="bulan" id="edit_penyesuaian_bulan" class="w-full h-10 px-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer" required>
                <option value="">-- Pilih Bulan --</option>
                @foreach ($list_bulan as $d)
                    <option value="{{ $d['kode_bulan'] }}" {{ $penyesuaiangaji->bulan == $d['kode_bulan'] ? 'selected' : '' }}>
                        {{ $d['nama_bulan'] }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_edit_penyesuaian_bulan"></div>
    </div>

    <!-- 2. Pilihan Tahun -->
    <div class="space-y-1.5">
        <label for="edit_penyesuaian_tahun" class="block text-xs font-bold text-slate-700">
            Tahun <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <select name="tahun" id="edit_penyesuaian_tahun" class="w-full h-10 px-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer" required>
                <option value="">-- Pilih Tahun --</option>
                @for ($t = $start_year; $t <= date('Y'); $t++)
                    <option value="{{ $t }}" {{ $penyesuaiangaji->tahun == $t ? 'selected' : '' }}>
                        Tahun {{ $t }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_edit_penyesuaian_tahun"></div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                onclick="$('#modalPenyesuaianGaji').addClass('hidden'); $('body').removeClass('overflow-hidden');">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitPenyesuaianEditV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Perbarui Periode</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formPenyesuaianEditV2');
        const bulanSelect = document.getElementById('edit_penyesuaian_bulan');
        const tahunSelect = document.getElementById('edit_penyesuaian_tahun');

        $(form).on('submit', function(e) {
            const bulan = $(bulanSelect).val();
            const tahun = $(tahunSelect).val();
            let hasError = false;

            $('.error-feedback').addClass('hidden').text('');
            $(bulanSelect).removeClass('border-rose-500');
            $(tahunSelect).removeClass('border-rose-500');

            if (!bulan) {
                $('#err_edit_penyesuaian_bulan').text('Bulan penyesuaian wajib dipilih').removeClass('hidden');
                $(bulanSelect).addClass('border-rose-500');
                hasError = true;
            }

            if (!tahun) {
                $('#err_edit_penyesuaian_tahun').text('Tahun penyesuaian wajib dipilih').removeClass('hidden');
                $(tahunSelect).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitPenyesuaianEditV2');
            $btn.prop('disabled', true).html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Menyimpan...</span>
            `);
        });
    })();
</script>
