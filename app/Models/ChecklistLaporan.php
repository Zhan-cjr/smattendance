<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistLaporan extends Model
{
    use HasFactory;

    protected $table = 'checklist_laporan';
    protected $guarded = [];

    protected $casts = [
        'disclaimer_agreed' => 'boolean',
        'tanggal' => 'date',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept', 'kode_dept');
    }

    public function details()
    {
        return $this->hasMany(ChecklistLaporanDetail::class, 'laporan_id', 'id');
    }

    public function collages()
    {
        return $this->hasMany(ChecklistLaporanCollage::class, 'laporan_id', 'id')->orderBy('batch_no', 'asc');
    }
}
