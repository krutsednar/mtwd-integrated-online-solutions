<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', static function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('contact_number');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
