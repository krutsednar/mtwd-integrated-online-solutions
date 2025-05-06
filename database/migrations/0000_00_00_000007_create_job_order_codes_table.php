<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_order_codes', static function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('description');
            $table->string('category_code');
            $table->string('division_code');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
