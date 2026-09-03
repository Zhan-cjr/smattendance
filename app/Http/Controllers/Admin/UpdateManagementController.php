<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Update;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;

class UpdateManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Update::query();

        if (!empty($request->version)) {
            $query->where('version', 'like', '%' . $request->version . '%');
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $updates = $query->orderBy('released_at', 'desc')->paginate(10);
        $updates->appends(request()->all());

        return view('admin.update.index', compact('updates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.update.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'version' => 'required|unique:updates,version',
            'title' => 'required',
            'description' => 'nullable',
            'changelog' => 'nullable',
            'file_upload' => 'required|file|mimes:zip',
            'is_major' => 'boolean',
            'is_active' => 'boolean',
            'released_at' => 'nullable|date',
        ]);

        try {
            $file = $request->file('file_upload');
            $filename = 'update-v' . $request->version . '-' . time() . '.zip';
            $destinationPath = public_path('updates');
            
            // Create directory if not exists
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            // Move file
            $file->move($destinationPath, $filename);
            
            $fullPath = $destinationPath . '/' . $filename;
            $fileUrl = asset('updates/' . $filename);
            
            // Calculate size and checksum
            $fileSize = File::size($fullPath);
            $checksum = md5_file($fullPath);

            Update::create([
                'version' => $request->version,
                'title' => $request->title,
                'description' => $request->description,
                'changelog' => $request->changelog,
                'file_url' => $fileUrl,
                'file_size' => $fileSize,
                'checksum' => $checksum,
                'is_major' => $request->has('is_major') ? 1 : 0,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'migrations' => null,
                'seeders' => null,
                'released_at' => $request->released_at ? $request->released_at : now(),
            ]);

            return Redirect::route('admin.update.index')->with(messageSuccess('Update berhasil ditambahkan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()))->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $update = Update::findOrFail($id);
        return view('admin.update.show', compact('update'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $update = Update::findOrFail($id);
        return view('admin.update.edit', compact('update'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $update = Update::findOrFail($id);

        $request->validate([
            'version' => 'required|unique:updates,version,' . $id,
            'title' => 'required',
            'description' => 'nullable',
            'changelog' => 'nullable',
            'file_url' => 'required|url',
            'file_size' => 'nullable|numeric',
            'checksum' => 'nullable',
            'is_major' => 'boolean',
            'is_active' => 'boolean',
            'released_at' => 'nullable|date',
        ]);

        try {
            // Parse migrations dan seeders jika ada
            $migrations = null;
            if ($request->filled('migrations')) {
                $migrations = array_filter(array_map('trim', explode(',', $request->migrations)));
            }

            $seeders = null;
            if ($request->filled('seeders')) {
                $seeders = array_filter(array_map('trim', explode(',', $request->seeders)));
            }

            $update->update([
                'version' => $request->version,
                'title' => $request->title,
                'description' => $request->description,
                'changelog' => $request->changelog,
                'file_url' => $request->file_url,
                'file_size' => $request->file_size,
                'checksum' => $request->checksum,
                'is_major' => $request->has('is_major') ? 1 : 0,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'migrations' => $migrations ? json_encode($migrations) : null,
                'seeders' => $seeders ? json_encode($seeders) : null,
                'released_at' => $request->released_at ? $request->released_at : $update->released_at,
            ]);

            return Redirect::route('admin.update.index')->with(messageSuccess('Update berhasil diupdate'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()))->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id = Crypt::decrypt($id);
        try {
            $update = Update::findOrFail($id);
            $update->delete();
            return Redirect::back()->with(messageSuccess('Update berhasil dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $id = Crypt::decrypt($id);
        try {
            $update = Update::findOrFail($id);
            $update->update([
                'is_active' => $update->is_active ? 0 : 1
            ]);
            return Redirect::back()->with(messageSuccess('Status update berhasil diubah'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
