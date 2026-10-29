@extends('v2.layouts.app')

@section('titlepage', 'Edit Indikator KPI')
@section('page_title', 'Edit Indikator KPI')

@section('content')

@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
    $isGlobal = empty($kpi_indicator->kode_jabatan) && empty($kpi_indicator->kode_dept);
@endphp

<div class="space-y-4">

    <!-- ================= 1. PAGE HEADER ================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
        <!-- Title & Subtitle with Back Button -->
        <div class="flex items-center gap-3">
            <a href="{{ route('kpi.indicators.index') }}" 
               class="w-10 h-10 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:bg-slate-50 hover:text-slate-900 flex items-center justify-center transition-all shadow-2xs cursor-pointer shrink-0">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('kpi.indicators.index') }}" class="hover:text-emerald-600 transition-colors">Indikator KPI</a>
                    <i class="ti ti-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Edit Konfigurasi</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5 mt-0.5">
                    <i class="ti ti-edit text-2xl" style="color: {{ $themeColor1 }};"></i>
                    <span>Edit Konfigurasi KPI</span>
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
                <a href="{{ route('kpi.indicators.index') }}" class="hover:text-slate-700 transition">KPI</a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="font-bold text-slate-800">Edit Indikator</span>
            </nav>
        </div>
    </div>

    <!-- Target Scope Banner -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0 shadow-2xs"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    @if($isGlobal)
                        <i class="ti ti-world text-lg"></i>
                    @else
                        <i class="ti ti-briefcase text-lg"></i>
                    @endif
                </div>
                <div>
                    @if($isGlobal)
                        <h3 class="text-sm font-bold text-slate-900">Indikator KPI Global Perusahaan</h3>
                        <p class="text-xs text-slate-500">Berlaku untuk seluruh karyawan di semua departemen & cabang</p>
                    @else
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-bold text-slate-900">{{ $kpi_indicator->jabatan->nama_jabatan ?? 'Jabatan' }}</h3>
                            <span class="font-mono text-xs text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                {{ $kpi_indicator->kode_jabatan }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Departemen: <strong class="text-slate-700 font-semibold">{{ $kpi_indicator->departemen->nama_dept ?? 'Semua' }}</strong></p>
                    @endif
                </div>
            </div>

            <button type="button" id="btnTambahIndikatorV2"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-plus text-sm"></i>
                <span>Tambah Baris Indikator</span>
            </button>
        </div>
    </div>

    <!-- Akumulasi Bobot & Progress Card -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-700">Akumulasi bobot keseluruhan</span>
            <span class="font-mono font-bold text-sm" id="totalBobotLabel">0% / 100%</span>
        </div>
        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
            <div id="progressBarBobot" class="h-full transition-all duration-300 rounded-full bg-slate-300" style="width: 0%;"></div>
        </div>
        <p class="text-[11.5px] text-slate-500" id="bobotNote">
            Pastikan total bobot indikator mencapai tepat 100%.
        </p>
    </div>

    <!-- Daftar Kartu Indikator -->
    <div class="space-y-3" id="indicatorsContainer">
        <!-- Will be populated via JS -->
    </div>

    <!-- Form Simpan Konfigurasi -->
    <form action="{{ route('kpi.indicators.update', $kpi_indicator->id) }}" method="POST" id="formSimpanKonfigurasiV2" class="pt-2">
        @csrf
        @method('PUT')
        <input type="hidden" name="indicators_data" id="indicators_data_input">

        <div class="flex items-center justify-end gap-2.5">
            <a href="{{ route('kpi.indicators.index') }}" 
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
                Batal
            </a>
            <button type="submit" id="btnSubmitKonfigurasiV2" 
                    class="px-6 py-2.5 text-xs font-bold text-white rounded-xl shadow-xs transition active:scale-95 cursor-pointer flex items-center gap-1.5"
                    style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                <i class="ti ti-device-floppy text-sm"></i>
                <span>Simpan Perubahan KPI</span>
            </button>
        </div>
    </form>

</div>

<!-- ================= MODAL TAMBAH / EDIT INDIKATOR ITEM ================= -->
<div id="modalItemIndikatorV2" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 shadow-2xl w-full max-w-xl overflow-hidden transform transition-all duration-300 flex flex-col my-auto">
        <!-- Header Modal -->
        <div class="px-5 sm:px-6 py-3.5 border-b border-slate-200/80 bg-white flex items-center justify-between relative shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-700 bg-slate-100 border border-slate-200">
                    <i class="ti ti-edit text-base"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base" id="modalItemTitle">
                        Tambah Indikator KPI
                    </h3>
                    <p class="text-[11px] text-slate-500">
                        Atur tolok ukur, bobot, target, dan sumber data
                    </p>
                </div>
            </div>
            <button type="button" class="btnCloseModalItemIndikator w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center border border-slate-200 transition cursor-pointer">
                <i class="ti ti-x text-sm"></i>
            </button>
        </div>

        <!-- Body Form Modal -->
        <div class="p-5 sm:p-6">
            <form id="formItemIndikatorModal" class="space-y-3.5" novalidate>
                <!-- Nama Indikator -->
                <div class="space-y-1">
                    <label for="item_nama_indikator" class="block text-xs font-bold text-slate-700">
                        Nama Indikator <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="item_nama_indikator" placeholder="Contoh: Realisasi Target Penjualan Bulanan"
                           class="w-full h-10 px-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                    <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_item_nama"></div>
                </div>

                <!-- Deskripsi / Definisi Operasional -->
                <div class="space-y-1">
                    <label for="item_deskripsi" class="block text-xs font-bold text-slate-700">
                        Deskripsi / Definisi Operasional (Opsional)
                    </label>
                    <textarea id="item_deskripsi" rows="2" placeholder="Jelaskan formula perhitungan atau catatan tolok ukur..."
                              class="w-full p-2.5 text-xs text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition resize-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Satuan -->
                    <div class="space-y-1">
                        <label for="item_satuan" class="block text-xs font-bold text-slate-700">
                            Satuan Ukur <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="item_satuan" placeholder="Contoh: %, Rupiah, Unit, Skala"
                               class="w-full h-10 px-3 text-xs text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_item_satuan"></div>
                    </div>

                    <!-- Jenis Target -->
                    <div class="space-y-1">
                        <label for="item_jenis_target" class="block text-xs font-bold text-slate-700">
                            Karakter Target <span class="text-rose-500">*</span>
                        </label>
                        <select id="item_jenis_target" class="w-full h-10 px-3 text-xs bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                            <option value="">-- Pilih Karakter --</option>
                            <option value="max">Maksimal (Semakin tinggi semakin baik)</option>
                            <option value="min">Minimal (Semakin rendah semakin baik)</option>
                        </select>
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_item_jenis_target"></div>
                    </div>

                    <!-- Target Nilai -->
                    <div class="space-y-1">
                        <label for="item_target" class="block text-xs font-bold text-slate-700">
                            Angka Target <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.01" id="item_target" placeholder="Contoh: 100"
                               class="w-full h-10 px-3 text-xs font-mono font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_item_target"></div>
                    </div>

                    <!-- Bobot (%) -->
                    <div class="space-y-1">
                        <label for="item_bobot" class="block text-xs font-bold text-slate-700">
                            Bobot (%) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.01" min="1" max="100" id="item_bobot" placeholder="Contoh: 25"
                               class="w-full h-10 px-3 text-xs font-mono font-bold text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition">
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_item_bobot"></div>
                    </div>

                    <!-- Mode Input -->
                    <div class="space-y-1 sm:col-span-2">
                        <label for="item_mode" class="block text-xs font-bold text-slate-700">
                            Mekanisme Input Realisasi <span class="text-rose-500">*</span>
                        </label>
                        <select id="item_mode" class="w-full h-10 px-3 text-xs bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                            <option value="manual">Manual Input oleh Atasan / Penilai</option>
                            <option value="auto">Kalkulasi Otomatis Sistem Presensi</option>
                        </select>
                    </div>

                    <!-- Metric Source Dropdown (Auto) -->
                    <div class="space-y-1 sm:col-span-2 hidden" id="item_metric_source_container">
                        <label for="item_metric_source" class="block text-xs font-bold text-slate-700">
                            Sumber Metrik Otomatis <span class="text-rose-500">*</span>
                        </label>
                        <select id="item_metric_source" class="w-full h-10 px-3 text-xs bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer">
                            <option value="">-- Pilih Sumber Data --</option>
                            <option value="attendance_hadir">Total Kehadiran (Hari)</option>
                            <option value="attendance_terlambat">Total Keterlambatan (Hari/Kali)</option>
                            <option value="attendance_sakit">Total Sakit (Hari)</option>
                            <option value="attendance_izin">Total Izin (Hari)</option>
                            <option value="attendance_cuti">Total Cuti (Hari)</option>
                            <option value="attendance_alpa">Total Alpa (Hari)</option>
                        </select>
                        <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_item_metric_source"></div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" class="btnCloseModalItemIndikator px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" id="btnSimpanItemModal" 
                            class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-xs transition active:scale-95 cursor-pointer flex items-center gap-1.5"
                            style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                        <i class="ti ti-check text-sm"></i>
                        <span>Terapkan Indikator</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    let indicators = [];
    let editIndex = -1;

    $(function() {
        $('#item_mode').change(function() {
            if ($(this).val() === 'auto') {
                $('#item_metric_source_container').removeClass('hidden');
            } else {
                $('#item_metric_source_container').addClass('hidden');
                $('#item_metric_source').val('');
            }
        });

        // Load existing detail indicators
        @foreach($kpi_indicator->details as $detail)
            indicators.push({
                nama_indikator: {!! json_encode($detail->nama_indikator) !!},
                deskripsi: {!! json_encode($detail->deskripsi ?? '') !!},
                satuan: {!! json_encode($detail->satuan) !!},
                jenis_target: {!! json_encode($detail->jenis_target) !!},
                bobot: parseFloat({!! json_encode($detail->bobot) !!}),
                target: parseFloat({!! json_encode($detail->target) !!}),
                mode: {!! json_encode($detail->mode ?? 'manual') !!},
                metric_source: {!! json_encode($detail->metric_source ?? '') !!}
            });
        @endforeach

        renderIndicators();

        function openItemModal(isEdit = false, index = -1) {
            editIndex = index;
            $('.error-feedback').addClass('hidden').text('');
            $('#formItemIndikatorModal input, #formItemIndikatorModal select').removeClass('border-rose-500');

            if (isEdit && indicators[index]) {
                const item = indicators[index];
                $('#modalItemTitle').text('Edit Indikator KPI');
                $('#item_nama_indikator').val(item.nama_indikator);
                $('#item_deskripsi').val(item.deskripsi || '');
                $('#item_satuan').val(item.satuan);
                $('#item_jenis_target').val(item.jenis_target);
                $('#item_target').val(item.target);
                $('#item_bobot').val(item.bobot);
                $('#item_mode').val(item.mode || 'manual').trigger('change');
                if (item.mode === 'auto') {
                    $('#item_metric_source').val(item.metric_source);
                }
            } else {
                $('#modalItemTitle').text('Tambah Indikator KPI');
                document.getElementById('formItemIndikatorModal').reset();
                $('#item_mode').val('manual').trigger('change');
            }

            $('#modalItemIndikatorV2').removeClass('hidden');
            $('body').addClass('overflow-hidden');
        }

        function closeItemModal() {
            $('#modalItemIndikatorV2').addClass('hidden');
            $('body').removeClass('overflow-hidden');
            editIndex = -1;
        }

        $('#btnTambahIndikatorV2').click(function() {
            openItemModal(false);
        });

        $('.btnCloseModalItemIndikator').click(closeItemModal);

        $('#modalItemIndikatorV2').click(function(e) {
            if (e.target === this) closeItemModal();
        });

        $(document).keydown(function(e) {
            if (e.key === 'Escape' && !$('#modalItemIndikatorV2').hasClass('hidden')) {
                closeItemModal();
            }
        });

        // Simpan Item Modal ke Array
        $('#btnSimpanItemModal').click(function() {
            let nama = $('#item_nama_indikator').val().trim();
            let deskripsi = $('#item_deskripsi').val().trim();
            let satuan = $('#item_satuan').val().trim();
            let jenis_target = $('#item_jenis_target').val();
            let target = parseFloat($('#item_target').val());
            let bobot = parseFloat($('#item_bobot').val());
            let mode = $('#item_mode').val();
            let metric_source = $('#item_metric_source').val();

            let hasError = false;
            $('.error-feedback').addClass('hidden').text('');
            $('#formItemIndikatorModal input, #formItemIndikatorModal select').removeClass('border-rose-500');

            if (!nama) {
                $('#err_item_nama').text('Nama indikator wajib diisi.').removeClass('hidden');
                $('#item_nama_indikator').addClass('border-rose-500');
                hasError = true;
            }
            if (!satuan) {
                $('#err_item_satuan').text('Satuan ukur wajib diisi.').removeClass('hidden');
                $('#item_satuan').addClass('border-rose-500');
                hasError = true;
            }
            if (!jenis_target) {
                $('#err_item_jenis_target').text('Pilih karakter jenis target.').removeClass('hidden');
                $('#item_jenis_target').addClass('border-rose-500');
                hasError = true;
            }
            if (isNaN(target)) {
                $('#err_item_target').text('Angka target wajib diisi.').removeClass('hidden');
                $('#item_target').addClass('border-rose-500');
                hasError = true;
            }
            if (isNaN(bobot) || bobot <= 0) {
                $('#err_item_bobot').text('Bobot wajib diisi (minimal 1%).').removeClass('hidden');
                $('#item_bobot').addClass('border-rose-500');
                hasError = true;
            }
            if (mode === 'auto' && !metric_source) {
                $('#err_item_metric_source').text('Pilih sumber data otomatis.').removeClass('hidden');
                $('#item_metric_source').addClass('border-rose-500');
                hasError = true;
            }

            if (hasError) return false;

            const item = {
                nama_indikator: nama,
                deskripsi: deskripsi,
                satuan: satuan,
                jenis_target: jenis_target,
                target: target,
                bobot: bobot,
                mode: mode,
                metric_source: metric_source
            };

            if (editIndex >= 0) {
                indicators[editIndex] = item;
            } else {
                indicators.push(item);
            }

            renderIndicators();
            closeItemModal();
        });

        function renderIndicators() {
            const container = $('#indicatorsContainer');
            container.empty();

            if (indicators.length === 0) {
                container.html(`
                    <div id="emptyIndicatorsState" class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200/90 shadow-2xs">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                            <i class="ti ti-list-check"></i>
                        </div>
                        <h5 class="text-xs font-bold text-slate-700">Belum Ada Indikator</h5>
                        <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol "Tambah Baris Indikator" untuk menambahkan poin penilaian.</p>
                    </div>
                `);
            } else {
                const metricLabels = {
                    'attendance_hadir': 'Kehadiran (Hari)',
                    'attendance_terlambat': 'Keterlambatan',
                    'attendance_sakit': 'Sakit (Hari)',
                    'attendance_izin': 'Izin (Hari)',
                    'attendance_cuti': 'Cuti (Hari)',
                    'attendance_alpa': 'Alpa (Hari)'
                };

                indicators.forEach((item, index) => {
                    const isMax = item.jenis_target === 'max';
                    const targetBadge = isMax 
                        ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10.5px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/80"><i class="ti ti-trending-up text-xs"></i> Maksimal</span>`
                        : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10.5px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/80"><i class="ti ti-trending-down text-xs"></i> Minimal</span>`;

                    const modeBadge = item.mode === 'auto'
                        ? `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200" title="Sumber: ${item.metric_source}"><i class="ti ti-cpu mr-0.5"></i> ${metricLabels[item.metric_source] || 'Otomatis'}</span>`
                        : `<span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200"><i class="ti ti-pencil mr-0.5"></i> Manual</span>`;

                    container.append(`
                        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 hover:border-slate-300 shadow-2xs space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2.5 border-b border-slate-100">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold shrink-0">
                                        #${index + 1}
                                    </span>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">${escapeHtml(item.nama_indikator)}</h4>
                                    ${modeBadge}
                                    ${targetBadge}
                                </div>
                                <div class="flex items-center gap-3 justify-between sm:justify-end">
                                    <span class="text-xs text-slate-500">Bobot: <strong class="text-emerald-700 font-mono text-sm">${item.bobot}%</strong></span>
                                    <span class="text-xs text-slate-500">Target: <strong class="text-slate-800 font-mono">${item.target} ${escapeHtml(item.satuan)}</strong></span>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div class="flex-1 text-slate-600">
                                    <span class="text-[10.5px] text-slate-400 block">Keterangan:</span>
                                    <p class="leading-relaxed ${item.deskripsi ? 'text-slate-700' : 'italic text-slate-400'}">${escapeHtml(item.deskripsi) || 'Tidak ada catatan deskripsi'}</p>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                                    <button type="button" class="btnEditItemRow px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-bold transition active:scale-95 cursor-pointer flex items-center gap-1" data-index="${index}">
                                        <i class="ti ti-edit text-xs"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button type="button" class="btnDeleteItemRow px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition active:scale-95 cursor-pointer flex items-center gap-1" data-index="${index}">
                                        <i class="ti ti-trash text-xs"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                });
            }

            updateBobotDisplay();
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        $(document).on('click', '.btnEditItemRow', function() {
            let idx = $(this).data('index');
            openItemModal(true, idx);
        });

        $(document).on('click', '.btnDeleteItemRow', function() {
            let idx = $(this).data('index');
            Swal.fire({
                title: 'Hapus Indikator Ini?',
                text: 'Indikator ini akan dihapus dari paket konfigurasi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-slate-100',
                    confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold shadow-xs',
                    cancelButton: 'px-4 py-2 rounded-xl text-xs font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    indicators.splice(idx, 1);
                    renderIndicators();
                }
            });
        });

        function updateBobotDisplay() {
            let total = indicators.reduce((sum, item) => sum + (parseFloat(item.bobot) || 0), 0);
            total = Math.round(total * 100) / 100;

            $('#totalBobotLabel').text(total + '% / 100%');
            $('#progressBarBobot').css('width', Math.min(total, 100) + '%');

            if (total === 100) {
                $('#progressBarBobot').removeClass('bg-slate-300 bg-amber-500 bg-rose-500').addClass('bg-emerald-500');
                $('#totalBobotLabel').removeClass('text-slate-800 text-amber-600 text-rose-600').addClass('text-emerald-700');
                $('#bobotNote').html('<span class="text-emerald-700 font-semibold"><i class="ti ti-circle-check text-xs"></i> Total bobot valid (tepat 100%).</span>');
            } else if (total > 100) {
                $('#progressBarBobot').removeClass('bg-slate-300 bg-emerald-500 bg-amber-500').addClass('bg-rose-500');
                $('#totalBobotLabel').removeClass('text-slate-800 text-emerald-700 text-amber-600').addClass('text-rose-600');
                $('#bobotNote').html('<span class="text-rose-600 font-semibold"><i class="ti ti-alert-triangle text-xs"></i> Total bobot melebihi batas (kelebihan ' + (total - 100) + '%).</span>');
            } else {
                $('#progressBarBobot').removeClass('bg-slate-300 bg-emerald-500 bg-rose-500').addClass('bg-amber-500');
                $('#totalBobotLabel').removeClass('text-emerald-700 text-rose-600').addClass('text-slate-800');
                $('#bobotNote').html('<span class="text-slate-500">Kurang ' + (100 - total) + '% lagi untuk mencapai tepat 100%.</span>');
            }
        }

        // Form Submit Konfigurasi
        $('#formSimpanKonfigurasiV2').submit(function(e) {
            if (indicators.length === 0) {
                e.preventDefault();
                Swal.fire({
                    title: 'Indikator Kosong',
                    text: 'Silakan tambahkan minimal 1 indikator penilaian KPI.',
                    icon: 'warning',
                    confirmButtonColor: '{{ $themeColor1 }}',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-slate-100',
                        confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold'
                    }
                });
                return false;
            }

            let total = indicators.reduce((sum, item) => sum + (parseFloat(item.bobot) || 0), 0);
            total = Math.round(total * 100) / 100;
            if (total !== 100) {
                e.preventDefault();
                Swal.fire({
                    title: 'Total Bobot Belum 100%',
                    text: 'Total bobot indikator harus tepat 100%. Saat ini akumulasi bobot adalah ' + total + '%.',
                    icon: 'warning',
                    confirmButtonColor: '{{ $themeColor1 }}',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-slate-100',
                        confirmButton: 'px-4 py-2 rounded-xl text-xs font-bold'
                    }
                });
                return false;
            }

            $('#indicators_data_input').val(JSON.stringify(indicators));
            $('#btnSubmitKonfigurasiV2').html('<div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div><span>Memperbarui...</span>').prop('disabled', true);
            return true;
        });
    });
</script>
@endpush
