<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\ChecklistCheckpoint;
use App\Models\ChecklistLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChecklistAdminController extends Controller
{
    /**
     * Master Checkpoints List
     */
    public function indexCheckpoints(Request $request)
    {
        $query = ChecklistCheckpoint::with('cabangs');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('kode_cabang')) {
            $kode = $request->kode_cabang;
            $query->where(function ($q) use ($kode) {
                $q->where('all_cabang', true)
                  ->orWhereHas('cabangs', function ($sub) use ($kode) {
                      $sub->where('cabang.kode_cabang', $kode);
                  });
            });
        }

        $checkpoints = $query->orderBy('tipe', 'asc')
            ->orderBy('urutan', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $cabangs = Cabang::orderBy('nama_cabang', 'asc')->get();

        return view('admin.checklist.checkpoints.index', compact('checkpoints', 'cabangs'));
    }

    /**
     * Store new Checkpoint
     */
    public function storeCheckpoint(Request $request)
    {
        $request->validate([
            'nama_checkpoint' => 'required|string|max:150',
            'tipe' => 'required|in:opening,closing,both',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer',
            'all_cabang' => 'nullable|boolean',
            'cabang_ids' => 'nullable|array',
        ]);

        $allCabang = $request->boolean('all_cabang', false);
        if (!$request->has('all_cabang') && empty($request->input('cabang_ids'))) {
            $allCabang = true; // default all branches if none selected
        }

        $checkpoint = ChecklistCheckpoint::create([
            'nama_checkpoint' => $request->nama_checkpoint,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
            'urutan' => $request->input('urutan', 0),
            'is_active' => true,
            'all_cabang' => $allCabang,
        ]);

        if (!$allCabang && $request->filled('cabang_ids')) {
            $checkpoint->cabangs()->sync($request->cabang_ids);
        }

        return redirect()->route('admin.checklist.checkpoints.index')
            ->with('success', 'Checkpoint berhasil ditambahkan.');
    }

    /**
     * Get Checkpoint Data for Edit Modal
     */
    public function editCheckpoint($id)
    {
        $checkpoint = ChecklistCheckpoint::with('cabangs')->findOrFail($id);
        $selectedCabangs = $checkpoint->cabangs->pluck('kode_cabang')->toArray();

        return response()->json([
            'checkpoint' => $checkpoint,
            'selected_cabangs' => $selectedCabangs,
        ]);
    }

    /**
     * Update Checkpoint
     */
    public function updateCheckpoint(Request $request, $id)
    {
        $checkpoint = ChecklistCheckpoint::findOrFail($id);

        $request->validate([
            'nama_checkpoint' => 'required|string|max:150',
            'tipe' => 'required|in:opening,closing,both',
            'deskripsi' => 'nullable|string',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'all_cabang' => 'nullable|boolean',
            'cabang_ids' => 'nullable|array',
        ]);

        $allCabang = $request->boolean('all_cabang', false);
        if (!$request->has('all_cabang') && empty($request->input('cabang_ids'))) {
            $allCabang = true;
        }

        $checkpoint->update([
            'nama_checkpoint' => $request->nama_checkpoint,
            'tipe' => $request->tipe,
            'deskripsi' => $request->deskripsi,
            'urutan' => $request->input('urutan', 0),
            'is_active' => $request->boolean('is_active', true),
            'all_cabang' => $allCabang,
        ]);

        if ($allCabang) {
            $checkpoint->cabangs()->detach();
        } else {
            $checkpoint->cabangs()->sync($request->input('cabang_ids', []));
        }

        return redirect()->route('admin.checklist.checkpoints.index')
            ->with('success', 'Checkpoint berhasil diperbarui.');
    }

    /**
     * Delete Checkpoint
     */
    public function destroyCheckpoint($id)
    {
        $checkpoint = ChecklistCheckpoint::findOrFail($id);
        $checkpoint->cabangs()->detach();
        $checkpoint->delete();

        return redirect()->route('admin.checklist.checkpoints.index')
            ->with('success', 'Checkpoint berhasil dihapus.');
    }

    /**
     * Admin Monitoring List of All Reports
     */
    public function indexMonitoring(Request $request)
    {
        $query = ChecklistLaporan::with(['karyawan', 'cabang', 'collages', 'details']);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        if ($request->filled('kode_cabang')) {
            $query->where('kode_cabang', $request->kode_cabang);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('kode_laporan', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function ($sub) use ($search) {
                      $sub->where('nama_karyawan', 'like', "%{$search}%")
                          ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        $laporans = $query->latest('tanggal')
            ->latest('jam')
            ->paginate(15)
            ->withQueryString();

        $cabangs = Cabang::orderBy('nama_cabang', 'asc')->get();

        return view('admin.checklist.monitoring.index', compact('laporans', 'cabangs'));
    }

    /**
     * Admin Show Detail Report
     */
    public function showMonitoring($id)
    {
        $laporan = ChecklistLaporan::with(['karyawan', 'cabang', 'collages', 'details'])
            ->findOrFail($id);

        return view('admin.checklist.monitoring.show', compact('laporan'));
    }

    /**
     * Admin Delete Report
     */
    public function destroyLaporan($id)
    {
        $laporan = ChecklistLaporan::with(['details', 'collages'])->findOrFail($id);

        $disk = Storage::disk('public');
        foreach ($laporan->details as $d) {
            if ($d->foto && $disk->exists($d->foto)) {
                $disk->delete($d->foto);
            }
        }

        foreach ($laporan->collages as $c) {
            if ($c->foto_collage && $disk->exists($c->foto_collage)) {
                $disk->delete($c->foto_collage);
            }
        }

        $laporan->delete();

        return redirect()->route('admin.checklist.monitoring.index')
            ->with('success', 'Laporan checklist berhasil dihapus.');
    }
}
