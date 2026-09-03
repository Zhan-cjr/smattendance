<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamanGenerateHistory extends Model
{
    use HasFactory;

    protected $table = 'pinjaman_generate_history';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function pembayaran_pinjaman()
    {
        return $this->hasMany(PembayaranPinjaman::class, 'history_generate_id', 'id');
    }
}
