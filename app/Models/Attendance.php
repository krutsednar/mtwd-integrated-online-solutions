<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'remote_id',
        'employee_number',
        'attendance_date',
        'morning_in',
        'morning_out',
        'afternoon_in',
        'afternoon_out',
        'ot_in',
        'ot_out',
        'is_synced',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'is_synced'       => 'boolean',
            'synced_at'       => 'datetime',
        ];
    }
}
