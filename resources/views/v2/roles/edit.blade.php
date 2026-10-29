@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('roles.update', Crypt::encrypt($role->id)) }}" id="formEditRoleV2" method="POST" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Nama Role -->
    <div class="space-y-1.5 field-container">
        <label class="block text-xs font-bold text-slate-700">
            Nama Role / Peran <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="input-icon absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center transition-colors">
                <i class="ti ti-shield text-base"></i>
            </span>
            <input type="text" name="name" id="role_name_edit" value="{{ ucwords($role->name) }}"
                   placeholder="contoh: hr staff, finance, supervisor" autocomplete="off"
                   class="w-full h-10 pl-10 pr-4 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
        </div>
        <p class="text-[11px] text-slate-400 help-text">
            Guard: <strong class="text-slate-600 font-semibold">{{ $role->guard_name }}</strong> &bull; Perubahan nama role akan berdampak pada pengguna dengan role ini.
        </p>
        <p class="error-feedback text-[11px] font-semibold text-rose-500 hidden items-center gap-1.5 mt-1" id="err_name_edit">
            <i class="ti ti-alert-circle text-sm"></i>
            <span class="err-text"></span>
        </p>
    </div>

    <!-- Modal Footer Actions -->
    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
        <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95" data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" id="btnUpdateRole"
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const $input = $('#role_name_edit');
        const $err = $('#err_name_edit');
        const $icon = $input.closest('.relative').find('.input-icon i');

        function setFieldError(msg) {
            $input.addClass('border-rose-500 bg-rose-50/30 focus:border-rose-500 focus:ring-rose-500/20').removeClass('border-slate-300 focus:border-emerald-600 focus:ring-emerald-500/20');
            $icon.addClass('text-rose-500').removeClass('text-slate-400 text-emerald-600');
            $err.find('.err-text').text(msg);
            $err.removeClass('hidden').addClass('flex');
        }

        function clearFieldError() {
            $input.removeClass('border-rose-500 bg-rose-50/30 focus:border-rose-500 focus:ring-rose-500/20').addClass('border-slate-300 focus:border-emerald-600 focus:ring-emerald-500/20');
            $icon.removeClass('text-rose-500').addClass('text-slate-400');
            $err.addClass('hidden').removeClass('flex');
            $err.find('.err-text').text('');
        }

        function validateRole() {
            let val = $input.val().trim();
            if (!val) {
                setFieldError('Nama role wajib diisi.');
                return false;
            }
            clearFieldError();
            return true;
        }

        // Realtime validation trigger on typing & blur
        $input.on('input blur change', function(e) {
            if (e.type === 'blur' || $err.is(':visible') || $(this).val().trim() !== '') {
                validateRole();
            }
        });

        $('#formEditRoleV2').on('submit', function(e) {
            if (!validateRole()) {
                e.preventDefault();
                $input.focus();
                return false;
            }

            $('#btnUpdateRole').prop('disabled', true).addClass('opacity-75 cursor-not-allowed').html(`
                <i class="ti ti-loader animate-spin text-sm"></i>
                <span>Menyimpan...</span>
            `);
        });
    })();
</script>
