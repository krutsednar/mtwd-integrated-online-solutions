<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('online_job_orders', function (Blueprint $table) {
            $table->boolean('is_online')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
};
