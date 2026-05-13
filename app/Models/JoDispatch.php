<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoDispatch extends Model
{
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $fillable = [
        'jo_number',
        'jo_user',
    ];

    public function onlineJobOrder()
    {
        return $this->belongsTo(OnlineJobOrder::class, 'jo_number', 'jo_number');
    }

    public function jo_users()
    {
        return $this->hasMany(User::class, 'jo_user', 'jo_id');
    }
}
