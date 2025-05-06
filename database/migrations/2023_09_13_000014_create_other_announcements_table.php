<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherAnnouncementsTable extends Migration
{
    public function up()
    {
        Schema::create('other_announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject');
            $table->date('date');
            $table->longText('details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
