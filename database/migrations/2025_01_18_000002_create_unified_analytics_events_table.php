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
        Schema::create('unified_analytics_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->uuid('workspace_id')->nullable();
            $table->string('event_type', 100);
            $table->string('event_category', 50)->default('engagement');
            $table->string('platform', 50);
            $table->string('entity_id', 100)->nullable();
            $table->string('entity_type', 50)->nullable();
            $table->json('properties')->nullable();
            $table->string('session_id', 100)->nullable();
            $table->string('visitor_id', 100)->nullable();
            $table->timestamp('timestamp');
            $table->decimal('revenue', 10, 2)->nullable();
            $table->decimal('conversion_value', 10, 2)->nullable();
            $table->string('utm_source', 255)->nullable();
            $table->string('utm_medium', 255)->nullable();
            $table->string('utm_campaign', 255)->nullable();
            $table->string('utm_term', 255)->nullable();
            $table->string('utm_content', 255)->nullable();
            $table->text('referrer')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('location_country', 2)->nullable();
            $table->string('location_city', 100)->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('os', 50)->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->string('screen_resolution', 20)->nullable();
            $table->text('page_url')->nullable();
            $table->string('page_title', 255)->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->integer('scroll_depth')->nullable(); // percentage
            $table->json('custom_attributes')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'workspace_id']);
            $table->index(['platform', 'event_type']);
            $table->index(['event_category', 'timestamp']);
            $table->index(['visitor_id', 'timestamp']);
            $table->index(['session_id', 'timestamp']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['utm_source', 'utm_medium']);
            $table->index(['location_country', 'location_city']);
            $table->index(['device_type', 'is_mobile']);
            $table->index(['timestamp', 'event_type']);
            $table->index(['revenue', 'conversion_value']);
            $table->index(['platform', 'event_category', 'timestamp']);
            
            // Composite indexes for common queries
            $table->index(['user_id', 'platform', 'timestamp']);
            $table->index(['workspace_id', 'event_type', 'timestamp']);
            $table->index(['visitor_id', 'platform', 'timestamp']);
            $table->index(['session_id', 'event_type', 'timestamp']);
            $table->index(['platform', 'event_category', 'revenue']);
            
            // Foreign key constraints (optional - depends on your database setup)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unified_analytics_events');

};