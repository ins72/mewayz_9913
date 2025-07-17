<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 3)->unique(); // USD, EUR, GBP, etc.
            $table->string('name');
            $table->string('symbol');
            $table->decimal('exchange_rate', 10, 6)->default(1.0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('currencies');
    }
};