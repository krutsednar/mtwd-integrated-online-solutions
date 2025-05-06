<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtoApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('cto_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_number');
            $table->string('cto_application_number')->nullable();
            $table->date('date_filed');
            $table->string('employee_name');
            $table->string('position')->nullable();
            $table->string('division');
            $table->string('inclusive_dates');
            $table->integer('working_days');
            $table->longText('reason');
            $table->string('cto_status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
