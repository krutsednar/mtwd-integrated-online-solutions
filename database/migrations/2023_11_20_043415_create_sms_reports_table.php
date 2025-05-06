<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsReportsTable extends Migration
{
    public function up()
    {
        Schema::create('sms_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_number')->nullable();
            $table->string('mobile')->nullable();
            $table->string('amount_before_due')->nullable();
            $table->string('due_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_reports');
    }
};
