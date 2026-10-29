@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('tunjangan.update', Crypt::encrypt($tunjangan->kode_tunjangan)) }}" method="POST" id="formTunjanganEditV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Info Banner -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-edit"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Perbarui Tunjangan ({{ $tunjangan->kode_tunjangan }})</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Sesuaikan nominal besaran per jenis tunjangan atau tanggal mulai berlakunya untuk karyawan ini.
            </p>
        </div>
    </div>

    <!-- 1. Karyawan (Readonly / Disabled Select) -->
    <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700">
            Karyawan
        </label>
        <div class="relative">
            <select name="nik_disabled" id="edit_tunjangan_nik" class="w-full px-3.5 py-2.5 text-xs bg-slate-100/80 border border-slate-200 rounded-xl text-slate-600 font-bold outline-none cursor-not-allowed select2TunjanganModalEdit" disabled>
                @foreach ($karyawan as $d)
                    <option value="{{ $d->nik }}" {{ $tunjangan->nik == $d->nik ? 'selected' : '' }}>
                        {{ $d->nik_show ?? $d->nik }} - {{ $d->nama_karyawan }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- 2. Komponen Tunjangan (Repeater Grid) -->
    <div class="bg-slate-50/70 border border-slate-200/90 rounded-2xl p-3.5 sm:p-4 space-y-3">
        <div class="flex items-center gap-2 border-b border-slate-200/70 pb-2.5">
            <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">
                <i class="ti ti-cash"></i>
            </div>
            <span class="text-xs font-black text-slate-800">
                Rincian Nominal Komponen Tunjangan <span class="text-rose-500">*</span>
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach ($jenis_tunjangan as $d)
                <input type="hidden" name="kode_jenis_tunjangan[]" value="{{ $d->kode_jenis_tunjangan }}">
                <div class="space-y-1.5">
                    <label for="edit_jumlah_{{ $d->kode_jenis_tunjangan }}" class="block text-xs font-bold text-slate-700">
                        {{ $d->jenis_tunjangan }}
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs pointer-events-none z-10">
                            Rp
                        </span>
                        <input type="text" 
                               id="edit_jumlah_{{ $d->kode_jenis_tunjangan }}" 
                               name="jumlah[]" 
                               value="{{ $d->jumlah != '' ? formatAngka($d->jumlah) : '0' }}"
                               placeholder="0"
                               autocomplete="off"
                               class="w-full h-9.5 pl-10 pr-3.5 text-xs font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition modal-money-input input-jumlah-tunjangan-edit text-right" 
                               required>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_edit_tunjangan_jumlah"></div>
    </div>

    <!-- 3. Tanggal Berlaku -->
    <div class="space-y-1.5">
        <label for="edit_tunjangan_tanggal_berlaku" class="block text-xs font-bold text-slate-700">
            Tanggal Berlaku <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                <i class="ti ti-calendar text-base"></i>
            </span>
            <input type="text" 
                   id="edit_tunjangan_tanggal_berlaku" 
                   name="tanggal_berlaku" 
                   value="{{ $tunjangan->tanggal_berlaku }}" 
                   placeholder="Pilih Tanggal Berlaku"
                   autocomplete="off"
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer modal-flatpickr-date" 
                   required>
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_edit_tunjangan_tanggal_berlaku"></div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                onclick="$('#modalTunjangan').addClass('hidden'); $('body').removeClass('overflow-hidden');">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitTunjanganEditV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Perbarui Tunjangan</span>
        </button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('formTunjanganEditV2');
        const tanggalInput = document.getElementById('edit_tunjangan_tanggal_berlaku');

        $(form).on('submit', function(e) {
            const tanggal_berlaku = $(tanggalInput).val();
            let hasError = false;

            // Reset error states
            $('.error-feedback').addClass('hidden').text('');
            $(tanggalInput).removeClass('border-rose-500');
            $('.input-jumlah-tunjangan-edit').removeClass('border-rose-500');

            let anyEmpty = false;
            $('.input-jumlah-tunjangan-edit').each(function() {
                if ($(this).val().trim() === '') {
                    anyEmpty = true;
                    $(this).addClass('border-rose-500');
                }
            });

            if (anyEmpty) {
                $('#err_edit_tunjangan_jumlah').text('Semua nominal tunjangan wajib diisi (isi 0 jika tidak ada)').removeClass('hidden');
                hasError = true;
            }

            if (!tanggal_berlaku) {
                $('#err_edit_tunjangan_tanggal_berlaku').text('Tanggal berlaku wajib diisi').removeClass('hidden');
                $(tanggalInput).addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitTunjanganEditV2');
            $btn.prop('disabled', true).html(`
                <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
                <span>Menyimpan...</span>
            `);
        });
    })();
</script>
