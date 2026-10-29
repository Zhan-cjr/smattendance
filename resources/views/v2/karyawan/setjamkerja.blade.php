{{-- V2 Modal Body: Set Jam Kerja Karyawan --}}
<div class="space-y-5" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <!-- Karyawan Info Card -->
    <div style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956)); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <i class="ti ti-user"></i>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.2;">{{ $karyawan->nama_karyawan }}</h4>
                        <span style="font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px; background: #e2e8f0; color: #334155; font-family: monospace;">{{ $karyawan->nik }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 12px; font-size: 11.5px; color: #64748b; margin-top: 3px;">
                        <span><i class="ti ti-building" style="margin-right: 3px;"></i>{{ textUpperCase($karyawan->nama_dept) }}</span>
                        <span>•</span>
                        <span><i class="ti ti-map-pin" style="margin-right: 3px;"></i>{{ textUpperCase($karyawan->nama_cabang) }}</span>
                    </div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 6px; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span>
                    Jadwal Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- Segmented Tab Switcher -->
    <div style="background: #f1f5f9; padding: 4px; border-radius: 10px; display: flex; gap: 4px; border: 1px solid #e2e8f0;">
        <button type="button" id="tab-btn-day" class="tab-pill-btn active" style="flex: 1; padding: 8px 14px; font-size: 12.5px; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s ease;">
            <i class="ti ti-calendar-event"></i>
            <span>Set Jam Kerja Reguler (Hari)</span>
        </button>
        <button type="button" id="tab-btn-date" class="tab-pill-btn" style="flex: 1; padding: 8px 14px; font-size: 12.5px; font-weight: 700; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s ease;">
            <i class="ti ti-calendar-time"></i>
            <span>Set Jam Kerja Khusus (Tanggal)</span>
        </button>
    </div>

    <!-- Tab 1 Content: Set Jam Kerja By Day -->
    <div id="tab-pane-day" style="display: block;">
        <form action="{{ route('karyawan.storejamkerjabyday', Crypt::encrypt($karyawan->nik)) }}" id="formSetJamkerjaV2" method="POST">
            @csrf
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="padding: 12px 16px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.03em;">Jadwal Mingguan</span>
                    <span style="font-size: 11px; color: #64748b;">Pilih shift kerja untuk masing-masing hari</span>
                </div>
                <div style="divide-y: 1px solid #f1f5f9;">
                    @php
                        $nama_hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                        $hari_icons = [
                            'Senin' => 'ti ti-briefcase',
                            'Selasa' => 'ti ti-briefcase',
                            'Rabu' => 'ti ti-briefcase',
                            'Kamis' => 'ti ti-briefcase',
                            'Jumat' => 'ti ti-briefcase',
                            'Sabtu' => 'ti ti-coffee',
                            'Minggu' => 'ti ti-sun'
                        ];
                    @endphp
                    @foreach ($nama_hari as $hari)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-bottom: 1px solid #f1f5f9; gap: 12px; background: {{ in_array($hari, ['Sabtu', 'Minggu']) ? '#fafaf9' : '#ffffff' }};">
                            <div style="width: 110px; display: flex; align-items: center; gap: 8px;">
                                <span style="width: 28px; height: 28px; border-radius: 6px; background: {{ in_array($hari, ['Sabtu', 'Minggu']) ? '#fee2e2' : '#e0f2fe' }}; color: {{ in_array($hari, ['Sabtu', 'Minggu']) ? '#dc2626' : '#0284c7' }}; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                    <i class="{{ $hari_icons[$hari] }}"></i>
                                </span>
                                <input type="hidden" name="hari[]" value="{{ $hari }}">
                                <span style="font-size: 13px; font-weight: 700; color: {{ in_array($hari, ['Sabtu', 'Minggu']) ? '#dc2626' : '#1e293b' }};">{{ $hari }}</span>
                            </div>
                            <div style="flex: 1; position: relative;">
                                <select name="kode_jam_kerja[]" class="form-select select-jam-kerja-v2" style="width: 100%; height: 38px; padding-left: 12px; padding-right: 32px; font-size: 12.5px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer; transition: all 0.15s ease;">
                                    <option value="">-- Libur / Tidak Ada Jam Kerja --</option>
                                    @foreach ($jamkerja as $d)
                                        @php
                                            $isSelected = array_key_exists($hari, $jamkerjabyday) && $jamkerjabyday[$hari] == $d->kode_jam_kerja;
                                        @endphp
                                        <option value="{{ $d->kode_jam_kerja }}" {{ $isSelected ? 'selected' : '' }}>
                                            {{ $d->nama_jam_kerja }} ({{ $d->jam_masuk }} - {{ $d->jam_pulang }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div style="margin-top: 16px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="height: 38px; padding: 0 16px; font-size: 13px; font-weight: 600; color: #64748b; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="submit" id="btnSimpanJamKerjaV2" style="height: 38px; padding: 0 20px; font-size: 13px; font-weight: 700; color: #ffffff; background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956)); border: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 6px -1px rgba(var(--theme-color-rgb, 10, 92, 65), 0.3); cursor: pointer; transition: transform 0.15s ease;">
                    <i class="ti ti-device-floppy"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tab 2 Content: Set Jam Kerja By Date (Calendar Shift Roster) -->
    <div id="tab-pane-date" style="display: none;">
        <!-- Calendar Filters & Actions -->
        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-bottom: 12px;">
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px;">Pilih Bulan</label>
                <select name="bulan" id="bulan_v2" style="width: 100%; height: 36px; padding: 0 12px; font-size: 12.5px; font-weight: 600; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                    @foreach ($list_bulan as $d)
                        <option {{ $d['kode_bulan'] == date('n') ? 'selected' : '' }} value="{{ $d['kode_bulan'] }}">{{ $d['nama_bulan'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 4px;">Pilih Tahun</label>
                <select name="tahun" id="tahun_v2" style="width: 100%; height: 36px; padding: 0 12px; font-size: 12.5px; font-weight: 600; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer;">
                    @for ($t = $start_year; $t <= date('Y'); $t++)
                        <option {{ $t == date('Y') ? 'selected' : '' }} value="{{ $t }}">{{ $t }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <!-- Shift Palette Pills -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 12px; margin-bottom: 12px;">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.02em;">Template Shift Tersedia (Klik untuk Menempelkan / Drag):</div>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;" id="jamKerjaTemplatesV2">
                @foreach ($jamkerja as $d)
                    <div class="jam-kerja-template-v2" data-kode="{{ $d->kode_jam_kerja }}" data-nama="{{ $d->nama_jam_kerja }}" data-jam="{{ $d->jam_masuk }} - {{ $d->jam_pulang }}" style="cursor: pointer; user-select: none;">
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; font-size: 11.5px; font-weight: 600; border-radius: 7px; background: #ffffff; color: #1e293b; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.15s ease;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--theme-color-1, #0a5c41);"></span>
                            <span>{{ $d->nama_jam_kerja }}</span>
                            <span style="color: #64748b; font-size: 10.5px; font-weight: 500;">({{ $d->jam_masuk }} - {{ $d->jam_pulang }})</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Toolbar Mode Seleksi Batch -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <button type="button" id="btn-mode-seleksi-v2" style="height: 32px; padding: 0 12px; font-size: 11.5px; font-weight: 600; border-radius: 7px; background: #ffffff; color: #334155; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 0.15s ease;">
                    <i class="ti ti-select"></i>
                    <span>Mode Multi-Pilih Tanggal</span>
                </button>
                <span id="count-selected-v2" style="display: none; padding: 3px 8px; font-size: 11px; font-weight: 700; border-radius: 6px; background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd;">0 tanggal terpilih</span>
            </div>
            <div style="display: flex; gap: 6px;">
                <button type="button" id="btn-apply-jam-kerja-v2" style="display: none; height: 32px; padding: 0 12px; font-size: 11.5px; font-weight: 700; border-radius: 7px; background: #059669; color: #ffffff; border: none; display: inline-flex; align-items: center; gap: 4px; cursor: pointer; box-shadow: 0 2px 4px rgba(5,150,105,0.25);">
                    <i class="ti ti-check"></i>
                    <span>Terapkan Shift</span>
                </button>
                <button type="button" id="btn-clear-selection-v2" style="display: none; height: 32px; padding: 0 10px; font-size: 11.5px; font-weight: 600; border-radius: 7px; background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; cursor: pointer;">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>

        <!-- Calendar Container -->
        <div id="calendar-container-v2" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <!-- Rendered dynamically -->
        </div>

        <!-- Shift History List Accordion -->
        <div style="margin-top: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 700; color: #334155;">Daftar Shift Khusus Bulan Ini</span>
                <span style="font-size: 11px; color: #64748b;">(Override Jadwal Reguler)</span>
            </div>
            <div style="max-height: 160px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead>
                        <tr style="background: #f1f5f9; color: #475569; text-align: left; border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 6px 10px; font-weight: 600;">Tanggal</th>
                            <th style="padding: 6px 10px; font-weight: 600;">Nama Shift</th>
                            <th style="padding: 6px 10px; font-weight: 600; width: 40px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="getjamkerjabydate-v2">
                        <!-- Loaded dynamically via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .tab-pill-btn {
        background: transparent;
        color: #64748b;
    }
    .tab-pill-btn.active {
        background: #ffffff !important;
        color: var(--theme-color-1, #0a5c41) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .tab-pill-btn:hover:not(.active) {
        color: #1e293b;
    }
    .select-jam-kerja-v2:focus {
        border-color: var(--theme-color-1, #0a5c41) !important;
        box-shadow: 0 0 0 3px rgba(var(--theme-color-rgb, 10, 92, 65), 0.12) !important;
    }
    .jam-kerja-template-v2.active-palette span {
        background: var(--theme-color-1, #0a5c41) !important;
        color: #ffffff !important;
        border-color: var(--theme-color-1, #0a5c41) !important;
    }
    .jam-kerja-template-v2.active-palette span span {
        color: rgba(255,255,255,0.85) !important;
    }

    /* Modern V2 Calendar Grid */
    .cal-grid-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .cal-grid-cell-hdr {
        padding: 8px 4px;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .cal-grid-body {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background: #e2e8f0;
    }
    .cal-day-cell {
        background: #ffffff;
        min-height: 76px;
        padding: 6px;
        position: relative;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all 0.12s ease;
    }
    .cal-day-cell:hover {
        background: #f8fafc;
    }
    .cal-day-cell.other-month {
        background: #fafaf9;
        opacity: 0.45;
        cursor: default;
    }
    .cal-day-cell.today {
        background: rgba(var(--theme-color-rgb, 10, 92, 65), 0.05);
    }
    .cal-day-cell.today .cal-num {
        background: var(--theme-color-1, #0a5c41);
        color: #ffffff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .cal-num {
        font-size: 11.5px;
        font-weight: 700;
        color: #334155;
        line-height: 1;
    }
    .cal-shift-badge {
        background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));
        color: #ffffff;
        padding: 2px 4px;
        border-radius: 4px;
        font-size: 9.5px;
        font-weight: 600;
        line-height: 1.2;
        margin-top: 4px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }
    .cal-shift-badge .del-shift-btn {
        opacity: 0;
        cursor: pointer;
        font-size: 10px;
        padding-left: 2px;
        transition: opacity 0.15s ease;
    }
    .cal-shift-badge:hover .del-shift-btn {
        opacity: 1;
    }
    .cal-day-cell.is-selected {
        background: #dbeafe !important;
        outline: 2px solid #3b82f6;
        outline-offset: -2px;
    }
    .cal-day-cell.cal-drop-hover {
        background: rgba(var(--theme-color-rgb, 10, 92, 65), 0.15) !important;
        outline: 2px dashed var(--theme-color-1, #0a5c41) !important;
        outline-offset: -2px;
        transform: scale(1.02);
    }

    /* Pop-up Action Buttons */
    .btn-swal-confirm-v2 {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 38px !important;
        padding: 0 16px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956)) !important;
        border: none !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 6px -1px rgba(var(--theme-color-rgb, 10, 92, 65), 0.35) !important;
        cursor: pointer !important;
        transition: transform 0.1s ease, box-shadow 0.15s ease !important;
    }
    .btn-swal-confirm-v2:hover {
        opacity: 0.95 !important;
        transform: translateY(-1px) !important;
    }
    .btn-swal-cancel-v2 {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 38px !important;
        padding: 0 14px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
    }
    .btn-swal-cancel-v2:hover {
        background: #e2e8f0 !important;
        color: #0f172a !important;
    }
</style>

<script>
$(document).ready(function() {
    // 1. Tab Switching
    $('#tab-btn-day').on('click', function() {
        $(this).addClass('active');
        $('#tab-btn-date').removeClass('active');
        $('#tab-pane-day').show();
        $('#tab-pane-date').hide();
    });

    $('#tab-btn-date').on('click', function() {
        $(this).addClass('active');
        $('#tab-btn-day').removeClass('active');
        $('#tab-pane-day').hide();
        $('#tab-pane-date').show();
        loadJamKerjaDataV2();
        loadJamKerjaListV2();
    });

    // 2. Calendar Logic
    let curDate = new Date();
    let curMonth = curDate.getMonth();
    let curYear = curDate.getFullYear();
    let jamKerjaDataV2 = [];
    let selectedDatesV2 = new Set();
    let isMultiSelect = false;
    let selectedPalette = null;
    let selectionStartDateV2 = null;
    let isDraggingSelectionV2 = false;

    function renderCalendarV2() {
        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const dayNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

        const firstDay = new Date(curYear, curMonth, 1);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());

        let html = `
            <div class="cal-grid-header">
                ${dayNames.map((d, idx) => `<div class="cal-grid-cell-hdr" style="color: ${idx === 0 || idx === 6 ? '#dc2626' : '#64748b'};">${d}</div>`).join('')}
            </div>
            <div class="cal-grid-body">
        `;

        for (let i = 0; i < 42; i++) {
            const d = new Date(startDate);
            d.setDate(startDate.getDate() + i);

            const isCurrentMonth = d.getMonth() === curMonth;
            const isToday = d.toDateString() === new Date().toDateString();
            const dateStr = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');

            const shifts = jamKerjaDataV2.filter(jk => {
                const s = jk.tanggal ? jk.tanggal.split(' ')[0] : '';
                return s === dateStr;
            });

            let shiftBadge = '';
            if (shifts.length > 0) {
                const s = shifts[0];
                shiftBadge = `
                    <div class="cal-shift-badge" data-kode="${s.kode_jam_kerja}" data-tanggal="${dateStr}">
                        <span class="truncate" style="max-width: 50px;">${s.nama_jam_kerja || 'Shift'}</span>
                        <span class="del-shift-btn" title="Hapus Shift">&times;</span>
                    </div>
                `;
            }

            const isSel = selectedDatesV2.has(dateStr);

            html += `
                <div class="cal-day-cell ${!isCurrentMonth ? 'other-month' : ''} ${isToday ? 'today' : ''} ${isSel ? 'is-selected' : ''}" data-date="${dateStr}">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="cal-num">${d.getDate()}</span>
                    </div>
                    ${shiftBadge}
                </div>
            `;
        }

        html += `</div>`;
        $('#calendar-container-v2').html(html);

        // Initialize Drag and Drop
        setupDragAndDropV2();
    }

    function setupDragAndDropV2() {
        if (typeof $.fn.draggable === 'undefined' || typeof $.fn.droppable === 'undefined') {
            return;
        }

        // Make Shift Palette templates draggable
        $('.jam-kerja-template-v2').draggable({
            helper: 'clone',
            cursor: 'grabbing',
            zIndex: 999999,
            revert: 'invalid',
            disabled: isMultiSelect,
            start: function(e, ui) {
                $(this).addClass('opacity-50 scale-95');
                $(ui.helper).css({
                    'box-shadow': '0 10px 25px -5px rgba(0, 0, 0, 0.25)',
                    'transform': 'scale(1.05)',
                    'z-index': 999999
                });
            },
            stop: function(e, ui) {
                $(this).removeClass('opacity-50 scale-95');
            }
        });

        // Make Calendar Day Cells droppable
        if (!isMultiSelect) {
            $('.cal-day-cell:not(.other-month)').droppable({
                accept: '.jam-kerja-template-v2',
                hoverClass: 'cal-drop-hover',
                drop: function(e, ui) {
                    const targetDate = $(this).data('date');
                    const dragged = ui.draggable;
                    const kode = dragged.data('kode');
                    const nama = dragged.data('nama');

                    // Check if already has shift
                    const existing = jamKerjaDataV2.find(jk => {
                        const s = jk.tanggal ? jk.tanggal.split(' ')[0] : '';
                        return s === targetDate;
                    });

                    if (existing) {
                        Swal.fire({
                            title: 'Ganti Shift?',
                            text: `Tanggal ${targetDate} sudah memiliki shift (${existing.nama_jam_kerja}). Ganti dengan ${nama}?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: 'var(--theme-color-1, #0a5c41)',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Ganti',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                applySingleShift(targetDate, kode);
                            }
                        });
                        return;
                    }

                    applySingleShift(targetDate, kode);
                }
            });
        }
    }

    function loadJamKerjaDataV2() {
        const bln = $("#bulan_v2").val() || (curMonth + 1);
        const thn = $("#tahun_v2").val() || curYear;

        $('#calendar-container-v2').html('<div style="text-align: center; padding: 30px; font-size: 13px; color: #64748b;"><i class="ti ti-loader animate-spin" style="font-size: 20px;"></i><p style="margin-top: 4px;">Memuat kalender...</p></div>');

        $.ajax({
            type: 'POST',
            url: "{{ route('karyawan.getjamkerjabydate') }}",
            data: {
                _token: "{{ csrf_token() }}",
                nik: "{{ $karyawan->nik }}",
                bulan: bln,
                tahun: thn
            },
            success: function(res) {
                jamKerjaDataV2 = res || [];
                renderCalendarV2();
            },
            error: function() {
                $('#calendar-container-v2').html('<div style="text-align: center; padding: 20px; color: #ef4444; font-size: 12px;">Gagal memuat data kalender</div>');
            }
        });
    }

    function loadJamKerjaListV2() {
        const bln = $("#bulan_v2").val() || (curMonth + 1);
        const thn = $("#tahun_v2").val() || curYear;

        $.ajax({
            type: 'POST',
            url: "{{ route('karyawan.getjamkerjabydate') }}",
            data: {
                _token: "{{ csrf_token() }}",
                nik: "{{ $karyawan->nik }}",
                bulan: bln,
                tahun: thn
            },
            success: function(res) {
                const tbody = $("#getjamkerjabydate-v2");
                tbody.empty();
                if (!res || res.length === 0) {
                    tbody.append(`<tr><td colspan="3" style="text-align: center; padding: 12px; color: #94a3b8; font-size: 11.5px;">Belum ada shift khusus di bulan ini</td></tr>`);
                    return;
                }
                res.forEach(d => {
                    tbody.append(`
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 6px 10px; font-weight: 600; color: #334155;">${d.tanggal}</td>
                            <td style="padding: 6px 10px; color: #475569;">
                                <span style="font-weight: 600; color: var(--theme-color-1, #0a5c41);">${d.nama_jam_kerja}</span> 
                                <span style="font-size: 10.5px; color: #64748b;">(${d.jam_masuk} - ${d.jam_pulang})</span>
                            </td>
                            <td style="padding: 6px 10px; text-align: center;">
                                <button type="button" class="btn-del-shift-v2" data-date="${d.tanggal}" data-kode="${d.kode_jam_kerja}" style="border: none; background: transparent; color: #ef4444; cursor: pointer; padding: 2px;">
                                    <i class="ti ti-trash" style="font-size: 14px;"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            }
        });
    }

    // Select month/year changed
    $('#bulan_v2, #tahun_v2').on('change', function() {
        const b = parseInt($('#bulan_v2').val());
        const t = parseInt($('#tahun_v2').val());
        if (b && t) {
            curMonth = b - 1;
            curYear = t;
            selectedDatesV2.clear();
            updateSelectionUIV2();
            loadJamKerjaDataV2();
            loadJamKerjaListV2();
        }
    });

    // Palette Shift selection
    $('.jam-kerja-template-v2').on('click', function() {
        if ($(this).hasClass('active-palette')) {
            $(this).removeClass('active-palette');
            selectedPalette = null;
        } else {
            $('.jam-kerja-template-v2').removeClass('active-palette');
            $(this).addClass('active-palette');
            selectedPalette = {
                kode: $(this).data('kode'),
                nama: $(this).data('nama'),
                jam: $(this).data('jam')
            };
            if (window.toastr) toastr.info(`Shift ${selectedPalette.nama} dipilih. Klik tanggal di kalender atau drag ke tanggal.`);
        }
    });

    // Multi-Select Mode Toggle
    $('#btn-mode-seleksi-v2').on('click', function() {
        isMultiSelect = !isMultiSelect;
        selectedDatesV2.clear();
        selectionStartDateV2 = null;
        updateSelectionUIV2();

        if (isMultiSelect) {
            $(this).css({'background': 'var(--theme-color-1, #0a5c41)', 'color': '#ffffff', 'border-color': 'var(--theme-color-1, #0a5c41)'});
            $(this).find('span').text('Keluar Multi-Pilih');
            $('.jam-kerja-template-v2').removeClass('active-palette');
            selectedPalette = null;
        } else {
            $(this).css({'background': '#ffffff', 'color': '#334155', 'border-color': '#cbd5e1'});
            $(this).find('span').text('Mode Multi-Pilih Tanggal');
        }
        renderCalendarV2();
    });

    function updateSelectionUIV2() {
        const count = selectedDatesV2.size;
        if (count > 0) {
            $('#count-selected-v2').text(`${count} tanggal terpilih`).show();
            $('#btn-apply-jam-kerja-v2').show();
            $('#btn-clear-selection-v2').show();
        } else {
            $('#count-selected-v2').hide();
            $('#btn-apply-jam-kerja-v2').hide();
            $('#btn-clear-selection-v2').hide();
        }
    }

    $('#btn-clear-selection-v2').on('click', function() {
        selectedDatesV2.clear();
        selectionStartDateV2 = null;
        updateSelectionUIV2();
        renderCalendarV2();
    });

    function selectDateRangeV2(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        let d1 = start, d2 = end;
        if (d1 > d2) { d1 = end; d2 = start; }

        selectedDatesV2.clear();
        let curr = new Date(d1);
        while (curr <= d2) {
            const dateStr = curr.getFullYear() + '-' +
                String(curr.getMonth() + 1).padStart(2, '0') + '-' +
                String(curr.getDate()).padStart(2, '0');
            selectedDatesV2.add(dateStr);
            curr.setDate(curr.getDate() + 1);
        }
        updateSelectionUIV2();
        renderCalendarV2();
    }

    // Mouse Down on Calendar Day Cell (Drag selection start)
    $(document).off('mousedown.v2cal', '.cal-day-cell').on('mousedown.v2cal', '.cal-day-cell', function(e) {
        if (!isMultiSelect) return;
        if ($(e.target).closest('.del-shift-btn').length) return;
        if ($(this).hasClass('other-month')) return;

        const date = $(this).data('date');

        if (e.shiftKey && selectionStartDateV2) {
            selectDateRangeV2(selectionStartDateV2, date);
            return;
        }

        isDraggingSelectionV2 = true;
        selectionStartDateV2 = date;

        if (e.ctrlKey || e.metaKey) {
            if (selectedDatesV2.has(date)) {
                selectedDatesV2.delete(date);
            } else {
                selectedDatesV2.add(date);
            }
        } else {
            selectedDatesV2.clear();
            selectedDatesV2.add(date);
        }

        updateSelectionUIV2();
        renderCalendarV2();
    });

    // Mouse Enter during Drag (Block drag range selection)
    $(document).off('mouseenter.v2cal', '.cal-day-cell').on('mouseenter.v2cal', '.cal-day-cell', function(e) {
        if (!isMultiSelect || !isDraggingSelectionV2 || !selectionStartDateV2) return;
        if ($(this).hasClass('other-month')) return;

        const date = $(this).data('date');
        selectDateRangeV2(selectionStartDateV2, date);
    });

    // Mouse Up anywhere ends drag selection
    $(document).off('mouseup.v2cal').on('mouseup.v2cal', function() {
        isDraggingSelectionV2 = false;
    });

    // Click on Calendar Day Cell
    $(document).off('click.v2cal', '.cal-day-cell').on('click.v2cal', '.cal-day-cell', function(e) {
        if ($(e.target).closest('.del-shift-btn').length) return;
        if ($(this).hasClass('other-month')) return;

        const date = $(this).data('date');

        if (isMultiSelect) {
            // Already handled in mousedown / drag
            return;
        }

        // If palette active, apply directly
        if (selectedPalette) {
            applySingleShift(date, selectedPalette.kode);
        } else {
            // Prompt choose shift
            showShiftPickerModal([date]);
        }
    });

    function applySingleShift(date, kode) {
        $.ajax({
            type: 'POST',
            url: "{{ route('karyawan.storejamkerjabydate') }}",
            data: {
                _token: "{{ csrf_token() }}",
                nik: "{{ $karyawan->nik }}",
                tanggal: date,
                kode_jam_kerja: kode
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan',
                        text: 'Shift khusus tanggal ' + date + ' berhasil diatur',
                        timer: 1000,
                        showConfirmButton: false
                    });
                    loadJamKerjaDataV2();
                    loadJamKerjaListV2();
                } else {
                    Swal.fire('Error', res.message || 'Gagal mengatur shift', 'error');
                }
            }
        });
    }

    function showShiftPickerModal(datesArray) {
        let options = '';
        @foreach ($jamkerja as $d)
            options += `<option value="{{ $d->kode_jam_kerja }}">{{ $d->nama_jam_kerja }} ({{ $d->jam_masuk }} - {{ $d->jam_pulang }})</option>`;
        @endforeach

        let dateInfoText = datesArray.length === 1 
            ? `Tanggal: <b>${datesArray[0]}</b>` 
            : `<b>${datesArray.length}</b> tanggal terpilih`;

        Swal.fire({
            html: `
                <div style="text-align: left; padding: 4px 2px;">
                    <!-- Header -->
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                        <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(var(--theme-color-rgb, 10, 92, 65), 0.1); color: var(--theme-color-1, #0a5c41); display: flex; align-items: center; justify-content: center; font-size: 17px; shrink: 0;">
                            <i class="ti ti-clock-edit"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.3;">Tentukan Jam Kerja</h4>
                            <p style="font-size: 11.5px; color: #64748b; margin: 0;">${dateInfoText}</p>
                        </div>
                    </div>

                    <!-- Input Shift -->
                    <div style="margin-bottom: 4px;">
                        <label style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 6px;">Pilih Shift Kerja</label>
                        <select id="swalJamKerjaSelect" style="width: 100%; height: 40px; padding: 0 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer; transition: border-color 0.15s ease;">
                            <option value="">-- Pilih Shift Kerja --</option>
                            ${options}
                        </select>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Terapkan Shift',
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn-swal-confirm-v2',
                cancelButton: 'btn-swal-cancel-v2',
                actions: 'flex items-center justify-end gap-2 mt-4 px-2'
            },
            width: '400px',
            preConfirm: () => {
                const val = document.getElementById('swalJamKerjaSelect').value;
                if (!val) {
                    Swal.showValidationMessage('Silakan pilih salah satu jam kerja');
                    return false;
                }
                return val;
            }
        }).then((res) => {
            if (res.isConfirmed && res.value) {
                applyBatchShifts(datesArray, res.value);
            }
        });
    }

    $('#btn-apply-jam-kerja-v2').on('click', function() {
        if (selectedDatesV2.size === 0) return;
        showShiftPickerModal(Array.from(selectedDatesV2));
    });

    function applyBatchShifts(dates, kode) {
        let completed = 0;
        Swal.fire({
            title: 'Menyimpan...',
            html: `Menerapkan ke ${dates.length} tanggal`,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        dates.forEach(d => {
            $.ajax({
                type: 'POST',
                url: "{{ route('karyawan.storejamkerjabydate') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    nik: "{{ $karyawan->nik }}",
                    tanggal: d,
                    kode_jam_kerja: kode
                },
                complete: function() {
                    completed++;
                    if (completed === dates.length) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Selesai',
                            text: `Jam kerja berhasil diterapkan ke ${dates.length} tanggal`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        selectedDatesV2.clear();
                        updateSelectionUIV2();
                        loadJamKerjaDataV2();
                        loadJamKerjaListV2();
                    }
                }
            });
        });
    }

    // Delete Shift
    $(document).on('click', '.del-shift-btn, .btn-del-shift-v2', function(e) {
        e.stopPropagation();
        const date = $(this).closest('[data-tanggal]').data('tanggal') || $(this).data('date');
        const kode = $(this).closest('[data-kode]').data('kode') || $(this).data('kode');

        Swal.fire({
            title: 'Hapus Shift?',
            text: `Shift pada tanggal ${date} akan dihapus & kembali ke jadwal reguler.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus'
        }).then((res) => {
            if (res.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('karyawan.deletejamkerjabydate') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        nik: "{{ $karyawan->nik }}",
                        tanggal: date,
                        kode_jam_kerja: kode
                    },
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus',
                            timer: 1000,
                            showConfirmButton: false
                        });
                        loadJamKerjaDataV2();
                        loadJamKerjaListV2();
                    }
                });
            }
        });
    });
});
</script>
