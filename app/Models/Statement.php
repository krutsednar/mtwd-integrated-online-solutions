<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use App\Traits\Auditable;
use App\Support\HasAdvancedFilter;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Statement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }


    protected $connection = 'mcpdb';

    public $table = 'statements';

    public $orderable = [
        'id',
        'account_number',
        'account_name',
        'address',
        'classification',
        'reading_date',
        'due_date',
        'previous_reading_cum',
        'present_reading_cum',
        'consumption_cum',
        'current_bill',
        'maintenance_fee',
        'franchise_tax',
        'arrears',
        'other_charges',
        'advance_payment',
        'senior_citizen_discount',
        'amount_before_due_date',
        'penalty',
        'amount_after_due_date',
        'status',
        'months_in_arrears',
        'paid',
        'transmitted',
    ];

    public $filterable = [
        'id',
        'account_number',
        'account_name',
        'address',
        'classification',
        'reading_date',
        'due_date',
        'previous_reading_cum',
        'present_reading_cum',
        'consumption_cum',
        'current_bill',
        'maintenance_fee',
        'franchise_tax',
        'arrears',
        'other_charges',
        'advance_payment',
        'senior_citizen_discount',
        'amount_before_due_date',
        'penalty',
        'amount_after_due_date',
        'status',
        'months_in_arrears',
        'paid',
        'transmitted',
    ];

    protected $dates = [
        'reading_date',
        'due_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'account_number',
        'account_name',
        'address',
        'classification',
        'reading_date',
        'due_date',
        'previous_reading_cum',
        'present_reading_cum',
        'consumption_cum',
        'current_bill',
        'maintenance_fee',
        'franchise_tax',
        'arrears',
        'other_charges',
        'advance_payment',
        'senior_citizen_discount',
        'amount_before_due_date',
        'penalty',
        'amount_after_due_date',
        'status',
        'months_in_arrears',
        'paid',
        'transmitted',
    ];

}
