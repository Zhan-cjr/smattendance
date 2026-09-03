<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(KpiEmployee::class, 'kpi_employee_id');
    }

    public function indicator()
    {
        return $this->belongsTo(KpiIndicatorDetail::class, 'kpi_indicator_detail_id');
    }
}
