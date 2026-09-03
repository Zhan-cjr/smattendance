<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $table = 'approvals';

    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'user_id',
        'level',
        'status',
        'keterangan',
    ];

    /**
     * Get the parent approvable model (Izinabsen, Cuti, etc).
     */
    public function approvable()
    {
        return $this->morphTo();
    }
}
