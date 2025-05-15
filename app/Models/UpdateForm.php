<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Spatie\MediaLibrary\HasMedia;
use App\Support\HasAdvancedFilter;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UpdateForm extends Model
{
    use HasFactory,  SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }


    protected $connection = 'mcisdb';

    public $table = 'update_forms';

    protected $appends = [
        'attachment',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const APPROVAL_SELECT = [
        'Pending'     => 'Pending',
        'Approved'    => 'Approved',
        'Disapproved' => 'Disapproved',
    ];

    public const VALIDATION_SELECT = [
        'Pending'     => 'Pending',
        'Validated'   => 'Validated',
        'Disapproved' => 'Disapproved',
    ];

    protected $fillable = [
        'account_number',
        'account_name',
        'address',
        'mobile_number',
        'email',
        'validation',
        'approval',
    ];

    public $orderable = [
        'id',
        'account_number',
        'account_name',
        'address',
        'mobile_number',
        'email',
        'validation',
        'approval',
    ];

    public $filterable = [
        'id',
        'account_number',
        'account_name',
        'address',
        'mobile_number',
        'email',
        'validation',
        'approval',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id', 'validated_by');
    }

    // public function approves()
    // {
    //     return $this->hasMany(User::class, 'id', 'approved_by');
    // }

    public function getAttachmentAttribute()
    {
        return $this->getMedia('update_form_attachment')->map(function ($item) {
            $media        = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

    public function getValidationLabelAttribute($value)
    {
        return static::VALIDATION_SELECT[$this->validation] ?? null;
    }

    public function getApprovalLabelAttribute($value)
    {
        return static::APPROVAL_SELECT[$this->approval] ?? null;
    }

}
