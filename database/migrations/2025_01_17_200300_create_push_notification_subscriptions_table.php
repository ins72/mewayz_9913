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
        Schema::create('push_notification_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->text('endpoint');
            $table->text('p256dh_key');
            $table->text('auth_key');
            $table->string('user_agent')->nullable();
            $table->json('notification_types')->nullable();
            $table->json('quiet_hours')->nullable();
            $table->enum('frequency', ['immediate', 'digest', 'weekly'])->default('immediate');
            $table->timestamp('subscribed_at');
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'subscribed_at']);
            $table->unique(['user_id', 'endpoint']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notification_subscriptions');
    }
};