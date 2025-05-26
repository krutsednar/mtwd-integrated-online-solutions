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
            $table->string('division_concerned')->nullable();
            $table->datetime('date_forwarded')->nullable();
            $table->string('forwarded_by')->nullable();
            $table->datetime('date_received')->nullable();
            $table->string('received_by')->nullable();
            $table->string('dispatched_by')->nullable();
            $table->string('division_received_by')->nullable();
            $table->datetime('date_dispatched')->nullable();
            $table->datetime('date_accomplished')->nullable();
            $table->string('actions_taken')->nullable();
            $table->string('accomplishment_processed_by')->nullable();
            $table->string('recommendations')->nullable();
            $table->string('field_findings')->nullable();
            $table->string('acknowledge_by')->nullable();
            $table->string('verified_by')->nullable();
            $table->datetime('date_verified')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_job_orders', function (Blueprint $table) {
            //
        });
    }
};
