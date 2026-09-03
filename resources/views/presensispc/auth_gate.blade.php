@extends('layouts.mobile.app')
@section('content')
    <style>
        .auth-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 50px auto;
            max-width: 400px;
            text-align: center;
        }
        .auth-card h4 {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 8px;
            height: 48px;
            font-size: 16px;
        }
        .btn-submit {
            margin-top: 20px;
            border-radius: 8px;
            height: 48px;
            font-size: 16px;
            font-weight: 600;
        }
        .alert-danger {
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>

    <div id="header-section">
        <div class="appHeader bg-primary text-light">
            <div class="left">
                <a href="{{ route('dashboard.index') }}" class="headerButton">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </a>
            </div>
            <div class="pageTitle">Akses Presensi Khusus</div>
            <div class="right"></div>
        </div>
    </div>

    <div id="content-section" style="margin-top: 56px; padding: 10px;">
        <div class="auth-card">
            <h4>Masukkan Password</h4>
            
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <form action="{{ route('presensispc.unlock') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password Rahasia" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-submit">Buka Akses</button>
            </form>
        </div>
    </div>
@endsection