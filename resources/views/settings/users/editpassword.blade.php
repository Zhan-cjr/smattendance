@extends('layouts.mobile.modern')

@section('title', 'Ubah Password')

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

        .form-label-group input {
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
        .form-label-group input:not(:placeholder-shown) ~ label {
            top: 2px;
            left: 42px;
            font-size: 10px;
            font-weight: 600;
            color: #32745e;
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
            margin-top: 5px;
            transition: all 0.3s;
        }

        .btn-submit-modern:active {
            transform: scale(0.97);
            background: #2a6350;
        }

        .custom-control-label {
            font-size: 14px;
            color: #32745e;
            font-weight: 600;
            cursor: pointer;
        }

        .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #32745e !important;
            border-color: #32745e !important;
        }
    </style>
@endpush

@section('content')
    <div class="fade-up form-container">
        <form action="{{ route('users.updatepassword', Crypt::encrypt($user->id)) }}" method="POST" id="formPassword" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="form-label-group">
                <ion-icon name="at-outline" class="input-icon"></ion-icon>
                <input type="text" name="username" id="username" placeholder=" " value="{{ $user->username }}" required>
                <label for="username">Username</label>
            </div>
            @error('username')
                <div class="text-red-500 text-xs px-3 mb-2 font-semibold">
                    {{ $message }}
                </div>
            @enderror

            <div class="form-label-group">
                <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                <input type="password" name="passwordbaru" id="passwordbaru" placeholder=" " required>
                <label for="passwordbaru">Password Baru</label>
            </div>

            <div class="form-label-group">
                <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                <input type="password" name="konfirmasipassword" id="konfirmasipassword" placeholder=" " required>
                <label for="konfirmasipassword">Konfirmasi Password</label>
            </div>

            <div class="px-2 mb-4">
                <div class="custom-control custom-checkbox flex items-center gap-2">
                    <input type="checkbox" class="w-4 h-4 accent-[#32745e]" id="show-password" onclick="tooglePassword()">
                    <label class="custom-control-label" for="show-password">Tampilkan Password</label>
                </div>
            </div>

            <button type="submit" class="btn-submit-modern" id="btnSimpan">
                <ion-icon name="save-outline"></ion-icon>
                <span>Update Password</span>
            </button>
        </form>
    </div>
@endsection

@push('myscript')
    <script>
        function tooglePassword() {
            var x = document.getElementById("passwordbaru");
            var y = document.getElementById("konfirmasipassword");
            if (x.type === "password") {
                x.type = "text";
                y.type = "text";
            } else {
                x.type = "password";
                y.type = "password";
            }
        }
        
        $("#formPassword").submit(function(e) {
             var passwordbaru = $("#passwordbaru").val();
             var konfirmasipassword = $("#konfirmasipassword").val();
             
             if(passwordbaru == "") {
                 e.preventDefault();
                 Swal.fire({title: "Oops!", text: 'Password Baru Harus Diisi !', icon: "warning"});
                 return false;
             }
             if(konfirmasipassword == "") {
                 e.preventDefault();
                 Swal.fire({title: "Oops!", text: 'Konfirmasi Password Harus Diisi !', icon: "warning"});
                 return false;
             }
             if (passwordbaru != konfirmasipassword) {
                 e.preventDefault();
                 Swal.fire({title: "Oops!", text: 'Password Tidak Sama !', icon: "warning"});
                 return false;
             }

             const btn = document.getElementById('btnSimpan');
             btn.disabled = true;
             btn.innerHTML = `<ion-icon name="sync-outline" class="animate-spin"></ion-icon><span>Memproses...</span>`;
        });
    </script>
@endpush
