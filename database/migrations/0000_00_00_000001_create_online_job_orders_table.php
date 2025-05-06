<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_job_orders', static function (Blueprint $table) {
            $table->id();
            $table->string('jo_number');
            $table->datetime('date_requested');
            $table->string('account_number');
            $table->string('registered_name');
            $table->string('meter_number');
            $table->string('job_order_code');
            $table->string('address');
            $table->string('town');
            $table->string('barangay');
            $table->string('contact_number');
            $table->string('email');
            $table->string('mode_received');
            $table->text('remarks');
            $table->string('processed_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
