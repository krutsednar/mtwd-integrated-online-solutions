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
            $table->datetime('date_returned')->nullable();
            $table->string('pad_received_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     Schema::table('online_job_orders', function (Blueprint $table) {
    //         //
    //     });
    // }
};
