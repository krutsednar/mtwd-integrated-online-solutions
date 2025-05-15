<?php

namespace App\Models;

use Hash;
use Carbon\Carbon;
use DateTimeInterface;
use App\Support\HasAdvancedFilter;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Translation\HasLocalePreference;

class ProdUser extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $connection = 'proddb';

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'employee_number',
        'name',
        'email',
        'password',
        'locale',
        'is_approved',
    ];

    public $orderable = [
        'id',
        'employee_number',
        'name',
        'email',
        'email_verified_at',
        'locale',
        'is_approved',
    ];

    public $filterable = [
        'id',
        'employee_number',
        'name',
        'email',
        'email_verified_at',
        'roles.title',
        'locale',
    ];

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('title', 'super_admin')->exists();
    }

    public function scopeAdmins()
    {
        return $this->whereHas('roles', fn ($q) => $q->where('title', 'super_admin'));
    }

}
