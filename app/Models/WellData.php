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

class WellData extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }


    protected $connection = 'proddb';

    public $table = 'well_data';

    protected $dates = [
        'date_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user_id',
        'production_well_id',
        'date_time',
        'flowmeter',
        'flowrate',
        'psi',
    ];

    public $orderable = [
        'id',
        'user.name',
        'production_well.name',
        'date_time',
        'flowmeter',
        'flowrate',
        'psi',
    ];

    public $filterable = [
        'id',
        'user.name',
        'production_well.name',
        'date_time',
        'flowmeter',
        'flowrate',
        'psi',
    ];


    public function user()
    {
        return $this->belongsTo(ProdUser::class);
    }

    public function productionWell()
    {
        return $this->belongsTo(ProductionWell::class);
    }

}
