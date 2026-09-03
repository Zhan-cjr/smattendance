@extends('layouts.mobile.app')
@section('content')
    <style>
        /* CSS Select2 dll (tidak berubah) */
        .presensi-content-modern {
            background: linear-gradient(135deg, #e0f7fa 0%, #fff 100%);
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(44, 62, 80, 0.08);
            padding: 18px 15px 24px 15px;
            margin: 10px 10px;
            display: flex;
            flex-direction: column;
        }

        .presensi-content-modern,
        .presensi-content-modern * {
            font-family: 'Poppins', sans-serif !important;
        }

        .form-group label {
            font-weight: 600;
            color: #222;
            margin-bottom: 5px; /* Tambahkan sedikit jarak */
            display: block; /* Pastikan label mengambil baris penuh */
        }

        .form-control, .custom-file-input, .custom-file-label {
            /* Tinggi Input diselaraskan */
            height: 44px; 
            border-radius: 10px;
            border: 1px solid #ced4da;
            box-shadow: none;
            transition: all 0.3s ease;
            font-size: 16px;
            padding: 10px 15px;
            width: 100%;
            line-height: 1.5;
        }
        
        /* Mengoverride padding pada input type=time/date */
        input[type="time"].form-control,
        input[type="date"].form-control {
            padding: 0 15px; /* Biarkan browser mobile yang menangani padding internalnya */
            appearance: none; /* Hapus gaya bawaan */
            -webkit-appearance: none;
        }

        .custom-file-label::after {
            content: "Pilih File";
            height: 42px; /* Sesuaikan tinggi */
            line-height: 2;
            padding: 0 15px;
        }
        
        #preview-container {
            margin-top: 15px;
            text-align: center;
        }
        
        #photo-preview {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .action-button {
            height: 50px !important;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 600;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.1s;
            background-color: #32745e !important; /* Warna primer dari dashboard */
            color: #fff;
            border: none;
        }

        .action-button ion-icon {
            margin-right: 8px;
            font-size: 20px;
        }

        .action-button:active {
            transform: scale(0.98);
        }

        .select-wrapper {
            position: relative;
        }

        /* Ikon panah drop-down Select */
        .select-wrapper::after {
            content: "";
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 12px;
            height: 12px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="%23343a40" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
            background-repeat: no-repeat;
            background-position: center;
            pointer-events: none;
        }
        
        /* Styling tambahan untuk Select2 agar tampil seragam */
        .select2-container .select2-selection--single {
            height: 44px !important;
            border-radius: 10px !important;
            border: 1px solid #ced4da !important;
            padding-top: 5px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 35px;
            color: #495057;
        }
        
        /* Header style dari histori.blade.php */
        .appHeader {
            background-color: #32745e !important;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            height: 56px;
        }

        .headerButton ion-icon {
            font-size: 24px;
        }

    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Hapus import datepicker.min.css karena kita pakai type="date" native --}}
    
    {{-- Import CSS Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="javascript:;" class="headerButton goBack">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Presensi Khusus</div>
            <div class="right"></div>
        </div>
    </div>
    <div id="content-section" style="margin-top: 56px; padding: 10px;">
        <div class="presensi-content-modern">
            <form id="form-presensispc">
                @csrf
                <input type="hidden" id="karyawan_nama_watermark" value="{{ $karyawan->nama_karyawan }}">

                <div class="form-group mb-3">
                    <label for="absen_type">Tipe Absensi</label>
                    <div class="select-wrapper">
                        <select name="absen_type" id="absen_type" class="form-control">
                            <option value="1">Absen Masuk</option>
                            <option value="2">Absen Pulang</option>
                        </select>
                    </div>
                </div>

                {{-- Select Box NIK dengan Pencarian (Select2) --}}
                <div class="form-group mb-3">
                    <label for="nik_input">Pilih Karyawan / NIK</label>
                    <div>
                        <select name="nik_input" id="nik_input" class="form-control" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach ($allKaryawan as $k)
                                <option 
                                    value="{{ $k->nik }}" 
                                    {{ $k->nik == ($karyawan->nik ?? '') ? 'selected' : '' }}>
                                    {{ $k->nik }} - {{ $k->nama_karyawan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="tanggal">Tanggal Absensi</label>
                    {{-- Diubah menjadi type="date" agar menggunakan native mobile date picker --}}
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" autocomplete="off">
                </div>

                <div class="form-group mb-3">
                    <label for="jam">Jam Absensi</label>
                    {{-- Tetap type="time" dan step="1" untuk presisi detik, namun tanpa auto-update --}}
                    <input type="time" id="jam" name="jam" class="form-control" value="{{ date('H:i:s') }}" step="1"> 
                </div>

                <div class="form-group mb-3">
                    <label for="cabang">Cabang (Lokasi Presensi)</label>
                    <div class="select-wrapper">
                        <select name="cabang" id="cabang" class="form-control">
                            @foreach ($cabang as $item)
                                <option value="{{ $item->lokasi_cabang }}">{{ $item->nama_cabang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="foto">Unggah Foto (Galeri/Kamera)</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="foto" name="foto" accept="image/*">
                        <label class="custom-file-label" for="foto">Pilih foto dari Galeri atau ambil dengan Kamera</label>
                    </div>
                </div>

                <div id="preview-container">
                    <img id="photo-preview" style="display: none;">
                    <canvas id="photo-canvas" style="display: none;"></canvas>
                </div>

                <button type="submit" id="submit-button" class="action-button">
                    <ion-icon name="send-outline"></ion-icon>
                    Kirim Absensi Khusus
                </button>
            </form>
        </div>
    </div>
@endsection

@push('myscript')
    {{-- Hapus script datepicker.min.js dan i18n/datepicker.id.js --}}
    
    {{-- Import JS Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Global variable untuk menyimpan nama karyawan
        let karyawanNama = $('#karyawan_nama_watermark').val(); 

        // Fungsi updateJamDetik dihapus karena ini adalah presensi khusus/manual
        // Waktu di input #jam sudah diisi dengan format H:i:s saat load, dan biarkan user yang mengeditnya.

        $(function() {
            // Mengisi input jam dengan waktu saat ini (H:i:s) saat load
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            $('#jam').val(`${hours}:${minutes}:${seconds}`);

            // Inisialisasi Select2 untuk NIK input
            $('#nik_input').select2({
                placeholder: "-- Cari NIK atau Nama Karyawan --",
                allowClear: true,
                dropdownAutoWidth: true,
                width: '100%',
                theme: "default"
            });

            // AJAX: Ambil nama karyawan saat NIK dipilih
            $('#nik_input').on('change', function() {
                const selectedNik = $(this).val();
                if (selectedNik) {
                    $.ajax({
                        url: "{{ route('presensispc.getKaryawanName') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            nik: selectedNik
                        },
                        success: function(response) {
                            if (response.status) {
                                karyawanNama = response.nama;
                            } else {
                                karyawanNama = selectedNik; // Jika gagal, gunakan NIK
                            }
                            $('#karyawan_nama_watermark').val(karyawanNama); // Update hidden field
                        },
                        error: function() {
                            karyawanNama = selectedNik;
                            $('#karyawan_nama_watermark').val(karyawanNama);
                        }
                    });
                } else {
                    karyawanNama = '';
                    $('#karyawan_nama_watermark').val(karyawanNama);
                }
            });
            
            // Tampilkan nama file yang dipilih
            $('#foto').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').html(fileName || 'Pilih foto dari Galeri atau ambil dengan Kamera');
                
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        $('#photo-preview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#photo-preview').hide().attr('src', '');
                }
            });

            // Proses form submission
            $('#form-presensispc').submit(function(e) {
                e.preventDefault();

                const fotoInput = document.getElementById('foto');
                if (!fotoInput.files[0]) {
                    Swal.fire('Peringatan', 'Anda harus mengunggah foto terlebih dahulu.', 'warning');
                    return;
                }

                if (!$('#nik_input').val()) {
                     Swal.fire('Peringatan', 'NIK Karyawan wajib dipilih.', 'warning');
                    return;
                }
                
                // Validasi format jam HH:MM:SS
                if (!/^\d{2}:\d{2}:\d{2}$/.test($('#jam').val())) {
                    Swal.fire('Peringatan', 'Format Jam harus HH:MM:SS (misal: 08:00:00). Pastikan Anda mengisi detik.', 'warning');
                    return;
                }


                Swal.fire({
                    title: 'Memproses Absensi...',
                    text: 'Mohon tunggu, foto sedang diolah dan data dikirim.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const file = fotoInput.files[0];
                processImageForWatermark(file)
                    .then(finalImageUri => {
                        sendAttendance(finalImageUri);
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Gagal memproses foto: ' + error, 'error');
                    });
            });

            // Fungsi untuk Watermark dan Konversi Gambar (RESIZE + Watermark Tanpa Ikon Kalender)
            function processImageForWatermark(file) {
                const TARGET_WIDTH = 480;
                const TARGET_HEIGHT = 640;

                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            // 1. Buat kanvas untuk RESIZE
                            const resizeCanvas = document.createElement('canvas');
                            const resizeCtx = resizeCanvas.getContext('2d');
                            
                            resizeCanvas.width = TARGET_WIDTH;
                            resizeCanvas.height = TARGET_HEIGHT;
                            
                            // Lakukan resize (drawImage akan menyesuaikan gambar asli ke ukuran kanvas baru)
                            resizeCtx.drawImage(img, 0, 0, TARGET_WIDTH, TARGET_HEIGHT);

                            // 2. Gunakan kanvas yang sudah di-resize untuk WATERMARK
                            const canvas = resizeCanvas;
                            const ctx = resizeCtx;

                            // Kita sekarang bekerja dengan dimensi baru:
                            const sourceWidth = TARGET_WIDTH;
                            const sourceHeight = TARGET_HEIGHT;
                            
                            // --- Watermark Logic ---
                            const isMasuk = $('#absen_type').val() === '1';
                            const titleText = isMasuk ? 'Absen Masuk' : 'Absen Pulang';
                            const watermarkColor = isMasuk ? 'rgba(40, 167, 69, 0.8)' : 'rgba(220, 53, 69, 0.8)';
                            const textColor = '#FFFFFF';
                            
                            // Skala font/padding berdasarkan HEIGHT BARU (640)
                            const titleFontSize = sourceHeight * 0.035; 
                            const textRectPadding = sourceWidth * 0.02;
                            const textRectRadius = 8;
                            const textRectX = sourceWidth * 0.02;
                            const textRectY = sourceWidth * 0.02;

                            // Watermark Background (Absen Type)
                            ctx.fillStyle = watermarkColor;
                            ctx.font = `bold ${titleFontSize}px Poppins, Arial`;
                            const titleTextMetrics = ctx.measureText(titleText);
                            const titleTextWidth = titleTextMetrics.width + textRectPadding * 2;
                            const titleTextHeight = titleFontSize + textRectPadding;

                            ctx.beginPath();
                            ctx.moveTo(textRectX + textRectRadius, textRectY);
                            ctx.lineTo(textRectX + titleTextWidth - textRectRadius, textRectY);
                            ctx.quadraticCurveTo(textRectX + titleTextWidth, textRectY, textRectX + titleTextWidth, textRectY + textRectRadius);
                            ctx.lineTo(textRectX + titleTextWidth, textRectY + titleTextHeight - textRectRadius);
                            ctx.quadraticCurveTo(textRectX + titleTextWidth, textRectY + titleTextHeight, textRectX + titleTextWidth - textRectRadius, textRectY + titleTextHeight);
                            ctx.lineTo(textRectX + textRectRadius, textRectY + titleTextHeight);
                            ctx.quadraticCurveTo(textRectX, textRectY + titleTextHeight, textRectX, textRectY + titleTextHeight - textRectRadius);
                            ctx.lineTo(textRectX, textRectY + textRectRadius);
                            ctx.quadraticCurveTo(textRectX, textRectY, textRectX + textRectRadius, textRectY);
                            ctx.closePath();
                            ctx.fill();
                            
                            // Watermark Text (Absen Type)
                            ctx.fillStyle = textColor;
                            ctx.fillText(titleText, textRectX + textRectPadding, textRectY + titleFontSize);

                            const iconSize = sourceHeight * 0.03;
                            const iconPadding = sourceWidth * 0.02;
                            const margin = sourceWidth * 0.04;
                            
                            // Posisi footer di bawah
                            const footerY = sourceHeight - (sourceHeight * 0.02); 

                            const jamY = sourceHeight * 0.77; 
                            const tanggalY = jamY + (sourceHeight * 0.04); 
                            const lokasiY = tanggalY + (sourceHeight * 0.04); 
                            const namaKaryawanY = lokasiY + (sourceHeight * 0.04); 

                            // Helper function to draw icon from SVG
                            const drawIcon = (svgData, x, y, size) => {
                                return new Promise((resolve) => {
                                    const imgIcon = new Image();
                                    imgIcon.onload = () => {
                                        ctx.drawImage(imgIcon, x, y, size, size);
                                        resolve();
                                    };
                                    imgIcon.src = 'data:image/svg+xml;utf8,' + encodeURIComponent(svgData);
                                });
                            };

                            const jam = $('#jam').val(); // HH:MM:SS
                            const dateValue = $('#tanggal').val(); // YYYY-MM-DD
                            const dateObj = new Date(dateValue.replace(/-/g, '/')); // Pastikan format kompatibel
                            const tanggalFull = dateObj.toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'});

                            const selectedOption = $("#cabang option:selected");
                            const branchName = selectedOption.text().trim(); 
                            const namaKaryawan = $('#karyawan_nama_watermark').val(); 

                            // Jam (HH:MM:SS) - Ikon Jam
                            drawIcon('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>', margin, jamY, iconSize).then(() => {
                                ctx.font = `bold ${sourceHeight * 0.05}px Poppins, Arial`;
                                ctx.fillStyle = textColor;
                                ctx.textAlign = 'left';
                                ctx.fillText(jam, margin + iconSize + iconPadding, jamY + iconSize);

                                // Tanggal (Tanpa Ikon)
                                ctx.font = `bold ${sourceHeight * 0.03}px Poppins, Arial`;
                                ctx.fillText(tanggalFull, margin + iconSize + iconPadding, tanggalY + iconSize); 

                                // Lokasi
                                return drawIcon('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>', margin, lokasiY, iconSize);
                            }).then(() => {
                                ctx.font = `bold ${sourceHeight * 0.03}px Poppins, Arial`;
                                ctx.fillText(branchName, margin + iconSize + iconPadding, lokasiY + iconSize);
                                
                                // Nama Karyawan
                                return drawIcon('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>', margin, namaKaryawanY, iconSize);
                            }).then(() => {
                                ctx.font = `bold ${sourceHeight * 0.03}px Poppins, Arial`;
                                ctx.fillText(namaKaryawan, margin + iconSize + iconPadding, namaKaryawanY + iconSize); 

                                // Footer Waktu dan Lokasi diverifikasi oleh SM Attendance
                                const footerText = 'Waktu dan Lokasi diverifikasi oleh SM Attendance';
                                ctx.font = `italic ${sourceHeight * 0.02}px Poppins, Arial`;
                                ctx.fillStyle = textColor;
                                ctx.textAlign = 'right';
                                ctx.fillText(footerText, sourceWidth - margin, footerY);

                                // Convert canvas to data URL (jpeg quality 0.9)
                                resolve(canvas.toDataURL('image/jpeg', 0.9));
                            }).catch(reject);
                        };
                        img.onerror = reject;
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Kirim data absensi ke server
            function sendAttendance(finalImageUri) {
                const absen_type = $('#absen_type').val(); 
                const nik_input = $('#nik_input').val(); 
                const tanggal = $('#tanggal').val();
                const jam = $('#jam').val(); // HH:MM:SS
                
                const lokasi_cabang_value = $('#cabang').val(); 
                const lokasi_presensi = lokasi_cabang_value + ',99999'; // Koordinat + Akurasi dummy

                Swal.fire({
                    title: 'Mengirim Data...',
                    text: 'Sedang mengirim absensi dan foto ke server.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    type: 'POST',
                    url: "{{ route('presensispc.store') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        nik_input: nik_input, 
                        image: finalImageUri,
                        status: absen_type,
                        tanggal: tanggal,
                        jam: jam,
                        lokasi: lokasi_presensi, 
                        lokasi_cabang: lokasi_cabang_value, 
                        kode_jam_kerja: 'JK01', 
                    },
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 4000
                        }).then(() => {
                            window.location.href = '/dashboard';
                        });
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON.message || 'Terjadi kesalahan saat menyimpan data absensi.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: message,
                        }).then(() => {
                             // Jika error, tidak perlu redirect ke dashboard, biarkan user mencoba lagi
                        });
                    }
                });
            }
        });
    </script>
@endpush