<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_number');
            $table->string('leave_application_number')->unique();
            $table->string('employee_name');
            $table->string('job_description');
            $table->string('basic_salary')->nullable();
            $table->date('date_filed');
            $table->string('type_of_leave')->nullable();
            $table->string('vl_type')->nullable();
            $table->string('vacation_input')->nullable();
            $table->string('sl_type')->nullable();
            $table->string('sick_input')->nullable();
            $table->string('inclusive_dates');
            $table->integer('number_of_working_days');
            $table->string('commutation')->nullable();
            $table->string('leave_status');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
