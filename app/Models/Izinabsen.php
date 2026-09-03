<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ApprovalLayer;

class Izinabsen extends Model
{
    use HasFactory;
    protected $table = 'presensi_izinabsen';
    protected $primaryKey = 'kode_izin';
    public $incrementing = false;
    protected $guarded = [];

    public function getNextApprovalLayer()
    {
        // Asumsi Feature Code untuk model ini adalah 'IZIN_ABSEN'
        // Kita butuh akses ke department karyawan terkait untuk logic dept-specific
        // Namun relationship karyawan belum definisikan di model ini secara explicit di sini
        // Tapi di query controller sudah di-join.
        // Untuk aman-nya, kita query manual saja atau rely on properties kalau sudah di-hydrated.
        
        // Cek next level
        $nextLevel = $this->approval_step;
        
        // Kita perlu tahu kode_dept pengunuju. 
        // Jika model ini di-load via query builder di controller yang men-select 'karyawan.kode_dept', 
        // maka $this->kode_dept tersedia.
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

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function isWaitingFor($roleName)
    {
        if ($this->status != 0) return false;
        
        $nextLayer = $this->getNextApprovalLayer();
        if (!$nextLayer) return false;

        return $nextLayer->role_name === $roleName;
    }
}
