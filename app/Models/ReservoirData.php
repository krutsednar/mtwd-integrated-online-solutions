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

class ReservoirData extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }


    protected $connection = 'proddb';

    public $table = 'reservoir_data';

    protected $dates = [
        'date_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'reservoir_id',
        'user_id',
        'date_time',
        'flowmeter',
        'flowrate',
        'tank_level',
    ];

    public $orderable = [
        'id',
        'reservoir.name',
        'user.name',
        'date_time',
        'flowmeter',
        'flowrate',
        'tank_level',
    ];

    public $filterable = [
        'id',
        'reservoir.name',
        'user.name',
        'date_time',
        'flowmeter',
        'flowrate',
        'tank_level',
    ];

    public function reservoir()
    {
        return $this->belongsTo(Reservoir::class);
    }

    public function user()
    {
        return $this->belongsTo(ProdUser::class);
    }
}
