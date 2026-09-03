<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userkaryawan extends Model
{
    use HasFactory;
    protected $table = 'users_karyawan';
    protected $guarded = [];

    /**
     * Admin user yang terhubung sebagai approver delegasi.
     */
    public function approvalAdmin()
    {
        return $this->belongsTo(User::class, 'approval_admin_id');
    }

    /**
     * User (login account) yang terhubung dengan karyawan ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Data karyawan yang terhubung.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}
