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

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $connection = 'mcpdb';

    public $table = 'payments';

    public $orderable = [
        'id',
        'AccountNumber',
        'MerchantCode',
        'MerchantRefNo',
        'Particulars',
        'Amount',
        'PayorName',
        'PayorEmail',
        'Status',
        'EppRefNo',
        'PaymentOption',
    ];

    public $filterable = [
        'id',
        'AccountNumber',
        'MerchantCode',
        'MerchantRefNo',
        'Particulars',
        'Amount',
        'PayorName',
        'PayorEmail',
        'Status',
        'EppRefNo',
        'PaymentOption',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'AccountNumber',
        'MerchantCode',
        'MerchantRefNo',
        'Particulars',
        'Amount',
        'PayorName',
        'PayorEmail',
        'Status',
        'EppRefNo',
        'PaymentOption',
    ];

}
