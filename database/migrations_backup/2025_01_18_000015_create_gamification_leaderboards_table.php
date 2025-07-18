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
        Schema::create('gamification_leaderboards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('type'); // xp, achievements, streaks, revenue, engagement
            $table->string('category')->default('general'); // general, weekly, monthly, yearly
            $table->string('period')->default('all_time'); // daily, weekly, monthly, yearly, all_time
            $table->boolean('is_active')->default(true);
            $table->json('criteria')->nullable(); // Leaderboard calculation criteria
            $table->json('rewards')->nullable(); // Rewards for top positions
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->integer('max_entries')->default(100);
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'category']);
            $table->index(['is_active', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamification_leaderboards');
    }
};