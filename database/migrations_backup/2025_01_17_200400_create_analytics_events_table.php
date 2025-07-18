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
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->uuid('workspace_id')->nullable();
            $table->string('event_type');
            $table->string('event_name');
            $table->json('event_data')->nullable();
            $table->json('user_properties')->nullable();
            $table->string('session_id')->nullable();
            $table->string('device_type')->nullable();
            $table->string('platform')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamp('event_time');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->index(['user_id', 'event_time']);
            $table->index(['workspace_id', 'event_time']);
            $table->index(['event_type', 'event_name']);
            $table->index('event_time');
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');

};