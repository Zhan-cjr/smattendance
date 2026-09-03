<?php

namespace App\Charts;

use App\Models\Karyawan;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\DB;

class StatusKaryawanChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build($request = null)
    {
        // Get all statuses from the new table
        $statuses = DB::table('status_karyawan')->get();

        $labels = [];
        $data = [];

        foreach ($statuses as $status) {
            $query = Karyawan::query();
            $query->where('status_karyawan', $status->kode_status_karyawan)
                ->where('status_aktif_karyawan', 1);

            // Filter by user access/request
            if (!empty($request->user_cabangs) && is_array($request->user_cabangs)) {
                $query->whereIn('karyawan.kode_cabang', $request->user_cabangs);
            } elseif (!empty($request->kode_cabang)) {
                $query->where('karyawan.kode_cabang', $request->kode_cabang);
            }

            if (!empty($request->user_departemens) && is_array($request->user_departemens)) {
                $query->whereIn('karyawan.kode_dept', $request->user_departemens);
            } elseif (!empty($request->kode_dept)) {
                $query->where('karyawan.kode_dept', $request->kode_dept);
            }

            $count = $query->count();
            
            $labels[] = $status->nama_status_karyawan;
            $data[] = $count;
        }

        return $this->chart->donutChart()
            ->addData($data)
            ->setLabels($labels)
            ->setColors(['#0f766e', '#0284c7', '#f59e0b', '#8b5cf6', '#ec4899', '#10b981', '#64748b'])
            ->setHeight(270)
            ->setDataLabels(true);
    }
}
