<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjaman';
    protected $guarded = ['id'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function rencana_cicilan()
    {
        return $this->hasMany(RencanaCicilan::class, 'pinjaman_id', 'id');
    }

    public function pembayaran_pinjaman()
    {
        return $this->hasMany(PembayaranPinjaman::class, 'pinjaman_id', 'id');
    }

    public function getStatusLabelAttribute()
    {
        if ($this->status == 'A') {
            return '<span class="badge bg-success">Aktif</span>';
        } elseif ($this->status == 'L') {
            return '<span class="badge bg-primary">Lunas</span>';
        } elseif ($this->status == 'B') {
            return '<span class="badge bg-danger">Batal</span>';
        }
    }
}
