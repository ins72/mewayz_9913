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
        if (!Schema::hasTable('email_campaign_analytics')) {
            Schema::create('email_campaign_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->string('event_type'); // sent, delivered, opened, clicked, unsubscribed, bounced, complained
            $table->timestamp('event_timestamp');
            $table->string('user_agent')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('event_data')->nullable(); // Additional event-specific data
            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('email_campaigns')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('email_subscribers')->onDelete('cascade');

            $table->index(['campaign_id', 'event_type']);
            $table->index(['subscriber_id', 'event_type']);
            $table->index(['campaign_id', 'event_timestamp']);
        });


    /**
     * Reverse the migrations.
     */



}

public function down(): void
    {
        Schema::dropIfExists('email_campaign_analytics');

};