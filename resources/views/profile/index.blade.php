@extends('layouts.mobile.modern')

@section('title')
    <div class="text-center leading-tight">
        <div class="font-extrabold text-[15px] tracking-tight">Profil Pengguna</div>
        <div class="text-[9.5px] font-medium opacity-75">SM-Attendance</div>
    </div>
@endsection

@section('header_left')
    <a href="{{ route('dashboard.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white active:scale-95 transition-all">
        <ion-icon name="chevron-back-outline" class="text-lg"></ion-icon>
    </a>
@endsection

@section('header_right')
    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/10 text-white text-xs font-bold">
        <ion-icon name="person-outline" class="text-lg"></ion-icon>
    </div>
@endsection

@push('mystyle')
    <script>
        (function() {
            var savedTheme = localStorage.getItem('smatt_theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark', 'dark-mode-active');
                if (document.body) {
                    document.body.classList.remove('dark', 'dark-mode-active');
                }
            }
        })();
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        /* Bento Cards */
        .profile-hero-card {
            background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #115e59 50%, #042f2e 100%);
            border-radius: 24px;
            box-shadow: 0 8px 24px -4px rgba(15, 118, 110, 0.35);
        }

        .form-card {
            background-color: #ffffff;
            border-radius: 22px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 4px 18px -2px rgba(15, 23, 42, 0.05);
        }

        .input-group-modern {
            position: relative;
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            transition: all 0.2s ease;
        }

        .input-group-modern:focus-within {
            border-color: {{ $t['primary'] ?? '#0f766e' }};
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.15);
        }

        .input-group-modern input,
        .input-group-modern textarea {
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
            color: #0f172a;
            font-weight: 700;
            font-size: 13.5px;
            padding: 22px 14px 8px 42px;
        }

        .input-group-modern textarea {
            min-height: 80px;
            resize: none;
        }

        .input-group-modern label {
            position: absolute;
            left: 42px;
            top: 7px;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            pointer-events: none;
        }

        .input-group-modern .field-icon {
            position: absolute;
            left: 14px;
            top: 15px;
            font-size: 16px;
            color: #64748b;
            pointer-events: none;
        }

        .upload-drop-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 18px;
            padding: 16px;
            text-align: center;
            background-color: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .upload-drop-zone:hover, .upload-drop-zone:active {
            border-color: {{ $t['primary'] ?? '#0f766e' }};
            background-color: rgba(15, 118, 110, 0.04);
        }

        /* Dark Mode Overrides */
        html.dark body, body.dark {
            background-color: #070b14 !important;
            color: #f8fafc !important;
        }

        html.dark .form-card, body.dark .form-card {
            background: linear-gradient(180deg, #131d31 0%, #0f172a 100%) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.4) !important;
        }

        html.dark .input-group-modern, body.dark .input-group-modern {
            background-color: #182339 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }

        html.dark .input-group-modern:focus-within, body.dark .input-group-modern:focus-within {
            border-color: #10b981 !important;
            background-color: #1e293b !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
        }

        html.dark .input-group-modern input, html.dark .input-group-modern textarea,
        body.dark .input-group-modern input, body.dark .input-group-modern textarea {
            color: #f8fafc !important;
        }

        html.dark .input-group-modern label, body.dark .input-group-modern label {
            color: #94a3b8 !important;
        }

        html.dark .input-group-modern .field-icon, body.dark .input-group-modern .field-icon {
            color: #94a3b8 !important;
        }

        html.dark .upload-drop-zone, body.dark .upload-drop-zone {
            background-color: #182339 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }
    </style>
@endpush

@section('content')
    <div class="px-1 pt-1 pb-28">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="formProfile" autocomplete="off">
            @csrf
            @method('PUT')

            {{-- ===== HERO PROFILE CARD ===== --}}
            <div class="profile-hero-card p-5 mb-4 text-white relative overflow-hidden text-center">
                <div class="relative z-10 flex flex-col items-center">
                    {{-- Avatar with Glow & Upload Trigger --}}
                    <div class="relative mb-3">
                        <div class="w-24 h-24 rounded-full p-1 bg-white/20 backdrop-blur-md shadow-xl border border-white/30">
                            @if (!empty($karyawan->foto) && Storage::disk('public')->exists('/karyawan/' . $karyawan->foto))
                                <img id="avatarPreview" src="{{ getfotoKaryawan($karyawan->foto) }}" alt="Profile" class="w-full h-full rounded-full object-cover shadow-inner">
                            @else
                                <img id="avatarPreview" src="{{ asset('assets/img/avatars/No_Image_Available.jpg') }}" alt="Profile" class="w-full h-full rounded-full object-cover shadow-inner">
                            @endif
                        </div>
                        <button type="button" onclick="document.getElementById('foto').click()" class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs shadow-lg border-2 border-white active:scale-90 transition-transform" title="Ganti Foto">
                            <i class="fa-solid fa-camera"></i>
                        </button>
                    </div>

                    <h2 class="text-base font-extrabold tracking-tight text-white mb-0.5">
                        {{ $karyawan->nama_karyawan ?? $user->name }}
                    </h2>
                    <div class="flex items-center gap-2 flex-wrap justify-center text-xs text-emerald-100/90 font-medium">
                        <span>NIK: {{ $karyawan->nik ?? '-' }}</span>
                        <span>&bull;</span>
                        <span>{{ $karyawan->nama_jabatan ?? 'Karyawan' }}</span>
                    </div>
                </div>

                {{-- Decorative circles --}}
                <div class="absolute -right-8 -bottom-8 w-32 h-32 rounded-full bg-white/10 pointer-events-none blur-xl"></div>
                <div class="absolute -left-6 -top-6 w-24 h-24 rounded-full bg-emerald-400/20 pointer-events-none blur-lg"></div>
            </div>

            {{-- ===== PERSONAL INFORMATION SECTION ===== --}}
            <div class="form-card p-4 mb-3.5 space-y-3">
                <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xs font-bold border border-teal-200/60 dark:border-teal-800/60">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">
                        Informasi Pribadi
                    </span>
                </div>

                {{-- Nama Lengkap --}}
                <div class="input-group-modern">
                    <i class="fa-solid fa-user field-icon"></i>
                    <label for="nama_karyawan">Nama Lengkap</label>
                    <input type="text" name="nama_karyawan" id="nama_karyawan" value="{{ $karyawan->nama_karyawan ?? '' }}" required>
                </div>

                {{-- No. KTP --}}
                <div class="input-group-modern">
                    <i class="fa-solid fa-address-card field-icon"></i>
                    <label for="no_ktp">Nomor KTP (NIK Kependudukan)</label>
                    <input type="text" name="no_ktp" id="no_ktp" value="{{ $karyawan->no_ktp ?? '' }}" required>
                </div>

                {{-- No. HP --}}
                <div class="input-group-modern">
                    <i class="fa-solid fa-phone field-icon"></i>
                    <label for="no_hp">No. WhatsApp / HP</label>
                    <input type="text" name="no_hp" id="no_hp" value="{{ $karyawan->no_hp ?? '' }}" required>
                </div>

                {{-- Alamat --}}
                <div class="input-group-modern">
                    <i class="fa-solid fa-location-dot field-icon" style="top:18px;"></i>
                    <label for="alamat">Alamat Tinggal</label>
                    <textarea name="alamat" id="alamat" required>{{ $karyawan->alamat ?? '' }}</textarea>
                </div>
            </div>

            {{-- ===== ACCOUNT CREDENTIALS SECTION ===== --}}
            <div class="form-card p-4 mb-3.5 space-y-3">
                <div class="flex items-center gap-2 pb-2.5 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-7 h-7 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs font-bold border border-sky-200/60 dark:border-sky-800/60">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">
                        Akun & Akses
                    </span>
                </div>

                {{-- Username --}}
                <div class="input-group-modern">
                    <i class="fa-solid fa-at field-icon"></i>
                    <label for="username">Username Akun</label>
                    <input type="text" name="username" id="username" value="{{ $user->username }}" required>
                </div>

                {{-- Email --}}
                <div class="input-group-modern">
                    <i class="fa-solid fa-envelope field-icon"></i>
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" value="{{ $user->email }}" required>
                </div>
            </div>

            {{-- ===== UPLOAD PHOTO SECTION ===== --}}
            <div class="form-card p-4 mb-4">
                <div class="flex items-center gap-2 pb-2.5 mb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold border border-amber-200/60 dark:border-amber-800/60">
                        <i class="fa-solid fa-image"></i>
                    </div>
                    <span class="text-xs font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">
                        Foto Profil Baru
                    </span>
                </div>

                <div class="upload-drop-zone flex flex-col items-center justify-center" onclick="document.getElementById('foto').click()">
                    <input type="file" name="foto" id="foto" accept=".jpg, .jpeg, .png" class="hidden">
                    <div class="w-12 h-12 rounded-2xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center text-xl mb-2 border border-teal-200/60 dark:border-teal-800/60">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <span class="text-xs font-extrabold text-slate-700 dark:text-slate-200 block">Ketuk untuk pilih foto profil</span>
                    <span class="text-[10.5px] text-slate-400 block mt-0.5">Format JPG, JPEG, PNG (Maks 2MB)</span>
                    <div id="fileName" class="text-xs font-bold text-teal-600 dark:text-teal-400 mt-2 truncate max-w-[240px]"></div>
                </div>
            </div>

            {{-- ===== SUBMIT BUTTON ===== --}}
            <button type="submit" id="btnSimpan" class="w-full py-3.5 rounded-2xl text-white font-extrabold text-sm flex items-center justify-center gap-2 shadow-lg active:scale-98 transition-all" style="background: linear-gradient(135deg, {{ $t['primary'] ?? '#0f766e' }} 0%, #0d9488 100%);">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Perubahan</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script>
        // Photo preview on file select
        document.getElementById('foto').addEventListener('change', function() {
            let file = this.files[0];
            const fileNameDisplay = document.getElementById('fileName');
            const avatarPreview = document.getElementById('avatarPreview');

            if (file) {
                fileNameDisplay.textContent = 'Terpilih: ' + file.name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (avatarPreview) avatarPreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            } else {
                fileNameDisplay.textContent = '';
            }
        });

        // Form Submit Validation
        $(function() {
            $("#formProfile").submit(function(e) {
                let nama_karyawan = $('input[name="nama_karyawan"]').val();
                let no_ktp = $('input[name="no_ktp"]').val();
                let no_hp = $('input[name="no_hp"]').val();
                let alamat = $('textarea[name="alamat"]').val();
                let username = $('input[name="username"]').val();
                let email = $('input[name="email"]').val();

                if (!nama_karyawan || !no_ktp || !no_hp || !alamat || !username || !email) {
                    e.preventDefault();
                    Swal.fire({
                        title: "Bidang Belum Lengkap",
                        text: 'Silakan isi seluruh formulir profil dengan lengkap!',
                        icon: "warning",
                        confirmButtonColor: '#0f766e',
                        customClass: {
                            popup: 'swal2-modern-choice-popup'
                        }
                    });
                    return false;
                }

                const btn = document.getElementById('btnSimpan');
                btn.disabled = true;
                btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i><span>Menyimpan...</span>`;
            });
        });
    </script>
@endpush

