<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengumuman::query();
        if (!empty($request->judul)) {
            $query->where('judul', 'like', '%' . $request->judul . '%');
        }
        $pengumuman = $query->orderBy('created_at', 'desc')->paginate(10);
        $pengumuman->appends(request()->all());
        
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user->hasRole('karyawan')) {
            return view('pengumuman.index_mobile', compact('pengumuman'));
        }

        return view('pengumuman.index', compact('pengumuman'));
    }

    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $pengumuman = Pengumuman::findOrFail($id);
        
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user->hasRole('karyawan')) {
            return view('pengumuman.show_mobile', compact('pengumuman'));
        }

        // Default or Admin view can be added here if needed
        return view('pengumuman.show', compact('pengumuman'));
    }

    public function create()
    {
        return view('pengumuman.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required',
        ]);

        try {
            DB::beginTransaction();
            $pengumuman = Pengumuman::create([
                'judul' => $request->judul,
                'isi' => $request->isi,
            ]);
            
            // Send Notification to all Users (Internal)
            $users = \App\Models\User::all(); // Or filter active users
            \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\PengumumanNotification($pengumuman));

            // Send OneSignal Push Notification
            // $this->sendOneSignalNotification($pengumuman->judul, $pengumuman->isi);

            DB::commit();
            
            return Redirect::route('pengumuman.index')->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollback();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $delete = Pengumuman::where('id', $id)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }

    /**
     * Send Push Notification via OneSignal
     */
    private function sendOneSignalNotification($title, $message)
    {
        // Get Icon from General Settings
        $icon = asset('assets/img/icon-48x48.png'); // Default
        $setting = \App\Models\Pengaturanumum::first();
        if ($setting && $setting->logo && \Illuminate\Support\Facades\Storage::exists('public/logo/' . $setting->logo)) {
            $icon = asset('storage/logo/' . $setting->logo);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . config('services.onesignal.rest_api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', [
                'app_id' => config('services.onesignal.app_id'),
                'included_segments' => ['All'], // Send to all subscribed users
                'headings' => ['en' => $title],
                'contents' => ['en' => substr(strip_tags($message), 0, 100) . '...'],
                'url' => route('pengumuman.index'), // Open this URL when clicked
                'chrome_web_icon' => $icon,
            ]);

            Log::info('OneSignal Response: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('OneSignal Error: ' . $e->getMessage());
        }
    }
}
