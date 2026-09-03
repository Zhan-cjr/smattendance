<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PresensiKaryawanExport implements FromView, ShouldAutoSize, WithTitle, WithDrawings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('laporan.presensi_karyawan_excel', $this->data);
    }

    public function title(): string
    {
        return 'Laporan Presensi Karyawan';
    }

    public function drawings()
    {
        $karyawan = $this->data['karyawan'];
        $drawings = [];

        if (!empty($karyawan->foto)) {
            $path = storage_path('app/public/karyawan/' . $karyawan->foto);
            
            if (file_exists($path)) {
                $drawing = new Drawing();
                $drawing->setName('Foto Karyawan');
                $drawing->setDescription('Foto Karyawan');
                $drawing->setPath($path);
                $drawing->setHeight(100); 
                $drawing->setCoordinates('A4');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(5);
                $drawings[] = $drawing;
            } else {
                 // Try finding it in public path if storage path fails (e.g. symlink structure differences)
                 $publicPath = public_path('storage/karyawan/' . $karyawan->foto);
                 if(file_exists($publicPath)){
                    $drawing = new Drawing();
                    $drawing->setName('Foto Karyawan');
                    $drawing->setDescription('Foto Karyawan');
                    $drawing->setPath($publicPath);
                    $drawing->setHeight(100);
                    $drawing->setCoordinates('A4');
                    $drawing->setOffsetX(10);
                    $drawing->setOffsetY(5);
                    $drawings[] = $drawing;
                 }
            }
        }
        
        return $drawings;
    }
}
