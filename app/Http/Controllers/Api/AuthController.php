<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        // Ambil data karyawan yang terhubung
        $karyawan = null;
        if ($user->userkaryawan) {
            $karyawan = \App\Models\Karyawan::find($user->userkaryawan->nik);
        }

        // Hapus token lama agar tidak menumpuk
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
            ],
            'karyawan' => $karyawan ? [
                'nik'  => $karyawan->nik,
                'nama' => $karyawan->nama_karyawan,
                'spy'  => (int) $karyawan->spy,
            ] : null,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logout berhasil'
        ]);
    }
}