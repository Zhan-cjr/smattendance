<form action="{{ route('karyawan.import_proses') }}" method="POST" enctype="multipart/form-data" id="frmImportV2" class="space-y-6">
    @csrf

    <!-- Top Action Card: Download Template Banner -->
    <div class="rounded-2xl p-4 sm:p-5 border border-emerald-100 bg-gradient-to-r from-emerald-50/80 via-teal-50/50 to-emerald-50/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg shrink-0 shadow-sm shadow-emerald-600/20">
                <i class="ti ti-file-download"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Format Template Excel</h4>
                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                    Unduh format resmi excel sebelum mengisi dan mengunggah data karyawan agar struktur kolom sesuai sistem.
                </p>
            </div>
        </div>
        <a href="{{ route('karyawan.download_template') }}" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-emerald-50 text-emerald-700 hover:text-emerald-800 border border-emerald-200/90 rounded-xl text-xs font-bold transition-all shadow-2xs shrink-0 active:scale-95 group">
            <i class="ti ti-download text-sm group-hover:-translate-y-0.5 transition-transform"></i>
            <span>Download Template</span>
        </a>
    </div>

    <!-- Drag & Drop / File Input Zone -->
    <div class="space-y-2">
        <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
            <i class="ti ti-cloud-upload text-emerald-600 text-sm"></i>
            <span>Pilih Berkas Excel (.xlsx / .xls)</span>
            <span class="text-rose-500">*</span>
        </label>
        
        <div class="relative border-2 border-dashed border-slate-300 hover:border-emerald-500 bg-slate-50/60 hover:bg-emerald-50/20 rounded-2xl p-6 sm:p-8 text-center transition-all cursor-pointer group"
             id="dropZoneV2"
             onclick="document.getElementById('fileInputV2').click()">
            <input type="file" 
                   name="file" 
                   id="fileInputV2" 
                   class="hidden" 
                   accept=".xlsx, .xls" 
                   required>
            
            <div id="dropZoneContentV2" class="flex flex-col items-center justify-center">
                <div class="w-13 h-13 rounded-2xl bg-white group-hover:bg-emerald-100 text-slate-400 group-hover:text-emerald-600 border border-slate-200 group-hover:border-emerald-200 flex items-center justify-center text-2xl transition-all shadow-2xs mb-3 group-hover:scale-105">
                    <i class="ti ti-file-spreadsheet"></i>
                </div>
                <p class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-emerald-800 transition-colors">
                    Klik untuk pilih berkas <span class="text-slate-500 font-normal">atau seret berkas ke sini</span>
                </p>
                <p class="text-[11px] text-slate-400 mt-1">Mendukung format Microsoft Excel (.xlsx, .xls) hingga batas 10MB</p>
            </div>

            <!-- Selected File Preview -->
            <div id="fileSelectedPreviewV2" class="hidden flex items-center justify-between p-3.5 bg-white rounded-xl border border-emerald-200 shadow-2xs">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shrink-0">
                        <i class="ti ti-file-check"></i>
                    </div>
                    <div class="min-w-0 text-left">
                        <p class="text-xs font-bold text-slate-800 truncate" id="fileNamePreviewV2">-</p>
                        <p class="text-[11px] text-slate-400" id="fileSizePreviewV2">-</p>
                    </div>
                </div>
                <button type="button" 
                        id="btnRemoveFileV2" 
                        class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition"
                        title="Hapus file"
                        onclick="event.stopPropagation(); removeSelectedFileV2();">
                    <i class="ti ti-trash text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Mapping & Guidelines Accordion / Card Table -->
    <div class="border border-slate-200/90 rounded-2xl overflow-hidden shadow-2xs bg-white">
        <!-- Section Header -->
        <div class="px-4 py-3 bg-slate-50/90 border-b border-slate-200/80 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="ti ti-info-circle text-emerald-600 text-base"></i>
                <h5 class="text-xs font-bold text-slate-800 tracking-tight">Panduan Kolom & Ketentuan Pengisian</h5>
            </div>
            <span class="text-[11px] text-slate-500 font-medium">17 Kolom Data</span>
        </div>

        <!-- Scrollable Table -->
        <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 text-xs">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-100/70 text-slate-600 font-bold uppercase tracking-wider text-[10.5px] sticky top-0 backdrop-blur-sm z-10">
                    <tr>
                        <th class="py-2.5 px-3.5 w-44">Nama Kolom</th>
                        <th class="py-2.5 px-3.5 w-24 text-center">Status</th>
                        <th class="py-2.5 px-3.5">Ketentuan & Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 bg-white">
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">nik</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Nomor Induk Karyawan, harus unik & belum pernah terdaftar.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">no_ktp</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Nomor KTP 16 digit.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">npwp</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">Opsional</span></td>
                        <td class="py-2 px-3.5 text-slate-500">Nomor Pokok Wajib Pajak (dapat dikosongkan).</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">nama_karyawan</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Nama lengkap pegawai sesuai KTP.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">tempat_lahir</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Kota / kabupaten tempat kelahiran.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">tanggal_lahir</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Format tanggal <code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded text-[11px] font-mono">YYYY-MM-DD</code> (contoh: 1995-08-17).</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">alamat</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Alamat domisili / tempat tinggal saat ini.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">alamat_sesuai_ktp</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">Opsional</span></td>
                        <td class="py-2 px-3.5 text-slate-500">Alamat lengkap sesuai KTP (jika berbeda dari domisili).</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">no_hp</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Nomor telepon / WhatsApp aktif.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">jenis_kelamin</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Gunakan inisial <strong class="font-bold text-slate-800">L</strong> (Laki-laki) atau <strong class="font-bold text-slate-800">P</strong> (Perempuan).</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">kode_status_kawin</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">
                            Pilihan kode status perkawinan:
                            <div class="flex flex-wrap gap-1.5 mt-1">
                                @foreach ($status_kawin as $sk)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-[10.5px]">
                                        <strong class="font-bold">{{ $sk->kode_status_kawin }}</strong>: {{ $sk->status_kawin }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">pendidikan_terakhir</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Contoh: SMA, SMK, D3, S1, S2.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">jurusan</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">Opsional</span></td>
                        <td class="py-2 px-3.5 text-slate-500">Jurusan atau program studi pendidikan terakhir.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">kode_cabang</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Harus sesuai dengan kode master cabang di sistem.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">kode_dept</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Harus sesuai dengan kode master departemen di sistem.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">kode_jabatan</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Harus sesuai dengan kode master jabatan di sistem.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">tanggal_masuk</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Format tanggal masuk kerja <code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded text-[11px] font-mono">YYYY-MM-DD</code>.</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">status_karyawan</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-150">Wajib</span></td>
                        <td class="py-2 px-3.5 text-slate-600">Gunakan kode status karyawan (contoh: <strong class="font-bold text-slate-800">T</strong> untuk Tetap, <strong class="font-bold text-slate-800">K</strong> untuk Kontrak).</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-700">status_aktif_karyawan</td>
                        <td class="py-2 px-3.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">Opsional</span></td>
                        <td class="py-2 px-3.5 text-slate-500">Isi <strong class="font-bold text-slate-800">1</strong> = Aktif, <strong class="font-bold text-slate-800">0</strong> = Non-Aktif (default: 1).</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Action Buttons Footer -->
    <div class="pt-2 flex flex-col sm:flex-row items-center justify-end gap-3 border-t border-slate-100">
        <button type="button" 
                class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold transition active:scale-95" 
                data-bs-dismiss="modal">
            Batal
        </button>
        <button type="submit" 
                id="btnSubmitImportV2" 
                class="w-full sm:w-auto px-6 py-2.5 text-xs font-bold text-white rounded-xl shadow-md flex items-center justify-center gap-2 transition active:scale-95 cursor-pointer"
                style="background: linear-gradient(135deg, var(--theme-color-1, #0a5c41), var(--theme-color-2, #117956));">
            <i class="ti ti-file-import text-sm"></i>
            <span>Mulai Import Data</span>
        </button>
    </div>
</form>

<script>
    // File Selection Handling & Drag-Drop UI
    const fileInput = document.getElementById('fileInputV2');
    const dropZoneContent = document.getElementById('dropZoneContentV2');
    const fileSelectedPreview = document.getElementById('fileSelectedPreviewV2');
    const fileNamePreview = document.getElementById('fileNamePreviewV2');
    const fileSizePreview = document.getElementById('fileSizePreviewV2');

    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            displayFile(this.files[0]);
        }
    });

    const dropZone = document.getElementById('dropZoneV2');
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.add('border-emerald-500', 'bg-emerald-50/40');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-emerald-500', 'bg-emerald-50/40');
        }, false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length) {
            fileInput.files = files;
            displayFile(files[0]);
        }
    });

    function displayFile(file) {
        fileNamePreview.textContent = file.name;
        const sizeInMb = (file.size / (1024 * 1024)).toFixed(2);
        fileSizePreview.textContent = `${sizeInMb} MB • Berkas siap diunggah`;
        dropZoneContent.classList.add('hidden');
        fileSelectedPreview.classList.remove('hidden');
    }

    function removeSelectedFileV2() {
        fileInput.value = '';
        dropZoneContent.classList.remove('hidden');
        fileSelectedPreview.classList.add('hidden');
    }

    // AJAX Form Submission with SweetAlert2
    $(document).off('submit', '#frmImportV2').on('submit', '#frmImportV2', function(e) {
        e.preventDefault();

        const form = this;
        const file = fileInput.files[0];

        if (!file) {
            Swal.fire({
                icon: 'warning',
                title: 'Berkas Belum Dipilih',
                text: 'Silakan pilih berkas Excel (.xlsx / .xls) terlebih dahulu.',
                confirmButtonColor: 'var(--theme-color-1, #0a5c41)'
            });
            return;
        }

        const formData = new FormData(form);
        const submitBtn = document.getElementById('btnSubmitImportV2');
        submitBtn.disabled = true;

        // Show Modern Progress Swal
        Swal.fire({
            title: 'Memproses Import Data...',
            html: `
                <div class="py-4 flex flex-col items-center justify-center">
                    <div class="w-12 h-12 border-4 border-emerald-500/20 border-t-emerald-600 rounded-full animate-spin mb-3"></div>
                    <p class="text-xs text-slate-600 font-medium">Sistem sedang memvalidasi dan mengimpor baris data ke database.</p>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false
        });

        // Submit AJAX request
        $.ajax({
            url: form.action,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Berhasil!',
                        text: response.message || 'Seluruh data karyawan berhasil diimpor.',
                        showConfirmButton: false,
                        timer: 1800
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    let errorMessage = response.message || 'Terjadi kesalahan validasi data.';
                    if (response.errors) {
                        errorMessage += '<br><br><ul class="text-left text-xs bg-rose-50 p-3 rounded-lg border border-rose-200 text-rose-700 space-y-1">';
                        Object.keys(response.errors).forEach(key => {
                            errorMessage += `<li>• ${response.errors[key]}</li>`;
                        });
                        errorMessage += '</ul>';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Import Gagal',
                        html: errorMessage,
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#ef4444'
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat memproses berkas Excel.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memproses',
                    text: errorMessage,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#ef4444'
                });
            },
            complete: function() {
                submitBtn.disabled = false;
            }
        });
    });
</script>
