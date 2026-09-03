<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranPinjaman extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_pinjaman';
    protected $guarded = ['id'];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id', 'id');
    }

    public function rencana_cicilan()
    {
        return $this->belongsTo(RencanaCicilan::class, 'rencana_cicilan_id', 'id');
    }

    public function history_generate()
    {
        return $this->belongsTo(PinjamanGenerateHistory::class, 'history_generate_id', 'id');
    }
}
