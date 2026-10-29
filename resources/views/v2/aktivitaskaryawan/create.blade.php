@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('aktivitaskaryawan.store') }}" method="POST" id="formAktivitasV2" enctype="multipart/form-data" class="space-y-4" novalidate>
    @csrf

    <div class="space-y-4">
        <!-- Banner Info Ketentuan Aktivitas -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Laporan Aktivitas Harian</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Isi detail ringkasan pekerjaan atau progres tugas yang diselesaikan. Anda dapat menyertakan foto dokumentasi dan titik lokasi pengerjaan.
                </p>
            </div>
        </div>

        <!-- 1. Karyawan -->
        <div class="space-y-1.5">
            <label for="modal_aktivitas_nik" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="nik" id="modal_aktivitas_nik" class="w-full px-3 py-2.5 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer select2KaryawanAktivitasModal" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawans as $karyawan)
                        <option value="{{ $karyawan->nik }}" {{ old('nik') == $karyawan->nik ? 'selected' : '' }}>
                            {{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_aktivitas_nik"></div>
        </div>

        <!-- 2. Titik Lokasi GPS -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="modal_aktivitas_lokasi" class="block text-xs font-bold text-slate-700">
                    Titik Koordinat Lokasi
                </label>
                <button type="button" id="btnGetAktivitasLocation" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 inline-flex items-center gap-1 cursor-pointer">
                    <i class="ti ti-crosshair text-xs"></i>
                    <span>Ambil GPS</span>
                </button>
            </div>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                    <i class="ti ti-map-pin text-base"></i>
                </span>
                <input type="text" 
                       id="modal_aktivitas_lokasi" 
                       name="lokasi" 
                       value="{{ old('lokasi') }}" 
                       placeholder="-6.200000, 106.816666" 
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition font-mono">
            </div>
            <div class="text-[10.5px] text-slate-400">Contoh format: latitude,longitude</div>
        </div>

        <!-- 3. Foto Bukti Aktivitas -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
                Foto Bukti Aktivitas <span class="text-slate-400 font-normal lowercase">(opsional, max 2MB)</span>
            </label>
            <div class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-slate-50/50">
                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 shrink-0 overflow-hidden shadow-2xs" id="imageAktivitasPreviewContainer">
                    <i class="ti ti-photo text-2xl" id="imageAktivitasPlaceholderIcon"></i>
                    <img id="imageAktivitasPreview" src="" alt="Preview" class="w-full h-full object-cover hidden">
                </div>
                <div class="flex-1 min-w-0">
                    <input type="file" 
                           id="modal_aktivitas_foto" 
                           name="foto" 
                           accept="image/*"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 file:cursor-pointer border border-slate-300 rounded-xl bg-white p-1">
                    <p class="text-[10.5px] text-slate-400 mt-1">Format: JPG, JPEG, PNG</p>
                </div>
            </div>
        </div>

        <!-- 4. Uraian Aktivitas -->
        <div class="space-y-1.5">
            <label for="modal_aktivitas_text" class="block text-xs font-bold text-slate-700">
                Deskripsi / Rincian Aktivitas <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <textarea id="modal_aktivitas_text" 
                          name="aktivitas" 
                          rows="4" 
                          maxlength="1000"
                          placeholder="Jelaskan uraian pekerjaan atau progres tugas yang diselesaikan hari ini..." 
                          class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                          required>{{ old('aktivitas') }}</textarea>
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Rincian kegiatan pekerjaan</span>
                <span id="aktivitasTextCount">0/1000</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_aktivitas_text"></div>
        </div>
    </div>

    <!-- Modal Footer Actions (Standard V2) -->
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button" 
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitAktivitasV2" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Aktivitas</span>
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Select2 Karyawan
    $('.select2KaryawanAktivitasModal').select2({
        placeholder: '-- Pilih Karyawan --',
        dropdownParent: $('#modalAktivitas'),
        width: '100%'
    });

    // Character counter
    $('#modal_aktivitas_text').on('input', function() {
        $('#aktivitasTextCount').text($(this).val().length + '/1000');
    });

    // Preview Foto Upload
    $('#modal_aktivitas_foto').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                $('#imageAktivitasPreview').attr('src', event.target.result).removeClass('hidden');
                $('#imageAktivitasPlaceholderIcon').addClass('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            $('#imageAktivitasPreview').attr('src', '').addClass('hidden');
            $('#imageAktivitasPlaceholderIcon').removeClass('hidden');
        }
    });

    // Get Current GPS Location
    $('#btnGetAktivitasLocation').click(function() {
        const btn = $(this);
        if (navigator.geolocation) {
            btn.html('<i class="ti ti-loader-2 animate-spin text-xs"></i> Mendapatkan GPS...');
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);
                    $('#modal_aktivitas_lokasi').val(`${lat},${lng}`);
                    btn.html('<i class="ti ti-check text-xs text-emerald-600"></i> <span class="text-emerald-700">GPS Ditemukan</span>');
                },
                function(error) {
                    btn.html('<i class="ti ti-crosshair text-xs"></i> Ambil GPS');
                    Swal.fire({
                        title: 'GPS Gagal',
                        text: 'Tidak dapat mengakses lokasi perangkat Anda. Pastikan izin lokasi browser aktif.',
                        icon: 'warning',
                        confirmButtonColor: '{{ $themeColor1 }}',
                        customClass: { popup: 'rounded-2xl' }
                    });
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            Swal.fire({
                title: 'Tidak Didukung',
                text: 'Geolocation tidak didukung oleh browser Anda.',
                icon: 'warning',
                confirmButtonColor: '{{ $themeColor1 }}',
                customClass: { popup: 'rounded-2xl' }
            });
        }
    });

    // Form Submit with Validation & Loading
    $('#formAktivitasV2').on('submit', function(e) {
        const nik = $('#modal_aktivitas_nik').val();
        const aktivitas = $('#modal_aktivitas_text').val().trim();
        let hasError = false;

        // Reset error feedback
        $('.error-feedback').addClass('hidden').text('');
        $('#modal_aktivitas_text').removeClass('border-rose-500');

        if (!nik) {
            $('#err_modal_aktivitas_nik').text('Karyawan wajib dipilih').removeClass('hidden');
            hasError = true;
        }

        if (!aktivitas) {
            $('#err_modal_aktivitas_text').text('Deskripsi aktivitas wajib diisi').removeClass('hidden');
            $('#modal_aktivitas_text').addClass('border-rose-500');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            return false;
        }

        const btn = $('#btnSubmitAktivitasV2');
        btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
        btn.html(`
            <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
            <span>Menyimpan...</span>
        `);
    });
});
</script>
