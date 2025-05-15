<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use App\Traits\Auditable;
use App\Support\HasAdvancedFilter;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Career extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $connection = 'mocadb';

    public $table = 'careers';

    public $orderable = [
        'id',
        'item_number',
        'position_title',
        'salary',
        'monthly_salary',
        'education',
        'training',
        'experience',
        'eligibility',
        'competency',
        'assignment',
        'posting_date',
        'closing_date',
    ];

    public $filterable = [
        'id',
        'item_number',
        'position_title',
        'salary',
        'monthly_salary',
        'education',
        'training',
        'experience',
        'eligibility',
        'competency',
        'assignment',
        'posting_date',
        'closing_date',
    ];

    protected $dates = [
        'posting_date',
        'closing_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'item_number',
        'position_title',
        'salary',
        'monthly_salary',
        'education',
        'training',
        'experience',
        'eligibility',
        'competency',
        'assignment',
        'posting_date',
        'closing_date',
    ];

}
