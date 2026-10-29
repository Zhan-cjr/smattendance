{{-- V2 Modal Body: Tambah Pelatihan Karyawan --}}
<form action="{{ route('pelatihan.store') }}" method="POST" id="formPelatihanV2" enctype="multipart/form-data" class="space-y-4" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    @csrf
    <input type="hidden" name="nik" value="{{ $nik }}">

    <!-- Nama Pelatihan -->
    <div class="space-y-1">
        <label for="nama_pelatihan" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
            Nama Pelatihan / Kursus <span style="color: #ef4444; font-weight: bold;">*</span>
        </label>
        <div style="position: relative; display: flex; align-items: center;">
            <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                <i class="ti ti-school" style="font-size: 16px;"></i>
            </span>
            <input type="text" 
                   id="nama_pelatihan" 
                   name="nama_pelatihan" 
                   required
                   placeholder="Contoh: Sertifikasi Manajemen Proyek / K3" 
                   style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
        </div>
    </div>

    <!-- Penyelenggara & Tanggal (2 Kolom) -->
    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
        <!-- Penyelenggara -->
        <div class="space-y-1">
            <label for="penyelenggara" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                Lembaga / Penyelenggara <span style="color: #ef4444; font-weight: bold;">*</span>
            </label>
            <div style="position: relative; display: flex; align-items: center;">
                <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                    <i class="ti ti-building" style="font-size: 16px;"></i>
                </span>
                <input type="text" 
                       id="penyelenggara" 
                       name="penyelenggara" 
                       required
                       placeholder="Contoh: BNSP / Kemnaker" 
                       style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: border-color 0.15s ease;">
            </div>
        </div>

        <!-- Tanggal Pelatihan -->
        <div class="space-y-1">
            <label for="tanggal_pelatihan" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
                Tanggal Pelatihan <span style="color: #ef4444; font-weight: bold;">*</span>
            </label>
            <div style="position: relative; display: flex; align-items: center;">
                <span style="position: absolute; left: 10px; pointer-events: none; color: #94a3b8; display: flex; align-items: center;">
                    <i class="ti ti-calendar" style="font-size: 16px;"></i>
                </span>
                <input type="text" 
                       id="tanggal_pelatihan" 
                       name="tanggal_pelatihan" 
                       required
                       placeholder="YYYY-MM-DD" 
                       class="flatpickr-date-v2"
                       style="width: 100%; height: 38px; padding-left: 36px; padding-right: 12px; font-size: 13px; font-weight: 500; color: #1e293b; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; cursor: pointer; transition: border-color 0.15s ease;">
            </div>
        </div>
    </div>

    <!-- Upload Sertifikat -->
    <div class="space-y-1">
        <label for="foto_pelatihan" style="display: block; font-size: 11.5px; font-weight: 600; color: #475569; margin-bottom: 4px;">
            Unggah Berkas Sertifikat / Foto <span style="font-size: 10.5px; color: #94a3b8; font-weight: normal;">(Opsional - JPG, PNG, PDF maks 2MB)</span>
        </label>
        <div style="border: 1.5px dashed #cbd5e1; border-radius: 8px; padding: 12px; text-align: center; background: #f8fafc; cursor: pointer;" onclick="document.getElementById('foto_pelatihan').click()">
            <i class="ti ti-cloud-upload" style="font-size: 24px; color: var(--theme-color-1, #0a5c41); margin-bottom: 4px; display: inline-block;"></i>
            <p style="font-size: 12px; font-weight: 600; color: #334155; margin: 0;">Pilih file sertifikat</p>
            <p id="file-chosen-text" style="font-size: 11px; color: #64748b; margin: 2px 0 0 0;">Format didukung: JPG, PNG, PDF</p>
            <input type="file" 
                   id="foto_pelatihan" 
                   name="foto" 
                   accept="image/*,.pdf" 
                   style="display: none;" 
                   onchange="if(this.files[0]){ document.getElementById('file-chosen-text').innerText = this.files[0].name; }">
        </div>
    </div>

    <!-- Modal Footer Actions -->
    <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" data-bs-dismiss="modal" style="height: 38px; padding: 0 16px; font-size: 13px; font-weight: 600; color: #475569; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer;">
            Batal
        </button>
        <button type="submit" id="btnSubmitPelatihanV2" style="height: 38px; padding: 0 20px; font-size: 13px; font-weight: 700; color: #ffffff; background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956)); border: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 6px -1px rgba(var(--theme-color-rgb, 10, 92, 65), 0.35); cursor: pointer;">
            <i class="ti ti-device-floppy"></i>
            <span>Simpan Data</span>
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    if (typeof flatpickr !== 'undefined') {
        $(".flatpickr-date-v2").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: false,
            disableMobile: true
        });
    }

    $("#formPelatihanV2").on('submit', function() {
        const btn = $("#btnSubmitPelatihanV2");
        btn.prop('disabled', true);
        btn.html('<i class="ti ti-loader animate-spin"></i><span>Menyimpan...</span>');
    });
});
</script>
