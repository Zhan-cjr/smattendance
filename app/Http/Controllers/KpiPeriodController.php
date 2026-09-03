<?php

namespace App\Http\Controllers;

use App\Models\KpiPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class KpiPeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = KpiPeriod::query();
        $query->orderBy('start_date', 'desc');
        if (!empty($request->nama_periode)) {
            $query->where('nama_periode', 'like', '%' . $request->nama_periode . '%');
        }
        $kpi_periods = $query->paginate(10);
        return view('kpi.periods.index', compact('kpi_periods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kpi.periods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            KpiPeriod::create([
                'nama_periode' => $request->nama_periode,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->filled('is_active') ? 1 : 0,
            ]);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
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
    public function edit(Request $request)
    {
        $id = $request->id;
        $kpi_period = KpiPeriod::findOrFail($id);
        return view('kpi.periods.edit', compact('kpi_period'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_periode' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $kpi_period = KpiPeriod::findOrFail($id);
            // If setting to active, deactivate others (Optional logic, can handle multiple active periods?)
            // Usually only one active period allowed.
            if ($request->filled('is_active')) {
                KpiPeriod::where('id', '<>', $id)->update(['is_active' => 0]);
            }

            $kpi_period->update([
                'nama_periode' => $request->nama_periode,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => $request->filled('is_active') ? 1 : 0,
            ]);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            KpiPeriod::findOrFail($id)->delete();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }
}
