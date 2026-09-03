<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;
    protected $table = 'presensi';
    protected $guarded = [];

    public function mesinfingerprint()
    {
        return $this->belongsTo(MesinFingerprint::class, 'id_mesin');
    }
}
