<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class GajiExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $data;
    protected $view;

    public function __construct(array $data, $view = 'laporan.gaji_excel')
    {
        $this->data = $data;
        $this->view = $view;
    }

    public function view(): View
    {
        return view($this->view, $this->data);
    }

    public function title(): string
    {
        return 'Laporan Gaji';
    }
}
