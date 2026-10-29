@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('pinjaman.update', Crypt::encrypt($pinjaman->id)) }}" method="POST" id="formPinjamanEditV2" class="space-y-4" novalidate>
    @csrf
    @method('PUT')

    <!-- Hidden NIK Karyawan -->
    <input type="hidden" name="nik" id="v2_edit_nik" value="{{ $pinjaman->nik }}">

    <!-- Banner Info -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-info-circle"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Edit Data Pinjaman: {{ $pinjaman->no_pinjaman }}</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Anda dapat mengubah tanggal pengajuan, nominal, tenor cicilan, atau mengatur ulang jadwal angsuran bulanan.
            </p>
        </div>
    </div>

    <!-- 1. Karyawan (Readonly) -->
    <div class="space-y-1.5">
        <label class="block text-xs font-bold text-slate-700">Karyawan</label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                <i class="ti ti-user text-base"></i>
            </span>
            <input type="text" 
                   value="{{ $pinjaman->nik }} - {{ $pinjaman->karyawan->nama_karyawan ?? '-' }}" 
                   readonly disabled
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-700 bg-slate-100/80 border border-slate-200 rounded-xl cursor-not-allowed shadow-2xs">
        </div>
    </div>

    <!-- 2. Tanggal Pinjaman -->
    <div class="space-y-1.5">
        <label for="v2_edit_tanggal_pinjaman" class="block text-xs font-bold text-slate-700">
            Tanggal Pinjaman <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                <i class="ti ti-calendar text-base"></i>
            </span>
            <input type="text" name="tanggal_pinjaman" id="v2_edit_tanggal_pinjaman" 
                   value="{{ $pinjaman->tanggal_pinjaman }}"
                   placeholder="Pilih Tanggal Pinjaman" 
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs flatpickr-pinjaman cursor-pointer">
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_edit_tanggal_pinjaman"></div>
    </div>

    <!-- 3. Jumlah Pinjaman & Tenor (Bulan) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="space-y-1.5">
            <label for="v2_edit_jumlah_pinjaman" class="block text-xs font-bold text-slate-700">
                Jumlah Pinjaman (Rp) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none font-bold text-xs">
                    Rp
                </span>
                <input type="text" name="jumlah_pinjaman" id="v2_edit_jumlah_pinjaman" 
                       value="{{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}"
                       placeholder="0" autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm text-right font-extrabold text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs money-pinjaman">
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_edit_jumlah_pinjaman"></div>
        </div>

        <div class="space-y-1.5">
            <label for="v2_edit_jumlah_cicilan" class="block text-xs font-bold text-slate-700">
                Tenor / Jangka Waktu <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input type="number" name="jumlah_cicilan" id="v2_edit_jumlah_cicilan" 
                       value="{{ $pinjaman->jumlah_cicilan }}"
                       min="1" max="120" placeholder="Contoh: 10"
                       class="w-full h-10 pl-3.5 pr-14 text-xs sm:text-sm font-bold text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs">
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">
                    Bulan
                </span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_edit_jumlah_cicilan"></div>
        </div>
    </div>

    <!-- 4. Mulai Cicilan (Bulan & Tahun) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="space-y-1.5">
            <label for="v2_edit_bulan_mulai_cicilan" class="block text-xs font-bold text-slate-700">
                Mulai Cicilan (Bulan) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-calendar-month text-base"></i>
                </span>
                <select name="bulan_mulai_cicilan" id="v2_edit_bulan_mulai_cicilan"
                        class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs cursor-pointer">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ $pinjaman->bulan_mulai_cicilan == $m ? 'selected' : '' }}>{{ getNamabulan($m) }}</option>
                    @endfor
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_edit_bulan_mulai_cicilan"></div>
        </div>

        <div class="space-y-1.5">
            <label for="v2_edit_tahun_mulai_cicilan" class="block text-xs font-bold text-slate-700">
                Mulai Cicilan (Tahun) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-calendar text-base"></i>
                </span>
                <select name="tahun_mulai_cicilan" id="v2_edit_tahun_mulai_cicilan"
                        class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs cursor-pointer">
                    @for($t=date('Y')-1; $t<=date('Y')+3; $t++)
                        <option value="{{ $t }}" {{ $pinjaman->tahun_mulai_cicilan == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endfor
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_edit_tahun_mulai_cicilan"></div>
        </div>
    </div>

    <!-- 5. Keterangan Pinjaman -->
    <div class="space-y-1.5">
        <label for="v2_edit_keterangan" class="block text-xs font-bold text-slate-700">Keterangan / Tujuan Pinjaman (Opsional)</label>
        <textarea name="keterangan" id="v2_edit_keterangan" rows="2" 
                  placeholder="Contoh: Kebutuhan mendesak / renovasi rumah / dll"
                  class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs">{{ $pinjaman->keterangan }}</textarea>
    </div>

    <!-- 6. Preview Simulation Button & Result Container -->
    <div class="pt-2">
        <button type="button" id="btnPreviewCicilanEditV2" 
                class="w-full py-2.5 px-4 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 text-xs font-extrabold rounded-xl transition active:scale-95 cursor-pointer shadow-2xs flex items-center justify-center gap-2">
            <i class="ti ti-layout-grid text-sm"></i>
            <span>Sesuaikan Ulang Simulasi Rencana Cicilan</span>
        </button>

        <div id="loadPreviewCicilanEditV2" class="mt-3">
            <!-- Current Installments Table -->
            <div class="rounded-2xl border border-slate-200/90 overflow-hidden shadow-2xs">
                <div class="px-4 py-3 flex items-center justify-between text-white border-0"
                     style="background-color: {{ $themeColor1 }};">
                    <h5 class="text-xs font-black text-white flex items-center gap-2">
                        <i class="ti ti-list-numbers"></i>
                        <span>Rencana Cicilan Saat Ini</span>
                    </h5>
                    <span class="text-[10.5px] font-bold bg-white/20 text-white px-2 py-0.5 rounded-full border border-white/25">Data Tersimpan</span>
                </div>
                <div class="overflow-x-auto w-full" style="background-color: {{ $themeColor1 }};">
                    <table class="w-full text-xs text-left border-0 border-collapse">
                        <thead class="text-white font-bold uppercase tracking-wider text-[11px] border-0 border-t-0 border-b border-black/15"
                               style="background-color: {{ $themeColor1 }};">
                            <tr class="border-0 border-t-0 text-white/90">
                                <th class="py-2.5 px-4 text-center w-16 text-white/90 border-0 border-t-0">No</th>
                                <th class="py-2.5 px-4 text-white/90 border-0 border-t-0">Periode Bulan / Tahun</th>
                                <th class="py-2.5 px-4 text-right text-white/90 border-0 border-t-0">Nominal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($pinjaman->rencana_cicilan as $rc)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2 px-4 text-center font-bold text-slate-700">{{ $rc->cicilan_ke }}</td>
                                <td class="py-2 px-4 font-semibold text-slate-800">
                                    {{ getNamabulan($rc->bulan) }} {{ $rc->tahun }}
                                    <input type="hidden" name="rencana_bulan[]" value="{{ $rc->bulan }}">
                                    <input type="hidden" name="rencana_tahun[]" value="{{ $rc->tahun }}">
                                </td>
                                <td class="py-2 px-4 text-right">
                                    <input type="text" name="rencana_nominal[]" 
                                           class="w-36 px-2.5 py-1.5 text-xs text-right font-bold bg-slate-50 border border-slate-300 rounded-lg text-slate-800 focus:outline-none focus:border-emerald-600 rencana_nominal mny shadow-2xs" 
                                           value="{{ number_format($rc->jumlah_cicilan, 0, ',', '.') }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Modal Action -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" class="btnCloseModalPinjaman px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitPinjamanEditV2"
                class="px-5 py-2.5 text-white font-bold rounded-xl text-xs shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, var(--theme-color-1, {{ $themeColor1 }}), var(--theme-color-2, {{ $themeColor2 }}));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Update Pinjaman</span>
        </button>
    </div>
</form>

<script>
    $(function() {
        if (typeof $.fn.maskMoney !== 'undefined') {
            $(".money-pinjaman, .mny").maskMoney({
                thousands: '.', decimal: ',', precision: 0, allowZero: true
            });
        }

        if (typeof flatpickr !== 'undefined') {
            $(".flatpickr-pinjaman").flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });
        }

        // Preview Cicilan Edit
        $('#btnPreviewCicilanEditV2').click(function() {
            let jumlah = $("#v2_edit_jumlah_pinjaman").val().replace(/\./g, '');
            let tenor = $("#v2_edit_jumlah_cicilan").val();
            let tanggal = $("#v2_edit_tanggal_pinjaman").val();
            let nik = $("#v2_edit_nik").val();

            if (!nik || !tanggal || !jumlah || jumlah == 0 || !tenor || tenor == 0) {
                Swal.fire({
                    title: 'Data Belum Lengkap',
                    text: 'Silakan lengkapi tanggal, jumlah pinjaman, dan tenor terlebih dahulu.',
                    icon: 'warning',
                    confirmButtonColor: '{{ $themeColor1 }}',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-slate-100',
                        confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold shadow-xs'
                    }
                });
                return false;
            }

            $('#loadPreviewCicilanEditV2').html(`
                <div class="py-6 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs">Menghitung ulang simulasi cicilan...</p>
                </div>
            `);

            $.ajax({
                type: 'POST',
                url: '{{ route("pinjaman.preview") }}',
                data: {
                    _token: "{{ csrf_token() }}",
                    jumlah_pinjaman: jumlah,
                    tenor: tenor,
                    bulan_mulai_cicilan: $("#v2_edit_bulan_mulai_cicilan").val(),
                    tahun_mulai_cicilan: $("#v2_edit_tahun_mulai_cicilan").val()
                },
                success: function(response) {
                    $('#loadPreviewCicilanEditV2').html(response);
                },
                error: function() {
                    $('#loadPreviewCicilanEditV2').html(`
                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            Gagal membuat preview rencana cicilan.
                        </div>
                    `);
                }
            });
        });

        // Form Submit Validation
        $('#formPinjamanEditV2').on('submit', function(e) {
            const tanggal = $('#v2_edit_tanggal_pinjaman').val();
            const jumlahStr = $('#v2_edit_jumlah_pinjaman').val().replace(/\./g, '');
            const jumlah = parseInt(jumlahStr) || 0;
            const tenor = parseInt($('#v2_edit_jumlah_cicilan').val()) || 0;
            let hasError = false;

            // Reset error feedback
            $('#formPinjamanEditV2 .error-feedback').addClass('hidden').text('');
            $('#formPinjamanEditV2 input, #formPinjamanEditV2 select').removeClass('border-rose-500');

            if (!tanggal) {
                $('#err_v2_edit_tanggal_pinjaman').text('Tanggal pinjaman wajib diisi').removeClass('hidden');
                $('#v2_edit_tanggal_pinjaman').addClass('border-rose-500');
                hasError = true;
            }

            if (!jumlah || jumlah <= 0) {
                $('#err_v2_edit_jumlah_pinjaman').text('Jumlah pinjaman minimal Rp 1').removeClass('hidden');
                $('#v2_edit_jumlah_pinjaman').addClass('border-rose-500');
                hasError = true;
            }

            if (!tenor || tenor <= 0) {
                $('#err_v2_edit_jumlah_cicilan').text('Tenor minimal 1 bulan').removeClass('hidden');
                $('#v2_edit_jumlah_cicilan').addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitPinjamanEditV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Memperbarui...</span>');
        });
    });
</script>
