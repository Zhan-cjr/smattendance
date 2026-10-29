@extends('v2.layouts.app')

@section('titlepage', 'Buat Pengajuan Reimbursement')
@section('page_title', 'Buat Pengajuan Reimbursement')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $userRole = auth()->user()->getRoleNames()->first();
@endphp

<div class="space-y-5">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle with Back Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('reimbursement.index') }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('reimbursement.index') }}" class="hover:text-emerald-600 transition-colors">Reimbursement</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Form Pengajuan</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 mt-0.5">
                    <i class="ti ti-receipt-refund text-2xl" style="color: {{ $themeColor1 }};"></i>
                    <span>Buat Pengajuan Reimbursement</span>
                </h1>
            </div>
        </div>

        <!-- Breadcrumbs Navigation -->
        <div class="flex flex-col md:items-end gap-2.5">
            <nav class="flex items-center text-xs text-slate-400 font-medium">
                <a href="{{ route('dashboard.index') }}" class="hover:text-slate-700 transition flex items-center gap-1">
                    <i class="ti ti-home text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <a href="{{ route('reimbursement.index') }}" class="hover:text-slate-700 transition">Reimbursement</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Buat Pengajuan</span>
            </nav>
        </div>
    </div>

    <!-- ================= 2. FORM PENGAJUAN ================= -->
    <form action="{{ route('reimbursement.store') }}" method="POST" enctype="multipart/form-data" id="formReimbursementV2" class="space-y-5" novalidate>
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            
            <!-- Left Column: Master Form Data (4 cols) -->
            <div class="lg:col-span-4 space-y-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs space-y-4">
                    
                    <!-- Card Title -->
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white shadow-2xs"
                             style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-file-description text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900">Informasi Pengajuan</h3>
                            <p class="text-[11px] text-slate-500">Data pemohon & identitas pengajuan</p>
                        </div>
                    </div>

                    <!-- Tanggal Pengajuan -->
                    <div class="space-y-1.5">
                        <label for="tanggal_pengajuan" class="block text-xs font-bold text-slate-700">
                            Tanggal Pengajuan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                                <i class="ti ti-calendar text-base"></i>
                            </span>
                            <input type="text" name="tanggal_pengajuan" id="tanggal_pengajuan" 
                                   value="{{ date('Y-m-d') }}"
                                   placeholder="YYYY-MM-DD"
                                   class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition flatpickr-reimburse cursor-pointer">
                        </div>
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_tanggal_pengajuan"></div>
                    </div>

                    <!-- Pilih Karyawan (Jika Admin) / Readonly (Jika Karyawan) -->
                    @if($nik)
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Pemohon Reimbursement</label>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                                <div class="font-extrabold text-slate-900 text-xs sm:text-sm">{{ $karyawan_selected->nama_karyawan }}</div>
                                <div class="text-[11px] font-mono text-slate-500 mt-0.5">{{ $karyawan_selected->nik_show ?: $karyawan_selected->nik }}</div>
                            </div>
                            <input type="hidden" name="nik" id="nik" value="{{ $nik }}">
                        </div>
                    @else
                        <div class="space-y-1.5">
                            <label for="nik" class="block text-xs font-bold text-slate-700">
                                Pilih Karyawan Pemohon <span class="text-rose-500">*</span>
                            </label>
                            <select name="nik" id="nik" class="w-full h-10 px-3 text-xs bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer select2">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($karyawan as $k)
                                    <option value="{{ $k->nik }}">{{ $k->nik_show ?: $k->nik }} - {{ $k->nama_karyawan }}</option>
                                @endforeach
                            </select>
                            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nik"></div>
                        </div>
                    @endif

                    <!-- Catatan Global -->
                    <div class="space-y-1.5">
                        <label for="catatan_global" class="block text-xs font-bold text-slate-700">
                            Keterangan Umum / Keperluan Klaim
                        </label>
                        <textarea name="catatan_global" id="catatan_global" rows="3"
                                  placeholder="Contoh: Operasional Perjalanan Dinas Jakarta, Konsumsi Rapat Klien, dll..."
                                  class="w-full p-3 text-xs text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition resize-none"></textarea>
                    </div>

                    <!-- Info Alert -->
                    <div class="p-3.5 rounded-xl bg-sky-50 border border-sky-200/80 text-sky-800 flex items-start gap-2.5">
                        <i class="ti ti-info-circle text-base text-sky-600 shrink-0 mt-0.5"></i>
                        <p class="text-[11.5px] leading-relaxed">
                            Pastikan nominal nota dan lampiran bukti fisik/struk telah sesuai dengan batas plafon kategori yang ditentukan.
                        </p>
                    </div>

                </div>
            </div>

            <!-- Right Column: Card Items Rincian Nota (8 cols) -->
            <div class="lg:col-span-8 space-y-4">
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    
                    <!-- Solid Card Header with Add Row Button -->
                    <div class="px-5 py-3.5 flex items-center justify-between gap-2 text-white border-0"
                         style="background-color: {{ $themeColor1 }};">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-white/20 text-white flex items-center justify-center text-xs font-bold border border-white/20 shadow-2xs backdrop-blur-xs">
                                <i class="ti ti-receipt"></i>
                            </div>
                            <h3 class="text-sm font-extrabold text-white tracking-tight">Rincian Item Nota & Bukti</h3>
                        </div>
                        <button type="button" id="btnAddRowV2" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold border border-white/30 shadow-2xs backdrop-blur-xs transition active:scale-95 cursor-pointer">
                            <i class="ti ti-plus text-xs"></i>
                            <span>Tambah Item</span>
                        </button>
                    </div>

                    <!-- Items Container Cards -->
                    <div class="p-4 sm:p-5 space-y-3" id="itemCardsContainerV2">
                        <!-- Item Card 1 -->
                        <div class="item-card-v2 bg-slate-50/70 hover:bg-slate-50 border border-slate-200/90 rounded-2xl p-4 shadow-2xs transition-all space-y-3">
                            <div class="flex items-center justify-between pb-2.5 border-b border-slate-200/70">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-slate-200/80 text-slate-700 flex items-center justify-center text-[11px] font-black item-number-badge">
                                        1
                                    </span>
                                    <span class="text-xs font-bold text-slate-800">Item Nota Klaim</span>
                                </div>
                                <button type="button" class="btnRemoveRowV2 px-2.5 py-1 rounded-lg text-rose-600 hover:bg-rose-100 bg-rose-50 border border-rose-200/80 text-[11px] font-bold transition flex items-center gap-1 cursor-pointer hidden">
                                    <i class="ti ti-trash text-xs"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                <!-- Tanggal Transaksi -->
                                <div class="sm:col-span-4 space-y-1">
                                    <label class="block text-[11px] font-bold text-slate-600">
                                        Tgl Nota <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                            <i class="ti ti-calendar text-sm"></i>
                                        </span>
                                        <input type="text" name="tgl_item[]" 
                                               value="{{ date('Y-m-d') }}"
                                               placeholder="YYYY-MM-DD"
                                               class="w-full h-9 pl-8 pr-2.5 text-xs font-semibold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 outline-none transition flatpickr-reimburse cursor-pointer">
                                    </div>
                                </div>

                                <!-- Kategori Reimbursement -->
                                <div class="sm:col-span-8 space-y-1">
                                    <label class="block text-[11px] font-bold text-slate-600">
                                        Kategori Reimbursement <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="kode_jenis_reimburse[]" class="w-full h-9 px-3 text-xs bg-white border border-slate-300 rounded-xl focus:border-emerald-500 outline-none transition cursor-pointer select-jenis-v2" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($jenis_reimburse as $j)
                                            <option value="{{ $j->kode_jenis_reimburse }}" 
                                                    data-limit="{{ $j->limit_nominal }}" 
                                                    data-wajib-bukti="{{ $j->wajib_bukti }}">
                                                {{ $j->nama_jenis }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Nominal Klaim -->
                                <div class="sm:col-span-6 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-[11px] font-bold text-slate-600">
                                            Nominal Klaim (Rp) <span class="text-rose-500">*</span>
                                        </label>
                                        <span class="limit-info-v2 text-[10.5px] text-slate-400 font-medium"></span>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none font-bold text-xs">
                                            Rp
                                        </span>
                                        <input type="text" name="nominal[]" 
                                               placeholder="0" 
                                               autocomplete="off"
                                               class="w-full h-9 pl-8 pr-3 text-xs text-right font-black font-mono text-slate-900 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 outline-none transition money-v2 nominal-field-v2" required>
                                    </div>
                                </div>

                                <!-- Upload Bukti File -->
                                <div class="sm:col-span-6 space-y-1">
                                    <label class="block text-[11px] font-bold text-slate-600">
                                        Bukti Nota / Struk
                                    </label>
                                    <div class="flex items-center gap-2">
                                        <label class="inline-flex items-center justify-center gap-1.5 flex-1 h-9 px-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs cursor-pointer shadow-2xs transition active:scale-95 shrink-0">
                                            <i class="ti ti-upload text-emerald-600"></i>
                                            <span class="file-label-text truncate max-w-[120px]">Upload Nota</span>
                                            <input type="file" name="file[]" class="hidden file-input-v2" accept="image/*,.pdf">
                                        </label>
                                        <span class="file-name-v2 text-[11px] text-slate-500 truncate max-w-[140px] flex-1"></span>
                                    </div>
                                </div>

                                <!-- Deskripsi Rincian -->
                                <div class="sm:col-span-12 space-y-1">
                                    <label class="block text-[11px] font-bold text-slate-600">
                                        Keterangan Rincian Nota (Opsional)
                                    </label>
                                    <input type="text" name="keterangan[]" 
                                           placeholder="Contoh: Tiket Kereta Api Argo Parahyangan BDG - GMR..." 
                                           class="w-full h-9 px-3 text-xs bg-white border border-slate-300 rounded-xl outline-none focus:border-emerald-500 transition text-slate-800">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grand Total Bar -->
                    <div class="px-5 py-3.5 bg-slate-100/90 border-t border-slate-200 flex items-center justify-between">
                        <span class="font-black text-xs text-slate-600 uppercase tracking-wider">
                            Grand Total Pengajuan
                        </span>
                        <span class="font-black font-mono text-lg text-emerald-700" id="grandTotalLabelV2">
                            Rp 0
                        </span>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="p-4 bg-white border-t border-slate-200 flex items-center justify-end gap-3">
                        <a href="{{ route('reimbursement.index') }}" 
                           class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
                            Batal
                        </a>
                        <button type="submit" id="btnSubmitReimbursementV2" 
                                class="px-6 py-2.5 text-xs font-bold text-white rounded-xl shadow-md transition active:scale-95 cursor-pointer flex items-center gap-1.5"
                                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                            <i class="ti ti-device-floppy text-sm"></i>
                            <span>Simpan Pengajuan</span>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </form>

</div>

@endsection

@push('myscript')
<script>
    $(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                placeholder: "-- Pilih Karyawan --",
                width: '100%'
            });
        }

        if (typeof flatpickr !== 'undefined') {
            $('.flatpickr-reimburse').flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });
        }

        if (typeof $.fn.maskMoney !== 'undefined') {
            $('.money-v2').maskMoney({
                thousands: '.', decimal: ',', precision: 0, allowZero: true
            });
        }

        function calculateTotal() {
            let grandTotal = 0;
            $('.nominal-field-v2').each(function() {
                let val = $(this).val().replace(/\./g, '').replace(/,/g, '.');
                if (val && !isNaN(val)) {
                    grandTotal += parseFloat(val);
                }
            });
            $('#grandTotalLabelV2').text('Rp ' + number_format(grandTotal, 0, ',', '.'));
        }

        function updateItemNumbers() {
            $('.item-card-v2').each(function(index) {
                $(this).find('.item-number-badge').text(index + 1);
                if ($('.item-card-v2').length > 1) {
                    $(this).find('.btnRemoveRowV2').removeClass('hidden');
                } else {
                    $(this).find('.btnRemoveRowV2').addClass('hidden');
                }
            });
        }

        // Limit indicator
        $(document).on('change', '.select-jenis-v2', function() {
            let limit = $(this).find(':selected').data('limit');
            let info = $(this).closest('.item-card-v2').find('.limit-info-v2');
            if (limit && limit > 0) {
                info.html(`Plafon: <strong class="text-emerald-700">Rp ${number_format(limit, 0, ',', '.')}</strong>`);
            } else if (limit === null || limit === 0) {
                info.html(`<span class="text-emerald-600 font-bold">Plafon: Unlimited</span>`);
            } else {
                info.html('');
            }
        });

        $(document).on('keyup', '.nominal-field-v2', calculateTotal);

        // Add Item Card
        $('#btnAddRowV2').click(function() {
            let newCard = $('.item-card-v2:first').clone();
            newCard.find('input[type="text"]').val('');
            newCard.find('select').val('');
            newCard.find('input[type="file"]').val('');
            newCard.find('.file-label-text').text('Upload Nota');
            newCard.find('.file-name-v2').text('').attr('title', '');
            newCard.find('.limit-info-v2').html('');
            newCard.find('.btnRemoveRowV2').removeClass('hidden');

            // Default date to today
            let today = new Date().toISOString().split('T')[0];
            newCard.find('input[name="tgl_item[]"]').val(today);

            // Re-bind flatpickr
            newCard.find('.flatpickr-reimburse').removeClass('flatpickr-input').flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true
            });

            // Re-bind maskMoney
            newCard.find('.money-v2').maskMoney({
                thousands: '.', decimal: ',', precision: 0, allowZero: true
            });

            $('#itemCardsContainerV2').append(newCard);
            updateItemNumbers();
            calculateTotal();
        });

        // Remove Item Card
        $(document).on('click', '.btnRemoveRowV2', function() {
            if ($('.item-card-v2').length > 1) {
                $(this).closest('.item-card-v2').remove();
                updateItemNumbers();
                calculateTotal();
            }
        });

        // File selection preview
        $(document).on('change', '.file-input-v2', function() {
            let fileName = $(this).val().split('\\').pop();
            let labelText = fileName ? 'Ubah Nota' : 'Upload Nota';
            $(this).siblings('.file-label-text').text(labelText);
            $(this).closest('.item-card-v2').find('.file-name-v2').text(fileName).attr('title', fileName);
        });

        // Form Submit Validation
        $('#formReimbursementV2').submit(function(e) {
            $('.error-feedback').addClass('hidden').text('');
            let hasError = false;
            let errorMessage = '';

            let tgl = $('#tanggal_pengajuan').val();
            if (!tgl) {
                hasError = true;
                errorMessage = 'Tanggal pengajuan wajib diisi.';
                $('#err_tanggal_pengajuan').removeClass('hidden').text(errorMessage);
                $('#tanggal_pengajuan').focus();
            }

            if (!hasError && $('#nik').length && !$('#nik').val()) {
                hasError = true;
                errorMessage = 'Silakan pilih karyawan pemohon terlebih dahulu.';
                $('#err_nik').removeClass('hidden').text(errorMessage);
                $('#nik').focus();
            }

            if (!hasError) {
                $('.item-card-v2').each(function(index) {
                    let card = $(this);
                    let jenis = card.find('.select-jenis-v2');
                    let nominalField = card.find('.nominal-field-v2');
                    let fileInput = card.find('.file-input-v2');
                    let opt = jenis.find('option:selected');

                    if (!jenis.val()) {
                        hasError = true;
                        errorMessage = `Kategori reimbursement item #${index + 1} belum dipilih.`;
                        jenis.focus();
                        return false;
                    }

                    let nominalRaw = nominalField.val();
                    if (!nominalRaw || nominalRaw === '0') {
                        hasError = true;
                        errorMessage = `Nominal klaim pada item #${index + 1} belum diisi.`;
                        nominalField.focus();
                        return false;
                    }

                    let nominal = parseFloat(nominalRaw.replace(/\./g, '').replace(/,/g, '.'));
                    let limit = opt.data('limit');
                    let wajibBukti = opt.data('wajib-bukti');
                    let jenisName = opt.text().trim();

                    if (wajibBukti == 1 && (!fileInput[0].files || fileInput[0].files.length === 0)) {
                        hasError = true;
                        errorMessage = `Kategori "${jenisName}" (Item #${index + 1}) mewajibkan lampiran bukti file nota.`;
                        fileInput.closest('label').addClass('border-rose-400');
                        return false;
                    }

                    if (limit !== undefined && limit !== '' && limit !== null && parseFloat(limit) > 0) {
                        if (nominal > parseFloat(limit)) {
                            hasError = true;
                            errorMessage = `Nominal klaim "${jenisName}" (Item #${index + 1}) sebesar Rp ${number_format(nominal, 0, ',', '.')} melebihi batas plafon Rp ${number_format(limit, 0, ',', '.')}.`;
                            nominalField.focus();
                            return false;
                        }
                    }
                });
            }

            if (hasError) {
                e.preventDefault();
                Swal.fire({
                    title: 'Validasi Belum Lengkap',
                    text: errorMessage,
                    icon: 'warning',
                    confirmButtonColor: '{{ $themeColor1 }}',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-slate-100',
                        confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold'
                    }
                });
                return false;
            }

            $('#btnSubmitReimbursementV2').html('<div class="inline-block animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-1"></div> Menyimpan...').attr('disabled', true);
            return true;
        });
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? ',' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
</script>
@endpush
