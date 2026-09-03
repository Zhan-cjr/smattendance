<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ApprovalLayer;

class Izindinas extends Model
{
    use HasFactory;
    protected $table = 'presensi_izindinas';
    protected $primaryKey = 'kode_izin_dinas';
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function getNextApprovalLayer()
    {
        // Asumsi Feature Code untuk model ini adalah 'IZIN'
        $nextLevel = $this->approval_step;
        
        $kode_dept = $this->kode_dept ?? null;

        $layer = ApprovalLayer::where('feature', 'IZIN')
            ->where('level', $nextLevel)
            ->where(function ($q) use ($kode_dept) {
                $q->where('kode_dept', $kode_dept)
                  ->orWhereNull('kode_dept');
            })
            ->first();

        return $layer;
    }
}
