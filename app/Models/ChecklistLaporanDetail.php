<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistLaporanDetail extends Model
{
    use HasFactory;

    protected $table = 'checklist_laporan_detail';
    protected $guarded = [];

    public function laporan()
    {
        return $this->belongsTo(ChecklistLaporan::class, 'laporan_id', 'id');
    }

    public function checkpoint()
    {
        return $this->belongsTo(ChecklistCheckpoint::class, 'checkpoint_id', 'id');
    }
}
