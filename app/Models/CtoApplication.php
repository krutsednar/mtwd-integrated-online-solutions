<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CtoApplication extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    public const CTO_STATUS_SELECT = [
        'Pending'     => 'Pending',
        'Approved'    => 'Approved',
        'Disapproved' => 'Disapproved',
    ];

    public $table = 'cto_applications';

    public $orderable = [
        'id',
        'employee_number',
        'cto_application_number',
        'date_filed',
        'employee_name',
        'position',
        'division',
        'inclusive_dates',
        'working_days',
        'reason',
        'cto_status',
    ];

    public $filterable = [
        'id',
        'employee_number',
        'cto_application_number',
        'date_filed',
        'employee_name',
        'position',
        'division',
        'inclusive_dates',
        'working_days',
        'reason',
        'cto_status',
    ];

    protected $dates = [
        'date_filed',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'employee_number',
        'cto_application_number',
        'date_filed',
        'employee_name',
        'position',
        'division',
        'inclusive_dates',
        'working_days',
        'reason',
        'cto_status',
    ];

}
