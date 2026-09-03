<?php

namespace App\Charts;

use App\Models\Karyawan;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\Facades\DB;

class JeniskelaminkaryawanChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build($request = null)
    {
        // Ambil jumlah karyawan berdasarkan jenis_kelamin (L, P)
        $query = Karyawan::query();
        $query->where('status_aktif_karyawan', 1)
            ->select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin');
        
        // Filter berdasarkan akses user jika ada di request
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
        $rawData = $query->pluck('total', 'jenis_kelamin')->toArray();

        $labels = ['Laki-Laki', 'Perempuan'];
        $data = [
            (int) ($rawData['L'] ?? 0),
            (int) ($rawData['P'] ?? 0),
        ];

        return $this->chart->donutChart()
            ->addData($data)
            ->setLabels($labels)
            ->setColors(['#3b82f6', '#ec4899'])
            ->setHeight(270)
            ->setDataLabels(true);
    }
}
