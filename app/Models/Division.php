<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $fillable = [
        'code',
        'name',
        'contact_number'
    ];

    public function jocodes()
    {
        return $this->hasMany(JobOrderCode::class, 'division_code', 'code');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'division_id', 'code');
    }

}
