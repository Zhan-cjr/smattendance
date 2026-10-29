<?php

namespace App\Http\Controllers;

use App\Models\Permission_group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;

class Permission_groupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Permission_group::query();

        if (!empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Subquery or count for permissions in each group
        $permission_groups = $query->orderBy('name')->paginate(15);
        $permission_groups->appends(request()->all());

        // Attach count of permissions for each group
        $groupCounts = Permission::selectRaw('id_permission_group, count(*) as total')
            ->groupBy('id_permission_group')
            ->pluck('total', 'id_permission_group');

        foreach ($permission_groups as $group) {
            $group->permissions_count = $groupCounts[$group->id] ?? 0;
        }

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.permission_groups.index' : 'settings.permission_groups.index';
        return view($view, compact('permission_groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.permission_groups.create' : 'settings.permission_groups.create';
        return view($view);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:permission_groups,name',
        ], [
            'name.required' => 'Nama group permission wajib diisi.',
            'name.unique' => 'Nama group permission sudah digunakan.',
        ]);

        try {
            Permission_group::create(['name' => $request->name]);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $permission_groups = Permission_group::findOrFail($decryptedId);
        } catch (\Exception $e) {
            $permission_groups = Permission_group::findOrFail($id);
        }

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.permission_groups.edit' : 'settings.permission_groups.edit';
        return view($view, compact('permission_groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $validated = $request->validate([
            'name' => 'required|unique:permission_groups,name,' . $decryptedId,
        ], [
            'name.required' => 'Nama group permission wajib diisi.',
            'name.unique' => 'Nama group permission sudah digunakan.',
        ]);

        try {
            Permission_group::where('id', $decryptedId)->update(['name' => $request->name]);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        try {
            Permission_group::where('id', $decryptedId)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }
}
