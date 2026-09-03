<?php

namespace App\Http\Controllers;

use App\Models\UserLoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class UserloginlogController extends Controller
{
    public function index(Request $request)
    {
        $query = UserLoginLog::query()->with('user');

        if ($request->filled('nama_user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nama_user . '%');
            });
        }

        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('login_at', [$request->tanggal_awal . ' 00:00:00', $request->tanggal_akhir . ' 23:59:59']);
        }

        $logs = $query->orderBy('login_at', 'desc')->paginate(20);
        $logs->appends($request->all());

        return view('utilities.user_login_log.index', compact('logs'));
    }
}
