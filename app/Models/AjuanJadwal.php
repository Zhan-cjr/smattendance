<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjuanJadwal extends Model
{
    use HasFactory;

    protected $table = 'ajuan_jadwal';
    protected $guarded = [];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function jamKerjaAwal()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja_awal', 'kode_jam_kerja');
    }

    public function jamKerjaTujuan()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja_tujuan', 'kode_jam_kerja');
    }
}
