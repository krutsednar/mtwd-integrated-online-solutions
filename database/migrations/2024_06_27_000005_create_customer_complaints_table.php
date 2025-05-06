<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerComplaintsTable extends Migration
{
    public function up()
    {
        Schema::create('customer_complaints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('complaints')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
