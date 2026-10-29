@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $currentRole = strtolower($user->roles->pluck('name')->first() ?? '');
    $isSuperAdmin = $currentRole === 'super admin' || $currentRole === 'master admin';
    $isKaryawan = $currentRole === 'karyawan';
@endphp

<form action="{{ route('users.update', Crypt::encrypt($user->id)) }}" id="formEditUserV2" method="POST" class="space-y-4">
    @csrf
    @method('PUT')

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
                <input type="text" name="name" id="name_edit" value="{{ $user->name }}" {{ $isKaryawan ? 'readonly' : '' }}
                       class="w-full h-10 pl-9 pr-3 {{ $isKaryawan ? 'bg-slate-100 cursor-not-allowed text-slate-500' : 'bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white' }} border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
            </div>
            <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_name_edit"></p>
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
                <input type="text" name="username" id="username_edit" value="{{ $user->username }}"
                       class="w-full h-10 pl-9 pr-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
            </div>
            <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_username_edit"></p>
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
                <input type="email" name="email" id="email_edit" value="{{ $user->email }}"
                       class="w-full h-10 pl-9 pr-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" required>
            </div>
            <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_email_edit"></p>
        </div>

        <!-- Password (Optional) -->
        <div class="space-y-1">
            <label class="block text-xs font-bold text-slate-700">
                Ganti Password <span class="text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span>
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="ti ti-key text-sm"></i>
                </span>
                <input type="password" name="password" id="password_edit" placeholder="Password baru"
                       class="w-full h-10 pl-9 pr-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
            </div>
        </div>
    </div>

    <!-- Role Selection -->
    @if(!$isKaryawan)
    <div class="space-y-1">
        <label class="block text-xs font-bold text-slate-700">
            Role / Hak Peran <span class="text-rose-500">*</span>
        </label>
        <select name="role" id="role_select_edit"
                class="w-full h-10 px-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer" required>
            @foreach($roles as $r)
                <option value="{{ $r->name }}" @selected($user->hasRole($r->name))>{{ ucwords($r->name) }}</option>
            @endforeach
        </select>
    </div>
    @else
        <input type="hidden" name="role" value="karyawan">
    @endif

    <!-- Hak Akses Cabang (Non Karyawan) -->
    @if(!$isKaryawan)
    <div class="space-y-1.5" id="cabang_access_wrapper_edit">
        <div class="flex items-center justify-between">
            <label class="block text-xs font-bold text-slate-700">
                Hak Akses Cabang <span class="text-rose-500">*</span>
            </label>
            <button type="button" id="btnSelectAllCabangEdit" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 cursor-pointer">
                Pilih Semua
            </button>
        </div>
        <div class="border border-slate-200 rounded-xl p-3 max-h-40 overflow-y-auto bg-slate-50/50 space-y-2">
            @forelse($cabangs as $cb)
                @php
                    $isChecked = $isSuperAdmin || in_array($cb->kode_cabang, $userCabangs ?? []);
                @endphp
                <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:bg-white p-1.5 rounded-lg transition">
                    <input type="checkbox" name="cabangs[]" value="{{ $cb->kode_cabang }}" 
                           class="cabang-checkbox-edit w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                           {{ $isChecked ? 'checked' : '' }}>
                    <span class="font-bold text-slate-800">{{ $cb->kode_cabang }}</span>
                    <span class="text-slate-500">- {{ $cb->nama_cabang }}</span>
                </label>
            @empty
                <p class="text-xs text-slate-400">Tidak ada data cabang</p>
            @endforelse
        </div>
        <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_cabangs_edit">Minimal 1 cabang harus dipilih</p>
    </div>

    <!-- Hak Akses Departemen (Non Karyawan) -->
    <div class="space-y-1.5" id="departemen_access_wrapper_edit">
        <div class="flex items-center justify-between">
            <label class="block text-xs font-bold text-slate-700">
                Hak Akses Departemen <span class="text-rose-500">*</span>
            </label>
            <button type="button" id="btnSelectAllDeptEdit" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-700 cursor-pointer">
                Pilih Semua
            </button>
        </div>
        <div class="border border-slate-200 rounded-xl p-3 max-h-40 overflow-y-auto bg-slate-50/50 space-y-2">
            @forelse($departemens as $dp)
                @php
                    $isChecked = $isSuperAdmin || in_array($dp->kode_dept, $userDepartemens ?? []);
                @endphp
                <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer hover:bg-white p-1.5 rounded-lg transition">
                    <input type="checkbox" name="departemens[]" value="{{ $dp->kode_dept }}" 
                           class="dept-checkbox-edit w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300"
                           {{ $isChecked ? 'checked' : '' }}>
                    <span class="font-bold text-slate-800">{{ $dp->kode_dept }}</span>
                    <span class="text-slate-500">- {{ $dp->nama_dept }}</span>
                </label>
            @empty
                <p class="text-xs text-slate-400">Tidak ada data departemen</p>
            @endforelse
        </div>
        <p class="text-[11px] text-rose-500 font-medium hidden error-feedback" id="err_departemens_edit">Minimal 1 departemen harus dipilih</p>
    </div>
    @endif

    <!-- Approval Delegation (Karyawan) -->
    @if($isKaryawan && isset($userkaryawan))
    <div class="space-y-1">
        <label class="block text-xs font-bold text-slate-700">
            Approval Admin (Delegasi Izin / Presensi)
        </label>
        <select name="approval_admin_id" class="w-full h-10 px-3 bg-slate-50/50 hover:bg-slate-50/90 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition cursor-pointer">
            <option value="">-- Tidak Ada Delegasi Khusus --</option>
            @foreach($adminUsers as $adminUser)
                <option value="{{ $adminUser->id }}" @selected($userkaryawan->approval_admin_id == $adminUser->id)>
                    {{ $adminUser->name }} ({{ $adminUser->getRoleNames()->first() }})
                </option>
            @endforeach
        </select>
        <p class="text-[11px] text-slate-400">Admin terpilih akan menerima notifikasi dan persetujuan izin atas nama karyawan ini.</p>
    </div>
    @endif

    <!-- Modal Footer Actions -->
    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
        <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" id="btnSubmitEditUser"
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script>
$(function() {
    const isKaryawan = @json($isKaryawan);

    function checkRoleAccessEdit() {
        let role = ($('#role_select_edit').val() || '').toLowerCase();
        let isSuper = (role === 'super admin' || role === 'master admin');

        if (isSuper) {
            $('#cabang_access_wrapper_edit').slideUp(150);
            $('#departemen_access_wrapper_edit').slideUp(150);
        } else {
            $('#cabang_access_wrapper_edit').slideDown(150);
            $('#departemen_access_wrapper_edit').slideDown(150);
        }
    }

    $('#role_select_edit').on('change', checkRoleAccessEdit);
    checkRoleAccessEdit();

    // Select All Cabang
    $('#btnSelectAllCabangEdit').on('click', function() {
        let allChecked = $('.cabang-checkbox-edit:checked').length === $('.cabang-checkbox-edit').length;
        $('.cabang-checkbox-edit').prop('checked', !allChecked);
        $(this).text(allChecked ? 'Pilih Semua' : 'Batal Semua');
    });

    // Select All Dept
    $('#btnSelectAllDeptEdit').on('click', function() {
        let allChecked = $('.dept-checkbox-edit:checked').length === $('.dept-checkbox-edit').length;
        $('.dept-checkbox-edit').prop('checked', !allChecked);
        $(this).text(allChecked ? 'Pilih Semua' : 'Batal Semua');
    });

    // Form Submit Client Validation
    $('#formEditUserV2').on('submit', function(e) {
        $('.error-feedback').addClass('hidden').text('');
        $('input, select').removeClass('border-rose-500');

        let isValid = true;
        let name = $('#name_edit').val().trim();
        let username = $('#username_edit').val().trim();
        let email = $('#email_edit').val().trim();
        let role = isKaryawan ? 'karyawan' : $('#role_select_edit').val();
        let roleLower = (role || '').toLowerCase();

        if (!name) {
            $('#name_edit').addClass('border-rose-500');
            $('#err_name_edit').removeClass('hidden').text('Nama lengkap wajib diisi');
            isValid = false;
        }

        if (!username) {
            $('#username_edit').addClass('border-rose-500');
            $('#err_username_edit').removeClass('hidden').text('Username wajib diisi');
            isValid = false;
        }

        if (!email) {
            $('#email_edit').addClass('border-rose-500');
            $('#err_email_edit').removeClass('hidden').text('Email wajib diisi');
            isValid = false;
        }

        if (!isKaryawan && roleLower !== 'super admin' && roleLower !== 'master admin') {
            let checkedCabang = $('.cabang-checkbox-edit:checked').length;
            if (checkedCabang === 0) {
                $('#err_cabangs_edit').removeClass('hidden').text('Minimal 1 cabang harus dipilih');
                isValid = false;
            }

            let checkedDept = $('.dept-checkbox-edit:checked').length;
            if (checkedDept === 0) {
                $('#err_departemens_edit').removeClass('hidden').text('Minimal 1 departemen harus dipilih');
                isValid = false;
            }
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }

        let btn = $('#btnSubmitEditUser');
        btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed')
           .html('<i class="ti ti-loader animate-spin text-sm"></i><span>Menyimpan...</span>');
    });
});
</script>
