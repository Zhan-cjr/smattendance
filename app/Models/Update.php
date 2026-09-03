<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'title',
        'description',
        'changelog',
        'file_url',
        'file_size',
        'checksum',
        'is_major',
        'is_active',
        'migrations',
        'seeders',
        'released_at',
    ];

    protected $casts = [
        'is_major' => 'boolean',
        'is_active' => 'boolean',
        'migrations' => 'array',
        'seeders' => 'array',
        'released_at' => 'datetime',
    ];

    /**
     * Get all update logs for this update
     */
    public function logs()
    {
        return $this->hasMany(UpdateLog::class, 'version', 'version');
    }

    /**
     * Scope untuk mendapatkan update aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mendapatkan update major
     */
    public function scopeMajor($query)
    {
        return $query->where('is_major', true);
    }
}
