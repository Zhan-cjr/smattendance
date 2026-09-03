<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveLocation extends Model
{
    use HasFactory;

    protected $table = 'live_location';
    protected $fillable = [
        'nik',
        'latitude',
        'longitude',
        'lokasi',
        'accuracy',
        'tracked_at'
    ];

    protected $casts = [
        'tracked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi dengan Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}
