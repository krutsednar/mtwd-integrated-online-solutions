<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtherAnnouncement extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    public $table = 'other_announcements';

    public $orderable = [
        'id',
        'subject',
        'date',
        'details',
    ];

    public $filterable = [
        'id',
        'subject',
        'date',
        'details',
    ];

    protected $appends = [
        'attachments',
    ];

    protected $fillable = [
        'subject',
        'date',
        'details',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getAttachmentsAttribute()
    {
        return $this->getMedia('other_announcement_attachments')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

}
