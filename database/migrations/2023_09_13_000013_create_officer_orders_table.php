<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficerOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('officer_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('oo_no')->unique();
            $table->string('series');
            $table->string('subject');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
