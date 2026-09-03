@extends('layouts.mobile.app')
@section('content')
    <style>
        :root {
            --bg-body: #dff9fb;
            --bg-nav: #ffffff;
            --color-nav: #32745e;
            --color-nav-active: #58907D;
            --bg-indicator: #32745e;
            --color-nav-hover: #3ab58c;
        }

        body {
            background: var(--bg-body);
            background: linear-gradient(135deg, #dff9fb 0%, #c7ecee 100%);
            min-height: 100vh;
        }

        .preview-container {
            padding: 20px;
            padding-bottom: 100px;
        }

        .header-info {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .header-info h2 {
            color: #32745e;
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 600;
        }

        .header-info p {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }

        .info-badge {
            display: inline-block;
            background: #32745e;
            color: white;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            margin-top: 10px;
        }

        .wajah-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .wajah-item {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            aspect-ratio: 3/4;
        }

        .wajah-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .wajah-item .wajah-label {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            color: white;
            padding: 10px;
            font-size: 11px;
            text-align: center;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .btn-action {
            padding: 16px 24px;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-hapus {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }

        .btn-hapus:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        .btn-rekam {
            background: linear-gradient(135deg, #32745e 0%, #58907D 100%);
            color: white;
        }

        .btn-rekam:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(50, 116, 94, 0.4);
            background: linear-gradient(135deg, #3ab58c 0%, #58907D 100%);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            margin-bottom: 20px;
        }

        .empty-state ion-icon {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: #999;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .wajah-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .header-info h2 {
                font-size: 20px;
            }
        }
    </style>

    <div class="preview-container">
        <div class="header-info">
            <h2>{{ $karyawan->nama_karyawan ?? 'Karyawan' }}</h2>
            <p><strong>NIK:</strong> {{ $nik }}</p>
            <span class="info-badge">
                <ion-icon name="images-outline"></ion-icon>
                {{ $wajahList->count() }} Foto Wajah Tersimpan
            </span>
        </div>

        @if($wajahList->count() > 0)
            <div class="wajah-grid">
                @foreach($wajahList as $index => $wajah)
                    <div class="wajah-item">
                        @if($wajah->file_exists && $wajah->image_url)
                            <img src="{{ $wajah->image_url }}" 
                                 alt="Wajah {{ $index + 1 }}" 
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'200\'/%3E%3Ctext fill=\'%23999\' font-family=\'sans-serif\' font-size=\'14\' x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\'%3EGambar tidak ditemukan%3C/text%3E%3C/svg%3E';">
                        @else
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f0f0f0; color: #999;">
                                <div style="text-align: center; padding: 20px;">
                                    <ion-icon name="image-outline" style="font-size: 48px; margin-bottom: 10px;"></ion-icon>
                                    <div style="font-size: 12px;">File tidak ditemukan</div>
                                </div>
                            </div>
                        @endif
                        <div class="wajah-label">
                            Foto {{ $index + 1 }}<br>
                            <small>{{ \Carbon\Carbon::parse($wajah->created_at)->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="action-buttons">
                <form id="formRekamUlang" action="{{ route('facerecognition.karyawan.destroyAll') }}" method="POST" style="margin: 0;">
                    @csrf
                    @method('POST')
                    <button type="button" class="btn-action btn-rekam" onclick="confirmRekamUlang()" style="width: 100%;">
                        <ion-icon name="camera-outline"></ion-icon>
                        Mulai Perekaman Ulang
                    </button>
                </form>
            </div>
        @else
            <div class="empty-state">
                <ion-icon name="images-outline"></ion-icon>
                <p>Belum ada data wajah yang tersimpan</p>
                <a href="{{ route('facerecognition.karyawan.create') }}" class="btn-action btn-rekam" style="text-decoration: none; display: inline-block; margin-top: 20px;">
                    <ion-icon name="camera-outline"></ion-icon>
                    Mulai Perekaman
                </a>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmRekamUlang() {
            Swal.fire({
                title: 'Mulai Perekaman Ulang?',
                text: 'Data wajah yang sudah tersimpan akan dihapus secara permanen, kemudian Anda akan diarahkan ke halaman perekaman baru.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#32745e',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Mulai Perekaman',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menghapus data wajah lama',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    document.getElementById('formRekamUlang').submit();
                }
            });
        }

        // Tampilkan pesan sukses/error jika ada
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: true
            });
        @endif
    </script>
@endsection

