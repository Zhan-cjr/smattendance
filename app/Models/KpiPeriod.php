<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiPeriod extends Model
{
    use HasFactory;
    protected $table = 'kpi_periods';
    protected $guarded = [];

    public function kpi_employees()
    {
        return $this->hasMany(KpiEmployee::class);
    }
}
