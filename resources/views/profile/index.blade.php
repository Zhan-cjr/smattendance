@extends('layouts.mobile.modern')

@section('title', 'Profile')

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@push('mystyle')
    <style>
        body {
            background: #e6fcf5 !important;
        }

        .form-container {
            padding: 10px 5px;
        }

        .form-label-group {
            position: relative;
            margin-bottom: 12px;
            background: transparent !important;
            border: 1px solid #32745e;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .form-label-group .input-icon {
            position: absolute;
            left: 14px;
            top: 11px;
            font-size: 20px;
            color: #32745e;
            z-index: 10;
            pointer-events: none;
        }

        .form-label-group input,
        .form-label-group textarea {
            width: 100% !important;
            height: 44px;
            padding: 18px 14px 2px 42px !important;
            font-size: 14px;
            font-weight: 500;
            color: #2a6350;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            display: block !important;
        }

        .form-label-group textarea {
            height: 80px !important;
            padding-top: 22px !important;
            resize: none;
        }

        .form-label-group label {
            position: absolute;
            top: 11px;
            left: 42px;
            font-size: 14px;
            color: #32745e;
            opacity: 0.8;
            pointer-events: none;
            transition: all 0.2s ease-in-out;
            margin-bottom: 0;
            z-index: 5;
        }

        .form-label-group input:focus ~ label,
        .form-label-group input:not(:placeholder-shown) ~ label,
        .form-label-group textarea:focus ~ label,
        .form-label-group textarea:not(:placeholder-shown) ~ label {
            top: 2px;
            left: 42px;
            font-size: 10px;
            font-weight: 600;
            color: #32745e;
        }

        /* Foto Profil */
        .profile-photo-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .profile-photo-box {
            position: relative;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            padding: 3px;
            background: linear-gradient(135deg, #32745e, #53c69c);
            box-shadow: 0 8px 20px rgba(50, 116, 94, 0.2);
        }

        .profile-photo-box img,
        .profile-photo-box .photo-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e6fcf5;
        }

        .profile-photo-box .photo-placeholder {
            background-size: cover;
            background-position: center;
        }

        /* Dashed Box File Upload */
        .custom-file-upload {
            border: 2px dashed #32745e;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            background: rgba(50, 116, 94, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 90px;
        }

        .custom-file-upload:active {
            background: rgba(50, 116, 94, 0.1);
            transform: scale(0.98);
        }

        .custom-file-upload input[type="file"] {
            display: none;
        }

        .custom-file-upload ion-icon {
            font-size: 32px;
            color: #32745e;
            margin-bottom: 5px;
        }

        .custom-file-upload span {
            font-size: 14px;
            font-weight: 600;
            color: #32745e;
        }

        .file-name {
            font-size: 11px;
            color: #2a6350;
            margin-top: 4px;
            font-weight: 500;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-submit-modern {
            width: 100%;
            height: 48px;
            background: #32745e;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            transition: all 0.3s;
        }

        .btn-submit-modern:active {
            transform: scale(0.97);
            background: #2a6350;
        }
    </style>
@endpush

@section('content')
    <div class="fade-up form-container pb-24">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="formProfile" autocomplete="off">
            @csrf
            @method('PUT')

            {{-- Profile Photo --}}
            <div class="profile-photo-wrapper">
                <div class="profile-photo-box">
                    @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                        <div class="photo-placeholder" style="background-image: url({{ getfotoKaryawan($karyawan->foto) }});"></div>
                    @else
                        <img src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="Profile Photo">
                    @endif
                </div>
            </div>

            {{-- Nama Lengkap --}}
            <div class="form-label-group">
                <ion-icon name="person-outline" class="input-icon"></ion-icon>
                <input type="text" name="nama_karyawan" id="nama_karyawan" placeholder=" " value="{{ $karyawan->nama_karyawan ?? '' }}" required>
                <label for="nama_karyawan">Nama Lengkap</label>
            </div>

            {{-- No. KTP --}}
            <div class="form-label-group">
                <ion-icon name="card-outline" class="input-icon"></ion-icon>
                <input type="text" name="no_ktp" id="no_ktp" placeholder=" " value="{{ $karyawan->no_ktp ?? '' }}" required>
                <label for="no_ktp">No. KTP</label>
            </div>

            {{-- No. HP --}}
            <div class="form-label-group">
                <ion-icon name="call-outline" class="input-icon"></ion-icon>
                <input type="text" name="no_hp" id="no_hp" placeholder=" " value="{{ $karyawan->no_hp ?? '' }}" required>
                <label for="no_hp">No. HP</label>
            </div>

            {{-- Alamat --}}
            <div class="form-label-group">
                <ion-icon name="location-outline" class="input-icon"></ion-icon>
                <textarea name="alamat" id="alamat" placeholder=" " required>{{ $karyawan->alamat ?? '' }}</textarea>
                <label for="alamat">Alamat</label>
            </div>

            {{-- Username --}}
            <div class="form-label-group">
                <ion-icon name="at-outline" class="input-icon"></ion-icon>
                <input type="text" name="username" id="username" placeholder=" " value="{{ $user->username }}" required>
                <label for="username">Username</label>
            </div>

            {{-- Email --}}
            <div class="form-label-group">
                <ion-icon name="mail-outline" class="input-icon"></ion-icon>
                <input type="email" name="email" id="email" placeholder=" " value="{{ $user->email }}" required>
                <label for="email">Email</label>
            </div>

            {{-- Upload Foto --}}
            <div class="custom-file-upload" onclick="document.getElementById('foto').click()">
                <input type="file" name="foto" id="foto" accept=".jpg, .jpeg, .png">
                <ion-icon name="camera-outline"></ion-icon>
                <span>Ganti Foto Profil</span>
                <div id="fileName" class="file-name"></div>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="save-outline"></ion-icon>
                <span>Update Profile</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script>
        // File Upload Handling
        document.getElementById('foto').addEventListener('change', function() {
            let file = this.files[0];
            const fileNameDisplay = document.getElementById('fileName');
            if (file) {
                fileNameDisplay.textContent = file.name;
            } else {
                fileNameDisplay.textContent = '';
            }
        });

        $(function() {
            $("#formProfile").submit(function(e) {
                let nama_karyawan = $('input[name="nama_karyawan"]').val();
                let no_ktp = $('input[name="no_ktp"]').val();
                let no_hp = $('input[name="no_hp"]').val();
                let alamat = $('textarea[name="alamat"]').val();
                let username = $('input[name="username"]').val();
               let email = $('input[name="email"]').val();

                if (nama_karyawan == "" || no_ktp == "" || no_hp == "" || alamat == "" || username == "" || email == "") {
                    e.preventDefault();
                    Swal.fire({title: "Oops!", text: 'Semua Bidang Harus Diisi !', icon: "warning"});
                    return false;
                }

                const btn = document.getElementById('btnSimpan');
                btn.disabled = true;
                btn.innerHTML = `<ion-icon name="sync-outline" class="animate-spin"></ion-icon><span>Menyimpan...</span>`;
            });
        });
    </script>
@endpush
