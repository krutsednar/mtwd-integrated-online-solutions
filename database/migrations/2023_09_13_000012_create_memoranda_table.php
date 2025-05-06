<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemorandaTable extends Migration
{
    public function up()
    {
        Schema::create('memoranda', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject');
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
