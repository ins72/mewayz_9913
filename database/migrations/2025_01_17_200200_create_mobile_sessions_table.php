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
        Schema::create('mobile_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->enum('device_type', ['mobile', 'tablet', 'desktop']);
            $table->enum('platform', ['ios', 'android', 'web']);
            $table->json('device_info')->nullable();
            $table->string('app_version')->nullable();
            $table->json('screen_size')->nullable();
            $table->timestamp('session_start');
            $table->timestamp('session_end')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('session_data')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'session_start']);
            $table->index(['platform', 'device_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_sessions');
    }
};