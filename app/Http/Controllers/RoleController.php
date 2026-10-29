<?php

namespace App\Http\Controllers;

use App\Models\Permission_group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions');
        if (!auth()->user()->hasRole('master admin')) {
            $query->where('name', '!=', 'master admin');
        }
        if (!empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        $roles = $query->paginate(10);
        $roles->appends(request()->all());

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.roles.index' : 'settings.roles.index';
        return view($view, compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.roles.create' : 'settings.roles.create';
        return view($view);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
        ]);

        $name = strtolower($request->name);
        try {
            Role::create(['name' => $name]);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return Redirect::back()->with(['error' => $message]);
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
            $role = Role::findOrFail($decryptedId);
        } catch (\Exception $e) {
            $role = Role::findOrFail($id);
        }

        if ($role->name === 'master admin' && !auth()->user()->hasRole('master admin')) {
            abort(403, 'Unauthorized action.');
        }

        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.roles.edit' : 'settings.roles.edit';
        return view($view, compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $decryptedId,
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique' => 'Nama role sudah digunakan.',
        ]);

        try {
            $role = Role::findOrFail($decryptedId);
            if ($role->name === 'master admin' && !auth()->user()->hasRole('master admin')) {
                abort(403, 'Unauthorized action.');
            }
            Role::where('id', $decryptedId)->update(['name' => strtolower($request->name)]);

            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        try {
            $role = Role::findOrFail($decryptedId);
            if ($role->name === 'master admin' && !auth()->user()->hasRole('master admin')) {
                abort(403, 'Unauthorized action.');
            }
            Role::where('id', $decryptedId)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }


    public function createrolepermission($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }

        $role = Role::findById($decryptedId);
        if ($role->name === 'master admin' && !auth()->user()->hasRole('master admin')) {
            abort(403, 'Unauthorized action.');
        }
        $permissions = Permission::orderBy('id_permission_group')
            ->selectRaw('id_permission_group,permission_groups.name as group_name,GROUP_CONCAT(permissions.id,"-",permissions.name) as permissions')
            ->join('permission_groups', 'permissions.id_permission_group', '=', 'permission_groups.id')
            ->groupBy('id_permission_group')
            ->groupBy('permission_groups.name')
            ->get();

        $rolepermissions = $role->permissions->pluck('name')->toArray();
        $view = session('admin_theme', 'v2') === 'v2' ? 'v2.roles.create_role_permission' : 'settings.roles.create_role_permission';
        return view($view, compact('permissions', 'role', 'rolepermissions'));
    }

    public function storerolepermission($id, Request $request)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
        } catch (\Exception $e) {
            $decryptedId = $id;
        }
        $role = Role::findById($decryptedId);
        if ($role->name === 'master admin' && !auth()->user()->hasRole('master admin')) {
            abort(403, 'Unauthorized action.');
        }
        $permissions = $request->permission;
        $old_permissions = $role->permissions->pluck('name')->toArray();


        if (empty($permissions)) {
            return Redirect::back()->with(['warning' => 'Data Permission Harus Di Pilih']);
        }

        try {
            $role->revokePermissionTo($old_permissions);
            $role->givePermissionTo($permissions);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['error' => $e->getMessage()]);
        }
    }
}
