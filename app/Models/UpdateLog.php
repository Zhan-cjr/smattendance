<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'version',
        'previous_version',
        'status',
        'progress_percentage',
        'progress_log',
        'message',
        'error_log',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that performed the update
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
