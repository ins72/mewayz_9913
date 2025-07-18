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
        Schema::create('gamification_streaks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('streak_type'); // daily_login, weekly_post, monthly_course, etc.
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->integer('total_completions')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->date('streak_start_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('streak_data')->nullable(); // Additional streak information
            $table->integer('streak_multiplier')->default(1);
            $table->json('milestones')->nullable(); // Streak milestone rewards
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['user_id', 'streak_type']);
            $table->index(['streak_type', 'current_streak']);
            $table->index(['is_active', 'last_activity_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_streaks');
    }
};