<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    public $table = 'barangays';

    protected $fillable = [
        'name',
        'city_id',

    ];
}
