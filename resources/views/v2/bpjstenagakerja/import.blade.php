@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('bpjstenagakerja.import_proses') }}" method="POST" id="formBpjsTkImportV2" enctype="multipart/form-data" class="space-y-4" novalidate>
    @csrf

    <!-- Info Banner Petunjuk -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-file-import"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Petunjuk Import Data BPJS Tenaga Kerja</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Unduh template excel yang telah disediakan, lengkapi data nominal iuran BPJS Ketenagakerjaan dan tanggal berlakunya, lalu unggah file spreadsheet tersebut di bawah ini.
            </p>
        </div>
    </div>

    <!-- Download Template Button -->
    <div>
        <a href="{{ route('bpjstenagakerja.download_template') }}" 
           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs border border-emerald-200 shadow-2xs transition active:scale-95 cursor-pointer">
            <i class="ti ti-download text-sm"></i>
            <span>Unduh Template Format Excel (.xlsx)</span>
        </a>
    </div>

    <!-- File Input Upload -->
    <div class="space-y-1.5">
        <label for="import_file_bpjs_tk" class="block text-xs font-bold text-slate-700">
            Pilih Berkas Excel <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <input type="file" 
                   id="import_file_bpjs_tk" 
                   name="file" 
                   accept=".xlsx, .xls"
                   required
                   class="w-full text-xs text-slate-700 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 bg-white border border-slate-300 rounded-xl cursor-pointer focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition py-1">
        </div>
        <p class="text-[11px] text-slate-400">Format yang didukung: .xlsx, .xls (Maks. 5MB)</p>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_import_file_bpjs_tk"></div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                onclick="$('#modalBpjsTenagaKerja').addClass('hidden'); $('body').removeClass('overflow-hidden');">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitBpjsTkImportV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-upload text-sm"></i>
            <span>Mulai Import Data</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formBpjsTkImportV2');
        const fileInput = document.getElementById('import_file_bpjs_tk');

        $(form).on('submit', function(e) {
            const file = fileInput.files[0];
            let hasError = false;

            $('.error-feedback').addClass('hidden').text('');
            $(fileInput).removeClass('border-rose-500');

            if (!file) {
                $('#err_import_file_bpjs_tk').text('Silakan pilih berkas Excel terlebih dahulu').removeClass('hidden');
                $(fileInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitBpjsTkImportV2');
            $btn.prop('disabled', true).html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Mengunggah & Memproses...</span>
            `);
        });
    })();
</script>
