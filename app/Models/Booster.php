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

class Booster extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $connection = 'proddb';

    public $table = 'boosters';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'production_facility_id',
        'name',
        'max_pressure',
        'min_pressure',
        'freq',
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
        'max_pressure',
        'min_pressure',
        'freq',
    ];

    public $filterable = [
        'id',
        'production_facility.name',
        'name',
        'max_pressure',
        'min_pressure',
        'freq',
    ];

    public function productionFacility()
    {
        return $this->belongsTo(ProductionFacility::class);
    }

}
