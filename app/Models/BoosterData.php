<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BoosterData extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $connection = 'proddb';

    public $table = 'booster_data';

    protected $dates = [
        'date_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'booster_id',
        'user_id',
        'date_time',
        'flowmeter',
        'flowrate',
        'psi',
    ];

    public $orderable = [
        'id',
        'booster.name',
        'user.name',
        'date_time',
        'flowmeter',
        'flowrate',
        'psi',
    ];

    public $filterable = [
        'id',
        'booster.name',
        'user.name',
        'date_time',
        'flowmeter',
        'flowrate',
        'psi',
    ];

    public function booster()
    {
        return $this->belongsTo(Booster::class);
    }

    public function user()
    {
        return $this->belongsTo(ProdUser::class);
    }
}
