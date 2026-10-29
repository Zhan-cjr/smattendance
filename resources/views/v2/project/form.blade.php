@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('project.store') }}" method="POST" id="formCreateProjectV2" class="space-y-5" novalidate>
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Left Section: Spesifikasi & Timeline Project (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            
            <!-- Section Header -->
            <div class="flex items-center gap-2.5 pb-2.5 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-info-circle text-sm"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800">Spesifikasi & Informasi Utama</h3>
                    <p class="text-[11px] text-slate-400">Nama project, kategori sasaran, dan rentang waktu pengerjaan</p>
                </div>
            </div>

            <!-- Nama Project -->
            <div class="space-y-1">
                <label for="nama_project" class="block text-xs font-bold text-slate-700">
                    Nama Project <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-briefcase text-sm"></i>
                    </span>
                    <input type="text" name="nama_project" id="nama_project" value="{{ old('nama_project') }}" required
                           placeholder="Contoh: Implementasi Sistem ERP & Presensi GPS"
                           class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold"
                           style="--tw-ring-color: {{ $themeColor1 }};">
                </div>
                <span class="text-[11px] text-rose-500 font-medium hidden error-feedback mt-1 block" id="err_nama_project"></span>
            </div>

            <!-- Kategori & Prioritas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div class="space-y-1">
                    <label for="category_id" class="block text-xs font-bold text-slate-700">
                        Kategori Project <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ti ti-category text-sm"></i>
                        </span>
                        <select name="category_id" id="category_id" required
                                class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold cursor-pointer"
                                style="--tw-ring-color: {{ $themeColor1 }};">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <span class="text-[11px] text-rose-500 font-medium hidden error-feedback mt-1 block" id="err_category_id"></span>
                </div>

                <div class="space-y-1">
                    <label for="prioritas" class="block text-xs font-bold text-slate-700">
                        Tingkat Prioritas <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ti ti-flag text-sm"></i>
                        </span>
                        <select name="prioritas" id="prioritas" required
                                class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold cursor-pointer"
                                style="--tw-ring-color: {{ $themeColor1 }};">
                            <option value="low" {{ old('prioritas') == 'low' ? 'selected' : '' }}>Rendah (Low)</option>
                            <option value="medium" {{ old('prioritas') == 'medium' || !old('prioritas') ? 'selected' : '' }}>Sedang (Medium)</option>
                            <option value="high" {{ old('prioritas') == 'high' ? 'selected' : '' }}>Tinggi (High)</option>
                            <option value="critical" {{ old('prioritas') == 'critical' ? 'selected' : '' }}>Kritis (Critical)</option>
                        </select>
                    </div>
                    <span class="text-[11px] text-rose-500 font-medium hidden error-feedback mt-1 block" id="err_prioritas"></span>
                </div>
            </div>

            <!-- Departemen & Cabang -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div class="space-y-1">
                    <label for="kode_dept" class="block text-xs font-bold text-slate-700">Departemen Terkait</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ti ti-building text-sm"></i>
                        </span>
                        <select name="kode_dept" id="kode_dept"
                                class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium cursor-pointer"
                                style="--tw-ring-color: {{ $themeColor1 }};">
                            <option value="">Semua Departemen (Lintas Dept)</option>
                            @foreach ($departements as $dept)
                                <option value="{{ $dept->kode_dept }}" {{ old('kode_dept') == $dept->kode_dept ? 'selected' : '' }}>
                                    {{ $dept->nama_dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="kode_cabang" class="block text-xs font-bold text-slate-700">Cabang / Lokasi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ti ti-map-pin text-sm"></i>
                        </span>
                        <select name="kode_cabang" id="kode_cabang"
                                class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium cursor-pointer"
                                style="--tw-ring-color: {{ $themeColor1 }};">
                            <option value="">Semua Cabang (Pusat & Cabang)</option>
                            @foreach ($cabangs as $cab)
                                <option value="{{ $cab->kode_cabang }}" {{ old('kode_cabang') == $cab->kode_cabang ? 'selected' : '' }}>
                                    {{ $cab->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tanggal Mulai & Target Selesai (Flatpickr Datepicker) -->
            <div class="p-3.5 rounded-2xl bg-slate-50/90 border border-slate-200/80 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                        <i class="ti ti-calendar-event text-emerald-600"></i> Rentang Waktu Project
                    </span>
                    <span class="text-[10px] text-slate-400">Pilih rentang tanggal pelaksanaan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label for="start_date" class="block text-[11px] font-bold text-slate-600">
                            Tanggal Mulai <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-calendar text-sm"></i>
                            </span>
                            <input type="text" name="start_date" id="start_date" 
                                   value="{{ old('start_date', date('Y-m-d')) }}" required
                                   placeholder="Pilih tanggal mulai"
                                   autocomplete="off"
                                   class="w-full pl-10 pr-9 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:border-transparent transition-all cursor-pointer flatpickr-project-date"
                                   style="--tw-ring-color: {{ $themeColor1 }};">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-chevron-down text-xs"></i>
                            </span>
                        </div>
                        <span class="text-[11px] text-rose-500 font-medium hidden error-feedback mt-1 block" id="err_start_date"></span>
                    </div>

                    <div class="space-y-1">
                        <label for="end_date" class="block text-[11px] font-bold text-slate-600">
                            Target Selesai (Deadline) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-calendar-due text-sm"></i>
                            </span>
                            <input type="text" name="end_date" id="end_date" 
                                   value="{{ old('end_date') }}" required
                                   placeholder="Pilih target selesai"
                                   autocomplete="off"
                                   class="w-full pl-10 pr-9 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:border-transparent transition-all cursor-pointer flatpickr-project-date"
                                   style="--tw-ring-color: {{ $themeColor1 }};">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                                <i class="ti ti-chevron-down text-xs"></i>
                            </span>
                        </div>
                        <span class="text-[11px] text-rose-500 font-medium hidden error-feedback mt-1 block" id="err_end_date"></span>
                    </div>
                </div>
            </div>

            <!-- Deskripsi & Tujuan Project -->
            <div class="space-y-1">
                <label for="deskripsi" class="block text-xs font-bold text-slate-700">Deskripsi & Catatan Tujuan</label>
                <textarea name="deskripsi" id="deskripsi" rows="2"
                          placeholder="Jelaskan ruang lingkup sasaran, latar belakang, atau target output yang ingin dicapai..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium leading-relaxed"
                          style="--tw-ring-color: {{ $themeColor1 }};">{{ old('deskripsi') }}</textarea>
            </div>
        </div>

        <!-- Right Section: Penugasan Tim & Anggaran (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            
            <!-- Section Header -->
            <div class="flex items-center gap-2.5 pb-2.5 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-2xs shrink-0"
                     style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
                    <i class="ti ti-users text-sm"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800">Penugasan Tim & Anggaran</h3>
                    <p class="text-[11px] text-slate-400">Pemimpin project, alokasi personil, dan estimasi biaya</p>
                </div>
            </div>

            <!-- Status & Anggaran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div class="space-y-1">
                    <label for="status" class="block text-xs font-bold text-slate-700">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="ti ti-progress-check text-sm"></i>
                        </span>
                        <select name="status" id="status" required
                                class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold cursor-pointer"
                                style="--tw-ring-color: {{ $themeColor1 }};">
                            <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="budget" class="block text-xs font-bold text-slate-700">Anggaran (Budget)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">
                            Rp
                        </span>
                        <input type="text" name="budget" id="budget" value="{{ old('budget') }}"
                               placeholder="0"
                               class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold text-right money"
                               style="--tw-ring-color: {{ $themeColor1 }};">
                    </div>
                </div>
            </div>

            <!-- Project Leader (Searchable Dropdown) -->
            <div class="space-y-1">
                <label for="leader_nik" class="block text-xs font-bold text-slate-700 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <i class="ti ti-crown text-amber-500"></i>
                        <span>Project Leader <span class="text-rose-500">*</span></span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-normal">Penanggung Jawab Utama</span>
                </label>
                <div class="relative mb-1.5">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-search text-xs"></i>
                    </span>
                    <input type="text" id="searchLeaderInput" placeholder="Cari nama leader atau NIK..."
                           class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:border-transparent transition-all"
                           style="--tw-ring-color: {{ $themeColor1 }};">
                </div>
                <select name="leader_nik" id="leader_nik" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-semibold cursor-pointer"
                        style="--tw-ring-color: {{ $themeColor1 }};">
                    <option value="">Pilih Penanggung Jawab Project</option>
                    @foreach ($karyawan as $k)
                        <option value="{{ $k->nik }}" {{ old('leader_nik') == $k->nik ? 'selected' : '' }}
                                class="leader-opt"
                                data-search="{{ strtolower($k->nama_karyawan . ' ' . $k->nik . ' ' . ($k->nik_show ?? '') . ' ' . ($k->jabatan ? $k->jabatan->nama_jabatan : '')) }}">
                            {{ !empty($k->nik_show) ? $k->nik_show : $k->nik }} - {{ $k->nama_karyawan }} ({{ $k->jabatan ? $k->jabatan->nama_jabatan : '-' }})
                        </option>
                    @endforeach
                </select>
                <span class="text-[11px] text-rose-500 font-medium hidden error-feedback mt-1 block" id="err_leader_nik"></span>
            </div>

            <!-- Anggota Tim (Searchable Card Selector Container) -->
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700">
                        Anggota Tim Project
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnSelectAllFiltered" class="text-[10px] font-bold text-emerald-700 hover:underline cursor-pointer">
                            Pilih Semua
                        </button>
                        <span class="text-slate-300 text-[10px]">|</span>
                        <button type="button" id="btnDeselectAll" class="text-[10px] font-bold text-slate-500 hover:text-rose-600 cursor-pointer">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Search Input for Team Members -->
                <div class="relative mb-2">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="ti ti-search text-xs"></i>
                    </span>
                    <input type="text" id="searchMemberInput" placeholder="Cari nama karyawan, NIK, jabatan..."
                           class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:border-transparent transition-all"
                           style="--tw-ring-color: {{ $themeColor1 }};">
                </div>

                <div class="border border-slate-200 rounded-xl bg-slate-50/70 p-2 max-h-44 overflow-y-auto space-y-1.5" id="memberChecklistContainer">
                    @foreach ($karyawan as $k)
                        <label class="member-item flex items-center gap-2.5 p-2 rounded-lg bg-white border border-slate-200/80 hover:bg-slate-50 transition cursor-pointer text-xs select-none"
                               data-search="{{ strtolower($k->nama_karyawan . ' ' . $k->nik . ' ' . ($k->nik_show ?? '') . ' ' . ($k->jabatan ? $k->jabatan->nama_jabatan : '')) }}">
                            <input type="checkbox" name="members[]" value="{{ $k->nik }}" 
                                   {{ (collect(old('members'))->contains($k->nik)) ? 'checked' : '' }}
                                   class="member-checkbox rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 border-slate-300">
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-slate-800 block truncate">{{ $k->nama_karyawan }}</span>
                                <span class="text-[10px] text-slate-400 block truncate">{{ !empty($k->nik_show) ? $k->nik_show : $k->nik }} • {{ $k->jabatan ? $k->jabatan->nama_jabatan : 'Staf' }}</span>
                            </div>
                        </label>
                    @endforeach
                    <div id="noMembersListFound" class="hidden text-xs text-slate-400 text-center py-4">
                        <i class="ti ti-user-x text-base block mb-1"></i>
                        Tidak ada anggota yang cocok dengan pencarian
                    </div>
                </div>
                <p class="text-[10.5px] text-slate-400">Gunakan kotak pencarian di atas untuk menyaring personil dengan cepat.</p>
            </div>

            <!-- Catatan Tambahan -->
            <div class="space-y-1">
                <label for="catatan" class="block text-xs font-bold text-slate-700">Catatan Tambahan</label>
                <textarea name="catatan" id="catatan" rows="2"
                          placeholder="Tautan Figma, link repo, atau catatan khusus lainnya..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition-all font-medium"
                          style="--tw-ring-color: {{ $themeColor1 }};">{{ old('catatan') }}</textarea>
            </div>
        </div>

    </div>

    <!-- Modal Footer Actions -->
    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
        <button type="button" class="btnCloseModalProject px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-bold transition shadow-2xs cursor-pointer">
            Batal
        </button>
        <button type="submit" id="btnSubmitProjectV2"
                class="inline-flex items-center gap-2 px-6 py-2.5 text-white font-bold rounded-xl text-xs shadow-xs transition-all duration-200 active:scale-95 cursor-pointer hover:opacity-95"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Project</span>
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // 1. Inisialisasi Flatpickr Tanggal
    if (typeof flatpickr !== 'undefined') {
        const fpStart = flatpickr("#start_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            allowInput: false,
            locale: (typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
            onChange: function(selectedDates, dateStr) {
                if (fpEnd && selectedDates[0]) {
                    fpEnd.set('minDate', dateStr);
                }
                validateField('#start_date');
            }
        });

        const fpEnd = flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            allowInput: false,
            minDate: $("#start_date").val() || null,
            locale: (typeof flatpickr.l10ns !== 'undefined' && flatpickr.l10ns.id) ? flatpickr.l10ns.id : 'default',
            onChange: function() {
                validateField('#end_date');
            }
        });
    }

    // 2. Format Rupiah Input Budget
    $('#budget').on('input', function() {
        let val = $(this).val().replace(/[^0-9]/g, '');
        if (val) {
            $(this).val(new Intl.NumberFormat('id-ID').format(val));
        } else {
            $(this).val('');
        }
    });

    // 3. Live Search Filter for Leader Select
    $('#searchLeaderInput').on('input', function() {
        const q = $(this).val().toLowerCase().trim();
        $('#leader_nik option.leader-opt').each(function() {
            const search = $(this).data('search') || $(this).text().toLowerCase();
            if (!q || search.includes(q)) {
                $(this).removeClass('hidden').show();
            } else {
                $(this).addClass('hidden').hide();
            }
        });
    });

    // 4. Live Search Filter for Team Members Checklist
    $('#searchMemberInput').on('input', function() {
        const q = $(this).val().toLowerCase().trim();
        let matchCount = 0;

        $('#memberChecklistContainer .member-item').each(function() {
            const search = $(this).data('search') || $(this).text().toLowerCase();
            if (!q || search.includes(q)) {
                $(this).removeClass('hidden').show();
                matchCount++;
            } else {
                $(this).addClass('hidden').hide();
            }
        });

        if (matchCount === 0 && q) {
            $('#noMembersListFound').removeClass('hidden');
        } else {
            $('#noMembersListFound').addClass('hidden');
        }
    });

    // 5. Select All / Deselect All Filtered Members
    $('#btnSelectAllFiltered').on('click', function() {
        $('#memberChecklistContainer .member-item:visible .member-checkbox').prop('checked', true);
    });

    $('#btnDeselectAll').on('click', function() {
        $('#memberChecklistContainer .member-checkbox').prop('checked', false);
    });

    // Helper: Validasi Konsisten
    function showError(selector, msg) {
        const $el = $(selector);
        const errId = '#err_' + $el.attr('id');
        $(errId).text(msg).removeClass('hidden');
        
        // Highlight input / flatpickr altInput
        $el.addClass('border-rose-500');
        if ($el.next('.flatpickr-input').length) {
            $el.next('.flatpickr-input').addClass('border-rose-500');
        }
    }

    function clearError(selector) {
        const $el = $(selector);
        const errId = '#err_' + $el.attr('id');
        $(errId).text('').addClass('hidden');
        $el.removeClass('border-rose-500');
        if ($el.next('.flatpickr-input').length) {
            $el.next('.flatpickr-input').removeClass('border-rose-500');
        }
    }

    function validateField(selector) {
        const $el = $(selector);
        const id = $el.attr('id');
        const val = ($el.val() || '').trim();

        if (id === 'nama_project') {
            if (!val) {
                showError('#nama_project', 'Nama project wajib diisi.');
                return false;
            }
            clearError('#nama_project');
            return true;
        }

        if (id === 'category_id') {
            if (!val) {
                showError('#category_id', 'Silakan pilih kategori project.');
                return false;
            }
            clearError('#category_id');
            return true;
        }

        if (id === 'start_date') {
            if (!val) {
                showError('#start_date', 'Tanggal mulai wajib diisi.');
                return false;
            }
            clearError('#start_date');
            return true;
        }

        if (id === 'end_date') {
            const startVal = $('#start_date').val();
            if (!val) {
                showError('#end_date', 'Target deadline wajib diisi.');
                return false;
            } else if (startVal && val < startVal) {
                showError('#end_date', 'Target deadline tidak boleh sebelum tanggal mulai.');
                return false;
            }
            clearError('#end_date');
            return true;
        }

        if (id === 'leader_nik') {
            if (!val) {
                showError('#leader_nik', 'Silakan pilih leader penanggung jawab project.');
                return false;
            }
            clearError('#leader_nik');
            return true;
        }

        return true;
    }

    // Realtime field validation on input/change
    $('#nama_project, #category_id, #leader_nik').on('input change blur', function() {
        validateField(this);
    });

    // Form Submit Handler
    $('#formCreateProjectV2').on('submit', function(e) {
        let isValid = true;
        let firstInvalidEl = null;

        const fields = ['#nama_project', '#category_id', '#start_date', '#end_date', '#leader_nik'];
        fields.forEach(function(f) {
            if (!validateField(f)) {
                isValid = false;
                if (!firstInvalidEl) {
                    firstInvalidEl = $(f);
                }
            }
        });

        if (!isValid) {
            e.preventDefault();
            if (firstInvalidEl) {
                firstInvalidEl.focus();
                if (firstInvalidEl.next('.flatpickr-input').length) {
                    firstInvalidEl.next('.flatpickr-input').focus();
                }
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Formulir Belum Lengkap',
                    text: 'Silakan lengkapi field yang ditandai merah sebelum menyimpan project.',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl'
                    }
                });
            }
            return false;
        }

        const btn = $('#btnSubmitProjectV2');
        btn.prop('disabled', true).addClass('opacity-70 cursor-not-allowed').html(`
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Menyimpan Project...</span>
        `);
    });
});
</script>
