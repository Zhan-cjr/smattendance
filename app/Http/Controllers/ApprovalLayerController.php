<?php

namespace App\Http\Controllers;

use App\Models\ApprovalLayer;
use App\Models\Cabang;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Redirect;

class ApprovalLayerController extends Controller
{
    public function index()
    {
        $approvalLayers = ApprovalLayer::orderBy('feature')
            ->orderBy('level')
            ->get();
        $cabangs = Cabang::all();
        $departemens = Departemen::all();
        $jabatans = \App\Models\Jabatan::all();

        return view('konfigurasi.approvallayer.index', compact('approvalLayers', 'cabangs', 'departemens', 'jabatans'));
    }

    public function create()
    {
        $roles = Role::all();
        $departemen = Departemen::all();
        $cabang = Cabang::all();
        $jabatan = \App\Models\Jabatan::all();
        return view('konfigurasi.approvallayer.create', compact('roles', 'departemen', 'cabang', 'jabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'role_names' => 'required|array|min:1',
            'role_names.*' => 'required|string',
        ]);

        try {
            // Delete existing configurations for this combination to prevent duplicates
            ApprovalLayer::where('kode_cabang', $request->kode_cabang)
                ->where('kode_dept', $request->kode_dept)
                ->where('kode_jabatan', $request->kode_jabatan)
                ->where('feature', 'IZIN')
                ->delete();

            $level = 1;
            foreach ($request->role_names as $role) {
                ApprovalLayer::create([
                    'feature' => 'IZIN',
                    'level' => $level,
                    'role_name' => $role,
                    'kode_dept' => $request->kode_dept,
                    'kode_jabatan' => $request->kode_jabatan,
                    'kode_cabang' => $request->kode_cabang,
                ]);
                $level++;
            }

            return Redirect::route('approvallayer.index')->with(['success' => 'Data Konfigurasi Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function editGroup(Request $request)
    {
        $kode_cabang = $request->query('cabang');
        $kode_dept = $request->query('dept');
        $kode_jabatan = $request->query('jabatan');

        $query = ApprovalLayer::where('feature', 'IZIN');
        
        if ($kode_cabang !== 'ALL') $query->where('kode_cabang', $kode_cabang);
        else $query->whereNull('kode_cabang');

        if ($kode_dept !== 'ALL') $query->where('kode_dept', $kode_dept);
        else $query->whereNull('kode_dept');

        if ($kode_jabatan !== 'ALL') $query->where('kode_jabatan', $kode_jabatan);
        else $query->whereNull('kode_jabatan');

        $approvalLayers = $query->orderBy('level')->get();

        $roles = Role::all();
        $departemen = Departemen::all();
        $cabang = Cabang::all();
        $jabatan = \App\Models\Jabatan::all();

        return view('konfigurasi.approvallayer.edit', compact(
            'approvalLayers', 'roles', 'departemen', 'cabang', 'jabatan',
            'kode_cabang', 'kode_dept', 'kode_jabatan'
        ));
    }

    public function updateGroup(Request $request)
    {
        $request->validate([
            'role_names' => 'required|array|min:1',
            'role_names.*' => 'required|string',
        ]);

        try {
            // Delete existing configs for the *original* combination being edited
            // (Assuming target parameters could change, we delete old and insert new. 
            // In Flow Builder, target is usually readonly during edit, but let's handle it)
            
            $old_cabang = $request->old_kode_cabang !== 'ALL' ? $request->old_kode_cabang : null;
            $old_dept = $request->old_kode_dept !== 'ALL' ? $request->old_kode_dept : null;
            $old_jabatan = $request->old_kode_jabatan !== 'ALL' ? $request->old_kode_jabatan : null;

            ApprovalLayer::where('feature', 'IZIN')
                ->where('kode_cabang', $old_cabang)
                ->where('kode_dept', $old_dept)
                ->where('kode_jabatan', $old_jabatan)
                ->delete();

            // Also delete if the new target already exists to prevent duplication
            ApprovalLayer::where('feature', 'IZIN')
                ->where('kode_cabang', $request->kode_cabang)
                ->where('kode_dept', $request->kode_dept)
                ->where('kode_jabatan', $request->kode_jabatan)
                ->delete();

            $level = 1;
            foreach ($request->role_names as $role) {
                ApprovalLayer::create([
                    'feature' => 'IZIN',
                    'level' => $level,
                    'role_name' => $role,
                    'kode_dept' => $request->kode_dept,
                    'kode_jabatan' => $request->kode_jabatan,
                    'kode_cabang' => $request->kode_cabang,
                ]);
                $level++;
            }

            return Redirect::route('approvallayer.index')->with(['success' => 'Data Konfigurasi Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function destroyGroup(Request $request)
    {
        try {
            $kode_cabang = $request->query('cabang') !== 'ALL' ? $request->query('cabang') : null;
            $kode_dept = $request->query('dept') !== 'ALL' ? $request->query('dept') : null;
            $kode_jabatan = $request->query('jabatan') !== 'ALL' ? $request->query('jabatan') : null;

            ApprovalLayer::where('feature', 'IZIN')
                ->where('kode_cabang', $kode_cabang)
                ->where('kode_dept', $kode_dept)
                ->where('kode_jabatan', $kode_jabatan)
                ->delete();

            return Redirect::back()->with(['success' => 'Data Konfigurasi Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
}
