<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Account extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $connection = 'kitdb';

    public $table = 'accounts';

    public $orderable = [
        'id',
        'accmasterlist',
        'mastername',
        'mobile',
        'latitude',
        'longtitude',
    ];

    public $filterable = [
        'id',
        'accmasterlist',
        'mastername',
        'mobile',
        'latitude',
        'longtitude',
    ];

    protected $fillable = [
        'accmasterlist',
        'mastername',
        'mobile',
        'meter_number',
        'latitude',
        'longtitude',
        'is_connected',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

}
