<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 10)->unique(); // en, es, fr, de, etc.
            $table->string('name');
            $table->string('native_name');
            $table->string('flag_icon')->nullable();
            $table->boolean('is_rtl')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });


    public function down()
    {
        Schema::dropIfExists('languages');

};