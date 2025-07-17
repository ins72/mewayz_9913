<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_calendars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('booking_services')->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true);
            $table->string('override_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['service_id', 'date']);
            $table->index(['date', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_calendars');
    }
};
