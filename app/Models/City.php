<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
   public $table = 'cities';

    protected $fillable = [
        'name',
        'province_id',

    ];
}
