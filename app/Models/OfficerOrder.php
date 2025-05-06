<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfficerOrder extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    public $table = 'officer_orders';

    protected $appends = [
        'attachments',
    ];

    protected $fillable = [
        'oo_no',
        'series',
        'subject',
        'date',
    ];

    public $orderable = [
        'id',
        'oo_no',
        'series',
        'subject',
        'date',
    ];

    public $filterable = [
        'id',
        'oo_no',
        'series',
        'subject',
        'date',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('officer_order_attachments')->map(function ($item) {
            $media        = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

}
