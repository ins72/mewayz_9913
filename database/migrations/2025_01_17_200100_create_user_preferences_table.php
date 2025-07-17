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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('theme')->default('dark');
            $table->string('language')->default('en');
            $table->string('timezone')->default('UTC');
            $table->json('primary_goals')->nullable();
            $table->string('business_type')->nullable();
            $table->string('experience_level')->default('beginner');
            $table->integer('team_size')->default(1);
            $table->json('recommended_features')->nullable();
            $table->json('feature_priorities')->nullable();
            $table->json('notification_preferences')->nullable();
            $table->json('dashboard_layout')->nullable();
            $table->json('accessibility_options')->nullable();
            $table->json('privacy_settings')->nullable();
            $table->json('mobile_preferences')->nullable();
            $table->json('custom_colors')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};