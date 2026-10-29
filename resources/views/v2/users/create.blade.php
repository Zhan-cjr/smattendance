@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('users.store') }}" id="formCreateUserV2" method="POST" class="space-y-4">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <!-- Nama Lengkap -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700">
                Nama Lengkap User <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="ti ti-user text-sm"></i>
                </span>
                <input type="text" name="name" id="name" placeholder="contoh: Budi Santoso"
                       class="w-full h-10 pl-9 pr-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
            </div>
            <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_name"></p>
        </div>

        <!-- Username -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700">
                Username <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="ti ti-at text-sm"></i>
                </span>
                <input type="text" name="username" id="username" placeholder="contoh: budisantoso"
                       class="w-full h-10 pl-9 pr-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
            </div>
            <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_username"></p>
        </div>

        <!-- Email -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700">
                Alamat Email <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="ti ti-mail text-sm"></i>
                </span>
                <input type="email" name="email" id="email" placeholder="contoh: budi@perusahaan.com"
                       class="w-full h-10 pl-9 pr-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
            </div>
            <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_email"></p>
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700">
                Password <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="ti ti-key text-sm"></i>
                </span>
                <input type="password" name="password" id="password" placeholder="Minimal 6 karakter"
                       class="w-full h-10 pl-9 pr-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
            </div>
            <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_password"></p>
        </div>
    </div>

    <!-- Role Selection -->
    <div class="space-y-1">
        <label class="block text-xs font-bold text-slate-700">
            Role / Hak Peran <span class="text-rose-500">*</span>
        </label>
        <select name="role" id="role_select"
                class="w-full h-10 px-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer" required>
            <option value="">-- Pilih Role Pengguna --</option>
            @foreach($roles as $r)
                <option value="{{ $r->name }}">{{ ucwords($r->name) }}</option>
            @endforeach
        </select>
        <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_role"></p>
    </div>

    <!-- Hak Akses Cabang -->
    <div class="space-y-1.5" id="cabang_access_wrapper">
        <div class="flex items-center justify-between">
            <label class="block text-xs font-bold text-slate-700">
                Hak Akses Cabang <span class="text-rose-500">*</span>
            </label>
            <button type="button" id="btnSelectAllCabang" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 cursor-pointer">
                Pilih Semua
            </button>
        </div>
        <div class="border border-slate-200 rounded-xl p-3 max-h-40 overflow-y-auto bg-slate-50/50 space-y-2">
            @forelse($cabangs as $cb)
                <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:bg-white p-1.5 rounded-lg transition">
                    <input type="checkbox" name="cabangs[]" value="{{ $cb->kode_cabang }}" class="cabang-checkbox w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <span class="font-bold text-slate-800">{{ $cb->kode_cabang }}</span>
                    <span class="text-slate-500">- {{ $cb->nama_cabang }}</span>
                </label>
            @empty
                <p class="text-xs text-slate-400">Tidak ada data cabang</p>
            @endforelse
        </div>
        <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_cabangs">Minimal 1 cabang harus dipilih</p>
    </div>

    <!-- Hak Akses Departemen -->
    <div class="space-y-1.5" id="departemen_access_wrapper">
        <div class="flex items-center justify-between">
            <label class="block text-xs font-bold text-slate-700">
                Hak Akses Departemen <span class="text-rose-500">*</span>
            </label>
            <button type="button" id="btnSelectAllDept" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 cursor-pointer">
                Pilih Semua
            </button>
        </div>
        <div class="border border-slate-200 rounded-xl p-3 max-h-40 overflow-y-auto bg-slate-50/50 space-y-2">
            @forelse($departemens as $dp)
                <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:bg-white p-1.5 rounded-lg transition">
                    <input type="checkbox" name="departemens[]" value="{{ $dp->kode_dept }}" class="dept-checkbox w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <span class="font-bold text-slate-800">{{ $dp->kode_dept }}</span>
                    <span class="text-slate-500">- {{ $dp->nama_dept }}</span>
                </label>
            @empty
                <p class="text-xs text-slate-400">Tidak ada data departemen</p>
            @endforelse
        </div>
        <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_departemens">Minimal 1 departemen harus dipilih</p>
    </div>

    <!-- Modal Footer Actions -->
    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
        <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" id="btnSubmitUser"
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan User</span>
        </button>
    </div>
</form>

<script>
$(function() {
    function checkRoleAccess() {
        let role = ($('#role_select').val() || '').toLowerCase();
        let isSuperAdmin = (role === 'super admin' || role === 'master admin');

        if (isSuperAdmin) {
            $('#cabang_access_wrapper').slideUp(150);
            $('#departemen_access_wrapper').slideUp(150);
        } else {
            $('#cabang_access_wrapper').slideDown(150);
            $('#departemen_access_wrapper').slideDown(150);
        }
    }

    $('#role_select').on('change', checkRoleAccess);
    checkRoleAccess();

    // Select All Cabang
    $('#btnSelectAllCabang').on('click', function() {
        let allChecked = $('.cabang-checkbox:checked').length === $('.cabang-checkbox').length;
        $('.cabang-checkbox').prop('checked', !allChecked);
        $(this).text(allChecked ? 'Pilih Semua' : 'Batal Semua');
    });

    // Select All Dept
    $('#btnSelectAllDept').on('click', function() {
        let allChecked = $('.dept-checkbox:checked').length === $('.dept-checkbox').length;
        $('.dept-checkbox').prop('checked', !allChecked);
        $(this).text(allChecked ? 'Pilih Semua' : 'Batal Semua');
    });

    // Form Submit Client Validation
    $('#formCreateUserV2').on('submit', function(e) {
        $('.error-feedback').addClass('hidden').text('');
        $('input, select').removeClass('border-rose-500');

        let isValid = true;
        let name = $('#name').val().trim();
        let username = $('#username').val().trim();
        let email = $('#email').val().trim();
        let password = $('#password').val();
        let role = $('#role_select').val();
        let roleLower = (role || '').toLowerCase();

        if (!name) {
            $('#name').addClass('border-rose-500');
            $('#err_name').removeClass('hidden').text('Nama lengkap wajib diisi');
            isValid = false;
        }

        if (!username) {
            $('#username').addClass('border-rose-500');
            $('#err_username').removeClass('hidden').text('Username wajib diisi');
            isValid = false;
        }

        if (!email) {
            $('#email').addClass('border-rose-500');
            $('#err_email').removeClass('hidden').text('Email wajib diisi');
            isValid = false;
        }

        if (!password || password.length < 6) {
            $('#password').addClass('border-rose-500');
            $('#err_password').removeClass('hidden').text('Password minimal 6 karakter');
            isValid = false;
        }

        if (!role) {
            $('#role_select').addClass('border-rose-500');
            $('#err_role').removeClass('hidden').text('Pilih salah satu role');
            isValid = false;
        }

        if (roleLower !== 'super admin' && roleLower !== 'master admin') {
            let checkedCabang = $('.cabang-checkbox:checked').length;
            if (checkedCabang === 0) {
                $('#err_cabangs').removeClass('hidden').text('Minimal 1 cabang harus dipilih');
                isValid = false;
            }

            let checkedDept = $('.dept-checkbox:checked').length;
            if (checkedDept === 0) {
                $('#err_departemens').removeClass('hidden').text('Minimal 1 departemen harus dipilih');
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }

        let btn = $('#btnSubmitUser');
        btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed')
           .html('<i class="ti ti-loader animate-spin text-sm"></i><span>Menyimpan...</span>');
    });
});
</script>
