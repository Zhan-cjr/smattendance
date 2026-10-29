@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('kunjungan.store') }}" method="POST" id="formKunjunganV2" enctype="multipart/form-data" class="space-y-4" novalidate>
    @csrf

    <div class="space-y-4">
        <!-- Banner Info Ketentuan Kunjungan -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm shrink-0">
                <i class="ti ti-info-circle"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-slate-800">Pencatatan Kunjungan Lapangan</h5>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Pastikan data karyawan, tanggal kunjungan, serta deskripsi aktivitas terisi dengan jelas. Titik koordinat GPS dan foto dapat dilampirkan sebagai bukti autentik.
                </p>
            </div>
        </div>

        <!-- 1. Karyawan -->
        <div class="space-y-1.5">
            <label for="modal_nik" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="nik" id="modal_nik" class="w-full px-3 py-2.5 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer select2KaryawanModal" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawans as $karyawan)
                        <option value="{{ $karyawan->nik }}" {{ old('nik') == $karyawan->nik ? 'selected' : '' }}>
                            {{ $karyawan->nik }} - {{ $karyawan->nama_karyawan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_nik"></div>
        </div>

        <!-- 2. Grid Tanggal Kunjungan & Titik Lokasi GPS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
            <!-- Tanggal Kunjungan -->
            <div class="space-y-1.5">
                <label for="modal_tanggal_kunjungan" class="block text-xs font-bold text-slate-700">
                    Tanggal Kunjungan <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                        <i class="ti ti-calendar text-base"></i>
                    </span>
                    <input type="text" 
                           id="modal_tanggal_kunjungan" 
                           name="tanggal_kunjungan" 
                           value="{{ old('tanggal_kunjungan', date('Y-m-d')) }}" 
                           placeholder="Pilih Tanggal"
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer" 
                           required>
                </div>
                <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_tanggal_kunjungan"></div>
            </div>

            <!-- Titik GPS / Lokasi -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="modal_lokasi" class="block text-xs font-bold text-slate-700">
                        Titik Koordinat GPS
                    </label>
                    <button type="button" id="btnGetCurrentLocation" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800 inline-flex items-center gap-1 cursor-pointer">
                        <i class="ti ti-crosshair text-xs"></i>
                        <span>Ambil GPS</span>
                    </button>
                </div>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center">
                        <i class="ti ti-map-pin text-base"></i>
                    </span>
                    <input type="text" 
                           id="modal_lokasi" 
                           name="lokasi" 
                           value="{{ old('lokasi') }}" 
                           placeholder="-6.200000, 106.816666" 
                           autocomplete="off"
                           class="w-full h-10 pl-10 pr-3.5 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition font-mono">
                </div>
                <div class="text-[10.5px] text-slate-400">Contoh format: latitude,longitude</div>
            </div>
        </div>

        <!-- 3. Foto Bukti Dokumentasi -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">
                Foto Bukti Dokumentasi <span class="text-slate-400 font-normal lowercase">(opsional, max 2MB)</span>
            </label>
            <div class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-slate-50/50">
                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 shrink-0 overflow-hidden shadow-2xs" id="imagePreviewContainer">
                    <i class="ti ti-photo text-2xl" id="imagePlaceholderIcon"></i>
                    <img id="imagePreview" src="" alt="Preview" class="w-full h-full object-cover hidden">
                </div>
                <div class="flex-1 min-w-0">
                    <input type="file" 
                           id="modal_foto" 
                           name="foto" 
                           accept="image/*"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 file:cursor-pointer border border-slate-300 rounded-xl bg-white p-1">
                    <p class="text-[10.5px] text-slate-400 mt-1">Format: JPG, JPEG, PNG</p>
                </div>
            </div>
        </div>

        <!-- 4. Deskripsi Kunjungan -->
        <div class="space-y-1.5">
            <label for="modal_deskripsi" class="block text-xs font-bold text-slate-700">
                Deskripsi / Hasil Kunjungan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <textarea id="modal_deskripsi" 
                          name="deskripsi" 
                          rows="3" 
                          maxlength="1000"
                          placeholder="Tuliskan agenda, tujuan, serta hasil ringkas kunjungan..." 
                          class="w-full p-3 text-xs sm:text-sm font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                          required>{{ old('deskripsi') }}</textarea>
            </div>
            <div class="flex justify-between items-center text-[10.5px] text-slate-400">
                <span>Rincian kegiatan kunjungan</span>
                <span id="deskripsiCount">0/1000</span>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_modal_deskripsi"></div>
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
                id="btnSubmitKunjungan" 
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, {{ $themeColor1 }}, {{ $themeColor2 }});">
            <i class="ti ti-device-floppy text-sm"></i>
            <span>Simpan Kunjungan</span>
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    // Flatpickr Tanggal
    if (typeof flatpickr !== 'undefined') {
        const elDate = document.getElementById('modal_tanggal_kunjungan');
        if (elDate) {
            flatpickr(elDate, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                defaultDate: '{{ old("tanggal_kunjungan", date("Y-m-d")) }}'
            });
        }
    }

    // Select2 Karyawan
    $('.select2KaryawanModal').select2({
        placeholder: '-- Pilih Karyawan --',
        dropdownParent: $('#modalKunjungan'),
        width: '100%'
    });

    // Character counter for deskripsi
    $('#modal_deskripsi').on('input', function() {
        $('#deskripsiCount').text($(this).val().length + '/1000');
    });

    // Preview Foto Upload
    $('#modal_foto').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                $('#imagePreview').attr('src', event.target.result).removeClass('hidden');
                $('#imagePlaceholderIcon').addClass('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').attr('src', '').addClass('hidden');
            $('#imagePlaceholderIcon').removeClass('hidden');
        }
    });

    // Get Current GPS Location
    $('#btnGetCurrentLocation').click(function() {
        const btn = $(this);
        if (navigator.geolocation) {
            btn.html('<i class="ti ti-loader-2 animate-spin text-xs"></i> Mendapatkan GPS...');
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude.toFixed(6);
                    const lng = position.coords.longitude.toFixed(6);
                    $('#modal_lokasi').val(`${lat},${lng}`);
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
    $('#formKunjunganV2').on('submit', function(e) {
        const nik = $('#modal_nik').val();
        const tgl = $('#modal_tanggal_kunjungan').val();
        const deskripsi = $('#modal_deskripsi').val().trim();
        let hasError = false;

        // Reset error feedback
        $('.error-feedback').addClass('hidden').text('');
        $('#modal_deskripsi').removeClass('border-rose-500');

        if (!nik) {
            $('#err_modal_nik').text('Karyawan wajib dipilih').removeClass('hidden');
            hasError = true;
        }

        if (!tgl) {
            $('#err_modal_tanggal_kunjungan').text('Tanggal kunjungan wajib diisi').removeClass('hidden');
            hasError = true;
        }

        if (!deskripsi) {
            $('#err_modal_deskripsi').text('Deskripsi / hasil kunjungan wajib diisi').removeClass('hidden');
            $('#modal_deskripsi').addClass('border-rose-500');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            return false;
        }

        const btn = $('#btnSubmitKunjungan');
        btn.prop('disabled', true).addClass('opacity-75 cursor-not-allowed');
        btn.html(`
            <div style="width: 14px; height: 14px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: spin 0.8s linear infinite; display: inline-block;"></div>
            <span>Menyimpan...</span>
        `);
    });
});
</script>
