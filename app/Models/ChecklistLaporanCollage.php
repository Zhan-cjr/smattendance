<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistLaporanCollage extends Model
{
    use HasFactory;

    protected $table = 'checklist_laporan_collages';
    protected $guarded = [];

    public function laporan()
    {
        return $this->belongsTo(ChecklistLaporan::class, 'laporan_id', 'id');
    }
}
