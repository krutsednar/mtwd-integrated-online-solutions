<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductionWell extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $connection = 'proddb';

    public $table = 'production_wells';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'production_facility_id',
        'name',
        'pump_setting',
        'first_well_screen',
        'critical_pwl',
        'swl',
        'twelveAM',
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
        'ten',
        'eleven',
        'twelve',
        'thirteen',
        'fourteen',
        'fifteen',
        'sixteen',
        'seventeen',
        'eighteen',
        'nineteen',
        'twenty',
        'twentyone',
        'twentytwo',
        'twentythree',
        'twentyfour',
        'twentyfive',
        'twentysix',
        'twentyseven',
        'twentyeight',
        'twentynine',
        'thirty',
        'thirtyone',

    ];

    public $orderable = [
        'id',
        'production_facility.name',
        'name',
        'pump_setting',
        'first_well_screen',
        'critical_pwl',
        'swl',
    ];

    public $filterable = [
        'id',
        'production_facility.name',
        'name',
        'pump_setting',
        'first_well_screen',
        'critical_pwl',
        'swl',
    ];

    public function wellData()
    {
        return $this->hasMany(WellData::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function productionFacility()
    {
        return $this->belongsTo(ProductionFacility::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }
}
