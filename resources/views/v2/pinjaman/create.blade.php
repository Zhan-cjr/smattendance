@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('pinjaman.store') }}" method="POST" id="formPinjamanV2" class="space-y-4" novalidate>
    @csrf

    <!-- Hidden NIK Karyawan -->
    <input type="hidden" name="nik" id="v2_nik">

    <!-- Banner Info -->
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
            <i class="ti ti-info-circle"></i>
        </div>
        <div>
            <h5 class="text-xs font-bold text-slate-800">Form Pengajuan Pinjaman Karyawan</h5>
            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                Pilih karyawan pemohon, tentukan nominal pinjaman & jumlah tenor cicilan bulanan. Anda dapat melakukan simulasi rencana angsuran sebelum menyimpan.
            </p>
        </div>
    </div>

    <!-- 1. Pilih Karyawan (Lookup) -->
    <div class="space-y-1.5">
        <label for="v2_nama_karyawan" class="block text-xs font-bold text-slate-700">
            Pilih Karyawan <span class="text-rose-500">*</span>
        </label>
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-user text-base"></i>
                </span>
                <input type="text" id="v2_nama_karyawan" 
                       placeholder="Klik tombol cari untuk memilih karyawan..." 
                       readonly
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm bg-slate-100/80 border border-slate-300 rounded-xl text-slate-800 font-bold shadow-2xs cursor-not-allowed">
            </div>
            <button type="button" id="btnLookupKaryawanV2" 
                    class="h-10 px-4 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-2xs transition active:scale-95 cursor-pointer flex items-center gap-1.5 shrink-0">
                <i class="ti ti-search text-sm"></i>
                <span>Cari</span>
            </button>
        </div>
        <div class="text-[10.5px] text-slate-400">Pilih karyawan aktif peminjam</div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_nik"></div>
    </div>

    <!-- 2. Tanggal Pinjaman -->
    <div class="space-y-1.5">
        <label for="v2_tanggal_pinjaman" class="block text-xs font-bold text-slate-700">
            Tanggal Pinjaman <span class="text-rose-500">*</span>
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                <i class="ti ti-calendar text-base"></i>
            </span>
            <input type="text" name="tanggal_pinjaman" id="v2_tanggal_pinjaman" 
                   value="{{ date('Y-m-d') }}"
                   placeholder="Pilih Tanggal Pinjaman" 
                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs flatpickr-pinjaman cursor-pointer">
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_tanggal_pinjaman"></div>
    </div>

    <!-- 3. Jumlah Pinjaman & Tenor (Bulan) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="space-y-1.5">
            <label for="v2_jumlah_pinjaman" class="block text-xs font-bold text-slate-700">
                Jumlah Pinjaman (Rp) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none font-bold text-xs">
                    Rp
                </span>
                <input type="text" name="jumlah_pinjaman" id="v2_jumlah_pinjaman" 
                       placeholder="0" autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm text-right font-extrabold text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs money-pinjaman">
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_jumlah_pinjaman"></div>
        </div>

        <div class="space-y-1.5">
            <label for="v2_jumlah_cicilan" class="block text-xs font-bold text-slate-700">
                Tenor / Jangka Waktu <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input type="number" name="jumlah_cicilan" id="v2_jumlah_cicilan" 
                       min="1" max="120" placeholder="Contoh: 10"
                       class="w-full h-10 pl-3.5 pr-14 text-xs sm:text-sm font-bold text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs">
                <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">
                    Bulan
                </span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_jumlah_cicilan"></div>
        </div>
    </div>

    <!-- 4. Mulai Cicilan (Bulan & Tahun) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="space-y-1.5">
            <label for="v2_bulan_mulai_cicilan" class="block text-xs font-bold text-slate-700">
                Mulai Cicilan (Bulan) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-calendar-month text-base"></i>
                </span>
                <select name="bulan_mulai_cicilan" id="v2_bulan_mulai_cicilan"
                        class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs cursor-pointer">
                    @for($m=1; $m<=12; $m++)
                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ getNamabulan($m) }}</option>
                    @endfor
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_bulan_mulai_cicilan"></div>
        </div>

        <div class="space-y-1.5">
            <label for="v2_tahun_mulai_cicilan" class="block text-xs font-bold text-slate-700">
                Mulai Cicilan (Tahun) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-calendar text-base"></i>
                </span>
                <select name="tahun_mulai_cicilan" id="v2_tahun_mulai_cicilan"
                        class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs cursor-pointer">
                    @for($t=date('Y')-1; $t<=date('Y')+2; $t++)
                        <option value="{{ $t }}" {{ date('Y') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endfor
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_tahun_mulai_cicilan"></div>
        </div>
    </div>

    <!-- 5. Keterangan Pinjaman -->
    <div class="space-y-1.5">
        <label for="v2_keterangan" class="block text-xs font-bold text-slate-700">Keterangan / Tujuan Pinjaman (Opsional)</label>
        <textarea name="keterangan" id="v2_keterangan" rows="2" 
                  placeholder="Contoh: Kebutuhan mendesak / renovasi rumah / dll"
                  class="w-full px-3.5 py-2.5 text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition shadow-2xs"></textarea>
    </div>

    <!-- 6. Preview Simulation Button & Result Container -->
    <div class="pt-2">
        <button type="button" id="btnPreviewCicilanV2" 
                class="w-full py-2.5 px-4 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 text-xs font-extrabold rounded-xl transition active:scale-95 cursor-pointer shadow-2xs flex items-center justify-center gap-2">
            <i class="ti ti-layout-grid text-sm"></i>
            <span>Generate & Preview Simulasi Rencana Cicilan</span>
        </button>

        <div id="loadPreviewCicilanV2" class="mt-3">
            <!-- Render via AJAX -->
        </div>
    </div>

    <!-- Footer Modal Action -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" class="btnCloseModalPinjaman px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitPinjamanV2"
                class="px-5 py-2.5 text-white font-bold rounded-xl text-xs shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, var(--theme-color-1, {{ $themeColor1 }}), var(--theme-color-2, {{ $themeColor2 }}));">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Pinjaman</span>
        </button>
    </div>
</form>

<script>
    $(function() {
        if (typeof $.fn.maskMoney !== 'undefined') {
            $(".money-pinjaman").maskMoney({
                thousands: '.', decimal: ',', precision: 0, allowZero: true
            });
        }

        if (typeof flatpickr !== 'undefined') {
            $(".flatpickr-pinjaman").flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });
        }

        // Open Lookup Karyawan Modal
        $('#btnLookupKaryawanV2').click(function() {
            $('#modalLookupKaryawanV2').removeClass('hidden');
            $('#loadLookupKaryawanV2').html(`
                <div class="py-12 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs font-semibold">Memuat daftar karyawan...</p>
                </div>
            `);
            $('#loadLookupKaryawanV2').load("{{ route('karyawan.getkaryawantable') }}");
        });

        // Preview Cicilan
        $('#btnPreviewCicilanV2').click(function() {
            let jumlah = $("#v2_jumlah_pinjaman").val().replace(/\./g, '');
            let tenor = $("#v2_jumlah_cicilan").val();
            let tanggal = $("#v2_tanggal_pinjaman").val();
            let nik = $("#v2_nik").val();

            if (!nik || !tanggal || !jumlah || jumlah == 0 || !tenor || tenor == 0) {
                Swal.fire({
                    title: 'Data Belum Lengkap',
                    text: 'Silakan pilih karyawan, isi tanggal, jumlah pinjaman, dan tenor terlebih dahulu.',
                    icon: 'warning',
                    confirmButtonColor: '{{ $themeColor1 }}',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-slate-100',
                        confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold shadow-xs'
                    }
                });
                return false;
            }

            $('#loadPreviewCicilanV2').html(`
                <div class="py-6 text-center text-slate-400">
                    <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-slate-300 border-t-emerald-600 mb-2"></div>
                    <p class="text-xs">Menghitung simulasi cicilan...</p>
                </div>
            `);

            $.ajax({
                type: 'POST',
                url: '{{ route("pinjaman.preview") }}',
                data: {
                    _token: "{{ csrf_token() }}",
                    jumlah_pinjaman: jumlah,
                    tenor: tenor,
                    bulan_mulai_cicilan: $("#v2_bulan_mulai_cicilan").val(),
                    tahun_mulai_cicilan: $("#v2_tahun_mulai_cicilan").val()
                },
                success: function(response) {
                    $('#loadPreviewCicilanV2').html(response);
                },
                error: function() {
                    $('#loadPreviewCicilanV2').html(`
                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            Gagal membuat preview rencana cicilan.
                        </div>
                    `);
                }
            });
        });

        // Form Submit Validation
        $('#formPinjamanV2').on('submit', function(e) {
            const nik = $('#v2_nik').val();
            const tanggal = $('#v2_tanggal_pinjaman').val();
            const jumlahStr = $('#v2_jumlah_pinjaman').val().replace(/\./g, '');
            const jumlah = parseInt(jumlahStr) || 0;
            const tenor = parseInt($('#v2_jumlah_cicilan').val()) || 0;
            let hasError = false;

            // Reset error feedback
            $('#formPinjamanV2 .error-feedback').addClass('hidden').text('');
            $('#formPinjamanV2 input, #formPinjamanV2 select').removeClass('border-rose-500');

            if (!nik) {
                $('#err_v2_nik').text('Silakan pilih karyawan peminjam').removeClass('hidden');
                $('#v2_nama_karyawan').addClass('border-rose-500');
                hasError = true;
            }

            if (!tanggal) {
                $('#err_v2_tanggal_pinjaman').text('Tanggal pinjaman wajib diisi').removeClass('hidden');
                $('#v2_tanggal_pinjaman').addClass('border-rose-500');
                hasError = true;
            }

            if (!jumlah || jumlah <= 0) {
                $('#err_v2_jumlah_pinjaman').text('Jumlah pinjaman minimal Rp 1').removeClass('hidden');
                $('#v2_jumlah_pinjaman').addClass('border-rose-500');
                hasError = true;
            }

            if (!tenor || tenor <= 0) {
                $('#err_v2_jumlah_cicilan').text('Tenor minimal 1 bulan').removeClass('hidden');
                $('#v2_jumlah_cicilan').addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitPinjamanV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Menyimpan...</span>');
        });
    });
</script>
