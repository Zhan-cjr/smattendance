<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statuskaryawan extends Model
{
    use HasFactory;

    protected $table = 'status_karyawan';
    protected $primaryKey = 'kode_status_karyawan';
    public $incrementing = false;
    protected $guarded = [];
}
