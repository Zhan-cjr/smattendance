<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\KpiIndicator;
use App\Models\KpiJabatanIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KpiJabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Jabatan::query();
        $query->orderBy('nama_jabatan', 'asc');
        if (!empty($request->nama_jabatan)) {
            $query->where('nama_jabatan', 'like', '%' . $request->nama_jabatan . '%');
        }
        $jabatan = $query->paginate(10);
        return view('kpi.jabatan.index', compact('jabatan'));
    }

    public function setting($kode_jabatan)
    {
        $jabatan = Jabatan::where('kode_jabatan', $kode_jabatan)->firstOrFail();
        // Get existing indicators for this jabatan
        $existing_indicators = KpiJabatanIndicator::where('kode_jabatan', $kode_jabatan)->get();
        // Get all master indicators
        $master_indicators = KpiIndicator::orderBy('nama_indikator')->get();

        return view('kpi.jabatan.setting', compact('jabatan', 'existing_indicators', 'master_indicators'));
    }

    public function store(Request $request, $kode_jabatan)
    {
        // $request->indicators will be an array of indicator IDs
        // $request->bobot will be an array keyed by indicator ID
        // $request->target will be an array keyed by indicator ID

        DB::beginTransaction();
        try {
            // Delete existing mappings for this jabatan
            KpiJabatanIndicator::where('kode_jabatan', $kode_jabatan)->delete();

            if ($request->has('kpi_indicator_id')) {
                foreach ($request->kpi_indicator_id as $index => $indicator_id) {
                    $bobot = $request->bobot[$index];
                    $target = $request->target[$index];

                    if (!empty($indicator_id)) {
                         KpiJabatanIndicator::create([
                            'kode_jabatan' => $kode_jabatan,
                            'kpi_indicator_id' => $indicator_id,
                            'bobot' => $bobot,
                            'target' => $target
                        ]);
                    }
                }
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Indikator Jabatan Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
}
