{{-- V2 Modal Body: Set Multi-Cabang Karyawan --}}
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
                        <span><i class="ti ti-briefcase" style="margin-right: 3px;"></i>{{ textUpperCase($karyawan->nama_dept) }}</span>
                        <span>•</span>
                        <span><i class="ti ti-map-pin" style="margin-right: 3px;"></i>Cabang Utama: <b style="color: #0f172a;">{{ textUpperCase($karyawan->nama_cabang) }}</b></span>
                    </div>
                </div>
            </div>
            <div>
                <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; font-size: 11px; font-weight: 600; border-radius: 6px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                    <i class="ti ti-building-community"></i>
                    Multi-Lokasi Presensi
                </span>
            </div>
        </div>
    </div>

    <!-- Notice Card: Cabang Utama -->
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 14px; display: flex; align-items: flex-start; gap: 10px;">
        <div style="width: 24px; height: 24px; border-radius: 6px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 14px; shrink: 0; margin-top: 1px;">
            <i class="ti ti-check"></i>
        </div>
        <div style="font-size: 12px; color: #166534; line-height: 1.4;">
            <b>Cabang Utama: {{ textUpperCase($karyawan->nama_cabang) }}</b> sudah otomatis terdaftar secara permanen. Anda dapat mencentang cabang tambahan di bawah ini jika karyawan diizinkan melakukan absensi di cabang lain.
        </div>
    </div>

    <!-- Form Set Multi-Cabang -->
    <form action="{{ route('karyawan.storecabang', Crypt::encrypt($karyawan->nik)) }}" id="formSetCabangV2" method="POST">
        @csrf
        <div>
            <!-- Header Section with Select All Toggle -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="font-size: 12px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.03em;">
                    Daftar Cabang Tambahan
                </div>
                <button type="button" id="btnToggleSelectAllCabang" style="font-size: 11.5px; font-weight: 600; color: var(--theme-color-1, #0a5c41); background: transparent; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                    <i class="ti ti-checks"></i>
                    <span id="txtToggleSelectAll">Pilih Semua</span>
                </button>
            </div>

            <!-- Branch Grid Cards -->
            @php
                $kode_cabang_array = $kode_cabang_array ?? [];
            @endphp
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
                @forelse ($cabang as $c)
                    @php
                        $isChecked = in_array($c->kode_cabang, $kode_cabang_array);
                    @endphp
                    <label class="cabang-select-card {{ $isChecked ? 'is-active' : '' }}" for="cabang_v2_{{ $c->kode_cabang }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 10px; border: 1.5px solid {{ $isChecked ? 'var(--theme-color-1, #0a5c41)' : '#e2e8f0' }}; background: {{ $isChecked ? 'rgba(var(--theme-color-rgb, 10, 92, 65), 0.04)' : '#ffffff' }}; cursor: pointer; transition: all 0.15s ease; user-select: none;">
                        <input type="checkbox" 
                               name="kode_cabang_array[]" 
                               value="{{ $c->kode_cabang }}"
                               id="cabang_v2_{{ $c->kode_cabang }}"
                               class="cabang-checkbox-v2"
                               {{ $isChecked ? 'checked' : '' }}
                               style="width: 17px; height: 17px; border-radius: 4px; accent-color: var(--theme-color-1, #0a5c41); cursor: pointer;">
                        
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $isChecked ? 'rgba(var(--theme-color-rgb, 10, 92, 65), 0.12)' : '#f1f5f9' }}; color: {{ $isChecked ? 'var(--theme-color-1, #0a5c41)' : '#64748b' }}; display: flex; align-items: center; justify-content: center; font-size: 16px; shrink: 0;">
                            <i class="ti ti-building"></i>
                        </div>

                        <div style="min-width: 0; flex: 1;">
                            <h5 style="font-size: 13px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.3;" class="truncate">{{ $c->nama_cabang }}</h5>
                            <span style="font-size: 11px; color: #64748b; font-family: monospace;">Kode: {{ $c->kode_cabang }}</span>
                        </div>
                    </label>
                @empty
                    <div style="grid-column: span 2; text-align: center; padding: 24px; color: #94a3b8; font-size: 12.5px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px;">
                        Tidak ada cabang tambahan yang tersedia.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div style="margin-top: 20px; padding-top: 14px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" data-bs-dismiss="modal" style="height: 38px; padding: 0 16px; font-size: 13px; font-weight: 600; color: #475569; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; transition: all 0.15s ease;">
                Batal
            </button>
            <button type="submit" id="btnSubmitCabangV2" style="height: 38px; padding: 0 20px; font-size: 13px; font-weight: 700; color: #ffffff; background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956)); border: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 6px -1px rgba(var(--theme-color-rgb, 10, 92, 65), 0.35); cursor: pointer; transition: transform 0.15s ease;">
                <i class="ti ti-device-floppy"></i>
                <span>Simpan Cabang</span>
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Checkbox change visual toggles
    $('.cabang-checkbox-v2').on('change', function() {
        const card = $(this).closest('.cabang-select-card');
        if ($(this).is(':checked')) {
            card.css({
                'border-color': 'var(--theme-color-1, #0a5c41)',
                'background': 'rgba(var(--theme-color-rgb, 10, 92, 65), 0.04)'
            });
            card.find('.ti-building').parent().css({
                'background': 'rgba(var(--theme-color-rgb, 10, 92, 65), 0.12)',
                'color': 'var(--theme-color-1, #0a5c41)'
            });
        } else {
            card.css({
                'border-color': '#e2e8f0',
                'background': '#ffffff'
            });
            card.find('.ti-building').parent().css({
                'background': '#f1f5f9',
                'color': '#64748b'
            });
        }
        updateToggleSelectAllState();
    });

    // Select All / Deselect All Toggle
    $('#btnToggleSelectAllCabang').on('click', function() {
        const checkboxes = $('.cabang-checkbox-v2');
        const allChecked = checkboxes.length > 0 && checkboxes.filter(':checked').length === checkboxes.length;

        checkboxes.prop('checked', !allChecked).trigger('change');
    });

    function updateToggleSelectAllState() {
        const checkboxes = $('.cabang-checkbox-v2');
        const allChecked = checkboxes.length > 0 && checkboxes.filter(':checked').length === checkboxes.length;
        $('#txtToggleSelectAll').text(allChecked ? 'Batalkan Semua' : 'Pilih Semua');
    }

    updateToggleSelectAllState();

    // Form Submit with Loading Feedback
    $('#formSetCabangV2').on('submit', function() {
        const btn = $('#btnSubmitCabangV2');
        btn.prop('disabled', true);
        btn.html('<i class="ti ti-loader animate-spin"></i><span>Menyimpan...</span>');
    });
});
</script>
