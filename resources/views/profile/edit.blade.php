@extends('layouts.app')
@section('titlepage', 'Edit Profile')
@section('content')
@section('navigasi')
    <span>Edit Profile</span>
@endsection
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Edit Profile</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.updateprofile') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="">Nama User</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi Password">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary w-100"><i class="ti ti-send me-1"></i> Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
