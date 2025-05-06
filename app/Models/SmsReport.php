<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsReport extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    public $table = 'sms_reports';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'Sent'   => 'Sent',
        'Unsent' => 'Unsent',
        'Failed' => 'Failed',
    ];

    protected $fillable = [
        'account_number',
        'mobile',
        'amount_before_due',
        'due_date',
        'status',
    ];

    public $orderable = [
        'id',
        'account_number',
        'mobile',
        'amount_before_due',
        'due_date',
        'status',
    ];

    public $filterable = [
        'id',
        'account_number',
        'mobile',
        'amount_before_due',
        'due_date',
        'status',
    ];

    public function getStatusLabelAttribute($value)
    {
        return static::STATUS_SELECT[$this->status] ?? null;
    }

}
