<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiEmployee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }

    public function period()
    {
        return $this->belongsTo(KpiPeriod::class, 'kpi_period_id');
    }

    public function details()
    {
        return $this->hasMany(KpiDetail::class, 'kpi_employee_id');
    }
}
