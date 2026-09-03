<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLayer extends Model
{
    use HasFactory;

    protected $table = 'approval_layers';

    protected $fillable = [
        'feature',
        'level',
        'role_name',
        'kode_dept',
        'kode_jabatan',
        'kode_cabang',
    ];
}
