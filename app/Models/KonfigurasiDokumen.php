<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KonfigurasiDokumen extends Model
{
    use HasFactory;
    protected $table = "konfigurasi_dokumen";
    protected $primaryKey = "kode_dokumen";
    public $incrementing = false;
    protected $guarded = [];
}
