@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('jenisreimbursement.storekaryawan', Crypt::encrypt($jenis->id)) }}" method="POST" id="formAddKaryawanEnrollV2" class="space-y-4" novalidate>
    @csrf

    <!-- Date Range & Override Plafon Config -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="space-y-1.5">
            <label for="v2_tanggal_mulai_enroll" class="block text-xs font-bold text-slate-700">
                Tanggal Mulai Berlaku <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-calendar text-base"></i>
                </span>
                <input type="text" name="tanggal_mulai" id="v2_tanggal_mulai_enroll" 
                       value="{{ date('Y-m-d') }}"
                       placeholder="Pilih Tanggal Mulai"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition flatpickr-enroll cursor-pointer">
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_tanggal_mulai_enroll"></div>
        </div>

        <div class="space-y-1.5">
            <label for="v2_tanggal_selesai_enroll" class="block text-xs font-bold text-slate-700">
                Tanggal Berakhir (Opsional)
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-calendar text-base"></i>
                </span>
                <input type="text" name="tanggal_selesai" id="v2_tanggal_selesai_enroll" 
                       placeholder="Kosongkan jika selamanya"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition flatpickr-enroll cursor-pointer">
            </div>
        </div>
    </div>

    <!-- Override Plafon -->
    <div class="space-y-1.5">
        <label for="v2_batas_override" class="block text-xs font-bold text-slate-700">
            Override Plafon Nominal Khusus (Opsional)
        </label>
        <div class="relative">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none font-bold text-xs">Rp</span>
            <input type="text" name="batas_nominal_override" id="v2_batas_override" 
                   placeholder="Isi jika berbeda dengan plafon global" autocomplete="off"
                   class="w-full h-10 pl-9 pr-3.5 text-xs sm:text-sm text-right font-extrabold text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition money-enroll">
        </div>
        <p class="text-[10.5px] text-slate-400">
            Biarkan kosong untuk mengikuti batas plafon default kategori (Rp {{ number_format($jenis->batas_nominal, 0, ',', '.') }}).
        </p>
    </div>

    <!-- Filter Multi-Select Karyawan -->
    <div class="pt-2 border-t border-slate-100 space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-800">Pilih Karyawan Yang Didaftarkan</span>
            <span class="text-[11px] font-bold text-slate-500" id="selectedCountV2">0 dipilih</span>
        </div>

        <!-- Filter Row -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <i class="ti ti-search text-xs"></i>
                </span>
                <input type="text" id="filterSearchEnrollV2" placeholder="Cari nama / NIK..." 
                       class="w-full h-9 pl-8 pr-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-200 rounded-xl outline-none focus:border-emerald-600 transition">
            </div>

            <div>
                <select id="filterDeptEnrollV2" class="w-full h-9 px-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-200 rounded-xl outline-none focus:border-emerald-600 transition cursor-pointer">
                    <option value="">Semua Departemen</option>
                    @foreach($departemen as $d)
                        <option value="{{ $d->kode_dept }}">{{ $d->nama_dept }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select id="filterCabangEnrollV2" class="w-full h-9 px-3 text-xs bg-slate-50 hover:bg-slate-100/60 focus:bg-white border border-slate-200 rounded-xl outline-none focus:border-emerald-600 transition cursor-pointer">
                    <option value="">Semua Cabang</option>
                    @foreach($cabang as $c)
                        <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Scrollable Modern Table Selection with Unified Solid Card Header & Table Header -->
        <div class="border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
            <!-- Solid Card Header -->
            <div class="px-4 py-2.5 flex items-center justify-between gap-2 text-white border-0"
                 style="background-color: {{ $themeColor1 }};">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                        <i class="ti ti-users"></i>
                    </div>
                    <h4 class="text-xs font-bold text-white tracking-tight">Daftar Karyawan Belum Terdaftar</h4>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-white/20 text-white border border-white/30 shadow-2xs backdrop-blur-xs">
                    {{ count($karyawan) }} Pegawai Tersedia
                </span>
            </div>

            <!-- Table with Matching Solid Header -->
            <div class="overflow-x-auto max-h-[280px] custom-scrollbar" style="background-color: {{ $themeColor1 }};">
                <table class="w-full text-left text-xs border-0 border-collapse whitespace-nowrap">
                    <thead class="text-white font-bold uppercase tracking-wider text-[10.5px] sticky top-0 z-10 border-0 border-b border-black/15 shadow-2xs"
                           style="background-color: {{ $themeColor1 }};">
                        <tr class="border-0 text-white/90">
                            <th class="py-2.5 px-3.5 text-center w-10 border-0">
                                <input type="checkbox" id="checkAllKaryawanEnrollV2" class="w-4 h-4 text-emerald-600 rounded border-white/40 focus:ring-emerald-400 cursor-pointer">
                            </th>
                            <th class="py-2.5 px-3 border-0 text-white/90">NIK</th>
                            <th class="py-2.5 px-3 border-0 text-white/90">Nama Karyawan</th>
                            <th class="py-2.5 px-3 border-0 text-white/90">Departemen</th>
                            <th class="py-2.5 px-3 border-0 text-white/90">Cabang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white" id="listKaryawanEnrollBodyV2">
                        @forelse ($karyawan as $k)
                            <tr class="hover:bg-emerald-50/40 transition-colors rowKaryawanEnrollV2 cursor-pointer"
                                data-name="{{ strtolower($k->nama_karyawan) }}" 
                                data-nik="{{ strtolower($k->nik) }}" 
                                data-nik-show="{{ strtolower($k->nik_show) }}" 
                                data-dept="{{ $k->kode_dept }}" 
                                data-cabang="{{ $k->kode_cabang }}">
                                <td class="py-2.5 px-3.5 text-center">
                                    <input type="checkbox" name="nik[]" value="{{ $k->nik }}" class="checkKaryawanEnrollV2 w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                </td>
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-700">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200 text-[11px]">
                                        {{ $k->nik_show ?: $k->nik }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="relative shrink-0">
                                            @if (!empty($k->foto) && Storage::disk('public')->exists('/karyawan/' . $k->foto))
                                                <img src="{{ getfotoKaryawan($k->foto) }}" alt="{{ $k->nama_karyawan }}" 
                                                     class="w-7 h-8 rounded-lg object-cover border border-slate-200 shadow-2xs">
                                            @else
                                                <div class="w-7 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 font-bold text-xs shadow-2xs">
                                                    {{ strtoupper(substr($k->nama_karyawan, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <span class="font-extrabold text-slate-900">{{ $k->nama_karyawan }}</span>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-slate-600 font-medium">
                                    @if(!empty($k->nama_dept))
                                        <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold bg-sky-50 text-sky-700 border border-sky-200/70">
                                            {{ $k->nama_dept }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-slate-600 font-medium">
                                    @if(!empty($k->nama_cabang))
                                        <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-700 border border-slate-200/80 uppercase">
                                            {{ $k->nama_cabang }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 bg-white">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-lg">
                                        <i class="ti ti-check"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700">Semua Karyawan Telah Terdaftar</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Seluruh karyawan aktif telah terdaftar pada skema jenis reimbursement ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_v2_select_karyawan"></div>
    </div>

    <!-- Action Buttons -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" class="btnCloseModalEnroll px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitEnrollV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, {{ $themeColor1 }}), var(--theme-color-2, {{ $themeColor2 }}));">
            <i class="ti ti-user-plus text-sm"></i>
            <span>Daftarkan Karyawan Terpilih</span>
        </button>
    </div>
</form>

<script>
    $(function() {
        if (typeof $.fn.maskMoney !== 'undefined') {
            $('#formAddKaryawanEnrollV2 .money-enroll').maskMoney({
                thousands: '.', decimal: ',', precision: 0, allowZero: true
            });
        }

        if (typeof flatpickr !== 'undefined') {
            $('#formAddKaryawanEnrollV2 .flatpickr-enroll').flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });
        }

        function updateSelectedCount() {
            let totalChecked = $('.checkKaryawanEnrollV2:checked').length;
            $('#selectedCountV2').text(totalChecked + ' dipilih');
        }

        $('#checkAllKaryawanEnrollV2').on('change', function() {
            let isChecked = $(this).prop('checked');
            $('.rowKaryawanEnrollV2:visible .checkKaryawanEnrollV2').prop('checked', isChecked);
            updateSelectedCount();
        });

        $(document).on('change', '.checkKaryawanEnrollV2', function() {
            updateSelectedCount();
        });

        // Click on row toggles checkbox
        $('.rowKaryawanEnrollV2').on('click', function(e) {
            if ($(e.target).is('input[type="checkbox"]')) return;
            let $chk = $(this).find('.checkKaryawanEnrollV2');
            $chk.prop('checked', !$chk.prop('checked'));
            updateSelectedCount();
        });

        // Search and filter rows
        function applyEnrollFilters() {
            let search = $('#filterSearchEnrollV2').val().toLowerCase().trim();
            let dept = $('#filterDeptEnrollV2').val();
            let cabang = $('#filterCabangEnrollV2').val();

            $('.rowKaryawanEnrollV2').each(function() {
                let name = String($(this).data('name') || '');
                let nik = String($(this).data('nik') || '');
                let nikShow = String($(this).data('nik-show') || '');
                let rowDept = String($(this).data('dept') || '');
                let rowCabang = String($(this).data('cabang') || '');

                let matchSearch = !search || name.includes(search) || nik.includes(search) || nikShow.includes(search);
                let matchDept = !dept || rowDept === dept;
                let matchCabang = !cabang || rowCabang === cabang;

                if (matchSearch && matchDept && matchCabang) {
                    $(this).show();
                } else {
                    $(this).hide();
                    $(this).find('.checkKaryawanEnrollV2').prop('checked', false);
                }
            });
            updateSelectedCount();
        }

        $('#filterSearchEnrollV2').on('keyup', applyEnrollFilters);
        $('#filterDeptEnrollV2, #filterCabangEnrollV2').on('change', applyEnrollFilters);

        // Submit validation
        $('#formAddKaryawanEnrollV2').on('submit', function(e) {
            let totalChecked = $('.checkKaryawanEnrollV2:checked').length;
            let tglMulai = $('#v2_tanggal_mulai_enroll').val();
            let hasError = false;

            $('#formAddKaryawanEnrollV2 .error-feedback').addClass('hidden').text('');
            $('#formAddKaryawanEnrollV2 input').removeClass('border-rose-500');

            if (!tglMulai) {
                $('#err_v2_tanggal_mulai_enroll').text('Tanggal mulai berlaku wajib diisi').removeClass('hidden');
                $('#v2_tanggal_mulai_enroll').addClass('border-rose-500');
                hasError = true;
            }

            if (totalChecked === 0) {
                $('#err_v2_select_karyawan').text('Silakan pilih minimal satu karyawan untuk didaftarkan').removeClass('hidden');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                return false;
            }

            const $btn = $('#btnSubmitEnrollV2');
            $btn.prop('disabled', true).html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Mendaftarkan...</span>');
        });
    });
</script>
