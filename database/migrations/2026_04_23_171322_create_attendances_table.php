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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('remote_id')->nullable()->unique();
            $table->string('employee_number', 50);
            $table->date('attendance_date');
            $table->time('morning_in')->nullable();
            $table->time('morning_out')->nullable();
            $table->time('afternoon_in')->nullable();
            $table->time('afternoon_out')->nullable();
            $table->time('ot_in')->nullable();
            $table->time('ot_out')->nullable();
            $table->boolean('is_synced')->default(false);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['employee_number', 'attendance_date']);
            $table->index('employee_number');
            $table->index('attendance_date');
            $table->index('is_synced');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
