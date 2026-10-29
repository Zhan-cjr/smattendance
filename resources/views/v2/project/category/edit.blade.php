@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('projectcategory.update', Crypt::encrypt($category->id)) }}" method="POST" id="formEditProjectCategoryV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Nama Kategori -->
    <div class="space-y-1">
        <label for="edit_nama_kategori" class="block text-xs font-bold text-slate-700">
            Nama Kategori <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                <i class="ti ti-tag text-sm"></i>
            </span>
            <input type="text" name="nama_kategori" id="edit_nama_kategori" value="{{ $category->nama_kategori }}" required maxlength="100"
                   class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium"
                   style="--tw-ring-color: {{ $themeColor1 }};"
                   placeholder="Contoh: Web Development, Marketing, dll">
        </div>
        <div class="error-feedback text-[11px] text-rose-500 font-semibold hidden"></div>
    </div>

    <!-- Warna Label -->
    <div class="space-y-1">
        <label for="edit_warna_hex" class="block text-xs font-bold text-slate-700">
            Warna Label (Hex) <span class="text-rose-500">*</span>
        </label>
        <div class="flex items-center gap-3">
            <input type="color" id="edit_warna_picker" value="{{ $category->warna }}"
                   class="w-12 h-10 rounded-xl border border-slate-200 cursor-pointer p-1 bg-white shadow-2xs">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                    <i class="ti ti-palette text-sm"></i>
                </span>
                <input type="text" name="warna" id="edit_warna_hex" value="{{ $category->warna }}" maxlength="7" required
                       class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 font-mono focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold"
                       style="--tw-ring-color: {{ $themeColor1 }};"
                       placeholder="#0a5c41">
            </div>
        </div>
        <div class="error-feedback text-[11px] text-rose-500 font-semibold hidden"></div>
    </div>

    <!-- Deskripsi Kategori -->
    <div class="space-y-1">
        <label for="edit_deskripsi" class="block text-xs font-bold text-slate-700">Deskripsi Kategori</label>
        <textarea name="deskripsi" id="edit_deskripsi" rows="3"
                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium"
                  style="--tw-ring-color: {{ $themeColor1 }};"
                  placeholder="Deskripsi singkat mengenai jenis proyek dalam kategori ini...">{{ $category->deskripsi }}</textarea>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
        <button type="button" class="btnCloseModalCategory px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-2xs cursor-pointer">
            Batal
        </button>
        <button type="submit" id="btnUpdateCategory"
                class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-xs"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Sync color picker with text input
    $('#edit_warna_picker').on('input', function() {
        $('#edit_warna_hex').val($(this).val());
    });
    $('#edit_warna_hex').on('input', function() {
        const val = $(this).val();
        if(val.startsWith('#') && val.length === 7) {
            $('#edit_warna_picker').val(val);
        }
    });

    // Form validation
    $('#formEditProjectCategoryV2').on('submit', function(e) {
        let isValid = true;
        $('.error-feedback').addClass('hidden').text('');
        $('input').removeClass('border-rose-500');

        const nama = $('#edit_nama_kategori').val().trim();
        if(!nama) {
            $('#edit_nama_kategori').addClass('border-rose-500');
            $('#edit_nama_kategori').closest('.space-y-1').find('.error-feedback').removeClass('hidden').text('Nama kategori wajib diisi.');
            isValid = false;
        }

        const warna = $('#edit_warna_hex').val().trim();
        if(!warna || !warna.startsWith('#')) {
            $('#edit_warna_hex').addClass('border-rose-500');
            $('#edit_warna_hex').closest('.space-y-1').find('.error-feedback').removeClass('hidden').text('Format warna harus berupa kode HEX valid (contoh: #0a5c41).');
            isValid = false;
        }

        if(!isValid) {
            e.preventDefault();
            return false;
        }

        const btn = $('#btnUpdateCategory');
        btn.prop('disabled', true).html(`
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Menyimpan...
        `);
    });
});
</script>
