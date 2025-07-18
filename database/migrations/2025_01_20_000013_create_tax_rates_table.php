<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('country_code', 2);
            $table->string('state_code', 10)->nullable();
            $table->decimal('rate', 8, 4); // percentage
            $table->string('type'); // vat, gst, sales_tax, etc.
            $table->boolean('is_active')->default(true);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();
            
            $table->index(['country_code', 'state_code', 'is_active']);
        });


    public function down()
    {
        Schema::dropIfExists('tax_rates');

};