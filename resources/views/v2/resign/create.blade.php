@php
    $themeColor1 = $general_setting->theme_color_1 ?? '#0a5c41';
    $themeColor2 = $general_setting->theme_color_2 ?? '#117956';
@endphp

<form action="{{ route('resign.store') }}" method="POST" id="formResignV2" enctype="multipart/form-data" class="space-y-4" novalidate>
    @csrf

    <div class="space-y-4">

        {{-- Banner Peringatan --}}
        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm shrink-0 mt-0.5">
                <i class="ti ti-alert-triangle"></i>
            </div>
            <div>
                <h5 class="text-xs font-bold text-amber-900">Perhatian</h5>
                <p class="text-[11px] text-amber-800 mt-0.5 leading-relaxed">
                    Menyimpan data ini akan otomatis mengubah status karyawan menjadi <strong>Non Aktif</strong> pada Data Master Karyawan.
                </p>
            </div>
        </div>

        {{-- 1. Pilih Karyawan --}}
        <div class="space-y-1.5">
            <label for="nik" class="block text-xs font-bold text-slate-700">
                Pilih Karyawan Aktif <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="nik" id="nik"
                    class="w-full px-3 py-2.5 text-xs bg-white border border-slate-300 rounded-xl text-slate-800 font-medium focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer select2-resign-v2"
                    required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($karyawan as $k)
                        <option value="{{ $k->nik }}">
                            {{ $k->nik }} - {{ $k->nama_karyawan }} ({{ $k->cabang->nama_cabang ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_nik"></div>
        </div>

        {{-- 2. Kategori Resign --}}
        <div class="space-y-1.5">
            <label for="kode_kategori" class="block text-xs font-bold text-slate-700">
                Kategori Resign / Non-Aktif <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <select name="kode_kategori" id="kode_kategori"
                    class="w-full h-10 px-3.5 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer"
                    required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($kategori as $kat)
                        <option value="{{ $kat->kode_kategori }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_kode_kategori"></div>
        </div>

        {{-- 3. Tanggal Efektif Resign --}}
        <div class="space-y-1.5">
            <label for="tanggal_resign" class="block text-xs font-bold text-slate-700">
                Tanggal Efektif Resign <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none flex items-center z-10">
                    <i class="ti ti-calendar text-base"></i>
                </span>
                <input type="text"
                       id="tanggal_resign"
                       name="tanggal_resign"
                       value="{{ date('Y-m-d') }}"
                       placeholder="Pilih Tanggal Resign"
                       autocomplete="off"
                       class="w-full h-10 pl-10 pr-3.5 text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition cursor-pointer flatpickr-date-resign"
                       required>
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_tanggal_resign"></div>
        </div>

        {{-- 4. Alasan Resign --}}
        <div class="space-y-1.5">
            <label for="alasan" class="block text-xs font-bold text-slate-700">
                Alasan Pengunduran Diri / Non-Aktif
                <span class="text-slate-400 font-normal lowercase">(opsional)</span>
            </label>
            <textarea name="alasan"
                      id="alasan"
                      rows="3"
                      placeholder="Masukkan alasan pengunduran diri..."
                      class="w-full text-xs font-medium text-slate-800 bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 placeholder-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition resize-none"></textarea>
        </div>

        {{-- 5. Upload Dokumen --}}
        <div class="space-y-1.5">
            <label for="dokumen" class="block text-xs font-bold text-slate-700">
                Upload Surat Resign / Dokumen Pendukung
                <span class="text-slate-400 font-normal lowercase">(opsional · pdf/gambar/doc · max 10MB)</span>
            </label>
            <div class="relative">
                <input type="file"
                       name="dokumen"
                       id="dokumen"
                       accept=".pdf,image/*,.doc,.docx"
                       class="w-full text-xs text-slate-700
                              file:mr-3 file:py-2 file:px-3.5
                              file:rounded-lg file:border-0
                              file:text-xs file:font-semibold
                              file:bg-slate-100 file:text-slate-700
                              hover:file:bg-slate-200
                              border border-slate-300 rounded-xl p-1
                              bg-white cursor-pointer transition">
            </div>
            <div class="text-rose-500 text-[11px] font-medium hidden error-feedback" id="err_dokumen"></div>
        </div>

    </div>

    {{-- Action Buttons --}}
    <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
        <button type="button"
                onclick="closeModal()"
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95 cursor-pointer">
            Batal
        </button>
        <button type="submit"
                id="btnSubmitResignV2"
                class="px-5 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-1.5 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, #e11d48, #be123c);">
            <i class="ti ti-user-x text-sm"></i>
            <span>Simpan &amp; Non-Aktifkan</span>
        </button>
    </div>
</form>

<script>
(function() {
    const form      = document.getElementById('formResignV2');
    const nikInput  = document.getElementById('nik');
    const katInput  = document.getElementById('kode_kategori');
    const tglInput  = document.getElementById('tanggal_resign');
    const dokInput  = document.getElementById('dokumen');

    // --- Init Select2 ---
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2-resign-v2').select2({
            dropdownParent: $('#modalInput'),
            placeholder: '-- Pilih Karyawan --',
            allowClear: true
        });
    }

    // --- Init Flatpickr ---
    if (typeof flatpickr !== 'undefined' && tglInput) {
        flatpickr(tglInput, {
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }

    // --- Helper: set field valid / invalid ---
    function setError(fieldEl, errId, msg) {
        const errEl = document.getElementById(errId);
        if (!errEl) return;
        if (msg) {
            fieldEl.classList.add('border-rose-500');
            fieldEl.classList.remove('border-slate-300', 'focus:border-emerald-500');
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
        } else {
            fieldEl.classList.remove('border-rose-500');
            fieldEl.classList.add('border-slate-300');
            errEl.textContent = '';
            errEl.classList.add('hidden');
        }
    }

    function clearErrors() {
        document.querySelectorAll('#formResignV2 .error-feedback').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        [nikInput, katInput, tglInput].forEach(el => {
            if (el) {
                el.classList.remove('border-rose-500');
                el.classList.add('border-slate-300');
            }
        });
        // Select2 container
        const s2Container = document.querySelector('.select2-resign-v2 + .select2-container .select2-selection--single');
        if (s2Container) s2Container.classList.remove('border-rose-500');
    }

    // --- Validate on submit ---
    form.addEventListener('submit', function(e) {
        clearErrors();
        let hasError = false;

        // NIK: Select2 so check value
        const nikVal = nikInput ? nikInput.value.trim() : '';
        if (!nikVal) {
            setError(nikInput, 'err_nik', 'Karyawan wajib dipilih');
            // Also highlight the Select2 rendered element
            const s2el = document.querySelector('.select2-resign-v2');
            if (s2el) {
                const sel = s2el.nextElementSibling;
                if (sel) sel.querySelector('.select2-selection--single')?.classList.add('!border-rose-500');
            }
            hasError = true;
        }

        const katVal = katInput ? katInput.value.trim() : '';
        if (!katVal) {
            setError(katInput, 'err_kode_kategori', 'Kategori resign wajib dipilih');
            hasError = true;
        }

        const tglVal = tglInput ? tglInput.value.trim() : '';
        if (!tglVal) {
            setError(tglInput, 'err_tanggal_resign', 'Tanggal efektif resign wajib diisi');
            hasError = true;
        }

        // Dokumen max 10MB check
        if (dokInput && dokInput.files.length > 0) {
            const maxBytes = 10 * 1024 * 1024;
            if (dokInput.files[0].size > maxBytes) {
                setError(dokInput, 'err_dokumen', 'Ukuran file melebihi 10MB');
                hasError = true;
            }
        }

        if (hasError) {
            e.preventDefault();
            // Scroll to first error
            const firstErr = form.querySelector('.error-feedback:not(.hidden)');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        // Show loading state on submit button
        const btn = document.getElementById('btnSubmitResignV2');
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = '<div style="width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.8s linear infinite;display:inline-block;"></div><span>Memproses...</span>';
        }
    });

    // --- Live clear error on change ---
    if (nikInput) {
        nikInput.addEventListener('change', function() { setError(nikInput, 'err_nik', ''); });
    }
    if (katInput) {
        katInput.addEventListener('change', function() { setError(katInput, 'err_kode_kategori', ''); });
    }
    if (tglInput) {
        tglInput.addEventListener('change', function() { setError(tglInput, 'err_tanggal_resign', ''); });
    }
    if (dokInput) {
        dokInput.addEventListener('change', function() { setError(dokInput, 'err_dokumen', ''); });
    }

    // Select2 change event via jQuery
    if (typeof $ !== 'undefined') {
        $('#nik').on('change', function() { setError(nikInput, 'err_nik', ''); });
    }
})();
</script>
