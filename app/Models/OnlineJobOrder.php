<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rupadana\ApiService\Contracts\HasAllowedSorts;
use Rupadana\ApiService\Contracts\HasAllowedFields;
use Rupadana\ApiService\Contracts\HasAllowedFilters;

class OnlineJobOrder extends Model implements HasAllowedFilters

{
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $fillable = [
        'jo_number',
        'date_requested',
        'account_number',
        'registered_name',
        'meter_number',
        'job_order_code',
        'address',
        'town',
        'barangay',
        'requested_by',
        'contact_number',
        'email',
        'mode_received',
        'remarks',
        'processed_by',
        'status',
        'is_online'
    ];


    public function jocode()
    {
        return $this->belongsTo(JobOrderCode::class, 'job_order_code', 'code');
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'accmasterlist', 'account_number');
    }

    public static function getAllowedFilters(): array
    {
        return [
            'created_at',
            'jo_number',
            'is_online',
        ];
    }

}
