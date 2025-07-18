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
        if (!Schema::hasTable('booking_services')) {
            Schema::create('booking_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration_minutes');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->integer('buffer_time_before')->default(0);
            $table->integer('buffer_time_after')->default(0);
            $table->integer('max_advance_booking_days')->default(30);
            $table->integer('min_notice_hours')->default(24);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(false);
            $table->integer('max_bookings_per_day')->nullable();
            $table->string('category')->nullable();
            $table->string('location')->nullable();
            $table->text('online_meeting_url')->nullable();
            $table->text('preparation_instructions')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index(['category', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_services');
    }
};
