<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;
    protected $table = "departemen";
    protected $primaryKey = "kode_dept";
    public $incrementing = false;
    protected $guarded = [];

    // Relasi dengan User (Many to Many)
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_departemen_access', 'kode_dept', 'user_id', 'kode_dept', 'id');
    }
}
