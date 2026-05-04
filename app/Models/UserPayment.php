<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserPayment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $connection = 'kitdb';

    public $table = 'user_payments';

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

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
