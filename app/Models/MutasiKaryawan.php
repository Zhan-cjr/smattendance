<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiKaryawan extends Model
{
    protected $table = 'mutasi_karyawan';
    protected $guarded = [];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function cabangLama()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang_lama', 'kode_cabang');
    }
    
    public function cabangBaru()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang_baru', 'kode_cabang');
    }

    public function deptLama()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept_lama', 'kode_dept');
    }

    public function deptBaru()
    {
        return $this->belongsTo(Departemen::class, 'kode_dept_baru', 'kode_dept');
    }

    public function jabatanLama()
    {
        return $this->belongsTo(Jabatan::class, 'kode_jabatan_lama', 'kode_jabatan');
    }

    public function jabatanBaru()
    {
        return $this->belongsTo(Jabatan::class, 'kode_jabatan_baru', 'kode_jabatan');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
