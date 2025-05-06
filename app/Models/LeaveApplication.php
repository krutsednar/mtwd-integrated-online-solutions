<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveApplication extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    public $table = 'leave_applications';

    protected $dates = [
        'date_filed',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const SL_TYPE_SELECT = [
        'In Hospital' => 'In Hospital',
        'Out Patient' => 'Out Patient',
    ];

    public const VL_TYPE_SELECT = [
        'Within Philippines' => 'Within Philippines',
        'Abroad'             => 'Abroad',
    ];

    public const COMMUTATION_SELECT = [
        'Requested'     => 'Requested',
        'Not Requested' => 'Not Requested',
    ];

    public const LEAVE_STATUS_SELECT = [
        'Pending'     => 'Pending',
        'Approved'    => 'Approved',
        'Disapproved' => 'Disapproved',
    ];

    protected $fillable = [
        'employee_number',
        'leave_application_number',
        'employee_name',
        'job_description',
        'basic_salary',
        'date_filed',
        'type_of_leave',
        'vl_type',
        'vacation_input',
        'sl_type',
        'sick_input',
        'inclusive_dates',
        'number_of_working_days',
        'commutation',
        'leave_status',
    ];

    public $orderable = [
        'id',
        'employee_number',
        'leave_application_number',
        'employee_name',
        'job_description',
        'basic_salary',
        'date_filed',
        'type_of_leave',
        'vl_type',
        'vacation_input',
        'sl_type',
        'sick_input',
        'inclusive_dates',
        'number_of_working_days',
        'commutation',
        'leave_status',
    ];

    public $filterable = [
        'id',
        'employee_number',
        'leave_application_number',
        'employee_name',
        'job_description',
        'basic_salary',
        'date_filed',
        'type_of_leave',
        'vl_type',
        'vacation_input',
        'sl_type',
        'sick_input',
        'inclusive_dates',
        'number_of_working_days',
        'commutation',
        'leave_status',
    ];

    public const TYPE_OF_LEAVE_SELECT = [
        'Vacation'       => 'Vacation',
        'Sick'           => 'Sick',
        'Mandatory'      => 'Mandatory',
        'Special'        => 'Special',
        'Maternity'      => 'Maternity',
        'Paternity'      => 'Paternity',
        'Study'          => 'Study',
        'Solo Parent'    => 'Solo Parent',
        'Rehabilitation' => 'Rehabilitation',
        'Others'         => 'Others',
    ];


    public function getTypeOfLeaveLabelAttribute($value)
    {
        return static::TYPE_OF_LEAVE_SELECT[$this->type_of_leave] ?? null;
    }

    public function getVlTypeLabelAttribute($value)
    {
        return static::VL_TYPE_SELECT[$this->vl_type] ?? null;
    }

    public function getSlTypeLabelAttribute($value)
    {
        return static::SL_TYPE_SELECT[$this->sl_type] ?? null;
    }

    public function getCommutationLabelAttribute($value)
    {
        return static::COMMUTATION_SELECT[$this->commutation] ?? null;
    }

    public function getLeaveStatusLabelAttribute($value)
    {
        return static::LEAVE_STATUS_SELECT[$this->leave_status] ?? null;
    }

}
