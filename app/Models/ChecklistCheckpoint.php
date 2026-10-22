<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistCheckpoint extends Model
{
    use HasFactory;

    protected $table = 'checklist_checkpoints';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'all_cabang' => 'boolean',
    ];

    public function cabangs()
    {
        return $this->belongsToMany(Cabang::class, 'checklist_checkpoint_cabang', 'checkpoint_id', 'kode_cabang', 'id', 'kode_cabang');
    }

    /**
     * Scope for a specific branch and type (opening/closing)
     */
    public function scopeForBranchAndType($query, $kodeCabang, $tipe)
    {
        return $query->where('is_active', true)
            ->where(function ($q) use ($tipe) {
                $q->where('tipe', $tipe)
                  ->orWhere('tipe', 'both');
            })
            ->where(function ($q) use ($kodeCabang) {
                $q->where('all_cabang', true)
                  ->orWhereHas('cabangs', function ($sub) use ($kodeCabang) {
                      $sub->where('cabang.kode_cabang', $kodeCabang);
                  });
            })
            ->orderBy('urutan', 'asc')
            ->orderBy('id', 'asc');
    }
}
