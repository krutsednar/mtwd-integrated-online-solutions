<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOrderCode extends Model
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
        'description',
        'category_code',
        'division_code'
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_code', 'code');
    }

    public function onlineJobOrders()
    {
        return $this->hasMany(OnlineJobOrder::class, 'job_order_code', 'code');
    }

}
